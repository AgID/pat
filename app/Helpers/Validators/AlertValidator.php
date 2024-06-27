<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Model\ContestsActsModel;
use System\Input;
use System\Registry;
use System\Validator;

/**
 * Validator per l'oggetto Avviso(object_contests_acts)
 */
class AlertValidator
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
    public function validateUriSegmentId($checkOwner = null): array
    {
        $this->validator->label('ID delibera')
            ->value(uri()->segment(4, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () use ($checkOwner) {

                //Recupero l'avviso con l'id passato in input
                $check = ContestsActsModel::where('typology', 'alert')
                    ->where('id', uri()->segment(4, 0))
                    ->with('proceedings:id,object,cig,activation_date,expiration_date,type')
                    ->with('relative_measure:id,object,number,date')
                    ->with('relative_notice:id,object,type,cig,activation_date,expiration_date')
                    ->with(['structure' => function ($query) {
                        $query->select(['object_structures.id', 'object_structures.structure_name', 'object_structures.reference_email', 'object_structures.structure_of_belonging_id', 'parent.structure_name as parent_name'])
                            ->join('object_structures as parent', 'parent.id', '=', 'object_structures.structure_of_belonging_id', 'left outer');
                    }])
                    ->with(['rup' => function ($query) {
                        $query->select(['object_personnel.id', 'full_name', 'email', 'title', 'role_id', 'role.name']);
                        $query->join('role', 'role.id', '=', 'object_personnel.role_id');
                    }])
                    ->with(['public_in' => function ($query) {
                        $query->select(['public_in_id', 'contest_act_id', 'section_fo.name'])
                            ->join('section_fo', 'section_fo.id', '=', 'public_in_id');
                    }]);

                $check = $check->first();

                //Se non esiste l'esito con questo id, oppure l'utente non può modificare i record non creati da lui
                //mostro un messaggio di errore
                if ($check == null || (!empty($checkOwner) && !checkRecordOwner(@$check->owner_id))) {
                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id', null, 'patos'), 'avviso ')
                    ];
                }

                //Se esiste l'avviso lo salvo nel registro
                Registry::set('alert', $check);

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

        $this->validator->label('Oggetto')
            ->value(Input::post('object'))
            ->required()
            ->betweenString(4, 800)
            ->end();

        $this->validator->label('Bando di gara relativo')
            ->value(Input::post('notice_id'))
//            ->required()
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        if (Input::post('procedures')) {
            foreach (explode(',', (string)Input::post('procedures')) as $procedure) {
                $this->validator->label('Altra procedura ' . $procedure)
                    ->value($procedure)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        $this->validator->label('Ufficio')
            ->value(Input::post('object_structures_id'))
            ->required()
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        $this->validator->label('Data dell\'atto')
            ->value(Input::post('act_date'))
            ->isDate('Y-m-d')
            ->end();

        $activationData = !empty(Input::post('activation_date')) ? str_replace('T', ' ', Input::post('activation_date')) : '';

        $this->validator->label('Data di pubblicazione sul sito')
            ->value($activationData)
            ->required()
            ->isDate('Y-m-d H:i')
            ->add(function () use ($activationData){
                if ($activationData < Input::post('act_date')) {

                    return ['error' => __('invalid_end_date_publication_2', null, 'patos')];

                }
                return null;
            })
            ->end();

        $this->validator->label('Data di scadenza')
            ->value(Input::post('expiration_date'))
            ->isDate('Y-m-d')
            ->add(function () {
                if (Input::post('expiration_date') < Input::post('act_date')) {

                    return ['error' => __('invalid_expiration_date_2', null, 'patos')];

                }
                return null;
            })
            ->end();

        if (Input::post('public_in')) {
            foreach (Input::post('public_in') as $in) {
                $this->validator->label('Pubblica in ' . $in)
                    ->value($in)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        $this->validator->label('Settore')
            ->value(Input::post('sector'))
            ->required()
            ->in('O-sotto,O-sopra,S')
            ->end();

        $this->validator->label('RUP')
            ->value(Input::post('object_personnel_id'))
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        $this->validator->label('Note')
            ->value(Input::post('details'))
            ->betweenString(2, 1000)
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
                    $regulation = ContestsActsModel::where('id', Input::post('id'))->first();
                    if (empty($regulation)) {
                        return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Avviso')];
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
