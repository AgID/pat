<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Admin;

use Exception;
use Helpers\ActivityLog;
use Helpers\S;
use Helpers\Utility\AttachmentArchive;
use Helpers\Utility\ButtonAction;
use Helpers\Validators\DatatableValidator;
use Helpers\Validators\GeneralActsDocumentsValidator;
use Model\GeneralActsDocumentsModel;
use Model\SectionFoConfigPublicationArchive;
use Scope\DeletedScope;
use System\Action;
use System\Arr;
use System\Input;
use System\JsonResponse;
use System\Registry;
use System\Session;
use System\Token;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class GeneralActsDocumentsController extends BaseAuthController
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
     * @description Renderizza la pagina index degli atti/documenti di carattere generale
     * @return void
     * @throws Exception
     * @url /admin/general-acts-documents.html
     * @method GET
     */
    public function index(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['read', 'create']);

        $this->breadcrumb->push('Elenco Atti e Documenti di carattere generale', '/');
        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Atti e Documenti di carattere generale';
        $data['subTitleSection'] = 'GESTIONE DEGLI ATTI E DOCUMENTI DI CARATTERE GENERALE';
        $data['sectionIcon'] = '<i class="fas fa-copy fa-3x"></i>';

        $data['formAction'] = '/admin/general-acts-documents';
        $data['formSettings'] = [
            'name' => 'form_general-acts-documents',
            'id' => 'form_general-acts-documents',
            'class' => 'form_general-acts-documents',
        ];

        $data['url'] = uri()->segment(2, 0);

        render('general-acts-documents/index', $data, 'admin');
    }

    /**
     * @description Funzione per la paginazione dei dati nel datatable
     *
     * @return void
     * @url /admin/general-acts-documents/list.html
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

        // Controllo se è una richiesta Ajax e se il datatable è stato validato correttamente
        if (Input::isAjax() === true && $validator['is_success'] === true) {

            $data = [];

            // Colonne su cui poter effettuare l'ordinamento
            $orderable = [
                1 => 'object',
                3 => 'userName',
                4 => 'updated_at'
            ];

            // Se l'utente è super Admin mostro la colonna dell'ente
            if (isSuperAdmin(true)) {
                $orderable[5] = 'i.full_name_institution';
            }

            //Setto proprietà datatable
            $dataTable = [];
            setDataTableData($dataTable, 'updated_at');

            // Query per i dati da mostrare nel datatable
            $totalRecords = GeneralActsDocumentsModel::select('count(id) as allcount')
                ->count();

            $totalRecordsWithFilter = GeneralActsDocumentsModel::search($dataTable['searchValue'])
                ->select('count(object_bdncp_general_acts_documents.id) as allcount')
                ->join('users', 'users.id', '=', 'object_bdncp_general_acts_documents.owner_id', 'left outer')
                ->join('rel_general_acts_documents_public_in as public_in', 'object_bdncp_general_acts_documents.id', '=', 'public_in.general_acts_documents_id', 'left outer')
                ->join('section_fo as section', 'section.id', '=', 'public_in.public_in_id', 'left outer');

            if (!empty($dataTable['searchValue'])) {
                $totalRecordsWithFilter->orWhere('object_bdncp_general_acts_documents.id', '=', $dataTable['searchValue']);
            }

            $totalRecordsWithFilter = $totalRecordsWithFilter->count();

            $order = setOrderDatatable($dataTable['columnName'], $orderable, 'name');

            $records = GeneralActsDocumentsModel::search($dataTable['searchValue'])
                ->select(['object_bdncp_general_acts_documents.id', 'object_bdncp_general_acts_documents.owner_id', 'document_date',
                    'object_bdncp_general_acts_documents.institution_id', 'object_bdncp_general_acts_documents.object',
                    'object_bdncp_general_acts_documents.updated_at', 'publishing_status', 'object_bdncp_general_acts_documents.notes',
                    'section.id as sectionId', 'section.name', 'users.name as userName', 'i.full_name_institution'])
                ->join('users', 'users.id', '=', 'object_bdncp_general_acts_documents.owner_id', 'left outer')
                ->with(['created_by' => function ($query) {
                    $query->withoutGlobalScopes([DeletedScope::class]);
                    $query->select(['id', 'name', 'deleted']);
                }])
                ->with(['public_in' => function ($query) {
                    $query->select(['section_fo_id', 'section_fo_config_publication_archive.id', 'section_fo.name'])
                        ->join('section_fo', 'section_fo.id', '=', 'section_fo_id');
                }])
                ->with(['institution' => function ($query) {
                    $query->select(['id', 'full_name_institution']);
                }]);

            if (!empty($dataTable['searchValue'])) {
                $records->orWhere('object_bdncp_general_acts_documents.id', '=', $dataTable['searchValue']);
            }

            $records = $records->join('institutions as i', 'object_bdncp_general_acts_documents.institution_id', '=', 'i.id', 'left outer')
                ->join('rel_general_acts_documents_public_in as public_in', 'object_bdncp_general_acts_documents.id', '=', 'public_in.general_acts_documents_id', 'left outer')
                ->join('section_fo as section', 'section.id', '=', 'public_in.public_in_id', 'left outer')
                ->orderBy($order, $dataTable['columnSortOrder'])
                ->offset($dataTable['start'])
                ->limit($dataTable['rowPerPage'])
                ->get();

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

                    if (!empty($record['document_date'])) {
                        $documentDate = '<small class="badge badge-info"> 
                                    <i class="far fa-clock"></i> '
                            . date('d-m-Y', strtotime($record['document_date'])) . '</small>';
                    } else {
                        $documentDate = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definita">N.D.</small>';
                    }

                    // Controllo se l'utente ha i permessi di modifica dei record o di scrittura(e quindi di modifica dei propri record)
                    $permits = ($this->acl->getCreate() && checkRecordOwner($record['owner_id']));
                    $updatePermits = ($this->acl->getUpdate() && checkRecordOwner($record['owner_id']));

                    //Setto i pulsanti delle actions da mostrare in base ai permessi dell'utente, se non ha i permessi non li setto
                    $buttonAction = ($this->acl->getUpdate() || $this->acl->getDelete() || $permits) ? ButtonAction::create([
                        'edit' => $this->acl->getUpdate() || $permits,
                        'duplicate' => $this->acl->getCreate(),
                        'versioning' => getAclVersioning(),
                        'delete' => $this->acl->getDelete() || $updatePermits || $permits,
                    ])
                        ->addEdit('admin/general-acts-documents/edit/' . $record['id'], $record['id'])
                        ->addDuplicate('admin/general-acts-documents/duplicate/' . $record['id'], $record['id'])
                        ->addDelete('admin/general-acts-documents/delete/' . $record['id'], $record['id'])
                        ->render() : '';

                    $icon = null;

                    $publicSectionId = !empty($record['public_in']) && !empty($record['public_in'][0]) ? $record['public_in'][0]['section_fo_id'] : 582;

                    //Setto i dati da mostrare nelle colonne del datatable
                    $setTempData = [];
                    $setTempData[] = ($this->acl->getCrud() || checkRecordOwner($record['owner_id'])) ? ButtonAction::checkList('item[]', $record['id']) : '';
                    $setTempData[] = $icon . (!empty($record['object'])
                            ? '<a href="'. siteUrl('/page/'.$publicSectionId.'/details/' . $record['id'] . '/' . urlTitle($record['object'])).'" target="_blank">' . escapeXss($record['object']) . '</a>'
                            : 'N.D.');
                    $setTempData[] = $documentDate;
                    $setTempData[] = !empty($record['public_in'] && !empty($record['public_in'][0])) ? $record['public_in'][0]['name'] : '';
                    $setTempData[] = createdByCheckDeleted($record['created_by']['name'] ?? null, @$record['created_by']['deleted'] ?? 0);
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
     * @description Renderizza il form di creazione di una nuova procedura
     *
     * @return void
     * @throws Exception
     * @url /admin/general-acts-documents/create.html
     * @method GET
     */
    public function create(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(__FUNCTION__);

        $data = [];

        //Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $data['is_box'] = uri()->uriString() === 'admin/general-acts-documents/create-box';

        //Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {

            $this->breadcrumb->push('Procedure BDNCP', 'admin/bdncp');
            $this->breadcrumb->push('Nuovo', '/');
            $data['breadcrumbs'] = $this->breadcrumb->show();

            //Dati header della sezione
            $data['titleSection'] = 'Atti e Documenti di carattere generale';
            $data['subTitleSection'] = 'GESTIONE DEGLI ATTI E DOCUMENTI DI CARATTERE GENERALE';
            $data['sectionIcon'] = '<i class="fas fa-copy fa-3x"></i>';
        }

        //Setto dati del form
        $data['formAction'] = '/admin/general-acts-documents/store';
        $data['formSettings'] = [
            'name' => 'form_general-acts-documents',
            'id' => 'form_general-acts-documents',
            'class' => 'form_general-acts-documents',
        ];
        $data['_storageType'] = 'insert';

        $publicIn = SectionFoConfigPublicationArchive::where('archive_name', '=', 'object_bdncp_general_acts_documents')
            ->with('section:id,name')
            ->get()
            ->toArray();

        $dataPublicIn = [];

        foreach ($publicIn as $tmp) {
            $dataPublicIn[$tmp['section']['id']] = !empty($tmp['section']['label']) ? $tmp['section']['label'] : $tmp['section']['name'];
        }

        // Per pubblica in
        $data['publicIn'] = [null => ''] + $dataPublicIn;

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        render('general-acts-documents/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua lo storage di un nuovo atto/documento
     *
     * @return void
     * @method POST
     * @throws Exception
     * @url /admin/general-acts-documents/store.html
     */
    public function store(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new GeneralActsDocumentsValidator();
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

                $solutionName = strip_tags((string)Input::post('object', true));

                $arrayValues = [
                    'owner_id' => $getIdentity['id'],
                    'institution_id' => checkAlternativeInstitutionId(),
                    'object' => $solutionName,
                    'notes' => Input::post('notes', true),
                    'document_date' => !empty(Input::post('document_date')) ? convertDateToDatabase(strip_tags(Input::post('document_date', true))) : null,
                    'external_link' => !empty(Input::post('external_link')) ? strip_tags(Input::post('external_link', true)) : null,
                ];

                if (Input::post('public_in') == 583) {
                    $arrayValues ['typology'] = setDefaultData(strip_tags(Input::post('typology', true)), null, ['', null]);
                }

                if (Input::post('public_in') == 586) {
                    $arrayValues ['cup'] = setDefaultData(strip_tags(Input::post('cup', true)), null, ['', null]);
                    $arrayValues ['financing_amount'] = !empty(Input::post('financing_amount')) ? toFloat(strip_tags(Input::post('financing_amount', true))) : null;
                    $arrayValues ['financial_sources'] = setDefaultData(strip_tags(Input::post('financial_sources', true)), null, ['', null]);
                    $arrayValues ['procedural_implementation_status'] = setDefaultData(strip_tags(Input::post('procedural_implementation_status', true)), null, ['', null]);
                    $arrayValues ['start_date'] = !empty(Input::post('start_date')) ? convertDateToDatabase(strip_tags(Input::post('start_date', true))) : null;
                }

                // Storage nuova Soluzione Tecnologica
                $insert = GeneralActsDocumentsModel::createWithLogs($arrayValues);

                // Storage allegati associati al personale.
                $attach->storage('attach_files', 'general_acts_documents', $insert->id, $insert['object']);

                // Svuoto le tabelle di relazione e le aggiorno
                $this->clear(
                    $insert,
                    Input::post('public_in', true)
                );

                $json->set('message', __('success_save_operation', null, 'patos'));

                if (!referenceOriginForRegenerateToken(3, 'create-box')) {
                    // Generazione nuovo token
                    Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
                }
            }

        } else {

            $code = $json->bad();
            $json->error('error', $check['errors']);
        }

        $json->setStatusCode($code);
        $json->response();
    }

    /**
     * @description Renderizza il form di modifica/duplicazione di un Atto/Documento
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/general-acts-documents/edit.html
     * @method GET
     */
    public function edit(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['update', 'create']);

        //Validatore che verifica se l'elemento da modificare esiste
        $validator = new GeneralActsDocumentsValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        if (!$validate['is_success']) {
            redirect('admin/general-acts-documents', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Allegati
        $attach = new AttachmentArchive();

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $document = Registry::get('general-acts-documents');
        $document = !empty($document) ? $document->toArray() : [];

        $this->breadcrumb->push('Elenco Atti e Documenti di carattere generale', 'admin/general-acts-documents');
        $this->breadcrumb->push('Modifica', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Atti e Documenti di carattere generale';
        $data['subTitleSection'] = 'GESTIONE DEGLI ATTI E DOCUMENTI DI CARATTERE GENERALE';
        $data['sectionIcon'] = '<i class="fas fa-copy fa-3x"></i>';

        // Vedo in sessione se sono arrivato qui da un operazione di ripristino dal versioning
        $session = new Session();
        $restore = $session->getFlash('restore');
        $data['restore'] = $restore;

        // Controllo se si sta duplicando un tasso di assenza
        $isDuplicate = uri()->segment(3, 0) === 'duplicate';

        // Setto il tipo di storage
        $data['_storageType'] = $isDuplicate ? 'duplicate' : 'update';

        //In base al tipo di storage setto l'action del form
        $data['formAction'] = ($isDuplicate) ? '/admin/general-acts-documents/store' : '/admin/general-acts-documents/update';
        $data['formSettings'] = [
            'name' => 'form_general_acts_documents',
            'id' => 'form_general_acts_documents',
            'class' => 'form_general_acts_documents',
        ];

        $date = convertDateToForm($document['document_date']);
        $document['document_date'] = $date['date'];

        $date = convertDateToForm($document['start_date']);
        $document['start_date'] = $date['date'];

        $data['document'] = $document;

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'general_acts_documents',
            $document['id']
        );

        // Labels
        $data['labels'] = [];

        $publicIn = SectionFoConfigPublicationArchive::where('archive_name', '=', 'object_bdncp_general_acts_documents')
            ->with('section:id,name')
            ->get()
            ->toArray();

        $dataPublicIn = [];

        foreach ($publicIn as $tmp) {
            $dataPublicIn[$tmp['section']['id']] = !empty($tmp['section']['label']) ? $tmp['section']['label'] : $tmp['section']['name'];
        }

        // Per pubblica in
        $data['publicIn'] = [null => ''] + $dataPublicIn;

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = $document['institution_id'];

        $data['publicInIDs'] = Arr::pluck($document['public_in'], 'section_fo_id');

        render('general-acts-documents/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua l'update di un Atto/Documento
     *
     * @return void
     * @throws Exception
     * @url /admin/general-acts-documents/update.html
     * @method POST
     */
    public function update(): void
    {
        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new GeneralActsDocumentsValidator();
        $check = $validator->check('update');

        if ($check['is_success']) {
            $doAction = true;

            $documentId = (int)strip_tags(Input::post('id', true));

            // Recupero l'atto attuale prima di modificarlo e lo salvo nel versioning
            $document = GeneralActsDocumentsModel::where('id', $documentId)
                ->with(['public_in' => function ($query) {
                    $query->select(['section_fo_id', 'section_fo_config_publication_archive.id', 'section_fo.name'])
                        ->join('section_fo', 'section_fo.id', '=', 'section_fo_id')
                        ->groupBy('section_fo_id');
                }])
                ->with('all_attachs');

            $document = $document->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute(__FUNCTION__, (!checkRecordOwner($document['owner_id']) && $this->acl->getCreate()));

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
                $data['document_date'] = !empty(Input::post('document_date')) ? convertDateToDatabase(strip_tags(Input::post('document_date', true))) : null;
                $data['object'] = !empty(Input::post('object')) ? strip_tags(Input::post('object', true)) : null;

                $data['external_link'] = !empty(Input::post('external_link')) ? strip_tags(Input::post('external_link', true)) : null;
                $data['notes'] = !empty(Input::post('notes')) ? Input::post('notes', true) : null;
                $data['cup'] = null;
                $data['financing_amount'] = null;
                $data['financial_sources'] = null;
                $data['implementation_state'] = null;
                $data['projects_start_date'] = null;
                $data['typology'] = null;

                if (Input::post('public_in') == 586) {
                    $data['cup'] = setDefaultData(strip_tags(Input::post('cup', true)), null, ['', null]);
                    $data['financing_amount'] = !empty(Input::post('financing_amount')) ? toFloat(strip_tags(Input::post('financing_amount', true))) : null;
                    $data['financial_sources'] = setDefaultData(strip_tags(Input::post('financial_sources', true)), null, ['', null]);
                    $data['procedural_implementation_status'] = setDefaultData(strip_tags(Input::post('procedural_implementation_status', true)), null, ['', null]);
                    $data['start_date'] = !empty(Input::post('start_date')) ? convertDateToDatabase(strip_tags(Input::post('start_date', true))) : null;
                }

                if (Input::post('public_in') == 583) {
                    $data['typology'] = setDefaultData(strip_tags(Input::post('typology', true)), null, ['', null]);
                }

                GeneralActsDocumentsModel::where('id', $documentId)->updateWithLogs($document, $data);

                // Svuoto le tabelle di relazione e le aggiorno
                $this->clear(
                    $document,
                    Input::post('public_in')
                );

                // Upload allegati associati al personale.
                $attach->update(
                    'attach_files',
                    'general_acts_documents',
                    $documentId,
                    $document['institution_id'],
                    $document['object']
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
     * @description Funzione che effettua l'eliminazione di un Atto/Documento
     *
     * @return void
     * @throws Exception
     * @url /admin/general-acts-documents/delete.html
     * @method GET
     */
    public function delete(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute([__FUNCTION__, 'create', 'update'], true);

        //Validatore che verifica se l'elemento da eliminare esiste
        $validator = new GeneralActsDocumentsValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getDelete() && $this->acl->getUpdate());

        if (!$validate['is_success']) {
            redirect('admin/notices-act', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Se la validazione va a buon fine recupero l'elemento da eliminare dal registro
        $document = Registry::get('general-acts-documents');

        // Elimino l'atto settando deleted = 1
        $document->deleteWithLogs($document);

        sessionSetNotify(__('success_delete_operation', null, 'patos'));

        // Generazione nuovo token
        Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        redirect('admin/general-acts-documents');
    }

    /**
     * @description Funzione per l'eliminazione multipla
     *
     * @return void
     * @throws Exception
     * @url /admin/general-acts-documents/deletes.html
     * @method GET
     */
    public function deletes(): void
    {
        // Setto il metodo della rotta per i permessi
        $this->acl->setRoute('delete');

        $validator = new GeneralActsDocumentsValidator();
        $validate = $validator->multipleSelection();

        if ($validate['is_success']) {

            sessionSetNotify('Operazione avvenuta con successo');

            //Recupero gli elementi da eliminare dal registro
            $documents = Registry::get('__ids__multi_select_profile__');

            //Estraggo gli array degli elementi da eliminare
            $ids = Arr::pluck($documents, 'id');

            //Elimino gli elementi
            foreach ($documents as $document) {
                $document->deleteWithLogs($document);
            }

            // Generazione nuovo token
            Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        } else {

            sessionSetNotify($validate['errors'], 'danger');
        }

        redirect('/admin/general-acts-documents');
    }

    /**
     * @description Funzione per l'export dei dati in CSV
     *
     * @return void
     * @throws Exception
     * @url admin/general-acts-documents/export-csv
     * @method GET
     */
    public function exportCsv(): void
    {
        //Tassi di assenza
        $fileName = 'Atti e Documenti di carattere generale.csv';

        setFileDownloadCookieCSV($fileName);

        $output = fopen("php://output", 'w');

        $records = GeneralActsDocumentsModel::with('created_by:id,name')
            ->with(['public_in' => function ($query) {
                $query->select(['section_fo_id', 'section_fo_config_publication_archive.id', 'section_fo.name'])
                    ->join('section_fo', 'section_fo.id', '=', 'section_fo_id');
            }])
            ->with('all_attachs')
            ->get()
            ->toArray();

        if (!empty($records)) {

            $typologies = [
                'lavori' => 'Lavori pubblici, per assenza di lavori',
                'acquisti' => 'Acquisti di forniture e servizi, per assenza di acquisti di forniture e servizi',
            ];

            //Setto le colonne
            $headers = array(
                'ID',
                'OGGETTO',
                'DATA DOCUMENTO',
                'LINK ESTERNO',
                'SEZIONE',
                'NOTE',
                'TIPOLOGIA',
                'CUP',
                'DATA AVVIO',
                'IMPORTO FINANZIAMENTO',
                'FONTI FINANZIARIE',
                'STATO DI ATTUAZIONE PROCEDURALE',
                'ALLEGATI',
                'CREATO DA',
                'ULTIMA MODIFICA',
            );
            fputcsv($output, $headers, ';', '"');

            foreach ($records as $record) {
                $actDate = !empty($record['document_date']) ? (date('d-m-Y', strtotime($record['document_date']))) : '';

                $startDate = !empty($record['start_date']) ? date('d-m-Y', strtotime($record['start_date'])) : '';

                $updateAt = !empty($record['updated_at']) ? date('d-m-Y', strtotime($record['updated_at'])) : '';

                $attachLists = '';
                if (!empty($record['all_attachs'])) {

                    foreach ($record['all_attachs'] as $attach) {

                        $attachLists .= '(' . $attach['label'] . ') - Url: '
                            . siteUrl('/download/' . $attach['id']) . "\n";

                    }
                }

                //Setto i dati da inserire nelle colonne del CSV
                $data = [
                    !empty($record['id']) ? $record['id'] : '',
                    !empty($record['object']) ? escapeXss($record['object']) : '',
                    $actDate,
                    !empty($record['external_link']) ? escapeXss($record['external_link']) : '',
                    !empty($record['public_in']) ? $record['public_in'][0]['name'] : '',
                    !empty($record['notes']) ? $record['notes'] : '',
                    !empty($record['typology']) ? $typologies[$record['typology']] ?? '' : '',
                    !empty($record['cup']) ? escapeXss($record['cup']) : '',
                    $startDate,
                    !empty($record['financing_amount']) ? '€ ' . escapeXss(S::currency($record['financing_amount'], 2, ',', '.')) : '',
                    !empty($record['financial_sources']) ? escapeXss($record['financial_sources']) : '',
                    !empty($record['procedural_implementation_status']) ? escapeXss($record['procedural_implementation_status']) : '',
                    trim($attachLists),
                    !empty($record['created_by']['name']) ? checkDecrypt($record['created_by']['name']) : '',
                    $updateAt,
                ];

                fputcsv($output, $data, ';', '"');
            }
            fclose($output);

            // Storage Activity log
            ActivityLog::create([
                'action' => 'Esportazione dati degli Atti e Documenti di carattere generale in CSV',
                'description' => 'Esportazione dati degli Atti e Documenti di carattere generale in CSV',
                'request_post' => [
                    'post' => @$_POST,
                    'get' => Input::get(),
                    'server' => Input::server(),
                ],
                'action_type' => 'exportCSV',
                'object_id' => 92,
                'area' => 'object'
            ]);

            exit();
        } else {

            //Se non sono presenti tassi di assenza nel db mostro una notifica con il messaggio e faccio il redirect
            sessionSetNotify(
                sprintf(__('error_export_csv_', null, 'patos'), 'atto/documento'),
                'warning'
            );

            redirect('admin/general-acts-documents');
        }
    }

    /**
     * @description Metodo per lo storage nelle tabelle di relazione
     * In caso di update, svuota prima le tabelle di relazione e poi inserisce i dati aggiornati
     *
     * @param GeneralActsDocumentsModel|null $document
     * @param array|int|null $publicIn Sezioni di pubblica in dell'atto
     * @param bool $rest Parametro aggiuntivo
     * @return void
     */
    protected function clear(GeneralActsDocumentsModel $document = null, array|string|int $publicIn = null, bool $rest = false): void
    {
        //Insert/Update nella tabella di relazione
        $document->public_in()->sync($publicIn);
    }
}
