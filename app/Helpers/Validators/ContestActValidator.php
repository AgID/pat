<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Model\ContestsActsModel;
use System\Input;
use System\Registry;
use System\Validator;

/**
 * Validator per l'oggetto Bando di gara (    object_contests_acts)
 */
class ContestActValidator
{
    public Validator $validator;

    /**
     * Costruttore
     */
    public function __construct()
    {
        $this->validator = new Validator();
    }

    /**
     * Controlla la validità dell'ID nell'URI segment e se esiste un bando di gara con quell'ID per l'ente
     *
     * @param null $checkOwner Indica se controllare il proprietario del record
     * @return array
     * @throws Exception
     */
    public function validateUriSegmentId($checkOwner = null): array
    {
        $this->validator->label('ID bando di gara')
            ->value(uri()->segment(4, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () use ($checkOwner) {

                //Recupero il bando con l'id passato in input
                $check = ContestsActsModel::where('typology', 'notice')
                    ->where('id', uri()->segment(4, 0))
                    ->with('proceedings:id,object,cig,activation_date,expiration_date,type')
                    ->with('relative_measure:id,object,number,date')
                    ->with('relative_lots:id,object,asta_base_value,cig,relative_notice_id')
                    ->with(['structure' => function ($query) {
                        $query->select(['object_structures.id', 'object_structures.structure_name', 'object_structures.reference_email', 'object_structures.structure_of_belonging_id', 'parent.structure_name as parent_name'])
                            ->join('object_structures as parent', 'parent.id', '=', 'object_structures.structure_of_belonging_id', 'left outer');
                    }])
                    ->with(['rup' => function ($query) {
                        $query->select(['object_personnel.id', 'full_name', 'email', 'title', 'role_id', 'role.name']);
                        $query->join('role', 'role.id', '=', 'object_personnel.role_id');
                    }])
                    ->with('requirements:id')
                    ->with(['public_in' => function ($query) {
                        $query->select(['public_in_id', 'contest_act_id', 'section_fo.name'])
                            ->join('section_fo', 'section_fo.id', '=', 'public_in_id');
                    }])
                    ->first();

                //Se non esiste l'esito con questo id, oppure l'utente non può modificare i record non creati da lui
                //mostro un messaggio di errore
                if ($check == null || (!empty($checkOwner) && !checkRecordOwner(@$check->owner_id))) {
                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id', null, 'patos'), 'bando di gara ')
                    ];
                }

                //Se esiste il bando lo salvo nel registro
                Registry::set('notice', $check);

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
     * @descriotion Metodo per la validazione dei campi del form
     *
     * @param string $mode Operazione che si sta validando
     * @return array
     * @throws Exception
     * @noinspection PhpInconsistentReturnPointsInspection
     * @noinspection DuplicatedCode
     */
    protected function validate(string $mode = 'insert'): array
    {
        $this->validator->verifyToken()
            ->end();

        $this->validator->label('ANAC - Anno di riferimento')
            ->value(Input::post('anac_year'))
            ->required()
            ->regex('/^\d{4}$/')
            ->end();

        $this->validator->label('Contratto')
            ->value(Input::post('contract'))
            ->in('1,2,3')
            ->end();

        $this->validator->label('Oggetto')
            ->value(Input::post('object'))
            ->required()
            ->betweenString(1, 700)
            ->end();

        if (Input::post('cig')) {
            $i = 1;
            foreach (Input::post('cig') as $cig) {
                $this->validator->label('Codice CIG [' . $i++ . ']')
                    ->value($cig)
                    ->exactLength(10)
                    ->isAlphaNum()
                    ->end();
            }
        }

        $this->validator->label('Errore CIG')
            ->value(Input::post('__ignore_cig'))
            ->in('0,1', 'Ops, c\'è un problema. Contattare l\'assistenza!')
            ->end();

        if (!empty($_POST['asta_base_value'])) {
            $i = 1;
            foreach ($_POST['asta_base_value'] as $value) {
                $this->validator->label('Importo dell\'appalto [' . $i++ . ']')
                    ->value($value)
                    ->isNumeric()
                    ->end();
            }
        }

        $this->validator->label('Senza importo')
            ->value(Input::post('no_amount'))
            ->in('1,2')
            ->end();

        if (Input::post('public_in')) {
            foreach (Input::post('public_in') as $in) {
                $this->validator->label('Pubblica in ' . $in)
                    ->value($in)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        $this->validator->label('Settore')
            ->value(Input::post('sector'))
            ->in('O-sotto,O-sopra,S,sponsor')
            ->end();

        $this->validator->label('Procedura di scelta del contraente')
            ->value(Input::post('contraent_choice'))
            ->required()
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        $this->validator->label('Amministrazione aggiudicatrice')
            ->value(Input::post('adjudicator_name'))
            ->required()
            ->betweenString(4, 120)
            ->end();

        $this->validator->label('Codice Fiscale Amministrazione aggiudicatrice')
            ->value(Input::post('adjudicator_data'))
            ->required()
            ->add(function () {
                $fiscalCodeCheck = new FiscalCodeRule(Input::post('adjudicator_data'));
                $vatCheck = new VatRule(Input::post('adjudicator_data'));
                if (!$fiscalCodeCheck->isValidate() && !$vatCheck->isValidate()) {
                    return ['error' => sprintf(__('fiscal_code_error', null, 'patos'), 'Amministrazione aggiudicatrice')];
                }
                return null;
            })
            ->end();

        $administrationTypes = config('administrationType', null, 'app');
        $administrationTypes = implode(',', array_keys($administrationTypes));
        $this->validator->label('Tipo di amministrazione')
            ->value(Input::post('administration_type'))
            ->in($administrationTypes)
            ->end();

        $provinces = config('province_short', null, 'locations');
        $provinces = implode(',', array_keys($provinces));
        $this->validator->label('Sede di gara - Provincia')
            ->value(Input::post('province_office'))
            ->isAlpha()
            ->betweenString(2, 2)
            ->in($provinces)
            ->end();

        $this->validator->label('Sede di gara - Comune')
            ->value(Input::post('municipality_office'))
            ->betweenString(4, 40)
            ->end();

        $this->validator->label('Sede di gara - Indirizzo')
            ->value(Input::post('office_address'))
            ->betweenString(4, 40)
            ->end();

        $this->validator->label('Sede di gara - Codice Istat')
            ->value(Input::post('istat_office'))
            ->regex('/^\d{9}$/')
            ->end();

        $this->validator->label('Sede di gara - Codice NUTS')
            ->value(Input::post('nuts_office'))
            ->maxLength(20)
            ->isAlphaNum()
            ->end();

        $this->validator->label('Ufficio')
            ->value(Input::post('object_structures_id'))
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        $this->validator->label('Data dell\'atto')
            ->value(Input::post('act_date'))
            ->isDate('Y-m-d')
            ->end();
        
        $activationData = !empty(Input::post('activation_date')) ? str_replace('T', ' ', Input::post('activation_date')) : '';

        $this->validator->label('Data di pubblicazione sul sito')
            ->value($activationData)
            ->required()
            ->isDate('Y-m-d H:i')
            ->add(function () use ($activationData){
                if ($activationData < Input::post('act_date')) {

                    return ['error' => __('invalid_end_date_publication_2', null, 'patos')];

                }
                return null;
            })
            ->end();

        $this->validator->label('Data di pubblicazione del bando di gara sulla G.U.U.E.')
            ->value(Input::post('guue_date'))
            ->isDate('Y-m-d')
            ->add(function () {
                if (Input::post('guue_date') < Input::post('act_date')) {

                    return ['error' => __('invalid_guue_date', null, 'patos')];

                }
                return null;
            })
            ->end();

        $this->validator->label('Data di pubblicazione del bando di gara sulla G.U.R.I.')
            ->value(Input::post('guri_date'))
            ->isDate('Y-m-d')
            ->add(function () {
                if (Input::post('guri_date') < Input::post('act_date')) {

                    return ['error' => __('invalid_guri_date', null, 'patos')];

                }
                return null;
            })
            ->end();


        $this->validator->label('Data di scadenza presentazione offerte')
            ->value(Input::post('expiration_date'))
            ->isDate('Y-m-d')
            ->add(function () {
                if (Input::post('expiration_date') <= Input::post('act_date')) {

                    return ['error' => __('invalid_question_date', null, 'patos')];

                }
                return null;
            })
            ->end();

        $this->validator->label('RUP')
            ->value(Input::post('object_personnel_id'))
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        if (Input::post('procedures')) {
            foreach (explode(',', (string)Input::post('procedures')) as $procedure) {
                $this->validator->label('Procedura ' . $procedure)
                    ->value($procedure)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        $this->validator->label('Requisiti di qualificazione')
            ->value(Input::post('requirement'))
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        $this->validator->label('Codice CPV')
            ->value(Input::post('cpv_code_id'))
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        $this->validator->label('URL di Pubblicazione su www.serviziocontrattipubblici.it')
            ->value(Input::post('url_scp'))
            ->isUrl(null, true)
            ->betweenString(4, 60)
            ->end();

        $this->validator->label('Note')
            ->value(Input::post('details'))
            ->betweenString(2, 1000)
            ->end();

        $this->validator->label('Provvedimento')
            ->value(Input::post('input_measure_id'))
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        if ($mode === 'update') {

            $this->validator->label('id')
                ->value(Input::post('id'))
                ->required()
                ->isInt()
                ->isNaturalNoZero()
                ->add(function () {
                    $notice = ContestsActsModel::where('id', Input::post('id'))->first();
                    if (empty($notice)) {
                        return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Bando di gara')];
                    }
                })
                ->end();

            $notice = ContestsActsModel::where('id', Input::post('id'))->first();
            $isMulticig = $notice['is_multicig'];

            // il CIG può essere modificato solo se non è multicig
            if (!$isMulticig) {
                if (Input::post('cig_code')) {
                    $i = 1;
                    foreach (Input::post('cig_code') as $cig) {
                        $this->validator->label('Codice CIG [' . $i++ . ']')
                            ->value($cig)
                            ->exactLength(10)
                            ->isAlphaNum()
                            ->end();
                    }
                }
            }
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

                $contestsAct = ContestsActsModel::select(['id', 'object', 'type', 'typology', 'relative_notice_id', 'relative_procedure_id', 'contraent_choice', 'anac_year', 'updated_at'])
                    ->whereIn('id', $ids)
                    ->get();

                if ($contestsAct === null) {

                    $isError = false;
                } else {

                    Registry::set('__ids__multi_select_profile__', $contestsAct);
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
