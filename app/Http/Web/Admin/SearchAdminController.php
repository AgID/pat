<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Admin;

use Exception;
use Helpers\Validators\SearchValidator;
use Model\PermitsModel;
use System\Input;
use System\JsonResponse;
use System\View;
use function authPatOs;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 *
 * Controller per il motore di ricerca globale
 *
 */
class SearchAdminController extends BaseAuthController
{
    protected string $getKeyword;

    /**
     * @description Costruttore della classe
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct('not_acl');
        helper('url');

        $this->getKeyword = trim((string)Input::get('s', true));
    }

    /**
     * @description Anteprima risultato di ricerca
     * @url admin/search
     * @return void
     * @throws Exception
     */
    public function index(): void
    {
        $this->breadcrumb->push('Risultati di ricerca', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        $getIdentity = authPatOs()->getIdentity();
        $profiles = unserialize($getIdentity['options']['profiles']);
        $query = PermitsModel::select(['permits.id as permitId', 'sbo.name', 'sbo.model', 'sbo.icon', 'permits.read', 'permits.sections_bo_id', 'sbo.id']);
        if (!isSuperAdmin()) {
            $query->whereIn('acl_profiles_id', $profiles);
        }
        $query->join('section_bo AS sbo', 'sbo.id', '=', 'permits.sections_bo_id');
        $query->where('read', 1);
        // ->where('parent_id', '>', 0);
        $query->whereNotNull('model');
        $query->where('model', '!=', 27);
        $result = $query->get();
        $result = !empty($result) ? $result->toArray() : [];
        $permits = collect($result)->unique('sections_bo_id');

        $data['titleSection'] = 'Risultati di ricerca';
        $data['subTitleSection'] = 'ANTEPRIMA DEI RISULTATI DI RICERCA';
        $data['sectionIcon'] = '<i class="fas fa-search"></i>';
        $data['token'] = Input::get(config('csrf_token_name',null,'app'));
        $data['keyToken'] = config('csrf_token_name',null,'app');
        // $data['info'] = '<i class="fas fa-search"></i>';

        $data['permits'] = $permits;

        render('search/index', $data, 'admin');
    }

    /**
     * @description Risultato di ricerca
     * @url search/result/nums
     * @return void
     * @throws Exception
     */
    public function resultSearchNums(): void
    {
        $json = new JsonResponse();
        $code = $json->success();
        $validator = new SearchValidator();

        $check = $validator->validateInputSearchNumb();

        if ($check['is_success']) {

            $config = config(Input::get('model'), null, 'modelConfigs');

            $classNameModel = $config['model'];
            $className = $classNameModel;

            $selectField = !empty($config['select_field']) ? $config['select_field'] : ['id'];
            $searchField = !empty($config['global_search_field']) ? $config['global_search_field'] : null;

            $query = $className::select($selectField);
            $query->search($this->getKeyword, $searchField);

            // Setto eventuali Join
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

            // Setto eventuali Where
            if (!empty($config['where'])) {
                foreach ($config['where'] as $value) {
                    $field = $value[0];
                    $operator = $value[1];
                    $value = $value[2];
                    $query->where($field, $operator, $value);
                }
            }

            $institutionId = (checkAlternativeInstitutionId() != 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

            $tableName = new $className();
            // Per le pagine generiche inserisco anche il controllo sull'id dell'ente dato che non hanno lo scope
            if(Input::get('model') == 23) {
                $query->where(function ($query) use($tableName, $institutionId){
                    $query->where($tableName->getTable().'.institution_id', $institutionId);
                    $query->orWhereNull($tableName->getTable().'.institution_id');
                });
            }

            if(!empty($this->getKeyword)) {
                $query->orWhere($tableName->getTable().'.id', '=' ,$this->getKeyword);
            }

            // Setto eventuali group by
            if (!empty($config['groupBy'])) {
                foreach ($config['groupBy'] as $value) {
                    $query->groupBy($value);
                }
            }

            $numRows = $query->get()->count();

            $json->set('num', $numRows);

        } else {

            $code = $json->bad();
            $json->error('error', $check['errors']);

        }

        $json->setStatusCode($code);
        $json->response();
    }

    /**
     * @description Risultati termine di ricerca
     * @url search/result/terms
     * @return void
     * @throws Exception
     */
    public function resultSearchTerms(): void
    {
        $json = new JsonResponse();
        $code = $json->success();
        $data = [];
        // $validator = new SearchValidator();

        // Calcolo Paginazione risultati di ricerca,
        $config = config(Input::get('model'), null, 'modelConfigs');
        $classNameModel = $config['model'];
        $className = $classNameModel;
        $perPage = !empty($config['per_page']) ? $config['per_page'] : 30;
        $query = $className::select($config['search_result_field']);
        $searchField = !empty($config['global_search_field']) ? $config['global_search_field'] : null;

        // Ricerca
        $query->search($this->getKeyword, $searchField);

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
                                $query->join($table, $foreignKey, $operator, $localKey);
                            }
                        }]);
                    } else {
                        $query->with($relation);
                    }
                }
            }
        }

        // Setto eventuali Join
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

        // Setto eventuali Where
        if (!empty($config['where'])) {

            foreach ($config['where'] as $value) {

                $field = $value[0];
                $operator = $value[1];
                $value = $value[2];
                $query->where($field, $operator, $value);
            }
        }

        $institutionId = (checkAlternativeInstitutionId() != 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        $tableName = new $className();
        //Per le pagine generiche inserisco anche il controllo sull'id dell'ente dato che non hanno lo scope
        if(Input::get('model') == 23) {
            $query->where(function ($query) use($tableName, $institutionId){
                $query->where($tableName->getTable().'.institution_id', $institutionId);
                $query->orWhereNull($tableName->getTable().'.institution_id');
            });
        }

        if(!empty($this->getKeyword)) {
            $query->orWhere($tableName->getTable().'.id', '=' ,$this->getKeyword);
        }

        // Setto eventuali group by
        if (!empty($config['groupBy'])) {
            foreach ($config['groupBy'] as $value) {
                $query->groupBy($value);
            }
        }

        // Paginazione
        $querySQL = $query->paginate($perPage, null, 'p', (int)Input::get('p'));
        $querySQL->appends(Input::get(['sid', 'type', 'model', 's', 'per_page', 'page']))
            ->setPath(currentUrl());

        // Risultati paginazione
        $results = !empty($querySQL) ? $querySQL->toArray() : [];

        // Valorizzo le variabili
        $data['pagination'] = paginateBootstrap($results, 'render');
        $data['results'] = $results;

        // Se il template esiste l'appendo nella vista
        $viewData = !empty($config['search_result_template'])
            ? View::create($config['search_result_template'], $data, 'admin')->render()
            : null;

        // Setto i dati della risposta
        $json->set('template', $viewData);
        $json->set('results', $results);
        $json->set('current_query_string_url', currentQueryStringUrl());

        // stampo a video i risultati (Response)
        $json->setStatusCode($code);
        $json->response();
    }
}
