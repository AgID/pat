<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Model\AssignmentsModel;
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
    public function validateUriSegmentId(?bool $checkOwner = null): array
    {
        $this->validator->label('ID Liquidazione')
            ->value(uri()->segment(4, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () use ($checkOwner) {

                //Recupero la liquidazione con l'id passato in input
                $check = AssignmentsModel::where('typology', 'liquidation')
                    ->where('id', uri()->segment(4, 0))
                    ->with(['related_assignment' => function ($query) {
                        $query->select(['id', 'name', 'object', 'assignment_start', 'assignment_end']);
                    }])
                    ->first();

                //Se non esiste l'esito con questo id, oppure l'utente non può modificare i record non creati da lui
                //mostro un messaggio di errore
                if ($check == null || (!empty($checkOwner) && !checkRecordOwner(@$check->owner_id))) {

                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id_2', null, 'patos'), 'liquidazione ')
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
    public function check(string $mode = 'insert'): array
    {
        return $this->validate($mode);
    }

    /**
     * Metodo per la validazione dei campi del form
     *
     * @param string $mode Tipo di operazione
     * @return array
     * @throws Exception
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    protected function validate(string $mode = 'insert'): array
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

            $this->validator->label('id')
                ->value(Input::post('id'))
                ->required()
                ->isInt()
                ->isNaturalNoZero()
                ->add(function () {
                    //Recupero la liquidazione da aggiornare
                    $liquidation = AssignmentsModel::where('id', Input::post('id'))->first();

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
