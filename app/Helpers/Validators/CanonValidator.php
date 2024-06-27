<?php

/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Model\LeaseCanonsModel;
use System\Input;
use System\Registry;
use System\Validator;

/**
 * Validator per l'oggetto Canone di locazione (object_lease_canons)
 */
class CanonValidator
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
     * Controlla la validità dell'ID nell'URI segment e se esiste un canone con quell'ID per l'ente
     *
     * @param null $checkOwner Indica se controllare il proprietario del record
     * @return array
     * @throws Exception
     */
    public function validateUriSegmentId($checkOwner = null): array
    {
        $this->validator->label('ID canone')
            ->value(uri()->segment(4, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () use ($checkOwner) {

                //Recupero il canone con l'id passato in input
                $check = LeaseCanonsModel::where('id', uri()->segment(4, 0))
                    ->with('properties:id')
                    ->with(['structure' => function ($query) {
                        $query->select(['object_structures.id', 'object_structures.structure_name', 'object_structures.structure_of_belonging_id', 'object_structures.reference_email', 'parent.structure_name as parent_name'])
                            ->join('object_structures as parent', 'parent.id', '=', 'object_structures.structure_of_belonging_id', 'left outer');
                    }])
                    ->first();

                //Se non esiste l'esito con questo id, oppure l'utente non può modificare i record non creati da lui
                //mostro un messaggio di errore
                if ($check == null || (!empty($checkOwner) && !checkRecordOwner(@$check->owner_id))) {

                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id', null, 'patos'), 'canone ')
                    ];
                }

                //Se esiste il canone lo salvo nel registro
                Registry::set('canon', $check);

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

        $this->validator->label('Tipo canone')
            ->value(Input::post('canon_type'))
            ->required()
            ->in('1,2')
            ->end();

        if (Input::post('canon_type') == 1) {
            $this->validator->label('Informazioni sul beneficiario')
                ->value(Input::post('beneficiary'))
                ->required()
                ->betweenString(5, 60)
                ->end();

            $this->validator->label('Partita IVA / Cod. fisc. beneficiario')
                ->value(Input::post('fiscal_code'))
                ->add(function () {
                    $fiscalCodeCheck = new FiscalCodeRule(Input::post('fiscal_code'));
                    $vatCheck = new VatRule(Input::post('fiscal_code'));

                    if (!$fiscalCodeCheck->isValidate() && !$vatCheck->isValidate()) {

                        return ['error' => __('fiscal_code_vat_error', null, 'patos')];
                    }

                    return null;
                })
                ->end();
        }

        $this->validator->label('Importo')
            ->value(Input::post('amount'))
            ->required()
            ->isNumeric()
            ->end();

        $this->validator->label('Estremi del contratto')
            ->value(Input::post('contract_statements'))
            ->betweenString(5, 60)
            ->end();

        $this->validator->label('Data inizio')
            ->value(Input::post('start_date'))
            ->required()
            ->isDate('Y-m-d')
            ->end();

        $this->validator->label('Data fine')
            ->value(Input::post('end_date'))
            ->required()
            ->isDate('Y-m-d')
            ->add(function () {
                if (Input::post('end_date') < Input::post('start_date')) {

                    return ['error' => __('invalid_end_date', null, 'patos')];

                }
                return null;
            })
            ->end();

        if (Input::post('properties')) {
            foreach (Input::post('properties') as $property) {
                $this->validator->label('Immobile ' . $property)
                    ->value($property)
                    ->required()
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        } else {

            $this->validator->label('Immobile')
                ->value(Input::post('properties'))
                ->required()
                ->end();
        }

        $this->validator->label('Ufficio referente per il contratto')
            ->value(Input::post('object_structures_id'))
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        $this->validator->label('Estremi del contratto')
            ->value(Input::post('notes'))
            ->betweenString(2, 1000)
            ->end();


        if ($mode === 'update') {

            $this->validator->label('id')
                ->value(Input::post('id'))
                ->required()
                ->isInt()
                ->isNaturalNoZero()
                ->add(function () {
                    $company = LeaseCanonsModel::where('id', Input::post('id'))->first();
                    if (empty($company)) {
                        return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Canone di locazione')];
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

                $canons = LeaseCanonsModel::select(['id', 'beneficiary'])
                    ->whereIn('id', $ids)
                    ->get();

                if ($canons === null) {

                    $isError = false;
                } else {

                    Registry::set('__ids__multi_select_profile__', $canons);
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
