<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

use Exception;
use Helpers\Validators\SearchValidator;
use Model\SectionsBoModel;
use System\Input;
use System\JsonResponse;
use System\Token;
use System\Validator;
use System\View;

defined('_FRAMEWORK_') or exit('No direct script access allowed');


class SearchFrontController extends BaseFrontController
{

    protected string $getKeyword;

    /**
     * Costruttore
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        helper(['url', 'form']);

        if (!empty(Input::get('sec_token')) && !Token::verify('sec_token')) {
            echo showError('Attenzione', 'Richiesta non valida.<br />Si prega di tornare indietro e ricaricare la pagina');
            exit();
        }

        $this->getKeyword = (trim((string)Input::get('s', true)));
    }

    /**
     * Metodo per il risultato di ricerca generale
     *
     * @url /search
     * @return void
     * @throws Exception
     */
    public function index(): void
    {
        $successValidate = true;
        $errorHtml = null;
        $sections = null;

        $validation = new Validator();
        if (Input::get('s') != null) {
            // $validation->verifyToken();
        }

        $validation->label('Frase da ricercare')
            ->value(trim(Input::get('s')))
            ->betweenString('3', '255')
            ->required()
            ->end();


        if (!$validation->isSuccess()) {

            $successValidate = false;
            $errorHtml = $validation->getErrorsHtml();

        }

        if ($successValidate) {

            $sections = SectionsBoModel::select(['id', 'name', 'model'])
                ->whereNotNull('model')
                ->where('searchable', 1)
                ->orderBy('search_sort')
                ->get()
                ->toArray();
        }

        $data['successValidate'] = $successValidate;
        $data['errorHtml'] = $errorHtml;
        $data['sections'] = $sections;
        $data['keyword'] = Input::get('s', true);
        $data['token'] = Input::get(config('csrf_token_name', null, 'app'));
        $data['keyToken'] = config('csrf_token_name', null, 'app');
        $data['notEditable'] = true;

        renderFront(config('vfo', null, 'app') . '/search/index', $data, 'frontend');
    }

    /**
     * Numero di occorrenze trovate nelle sezioni
     *
     * @url /search/nums
     * @return void
     * @throws Exception
     */
    public function resultSearchNums(): void
    {


        $json = new JsonResponse();
        $code = $json->success();
        $validator = new SearchValidator();

        // Validatore input motore di ricerca
        $check = $validator->validateInputSearchNumb(false);

        if ($check['is_success']) {

            $config = config(Input::get('model'), null, 'modelFrontConfigs');

            if (!empty($config)) {
                $classNameModel = $config['model'];
                $className = '\\Model\\' . $classNameModel;

                $selectField = !empty($config['select_field']) ? $config['select_field'] : ['id'];

                $field = $config['field'];
                $fieldWhereHas = $config['fieldWhereHas'] ?? null;
                $fieldDate = $config['date_field'] ?? null;
                $modelScope = $config['modelScope'] ?? [];

                $query = $className::search($this->getKeyword, $field, $fieldWhereHas, true, $fieldDate, $modelScope);
                $query = $query->select($selectField);

                // Set Join
                if (!empty($config['join'])) {
                    foreach ($config['join'] as $value) {
                        $table = $value[0];
                        $foreignKey = $value[1];
                        $operator = $value[2];
                        $localKey = $value[3];
                        $typeJoin = !empty($value[4]) ? $value[4] : null;
                        $query->join($table, $foreignKey, $operator, $localKey, $typeJoin);
                    }
                }

                // Set Where
                if (!empty($config['where'])) {
                    foreach ($config['where'] as $value) {
                        $field = $value[0];
                        $operator = $value[1];
                        $value = $value[2];
                        $otherCondition = !empty($value[3]) ? $value[3] : 'and';
                        $query->where($field, $operator, $value);
                    }
                }

                // Set Where
                if (!empty($config['whereIn'])) {
                    foreach ($config['whereIn'] as $value) {
                        $field = $value[0];
                        $value = $value[1];
                        $otherCondition = !empty($value[3]) ? $value[3] : 'and';
                        $query->whereIn($field, $value);
                    }
                }

                // Setto eventuali scope per filtri specifici
                if (!empty($config['scope'])) {
                    foreach ($config['scope'] as $scope) {
                        $query->$scope();
                    }
                }

                // Setto eventuali group by
                if (!empty($config['groupBy'])) {
                    foreach ($config['groupBy'] as $value) {
                        $query->groupBy($value);
                    }
                }

                $institutionId = (checkAlternativeInstitutionId() != 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

                //Per le pagine generiche inserisco anche il controllo sull'id dell'ente dato che non hanno lo scope
                if (Input::get('model') == 23) {
                    $tableName = new $className();
                    $query->where(function ($query) use ($tableName, $institutionId) {
                        $query->where($tableName->getTable() . '.institution_id', $institutionId);
                        $query->orWhereNull($tableName->getTable() . '.institution_id');
                    });
                }

                $numRows = $query->get()->count();

                $json->set('num', $numRows);
            }
        }

        $json->setStatusCode($code);
        $json->response();
    }

    /**
     * Ritorna i record filtrati per la sezione selezionata
     *
     * @url /search/terms
     * @return void
     * @throws Exception
     */
    public function resultSearchTerms(): void
    {
        if (!Input::isAjax()) {

            echo show404();
            die();

        } else {

            $json = new JsonResponse();
            $code = $json->success();
            $data = [];
            // $validator = new SearchValidator();

            // Calcolo Paginazione risultati di ricerca,
            $config = config(Input::get('model'), null, 'modelFrontConfigs');
            $classNameModel = $config['model'];
            $className = '\\Model\\' . $classNameModel;
            $perPage = !empty($config['per_page']) ? $config['per_page'] : 30;
            $field = $config['field'];
            $fieldWhereHas = $config['fieldWhereHas'] ?? null;
            $fieldDate = $config['date_field'] ?? null;
            $modelScope = $config['modelScope'] ?? [];

            // Ricerca
            $query = $className::search($this->getKeyword, $field, $fieldWhereHas, true, $fieldDate, $modelScope);
            $query = $query->select($config['search_result_field']);

            // Relazione (With)
            if (!empty($config['with'])) {
                foreach ($config['with'] as $with) {
                    if (!empty($with['relation'])) {
                        $relation = $with['relation'];
                        $fields = !empty($with['select']) ? $with['select'] : false;
                        if ($fields) {
                            $query->with([$relation => function ($query) use ($fields) {
                                $query->select($fields);
                                if (!empty($with['join'])) {
                                    $withJoin = $with['join'];
                                    $table = $withJoin[0];
                                    $foreignKey = $withJoin[1];
                                    $operator = $withJoin[2];
                                    $localKey = $withJoin[3];
                                    $typeJoin = !empty($withJoin[4]) ? $withJoin[4] : null;
                                    $query->join($table, $foreignKey, $operator, $localKey);
                                }
                            }]);
                        } else {
                            $query->with($relation);
                        }
                    }
                }
            }

            // Set Join
            if (!empty($config['join'])) {
                foreach ($config['join'] as $value) {
                    $table = $value[0];
                    $foreignKey = $value[1];
                    $operator = $value[2];
                    $localKey = $value[3];
                    $typeJoin = !empty($value[4]) ? $value[4] : null;
                    $query->join($table, $foreignKey, $operator, $localKey, $typeJoin);
                }
            }

            // Set Where
            if (!empty($config['where'])) {

                foreach ($config['where'] as $value) {

                    $field = $value[0];
                    $operator = $value[1];
                    $value = $value[2];
                    $otherCondition = !empty($value[3]) ? $value[3] : 'and';
                    $query->where($field, $operator, $value);
                }
            }

            // Setto eventuali scope per filtri specifici
            if (!empty($config['scope'])) {
                foreach ($config['scope'] as $scope) {
                    $query->$scope();
                }
            }

            // Setto eventuali group by
            if (!empty($config['groupBy'])) {
                foreach ($config['groupBy'] as $value) {
                    $query->groupBy($value);
                }
            }

            $institutionId = (checkAlternativeInstitutionId() != 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

            //Per le pagine generiche inserisco anche il controllo sull'id dell'ente dato che non hanno lo scope
            if (Input::get('model') == 23) {
                $tableName = new $className();
                $query->where(function ($query) use ($tableName, $institutionId) {
                    $query->where($tableName->getTable() . '.institution_id', $institutionId);
                    $query->orWhereNull($tableName->getTable() . '.institution_id');
                });
            }

            // Paginazione
            $querySQL = $query->paginate($perPage, null, 'p', (int)Input::get('p'));
            $querySQL->appends(Input::get(['sid', 'type', 'model', 's', 'per_page', 'page']))
                ->setPath(currentUrl());

            // Risultati paginazione
            $results = !empty($querySQL) ? $querySQL->toArray() : [];

            $institution_info = patOsInstituteInfo()['show_update_date'];

            // Valorizzo le variabili
            $data['pagination'] = paginateBootstrap($results, 'render');
            $data['results'] = $results;
            $data['institution_info'] = $institution_info;

            // Se il template esiste l'appendo nella vista
            $viewData = !empty($config['search_result_template'])
                ? View::create($config['search_result_template'], $data, 'frontend')->render()
                : null;

            // Setto i dati della risposta
            $json->set('template', $viewData);
            $json->set('results', $results);
            $json->set('current_query_string_url', currentQueryStringUrl());

            // stampo a video i risultati (Response)
            $json->setStatusCode($code);
            $json->response();

        }

        $json->setStatusCode($code);
        $json->response();
    }
}
