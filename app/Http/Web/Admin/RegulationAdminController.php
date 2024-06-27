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
use Helpers\Validators\RegulationValidator;
use Model\RegulationsModel;
use Model\SectionFoConfigPublicationArchive;
use Scope\DeletedScope;
use System\Arr;
use System\Input;
use System\JsonResponse;
use System\Registry;
use System\Session;
use System\Token;

/**
 *
 * Controller Regolamenti e documentazione
 *
 */
class RegulationAdminController extends BaseAuthController
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
     * @description Renderizza la pagina index dei Regolamenti
     * @return void
     * @throws Exception
     * @url /admin/regulation.html
     * @method GET
     */
    public function index(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['read', 'create']);

        $this->breadcrumb->push('Regolamenti e documentazione', '/');
        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Regolamenti e documentazione';
        $data['subTitleSection'] = 'GESTIONE DELLA DOCUMENTAZIONE DELL\'ENTE';
        $data['sectionIcon'] = '<i class="fas fa-paste fa-3x"></i>';

        $data['formAction'] = '/admin/regulation';
        $data['formSettings'] = [
            'name' => 'form_regulation',
            'id' => 'form_regulation',
            'class' => 'form_regulation',
        ];

        $data['include'] = '';

        $data['url'] = uri()->segment(2, 0);

        render('regulation/index', $data, 'admin');
    }

    /**
     * @description Funzione per la paginazione dei dati nel datatable
     *
     * @return void
     * @url /admin/structure/list.html
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
                5 => 'users.name',
                6 => 'object_regulations.updated_at'
            ];

            //Se l'utente è super Admin mostro la colonna dell'ente
            if (isSuperAdmin(true)) {
                $orderable[7] = 'i.full_name_institution';
            }

            //Setto proprietà datatable
            $dataTable = [];
            setDataTableData($dataTable, 'title');

            //Query per i dati da mostrare nel datatable
            $totalRecords = RegulationsModel::select('count(id) as allcount')
                ->count();

            $totalRecordsWithFilter = RegulationsModel::search($dataTable['searchValue'])
                ->select(['count(object_regulations.id) as allcount',])
                ->join('rel_regulations_public_in as public_in', 'object_regulations.id', '=', 'public_in.object_regulation_id', 'left outer')
                ->join('section_fo as section', 'section.id', '=', 'public_in.public_in_id', 'left outer')
                ->join('users', 'users.id', '=', 'object_regulations.owner_id', 'left outer')
                ->distinct('object_regulations.id');

            if (!empty($dataTable['searchValue'])) {
                $totalRecordsWithFilter->orWhere('object_regulations.id', '=', $dataTable['searchValue']);
            }
            $totalRecordsWithFilter = $totalRecordsWithFilter->count();

            $order = setOrderDatatable($dataTable['columnName'], $orderable, 'title');

            $records = RegulationsModel::search($dataTable['searchValue'])
                ->select(['object_regulations.id', 'object_regulations.institution_id', 'object_regulations.owner_id', 'title', 'publishing_status',
                    'object_regulations.updated_at', 'section.id as sectionId', 'section.name', 'users.name', 'i.full_name_institution'])
                ->with('structures:id,structure_name,archived')
                ->with('proceedings:id,name,archived')
                ->with(['created_by' => function ($query) {
                    $query->withoutGlobalScopes([DeletedScope::class]);
                    $query->select(['id', 'name', 'deleted']);
                }])
                ->with('institution:id,full_name_institution')
                ->with(['public_in' => function ($query) {
                    $query->select(['section_fo_id', 'section_fo_config_publication_archive.id', 'section_fo.name'])
                        ->join('section_fo', 'section_fo.id', '=', 'section_fo_id');
                }]);

            if (!empty($dataTable['searchValue'])) {
                $records->orWhere('object_regulations.id', '=', $dataTable['searchValue']);
            }

            $records = $records->join('institutions as i', 'object_regulations.institution_id', '=', 'i.id', 'left outer')
                ->join('users', 'users.id', '=', 'object_regulations.owner_id', 'left outer')
                ->join('rel_regulations_public_in as public_in', 'object_regulations.id', '=', 'public_in.object_regulation_id', 'left outer')
                ->join('section_fo as section', 'section.id', '=', 'public_in.public_in_id', 'left outer')
                ->groupBy('object_regulations.id')
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

                    if (!empty($record['structures']) && is_array($record['structures'])) {

                        $tmpStructures = Arr::pluck($record['structures'], 'structure_name');
                        $tmpArchStructures = Arr::pluck($record['structures'], 'archived');
                        $structures = str_replace(',', ',' . '<br>', implode(
                            ',',
                            array_map(
                                function ($structure) {
                                    return ('<small class="badge-primary mb-1">'
                                        . escapeXss($structure) . '</small>');
                                },
                                $tmpStructures,
                                $tmpArchStructures
                            )
                        ));
                    } else {

                        $structures = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definite">N.D.</small>';
                    }

                    if (!empty($record['proceedings']) && is_array($record['proceedings'])) {

                        $tmpProceedings = Arr::pluck($record['proceedings'], 'name');
                        $tmpArchProceedings = Arr::pluck($record['proceedings'], 'archived');
                        $proceedings = str_replace(',', ',' . '<br>', implode(
                            ',',
                            array_map(
                                function ($proceeding) {
                                    return ('<small class="badge badge-primary mb-1">'
                                        . escapeXss($proceeding) . '</small>');
                                },
                                $tmpProceedings,
                                $tmpArchProceedings
                            )
                        ));
                    } else {

                        $proceedings = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definiti">N.D.</small>';
                    }

                    if (!empty($record['public_in']) && is_array($record['public_in'])) {

                        $tmpPublicIn = Arr::pluck($record['public_in'], 'name');
                        $sections = str_replace(',', ',' . '<br>', implode(
                            ',',
                            array_map(
                                function ($section) {
                                    return ('<small class="badge badge-primary mb-1">' . escapeXss($section) . '</small>');
                                },
                                $tmpPublicIn
                            )
                        ));
                    } else {

                        $sections = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definiti">N.D.</small>';
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
                        ->addEdit('admin/regulation/edit/' . $record['id'], $record['id'])
                        ->addDuplicate('admin/regulation/duplicate/' . $record['id'], $record['id'])
                        ->addDelete('admin/regulation/delete/' . $record['id'], $record['id'])
                        ->render() : '';

                    $icon = null;

                    //Setto i dati da mostrare nelle colonne del datatable
                    $setTempData = [];
                    $setTempData[] = ($this->acl->getCrud() || checkRecordOwner($record['owner_id'])) ? ButtonAction::checkList('item[]', $record['id']) : '';
                    $setTempData[] = $icon . (!empty($record['title'])
                            ? '<a href="' . siteUrl('/page/29/details/' . $record['id'] . '/' . urlTitle($record['title'])) . '" target="_blank">' . escapeXss($record['title']) . '</a>'
                            : 'N.D.');
                    $setTempData[] = $structures;
                    $setTempData[] = $sections;
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
     * @description Renderizza il form di creazione di un nuovo regolamento
     *
     * @return void
     * @throws Exception
     * @url /admin/regulation/create.html
     * @method GET
     */
    public function create(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(__FUNCTION__);

        $data = [];

        //Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $data['is_box'] = uri()->uriString() === 'admin/regulation/create-box';

        //Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {

            $this->breadcrumb->push('Regolamenti e documentazione', 'admin/regulation');
            $this->breadcrumb->push('Nuovo', '/');
            $data['breadcrumbs'] = $this->breadcrumb->show();

            //Dati header della sezione
            $data['titleSection'] = 'Regolamenti e documentazione';
            $data['subTitleSection'] = 'GESTIONE DELLA DOCUMENTAZIONE DELL\'ENTE';
            $data['sectionIcon'] = '<i class="fas fa-paste fa-3x"></i>';
        }

        $data['formAction'] = '/admin/regulation/store';
        $data['formSettings'] = [
            'name' => 'form_regulation',
            'id' => 'form_regulation',
            'class' => 'form_regulation',
        ];
        $data['_storageType'] = 'insert';

        // Labels
        $data['labels'] = [];

        $publicIn = SectionFoConfigPublicationArchive::where('archive_name', '=', 'object_regulations')
            ->with(['section' => function ($query) {
                $query->select(['section_fo.id', 'name']);
            }])
            ->get()
            ->toArray();

        $dataPublicIn = [];

        foreach ($publicIn as $tmp) {
            $dataPublicIn[$tmp['section']['id']] = !empty($tmp['section']['label']) ? $tmp['section']['label'] : $tmp['section']['name'];
        }

        // Per pubblica in
        $data['publicIn'] = $dataPublicIn;

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        render('regulation/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua lo storage di un nuovo Regolamento
     *
     * @return void
     * @throws Exception
     * @url /admin/regulation/store.html
     * @method POST
     */
    public function store(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new RegulationValidator();
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

                $title = strip_tags(Input::post('title', true));
                $arrayValues = [
                    'owner_id' => $getIdentity['id'],
                    'institution_id' => checkAlternativeInstitutionId(),
                    'title' => $title,
                    'issue_date' => !empty(Input::post('issue_date')) ? convertDateToDatabase(strip_tags(Input::post('issue_date', true))) : null,
                    'number' => strip_tags(Input::post('number', true)),
                    'protocol' => setDefaultData(strip_tags(Input::post('protocol', true)), null, ['']),
                    'description' => Input::post('description', true),
                    'order' => setDefaultData(strip_tags(Input::post('order', true)), null, [''])
                ];

                // Storage nuovo Regolamento
                $insert = RegulationsModel::createWithLogs($arrayValues);

                // Svuoto le tabelle di relazione e le aggiorno
                $this->clear(
                    $insert,
                    explode(',', strip_tags((string)Input::post('structures', true))),
                    Input::post('proceedings', true),
                    Input::post('public_in', true)
                );

                // Storage allegati associati al personale.
                $attach->storage('attach_files', 'regulations', $insert->id, $insert['title']);

                // Generazione nuovo token
                if (!referenceOriginForRegenerateToken(3, 'create-box')) {
                    Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
                }

                if (!referenceOriginForRegenerateToken(3, 'create-box')) {
                    $json->set('message', __('success_save_operation', null, 'patos'));
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
     * @description Renderizza il form di modifica/duplicazione di un regolamento
     *
     * @return void
     * @throws Exception
     * @url /admin/regulation/edit.html
     * @method GET
     */
    public function edit(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['update', 'create']);

        //Validatore che verifica se l'elemento da modificare esiste
        $validator = new RegulationValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        $data = [];

        $segments = uri()->segmentArray();
        array_pop($segments);

        // Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $data['is_box'] = implode('/', $segments) === 'admin/regulation/edit-box';

        if (!$validate['is_success']) {
            if(!$data['is_box']){
                redirect('admin/regulation', sessionSetNotify($validate['errors'], 'danger'));
            } else {
                render('access_denied/modal_access_denied', [], 'admin');
                die();
            }
        }

        // Allegati
        $attach = new AttachmentArchive();

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $regulation = Registry::get('regulation');
        $regulation = !empty($regulation) ? $regulation->toArray() : [];

        //Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {
            $this->breadcrumb->push('Regolamenti e documentazione', 'admin/regulation');
            $this->breadcrumb->push('Modifica', '/');

            $data['breadcrumbs'] = $this->breadcrumb->show();

            //Dati header della sezione
            $data['titleSection'] = 'Regolamenti e documentazione';
            $data['subTitleSection'] = 'GESTIONE DELLA DOCUMENTAZIONE DELL\'ENTE';
            $data['sectionIcon'] = '<i class="fas fa-paste fa-3x"></i>';
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
        $data['formAction'] = ($isDuplicate) ? '/admin/regulation/store' : '/admin/regulation/update';
        $data['formSettings'] = [
            'name' => 'form_regulation',
            'id' => 'form_regulation',
            'class' => 'form_regulation',
        ];

        $date = convertDateToForm($regulation['issue_date']);

        $data['regulation'] = $regulation;

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'regulations',
            $regulation['id'],
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

        $publicIn = SectionFoConfigPublicationArchive::where('archive_name', '=', 'object_regulations')
            ->with(['section' => function ($query) {
                $query->select(['section_fo.id', 'name']);
            }])
            ->get()
            ->toArray();

        $dataPublicIn = [];

        foreach ($publicIn as $tmp) {
            $dataPublicIn[$tmp['section']['id']] = !empty($tmp['section']['label']) ? $tmp['section']['label'] : $tmp['section']['name'];
        }

        // Per pubblica in
        $data['publicIn'] = $dataPublicIn;
        $data['publicInIDs'] = Arr::pluck($regulation['public_in'], 'section_fo_id');

        $data['issue_date'] = $date['date'];
        $data['structureIds'] = Arr::pluck($regulation['structures'], 'id');
        $data['proceedingIds'] = Arr::pluck($regulation['proceedings'], 'id');

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = $regulation['institution_id'];

        $data['seo'] = $regulation['p_s_d_r'] ?? null;

        render('regulation/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua l'update di un Regolamento
     *
     * @return void
     * @throws Exception
     * @url /admin/regulation/update.html
     * @method POST
     */
    public function update(): void
    {
        $json = new JsonResponse();
        $code = $json->success();

        //Validatore form
        $validator = new RegulationValidator();
        $check = $validator->check('update');

        if ($check['is_success']) {
            $doAction = true;

            $regulationId = (int)strip_tags(Input::post('id', true));

            // Recupero il regolamento attuale prima di modificarlo e lo salvo nel versioning
            $regulation = RegulationsModel::where('id', $regulationId)
                ->with('structures:id,structure_name')
                ->with('proceedings:id,name')
                ->with(['public_in' => function ($query) {
                    $query->select(['section_fo_id', 'section_fo_config_publication_archive.id', 'section_fo.name'])
                        ->join('section_fo', 'section_fo.id', '=', 'section_fo_id');
                }])
                ->with('all_attachs');

            $regulation = $regulation->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute(__FUNCTION__, (!checkRecordOwner($regulation['owner_id']) && $this->acl->getCreate()));

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
                $data['title'] = strip_tags(Input::post('title', true));
                $data['issue_date'] = !empty(Input::post('issue_date')) ? convertDateToDatabase(strip_tags(Input::post('issue_date', true))) : null;
                $data['number'] = strip_tags(Input::post('number', true));
                $data['protocol'] = setDefaultData(strip_tags(Input::post('protocol', true)), null, ['']);
                $data['description'] = Input::post('description', true);
                $data['order'] = setDefaultData(strip_tags(Input::post('order', true)), null, ['']);

                // Update Regolamento
                RegulationsModel::where('id', $regulationId)->updateWithLogs($regulation, $data);

                // Svuoto le tabelle di relazione e le aggiorno
                $this->clear(
                    $regulation,
                    explode(',', strip_tags(Input::post('structures', true))),
                    Input::post('proceedings', true),
                    Input::post('public_in', true)
                );

                // Upload allegati associati al personale
                $attach->update(
                    'attach_files',
                    'regulations',
                    $regulationId,
                    $regulation['institution_id'],
                    $regulation['title']
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
     * @param RegulationsModel|null $regulation  Regolamento
     * @param array|int|null        $structures  Strutture associate al regolamento
     * @param array|int|null        $proceedings Procedimenti associati al regolamento
     * @param array|int|null        $publicIn    Sezioni per il pubblica in del regolamento
     * @return void
     */
    protected function clear(RegulationsModel $regulation = null, array|int $structures = null, array|int $proceedings = null, array|int $publicIn = null): void
    {
        $dataStructures = [];
        if ($structures !== null) {
            foreach ($structures as $structure) {
                $dataStructures[] = is_array($structure) ? $structure['id'] : $structure;
            }
        }
        //Insert/Update nella tabella di relazione
        $regulation->structures()->sync($dataStructures);

        $dataProceedings = [];
        if ($proceedings) {
            foreach ($proceedings as $proceeding) {
                $dataProceedings[] = is_array($proceeding) ? strip_tags($proceeding['id']) : strip_tags($proceeding);
            }
        }
        //Insert/Update nella tabella di relazione
        $regulation->proceedings()->sync($dataProceedings);


        $dataPublicIn = [];
        if ($publicIn !== null) {
            foreach ($publicIn as $in) {
                $dataPublicIn[] = is_array($in) ? strip_tags($in['section_fo_id']) : strip_tags($in);
            }
        }
        //Insert/Update nella tabella di relazione
        $regulation->public_in()->sync($dataPublicIn);
    }


    /**
     * @description Funzione che effettua l'eliminazione di un Regolamento
     *
     * @return void
     * @throws Exception
     * @url /admin/regulation/delete/:id.html
     * @method GET
     */
    public function delete(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute([__FUNCTION__, 'create', 'update'], true);

        //Validatore che verifica se l'elemento da eliminare esiste
        $validator = new RegulationValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getDelete() && $this->acl->getUpdate());

        if (!$validate['is_success']) {
            redirect('admin/regulation', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Se la validazione va a buon fine recupero l'elemento da eliminare dal registro
        $regulation = Registry::get('regulation');

        //Elimino il regolamento
        $regulation->deleteWithLogs($regulation);

        // Generazione nuovo token
        Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        sessionSetNotify(__('success_delete_operation', null, 'patos'));

        redirect('admin/regulation');
    }

    /**
     * @description Funzione per l'eliminazione multipla
     *
     * @return void
     * @throws Exception
     * @url /admin/regulation/deletes.html
     * @method GET
     */
    public function deletes(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('delete');

        $validator = new RegulationValidator();
        $validate = $validator->multipleSelection();

        if ($validate['is_success']) {

            sessionSetNotify('Operazione avvenuta con successo');

            //Recupero gli elementi da eliminare dal registro
            $regulations = Registry::get('__ids__multi_select_profile__');

            //Estraggo gli array degli elementi da eliminare
            $ids = Arr::pluck($regulations, 'id');

            //Elimino gli elementi
            foreach ($regulations as $regulation) {
                $regulation->deleteWithLogs($regulation);
            }

            // Generazione nuovo token
            Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        } else {

            sessionSetNotify($validate['errors'], 'danger');
        }

        redirect('/admin/regulation');
    }
}
