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
 * Validator per l'oggetto Esito di gara (object_contests_act)
 */
class ResultValidator
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
     * Controlla la validità dell'ID nell'URI segment e se esiste un esito di gara con quell'ID per l'ente
     *
     * @param null $checkOwner Indica se controllare il proprietario del record
     * @return array
     * @throws Exception
     */
    public function validateUriSegmentId($checkOwner = null, $uriSegment = 4, $institutionId = null, $userId = null): array
    {

        $this->validator->label('ID esito gara')
            ->value(uri()->segment($uriSegment, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () use ($checkOwner, $uriSegment, $institutionId, $userId) {

                //Recupero l'esito con l'id passato in input
                $check = ContestsActsModel::where('typology', 'result');
                $check->where('id', uri()->segment($uriSegment, 0));
                $check->with('relative_notice:id,object,cig,activation_date,anac_year,expiration_date,relative_notice_id,type,contraent_choice');
                $check->with('relative_measure:id,object,number,date');
                $check->with('proceedings:id,object,cig,activation_date,expiration_date,type');
                $check->with('participants:id,name,vat,type');
                $check->with('awardees:id,name,vat,type');
                $check->with('structure:id,structure_name');
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
                Registry::set('result', $check);

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
     * @param string $mode Indica l'operazione che si sta eseguendo (Insert/Update)
     * @param int $institutionId
     * @return array
     * @throws Exception
     */
    protected function validate(string $mode, int $institutionId): array
    {

        $this->validator->verifyToken()
            ->end();



        $this->validator->label('Bando di gara')
            ->value(Input::post('notice_id'))
            ->required()
            ->isInt()
            ->isNaturalNoZero()
            ->add(function(){
                $db = \System\Container::getInstance()->make('db');
                $notice = $db->table('object_contests_acts')
                    ->select(['id'])
                    ->where('institution_id', checkAlternativeInstitutionId())
                    ->first();

                if($notice===null){
                    return ['error' => 'Bando associato non esistente.'];
                }

                return null;
            })
            ->end();

        $this->validator->label('Oggetto')
            ->value(Input::post('object'))
            ->required()
            ->betweenString(4, 60)
            ->end();

        if (Input::post('procedures')) {
            foreach (explode(',', (string)Input::post('procedures')) as $procedure) {
                $this->validator->label('Altra procedura ' . $procedure)
                    ->value($procedure)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        $this->validator->label('Valore Importo di aggiudicazione')
            ->value(Input::post('award_amount_value'))
            ->isNumeric()
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

        if (Input::post('participants')) {
            foreach (explode(',', (string)Input::post('participants')) as $participant) {
                $this->validator->label('Partecipante alla gara ' . $participant)
                    ->value($participant)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        if (Input::post('awardees')) {
            foreach (explode(',', (string)Input::post('awardees')) as $awardee) {
                $this->validator->label('Aggiudicatario della gara ' . $awardee)
                    ->value($awardee)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }


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

        $this->validator->label('Data di effettivo inizio dei lavori, servizi o forniture')
            ->value(Input::post('work_start_date'))
            ->isDate('Y-m-d')
            ->end();

        $this->validator->label('Data di ultimazione dei lavori, servizi o forniture')
            ->value(Input::post('work_end_date'))
            ->isDate('Y-m-d')
            ->add(function () {
                if (Input::post('work_end_date') < Input::post('work_start_date')) {

                    return ['error' => __('invalid_work_date', null, 'patos')];

                }
                return null;
            })
            ->end();

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

            $id = Input::post(
                'id',
                true,
                true,
                true,
                ['int']
            );

            $this->validator->label('id')
                ->value($id)
                ->required()
                ->isInt()
                ->isNaturalNoZero()
                ->add(function () use ($id, $institutionId) {
                    // $regulation = ContestsActsModel::where('id', Input::post('id'))->first();
                    $query = ContestsActsModel::where('id', $id);
                    $regulation = $query->first();

                    if (empty($regulation)) {
                        return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Esito di gara')];
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
