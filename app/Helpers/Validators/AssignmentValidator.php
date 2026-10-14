<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Carbon\Carbon;
use Exception;
use Model\AssignmentsModel;
use Scope\DeletedScope;
use System\Input;
use System\Registry;
use System\Validator;

/**
 * Validator per l'oggetto Incarichi (object_assignments)
 */
class AssignmentValidator
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
     * Controlla la validità dell'ID nell'URI segment e se esiste un incarico con quell'ID per l'ente
     *
     * @param null $checkOwner Indica se controllare il proprietario del record
     * @return array
     * @throws Exception
     */
    public function validateUriSegmentId($checkOwner = null, $uriSegment = 4, $institutionId = null, $userId = null): array
    {
        $this->validator->label('ID Incarico')
            ->value(uri()->segment($uriSegment, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () use ($checkOwner, $uriSegment, $institutionId, $userId) {

                //Recupero l'incarico con l'id passato in input
                $check = AssignmentsModel::where('id', uri()->segment($uriSegment, 0));

                $check->with('measures:id,object,number,date');
                $check->with([
                    'structure' => function ($query) {
                        $query->select(['object_structures.id', 'object_structures.structure_name', 'object_structures.reference_email', 'object_structures.structure_of_belonging_id', 'parent.structure_name as parent_name'])
                            ->join('object_structures as parent', 'parent.id', '=', 'object_structures.structure_of_belonging_id', 'left outer');
                    }
                ])
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

                //Se esiste l'incarico lo salvo nel registro
                Registry::set('assignment', $check);

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

        $this->validator->label('Soggetto incaricato')
            ->value(Input::post('name'))
            ->required()
            ->betweenString(2, 120)
            ->end();

        $this->validator->label('Oggetto incarico o consulenza')
            ->value(Input::post('object'))
            ->required()
            ->betweenString(2, 500)
            ->end();


        $this->validator->label('Tipo di incarico')
            ->value(Input::post('assignment_type'))
            ->required()
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        $this->validator->label('Tipo consulenza')
            ->value(Input::post('consulting_type'))
            ->in('1,2,3,4')
            ->end();

        $this->validator->label('Struttura organizzativa responsabile')
            ->value(Input::post('object_structures_id'))
            ->required()
            ->isInt()
            ->isNaturalNoZero()
            ->end();


        $this->validator->label('Data di inizio incarico')
            ->value(Input::post('assignment_start'))
            ->required()
            ->isDate('Y-m-d')
            ->end();


        $this->validator->label('Data di fine incarico non disponibile')
            ->value(Input::post('end_of_assignment_not_available'))
            ->required()
            ->in('0,1')
            ->end();


        $this->validator = $this->validator ?? null;

        $this->validator = $this->validator ?? null;

        $this->validator = $this->validator ?? null;

        $this->validator = $this->validator ?? null;

        if (Input::post('end_of_assignment_not_available') == '0') {

            $this->validator->label('Data di fine incarico')
                ->value(Input::post('assignment_end'))
                ->required()
                ->isDate('Y-m-d')
                ->add(function () {
                    if (Input::post('assignment_end') < Input::post('assignment_start')) {

                        return ['error' => __('invalid_end_date_office', null, 'patos')];

                    }
                    return null;
                })
                ->end();
        } else {

            $this->validator->label('Note data di fine incarico non disponibile')
                ->value(Input::post('end_of_assignment_not_available_txt'))
                ->required()
                ->betweenString(1, 120)
                ->end();
        }

        $this->validator->label('Compenso')
            ->value(Input::post('compensation'))
            ->required()
            ->isNumeric()
            ->end();

        $this->validator->label('Componenti variabili del compenso')
            ->value(Input::post('variable_compensation'))
            ->betweenString(1, 500)
            ->end();

        $this->validator->label('Estremi atto di conferimento')
            ->value(Input::post('acts_extremes'));
        if (Input::post('assignment_type') != 1) {
            $this->validator->required();
        }
        $this->validator->betweenString(1, 10000)
            ->end();

        if (Input::post('measures')) {
            foreach (explode(',', (string)Input::post('measures')) as $measure) {
                $this->validator->label('Provvedimento associato ' . $measure)
                    ->value($measure)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        $this->validator->label('Ragione dell\'incarico')
            ->value(Input::post('assignment_reason'))
            ->betweenString(2, 500);
        if (Input::post('assignment_type') != 1) {
            $this->validator->required();
        }
        $this->validator->end();


        $this->validator->label('Note (incarichi, cariche, altre attività)')
            ->value(Input::post('notes'))
            ->betweenString(2, 10000)
            ->end();

        if ($mode === 'update') {

            $id = Input::post('id');

            $this->validator->label('id')
                ->value($id)
                ->required()
                ->isInt()
                ->isNaturalNoZero()
                ->add(function () use ($id, $institutionId) {

                    $query = AssignmentsModel::where('id', $id);

                    $assignment = $query->first();

                    if (empty($assignment)) {
                        return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Incarico')];
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

                $assignment = AssignmentsModel::select(['id', 'object', 'type', 'typology'])
                    ->whereIn('id', $ids)
                    ->get();

                if ($assignment === null) {

                    $isError = false;
                } else {

                    Registry::set('__ids__multi_select_profile__', $assignment);
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
