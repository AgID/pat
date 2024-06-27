<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Admin;

use Exception;
use Helpers\ActivityLog;
use Helpers\Utility\AttachmentArchive;
use Helpers\Utility\ButtonAction;
use Helpers\Validators\BdncpProcedureValidator;
use Helpers\Validators\DatatableValidator;
use Model\BdncpProcedureModel;
use Scope\DeletedScope;
use System\Action;
use System\Arr;
use System\Input;
use System\JsonResponse;
use System\Registry;
use System\Session;
use System\Token;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class BdncpProcedureController extends BaseAuthController
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
     * @description Renderizza la pagina index dei bandi di gara
     * @return void
     * @throws Exception
     * @url /admin/contests-act.html
     * @method GET
     */
    public function index(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['read', 'create']);

        $this->breadcrumb->push('Procedure BDNCP', '/');
        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Bandi di gara e contratti (dal 1/1/2024)';
        $data['subTitleSection'] = 'GESTIONE DEI BANDI DI GARA E CONTRATTI';
        $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';

        $data['formAction'] = '/admin/bdncp-procedure';
        $data['formSettings'] = [
            'name' => 'form_bdncp',
            'id' => 'form_bdncp',
            'class' => 'form_bdncp',
        ];

        $data['url'] = uri()->segment(2, 0);

        render('bdncp_procedure/index', $data, 'admin');
    }

    /**
     * @description Funzione per la paginazione dei dati nel datatable
     *
     * @return void
     * @url /admin/bdncp-procedure/list.html
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
                1 => 'type',
                3 => 'object',
                5 => 'userName',
                6 => 'updated_at'
            ];

            // Se l'utente è super Admin mostro la colonna dell'ente
            if (isSuperAdmin(true)) {
                $orderable[5] = 'i.full_name_institution';
            }

            //Setto proprietà datatable
            $dataTable = [];
            setDataTableData($dataTable, 'updated_at');

            // Query per i dati da mostrare nel datatable
            $totalRecords = BdncpProcedureModel::select('count(id) as allcount')
                ->count();

            $totalRecordsWithFilter = BdncpProcedureModel::search($dataTable['searchValue'])
                ->select('count(object_bdncp_procedure.id) as allcount')
                ->join('users', 'users.id', '=', 'object_bdncp_procedure.owner_id', 'left outer');

            if (!empty($dataTable['searchValue'])) {
                $totalRecordsWithFilter->orWhere('object_bdncp_procedure.id', '=', $dataTable['searchValue']);
            }

            $totalRecordsWithFilter = $totalRecordsWithFilter->count();

            $order = setOrderDatatable($dataTable['columnName'], $orderable, 'updated_at');

            $records = BdncpProcedureModel::search($dataTable['searchValue'])
                ->select(['object_bdncp_procedure.id', 'object_bdncp_procedure.owner_id', 'object_bdncp_procedure.amount_liquidated', 'object_bdncp_procedure.institution_id', 'object_bdncp_procedure.object',
                    'object_bdncp_procedure.updated_at', 'bdncp_link', 'publishing_status', 'object_bdncp_procedure.cig', 'object_bdncp_procedure.type', 'object_bdncp_procedure.typology',
                    'users.name as userName', 'i.full_name_institution'])
                ->join('users', 'users.id', '=', 'object_bdncp_procedure.owner_id', 'left outer')
                ->with(['created_by' => function ($query) {
                    $query->withoutGlobalScopes([DeletedScope::class]);
                    $query->select(['id', 'name', 'deleted']);
                }])
                ->with(['institution' => function ($query) {
                    $query->select(['id', 'full_name_institution']);
                }]);

            if (!empty($dataTable['searchValue'])) {
                $records->orWhere('object_bdncp_procedure.id', '=', $dataTable['searchValue']);
            }

            $records = $records->join('institutions as i', 'object_bdncp_procedure.institution_id', '=', 'i.id', 'left outer')
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
                        ->addEdit('admin/bdncp-procedure/edit' . ($record['typology'] === 'procedure' ? '' : '-'.$record['typology']) . '/' . $record['id'], $record['id'])
                        ->addDuplicate('admin/bdncp-procedure/duplicate' . ($record['typology'] === 'procedure' ? '' : '-'.$record['typology']) . '/' . $record['id'], $record['id'])
                        ->addDelete('admin/bdncp-procedure/delete/' . $record['id'], $record['id'])
                        ->render() : '';

                    $icon = null;

                    //Setto i dati da mostrare nelle colonne del datatable
                    $setTempData = [];
                    $setTempData[] = ($this->acl->getCrud() || checkRecordOwner($record['owner_id'])) ? ButtonAction::checkList('item[]', $record['id']) : '';
                    $setTempData[] = !empty($record['type']) ? ucfirst($record['type']) : '';
                    $setTempData[] = !empty($record['cig']) ? '<a href="' . siteUrl('/page/10/details/' . $record['id'] . '/' . urlTitle($record['cig'])) . '" target="_blank">' . escapeXss($record['cig']) . '</a>' : '';
                    $setTempData[] = $icon . (!empty($record['object'])
                            ? '<a href="' . siteUrl('/page/10/details/' . $record['id'] . '/' . urlTitle(escapeXss($record['object']))) . '" target="_blank">' . escapeXss($record['object']) . '</a>'
                            : 'N.D.');
                    $setTempData[] = !empty($record['bdncp_link'])
                        ? '<a href="' . $record['bdncp_link'] . '" target="_blank">' . escapeXss($record['bdncp_link']) . '</a>'
                        : '<small class="badge badge-danger">N/A</small>';

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
     * @url /admin/bdncp/create.html
     * @method GET
     */
    public function create(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(__FUNCTION__);

        $data = [];

        //Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $data['is_box'] = uri()->uriString() === 'admin/bdncp-procedure/create-box';

        //Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {

            $this->breadcrumb->push('Procedure BDNCP', 'admin/bdncp-procedure');
            $this->breadcrumb->push('Nuova', '/');
            $data['breadcrumbs'] = $this->breadcrumb->show();

            //Dati header della sezione
            $data['titleSection'] = 'Bandi di gara e contratti (dal 1/1/2024)';
            $data['subTitleSection'] = 'GESTIONE DEI BANDI DI GARA E CONTRATTI';
            $data['sectionIcon'] = '<i class="fas fa-clipboard-list fa-3x"></i>';
        }

        //Setto dati del form
        $data['formAction'] = '/admin/bdncp-procedure/store';
        $data['formSettings'] = [
            'name' => 'form_bdncp-procedure',
            'id' => 'form_bdncp-procedure',
            'class' => 'form_bdncp-procedure',
        ];
        $data['_storageType'] = 'insert';
        $data['procedureCat'] = config('config', null, 'bdncp_procedure_config');

        // Labels
        $data['labels'] = [];
        $data['attLabels'] = $data['labels'];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        render('bdncp_procedure/form_store', $data, 'admin');
    }

    /**
     * @description Renderizza il form di creazione di un nuovo Avviso
     *
     * @return void
     * @throws Exception
     * @url /admin/bdncp-procedure/create-alert.html
     * @method GET
     */
    public function createAlert(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $data = [];

        //Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $data['is_box'] = uri()->uriString() === 'admin/bdncp-procedure/create-alert-box';

        //Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {

            $this->breadcrumb->push('Procedure BDNCP', 'admin/bdncp-procedure');
            $this->breadcrumb->push('Nuovo avviso', '/');
            $data['breadcrumbs'] = $this->breadcrumb->show();

            //Dati header della sezione
            $data['titleSection'] = 'Bandi di gara e contratti (dal 1/1/2024)';
            $data['subTitleSection'] = 'GESTIONE DEI BANDI DI GARA E CONTRATTI';
            $data['sectionIcon'] = '<i class="fas fa-clipboard-list fa-3x"></i>';
        }

        //Setto dati del form
        $data['formAction'] = '/admin/bdncp-procedure/store-alert';
        $data['formSettings'] = [
            'name' => 'form_bdncp-procedure',
            'id' => 'form_bdncp-procedure',
            'class' => 'form_bdncp-procedure',
        ];
        $data['_storageType'] = 'insert';

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        render('bdncp_procedure/form_store_alert', $data, 'admin');
    }

    /**
     * @description Funzione che effettua lo storage di una nuova soluzione tecnologica
     *
     * @return void
     * @method POST
     * @throws Exception
     * @url /admin/bdncp-procedure/store.html
     */
    public function store(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new BdncpProcedureValidator();
        $check = $validator->check();
        $tmpCat = [];

        if ($check['is_success']) {
            $doAction = true;

            $bdncpProcedureCat = config('config', null, 'bdncp_procedure_config');
            if (!empty($bdncpProcedureCat) && is_array($bdncpProcedureCat)) {
                $tmpCat = array_keys($bdncpProcedureCat);
            }
            $tmpCat [] = '_generalInfo';

            // Validazione upload allegati associati al tasso di assenza
            $attach = new AttachmentArchive();
            $attachValidatorError = false;

            foreach ($tmpCat as $cat) {
                $attachValidator = $attach->validate('attach_files' . $cat, false, false, $cat);

                if (!empty($attachValidator['error'])) {
                    $attachValidatorError = true;
                }
            }

            // Validatore per gli eventuali allegati
            // Se la validazione non va a buon fine setto il messaggio di errore e non eseguo lo storage dell'oggetto
            if ($attachValidatorError) {
                $doAction = false;
                $code = $json->bad();
                $json->set('error_partial_attach', $attach->errorsToString());
            }

            // Se la validazione degli allegati va a buon fine eseguo lo storage dell'oggetto
            if ($doAction) {
                $getIdentity = authPatOs()->getIdentity(['id', 'name']);

                $object = strip_tags(trim((string)Input::post('object', true)));

                $cig = strip_tags(strtoupper(trim(Input::post('cig', true))));

                $bdncpLink = '';
                //Controllo per vedere se almeno uno dei campi delle checkbox è popolato altrimenti metto la checkbox automaticamente a false
                $checkForCheckbox = $this->checkCheckbox();

                $typology = 'procedure';
                $type = 'Procedura';

                $arrayValues = [
                    'owner_id' => $getIdentity['id'],
                    'institution_id' => checkAlternativeInstitutionId(),
                    'object' => $object,
                    'cig' => $cig,
                    'bdncp_link' => !empty(Input::post('bdncp_link')) ? strip_tags(trim(Input::post('bdncp_link', true))) : $bdncpLink,
                    'typology' => $typology,
                    'type' => $type,

                    //Checkbox
                    'public_debate_check' => $checkForCheckbox['publicDebateCheck'] ? setDefaultData((int)(Input::post('public_debate_check', true)), 0, ['', null]) : 0,
                    'notice_documents_check' => $checkForCheckbox['noticeDocumentsCheck'] ? setDefaultData((int)(Input::post('notice_documents_check', true)), 0, ['', null]) : 0,
                    'judging_commission_check' => $checkForCheckbox['judgingCommissionCheck'] ? setDefaultData((int)(Input::post('judging_commission_check', true)), 0, ['', null]) : 0,
                    'equal_opportunities_af_check' => $checkForCheckbox['equalOpportunitiesAfCheck'] ? setDefaultData((int)(Input::post('equal_opportunities_af_check', true)), 0, ['', null]) : 0,
                    'local_public_services_check' => $checkForCheckbox['localPublicServicesCheck'] ? setDefaultData((int)(Input::post('local_public_services_check', true)), 0, ['', null]) : 0,
                    'advisory_board_technical_check' => $checkForCheckbox['advisoryBoardTechnicalCheck'] ? setDefaultData((int)(Input::post('advisory_board_technical_check', true)), 0, ['', null]) : 0,
                    'equal_opportunities_es_check' => $checkForCheckbox['equalOpportunitiesEsCheck'] ? setDefaultData((int)(Input::post('equal_opportunities_es_check', true)), 0, ['', null]) : 0,
                    'free_contract_check' => $checkForCheckbox['freeContractCheck'] ? setDefaultData((int)Input::post('free_contract_check', true), 0, ['', null]) : 0,
                    'emergency_foster_check' => $checkForCheckbox['emergencyFosterCheck'] ? setDefaultData((int)Input::post('emergency_foster_check', true), 0, ['', null]) : 0,
                    'foster_procedure_check' => $checkForCheckbox['fosterProcedureCheck'] ? setDefaultData((int)Input::post('foster_procedure_check', true), 0, ['', null]) : 0,

                    //Note
                    'public_debate_notes' => setDefaultData(Input::post('public_debate_notes', true), null, ['', null]),
                    'notice_documents_notes' => setDefaultData(Input::post('notice_documents_notes', true), null, ['', null]),
                    'judging_commission_notes' => setDefaultData(Input::post('judging_commission_notes', true), null, ['', null]),
                    'equal_opportunities_af_notes' => setDefaultData(Input::post('equal_opportunities_af_notes', true), null, ['', null]),
                    'local_public_services_notes' => setDefaultData(Input::post('local_public_services_notes', true), null, ['', null]),
                    'advisory_board_technical_notes' => setDefaultData(Input::post('advisory_board_technical_notes', true), null, ['', null]),
                    'equal_opportunities_es_notes' => setDefaultData(Input::post('equal_opportunities_es_notes', true), null, ['', null]),
                    'free_contract_notes' => setDefaultData(Input::post('free_contract_notes', true), null, ['', null]),
                    'emergency_foster_notes' => setDefaultData(Input::post('emergency_foster_notes', true), null, ['', null]),
                    'foster_procedure_notes' => setDefaultData(Input::post('foster_procedure_notes', true), null, ['', null]),
                    'notes' => setDefaultData(Input::post('notes', true), null, ['', null]),
                ];

                // Storage nuova Soluzione Tecnologica
                $insert = BdncpProcedureModel::createWithLogs($arrayValues);

                // Storage nelle tabelle di relazione
                $this->clear(
                    $insert,
                    !empty(Input::post('judging_commission')) ? explode(',', strip_tags((string)Input::post('judging_commission', true))) : null,
                    !empty(Input::post('technical_advisory_board')) ? explode(',', strip_tags((string)Input::post('technical_advisory_board', true))) : null,
                );

                $i = 0;
                foreach ($tmpCat as $cat) {
                    $attach->storage(
                        'attach_files' . $cat,
                        'bdncp_procedure',
                        $insert->id,
                        $insert['object'],
                        '',
                        null,
                        null,
                        false,
                        true,
                        $cat . 'O__O' . $i++
                    );
                }

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
     * @description Funzione che effettua lo storage di un nuovo Avviso
     *
     * @return void
     * @method POST
     * @throws Exception
     * @url /admin/bdncp-procedure/store-alert.html
     */
    public function storeAlert(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new BdncpProcedureValidator();
        $check = $validator->check('insert', 'alert');

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

                $object = strip_tags(trim((string)Input::post('object', true)));
                $typology = 'alert';
                $type = 'Avviso';

                $arrayValues = [
                    'owner_id' => $getIdentity['id'],
                    'institution_id' => checkAlternativeInstitutionId(),
                    'object' => $object,
                    'typology' => $typology,
                    'type' => $type,
                    'object_procedure_id' => setDefaultData(strip_tags(Input::post('object_bdncp_procedure_id', true)), null, ['']),
                    'alert_date' => !empty(Input::post('alert_date')) ? convertDateToDatabase(strip_tags(Input::post('alert_date', true))) : null,
                    'notes' => setDefaultData(Input::post('notes', true), null, ['', null]),
                ];

                // Storage nuova Soluzione Tecnologica
                $insert = BdncpProcedureModel::createWithLogs($arrayValues);

                // Storage allegati associati al personale.
                $attach->storage('attach_files', 'bdncp_procedure', $insert->id, $insert['object']);

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
     * @description Renderizza il form di modifica/duplicazione di una Procedura BDNCP
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/bdncp-procedure/edit.html
     * @method GET
     */
    public function edit(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['update', 'create']);

        //Validatore che verifica se l'elemento da modificare esiste
        $validator = new BdncpProcedureValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        if (!$validate['is_success']) {
            redirect('admin/bdncp-procedure', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Allegati
        $attach = new AttachmentArchive();

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $procedure = Registry::get('bdncp-procedure');
        $procedure = !empty($procedure) ? $procedure->toArray() : [];

        $this->breadcrumb->push('Procedure BDNCP', 'admin/bdncp-procedure');
        $this->breadcrumb->push('Modifica', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Bandi di gara e contratti (dal 1/1/2024)';
        $data['subTitleSection'] = 'GESTIONE DEI BANDI DI GARA E CONTRATTI';
        $data['sectionIcon'] = '<i class="fas fa-clipboard-list fa-3x"></i>';

        // Vedo in sessione se sono arrivato qui da un operazione di ripristino dal versioning
        $session = new Session();
        $restore = $session->getFlash('restore');
        $data['restore'] = $restore;

        // Controllo se si sta duplicando un tasso di assenza
        $isDuplicate = uri()->segment(3, 0) === 'duplicate';

        // Setto il tipo di storage
        $data['_storageType'] = $isDuplicate ? 'duplicate' : 'update';

        //In base al tipo di storage setto l'action del form
        $data['formAction'] = ($isDuplicate) ? '/admin/bdncp-procedure/store' : '/admin/bdncp-procedure/update';
        $data['formSettings'] = [
            'name' => 'form_bdncp_procedure',
            'id' => 'form_bdncp_procedure',
            'class' => 'form_bdncp_procedure',
        ];

        $data['procedure'] = $procedure;

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'bdncp_procedure',
            $procedure['id']
        );

        $tmpAttachs = [];

        foreach ($data['listAttach'] as $attach) {
            $tmpAttachs[$attach['bdncp_cat']][] = $attach;
        }

        $data['attachs'] = $tmpAttachs;

        // Labels
        $data['labels'] = [];
        $data['attLabels'] = $data['labels'];

        $data['procedureCat'] = config('config', null, 'bdncp_procedure_config');

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = $procedure['institution_id'];

        render('bdncp_procedure/form_store', $data, 'admin');
    }

    /**
     * @description Renderizza il form di modifica/duplicazione di una Procedura BDNCP
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/bdncp-procedure/edit-alert.html
     * @method GET
     */
    public function editAlert(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['update', 'create']);

        //Validatore che verifica se l'elemento da modificare esiste
        $validator = new BdncpProcedureValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        if (!$validate['is_success']) {
            redirect('admin/bdncp-procedure', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Allegati
        $attach = new AttachmentArchive();

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $alert = Registry::get('bdncp-procedure');
        $alert = !empty($alert) ? $alert->toArray() : [];

        $this->breadcrumb->push('Procedure BDNCP', 'admin/bdncp-procedure');
        $this->breadcrumb->push('Modifica avviso', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Bandi di gara e contratti (dal 1/1/2024)';
        $data['subTitleSection'] = 'GESTIONE DEI BANDI DI GARA E CONTRATTI';
        $data['sectionIcon'] = '<i class="fas fa-clipboard-list fa-3x"></i>';

        // Vedo in sessione se sono arrivato qui da un operazione di ripristino dal versioning
        $session = new Session();
        $restore = $session->getFlash('restore');
        $data['restore'] = $restore;

        // Controllo se si sta duplicando un tasso di assenza
        $isDuplicate = uri()->segment(3, 0) === 'duplicate-alert';

        // Setto il tipo di storage
        $data['_storageType'] = $isDuplicate ? 'duplicate' : 'update';

        //In base al tipo di storage setto l'action del form
        $data['formAction'] = ($isDuplicate) ? '/admin/bdncp-procedure/store-alert' : '/admin/bdncp-procedure/update-alert';
        $data['formSettings'] = [
            'name' => 'form_bdncp_procedure',
            'id' => 'form_bdncp_procedure',
            'class' => 'form_bdncp_procedure',
        ];

        $alertDate = convertDateToForm($alert['alert_date']);
        $alert['alert_date'] = $alertDate['date'];
        $data['alert'] = $alert;

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'bdncp_procedure',
            $alert['id']
        );

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = $alert['institution_id'];

        render('bdncp_procedure/form_store_alert', $data, 'admin');
    }

    /**
     * @description Funzione che effettua l'update di una procedura
     *
     * @return void
     * @throws Exception
     * @url /admin/bdncp-procedure/update.html
     * @method POST
     */
    public function update(): void
    {
        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new BdncpProcedureValidator();
        $check = $validator->check('update');

        if ($check['is_success']) {
            $doAction = true;

            $procedureId = (int)strip_tags(Input::post('id', true));

            // Recupero l'atto attuale prima di modificarlo e lo salvo nel versioning
            $procedure = BdncpProcedureModel::where('id', $procedureId)
                ->with('commission:id,assignment_start,name,object')
                ->with('board:id,assignment_start,name,object')
                ->with('relative_bdncp_procedure:id,object')
                ->with('all_attachs');

            $procedure = $procedure->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute(__FUNCTION__, (!checkRecordOwner($procedure['owner_id']) && $this->acl->getCreate()));

            $tmpCat = [];
            $bdncpProcedureCat = config('config', null, 'bdncp_procedure_config');
            if (!empty($bdncpProcedureCat) && is_array($bdncpProcedureCat)) {
                $tmpCat = array_keys($bdncpProcedureCat);
            }
            $tmpCat [] = '_generalInfo';

            // Validazione upload allegati associati al tasso di assenza
            $attach = new AttachmentArchive();
            $attachValidatorError = false;

            foreach ($tmpCat as $cat) {
                $attachValidator = $attach->validate('attach_files' . $cat, false, false, $cat);

                if (!empty($attachValidator['error'])) {
                    $attachValidatorError = true;
                }
            }

            // Validatore per gli eventuali allegati
            // Se la validazione non va a buon fine setto il messaggio di errore e non eseguo lo storage dell'oggetto
            if ($attachValidatorError) {
                $doAction = false;
                $code = $json->bad();
                $json->set('error_partial_attach', $attach->errorsToString());
            }

            $cig = !empty(Input::post('cig')) ? strtoupper(strip_tags(trim((string)Input::post('cig', true)))) : null;
            $bdncpLink = '';

            // Se la validazione degli allegati va a buon fine eseguo l'update dell'oggetto
            if ($doAction) {
                $data = [];
                $data['object'] = !empty(Input::post('object')) ? strip_tags(trim((string)Input::post('object', true))) : null;
                $data['cig'] = $cig;
                $data['bdncp_link'] = !empty(Input::post('bdncp_link')) ? strip_tags(trim((string)Input::post('bdncp_link', true))) : $bdncpLink;

                //Controllo per vedere se almeno uno dei campi delle checkbox è popolato altrimenti metto la checkbox automaticamente a false
                $checkForCheckbox = $this->checkCheckbox();

                //Checkbox
                $data['public_debate_check'] = $checkForCheckbox['publicDebateCheck'] ? setDefaultData(strip_tags(Input::post('public_debate_check', true)), 0, ['', null]) : 0;
                $data['notice_documents_check'] = $checkForCheckbox['noticeDocumentsCheck'] ? setDefaultData(strip_tags(Input::post('notice_documents_check', true)), 0, ['', null]) : 0;
                $data['judging_commission_check'] = $checkForCheckbox['judgingCommissionCheck'] ? setDefaultData(strip_tags(Input::post('judging_commission_check', true)), 0, ['', null]) : 0;
                $data['equal_opportunities_af_check'] = $checkForCheckbox['equalOpportunitiesAfCheck'] ? setDefaultData(strip_tags(Input::post('equal_opportunities_af_check', true)), 0, ['', null]) : 0;
                $data['local_public_services_check'] = $checkForCheckbox['localPublicServicesCheck'] ? setDefaultData(strip_tags(Input::post('local_public_services_check', true)), 0, ['', null]) : 0;
                $data['advisory_board_technical_check'] = $checkForCheckbox['advisoryBoardTechnicalCheck'] ? setDefaultData(strip_tags(Input::post('advisory_board_technical_check', true)), 0, ['', null]) : 0;
                $data['equal_opportunities_es_check'] = $checkForCheckbox['equalOpportunitiesEsCheck'] ? setDefaultData(strip_tags(Input::post('equal_opportunities_es_check', true)), 0, ['', null]) : 0;
                $data['free_contract_check'] = $checkForCheckbox['freeContractCheck'] ? setDefaultData(strip_tags(Input::post('free_contract_check', true)), 0, ['', null]) : 0;
                $data['emergency_foster_check'] = $checkForCheckbox['emergencyFosterCheck'] ? setDefaultData(strip_tags(Input::post('emergency_foster_check', true)), 0, ['', null]) : 0;
                $data['foster_procedure_check'] = $checkForCheckbox['fosterProcedureCheck'] ? setDefaultData(strip_tags(Input::post('foster_procedure_check', true)), 0, ['', null]) : 0;

                //Note
                $data['public_debate_notes'] = setDefaultData(Input::post('public_debate_notes', true), null, ['', null]);
                $data['notice_documents_notes'] = setDefaultData(Input::post('notice_documents_notes', true), null, ['', null]);
                $data['judging_commission_notes'] = setDefaultData(Input::post('judging_commission_notes', true), null, ['', null]);
                $data['equal_opportunities_af_notes'] = setDefaultData(Input::post('equal_opportunities_af_notes', true), null, ['', null]);
                $data['local_public_services_notes'] = setDefaultData(Input::post('local_public_services_notes', true), null, ['', null]);
                $data['advisory_board_technical_notes'] = setDefaultData(Input::post('advisory_board_technical_notes', true), null, ['', null]);
                $data['equal_opportunities_es_notes'] = setDefaultData(Input::post('equal_opportunities_es_notes', true), null, ['', null]);
                $data['free_contract_notes'] = setDefaultData(Input::post('free_contract_notes', true), null, ['', null]);
                $data['emergency_foster_notes'] = setDefaultData(Input::post('emergency_foster_notes', true), null, ['', null]);
                $data['foster_procedure_notes'] = setDefaultData(Input::post('foster_procedure_notes', true), null, ['', null]);
                $data['notes'] = setDefaultData(Input::post('notes', true), null, ['', null]);

                BdncpProcedureModel::where('id', $procedureId)->updateWithLogs($procedure, $data);

                // Svuoto le tabelle di relazione e le aggiorno
                $this->clear(
                    $procedure,
                    !empty(Input::post('judging_commission')) ? explode(',', strip_tags((string)Input::post('judging_commission', true))) : null,
                    !empty(Input::post('technical_advisory_board')) ? explode(',', strip_tags((string)Input::post('technical_advisory_board', true))) : null,
                );

                $i = 0;
                foreach ($tmpCat as $cat) {
                    $attach->update(
                        'attach_files' . $cat,
                        'bdncp_procedure',
                        $procedureId,
                        $procedure['institution_id'],
                        $procedure['object'],
                        '',
                        null,
                        false,
                        $cat . 'O__O' . $i++
                    );
                }

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
     * @description Funzione che effettua l'update di una procedura
     *
     * @return void
     * @throws Exception
     * @url /admin/bdncp-procedure/update-alert.html
     * @method POST
     */
    public function updateAlert(): void
    {
        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new BdncpProcedureValidator();
        $check = $validator->check('update', 'alert');

        if ($check['is_success']) {
            $doAction = true;

            $alertId = (int)strip_tags(Input::post('id', true));

            // Recupero l'atto attuale prima di modificarlo e lo salvo nel versioning
            $alert = BdncpProcedureModel::where('id', $alertId)
                ->with('relative_bdncp_procedure:id,object,cig')
                ->with('all_attachs');

            $alert = $alert->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute('update', (!checkRecordOwner($alert['owner_id']) && $this->acl->getCreate()));

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
                $data['object'] = !empty(Input::post('object')) ? strip_tags(trim((string)Input::post('object', true))) : null;
                $data['object_procedure_id'] = setDefaultData(strip_tags(Input::post('object_bdncp_procedure_id', true)), null, ['']);
                $data['alert_date'] = !empty(Input::post('alert_date')) ? convertDateToDatabase(strip_tags(Input::post('alert_date', true))) : null;
                $data['notes'] = setDefaultData(Input::post('notes', true), null, ['', null]);

                BdncpProcedureModel::where('id', $alertId)->updateWithLogs($alert, $data);

                // Upload allegati associati al personale.
                $attach->update(
                    'attach_files',
                    'bdncp_procedure',
                    $alertId,
                    $alert['institution_id'],
                    $alert['object']
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
     * @description Funzione che effettua l'eliminazione di una Procedura
     *
     * @return void
     * @throws Exception
     * @url /admin/bdncp-procedure/delete.html
     * @method GET
     */
    public function delete(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute([__FUNCTION__, 'create', 'update'], true);

        //Validatore che verifica se l'elemento da eliminare esiste
        $validator = new BdncpProcedureValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getDelete() && $this->acl->getUpdate());

        if (!$validate['is_success']) {
            redirect('admin/notices-act', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Se la validazione va a buon fine recupero l'elemento da eliminare dal registro
        $procedure = Registry::get('bdncp-procedure');

        // Elimino l'atto settando deleted = 1
        $procedure->deleteWithLogs($procedure);

        sessionSetNotify(__('success_delete_operation', null, 'patos'));

        // Generazione nuovo token
        Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        redirect('admin/bdncp-procedure');
    }

    /**
     * @description Funzione per l'eliminazione multipla
     *
     * @return void
     * @throws Exception
     * @url /admin/bdncp-procedure/deletes.html
     * @method GET
     */
    public function deletes(): void
    {
        // Setto il metodo della rotta per i permessi
        $this->acl->setRoute('delete');

        $validator = new BdncpProcedureValidator();
        $validate = $validator->multipleSelection();

        if ($validate['is_success']) {

            sessionSetNotify('Operazione avvenuta con successo');

            //Recupero gli elementi da eliminare dal registro
            $procedures = Registry::get('__ids__multi_select_profile__');

            //Estraggo gli array degli elementi da eliminare
            $ids = Arr::pluck($procedures, 'id');

            //Elimino gli elementi
            foreach ($procedures as $procedure) {
                $procedure->deleteWithLogs($procedure);
            }

            // Generazione nuovo token
            Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        } else {

            sessionSetNotify($validate['errors'], 'danger');
        }

        redirect('/admin/bdncp-procedure');
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
        $fileName = 'Procedure di gara.csv';

        setFileDownloadCookieCSV($fileName);

        $output = fopen("php://output", 'w');

        $records = BdncpProcedureModel::with('all_attachs')
            ->with('created_by:id,name')
            ->with('commission:id,assignment_start,name,object')
            ->with('board:id,assignment_start,name,object')
            ->orderBy('created_at', 'DESC')
            ->get()
            ->toArray();

        if (!empty($records)) {

            //Setto le colonne
            $headers = array(
                'ID',
                'TIPOLOGIA',
                'CIG',
                'OGGETTO',
                'LINK BDNCP',
                'DATA AVVISO',
                'NOTE DIBATTITO PUBBLICO',
                'ALLEGATI DIBATTITO PUBBLICO',
                'NOTE DOCUMENTI DI GARA',
                'ALLEGATI DOCUMENTI DI GARA',
                'NOTE COMPOSIZIONE DELLE COMMISSIONI GIUDICATRICI',
                'COMPONENTI DELLE COMMISSIONI GIUDICATRICI',
                'NOTE PARI OPPORTUNITA',
                'ALLEGATI PARI OPPORTUNITA',
                'NOTE PROCEDURE DI AFFIDAMENTO DEI SERVIZI PUBBLICI LOCALI',
                'ALLEGATI PROCEDURE DI AFFIDAMENTO DEI SERVIZI PUBBLICI LOCALI',
                'NOTE COMPOSIZIONE COLLEGIO CONSULTIVO TECNICO',
                'COMPONENTI DEL COMPOSIZIONE COLLEGIO CONSULTIVO TECNICO',
                'NOTE PARI OPPORTUNITA',
                'ALLEGATI PARI OPPORTUNITA',
                'NOTE CONTRATTI GRATUITI E FORME SPECIALI DI PARENTARIATO',
                'ALLEGATI CONTRATTI GRATUITI E FORME SPECIALI DI PARENTARIATO',
                'NOTE AFFIDAMENTI DI SOMMA URGENZA',
                'ALLEGATI AFFIDAMENTI DI SOMMA URGENZA',
                'NOTE PROCEDURA DI AFFIDAMENTO',
                'ALLEGATI PROCEDURA DI AFFIDAMENTO',
                'NOTE',
                'ALLEGATI',
                'CREATO DA',
                'ULTIMA MODIFICA',
            );
            fputcsv($output, $headers, ';', '"');

            foreach ($records as $record) {

                $alertDate = !empty($record['alert_date']) ? (date('d-m-Y', strtotime($record['alert_date']))) : '';

                $attachLists = [];
                if (!empty($record['all_attachs'])) {

                    foreach ($record['all_attachs'] as $attach) {
                        if (empty($attach['bdncp_cat'])) {
                            $attach['bdncp_cat'] = '_generalInfo';
                        }
                        $attachLists [$attach['bdncp_cat']] [] = '(' . $attach['label'] . ') - Url: '
                            . siteUrl('/download/' . $attach['id']) . "\n";

                    }
                }

                // Concateno gli incarichi
                $commissions = '';
                if (!empty($record['commission'])) {
                    foreach ($record['commission'] as $commission) {

                        $commissions .= $commission['name'] . ' - [Url]: '
                            . siteUrl('page/46/details/' . $commission['id'] . '/' . urlTitle($commission['name'])) . "\n";

                    }
                }

                // Concateno gli incarichi
                $boards = '';
                if (!empty($record['board'])) {
                    foreach ($record['board'] as $board) {

                        $boards .= $board['name'] . ' - [Url]: '
                            . siteUrl('page/46/details/' . $board['id'] . '/' . urlTitle($board['name'])) . "\n";

                    }
                }

                $updateAt = !empty($record['updated_at']) ? date('d-m-Y H:i:s', strtotime($record['updated_at'])) : null;

                //Setto i dati da inserire nelle colonne del CSV
                $data = [
                    !empty($record['id']) ? $record['id'] : null,
                    !empty($record['type']) ? $record['type'] : null,
                    !empty($record['cig']) ? (string)$record['cig'] . ' ' : null,
                    !empty($record['object']) ? $record['object'] : null,
                    !empty($record['bdncp_link']) ? $record['bdncp_link'] : null,
                    $alertDate,
                    !empty($record['public_debate_notes']) ? $record['public_debate_notes'] : null,
                    !empty($attachLists['_publicDebate']) ? trim(implode('', $attachLists['_publicDebate'])) : null,
                    !empty($record['notice_documents_notes']) ? $record['notice_documents_notes'] : null,
                    !empty($attachLists['_noticeDocuments']) ? trim(implode('', $attachLists['_noticeDocuments'])) : null,
                    !empty($record['judging_commission_notes']) ? $record['judging_commission_notes'] : null,
                    trim($commissions),
                    !empty($record['equal_opportunities_af_notes']) ? $record['equal_opportunities_af_notes'] : null,
                    !empty($attachLists['_equalOpportunitiesAf']) ? trim(implode('', $attachLists['_equalOpportunitiesAf'])) : null,
                    !empty($record['local_public_services_notes']) ? $record['local_public_services_notes'] : null,
                    !empty($attachLists['_localPublicServices']) ? trim(implode('', $attachLists['_localPublicServices'])) : null,
                    !empty($record['advisory_board_technical_notes']) ? $record['advisory_board_technical_notes'] : null,
                    trim($boards),
                    !empty($record['equal_opportunities_es_notes']) ? $record['equal_opportunities_es_notes'] : null,
                    !empty($attachLists['_equalOpportunitiesEs']) ? trim(implode('', $attachLists['_equalOpportunitiesEs'])) : null,
                    !empty($record['free_contract_notes']) ? $record['free_contract_notes'] : null,
                    !empty($attachLists['_freeContract']) ? trim(implode('', $attachLists['_freeContract'])) : null,
                    !empty($record['emergency_foster_notes']) ? $record['emergency_foster_notes'] : null,
                    !empty($attachLists['_emergencyFoster']) ? trim(implode('', $attachLists['_emergencyFoster'])) : null,
                    !empty($record['foster_procedure_notes']) ? $record['foster_procedure_notes'] : null,
                    !empty($attachLists['_fosterProcedure']) ? trim(implode('', $attachLists['_fosterProcedure'])) : null,
                    !empty($record['notes']) ? $record['notes'] : null,
                    !empty($attachLists['_generalInfo']) ? trim(implode('', $attachLists['_generalInfo'])) : null,
                    !empty($record['created_by']['name']) ? checkDecrypt($record['created_by']['name']) : null,
                    $updateAt,
                ];

                fputcsv($output, $data, ';', '"');
            }
            fclose($output);

            // Storage Activity log
            ActivityLog::create([
                'action' => 'Esportazione dati dei Bandi di gara e contratti (dal 1/1/2024) in CSV',
                'action_type' => 'exportCSV',
                'description' => 'Esportazione dati dei Bandi di gara e contratti (dal 1/1/2024) in CSV',
                'request_post' => [
                    'post' => @$_POST,
                    'get' => Input::get(),
                    'server' => Input::server(),
                ],
                'object_id' => 91,
                'area' => 'object'
            ]);

            exit();
        } else {

            //Se non sono presenti tassi di assenza nel db mostro una notifica con il messaggio e faccio il redirect
            sessionSetNotify(
                sprintf(__('error_export_csv_', null, 'patos'), 'bando di gara'),
                'warning'
            );

            redirect('admin/bdncp-procedure');
        }
    }

    /**
     * @description Metodo per lo storage nelle tabelle di relazione
     *
     * @param BdncpProcedureModel|null $procedure Procedura per cui inserire i dati di relazione
     * @param array|int|null $judgingCommission Incarichi che compongono la commissione giudicatrice
     * @param array|int|null $technicalAdvisoryBoard Incarichi che compongono il Collegio consultivo tecnico
     * @return void
     */
    protected function clear(BdncpProcedureModel $procedure = null, array|int $judgingCommission = null, array|int $technicalAdvisoryBoard = null): void
    {
        $dataAssignments = [];
        if ($judgingCommission !== null) {
            foreach ($judgingCommission as $assignment) {
                $dataAssignments[] = is_array($assignment) ? $assignment['id'] : $assignment;
            }
        }
        //Insert/Update nella tabella di relazione
        $procedure->commission()->syncWithPivotValues($dataAssignments, ['typology' => 'commission']);

        $dataAssignments = [];
        if ($technicalAdvisoryBoard !== null) {
            foreach ($technicalAdvisoryBoard as $assignment) {
                $dataAssignments[] = is_array($assignment) ? $assignment['id'] : $assignment;
            }
        }
        //Insert/Update nella tabella di relazione
        $procedure->board()->syncWithPivotValues($dataAssignments, ['typology' => 'board']);

    }

    private function checkCheckbox()
    {

        return [
            'publicDebateCheck' => (!empty(Input::post('attach_id_publicDebate'))) || !empty(trim((string)Input::post('public_debate_notes'))),
            'noticeDocumentsCheck' => (!empty(Input::post('attach_id_noticeDocuments'))) || !empty(trim((string)Input::post('notice_documents_notes'))),
            'judgingCommissionCheck' => (!empty(Input::post('judging_commission'))) || !empty(trim((string)Input::post('judging_commission_notes'))),
            'equalOpportunitiesAfCheck' => (!empty(Input::post('attach_id_equalOpportunitiesAf'))) || !empty(trim((string)Input::post('equal_opportunities_af_notes'))),
            'localPublicServicesCheck' => (!empty(Input::post('attach_id_localPublicServices'))) || !empty(trim((string)Input::post('local_public_services_notes'))),
            'advisoryBoardTechnicalCheck' => (!empty(Input::post('technical_advisory_board'))) || !empty(trim((string)Input::post('public_debate_notes'))),
            'equalOpportunitiesEsCheck' => (!empty(Input::post('attach_id_equalOpportunitiesEs'))) || !empty(trim((string)Input::post('equal_opportunities_es_notes'))),
            'freeContractCheck' => (!empty(Input::post('attach_id_freeContract'))) || !empty(trim((string)Input::post('free_contract_notes'))),
            'emergencyFosterCheck' => (!empty(Input::post('attach_id_emergencyFoster'))) || !empty(trim((string)Input::post('emergency_foster_notes'))),
            'fosterProcedureCheck' => (!empty(Input::post('attach_id_fosterProcedure'))) || !empty(trim((string)Input::post('foster_procedure_notes'))),
        ];
    }
}
