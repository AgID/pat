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
use Helpers\Validators\GrantLiquidationValidator;
use Helpers\Validators\GrantValidator;
use Model\GrantsModel;
use Scope\DeletedScope;
use System\Arr;
use System\Input;
use System\JsonResponse;
use System\Registry;
use System\Session;
use System\Token;

/**
 *
 * Controller Sovvenzioni e vantaggi economici
 *
 */
class GrantAdminController extends BaseAuthController
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
     * @description Renderizza la pagina index delle Sovvenzioni
     *
     * @return void
     * @throws Exception
     * @url /admin/grant.html
     * @method GET
     */
    public function index(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['read', 'create']);

        $this->breadcrumb->push('Sovvenzioni e vantaggi', '/');
        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Sovvenzioni e vantaggi';
        $data['subTitleSection'] = 'GESTIONE DELLE SOVVENZIONI E DEI VANTAGGI ECONOMICI';
        $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';

        $data['formAction'] = '/admin/grant';
        $data['formSettings'] = [
            'name' => 'form_grant',
            'id' => 'form_grant',
            'class' => 'form_grant',
        ];

        $data['include'] = '';

        $data['url'] = uri()->segment(2, 0);

        render('grant/index', $data, 'admin');
    }

    /**
     * @description Funzione per la paginazione dei dati nel datatable
     *
     * @return void
     * @url /admin/grant/list.html
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

            // Colonne su cui poter effettuare l'ordinamento
            $orderable = [
                3 => 'typology',
                6 => 'users.name',
                7 => 'updated_at'
            ];

            //Se l'utente è super Admin mostro la colonna dell'ente
            if (isSuperAdmin(true)) {
                $orderable[8] = 'i.full_name_institution';
            }

            //Setto proprietà datatable
            $dataTable = [];
            setDataTableData($dataTable, 'object');

            //Query per i dati da mostrare nel datatable
            $totalRecords = GrantsModel::select('count(id) as allcount')
                ->count();

            $searchValue = $dataTable['searchValue'];

            // Query count-sovvenzioni e vantaggi
            $queryCount = GrantsModel::search($searchValue);
            $queryCount->select(['count(id) as allcount']);
            $queryCount->join('users', 'users.id', '=', 'object_grants.owner_id', 'left outer');
            $queryCount->leftJoin('object_structures as structure', function ($join) {
                $join->on('structure.id', '=', 'object_grants.object_structures_id');
            });
            $totalRecordsWithFilter = $queryCount->count();

            $order = setOrderDatatable($dataTable['columnName'], $orderable, 'object');

            // Query count-sovvenzioni e vantaggi
            $queryRecords = GrantsModel::search($searchValue);
            $queryRecords->select(['object_grants.id', 'object_grants.owner_id', 'object_grants.institution_id', 'object_grants.object_structures_id',
                'object_grants.grant_id', 'object_grants.beneficiary_name', 'object_grants.object', 'object_grants.typology',
                'object_grants.type', 'object_grants.publishing_status', 'object_grants.updated_at', 'users.name', 'grant.object as relativeObject']);
            $queryRecords->leftJoin('object_grants as grant', function ($join) {
                $join->on('grant.id', '=', 'object_grants.grant_id');
            });
            $queryRecords->join('users', 'users.id', '=', 'object_grants.owner_id', 'left outer');
            $queryRecords->leftJoin('object_structures as structure', function ($join) {
                $join->on('structure.id', '=', 'object_grants.object_structures_id');
            });
            $queryRecords->with('personnel:id,full_name,archived');
            $queryRecords->with('structure:id,structure_name,archived');
            $queryRecords->with(['created_by' => function ($query) {
                $query->withoutGlobalScopes([DeletedScope::class]);
                $query->select(['id', 'name', 'deleted']);
            }]);
            $queryRecords->with('institution:id,full_name_institution');
            $queryRecords->with(['relative_grant' => function ($query) {
                $query->select(['id', 'object', 'beneficiary_name', 'concession_act_date', 'object_structures_id']);
                $query->with('structure:id,structure_name,archived');
            }]);
            $queryRecords->join('institutions as i', 'object_grants.institution_id', '=', 'i.id');
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

                    if (!empty($record['personnel']) && is_array($record['personnel'])) {

                        $tmpResponsibles = Arr::pluck($record['personnel'], 'full_name');
                        $tmpArchResponsibles = Arr::pluck($record['personnel'], 'archived');
                        $responsibles = str_replace(',', ',' . nbs(), implode(
                            ',',
                            array_map(
                                function ($responsible) {
                                    return ('<small class="badge badge-primary mb-1">'
                                        . escapeXss($responsible) . '</small>');
                                },
                                $tmpResponsibles,
                                $tmpArchResponsibles
                            )
                        ));
                    } else {

                        $responsibles = ($record['typology'] === 'Sovvenzione') ? '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definiti">N.D.</small>' : '';
                    }

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
                        ->addEdit('admin/grant/edit-' . $record['type'] . '/' . $record['id'], $record['id'])
                        ->addDuplicate('admin/grant/duplicate-' . $record['type'] . '/' . $record['id'], $record['id'])
                        ->addDelete('admin/grant/delete-' . $record['type'] . '/' . $record['id'], $record['id'])
                        ->render() : '';

                    $beneficiaryName = !empty($record['beneficiary_name'])
                        ? '<a href="' . siteUrl('/page/11/details/' . $record['id'] . '/' . urlTitle($record['beneficiary_name'])) . '" target="_blank">' . escapeXss($record['beneficiary_name']) . '</a>'
                        : (!empty($record['relative_grant']['beneficiary_name'])
                            ? '<a href="' . siteUrl('/page/155/details/' . $record['id'] . '/' . urlTitle($record['relative_grant']['beneficiary_name'])) . '" target="_blank">' . escapeXss($record['relative_grant']['beneficiary_name']) . '</a>'
                            : 'N.D.'
                        );

                    $object = !empty($record['object'])
                        ? $record['object']
                        : (!empty($record['relative_grant']['object'])
                            ? escapeXss($record['relative_grant']['object'])
                            : 'N.D.'
                        );

                    $structureName = !empty($record['structure']['structure_name'])
                        ? escapeXss($record['structure']['structure_name'])
                        : (!empty($record['relative_grant']['structure']['structure_name'])
                            ? escapeXss($record['relative_grant']['structure']['structure_name'])
                            : 'N.D.'
                        );

                    $icon = null;

                    //Setto i dati da mostrare nelle colonne del datatable
                    $setTempData = [];
                    $setTempData[] = (($this->acl->getCrud() || checkRecordOwner($record['owner_id'])) ? ButtonAction::checkList('item[]', $record['id']) : '') . $icon;
                    $setTempData[] = $beneficiaryName;
                    $setTempData[] = $object;
                    $setTempData[] = !empty($record['typology']) ? escapeXss($record['typology']) : 'N.D.';
                    $setTempData[] = $structureName;
                    $setTempData[] = $responsibles;
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
     * @description Renderizza il form per la creazione di una nuova Sovvenzione
     *
     * @return void
     * @throws Exception
     * @url /admin/grant/create-grant.html
     * @method GET
     */
    public function createGrant(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $data = [];

        //Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $data['is_box'] = uri()->uriString() === 'admin/grant/create-box';

        //Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {
            $this->breadcrumb->push('Sovvenzioni e vantaggi', 'admin/grant');
            $this->breadcrumb->push('Nuova', '/');
            $data['breadcrumbs'] = $this->breadcrumb->show();

            $data['titleSection'] = 'Sovvenzioni e vantaggi';
            $data['subTitleSection'] = 'GESTIONE DELLE SOVVENZIONI E DEI VANTAGGI ECONOMICI';
            $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';
        }

        $data['formAction'] = '/admin/grant/store-grant';
        $data['formSettings'] = [
            'name' => 'form_grant',
            'id' => 'form_grant',
            'class' => 'form_grant',
        ];
        $data['_storageType'] = 'insert';

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        render('grant/form_store_grant', $data, 'admin');
    }

    /**
     * @description Renderizza il form per la creazione di una nuova liquidazione
     *
     * @return void
     * @throws Exception
     * @url /admin/grant/create-liquidation.html
     * @method GET
     */
    public function createLiquidation(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $this->breadcrumb->push('Liquidazioni', 'admin/grant');
        $this->breadcrumb->push('Nuova', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();
        $data['titleSection'] = 'Sovvenzioni e vantaggi';
        $data['subTitleSection'] = 'GESTIONE DELLE SOVVENZIONI E DEI VANTAGGI ECONOMICI';
        $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';

        $data['formAction'] = '/admin/grant/store-liquidation';
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

        render('grant/form_store_liquidation', $data, 'admin');
    }

    /**
     * @description Funzione che effettua lo storage di una nuova Sovvenzione
     *
     * @return void
     * @throws Exception
     * @url /admin/grant/store-grant.html
     * @method POST
     */
    public function storeGrant(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new GrantValidator();
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
                    'beneficiary_name' => strip_tags((string)Input::post('beneficiary_name', true)),
                    'type' => 'grant',
                    'typology' => 'Sovvenzione',
                    'fiscal_data_not_available' => setDefaultData(strip_tags((string)Input::post('fiscal_data_not_available', true)), null, ['']),
                    'fiscal_data' => empty(Input::post('fiscal_data_not_available')) ? setDefaultData(strip_tags((string)Input::post('fiscal_data', true)), null, ['', null]) : null,
                    'object_structures_id' => setDefaultData(strip_tags((string)Input::post('object_structures_id', true)), null, ['']),
                    'object' => strip_tags((string)Input::post('object', true)),
                    'concession_amount' => !empty(Input::post('concession_amount')) ? toFloat(strip_tags(Input::post('concession_amount', true))) : null,
                    'concession_act_date' => !empty(Input::post('concession_act_date')) ? convertDateToDatabase(strip_tags((string)Input::post('concession_act_date', true))) : null,
                    'object_regulations_id' => setDefaultData(strip_tags((int)Input::post('object_regulations_id', true)), null, ['']),
                    'start_date' => !empty(Input::post('start_date')) ? convertDateToDatabase(strip_tags((string)Input::post('start_date', true))) : null,
                    'end_date' => !empty(Input::post('end_date')) ? convertDateToDatabase(strip_tags((string)Input::post('end_date', true))) : null,
                    'detection_mode' => Input::post('detection_mode', true),
                    'notes' => Input::post('notes', true),
                    'privacy' => setDefaultData(strip_tags(Input::post('privacy', true)), 0, [''])
                ];

                // Storage nuova Sovvenzione
                $insert = GrantsModel::createWithLogs($arrayValues);

                // Storage nelle tabelle di relazione
                $this->clear(
                    $insert,
                    !empty(Input::post('managers')) ? explode(',', strip_tags((string)Input::post('managers', true))) : null,
                    Input::post('normatives', true)
                );

                // Storage allegati associati al personale.
                $attach->storage('attach_files', 'grants', $insert->id, $insert['object']);

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
     * @description Funzione che effettua lo storage di una nuova Liquidazione
     *
     * @return void
     * @throws Exception
     * @url /admin/grant/store-liquidation.html
     * @method POST
     */
    public function storeLiquidation(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new GrantLiquidationValidator();
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
                    'typology' => 'Liquidazione',
                    'type' => 'liquidation',
                    'grant_id' => setDefaultData(strip_tags((int)Input::post('grant_id', true)), null, ['']),
                    'compensation_paid' => !empty(Input::post('compensation_paid')) ? toFloat(strip_tags(Input::post('compensation_paid', true))) : null,
                    'compensation_paid_date' => setDefaultData(strip_tags(Input::post('compensation_paid_date', true)), null, ['']),
                    'reference_date' => !empty(Input::post('reference_date')) ? convertDateToDatabase(strip_tags((string)Input::post('reference_date', true))) : null,
                    'notes' => Input::post('notes', true)
                ];

                // Storage nuova Liquidazione
                $insert = GrantsModel::createWithLogs($arrayValues);

                // Storage allegati associati al personale.
                $attach->storage('attach_files', 'grants', $insert->id, $insert->relative_grant['object']);

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
     * @description Renderizza il form per la modifica/duplicazione delle Sovvenzioni
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/grant/edit-grant.html
     * @method GET
     */
    public function editGrant(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['update', 'create']);

        //Validatore che verifica se l'elemento da modificare esiste
        $validator = new GrantValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/grant', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Allegati
        $attach = new AttachmentArchive();

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $grant = Registry::get('grant');
        $grant = !empty($grant) ? $grant->toArray() : [];

        $this->breadcrumb->push('Sovvenzioni e vantaggi', 'admin/grant');
        $this->breadcrumb->push('Modifica', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();
        $data['titleSection'] = 'Sovvenzioni e vantaggi';
        $data['subTitleSection'] = 'GESTIONE DELLE SOVVENZIONI E DEI VANTAGGI ECONOMICI';
        $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';

        // Vedo in sessione se sono arrivato qui da un operazione di ripristino dal versioning
        $session = new Session();
        $restore = $session->getFlash('restore');
        $data['restore'] = $restore;

        // Controllo se si sta duplicando un tasso di assenza
        $isDuplicate = uri()->segment(3, 0) === 'duplicate-grant';

        // Setto il tipo di storage
        $data['_storageType'] = $isDuplicate ? 'duplicate' : 'update';

        //In base al tipo di storage setto l'action del form
        $data['formAction'] = ($isDuplicate) ? '/admin/grant/store-grant' : '/admin/grant/update-grant';
        $data['formSettings'] = [
            'name' => 'form_grant',
            'id' => 'form_grant',
            'class' => 'form_grant',
        ];

        $concessionActDate = convertDateToForm($grant['concession_act_date']);
        $startDate = convertDateToForm($grant['start_date']);
        $endDate = convertDateToForm($grant['end_date']);

        $grant['start_date'] = $startDate['date'];

        $data['grant'] = $grant;

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'grants',
            $grant['id']
        );

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = $grant['institution_id'];

        $data['concession_act_date'] = $concessionActDate['date'];
        $data['end_date'] = $endDate['date'];

        $data['managerIds'] = Arr::pluck($grant['personnel'], 'id');
        $data['normativeIds'] = Arr::pluck($grant['normatives'], 'id');

        $data['seo'] = $grant['p_s_d_r'] ?? null;

        render('grant/form_store_grant', $data, 'admin');
    }

    /**
     * @description Renderizza il form di modifica/duplicazione delle Liquidazioni
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/grant/edit-liquidation.html
     * @method GET
     */
    public function editLiquidation(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['update', 'create']);

        //Validatore che verifica se l'elemento da modificare esiste
        $validator = new GrantLiquidationValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/grant', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Allegati
        $attach = new AttachmentArchive();

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $liquidation = Registry::get('grant_liquidation');
        $liquidation = !empty($liquidation) ? $liquidation->toArray() : [];

        $this->breadcrumb->push('Liquidazioni', 'admin/grant');
        $this->breadcrumb->push('Modifica', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();
        $data['titleSection'] = 'Sovvenzioni e vantaggi';
        $data['subTitleSection'] = 'GESTIONE DELLE SOVVENZIONI E DEI VANTAGGI ECONOMICI';
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
        $data['formAction'] = ($isDuplicate) ? '/admin/grant/store-liquidation' : '/admin/grant/update-liquidation';
        $data['formSettings'] = [
            'name' => 'form_liquidation',
            'id' => 'form_liquidation',
            'class' => 'form_liquidation',
        ];

        $referenceDate = convertDateToForm($liquidation['reference_date']);

        $data['liquidation'] = $liquidation;

        // Anno liquidazione
        $years = [null => 'Seleziona un anno'];
        for ((int)$i = date('Y'); $i >= 2013; $i--) {
            $years[$i] = $i;
        }

        $data['liquidationYears'] = $years;

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'grants',
            $liquidation['id']
        );

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = $liquidation['institution_id'];

        $data['reference_date'] = $referenceDate['date'];

        $data['seo'] = $liquidation['p_s_d_r'] ?? null;

        render('grant/form_store_liquidation', $data, 'admin');
    }

    /**
     * @description Funzione che effettua l'update di una Liquidazione
     *
     * @return void
     * @throws Exception
     * @url /admin/grant/update-liquidation/:id.html
     * @method POST
     */
    public function updateLiquidation(): void
    {
        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new GrantLiquidationValidator();
        $check = $validator->check('update');

        if ($check['is_success']) {
            $doAction = true;

            $liquidationId = (int)strip_tags(Input::post('id', true));

            // Recupero la liquidazione attuale prima di modificarla e la salvo nel versioning
            $liquidation = GrantsModel::where('id', $liquidationId)
                ->with(['relative_grant' => function ($query) {
                    $query->select(['object_grants.id', 'object', 'beneficiary_name', 'structure.structure_name as relative_structure_name'])
                        ->join('object_structures as structure', 'structure.id', '=', 'object_grants.object_structures_id', 'left outer');
                }])
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
                $data['grant_id'] = setDefaultData(strip_tags((int)Input::post('grant_id', true)), null, ['']);
                $data['compensation_paid'] = !empty(Input::post('compensation_paid')) ? toFloat(strip_tags(Input::post('compensation_paid', true))) : null;
                $data['compensation_paid_date'] = setDefaultData(strip_tags((string)Input::post('compensation_paid_date', true)), null, ['']);
                $data['reference_date'] = !empty(Input::post('reference_date')) ? convertDateToDatabase(strip_tags((string)Input::post('reference_date', true))) : null;
                $data['notes'] = Input::post('notes', true);

                // Update Liquidazione
                GrantsModel::where('id', $liquidationId)->updateWithLogs($liquidation, $data);

                // Upload allegati associati al personale.
                $attach->update(
                    'attach_files',
                    'grants',
                    $liquidationId,
                    $liquidation['institution_id'],
                    $liquidation->relative_grant['object']
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
     * @description Funzione che effettua l'update di una Sovvenzione
     *
     * @return void
     * @throws Exception
     * @url /admin/grant/update-grant/:id.html
     * @method POST
     */
    public function updateGrant(): void
    {
        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new GrantValidator();
        $check = $validator->check('update');

        if ($check['is_success']) {
            $doAction = true;

            $grantId = (int)strip_tags(Input::post('id', true));

            // Recupero la sovvenzione attuale prima di modificarla e la salvo nel versioning
            $grant = GrantsModel::where('id', $grantId)
                ->with('personnel:id,full_name')
                ->with('normatives:id,name')
                ->with('regulation:id,title')
                ->with('structure:id,structure_name')
                ->with('all_attachs');

            $grant = $grant->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute('update', (!checkRecordOwner($grant['owner_id']) && $this->acl->getCreate()));

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
                $data['beneficiary_name'] = strip_tags((string)Input::post('beneficiary_name', true));
                $data['fiscal_data_not_available'] = setDefaultData(strip_tags((string)Input::post('fiscal_data_not_available', true)), null, ['']);
                $data['fiscal_data'] = empty(Input::post('fiscal_data_not_available')) ? setDefaultData(strip_tags((string)Input::post('fiscal_data', true)), null, ['', null]) : null;
                $data['object_structures_id'] = setDefaultData(strip_tags((int)Input::post('object_structures_id', true)), null, ['']);
                $data['object'] = strip_tags((string)Input::post('object', true));
                $data['concession_amount'] = !empty(Input::post('concession_amount')) ? toFloat(strip_tags(Input::post('concession_amount', true))) : null;
                $data['concession_act_date'] = !empty(Input::post('concession_act_date')) ? convertDateToDatabase(strip_tags((string)Input::post('concession_act_date', true))) : null;
                $data['object_regulations_id'] = setDefaultData(strip_tags((int)Input::post('object_regulations_id', true)), null, ['']);
                $data['start_date'] = !empty(Input::post('start_date')) ? convertDateToDatabase(strip_tags((string)Input::post('start_date', true))) : null;
                $data['end_date'] = !empty(Input::post('end_date')) ? convertDateToDatabase(strip_tags((string)Input::post('end_date', true))) : null;
                $data['detection_mode'] = Input::post('detection_mode', true);
                $data['notes'] = Input::post('notes', true);
                $data['privacy'] = setDefaultData(strip_tags((int)Input::post('privacy', true)), 0, ['']);

                // Update Sovvenzione
                GrantsModel::where('id', $grantId)->updateWithLogs($grant, $data);

                // Svuoto le tabelle di relazione e le aggiorno
                $this->clear(
                    $grant,
                    !empty(Input::post('managers')) ? explode(',', strip_tags((string)Input::post('managers', true))) : null,
                    Input::post('normatives', true)
                );

                // Upload allegati associati al personale.
                $attach->update(
                    'attach_files',
                    'grants',
                    $grantId,
                    $grant['institution_id'],
                    $grant['object']
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
     * @param GrantsModel|null $grant      Sovvenzione
     * @param array|int|null   $managers   Personale associato alla Sovvenzione
     * @param array|int|null   $normatives Normative associate alla Sovvenzione
     * @return void
     */
    protected function clear(GrantsModel $grant = null, array|int $managers = null, array|int $normatives = null): void
    {
        $dataManager = [];
        if ($managers !== null) {
            foreach ($managers as $manager) {

                $dataManager[] = is_array($manager) ? $manager['id'] : $manager;
            }
        }
        //Insert/Update nella tabella di relazione
        $grant->personnel()->sync($dataManager);

        $dataNormative = [];
        if ($normatives !== null) {
            foreach ($normatives as $normative) {

                $dataNormative[] = is_array($normative) ? $normative['id'] : $normative;
            }
        }
        //Insert/Update nella tabella di relazione
        $grant->normatives()->sync($dataNormative);
    }


    /**
     * @description Funzione che effettua l'eliminazione di una Sovvenzione
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/grant/delete-grant/:id.html
     * @method GET
     */
    public function deleteGrant(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['delete', 'create', 'update'], true);

        //Validatore che verifica se l'elemento da eliminare esiste
        $validator = new GrantValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getDelete() && $this->acl->getUpdate());

        if (!$validate['is_success']) {
            redirect('admin/grant', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Se la validazione va a buon fine recupero l'elemento da eliminare dal registro
        $grant = Registry::get('grant');

        //Elimino la sovvenzione
        $grant->deleteWithLogs($grant);

        sessionSetNotify(__('success_delete_operation', null, 'patos'));
        // Generazione nuovo token
        Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        redirect('admin/grant');
    }

    /**
     * @description Funzione che effettua l'eliminazione di una Liquidazione
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/grant/delete-liquidation/:id.html
     * @method GET
     */
    public function deleteLiquidation(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['delete', 'create', 'update'], true);

        //Validatore che verifica se l'elemento da eliminare esiste
        $validator = new GrantLiquidationValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getDelete() && $this->acl->getUpdate());

        if (!$validate['is_success']) {
            redirect('admin/grant', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Se la validazione va a buon fine recupero l'elemento da eliminare dal registro
        $liquidation = Registry::get('grant_liquidation');

        //Elimino la liquidazione
        $liquidation->deleteWithLogs($liquidation);

        sessionSetNotify(__('success_delete_operation', null, 'patos'));
        // Generazione nuovo token
        Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        redirect('admin/grant');
    }

    /**
     * @description Funzione per l'eliminazione multipla
     *
     * @return void
     * @throws Exception
     * @url /admin/grant/deletes.html
     * @method GET
     */
    public function deletes(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('delete');

        $validator = new GrantValidator();
        $validate = $validator->multipleSelection();

        if ($validate['is_success']) {

            sessionSetNotify('Operazione avvenuta con successo');

            //Recupero gli elementi da eliminare dal registro
            $grants = Registry::get('__ids__multi_select_profile__');

            //Estraggo gli array degli elementi da eliminare
            $ids = Arr::pluck($grants, 'id');

            //Elimino gli elementi
            foreach ($grants as $grant) {
                $grant->deleteWithLogs($grant);
            }

            // Generazione nuovo token
            Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        } else {

            sessionSetNotify($validate['errors'], 'danger');
        }

        redirect('/admin/grant');
    }
}
