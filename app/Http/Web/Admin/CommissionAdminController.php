<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Admin;

use Exception;
use Helpers\FileSystem\File;
use Helpers\Utility\AttachmentArchive;
use Helpers\Utility\ButtonAction;
use Helpers\Validators\CommissionValidator;
use Helpers\Validators\DatatableValidator;
use Model\CommissionsModel;
use Scope\DeletedScope;
use System\Arr;
use System\Input;
use System\JsonResponse;
use System\Registry;
use System\Session;
use System\Token;
use System\Uploads;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 *
 * Controller Commissioni e gruppi consiliari
 *
 */
class CommissionAdminController extends BaseAuthController
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
     * @description Renderizza la pagina index per le Commissioni
     *
     * @return void
     * @throws Exception
     * @url /admin/commission.html
     * @method GET
     */
    public function index(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['read', 'create']);

        $data = [];

        // Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $data['is_box'] = uri()->uriString() === 'admin/commission/create-box';

        //Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {

            $this->breadcrumb->push('Commissioni e gruppi consiliari', '/');
            $data['breadcrumbs'] = $this->breadcrumb->show();

            //Dati header della sezione
            $data['titleSection'] = 'Commissioni e gruppi consiliari';
            $data['subTitleSection'] = 'GESTIONE DEGLI ELEMENTI DELL\'ARCHIVIO';
            $data['sectionIcon'] = '<i class="fas fa-users fa-3x"></i>';
        }

        //Setto dati del form
        $data['formAction'] = '/admin/commission';
        $data['formSettings'] = [
            'name' => 'form_commission',
            'id' => 'form_commission',
            'class' => 'form_commission'
        ];

        $data['include'] = '';

        $data['url'] = uri()->segment(2, 0);

        render('commission/index', $data, 'admin');
    }

    /**
     * @description Funzione per la paginazione dei dati nel datatable
     *
     * @return void
     * @url /admin/commission/list.html
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
                2 => 'typology',
                3 => 'president.full_name',
                4 => 'userName',
                5 => 'updated_at'
            ];

            //Se l'utente è super Admin mostro la colonna dell'ente
            if (isSuperAdmin(true)) {
                $orderable[6] = 'i.full_name_institution';
            }

            //Setto proprietà datatable
            $dataTable = [];
            setDataTableData($dataTable, 'name');

            // Query per i dati da mostrare nel datatable
            $totalRecords = CommissionsModel::select('count(id) as allcount')
                ->count();

            $totalRecordsWithFilter = CommissionsModel::search($dataTable['searchValue'])
                ->select(['count(id) as allcount'])
                ->join('users', 'users.id', '=', 'object_commissions.owner_id', 'left outer')
                ->leftJoin('object_personnel as president', function ($join) {
                    $join->on('president.id', '=', 'object_commissions.president_id');
                });
            if (!empty($dataTable['searchValue'])) {
                $totalRecordsWithFilter->orWhere('object_commissions.id', '=', $dataTable['searchValue']);
            }
            $totalRecordsWithFilter = $totalRecordsWithFilter->count();

            $order = setOrderDatatable($dataTable['columnName'], $orderable, 'name');

            $records = CommissionsModel::search($dataTable['searchValue'])
                ->select(['object_commissions.id', 'object_commissions.updated_at', 'object_commissions.owner_id', 'object_commissions.institution_id',
                    'object_commissions.name', 'typology', 'president_id', 'object_commissions.archived', 'object_commissions.publishing_status', 'users.name as userName',
                    'i.full_name_institution', 'president.full_name'])
                ->with(['created_by' => function ($query) {
                    $query->withoutGlobalScopes([DeletedScope::class]);
                    $query->select(['id', 'name', 'deleted']);
                }])
                ->with('president:id,full_name,archived')
                ->with('institution:id,full_name_institution')
                ->join('institutions as i', 'object_commissions.institution_id', '=', 'i.id', 'left outer')
                ->leftJoin('object_personnel as president', function ($join) {
                    $join->on('president.id', '=', 'object_commissions.president_id');
                })
                ->join('users', 'users.id', '=', 'object_commissions.owner_id', 'left outer');

            if (!empty($dataTable['searchValue'])) {
                $records->orWhere('object_commissions.id', '=', $dataTable['searchValue']);
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

                    // Id della pagina di dettaglio del record in front-office
                    $sectionId = $record['typology'] === 'commissione' ? 245 : 244;

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
                        ->addEdit('admin/commission/edit/' . $record['id'], $record['id'])
                        ->addDuplicate('admin/commission/duplicate/' . $record['id'], $record['id'])
                        ->addDelete('admin/commission/delete/' . $record['id'], $record['id'])
                        ->render() : '';

                    $icon = null;

                    //Setto i dati da mostrare nelle colonne del datatable
                    $setTempData = [];
                    $setTempData[] = ($this->acl->getCrud() || checkRecordOwner($record['owner_id'])) ? ButtonAction::checkList('item[]', escapeXss($record['id'])) : '';
                    $setTempData[] = !empty($record['name'])
                        ? $icon . '<a href="' . siteUrl('/page/' . $sectionId . '/details/' . $record['id'] . '/' . urlTitle($record['name'])) . '" target="_blank">' . escapeXss($record['name']) . '</a>'
                        : 'N.D.';
                    $setTempData[] = !empty($record['typology']) ? escapeXss($record['typology']) : 'N.D.';
                    $setTempData[] = !empty($record['president']['full_name'])
                        ? escapeXss($record['president']['full_name'])
                        : 'N.D.';
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
     * @description Renderizza il form di creazione di una nuova commissione
     *
     * @return void
     * @throws Exception
     * @url /admin/commission/create.html
     * @method GET
     */
    public function create(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(__FUNCTION__);

        $data = [];

        //Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $data['is_box'] = uri()->uriString() === 'admin/commission/create-box';

        //Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {

            $this->breadcrumb->push('Commissioni e gruppi consiliari', 'admin/commission');
            $this->breadcrumb->push('Nuova', '/');
            $data['breadcrumbs'] = $this->breadcrumb->show();

            //Dati header della sezione
            $data['titleSection'] = 'Commissioni e gruppi consiliari';
            $data['subTitleSection'] = 'GESTIONE DEGLI ELEMENTI DELL\'ARCHIVIO';
            $data['sectionIcon'] = '<i class="fas fa-users fa-3x"></i>';
        }

        $data['formAction'] = '/admin/commission/store';
        $data['formSettings'] = [
            'name' => 'form_commission',
            'id' => 'form_commission',
            'class' => 'form_commission'
        ];
        $data['_storageType'] = 'insert';

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        render('commission/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua lo storage di una nuova Commissione
     *
     * @return void
     * @throws Exception
     * @url /admin/commission/store.html
     * @method POST
     */
    public function store(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $code = $json->success();
        $doUpload = null;
        $hasError = false;

        // Validatore form
        $validator = new CommissionValidator();
        $check = $validator->check();

        if ($check['is_success']) {
            $doAction = true;

            // Controllo sul file da caricare
            if (filesUploaded('img') === true) {
                $doUpload = $this->doUpload();
                $hasError = (bool)$doUpload['success'];
            }

            if (!$hasError) {

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
                        'typology' => setDefaultData(strip_tags(Input::post('typology', true)), null, ['']),
                        'president_id' => setDefaultData(strip_tags((int)Input::post('president_id', true)), null, ['']),
                        'description' => Input::post('description', true),
                        'email' => strip_tags((string)Input::post('email', true)),
                        'phone' => strip_tags((string)Input::post('phone', true)),
                        'fax' => strip_tags((string)Input::post('fax', true)),
                        'address' => strip_tags((string)Input::post('address', true)),
                        'activation_date' => !empty(Input::post('activation_date')) ? convertDateToDatabase(strip_tags((string)Input::post('activation_date', true))) : null,
                        'expiration_date' => !empty(Input::post('expiration_date')) ? convertDateToDatabase(strip_tags((string)Input::post('expiration_date', true))) : null,
                        'image' => !empty($doUpload['data']['file_name']) ? $doUpload['data']['file_name'] : null,
                        'order' => setDefaultData(strip_tags((int)Input::post('order', true)), null, ['']),
                    ];

                    // Storage nuova Commissione
                    $insert = CommissionsModel::createWithLogs($arrayValues);

                    // Storage nelle tabelle di relazione
                    $this->clear(
                        $insert,
                        !empty(Input::post('vice_presidents')) ? explode(',', strip_tags((string)Input::post('vice_presidents', true))) : null,
                        !empty(Input::post('secretaries')) ? explode(',', strip_tags((string)Input::post('secretaries', true))) : null,
                        !empty(Input::post('substitutes')) ? explode(',', strip_tags((string)Input::post('substitutes', true))) : null,
                        !empty(Input::post('members')) ? explode(',', strip_tags((string)Input::post('members', true))) : null
                    );

                    // Storage allegati associati al personale.
                    $attach->storage('attach_files', 'commissions', $insert->id, $arrayValues['name']);

                    // Generazione nuovo token
                    if (!referenceOriginForRegenerateToken(3, 'create-box')) {
                        Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
                    }

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
     * @description Renderizza il form di modifica/duplicazione di una Commissione
     *
     * @return void
     * @throws Exception
     * @url /admin/commission/edit/:num.html
     * @method GET
     */
    public function edit(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['update', 'create']);

        //Validatore che verifica se l'elemento da modificare esiste
        $validator = new CommissionValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        if (!$validate['is_success']) {
            sessionSetNotify($validate['errors'], 'danger');
            redirect('admin/commission');
            exit();
        }

        // Allegati
        $attach = new AttachmentArchive();

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $commission = Registry::get('commission');
        $commission = !empty($commission) ? $commission->toArray() : [];

        // Vedo in sessione se sono arrivato qui da un operazione di ripristino dal versioning
        $session = new Session();
        $restore = $session->getFlash('restore');

        $this->breadcrumb->push('Commissioni e gruppi consiliari', 'admin/commission');
        $this->breadcrumb->push('Modifica', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Commissioni e gruppi consiliari';
        $data['subTitleSection'] = 'GESTIONE DEGLI ELEMENTI DELL\'ARCHIVIO';
        $data['sectionIcon'] = '<i class="fas fa-users fa-3x"></i>';

        // Controllo se si sta duplicando un tasso di assenza
        $isDuplicate = uri()->segment(3, 0) === 'duplicate';

        // Setto il tipo di storage
        $data['_storageType'] = $isDuplicate ? 'duplicate' : 'update';

        //In base al tipo di storage setto l'action del form
        $data['formAction'] = ($isDuplicate) ? '/admin/commission/store' : '/admin/commission/update';
        $data['formSettings'] = [
            'name' => 'form_commission',
            'id' => 'form_commission',
            'class' => 'form_commission'
        ];

        $data['commission'] = $commission;

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'commissions',
            $commission['id'],
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

        $data['is_box'] = false;

        $activationDate = convertDateToForm($commission['activation_date']);
        $expirationDate = convertDateToForm($commission['expiration_date']);
        $data['activation_date'] = $activationDate['date'];
        $data['expiration_date'] = $expirationDate['date'];

        $data['vicePresidentIds'] = Arr::pluck($commission['vicepresidents'], 'id');
        $data['secretarieIds'] = Arr::pluck($commission['secretaries'], 'id');
        $data['substituteIds'] = Arr::pluck($commission['substitutes'], 'id');
        $data['memberIds'] = Arr::pluck($commission['members'], 'id');

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = $commission['institution_id'];

        $data['restore'] = $restore;
        $data['seo'] = $commission['p_s_d_r'] ?? null;

        render('commission/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua l'update di una Commissione
     *
     * @return void
     * @throws Exception
     * @url /admin/commission/update.html
     * @method POST
     */
    public function update(): void
    {
        $hasError = false;
        $json = new JsonResponse();
        $code = $json->success();
        $doUpload = null;

        // Validatore form
        $validator = new CommissionValidator();
        $check = $validator->check('update');

        if ($check['is_success']) {
            $doAction = true;

            $commissionId = (int)strip_tags(Input::post('id'));

            // Recupero la commissione attuale prima di modificarla e la salvo nel versioning
            $commission = CommissionsModel::where('id', $commissionId)
                ->with(['president' => function ($query) {
                    $query->select(['id', 'full_name']);
                }])
                ->with('vicepresidents:id,full_name')
                ->with('secretaries:id,full_name')
                ->with('substitutes:id,full_name')
                ->with('members:id,full_name')
                ->with('all_attachs');

            $commission = $commission->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute(__FUNCTION__, (!checkRecordOwner($commission->owner_id) && $this->acl->getCreate()));

            //Controllo sul file da aggiornare
            if (filesUploaded('img') === true) {

                $doUpload = $this->doUpload();
                $hasError = (bool)$doUpload['success'];

                if (!$hasError) {

                    //Se esiste elimino il vecchio file dalla cartella dei media dell'Ente
                    if (File::exists(MEDIA_PATH . instituteDir() . '/assets/images/' . $commission->image)) {

                        File::delete(MEDIA_PATH . instituteDir() . '/assets/images/' . $commission->image);
                    }
                }
            }

            if (!$hasError) {

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
                    $data['name'] = strip_tags((string)Input::post('name', true));
                    $data['typology'] = setDefaultData(strip_tags(Input::post('typology', true)), null, ['']);
                    $data['president_id'] = setDefaultData(strip_tags((int)Input::post('president_id', true)), null, ['']);
                    $data['description'] = Input::post('description', true);
                    $data['email'] = strip_tags((string)Input::post('email', true));
                    $data['phone'] = strip_tags((string)Input::post('phone', true));
                    $data['fax'] = strip_tags((string)Input::post('fax', true));
                    $data['address'] = strip_tags((string)Input::post('address', true));
                    $data['activation_date'] = !empty(Input::post('activation_date')) ? convertDateToDatabase(strip_tags((string)Input::post('activation_date', true))) : null;
                    $data['expiration_date'] = !empty(Input::post('expiration_date')) ? convertDateToDatabase(strip_tags((string)Input::post('expiration_date', true))) : null;
                    $data['image'] = !empty($doUpload['data']['file_name']) ? $doUpload['data']['file_name'] : $commission->image;
                    $data['order'] = setDefaultData(strip_tags((string)Input::post('order', true)), null, ['']);

                    // Update Commissione
                    CommissionsModel::where('id', '=', $commissionId)->updateWithLogs($commission, $data);

                    // Svuoto le tabelle di relazione e le aggiorno
                    $this->clear(
                        $commission,
                        !empty(Input::post('vice_presidents')) ? explode(',', strip_tags((string)Input::post('vice_presidents', true))) : null,
                        !empty(Input::post('secretaries')) ? explode(',', strip_tags((string)Input::post('secretaries', true))) : null,
                        !empty(Input::post('substitutes')) ? explode(',', strip_tags((string)Input::post('substitutes', true))) : null,
                        !empty(Input::post('members')) ? explode(',', strip_tags((string)Input::post('members', true))) : null
                    );

                    // Upload allegati associati al personale.
                    $attach->update(
                        'attach_files',
                        'commissions',
                        $commissionId,
                        $commission->institution_id,
                        $commission->name
                    );

                    // Generazione nuovo token
                    if (!referenceOriginForRegenerateToken(3, 'create-box')) {
                        Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
                    }
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
     * @param CommissionsModel|null $commission     Commissione di cui salvare le relazioni
     * @param array|int|null        $vicePresidents Membri con il ruolo di vice presidente
     * @param array|int|null        $secretaries    Membri con il ruolo di segretari
     * @param array|int|null        $substitutes    Membri con il ruolo di membri supplenti
     * @param array|int|null        $members        Membri della commissione
     * @return void
     */
    protected function clear(CommissionsModel $commission = null, array|int $vicePresidents = null, array|int $secretaries = null, array|int $substitutes = null, array|int $members = null): void
    {
        $dataVicePresidents = [];
        if ($vicePresidents !== null) {
            foreach ($vicePresidents as $vice) {
                $dataVicePresidents[] = is_array($vice) ? $vice['id'] : $vice;
            }
        }
        $commission->vicepresidents()->syncWithPivotValues($dataVicePresidents, ['typology' => 'vice-president']);

        $dataSecretaries = [];
        if ($secretaries) {
            foreach ($secretaries as $secretarie) {
                $dataSecretaries[] = is_array($secretarie) ? $secretarie['id'] : $secretarie;
            }
        }
        $commission->secretaries()->syncWithPivotValues($dataSecretaries, ['typology' => 'secretarie']);

        $dataSubstitutes = [];
        if ($substitutes) {
            foreach ($substitutes as $substitute) {
                $dataSubstitutes[] = is_array($substitute) ? $substitute['id'] : $substitute;
            }
        }
        $commission->substitutes()->syncWithPivotValues($dataSubstitutes, ['typology' => 'substitute']);

        $dataMembers = [];
        if ($members) {
            foreach ($members as $member) {
                $dataMembers[] = is_array($member) ? $member['id'] : $member;
            }
        }
        $commission->members()->syncWithPivotValues($dataMembers, ['typology' => 'member']);
    }

    /**
     * @description Funzione che effettua l'eliminazione di una Commissione
     *
     * @return void
     * @throws Exception
     * @url /admin/commission/delete.html
     * @method GET
     */
    public function delete(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute([__FUNCTION__, 'create', 'update'], true);

        //Validatore che verifica se l'elemento da eliminare esiste
        $validator = new CommissionValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getDelete() && $this->acl->getUpdate());

        if (!$validate['is_success']) {
            sessionSetNotify($validate['errors'], 'danger');
            redirect('admin/commission');
            exit();
        }

        // Se la validazione va a buon fine recupero l'elemento da eliminare dal registro
        $commission = Registry::get('commission');

        $image = $commission->image;

        //Elimino la commissione settando deleted = 1
        $commission->deleteWithLogs($commission);

        //Elimino l'eventuale immagine della commissione
        removePhoto($image);

        sessionSetNotify(__('success_delete_operation', null, 'patos'));

        // Generazione nuovo token
        Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        redirect('admin/commission');
    }

    /**
     * @description Funzione per l'eliminazione multipla
     *
     * @return void
     * @throws Exception
     * @url /admin/commission/deletes.html
     * @method GET
     */
    public function deletes(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('delete');

        $validator = new CommissionValidator();
        $validate = $validator->multipleSelection();

        if ($validate['is_success']) {

            sessionSetNotify('Operazione avvenuta con successo');

            //Recupero gli elementi da eliminare dal registro
            $commissions = Registry::get('__ids__multi_select_profile__');

            //Elimino gli elementi
            foreach ($commissions as $commission) {
                $image = $commission->image;

                $commission->deleteWithLogs($commission);

                //Elimino l'eventuale immagine della commissione
                removePhoto($image);
            }

            // Generazione nuovo token
            Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        } else {

            sessionSetNotify($validate['errors'], 'danger');
        }

        redirect('/admin/commission');
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
        $config['max_size'] = 5024;
        $config['max_width'] = 1024;
        $config['max_height'] = 1024;
        $config['min_width'] = 50;
        $config['min_height'] = 50;
        $config['max_filename'] = 50;
        $config['remove_spaces'] = true;

        $upload->initialize($config);

        if ($upload->doUpload('img')) {
            $data['success'] = false;
            $data['data'] = $upload->data();
        } else {
            $data['success'] = true;
            $data['data'] = $upload->displayErrors();
        }

        return $data;
    }
}
