<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Validators;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Model\GeneralActsDocumentsModel;
use System\Input;
use System\Registry;
use System\Validator;

class GeneralActsDocumentsValidator
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
     * Controlla la validità dell'ID nell'URI segment e se esiste un atto/documento con quell'ID per l'ente
     *
     * @param null $checkOwner Indica se controllare il proprietario del record
     * @return array
     * @throws Exception
     */
    public function validateUriSegmentId($checkOwner = null): array
    {
        $this->validator->label('ID atto/documento')
            ->value(uri()->segment(4, 0))
            ->required()
            ->isNaturalNoZero()
            ->isInt()
            ->add(function () use ($checkOwner) {

                //Recupero il bilancio con l'id passato in input
                $check = GeneralActsDocumentsModel::select()
                    ->with(['public_in' => function ($query) {
                        $query->select(['section_fo_id', 'section_fo_config_publication_archive.id', 'section_fo.name'])
                            ->join('section_fo', 'section_fo.id', '=', 'section_fo_id')
                            ->groupBy('section_fo_id');
                    }]);

                $check = $check->find(uri()->segment(4, 0));

                //Se non esiste l'esito con questo id, oppure l'utente non può modificare i record non creati da lui
                //mostro un messaggio di errore
                if ($check == null || (!empty($checkOwner) && !checkRecordOwner(@$check->owner_id))) {

                    return [
                        'error' => sprintf(__('error_validate_uri_segment_id', null, 'patos'), 'atto/documento ')
                    ];
                }

                //Se esiste il bilancio lo salvo nel registro
                Registry::set('general-acts-documents', $check);

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

        $this->validator->label('Oggetto')
            ->value(Input::post('object'))
            ->required()
            ->betweenString(2, 255)
            ->end();

        $this->validator->label('Note')
            ->value(Input::post('notes'))
            ->betweenString(4, 1000)
            ->end();

        $this->validator->label('Data documento')
            ->value(Input::post('document_date'))
            ->required()
            ->isDate('Y-m-d')
            ->end();

        $this->validator->label('Link esterno')
            ->value(Input::post('external_link'))
            ->isUrl(null, true)
            ->betweenString(2, 191)
            ->end();

        $this->validator->label('Pubblica in')
            ->value(Input::post('public_in'))
            ->required()
            ->isInt()
            ->isNaturalNoZero()
            ->end();

        if (Input::post('public_in')) {

            if(Input::post('public_in') == 583) {
                $this->validator->label('Tipologia')
                    ->value(Input::post('typology'))
                    ->required()
                    ->in('lavori,acquisti')
                    ->end();
            }

            if(Input::post('public_in') == 586) {
                $this->validator->label('Data avvio')
                    ->value(Input::post('start_date'))
                    ->isDate('Y-m-d')
                    ->end();

                $this->validator->label('CUP')
                    ->value(Input::post('cup'))
                    ->betweenString(1, 18)
                    ->end();

                $this->validator->label('Stato di attuazione finanziario e procedurale')
                    ->value(Input::post('procedural_implementation_status'))
                    ->betweenString(1, 255)
                    ->end();

                $this->validator->label('Fonti finanziarie')
                    ->value(Input::post('financial_sources'))
                    ->betweenString(1, 255)
                    ->end();

                $this->validator->label('Importo totale del finanziamento')
                    ->value(Input::post('financing_amount'))
                    ->isNumeric()
                    ->end();
            }
        }

        if ($mode === 'update') {

            $this->validator->label('id')
                ->value(Input::post('id'))
                ->required()
                ->isInt()
                ->isNaturalNoZero()
                ->add(function () {
                    $regulation = GeneralActsDocumentsModel::where('id', Input::post('id'))->first();
                    if (empty($regulation)) {
                        return ['error' => sprintf(__('not_exist_obj', null, 'patos'), 'Atti e Documenti di carattere generale')];
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

                $documents = GeneralActsDocumentsModel::select(['id', 'object'])
                    ->whereIn('id', $ids)
                    ->get();

                if ($documents === null) {

                    $isError = false;
                } else {

                    Registry::set('__ids__multi_select_profile__', $documents);
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
