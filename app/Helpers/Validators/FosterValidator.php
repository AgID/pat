<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Scope\DeletedScope;
use System\Input;
use System\Registry;
use System\Validator;
use Model\ContestsActsModel;

/**
 * Validator per l'oggetto Esito/Affidamento (object_contests_acts)
 */
class FosterValidator
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
     * Controlla la validità dell'ID nell'URI segment e se esiste un esito/affidamento con quell'ID per l'ente
     *
     * @param null $checkOwner    Indica se controllare il proprietario del record
     * @param int  $uriSegment
     * @param null $institutionId
     * @param null $userId
     * @return array
     * @throws Exception
     */
    public function validateUriSegmentId($checkOwner = null, $uriSegment = 4, $institutionId = null, $userId = null): array
    {
        $this->validator->label('ID esito/affidamento')
            ->value(uri()->segment($uriSegment, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () use ($checkOwner, $uriSegment, $institutionId, $userId) {

                //Recupero l'esito con l'id passato in input
                $check = ContestsActsModel::where('typology', 'foster');
                $check->where('id', uri()->segment($uriSegment, 0));
                $check->with('relative_procedure:id,object,type,cig,activation_date,expiration_date');
                $check->with('relative_measure:id,object,number,date');
                $check->with('participants:id,name,vat,type');
                $check->with('awardees:id,name,vat,type');
                $check->with('requirements');
                $check->with([
                    'structure' => function ($query) {
                        $query->select(['object_structures.id', 'object_structures.structure_name', 'object_structures.reference_email', 'object_structures.structure_of_belonging_id', 'parent.structure_name as parent_name'])
                            ->join('object_structures as parent', 'parent.id', '=', 'object_structures.structure_of_belonging_id', 'left outer');
                    }
                ]);
                $check->with([
                    'rup' => function ($query) {
                        $query->select(['object_personnel.id', 'full_name', 'email', 'title', 'role_id', 'role.name']);
                        $query->join('role', 'role.id', '=', 'object_personnel.role_id');
                    }
                ]);
                $check->with([
                    'public_in' => function ($query) {
                        $query->select(['public_in_id', 'contest_act_id', 'section_fo.name'])
                            ->join('section_fo', 'section_fo.id', '=', 'public_in_id');
                    }
                ]);
                $check->with(['created_by' => function ($query) {
                    $query->withoutGlobalScopes([DeletedScope::class]);
                    $query->select(['id', 'name', 'deleted']);
                }]);

                $check = $check->first();

                if ($check == null || (!empty($checkOwner) && !checkRecordOwner(@$check->owner_id, $userId))) {
                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id', null, 'patos'), 'bando di concorso ')
                    ];
                }

                //Se esiste l'esito lo salvo nel registro
                Registry::set('foster', $check);

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
     * @param string   $mode          Indica l'operazione che si sta eseguendo (Insert/Update)
     * @param int|bool $institutionId indica l'id dell'istituto
     * @return array
     * @throws Exception
     */
    public function check(string $mode = 'insert', int|bool $institutionId = false): array
    {
        return $this->validate($mode, $institutionId);
    }

    /**
     * Metodo per la validazione dei campi del form
     *
     * @param string $mode          Indica l'operazione che si sta eseguendo (Insert/Update)
     * @param int    $institutionId
     * @return array
     * @throws Exception
     * @noinspection PhpInconsistentReturnPointsInspection
     * @noinspection DuplicatedCode
     */
    protected function validate(string $mode, int $institutionId): array
    {
        $this->validator->verifyToken()
            ->end();


        $this->validator->label('ANAC - Anno di riferimento')
            ->value(Input::post('anac_year'))
            ->required()
            ->regex('/^\d{4}$/')
            ->end();

        $this->validator->label('Oggetto')
            ->value(Input::post('object'))
            ->required()
            ->betweenString(1, 1000)
            ->end();

        $this->validator->label('Link alla Banca Dati Nazionale Contratti Pubblici (BDNCP)')
            ->value(Input::post('bdncp_link'))
            ->betweenString(1, 250)
            ->end();

        $this->validator->label('Procedura relativa')
            ->value(Input::post('relative_procedure_id'))
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        $this->validator->label('Codice CIG')
            ->value(Input::post('cig'))
            ->exactLength(10)
            ->isAlphaNum();
        $this->validator = $this->validator ?? null;
        $this->validator = $this->validator ?? null;
        $this->validator->end();

        $this->validator->label('Errore CIG')
            ->value(Input::post('__ignore_cig'))
            ->in('0,1', 'Ops, c\'è un problema. Contattare l\'assistenza!')
            ->end();

        $this->validator->label('Senza importo')
            ->value(Input::post('no_amount'))
            ->in('1,2')
            ->end();

        $this->validator->label('Decreto o determina di affidamento di lavori, servizi e forniture di somma urgenza e di protezione civile (art.163)')
            ->value(Input::post('decree_163'))
            ->in('0,1')
            ->end();

        $this->validator->label('Settore')
            ->value(Input::post('sector'))
            ->in('O-sotto,O-sopra,S,sponsor')
            ->end();

        $this->validator->label('Valore Importo dell\'appalto')
            ->value(Input::post('award_amount_value'))
            ->isNumeric();
        $this->validator = $this->validator ?? null;
        $this->validator = $this->validator ?? null;
        $this->validator->end();

        if (Input::post('public_in')) {
            foreach (Input::post('public_in') as $in) {
                $this->validator->label('Pubblica in ' . $in)
                    ->value($in)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        $this->validator->label('Valore Importo di aggiudicazione')
            ->value(Input::post('asta_base_value'))
            ->isNumeric();
        $this->validator = $this->validator ?? null;
        $this->validator = $this->validator ?? null;
        $this->validator->end();


        if (Input::post('participants')) {
            foreach (explode(',', (string) Input::post('participants')) as $participant) {
                $this->validator->label('Partecipante alla gara ' . $participant)
                    ->value($participant)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        $this->validator = $this->validator ?? null;
        $this->validator = $this->validator ?? null;

        if (Input::post('awardees')) {
            foreach (explode(',', (string) Input::post('awardees')) as $awardee) {
                $this->validator->label('Aggiudicatario della gara ' . $awardee)
                    ->value($awardee)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        $this->validator->label('Procedura di scelta del contraente')
            ->value(Input::post('contraent_choice'))
            ->required()
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        $this->validator->label('Amministrazione aggiudicatrice')
            ->value(Input::post('adjudicator_name'))
            ->required()
            ->betweenString(2, 120)
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

        $this->validator->label('Sede di gara - Provincia')
            ->value(Input::post('province_office'))
            ->isAlpha()
            ->betweenString(2, 2)
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
            ->add(function () use ($activationData) {
                if ($activationData < Input::post('act_date')) {

                    return ['error' => __('invalid_end_date_publication_2', null, 'patos')];

                }
                return null;
            })
            ->end();

        $this->validator->label('Specifica tipologia data di pubblicazione')
            ->value(Input::post('publication_date_type'))
            ->in('data perfezionamento contratto,data perfezionamento adesione ad accordo quadro,data convenzione,data acquisto su MEPA')
            ->end();

        $this->validator->label('Data di effettivo inizio dei lavori, servizi o forniture')
            ->value(Input::post('work_start_date'))
            ->isDate('Y-m-d');
        $this->validator = $this->validator ?? null;
        $this->validator = $this->validator ?? null;
        $this->validator->end();

        $this->validator->label('Data di ultimazione dei lavori, servizi o forniture')
            ->value(Input::post('work_end_date'))
            ->isDate('Y-m-d')
            ->add(function () {
                if (Input::post('work_end_date') < Input::post('work_start_date')) {

                    return ['error' => __('invalid_work_date', null, 'patos')];

                }
                return null;
            });
        $this->validator = $this->validator ?? null;
        $this->validator = $this->validator ?? null;
        $this->validator->end();

        $this->validator->label('Aggiudicazione appalto - Data di pubblicazione sulla G.U.U.E.')
            ->value(Input::post('guue_date'))
            ->isDate('Y-m-d')
            ->add(function () {
                if (Input::post('guue_date') < Input::post('act_date')) {

                    return ['error' => __('invalid_guue_date', null, 'patos')];

                }
                return null;
            })
            ->end();

        $this->validator->label('Aggiudicazione appalto - Data di pubblicazione sulla G.U.R.I.')
            ->value(Input::post('guri_date'))
            ->isDate('Y-m-d')
            ->add(function () {
                if (Input::post('guri_date') < Input::post('act_date')) {

                    return ['error' => __('invalid_guri_date', null, 'patos')];

                }
                return null;
            })
            ->end();

        $this->validator->label('Data di pubblicazione sul sito della Stazione Appaltante')
            ->value(Input::post('contracting_stations_publication_date'))
            ->isDate('Y-m-d')
            ->add(function () {
                if (Input::post('contracting_stations_publication_date') < Input::post('act_date')) {

                    return ['error' => __('invalid_contracting_stations_date', null, 'patos')];

                }
                return null;
            })
            ->end();

        $this->validator->label('Tipologia esito')
            ->value(Input::post('typology_result'))
            ->in('1,2')
            ->end();

        $this->validator->label('RUP')
            ->value(Input::post('object_personnel_id'))
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        if (Input::post('requirements')) {
            foreach (Input::post('requirements') as $requirement) {
                $this->validator->label('Requisito di qualificazione  ' . $requirement)
                    ->value($requirement)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

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
            ->betweenString(2, 8000)
            ->end();

        $this->validator->label('Provvedimento')
            ->value(Input::post('input_measure_id'))
            ->isInt()
            ->isNaturalNoZero()
            ->end();



        if ($mode === 'update') {

            $id = Input::post('id', true, true, true, ['int']);

            $this->validator->label('id')
                ->value($id)
                ->required()
                ->isInt()
                ->isNaturalNoZero()
                ->add(function () use ($id, $institutionId) {

                    $query = ContestsActsModel::where('id', $id);

                    $result = $query->first();

                    if (empty($result)) {
                        return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Esito/Affidamento')];
                    }
                })
                ->end();

        }

        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
    }
}
