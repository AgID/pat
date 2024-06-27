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
use Helpers\Validators\RealEstateAssetValidator;
use Model\RealEstateAssetModel;
use Scope\DeletedScope;
use System\Arr;
use System\Input;
use System\JsonResponse;
use System\Registry;
use System\Session;
use System\Token;

/**
 *
 * Controller Patrimoni Immobiliari
 *
 */
class RealEstateAssetAdminController extends BaseAuthController
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
     * @description Renderizza la pagina index dei Patrimoni Immobiliari
     *
     * @return void
     * @throws Exception
     * @url /admin/real-estate-asset.html
     * @method GET
     */
    public function index(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['read', 'create']);

        $data = [];

        $this->breadcrumb->push('Patrimonio immobiliare', '/');
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Patrimonio immobiliare';
        $data['subTitleSection'] = 'GESTIONE DEL PATRIMONIO IMMOBILIARE';
        $data['sectionIcon'] = '<i class="fas fa-briefcase fa-3x"></i>';

        $data['formAction'] = '/admin/real-estate-asset';
        $data['formSettings'] = [
            'name' => 'form_real_estate_asset',
            'id' => 'form_real_estate_asset',
            'class' => 'form_real_estate_asset',
        ];

        $data['include'] = '';

        $data['url'] = uri()->segment(2, 0);

        render('real_estate_asset/index', $data, 'admin');
    }

    /**
     * @description Funzione per la paginazione dei dati nel datatable
     *
     * @return void
     * @url /admin/real-estate-asset/list.html
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
                3 => 'users.name',
                4 => 'updated_at'
            ];

            //Se l'utente è super Admin mostro la colonna dell'ente
            if (isSuperAdmin(true)) {
                $orderable[5] = 'i.full_name_institution';
            }

            //Setto proprietà datatable
            $dataTable = [];
            setDataTableData($dataTable, 'name');

            //Query per i dati da mostrare nel datatable
            $totalRecords = RealEstateAssetModel::select('count(id) as allcount')
                ->count();

            $totalRecordsWithFilter = RealEstateAssetModel::search($dataTable['searchValue'])
                ->select(['count(object_real_estate_asset.id) as allcount'])
                ->join('users', 'users.id', '=', 'object_real_estate_asset.owner_id', 'left outer');

            if (!empty($dataTable['searchValue'])) {
                $totalRecordsWithFilter->orWhere('object_real_estate_asset.id', '=', $dataTable['searchValue']);
            }

            $totalRecordsWithFilter = $totalRecordsWithFilter->count();

            $order = setOrderDatatable($dataTable['columnName'], $orderable, 'name');

            $records = RealEstateAssetModel::search($dataTable['searchValue'])
                ->select(['object_real_estate_asset.id', 'object_real_estate_asset.updated_at', 'object_real_estate_asset.owner_id', 'object_real_estate_asset.institution_id',
                    'object_real_estate_asset.name', 'address', 'publishing_status', 'archived', 'users.name  as userName'])
                ->with(['created_by' => function ($query) {
                    $query->withoutGlobalScopes([DeletedScope::class]);
                    $query->select(['id', 'name', 'deleted']);
                }])
                ->with('institution:id,full_name_institution')
                ->join('institutions as i', 'object_real_estate_asset.institution_id', '=', 'i.id', 'left outer')
                ->join('users', 'users.id', '=', 'object_real_estate_asset.owner_id', 'left outer');

            if (!empty($dataTable['searchValue'])) {
                $records->orWhere('object_real_estate_asset.id', '=', $dataTable['searchValue']);
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
                        ->addEdit('admin/real-estate-asset/edit/' . $record['id'], $record['id'])
                        ->addDuplicate('admin/real-estate-asset/duplicate/' . $record['id'], $record['id'])
                        ->addDelete('admin/real-estate-asset/delete/' . $record['id'], $record['id'])
                        ->render() : '';

                    $icon = null;

                    //Setto i dati da mostrare nelle colonne del datatable
                    $setTempData = [];
                    $setTempData[] = ($this->acl->getCrud() || checkRecordOwner($record['owner_id'])) ? ButtonAction::checkList('item[]', $record['id']) : '';
                    $setTempData[] = !empty($record['name'])
                        ? $icon . '<a href="' . siteUrl('/page/133/details/' . $record['id'] . '/' . urlTitle($record['name'])) . '" target="_blank">' . escapeXss($record['name']) . '</a>'
                        : 'N.D.';
                    $setTempData[] = !empty($record['address']) ? escapeXss($record['address']) : 'N.D.';
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
     * @description Renderizza il form di creazione di un nuovo Immobile
     *
     * @return void
     * @throws Exception
     * @url /admin/real-estate-asset/create.html
     * @method GET
     */
    public function create(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(__FUNCTION__);

        $data = [];

        //Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $data['is_box'] = uri()->uriString() === 'admin/real-estate-asset/create-box';

        //Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {

            //Setto dati breadcrumb
            $this->breadcrumb->push('Patrimonio immobiliare', 'admin/real-estate-asset');
            $this->breadcrumb->push('Nuovo', '/');
            $data['breadcrumbs'] = $this->breadcrumb->show();

            //Dati header della sezione
            $data['titleSection'] = 'Patrimonio immobiliare';
            $data['subTitleSection'] = 'GESTIONE DEL PATRIMONIO IMMOBILIARE';
            $data['sectionIcon'] = '<i class="fas fa-briefcase fa-3x"></i>';

        }

        //Setto dati del form
        $data['formAction'] = '/admin/real-estate-asset/store';
        $data['formSettings'] = [
            'name' => 'form_real_estate_asset',
            'id' => 'form_real_estate_asset',
            'class' => 'form_real_estate_asset',
        ];
        $data['_storageType'] = 'insert';

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        render('real_estate_asset/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua lo storage di un nuovo Immobile
     *
     * @return void
     * @throws Exception
     * @url /admin/real-estate-asset/store.html
     * @method POST
     */
    public function store(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $code = $json->success();

        //Validatore form
        $validator = new RealEstateAssetValidator();
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
                    'address' => strip_tags(Input::post('address', true)),
                    'sheet' => strip_tags(Input::post('sheet', true)),
                    'particle' => strip_tags(Input::post('particle', true)),
                    'subaltern' => strip_tags(Input::post('subaltern', true)),
                    'gross_surface' => strip_tags(Input::post('gross_surface', true)),
                    'discovered_surface' => strip_tags(Input::post('discovered_surface', true)),
                    'description' => Input::post('description', true),
                ];

                // Storage nuovo Patrimonio Immobiliare
                $insert = RealEstateAssetModel::createWithLogs($arrayValues);

                // Svuoto le tabelle di relazione e le aggiorno
                $this->clear(
                    $insert,
                    !empty(Input::post('user_offices')) ? explode(',', strip_tags((string)Input::post('user_offices', true))) : null,
                );

                // Storage allegati associati al personale.
                $attach->storage('attach_files', 'real_estate_asset', $insert->id, $insert['name']);

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
     * @description Renderizza il form di modifica/duplicazione di un Immobile
     *
     * @return void
     * @throws Exception
     * @url /admin/real-estate-asset/edit/:id.html
     * @method GET
     */
    public function edit(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['update', 'create']);

        //Validatore che verifica se l'elemento da modificare esiste
        $validator = new RealEstateAssetValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/real-estate-asset', sessionSetNotify($validate['errors'], 'danger'));
            exit();

        }

        // Allegati
        $attach = new AttachmentArchive();

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $asset = Registry::get('real_estate_asset');
        $asset = !empty($asset) ? $asset->toArray() : [];

        $this->breadcrumb->push('Patrimonio immobiliare', 'admin/real-estate-asset');
        $this->breadcrumb->push('Modifica', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Patrimonio immobiliare';
        $data['subTitleSection'] = 'GESTIONE DEL PATRIMONIO IMMOBILIARE';
        $data['sectionIcon'] = '<i class="fas fa-briefcase fa-3x"></i>';

        // Vedo in sessione se sono arrivato qui da un operazione di ripristino dal versioning
        $session = new Session();
        $restore = $session->getFlash('restore');

        // Controllo se si sta duplicando un tasso di assenza
        $isDuplicate = uri()->segment(3, 0) === 'duplicate';

        // Setto il tipo di storage
        $data['_storageType'] = $isDuplicate ? 'duplicate' : 'update';

        //In base al tipo di storage setto l'action del form
        $data['formAction'] = ($isDuplicate) ? '/admin/real-estate-asset/store' : '/admin/real-estate-asset/update';
        $data['formSettings'] = [
            'name' => 'form_real-estate-asset',
            'id' => 'form_real-estate-asset',
            'class' => 'form_real-estate-asset',
        ];

        $data['real_estate_asset'] = $asset;

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'real_estate_asset',
            $asset['id'],
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
        $data['institution_id'] = $asset['institution_id'];

        $data['userOfficeIds'] = Arr::pluck($asset['offices'], 'id');
        $data['is_box'] = false;

        $data['restore'] = $restore;

        $data['seo'] = $asset['p_s_d_r'] ?? null;

        render('real_estate_asset/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua l'update di un Immobile
     *
     * @return void
     * @throws Exception
     * @url /admin/real-estate-asset/update.html
     * @method POST
     */
    public function update(): void
    {
        $json = new JsonResponse();
        $code = $json->success();

        //Validatore form
        $validator = new RealEstateAssetValidator();
        $check = $validator->check('update');

        if ($check['is_success']) {
            $doAction = true;

            $realEstateAssetId = (int)strip_tags(Input::post('id', true));

            // Recupero l'immobile attuale prima di modificarlo e lo salvo nel versioning
            $realEstateAsset = RealEstateAssetModel::where('id', $realEstateAssetId)
                ->with('offices:id,structure_name')
                ->with('all_attachs');

            $realEstateAsset = $realEstateAsset->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute(__FUNCTION__, (!checkRecordOwner($realEstateAsset['owner_id']) && $this->acl->getCreate()));

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
                $data['address'] = strip_tags(Input::post('address', true));
                $data['sheet'] = strip_tags(Input::post('sheet', true));
                $data['particle'] = strip_tags(Input::post('particle', true));
                $data['subaltern'] = strip_tags(Input::post('subaltern', true));
                $data['gross_surface'] = strip_tags(Input::post('gross_surface', true));
                $data['discovered_surface'] = strip_tags(Input::post('discovered_surface', true));
                $data['description'] = Input::post('description', true);

                // Update Patrimonio Immobiliare
                RealEstateAssetModel::where('id', $realEstateAssetId)->updateWithLogs($realEstateAsset, $data);

                // Svuoto le tabelle di relazione e le aggiorno
                $this->clear(
                    $realEstateAsset,
                    !empty(Input::post('user_offices')) ? explode(',', strip_tags((string)Input::post('user_offices', true))) : null,
                );

                // Upload allegati associati al personale.
                $attach->update(
                    'attach_files',
                    'real_estate_asset',
                    $realEstateAssetId,
                    $realEstateAsset['institution_id'],
                    $realEstateAsset['name']
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
     * @description  Metodo per lo storage nelle tabelle di relazione
     * In caso di update, svuota prima le tabelle di relazione e poi inserisce i dati aggiornati
     *
     * @param RealEstateAssetModel|null $asset       Immobile di cui salvare le relazioni
     * @param array|int|null            $userOffices Strutture associate all'immobile
     * @return void
     */
    protected function clear(RealEstateAssetModel $asset = null, array|int $userOffices = null): void
    {
        $dataOffices = [];
        if ($userOffices !== null) {
            foreach ($userOffices as $office) {
                $dataOffices[] = is_array($office) ? $office['id'] : $office;
            }
        }
        //Insert/Update nella tabella di relazione
        $asset->offices()->sync($dataOffices);
    }


    /**
     * @description Funzione che effettua l'eliminazione di un Immobile
     *
     * @return void
     * @throws Exception
     * @url /admin/real-estate-asset/delete.html
     * @method GET
     */
    public function delete(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute([__FUNCTION__, 'create', 'update'], true);

        //Validatore che verifica se l'elemento da eliminare esiste
        $validator = new RealEstateAssetValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getDelete() && $this->acl->getUpdate());

        if (!$validate['is_success']) {
            redirect('admin/real-estate-asset', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Se la validazione va a buon fine recupero l'elemento da eliminare dal registro
        $asset = Registry::get('real_estate_asset');

        //Elimino il patrimonio immobiliare
        $asset->deleteWithLogs($asset);

        sessionSetNotify(__('success_delete_operation', null, 'patos'));

        redirect('admin/real-estate-asset');
    }

    /**
     * @description Funzione per l'eliminazione multipla
     *
     * @return void
     * @throws Exception
     * @url /admin/real-estate-asset/deletes.html
     * @method GET
     */
    public function deletes(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('delete');

        $validator = new RealEstateAssetValidator();
        $validate = $validator->multipleSelection();

        if ($validate['is_success']) {

            sessionSetNotify('Operazione avvenuta con successo');

            //Recupero gli elementi da eliminare dal registro
            $realEstateAssets = Registry::get('__ids__multi_select_profile__');

            //Elimino gli elementi
            foreach ($realEstateAssets as $realEstateAsset) {
                $realEstateAsset->deleteWithLogs($realEstateAsset);
            }

        } else {

            sessionSetNotify($validate['errors'], 'danger');

        }

        redirect('/admin/real-estate-asset');
    }
}
