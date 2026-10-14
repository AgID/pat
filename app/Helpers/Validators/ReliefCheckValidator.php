<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Model\ReliefChecksModel;
use Scope\DeletedScope;
use System\Input;
use System\Registry;
use System\Validator;

/**
 * Validator per l'oggetto Controllo e rilievi (object_relief_checks)
 */
class ReliefCheckValidator
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
     * Controlla la validità dell'ID nell'URI segment e se esiste un controllo/rilievo con quell'ID per l'ente
     *
     * @param null $checkOwner Indica se controllare il proprietario del record
     * @return array
     * @throws Exception
     */
    public function validateUriSegmentId($checkOwner = null, $uriSegment = 4, $institutionId = null, $userId = null): array
    {
        $this->validator->label('ID controllo/rilievo')
            ->value(uri()->segment($uriSegment, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () use ($checkOwner, $uriSegment, $institutionId, $userId) {

                //Recupero il controllo con l'id passato in input
				$check = ReliefChecksModel::where('id', uri()->segment($uriSegment, 0));

                $check->with(['public_in' => function ($query) {
                    $query->select(['section_fo_id', 'section_fo_config_publication_archive.id', 'section_fo.name'])
                        ->withoutGlobalScope(DeletedScope::class)
                        ->join('section_fo', 'section_fo.id', '=', 'section_fo_id');
                }]);

                $check->with(['office' => function ($query) {
                    $query->select(['object_structures.id', 'object_structures.structure_name', 'object_structures.structure_of_belonging_id', 'object_structures.reference_email', 'parent.structure_name as parent_name'])
                        ->join('object_structures as parent', 'parent.id', '=', 'object_structures.structure_of_belonging_id', 'left outer');
                }]);

                $check->with(['created_by' => function ($query) {
                    $query->withoutGlobalScopes([DeletedScope::class]);
                    $query->select(['id', 'name', 'deleted']);
                }]);


                $check = $check->first();

                //Se non esiste l'esito con questo id, oppure l'utente non può modificare i record non creati da lui
                //mostro un messaggio di errore
                if ($check == null || (!empty($checkOwner) && !checkRecordOwner(@$check->owner_id))) {

                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id', null, 'patos'), 'controllo/rilievo ')
                    ];
                }

                //Se esiste il controllo lo salvo nel registro
                Registry::set('relief_check', $check);

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
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    protected function validate(string $mode, int $institutionId): array
    {

        $this->validator->verifyToken()
            ->end();
        
        $this->validator->label('Oggetto')
            ->value(Input::post('object'))
            ->required()
            ->betweenString(4, 250)
            ->end();

        $this->validator->label('Data')
            ->value(Input::post('date'))
            ->required()
            ->isDate('Y-m-d')
            ->end();

        $this->validator->label('Pubblica in')
            ->value(Input::post('public_in'))
            ->required()
            ->end();

        if (!empty(Input::post('public_in'))) {
            foreach (Input::post('public_in') as $in) {
                $this->validator->label('Pubblica in ' . $in)
                    ->value($in)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }


            if(in_array('608', Input::post('public_in'))){

                $this->validator->label('Oggetto del rilievo')
                    ->value(Input::post('act_object'))
                    ->required()
                    ->end();

            }

        }

        $this->validator->label('Ufficio')
            ->value(Input::post('object_structures_id'))
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        $this->validator->label('Descrizione')
            ->value(Input::post('description'))
            ->betweenString(4, 15000)
            ->end();

        

        if ($mode === 'update') {

            $id = Input::post('id');

            $this->validator->label('id')
                ->value($id)
                ->required()
                ->isInt()
                ->isNaturalNoZero()
                ->add(function () use ($id, $institutionId) {

                    $query = ReliefChecksModel::where('id', $id);

                    $company = $query->first();

                    if (empty($company)) {
                        return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Controllo/Rilievo')];
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

                $reliefChecks = ReliefChecksModel::select(['id', 'object'])
                    ->whereIn('id', $ids)
                    ->get();

                if ($reliefChecks === null) {

                    $isError = false;
                } else {

                    Registry::set('__ids__multi_select_profile__', $reliefChecks);
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
