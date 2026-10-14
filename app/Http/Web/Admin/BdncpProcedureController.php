<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Admin;

use Exception;
use Helpers\Utility\AttachmentArchive;
use Helpers\Utility\ButtonAction;
use Helpers\Validators\BdncpProcedureValidator;
use Helpers\Validators\DatatableValidator;
use Model\BdncpProcedureModel;
use Model\UsersModel;
use Scope\DeletedScope;
use System\Arr;
use System\Input;
use System\JsonResponse;
use System\Log;
use System\Registry;
use System\Session;

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

        $data['include'] = '';





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


            //Setto proprietà datatable
            $dataTable = [];
            setDataTableData($dataTable, 'updated_at');



            // Query per i dati da mostrare nel datatable
            $totalRecords = BdncpProcedureModel::select('count(id) as allcount')
                ->count();

            $totalRecordsWithFilter = BdncpProcedureModel::search($dataTable['searchValue'])
                ->select('count(object_bdncp_procedure.id) as allcount')
                ->join('users', 'users.id', '=', 'object_bdncp_procedure.owner_id', 'left outer')
                ->BdncpProcedureFilterDataTable();

            if (!empty($dataTable['searchValue'])) {
                $totalRecordsWithFilter->orWhere('object_bdncp_procedure.id', '=', $dataTable['searchValue']);
            }
            // Inclusione di librerie esterne
            $totalRecordsWithFilter = $totalRecordsWithFilter->count();

            $order = setOrderDatatable($dataTable['columnName'], $orderable, 'updated_at');

            $selectFields = [
                'object_bdncp_procedure.id', 'object_bdncp_procedure.owner_id', 'object_bdncp_procedure.amount_liquidated', 'object_bdncp_procedure.institution_id', 'object_bdncp_procedure.object',
                'object_bdncp_procedure.updated_at', 'bdncp_link', 'object_bdncp_procedure.cig', 'object_bdncp_procedure.type', 'object_bdncp_procedure.multicig', 'object_bdncp_procedure.typology',
                'users.name as userName', 'i.full_name_institution'
            ];


            $records = BdncpProcedureModel::search($dataTable['searchValue'])
                ->select($selectFields)
                ->join('users', 'users.id', '=', 'object_bdncp_procedure.owner_id', 'left outer')
                ->BdncpProcedureFilterDataTable()
                ->with(['created_by' => function ($query) {
                    $query->withoutGlobalScopes([DeletedScope::class]);
                    $query->select(['id', 'name', 'deleted']);
                }])
                ->with(['institution' => function ($query) {
                    $query->select(['id', 'full_name_institution']);
                }])
                ->with('all_attachs:id,archive_id,bdncp_cat');

            $records = $records ?? null;


            if (!empty($dataTable['searchValue'])) {
                $records->orWhere('object_bdncp_procedure.id', '=', $dataTable['searchValue']);
            }

            // Inclusione di librerie esterne
            $records = $records ?? null;
            $records = optional($records->join('institutions as i', 'object_bdncp_procedure.institution_id', '=', 'i.id', 'left outer')
                ->orderBy($order, $dataTable['columnSortOrder'])
                ->offset($dataTable['start'])
                ->limit($dataTable['rowPerPage'])
                ->get())->toArray();

            $response['draw'] = intval($dataTable['draw']);
            $response['iTotalRecords'] = ($totalRecords);
            $response['iTotalDisplayRecords'] = ($totalRecordsWithFilter);
            $response['aaData'] = [];

            if (!empty($records)) {

                $checkWf = false;
                $manageableStates = [];
                $getIdentity = authPatOs()->getIdentity(['id']);

                foreach ($records as $record) {

                    if (!empty($record['updated_at'])) {
                        $updateAt = '<small class="badge badge-info"> 
                                    <i class="far fa-clock"></i> '
                            . date('d-m-Y H:i:s', strtotime($record['updated_at'])) . '</small>';
                    } else {
                        $updateAt = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definita">N.D.</small>';
                    }

                    // Controllo se l'utente ha i permessi di modifica dei record o di scrittura (e quindi di modifica dei propri record)
                    $permits = ($this->acl->getCreate() && checkRecordOwner($record['owner_id'], $getIdentity['id']));
                    $updatePermits = ($this->acl->getUpdate() && checkRecordOwner($record['owner_id'], $getIdentity['id']));
                    $wfPermits = false;
                    $workflowState = $record['workflow_state'] ?? 'finale';
                    $wfOwnerPermits = $permits && $workflowState == 'iniziale';

                    $ecPermits = true;

                    $deletePermits = $this->acl->getDelete() || $updatePermits || $permits;

                    $record = $record ?? null;

                    $acPermits = true;

                    //Setto i pulsanti delle actions da mostrare in base ai permessi dell'utente, se non ha i permessi non li setto
                    $buttonAction = ($this->acl->getUpdate() || $this->acl->getDelete() || $permits) ? ButtonAction::create([
                        'edit' => ($checkWf ? (($this->acl->getUpdate() && $wfPermits) || $wfOwnerPermits) : $this->acl->getUpdate() || $permits) && $acPermits,
                        'duplicate' => $this->acl->getCreate() && $workflowState == 'finale',
                        'versioning' => getAclVersioning(),
                        'delete' => ($checkWf ? (($this->acl->getDelete() && $wfPermits) || $wfOwnerPermits) : $deletePermits) && $acPermits,
                    ])
                        ->addEdit('admin/bdncp-procedure/edit' . ($record['typology'] === 'procedure' ? '' : '-' . $record['typology']) . '/' . $record['id'], $record['id'], '', true)
                        ->addDuplicate('admin/bdncp-procedure/duplicate' . ($record['typology'] === 'procedure' ? '' : '-' . $record['typology']) . '/' . $record['id'], $record['id'])
                        ->addCustomButton('b_a_v', siteUrl('admin/bdncp-procedure/versioning'), $record['id'], '', '', 'versioning')
                        ->addCustomButton('s_r_l', siteUrl('admin/bdncp-procedure/show-record-log/' . $record['id']), $record['id'], '91', 'show-record-log', 'view')
                        ->addCustomButton('btn_c_p_a', siteUrl('admin/measure/create?cpa=' . $record['id'] . '&t=1'), $record['id'], $record['typology'], 'fa-copy', 'edit')
                        ->addCustomButton('wf_a_f_e', "", $record['id'], $workflowState, '', 'view')
                        ->addCustomButton(
                            's_no_app_io',
                            '#!',
                            $record['id'],
                            'bdncp',
                            'Invia notifica App IO',
                            'view'
                        )
                        ->addDelete('admin/bdncp-procedure/delete/' . $record['id'], $record['id'])
                        ->render() : '';

                    $icon = null;

                    //Inclusione librerie esterne

                    //Setto i dati da mostrare nelle colonne del datatable
                    $setTempData = [];

                    $checkBox = ($this->acl->getCrud() || checkRecordOwner($record['owner_id'])) && $acPermits ? ButtonAction::checkList('item[]', $record['id']) : '';

                    $checkBoxEc = '';


                    $setTempData[] = $checkBox . $checkBoxEc;
                    $setTempData[] = !empty($record['type']) ? ucfirst($record['type']) : '';

                    $txtColonnaCig = !empty($record['cig']) ? $record['cig'] : '';

                    if (!empty($record['cig']) && $record['cig'] != '0000000000') {

                        if (!empty($record['bdncp_link'])) {
                            $txtColonnaCig = '<a href="' . escapeXss($record['bdncp_link']) . '"   target="_blank"   rel="noopener noreferrer"   class="font-weight-bold text-primary text-decoration-none"   title="Apri il CIG su BDNCP">    ' . escapeXss($record['cig']) . '    <i class="fas fa-external-link-alt ml-1" style="font-size:0.8em;"></i></a>';
                        } else {
                            $txtColonnaCig = '<span class="text-muted font-weight-bold">    ' . escapeXss($record['cig']) . '</span>';
                        }
                    }

                    $setTempData[] = $txtColonnaCig;

                    $setTempData[] = $icon . (!empty($record['object'])
                            ? '<a href="' . siteUrl('/page/10/details/' . $record['id'] . '/' . urlTitle(escapeXss($record['object']))) . '" target="_blank">' . escapeXss($record['object']) . '</a>'
                            : 'N.D.');



                    $record['created_by'] = $record['created_by'] ?? null;

                    $setTempData[] = createdByCheckDeleted($record['created_by']['name'] ?? null, @$record['created_by']['deleted'] ?? 0);
                    $setTempData[] = $updateAt;

                    //Ente
                    $record = $record ?? null;
                    
                    $setTempData[] = $this->attachAnchorMenu($record);

                    $setTempData[] = $buttonAction;

                    $data[] = $setTempData;
                }

                $response['aaData'] = $data;
            }
            jsonOut($response);
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
        $data['is_box'] = uri()->uriString() === verifyFirstOrLastSlash('admin/bdncp-procedure/create-box', true);

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

        // ID dell'ente corrente.
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
        $data['is_box'] = uri()->uriString() === verifyFirstOrLastSlash('admin/bdncp-procedure/create-alert-box', true);

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


        // ID dell'ente corrente.
        $data['institution_id'] = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();


        render('bdncp_procedure/form_store_alert', $data, 'admin');
    }


    /**
     * @description Renderizza il form di creazione di un nuovo Avviso
     *
     * @return void
     * @throws Exception
     * @url /admin/bdncp-procedure/create-alert.html
     * @method GET
     */
    public function createVariation(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $data = [];

        //Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $data['is_box'] = uri()->uriString() === verifyFirstOrLastSlash('admin/bdncp-procedure/create-variation-box', true);

        //Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {

            $this->breadcrumb->push('Procedure BDNCP', 'admin/bdncp-procedure');
            $this->breadcrumb->push('Nuova variante', '/');
            $data['breadcrumbs'] = $this->breadcrumb->show();

            //Dati header della sezione
            $data['titleSection'] = 'Bandi di gara e contratti (dal 1/1/2024)';
            $data['subTitleSection'] = 'GESTIONE DEI BANDI DI GARA E CONTRATTI';
            $data['sectionIcon'] = '<i class="fas fa-clipboard-list fa-3x"></i>';
        }

        //Setto dati del form
        $data['formAction'] = '/admin/bdncp-procedure/store-variation';
        $data['formSettings'] = [
            'name' => 'form_bdncp-procedure',
            'id' => 'form_bdncp-procedure',
            'class' => 'form_bdncp-procedure',
        ];
        $data['_storageType'] = 'insert';

        // Labels
        $data['labels'] = [];
        $data['labels'] = @$data['labels']['variation'];


        // ID dell'ente corrente.
        $data['institution_id'] = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();


        render('bdncp_procedure/form_store_variation', $data, 'admin');
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
        $this->acl->setRoute('create', true);

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
                $attachValidator = $attach->validate('attach_files' . $cat, $cat);

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
                $eprocUrl = null;

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
                    'eproc_url' => !empty(Input::post('eproc_url')) ? strip_tags(trim(Input::post('eproc_url', true))) : $eprocUrl,
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
                        true,
                        $cat . 'O__O' . $i++
                    );
                }



                $json->set('message', __('success_save_operation', null, 'patos'));

//                if (!referenceOriginForRegenerateToken(3, 'create-box')) {
//                    // Generazione nuovo token
//                    Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
//                }
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
        $this->acl->setRoute('create', true);

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

//                if (!referenceOriginForRegenerateToken(3, 'create-box')) {
//                    // Generazione nuovo token
//                    Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
//                }
            }

        } else {

            $code = $json->bad();
            $json->error('error', $check['errors']);
        }

        $json->setStatusCode($code);
        $json->response();
    }


    /**
     * @description Funzione che effettua lo storage di una nuova Variante
     *
     * @return void
     * @method POST
     * @throws Exception
     * @url /admin/bdncp-procedure/store-variation.html
     */
    public function storeVariation(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create', true);

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new BdncpProcedureValidator();
        $check = $validator->check('insert', 'variation');

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
                $typology = 'variation';
                $type = "Variante in corso d'opera";

                $arrayValues = [
                    'owner_id' => $getIdentity['id'],
                    'institution_id' => checkAlternativeInstitutionId(),
                    'object' => $object,
                    'typology' => $typology,
                    'type' => $type,
                    'notes' => setDefaultData(Input::post('notes', true), null, ['', null]),
                ];


                // Storage nuova Soluzione Tecnologica
                $insert = BdncpProcedureModel::createWithLogs($arrayValues);


                // Storage nelle tabelle di relazione
                $this->clear(
                    $insert,
                    null,
                    null,
                    !empty(Input::post('object_bdncp_procedure_id')) ? explode(',', strip_tags((string)Input::post('object_bdncp_procedure_id', true))) : null,
                );

                // Storage allegati associati al personale.
                $attach->storage('attach_files', 'bdncp_procedure', $insert->id, $insert['object']);


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

        // ID dell'ente corrente.
        $data['institution_id'] = $procedure['institution_id'];
        $data['seo'] = $procedure['p_s_d_r'] ?? null;



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


        // ID dell'ente corrente.
        $data['institution_id'] = $alert['institution_id'];
        $data['seo'] = $alert['p_s_d_r'] ?? null;


        render('bdncp_procedure/form_store_alert', $data, 'admin');
    }


    /**
     * @description Renderizza il form di modifica/duplicazione di una Procedura BDNCP
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/bdncp-procedure/edit-variation.html
     * @method GET
     */
    public function editVariation(): void
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
        $variation = Registry::get('bdncp-procedure');
        $variation = !empty($variation) ? $variation->toArray() : [];

        $this->breadcrumb->push('Procedure BDNCP', 'admin/bdncp-procedure');
        $this->breadcrumb->push('Modifica variante', '/');

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
        $isDuplicate = uri()->segment(3, 0) === 'duplicate-variation';

        // Setto il tipo di storage
        $data['_storageType'] = $isDuplicate ? 'duplicate' : 'update';

        //In base al tipo di storage setto l'action del form
        $data['formAction'] = ($isDuplicate) ? '/admin/bdncp-procedure/store-variation' : '/admin/bdncp-procedure/update-variation';
        $data['formSettings'] = [
            'name' => 'form_bdncp_procedure',
            'id' => 'form_bdncp_procedure',
            'class' => 'form_bdncp_procedure',
        ];

        $data['variation'] = $variation;

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'bdncp_procedure',
            $variation['id']
        );

        // Labels
        $data['labels'] = [];
        $data['labels'] = @$data['labels']['variation'];


        // ID dell'ente corrente.
        $data['institution_id'] = $variation['institution_id'];
        $data['seo'] = $variation['p_s_d_r'] ?? null;


        render('bdncp_procedure/form_store_variation', $data, 'admin');
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
            $this->acl->setRoute(checkRecordOwner($procedure['owner_id']) ? ['update', 'create'] : 'update', true);

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
                $attachValidator = $attach->validate('attach_files' . $cat, $cat);

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
            $eprocUrl = null;

            // Se la validazione degli allegati va a buon fine eseguo l'update dell'oggetto
            if ($doAction) {
                $data = [];
                $data['object'] = !empty(Input::post('object')) ? strip_tags(trim((string)Input::post('object', true))) : null;
                $data['cig'] = $cig;
                $data['bdncp_link'] = !empty(Input::post('bdncp_link')) ? strip_tags(trim((string)Input::post('bdncp_link', true))) : $bdncpLink;
                $data['eproc_url'] = !empty(Input::post('eproc_url')) ? strip_tags(trim((string)Input::post('eproc_url', true))) : $eprocUrl;

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
                        $cat . 'O__O' . $i++
                    );
                }


                // Generazione nuovo token
//                Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
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
            $this->acl->setRoute(checkRecordOwner($alert['owner_id']) ? ['update', 'create'] : 'update', true);

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
//                Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
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
     * @url /admin/bdncp-procedure/update-variation.html
     * @method POST
     */
    public function updateVariation(): void
    {
        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new BdncpProcedureValidator();
        $check = $validator->check('update', 'variation');

        if ($check['is_success']) {
            $doAction = true;

            $variationId = (int)strip_tags(Input::post('id', true));

            // Recupero l'atto attuale prima di modificarlo e lo salvo nel versioning
            $variation = BdncpProcedureModel::where('id', $variationId)
                ->with('rel_variation_procedure:id,object,cig')
                ->with('all_attachs');

            $variation = $variation->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute(checkRecordOwner($variation['owner_id']) ? ['update', 'create'] : 'update', true);

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
                $data['notes'] = setDefaultData(Input::post('notes', true), null, ['', null]);


                BdncpProcedureModel::where('id', $variationId)->updateWithLogs($variation, $data);


                // Storage nelle tabelle di relazione
                $this->clear(
                    $variation,
                    null,
                    null,
                    !empty(Input::post('object_bdncp_procedure_id')) ? explode(',', strip_tags((string)Input::post('object_bdncp_procedure_id', true))) : null,
                );


                // Upload allegati associati al personale.
                $attach->update(
                    'attach_files',
                    'bdncp_procedure',
                    $variationId,
                    $variation['institution_id'],
                    $variation['object']
                );


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
            redirect('admin/bdncp-procedure', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Se la validazione va a buon fine recupero l'elemento da eliminare dal registro
        $procedure = Registry::get('bdncp-procedure');


        // Elimino l'atto settando deleted = 1
        $procedure->deleteWithLogs($procedure);



        sessionSetNotify(__('success_delete_operation', null, 'patos'));

        // Generazione nuovo token
//        Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
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
        $this->acl->setRoute('delete', true);

        $validator = new BdncpProcedureValidator();
        $validate = $validator->multipleSelection();

        if ($validate['is_success']) {

            //Recupero gli elementi da eliminare dal registro
            $procedures = Registry::get('__ids__multi_select_profile__');

            //Estraggo gli array degli elementi da eliminare
            $ids = Arr::pluck($procedures, 'id');

            $esclusi = [];


            //Elimino gli elementi
            foreach ($procedures as $procedure) {

                $procedure->deleteWithLogs($procedure);


            }


            if (empty($esclusi)) {
                sessionSetNotify('Operazione avvenuta con successo');
            } else {
            }

            // Generazione nuovo token
//            Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        } else {

            sessionSetNotify($validate['errors'], 'danger');
        }

        redirect('/admin/bdncp-procedure');
    }

    /**
     * @description Metodo per lo storage nelle tabelle di relazione
     *
     * @param BdncpProcedureModel|null $procedure Procedura per cui inserire i dati di relazione
     * @param array|int|null $judgingCommission Incarichi che compongono la commissione giudicatrice
     * @param array|int|null $technicalAdvisoryBoard Incarichi che compongono il Collegio consultivo tecnico
     * @param array|int|null $relativeBdncpProcedure Procedure relative per le varianti
     * @param array|int|null $measures Provvedimenti associati
     * @return void
     */
    protected function clear(BdncpProcedureModel $procedure = null, array|int $judgingCommission = null, array|int $technicalAdvisoryBoard = null, array|int $relativeBdncpProcedure = null, array|int $measures = null): void
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

        $dataProcedure = [];
        if ($relativeBdncpProcedure !== null) {
            foreach ($relativeBdncpProcedure as $proc) {
                $dataProcedure[] = is_array($proc) ? $proc['id'] : $proc;
            }
        }
        //Insert/Update nella tabella di relazione
        $procedure->rel_variation_procedure()->syncWithPivotValues($dataProcedure, ['typology' => 'variation']);

        $dataMeasures = [];
        if ($measures !== null) {
            foreach ($measures as $measure) {
                $dataMeasures[] = is_array($measure) ? $measure['id'] : $measure;
            }
        }
        //Insert/Update nella tabella di relazione
        $procedure->measures()->sync($dataMeasures);

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


    private function attachAnchorMenu($record)
    {

        if ($record['typology'] == 'procedure') {

            $linkEdit = siteUrl('admin/bdncp-procedure/edit/' . $record['id']);

            $selectColor = '#dc3545';
            $hiddenOpt = 0;
            $attachCount = [];

            if (!empty($record['all_attachs'])) {
                $hiddenOpt = count($record['all_attachs']);
                $selectColor = '#28a745';
                $attachCount = array_count_values(
                    array_column($record['all_attachs'], 'bdncp_cat')
                );
            }

            return '
                <select style="color: #fff; background-color: ' . $selectColor . ';" id="select-attach-' . $record['id'] . '" class="form-control select-attach" onchange="window.location.href=this.value;">
                    <option selected disabled value="">' . $hiddenOpt . '</option>
                
                    <optgroup label="Fase pubblicazione">
                        <option value="' . $linkEdit . '#anchor__publicDebate">Dibattito pubblico ' . (!empty($attachCount['_publicDebate']) ? "(" . $attachCount['_publicDebate'] . ")" : "") . '</option>
                        <option value="' . $linkEdit . '#anchor__noticeDocuments">Documenti di gara e affidamenti diretti ' . (!empty($attachCount['_noticeDocuments']) ? '(' . $attachCount['_noticeDocuments'] . ')' : '') . '</option>
                    </optgroup>
                
                    <optgroup label="Fase affidamento">
                        <option value="' . $linkEdit . '#anchor__equalOpportunitiesAf">Pari opportunità e inclusione lavorativa ' . (!empty($attachCount['_equalOpportunitiesAf']) ? '(' . $attachCount['_equalOpportunitiesAf'] . ')' : '') . '</option>
                        <option value="' . $linkEdit . '#anchor__localPublicServices">Procedure di affidamento dei servizi pubblici locali ' . (!empty($attachCount['_localPublicServices']) ? '(' . $attachCount['_localPublicServices'] . ')' : '') . '</option>
                    </optgroup>
                
                    <optgroup label="Fase esecutiva">
                        <option value="' . $linkEdit . '#anchor__equalOpportunitiesEs">Pari opportunità e inclusione lavorativa ' . (!empty($attachCount['_equalOpportunitiesEs']) ? '(' . $attachCount['_equalOpportunitiesEs'] . ')' : '') . '</option>
                    </optgroup>
                
                    <optgroup label="Sponsorizzazioni">
                        <option value="' . $linkEdit . '#anchor__freeContract">Contratti gratuiti e forme speciali di partenariato ' . (!empty($attachCount['_freeContract']) ? '(' . $attachCount['_freeContract'] . ')' : '') . '</option>
                    </optgroup>
                
                    <optgroup label="Procedure di somma urgenza e protezione civile">
                        <option value="' . $linkEdit . '#anchor__emergencyFoster">Affidamenti di somma urgenza ' . (!empty($attachCount['_emergencyFoster']) ? '(' . $attachCount['_emergencyFoster'] . ')' : '') . '</option>
                    </optgroup>
                
                    <optgroup label="Finanza di progetto">
                        <option value="' . $linkEdit . '#anchor__fosterProcedure">Procedura di affidamento ' . (!empty($attachCount['_fosterProcedure']) ? '(' . $attachCount['_fosterProcedure'] . ')' : '') . '</option>
                    </optgroup>
                
                    <optgroup label="Altre informazioni">
                        <option value="' . $linkEdit . '#anchor__generalInfo">Altre informazioni ' . (!empty($attachCount['_generalInfo']) ? '(' . $attachCount['_generalInfo'] . ')' : '') . '</option>
                    </optgroup>
                </select>';
        } else {
            return !empty($record['all_attachs'])
                ? '<small class="badge badge-success" style="background-color:#28a745;color:#fff; width: 60px;" data-toggle="tooltip" title="Sono presenti allegati">Si</small>'
                : '<small class="badge badge-danger"  style="background-color:#dc3545;color:#fff; width: 60px;" data-toggle="tooltip" title="Non sono presenti allegati">No</small>';
        }

    }
}
