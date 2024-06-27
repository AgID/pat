<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Model\MeasuresModel;
use System\Input;
use System\Registry;
use System\Validator;

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
     */
    public function validateUriSegmentId($checkOwner = null): array
    {
        $this->validator->label('ID intervento')
            ->value(uri()->segment(4, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () use ($checkOwner) {

                //Recupero l'intervento con l'id passato in input
                $check = MeasuresModel::where('id', uri()->segment(4, 0))
                    ->with(['structures' => function ($query) {
                        $query->select(['object_structures.id', 'object_structures.structure_name', 'object_structures.structure_of_belonging_id', 'object_structures.reference_email',
                            'parent.structure_name as parent_name'])
                            ->join('object_structures as parent', 'parent.id', '=', 'object_structures.structure_of_belonging_id', 'left outer');
                    }])
                    ->with(['personnel' => function ($query) {
                        $query->select(['object_personnel.id', 'full_name', 'email', 'title', 'role_id', 'role.name']);
                        $query->join('role', 'role.id', '=', 'role_id');
                    }])
                    ->with(['relative_procedure_contraent' => function ($query) {
                        $query->select(['id', 'object', 'cig', 'type', 'activation_date', 'expiration_date']);
                    }])
                    ->first();

                //Se non esiste l'esito con questo id, oppure l'utente non può modificare i record non creati da lui
                //mostro un messaggio di errore
                if ($check == null || (!empty($checkOwner) && !checkRecordOwner(@$check->owner_id))) {

                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id', null, 'patos'), 'provvedimento amministrativo ')
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
     */
    protected function validate(string $mode = 'insert'): array
    {

        $this->validator->verifyToken()
            ->end();

        $this->validator->label('Numero del provvedimento')
            ->value(Input::post('number'))
            ->betweenString(1, 1000)
            ->end();

        $this->validator->label('Oggetto del provvedimento')
            ->value(Input::post('object'))
            ->required()
            ->betweenString(4, 800)
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

        $this->validator->label('Procedura relativa (scelta del contraente)')
            ->value(Input::post('object_contests_acts_id'))
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        $this->validator->label('Note (scelta del contraente)')
            ->value(Input::post('choice_of_contractor'))
            ->betweenString(4, 1000)
            ->end();

        $this->validator->label('Note')
            ->value(Input::post('notes'))
            ->betweenString(4, 1000)
            ->end();


        if ($mode === 'update') {

            $this->validator->label('id')
                ->value(Input::post('id'))
                ->required()
                ->isInt()
                ->isNaturalNoZero()
                ->add(function () {
                    $measure = MeasuresModel::where('id', Input::post('id'))->first();
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
