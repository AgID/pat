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
use Helpers\Validators\ContestValidator;
use Helpers\Validators\DatatableValidator;
use Model\ContestModel;
use Scope\DeletedScope;
use System\Arr;
use System\Input;
use System\JsonResponse;
use System\Registry;
use System\Session;
use System\Token;

/**
 *
 * Controller Bandi di Concorso
 *
 */
class ContestAdminController extends BaseAuthController
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
     * @description Renderizza la pagina index dei Bandi di Concorso
     * @return void
     * @throws Exception
     * @url /admin/contest.html
     * @method GET
     */
    public function index(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['read', 'create']);

        $archiveName = 'Bandi di Concorso';

        $this->breadcrumb->push($archiveName, '/');
        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();
        $data['provinceShort'] = [null => 'Seleziona'] + config('province_short', null, 'locations');

        //Dati header della sezione
        $data['titleSection'] = $archiveName;
        $data['subTitleSection'] = 'GESTIONE DELLE PUBBLICAZIONI DI ' . strtoupper($archiveName);
        $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';

        $data['formAction'] = '/admin/contest';
        $data['formSettings'] = [
            'name' => 'form_contest',
            'id' => 'form_contest',
            'class' => 'form_contest',
        ];

        $data['include'] = '';

        $data['url'] = uri()->segment(2, 0);
        $data['archiveName'] = $archiveName;

        render('contest/index', $data, 'admin');
    }

    /**
     * @description Funzione per la paginazione dei dati nel datatable
     *
     * @return void
     * @url /admin/contest/list.html
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

            $typologyMap = [
                'avviso' => 'alert',
                'concorso' => 'contest',
                'esito' => 'result',
            ];

            $data = [];

            //Colonne su cui poter effettuare l'ordinamento
            $orderable = [
                1 => 'object',
                2 => 'typology',
                3 => 'object_contest.activation_date',
                4 => 'object_contest.expiration_date',
                5 => 'users.name',
                6 => 'object_contest.updated_at'
            ];

            //Se l'utente è super Admin mostro la colonna dell'ente
            if (isSuperAdmin(true)) {
                $orderable[7] = 'i.full_name_institution';
            }

            //Setto proprietà datatable
            $dataTable = [];
            setDataTableData($dataTable, 'object');

            //Query per i dati da mostrare nel datatable
            $totalRecords = ContestModel::select('count(id) as allcount')
                ->count();

            $totalRecordsWithFilter = ContestModel::search($dataTable['searchValue'])
                ->select(['count(object_contest.id) as allcount'])
                ->join('users', 'users.id', '=', 'object_contest.owner_id', 'left outer');

            if (!empty($dataTable['searchValue'])) {
                $totalRecordsWithFilter->orWhere('object_contest.id', '=', $dataTable['searchValue']);
            }

            $totalRecordsWithFilter = $totalRecordsWithFilter->count();

            $order = setOrderDatatable($dataTable['columnName'], $orderable, 'object');

            $records = ContestModel::search($dataTable['searchValue'])
                ->select(['object_contest.id', 'owner_id', 'object_contest.institution_id', 'typology', 'object', 'publishing_status',
                    'object_contest.updated_at', 'users.name', 'i.full_name_institution', 'object_contest.activation_date',
                    'object_contest.expiration_date'])
                ->join('users', 'users.id', '=', 'object_contest.owner_id', 'left outer')
                ->with(['created_by' => function ($query) {
                    $query->withoutGlobalScopes([DeletedScope::class]);
                    $query->select(['id', 'name', 'deleted']);
                }])
                ->with(['institution' => function ($query) {
                    $query->select(['id', 'full_name_institution']);
                }]);

            if (!empty($dataTable['searchValue'])) {
                $records->orWhere('object_contest.id', '=', $dataTable['searchValue']);
            }

            $records = $records->join('institutions as i', 'object_contest.institution_id', '=', 'i.id', 'left outer')
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

                    $activationDate = !empty($record['activation_date'])
                        ? ('<small class="badge badge-info"> 
                            <i class="far fa-calendar-alt"></i> ' .
                            date('d-m-Y', strtotime($record['activation_date'])) .
                            '</small>')
                        : '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definita">N.D.</small>';

                    $expirationDate = !empty($record['expiration_date'])
                        ? ('<small class="badge badge-info"> 
                            <i class="far fa-calendar-alt"></i> ' .
                            date('d-m-Y', strtotime($record['expiration_date'])) .
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
                        ->addEdit('admin/contest/edit-' . $typologyMap[$record['typology']] . '/' . $record['id'], $record['id'])
                        ->addDuplicate('admin/contest/duplicate-' . $typologyMap[$record['typology']] . '/' . $record['id'], $record['id'])
                        ->addDelete('admin/contest/delete/' . $record['id'], $record['id'])
                        ->render() : '';

                    $icon = null;

                    //Setto i dati da mostrare nelle colonne del datatable
                    $setTempData = [];
                    $setTempData[] = ($this->acl->getCrud() || checkRecordOwner($record['owner_id'])) ? ButtonAction::checkList('item[]', $record['id']) : '';
                    $setTempData[] = $icon . (!empty($record['object'])
                            ? '<a href="' . siteUrl('/page/5/details/' . $record['id'] . '/' . urlTitle($record['object'])) . '" target="_blank">' . escapeXss($record['object']) . '</a>'
                            : 'N.D.');
                    $setTempData[] = !empty($record['typology']) ? escapeXss(ucfirst($record['typology'])) : 'N.D.';
                    $setTempData[] = $activationDate;
                    $setTempData[] = $expirationDate;
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
     * @description Renderizza il form di creazione di un nuovo Bando di Concorso
     * @return void
     * @throws Exception
     * @url /admin/contest/create.html
     * @method GET
     */
    public function createContest(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $data = [];

        //Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $data['is_box'] = uri()->uriString() === 'admin/contest/create-box';

        //Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {

            $this->breadcrumb->push('Bandi di Concorso', 'admin/contest');
            $this->breadcrumb->push('Nuovo', '/');
            $data['breadcrumbs'] = $this->breadcrumb->show();

            //Dati header della sezione
            $data['titleSection'] = 'Bandi di Concorso';
            $data['subTitleSection'] = 'GESTIONE DELLE PUBBLICAZIONI DI BANDI DI CONCORSO';
            $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';
        }

        $data['provinceShort'] = [null => 'Seleziona'] + config('province_short', null, 'locations');
        $data['formAction'] = '/admin/contest/store-contest';
        $data['formSettings'] = [
            'name' => 'form_contest',
            'id' => 'form_contest',
            'class' => 'form_contest',
        ];
        $data['_storageType'] = 'insert';

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        render('contest/form_store_contest', $data, 'admin');
    }

    /**
     * @description Renderizza il form di creazione di un nuovo AVVISO
     * @return void
     * @throws Exception
     * @url /admin/contest/create-alert.html
     * @method GET
     */
    public function createAlert(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $data = [];

        $archiveName = 'Bandi di Concorso';

        //Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $data['is_box'] = uri()->uriString() === 'admin/contest/create-box';

        //Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {

            $this->breadcrumb->push($archiveName, 'admin/contest');
            $this->breadcrumb->push('Nuovo Avviso', '/');
            $data['breadcrumbs'] = $this->breadcrumb->show();

            //Dati header della sezione
            $data['titleSection'] = $archiveName;
            $data['subTitleSection'] = 'GESTIONE DELLE PUBBLICAZIONI DI BANDI DI ' . strtoupper($archiveName);
            $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';
        }

        $data['formAction'] = '/admin/contest/store-alert';
        $data['formSettings'] = [
            'name' => 'form_contest_alert',
            'id' => 'form_contest_alert',
            'class' => 'form_contest_alert',
        ];
        $data['_storageType'] = 'insert';

        $data['archiveName'] = $archiveName;

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        render('contest/form_store_alert', $data, 'admin');
    }

    /**
     * @description Renderizza il form di creazione di un nuovo Esito
     * @return void
     * @throws Exception
     * @url /admin/contest/create-result.html
     * @method GET
     */
    public function createResult(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $data = [];

        $archiveName = 'Bandi di Concorso';

        $this->breadcrumb->push($archiveName, 'admin/contest');
        $this->breadcrumb->push('Nuovo Esito', '/');
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = $archiveName;
        $data['subTitleSection'] = 'GESTIONE DELLE PUBBLICAZIONI DI ' . strtoupper($archiveName);
        $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';

        $data['formAction'] = '/admin/contest/store-result';
        $data['formSettings'] = [
            'name' => 'form_contest_result',
            'id' => 'form_contest_result',
            'class' => 'form_contest_result',
        ];
        $data['_storageType'] = 'insert';

        $data['archiveName'] = $archiveName;

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        render('contest/form_store_result', $data, 'admin');
    }

    /**
     * @description Funzione che effettua lo storage di un nuovo Bando di concorso
     *
     * @return void
     * @throws Exception
     * @url /admin/contest/store-contest.html
     * @method POST
     */
    public function storeContest(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new ContestValidator();
        $check = $validator->checkContest();

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
                    'owner_id' => (int)$getIdentity['id'],
                    'institution_id' => checkAlternativeInstitutionId(),
                    'typology' => 'concorso',
                    'object' => strip_tags(Input::post('object', true)),
                    'province_office' => setDefaultData(strip_tags(Input::post('province_office', true)), null, ['']),
                    'city_office' => strip_tags(Input::post('city_office', true)),
                    'office_address' => strip_tags(Input::post('office_address', true)),
                    'object_structures_id' => setDefaultData(strip_tags((int)Input::post('object_structures_id')), null, ['']),
                    'object_measure_id' => setDefaultData(strip_tags((int)Input::post('object_measure_id')), null, ['', null]),
                    'test_calendar' => Input::post('test_calendar', true),
                    'evaluation_criteria' => Input::post('evaluation_criteria', true),
                    'traces_written_tests' => Input::post('traces_written_tests', true),
                    'related_contest_id' => setDefaultData(strip_tags((int)Input::post('related_contest_id')), null, ['']),
                    'activation_date' => !empty(Input::post('activation_date')) ? strip_tags(Input::post('activation_date')) : null,
                    'expiration_date' => !empty(Input::post('expiration_date')) ? convertDateToDatabase(strip_tags(Input::post('expiration_date'))) : null,
                    'expiration_contest_date' => !empty(Input::post('expiration_contest_date')) ? convertDateToDatabase(strip_tags(Input::post('expiration_contest_date'))) : null,
                    'expiration_time' => strip_tags(Input::post('expiration_time', true)),
                    'hired_employees' => setDefaultData(strip_tags((int)Input::post('hired_employees')), null, ['']),
                    'expected_expenditure' => !empty(Input::post('expected_expenditure')) ? toFloat(strip_tags(Input::post('expected_expenditure'))) : null,
                    'expenditures_made' => !empty(Input::post('expenditures_made')) ? toFloat(strip_tags(Input::post('expenditures_made'))) : null,
                    'description' => Input::post('description', true)
                ];

                // Storage nuovo Bando di concorso
                $insert = ContestModel::createWithLogs($arrayValues);

                // Svuoto le tabelle di relazione e le aggiorno
                $this->clear(
                    $insert,
                    !empty(Input::post('commissions')) ? explode(',', strip_tags((string)Input::post('commissions', true))) : null
                );

                // Storage allegati associati al personale.
                $attach->storage('attach_files', 'contest', $insert->id, $arrayValues['object']);

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
     * @description Funzione che effettua lo storage di un nuovo Avviso per Bando di concorso
     *
     * @return void
     * @throws Exception
     * @url /admin/contest/store-alert.html
     * @method POST
     */
    public function storeAlert(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new ContestValidator();
        $check = $validator->checkAlert();

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
                    'owner_id' => (int)$getIdentity['id'],
                    'institution_id' => checkAlternativeInstitutionId(),
                    'typology' => 'avviso',
                    'object' => strip_tags(Input::post('object', true)),
                    'object_structures_id' => setDefaultData(strip_tags((int)Input::post('object_structures_id')), null, ['']),
                    'related_contest_id' => setDefaultData(strip_tags((int)Input::post('related_contest_id')), null, ['']),
                    'activation_date' => !empty(Input::post('activation_date')) ? strip_tags(Input::post('activation_date')) : null,
                    'expiration_date' => !empty(Input::post('expiration_date')) ? convertDateToDatabase(strip_tags(Input::post('expiration_date'))) : null,
                    'description' => Input::post('description', true)
                ];

                // Storage nuovo Bando di concorso
                $insert = ContestModel::createWithLogs($arrayValues);

                // Storage allegati associati al personale.
                $attach->storage('attach_files', 'contest', $insert->id, $arrayValues['object']);

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
     * @description Funzione che effettua lo storage di un nuovo Esito per Bando di concorso
     *
     * @return void
     * @throws Exception
     * @url /admin/contest/store-result.html
     * @method POST
     */
    public function storeResult(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new ContestValidator();
        $check = $validator->checkResult();

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
                    'owner_id' => (int)$getIdentity['id'],
                    'institution_id' => checkAlternativeInstitutionId(),
                    'typology' => 'esito',
                    'object' => strip_tags(Input::post('object', true)),
                    'related_contest_id' => setDefaultData(strip_tags((int)Input::post('related_contest_id')), null, ['']),
                    'activation_date' => !empty(Input::post('activation_date')) ? strip_tags(Input::post('activation_date')) : null,
                    'description' => Input::post('description', true)
                ];

                // Storage nuovo Bando di concorso
                $insert = ContestModel::createWithLogs($arrayValues);

                // Storage allegati associati al personale.
                $attach->storage('attach_files', 'contest', $insert->id, $arrayValues['object']);

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
     * @description Renderizza il form di modifica/duplicazione di un bando di concorso
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/contest/edit-contest/:id.html
     * @method GET
     */
    public function editContest(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('update');

        //Validatore che verifica se l'elemento da modificare esiste
        $validator = new ContestValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        $data = [];

        $segments = uri()->segmentArray();
        array_pop($segments);

        //Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $data['is_box'] = implode('/', $segments) === 'admin/contest/edit-box';

        if (!$validate['is_success']) {
            if (!$data['is_box']) {
                redirect('admin/contest', sessionSetNotify($validate['errors'], 'danger'));
            } else {
                render('access_denied/modal_access_denied', [], 'admin');
                die();
            }
        }

        // Allegati
        $attach = new AttachmentArchive();

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $contest = Registry::get('contest');
        $contest = !empty($contest) ? $contest->toArray() : [];

        // Vedo in sessione se sono arrivato qui da un operazione di ripristino dal versioning
        $session = new Session();
        $restore = $session->getFlash('restore');
        $data['restore'] = $restore;

        //Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {
            $this->breadcrumb->push('Bandi di Concorso', 'admin/contest');
            $this->breadcrumb->push('Modifica', '/');

            $data['breadcrumbs'] = $this->breadcrumb->show();

            //Dati header della sezione
            $data['titleSection'] = 'Bandi di Concorso';
            $data['subTitleSection'] = 'GESTIONE DELLE PUBBLICAZIONI DI BANDI DI CONCORSO';
            $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';
        }

        // Controllo se si sta duplicando un tasso di assenza
        $isDuplicate = uri()->segment(3, 0) === 'duplicate-contest';

        // Setto il tipo di storage
        $data['_storageType'] = $isDuplicate ? 'duplicate' : 'update';

        //In base al tipo di storage setto l'action del form
        $data['formAction'] = ($isDuplicate) ? '/admin/contest/store-contest' : '/admin/contest/update-contest';
        $data['formSettings'] = [
            'name' => 'form_contest',
            'id' => 'form_contest',
            'class' => 'form_contest',
        ];

        $activationDate = convertDateToForm($contest['activation_date']);
        $expirationDate = convertDateToForm($contest['expiration_date']);
        $expirationTime = convertDateToForm($contest['expiration_date']);
        $expirationContestDate = convertDateToForm($contest['expiration_contest_date']);

        $contest['activation_date'] = $activationDate['date'].' '.$activationDate['hours'];
        $contest['expiration_date'] = $expirationDate['date'];
        $data['contest'] = $contest;

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'contest',
            $contest['id']
        );

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = $contest['institution_id'];

        $data['expiration_contest_date'] = $expirationContestDate['date'];
        $data['expiration_time'] = $expirationTime['date'];
        $data['provinceShort'] = [null => 'Seleziona'] + config('province_short', null, 'locations');

        $data['commissionIds'] = Arr::pluck($contest['assignments'], 'id');
        $data['seo'] = $contest['p_s_d_r'] ?? null;

        render('contest/form_store_contest', $data, 'admin');
    }

    /**
     * @description Renderizza il form di modifica/duplicazione di un avviso per bando di concorso
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/contest/edit-alert/:id.html
     * @method GET
     */
    public function editAlert(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('update');

        //Validatore che verifica se l'elemento da modificare esiste
        $validator = new ContestValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        if (!$validate['is_success']) {
            redirect('admin/contest', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        $archiveName = 'Bandi di Concorso';

        // Allegati
        $attach = new AttachmentArchive();

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $alert = Registry::get('contest');
        $alert = !empty($alert) ? $alert->toArray() : [];

        $data = [];

        // Vedo in sessione se sono arrivato qui da un operazione di ripristino dal versioning
        $session = new Session();
        $restore = $session->getFlash('restore');
        $data['restore'] = $restore;

        $this->breadcrumb->push('Bandi di Concorso', 'admin/contest');
        $this->breadcrumb->push('Modifica', '/');

        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Bandi di Concorso';
        $data['subTitleSection'] = 'GESTIONE DELLE PUBBLICAZIONI DI BANDI DI CONCORSO';
        $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';

        // Controllo se si sta duplicando un tasso di assenza
        $isDuplicate = uri()->segment(3, 0) === 'duplicate-alert';

        // Setto il tipo di storage
        $data['_storageType'] = $isDuplicate ? 'duplicate' : 'update';

        $data['archiveName'] = $archiveName;

        //In base al tipo di storage setto l'action del form
        $data['formAction'] = ($isDuplicate) ? '/admin/contest/store-alert' : '/admin/contest/update-alert';
        $data['formSettings'] = [
            'name' => 'form_contest_alert',
            'id' => 'form_contest_alert',
            'class' => 'form_contest_alert',
        ];

        $activationDate = convertDateToForm($alert['activation_date']);
        $expirationDate = convertDateToForm($alert['expiration_date']);

        $alert['activation_date'] = $activationDate['date'].' '.$activationDate['hours'];
        $alert['expiration_date'] = $expirationDate['date'];
        $data['alert'] = $alert;

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'contest',
            $alert['id']
        );

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = $alert['institution_id'];
        $data['seo'] = $alert['p_s_d_r'] ?? null;

        render('contest/form_store_alert', $data, 'admin');
    }

    /**
     * @description Renderizza il form di modifica/duplicazione di un esito per bando di concorso
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/contest/edit-result/:id.html
     * @method GET
     */
    public function editResult(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('update');

        //Validatore che verifica se l'elemento da modificare esiste
        $validator = new ContestValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/contest', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Allegati
        $attach = new AttachmentArchive();

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $result = Registry::get('contest');
        $result = !empty($result) ? $result->toArray() : [];

        $data = [];

        $archiveName = 'Bandi di Concorso';

        // Vedo in sessione se sono arrivato qui da un operazione di ripristino dal versioning
        $session = new Session();
        $restore = $session->getFlash('restore');
        $data['restore'] = $restore;

        $this->breadcrumb->push('Esito Bandi di Concorso', 'admin/contest');
        $this->breadcrumb->push('Modifica', '/');

        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Bandi di Concorso';
        $data['subTitleSection'] = 'GESTIONE DELLE PUBBLICAZIONI DI BANDI DI CONCORSO';
        $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';

        // Controllo se si sta duplicando un tasso di assenza
        $isDuplicate = uri()->segment(3, 0) === 'duplicate-result';

        // Setto il tipo di storage
        $data['_storageType'] = $isDuplicate ? 'duplicate' : 'update';
        $data['archiveName'] = $archiveName;
        //In base al tipo di storage setto l'action del form
        $data['formAction'] = ($isDuplicate) ? '/admin/contest/store-result' : '/admin/contest/update-result';
        $data['formSettings'] = [
            'name' => 'form_contest_result',
            'id' => 'form_contest_result',
            'class' => 'form_contest_result',
        ];

        $activationDate = convertDateToForm($result['activation_date']);

        $result['activation_date'] = $activationDate['date'].' '.$activationDate['hours'];
        $data['result'] = $result;

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'contest',
            $result['id']
        );

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = $result['institution_id'];
        $data['seo'] = $result['p_s_d_r'] ?? null;

        render('contest/form_store_result', $data, 'admin');
    }

    /**
     * @description Funzione che effettua l'update di un Bando di concorso
     *
     * @return void
     * @throws Exception
     * @url /admin/contest/update-contest.html
     * @method POST
     */
    public function updateContest(): void
    {

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new ContestValidator();
        $check = $validator->checkContest('update');

        if ($check['is_success']) {
            $doAction = true;

            $contestId = (int)strip_tags(Input::post('id'));

            // Recupero il bando di concorso attuale prima di modificarlo e lo salvo nel versioning
            $contest = ContestModel::where('id', $contestId)
                ->with('assignments:id,name,object')
                ->with(['office' => function ($query) {
                    $query->select(['id', 'structure_name']);
                }])
                ->with('all_attachs');

            $contest = $contest->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute(__FUNCTION__, (!checkRecordOwner($contest['owner_id']) && $this->acl->getCreate()));

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
                $data['typology'] = 'concorso';
                $data['object'] = strip_tags(Input::post('object', true));
                $data['province_office'] = setDefaultData(strip_tags(Input::post('province_office', true)), null, ['']);
                $data['city_office'] = strip_tags(Input::post('city_office', true));
                $data['office_address'] = strip_tags(Input::post('office_address', true));
                $data['object_structures_id'] = setDefaultData(strip_tags((int)Input::post('object_structures_id')), null, ['']);
                $data['object_measure_id'] = setDefaultData(strip_tags((int)Input::post('object_measure_id')), null, ['', null]);
                $data['test_calendar'] = Input::post('test_calendar', true);
                $data['evaluation_criteria'] = Input::post('evaluation_criteria', true);
                $data['traces_written_tests'] = Input::post('traces_written_tests', true);
                $data['related_contest_id'] = setDefaultData(strip_tags((int)Input::post('related_contest_id')), null, ['']);
                $data['activation_date'] = !empty(Input::post('activation_date')) ? strip_tags(Input::post('activation_date')) : null;
                $data['expiration_date'] = !empty(Input::post('expiration_date')) ? convertDateToDatabase(strip_tags(Input::post('expiration_date'))) : null;
                $data['expiration_contest_date'] = !empty(Input::post('expiration_contest_date')) ? convertDateToDatabase(strip_tags(Input::post('expiration_contest_date'))) : null;
                $data['expiration_time'] = strip_tags(Input::post('expiration_time', true));
                $data['hired_employees'] = setDefaultData(strip_tags((int)Input::post('hired_employees')), null, ['']);
                $data['expected_expenditure'] = !empty(Input::post('expected_expenditure')) ? toFloat(strip_tags(Input::post('expected_expenditure'))) : null;
                $data['expenditures_made'] = !empty(Input::post('expenditures_made')) ? toFloat(strip_tags(Input::post('expenditures_made'))) : null;
                $data['description'] = Input::post('description', true);

                // Update Bando di concorso
                ContestModel::where('id', $contestId)->updateWithLogs($contest, $data);

                // Svuoto le tabelle di relazione e le aggiorno
                $this->clear(
                    $contest,
                    !empty(Input::post('commissions')) ? explode(',', strip_tags((string)Input::post('commissions', true))) : null
                );

                // Upload allegati associati al personale.
                $attach->update(
                    'attach_files',
                    'contest',
                    $contestId,
                    $contest['institution_id'],
                    $contest['object']
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
     * @description Funzione che effettua l'update di un Avviso per Bando di concorso
     *
     * @return void
     * @throws Exception
     * @url /admin/contest/update-alert.html
     * @method POST
     */
    public function updateAlert(): void
    {

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new ContestValidator();
        $check = $validator->checkAlert('update');

        if ($check['is_success']) {
            $doAction = true;

            $alertId = (int)strip_tags(Input::post('id'));

            // Recupero il bando di concorso attuale prima di modificarlo e lo salvo nel versioning
            $alert = ContestModel::where('id', $alertId)
                ->with(['office' => function ($query) {
                    $query->select(['id', 'structure_name']);
                }])
                ->with('all_attachs');

            $alert = $alert->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute(__FUNCTION__, (!checkRecordOwner($alert['owner_id']) && $this->acl->getCreate()));

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
                $data['object'] = strip_tags(Input::post('object', true));
                $data['object_structures_id'] = setDefaultData(strip_tags((int)Input::post('object_structures_id')), null, ['']);
                $data['related_contest_id'] = setDefaultData(strip_tags((int)Input::post('related_contest_id')), null, ['']);
                $data['activation_date'] = !empty(Input::post('activation_date')) ? strip_tags(Input::post('activation_date')) : null;
                $data['expiration_date'] = !empty(Input::post('expiration_date')) ? convertDateToDatabase(strip_tags(Input::post('expiration_date'))) : null;
                $data['description'] = Input::post('description', true);

                // Update Bando di concorso
                ContestModel::where('id', $alertId)->updateWithLogs($alert, $data);

                // Upload allegati associati al personale.
                $attach->update(
                    'attach_files',
                    'contest',
                    $alertId,
                    $alert['institution_id'],
                    $alert['object']
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
     * @description Funzione che effettua l'update di un Esito per Bando di concorso
     *
     * @return void
     * @throws Exception
     * @url /admin/contest/update-result.html
     * @method POST
     */
    public function updateResult(): void
    {

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new ContestValidator();
        $check = $validator->checkResult('update');

        if ($check['is_success']) {
            $doAction = true;

            $resultId = (int)strip_tags(Input::post('id'));

            // Recupero il bando di concorso attuale prima di modificarlo e lo salvo nel versioning
            $result = ContestModel::where('id', $resultId)
                ->with(['office' => function ($query) {
                    $query->select(['id', 'structure_name']);
                }])
                ->with('all_attachs');

            $result = $result->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute(__FUNCTION__, (!checkRecordOwner($result['owner_id']) && $this->acl->getCreate()));

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
                $data['object'] = strip_tags(Input::post('object', true));
                $data['related_contest_id'] = setDefaultData(strip_tags((int)Input::post('related_contest_id')), null, ['']);
                $data['activation_date'] = !empty(Input::post('activation_date')) ? strip_tags(Input::post('activation_date')) : null;
                $data['description'] = Input::post('description', true);

                // Update Bando di concorso
                ContestModel::where('id', $resultId)->updateWithLogs($result, $data);

                // Upload allegati associati al personale.
                $attach->update(
                    'attach_files',
                    'contest',
                    $resultId,
                    $result['institution_id'],
                    $result['object']
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
     * @param ContestModel|null $contest     Bando di concorso
     * @param array|int|null    $assignments Incarichi associati al concorso
     * @return void
     */
    protected function clear(ContestModel $contest = null, array|int $assignments = null): void
    {
        $dataAssignments = [];
        if ($assignments !== null) {
            foreach ($assignments as $assignment) {
                $dataAssignments[] = is_array($assignment) ? $assignment['id'] : $assignment;
            }
        }
        //Insert/Update nella tabella di relazione
        $contest->assignments()->sync($dataAssignments);
    }

    /**
     * @description Funzione che effettua l'eliminazione di un Bando di concorso
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/contest/delete.html
     * @method GET
     */
    public function delete(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute([__FUNCTION__, 'create', 'update'], true);

        //Validatore che verifica se l'elemento da eliminare esiste
        $validator = new ContestValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getDelete() && $this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/contest', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Se la validazione va a buon fine recupero l'elemento da eliminare dal registro
        $contest = Registry::get('contest');

        //Elimino il bando do concorso settando deleted = 1
        $contest->deleteWithLogs($contest);

        sessionSetNotify(__('success_delete_operation', null, 'patos'));

        // Generazione nuovo token
        Token::forceRegenerate((int)config('csrf_expire', null, 'app'));

        redirect('admin/contest');
    }

    /**
     * @description Funzione per l'eliminazione multipla
     *
     * @return void
     * @throws Exception
     * @url /admin/contest/deletes.html
     * @method GET
     */
    public function deletes(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('delete');

        $validator = new ContestValidator();
        $validate = $validator->multipleSelection();

        if ($validate['is_success']) {

            sessionSetNotify('Operazione avvenuta con successo');

            //Recupero gli elementi da eliminare dal registro
            $contests = Registry::get('__ids__multi_select_profile__');

            //Estraggo gli array degli elementi da eliminare
            $ids = Arr::pluck($contests, 'id');

            //Elimino gli elementi
            foreach ($contests as $contest) {
                $contest->deleteWithLogs($contest);
            }

            // Generazione nuovo token
            Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        } else {

            sessionSetNotify($validate['errors'], 'danger');
        }

        redirect('/admin/contest');
    }
}
