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
use Helpers\Validators\AssignmentLiquidationValidator;
use Helpers\Validators\AssignmentValidator;
use Helpers\Validators\DatatableValidator;
use Model\AssignmentsModel;
use Scope\DeletedScope;
use System\Arr;
use System\Input;
use System\JsonResponse;
use System\Registry;
use System\Session;
use System\Token;

/**
 *
 * Controller Incarichi e consulenze
 *
 */
class AssignmentAdminController extends BaseAuthController
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
     * @description Renderizza la pagina index degli Incarichi
     *
     * @return void
     * @method GET
     * @throws Exception
     * @url admin/assignment.html
     */
    public function index(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['read', 'create']);

        $this->breadcrumb->push('Incarichi e consulenze', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Incarichi e consulenze';
        $data['subTitleSection'] = 'GESTIONE DEGLI INCARICHI E DELLE CONSULENZE';
        $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';

        $data['formAction'] = '/admin/assignment';
        $data['formSettings'] = [
            'name' => 'form_assignment',
            'id' => 'form_assignment',
            'class' => 'form_assignment',
        ];

        $data['include'] = '';

        $data['url'] = uri()->segment(2, 0);

        render('assignment/index', $data, 'admin');
    }

    /**
     * @description Funzione per la paginazione dei dati nel datatable
     *
     * @return void
     * @url /admin/assignment/list.html
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
                1 => 'name',
                2 => 'object',
                3 => 'type',
                4 => 'assignment_start',
                5 => 'userName',
                6 => 'updated_at'
            ];

            // Se l'utente è super Admin mostro la colonna dell'ente
            if (isSuperAdmin(true)) {
                $orderable[7] = 'i.full_name_institution';
            }

            //Setto proprietà datatable
            $dataTable = [];
            setDataTableData($dataTable, 'object');

            // Query per i dati da mostrare nel datatable
            $totalRecords = AssignmentsModel::select('count(id) as allcount')
                ->count();

            $searchValue = $dataTable['searchValue'];

            // Query count - Incarichi e consulenze
            $queryCount = AssignmentsModel::search($searchValue);
            $queryCount->select(['count(object_assignments.id) as allcount'])
                ->join('users', 'users.id', '=', 'object_assignments.owner_id', 'left outer');

            //Ricerca per id
            if (!empty($searchValue)) {
                $queryCount->orWhere('object_assignments.id', '=', $searchValue);
            }
            $totalRecordsWithFilter = $queryCount->count();

            $order = setOrderDatatable($dataTable['columnName'], $orderable, 'object');

            // Query result - Incarichi e consulenze
            $queryRecords = AssignmentsModel::search($searchValue);
            $queryRecords->select(['object_assignments.id', 'object_assignments.owner_id', 'object_assignments.institution_id',
                'object_assignments.object_structures_id', 'object_assignments.related_assignment_id', 'object_assignments.typology',
                'object_assignments.type', 'object_assignments.name', 'object_assignments.object', 'users.name as userName', 'i.full_name_institution',
                'object_assignments.assignment_start', 'object_assignments.publishing_status', 'object_assignments.updated_at']);
            $queryRecords->join('users', 'users.id', '=', 'object_assignments.owner_id', 'left outer');
            $queryRecords->with('related_assignment:id,name,object');
            $queryRecords->with(['created_by' => function ($query) {
                $query->withoutGlobalScopes([DeletedScope::class]);
                $query->select(['id', 'name', 'deleted']);
            }]);
            $queryRecords->with('institution:id,full_name_institution');
            $queryRecords->join('object_assignments as related_assignment', 'related_assignment.id', '=', 'object_assignments.related_assignment_id', 'left outer');
            $queryRecords->join('institutions as i', 'object_assignments.institution_id', '=', 'i.id', 'left outer');

            //Ricerca per id
            if (!empty($searchValue)) {
                $queryRecords->orWhere('object_assignments.id', '=', $searchValue);
            }

            $queryRecords->orderBy($order, $dataTable['columnSortOrder']);
            $queryRecords->offset($dataTable['start']);
            $queryRecords->limit($dataTable['rowPerPage']);
            $records = $queryRecords->get();
            $records = !empty($records) ? $records->toArray() : [];

            $response['draw'] = intval($dataTable['draw']);
            $response['iTotalRecords'] = ($totalRecords);
            $response['iTotalDisplayRecords'] = ($totalRecordsWithFilter);
            $response['aaData'] = [];

            if (!empty($records)) {

                foreach ($records as $record) {

                    $startDate = !empty($record['assignment_start'])
                        ? ('<small class="badge badge-info"> 
                            <i class="far fa-calendar-alt"></i> ' .
                            date('d-m-Y', strtotime($record['assignment_start'])) .
                            '</small>')
                        : '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definita">N.D.</small>';

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
                        ->addEdit('admin/assignment/edit-' . $record['typology'] . '/' . $record['id'], $record['id'])
                        ->addDuplicate('admin/assignment/duplicate-' . $record['typology'] . '/' . $record['id'], $record['id'])
                        ->addDelete('admin/assignment/delete-' . $record['typology'] . '/' . $record['id'], $record['id'])
                        ->render() : '';

                    $name = !empty($record['name'])
                        ? '<a href="' . siteUrl('/page/3/details/' . $record['id'] . '/' . urlTitle($record['name'])) . '" target="_blank">' . escapeXss($record['name']) . '</a>'
                        : (!empty($record['related_assignment']['name'])
                            ? '<a href="' . siteUrl('/page/3/details/' . $record['id'] . '/' . urlTitle($record['related_assignment']['name'])) . '" target="_blank">' . escapeXss($record['related_assignment']['name']) . '</a>'
                            : 'N.D.'
                        );

                    $object = !empty($record['object'])
                        ? $record['object']
                        : (!empty($record['related_assignment']['object'])
                            ? escapeXss($record['related_assignment']['object'])
                            : 'N.D.'
                        );

                    $icon = null;

                    //Setto i dati da mostrare nelle colonne del datatable
                    $setTempData = [];
                    $setTempData[] = (($this->acl->getCrud() || checkRecordOwner($record['owner_id'])) ? ButtonAction::checkList('item[]', $record['id']) : '') . $icon;
                    $setTempData[] = !empty($name) ? $name : 'N.D.';
                    $setTempData[] = !empty($object) ? $object : 'N.D.';
                    $setTempData[] = !empty($record['type']) ? escapeXss($record['type']) : 'N.D.';
                    $setTempData[] = $startDate;
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
     * @description Renderizza il form per la creazione di un nuovo Incarico
     *
     * @return void
     * @method GET
     * @throws Exception
     * @url /admin/assignment/create-assignment.html
     */
    public function createAssignment(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $data = [];

        //Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $data['is_box'] = uri()->uriString() === 'admin/assignment/create-box';

        //Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {

            $this->breadcrumb->push('Incarichi e consulenze', 'admin/assignment');
            $this->breadcrumb->push('Nuovo', '/');
            $data['breadcrumbs'] = $this->breadcrumb->show();

            //Dati header della sezione
            $data['titleSection'] = 'Incarichi e consulenze';
            $data['subTitleSection'] = 'GESTIONE DEGLI INCARICHI E DELLE CONSULENZE';
            $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';
        }

        $data['formAction'] = '/admin/assignment/store-assignment';
        $data['formSettings'] = [
            'name' => 'form_assignment',
            'id' => 'form_assignment',
            'class' => 'form_assignment',
        ];
        $data['_storageType'] = 'insert';

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        $data['typologies'] = ['' => null] + config('assignmentTypologies', null, 'app');

        // Labels
        $data['labels'] = [];

        render('assignment/form_store_assignment', $data, 'admin');
    }

    /**
     * @description Renderizza il form per la creazione di una nuova liquidazione
     *
     * @return void
     * @method GET
     * @throws Exception
     * @url /admin/assignment/create-liquidation.html
     */
    public function createLiquidation(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $this->breadcrumb->push('Incarichi e consulenze', 'admin/assignment');
        $this->breadcrumb->push('Nuova', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Incarichi e consulenze';
        $data['subTitleSection'] = 'GESTIONE DEGLI INCARICHI E DELLE CONSULENZE';
        $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';

        $data['formAction'] = '/admin/assignment/store-liquidation';
        $data['formSettings'] = [
            'name' => 'form_liquidation',
            'id' => 'form_liquidation',
            'class' => 'form_liquidation',
        ];

        $data['_storageType'] = 'insert';

        // Anno liquidazione
        $years = [null => 'Seleziona un anno'];
        for ((int)$i = date('Y'); $i >= 2013; $i--) {
            $years[$i] = $i;
        }

        $data['liquidationYears'] = $years;

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        render('assignment/form_store_liquidation', $data, 'admin');
    }

    /**
     * @description Funzione che effettua lo storage di un nuovo Incarico
     *
     * @return void
     * @method POST
     * @throws Exception
     * @url /admin/assignment/create-store-assignment.html
     */
    public function storeAssignment(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new AssignmentValidator();
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
                    'name' => strip_tags((string)Input::post('name', true)),
                    'object' => strip_tags((string)Input::post('object', true)),
                    'assignment_type' => setDefaultData(strip_tags((string)Input::post('assignment_type', true)), null, ['']),
                    'consulting_type' => setDefaultData(strip_tags((string)Input::post('consulting_type', true)), null, ['']),
                    'object_structures_id' => setDefaultData(strip_tags((int)Input::post('object_structures_id', true)), null, ['']),
                    'assignment_start' => !empty(Input::post('assignment_start')) ? convertDateToDatabase(strip_tags(Input::post('assignment_start', true))) : null,
                    'end_of_assignment_not_available' => setDefaultData(strip_tags(Input::post('end_of_assignment_not_available', true)), null, ['']),
                    'assignment_end' => !empty(Input::post('assignment_end')) && empty(Input::post('end_of_assignment_not_available')) ? convertDateToDatabase(strip_tags(Input::post('assignment_end', true))) : null,
                    'end_of_assignment_not_available_txt' => !empty(Input::post('end_of_assignment_not_available')) ? strip_tags(Input::post('end_of_assignment_not_available_txt', true)) : null,
                    'compensation' => !empty(Input::post('compensation')) ? toFloat(strip_tags(Input::post('compensation', true))) : null,
                    'variable_compensation' => strip_tags(Input::post('variable_compensation', true)),
                    'acts_extremes' => Input::post('acts_extremes', true),
                    'assignment_reason' => strip_tags(Input::post('assignment_reason', true)),
                    'notes' => Input::post('notes', true),
                    'typology' => 'assignment',
                    'type' => 'Incarico',
                ];

                // Storage nuovo Incarico
                $insert = AssignmentsModel::createWithLogs($arrayValues);

                // Storage nelle tabelle di relazione
                $this->clear(
                    $insert,
                    !empty(Input::post('measures')) ? explode(',', strip_tags((string)Input::post('measures', true))) : null
                );

                // Storage allegati associati al personale.
                $attach->storage('attach_files', 'assignments', $insert->id, $arrayValues['name']);

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
     * @description Funzione che effettua lo storage di una nuova Liquidazione
     *
     * @return void
     * @method POST
     * @throws Exception
     * @url /admin/assignment/store-liquidation.html
     */
    public function storeLiquidation(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new AssignmentLiquidationValidator();
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

            // Se la validazione degli allegati va a buon fine eseguo l'update dell'oggetto
            if ($doAction) {
                $getIdentity = authPatOs()->getIdentity(['id', 'name']);

                $relativeAssignmentId = strip_tags(Input::post('related_assignment_id', true));

                $arrayValues = [
                    'owner_id' => $getIdentity['id'],
                    'institution_id' => checkAlternativeInstitutionId(),
                    'type' => 'Liquidazione',
                    'typology' => 'liquidation',
                    'related_assignment_id' => setDefaultData($relativeAssignmentId, null, ['']),
                    'compensation_provided' => !empty(Input::post('compensation_provided')) ? toFloat(strip_tags(Input::post('compensation_provided', true))) : null,
                    'liquidation_year' => setDefaultData(strip_tags(Input::post('liquidation_year', true)), null, ['']),
                    'liquidation_date' => !empty(Input::post('liquidation_date')) ? convertDateToDatabase(strip_tags(Input::post('liquidation_date', true))) : null,
                    'notes' => Input::post('notes', true)
                ];

                // Storage nuova Liquidazione
                $insert = AssignmentsModel::createWithLogs($arrayValues);

                // Storage allegati associati al personale.
                $attach->storage('attach_files', 'assignments', $insert->id, $insert->related_assignment['object']);

                // Generazione nuovo token
                Token::forceRegenerate((int)config('csrf_expire', null, 'app'));

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
     * @description Renderizza il form per la modifica/duplicazione di un Incarico
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/assignment/edit-assignment/:id.html
     * @method GET
     */
    public function editAssignment(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['update', 'create']);

        //Validatore che verifica se l'elemento da modificare esiste
        $validator = new AssignmentValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        $data = [];

        //Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $segments = uri()->segmentArray();
        array_pop($segments);
        $data['is_box'] = implode('/', $segments) === 'admin/assignment/edit-box-assignment';

        if (!$validate['is_success']) {
            //Controllo se non sono in un modale
            if (!$data['is_box']) {
                // Controllo se si hanno i permessi di modifica, in caso positivo
                //mostro il messaggio di errore che il record non esiste o non si hanno i permessi per modificarlo
                if (guard()) {
                    sessionSetNotify($validate['errors'], 'danger');
                }

                redirect('admin/assignment');
            } else {
                render('access_denied/modal_access_denied', [], 'admin');
                die();
            }
        }

        // Allegati
        $attach = new AttachmentArchive();

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $assignment = Registry::get('assignment');
        $assignment = !empty($assignment) ? $assignment->toArray() : [];

        // Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {
            $this->breadcrumb->push('Incarichi e consulenze', 'admin/assignment');
            $this->breadcrumb->push('Modifica', '/');

            $data = [];
            $data['breadcrumbs'] = $this->breadcrumb->show();

            //Dati header della sezione
            $data['titleSection'] = 'Incarichi e consulenze';
            $data['subTitleSection'] = 'GESTIONE DEGLI INCARICHI E DELLE CONSULENZE';
            $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';
        }

        // Vedo in sessione se sono arrivato qui da un operazione di ripristino dal versioning
        $session = new Session();
        $restore = $session->getFlash('restore');
        $data['restore'] = $restore;

        // Controllo se si sta duplicando un tasso di assenza
        $isDuplicate = uri()->segment(3, 0) === 'duplicate-assignment';

        // Setto il tipo di storage
        $data['_storageType'] = $isDuplicate ? 'duplicate' : 'update';

        //In base al tipo di storage setto l'action del form
        $data['formAction'] = ($isDuplicate) ? '/admin/assignment/store-assignment' : '/admin/assignment/update-assignment';
        $data['formSettings'] = [
            'name' => 'form_assignment',
            'id' => 'form_assignment',
            'class' => 'form_assignment',
        ];

        $assignmentStart = convertDateToForm($assignment['assignment_start']);
        $assignmentEnd = convertDateToForm($assignment['assignment_end']);
        $assignment['assignment_start'] = $assignmentStart['date'];
        $assignment['assignment_end'] = $assignmentEnd['date'];

        $data['assignment'] = $assignment;

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'assignments',
            $assignment['id']
        );

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = $assignment['institution_id'];

        $institutionTypeId = patOsInstituteInfo(['institution_type_id']);
        $data['typologies'] = [null => ''] + config('assignmentTypologies', null, 'app');

        // Labels
        $data['labels'] = [];

        $data['measureIds'] = Arr::pluck($assignment['measures'], 'id');
        $data['seo'] = $assignment['p_s_d_r'] ?? null;

        render('assignment/form_store_assignment', $data, 'admin');
    }

    /**
     * @description Renderizza il form per la modifica/duplicazione di una liquidazione
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/assignment/edit-liquidation/:id.html
     * @method GET
     */
    public function editLiquidation(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['update', 'create']);

        //Validatore che verifica se l'elemento da modificare esiste
        $validator = new AssignmentLiquidationValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/assignment', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Allegati
        $attach = new AttachmentArchive();

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $liquidation = Registry::get('assignment_liquidation');
        $liquidation = !empty($liquidation) ? $liquidation->toArray() : [];

        $this->breadcrumb->push('Incarichi e consulenze', 'admin/assignment');
        $this->breadcrumb->push('Modifica', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Incarichi e consulenze';
        $data['subTitleSection'] = 'GESTIONE DEGLI INCARICHI E DELLE CONSULENZE';
        $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';

        // Vedo in sessione se sono arrivato qui da un operazione di ripristino dal versioning
        $session = new Session();
        $restore = $session->getFlash('restore');
        $data['restore'] = $restore;

        // Controllo se si sta duplicando un tasso di assenza
        $isDuplicate = uri()->segment(3, 0) === 'duplicate-liquidation';

        // Setto il tipo di storage
        $data['_storageType'] = $isDuplicate ? 'duplicate' : 'update';

        //In base al tipo di storage setto l'action del form
        $data['formAction'] = ($isDuplicate) ? '/admin/assignment/store-liquidation' : '/admin/assignment/update-liquidation';
        $data['formSettings'] = [
            'name' => 'form_liquidation',
            'id' => 'form_liquidation',
            'class' => 'form_liquidation',
        ];

        $liquidationDate = convertDateToForm($liquidation['liquidation_date']);

        $data['liquidation'] = $liquidation;

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'assignments',
            $liquidation['id']
        );

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = $liquidation['institution_id'];

        $data['liquidation_date'] = $liquidationDate['date'];

        // Anno liquidazione
        $years = [null => 'Seleziona un anno'];
        for ((int)$i = date('Y'); $i >= 2013; $i--) {
            $years[$i] = $i;
        }

        $data['liquidationYears'] = $years;

        // Labels
        $data['labels'] = [];
        $data['seo'] = $liquidation['p_s_d_r'] ?? null;

        render('assignment/form_store_liquidation', $data, 'admin');
    }

    /**
     * @description Funzione che effettua l'update di un Incarico
     *
     * @return void
     * @throws Exception
     * @url /admin/assignment/update-assignment.html
     * @method POST
     */
    public function updateAssignment(): void
    {
        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new AssignmentValidator();
        $check = $validator->check('update');

        if ($check['is_success']) {
            $doAction = true;

            $assignmentId = (int)strip_tags(Input::post('id'));

            // Recupero l'incarico attuale prima di modificarlo e lo salvo nel versioning
            $assignment = AssignmentsModel::where('id', $assignmentId)
                ->with('measures:id,object')
                ->with('structure:id,structure_name')
                ->with('all_attachs');
            $assignment = $assignment->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute('update', (!checkRecordOwner($assignment['owner_id']) && $this->acl->getCreate()));

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
                $data['name'] = strip_tags(Input::post('name', true));
                $data['object'] = strip_tags(Input::post('object', true));
                $data['assignment_type'] = setDefaultData(strip_tags(Input::post('assignment_type', true)), null, ['']);
                $data['consulting_type'] = setDefaultData(strip_tags(Input::post('consulting_type', true)), null, ['']);
                $data['object_structures_id'] = setDefaultData(strip_tags(Input::post('object_structures_id', true)));
                $data['assignment_start'] = !empty(Input::post('assignment_start')) ? convertDateToDatabase(strip_tags(Input::post('assignment_start', true))) : null;
                $data['end_of_assignment_not_available'] = setDefaultData(strip_tags(Input::post('end_of_assignment_not_available', true)), null, ['']);
                $data['assignment_end'] = !empty(Input::post('assignment_end')) && empty(Input::post('end_of_assignment_not_available')) ? convertDateToDatabase(strip_tags(Input::post('assignment_end', true))) : null;
                $data['end_of_assignment_not_available_txt'] = !empty(Input::post('end_of_assignment_not_available')) ? strip_tags(Input::post('end_of_assignment_not_available_txt', true)) : null;
                $data['compensation'] = !empty(Input::post('compensation')) ? toFloat(strip_tags(Input::post('compensation', true))) : null;
                $data['variable_compensation'] = strip_tags(Input::post('variable_compensation', true));
                $data['acts_extremes'] = Input::post('acts_extremes', true);
                $data['assignment_reason'] = strip_tags(Input::post('assignment_reason', true));
                $data['notes'] = Input::post('notes', true);

                // Update Incarico
                AssignmentsModel::where('id', $assignmentId)->updateWithLogs($assignment, $data);

                // Svuoto le tabelle di relazione e le aggiorno
                $this->clear(
                    $assignment,
                    !empty(Input::post('measures')) ? explode(',', strip_tags((string)Input::post('measures', true))) : null
                );

                // Upload allegati associati al personale.
                $attach->update(
                    'attach_files',
                    'assignments',
                    $assignmentId,
                    $assignment['institution_id'],
                    $data['object']
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
     * @description Funzione che effettua l'update di una liquidazione
     *
     * @return void
     * @throws Exception
     * @url /admin/assignment/update-assignment.html
     * @method POST
     */
    public function updateLiquidation(): void
    {

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new AssignmentLiquidationValidator();
        $check = $validator->check('update');

        if ($check['is_success']) {
            $doAction = true;

            $liquidationId = (int)strip_tags(Input::post('id'));

            // Recupero la liquidazione attuale prima di modificarla e la salvo nel versioning
            $liquidation = AssignmentsModel::where('id', $liquidationId)
                ->with('related_assignment:id,object,assignment_type,name')
                ->with('all_attachs');
            $liquidation = $liquidation->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute('update', (!checkRecordOwner($liquidation['owner_id']) && $this->acl->getCreate()));

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
                $data['related_assignment_id'] = !empty(Input::post('related_assignment_id')) ? strip_tags(Input::post('related_assignment_id', true)) : null;
                $data['liquidation_date'] = !empty(Input::post('liquidation_date')) ? strip_tags(convertDateToDatabase(Input::post('liquidation_date', true))) : null;
                $data['compensation_provided'] = !empty(Input::post('compensation_provided')) ? toFloat(strip_tags(Input::post('compensation_provided', true))) : null;
                $data['liquidation_year'] = !empty(Input::post('liquidation_year')) ? strip_tags(Input::post('liquidation_year', true)) : null;
                $data['notes'] = !empty(Input::post('notes')) ? Input::post('notes', true) : null;

                // Update Liquidazione
                AssignmentsModel::where('id', $liquidationId)->updateWithLogs($liquidation, $data);

                // Upload allegati associati al personale.
                $attach->update(
                    'attach_files',
                    'assignments',
                    $liquidationId,
                    $liquidation['institution_id'],
                    $liquidation->related_assignment['object']
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
     * @param AssignmentsModel|null $assignment Incarico
     * @param array|int|null        $measures   Provvedimenti associati all'incarico
     * @return void
     */
    protected function clear(AssignmentsModel $assignment = null, array|int $measures = null): void
    {
        $dataMeasure = [];
        if ($measures !== null) {
            foreach ($measures as $measure) {
                $dataMeasure[] = is_array($measure) ? $measure['id'] : $measure;
            }
        }
        //Insert/Update nella tabella di relazione
        $assignment->measures()->sync($dataMeasure);
    }

    /**
     * @description Funzione che effettua l'eliminazione di un Incarico
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/assignment/delete-assignment/:id.html
     * @method GET
     */
    public function deleteAssignment(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute([__FUNCTION__, 'create', 'update'], true);

        //Validatore che verifica se l'elemento da eliminare esiste
        $validator = new AssignmentValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getDelete() && $this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/assignment', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Se la validazione va a buon fine recupero l'elemento da eliminare dal registro
        $assignment = Registry::get('assignment');

        $assignmentId = $assignment->id;

        //Elimino l'incarico
        $assignment->deleteWithLogs($assignment);

        sessionSetNotify(__('success_delete_operation', null, 'patos'));

        // Generazione nuovo token
        Token::forceRegenerate((int)config('csrf_expire', null, 'app'));

        redirect('admin/assignment');
    }

    /**
     * @description Funzione che effettua l'eliminazione di una liquidazione
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/assignment/delete-liquidation/:id.html
     * @method GET
     */
    public function deleteLiquidation(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute([__FUNCTION__, 'create', 'update'], true);

        // Validatore che verifica se l'elemento da eliminare esiste
        $validator = new AssignmentLiquidationValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getDelete() && $this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/assignment', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Se la validazione va a buon fine recupero l'elemento da eliminare dal registro
        $liquidation = Registry::get('assignment_liquidation');

        //Elimino la liquidazione
        $liquidation->deleteWithLogs($liquidation);

        sessionSetNotify(__('success_delete_operation', null, 'patos'));

        // Generazione nuovo token
        Token::forceRegenerate((int)config('csrf_expire', null, 'app'));

        redirect('admin/assignment');
    }

    /**
     * @description Funzione per l'eliminazione multipla
     *
     * @return void
     * @method GET
     * @throws Exception
     * @url /admin/assignment/deletes.html
     */
    public function deletes(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('delete');

        // Validatore sugli elementi da eliminare
        $validator = new AssignmentValidator();
        $validate = $validator->multipleSelection();

        if ($validate['is_success']) {

            sessionSetNotify('Operazione avvenuta con successo');

            //Recupero gli elementi da eliminare dal registro
            $assignments = Registry::get('__ids__multi_select_profile__');

            //Estraggo gli array degli elementi da eliminare
            $ids = Arr::pluck($assignments, 'id');

            //Elimino gli elementi
            foreach ($assignments as $assignment) {
                $assignment->deleteWithLogs($assignment);
            }

            // Generazione nuovo token
            Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        } else {

            sessionSetNotify($validate['errors'], 'danger');
        }

        redirect('/admin/assignment');
    }
}
