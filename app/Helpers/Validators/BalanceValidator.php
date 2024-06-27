<?php

/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Model\BalanceSheetsModel;
use System\Input;
use System\Registry;
use System\Validator;

/**
 * Validator per l'oggetto Bilancio (object_balance_sheets)
 */
class BalanceValidator
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
     * Controlla la validità dell'ID nell'URI segment e se esiste un bilancio con quell'ID per l'ente
     *
     * @param null $checkOwner Indica se controllare il proprietario del record
     * @return array
     * @throws Exception
     */
    public function validateUriSegmentId($checkOwner = null): array
    {
        $this->validator->label('ID bilancio')
            ->value(uri()->segment(4, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () use ($checkOwner) {

                //Recupero il bilancio con l'id passato in input
                $check = BalanceSheetsModel::select()
                    ->with(['related_measure' => function ($query) {
                        $query->select(['id', 'object', 'number', 'date']);
                    }])
                    ->find(uri()->segment(4, 0));

                //Se non esiste l'esito con questo id, oppure l'utente non può modificare i record non creati da lui
                //mostro un messaggio di errore
                if ($check == null || (!empty($checkOwner) && !checkRecordOwner(@$check->owner_id))) {

                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id', null, 'patos'), 'bilancio ')
                    ];
                }

                //Se esiste il bilancio lo salvo nel registro
                Registry::set('balance', $check);

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
     */
    protected function validate(string $mode = 'insert'): array
    {

        $this->validator->verifyToken()
            ->end();

        $allowedTypologies = 'bilancio preventivo,bilancio consuntivo,piano indicatori e risultati,variazioni di bilancio';
//        trace($allowedTypologies, true);
        $this->validator->label('Tipologia')
            ->value(Input::post('typology'))
            ->required()
            ->in($allowedTypologies)
            ->end();

        $this->validator->label('Nome')
            ->value(Input::post('name'))
            ->required()
            ->betweenString(4, 60)
            ->end();

        $this->validator->label('Anno')
            ->value(Input::post('year'))
            ->required()
            ->regex('/^\d{4}$/')
            ->end();

        $this->validator->label('Descrizione')
            ->value(Input::post('description'))
            ->betweenString(4, 1000)
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
                    $regulation = BalanceSheetsModel::where('id', Input::post('id'))->first();
                    if (empty($regulation)) {
                        return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Bilancio')];
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

                $balances = BalanceSheetsModel::select(['id', 'name'])
                    ->whereIn('id', $ids)
                    ->get();

                if ($balances === null) {

                    $isError = false;
                } else {

                    Registry::set('__ids__multi_select_profile__', $balances);
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
