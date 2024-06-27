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
use Helpers\Validators\ReliefCheckValidator;
use Model\ReliefChecksModel;
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
 * Controller Controlli e rilievi
 *
 */
class ReliefCheckAdminController extends BaseAuthController
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
     * @description Renderizza la pagina index dei Controlli e Rilievi
     *
     * @return void
     * @throws Exception
     * @url /admin/relief-check.html
     * @method GET
     */
    public function index(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['read', 'create']);

        $this->breadcrumb->push('Controlli e rilievi', '/');
        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Controlli e rilievi';
        $data['subTitleSection'] = 'GESTIONE DEI CONTROLLI E RILIEVI SULL\'AMMINISTRAZIONE';
        $data['sectionIcon'] = '<i class="fas fa-tasks fa-3x"></i>';

        $data['formAction'] = '/admin/relief-check';
        $data['formSettings'] = [
            'name' => 'form_relief_check',
            'id' => 'form_relief_check',
            'class' => 'form_relief_check',
        ];

        $data['include'] = '';

        $data['url'] = uri()->segment(2, 0);


        render('relief_check/index', $data, 'admin');
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
                1 => 'object',
                2 => 'date',
                3 => 'users.name',
                4 => 'updated_at'
            ];

            //Se l'utente è super Admin mostro la colonna dell'ente
            if (isSuperAdmin(true)) {
                $orderable[5] = 'i.full_name_institution';
            }

            //Setto proprietà datatable
            $dataTable = [];
            setDataTableData($dataTable, 'object');

            //Query per i dati da mostrare nel datatable
            $totalRecords = ReliefChecksModel::select('count(id) as allcount')
                ->count();

            $totalRecordsWithFilter = ReliefChecksModel::search($dataTable['searchValue'])
                ->select(['count(object_relief_checks.id) as allcount'])
                ->join('users', 'users.id', '=', 'object_relief_checks.owner_id', 'left outer');
            if (!empty($dataTable['searchValue'])) {
                $totalRecordsWithFilter->orWhere('object_relief_checks.id', '=', $dataTable['searchValue']);
            }
            $totalRecordsWithFilter = $totalRecordsWithFilter->count();

            $order = setOrderDatatable($dataTable['columnName'], $orderable, 'object');

            $records = ReliefChecksModel::search($dataTable['searchValue'])
                ->select(['object_relief_checks.id', 'object_relief_checks.owner_id', 'object_relief_checks.institution_id', 'object_structures_id',
                    'object', 'date', 'publishing_status', 'object_relief_checks.updated_at', 'users.name', 'i.full_name_institution'])
                ->with(['created_by' => function ($query) {
                    $query->withoutGlobalScopes([DeletedScope::class]);
                    $query->select(['id', 'name', 'deleted']);
                }])
                ->with('institution:id,full_name_institution')
                ->join('institutions as i', 'object_relief_checks.institution_id', '=', 'i.id', 'left outer')
                ->join('users', 'users.id', '=', 'object_relief_checks.owner_id', 'left outer');

            if (!empty($dataTable['searchValue'])) {
                $records->orWhere('object_relief_checks.id', '=', $dataTable['searchValue']);
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

                    $reliefDate = !empty($record['date'])
                        ? ('<small class="badge badge-info"> 
                            <i class="far fa-calendar-alt"></i> ' .
                            date('d-m-Y', strtotime($record['date'])) .
                            '</small>')
                        : '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definita">N.D.</small>';

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
                        ->addEdit('admin/relief-check/edit/' . $record['id'], $record['id'])
                        ->addDuplicate('admin/relief-check/duplicate/' . $record['id'], $record['id'])
                        ->addDelete('admin/relief-check/delete/' . $record['id'], $record['id'])
                        ->render() : '';

                    $icon = null;

                    //Setto i dati da mostrare nelle colonne del datatable
                    $setTempData = [];
                    $setTempData[] = ($this->acl->getCrud() || checkRecordOwner($record['owner_id'])) ? ButtonAction::checkList('item[]', $record['id']) : '';
                    $setTempData[] = $icon . (!empty($record['object'])
                            ? '<a href="' . siteUrl('/page/14/details/' . $record['id'] . '/' . urlTitle($record['object'])) . '" target="_blank">' . escapeXss($record['object']) . '</a>'
                            : 'N.D.');
                    $setTempData[] = $reliefDate;
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
     * @description Renderizza il form di creazione di un nuovo controllo
     *
     * @return void
     * @throws Exception
     * @url /admin/relief-check/create.html
     * @method GET
     */
    public function create(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(__FUNCTION__);

        $this->breadcrumb->push('Controlli e rilievi', 'admin/relief-check');
        $this->breadcrumb->push('Nuovo', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Controlli e rilievi';
        $data['subTitleSection'] = 'GESTIONE DEI CONTROLLI E RILIEVI SULL\'AMMINISTRAZIONE';
        $data['sectionIcon'] = '<i class="fas fa-tasks fa-3x"></i>';

        $data['formAction'] = '/admin/relief-check/store';
        $data['formSettings'] = [
            'name' => 'form_relief_check',
            'id' => 'form_relief_check',
            'class' => 'form_relief_check',
        ];
        $data['_storageType'] = 'insert';

        // Recupero i ruoli per il personale in base alla tipologia dell'ente
        $institutionTypeId = (int)patOsInstituteInfo(['institution_type_id'])['institution_type_id'];

        // Labels
        $this->setPublicInData($data, $institutionTypeId);

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        render('relief_check/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua lo storage di un nuovo Controllo
     *
     * @return void
     * @throws Exception
     * @url /admin/relief-check/store.html
     * @method POST
     */
    public function store(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $code = $json->success();

        //Validatore form
        $validator = new ReliefCheckValidator();
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

                $arraValues = [
                    'owner_id' => $getIdentity['id'],
                    'institution_id' => checkAlternativeInstitutionId(),
                    'object' => strip_tags((string)Input::post('object', true)),
                    'date' => !empty(Input::post('date')) ? convertDateToDatabase(strip_tags((string)Input::post('date', true))) : null,
                    'object_structures_id' => setDefaultData(strip_tags((int)Input::post('object_structures_id', true)), null, ['']),
                    'description' => Input::post('description', true)
                ];

                // Storage nuovo Controllo
                $insert = ReliefChecksModel::createWithLogs($arraValues);

                // Svuoto le tabelle di relazione e le aggiorno
                $this->clear(
                    $insert,
                    Input::post('public_in', true)
                );

                // Storage allegati associati ai controlli e rilievi.
                $attach->storage('attach_files', 'relief_checks', $insert->id, $insert['object']);

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
     * @description Renderizza il form di modifica/duplicazione di un controllo
     *
     * @return void
     * @throws Exception
     * @url /admin/relief-check/edit/:id.html
     * @method GET
     */
    public function edit(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['update', 'create']);

        //Validatore che verifica se l'elemento da modificare esiste
        $validator = new ReliefCheckValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/relief-check', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Allegati
        $attach = new AttachmentArchive();

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $reliefCheck = Registry::get('relief_check');
        $reliefCheck = !empty($reliefCheck) ? $reliefCheck->toArray() : [];

        $this->breadcrumb->push('Controlli e rilievi', 'admin/relief-check');
        $this->breadcrumb->push('Modifica', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Controlli e rilievi';
        $data['subTitleSection'] = 'GESTIONE DEI CONTROLLI E RILIEVI SULL\'AMMINISTRAZIONE';
        $data['sectionIcon'] = '<i class="fas fa-tasks fa-3x"></i>';

        // Vedo in sessione se sono arrivato qui da un operazione di ripristino dal versioning
        $session = new Session();
        $restore = $session->getFlash('restore');
        $data['restore'] = $restore;

        // Controllo se si sta duplicando un tasso di assenza
        $isDuplicate = uri()->segment(3, 0) === 'duplicate';

        // Setto il tipo di storage
        $data['_storageType'] = $isDuplicate ? 'duplicate' : 'update';

        //In base al tipo di storage setto l'action del form
        $data['formAction'] = ($isDuplicate) ? '/admin/relief-check/store' : '/admin/relief-check/update';
        $data['formSettings'] = [
            'name' => 'form_relief_check',
            'id' => 'form_relief_check',
            'class' => 'form_relief_check',
        ];

        $date = convertDateToForm($reliefCheck['date']);

        $data['relief_check'] = $reliefCheck;

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'relief_checks',
            $reliefCheck['id'],
            [
                'id',
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

        // Recupero i ruoli per il personale in base alla tipologia dell'ente
        $institutionTypeId = patOsInstituteInfo(['institution_type_id'])['institution_type_id'];

        // Labels
        $this->setPublicInData($data, $institutionTypeId);

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = $reliefCheck['institution_id'];
        $data['publicInIDs'] = Arr::pluck($reliefCheck['public_in'], 'section_fo_id');

        $data['date'] = $date['date'];

        $data['seo'] = $reliefCheck['p_s_d_r'] ?? null;

        render('relief_check/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua l'update di un Controllo
     *
     * @return void
     * @throws Exception
     * @url /admin/relief-check/update.html
     * @method POST
     */
    public function update(): void
    {

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new ReliefCheckValidator();
        $check = $validator->check('update');

        if ($check['is_success']) {
            $doAction = true;

            $reliefCheckId = (int)strip_tags(Input::post('id', true));

            // Recupero il controllo attuale prima di modificarlo e lo salvo nel versioning
            $reliefCheck = ReliefChecksModel::where('id', $reliefCheckId)
                ->with('office:id,structure_name')
                ->with(['public_in' => function ($query) {
                    $query->select(['section_fo_id', 'section_fo_config_publication_archive.id', 'section_fo.name'])
                        ->join('section_fo', 'section_fo.id', '=', 'section_fo_id');
                }])
                ->with('all_attachs');

            $reliefCheck = $reliefCheck->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute(__FUNCTION__, (!checkRecordOwner($reliefCheck['owner_id']) && $this->acl->getCreate()));

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
                $data['date'] = !empty(Input::post('date')) ? convertDateToDatabase(strip_tags(Input::post('date', true))) : null;
                $data['object_structures_id'] = setDefaultData(strip_tags(Input::post('object_structures_id', true)), null, ['']);
                $data['description'] = Input::post('description', true);

                // Update Controllo
                ReliefChecksModel::where('id', $reliefCheckId)->updateWithLogs($reliefCheck, $data);

                // Svuoto le tabelle di relazione e le aggiorno
                $this->clear(
                    $reliefCheck,
                    Input::post('public_in', true)
                );

                // Upload allegati associati al personale
                $attach->update(
                    'attach_files',
                    'relief_checks',
                    $reliefCheckId,
                    $reliefCheck['institution_id'],
                    $reliefCheck['object']
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
     * @param ReliefChecksModel|null $reliefCheck Controllo/Rilievo
     * @param array|int|null         $publicIn    Sezioni per il pubblica in
     * @return void
     */
    protected function clear(ReliefChecksModel $reliefCheck = null, array|int $publicIn = null): void
    {
        $dataPublicIn = [];
        if ($publicIn !== null) {
            foreach ($publicIn as $in) {
                $dataPublicIn[] = is_array($in) ? $in['section_fo_id'] : $in;
            }
        }
        //Insert/Update nella tabella di relazione
        $reliefCheck->public_in()->sync($dataPublicIn);
    }

    /**
     * @description Funzione che effettua l'eliminazione di un Controllo
     *
     * @return void
     * @throws Exception
     * @url /admin/relief-check/delete/:id.html
     * @method GET
     */
    public function delete(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute([__FUNCTION__, 'create', 'update'], true);

        //Validatore che verifica se l'elemento da eliminare esiste
        $validator = new ReliefCheckValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getDelete() && $this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/relief-check', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Se la validazione va a buon fine recupero l'elemento da eliminare dal registro
        $reliefCheck = Registry::get('relief_check');

        //Elimino il controllo
        $reliefCheck->deleteWithLogs($reliefCheck);

        // Generazione nuovo token
        Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        sessionSetNotify(__('success_delete_operation', null, 'patos'));

        redirect('admin/relief-check');
    }

    /**
     * @description Funzione per l'eliminzaione multipla
     *
     * @return void
     * @throws Exception
     * @url /admin/relief-check/deletes.html
     * @method GET
     */
    public function deletes(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('delete');

        $validator = new ReliefCheckValidator();
        $validate = $validator->multipleSelection();

        if ($validate['is_success']) {

            sessionSetNotify('Operazione avvenuta con successo');

            //Recupero gli elementi da eliminare dal registro
            $reliefChecks = Registry::get('__ids__multi_select_profile__');

            //Elimino gli elementi
            foreach ($reliefChecks as $reliefCheck) {
                $reliefCheck->deleteWithLogs($reliefCheck);
            }

            // Generazione nuovo token
            Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        } else {

            sessionSetNotify($validate['errors'], 'danger');
        }

        redirect('/admin/relief-check');
    }

    /**
     * @description Funzione che setta i dati per il campo "Pubblica In"
     * @param array $data              Array di dati da passare alla vista
     * @param int   $institutionTypeId Id tipo ente
     * @return void
     */
    private function setPublicInData(array &$data, int $institutionTypeId): void
    {
        $data['labels'] = [];

        $publicIn = SectionFoConfigPublicationArchive::where('archive_name', '=', 'object_relief_checks')
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
    }
}
