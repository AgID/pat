<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Model\RealEstateAssetModel;
use System\Input;
use System\Registry;
use System\Validator;

/**
 * Validator per l'oggetto Patrimonio Immobiliare (object_real_estate_asset)
 */
class RealEstateAssetValidator
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
     * Controlla la validità dell'ID nell'URI segment e se esiste un patrimonio immobiliare con quell'ID per l'ente
     *
     * @param null $checkOwner Indica se controllare il proprietario del record
     * @return array
     * @throws Exception
     */
    public function validateUriSegmentId($checkOwner = null): array
    {
        $this->validator->label('ID patrimonio immobiliare')
            ->value(uri()->segment(4, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () use ($checkOwner) {

                //Recupero il patrimonio con l'id passato in input
                $check = RealEstateAssetModel::select()->where('id', uri()->segment(4, 0))
                    ->with(['offices' => function ($query) {
                        $query->select(['object_structures.id', 'object_structures.structure_name', 'object_structures.structure_of_belonging_id',
                            'object_structures.reference_email', 'parent.structure_name as parent_name'])
                            ->join('object_structures as parent', 'parent.id', '=', 'object_structures.structure_of_belonging_id', 'left outer');
                    }])
                    ->first();

                //Se non esiste l'esito con questo id, oppure l'utente non può modificare i record non creati da lui
                //mostro un messaggio di errore
                if ($check == null || (!empty($checkOwner) && !checkRecordOwner(@$check->owner_id))) {

                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id', null, 'patos'), 'patrimonio immobiliare ')
                    ];
                }

                //Se esiste il patrimonio lo salvo nel registro
                Registry::set('real_estate_asset', $check);

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
        if ($mode == 'archiving') {
            return $this->archivingValidate();
        } else {
            return $this->validate($mode);
        }
    }

    /**
     * Metodo per la validazione dei campi del form per l'archiviazione
     *
     * @return array
     * @throws Exception
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    protected function archivingValidate(): array
    {
        $this->validator->label('Item Id')
            ->value(Input::post('itemId'))
            ->required()
            ->isInt()
            ->isNaturalNoZero()
            ->add(function () {
                $user = RealEstateAssetModel::where('id', Input::post('itemId'))->first();
                if (empty($user)) {
                    return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Immobile')];
                }
            })
            ->end();

        $this->validator->label('Data di fine pubblicazione in archivio')
            ->value(Input::post('end_date'))
            ->required()
            ->isDate('Y-m-d')
            ->end();

        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
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

        $this->validator->label('Nome identificativo')
            ->value(Input::post('name'))
            ->required()
            ->betweenString(5, 60)
            ->end();

        $this->validator->label('Indirizzo')
            ->value(Input::post('address'))
            ->betweenString(5, 120)
            ->end();

        $this->validator->label('Superficie lorda (mq)')
            ->value(Input::post('gross_surface'))
            ->betweenString(2, 20)
            ->end();

        $this->validator->label('Superficie scoperta (mq)')
            ->value(Input::post('discovered_surface'))
            ->betweenString(2, 20)
            ->end();

        $this->validator->label('Descrizione e note')
            ->value(Input::post('description'))
            ->betweenString(2, 1000)
            ->end();

        if (Input::post('user_offices')) {
            foreach (explode(',', (string)Input::post('user_offices')) as $office) {
                $this->validator->label('Ufficio utilizzatore ' . $office)
                    ->value($office)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        if ($mode === 'update') {

            $this->validator->label('id')
                ->value(Input::post('id'))
                ->required()
                ->isInt()
                ->isNaturalNoZero()
                ->add(function () {
                    $asset = RealEstateAssetModel::where('id', Input::post('id'))->first();
                    if (empty($asset)) {
                        return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Patrimonio immobiliare')];
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

                $realEstateAsset = RealEstateAssetModel::select(['id', 'name'])
                    ->whereIn('id', $ids)
                    ->get();

                if ($realEstateAsset === null) {

                    $isError = false;
                } else {

                    Registry::set('__ids__multi_select_profile__', $realEstateAsset);
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
