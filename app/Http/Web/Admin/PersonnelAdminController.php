<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Admin;

use Exception;
use Helpers\ActivityLog;
use Helpers\FileSystem\File;
use Helpers\Utility\AttachmentArchive;
use Helpers\Utility\ButtonAction;
use Helpers\Validators\DatatableValidator;
use Helpers\Validators\PersonnelValidator;
use Model\DataHistoricalPersonnelModel;
use Model\PersonnelModel;
use Model\PersonnelQualificationsModel;
use Model\RelSectionsFoPoliticOrganPersonnel;
use Model\RoleModel;
use Model\SectionFoConfigPublicationArchive;
use Model\SectionsFoModel;
use Model\SelectDataModel;
use Model\StructuresModel;
use Scope\DeletedScope;
use System\Arr;
use System\Input;
use System\JsonResponse;
use System\Log;
use System\Registry;
use System\Session;
use System\Token;
use System\Uploads;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 *
 * Controller Personale
 *
 */
class PersonnelAdminController extends BaseAuthController
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
     * @description Renderizza la pagina index del Personale
     * @return void
     * @throws Exception
     * @url /admin/personnel.html
     * @method GET
     */
    public function index(): void
    {
        //Setto il metodo della rotta
        $this->acl->setRoute(['read', 'create']);

        $this->breadcrumb->push('Personale', '/');
        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Personale';
        $data['subTitleSection'] = 'GESTIONE DEL PERSONALE DELL\'ENTE';
        $data['sectionIcon'] = '<i class="fas fa-users fa-3x"></i>';

        $data['formAction'] = '/admin/personnel';
        $data['formSettings'] = [
            'name' => 'form_personnel',
            'id' => 'form_personnel',
            'class' => 'form_personnel',
        ];

        $data['include'] = '';

        $data['url'] = uri()->segment(2, 0);

        render('personnel/index', $data, 'admin');

    }

    /**
     * @description Funzione per la paginazione dei dati nel datatable
     *
     * @return void
     * @url /admin/personnel/list.html
     * @method AJAX
     * @throws Exception
     */
    public function asyncPaginateDatatable(): void
    {
        //Setto il metodo della rotta
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
                1 => 'lastname',
                2 => 'firstname',
                3 => 'r.name',
                4 => 'political_role',
                6 => 'users.name',
                7 => 'updated_at'
            ];

            //Setto proprietà datatable
            $dataTable = [];
            setDataTableData($dataTable, 'full_name');

            //Query per i dati da mostrare nel datatable
            $totalRecords = PersonnelModel::select('count(id) as allcount')
                ->count();

            $totalRecordsWithFilter = PersonnelModel::search($dataTable['searchValue'])
                ->select(['count(object_personnel.id) as allcount'])
                ->PersonnelFilterDataTable()
                ->join('users', 'users.id', '=', 'object_personnel.owner_id', 'left outer')
                ->join('role as r', 'r.id', '=', 'object_personnel.role_id', 'left outer')
                ->join('institutions as i', 'object_personnel.institution_id', '=', 'i.id', 'left outer');

            //Filtro per id
            if (!empty($dataTable['searchValue'])) {
                $totalRecordsWithFilter->orWhere('object_personnel.id', '=', $dataTable['searchValue']);
            }
            // Inclusione di librerie esterne
            $totalRecordsWithFilter = $totalRecordsWithFilter->count();

            $order = setOrderDatatable($dataTable['columnName'], $orderable, 'object_personnel.updated_at');

            $records = PersonnelModel::search($dataTable['searchValue'])
                ->select(['object_personnel.id', 'full_name', 'firstname', 'lastname', 'object_personnel.updated_at', 'object_personnel.institution_id', 'political_role',
                    'role_id', 'object_personnel.owner_id', 'archived', 'users.name', 'i.full_name_institution', 'r.name'])
                ->PersonnelFilterDataTable()
                ->with('role:id,name')
                ->with('referent_structures:id,structure_name,archived')
                ->with('political_organ')
                ->with(['created_by' => function ($query) {
                    $query->withoutGlobalScopes([DeletedScope::class]);
                    $query->select(['id', 'name', 'deleted']);
                }])
                ->with('institution:id,full_name_institution')
                ->with('all_attachs:id,archive_id')
                ->join('role as r', 'r.id', '=', 'object_personnel.role_id', 'left outer')
                ->join('institutions as i', 'object_personnel.institution_id', '=', 'i.id', 'left outer')
                ->join('users', 'users.id', '=', 'object_personnel.owner_id', 'left outer');

            //Filtro per ID
            if (!empty($dataTable['searchValue'])) {
                $records->orWhere('object_personnel.id', '=', $dataTable['searchValue']);
            }

            $records = $records->orderBy($order, $dataTable['columnSortOrder'])
                ->offset($dataTable['start'])
                ->limit($dataTable['rowPerPage'])
                ->get()
                ->toArray();

            $response ['draw'] = intval($dataTable['draw']);
            $response ['iTotalRecords'] = ($totalRecords);
            $response ['iTotalDisplayRecords'] = ($totalRecordsWithFilter);
            $response ['aaData'] = [];

            if (!empty($records)) {

                $checkWf = false;
                $manageableStates = [];
                $getIdentity = authPatOs()->getIdentity(['id']);

                foreach ($records as $record) {

                    $referentForStructures = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definite">N.D.</small>';
                    if (!empty($record['referent_structures']) && is_array($record['referent_structures'])) {

                        $tmpStructures = Arr::pluck($record['referent_structures'], 'structure_name');
                        $tmpArchived = Arr::pluck($record['referent_structures'], 'archived');
                        $referentForStructures = str_replace(',', ',' . nbs(2), implode(',',
                            array_map(
                                function ($name, $archived) {
                                    return ('<small class="badge-primary mb-1">'
                                        . ($archived
                                            ? '<i class="fas fa-folder archived" data-toggle="tooltip" data-placement="top" data-original-title="Questo elemento è stato archiviato"></i> '
                                            : '') . escapeXss($name) . '</small>');
                                }, $tmpStructures, $tmpArchived)));

                    }

                    $politicianOrgans = null;
                    if (!empty($record['political_organ'])){
                        $politicianOrgans = implode(', ', array_map(fn($item) => $this->getSectionName($item['section_fo_id']), $record['political_organ']));
                    }

                    $updateAt = !empty($record['updated_at']) ? '<small class="badge badge-info"> 
                                    <i class="far fa-clock"></i> '
                        . date('d-m-Y H:i:s', strtotime($record['updated_at'])) .
                        '</small>' : '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definita">N.D.</small>';

                    // Controllo se l'utente ha i permessi di modifica dei record o di scrittura (e quindi di modifica dei propri record)
                    $permits = ($this->acl->getCreate() && checkRecordOwner($record['owner_id'],$getIdentity['id']));
                    $updatePermits = ($this->acl->getUpdate() && checkRecordOwner($record['owner_id'],$getIdentity['id']));
                    $wfPermits = false;
                    $workflowState = $record['workflow_state'] ?? 'finale';
                    $wfOwnerPermits = $permits && $workflowState == 'iniziale';

                    //Setto i pulsanti delle actions da mostrare in base ai permessi dell'utente
                    $buttonAction = ($this->acl->getUpdate() || $this->acl->getDelete() || $permits) ? ButtonAction::create([
                        'edit' => $checkWf ? (($this->acl->getUpdate() && $wfPermits) || $wfOwnerPermits) : $this->acl->getUpdate() || $permits,
                'duplicate' => $this->acl->getCreate() && $workflowState == 'finale',
                        'versioning' => getAclVersioning() && !$record['archived'],
                        'archiving' => getAclArchiving() && !$record['archived'] && $workflowState == 'finale',
                        'delete' => $checkWf ? (($this->acl->getDelete() && $wfPermits) || $wfOwnerPermits) : $this->acl->getDelete() || $updatePermits || $permits,
                    ])
                        ->addEdit('admin/personnel/edit/' . $record['id'], $record['id'], '', true)
                        ->addDuplicate('admin/personnel/duplicate/' . $record['id'], $record['id'])
                        ->addCustomButton('b_a_v', siteUrl('admin/personnel/versioning'), $record['id'], '', '', 'versioning')
                        ->addCustomButton('b_a_a', siteUrl('admin/personnel/archive'), $record['id'], 'personnel', escapeXss($record['full_name']), 'archiving')
                        ->addCustomButton('s_r_l', siteUrl('admin/personnel/show-record-log/' . $record['id']), $record['id'], '3', 'show-record-log', 'view')
                        ->addCustomButton('wf_a_f_e',"", $record['id'], $workflowState, '', 'view')
                        ->addDelete('admin/personnel/delete/' . $record['id'], $record['id'])
                        ->render() : '';

                    $politicalRole = !empty($record['political_role']) ? escapeXss($record['political_role']) : '';
                    $icon = null;

                    $link = true;

                    //Setto i dati da mostrare nelle colonne del datatable
                    $setTempData = [];

                    $checkBox = ($this->acl->getCrud() || checkRecordOwner($record['owner_id'])) ? ButtonAction::checkList('item[]', $record['id']) : '';

                    $setTempData[] = $checkBox;
                    $setTempData[] = !empty($record['lastname'])
                        ? $icon . (($link) ? '<a href="' . siteUrl('/page/4/details/' . $record['id'] . '/' . urlTitle($record['full_name'])) . '" target="_blank">' . escapeXss($record['lastname']) . '</a>' : escapeXss($record['lastname']))
                        : $icon . (($link) ? '<a href="' . siteUrl('/page/4/details/' . $record['id'] . '/' . urlTitle($record['full_name'])) . '" target="_blank">' . escapeXss($record['full_name']) . '</a>' : escapeXss($record['lastname']));
                    $setTempData[] = !empty($record['firstname'])
                        ? (($link) ? '<a href="' . siteUrl('/page/4/details/' . $record['id'] . '/' . urlTitle($record['full_name'])) . '" target="_blank">' . escapeXss($record['firstname']) . '</a>' : escapeXss($record['lastname']))
                        : 'N.D.';
                    $setTempData[] = !empty($record['role']['name']) ? escapeXss($record['role']['name']) : 'N.D.';
                    $setTempData[] = (!empty($politicalRole) ? $politicalRole . ' - ' : '') . $politicianOrgans;
                    $setTempData[] = $referentForStructures;
                    $setTempData[] = createdByCheckDeleted(@$record['created_by']['name'], @$record['created_by']['deleted']);
                    $setTempData[] = $updateAt;
                    $setTempData[] = !empty($record['all_attachs'])
                        ? '<small class="badge badge-success" style="background-color:#28a745;color:#fff; width: 60px;" data-toggle="tooltip" title="Sono presenti allegati">Si</small>'
                        : '<small class="badge badge-danger"  style="background-color:#dc3545;color:#fff; width: 60px;" data-toggle="tooltip" title="Non sono presenti allegati">No</small>';

                    $setTempData[] = $buttonAction;

                    $data[] = $setTempData;

                }

                $response ['aaData'] = $data;

            }
            jsonOut($response);
        }
    }

    /**
     * @description Renderizza il form di creazione di un nuovo Personale
     *
     * @return void
     * @throws Exception
     * @url /admin/personnel/create.html
     * @method GET
     */
    public function create(): void
    {
        //Setto il metodo della rotta
        $this->acl->setRoute(__FUNCTION__);

        $data = [];

        //Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $data['is_box'] = uri()->uriString() === verifyFirstOrLastSlash('admin/personnel/create-box', true);

        //Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {

            $this->breadcrumb->push('Personale', 'admin/personnel');
            $this->breadcrumb->push('Nuovo', '/');

            $data['breadcrumbs'] = $this->breadcrumb->show();

            //Dati header della sezione
            $data['titleSection'] = 'Personale';
            $data['subTitleSection'] = 'GESTIONE DEL PERSONALE DELL\'ENTE';
            $data['sectionIcon'] = '<i class="fas fa-users fa-3x"></i>';

        }

        //Setto dati del form
        $data['formAction'] = '/admin/personnel/store';
        $data['formSettings'] = [
            'name' => 'form_personnel',
            'id' => 'form_personnel',
            'class' => 'form_personnel'
        ];
        $data['_storageType'] = 'insert';

        $institutionTypeId = patOsInstituteInfo(['institution_type_id']);
        $institutionId = (checkAlternativeInstitutionId() != 0 ? checkAlternativeInstitutionId() : PatOsInstituteId());

        $allRoles = RoleModel::query();
        $allRoles = $allRoles->orderBy('name', 'ASC')->get();


        $politicOrgan = getSections(605);
        $data['politicalAdministrative'] = [];
        array_walk( $politicOrgan, function($section) use (&$data){
            $data['politicalAdministrative'][$section['id']] = (!empty($section['label']) ? $section['label'] : $section['name']);
        });


        $data['qualifications'] = optional(PersonnelQualificationsModel::where('view_in_personnel_form', '=', 1)
            ->get()->pluck('name', 'id'))->toArray();


        //Recupero le sezioni per il "pubblica in"
        $data['publicIn'] = $this->setPublicInData($institutionTypeId);

        // Labels
        $data['labels'] = [];

        $data['structureIds'] = null;
        $data['roles'] = [null => ''] + $allRoles->pluck('name', 'id')->toArray();
        $data['political'] = Arr::pluck(array_filter($allRoles->toArray(), function ($var) {
            return ($var['political'] == 1);
        }), 'name', 'id');
        $data['po_dir_fields'] = Arr::pluck(array_filter($allRoles->toArray(), function ($var) {
            return (!empty($var['po_dir_fields']));
        }), 'name', 'id');

        $data['assignmentIds'] = null;
        $data['institution_type_id'] = $institutionTypeId['institution_type_id'];

        // ID dell'ente corrente.
        $data['institution_id'] = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        render('personnel/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua lo storage di un nuovo Personale
     *
     * @return void
     * @throws Exception
     * @url /admin/personnel/store.html
     * @method POST
     */
    public function store(): void
    {
        //Setto il metodo della rotta
        $this->acl->setRoute('create', true);
        $json = new JsonResponse();
        $code = $json->success();
        $doUpload = null;
        $hasError = false;

        // Validatore form
        $validator = new PersonnelValidator();
        $check = $validator->check();

        if ($check['is_success']) {

            //Controllo sul file da caricare
            if (filesUploaded('photo') === true) {
                $doUpload = $this->doUpload();
                $hasError = (bool)$doUpload['success'];
            }

            if (!$hasError) {

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
                        'role_id' => strip_tags(Input::post('role_id', true)),
                        'qualification_id' => !empty(Input::post('qualification_id')) ? strip_tags(Input::post('qualification_id', true)) : null,
                        'title' => setDefaultData(strip_tags(Input::post('title', true)), null, ['']),
                        'firstname' => strip_tags(Input::post('firstname', true)),
                        'lastname' => strip_tags(Input::post('lastname', true)),
                        'full_name' => getFullName(strip_tags(Input::post('lastname', true)), strip_tags(Input::post('firstname', true))),
                        'determined_term' => setDefaultData(strip_tags(Input::post('determined_term', true)), 0, ['', null]),
                        'on_leave' => setDefaultData(strip_tags(Input::post('on_leave', true)), 0, ['', null]),
                        'photo' => !empty($doUpload['data']['file_name']) ? $doUpload['data']['file_name'] : null,
                        'phone' => strip_tags(Input::post('phone', true)),
                        'mobile_phone' => strip_tags(Input::post('mobile_phone', true)),
                        'fax' => strip_tags(Input::post('fax', true)),
                        'certified_email' => strip_tags(Input::post('certified_email', true)),
                        'not_available_email' => setDefaultData(strip_tags(Input::post('not_available_email', true)), 0, ['']),
                        'email' => empty(Input::post('not_available_email')) ? null : strip_tags(Input::post('email', true)),
                        'not_available_email_txt' => empty(Input::post('not_available_email')) ? strip_tags(Input::post('not_available_email_txt', true)) : null,
                        'in_office_since' => !empty(Input::post('in_office_since')) ? convertDateToDatabase(strip_tags(Input::post('in_office_since', true))) : null,
                        'in_office_until' => !empty(Input::post('in_office_until')) ? convertDateToDatabase(strip_tags(Input::post('in_office_until', true))) : null,
                        'personnel_lists' => setDefaultData(strip_tags(Input::post('personnel_lists', true)), 0, ['', null]),
                        'priority' => setDefaultData(strip_tags(Input::post('priority', true)), null, ['', null]),
                        'other_info' => Input::post('other_info', true),
                        'information_archive' => Input::post('information_archive', true),

                        //Per i ruoli Incaricato politico
                        'political_role' => strip_tags(Input::post('political_role', true)),

                        //Per i ruoli P.O., Dirigente e Segretario Generale
                        'extremes_of_conference' => Input::post('extremes_of_conference', true),
                        'compensations' => Input::post('compensations', true),
                        'trips_import' => Input::post('trips_import', true),
                        'other_assignments' => Input::post('other_assignments', true),
                        'other_assignments_institutions' => Input::post('other_assignments_institutions', true),
                        'notes' => Input::post('notes', true),
                        'total_emoluments' => Input::post('total_emoluments', true),
                    ];

                    // Storage nuovo Personale
                    $insert = PersonnelModel::createWithLogs($arrayValues);

                    if (Input::post('_historical', true)) {
                        // Insert dei dati del historical
                        $historicalData = objectToArray(json_decode(Input::post('_historical', true)));

                        if (!empty($historicalData)) {
                            $this->historicalData($insert->id, $historicalData);
                        }
                    }

                    // Svuoto le tabelle di relazione e le aggiorno
                    $this->clear(
                        $insert,
                        !empty(Input::post('structures')) ? explode(',', strip_tags((string)Input::post('structures', true))) : null,
                        !empty(Input::post('assignments')) ? explode(',', strip_tags((string)Input::post('assignments', true))) : null,
                        !empty(Input::post('measures')) ? explode(',', strip_tags((string)Input::post('measures', true))) : null,
                        Input::post('public_in', true),
                        Input::post('organs', true)
                    );

                    // Storage allegati associati al personale.
                    $attach->storage('attach_files', 'personnel', $insert->id, $insert['full_name']);

                    $json->set('message', __('success_save_operation', null, 'patos'));
                }


            } else {

                $code = $json->bad();
                $json->error('error', $doUpload['data']);

            }

        } else {

            $code = $json->bad();
            $json->error('error', $check['errors']);

        }

        $json->setStatusCode($code);
        $json->response();
    }

    /**
     * @description Renderizza il form di modifica/duplicazione del Personale
     *
     * @return void
     * @throws Exception
     * @url /admin/personnel/edit.html
     * @method GET
     */
    public function edit(): void
    {
        //Setto il metodo della rotta
        $this->acl->setRoute(['update', 'create']);

        //Validatore che verifica se l'elemento da modificare esiste
        $validator = new PersonnelValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());
        $data = [];

        $segments = uri()->segmentArray();
        array_pop($segments);

        //Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $data['is_box'] = implode('/', $segments) === 'admin/personnel/edit-box';

        if (!$validate['is_success']) {
            if (!$data['is_box']) {
                sessionSetNotify($validate['errors'], 'danger');
                redirect('admin/personnel');
            } else {
                render('access_denied/modal_access_denied', [], 'admin');
                die();
            }
        }

        $personnel = Registry::get('personnel');
        $personnel = !empty($personnel) ? $personnel->toArray() : [];

        // Vedo in sessione se sono arrivato qui da un operazione di ripristino dal versioning
        $session = new Session();
        $restore = $session->getFlash('restore');
        $data['restore'] = $restore;

        // Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {
            $this->breadcrumb->push('Personale', 'admin/personnel');
            $this->breadcrumb->push('Modifica', '/');
            $data['breadcrumbs'] = $this->breadcrumb->show();

            //Dati header della sezione
            $data['titleSection'] = 'Personale';
            $data['subTitleSection'] = 'GESTIONE DEL PERSONALE DELL\'ENTE';
            $data['sectionIcon'] = '<i class="fas fa-users fa-3x"></i>';
        }

        // Controllo se si sta duplicando un tasso di assenza
        $isDuplicate = uri()->segment(3, 0) === 'duplicate';

        // Setto il tipo di storage
        $data['_storageType'] = $isDuplicate ? 'duplicate' : 'update';

        //In base al tipo di storage setto l'action del form
        $data['formAction'] = ($isDuplicate) ? '/admin/personnel/store' : '/admin/personnel/update';
        $data['formSettings'] = [
            'name' => 'form_personnel',
            'id' => 'form_personnel',
            'class' => 'form_personnel'
        ];

        $data['personnel'] = $personnel;

        $startDate = convertDateToForm($personnel['in_office_since'] ?? null);
        $endDate = convertDateToForm($personnel['in_office_until'] ?? null);
        $data['in_office_since'] = $startDate['date'];
        $data['in_office_until'] = $endDate['date'];

        // Allegati
        $attach = new AttachmentArchive();

        // Recupero i ruoli per il personale in base alla tipologia dell'ente
        $institutionTypeId = patOsInstituteInfo(['institution_type_id']);
        $institutionId = (checkAlternativeInstitutionId() != 0 ? checkAlternativeInstitutionId() : PatOsInstituteId());

        $allRoles = RoleModel::query();
        $allRoles = $allRoles->orderBy('name', 'ASC')->get();

        $data['roles'] = [null => ''] + $allRoles->pluck('name', 'id')->toArray();

        $data['political'] = Arr::pluck(array_filter($allRoles->toArray(), function ($var) {
            return ($var['political'] == 1);
        }), 'name', 'id');

        $data['po_dir_fields'] = Arr::pluck(array_filter($allRoles->toArray(), function ($var) {
            return (!empty($var['po_dir_fields']));
        }), 'name', 'id');

        $politicOrgan = getSections(605);
        $data['politicalAdministrative'] = [];
        array_walk( $politicOrgan, function($section) use (&$data){
            $data['politicalAdministrative'][$section['id']] = (!empty($section['label']) ? $section['label'] : $section['name']);
        });

        //Recupero le sezioni per il "pubblica in"
        $data['publicIn'] = $this->setPublicInData($institutionTypeId);

        $data['qualifications'] = optional(PersonnelQualificationsModel::select(['id', 'name'])
            ->where('view_in_personnel_form', '=', 1)
            ->orWhere('id', $personnel['qualification_id'])
            ->get()->pluck('name', 'id'))->toArray();

        $data['structureIds'] = Arr::pluck($personnel['referent_structures'], 'id');
        $data['assignmentIds'] = Arr::pluck($personnel['assignments'], 'id');
        $data['measureIds'] = Arr::pluck($personnel['measures'], 'id');
        $data['publicInIDs'] = Arr::pluck($personnel['public_in'], 'section_fo_id');
        $data['organIds'] = Arr::pluck($personnel['political_organ'], 'section_fo_id');

        $data['institution_type_id'] = $institutionTypeId['institution_type_id'];

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'personnel',
            $personnel['id'],
            [
                'id',
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

        // ID dell'ente corrente.
        $data['institution_id'] = $personnel['institution_id'];
        $data['seo'] = $personnel['p_s_d_r'] ?? null;
        $data['scp'] = $personnel['scp'] ?? null;

        render('personnel/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua l'update di un Personale
     *
     * @return void
     * @throws Exception
     * @url /admin/personnel/update.html
     * @method POST
     */
    public function update(): void
    {
        $hasError = false;
        $json = new JsonResponse();
        $code = $json->success();
        $doUpload = null;

        // Validatore form
        $validator = new PersonnelValidator();
        $check = $validator->check('update');

        if ($check['is_success']) {

            $personnelId = (int)strip_tags(Input::post('id', true));

            // Recupero il personale attuale prima di modificarlo e lo salvo nel versioning
            $personnel = PersonnelModel::where('id', $personnelId)
                ->with('referent_structures:id,structure_name')
                ->with('assignments:id,object')
                ->with('measures:id,object')
                ->with('role:id,name')
                ->with('public_in')
                ->with('political_organ')
                ->with('all_attachs');


            $personnel = $personnel->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute(checkRecordOwner($personnel->owner_id) ? ['update', 'create'] : 'update', true);

            $photo = $personnel->photo;

            if (!empty(Input::post('remove_photo')) && empty(Input::post('photo'))) {
                $photo = null;
                removePhoto($personnel->photo);
            }

            //Controllo sul file da aggiornare
            if (filesUploaded('photo') === true) {

                $doUpload = $this->doUpload();
                $hasError = (bool)$doUpload['success'];

                if (!$hasError) {

                    //Se esiste elimino il vecchio file dalla cartella dei media dell'Ente
                    if (File::exists(MEDIA_PATH . instituteDir() . '/assets/images/' . $personnel->photo)) {

                        File::delete(MEDIA_PATH . instituteDir() . '/assets/images/' . $personnel->photo);

                    }

                }
            }

            if (!$hasError) {
                $doAction = true;

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
                    $data['role_id'] = strip_tags(Input::post('role_id', true));
                    $data['qualification_id'] = !empty(Input::post('qualification_id')) ? strip_tags(Input::post('qualification_id', true)) : null;
                    $data['title'] = setDefaultData(strip_tags(Input::post('title', true)), null, ['']);
                    $data['firstname'] = strip_tags(Input::post('firstname', true));
                    $data['lastname'] = strip_tags(Input::post('lastname', true));
                    $data['full_name'] = getFullName($data['lastname'], $data['firstname']);
                    $data['determined_term'] = setDefaultData(strip_tags(Input::post('determined_term', true)), 0, ['', null]);
                    $data['on_leave'] = setDefaultData(strip_tags(Input::post('on_leave', true)), 0, ['', null]);
                    $data['photo'] = !empty($doUpload['data']['file_name']) ? $doUpload['data']['file_name'] : $photo;
                    $data['phone'] = strip_tags(Input::post('phone', true));
                    $data['mobile_phone'] = strip_tags(Input::post('mobile_phone', true));
                    $data['fax'] = strip_tags(Input::post('fax', true));
                    $data['certified_email'] = strip_tags(Input::post('certified_email', true));
                    $data['not_available_email'] = setDefaultData(strip_tags(Input::post('not_available_email', true)), null, ['']);
                    $data['email'] = empty(Input::post('not_available_email')) ? null : strip_tags(Input::post('email', true));
                    $data['not_available_email_txt'] = empty(Input::post('not_available_email')) ? strip_tags(Input::post('not_available_email_txt', true)) : null;
                    $data['in_office_since'] = !empty(Input::post('in_office_since')) ? convertDateToDatabase(strip_tags(Input::post('in_office_since', true))) : null;
                    $data['in_office_until'] = !empty(Input::post('in_office_until')) ? convertDateToDatabase(strip_tags(Input::post('in_office_until', true))) : null;
                    $data['personnel_lists'] = setDefaultData(strip_tags(Input::post('personnel_lists', true)), 0, ['', null]);
                    $data['priority'] = setDefaultData(strip_tags(Input::post('priority', true)), null, ['', null]);
                    $data['other_info'] = Input::post('other_info', true);
                    $data['information_archive'] = Input::post('information_archive', true);

                    //Per i ruoli Incaricato politico
                    $data['political_role'] = strip_tags(Input::post('political_role', true));

                    //Per i ruoli P.O., Dirigente e Segretario Generale
                    $data['extremes_of_conference'] = Input::post('extremes_of_conference', true);
                    $data['compensations'] = Input::post('compensations', true);
                    $data['trips_import'] = Input::post('trips_import', true);
                    $data['other_assignments'] = Input::post('other_assignments', true);
                    $data['other_assignments_institutions'] = Input::post('other_assignments_institutions', true);
                    $data['notes'] = Input::post('notes', true);
                    $data['total_emoluments'] = Input::post('total_emoluments', true);

                    // Update Personale
                    PersonnelModel::where('id', '=', $personnelId)->updateWithLogs($personnel, $data);


                    if (Input::post('_historical', true)) {
                        // Insert e update dei dati dello storico
                        $historicalData = objectToArray(json_decode(Input::post('_historical', true)));
                        $this->historicalData($personnelId, $historicalData);
                    }

                    // Svuoto le tabelle di relazione e le aggiorno
                    $this->clear(
                        $personnel,
                        !empty(Input::post('structures')) ? explode(',', strip_tags((string)Input::post('structures', true))) : null,
                        !empty(Input::post('assignments')) ? explode(',', strip_tags((string)Input::post('assignments', true))) : null,
                        !empty(Input::post('measures')) ? explode(',', strip_tags((string)Input::post('measures', true))) : null,
                        Input::post('public_in', true),
                        Input::post('organs', true)
                    );

                    // Upload allegati associati al personale.
                    $attach->update(
                        'attach_files',
                        'personnel',
                        $personnelId,
                        $personnel->institution_id,
                        $personnel->full_name,
                    );

                    $json->set('message', __('success_update_operation', null, 'patos'));
                }

            } else {

                $code = $json->bad();
                $json->error('error', $doUpload['data']);
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
     * @param PersonnelModel|null $personnel Personale per cui inserire le relazioni
     * @param array|int|null $structures Strutture associate al personale
     * @param array|int|null $assignments Incarichi associati al personale
     * @param array|int|null $measures Provvedimenti associati al personale
     * @param array|int|null $publicIn Sezioni per il "pubblica in"
     * @param array|int|null $politicalOrgans Valori per il campo "Organo-politico"
     * @return void
     */
    protected function clear(PersonnelModel $personnel = null, array|int $structures = null, array|int $assignments = null, array|int $measures = null, array|int $publicIn = null, array|int $politicalOrgans = null): void
    {
        $dataStructures = [];
        if (!empty($structures)) {
            foreach ($structures as $structure) {
                $dataStructures[] = is_array($structure) ? $structure['id'] : $structure;
            }
        }
        //Insert/Update nella tabella di relazione
        $personnel->referent_structures()->syncWithPivotValues($dataStructures, ['typology' => 'referent']);

        $dataAssignments = [];
        if ($assignments !== null) {
            foreach ($assignments as $assignment) {
                $dataAssignments[] = is_array($assignment) ? $assignment['id'] : $assignment;
            }
        }
        //Insert/Update nella tabella di relazione
        $personnel->assignments()->sync($dataAssignments);

        $dataMeasures = [];
        if ($measures !== null) {
            foreach ($measures as $measure) {
                $dataMeasures[] = is_array($measure) ? $measure['id'] : $measure;
            }
        }
        //Insert/Update nella tabella di relazione
        $personnel->measures()->sync($dataMeasures);

        $dataPublicIn = [];
        if ($publicIn !== null) {
            foreach ($publicIn as $in) {
                $dataPublicIn[] = is_array($in) ? strip_tags($in['section_fo_id']) : strip_tags($in);
            }
        }
        //Insert/Update nella tabella di relazione
        $personnel->public_in()->sync($dataPublicIn);

        //Organi politici
        RelSectionsFoPoliticOrganPersonnel::where('object_personnel_id', $personnel->id)->delete();
        if ($politicalOrgans !== null) {
            $now = date("Y-m-d H:i:s");
            $dataPoliticalOrgans = array_map(function($org) use($personnel,$now){
                return [
                    'institution_id' => $personnel->institution_id,
                    'section_fo_id' => $org,
                    'object_personnel_id' => $personnel->id,
                    'created_at' => $now,
                    'updated_at' => $now
                ];
            }, $politicalOrgans);
            RelSectionsFoPoliticOrganPersonnel::insert($dataPoliticalOrgans);
        }

    }

    /**
     * @description Funzione che effettua l'eliminazione di un Personale
     *
     * @return void
     * @throws Exception
     * @url /admin/personnel/delete.html
     * @method GET
     */
    public function delete(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute([__FUNCTION__, 'create', 'update'], true);

        if (!guard()) {
            redirect('admin/personnel');
        }

        //Validatore che verifica se l'elemento da eliminare esiste
        $validator = new PersonnelValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getDelete() && $this->acl->getUpdate());

        if (!$validate['is_success']) {
            sessionSetNotify($validate['errors'], 'danger');
            redirect('admin/personnel');
            exit();
        }

        $personnel = Registry::get('personnel');

        $photo = $personnel->photo;

        //Elimino il personale
        $personnel->deleteWithLogs($personnel);

        //Elimino l'eventuale foto del personale dal file system
        if (!empty($photo)) {
            removePhoto($photo);
        }

        // Generazione nuovo token
//        Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        sessionSetNotify(__('success_delete_operation', null, 'patos'));

        redirect('admin/personnel');
    }

    /**
     * @description Funzione per l'eliminazione multipla
     *
     * @return void
     * @throws Exception
     * @url /admin/personnel/deletes.html
     * @method GET
     */
    public function deletes(): void
    {
        //Setto il metodo della rotta
        $this->acl->setRoute('delete', true);
        $validator = new PersonnelValidator();
        $validate = $validator->multipleSelection();

        if ($validate['is_success']) {

            sessionSetNotify('Operazione avvenuta con successo');

            //Recupero gli elementi da eliminare dal registro
            $personnel = Registry::get('__ids__multi_select_profile__');

            //Elimino gli elementi
            foreach ($personnel as $p) {
                $p->deleteWithLogs($p);

                $photo = $p->photo;

                //Elimino l'eventuale foto del personale dal file system
                if (!empty($photo)) {
                    removePhoto($photo);
                }
            }

        } else {

            sessionSetNotify($validate['errors'], 'danger');

        }

        redirect('/admin/personnel');
    }

    /**
     * @description Funzione per l'upload dei file
     *
     * @return array
     */
    private function doUpload(): array
    {
        $data = [];

        $upload = new Uploads();
        $config['upload_path'] = './media/' . instituteDir() . '/assets/images/';
        $config['allowed_types'] = 'png|jpeg|gif|jpg';
        $config['encrypt_name'] = true;
        $config['file_ext_tolower'] = true;
        // Allineato al limite già applicato da PersonnelValidator.
        $config['max_size'] = 1024;
        $config['min_width'] = 50;
        $config['min_height'] = 50;
        $config['max_filename'] = 50;
        $config['remove_spaces'] = true;

        $upload->initialize($config);

        if ($upload->doUpload('photo')) {

            $data['success'] = false;
            $data['data'] = $upload->data();

        } else {

            $data['success'] = true;
            $data['data'] = $upload->displayErrors();

        }

        return $data;
    }

    /**
     * @description Funzione che setta i dati per il campo "Pubblica In"
     * @param bool|array|string|null $institutionTypeId Id del tipo ente
     * @return array
     */
    private function setPublicInData(bool|array|string|null $institutionTypeId): array
    {
        $publicIn = SectionFoConfigPublicationArchive::where('archive_name', '=', 'object_personnel')
            ->leftJoin("rel_institution_type_public_in_section", function ($join) use ($institutionTypeId) {
                $join->on("rel_institution_type_public_in_section.section_public_in_id", "=", "section_fo_config_publication_archive.id")
                    ->where('rel_institution_type_public_in_section.institution_type_id', '=', $institutionTypeId['institution_type_id']);
            })
//            ->join('rel_institution_type_public_in_section', 'rel_institution_type_public_in_section.section_public_in_id', '=', 'section_fo_config_publication_archive.id')
            ->with(['section' => function ($query) use ($institutionTypeId) {
                $query->select(['section_fo.id', 'name', 'rel_institution_type_sections_labeling.label'])
                    ->leftJoin("rel_institution_type_sections_labeling", function ($join) use ($institutionTypeId) {
                        $join->on("rel_institution_type_sections_labeling.sections_id", "=", "section_fo.id")
                            ->where('rel_institution_type_sections_labeling.institution_type_id', '=', $institutionTypeId['institution_type_id']);
                    });
            }]);

        $publicIn = $publicIn->get()->toArray();

        $dataPublicIn = [];

        foreach ($publicIn as $tmp) {
            if(!empty($tmp['section'])){
                $dataPublicIn[$tmp['section']['id']] = !empty($tmp['label']) ? $tmp['label'] . ' - ' : '';
                $dataPublicIn[$tmp['section']['id']] .= !empty($tmp['section']['label']) ? $tmp['section']['label'] : $tmp['section']['name'];
            }
        }

        return $dataPublicIn;
    }


    /**
     * @description Metodo per l'update e l'insert dei dati dello storico del personale
     *
     * @param int|null $personnelId {id del personale}
     * @param array|int|null $historicalData {dati dello storico}
     * @return void
     */
    private function historicalData(int $personnelId = null, array|int $historicalData = null): void
    {

        DataHistoricalPersonnelModel::where('personnel_id', $personnelId)
            ->delete();

        foreach ($historicalData as $hist) {

            $histData = [
                'personnel_id' => strip_tags(escapeXss($personnelId)),
                'historical_role' => !empty($hist['historical_role']) ? strip_tags(escapeXss($hist['historical_role'])) : null,
                'historical_structure' => !empty($hist['historical_structure']) ? strip_tags(escapeXss($hist['historical_structure'])) : null,
                'historical_from_date' => !empty($hist['historical_from_date']) ? strip_tags(escapeXss($hist['historical_from_date'])) : null,
                'historical_to_date' => !empty($hist['historical_to_date']) ? strip_tags(escapeXss($hist['historical_to_date'])) : null,
            ];

            DataHistoricalPersonnelModel::create($histData);
        }
    }


    protected function getSectionName($sectionID = 0)
    {

        if(empty($sectionID)){
            return "";
        }

        $name = SectionsFoModel::select(['id', 'name'])->where('id', $sectionID)->first();

        if(!empty($name)){
            return $name->toArray()['name'];
        }
        return "";

    }

}
