<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Model\StructuresModel;
use System\Input;
use System\Registry;
use System\Validator;

/**
 * Validator per l'oggetto Struttura Organizzativa (object_structures)
 */
class StructureValidator
{

    public Validator $validator;

    public function __construct()
    {
        $this->validator = new Validator();
    }

    /**
     * Controlla la validità dell'ID nell'URI segment e se esiste una struttura con quell'ID per l'ente
     *
     * @param null $checkOwner Indica se si deve controllare se l'utente ha i permessi per modificare solo i record che ha creato o tutti
     * @return array
     * @throws Exception
     */
    public function validateUriSegmentId($checkOwner = null, $id = 4, $hasApi = true, $userId = null): array
    {
        $this->validator->label('ID struttura')
            ->value(uri()->segment(4, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () use ($checkOwner, $hasApi, $userId) {

                //Recupero la struttura con l'id passato in input
                $check = StructuresModel::where('id', uri()->segment(4, 0))
                    ->with(['responsibles' => function ($query) {
                        $query->select(['object_personnel.id', 'full_name', 'email', 'title', 'role_id', 'role.name']);
                        $query->join('role', 'role.id', '=', 'role_id');
                    }])
                    ->with(['to_contact' => function ($query) {
                        $query->select(['object_personnel.id', 'full_name', 'email', 'title', 'role_id', 'role.name']);
                        $query->join('role', 'role.id', '=', 'role_id');
                    }])
                    ->with('normatives:id')
                    ->with('structure_of_belonging:id,structure_name,reference_email');

                $check = $check->first();

                //Se non esiste l'esito con questo id, oppure l'utente non può modificare i record non creati da lui
                //mostro un messaggio di errore
                if ($check == null || (!empty($checkOwner) && !checkRecordOwner(@$check->owner_id, $userId))) {
                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id_2', null, 'patos'), 'struttura ')
                    ];
                }

                //Se esiste la struttura la salvo nel registro
                Registry::set('structure', $check);

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
     * @param string $mode Indica l'operazione che si sta eseguendo (Insert/Update)
     * @return array
     * @throws Exception
     */
    public function check(string $mode = 'insert'): array
    {
        return $this->validate($mode);
    }

    /**
     * Metodo per la validazione dei campi del form
     *
     * @param string $mode Indica l'operazione che si sta eseguendo (Insert/Update)
     * @return array
     * @throws Exception
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    protected function validate(string $mode = 'insert'): array
    {
        $this->validator->verifyToken()
            ->end();

        $this->validator->label('Ente di appartenenza')
            ->value(Input::post('institute_id'))
            ->required()
            ->isInt()
            ->end();

        $this->validator->label('Nome struttura')
            ->value(Input::post('structure_name'))
            ->required()
            ->betweenString(2, 191)
            ->end();

        $this->validator->label('Responsabile non disponibile')
            ->value(Input::post('responsible_not_available'))
            ->in('0,1')
            ->end();

        if (Input::post('responsible_not_available') === '0') {

            $this->validator->label('Note responsabile non disponibile')
                ->value(Input::post('referent_not_available_txt'))
                ->required()
                ->betweenString(5, 60)
                ->end();
        } else {

            $this->validator->label('Ad interim')
                ->value(Input::post('ad_interim'))
                ->in('0,1')
                ->end();

            if (Input::post('responsibles')) {
                foreach (explode(',', (string)Input::post('responsibles')) as $responsible) {
                    $this->validator->label('Responsabile ' . $responsible)
                        ->value($responsible)
                        ->isInt()
                        ->isNaturalNoZero()
                        ->end();
                }
            } else {
                $this->validator->label('Responsabile')
                    ->value(Input::post('responsibles'))
                    ->required()
                    ->end();
            }
        }

        $this->validator->label('Indirizzo email non disponibile')
            ->value(Input::post('email_not_available'))
            ->in('0,1')
            ->end();

        if (Input::post('email_not_available') === '1') {

            $this->validator->label('Indirizzo email')
                ->value(Input::post('reference_email'))
                ->required()
                ->isEmail()
                ->betweenString(2, 191)
                ->end();
        } else {

            $this->validator->label('Note email non disponibile')
                ->value(Input::post('email_not_available_txt'))
                ->required()
                ->betweenString(4, 60)
                ->end();
        }

        $this->validator->label('Indirizzo email certificata')
            ->value(Input::post('certified_email'))
            ->isEmail()
            ->betweenString(2, 191)
            ->end();

        $this->validator->label('Recapito telefonico')
            ->value(Input::post('phone'))
            ->maxLength(25, __('max_phone_length', null, 'patos'))
            ->minLength(3, __('min_phone_length', null, 'patos'))
            ->regex('/^([0-9().\-+ ]){3,25}$/', __('phone_error', null, 'patos'))
            ->end();

        $this->validator->label('Recapito fax')
            ->value(Input::post('fax'))
            ->betweenString(3, 30)
            ->end();

        $this->validator->label('Descrizione delle attività')
            ->value(Input::post('description'))
            ->required()
            ->betweenString(2, 10000)
            ->end();

        $this->validator->label('Orari al pubblico')
            ->value(Input::post('timetables'))
            ->betweenString(2, 500)
            ->end();

        $this->validator->label('Utilizza in Articolazione degli Uffici')
            ->value(Input::post('articulation'))
            ->in('0,1')
            ->end();

        $this->validator->label('Ordine di visualizzazione')
            ->value(Input::post('order'))
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        $this->validator->label('Struttura con sede')
            ->value(Input::post('based_structure'))
            ->in('0,1')
            ->end();

        $this->validator->label('Indirizzo')
            ->value(Input::post('address'))
            ->betweenString(2, 80)
            ->end();

        $this->validator->label('Dettaglio indirizzo')
            ->value(Input::post('address_detail'))
            ->betweenString(2, 50)
            ->end();

        $this->validator->label('Struttura di appartenenza')
            ->value(Input::post('structure_of_belonging_id'))
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        if (Input::post('toContacts')) {
            foreach (explode(',', (string)Input::post('toContacts')) as $toContact) {
                $this->validator->label('Responsabile da contattare' . $toContact)
                    ->value($toContact)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        if (Input::post('normatives')) {
            foreach (Input::post('normatives') as $normative) {
                $this->validator->label('Normativa ' . $normative)
                    ->value($normative)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        if ($mode === 'update') {

            $this->validator->label('id')
                ->value(Input::post('id'))
                ->required()
                ->isInt()
                ->isNaturalNoZero()
                ->add(function () {
                    $user = StructuresModel::where('id', Input::post('id'))->first();
                    if (empty($user)) {
                        return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Struttura')];
                    }
                })
                ->end();

            $belongingStructure = !empty(Input::post('structure_of_belonging_id')) ? Input::post('structure_of_belonging_id') : '';

            if ($belongingStructure != '') {

                $this->validator->label('Struttura di appartenenza')
                    ->value($belongingStructure)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->add(function () use ($belongingStructure) {
                        if ($belongingStructure == Input::post('id')) {
                            return ['error' => sprintf(__('belong_to_himself', null, 'patos'), '')];
                        }
                    })
                    ->end();
            }

        } else {

            $this->validator->label('Struttura di appartenenza')
                ->value(Input::post('structure_of_belonging_id'))
                ->isInt()
                ->isNaturalNoZero()
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

            if ($isError) {

                $structures = StructuresModel::select(['id', 'structure_name'])
                    ->whereIn('id', $ids)
                    ->get();

                if ($structures === null) {

                    $isError = false;
                } else {

                    Registry::set('__ids__multi_select_profile__', $structures);
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
