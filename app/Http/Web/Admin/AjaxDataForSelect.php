<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Admin;

use Helpers\Validators\DataForSelectValidator;
use System\Database;
use System\Input;
use System\JsonResponse;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 *
 * Controller per gestire le chiamate Ajax per i dati delle select2
 *
 */
class AjaxDataForSelect extends BaseAuthController
{
    public function __construct()
    {
        parent::__construct();
    }

    /**
     * Funzione che restituisce i dati selezionati(form di update) per le select nei form
     *
     * @url /admin/asyncSelectedData.html
     */
    public function asyncGetSelectedData(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('read');

        $json = new JsonResponse();
        $code = $json->success();
        $data = [];
        $db = new Database();

        //Validatore select2
        $selectDataValidator = new DataForSelectValidator();
        $validator = $selectDataValidator->validate('selected');

        //Controllo se è una richiesta Ajax e se la validazione è andata a buon fine
        if (Input::isAjax() === true && $validator['is_success'] === true) {

            $model = Input::get('model');
            $config = config($model, null, 'modelConfigs');
            $hasError = false;

            if (empty($config['model'])) {

                $hasError = true;
                $code = $json->bad();
                $json->error('error', 'Errore nell caricamento del config. Contattare il servizio assistenza');
            }

            //Setto il modello su cui effettuare la query
            $class = $config['model'];

            if (!class_exists($class)) {

                $hasError = true;
                $code = $json->bad();
                $json->error('error', 'Errore nell caricamento del modello. Contattare il servizio assistenza');
            }

            if (!$hasError) {

                // Setto il campo da mostrare nella select
                $field = !empty(Input::get('field')) ? [Input::get('field')] : config($model, null, 'modelConfigs')['field'];
                //$filed = sanitizeArray($field);

                if ($field == null) {

                    $code = $json->bad();
                    $json->error('error', 'parametri non validi');
                } else {

                    //Se è un modello archiviabile, nella select includo il campo "archived" per verificare quali record sono stati archiviati
                    $archived = !empty(config($model, null, 'modelConfigs')['archived_field']) ? implode(',', config($model, null, 'modelConfigs')['archived_field']) : null;
                    $fields = !empty($archived) ? ['id', 'archived'] : ['id'];

                    //Query per prelevare i dati dal db
                    $query = $class::whereIn("id", explode(',', (string)Input::get('id')));

                    // Set eventuali scope per filtri specifici
//                    if (!empty($config['scope'])) {
//                        $scope = $config['scope'];
//                        $query->$scope();
//                    }

                    //Per gli utenti
                    if ($model == 39) {
                        $query->select($field);
                    } else {
                        $query->select([$db::raw("CONCAT(" . implode(",' - ',", $field) . ") AS text")]);
                    }

                    $query->addSelect($fields);

                    // Applico esplicitamente il filtro per ente alla query.
                    // Altrimenti nelle select vedo gli oggetti di tutti gli enti


                    $items = $query->get();
                    $items = !empty($items) ? $items->toArray() : [];

                    //Dati da restituire alla select2
                    if (!empty($items)) {
                        foreach ($items as $item) {
                            //Se è un elemento archiviabile, controllo se è stato archiviato o meno
                            $archived = (!empty($item['archived']) && $item['archived'])
                                ? '<b>[Elemento archiviato]</b> '
                                : null;

                            //Per gli utenti
                            if ($model == 39) {
                                $name = urldecode(escapeXss(urlencode(checkDecrypt($item['name']))));
                                $userName = urldecode(escapeXss(urlencode(checkDecrypt($item['username']))));

                                $item['text'] = $name . ' - ' . $userName;
                            }

                            $data[] = [
                                'id' => (int)$item['id'],
                                'text' => $archived . urldecode(escapeXss(urlencode(checkDecrypt($item['text'])), true, false))
                            ];
                        }
                    }
                }
            }
        } else {

            //Messaggio in caso di errore
            $code = $json->bad();
            $json->error('error', $validator['errors']);
        }

        $json->setStatusCode($code);

        // //Setto i dati nella risposta
        $json->set('selected', $data);

        $json->response();
    }

    /**
     * Funzione che restituisce i dati selezionati con paginazione
     * Le select tabellari chiamano sempre questa funzione
     *
     * @url async/get/data.html
     */
    public function asyncGetPaginationSelectedData(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('read');

        $json = new JsonResponse();
        $code = $json->success();
        $db = new Database();

        //Validatore select2
        $selectDataValidator = new DataForSelectValidator();
        $validator = $selectDataValidator->validate();
        $data = [];

        //Controllo se è una richiesta Ajax e se la validazione è andata a buon fine
        if (Input::isAjax() && $validator['is_success']) {

            $perPage = Input::get('per_page') != null ? (int)Input::get('per_page') : 5;

            $model = Input::get('model');
            $modelConfig = config($model, null, 'modelConfigs');

            $hasError = false;

            if (empty($modelConfig['model'])) {

                $hasError = true;
                $code = $json->bad();
                $json->error('error', 'Errore nell caricamento del config. Contattare il servizio assistenza');
            }

            //Setto il modello su cui effettuare la query
            $class = $modelConfig['model'];

            if (!class_exists($class)) {

                $hasError = true;
                $code = $json->bad();
                $json->error('error', 'Errore nell caricamento del modello. Contattare il servizio assistenza');
            }

            //Setto il modello su cui effettuare la query
            $class = config($model, null, 'modelConfigs')['model'];

            if (!$hasError) {
                //Setto il campo da mostrare nella select
//                $field = !empty(Input::get('field')) ? Input::get('field') : config($model, null, 'modelConfigs')['field'];

                $searchTerm = !empty(Input::get('searchTerm')) ? Input::get('searchTerm') : null;

                // Setto i campi su cui effettuare la ricerca
                $searchField = !empty(Input::get('field')) ? [Input::get('field')] : $modelConfig['search_field'];

                $fieldWhereHas = $modelConfig['fieldWhereHas'] ?? null;

                $exactSearch = !empty(Input::get('exact_search')) && (bool)Input::get('exact_search');

                //Query per prelevare i dati dal db
                $query = $class::search($searchTerm, $searchField, $fieldWhereHas, null, null, [], $exactSearch);

                // Applico esplicitamente il filtro per ente alla query.
                // Altrimenti nelle select vedo gli oggetti di tutti gli enti





                //Impedisco di selezionare nella select l'elemento stesso che si sta modificando
                //ES: nella struttura di appartenenza impedisco che si possa selezionare la struttura stessa
                if (!empty(Input::get('item_id'))) {
                    $query->where($modelConfig['table'] . '.id', '!=', Input::get('item_id'));
                }


                if(!empty($modelConfig['whereHas'])){
                    $query->whereHas($modelConfig['whereHas']);
                }

                // Set Join
                if (!empty($modelConfig['joinSelect2'])) {
                    foreach ($modelConfig['joinSelect2'] as $value) {
                        $table = $value[0];
                        if (!in_array($table, ['users', 'institutions as i'])) {
                            $foreignKey = $value[1];
                            $operator = $value[2];
                            $localKey = $value[3];
                            $typeJoin = !empty($value[4]) ? $value[4] : null;
                            $query->join($table, $foreignKey, $operator, $localKey, $typeJoin);
                        }
                    }
                }

                // Setto eventuali Where
                if (!empty($modelConfig['where'])) {
                    foreach ($modelConfig['where'] as $value) {
                        $cond = $value[0];
                        $operator = $value[1];
                        $value = $value[2];
                        $otherCondition = !empty($value[3]) ? $value[3] : 'and';
                        $query->where($cond, $operator, $value);
                    }
                }

                // Setto eventuali WhereIn
                if (!empty($modelConfig['whereIn'])) {
                    foreach ($modelConfig['whereIn'] as $value) {
                        $field = $value[0];
                        $value = $value[1];
                        $otherCondition = !empty($value[3]) ? $value[3] : 'and';
                        $query->whereIn($field, $value);
                    }
                }

                $selecFields = !empty($modelConfig['select2_field']) ? $modelConfig['select2_field'] : $modelConfig['search_result_field'];

                //Per mettere i record già selezionati(nel caso di edit) per primi cosi da vederli nella prima pagina della
                //select tabellare. In tal caso si devono passare nella chiamata ajax gli ID degi record già selezionati
//                if(Input::get('p') == 1 || empty(Input::get('p'))) {
//                    $query->orderByRaw(
//                        "CASE WHEN " . $modelConfig['table'] . ".id IN (421,427) THEN 0 ELSE 1 END"
//                    );
//                } else {
//                $query->orderBy($modelConfig['table'] . '.updated_at', 'DESC');
//                }

                $field = !empty(Input::get('order_field')) ? Input::get('order_field') : 'updated_at';
                $ord = !empty(Input::get('order_type')) ? Input::get('order_type') : 'DESC';

                $query->orderBy($modelConfig['table'] . '.'.$field, $ord);

                $items = $query->paginate($perPage, $selecFields, 'p', boundedQueryInt('p', 10000))
                    ->onEachSide(2)
                    ->setPath(currentUrl())
                    ->appends(Input::get(['model', 'institution_id', 'searchTerm', 'per_page']));

                $items = !empty($items) ? $items->toArray() : [];

                foreach ($items as $k => $v) {
                    $tmp[$k] = escapeXss($v, true, false);
                }
                $items = $tmp;

                $json->set('results', $items);
            }
        }

        $json->setStatusCode($code);
        //Setto i dati nella risposta

        $json->response();

        jsonOut($data);
    }


    /**
     * Funzione che restituisce i dati da mostrare nelle options per le select nei form
     *
     * @url /admin/asyncData.html
     */
    public function asyncGetData(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('read');

        $json = new JsonResponse();
        $code = $json->success();
        $db = new Database();

        //Validatore select2
        $selectDataValidator = new DataForSelectValidator();
        $validator = $selectDataValidator->validate();
        $data = [];

        //Controllo se è una richiesta Ajax e se la validazione è andata a buon fine
        if ($validator['is_success'] === true) {

            $model = Input::get('model');
            $modelConfig = config($model, null, 'modelConfigs');
            $hasError = false;

            if (empty($modelConfig['model'])) {

                $hasError = true;
                $code = $json->bad();
                $json->error('error', 'Errore nell caricamento del config. Contattare il servizio assistenza');
            }

            // Setto il modello su cui effettuare la query
            $class = $modelConfig['model'];

            if (!class_exists($class)) {
                $hasError = true;
                $code = $json->bad();
                $json->error('error', 'Errore nell caricamento del modello. Contattare il servizio assistenza');
            }

            if (!$hasError) {
                // Setto il campo da mostrare nella select
                $field = !empty(Input::get('field')) ? [Input::get('field')] : $modelConfig['field'];
                //$filed = sanitizeArray($field);

                if ($field == null) {

                    $code = $json->bad();
                    $json->error('error', 'parametri non validi');
                } else {

                    // Setto i campi su cui effettuare la ricerca
                    $searchField = !empty(Input::get('field')) ? [Input::get('field')] : $modelConfig['search_field'];

                    $searchTerm = !empty(Input::get('searchTerm')) ? Input::get('searchTerm') : null;

                    // Query per prelevare i dati dal db
                    $query = $class::search($searchTerm, $searchField);

                    // Set eventuali scope per filtri specifici
                    if (!empty($modelConfig['scope'])) {
                        $scope = $modelConfig['scope'];
                        $query->$scope();
                    }

                    //sto cercando in section_fo
                    if($model == 43 and !empty($searchTerm) and is_numeric($searchTerm)){
                        //il search generico parte solo con stringe >= 4
                        $query->where('section_fo.id', trim($searchTerm));
                    }


                    //Se è un modello archiviabile, nella select includo il campo "archived" per verificare quali record sono stati archiviati
                    $archived = !empty($modelConfig['archived_field']) ? implode(',', $modelConfig['archived_field']) : null;
                    $fields = !empty($archived) ? ['id', 'archived'] : ['id'];

                    //Per gli utenti
                    if ($model == 39 or $model == 48) {
                        $query->select($field);
                    } else {
                        // Concateno i campi da mostrare nella select
                        $query->select([$db::raw("CONCAT(" . implode(", ' - ',", $field) . ") AS text")]);
                    }

                    $query->addSelect($fields);

                    if($model == 48) {
                        $query->whereHas('profiles', function($q){
                            $q->whereIn('acl_profiles.id', [1,74]);
                        });
                    }

                    // Applico esplicitamente il filtro per ente alla query.
                    // Altrimenti nelle select vedo gli oggetti di tutti gli enti
                     /*elseif ($model == 27 && !empty(Input::get('institution_id'))) {
                        //Profili ACL - prendo quelli dell'ente e quelli di sistema
                        $query->where(function ($query) {
                            $query->where('institution_id', Input::get('institution_id'))
                                ->where('is_system', 0);
                        })
                            ->orWhere(function ($query) {
                                $query->where('is_system', 1)
                                    ->whereNull('institution_id');
                            });

                    }*/

                    // Setto eventuali Where
                    if (!empty($modelConfig['where'])) {
                        foreach ($modelConfig['where'] as $value) {
                            $cond = $value[0];
                            $operator = $value[1];
                            $value = $value[2];
                            $otherCondition = !empty($value[3]) ? $value[3] : 'and';
                            $query->where($cond, $operator, $value);
                        }
                    }

                    // Setto eventuali WhereIn
                    if (!empty($modelConfig['whereIn'])) {
                        foreach ($modelConfig['whereIn'] as $value) {
                            $field = $value[0];
                            $value = $value[1];
                            $otherCondition = !empty($value[3]) ? $value[3] : 'and';
                            $query->whereIn($field, $value);
                        }
                    }

                    if(empty($modelConfig['limit'])) {
                        $query->limit(20);
                    } else {
                        $query->limit($modelConfig['limit']);
                    }

                    $items = $query->get();
                    $items = !empty($items) ? $items->toArray() : [];

                    //Dati da restituire alla select2
                    if (!empty($items)) {

                        foreach ($items as $item) {

                            //Se è un elemento archiviabile, controllo se è stato archiviato o meno
                            $archived = (!empty($item['archived']) && $item['archived'])
                                ? '<b>[Elemento archiviato]</b> '
                                : null;

                            //Per gli utenti
                            if ($model == 39 || $model == 48) {
                                $name = urldecode(escapeXss(urlencode(checkDecrypt($item['name']))));
                                $userName = urldecode(escapeXss(urlencode(checkDecrypt($item['username']))));

                                $item['text'] = $name . ' - ' . $userName;
                            }

                            if(!empty($item['text'])){
                                $data[] = [
                                    'id' => (int)$item['id'],
                                    'text' => $archived . urldecode(escapeXss(urlencode(checkDecrypt($item['text'])), true, false))
                                ];
                            }

                        }
                        if ($model == 28) {
                            $data = $data ?? null;
                        }

                    }
                }
            }
        } else {

            //Messaggio in caso di errore
            $code = $json->bad();
            $json->error('error', $validator['errors']);
        }

        $json->setStatusCode($code);

        //Setto i dati nella risposta
        $json->set('options', $data);
        echo $json->response();
    }
}
