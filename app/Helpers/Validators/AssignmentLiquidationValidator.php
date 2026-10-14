<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Model\AssignmentsModel;
use Scope\DeletedScope;
use System\Input;
use System\Registry;
use System\Validator;

/**
 * Validator per l'oggetto Liquidazione(object_assignments)
 */
class AssignmentLiquidationValidator
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
     * Controlla la validità dell'ID nell'URI segment e se esiste una liquidazione con quell'ID per l'ente
     *
     * @param bool|null $checkOwner Indica se si deve controllare se l'utente è il creatore del record
     * @return array
     * @throws Exception
     */
    public function validateUriSegmentId($checkOwner = null, $uriSegment = 4, $institutionId = null, $userId = null): array
    {
        $this->validator->label('ID Liquidazione')
            ->value(uri()->segment($uriSegment, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () use ($checkOwner, $uriSegment, $institutionId, $userId) {

                //Recupero la liquidazione con l'id passato in input
                $check = AssignmentsModel::where('typology', 'liquidation');
                $check->with('structure:id,structure_name');
                $check->where('id', uri()->segment($uriSegment, 0));
                $check->with(['related_assignment' => function ($query) {
                    $query->select(['id', 'name', 'object', 'assignment_start', 'assignment_end']);
                }]);
                $check->with([
                    'created_by' => function ($query) {
                        $query->withoutGlobalScopes([DeletedScope::class]);
                        $query->select(['id', 'name', 'deleted']);
                    }
                ]);

                $check = $check->first();

                if ($check == null || (!empty($checkOwner) && !checkRecordOwner(@$check->owner_id, $userId))) {
                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id', null, 'patos'), 'bando di concorso ')
                    ];
                }

                //Se esiste la liquidazione la salvo nel registro
                Registry::set('assignment_liquidation', $check);

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
     * @param string $mode Tipo di operazione
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
     * @param string $mode Tipo di operazione
     * @return array
     * @throws Exception
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    protected function validate(string $mode, int $institutionId): array
    {

        $this->validator->verifyToken()
            ->end();

        $this->validator->label('Incarico relativo')
            ->value(Input::post('related_assignment_id'))
            ->required()
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        $this->validator->label('Compenso erogato')
            ->value(Input::post('compensation_provided'))
            ->required()
            ->isNumeric()
            ->end();

        $this->validator->label('Anno di liquidazione')
            ->value(Input::post('liquidation_year'))
            ->required()
            ->regex('/^\d{4}$/')
            ->end();

        $this->validator->label('Data di riferimento')
            ->value(Input::post('liquidation_date'))
            ->required()
            ->isDate('Y-m-d')
            ->end();

        $this->validator->label('Note')
            ->value(Input::post('notes'))
            ->betweenString(4, 1000)
            ->end();






        if ($mode === 'update') {

            $id = Input::post('id');


            $this->validator->label('id')
                ->value($id)
                ->required()
                ->isInt()
                ->isNaturalNoZero()
                ->add(function () use ($id, $institutionId) {

                    //Recupero la liquidazione da aggiornare
                    $query = AssignmentsModel::where('id', $id);

                    $liquidation = $query->first();


                    //Se non esiste mostro un messaggio di errore
                    if (empty($liquidation)) {
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
