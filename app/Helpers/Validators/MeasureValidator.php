<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

/** @noinspection PhpInconsistentReturnPointsInspection */



namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Scope\DeletedScope;
use System\Input;
use System\Registry;
use System\Validator;
use Model\MeasuresModel;

/**
 * Validator per l'oggetto Provvedimento Amministrativo (object_measures)
 */
class MeasureValidator
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
     * Controlla la validità dell'ID nell'URI segment e se esiste un provvedimento con quell'ID per l'ente
     *
     * @param null $checkOwner Indica se controllare il proprietario del record
     * @return array
     * @throws Exception
     * @throws Exception
     */
    public function validateUriSegmentId($checkOwner = null, $uriSegment = 4, $institutionId = null, $userId = null): array
    {

        $this->validator->label('ID provvedimento')
            ->value(uri()->segment($uriSegment, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () use ($checkOwner, $uriSegment, $institutionId, $userId) {

                //Recupero il provvedimento con l'id passato in input
                $check = MeasuresModel::where('id', uri()->segment($uriSegment, 0));

                $check->with([
                    'structures' => function ($query) {
                        $query->select([
                            'object_structures.id',
                            'object_structures.structure_name',
                            'object_structures.structure_of_belonging_id',
                            'object_structures.reference_email',
                            'parent.structure_name as parent_name'
                        ])
                            ->join('object_structures as parent', 'parent.id', '=', 'object_structures.structure_of_belonging_id', 'left outer');
                    }
                ]);
                $check->with([
                    'personnel' => function ($query) {
                        $query->select(['object_personnel.id', 'full_name', 'email', 'title', 'role_id', 'role.name']);
                        $query->join('role', 'role.id', '=', 'role_id');
                    }
                ]);
                $check->with([
                    'relative_procedure_contraent' => function ($query) {
                        $query->select(['id', 'object', 'cig', 'type', 'activation_date', 'expiration_date']);
                    }
                ]);
//                Vecchio campo per la procedura relativa (dal 01/01/2024) che non viene più utilizzato
//                $check->with([
//                    'relative_bdncp_procedure' => function ($query) {
//                        $query->select(['id', 'object', 'cig']);
//                    }
//                ]);
                $check->with([
                    'bdncp_procedures' => function ($query) {
                        $query->select(['object_bdncp_procedure.id', 'object', 'cig']);
                    }
                ]);
                $check->with(['created_by' => function ($query) {
                    $query->withoutGlobalScopes([DeletedScope::class]);
                    $query->select(['id', 'name', 'deleted']);
                }]);

                $check = $check->first();

                if ($check == null || (!empty($checkOwner) && !checkRecordOwner(@$check->owner_id, $userId))) {
                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id', null, 'patos'), 'bando di concorso ')
                    ];
                }

                //Se esiste l'intervento lo salvo nel registro
                Registry::set('measure', $check);

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
     * @param string $mode Indica l'operazione che si sta eseguendo (Insert/Update)
     * @return array
     * @throws Exception
     */
    protected function validate(string $mode, int|null $institutionId): array
    {

        $this->validator->verifyToken()
            ->end();


        $this->validator->label('Numero del provvedimento')
            ->value(Input::post('number'))
            ->betweenString(1, 1000);
        $this->validator->end();

        $maxObjLength = 1000;
        $maxObjLength = $maxObjLength ?? null;

        $this->validator->label('Oggetto del provvedimento')
            ->value(Input::post('object'))
            ->required()
            ->betweenString(4, $maxObjLength)
            ->end();

        $this->validator->label('Tipologia')
            ->value(Input::post('type'))
            ->required()
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        $this->validator->label('Data del provvedimento')
            ->value(Input::post('date'))
            ->required()
            ->isDate('Y-m-d')
            ->end();

        $this->validator = $this->validator ?? null;






        if (Input::post('structures')) {
            foreach (explode(',', (string)Input::post('structures')) as $structure) {
                $this->validator->label('Struttura organizzativa responsabile ' . $structure)
                    ->value($structure)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        if (Input::post('personnel')) {
            foreach (explode(',', (string)Input::post('personnel')) as $person) {
                $this->validator->label('Responsabile del provvedimento ' . $person)
                    ->value($person)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        $this->validator->label('Procedura relativa (fino al 31/12/2023) ')
            ->value(Input::post('object_contests_acts_id'))
            ->isInt()
            ->isNaturalNoZero()
            ->end();

//        Vecchio campo per la procedura relativa (dal 01/01/2024) che non viene più utilizzato
//        $this->validator->label('Procedura relativa (dal 01/01/2024)')
//            ->value(Input::post('object_bdncp_procedure_id'))
//            ->isInt()
//            ->isNaturalNoZero()
//            ->end();
        if (Input::post('object_bdncp_procedure_id')) {
            foreach (explode(',', (string)Input::post('object_bdncp_procedure_id')) as $procId) {
                $this->validator->label('Procedura relativa (dal 01/01/2024) ' . $procId)
                    ->value($procId)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        $this->validator->label('Note (scelta del contraente)')
            ->value(Input::post('choice_of_contractor'))
            ->betweenString(4, 1000)
            ->end();

        $this->validator->label('Note')
            ->value(Input::post('notes'))
            ->betweenString(4, 1000)
            ->end();


        if ($mode === 'update') {

            $id = Input::post('id');

            $this->validator->label('id')
                ->value($id)
                ->required()
                ->isInt()
                ->isNaturalNoZero()
                ->add(function () use ($institutionId, $id) {

                    // $measure = MeasuresModel::where('id', Input::post('id'))->first();
                    $query = MeasuresModel::where('id', '=', $id);

                    $measure = $query->first();

                    if (empty($measure)) {
                        return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Provvedimento')];
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

                $measures = MeasuresModel::select(['id', 'object'])
                    ->whereIn('id', $ids)
                    ->get();

                if ($measures === null) {

                    $isError = false;
                } else {

                    Registry::set('__ids__multi_select_profile__', $measures);
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
