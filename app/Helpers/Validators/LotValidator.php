<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Model\ContestsActsModel;
use Scope\DeletedScope;
use System\Input;
use System\Registry;
use System\Validator;

/**
 * Validator per l'oggetto Lotto (object_contests_acts)
 */
class LotValidator
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
     * Controlla la validità dell'ID nell'URI segment e se esiste un lotto con quell'ID per l'ente
     *
     * @param null $checkOwner Indica se effettuare il controllo sul proprietario dell'elemento o meno
     * @param int $uriSegment
     * @param null $institutionId
     * @param null $userId
     * @return array
     * @throws Exception
     */
    public function validateUriSegmentId($checkOwner = null, $uriSegment = 4, $institutionId = null, $userId = null): array
    {
        $this->validator->label('ID lotto')
            ->value(uri()->segment($uriSegment, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () use ($checkOwner, $uriSegment, $institutionId, $userId) {

                //Recupero il lotto con l'id passato in input
                $check = ContestsActsModel::where('typology', 'lot');
                $check->where('id', uri()->segment($uriSegment, 0));
                $check->with('relative_notice:id,object,cig,type,activation_date,expiration_date')
                ->with(['created_by' => function ($query) {
                    $query->withoutGlobalScopes([DeletedScope::class]);
                    $query->select(['id', 'name', 'deleted']);
                }]);

                $check = $check->first();

                if ($check == null || (!empty($checkOwner) && !checkRecordOwner(@$check->owner_id, $userId))) {
                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id', null, 'patos'), 'bando di concorso ')
                    ];
                }

                //Se esiste il lotto lo salvo nel registro
                Registry::set('lot', $check);

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
     * @param int|bool $institutionId indica l'id dell'istituto     * @return array
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


        $this->validator->label('Bando relativo')
            ->value(Input::post('relative_notice_id'))
            ->required()
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        $this->validator->label('Oggetto')
            ->value(Input::post('object'))
            ->required()
            ->betweenString(1, 250)
            ->end();

        $this->validator->label('Link alla Banca Dati Nazionale Contratti Pubblici (BDNCP)')
            ->value(Input::post('bdncp_link'))
            ->betweenString(1, 250)
            ->end();

        $this->validator->label('Codice CIG')
            ->value(Input::post('cig'))
            ->exactLength(10)
            ->isAlphaNum()
            ->end();

        $this->validator->label('Errore CIG')
            ->value(Input::post('__ignore_cig'))
            ->in('0,1', 'Ops, c\'è un problema. Contattare l\'assistenza!')
            ->end();

        $this->validator->label('Importo dell\'appalto')
            ->value(Input::post('asta_base_value'))
            ->required()
            ->isNumeric()
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

                    $lot = $query->first();

                    if (empty($lot)) {
                        return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Lotto')];
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
