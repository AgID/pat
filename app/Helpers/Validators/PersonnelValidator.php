<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Model\PersonnelModel;
use System\Input;
use System\Registry;
use System\Validator;

/**
 * Validator per l'oggetto Personale (object_personnel)
 */
class PersonnelValidator
{

    public Validator $validator;

    /**
     * @description Costruttore
     */
    public function __construct()
    {
        $this->validator = new Validator();
    }

    /**
     * Controlla la validità dell'ID nell'URI segment e se esiste un personale con quell'ID per l'ente
     *
     * @param null $checkOwner Indica se controllare o meno l'utente propietario del record
     * @return array
     * @throws Exception
     */
    public function validateUriSegmentId($checkOwner = null): array
    {

        $this->validator->label('ID personale')
            ->value(uri()->segment(4, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () use ($checkOwner) {

                //Recupero il personale con l'id passato in input
                $check = PersonnelModel::where('id', uri()->segment(4, 0))
                    ->with(['referent_structures' => function ($query) {
                        $query->select(['object_structures.id', 'object_structures.structure_name', 'object_structures.structure_of_belonging_id',
                            'object_structures.reference_email', 'parent.structure_name as parent_name'])
                            ->join('object_structures as parent', 'parent.id', '=', 'object_structures.structure_of_belonging_id', 'left outer');
                    }])
                    ->with('assignments:id,object,name,assignment_start')
                    ->with('measures:id,object,date,number,type')
                    ->with(['public_in' => function ($query) {
                        $query->select(['section_fo_id', 'section_fo_config_publication_archive.id', 'section_fo.name'])
                            ->join('section_fo', 'section_fo.id', '=', 'section_fo_id');
                    }])
                    ->with('political_organ:id,political_organ_id,object_personnel_id')
                    ->with('historical_datas')
                    ->first();

                //Se non esiste il personale con questo id, oppure l'utente non può modificare i record non creati da lui
                //mostro un messaggio di errore
                if ($check == null || (!empty($checkOwner) && !checkRecordOwner(@$check->owner_id))) {

                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id', null, 'patos'), 'personale ')
                    ];
                }

                //Se esiste il personale lo salvo nel registro
                Registry::set('personnel', $check);

                return null;
            })
            ->end();


        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
    }

    /**
     * Metodo che esegue la validazione dei campi del form
     *
     * @param string $mode Indica l'operazione
     * @return array
     * @throws Exception
     */
    public function check(string $mode = 'insert'): array
    {
        if ($mode == 'archiving') {
            return $this->archivingValidate();
        } else {
            return $this->validate($mode);
        }
    }

    /**
     * Metodo per la validazione dei campi del form per l'archiviazione
     *
     * @return array
     * @throws Exception
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    protected function archivingValidate(): array
    {
        $this->validator->verifyToken()
            ->end();

        $this->validator->label('Item Id')
            ->value(Input::post('itemId'))
            ->required()
            ->isInt()
            ->isNaturalNoZero()
            ->add(function () {
                $user = PersonnelModel::where('id', Input::post('itemId'))->first();
                if (empty($user)) {
                    return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Personale')];
                }
            })
            ->end();

        $this->validator->label('Attiva dal')
            ->value(Input::post('active_from'))
            ->isDate('Y-m-d')
            ->end();


        $this->validator->label('Attiva fino al')
            ->value(Input::post('active_to'))
            ->isDate('Y-m-d')
            ->add(function () {
                if (Input::post('active_to') < Input::post('active_from')) {

                    return ['error' => __('invalid_archive_ending_date', null, 'patos')];

                }
                return null;
            })
            ->end();


        $this->validator->label('Data di fine pubblicazione in archivio')
            ->value(Input::post('end_date'))
            ->required()
            ->isDate('Y-m-d')
            ->add(function () {
                if (Input::post('end_date') <= Input::post('active_to')) {

                    return ['error' => __('invalid_archive_date', null, 'patos')];

                }
                return null;
            })
            ->end();

        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
    }

    /**
     * Metodo per la validazione dei campi del form
     *
     * @param string $mode Indica l'operazione
     * @return array
     * @throws Exception
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    protected function validate(string $mode = 'insert'): array
    {
        $this->validator->label('Ruolo')
            ->value(Input::post('role_id'))
            ->required()
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        //        $this->validator->label('Pubblica in')
        //            ->value(Input::post('public_in'))
        //            ->required()
        //            ->end();

        if (Input::post('public_in')) {
            foreach (Input::post('public_in') as $in) {
                $this->validator->label('Pubblica in ' . $in)
                    ->value($in)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        $this->validator->label('Titolo accademico o professionale')
            ->value(Input::post('title'))
            ->in('arch.,avv.,dott.,dott.ssa,dr.,ing.,on.le,geom.,prof.,reg.,sig.,sig.ra,per.')
            ->end();

        $this->validator->label('Nome')
            ->value(Input::post('firstname'))
            ->required()
            ->betweenString(2, 60)
            ->end();

        $this->validator->label('Cognome')
            ->value(Input::post('lastname'))
            ->required()
            ->betweenString(3, 60)
            ->end();

        $this->validator->label('Contratto a tempo determinato')
            ->value(Input::post('determined_term'))
            ->in('0,1')
            ->end();

        if (Input::post('structures')) {
            foreach (explode(',', (string)Input::post('structures')) as $structure) {
                $this->validator->label('Struttura organizzativa ' . $structure)
                    ->value($structure)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        if (Input::post('assignments')) {
            foreach (explode(',', (string)Input::post('assignments')) as $assignments) {
                $this->validator->label('Incarico associato ' . $assignments)
                    ->value($assignments)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        if (Input::post('measures')) {
            foreach (explode(',', (string)Input::post('measures')) as $measures) {
                $this->validator->label('Provvedimento associato ' . $measures)
                    ->value($measures)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        $this->validator->label('Recapito telefonico fisso')
            ->value(Input::post('phone'))
            ->maxLength(17, 'Lunghezza numero telefonico superiore alla lunghezza massima consentita')
            ->minLength(3, 'Lunghezza numero telefonico inferiore alla lunghezza minima consentita')
            ->regex('/^([0-9().\-+ ]){3,17}$/', 'Formato numero telefono non valido.')
            ->end();

        $this->validator->label('Recapito fax')
            ->value(Input::post('fax'))
            ->betweenString(3, 30)
            ->end();

        $this->validator->label('Indirizzo email non disponibile')
            ->value(Input::post('not_available_email'))
            ->in('0,1')
            ->end();

        $this->validator->label('In carica dal')
            ->value(Input::post('in_office_since'))
            ->isDate('Y-m-d')
            ->end();

        $this->validator->label('In carica fino al')
            ->value(Input::post('in_office_until'))
            ->isDate('Y-m-d')
            ->add(function () {
                if (Input::post('in_office_until') <= Input::post('in_office_since')) {

                    return ['error' => __('invalid_end_date_office', null, 'patos')];

                }
                return null;
            })
            ->end();

        $this->validator->label('Utilizza negli elenchi del personale')
            ->value(Input::post('personnel_lists'))
            ->in('0,1')
            ->end();

        $this->validator->label('Ordine di visualizzazione')
            ->value(Input::post('priority'))
            ->required()
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        $this->validator->label('Altre informazioni')
            ->value(Input::post('other_info'))
            ->betweenString(5, 10000)
            ->end();

        $this->validator->label('Archivio informazioni')
            ->value(Input::post('information_archive'))
            ->betweenString(5, 1000)
            ->end();

        //Per i ruoli P.O., Dirigente e Segretario Generale
        $this->validator->label('Estremi atto di nomina o proclamazione')
            ->value(Input::post('extremes_of_conference'))
            ->betweenString(5, 10000)
            ->end();

        if (Input::post('organs')) {
            foreach (Input::post('organs') as $organ) {
                $this->validator->label('Organo politico-amministrativo ' . $organ)
                    ->value($organ)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        if (Input::post('role_id') == 5) {
            $this->validator->label('Incarico di stampo politico')
                ->value(Input::post('political_role'))
                ->required()
                ->betweenString(5, 120)
                ->end();
        }

        $this->validator->label('Compensi connessi all\'assunzione della carica')
            ->value(Input::post('compensations'))
            ->betweenString(5, 1000)
            ->end();

        $this->validator->label('Importi di viaggi di servizio e missioni')
            ->value(Input::post('trips_import'))
            ->betweenString(5, 1000)
            ->end();

        $this->validator->label('Altri incarichi con oneri a carico della finanza pubblica e relativi compensi')
            ->value(Input::post('other_assignments'))
            ->betweenString(5, 1000)
            ->end();

        $this->validator->label('Documentazione Art. 14 e Art. 47, c. 1, Dlgs n. 33/2013; Art. 1,2,3,4 l. n. 441/1982')
            ->value(Input::post('notes'))
            ->betweenString(5, 1000)
            ->end();

        // Se sto inserendo un nuovo personale
        if ($mode === 'insert') {

            $this->validator->label('Recapito telefonico mobile')
                ->value(Input::post('mobile_phone'))
                ->maxLength(17, __('max_phone_length', null, 'patos'))
                ->minLength(3, __('min_phone_length', null, 'patos'))
                ->regex('/^([0-9().\-+ ]){3,17}$/', __('phone_error', null, 'patos'))
                ->add(function () {
                    $mobilePhoneExist = PersonnelModel::where('mobile_phone', Input::post('mobile_phone'))->first();
                    if (!empty($mobilePhoneExist)) {
                        return ['error' => __('already_exist_phone', null, 'patos')];
                    }
                    return null;
                })
                ->end();

            $this->validator->label('Indirizzo email certificata')
                ->value(Input::post('certified_email'))
                ->isEmail()
                ->betweenString(2, 30)
                ->add(function () {
                    $certifiedEmailExist = PersonnelModel::where('certified_email', Input::post('certified_email'))->first();
                    if (!empty($certifiedEmailExist)) {
                        return ['error' => __('certified_email_exist', null, 'patos')];
                    }
                    return null;
                })
                ->end();

            if (Input::post('not_available_email') === '1') {

                $this->validator->label('Indirizzo email')
                    ->value(Input::post('email'))
                    ->required()
                    ->isEmail()
                    ->betweenString(4, 191)
                    ->end();
            } else {

                $this->validator->label('Note email non disponibile')
                    ->value(Input::post('not_available_email_txt'))
                    ->required()
                    ->betweenString(4, 60)
                    ->end();
            }
        }

        //Se sto modificando un personale esistente
        if ($mode === 'update') {

            $this->validator->label('id')
                ->value(Input::post('id'))
                ->required()
                ->isInt()
                ->isNaturalNoZero()
                ->add(function () {
                    $personnel = PersonnelModel::where('id', Input::post('id'))->first();
                    if (empty($personnel)) {
                        return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Personale')];
                    }
                })
                ->end();

            $this->validator->label('Recapito telefonico mobile')
                ->value(Input::post('mobile_phone'))
                ->maxLength(17, __('max_phone_length', null, 'patos'))
                ->minLength(3, __('min_phone_length', null, 'patos'))
                ->regex('/^([0-9().\-+ ]){3,17}$/', __('phone_error', null, 'patos'))
                ->add(function () {
                    $mobilePhoneExist = PersonnelModel::where('id', '!=', Input::post('id'))
                        ->where('mobile_phone', Input::post('mobile_phone'))
                        ->first();
                    if (!empty($mobilePhoneExist)) {
                        return ['error' => __('already_exist_phone', null, 'patos')];
                    }
                    return null;
                })
                ->end();

            $this->validator->label('Indirizzo email certificata')
                ->value(Input::post('certified_email'))
                ->isEmail()
                ->betweenString(4, 191)
                ->end();

            if (Input::post('not_available_email') === '1') {

                $this->validator->label('Indirizzo email')
                    ->value(Input::post('email'))
                    ->required()
                    ->isEmail()
                    ->betweenString(4, 191)
                    ->end();
            } else {

                $this->validator->label('Note email non disponibile')
                    ->value(Input::post('not_available_email_txt'))
                    ->required()
                    ->betweenString(4, 60)
                    ->end();
            }
        }

        if (filesUploaded('photo')) {
            $this->validator->label('Foto Allegata')
                ->file(Input::files('photo'))
                ->required()
                ->allowed(['png', 'jpeg', 'jpg', 'gif'], "l'estensione dell'immagine non è valida")
                ->maxSize('1MB', "Il file che stai tentando di caricare supera la dimensione massima di 1MB")
                ->minSize('1KB', "il file che stai tentando deve avere una dimensione minima di 1KB")
                ->end();
        }

        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
    }

    /**
     * Validatore dell'eliminazione multipla
     *
     * @return array
     * @throws Exception
     */
    public function multipleSelection(): array
    {
        $this->validator->label('Identificativi elementi');
        $this->validator->value(Input::get('ids'));
        $this->validator->required();
        $this->validator->regex('/^[0-9,]+$/', __('multiple_selection_errors', null, 'patos'));
        $this->validator->add(function () {

            $ids = explode(',', Input::get('ids'));
            $isError = false;

            if (!empty($ids) && is_array($ids)) {
                $isError = true;
            }

            if ($isError === true) {

                $personnel = PersonnelModel::select(['id', 'full_name', 'photo'])
                    ->whereIn('id', $ids)
                    ->get();

                if ($personnel === null) {

                    $isError = false;
                } else {

                    Registry::set('__ids__multi_select_profile__', $personnel);
                }
            }

            if (!$isError) {

                return ['error' => __('no_permits', null, 'patos')];
            }

            return null;
        });

        $this->validator->end();

        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
    }
}
