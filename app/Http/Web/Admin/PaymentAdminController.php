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
use Helpers\Validators\PaymentValidator;
use Model\PaymentModel;
use Scope\DeletedScope;
use System\Input;
use System\JsonResponse;
use System\Registry;
use System\Session;

/**
 *
 * Controller Sovvenzioni e vantaggi economici
 *
 */
class PaymentAdminController extends BaseAuthController
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
     * @url /admin/payment.html
     * @method GET
     */
    public function index(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['read', 'create']);

        $this->breadcrumb->push('Dati sui pagamenti', '/');
        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Dati sui pagamenti';
        $data['subTitleSection'] = 'GESTIONE DEI PAGAMENTI';
        $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';

        $data['formAction'] = '/admin/payment';
        $data['formSettings'] = [
            'name' => 'form_payment',
            'id' => 'form_payment',
            'class' => 'form_payment',
        ];

        $data['include'] = '';

        $data['url'] = uri()->segment(2, 0);

        render('payment/index', $data, 'admin');
    }

    /**
     * @description Funzione per la paginazione dei dati nel datatable
     *
     * @return void
     * @url /admin/payment/list.html
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
                1 => 'object',
                3 => 'year',
                5 => 'expense_category',
                7 => 'updated_at'
            ];



            //Setto proprietà datatable
            $dataTable = [];
            setDataTableData($dataTable, 'updated_at');



            $filter = Input::get('filter');

            $checkVl = false;
            $manageableStatesVl = [];
            $usInfo = null;
            //Query per i dati da mostrare nel datatable
            $totalRecords = PaymentModel::select('count(id) as allcount')
                ->count();

            $searchValue = $dataTable['searchValue'];

            //Query
            $queryCount = PaymentModel::search($searchValue);
            $queryCount->select(['count(id) as allcount']);
            $queryCount->join('users', 'users.id', '=', 'object_payment.owner_id', 'left outer');

            // Inclusione di librerie esterne

            $totalRecordsWithFilter = $queryCount->count();

            $order = setOrderDatatable($dataTable['columnName'], $orderable, 'updated_at');

            $queryRecords = PaymentModel::search($searchValue);
            $queryRecords->select(['object_payment.*', 'users.name']);
            $queryRecords->join('users', 'users.id', '=', 'object_payment.owner_id', 'left outer');
            $queryRecords->with('structure:id,structure_name');
            $queryRecords->with(['created_by' => function ($query) {
                $query->withoutGlobalScopes([DeletedScope::class]);
                $query->select(['id', 'name', 'deleted']);
            }]);
            $queryRecords->with('institution:id,full_name_institution');
            $queryRecords->with('all_attachs:id,archive_id');
            $queryRecords->join('institutions as i', 'object_payment.institution_id', '=', 'i.id');

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

                $checkWf = false;
                $manageableStates = [];
                $getIdentity = authPatOs()->getIdentity(['id']);

                $categories = [ 1 => 'Uscite correnti', 2 => 'Uscite in conto capitale'];

                $paymentSupplier = config('paymentSupplierTypology', [], 'app');

                $linkFo = false;

                foreach ($records as $record) {

                    if (!empty($record['updated_at'])) {
                        $updateAt = '<small class="badge badge-info"> 
                                    <i class="far fa-clock"></i> '
                            . date('d-m-Y H:i:s', strtotime($record['updated_at'])) . '</small>';
                    } else {
                        $updateAt = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definita">N.D.</small>';
                    }

                    // Controllo se l'utente ha i permessi di modifica dei record o di scrittura (e quindi di modifica dei propri record)
                    $permits = ($this->acl->getCreate() && checkRecordOwner($record['owner_id'],$getIdentity['id']));
                    $updatePermits = ($this->acl->getUpdate() && checkRecordOwner($record['owner_id'],$getIdentity['id']));

                    $wfPermits = false;

                    $workflowState = $record['workflow_state'] ?? 'finale';
                    $wfOwnerPermits = $checkWf && $permits && $workflowState == 'iniziale';
                    $vlPermits = false;
                    $validationState = $record['validation_state'] ?? null;
                    $vlOwnerPermits = $checkVl && $permits && $validationState == 'iniziale';

                    $editPerm = match (true){
                        $checkWf => (($this->acl->getUpdate() && $wfPermits) || $wfOwnerPermits),
                        $checkVl => (($this->acl->getUpdate() && $vlPermits) || $vlOwnerPermits),
                        default => $this->acl->getUpdate() || $permits
                    };

                    $deletePerm = match (true){
                        $checkWf => (($this->acl->getDelete() && $wfPermits) || $wfOwnerPermits),
                        $checkVl => (($this->acl->getDelete() && $vlPermits) || $vlOwnerPermits || $usInfo['cdvl']),
                        default => $this->acl->getDelete() || $updatePermits || $permits
                    };

                    //Setto i pulsanti delle actions da mostrare in base ai permessi dell'utente
                    $buttonAction = ($this->acl->getUpdate() || $this->acl->getDelete() || $permits) ? ButtonAction::create([
                        'edit' => $editPerm,
                        'duplicate' => $this->acl->getCreate()
                            && (!$checkWf || $workflowState == 'finale')
                            && (!$checkVl || $validationState == 'finale'),
                        'versioning' => getAclVersioning(),
                        'delete' => $deletePerm,
                    ])
                        ->addEdit('admin/payment/edit' . '/' . $record['id'], $record['id'],'',true)
                        ->addDuplicate('admin/payment/duplicate' . '/' . $record['id'], $record['id'])
                        ->addCustomButton('b_a_v', siteUrl('admin/payment/versioning'), $record['id'], '', '', 'versioning')
                        ->addCustomButton('s_r_l',siteUrl('admin/payment/show-record-log/'.$record['id']), $record['id'], '110', 'show-record-log', 'view')
                        ->addCustomButton('wf_a_f_e',"", $record['id'], $workflowState, '', 'view')
                        ->addDelete('admin/payment/delete'  . '/' . $record['id'], $record['id'])
                        ->render() : '';


                    $icon = null;

                    //Setto i dati da mostrare nelle colonne del datatable
                    $setTempData = [];

                    $checkBox = (($this->acl->getCrud() || checkRecordOwner($record['owner_id'])) ? ButtonAction::checkList('item[]', $record['id']) : '') . $icon;

                    $setTempData[] = $checkBox;

                    if(!empty($record['object'])){
                        $object = $record['object'];
                    }else{
                       $object = "Pagamento a ".($paymentSupplier[$record['supplier']] ?? '');
                    }

                    if($linkFo){
                        $setTempData[] = '<a href="' . siteUrl('/page/154/details/' . $record['id'] . '/' . urlTitle(escapeXss($object))) . '" target="_blank">' . escapeXss($object) . '</a>';
                    }else{
                        $setTempData[] = escapeXss($object);
                    }

                    $setTempData[] = !empty($record['supplier']) ? ($paymentSupplier[$record['supplier']] ?? '') : '';
                    $setTempData[] = !empty($record['year']) ? $record['year'] : '';
                    $setTempData[] = !empty($record['compensation']) ? $record['compensation'].' €' : '';
                    $setTempData[] = !empty($record['expense_category']) ? $categories[$record['expense_category']] : '';
                    //$setTempData[] = !empty($record['structure']) ? $record['structure']['structure_name'] : '';
                    $setTempData[] = createdByCheckDeleted(@$record['created_by']['name'], @$record['created_by']['deleted']);
                    $setTempData[] = $updateAt;
                    $setTempData[] = !empty($record['all_attachs'])
                        ? '<small class="badge badge-success" style="background-color:#28a745;color:#fff; width: 60px;" data-toggle="tooltip" title="Sono presenti allegati">Si</small>'
                        : '<small class="badge badge-danger"  style="background-color:#dc3545;color:#fff; width: 60px;" data-toggle="tooltip" title="Non sono presenti allegati">No</small>';

                    $setTempData[] = $buttonAction;

                    $data[] = $setTempData;
                }

                $response['aaData'] = $data;
            }
            jsonOut($response);
        }
    }

    /**
     * @description Renderizza il form per la creazione di una nuova Sovvenzione
     *
     * @return void
     * @throws Exception
     * @url /admin/payment/create.html
     * @method GET
     */
    public function create(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $data = [];

        //Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        $this->breadcrumb->push('Dati sui pagamenti', 'admin/payment');
        $this->breadcrumb->push('Nuovo', '/');
        $data['breadcrumbs'] = $this->breadcrumb->show();

        $data['titleSection'] = 'Dati sui pagamenti';
        $data['subTitleSection'] = 'GESTIONE DEI DATI SUI PAGAMENTI';
        $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';


        $data['formAction'] = '/admin/payment/store';
        $data['formSettings'] = [
            'name' => 'form_payment',
            'id' => 'form_payment',
            'class' => 'form_payment',
        ];
        $data['_storageType'] = 'insert';


        $years = range((int)date("Y"), 2025, -1);
        $data['years'] = [''=>null] + array_combine($years, $years);


        $data['paymentTypologyCorrenti'] = [null => ''] + config('paymentTypologyCorrenti', [], 'app');

        $data['paymentTypologyCapitale'] = [null => ''] + config('paymentTypologyCapitale', [], 'app');

        $data['paymentSupplierTypology'] = [null => ''] + config('paymentSupplierTypology', [], 'app');

        // Labels
        $data['labels'] = [];

        // ID dell'ente corrente.
        $data['institution_id'] = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        render('payment/form_store', $data, 'admin');
    }


    /**
     * @description Funzione che effettua lo storage di una nuova Sovvenzione
     *
     * @return void
     * @throws Exception
     * @url /admin/payment/store.html
     * @method POST
     */
    public function store(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new PaymentValidator();
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

                if((int)Input::post('payment_category') == 1){
                    $typology = (int)Input::post('payment_typology1',true);
                }else{
                    $typology = (int)Input::post('payment_typology2',true);
                }

                $arrayValues = [
                    'owner_id' => $getIdentity['id'],
                    'institution_id' => checkAlternativeInstitutionId(),
                    'object' => !empty(Input::post('payment_object', true)) ? strip_tags((string)Input::post('payment_object', true)) : '',
                    'year' => !empty(Input::post('payment_year')) ? (int)Input::post('payment_year',true) : 0,
                    'trimester' => !empty(Input::post('payment_trimester')) ? (int)Input::post('payment_trimester',true) : 0,
                    'compensation' => !empty(Input::post('payment_compensation')) ? toFloat(strip_tags(Input::post('payment_compensation', true))) : null,
                    'expense_category' => !empty(Input::post('payment_category')) ? (int)Input::post('payment_category',true) : null,
                    'expense_typology' => $typology,
                    'supplier' => (int)Input::post('payment_supplier', true),
                    'notes' => Input::post('payment_notes', true)
                ];

                // Storage nuova Sovvenzione
                $insert = PaymentModel::createWithLogs($arrayValues);

                // Storage allegati associati al personale.
                $attach->storage('attach_files', 'payment', $insert->id, $insert['object']);


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
     * @description Renderizza il form per la modifica/duplicazione dei pagamenti
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/payment/edit.html
     * @method GET
     */
    public function edit(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['update', 'create']);

        //Validatore che verifica se l'elemento da modificare esiste
        $validator = new PaymentValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/payment', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Allegati
        $attach = new AttachmentArchive();

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $payment = Registry::get('payment');
        $payment = !empty($payment) ? $payment->toArray() : [];

        $this->breadcrumb->push('Dati sui pagamenti', 'admin/payment');
        $this->breadcrumb->push('Modifica', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();
        $data['titleSection'] = 'Dati sui pagamenti';
        $data['subTitleSection'] = 'GESTIONE DEI DATI SUI PAGAMENTI';
        $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';

        // Vedo in sessione se sono arrivato qui da un operazione di ripristino dal versioning
        $session = new Session();
        $restore = $session->getFlash('restore');
        $data['restore'] = $restore;

        // Controllo se si sta duplicando un tasso di assenza
        $isDuplicate = uri()->segment(3, 0) === 'duplicate';

        // Setto il tipo di storage
        $data['_storageType'] = $isDuplicate ? 'duplicate' : 'update';

        //In base al tipo di storage setto l'action del form
        $data['formAction'] = ($isDuplicate) ? '/admin/payment/store' : '/admin/payment/update';
        $data['formSettings'] = [
            'name' => 'form_payment',
            'id' => 'form_payment',
            'class' => 'form_payment',
        ];


        $data['payment'] = $payment;


        $years = range((int)date("Y"), 2025, -1);
        $data['years'] = [''=>null] + array_combine($years, $years);


        $data['paymentTypologyCorrenti'] = [null => ''] + config('paymentTypologyCorrenti', [], 'app');

        $data['paymentTypologyCapitale'] = [null => ''] + config('paymentTypologyCapitale', [], 'app');


        $data['paymentSupplierTypology'] = [null => ''] + config('paymentSupplierTypology', [], 'app');

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'payment',
            $payment['id']
        );

        // Labels
        $data['labels'] = [];

        // ID dell'ente corrente.
        $data['institution_id'] = $payment['institution_id'];

        $data['seo'] = $payment['p_s_d_r'] ?? null;

        render('payment/form_store', $data, 'admin');
    }


    /**
     * @description Funzione che effettua l'update di una Sovvenzione
     *
     * @return void
     * @throws Exception
     * @url /admin/payment/update/:id.html
     * @method POST
     */
    public function update(): void
    {
        $json = new JsonResponse();
        $code = $json->success();
        // Validatore form
        $validator = new PaymentValidator();
        $check = $validator->check('update');

        if ($check['is_success']) {
            $doAction = true;

            $paymentId = (int)strip_tags(Input::post('id', true));

            // Recupero la sovvenzione attuale prima di modificarla e la salvo nel versioning
            $payment = PaymentModel::where('id', $paymentId)
                ->with('structure:id,structure_name')
                ->with('all_attachs');
            $payment = $payment->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute(checkRecordOwner($payment['owner_id']) ? ['update', 'create'] : 'update', true);

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

                if((int)Input::post('payment_category') == 1){
                    $typology = (int)Input::post('payment_typology1',true);
                }else{
                    $typology = (int)Input::post('payment_typology2',true);
                }

                $data['object'] = !empty(Input::post('payment_object', true)) ? strip_tags((string)Input::post('payment_object', true)) : '';
                $data['year'] = !empty(Input::post('payment_year')) ? (int)Input::post('payment_year',true) : 0;
                $data['trimester'] = !empty(Input::post('payment_trimester')) ? (int)Input::post('payment_trimester',true) : 0;
                $data['compensation'] = !empty(Input::post('payment_compensation')) ? toFloat(strip_tags(Input::post('payment_compensation', true))) : null;
                $data['expense_category'] = !empty(Input::post('payment_category')) ? (int)Input::post('payment_category',true) : null;
                $data['expense_typology'] = $typology;
                $data['supplier'] = (int)Input::post('payment_supplier', true);
                $data['notes'] = Input::post('payment_notes', true);

                // Update Sovvenzione
                PaymentModel::where('id', $paymentId)->updateWithLogs($payment, $data);

                // Upload allegati associati al personale.
                $attach->update(
                    'attach_files',
                    'payment',
                    $paymentId,
                    $payment['institution_id'],
                    $payment['object']
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
     * @description Funzione che effettua l'eliminazione di una Sovvenzione
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/payment/delete/:id.html
     * @method GET
     */
    public function delete(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['delete', 'create', 'update'], true);

        //Validatore che verifica se l'elemento da eliminare esiste
        $validator = new PaymentValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getDelete() && $this->acl->getUpdate());

        if (!$validate['is_success']) {
            redirect('admin/payment', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Se la validazione va a buon fine recupero l'elemento da eliminare dal registro
        $payment = Registry::get('payment');

        //Elimino la sovvenzione
        $payment->deleteWithLogs($payment);

        sessionSetNotify(__('success_delete_operation', null, 'patos'));

        redirect('admin/payment');
    }

    /**
     * @description Funzione per l'eliminazione multipla
     *
     * @return void
     * @throws Exception
     * @url /admin/payment/deletes.html
     * @method GET
     */
    public function deletes(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('delete', true);

        $validator = new PaymentValidator();
        $validate = $validator->multipleSelection();

        if ($validate['is_success']) {

            sessionSetNotify('Operazione avvenuta con successo');

            //Recupero gli elementi da eliminare dal registro
            $payments = Registry::get('__ids__multi_select_profile__');

            //Elimino gli elementi
            foreach ($payments as $payment) {
                $payment->deleteWithLogs($payment);
            }

        } else {

            sessionSetNotify($validate['errors'], 'danger');
        }

        redirect('/admin/payment');
    }

}
