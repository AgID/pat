<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Model\NormativesModel;
use System\Input;
use System\Registry;
use System\Validator;

/**
 * Validator per l'oggetto Normativa (object_normatives)
 */
class NormativeValidator
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
     * Controlla la validità dell'ID nell'URI segment e se esiste una normativa con quell'ID per l'ente
     *
     * @param null $checkOwner Indica se controllare il proprietario del record
     * @return array
     * @throws Exception
     */
    public function validateUriSegmentId($checkOwner = null): array
    {
        $this->validator->label('ID normativa')
            ->value(uri()->segment(4, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () use ($checkOwner) {

                //Recupero la normativa con l'id passato in input
                $check = NormativesModel::where('id', uri()->segment(4, 0))
                    ->with(['structures' => function ($query) {
                        $query->select(['object_structures.id', 'object_structures.structure_name', 'object_structures.structure_of_belonging_id',
                            'object_structures.reference_email', 'parent.structure_name as parent_name'])
                            ->join('object_structures as parent', 'parent.id', '=', 'object_structures.structure_of_belonging_id', 'left outer');
                    }])
                    ->first();

                //Se non esiste l'esito con questo id, oppure l'utente non può modificare i record non creati da lui
                //mostro un messaggio di errore
                if ($check == null || (!empty($checkOwner) && !checkRecordOwner(@$check->owner_id))) {

                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id_2', null, 'patos'), 'normativa ')
                    ];
                }

                //Se esiste la normativa la salvo nel registro
                Registry::set('normative', $check);

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

        $this->validator->label('Tipologia atto')
            ->value(Input::post('act_type'))
            ->required()
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        $this->validator->label('Numero')
            ->value(Input::post('number'))
            ->isInt()
            ->end();

        $this->validator->label('Protocollo')
            ->value(Input::post('protocol'))
            ->isInt()
            ->isNatural()
            ->end();

        $this->validator->label('Data promulgazione')
            ->value(Input::post('issue_date'))
            ->required()
            ->isDate('Y-m-d')
            ->end();

        $this->validator->label('Titolo della Norma')
            ->value(Input::post('name'))
            ->required()
            ->betweenString(4, 250)
            ->end();

        $this->validator->label('Argomento della Normativa')
            ->value(Input::post('normative_topic'))
            ->required()
            ->in('1,2,3')
            ->end();

        $this->validator->label('Inserisci link a Normativa')
            ->value(Input::post('normative_link'))
            ->isUrl(null, true)
            ->betweenString(4, 191)
            ->end();

        if (Input::post('structures')) {
            foreach (explode(',', (string)Input::post('structures')) as $structure) {
                $this->validator->label('Struttura ' . $structure)
                    ->value($structure)
                    ->isInt()
                    ->isNaturalNoZero()
                    ->end();
            }
        }

        $this->validator->label('Testo di descrizione')
            ->value(Input::post('description'))
            ->betweenString(4, 1000)
            ->end();

        if ($mode === 'update') {

            $this->validator->label('id')
                ->value(Input::post('id'))
                ->required()
                ->isInt()
                ->isNaturalNoZero()
                ->add(function () {
                    $regulation = NormativesModel::where('id', Input::post('id'))->first();
                    if (empty($regulation)) {
                        return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Normativa')];
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

                $normatives = NormativesModel::select(['id', 'name'])
                    ->whereIn('id', $ids)
                    ->get();

                if ($normatives === null) {

                    $isError = false;
                } else {

                    Registry::set('__ids__multi_select_profile__', $normatives);
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
