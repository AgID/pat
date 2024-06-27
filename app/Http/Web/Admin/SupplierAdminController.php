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
use Helpers\Validators\SupplierValidator;
use Model\SupplieListModel;
use Scope\DeletedScope;
use System\Arr;
use System\Input;
use System\JsonResponse;
use System\Registry;
use System\Session;
use System\Token;

/**
 *
 * Controller Elenco partecipanti/aggiudicatari
 *
 */
class SupplierAdminController extends BaseAuthController
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
     * @description Renderizza la pagina index dei Fornitori
     * @return void
     * @throws Exception
     * @url /admin/supplier.html
     * @method GET
     */
    public function index(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['read', 'create']);

        $this->breadcrumb->push('Fornitori', '/');
        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Elenco partecipanti/aggiudicatari';
        $data['subTitleSection'] = 'GESTIONE DELL\'ELENCO DEI FORNITORI DELL\'ENTE';
        $data['sectionIcon'] = '<i class="fas fa-clipboard-list fa-3x"></i>';

        $data['formAction'] = '/admin/supplier';
        $data['formSettings'] = [
            'name' => 'form_supplier',
            'id' => 'form_supplier',
            'class' => 'form_supplier',
        ];

        $data['include'] = '';

        $data['url'] = uri()->segment(2, 0);

        render('supplier/index', $data, 'admin');
    }

    /**
     * @description Funzione per la paginazione dei dati nel datatable
     *
     * @return void
     * @url /admin/supplier/list.html
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
                1 => 'name',
                2 => 'vat',
                3 => 'userName',
                4 => 'updated_at'
            ];

            //Se l'utente è super Admin mostro la colonna dell'ente
            if (isSuperAdmin(true)) {
                $orderable[5] = 'i.full_name_institution';
            }

            //Setto proprietà datatable
            $dataTable = [];
            setDataTableData($dataTable, 'name');
            $filter = Input::get('filter');
            //Query per i dati da mostrare nel datatable
            $totalRecords = SupplieListModel::select('count(*) as allcount')
                ->count();

            $totalRecordsWithFilter = SupplieListModel::search($dataTable['searchValue'])
                ->select(['count(object_supplie_list.id) as allcount']);

            $totalRecordsWithFilter->join('users', 'users.id', '=', 'object_supplie_list.owner_id');

            if (!empty($dataTable['searchValue'])) {
                $totalRecordsWithFilter->orWhere('object_supplie_list.id', '=', $dataTable['searchValue']);
            }

            $totalRecordsWithFilter = $totalRecordsWithFilter->count();

            $order = setOrderDatatable($dataTable['columnName'], $orderable, 'name');

            $records = SupplieListModel::search($dataTable['searchValue'])
                ->select(['object_supplie_list.id', 'owner_id', 'object_supplie_list.institution_id', 'typology', 'type', 'object_supplie_list.vat', 'foreign_tax_identification',
                    'object_supplie_list.name', 'object_supplie_list.updated_at', 'object_supplie_list.publishing_status', 'users.name as userName', 'i.full_name_institution',])
                ->join('users', 'users.id', '=', 'object_supplie_list.owner_id');

            $records->with('institution:id,full_name_institution')
                ->with(['created_by' => function ($query) {
                    $query->withoutGlobalScopes([DeletedScope::class]);
                    $query->select(['id', 'name', 'deleted']);
                }]);

            if (!empty($dataTable['searchValue'])) {
                $records->orWhere('object_supplie_list.id', '=', $dataTable['searchValue']);
            }

            $records = $records->join('institutions as i', 'object_supplie_list.institution_id', '=', 'i.id', 'left outer')
                ->orderBy($order, $dataTable['columnSortOrder'])
                ->offset($dataTable['start'])
                ->limit($dataTable['rowPerPage'])
                ->get()
                ->toArray();

            $response['draw'] = intval($dataTable['draw']);
            $response['iTotalRecords'] = ($totalRecords);
            $response['iTotalDisplayRecords'] = ($totalRecordsWithFilter);
            $response['aaData'] = [];

            if (!empty($records)) {

                foreach ($records as $record) {

                    if (!empty($record['updated_at'])) {
                        $updateAt = '<small class="badge badge-info"> 
                                    <i class="far fa-clock"></i> '
                            . date('d-m-Y H:i:s', strtotime($record['updated_at'])) . '</small>';
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
                        'versioning' => getAclVersioning(),
                        'delete' => $this->acl->getDelete() || $updatePermits || $permits,
                    ])
                        ->addEdit('admin/supplier/edit/' . $record['id'], $record['id'])
                        ->addDuplicate('admin/supplier/duplicate/' . $record['id'], $record['id'])
                        ->addDelete('admin/supplier/delete/' . $record['id'], $record['id'])
                        ->render() : '';

                    $icon = null;

                    //Setto i dati da mostrare nelle colonne del datatable
                    $setTempData = [];
                    $checkbox = ($this->acl->getCrud() || checkRecordOwner($record['owner_id'])) ? ButtonAction::checkList('item[]', $record['id']) : '';
                    $setTempData[] = $checkbox;
                    $setTempData[] = $icon . (!empty($record['name']) ? escapeXss($record['name']) : 'N.D.');
                    $setTempData[] = !empty($record['vat']) ? escapeXss($record['vat']) : 'N.D.';
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
     * @description Renderizza il form di creazione di un nuovo fornitore/aggiudicatario
     *
     * @return void
     * @throws Exception
     * @url /admin/supplier/create.html
     * @method GET
     */
    public function create(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(__FUNCTION__);

        $data = [];

        //Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $data['is_box'] = uri()->uriString() === 'admin/supplier/create-box';

        //Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {

            $this->breadcrumb->push('Fornitori', 'admin/supplier');
            $this->breadcrumb->push('Nuovo', '/');
            $data['breadcrumbs'] = $this->breadcrumb->show();

            //Dati header della sezione
            $data['titleSection'] = 'Elenco partecipanti/aggiudicatari';
            $data['subTitleSection'] = 'GESTIONE DELL\'ELENCO DEI FORNITORI DELL\'ENTE';
            $data['sectionIcon'] = '<i class="fas fa-clipboard-list fa-3x"></i>';
        }

        //Setto dati del form
        $data['formAction'] = '/admin/supplier/store';
        $data['formSettings'] = [
            'name' => 'form_supplier',
            'id' => 'form_supplier',
            'class' => 'form_supplier',
        ];
        $data['_storageType'] = 'insert';

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        render('supplier/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua lo storage di un nuovo fornitore
     *
     * @return void
     * @throws Exception
     * @url /admin/supplier/store.html
     * @method POST
     */
    public function store(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $code = $json->success();

        //Validatore form
        $validator = new SupplierValidator();
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
                $getIdentity = authPatOs()->getIdentity(['id', 'name']);

                $arrayValues = [
                    'owner_id' => $getIdentity['id'],
                    'institution_id' => checkAlternativeInstitutionId(),
                    'typology' => setDefaultData(strip_tags(Input::post('typology', true)), 1, ['', null, 0]),
                    'type' => (strip_tags(Input::post('typology', true)) == 1) ? 'Fornitore singolo' : 'Raggruppamento',
                    'it' => strip_tags(Input::post('supplier_typology', true)),
                    'name' => strip_tags(Input::post('name', true)),
                    'vat' => strip_tags(Input::post('vat', true)),
                    'foreign_tax_identification' => strip_tags(Input::post('foreign_tax_identification', true)),
                    'address' => strip_tags(Input::post('address', true)),
                    'email' => strip_tags(Input::post('email', true)),
                    'phone' => strip_tags(Input::post('phone', true)),
                    'fax' => strip_tags(Input::post('fax', true))
                ];

                // Storage nuovo Elenco fornitori
                $insert = SupplieListModel::createWithLogs($arrayValues);

                // Svuoto le tabelle di relazione e le aggiorno
                $this->clear(
                    $insert,
                    !empty(Input::post('group_leaders')) ? explode(',', strip_tags(Input::post('group_leaders', true))) : null,
                    !empty(Input::post('principals')) ? explode(',', strip_tags(Input::post('principals', true))) : null,
                    !empty(Input::post('mandatarie')) ? explode(',', strip_tags(Input::post('mandatarie', true))) : null,
                    !empty(Input::post('associates')) ? explode(',', strip_tags(Input::post('associates', true))) : null,
                    !empty(Input::post('consortiums')) ? explode(',', strip_tags(Input::post('consortiums', true))) : null
                );

                // Storage allegati associati al personale
                $attach->storage('attach_files', 'supplie_list', $insert->id, $insert['name']);

                // Generazione nuovo token
                if (!referenceOriginForRegenerateToken(3, 'create-box')) {
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
     * @description Renderizza il form di modifica/duplicazione di un fornitore
     *
     * @return void
     * @throws Exception
     * @url /admin/supplier/edit/:id.html
     * @method GET
     */
    public function edit(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['update', 'create']);

        //Validatore che verifica se l'elemento da modificare esiste
        $validator = new SupplierValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        $data = [];

        //Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $segments = uri()->segmentArray();
        array_pop($segments);
        $data['is_box'] = implode('/', $segments) === 'admin/supplier/edit-box';

        if (!$validate['is_success']) {
            if (!$data['is_box']) {
                redirect('admin/supplier', sessionSetNotify($validate['errors'], 'danger'));
            } else {
                render('access_denied/modal_access_denied', [], 'admin');
                die();
            }
        }

        // Allegati
        $attach = new AttachmentArchive();

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $supplier = Registry::get('supplier');
        $supplier = !empty($supplier) ? $supplier->toArray() : [];

        // Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {
            $this->breadcrumb->push('Elenco partecipanti/aggiudicatari', 'admin/supplier');
            $this->breadcrumb->push('Modifica', '/');

            $data['breadcrumbs'] = $this->breadcrumb->show();

            //Dati header della sezione
            $data['titleSection'] = 'Elenco partecipanti/aggiudicatari';
            $data['subTitleSection'] = 'GESTIONE DELL\'ELENCO DEI FORNITORI DELL\'ENTE';
            $data['sectionIcon'] = '<i class="fas fa-clipboard-list fa-3x"></i>';
        }

        // Vedo in sessione se sono arrivato qui da un operazione di ripristino dal versioning
        $session = new Session();
        $restore = $session->getFlash('restore');
        $data['restore'] = $restore;

        // Controllo se si sta duplicando un tasso di assenza
        $isDuplicate = uri()->segment(3, 0) === 'duplicate';

        // Setto il tipo di storage
        $data['_storageType'] = $isDuplicate ? 'duplicate' : 'update';

        //In base al tipo di storage setto l'action del form
        $data['formAction'] = ($isDuplicate) ? '/admin/supplier/store' : '/admin/supplier/update';
        $data['formSettings'] = [
            'name' => 'form_supplier',
            'id' => 'form_supplier',
            'class' => 'form_supplier',
        ];

        $data['supplier'] = $supplier;

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'supplie_list',
            $supplier['id'],
            [
                'id',
                'cat_id',
                'cat_id',
                'archive_name',
                'archive_id',
                'client_name',
                'file_name',
                'file_type',
                'file_ext',
                'file_size',
                'label',
                'indexable',
                'active',
                'created_at',
                'updated_at'
            ]
        );

        // Labels
        $data['labels'] = [];

        $data['groupLeaderIds'] = Arr::pluck($supplier['group_leaders'], 'id');
        $data['principalIds'] = Arr::pluck($supplier['principals'], 'id');
        $data['mandataryIds'] = Arr::pluck($supplier['mandatarie'], 'id');
        $data['associateIds'] = Arr::pluck($supplier['associates'], 'id');
        $data['consortiumIds'] = Arr::pluck($supplier['consortiums'], 'id');

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = $supplier['institution_id'];
        $data['scp'] = $supplier['scp'] ?? null;

        render('supplier/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua l'update di un fornitore
     *
     * @return void
     * @throws Exception
     * @url /admin/supplier/update.html
     * @method POST
     */
    public function update(): void
    {
        $json = new JsonResponse();
        $code = $json->success();

        //Validatore form
        $validator = new SupplierValidator();
        $check = $validator->check('update');

        if ($check['is_success']) {
            $doAction = true;

            $supplierId = (int)strip_tags(Input::post('id', true));

            // Recupero il fornitore attuale prima di modificarlo e lo salvo nel versioning
            $supplier = SupplieListModel::where('id', $supplierId)
                ->with('group_leaders:id,name,vat,type,foreign_tax_identification')
                ->with('principals:id,name,vat,type,foreign_tax_identification')
                ->with('mandatarie:id,name,vat,type,foreign_tax_identification')
                ->with('associates:id,name,vat,type,foreign_tax_identification')
                ->with('consortiums:id,name,vat,type,foreign_tax_identification')
                ->with('all_attachs')
                ->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute(__FUNCTION__, (!checkRecordOwner($supplier['owner_id']) && $this->acl->getCreate()));

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
                $data['typology'] = strip_tags(setDefaultData(Input::post('typology', true), null, ['']));
                $data['type'] = (strip_tags(Input::post('typology', true)) == 1) ? 'Fornitore singolo' : 'Raggruppamento';
                $data['it'] = strip_tags(Input::post('supplier_typology', true));
                $data['name'] = strip_tags(Input::post('name', true));
                $data['vat'] = (Input::post('typology') == 1) ? trim(strip_tags(Input::post('vat', true))) : null;
                $data['foreign_tax_identification'] = strip_tags(Input::post('foreign_tax_identification', true));
                $data['address'] = strip_tags(Input::post('address', true));
                $data['email'] = strip_tags(Input::post('email', true));
                $data['phone'] = strip_tags(Input::post('phone', true));
                $data['fax'] = strip_tags(Input::post('fax', true));

                // Update Elenco fornitori
                SupplieListModel::where('id', $supplierId)->updateWithLogs($supplier, $data);

                // Svuoto le tabelle di relazione e le aggiorno
                $this->clear(
                    $supplier,
                    !empty(Input::post('group_leaders')) ? explode(',', strip_tags((string)Input::post('group_leaders', true))) : null,
                    !empty(Input::post('principals')) ? explode(',', strip_tags((string)Input::post('principals', true))) : null,
                    !empty(Input::post('mandatarie')) ? explode(',', strip_tags((string)Input::post('mandatarie', true))) : null,
                    !empty(Input::post('associates')) ? explode(',', strip_tags((string)Input::post('associates', true))) : null,
                    !empty(Input::post('consortiums')) ? explode(',', strip_tags((string)Input::post('consortiums', true))) : null
                );

                // Upload allegati associati al personale
                $attach->update(
                    'attach_files',
                    'supplie_list',
                    $supplierId,
                    $supplier['institution_id'],
                    $supplier['name']
                );

                // Generazione nuovo token
                if (!referenceOriginForRegenerateToken(3, 'edit-box')) {
                    Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
                }
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
     * @param SupplieListModel|null $supplier Fornitore
     * @param array|int|null $groupLeaders Fornitori capogruppo del raggruppamento
     * @param array|int|null $principals Fornitori mandanti del raggruppamento
     * @param array|int|null $mandatarie Fornitori mandatari del raggruppamento
     * @param array|int|null $associates Fornitori associati del raggruppamento
     * @param array|int|null $consortiums Fornitori consorziata del raggruppamento
     * @return void
     */
    protected function clear(SupplieListModel $supplier = null, array|int $groupLeaders = null, array|int $principals = null, array|int $mandatarie = null, array|int $associates = null, array|int $consortiums = null): void
    {
        $dataGroupLeaders = [];
        if ($groupLeaders !== null) {
            foreach ($groupLeaders as $proceeding) {
                $dataGroupLeaders[] = is_array($proceeding) ? $proceeding['id'] : $proceeding;
            }
        }
        //Insert/Update nella tabella di relazione
        $supplier->group_leaders()->syncWithPivotValues($dataGroupLeaders, ['typology' => 'group_leader']);

        $dataPrincipals = [];
        if ($principals !== null) {
            foreach ($principals as $principal) {
                $dataPrincipals[] = is_array($principal) ? $principal['id'] : $principal;
            }
        }
        //Insert/Update nella tabella di relazione
        $supplier->principals()->syncWithPivotValues($dataPrincipals, ['typology' => 'principal']);

        $dataMandatarie = [];
        if ($mandatarie !== null) {
            foreach ($mandatarie as $mandatary) {

                $dataMandatarie[] = is_array($mandatary) ? $mandatary['id'] : $mandatary;
            }
        }
        //Insert/Update nella tabella di relazione
        $supplier->mandatarie()->syncWithPivotValues($dataMandatarie, ['typology' => 'mandatary']);

        $dataAssociates = [];
        if ($associates !== null) {
            foreach ($associates as $associate) {
                $dataAssociates[] = is_array($associate) ? $associate['id'] : $associate;
            }
        }
        //Insert/Update nella tabella di relazione
        $supplier->associates()->syncWithPivotValues($dataAssociates, ['typology' => 'associate']);

        $dataConsortiums = [];
        if ($consortiums !== null) {
            foreach ($consortiums as $consortium) {
                $dataConsortiums[] = is_array($consortium) ? $consortium['id'] : $consortium;
            }
        }
        //Insert/Update nella tabella di relazione
        $supplier->consortiums()->syncWithPivotValues($dataConsortiums, ['typology' => 'consortium']);
    }


    /**
     * @description Funzione che effettua l'eliminazione di un Fornitore
     *
     * @return void
     * @throws Exception
     * @url /admin/supplier/delete/:id.html
     * @method GET
     */
    public function delete(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute([__FUNCTION__, 'create', 'update'], true);

        //Validatore che verifica se l'elemento da eliminare esiste
        $validator = new SupplierValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getDelete() && $this->acl->getUpdate());

        if (!$validate['is_success']) {
            redirect('admin/supplier', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Se la validazione va a buon fine recupero l'elemento da eliminare dal registro
        $supplier = Registry::get('supplier');

        //Elimino l'elenco fornitori settando deleted = 1
        $supplier->deleteWithLogs($supplier);

        sessionSetNotify(__('success_delete_operation', null, 'patos'));

        // Generazione nuovo token
        Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        redirect('admin/supplier');
    }

    /**
     * @description Funzione per l'eliminazione multipla
     *
     * @return void
     * @throws Exception
     * @url /admin/supplier/deletes.html
     * @method GET
     */
    public function deletes(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('delete');

        $validator = new SupplierValidator();
        $validate = $validator->multipleSelection();

        if ($validate['is_success']) {

            sessionSetNotify('Operazione avvenuta con successo');

            $suppliers = Registry::get('__ids__multi_select_profile__');

            //Elimino gli elementi
            foreach ($suppliers as $supplier) {
                $supplier->deleteWithLogs($supplier);
            }

            // Generazione nuovo token
            Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        } else {

            sessionSetNotify($validate['errors'], 'danger');
        }

        redirect('/admin/supplier');
    }

}
