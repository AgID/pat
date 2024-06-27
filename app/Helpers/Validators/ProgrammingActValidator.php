<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Model\ProgrammingActsModel;
use System\Input;
use System\Registry;
use System\Validator;

/**
 * Validator per l'oggetto Atto di programmazione (object_programming_acts)
 */
class ProgrammingActValidator
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
     * Controlla la validità dell'ID nell'URI segment e se esiste un atto di programmazione con quell'ID per l'ente
     *
     * @param null $checkOwner Indica se controllare il proprietario del record
     * @return array
     * @throws Exception
     */
    public function validateUriSegmentId($checkOwner = null): array
    {
        $this->validator->label('ID atto di programmazione')
            ->value(uri()->segment(4, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () use ($checkOwner) {

                //Recupero l'atto con l'id passato in input
                $check = ProgrammingActsModel::where('id', uri()->segment(4, 0))
                    ->first();

                //Se non esiste l'esito con questo id, oppure l'utente non può modificare i record non creati da lui
                //mostro un messaggio di errore
                if ($check == null || (!empty($checkOwner) && !checkRecordOwner(@$check->owner_id))) {

                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id', null, 'patos'), 'atto di programmazione ')
                    ];
                }

                //Se esiste l'atto lo salvo nel registro
                Registry::set('programming_act', $check);

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

        $this->validator->label('Oggetto')
            ->value(Input::post('object'))
            ->required()
            ->betweenString(4, 120)
            ->end();

        $this->validator->label('Data')
            ->value(Input::post('date'))
            ->required()
            ->isDate('Y-m-d')
            ->end();

        $this->validator->label('Tipo atto')
            ->value(Input::post('act_type'))
            ->required()
            ->in('1,2,3')
            ->end();

        $this->validator->label('Pubblica in "Opere pubbliche - Documenti di programmazione"')
            ->value(Input::post('public_in_public_works'))
            ->in('0,1')
            ->end();

        $this->validator->label('Descrizione')
            ->value(Input::post('description'))
            ->betweenString(5, 1000)
            ->end();

        if ($mode === 'update') {

            $this->validator->label('id')
                ->value(Input::post('id'))
                ->required()
                ->isInt()
                ->isNaturalNoZero()
                ->add(function () {
                    $regulation = ProgrammingActsModel::where('id', Input::post('id'))->first();
                    if (empty($regulation)) {
                        return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Atto di programmazione')];
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

            if ($isError === true) {

                $programmingActs = ProgrammingActsModel::select(['id', 'object'])
                    ->whereIn('id', $ids)
                    ->get();

                if ($programmingActs === null) {

                    $isError = false;
                } else {

                    Registry::set('__ids__multi_select_profile__', $programmingActs);
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
