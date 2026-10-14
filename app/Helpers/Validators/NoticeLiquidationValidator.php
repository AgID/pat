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
 * @description Validator per l'oggetto Liquidazione (object_contests_acts)
 */
class NoticeLiquidationValidator
{
    public $validator;

    public function __construct()
    {
        $this->validator = new Validator();
    }

    /**
     * Controlla la validità dell'ID nell'URI segment e se esiste una liquidazione con quell'ID per l'ente
     *
     * @return array
     * @throws Exception
     */
    public function validateUriSegmentId($checkOwner = null, $uriSegment = 4, $institutionId = null, $userId = null): array
    {
       
        $this->validator->label('ID liquidazione')
            ->value(uri()->segment($uriSegment, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () use ($checkOwner, $uriSegment, $institutionId, $userId) {

                //Recupero la liquidazione con l'id passato in input
                $check = ContestsActsModel::where('typology', 'liquidation');
                $check->where('id', uri()->segment($uriSegment, 0));
                $check->with([
                    'relative_procedure' => function ($query) {
                        $query->select([
                            'id',
                            'object',
                            'cig',
                            'type',
                            'typology',
                            'relative_notice_id',
                            'updated_at',
                            'contraent_choice',
                            'anac_year',
                            'adjudicator_name',
                            'adjudicator_data',
                            'award_amount_value'
                        ]);
                        $query->with([
                            'relative_notice' => function ($query) {
                                $query->select([
                                    'object_contests_acts.id',
                                    'object',
                                    'cig',
                                    'relative_notice_id',
                                    'object_structures_id',
                                    'contraent_choice',
                                    'adjudicator_data',
                                    'adjudicator_name',
                                    'anac_year',
                                    'typology',
                                    'updated_at'
                                ]);
                            }
                        ]);
                        $query->with(['created_by' => function ($query) {
                            $query->withoutGlobalScopes([DeletedScope::class]);
                            $query->select(['id', 'name', 'deleted']);
                        }]);
                    }
                ]);

                $check->with('structure:id,structure_name');

                $check = $check->first();
          
                if ($check == null || (!empty($checkOwner) && !checkRecordOwner(@$check->owner_id, $userId))) {
                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id', null, 'patos'), 'bando di concorso ')
                    ];
                }

                //Se esiste la liquidazione la salvo nel registro
                Registry::set('notice_liquidation', $check);

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


        $this->validator->label('Procedura relativa')
            ->value(Input::post('relative_procedure_id'))
            ->required()
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        $this->validator->label('Oggetto')
            ->value(Input::post('object'))
            ->required()
            ->betweenString(1, 250)
            ->end();

        $this->validator->label('Valore Importo liquidato')
            ->value(Input::post('amount_liquidated'))
            ->isNumeric()
            ->required()
            ->end();

        $this->validator->label('ANAC - Anno di riferimento')
            ->value(Input::post('anac_year'))
            ->required()
            ->regex('/^\d{4}$/')
            ->end();

        $this->validator->label('Data della liquidazione')
            ->value(Input::post('activation_date'))
            ->isDate('Y-m-d')
            ->required()
            ->end();

        $this->validator->label('Note')
            ->value(Input::post('details'))
            ->betweenString(2, 1000)
            ->end();





        if ($mode === 'update') {

            $id = Input::post('id');

            $this->validator->label('identificativo')
                ->value($id)
                ->required()
                ->isInt()
                ->isNaturalNoZero()
                ->add(function () use ($id, $institutionId) {

                    $query = ContestsActsModel::where('id', $id);

                    $result = $query->first();

                    if (empty($result)) {
                        return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Liquidazione')];
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
