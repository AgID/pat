<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

 /** @noinspection PhpInconsistentReturnPointsInspection */



namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Model\PaymentModel;
use Model\SupplieListModel;
use Scope\DeletedScope;
use System\Input;
use System\Registry;
use System\Validator;

/**
 * Validator per l'oggetto Bilancio (object_balance_sheets)
 */
class PaymentValidator
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
    public function validateUriSegmentId($checkOwner = null, $uriSegment = 4, $institutionId = null, $userId = null): array
    {
        $this->validator->label('ID pagamento')
            ->value(uri()->segment($uriSegment, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () use ($checkOwner, $uriSegment, $institutionId, $userId) {

                //Recupero il bilancio con l'id passato in input
                $check = PaymentModel::select();
                $check->with('structure:id,structure_name')
                    ->with(['created_by' => function ($query) {
                        $query->withoutGlobalScopes([DeletedScope::class]);
                        $query->select(['id', 'name', 'deleted']);
                    }]);

                $check = $check->find(uri()->segment($uriSegment, 0));

                //Se non esiste l'esito con questo id, oppure l'utente non può modificare i record non creati da lui
                //mostro un messaggio di errore
                if ($check == null || (!empty($checkOwner) && !checkRecordOwner(@$check->owner_id))) {

                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id', null, 'patos'), 'pagamento ')
                    ];
                }

                //Se esiste il bilancio lo salvo nel registro
                Registry::set('payment', $check);

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
    public function check(string $mode = 'insert', int|bool $institutionId = false): array
    {
        return $this->validate($mode, $institutionId);
    }

    /**
     * Metodo per la validazione dei campi del form
     *
     * @param string $mode Indica l'operazione che si sta eseguendo (Insert/Update)
     * @return array
     * @throws Exception
     */
    protected function validate(string $mode, int $institutionId): array
    {

        $this->validator->verifyToken()
            ->end();

        $this->validator->label('Oggetto')
            ->value(Input::post('payment_object'))
            ->betweenString(4, 1000)
            ->end();

        $this->validator->label('Anno')
            ->value(Input::post('payment_year'))
            ->required()
            ->regex('/^\d{4}$/')
            ->end();

        $this->validator->label('Trimestre')
            ->value(Input::post('payment_trimester'))
            ->required()
            ->in('1,2,3,4')
            ->end();

        $this->validator->label('Importo')
            ->value(Input::post('payment_compensation'))
            ->required()
            ->isNumeric()
            ->end();

        $this->validator->label('Categoria di spesa')
            ->value(Input::post('payment_category'))
            ->required()
            ->end();

        $isTypologySelected = !empty(Input::post('payment_typology1')) || !empty(Input::post('payment_typology2'));

        if (!$isTypologySelected) {
            $this->validator->label('Tipologia di spesa')
                ->value(null)
                ->required()
                ->end();
        }

        $this->validator->label('Beneficiario')
            ->value(Input::post('payment_supplier'))
            ->required()
            ->isNaturalNoZero()
            ->in('515,516,517')
            ->end();



        if ($mode === 'update') {

            $id = Input::post('id');

            $this->validator->label('id')
                ->value($id)
                ->required()
                ->isInt()
                ->isNaturalNoZero()
                ->add(function () use ($id, $institutionId) {

                    $query = PaymentModel::where('id', $id);

                    $payment = $query->first();

                    if (empty($payment)) {
                        return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Pagamento')];
                    }

                    return null;
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

                $balances = PaymentModel::select(['id', 'object'])
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
