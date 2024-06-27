<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Admin;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Helpers\S;
use Helpers\Utility\AttachmentArchive;
use Helpers\Utility\ButtonAction;
use Helpers\Validators\CanonValidator;
use Helpers\Validators\DatatableValidator;
use Model\LeaseCanonsModel;
use Model\StructuresModel;
use Scope\DeletedScope;
use System\Arr;
use System\Input;
use System\JsonResponse;
use System\Registry;
use System\Session;
use System\Token;

/**
 *
 * Controller Canoni di locazione
 *
 */
class CanonAdminController extends BaseAuthController
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
     * @description Renderizza la pagina index dei Canoni di locazione
     *
     * @return void
     * @throws Exception
     * @url /admin/canon.html
     * @method GET
     */
    public function index(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['read', 'create']);

        $this->breadcrumb->push('Canoni di locazione', '/');
        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Canoni di locazione';
        $data['subTitleSection'] = 'GESTIONE DEI CANONI DI LOCAZIONE';
        $data['sectionIcon'] = '<i class="fas fa-briefcase fa-3x"></i>';

        $data['formAction'] = '/admin/canon';
        $data['formSettings'] = [
            'name' => 'form_canon',
            'id' => 'form_canon',
            'class' => 'form_canon',
        ];

        $data['include'] = '';

        $data['url'] = uri()->segment(2, 0);

        render('canon/index', $data, 'admin');
    }

    /**
     * @description Funzione per la paginazione dei dati nel datatable
     *
     * @return void
     * @url /admin/canon/list.html
     * @throws Exception
     * @noinspection DuplicatedCode
     */
    public function asyncPaginateDatatable(): void
    {
        // Setto il metodo della rotta per i permessi
        $this->acl->setRoute('read');

        // Validatore datatable
        $datatableValidator = new DatatableValidator();
        $validator = $datatableValidator->validate();
        $response = [];

        // Controllo se è una richiesta Ajax
        if (Input::isAjax() === true && $validator['is_success'] === true) {

            $data = [];

            // Colonne su cui poter effettuare l'ordinamento
            $orderable = [
                1 => 'beneficiary',
                4 => 'users.name',
                5 => 'updated_at'
            ];

            if (isSuperAdmin(true)) {
                $orderable[6] = 'i.full_name_institution';
            }

            //Setto proprietà datatable
            $dataTable = [];
            setDataTableData($dataTable, 'beneficiary');

            // Query per i dati da mostrare nel datatable
            $totalRecords = LeaseCanonsModel::select('count(id) as allcount')
                ->count();

            $totalRecordsWithFilter = LeaseCanonsModel::search($dataTable['searchValue'])
                ->select(['count(object_lease_canons.id) as allcount'])
                ->join('users', 'users.id', '=', 'object_lease_canons.owner_id', 'left outer');

            if (!empty($dataTable['searchValue'])) {
                $totalRecordsWithFilter->orWhere('object_lease_canons.id', '=', $dataTable['searchValue']);
            }
            $totalRecordsWithFilter = $totalRecordsWithFilter->count();

            $order = setOrderDatatable($dataTable['columnName'], $orderable, 'beneficiary');

            $records = LeaseCanonsModel::search($dataTable['searchValue'])
                ->select(['object_lease_canons.id', 'object_lease_canons.owner_id', 'object_lease_canons.institution_id',
                    'canon_type', 'publishing_status', 'beneficiary', 'object_lease_canons.updated_at', 'amount', 'users.name', 'i.full_name_institution'])
                ->with('properties:id,name,archived')
                ->with(['created_by' => function ($query) {
                    $query->withoutGlobalScopes([DeletedScope::class]);
                    $query->select(['id', 'name', 'deleted']);
                }])
                ->with(['institution' => function ($query) {
                    $query->select(['id', 'full_name_institution']);
                }])
                ->join('institutions as i', 'object_lease_canons.institution_id', '=', 'i.id', 'left outer')
                ->join('users', 'users.id', '=', 'object_lease_canons.owner_id', 'left outer');

            if (!empty($dataTable['searchValue'])) {
                $records->orWhere('object_lease_canons.id', '=', $dataTable['searchValue']);
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

                    if (!empty($record['properties']) && is_array($record['properties'])) {

                        $tmpProperties = Arr::pluck($record['properties'], 'name');
                        $tmpArchProperties = Arr::pluck($record['properties'], 'archived');
                        $responsibles = str_replace(',', ',' . nbs(2), implode(
                            ',',
                            array_map(
                                function ($name, $archived) {
                                    return ('<small class="badge badge-primary mb-1">'
                                        . escapeXss($name) . '</small>');
                                },
                                $tmpProperties,
                                $tmpArchProperties
                            )
                        ));
                    } else {

                        $responsibles = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definiti">N.D.</small>';
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
                        'duplicate' => $this->acl->getCreate(),
                        'delete' => $this->acl->getDelete() || $updatePermits || $permits,
                    ])
                        ->addEdit('admin/canon/edit/' . $record['id'], $record['id'])
                        ->addDuplicate('admin/canon/duplicate/' . $record['id'], $record['id'])
                        ->addDelete('admin/canon/delete/' . $record['id'], $record['id'])
                        ->render() : '';

                    $icon = null;

                    //Setto i dati da mostrare nelle colonne del datatable
                    $setTempData = [];
                    $setTempData[] = (($this->acl->getCrud() || checkRecordOwner($record['owner_id'])) ? ButtonAction::checkList('item[]', $record['id']) : '');
                    $setTempData[] = $icon . (!empty($record['beneficiary']) ? escapeXss($record['beneficiary']) : 'N.D.');
                    $setTempData[] = $responsibles;
                    $setTempData[] = !empty($record['amount']) ? '<small class="badge badge-success">' . S::currency(escapeXss($record['amount']), 2, ',', '.') . ' &euro; </small>' : 'N.D.';
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
     * @description Renderizza il form di creazione di un nuovo Canone di locazione
     *
     * @return void
     * @throws Exception
     * @url /admin/canon/create.html
     * @method GET
     */
    public function create(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(__FUNCTION__);

        $data = [];

        $this->breadcrumb->push('Canoni di locazione', 'admin/canon');
        $this->breadcrumb->push('Nuovo', '/');
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Canoni di locazione';
        $data['subTitleSection'] = 'GESTIONE DEI CANONI DI LOCAZIONE';
        $data['sectionIcon'] = '<i class="fas fa-briefcase fa-3x"></i>';

        $data['formAction'] = '/admin/canon/store';
        $data['formSettings'] = [
            'name' => 'form_canon',
            'id' => 'form_canon',
            'class' => 'form_canon',
        ];
        $data['_storageType'] = 'insert';

        $data['structures'] = [null => ''] + StructuresModel::all()
                ->pluck('structure_name', 'id')
                ->toArray();

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        render('canon/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua lo storage di un nuovo Canone
     *
     * @return void
     * @throws Exception
     * @url /admin/canon/store.html
     * @method POST
     */
    public function store(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new CanonValidator();
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

                $canonTypology = setDefaultData(strip_tags(Input::post('canon_type', true)), null, ['']);

                $arrayValues = [
                    'owner_id' => $getIdentity['id'],
                    'institution_id' => checkAlternativeInstitutionId(),
                    'canon_type' => $canonTypology,
                    'beneficiary' => ($canonTypology == 1) ? strip_tags(Input::post('beneficiary', true)) : null,
                    'fiscal_code' => ($canonTypology == 1) ? strip_tags(Input::post('fiscal_code', true)) : null,
                    'amount' => !empty(Input::post('amount')) ? floatvalue(strip_tags(Input::post('amount', true))) : null,
                    'contract_statements' => strip_tags(Input::post('contract_statements', true)),
                    'start_date' => !empty(Input::post('start_date')) ? convertDateToDatabase(strip_tags(Input::post('start_date', true))) : null,
                    'end_date' => !empty(Input::post('end_date')) ? convertDateToDatabase(strip_tags(Input::post('end_date', true))) : null,
                    'object_structures_id' => setDefaultData(strip_tags(Input::post('object_structures_id', true)), null, ['']),
                    'notes' => Input::post('notes', true)
                ];

                // Storage nuovo Canone
                $insert = LeaseCanonsModel::createWithLogs($arrayValues);

                // Storage nelle tabelle di relazione
                $this->clear(
                    $insert,
                    Input::post('properties', true)
                );

                $canonType = ($canonTypology == 1)
                    ? 'Canoni di locazione o di affitto versati'
                    : 'Canoni di locazione o di affitto percepiti';

                // Storage allegati associati al personale.
                $attach->storage('attach_files', 'lease_canons', $insert->id, $canonType);

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
     * @description Renderizza il form di modifica di un Canone di locazione
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/canon/edit/:id.html
     * @method GET
     */
    public function edit(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['update', 'create']);

        //Validatore che verifica se l'elemento da modificare esiste
        $validator = new CanonValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/canon', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Allegati
        $attach = new AttachmentArchive();

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $canon = Registry::get('canon');
        $canon = !empty($canon) ? $canon->toArray() : [];

        $this->breadcrumb->push('Canoni di locazione', 'admin/canon');
        $this->breadcrumb->push('Modifica', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Canoni di locazione';
        $data['subTitleSection'] = 'GESTIONE DEI CANONI DI LOCAZIONE';
        $data['sectionIcon'] = '<i class="fas fa-briefcase fa-3x"></i>';

        // Vedo in sessione se sono arrivato qui da un operazione di ripristino dal versioning
        $session = new Session();
        $restore = $session->getFlash('restore');
        $data['restore'] = $restore;

        // Controllo se si sta duplicando un tasso di assenza
        $isDuplicate = uri()->segment(3, 0) === 'duplicate';

        // Setto il tipo di storage
        $data['_storageType'] = $isDuplicate ? 'duplicate' : 'update';

        //In base al tipo di storage setto l'action del form
        $data['formAction'] = ($isDuplicate) ? '/admin/canon/store' : '/admin/canon/update';
        $data['formSettings'] = [
            'name' => 'form_canon',
            'id' => 'form_canon',
            'class' => 'form_canon',
        ];

        $startDate = convertDateToForm($canon['start_date']);
        $endDate = convertDateToForm($canon['end_date']);

        $data['canon'] = $canon;

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'lease_canons',
            $canon['id'],
            [
                'id',
                'cat_id',
                'archive_name',
                'archive_id',
                'client_name',
                'file_name',
                'file_type',
                'file_ext',
                'file_size',
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
        $data['institution_id'] = $canon['institution_id'];

        $data['start_date'] = $startDate['date'];
        $data['end_date'] = $endDate['date'];

        $data['propertyIds'] = Arr::pluck($canon['properties'], 'id');
        $data['seo'] = $canon['p_s_d_r'] ?? null;

        render('canon/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua l'update di un Canone
     *
     * @return void
     * @throws Exception
     * @url /admin/canon/update.html
     * @method POST
     */
    public function update(): void
    {

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new CanonValidator();
        $check = $validator->check('update');

        if ($check['is_success']) {
            $doAction = true;

            $canonId = (int)strip_tags(Input::post('id', true));

            // Recupero il canone attuale prima di modificarlo e lo salvo nel versioning
            $canon = LeaseCanonsModel::where('id', $canonId)
                ->with('properties:id,name,archived')
                ->with(['structure' => function ($query) {
                    $query->select(['id', 'structure_name']);
                }])
                ->with('all_attachs');

            $canon = $canon->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute(__FUNCTION__, (!checkRecordOwner($canon['owner_id']) && $this->acl->getCreate()));

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
                $canonTypology = setDefaultData(strip_tags(Input::post('canon_type', true)), null, ['']);

                $data = [];
                $data['canon_type'] = $canonTypology;
                $data['beneficiary'] = ($canonTypology == 1) ? strip_tags(Input::post('beneficiary', true)) : null;
                $data['fiscal_code'] = ($canonTypology == 1) ? strip_tags(Input::post('fiscal_code', true)) : null;
                $data['amount'] = !empty(Input::post('amount')) ? floatvalue(strip_tags(Input::post('amount', true))) : null;
                $data['contract_statements'] = strip_tags(Input::post('contract_statements', true));
                $data['start_date'] = !empty(Input::post('start_date')) ? convertDateToDatabase(strip_tags(Input::post('start_date', true))) : null;
                $data['end_date'] = !empty(Input::post('end_date')) ? convertDateToDatabase(strip_tags(Input::post('end_date', true))) : null;
                $data['object_structures_id'] = setDefaultData(strip_tags(Input::post('object_structures_id', true)), null, ['']);
                $data['notes'] = Input::post('notes', true);

                // Update Canone
                LeaseCanonsModel::where('id', $canonId)->updateWithLogs($canon, $data);

                // Svuoto le tabelle di relazione e le aggiorno
                $this->clear(
                    $canon,
                    Input::post('properties', true)
                );

                $canonType = ($canonTypology == 1)
                    ? 'Canoni di locazione o di affitto versati'
                    : 'Canoni di locazione o di affitto percepiti';

                // Upload allegati associati al personale.
                $attach->update(
                    'attach_files',
                    'lease_canons',
                    $canonId,
                    $canon['institution_id'],
                    $canonType
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
     * @param LeaseCanonsModel|null $canon      Canone di locazione
     * @param int|array|null        $properties Immobili associati al canone di locazione
     * @return void
     */
    protected function clear(LeaseCanonsModel $canon = null, int|array $properties = null): void
    {
        $dataProperties = [];
        if ($properties !== null) {
            foreach ($properties as $property) {
                $dataProperties[] = is_array($property) ? $property['id'] : $property;
            }
        }
        //Insert/Update nella tabella di relazione
        $canon->properties()->sync($dataProperties);
    }


    /**
     * @description Funzione che effettua l'eliminazione di un Canone
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/canon/delete/:id.html
     * @method GET
     */
    public function delete(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute([__FUNCTION__, 'create', 'update'], true);

        //Validatore che verifica se l'elemento da eliminare esiste
        $validator = new CanonValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getDelete() && $this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/canon', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Se la validazione va a buon fine recupero l'elemento da eliminare dal registro
        $canon = Registry::get('canon');

        //Elimino il canone
        $canon->deleteWithLogs($canon);

        sessionSetNotify(__('success_delete_operation', null, 'patos'));

        // Generazione nuovo token
        Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        redirect('admin/canon');
    }

    /**
     * @description Funzione per l'eliminazione multipla
     *
     * @return void
     * @throws Exception
     * @url /admin/canon/deletes.html
     * @method GET
     */
    public function deletes(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('delete');

        // Validatore sugli elementi da eliminare
        $validator = new CanonValidator();
        $validate = $validator->multipleSelection();

        if ($validate['is_success']) {

            sessionSetNotify('Operazione avvenuta con successo');

            //Recupero gli elementi da eliminare dal registro
            $canons = Registry::get('__ids__multi_select_profile__');

            //Elimino gli elementi
            foreach ($canons as $canon) {
                $canon->deleteWithLogs($canon);
            }

            // Generazione nuovo token
            Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        } else {

            sessionSetNotify($validate['errors'], 'danger');
        }

        redirect('/admin/canon');
    }
}
