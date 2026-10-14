<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Admin;

use Exception;
use Helpers\Validators\DatatableValidator;
use Model\ActivityLogModel;
use Scope\DeletedScope;
use System\Input;
use System\Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class UserLogAdminController extends BaseAuthController
{
    /**
     * @description Costruttore
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct(__CLASS__);
    }

    /**
     * @return void
     * @throws Exception
     * @url /admin/activity-log.html
     * @method GET
     */
    public function index(): void
    {
        $responsible = patOsInstituteInfo(['trasp_responsible_user_id']);
        $responsible = $responsible['trasp_responsible_user_id'] ?? null;
        $data = [];

        $getIdentity = $this->auth->getIdentity();
        if(!empty($getIdentity['technical_user']) || $getIdentity['id'] == $responsible) {
            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute('read');

            $this->breadcrumb->push('Log degli utenti', '/');

            $data['breadcrumbs'] = $this->breadcrumb->show();

            //Dati header della sezione
            $data['titleSection'] = 'Log degli utenti';
            $data['subTitleSection'] = 'ATTIVITÀ EFFETTUATE SUGLI UTENTI';
            $data['sectionIcon'] = '<i class="far fa-chart-bar fa-3x"></i>';

            $data['formAction'] = '/admin/user-log';
            $data['formSettings'] = [
                'name' => 'form_user-log',
                'id' => 'form_user-log',
                'class' => 'form_user-log',
            ];
        }

        render('user_log/index', $data, 'admin');
    }

    /**
     * Funzione per la paginazione dei dati nel datatable
     *
     * @return void
     * @url /admin/activity-log/list.html
     * @throws Exception
     */
    public function asyncPaginateDatatable()
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('read');

        //Validatore datatable
        $datatableValidator = new DatatableValidator();
        $validator = $datatableValidator->validate();

        //Controllo se è una richiesta Ajax e se il datatable è stato validato correttamente
        if (Input::isAjax() === true && $validator['is_success'] === true) {

            $data = [];

            //Colonne su cui poter effettuare l'ordinamento (devono corrispondere all'ordine delle colonne mostrate:
            //0 = NOME UTENTE, 1 = DATA, 2 = AZIONE, 3 = DESCRIZIONE)
            $orderable = [
                1 => 'created_at',
                2 => 'action',
            ];

            // Setto proprietà datatable
            $draw = !empty(Input::get('draw')) ? Input::get('draw', true) : 1;
            $start = !empty(Input::get("start")) ? (int)Input::get("start", true) : 0;
            $rowPerPage = !empty(Input::get("length")) ? Input::get("length", true) : 25;

            $columnIndexArr = Input::get('order', true);
            $columnNameArr = Input::get('columns', true);
            $orderArr = Input::get('order', true);
            $searchArr = Input::get('search', true);

            $columnIndex = !empty($columnIndexArr[0]['column']) ? $columnIndexArr[0]['column'] : null;
            $columnName = !empty($columnNameArr[$columnIndex]['data']) ? (int)$columnNameArr[$columnIndex]['data'] : 'activity_log.created_at';
            $columnSortOrder = !empty($orderArr[0]['dir']) ? $orderArr[0]['dir'] : 'DESC';
            $searchValue = !empty($searchArr['value']) ? $searchArr['value'] : null;

            // Query per i dati da mostrare nel datatable
            $totalRecords = ActivityLogModel::select(['count(id) as allcount'])
                ->where('object_id', '=', 54)
                ->count();

            $totalRecordsWithFilter = ActivityLogModel::search($searchValue)
                ->select(['count(*) as allcount'])
                ->where('object_id', '=', 54)
                ->join('users as u', 'activity_log.user_id', '=', 'u.id', 'left outer')
                ->count();

            $order = setOrderDatatable($columnName, $orderable, 'activity_log.created_at');

            $records = ActivityLogModel::search($searchValue)
                ->select(['activity_log.id', 'activity_log.user_id', 'activity_log.institution_id', 'description', 'object_id', 'record_id', 'area',
                    'action', 'referer', 'activity_log.origin', 'activity_log.updated_at', 'activity_log.created_at', 'u.name', 'i.full_name_institution'])
                ->where('object_id', '=', 54)
                ->join('users as u', 'u.id', '=', 'activity_log.user_id', 'left outer')
                ->join('institutions as i', 'activity_log.institution_id', '=', 'i.id', 'left outer')
                ->with(['user' => function ($query) {
                    $query->withoutGlobalScopes([DeletedScope::class]);
                    $query->select(['id', 'name', 'deleted']);
                }])
                ->with(['institution' => function ($query) {
                    $query->select(['id', 'full_name_institution']);
                }])
                ->orderBy($order, $columnSortOrder)
                ->offset($start)
                ->limit($rowPerPage)
                ->get()
                ->toArray();

            $response ['draw'] = intval($draw);
            $response ['iTotalRecords'] = ($totalRecords);
            $response ['iTotalDisplayRecords'] = ($totalRecordsWithFilter);
            $response ['aaData'] = [];

            if (!empty($records)) {

                foreach ($records as $record) {

                    if (!empty($record['created_at'])) {

                        $updateAt = '<small class="badge badge-info"> 
                                    <i class="far fa-clock"></i> '
                            . date('d-m-Y H:i:s', strtotime($record['created_at'])) . '</small>';

                    } else {

                        $updateAt = '<small class="badge badge-danger">N.D.</small>';

                    }

                    //Setto i dati da mostrare nelle colonne del datatable
                    $setTempData = [];
                    $setTempData[] = createdByCheckDeleted(@$record['user']['name'], @$record['user']['deleted'] ?? 0);
                    $setTempData[] = $updateAt;
                    $setTempData[] = !empty($record['action']) ? escapeXss($record['action']) : 'N.D.';
                    $setTempData[] = !empty($record['description']) ? ($record['description']) : 'N.D.';

                    $setTempData[] = '<span class="badge badge-secondary border border-dark">Web</span>';

                    $data[] = $setTempData;
                }

                $response ['aaData'] = $data;

            }

            jsonOut($response);

        }

    }

}
