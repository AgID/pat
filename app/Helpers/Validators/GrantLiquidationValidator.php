<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Model\GrantsModel;
use System\Input;
use System\Registry;
use System\Validator;

/**
 * Validator per l'oggetto Liquidazione (object_grants)
 */
class GrantLiquidationValidator
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
     * Controlla la validità dell'ID nell'URI segment e se esiste una liquidazione con quell'ID per l'ente
     *
     * @param null $checkOwner Indica se controllare il proprietario del record
     * @return array
     * @throws Exception
     */
    public function validateUriSegmentId($checkOwner = null): array
    {
        $this->validator->label('ID liquidazione')
            ->value(uri()->segment(4, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () use ($checkOwner) {

                //Recupero la liquidazione con l'id passato in input
                $check = GrantsModel::where('id', uri()->segment(4, 0));

                $check = $check->first();

                //Se non esiste l'esito con questo id, oppure l'utente non può modificare i record non creati da lui
                //mostro un messaggio di errore
                if ($check == null || (!empty($checkOwner) && !checkRecordOwner(@$check->owner_id))) {

                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id_2', null, 'patos'), 'liquidazione ')
                    ];
                }

                //Se esiste la liquidazione la salvo nel registro
                Registry::set('grant_liquidation', $check);

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

        $this->validator->label('Procedura relativa')
            ->value(Input::post('grant_id'))
            ->required()
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        $this->validator->label('Importo del vantaggio economico corrisposto')
            ->value(Input::post('compensation_paid'))
            ->required()
            ->isNumeric()
            ->end();

        $this->validator->label('Anno di liquidazione')
            ->value(Input::post('compensation_paid_date'))
            ->required()
            ->regex('/^\d{4}$/')
            ->end();

        $this->validator->label('Data di riferimento')
            ->value(Input::post('reference_date'))
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
                    $grant = GrantsModel::where('id', Input::post('id'))->first();
                    if (empty($grant)) {
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
