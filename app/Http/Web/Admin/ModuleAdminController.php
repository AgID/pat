<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Admin;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Helpers\Utility\AttachmentArchive;
use Helpers\Utility\ButtonAction;
use Helpers\Validators\DatatableValidator;
use Helpers\Validators\ModuleValidator;
use Model\ModulesRegulationsModel;
use Scope\DeletedScope;
use System\Arr;
use System\Input;
use System\JsonResponse;
use System\Registry;
use System\Security;
use System\Session;
use System\Token;

/**
 *
 * Controller Modulistica
 *
 */
class ModuleAdminController extends BaseAuthController
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
     * @description Renderizza la pagina index dei Moduli
     * @return void
     * @throws Exception
     * @url /admin/module.html
     * @method GET
     */
    public function index(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['read', 'create']);

        $this->breadcrumb->push('Modulistica', '/');
        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Modulistica';
        $data['subTitleSection'] = 'GESTIONE DELLA MODULISTICA DELL\'ENTE';
        $data['sectionIcon'] = '<i class="fas fa-paste fa-3x"></i>';
        $data['formAction'] = '/admin/module';
        $data['formSettings'] = [
            'name' => 'form_module',
            'id' => 'form_module',
            'class' => 'form_module',
        ];

        $data['include'] = '';

        $data['url'] = uri()->segment(2, 0);

        render('module/index', $data, 'admin');
    }

    /**
     * @description Funzione per la paginazione dei dati nel datatable
     *
     * @return void
     * @url /admin/module/list.html
     * @method AJAX
     * @throws Exception
     */
    public function asyncPaginateDatatable(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('read');

        //Validatore datatable
        $datatableValidator = new DatatableValidator();
        $validator = $datatableValidator->validate();
        $response = [];

        //Controllo se è una richiesta Ajax e se il datatable è stato validato correttamente
        if (Input::isAjax() === true && $validator['is_success'] === true) {

            $data = [];

            //Colonne su cui poter effettuare l'ordinamento
            $orderable = [
                1 => 'title',
                3 => 'userName',
                4 => 'updated_at'
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
            $columnName = !empty($columnNameArr[$columnIndex]['data']) ? (int)$columnNameArr[$columnIndex]['data'] : 'title';
            $columnSortOrder = !empty($orderArr[0]['dir']) ? $orderArr[0]['dir'] : 'ASC';
            $searchValue = !empty($searchArr['value']) ? $searchArr['value'] : null;

            //Query per i dati da mostrare nel datatable
            $totalRecords = ModulesRegulationsModel::select('count(object_modules_regulations.id) as allcount')
                ->count();

            $totalRecordsWithFilter = ModulesRegulationsModel::search($searchValue)
                ->select(['count(object_modules_regulations.is) as allcount'])
                ->join('users', 'users.id', '=', 'object_modules_regulations.owner_id', 'left outer');
            if (!empty($dataTable['searchValue'])) {
                $totalRecordsWithFilter->orWhere('object_modules_regulations.id', '=', $dataTable['searchValue']);
            }
            $totalRecordsWithFilter = $totalRecordsWithFilter->count();

            $order = setOrderDatatable($columnName, $orderable, 'title');

            $records = ModulesRegulationsModel::search($searchValue)
                ->select(['object_modules_regulations.id', 'title', 'object_modules_regulations.owner_id', 'object_modules_regulations.updated_at',
                    'object_modules_regulations.institution_id', 'object_modules_regulations.publishing_status', 'users.name as userName', 'i.full_name_institution',
                    'typology'])
                ->join('users', 'users.id', '=', 'object_modules_regulations.owner_id', 'left outer')
                ->with('proceedings:id,name,archived')
                ->with(['created_by' => function ($query) {
                    $query->withoutGlobalScopes([DeletedScope::class]);
                    $query->select(['id', 'name', 'deleted']);
                }])
                ->with('institution:id,full_name_institution');

            if (!empty($dataTable['searchValue'])) {
                $records->orWhere('object_modules_regulations.id', '=', $dataTable['searchValue']);
            }

            $records = $records->join('institutions as i', 'object_modules_regulations.institution_id', '=', 'i.id', 'left outer')
                ->orderBy($order, $columnSortOrder)
                ->offset($start)
                ->limit($rowPerPage)
                ->get()
                ->toArray();

            $response['draw'] = intval($draw);
            $response['iTotalRecords'] = ($totalRecords);
            $response['iTotalDisplayRecords'] = ($totalRecordsWithFilter);
            $response['aaData'] = [];

            if (!empty($records)) {

                foreach ($records as $record) {

                    if ($record['typology'] == 'dichiarazione sostitutiva') {
                        $recordUrl = siteUrl('/page/101/details/' . $record['id'] . '/' . urlTitle($record['title']));
                    } else {
                        $recordUrl = siteUrl('/page/30/details/' . $record['id'] . '/' . urlTitle($record['title']));
                    }

                    if (!empty($record['proceedings']) && is_array($record['proceedings'])) {

                        $tmpProceedings = Arr::pluck($record['proceedings'], 'name');
                        $tmpArchProceedings = Arr::pluck($record['proceedings'], 'archived');
                        $proceedings = str_replace(',', ',' . nbs(2), implode(
                            ',',
                            array_map(
                                function ($number) {
                                    return ('<small class="badge badge-primary mb-1">'
                                        . escapeXss($number) . '</small>');
                                },
                                $tmpProceedings,
                                $tmpArchProceedings
                            )
                        ));
                    } else {

                        $proceedings = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definiti">N.D.</small>';
                    }

                    if (!empty($record['updated_at'])) {
                        $updateAt = '<small class="badge badge-info"> 
                                    <i class="far fa-clock"></i> '
                            . date('d-m-Y H:i:s', strtotime($record['updated_at'])) .
                            '</small>';
                    } else {
                        $updateAt = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definita">N.D.</small>';
                    }

                    // Controllo se l'utente ha i permessi di modifica dei record o di scrittura(e quindi di modifica dei propri record)
                    $permits = ($this->acl->getCreate() && checkRecordOwner($record['owner_id']));
                    $updatePermits = ($this->acl->getUpdate() && checkRecordOwner($record['owner_id']));

                    //Setto i pulsanti delle actions da mostrare in base ai permessi dell'utente
                    $buttonAction = ($this->acl->getUpdate() || $this->acl->getDelete() || $permits) ? ButtonAction::create([
                        'edit' => $this->acl->getUpdate() || $permits,
                        'duplicate' => $this->acl->getCreate(),
                        'delete' => $this->acl->getDelete() || $updatePermits || $permits,
                    ])
                        ->addEdit('admin/module/edit/' . $record['id'], $record['id'])
                        ->addDuplicate('admin/module/duplicate/' . $record['id'], $record['id'])
                        ->addDelete('admin/module/delete/' . $record['id'], $record['id'])
                        ->render() : '';

                    $icon = null;

                    //Setto i dati da mostrare nelle colonne del datatable
                    $setTempData = [];
                    $setTempData[] = ($this->acl->getCrud() || checkRecordOwner($record['owner_id'])) ? ButtonAction::checkList('item[]', $record['id']) : '';
                    $setTempData[] = $icon . (!empty($record['title'])
                            ? '<a href="' . $recordUrl . '" target="_blank">' . escapeXss($record['title']) . '</a>'
                            : 'N.D.');
                    $setTempData[] = $proceedings;
                    $setTempData[] = createdByCheckDeleted(@$record['created_by']['name'], @$record['created_by']['deleted']);
                    $setTempData[] = $updateAt;

                    //Se è un SuperAdmin mostro la colonna dell'Ente
                    if (isSuperAdmin(true)) {
                        $setTempData[] = !empty($record['institution']['full_name_institution'])
                            ? escapeXss($record['institution']['full_name_institution'])
                            : 'N.D.';
                    }

                    $setTempData[] = $buttonAction;

                    $data[] = $setTempData;
                }

                $response['aaData'] = $data;
            }

            echo json_encode($response);
        }
    }

    /**
     * @description Renderizza il form di creazione di un nuovo Modulo
     *
     * @return void
     * @throws Exception
     * @url /admin/module/create.html
     * @method GET
     */
    public function create(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(__FUNCTION__);

        $data = [];

        //Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $data['is_box'] = uri()->uriString() === 'admin/module/create-box';

        //Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {
            $this->breadcrumb->push('Modulistica', 'admin/module');
            $this->breadcrumb->push('Nuovo', '/');

            $data['breadcrumbs'] = $this->breadcrumb->show();

            //Dati header della sezione
            $data['titleSection'] = 'Modulistica';
            $data['subTitleSection'] = 'GESTIONE DELLA MODULISTICA DELL\'ENTE';
            $data['sectionIcon'] = '<i class="fas fa-paste fa-3x"></i>';
        }

        $data['formAction'] = '/admin/module/store';
        $data['formSettings'] = [
            'name' => 'form_module',
            'id' => 'form_module',
            'class' => 'form_module',
        ];
        $data['_storageType'] = 'insert';

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        render('module/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua lo storage di un nuovo Modulo
     *
     * @return void
     * @throws Exception
     * @url /admin/module/store.html
     * @method POST
     */
    public function store(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new ModuleValidator();
        $check = $validator->check();

        if ($check['is_success']) {
            $doAction = true;
            $attach = new AttachmentArchive();
            $attachValidator = $attach->validate('attach_files');

            // Validatore per gli eventuali allegati
            // Se la validazione non va a buon fine setto il messaggio di errore e non eseguo lo storage dell'oggetto
            if (!empty($attachValidator['error'])) {
                $doAction = false;
                $code = $json->bad();
                $json->set('error_partial_attach', $attach->errorsToString());
            }

            // Se la validazione degli allegati va a buon fine eseguo lo storage dell'oggetto
            if ($doAction) {
                // Dati per registrazione ActivityLog
                $getIdentity = authPatOs()->getIdentity(['id', 'name']);

                $typology = setDefaultData(strip_tags(Input::post('typology', true)), null, ['']);

                $arrayValues = [
                    'owner_id' => $getIdentity['id'],
                    'institution_id' => checkAlternativeInstitutionId(),
                    'typology' => $typology,
                    'title' => strip_tags(Input::post('title', true)),
                    'description' => Input::post('description', true),
                    'order' => setDefaultData(strip_tags(Input::post('order', true)), null, [''])
                ];

                // Storage nuovo Modulo
                $insert = ModulesRegulationsModel::createWithLogs($arrayValues);

                // Svuoto le tabelle di relazione e le aggiorno
                $this->clear(
                    $insert,
                    Input::post('proceedings', true)
                );

                // Storage allegati associati al personale.
                $attach->storage('attach_files', 'modules_regulations', $insert->id, $insert['title']);

                if (!referenceOriginForRegenerateToken(3, 'create-box')) {
                    // Generazione nuovo token
                    Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
                }

                $json->set('message', __('success_save_operation', null, 'patos'));
            }
        } else {

            $code = $json->bad();
            $json->error('error', $check['errors']);
        }

        $json->setStatusCode($code);
        $json->response();
    }

    /**
     * @description Renderizza il form di modifica/duplicazione di un Modulo
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/module/edit/:id.html
     * @method GET
     */
    public function edit(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['update', 'create']);

        //Validatore che verifica se l'elemento da modificare esiste
        $validator = new ModuleValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/module', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Allegati
        $attach = new AttachmentArchive();

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $module = Registry::get('module');
        $module = !empty($module) ? $module->toArray() : [];

        $this->breadcrumb->push('Modulistica', 'admin/module');
        $this->breadcrumb->push('Modifica', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Modulistica';
        $data['subTitleSection'] = 'GESTIONE DELLA MODULISTICA DELL\'ENTE';
        $data['sectionIcon'] = '<i class="fas fa-paste fa-3x"></i>';

        // Vedo in sessione se sono arrivato qui da un operazione di ripristino dal versioning
        $session = new Session();
        $restore = $session->getFlash('restore');
        $data['restore'] = $restore;

        // Controllo se si sta duplicando un tasso di assenza
        $isDuplicate = uri()->segment(3, 0) === 'duplicate';

        // Setto il tipo di storage
        $data['_storageType'] = $isDuplicate ? 'duplicate' : 'update';

        //In base al tipo di storage setto l'action del form
        $data['formAction'] = ($isDuplicate) ? '/admin/module/store' : '/admin/module/update';
        $data['formSettings'] = [
            'name' => 'form_module',
            'id' => 'form_module',
            'class' => 'form_module',
        ];

        $data['module'] = $module;

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'modules_regulations',
            $module['id'],
            [
                'id',
                'cat_id',
                'archive_name',
                'archive_id',
                'client_name',
                'file_name',
                'file_type',
                'file_size',
                'file_ext',
                'label',
                'indexable',
                'active',
                'created_at',
                'updated_at'
            ]
        );

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = $module['institution_id'];

        $data['proceedingIds'] = Arr::pluck($module['proceedings'], 'id');

        $data['seo'] = $module['p_s_d_r'] ?? null;

        render('module/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua l'update di un Modulo
     *
     * @return void
     * @throws Exception
     * @url /admin/module/update.html
     * @method POST
     */
    public function update(): void
    {
        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new ModuleValidator();
        $check = $validator->check('update');

        if ($check['is_success']) {
            $doAction = true;

            $moduleId = (int)strip_tags(Input::post('id', true));

            // Recupero il modulo attuale prima di modificarlo e lo salvo nel versioning
            $module = ModulesRegulationsModel::where('id', $moduleId)
                ->with('proceedings:id,name')
                ->with('all_attachs');

            $module = $module->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute(__FUNCTION__, (!checkRecordOwner($module['owner_id']) && $this->acl->getCreate()));

            // Validazione upload allegati associati al tasso di assenza
            $attach = new AttachmentArchive();
            $attachValidator = $attach->validate('attach_files');

            // Validatore per gli eventuali allegati
            // Se la validazione non va a buon fine setto il messaggio di errore e non eseguo l'update dell'oggetto
            if (!empty($attachValidator['error'])) {
                $doAction = false;
                $code = $json->bad();
                $json->set('error_partial_attach', $attach->errorsToString());
            }

            // Se la validazione degli allegati va a buon fine eseguo l'update dell'oggetto
            if ($doAction) {
                $data = [];
                $data['typology'] = setDefaultData(strip_tags(Input::post('typology', true)), null, ['']);
                $data['title'] = strip_tags(Input::post('title', true));
                $data['description'] = Input::post('description', true);
                $data['order'] = setDefaultData(strip_tags(Input::post('order', true)));

                // Update Modulistica
                ModulesRegulationsModel::where('id', $moduleId)->updateWithLogs($module, $data);

                // Svuoto le tabelle di relazione e le aggiorno
                $this->clear(
                    $module,
                    Input::post('proceedings', true)
                );

                // Upload allegati associati al personale.
                $attach->update(
                    'attach_files',
                    'modules_regulations',
                    $moduleId,
                    $module['institution_id'],
                    $module['title']
                );

                // Generazione nuovo token
                Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
                $json->set('message', __('success_update_operation', null, 'patos'));
            }
        } else {

            $code = $json->bad();
            $json->error('error', $check['errors']);
        }

        $json->setStatusCode($code);
        $json->response();
    }

    /**
     * @description Metodo per lo storage nelle tabelle di relazione
     * In caso di update, svuota prima le tabelle di relazione e poi inserisce i dati aggiornati
     *
     * @param ModulesRegulationsModel|null $module      Modulistica
     * @param array|int|null               $proceedings Procedimenti associati al modulo
     * @return void
     */
    protected function clear(ModulesRegulationsModel $module = null, array|int $proceedings = null): void
    {
        $dataProceedings = [];
        if ($proceedings !== null) {
            foreach ($proceedings as $proceeding) {
                $dataProceedings[] = is_array($proceeding) ? strip_tags($proceeding['id']) : strip_tags($proceeding);
            }
        }
        //Insert/Update nella tabella di relazione
        $module->proceedings()->sync($dataProceedings);
    }


    /**
     * @description Funzione che effettua l'eliminazione di un Modulo
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/module/delete/:id.html
     * @method GET
     */
    public function delete(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute([__FUNCTION__, 'create', 'update'], true);

        //Validatore che verifica se l'elemento da eliminare esiste
        $validator = new ModuleValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getDelete() && $this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/module', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Se la validazione va a buon fine recupero l'elemento da eliminare dal registro
        $module = Registry::get('module');

        //Elimino il modulo
        $module->deleteWithLogs($module);

        // Generazione nuovo token
        Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        sessionSetNotify(__('success_delete_operation', null, 'patos'));

        redirect('admin/module');
    }

    /**
     * @description Funzione per l'eliminazione multipla
     *
     * @return void
     * @throws Exception
     * @url /admin/module/deletes.html
     * @method GET
     */
    public function deletes(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('delete');

        $validator = new ModuleValidator();
        $validate = $validator->multipleSelection();

        if ($validate['is_success']) {

            sessionSetNotify('Operazione avvenuta con successo');

            //Recupero gli elementi da eliminare dal registro
            $modules = Registry::get('__ids__multi_select_profile__');

            //Estraggo gli array degli elementi da eliminare
            $ids = Arr::pluck($modules, 'id');

            //Elimino gli elementi
            foreach ($modules as $module) {
                $module->deleteWithLogs($module);
            }

            // Generazione nuovo token
            Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        } else {

            sessionSetNotify($validate['errors'], 'danger');
        }

        redirect('/admin/module');
    }
}
