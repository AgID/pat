<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Model\GrantsModel;
use System\Input;
use System\Registry;
use System\Validator;

/**
 * Validator per l'oggetto Sovvenzione (object_grants)
 */
class GrantValidator
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
     * Controlla la validità dell'ID nell'URI segment e se esiste una sovvenzione con quell'ID per l'ente
     *
     * @param null $checkOwner Indica se controllare il proprietario del record
     * @return array
     * @throws Exception
     */
    public function validateUriSegmentId($checkOwner = null): array
    {
        $this->validator->label('ID sovvenzione')
            ->value(uri()->segment(4, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () use ($checkOwner) {

                //Recupero la sovvenzione con l'id passato in input
                $check = GrantsModel::where('id', uri()->segment(4, 0))
                    ->with(['personnel' => function ($query) {
                        $query->select(['object_personnel.id', 'full_name', 'email', 'title', 'role_id', 'role.name']);
                        $query->join('role', 'role.id', '=', 'role_id');
                    }])
                    ->with(['structure' => function ($query) {
                        $query->select(['object_structures.id', 'object_structures.structure_name', 'object_structures.reference_email', 'object_structures.structure_of_belonging_id', 'parent.structure_name as parent_name'])
                            ->join('object_structures as parent', 'parent.id', '=', 'object_structures.structure_of_belonging_id', 'left outer');
                    }])
                    ->with('normatives:id')
                    ->first();

                //Se non esiste l'esito con questo id, oppure l'utente non può modificare i record non creati da lui
                //mostro un messaggio di errore
                if ($check == null || (!empty($checkOwner) && !checkRecordOwner(@$check->owner_id))) {

                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id_2', null, 'patos'), 'sovvenzione ')
                    ];
                }

                //Se esiste la sovvenzione lo salvo nel registro
                Registry::set('grant', $check);

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
     * @noinspection PhpInconsistentReturnPointsInspection
     */
    protected function validate(string $mode = 'insert'): array
    {

        $this->validator->verifyToken()
            ->end();

        $this->validator->label('Nominativo del beneficiario')
            ->value(Input::post('beneficiary_name'))
            ->required()
            ->betweenString(4, 40)
            ->end();

        $this->validator->label('Dati fiscali non disponibili')
            ->value(Input::post('fiscal_data_not_available'))
            ->in('0,1')
            ->end();

        if (Input::post('fiscal_data_not_available') === '0') {
            $this->validator->label('Dati fiscali')
                ->value(Input::post('fiscal_data'))
                ->required()
                ->betweenString(4, 500)
                ->end();
        }

        $this->validator->label('Struttura organizzativa responsabile')
            ->value(Input::post('object_structures_id'))
            ->required()
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        if (Input::post('managers')) {
            foreach (explode(',', (string)Input::post('managers')) as $manager) {
                $this->validator->label('Dirigente o funzionario responsabile ' . $manager)
                    ->value($manager)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        } else {

            $this->validator->label('Dirigente o funzionario responsabile')
                ->value(Input::post('managers'))
                ->required()
                ->end();
        }

        $this->validator->label('Oggetto')
            ->value(Input::post('object'))
            ->required()
            ->betweenString(4, 60)
            ->end();

        $this->validator->label('Importo atto di concessione')
            ->value(Input::post('concession_amount'))
            ->required()
            ->isNumeric()
            ->end();

        $this->validator->label('Data atto di concessione')
            ->value(Input::post('concession_act_date'))
            ->required()
            ->isDate('Y-m-d')
            ->end();

        if (Input::post('normatives')) {
            foreach (Input::post('normatives') as $normative) {
                $this->validator->label('Normativa alla base dell\'attribuzione ' . $normative)
                    ->value($normative)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        $this->validator->label('Regolamento alla base dell\'attribuzione')
            ->value(Input::post('regulation_id'))
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        $this->validator->label('Data inizio')
            ->value(Input::post('start_date'))
            ->isDate('Y-m-d')
            ->end();

        $this->validator->label('Data fine')
            ->value(Input::post('end_date'))
            ->isDate('Y-m-d')
            ->add(function () {
                if (Input::post('end_date') < Input::post('start_date')) {

                    return ['error' => __('invalid_end_date_less', null, 'patos')];

                }
                return null;
            })
            ->end();

        $this->validator->label('Modalità seguita per l\'individuazione del beneficiario')
            ->value(Input::post('detection_mode'))
            ->required()
            ->betweenString(4, 1000)
            ->end();

        $this->validator->label('Note')
            ->value(Input::post('notes'))
            ->betweenString(4, 1000)
            ->end();

        $this->validator->label('Omissis (Privacy)')
            ->value(Input::post('privacy'))
            ->in('0,1')
            ->end();

        if ($mode === 'update') {

            $this->validator->label('id')
                ->value(Input::post('id'))
                ->required()
                ->isInt()
                ->isNaturalNoZero()
                ->add(function () {
                    $regulation = GrantsModel::where('id', Input::post('id'))->first();
                    if (empty($regulation)) {
                        return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Sovvenzione')];
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

                $grants = GrantsModel::select(['id', 'type', 'typology'])
                    ->whereIn('id', $ids)
                    ->get();

                if ($grants === null) {

                    $isError = false;
                } else {

                    Registry::set('__ids__multi_select_profile__', $grants);
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