<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Admin;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Helpers\ActivityLog;
use Helpers\Utility\ButtonAction;
use Helpers\Validators\AclUserProfileValidator;
use Helpers\Validators\DatatableValidator;
use Model\AclProfilesModel;
use Model\PermitsModel;
use Model\RelUsersAclProfilesModel;
use System\Hierarchy;
use System\Input;
use System\JsonResponse;
use System\Registry;
use System\Security;
use System\Token;

/**
 *
 * Controller Profili ACL
 *
 */
class AclUsersProfileAdminController extends BaseAuthController
{
    /**
     * @description Costruttore
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct(__CLASS__);
        helper('AclUsersProfilesHelper');
    }

    /**
     * @description Renderizza la pagina index dei Profili ACL
     *
     * @return void
     * @method GET
     * @throws Exception
     * @url /admin/acl-users-profile.html
     */
    public function index(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('read');

        $this->breadcrumb->push('Profili ACL', '/');
        $data = [];

        //Dati header della sezione
        $data['titleSection'] = 'Gestione Profili Acl';
        $data['subTitleSection'] = 'GESTIONE DEI PROFILI ACCESS CONTROL LIST PER GLI AMMINISTRATORI';
        $data['sectionIcon'] = '<i class="fas fa-unlock-alt fa-3x"></i>';

        $data['breadcrumbs'] = $this->breadcrumb->show();
        $data['profiles'] = AclProfilesModel::get()->toArray();
        render('acl_users_profile/index', $data, 'admin');
    }

    /**
     * @description Funzione per la paginazione dei dati nel datatable
     *
     * @url /admin/acl-users-profile/list.html
     * @return void
     * @throws Exception
     */
    public function asyncPaginateDatatable(): void
    {
        // Setto il metodo della rotta per i permessi
        $this->acl->setRoute('read');

        // Validatore datatable
        $datatableValidator = new DatatableValidator();
        $validator = $datatableValidator->validate();
        $response = [];

        //Controllo se è una richiesta Ajax e se il datatable è stato validato correttamente
        if (Input::isAjax() === true && $validator['is_success'] === true) {

            $data = [];

            //Colonne su cui poter effettuare l'ordinamento
            $orderable = [
                1 => 'name',
                2 => 'description',
            ];

            //Se l'utente è super Admin mostro la colonna dell'ente
            if (isSuperAdmin(true)) {
                $orderable[5] = 'i.full_name_institution';
            }

            //Setto proprietà datatable
            $draw = !empty(Input::get('draw')) ? Input::get('draw', true) : 1;
            $start = !empty(Input::get("start")) ? (int)Input::get("start", true) : 0;
            $rowPerPage = !empty(Input::get("length")) ? Input::get("length", true) : 25;

            $security = new Security();

            $columnIndexArr = Input::get('order', true);
            $columnNameArr = Input::get('columns', true);
            $orderArr = Input::get('order', true);
            $searchArr = !empty($_GET['search']) ? $security->xssClean(removeInvisibleCharacters($_GET['search'])) : null;

            $columnIndex = !empty($columnIndexArr[0]['column']) ? $columnIndexArr[0]['column'] : null;
            $columnName = !empty($columnNameArr[$columnIndex]['data']) ? (int)$columnNameArr[$columnIndex]['data'] : 'name';
            $columnSortOrder = !empty($orderArr[0]['dir']) ? $orderArr[0]['dir'] : 'ASC';
            $searchValue = !empty($searchArr['value']) ? $searchArr['value'] : null;

            $institutionId = (checkAlternativeInstitutionId() ? checkAlternativeInstitutionId() : PatOsInstituteId());

            $totalRecords = AclProfilesModel::select(['count(id) as allcount'])
                ->where(function ($query) use ($institutionId) {
                    $query->where('acl_profiles.institution_id', '=', $institutionId);
                    $query->orWhereNull('institution_id');
                })
                ->count();

            $totalRecordsWithFilter = AclProfilesModel::search($searchValue)
                ->select(['count(acl_profiles.id) as allcount'])
                ->where(function ($query) use ($institutionId) {
                    $query->where('acl_profiles.institution_id', '=', $institutionId);
                    $query->orWhereNull('institution_id');
                })
                ->count();

            $order = setOrderDatatable($columnName, $orderable, 'name');

            $records = AclProfilesModel::search($searchValue)
                ->select(['acl_profiles.id', 'acl_profiles.institution_id', 'name', 'is_system', 'description', 'acl_profiles.updated_at',
                    'i.full_name_institution'])
                ->with('institution:id,full_name_institution')
                ->where(function ($query) use ($institutionId) {
                    $query->where('acl_profiles.institution_id', '=', $institutionId);
                    $query->orWhereNull('institution_id');
                })
                ->join('institutions as i', 'acl_profiles.institution_id', '=', 'i.id', 'left outer')
                ->orderBy('acl_profiles.is_system', 'desc')
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

                    $isSystem = (bool)$record['is_system'];

                    //Setto i pulsanti da mostrare in base ai permessi dell'utente e se è un profilo di sistema o meno
                    $buttonAction = ButtonAction::create([
                        'view' => isSuperAdmin() ? $this->acl->getRead() : $this->acl->getRead(),
                        'edit' => isSuperAdmin() ? $this->acl->getUpdate() : $this->acl->getUpdate() && !$isSystem,
                        'duplicate' => isSuperAdmin() ? $this->acl->getCreate() : ($this->acl->getCreate() && !$isSystem),
                        'delete' => isSuperAdmin() ? $this->acl->getDelete() : $this->acl->getDelete() && !$isSystem,
                    ])
                        ->addView('admin/acl-users-profile/read-only/' . $record['id'], $record['id'])
                        ->addEdit('admin/acl-users-profile/edit/' . $record['id'], $record['id'])
                        ->addDuplicate('admin/acl-users-profile/clone/' . $record['id'], $record['id'])
                        ->addDelete('admin/acl-users-profile/delete/' . $record['id'], $record['id'])
                        ->render();

                    //Setto i dati da mostrare nelle colonne del datatable
                    $setTempData = [];
                    $setTempData[] = !$isSystem ? ButtonAction::checkList('item[]', $record['id']) : '<i class="fas fa-ban text-muted"></i>';
                    $setTempData[] = !empty($record['name']) ? escapeXss($record['name']) : 'N.D.';
                    $setTempData[] = !empty($record['description']) ? escapeXss($record['description']) : 'N.D.';

                    //Se è un SuperAdmin mostro la colonna dell'Ente
                    if (isSuperAdmin(true)) {
                        $setTempData[] = !empty($record['institution']['full_name_institution']) && $record['is_system'] === 0
                            ? '<small class="badge badge-primary" style="font-size: 85%;">' . escapeXss($record['institution']['full_name_institution']) . '</small>'
                            : '<small class="badge badge-success" style="font-size: 85%;">Di Sistema</small>';

                    } else {
                        //Se non è SuperAdmin mostro solo il tipo di profilo
                        $setTempData[] = $record['is_system'] === 0
                            ? '<small class="badge badge-primary" style="font-size: 85%;">Dell\' Ente</small>'
                            : '<small class="badge badge-success" style="font-size: 85%;">Di Sistema</small>';

                    }

                    $setTempData[] = $buttonAction;

                    $data[] = $setTempData;
                }

                $response ['aaData'] = $data;

            }

            echo json_encode($response);

        }

    }

    /**
     * @description Renderizza il form per la creazione di un nuovo Profilo Acl
     *
     * @return void
     * @method GET
     * @throws Exception
     * @url /admin/acl-users-profile/create.html
     */
    public function create(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(__FUNCTION__);

        $institutionId = checkAlternativeInstitutionId();

        $sectionBackOffice = new Hierarchy();
        $sectionFrontOffice = new Hierarchy('front_office');

        $this->breadcrumb->push('Profili ACL', 'admin/acl-users-profile');
        $this->breadcrumb->push('Nuovo', '/');
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Gestione Profili Acl';
        $data['subTitleSection'] = 'GESTIONE DEI PROFILI ACCESS CONTROL LIST PER GLI AMMINISTRATORI';
        $data['sectionIcon'] = '<i class="fas fa-unlock-alt fa-3x"></i>';

        $data['sectionBackOffice'] = $sectionBackOffice->getGroupedChildren();
        $data['sectionFrontOffice'] = ($sectionFrontOffice) ? $sectionFrontOffice->getGroupedChildren(0, $institutionId) : null;
        $data['formAction'] = '/admin/acl-users-profile/store';
        $data['formSettings'] = [
            'name' => 'form_acl_profile',
            'id' => 'form_acl_profile',
            'class' => 'form_acl_profile',
        ];
        $data['_storageType'] = 'insert';

        render('acl_users_profile/form_store', $data, 'admin');
    }

    /**
     * @description Renderizza la vista di sola visualizzazione di un profilo acl
     *
     * @return void
     * @method GET
     * @throws Exception
     * @url /admin/acl-users-profile/read-only/(num).html
     */
    public function readOnly(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('read');

        $validator = new AclUserProfileValidator();
        $validate = $validator->validateUriSegmentId();

        $institutionId = checkAlternativeInstitutionId();

        if (!$validate['is_success']) {

            redirect('admin/acl-users-profile', sessionSetNotify($validate['errors'], 'danger'));
            exit();

        }

        $sectionBackOffice = new Hierarchy();
        $sectionFrontOffice = new Hierarchy('front_office');

        $this->breadcrumb->push('Profili ACL', 'admin/acl-users-profile');
        $this->breadcrumb->push('Nuovo', '/');

        // Query Profile
        $profile = AclProfilesModel::with('permits')
            // ->with('usersAclProfiles')
            ->find(uri()->segment(4, 0))
            ->toArray();

        // Assegnazione e valorizzazioni dei estratti nel database.
        $data['title'] = $profile['name'];
        $data['description'] = $profile['description'];
        $data['institution_id'] = $profile['institution_id'];
        $data['id'] = $profile['id'];
        $data['created_at'] = $profile['created_at'];
        $data['updated_at'] = $profile['updated_at'];
        $data['permits'] = $profile['permits'];
        $data['versioning'] = $profile['versioning'];
        $data['archiving'] = $profile['archiving'];
        $data['is_system'] = $profile['is_system'];
        $data['lock_user'] = $profile['lock_user'];
        $data['advanced'] = $profile['advanced'];
        $data['fileArchive'] = $profile['file_archive'];
        $data['editorWishing'] = $profile['editor_wishing'];
        $data['breadcrumbs'] = $this->breadcrumb->show();
        $data['titleSection'] = 'Gestione Profili Acl';
        $data['subTitleSection'] = 'GESTIONE DEI PROFILI ACCESS CONTROL LIST PER GLI AMMINISTRATORI';
        $data['sectionIcon'] = '<i class="fas fa-unlock-alt fa-3x"></i>';
        $data['sectionBackOffice'] = $sectionBackOffice->getGroupedChildren();
        $data['sectionFrontOffice'] = ($sectionFrontOffice) ? $sectionFrontOffice->getGroupedChildren(0, $institutionId) : null;

        $data['_storageType'] = 'read_only';

        render('acl_users_profile/read_only', $data, 'admin');
    }

    /**
     * @description Modalità di visualizzazione editor
     *
     * @param $editor
     * @return string
     */
    private static function editorWishing($editor)
    {
        $data = 'Base';

        if ($editor == 'adv') {
            $data = 'Avanzato';
        }

        return $data;
    }

    /**
     * @description Renderizza il form per la duplicazione di un profilo acl
     *
     * @return void
     * @method GET
     * @throws Exception
     * @url /admin/acl-users-profile/create.html
     */
    public function clone(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('update');

        // Validatore...
        $validator = new AclUserProfileValidator();
        $validate = $validator->validateUriSegmentId();

        if (!$validate['is_success']) {

            redirect('admin/acl-users-profile', sessionSetNotify($validate['errors'], 'danger'));
            exit();

        }

        $sectionBackOffice = new Hierarchy();
        $sectionFrontOffice = new Hierarchy('front_office');

        $this->breadcrumb->push('Profili ACL', 'admin/acl-users-profile');
        $this->breadcrumb->push('Nuovo', '/');

        // Query Profile
        $profile = AclProfilesModel::with('permits')
            // ->with('usersAclProfiles')
            ->find(uri()->segment(4, 0))->toArray();

        // Assegnazione e valorizzazioni dei estratti nel database.
        $data['title'] = $profile['name'];
        $data['description'] = $profile['description'];
        $data['institution_id'] = $profile['institution_id'];
        $data['id'] = $profile['id'];
        $data['created_at'] = $profile['created_at'];
        $data['updated_at'] = $profile['updated_at'];
        $data['permits'] = $profile['permits'];
        $data['breadcrumbs'] = $this->breadcrumb->show();
        $data['titleSection'] = 'Profili ACL';
        $data['sectionBackOffice'] = $sectionBackOffice->getGroupedChildren();
        $data['sectionFrontOffice'] = $sectionFrontOffice->getGroupedChildren(0, false);

        // $test = multiSearch($data['permits'], ['sections_bo_id' => 0, 'sections_fo_id' => 20]);

        $data['formAction'] = '/admin/acl-users-profile/store';
        $data['formSettings'] = [
            'name' => 'form_acl_profile',
            'id' => 'form_acl_profile',
            'class' => 'form_acl_profile',
        ];

        $data['_storageType'] = 'clone';

        render('acl_users_profile/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua lo storage di un nuovo Profilo ACL
     *
     * @return void
     * @method POST
     * @throws Exception
     * @url /admin/acl-users-profile/store.html
     */
    public function store(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $validator = new AclUserProfileValidator();
        $code = $json->success();

        $validate = $validator->storage();

        if ($validate['is_success']) {

            $sectionsFrontOffice = Input::post('section_fo', true);
            $institutionId = checkAlternativeInstitutionId();
            $profileACL = Registry::get('aclProfilePost');
            $dataProfile = [];
            $isSystem = 0;

            if (isSuperAdmin(true) === true) {

                // Setto l'ID dell'ente se la select è valorizzata
                if (!empty(Input::post('select_institution_id'))) {

                    $institutionId = strip_tags((int)Input::post('select_institution_id', true));
                }

                $isSystem = !empty(Input::post('is_system')) ? (int)Input::post('is_system', true) : 0;

            }

            // ACL Profiles
            $dataAclProfile = [
                'is_system' => $isSystem,
                'name' => strip_tags((string)Input::post('title', true)),
                'description' => strip_tags((string)Input::post('description', true)),
                'lock_user' => strip_tags((int)Input::post('lock_user', true)),
            ];

            if (empty($isSystem)) {
                $dataAclProfile['institution_id'] = $institutionId;
            }

            // Storage nuovo Profilo ACL
            $insert = AclProfilesModel::create($dataAclProfile);

            $insertId = $insert->id;

            foreach ($profileACL as $key => $value) {

                //Setto i permessi sulle sezioni di back office
                $dataProfile[] = [
                    'acl_profiles_id' => $insertId,
                    'sections_bo_id' => (int)$key,
                    'sections_fo_id' => null,
                    'institution_id' => $institutionId,
                    'create' => !empty($value['add']) ? (int)$value['add'] : 0,
                    'read' => !empty($value['read']) ? (int)$value['read'] : 0,
                    'update' => !empty($value['modify']) ? (int)$value['modify'] : 0,
                    'delete' => !empty($value['delete']) ? (int)$value['delete'] : 0,
                    'send_notify_app_io' => !empty($value['app_io']) ? (int)$value['app_io'] : 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

            }

            if ($sectionsFrontOffice != null) {

                foreach ($sectionsFrontOffice as $key => $value) {

                    //Setto i permessi sulle sezioni di front office
                    $dataProfile[] = [
                        'acl_profiles_id' => $insertId,
                        'sections_bo_id' => 44,
                        'sections_fo_id' => (int)$key,
                        'institution_id' => $institutionId,
                        'create' => 1,
                        'read' => 1,
                        'update' => 1,
                        'delete' => 1,
                        'send_notify_app_io' => 0,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                }

            }

            //Salvo i permessi associati al profilo nel db
            $insert = PermitsModel::insert($dataProfile);

            // Storage Activity log
            ActivityLog::create([
                'action' => 'Creazione nuovo profilo ACL "' . strip_tags((string)Input::post('title', true)),
                'description' => Input::post('description', true),
                'request_post' => [
                    'post' => @$_POST,
                    'get' => Input::get(),
                    'server' => Input::server(),
                ],
                'action_type' => 'addACLInstance',
                'object_id' => 55,
                'record_id' => $insertId,
                'area' => 'acl',
                'platform' => 'all',
            ]);

            if ($insert === true) {

                // Generazione nuovo token
                Token::forceRegenerate((int)config('csrf_expire', null, 'app'));

                $json->set('message', 'Operazione avvenuta con successo!');

            } else {

                $code = $json->bad();
                $json->error('error', 'Errore temporaneo, riprovare pi&ugrave; tardi. <br /> Se il problema persiste, contattare il servizio assistenza.');

            }

        } else {

            $code = $json->bad();
            $json->error('error', $validate['errors']);

        }

        $json->setStatusCode($code);
        $json->response();

    }

    /**
     * @description Funzione che effettua l'update di un Profilo ACL
     *
     * @return void
     * @method POST
     * @throws Exception
     * @url /admin/acl-users-profile/update.html
     */
    public function update(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(__FUNCTION__);

        $json = new JsonResponse();
        $validator = new AclUserProfileValidator();
        $code = $json->success();

        $validate = $validator->storage('update');

        if ($validate['is_success']) {

            // Dati per registrazione ActivityLog
            $getIdentity = authPatOs()->getIdentity(['id', 'username']);

            $sectionsFrontOffice = Input::post('section_fo', true);
            $institutionId = checkAlternativeInstitutionId();
            $profileACL = Registry::get('aclProfilePost');
            $dataProfile = [];
            $isSystem = 0;

            if (isSuperAdmin(true) === true) {

                // Setto l'ID dell'ente se la select è valorizzata
                if (!empty(Input::post('select_institution_id')) && Input::post('is_system') != 1) {

                    $institutionId = strip_tags((int)Input::post('select_institution_id'));
                }

                $isSystem = !empty(Input::post('is_system')) ? strip_tags(Input::post('is_system', true)) : 0;

            }

            // Query Profile
            // Recupero il profilo ACL attuale prima di modificarlo e lo salvo nel versioning
            $profile = AclProfilesModel::with('permits')
                ->find(Input::post('id'))
                ->toArray();

            // ACL Profiles UPDATE
            $dataAclProfile = [
                'is_system' => $isSystem,
                'name' => strip_tags((string)Input::post('title', true)),
                'description' => strip_tags((string)Input::post('description', true)),
                'lock_user' => strip_tags((int)Input::post('lock_user', true)),
            ];

            if (empty($isSystem)) {
                $dataAclProfile['institution_id'] = $institutionId;
            }

            //Update sul db del profilo ACL
            AclProfilesModel::where('id', '=', (int)Input::post('id'))
                ->update($dataAclProfile);

            //Elimino i permessi associati al profilo prima di reinserirli aggiornati
            PermitsModel::where('acl_profiles_id', '=', (int)Input::post('id'))
                ->delete();

            foreach ($profileACL as $key => $value) {

                //Setto i permessi sulle sezioni back office aggiornati
                $dataProfile[] = [
                    'acl_profiles_id' => (int)Input::post('id'),
                    'sections_bo_id' => (int)$key,
                    'sections_fo_id' => null,
                    'institution_id' => $institutionId,
                    'create' => !empty($value['add']) ? (int)$value['add'] : 0,
                    'read' => !empty($value['read']) ? (int)$value['read'] : 0,
                    'update' => !empty($value['modify']) ? (int)$value['modify'] : 0,
                    'delete' => !empty($value['delete']) ? (int)$value['delete'] : 0,
                    'send_notify_app_io' => !empty($value['app_io']) ? (int)$value['app_io'] : 0,
                    'created_at' => date('Y-m-d H:i:s'),
                    'updated_at' => date('Y-m-d H:i:s')
                ];

            }

            if ($sectionsFrontOffice != null) {

                foreach ($sectionsFrontOffice as $key => $value) {

                    //Setto i permessi sulle sezioni front office aggiornati
                    $dataProfile[] = [
                        'acl_profiles_id' => (int)Input::post('id'),
                        'sections_bo_id' => 44,
                        'sections_fo_id' => (int)$key,
                        'institution_id' => $institutionId,
                        'create' => 1,
                        'read' => 1,
                        'update' => 1,
                        'delete' => 1,
                        'send_notify_app_io' => 0,
                        'created_at' => date('Y-m-d H:i:s'),
                        'updated_at' => date('Y-m-d H:i:s')
                    ];

                }

            }

            //Update dei permessi associati al profilo
            $insert = PermitsModel::insert($dataProfile);

            // Storage Activity log
            ActivityLog::create([
                'action' => 'Modifica profilo ACL "' . strip_tags((string)Input::post('title', true)),
                'description' => Input::post('description', true),
                'request_post' => [
                    'post' => @$_POST,
                    'get' => Input::get(),
                    'server' => Input::server(),
                ],
                'action_type' => 'updateACLInstance',
                'object_id' => 55,
                'record_id' => (int)Input::post('id'),
                'area' => 'acl',
                'platform' => 'all',
            ]);

            if ($insert === true) {

                $json->set('message', 'Operazione avvenuta con successo!');

                // Generazione nuovo token
                Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
            } else {

                $code = $json->bad();
                $json->error('error', 'Errore temporaneo, riprovare pi&ugrave; tardi. <br /> Se il problema persiste, contattare il servizio assistenza.');

            }

        } else {

            $code = $json->bad();
            $json->error('error', $validate['errors']);

        }

        $json->setStatusCode($code);
        $json->response();

    }

    /**
     * @description Funzione che effettua l'eliminazione di un profilo ACL
     *
     * @return void
     * @method GET
     * @throws Exception
     * @url /admin/acl-users-profile/delete.html
     */
    public function delete(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(__FUNCTION__);

        $validator = new AclUserProfileValidator();
        $validate = $validator->validateUriSegmentId();

        if (!$validate['is_success']) {

            redirect('admin/acl-users-profile', sessionSetNotify($validate['errors'], 'danger'));
            exit();

        }

        //Recupero il profilo da eliminare dal Registro
        $profile = Registry::get('temp_profile');

        if(!empty($profile)){
            $profileId = $profile->id;
            $institutionId = $profile->institution_id;

            //Elimino il profilo (con tutti i relativi permessi)
            $profile->delete();

            // Create Activity Log
            ActivityLog::create([
                'action' => 'Eliminazione profilo ACL "' . $profile['name'],
                'description' => $profile['description'],
                'request_post' => [
                    'post' => @$_POST,
                    'get' => Input::get(),
                    'server' => Input::server(),
                ],
                'action_type' => 'deleteACLInstance',
                'object_id' => 55,
                'record_id' => $profile['id'],
                'area' => 'acl',
                'platform' => 'all',
            ]);

            PermitsModel::where('acl_profiles_id', '=', $profileId)
                ->where('institution_id', '=',$institutionId)
                ->delete();

            RelUsersAclProfilesModel::where('acl_profile_id', '=', $profileId)
                ->delete();

            // Generazione nuovo token
            Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
            sessionSetNotify(sprintf(__('success_delete', null, 'patos'), 'Profilo ACL '));

            redirect('admin/acl-users-profile');
        } else {
            redirect('admin/acl-users-profile', sessionSetNotify('Profilo non trovato!', 'danger'));
            exit();
        }
    }

    /**
     * @description Renderizza il form di modifica di un Profilo Acl
     *
     * @return void
     * @method GET
     * @throws Exception
     * @url /admin/acl-users-profile/edit.html
     */
    public function edit(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('update');

        //Validatore che verifica se il profilo ACL da modificare esiste
        $validator = new AclUserProfileValidator();
        $validate = $validator->validateUriSegmentId('edit');

        if (!$validate['is_success']) {

            redirect('admin/acl-users-profile', sessionSetNotify($validate['errors'], 'danger'));
            exit();

        }

        $sectionBackOffice = new Hierarchy();
        $sectionFrontOffice = new Hierarchy('front_office');

        $this->breadcrumb->push('Profili ACL', 'admin/acl-users-profile');
        $this->breadcrumb->push('Nuovo', '/');

        // Query Profile
        $profile = AclProfilesModel::with('permits')
            ->find(uri()->segment(4, 0))
            ->toArray();

        // Assegnazione e valorizzazioni dei estratti nel database.
        $data['title'] = $profile['name'];
        $data['description'] = $profile['description'];
        $data['institution_id'] = $profile['institution_id'];
        $data['id'] = $profile['id'];
        $data['created_at'] = $profile['created_at'];
        $data['updated_at'] = $profile['updated_at'];
        $data['permits'] = $profile['permits'];
        $data['lock_user'] = $profile['lock_user'];
        $data['breadcrumbs'] = $this->breadcrumb->show();
        //Dati header della sezione
        $data['titleSection'] = 'Gestione Profili Acl';
        $data['subTitleSection'] = 'GESTIONE DEI PROFILI ACCESS CONTROL LIST PER GLI AMMINISTRATORI';
        $data['sectionIcon'] = '<i class="fas fa-unlock-alt fa-3x"></i>';

        $institutionId = $profile['institution_id'];

        $data['sectionBackOffice'] = $sectionBackOffice->getGroupedChildren();
        $data['sectionFrontOffice'] = ($sectionFrontOffice) ? $sectionFrontOffice->getGroupedChildren(0, $institutionId) : null;

        $data['formAction'] = '/admin/acl-users-profile/update';
        $data['formSettings'] = [
            'name' => 'form_acl_profile',
            'id' => 'form_acl_profile',
            'class' => 'form_acl_profile',
        ];

        $data['_storageType'] = 'edit';

        render('acl_users_profile/form_store', $data, 'admin');
    }

    /**
     * @description Funzione per l'eliminazione multipla dei profili selezionati
     *
     * @return void
     * @method GET
     * @throws Exception
     * @url /admin/acl-users-profile/deletes.html
     */
    public function deletes(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('delete');

        //Validatore per le operazioni multiple
        $validator = new AclUserProfileValidator();
        $validate = $validator->multipleSelection(false);

        if ($validate['is_success'] === true) {

            sessionSetNotify('Operazione avvenuta con successo');

            //Recupero i profili selezionati da eliminare
            $profilesAcl = Registry::get('__ids__multi_select_profile__');
            $deletedIds = [];
            $institutionId = checkAlternativeInstitutionId();

            foreach ($profilesAcl as $profile) {

                //Eliminazione del profilo con i relativi permessi (vedere nel modello)
                $profile->delete();
                $deletedIds[] = $profile->id;


                // Storage Activity log
                ActivityLog::create([
                    'action' => 'Eliminazione Profilo ACL',
                    'description' => 'Eliminazione Profilo ACL "' . $profile['name'] . '" con ID ' . $profile['id'],
                    'request_post' => [
                        'post' => @$_POST,
                        'get' => Input::get(),
                        'server' => Input::server(),
                    ],
                    'action_type' => 'deleteACLInstance',
                    'record_id' => $profile['id'],
                    'object_id' => 55,
                    'area' => 'acl',
                    'platform' => 'all',
                ]);
            }

            PermitsModel::whereIn('acl_profiles_id', $deletedIds)
                ->where('institution_id', '=',$institutionId)
                ->delete();

            RelUsersAclProfilesModel::whereIn('acl_profile_id', $deletedIds)
                ->delete();

            // Generazione nuovo token
            Token::forceRegenerate((int)config('csrf_expire', null, 'app'));

        } else {

            sessionSetNotify($validate['errors'], 'danger');

        }

        redirect('/admin/acl-users-profile');
    }
}
