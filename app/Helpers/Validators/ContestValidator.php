<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Model\ContestModel;
use System\Input;
use System\Registry;
use System\Validator;

/**
 * Validator per l'oggetto Bando di concorso (object_contest)
 */
class ContestValidator
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
     * Controlla la validità dell'ID nell'URI segment e se esiste un bando di concorso con quell'ID per l'ente
     *
     * @param null $checkOwner Indica se controllare il proprietario del record
     * @return array
     * @throws Exception
     */
    public function validateUriSegmentId($checkOwner = null): array
    {
        $this->validator->label('ID bando di concorso')
            ->value(uri()->segment(4, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () use ($checkOwner) {

                //Recupero il bando con l'id passato in input
                $check = ContestModel::where('id', uri()->segment(4, 0))
                    ->with('assignments:id,name,object')
                    ->with(['relative_measure' => function ($query) {
                        $query->select(['id', 'object', 'number', 'date']);
                    }])
                    ->with(['related_contest' => function ($query) {
                        $query->select(['id', 'object', 'typology', 'expiration_date', 'activation_date']);
                    }])
                    ->with(['office' => function ($query) {
                        $query->select(['object_structures.id', 'object_structures.structure_name', 'object_structures.reference_email', 'object_structures.structure_of_belonging_id', 'parent.structure_name as parent_name'])
                            ->join('object_structures as parent', 'parent.id', '=', 'object_structures.structure_of_belonging_id', 'left outer');
                    }])
                    ->first();

                //Se non esiste l'esito con questo id, oppure l'utente non può modificare i record non creati da lui
                //mostro un messaggio di errore
                if ($check == null || (!empty($checkOwner) && !checkRecordOwner(@$check->owner_id))) {

                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id', null, 'patos'), 'bando di concorso ')
                    ];
                }

                //Se esiste il bando lo salvo nel registro
                Registry::set('contest', $check);

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
     * @param string $mode Indica l'operazione
     * @return array
     * @throws Exception
     */
    public function check(string $mode = 'insert'): array
    {
        return $this->validate($mode);
    }

    /**
     * Metodo che esegue la validazione dei campi del form
     *
     * @param string $mode Indica l'operazione
     * @return array
     * @throws Exception
     */
    public function checkContest(string $mode = 'insert'): array
    {
        $this->validator->verifyToken()
            ->end();

        $this->validator->label('Oggetto')
            ->value(Input::post('object'))
            ->required()
            ->betweenString(4, 10000)
            ->end();

        $this->validator->label('Sede di gara - Provincia')
            ->value(Input::post('province_office'))
            ->isAlpha()
            ->betweenString(2, 2)
            ->end();

        $this->validator->label('Sede di gara - Comune')
            ->value(Input::post('city_office'))
            ->betweenString(4, 40)
            ->end();

        $this->validator->label('Sede di gara - Indirizzo')
            ->value(Input::post('office_address'))
            ->betweenString(4, 40)
            ->end();

        $this->validator->label('Ufficio di riferimento')
            ->value(Input::post('object_structures_id'))
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        $this->validator->label('Calendario delle prove')
            ->value(Input::post('test_calendar'))
            ->betweenString(4, 5000)
            ->end();

        $this->validator->label('Criteri di valutazione')
            ->value(Input::post('evaluation_criteria'))
            ->betweenString(4, 5000)
            ->end();

        $this->validator->label('Tracce prove scritte')
            ->value(Input::post('traces_written_tests'))
            ->betweenString(4, 10000)
            ->end();

        $this->validator->label('Concorso o Avviso relativo')
            ->value(Input::post('related_contest_id'))
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        $activationData = Input::post('activation_date');
        $dateFormat = 'Y-m-d';

        if (!empty(Input::post('activation_date')) && strpos(Input::post('activation_date'), 'T')) {

            $activationData = !empty(Input::post('activation_date')) ? str_replace('T', ' ', Input::post('activation_date')) : '';
            $dateFormat = 'Y-m-d H:i';
        }

        $this->validator->label('Data di pubblicazione')
            ->value($activationData)
            ->required()
            ->isDate($dateFormat)
            ->end();


        $this->validator->label('Data di scadenza del bando')
            ->value(Input::post('expiration_date'))
            ->isDate('Y-m-d')
            ->add(function () use ($activationData) {
                if (Input::post('expiration_date') <= $activationData) {

                    return ['error' => __('invalid_expiration_date', null, 'patos')];

                }
                return null;
            })
            ->end();

        $this->validator->label('Data di termine del concorso')
            ->value(Input::post('expiration_contest_date'))
            ->isDate('Y-m-d')
            ->add(function () {
                if (Input::post('expiration_contest_date') <= Input::post('activation_date')) {

                    return ['error' => __('invalid_ending_date', null, 'patos')];

                }
                return null;
            })
            ->end();

        $this->validator->label('Orario di scadenza del bando')
            ->value(Input::post('expiration_time'))
            ->isHour('H:m')
            ->end();

        $this->validator->label('Numero dipendenti assunti')
            ->value(Input::post('hired_employees'))
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        $this->validator->label('Eventuale spesa prevista')
            ->value(Input::post('expected_expenditure'))
            ->isNumeric()
            ->end();

        $this->validator->label('Spese effettuate')
            ->value(Input::post('expenditures_made'))
            ->isNumeric()
            ->end();

        if (Input::post('commissions')) {
            foreach (explode(',', (string)Input::post('commissions')) as $commission) {
                $this->validator->label('Commissione giudicatrice ' . $commission)
                    ->value($commission)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        $this->validator->label('Maggiori informazioni sul bando')
            ->value(Input::post('description'))
            ->betweenString(4, 10000)
            ->end();

        $this->validator->label('Provvedimento')
            ->value(Input::post('input_measure_id'))
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        if ($mode === 'update') {

            $this->validator->label('id')
                ->value(Input::post('id'))
                ->required()
                ->isInt()
                ->isNaturalNoZero()
                ->add(function () {
                    $regulation = ContestModel::where('id', Input::post('id'))->first();
                    if (empty($regulation)) {
                        return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Bando di concorso')];
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
     * Metodo che esegue la validazione dei campi del form per gli Avvisi
     *
     * @param string $mode Indica l'operazione
     * @return array
     * @throws Exception
     */
    public function checkAlert(string $mode = 'insert'): array
    {
        $this->validator->verifyToken()
            ->end();

        $this->validator->label('Oggetto')
            ->value(Input::post('object'))
            ->required()
            ->betweenString(4, 10000)
            ->end();

        $this->validator->label('Ufficio di riferimento')
            ->value(Input::post('object_structures_id'))
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        $this->validator->label('Concorso o Avviso relativo')
            ->value(Input::post('related_contest_id'))
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        $activationData = !empty(Input::post('activation_date')) ? str_replace('T', ' ', Input::post('activation_date')) : '';
        $this->validator->label('Data di pubblicazione')
            ->value($activationData)
            ->required()
            ->isDate('Y-m-d H:i')
            ->end();

        $this->validator->label('Data di scadenza del bando')
            ->value(Input::post('expiration_date'))
            ->isDate('Y-m-d')
            ->add(function () use ($activationData) {
                if (Input::post('expiration_date') <= $activationData) {

                    return ['error' => __('invalid_expiration_date', null, 'patos')];

                }
                return null;
            })
            ->end();

        $this->validator->label('Maggiori informazioni sul bando')
            ->value(Input::post('description'))
            ->betweenString(4, 10000)
            ->end();

        if ($mode === 'update') {

            $this->validator->label('id')
                ->value(Input::post('id'))
                ->required()
                ->isInt()
                ->isNaturalNoZero()
                ->add(function () {
                    $regulation = ContestModel::where('id', Input::post('id'))->first();
                    if (empty($regulation)) {
                        return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Bando di concorso')];
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
     * Metodo che esegue la validazione dei campi del form
     *
     * @param string $mode Indica l'operazione
     * @return array
     * @throws Exception
     */
    public function checkResult(string $mode = 'insert'): array
    {
        $this->validator->verifyToken()
            ->end();

        $this->validator->label('Oggetto')
            ->value(Input::post('object'))
            ->required()
            ->betweenString(4, 10000)
            ->end();

        $this->validator->label('Concorso o Avviso relativo')
            ->value(Input::post('related_contest_id'))
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        $activationData = !empty(Input::post('activation_date')) ? str_replace('T', ' ', Input::post('activation_date')) : '';
        $this->validator->label('Data di pubblicazione')
            ->value($activationData)
            ->required()
            ->isDate('Y-m-d H:i')
            ->end();

        $this->validator->label('Maggiori informazioni sul bando')
            ->value(Input::post('description'))
            ->betweenString(4, 10000)
            ->end();

        if ($mode === 'update') {

            $this->validator->label('id')
                ->value(Input::post('id'))
                ->required()
                ->isInt()
                ->isNaturalNoZero()
                ->add(function () {
                    $regulation = ContestModel::where('id', Input::post('id'))->first();
                    if (empty($regulation)) {
                        return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Bando di concorso')];
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
     * Metodo per la validazione dei campi del form
     *
     * @param string $mode Indica l'operazione
     * @return array
     * @throws Exception
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    protected function validate(string $mode = 'insert'): array
    {

        $this->validator->verifyToken()
            ->end();

        $typologies = 'concorso,avviso,esito';

        $this->validator->label('Tipologia')
            ->value(Input::post('typology'))
            ->required()
            ->in($typologies)
            ->end();

        $this->validator->label('Oggetto')
            ->value(Input::post('object'))
            ->required()
            ->betweenString(4, 10000)
            ->end();

        $this->validator->label('Sede di gara - Provincia')
            ->value(Input::post('province_office'))
            ->isAlpha()
            ->betweenString(2, 2)
            ->end();

        $this->validator->label('Sede di gara - Comune')
            ->value(Input::post('city_office'))
            ->betweenString(4, 40)
            ->end();

        $this->validator->label('Sede di gara - Indirizzo')
            ->value(Input::post('office_address'))
            ->betweenString(4, 40)
            ->end();

        $this->validator->label('Ufficio di riferimento')
            ->value(Input::post('object_structures_id'))
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        $this->validator->label('Calendario delle prove')
            ->value(Input::post('test_calendar'))
            ->betweenString(4, 5000)
            ->end();

        $this->validator->label('Criteri di valutazione')
            ->value(Input::post('evaluation_criteria'))
            ->betweenString(4, 5000)
            ->end();

        $this->validator->label('Tracce prove scritte')
            ->value(Input::post('traces_written_tests'))
            ->betweenString(4, 10000)
            ->end();

        $this->validator->label('Concorso o Avviso relativo')
            ->value(Input::post('related_contest_id'))
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        $this->validator->label('Data di pubblicazione')
            ->value(Input::post('activation_date'))
            ->required()
            ->isDate('Y-m-d')
            ->end();

        $this->validator->label('Data di scadenza del bando')
            ->value(Input::post('expiration_date'))
            ->isDate('Y-m-d')
            ->add(function () {
                if (Input::post('expiration_date') <= Input::post('activation_date')) {

                    return ['error' => __('invalid_expiration_date', null, 'patos')];

                }
                return null;
            })
            ->end();

        $this->validator->label('Data di termine del concorso')
            ->value(Input::post('expiration_contest_date'))
            ->isDate('Y-m-d')
            ->add(function () {
                if (Input::post('expiration_contest_date') <= Input::post('activation_date')) {

                    return ['error' => __('invalid_ending_date', null, 'patos')];

                }
                return null;
            })
            ->end();

        $this->validator->label('Orario di scadenza del bando')
            ->value(Input::post('expiration_time'))
            ->isHour('H:m')
            ->end();

        $this->validator->label('Numero dipendenti assunti')
            ->value(Input::post('hired_employees'))
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        $this->validator->label('Eventuale spesa prevista')
            ->value(Input::post('expected_expenditure'))
            ->isNumeric()
            ->end();

        $this->validator->label('Spese effettuate')
            ->value(Input::post('expenditures_made'))
            ->isNumeric()
            ->end();

        if (Input::post('commissions')) {
            foreach (explode(',', (string)Input::post('commissions')) as $commission) {
                $this->validator->label('Commissione giudicatrice ' . $commission)
                    ->value($commission)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        $this->validator->label('Maggiori informazioni sul bando')
            ->value(Input::post('description'))
            ->betweenString(4, 10000)
            ->end();

        $this->validator->label('Provvedimento')
            ->value(Input::post('input_measure_id'))
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        if ($mode === 'update') {

            $this->validator->label('id')
                ->value(Input::post('id'))
                ->required()
                ->isInt()
                ->isNaturalNoZero()
                ->add(function () {
                    $regulation = ContestModel::where('id', Input::post('id'))->first();
                    if (empty($regulation)) {
                        return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Bando di concorso')];
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

                $contests = ContestModel::select(['id', 'object', 'typology'])
                    ->whereIn('id', $ids)
                    ->get();

                if ($contests === null) {

                    $isError = false;
                } else {

                    Registry::set('__ids__multi_select_profile__', $contests);
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
