<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

use Exception;
use Model\SectionsFoModel;
use System\Input;
use System\Registry;
use System\Validator;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller per la gestione della guida e dei riferimenti normativi delle pagine generiche da parte del superAdmin
 */
class SectionFoSuperAdminValidator
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
     * Controlla la validità dell'ID nell'URI segment e se esiste una delibera con quell'ID per l'ente
     *
     * @return array
     * @throws Exception
     */
    public function validateUriSegmentId(): array
    {
        $id = !empty(Input::post('page_id')) ? Input::post('page_id') : Input::get('id');

        $this->validator->label('ID Pagina')
            ->value($id)
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () {

                $arg = func_get_args();
                //Recupero la delibera con l'id passato in input
                $check = SectionsFoModel::find($arg[1]);

                //Se non esiste la delibera con questo id, mostro un messaggio di errore
                if ($check == null) {

                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id_2', null, 'patos'), 'pagina di front-office ')
                    ];
                }

                //Se esiste la delibera la salvo nel registro
                Registry::set('section_fo_page', $check);

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

        //Validatore per la modifica della guida o dei riferimenti normativi associati alla sezione,
        //o per l'inserimento di un nuovo riferimento normativo.
        if ($mode === 'insert' || $mode === 'update') {

            $this->validator->label('ID Pagina')
                ->value(Input::post('page_id'))
                ->required()
                ->isNaturalNoZero()
                ->isInt()
                ->end();

            $this->validator->label('Guida ai contenuti')
                ->value(Input::post('guide'))
                ->betweenString(4, 10000)
                ->end();

            if (Input::post('normatives')) {
                foreach (Input::post('normatives') as $normative) {
                    $this->validator->label('Normativa ' . $normative)
                        ->value($normative)
                        ->isInt()
                        ->isNaturalNoZero()
                        ->end();
                }
            }
        }

        //Validatore per la cancellazione di un riferimento normativo associato alla sezione
        if ($mode === 'delete') {

            if (Input::get('id')) {
                $this->validator->label('Id record di relazione Normativa-Sezione')
                    ->value(Input::get('id'))
                    ->required()
                    ->isNaturalNoZero()
                    ->isInt()
                    ->end();
            }
        }

        return [
            'is_success' => $this->validator->isSuccess(),
            'errors' => $this->validator->getErrorsHtml()
        ];
    }
}
