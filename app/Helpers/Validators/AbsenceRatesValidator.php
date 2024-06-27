<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Model\AbsenceRatesModel;
use System\Input;
use System\Registry;
use System\Validator;

/**
 * Validator per l'oggetto Tasso di assenza(object_absence_rates)
 */
class AbsenceRatesValidator
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
     * @description Controlla la validità dell'ID nell'URI segment e se esiste un tasso di assenza con quell'ID per l'ente
     *
     * @param bool|null $checkOwner Indica se si deve controllare se l'utente è il creatore del record
     * @return array
     * @throws Exception
     */
    public function validateUriSegmentId(?bool $checkOwner = null): array
    {
        $this->validator->label('ID tasso di assenza')
            ->value(uri()->segment(4, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () use ($checkOwner) {

                //Recupero il tasso di assenza con l'id passato in input
                $check = AbsenceRatesModel::where('id', uri()->segment(4, 0))
                    ->with(['structure' => function ($query) {
                        $query->select(['object_structures.id', 'object_structures.structure_name', 'object_structures.reference_email', 'object_structures.structure_of_belonging_id', 'parent.structure_name as parent_name'])
                            ->join('object_structures as parent', 'parent.id', '=', 'object_structures.structure_of_belonging_id', 'left outer');
                    }]);

                $check = $check->first();

                //Se non esiste il tasso di assenza con questo id, oppure l'utente non può modificare i record non creati da lui
                //mostro un messaggio di errore
                if ($check == null || (!empty($checkOwner) && !checkRecordOwner(@$check->owner_id))) {

                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id', null, 'patos'), 'tasso di assenza ')
                    ];
                }

                //Se esiste il tasso di assenza, lo salvo nel registro
                Registry::set('absence_rate', $check);

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
     * @param string $mode Tipo di operazione
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
     * @param string $mode Tipo di operazione
     * @return array
     * @throws Exception
     */
    protected function validate(string $mode = 'insert'): array
    {

        $this->validator->verifyToken()
            ->end();

        $this->validator->label('Struttura')
            ->value(Input::post('object_structures_id'))
            ->required()
            ->isNaturalNoZero()
            ->end();

        if (Input::post('months')) {
            foreach (Input::post('months') as $month) {
                $this->validator->label('Periodo ' . $month)
                    ->value($month)
                    ->in(implode(',', array_keys(config('absenceRatesPeriod', null, 'app'))))
                    ->end();
            }
        } else {

            $this->validator->label('Periodo')
                ->value(Input::post('months'))
                ->required()
                ->end();
        }

        $this->validator->label('Anno')
            ->value(Input::post('year'))
            ->required()
            ->regex('/^\d{4}$/')
            ->end();

        $this->validator->label('Percentuale di presenze')
            ->value(Input::post('presence_percentage'))
            ->required()
            ->isNumeric()
            ->end();

        $this->validator->label('Percentuale di assenze totali')
            ->value(Input::post('total_absence'))
            ->required()
            ->isNumeric()
            ->end();

        if ($mode === 'update') {

            $this->validator->label('id')
                ->value(Input::post('id'))
                ->required()
                ->isInt()
                ->isNaturalNoZero()
                ->add(function () {
                    $absenceRate = AbsenceRatesModel::where('id', Input::post('id'))->first();
                    if (empty($absenceRate)) {
                        return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Tasso di assenza')];
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

                $absenceRates = AbsenceRatesModel::select(['id'])
                    ->whereIn('id', $ids)
                    ->get();

                if ($absenceRates === null) {

                    $isError = false;
                } else {

                    Registry::set('__ids__multi_select_profile__', $absenceRates);
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
