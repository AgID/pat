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
use Helpers\Validators\CompanyValidator;
use Helpers\Validators\DatatableValidator;
use Model\CompanyModel;
use Scope\DeletedScope;
use System\Arr;
use System\Input;
use System\JsonResponse;
use System\Registry;
use System\Session;
use System\Token;

/**
 *
 * Controller Enti e società controllate
 *
 */
class CompanyAdminController extends BaseAuthController
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
     * @description Renderizza la pagina index delle Società
     * @return void
     * @throws Exception
     * @url /admin/company.html
     * @method GET
     */
    public function index(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['read', 'create']);

        $this->breadcrumb->push('Enti e società controllate', '/');
        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Enti e società controllate';
        $data['subTitleSection'] = 'GESTIONE DEGLI ENTI CONTROLLATI E DELLE SOCIETÀ PARTECIPATE';
        $data['sectionIcon'] = '<i class="far fa-building fa-3x"></i>';

        $data['formAction'] = '/admin/company';
        $data['formSettings'] = [
            'name' => 'form_company',
            'id' => 'form_company',
            'class' => 'form_company',
        ];

        $data['include'] = '';

        $data['url'] = uri()->segment(2, 0);

        render('company/index', $data, 'admin');
    }

    /**
     * @description Funzione per la paginazione dei dati nel datatable
     *
     * @return void
     * @url /admin/company/list.html
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
                1 => 'company_name',
                2 => 'typology',
                4 => 'users.name',
                5 => 'updated_at'
            ];

            //Se l'utente è super Admin mostro la colonna dell'ente
            if (isSuperAdmin(true)) {
                $orderable[6] = 'i.full_name_institution';
            }

            //Setto proprietà datatable
            $dataTable = [];
            setDataTableData($dataTable, 'company_name');

            //Query per il count dei dati da mostrare nel datatable
            $totalRecords = CompanyModel::select('count(id) as allcount')
                ->count();
            $totalRecordsWithFilter = CompanyModel::search($dataTable['searchValue'])
                ->select(['count(id) as allcount'])
                ->join('users', 'users.id', '=', 'object_company.owner_id', 'left outer');

            if (!empty($dataTable['searchValue'])) {
                $totalRecordsWithFilter->orWhere('object_company.id', '=', $dataTable['searchValue']);
            }

            $totalRecordsWithFilter = $totalRecordsWithFilter->count();

            $order = setOrderDatatable($dataTable['columnName'], $orderable, 'company_name');

            //Dati da mostrare nel datatable
            $records = CompanyModel::search($dataTable['searchValue'])
                ->select(['object_company.id', 'object_company.updated_at', 'object_company.owner_id', 'object_company.institution_id',
                    'company_name', 'website_url', 'archived', 'publishing_status', 'typology', 'users.name', 'i.full_name_institution'])
                ->with(['created_by' => function ($query) {
                    $query->withoutGlobalScopes([DeletedScope::class]);
                    $query->select(['id', 'name', 'deleted']);
                }])
                ->with('institution:id,full_name_institution')
                ->join('institutions as i', 'object_company.institution_id', '=', 'i.id', 'left outer')
                ->join('users', 'users.id', '=', 'object_company.owner_id', 'left outer');

            //Filtro per id
            if (!empty($dataTable['searchValue'])) {
                $records->orWhere('object_company.id', '=', $dataTable['searchValue']);
            }

            $records = $records->orderBy($order, $dataTable['columnSortOrder'])
                ->offset($dataTable['start'])
                ->limit($dataTable['rowPerPage'])
                ->get()
                ->toArray();

            $response ['draw'] = intval($dataTable['draw']);
            $response ['iTotalRecords'] = ($totalRecords);
            $response ['iTotalDisplayRecords'] = ($totalRecordsWithFilter);
            $response ['aaData'] = [];

            if (!empty($records)) {

                foreach ($records as $record) {
                    if ($record['typology'] == 'ente pubblico vigilato') {
                        $recordUrl = siteUrl('/page/89/details/' . $record['id'] . '/' . urlTitle($record['company_name']));
                    } else if ($record['typology'] == 'societa partecipata') {
                        $recordUrl = siteUrl('/page/91/details/' . $record['id'] . '/' . urlTitle($record['company_name']));
                    } else $recordUrl = siteUrl('/page/93/details/' . $record['id'] . '/' . urlTitle($record['company_name']));

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
                        ->addEdit('admin/company/edit/' . $record['id'], $record['id'])
                        ->addDuplicate('admin/company/duplicate/' . $record['id'], $record['id'])
                        ->addDelete('admin/company/delete/' . $record['id'], $record['id'])
                        ->render() : '';

                    $icon = null;

                    //Setto i dati da mostrare nelle colonne del datatable
                    $setTempData = [];
                    $setTempData[] = ($this->acl->getCrud() || checkRecordOwner($record['owner_id'])) ? ButtonAction::checkList('item[]', $record['id']) : '';
                    $setTempData[] = !empty($record['company_name'])
                        ? $icon . '<a href="' . $recordUrl . '" target="_blank">' . escapeXss($record['company_name']) . '</a>'
                        : 'N.D.';
                    $setTempData[] = !empty($record['typology']) ? escapeXss($record['typology']) : 'N.D.';
                    $setTempData[] = !empty($record['website_url']) ? escapeXss($record['website_url']) : 'N.D.';
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
     * @description Renderizza il form di creazione di una nuova Società
     * @return void
     * @throws Exception
     * @url /admin/company/create.html
     * @method GET
     */
    public function create(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(__FUNCTION__);

        $data = [];

        //Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $data['is_box'] = uri()->uriString() === 'admin/company/create-box';

        //Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {

            $this->breadcrumb->push('Enti e società controllate', 'admin/company');
            $this->breadcrumb->push('Nuovo', '/');
            $data['breadcrumbs'] = $this->breadcrumb->show();

            //Dati header della sezione
            $data['titleSection'] = 'Enti e società controllate';
            $data['subTitleSection'] = 'GESTIONE DEGLI ENTI CONTROLLATI E DELLE SOCIETÀ PARTECIPATE';
            $data['sectionIcon'] = '<i class="far fa-building fa-3x"></i>';

        }

        //Setto dati del form
        $data['formAction'] = '/admin/company/store';
        $data['formSettings'] = [
            'name' => 'form_company',
            'id' => 'form_company',
            'class' => 'form_company',
        ];
        $data['_storageType'] = 'insert';

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        render('company/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua lo storage di una nuova Società
     *
     * @return void
     * @throws Exception
     * @url /admin/company/store.html
     * @method POST
     */
    public function store(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new CompanyValidator();
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
                    'company_name' => strip_tags(Input::post('company_name', true)),
                    'typology' => setDefaultData(strip_tags(Input::post('typology', true)), null, ['']),
                    'participation_measure' => strip_tags(Input::post('participation_measure', true)),
                    'duration' => strip_tags(Input::post('duration', true)),
                    'year_charges' => Input::post('year_charges', true),
                    'description' => Input::post('description', true),
                    'treatment_assignments' => Input::post('treatment_assignments', true),
                    'website_url' => strip_tags(Input::post('website_url', true)),
                    'balance' => Input::post('balance', true),
                    'inconferability_dec_link' => strip_tags(Input::post('inconferability_dec_link', true)),
                    'incompatibility_dec_link' => strip_tags(Input::post('incompatibility_dec_link', true)),
                ];

                // Storage nuova Società
                $insert = CompanyModel::createWithLogs($arrayValues);

                $this->clear(
                    $insert,
                    !empty(Input::post('representatives')) ? explode(',', strip_tags((string)Input::post('representatives', true))) : null
                );

                // Storage allegati associati alla società
                $attach->storage('attach_files', 'company', $insert->id, $arrayValues['company_name']);

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
     * @description Renderizza il form di modifica/duplicazione di una Società
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/company/edit/:id.html
     * @method GET
     */
    public function edit(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['update', 'create']);

        //Validatore che verifica se l'elemento da modificare esiste
        $validator = new CompanyValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        if (!$validate['is_success']) {
            redirect('admin/company', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Allegati
        $attach = new AttachmentArchive();

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $company = Registry::get('company');
        $company = !empty($company) ? $company->toArray() : [];

        // Vedo in sessione se sono arrivato qui da un operazione di ripristino dal versioning
        $session = new Session();
        $restore = $session->getFlash('restore');

        $this->breadcrumb->push('Enti e società controllate', 'admin/company');
        $this->breadcrumb->push('Modifica', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Enti e società controllate';
        $data['subTitleSection'] = 'GESTIONE DEGLI ENTI CONTROLLATI E DELLE SOCIETÀ PARTECIPATE';
        $data['sectionIcon'] = '<i class="far fa-building fa-3x"></i>';

        // Controllo se si sta duplicando un tasso di assenza
        $isDuplicate = uri()->segment(3, 0) === 'duplicate';

        // Setto il tipo di storage
        $data['_storageType'] = $isDuplicate ? 'duplicate' : 'update';

        //In base al tipo di storage setto l'action del form
        $data['formAction'] = ($isDuplicate) ? '/admin/company/store' : '/admin/company/update';
        $data['formSettings'] = [
            'name' => 'form_company',
            'id' => 'form_company',
            'class' => 'form_company'
        ];

        $data['is_box'] = false;
        $data['company'] = $company;

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'company',
            $company['id'],
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
            ]);

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = $company['institution_id'];

        $data['restore'] = $restore;
        $data['representativeIds'] = Arr::pluck($company['representatives'], 'id');
        $data['seo'] = $company['p_s_d_r'] ?? null;
        render('company/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua l'update di una Società
     *
     * @return void
     * @noinspection DuplicatedCode
     * @throws Exception
     * @url /admin/company/update.html
     * @method POST
     */
    public function update(): void
    {

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new CompanyValidator();
        $check = $validator->check('update');

        if ($check['is_success']) {

            $doAction = true;

            $companyId = (int)strip_tags(Input::post('id', true));

            // Recupero la società attuale prima di modificarla e la salvo nel versioning
            $company = CompanyModel::where('id', $companyId)
                ->with('representatives:id,full_name')
                ->with('all_attachs');

            $company = $company->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute(__FUNCTION__, (!checkRecordOwner($company['owner_id']) && $this->acl->getCreate()));

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
                $data['company_name'] = strip_tags(Input::post('company_name', true));
                $data['typology'] = setDefaultData(strip_tags(Input::post('typology', true)), null, ['']);
                $data['participation_measure'] = strip_tags(Input::post('participation_measure', true));
                $data['duration'] = strip_tags(Input::post('duration', true));
                $data['year_charges'] = Input::post('year_charges', true);
                $data['description'] = Input::post('description', true);
                $data['treatment_assignments'] = Input::post('treatment_assignments', true);
                $data['website_url'] = strip_tags(Input::post('website_url', true));
                $data['balance'] = Input::post('balance', true);
                $data['inconferability_dec_link'] = strip_tags(Input::post('inconferability_dec_link', true));
                $data['incompatibility_dec_link'] = strip_tags(Input::post('incompatibility_dec_link', true));

                // Update Società
                CompanyModel::where('id', $companyId)->updateWithLogs($company, $data);

                // Svuoto le tabelle di relazione e le aggiorno
                $this->clear(
                    $company,
                    !empty(Input::post('representatives')) ? explode(',', strip_tags((string)Input::post('representatives', true))) : null
                );

                // Upload allegati associati al personale.
                $attach->update(
                    'attach_files',
                    'company',
                    $companyId,
                    $company['institution_id'],
                    $company['company_name']
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
     * @description Metodo per lo storage nelle tabelle di relazione
     * In caso di update, svuota prima le tabelle di relazione e poi inserisce i dati aggiornati
     *
     * @param CompanyModel|null $company         Società per cui si devono
     *                                           salvare le relazioni
     * @param array|int|null    $representatives Personale rappresentante della
     *                                           Società
     * @return void
     */
    protected function clear(CompanyModel $company = null, array|int $representatives = null): void
    {
        $dataRepresentatives = [];
        if ($representatives !== null) {
            foreach ($representatives as $representative) {
                $dataRepresentatives[] = is_array($representative) ? $representative['id'] : $representative;
            }
        }
        //Insert/Update nella tabella di relazione
        $company->representatives()->sync($dataRepresentatives);
    }

    /**
     * @description Funzione che effettua l'eliminazione di una Società
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/company/delete/:id.html
     * @method GET
     */
    public function delete(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute([__FUNCTION__, 'create', 'update'], true);

        //Validatore che verifica se l'elemento da eliminare esiste
        $validator = new CompanyValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getDelete() && $this->acl->getUpdate());

        if (!$validate['is_success']) {
            redirect('admin/company', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Se la validazione va a buon fine recupero l'elemento da eliminare dal registro
        $company = Registry::get('company');

        //Elimino la società
        $company->deleteWithLogs($company);

        sessionSetNotify(__('success_delete_operation', null, 'patos'));

        redirect('admin/company');
    }

    /**
     * @description Funzione per l'eliminazione multipla
     *
     * @return void
     * @method GET
     * @throws Exception
     * @url /admin/structure/deletes.html
     */
    public function deletes(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('delete');

        $validator = new CompanyValidator();
        $validate = $validator->multipleSelection();

        if ($validate['is_success']) {

            sessionSetNotify('Operazione avvenuta con successo');

            //Recupero gli elementi da eliminare dal registro
            $companies = Registry::get('__ids__multi_select_profile__');

            //Elimino gli elementi
            foreach ($companies as $company) {
                $company->deleteWithLogs($company);
            }


        } else {

            sessionSetNotify($validate['errors'], 'danger');

        }

        redirect('/admin/company');
    }
}
