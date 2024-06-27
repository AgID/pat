<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Model\SupplieListModel;
use Scope\InstitutionScope;
use System\Input;
use System\Registry;
use System\Validator;

/**
 * Validator per l'oggetto Elenco partecipanti/aggiudicatari (object_supplie_list)
 */
class SupplierValidator
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
     * Controlla la validità dell'ID nell'URI segment e se esiste un fornitore con quell'ID per l'ente
     *
     * @param null $checkOwner Indica se controllare il proprietario del record
     * @return array
     * @throws Exception
     */
    public function validateUriSegmentId($checkOwner = null, $uriSegment = 4, $hasApi = false, $institutionId = null, $userId = null): array
    {

        $this->validator->label('ID fornitore')
            ->value(uri()->segment($uriSegment, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () use ($checkOwner, $uriSegment, $hasApi, $institutionId, $userId) {

                //Recupero il fornitore con l'id passato in input
                $check = SupplieListModel::where('id', uri()->segment($uriSegment, 0))
                    ->with('group_leaders:id,name,type,vat')
                    ->with('principals:id,name,type,vat')
                    ->with('mandatarie:id,name,type,vat')
                    ->with('associates:id,name,type,vat')
                    ->with('consortiums:id,name,type,vat');

                if ($hasApi && $institutionId !== null) {
                    $check->withoutGlobalScopes([InstitutionScope::class]);
                    $check->where('institution_id', '=', $institutionId);
                }

                $check = $check->first();

                //Se non esiste l'esito con questo id, oppure l'utente non può modificare i record non creati da lui
                //mostro un messaggio di errore
                if ($check == null || (!empty($checkOwner) && !checkRecordOwner(@$check->owner_id, $userId))) {
                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id', null, 'patos'), 'fornitore ')
                    ];
                }

                //Se esiste il fornitore lo salvo nel registro
                Registry::set('supplier', $check);

                return null;
            })
            ->end();


        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => !$hasApi ?
                $this->validator->getErrorsHtml() :
                $this->validator->getErrors()
        ];
    }

    /**
     *  Metodo che esegue la validazione dei campi del form
     *
     * @param string $mode Indica l'operazione
     * @param bool $hasApi Indica se la validazione avviene da una richiesta ajax
     * @param int|bool $institutionId indica l'id dell'istituto
     * @return array
     * @throws Exception
     */
    public function check(string $mode = 'insert', bool $hasApi = false, int|bool $institutionId = false): array
    {
        return $this->validate($mode, $hasApi, $institutionId);
    }


    /**
     *  Metodo per la validazione dei campi del form
     *
     * @param string $mode Indica l'operazione
     * @param bool $hasApi
     * @param int $institutionId
     * @return array
     * @throws Exception
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    protected function validate(string $mode, bool $hasApi, int $institutionId): array
    {
        if (!$hasApi) {
            $this->validator->verifyToken()
                ->end();
        }

        if ($mode === 'update') {

            $id = $hasApi ? uri()->segment(4, 0) : Input::post('id');

            $this->validator->label('id')
                ->value($id)
                ->required()
                ->isNaturalNoZero()
                ->add(function () use ($id) {
                    $regulation = SupplieListModel::where('id', $id)->first();

                    if (empty($regulation)) {
                        return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Fornitore')];
                    }
                })
                ->end();

            if (Input::post('typology') == '1') {
                //italiano
                if (Input::post('supplier_typology') == 0) {
                    $this->validator->label('Codice fiscale')
                        ->value(Input::post('vat'))
                        ->required()
                        ->add(function () use ($hasApi, $id, $institutionId) {

                            $query = SupplieListModel::where('id', '!=', $id)
                                ->where('vat', Input::post('vat'))
                                ->where('institution_id', $institutionId);

                            if ($hasApi) {
                                $query->withoutGlobalScope(InstitutionScope::class);
                            }

                            $vatExist = $query->first();

                            if (!empty($vatExist)) {
                                return ['error' => __('fiscal_code_exist', null, 'patos')];
                            }
                            return null;
                        })
                        ->end();
                } else {
                    $this->validator->label('Identificativo fiscale estero')
                        ->value(Input::post('foreign_tax_identification'))
                        ->required()
                        ->add(function () use ($hasApi, $id, $institutionId) {

                            $query = SupplieListModel::where('id', '!=', $id)
                                ->where('foreign_tax_identification', Input::post('foreign_tax_identification'))
                                ->where('institution_id', $institutionId);
                            if ($hasApi) {
                                $query->withoutGlobalScope(InstitutionScope::class);
                            }

                            $foreignTaxExist = $query->first();

                            if (!empty($foreignTaxExist)) {
                                return ['error' => __('foreign_tax_identification_exist', null, 'patos')];
                            }
                            return null;
                        })
                        ->end();
                }
            }
        }

        $this->validator->label('Tipologia fornitore')
            ->value(Input::post('typology'))
            ->required()
            ->in('1,2')
            ->end();

        $this->validator->label('Tipologia fornitore')
            ->value(Input::post('supplier_typology'))
            ->required()
            ->in('0,1')
            ->end();

        /* Per Fornitore Singolo */
        if (Input::post('typology') == '1') {

            $this->validator->label('Nominativo e ragione sociale')
                ->value(Input::post('name'))
                ->required()
                ->betweenString(2, 150)
                ->end();

            if ($mode === 'insert') {

                //italiano
                if (Input::post('supplier_typology') == 0) {
                    $this->validator->label('Codice fiscale')
                        ->value(Input::post('vat'))
                        ->required()
                        ->add(function () use ($hasApi, $institutionId) {
                            $vatCheck = new VatRule(Input::post('vat'));
                            if (!$vatCheck->isValidate()) {
                                return ['error' => sprintf(__('fiscal_code_error', null, 'patos'), ' fornitore')];
                            } else {

                                $query = SupplieListModel::where('vat', Input::post('vat'))
                                    ->where('institution_id', $institutionId);

                                if ($hasApi) {
                                    $query->withoutGlobalScope(InstitutionScope::class);
                                }

                                $vatExist = $query->first();

                                if (!empty($vatExist)) {
                                    return ['error' => __('fiscal_code_exist', null, 'patos')];
                                }
                            }
                            return null;
                        })
                        ->end();
                } else {
                    //estero
                    $this->validator->label('Identificativo fiscale estero')
                        ->value(Input::post('foreign_tax_identification'))
                        ->required()
                        ->add(function () use ($hasApi, $institutionId) {

                            $query = SupplieListModel::where('foreign_tax_identification', Input::post('foreign_tax_identification'))
                                ->where('institution_id', $institutionId);

                            if ($hasApi) {
                                $foreignTaxExist = $query->first();
                            }

                            if (!empty($foreignTaxExist)) {
                                return ['error' => __('foreign_tax_identification_exist', null, 'patos')];
                            }
                            return null;
                        })
                        ->end();
                }

            }

            $this->validator->label('Indirizzo sede')
                ->value(Input::post('address'))
                ->betweenString(2, 60)
                ->end();

            $this->validator->label('Email')
                ->value(Input::post('email'))
                ->isEmail()
                ->betweenString(2, 60)
                ->end();

            $this->validator->label('Recapito telefonico')
                ->value(Input::post('phone'))
                ->maxLength(17, __('max_phone_length', null, 'patos'))
                ->minLength(3, __('min_phone_length', null, 'patos'))
                ->regex('/^([0-9().\-+ ]){3,17}$/', __('phone_error', null, 'patos'))
                ->end();

            $this->validator->label('Recapito fax')
                ->value(Input::post('fax'))
                ->betweenString(3, 30)
                ->end();
        } else {

            /* Per Raggruppamento */
            $this->validator->label('Nominativo del raggruppamento')
                ->value(Input::post('name'))
                ->required()
                ->betweenString(2, 150)
                ->end();

            $flagMinComp = [
                'flag_group_leaders' => false,
                'flag_principals' => false,
                'flag_mandatarie' => false,
                'flag_associates' => false,
                'flag_consortiums' => false
            ];

            if (Input::post('group_leaders')) {
                $flagMinComp['flag_group_leaders'] = true;
                foreach (explode(',', (string)Input::post('group_leaders')) as $groupLeader) {
                    $this->validator->label('Capogruppo ' . $groupLeader)
                        ->value($groupLeader)
                        ->isInt()
                        ->isNaturalNoZero()
                        ->end();
                }
            }

            if (Input::post('principals')) {
                $flagMinComp['flag_principals'] = true;
                foreach (explode(',', (string)Input::post('principals')) as $principal) {
                    $this->validator->label('Mandante ' . $principal)
                        ->value($principal)
                        ->isInt()
                        ->isNaturalNoZero()
                        ->end();
                }
            }

            if (Input::post('mandatarie')) {
                $flagMinComp['flag_mandatarie'] = true;
                foreach (explode(',', (string)Input::post('mandatarie')) as $mandatary) {
                    $this->validator->label('Mandataria ' . $mandatary)
                        ->value($mandatary)
                        ->isInt()
                        ->isNaturalNoZero()
                        ->end();
                }
            }

            if (Input::post('associates')) {
                $flagMinComp['flag_associates'] = true;
                foreach (explode(',', (string)Input::post('associates')) as $associate) {
                    $this->validator->label('Associata ' . $associate)
                        ->value($associate)
                        ->isInt()
                        ->isNaturalNoZero()
                        ->end();
                }
            }

            if (Input::post('consortiums')) {
                $flagMinComp['flag_consortiums'] = true;
                foreach (explode(',', (string)Input::post('consortiums')) as $consortium) {
                    $this->validator->label('Consorziata ' . $consortium)
                        ->value($consortium)
                        ->isInt()
                        ->isNaturalNoZero()
                        ->end();
                }
            }

            $contFlag = 0;

            foreach ($flagMinComp as $k => $v) {
                if ($v) {
                    $contFlag += 1;
                }
            }

            if ($contFlag <= 1) {
                return [
                    'is_success' => false,
                    'errors' => __('few_components', null, 'patos')
                ];
            }

        }

        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => !$hasApi
                ? $this->validator->getErrorsHtml()
                : $this->validator->getErrors()
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

                $suppliers = SupplieListModel::select(['id', 'name'])
                    ->whereIn('id', $ids)
                    ->get();

                if ($suppliers === null) {

                    $isError = false;
                } else {

                    Registry::set('__ids__multi_select_profile__', $suppliers);
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