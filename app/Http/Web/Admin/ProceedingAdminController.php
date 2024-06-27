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
use Helpers\Validators\ProceedingValidator;
use Model\DataMonitoringProceedings;
use Model\ProceedingsModel;
use Scope\DeletedScope;
use System\Arr;
use System\Input;
use System\JsonResponse;
use System\Registry;
use System\Session;
use System\Token;

/**
 *
 * Controller Procedimenti
 *
 */
class ProceedingAdminController extends BaseAuthController
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
     * @description Renderizza la pagina index dei Procedimenti
     *
     * @return void
     * @throws Exception
     * @url /admin/proceeding.html
     * @method GET
     */
    public function index(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['read', 'create']);
        $data = [];

        //Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $data['is_box'] = uri()->uriString() === 'admin/personnel/create-box';

        //Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {

            $this->breadcrumb->push('Procedimenti dell\'Ente', '/');
            $data['breadcrumbs'] = $this->breadcrumb->show();

            //Dati header della sezione
            $data['titleSection'] = 'Procedimenti dell\'Ente';
            $data['subTitleSection'] = 'GESTIONE DEI PROCEDIMENTI DELL\'ENTE';
            $data['sectionIcon'] = '<i class="fas fa-briefcase fa-3x"></i>';
        }

        //Setto dati del form
        $data['formAction'] = '/admin/proceeding';
        $data['formSettings'] = [
            'name' => 'form_proceeding',
            'id' => 'form_proceeding',
            'class' => 'form_proceeding',
        ];

        $data['include'] = '';

        $data['url'] = uri()->segment(2, 0);

        render('proceeding/index', $data, 'admin');
    }

    /**
     * @description Funzione per la paginazione dei dati nel datatable
     *
     * @return void
     * @url /admin/proceeding/list.html
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
                1 => 'name',
                4 => 'userName',
                5 => 'updated_at'
            ];

            //Se l'utente è super Admin mostro la colonna dell'ente
            if (isSuperAdmin(true)) {
                $orderable[4] = 'i.full_name_institution';
            }

            ///Setto proprietà datatable
            $dataTable = [];
            setDataTableData($dataTable, 'name');

            $totalRecords = ProceedingsModel::select('count(id) as allcount')
                ->count();

            $totalRecordsWithFilter = ProceedingsModel::search($dataTable['searchValue'])
                ->select(['count(id) as allcount'])
                ->join('users', 'users.id', '=', 'object_proceedings.owner_id', 'left outer');

            if (!empty($dataTable['searchValue'])) {
                $totalRecordsWithFilter->orWhere('object_proceedings.id', '=', $dataTable['searchValue']);
            }

            $totalRecordsWithFilter = $totalRecordsWithFilter->count();

            $order = setOrderDatatable($dataTable['columnName'], $orderable, 'name');

            $records = ProceedingsModel::search($dataTable['searchValue'])
                ->select(['object_proceedings.id', 'object_proceedings.institution_id', 'object_proceedings.owner_id', 'object_proceedings.name',
                    'object_proceedings.updated_at', 'archived', 'publishing_status', 'users.name as userName', 'i.full_name_institution'])
                ->with('responsibles:id,full_name,archived')
                ->with('offices_responsibles:id,structure_name,archived')
                ->with(['created_by' => function ($query) {
                    $query->withoutGlobalScopes([DeletedScope::class]);
                    $query->select(['id', 'name', 'deleted']);
                }])
                ->with('institution:id,full_name_institution')
                ->join('institutions as i', 'object_proceedings.institution_id', '=', 'i.id', 'left outer')
                ->join('users', 'users.id', '=', 'object_proceedings.owner_id', 'left outer');

            if (!empty($dataTable['searchValue'])) {
                $records->orWhere('object_proceedings.id', '=', $dataTable['searchValue']);
            }

            $records = $records->orderBy($order, $dataTable['columnSortOrder'])
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

                    if (!empty($record['responsibles']) && is_array($record['responsibles'])) {

                        $tmpResponsibles = Arr::pluck($record['responsibles'], 'full_name');
                        $tmpArchived = Arr::pluck($record['responsibles'], 'archived');
                        $responsibles = str_replace(',', ',' . nbs(2), implode(
                            ',',
                            array_map(
                                function ($personnel) {
                                    return ('<small class="badge-primary mb-1" style="margin-bottom: 2px;">'
                                        . escapeXss($personnel) . '</small>');
                                },
                                $tmpResponsibles,
                                $tmpArchived
                            )
                        ));
                    } else {

                        $responsibles = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definiti">N.D.</small>';
                    }

                    if (!empty($record['offices_responsibles']) && is_array($record['offices_responsibles'])) {

                        $tmpOfficeResponsibles = Arr::pluck($record['offices_responsibles'], 'structure_name');
                        $tmpArchivedStr = Arr::pluck($record['offices_responsibles'], 'archived');
                        $officeResponsibles = str_replace(',', ',' . nbs(2), implode(
                            ',',
                            array_map(
                                function ($office) {
                                    return ('<small class="badge-primary mb-1" style="margin-bottom: 2px;">'
                                        . escapeXss($office) . '</small>');
                                },
                                $tmpOfficeResponsibles,
                                $tmpArchivedStr
                            )
                        ));
                    } else {

                        $officeResponsibles = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definiti">N.D.</small>';
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
                        'duplicate' => $this->acl->getCreate() && !$record['archived'],
                        'delete' => $this->acl->getDelete() || $updatePermits || $permits,
                    ])
                        ->addEdit('admin/proceeding/edit/' . $record['id'], $record['id'])
                        ->addDuplicate('admin/proceeding/duplicate/' . $record['id'], $record['id'])
                        ->addDelete('admin/proceeding/delete/' . $record['id'], $record['id'])
                        ->render() : '';

                    $icon = null;

                    //Setto i dati da mostrare nelle colonne del datatable
                    $setTempData = [];
                    $setTempData[] = ($this->acl->getCrud() || checkRecordOwner($record['owner_id'])) ? ButtonAction::checkList('item[]', $record['id']) : '';
                    $setTempData[] = !empty($record['name'])
                        ? $icon . '<a href="' . siteUrl('/page/98/details/' . $record['id'] . '/' . urlTitle($record['name'])) . '" target="_blank">' . escapeXss($record['name']) . '</a>'
                        : 'N.D.';
                    $setTempData[] = $officeResponsibles;
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
     * @description Renderizza il form di creazione di un nuovo Procedimento
     *
     * @return void
     * @throws Exception
     * @url /admin/proceeding/create.html
     * @method GET
     */
    public function create(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(__FUNCTION__);

        $data = [];

        //Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $data['is_box'] = uri()->uriString() === 'admin/proceeding/create-box';

        //Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {

            $this->breadcrumb->push('Procedimenti dell\'Ente', 'admin/proceeding');
            $this->breadcrumb->push('Nuovo', '/');
            $data['breadcrumbs'] = $this->breadcrumb->show();

            //Dati header della sezione
            $data['titleSection'] = 'Procedimenti dell\'Ente';
            $data['subTitleSection'] = 'GESTIONE DEI PROCEDIMENTI DELL\'ENTE';
            $data['sectionIcon'] = '<i class="fas fa-briefcase fa-3x"></i>';
        }

        //Setto dati del form
        $data['formAction'] = '/admin/proceeding/store';
        $data['formSettings'] = [
            'name' => 'form_proceeding',
            'id' => 'form_proceeding',
            'class' => 'form_proceeding',
        ];
        $data['_storageType'] = 'insert';


        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        render('proceeding/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua lo storage di un nuovo Procedimento
     *
     * @return void
     * @throws Exception
     * @url /admin/proceeding/store.html
     * @method POST
     */
    public function store(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new ProceedingValidator();
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
                    'name' => strip_tags(Input::post('name', true)),
                    'contact' => setDefaultData(strip_tags(Input::post('contact', true)), null, ['']),
                    'description' => Input::post('description', true),
                    'costs' => Input::post('costs', true),
                    'silence_consent' => setDefaultData(strip_tags(Input::post('silence_consent', true)), null, ['']),
                    'declaration' => setDefaultData(strip_tags(Input::post('declaration', true)), null, ['']),
                    'regulation' => Input::post('regulation', true),
                    'deadline' => strip_tags(Input::post('deadline', true)),
                    'protection_instruments' => strip_tags(Input::post('protection_instruments', true)),
                    'service_available' => setDefaultData(strip_tags(Input::post('service_available', true)), null, ['', null]),
                    'public_monitoring_proceeding' => setDefaultData(Input::post('public_monitoring_proceeding', true), 0, ['', null]),
                    'url_service' => !empty(Input::post('service_available')) ? setDefaultData(strip_tags(Input::post('url_service', true)), null, ['', null]) : null,
                    'service_time' => empty(Input::post('service_available')) ? setDefaultData(strip_tags(Input::post('service_time', true))) : null,
                ];

                // Storage nuovo Procedimento
                $insert = ProceedingsModel::createWithLogs($arrayValues);

                if (Input::post('_monitoring', true)) {
                    // Insert dei dati del monitoraggio
                    $proceedingMonitoringData = objectToArray(json_decode(Input::post('_monitoring', true)));
                    $this->monitoringData($insert->id, $proceedingMonitoringData);
                }

                // Svuoto le tabelle di relazione e le aggiorno
                $this->clear(
                    $insert,
                    !empty(Input::post('responsibles')) ? explode(',', strip_tags((string)Input::post('responsibles', true))) : null,
                    !empty(Input::post('measure_responsibles')) ? explode(',', strip_tags((string)Input::post('measure_responsibles', true))) : null,
                    !empty(Input::post('substitute_responsibles')) ? explode(',', strip_tags((string)Input::post('substitute_responsibles', true))) : null,
                    !empty(Input::post('offices_responsibles')) ? explode(',', strip_tags((string)Input::post('offices_responsibles', true))) : null,
                    !empty(Input::post('to_contacts')) ? explode(',', strip_tags((string)Input::post('to_contacts', true))) : null,
                    !empty(Input::post('other_offices')) ? explode(',', strip_tags((string)Input::post('other_offices', true))) : null,
                    Input::post('normatives', true),
                );

                // Storage allegati associati al personale.
                $attach->storage('attach_files', 'proceedings', $insert->id, $insert['name']);

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
     * @description Renderizza il form di modifica/duplicazione di un Procedimento
     *
     * @return void
     * @throws Exception
     * @url /admin/proceeding/edit/:id.html
     * @method GET
     */
    public function edit(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['update', 'create']);

        //Validatore che verifica se l'elemento da modificare esiste
        $validator = new ProceedingValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/proceeding', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Allegati
        $attach = new AttachmentArchive();

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $proceeding = Registry::get('proceeding');
        $proceeding = !empty($proceeding) ? $proceeding->toArray() : null;

        // Vedo in sessione se sono arrivato qui da un operazione di ripristino dal versioning
        $session = new Session();
        $restore = $session->getFlash('restore');

        $this->breadcrumb->push('Procedimenti dell\'Ente', 'admin/proceeding');
        $this->breadcrumb->push('Modifica', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Procedimenti dell\'Ente';
        $data['subTitleSection'] = 'GESTIONE DEI PROCEDIMENTI DELL\'ENTE';
        $data['sectionIcon'] = '<i class="fas fa-briefcase fa-3x"></i>';

        // Controllo se si sta duplicando un tasso di assenza
        $isDuplicate = uri()->segment(3, 0) === 'duplicate';

        // Setto il tipo di storage
        $data['_storageType'] = $isDuplicate ? 'duplicate' : 'update';

        //In base al tipo di storage setto l'action del form
        $data['formAction'] = ($isDuplicate) ? '/admin/proceeding/store' : '/admin/proceeding/update';
        $data['formSettings'] = [
            'name' => 'form_proceeding',
            'id' => 'form_proceeding',
            'class' => 'form_proceeding',
        ];

        $data['proceeding'] = $proceeding;

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'proceedings',
            $proceeding['id'],
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

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = $proceeding['institution_id'];

        // Utilizzata per la creazione di un procedimento all'interno di un altro form
        $data['is_box'] = false;

        $data['responsibleIds'] = Arr::pluck($proceeding['responsibles'], 'id');
        $data['measureResponsibleIds'] = Arr::pluck($proceeding['measure_responsibles'], 'id');
        $data['substituteResponsibleIds'] = Arr::pluck($proceeding['substitute_responsibles'], 'id');
        $data['officesResponsibleIds'] = Arr::pluck($proceeding['offices_responsibles'], 'id');
        $data['toContactIds'] = Arr::pluck($proceeding['to_contacts'], 'id');
        $data['otherOfficeIds'] = Arr::pluck($proceeding['other_structures'], 'id');
        $data['normativeIds'] = Arr::pluck($proceeding['normatives'], 'id');
        $data['restore'] = $restore;

        $data['seo'] = $proceeding['p_s_d_r'] ?? null;

        render('proceeding/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua l'update di un Procedimento
     *
     * @return void
     * @throws Exception
     * @url /admin/proceeding/update.html
     * @method POST
     */
    public function update(): void
    {
        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new ProceedingValidator();
        $check = $validator->check('update');

        if ($check['is_success']) {
            $doAction = true;

            $proceedingId = (int)strip_tags(Input::post('id', true));

            $proceeding = ProceedingsModel::where('id', $proceedingId)
                ->with('responsibles:id,full_name')
                ->with('measure_responsibles:id,full_name')
                ->with('substitute_responsibles:id,full_name')
                ->with('to_contacts:id,full_name')
                ->with(['offices_responsibles' => function ($query) {
                    $query->select([
                        'object_structures.id',
                        'object_structures.structure_name as structure_name', 'object_structures.phone as structure_phone', 'object_structures.reference_email as structure_email',
                        'belong.structure_name as belong_name', 'belong.phone as belong_phone', 'belong.reference_email as belong_email'
                    ])
                        ->join('object_structures as belong', 'belong.id', '=', 'object_structures.structure_of_belonging_id', 'left outer');
                }])
                ->with('other_structures:id,structure_name')
                ->with('normatives:id,name')
                ->with('all_attachs');

            $proceeding = $proceeding->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute(__FUNCTION__, (!checkRecordOwner($proceeding['owner_id']) && $this->acl->getCreate()));

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
                $data['contact'] = setDefaultData(strip_tags(Input::post('contact', true)), null, ['']);
                $data['description'] = Input::post('description', true);
                $data['costs'] = Input::post('costs', true);
                $data['silence_consent'] = setDefaultData(strip_tags(Input::post('silence_consent', true)), null, ['']);
                $data['declaration'] = setDefaultData(strip_tags(Input::post('declaration', true)), null, ['']);
                $data['regulation'] = Input::post('regulation', true);
                $data['deadline'] = strip_tags(Input::post('deadline', true));
                $data['protection_instruments'] = strip_tags(Input::post('protection_instruments', true));
                $data['service_available'] = setDefaultData(strip_tags((string)Input::post('service_available', true)), null, ['', null]);
                $data['public_monitoring_proceeding'] = !empty(Input::post('public_monitoring_proceeding')) ? setDefaultData(strip_tags(Input::post('public_monitoring_proceeding', true)), 0, ['', null]) : null;
                $data['url_service'] = !empty(Input::post('service_available')) ? setDefaultData(strip_tags(Input::post('url_service', true)), null, ['', null]) : null;
                $data['service_time'] = empty(Input::post('service_available')) ? setDefaultData(strip_tags(Input::post('service_time', true)), null, ['', null]) : null;

                // Update Procedimento
                ProceedingsModel::where('id', $proceedingId)->updateWithLogs($proceeding, $data);

                if (Input::post('_monitoring', true)) {
                    // Insert e update dei dati del monitoraggio
                    $proceedingMonitoringData = objectToArray(json_decode(Input::post('_monitoring', true)));
                    $this->monitoringData($proceedingId, $proceedingMonitoringData);
                }

                // Svuoto le tabelle di relazione e le aggiorno
                $this->clear(
                    $proceeding,
                    !empty(Input::post('responsibles')) ? explode(',', strip_tags((string)Input::post('responsibles', true))) : null,
                    !empty(Input::post('measure_responsibles')) ? explode(',', strip_tags((string)Input::post('measure_responsibles', true))) : null,
                    !empty(Input::post('substitute_responsibles')) ? explode(',', strip_tags((string)Input::post('substitute_responsibles', true))) : null,
                    !empty(Input::post('offices_responsibles')) ? explode(',', strip_tags((string)Input::post('offices_responsibles', true))) : null,
                    !empty(Input::post('to_contacts')) ? explode(',', strip_tags((string)Input::post('to_contacts', true))) : null,
                    !empty(Input::post('other_offices')) ? explode(',', strip_tags((string)Input::post('other_offices', true))) : null,
                    Input::post('normatives', true)
                );

                // Upload allegati associati al personale.
                $attach->update(
                    'attach_files',
                    'proceedings',
                    $proceedingId,
                    $proceeding['institution_id'],
                    $proceeding['name']
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
     * @description Metodo per l'update e l'insert dei dati del monitoraggio dei procedimenti
     *
     * @param int|null       $proceedingId             {id del procedimento}
     * @param array|int|null $proceedingMonitoringData {dati del monitoraggio}
     * @return void
     */
    private function monitoringData(int $proceedingId = null, array|int $proceedingMonitoringData = null): void
    {
        DataMonitoringProceedings::where('proceeding_id', $proceedingId)
            ->delete();

        foreach ($proceedingMonitoringData as $monitoring) {

            $monitoringData = [
                'proceeding_id' => strip_tags(escapeXss($proceedingId)),
                'year' => strip_tags(escapeXss($monitoring['year'])),
                'year_concluded_proceedings' => strip_tags(escapeXss($monitoring['year_concluded_proceedings'])),
                'conclusion_days' => strip_tags(escapeXss($monitoring['conclusion_days'])),
                'percentage_year_concluded_proceedings' => strip_tags(escapeXss($monitoring['percentage_year_concluded_proceedings'])),
            ];

            DataMonitoringProceedings::create($monitoringData);
        }
    }

    /**
     * @description Metodo per lo storage nelle tabelle di relazione
     * In caso di update, svuota prima le tabelle di relazione e poi inserisce i dati aggiornati
     *
     * @param ProceedingsModel|null $proceeding             Procedimento di cui si devono salvare le relazioni
     * @param array|int|null        $responsibles           Personale associato al procedimento (responsabile del procedimento)
     * @param array|int|null        $measureResponsibles    Personale associato al procedimento (responsabile del provvedimento)
     * @param array|int|null        $substituteResponsibles Personale associato al procedimento (responsabile sostitutivo)
     * @param array|int|null        $officesResponsibles    Strutture associate al procedimento (uffici responsabili)
     * @param array|int|null        $toContacts             Personale associato al procedimento (chi contattare)
     * @param array|int|null        $otherOffices           Strutture associate al procedimento (altre strutture)
     * @param array|int|null        $normatives             Normative associate al procedimento
     * @return void
     */
    protected function clear(ProceedingsModel $proceeding = null, array|int $responsibles = null, array|int $measureResponsibles = null, array|int $substituteResponsibles = null,
                             array|int        $officesResponsibles = null, array|int $toContacts = null, array|int $otherOffices = null, array|int $normatives = null): void
    {
        $dataResponsibles = [];
        if (!empty($responsibles)) {
            foreach ($responsibles as $responsible) {
                $dataResponsibles[] = is_array($responsible) ? $responsible['id'] : $responsible;
            }
        }
        //Insert/Update nella tabella di relazione
        $proceeding->responsibles()->syncWithPivotValues($dataResponsibles, ['typology' => 'responsible']);

        $dataMeasureResponsibles = [];
        if (!empty($measureResponsibles)) {
            foreach ($measureResponsibles as $measureResponsible) {
                $dataMeasureResponsibles[] = is_array($measureResponsible) ? $measureResponsible['id'] : $measureResponsible;
            }
        }
        //Insert/Update nella tabella di relazione
        $proceeding->measure_responsibles()->syncWithPivotValues($dataMeasureResponsibles, ['typology' => 'measure-responsible']);

        $dataSubstituteResponsible = [];
        if (!empty($substituteResponsibles)) {
            foreach ($substituteResponsibles as $substituteResponsible) {
                $dataSubstituteResponsible[] = is_array($substituteResponsible) ? $substituteResponsible['id'] : $substituteResponsible;
            }
        }
        //Insert/Update nella tabella di relazione
        $proceeding->substitute_responsibles()->syncWithPivotValues($dataSubstituteResponsible, ['typology' => 'substitute-responsible']);

        $dataOfficesResponsible = [];
        if (!empty($officesResponsibles)) {
            foreach ($officesResponsibles as $officesResponsible) {
                $dataOfficesResponsible[] = is_array($officesResponsible) ? $officesResponsible['id'] : $officesResponsible;
            }
        }
        //Insert/Update nella tabella di relazione
        $proceeding->offices_responsibles()->syncWithPivotValues($dataOfficesResponsible, ['typology' => 'office-responsible']);

        $dataToContact = [];
        if (!empty($toContacts)) {
            foreach ($toContacts as $toContact) {
                $dataToContact[] = is_array($toContact) ? $toContact['id'] : $toContact;
            }
        }
        //Insert/Update nella tabella di relazione
        $proceeding->to_contacts()->syncWithPivotValues($dataToContact, ['typology' => 'to-contact']);

        $dataStructure = [];
        if (!empty($otherOffices)) {
            foreach ($otherOffices as $structure) {
                $dataStructure[] = is_array($structure) ? $structure['id'] : $structure;
            }
        }
        //Insert/Update nella tabella di relazione
        $proceeding->other_structures()->syncWithPivotValues($dataStructure, ['typology' => 'other-structure']);

        $dataNormative = [];
        if (!empty($normatives)) {
            foreach ($normatives as $normative) {
                $dataNormative[] = is_array($normative) ? strip_tags($normative['id']) : strip_tags($normative);
            }
        }
        //Insert/Update nella tabella di relazione
        $proceeding->normatives()->sync($dataNormative);
    }

    /**
     * @description Funzione che effettua l'eliminazione di un Procedimento
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/proceeding/delete.html
     * @method GET
     */
    public function delete(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute([__FUNCTION__, 'create', 'update'], true);
        //Validatore che verifica se l'elemento da eliminare esiste
        $validator = new ProceedingValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getDelete() && $this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/proceeding', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Se la validazione va a buon fine recupero l'elemento da eliminare dal registro
        $proceeding = Registry::get('proceeding');

        //Elimino il procedimento
        $proceeding->deleteWithLogs($proceeding);

        sessionSetNotify(__('success_delete_operation', null, 'patos'));
        // Generazione nuovo token
        Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        redirect('admin/proceeding');
    }

    /**
     * @description Funzione per l'eliminazione multipla
     *
     * @return void
     * @method GET
     * @throws Exception
     * @url /admin/proceeding/deletes.html
     */
    public function deletes(): void
    {
        //Setto il metodo della rotta
        $this->acl->setRoute('delete');

        $validator = new ProceedingValidator();
        $validate = $validator->multipleSelection();

        if ($validate['is_success']) {

            sessionSetNotify('Operazione avvenuta con successo');

            //Recupero gli elementi da eliminare dal registro
            $proceedings = Registry::get('__ids__multi_select_profile__');

            //Estraggo gli array degli elementi da eliminare
            $ids = Arr::pluck($proceedings, 'id');

            //Elimino gli elementi
            foreach ($proceedings as $proceeding) {
                $proceeding->deleteWithLogs($proceeding);
            }

            // Generazione nuovo token
            Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        } else {

            sessionSetNotify($validate['errors'], 'danger');
        }

        redirect('/admin/proceeding');
    }
}
