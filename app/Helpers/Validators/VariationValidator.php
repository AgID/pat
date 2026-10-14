<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Scope\DeletedScope;
use System\Input;
use System\Registry;
use System\Validator;
use Model\ContestsActsModel;

/**
 * Validator per l'oggetto Avviso(object_contests_acts)
 */
class VariationValidator
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
     * Controlla la validità dell'ID nell'URI segment e se esiste un avviso con quell'ID per l'ente
     *
     * @param null $checkOwner Indica se controllare il proprietario del record
     * @return array
     * @throws Exception
     */
    public function validateUriSegmentId($checkOwner = null, $uriSegment = 4, $institutionId = null, $userId = null): array
    {
        $this->validator->label('ID variazione')
            ->value(uri()->segment($uriSegment, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () use ($checkOwner, $uriSegment, $institutionId, $userId) {

                //Recupero l'avviso con l'id passato in input
                $check = ContestsActsModel::where('typology', 'variation');
                $check->where('id', uri()->segment($uriSegment, 0));
                $check->with('proceedings:id,object,cig,type');
                $check->with('structure:id,structure_name');
                $check->with(['created_by' => function ($query) {
                    $query->withoutGlobalScopes([DeletedScope::class]);
                    $query->select(['id', 'name', 'deleted']);
                }]);


                $check = $check->first();

                //Se non esiste l'esito con questo id, oppure l'utente non può modificare i record non creati da lui
                //mostro un messaggio di errore
                if ($check == null || (!empty($checkOwner) && !checkRecordOwner(@$check->owner_id, $userId))) {
                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id', null, 'patos'), 'avviso ')
                    ];
                }

                //Se esiste l'avviso lo salvo nel registro
                Registry::set('variation', $check);

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
     * @param string   $mode          Indica l'operazione che si sta eseguendo (Insert/Update)
     * @param int|bool $institutionId indica l'id dell'istituto
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
     * @param string $mode          Indica l'operazione che si sta eseguendo (Insert/Update)
     * @param int    $institutionId
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
            ->betweenString(4, 800)
            ->end();

        if(empty(Input::post('object_contests_acts_id'))){
            $this->validator->value(null)->required('Campo "Procedura relativa" obbligatorio.')->end();
        }else{
            $procedure = explode(",", Input::post('object_contests_acts_id'));
            foreach ($procedure as $procId){
                $this->validator->label('Procedura relativa')
                    ->value($procId)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }



        $activationData = !empty(Input::post('activation_date')) ? str_replace('T', ' ', Input::post('activation_date')) : '';

        $this->validator->label('Data di pubblicazione sul sito')
            ->value($activationData)
            ->required()
            ->isDate('Y-m-d')
            ->end();

        $this->validator->label('Note')
            ->value(Input::post('details'))
            ->betweenString(2, 1000)
            ->end();



        if ($mode === 'update') {

            $id = Input::post('id');

            $this->validator->label('id')
                ->value($id)
                ->required()
                ->isInt()
                ->isNaturalNoZero()
                ->add(function () use ($id, $institutionId) {
                    $query = ContestsActsModel::where('id', $id);
                    $result = $query->first();

                    if (empty($result)) {
                        return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Variazione')];
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
