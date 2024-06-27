<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Model\NoticesActsModel;
use System\Input;
use System\Registry;
use System\Validator;

/**
 * Validator per l'oggetto Atto amministrativo (object_notices_acts)
 */
class NoticeActValidator
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
     * @description Controlla la validità dell'ID nell'URI segment e se esiste un atto amministrativo con quell'ID per l'ente
     *
     * @param null $checkOwner Indica se controllare il proprietario del record
     * @return array
     * @throws Exception
     */
    public function validateUriSegmentId($checkOwner = null): array
    {
        $this->validator->label('ID atto amministrativo')
            ->value(uri()->segment(4, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () use ($checkOwner) {

                //Recupero l'atto con l'id passato in input
                $notice = NoticesActsModel::where('id', uri()->segment(4, 0))
                    ->with('assignments:id,name,object,assignment_start')
                    ->with(['public_in' => function ($query) {
                        $query->select(['section_fo_id', 'section_fo_config_publication_archive.id', 'section_fo.name'])
                            ->join('section_fo', 'section_fo.id', '=', 'section_fo_id')
                            ->groupBy('section_fo_id');
                    }])
                    ->with('relative_contest_act:id,object,cig,type,activation_date,expiration_date')
                    ->first();

                //Se non esiste l'esito con questo id, oppure l'utente non può modificare i record non creati da lui
                //mostro un messaggio di errore
                if ($notice == null || (!empty($checkOwner) && !checkRecordOwner(@$notice->owner_id))) {

                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id', null, 'patos'), 'atto amministrativo ')
                    ];
                }

                //Se esiste l'atto lo salvo nel registro
                Registry::set('notice_act', $notice);

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

        $requiredRelativeProcedure = true;

        $this->validator->verifyToken()
            ->end();

        $this->validator->label('Data')
            ->value(Input::post('date'))
            ->required()
            ->isDate('Y-m-d')
            ->end();

        $this->validator->label('Oggetto')
            ->value(Input::post('object'))
            ->required()
            ->betweenString(1, 250)
            ->end();

        $this->validator->label('Link alla Banca Dati Nazionale Contratti Pubblici (BDNCP)')
            ->value(Input::post('bdncp_link'))
            ->betweenString(1, 250)
            ->end();

        $this->validator->label('Pubblica in')
            ->value(Input::post('public_in'))
            ->required()
            ->end();

        if (Input::post('public_in')) {
            foreach (Input::post('public_in') as $in) {
                if (count(Input::post('public_in')) === 1 && $in == 117) {
                    $requiredRelativeProcedure = false;
                }

                $this->validator->label('Pubblica in ' . $in)
                    ->value($in)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        if ($requiredRelativeProcedure) {
            foreach (explode(',', (string)Input::post('object_contests_acts_id')) as $procedure) {
                $this->validator->label('Procedura ' . $procedure)
                    ->value($procedure)
                    ->required()
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        } else {
            $this->validator->label('Procedura relativa')
                ->value(Input::post('object_contests_acts_id'))
                ->isInt()
                ->isNaturalNoZero()
                ->end();
        }

        if (Input::post('assignments')) {
            foreach (explode(',', (string)Input::post('assignments')) as $commission) {
                $this->validator->label('Commissione giudicatrice ' . $commission)
                    ->value($commission)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        if (!empty(Input::post('public_in')) && in_array(531, Input::post('public_in'))) {
            $this->validator->label('Data avvio progetti')
                ->value(Input::post('projects_start_date'))
                ->isDate('Y-m-d')
                ->end();

            $this->validator->label('CUP')
                ->value(Input::post('cup'))
                ->betweenString(1, 191)
                ->end();

            $this->validator->label('Stato di attuazione finanziario e procedurale')
                ->value(Input::post('implementation_state'))
                ->betweenString(1, 255)
                ->end();

            $this->validator->label('Fonti finanziarie')
                ->value(Input::post('financial_sources'))
                ->betweenString(1, 255)
                ->end();

            $this->validator->label('Importo totale del finanziamento')
                ->value(Input::post('total_fin_amount'))
                ->isNumeric()
                ->end();
        }

        $this->validator->label('Note')
            ->value(Input::post('details'))
            ->betweenString(2, 1000)
            ->end();

        if ($mode === 'update') {

            $this->validator->label('id')
                ->value(Input::post('id'))
                ->required()
                ->isInt()
                ->isNaturalNoZero()
                ->add(function () {
                    $lot = NoticesActsModel::where('id', Input::post('id'))->first();
                    if (empty($lot)) {
                        return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Atto amministrativo')];
                    }
                })
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

                $noticesAct = NoticesActsModel::select(['id', 'object'])
                    ->whereIn('id', $ids)
                    ->get();

                if ($noticesAct === null) {

                    $isError = false;
                } else {

                    Registry::set('__ids__multi_select_profile__', $noticesAct);
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