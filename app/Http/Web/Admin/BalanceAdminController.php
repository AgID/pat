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
use Helpers\Validators\BalanceValidator;
use Helpers\Validators\DatatableValidator;
use Model\BalanceSheetsModel;
use Scope\DeletedScope;
use System\Arr;
use System\Input;
use System\JsonResponse;
use System\Registry;
use System\Session;
use System\Token;

/**
 *
 * Controller Bilanci
 *
 */
class BalanceAdminController extends BaseAuthController
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
     * @description Renderizza la pagina index dei Bilanci
     *
     * @return void
     * @method GET
     * @throws Exception
     * @url /admin/balance.html
     */
    public function index(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['read', 'create']);

        $this->breadcrumb->push('Bilanci', '/');
        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Bilanci';
        $data['subTitleSection'] = 'GESTIONE DEI BILANCI DELL\'ENTE';
        $data['sectionIcon'] = '<i class="fas fa-chart-line fa-3x"></i>';

        $data['formAction'] = '/admin/balance';
        $data['formSettings'] = [
            'name' => 'form_balance',
            'id' => 'form_balance',
            'class' => 'form_balance',
        ];

        $data['include'] = '';

        $data['url'] = uri()->segment(2, 0);

        render('balance/index', $data, 'admin');
    }

    /**
     * @description Funzione per la paginazione dei dati nel datatable
     *
     * @return void
     * @url /admin/balance/list.html
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
                2 => 'typology',
                3 => 'year',
                4 => 'userName',
                5 => 'updated_at'
            ];

            // Se l'utente è super Admin mostro la colonna dell'ente
            if (isSuperAdmin(true)) {
                $orderable[6] = 'i.full_name_institution';
            }

            //Setto proprietà datatable
            $dataTable = [];
            setDataTableData($dataTable, 'updated_at');

            // Query per i dati da mostrare nel datatable
            $totalRecords = BalanceSheetsModel::select('count(id) as allcount')
                ->count();

            $totalRecordsWithFilter = BalanceSheetsModel::search($dataTable['searchValue'])
                ->select('count(object_balance_sheets.id) as allcount')
                ->join('users', 'users.id', '=', 'object_balance_sheets.owner_id', 'left outer');

            if (!empty($dataTable['searchValue'])) {
                $totalRecordsWithFilter->orWhere('object_balance_sheets.id', '=', $dataTable['searchValue']);
            }

            $totalRecordsWithFilter = $totalRecordsWithFilter->count();

            $order = setOrderDatatable($dataTable['columnName'], $orderable, 'name');

            $records = BalanceSheetsModel::search($dataTable['searchValue'])
                ->select(['object_balance_sheets.id', 'object_balance_sheets.owner_id', 'object_balance_sheets.institution_id', 'object_balance_sheets.name',
                    'typology', 'year', 'object_balance_sheets.updated_at', 'publishing_status', 'users.name as userName', 'i.full_name_institution'])
                ->join('users', 'users.id', '=', 'object_balance_sheets.owner_id', 'left outer')
                ->with(['created_by' => function ($query) {
                    $query->withoutGlobalScopes([DeletedScope::class]);
                    $query->select(['id', 'name', 'deleted']);
                }])
                ->with(['institution' => function ($query) {
                    $query->select(['id', 'full_name_institution']);
                }]);

            if (!empty($dataTable['searchValue'])) {
                $records->orWhere('object_balance_sheets.id', '=', $dataTable['searchValue']);
            }

            $records = $records->join('institutions as i', 'object_balance_sheets.institution_id', '=', 'i.id', 'left outer')
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

                    if ($record['typology'] == 'piano indicatori e risultati') {
                        $recordUrl = siteUrl('/page/131/details/' . $record['id'] . '/' . urlTitle($record['name']));
                    } else {
                        $recordUrl = siteUrl('/page/130/details/' . $record['id'] . '/' . urlTitle($record['name']));
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

                    //Setto i pulsanti delle actions da mostrare in base ai permessi dell'utente, se non ha i permessi non li setto
                    $buttonAction = ($this->acl->getUpdate() || $this->acl->getDelete() || $permits) ? ButtonAction::create([
                        'edit' => $this->acl->getUpdate() || $permits,
                        'duplicate' => $this->acl->getCreate(),
                        'delete' => $this->acl->getDelete() || $updatePermits || $permits,
                    ])
                        ->addEdit('admin/balance/edit/' . $record['id'], $record['id'])
                        ->addDuplicate('admin/balance/duplicate/' . $record['id'], $record['id'])
                        ->addDelete('admin/balance/delete/' . $record['id'], $record['id'])
                        ->render() : '';

                    $icon = null;

                    //Setto i dati da mostrare nelle colonne del datatable
                    $setTempData = [];
                    $setTempData[] = ($this->acl->getCrud() || checkRecordOwner($record['owner_id'])) ? ButtonAction::checkList('item[]', $record['id']) : '';
                    $setTempData[] = $icon . (!empty($record['name'])
                            ? '<a href="' . $recordUrl . '" target="_blank">' . escapeXss($record['name']) . '</a>'
                            : 'N.D.');
                    $setTempData[] = !empty($record['typology']) ? ucfirst(escapeXss($record['typology'])) : 'N.D.';
                    $setTempData[] = !empty($record['year']) ? escapeXss($record['year']) : 'N.D.';
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
     * @description Renderizza il form per la creazione dei Bilanci
     *
     * @return void
     * @method GET
     * @throws Exception
     * @url /admin/balance/create.html
     */
    public function create(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(__FUNCTION__);

        $data = [];

        //Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $data['is_box'] = uri()->uriString() === 'admin/balance/create-box';

        //Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {
            $this->breadcrumb->push('Bilanci', 'admin/balance');
            $this->breadcrumb->push('Nuovo', '/');

            $data['breadcrumbs'] = $this->breadcrumb->show();

            //Dati header della sezione
            $data['titleSection'] = 'Bilanci';
            $data['subTitleSection'] = 'GESTIONE DEI BILANCI DELL\'ENTE';
            $data['sectionIcon'] = '<i class="fas fa-chart-line fa-3x"></i>';
        }

        $data['formAction'] = '/admin/balance/store';
        $data['formSettings'] = [
            'name' => 'form_balance',
            'id' => 'form_balance',
            'class' => 'form_balance',
        ];

        $data['_storageType'] = 'insert';

        //Valori selezionabili per il campo tipologia
        $data['typologies'] = [
            '' => '',
            'bilancio preventivo'=>'Bilancio preventivo',
            'bilancio consuntivo'=>'Bilancio consuntivo',
            'piano indicatori e risultati'=>'Piano degli indicatori e risultati attesi',
            'variazioni di bilancio'=>'Variazione di Bilancio'
        ];

        // Labels
        $data['labels'] = [];

        render('balance/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua lo storage di un nuovo Bilancio
     *
     * @return void
     * @method POST
     * @throws Exception
     * @url /admin/balance/store.html
     */
    public function store(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new BalanceValidator();
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

                $balanceName = strip_tags((string)Input::post('name', true));

                $arrayValues = [
                    'owner_id' => $getIdentity['id'],
                    'institution_id' => checkAlternativeInstitutionId(),
                    'object_measure_id' => setDefaultData(strip_tags((int)Input::post('object_measure_id', true)), null, ['', null]),
                    'typology' => setDefaultData(strip_tags(Input::post('typology', true)), null, ['']),
                    'name' => $balanceName,
                    'year' => setDefaultData(strip_tags((string)Input::post('year', true)), null, ['']),
                    'description' => Input::post('description', true)
                ];

                // Storage nuovo Bilancio
                $insert = BalanceSheetsModel::createWithLogs($arrayValues);

                // Storage allegati associati al personale.
                $attach->storage('attach_files', 'balance_sheets', $insert->id, $balanceName);

                $json->set('message', __('success_save_operation', null, 'patos'));
            }

            if (!referenceOriginForRegenerateToken(3, 'create-box')) {
                // Generazione nuovo token
                Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
            }

        } else {

            $code = $json->bad();
            $json->error('error', $check['errors']);
        }

        $json->setStatusCode($code);
        $json->response();
    }

    /**
     * @description Funzione che renderizza il form di modifica/duplicazione di un Bilancio
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/balance/edit/:id.html
     * @method GET
     */
    public function edit(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['update', 'create']);

        //Validatore che verifica se l'elemento da modificare esiste
        $validator = new BalanceValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        if (!$validate['is_success']) {
            redirect('admin/balance', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Allegati
        $attach = new AttachmentArchive();

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $balance = Registry::get('balance');
        $balance = !empty($balance) ? $balance->toArray() : [];

        $this->breadcrumb->push('Bilanci', 'admin/balance');
        $this->breadcrumb->push('Modifica', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Bilanci';
        $data['subTitleSection'] = 'GESTIONE DEI BILANCI DELL\'ENTE';
        $data['sectionIcon'] = '<i class="fas fa-chart-line fa-3x"></i>';

        // Vedo in sessione se sono arrivato qui da un operazione di ripristino dal versioning
        $session = new Session();
        $restore = $session->getFlash('restore');
        $data['restore'] = $restore;

        // Controllo se si sta duplicando un tasso di assenza
        $isDuplicate = uri()->segment(3, 0) === 'duplicate';

        // Setto il tipo di storage
        $data['_storageType'] = $isDuplicate ? 'duplicate' : 'update';

        //In base al tipo di storage setto l'action del form
        $data['formAction'] = ($isDuplicate) ? '/admin/balance/store' : '/admin/balance/update';
        $data['formSettings'] = [
            'name' => 'form_balance',
            'id' => 'form_balance',
            'class' => 'form_balance',
        ];

        $data['balance'] = $balance;

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'balance_sheets',
            $balance['id']
        );

        //Valori selezionabili per il campo tipologia
        $data['typologies'] = [
            '' => '',
            'bilancio preventivo'=>'Bilancio preventivo',
            'bilancio consuntivo'=>'Bilancio consuntivo',
            'piano indicatori e risultati'=>'Piano degli indicatori e risultati attesi',
            'variazioni di bilancio'=>'Variazione di Bilancio'
        ];


        // Labels
        $data['labels'] = [];
        $data['seo'] = $balance['p_s_d_r'] ?? null;

        render('balance/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua l'update di un Bilancio
     *
     * @return void
     * @throws Exception
     * @url /admin/balance/update.html
     * @method POST
     */
    public function update(): void
    {

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new BalanceValidator();
        $check = $validator->check('update');

        if ($check['is_success']) {
            $doAction = true;

            $balanceId = (int)strip_tags(Input::post('id', true));

            // Recupero il bilancio attuale prima di modificarlo e lo salvo nel versioning
            $balance = BalanceSheetsModel::where('id', $balanceId)
                ->with('all_attachs');

            $balance = $balance->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute(__FUNCTION__, (!checkRecordOwner($balance['owner_id']) && $this->acl->getCreate()));

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

            // Se la validazione degli allegati va a buon fine eseguo lo storage dell'oggetto
            if ($doAction) {
                $data = [];
                $data['typology'] = setDefaultData(strip_tags(Input::post('typology', true)), null, ['']);
                $data['object_measure_id'] = setDefaultData(strip_tags((int)Input::post('object_measure_id', true)), null, ['', null]);
                $data['name'] = strip_tags((string)Input::post('name', true));
                $data['year'] = setDefaultData(strip_tags((string)Input::post('year', true)), null, ['']);
                $data['description'] = Input::post('description', true);

                // Update Bilancio
                BalanceSheetsModel::where('id', $balanceId)->updateWithLogs($balance, $data);

                // Upload allegati associati al personale.
                $attach->update(
                    'attach_files',
                    'balance_sheets',
                    $balanceId,
                    $balance['institution_id'],
                    $balance['name']
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
     * @description Funzione che effettua l'eliminazione di un bilancio
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/balance/delete/:id.html
     * @method GET
     */
    public function delete(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute([__FUNCTION__, 'create', 'update'], true);

        //Validatore che verifica se l'elemento da eliminare esiste
        $validator = new BalanceValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getDelete() && $this->acl->getUpdate());

        if (!$validate['is_success']) {
            redirect('admin/balance', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Se la validazione va a buon fine recupero l'elemento da eliminare dal registro
        $balance = Registry::get('balance');

        $balanceId = $balance->id;

        //Elimino il bilancio
        $balance->deleteWithLogs($balance);

        sessionSetNotify(__('success_delete_operation', null, 'patos'));

        redirect('admin/balance');
    }

    /**
     * @description Funzione per l'eliminazione multipla
     *
     * @return void
     * @throws Exception
     * @url /admin/balance/deletes.html
     * @method GET
     */
    public function deletes(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('delete');

        // Validatore sugli elementi da eliminare
        $validator = new BalanceValidator();
        $validate = $validator->multipleSelection();

        if ($validate['is_success']) {

            sessionSetNotify('Operazione avvenuta con successo');

            //Recupero gli elementi da eliminare dal registro
            $balances = Registry::get('__ids__multi_select_profile__');

            //Estraggo gli array degli elementi da eliminare
            $ids = Arr::pluck($balances, 'id');

            //Elimino gli elementi
            foreach ($balances as $balance) {
                $balance->deleteWithLogs($balance);
            }

            // Generazione nuovo token
            Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        } else {

            sessionSetNotify($validate['errors'], 'danger');
        }

        redirect('/admin/balance');
    }
}
