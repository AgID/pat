<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Admin;

use Exception;
use Helpers\Utility\AttachmentArchive;
use Helpers\Utility\ButtonAction;
use Helpers\Validators\DatatableValidator;
use Helpers\Validators\StructureValidator;
use Model\StructuresModel;
use Scope\DeletedScope;
use System\Arr;
use System\Input;
use System\JsonResponse;
use System\Registry;
use System\Session;
use System\Token;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 *
 * Controller Strutture Organizzative
 *
 */
class StructureAdminController extends BaseAuthController
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
     * @description Renderizza la pagina index delle Strutture Organizzative
     *
     * @return void
     * @throws Exception
     * @url /admin/structure.html
     * @method GET
     */
    public function index(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['read', 'create']);

        $this->breadcrumb->push('Strutture organizzative', '/');
        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Strutture organizzative';
        $data['subTitleSection'] = 'GESTIONE DEGLI UFFICI E DELLE STRUTTURE DELL\'ENTE';
        $data['sectionIcon'] = '<i class="fas fa-building fa-3x"></i>';

        $data['formAction'] = '/admin/structure';
        $data['formSettings'] = [
            'name' => 'form_structure',
            'id' => 'form_structure',
            'class' => 'form_structure',
        ];

        $data['include'] = '';

        $data['url'] = uri()->segment(2, 0);

        render('structure/index', $data, 'admin');
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
        if (Input::isAjax() && $validator['is_success'] === true) {

            $data = [];

            //Colonne su cui poter effettuare l'ordinamento
            $orderable = [
                1 => 'object_structures.structure_name',
                2 => 'structureName',
                5 => 'users.name',
                6 => 'updated_at'
            ];

            //Se l'utente è super Admin mostro la colonna dell'ente
            if (isSuperAdmin(true)) {
                $orderable[7] = 'full_name_institution';
            }

            //Setto proprietà datatable
            $dataTable = [];
            setDataTableData($dataTable, 'structure_name');

            //Query per i dati da mostrare nel datatable
            $totalRecords = StructuresModel::select('count(id) as allcount')
                ->count();

            $totalRecordsWithFilter = StructuresModel::search($dataTable['searchValue']);
            $totalRecordsWithFilter->select(['count(object_structures.id) as allcount'])
                ->join('users', 'users.id', '=', 'object_structures.owner_id', 'left outer')
                ->leftJoin('object_structures as os', function ($join) {
                    $join->on('os.id', '=', 'object_structures.structure_of_belonging_id');
                });

            if (!empty($dataTable['searchValue'])) {
                $totalRecordsWithFilter->orWhere('object_structures.id', '=', $dataTable['searchValue']);
            }

            $totalRecordsWithFilter = $totalRecordsWithFilter->count();

            $order = setOrderDatatable($dataTable['columnName'], $orderable, 'structure_name');

            $records = StructuresModel::search($dataTable['searchValue']);
            $records->select(['object_structures.id', 'object_structures.structure_name', 'object_structures.updated_at', 'object_structures.archived',
                'object_structures.publishing_status', 'object_structures.institution_id', 'object_structures.owner_id',
                'object_structures.structure_of_belonging_id', 'object_structures.articulation', 'i.full_name_institution',
                'users.name', 'os.structure_name as structureName'])
                ->with('structure_of_belonging:id,structure_of_belonging_id,structure_name,archived')
                ->with('responsibles:id,full_name,archived')
                ->with(['created_by' => function ($query) {
                    $query->withoutGlobalScopes([DeletedScope::class]);
                    $query->select(['id', 'name', 'deleted']);
                }])
                ->with('institution:id,full_name_institution')
                ->leftJoin('object_structures as os', function ($join) {
                    $join->on('os.id', '=', 'object_structures.structure_of_belonging_id');
                })
                ->join('institutions as i', 'object_structures.institution_id', '=', 'i.id', 'left outer')
                ->join('users', 'users.id', '=', 'object_structures.owner_id', 'left outer');

            if (!empty($dataTable['searchValue'])) {
                $records->orWhere('object_structures.id', '=', $dataTable['searchValue']);
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
                        $tmpArchResponsibles = Arr::pluck($record['responsibles'], 'archived');

                        $responsibles = str_replace(',', ',  ', implode(
                            ',',
                            array_map(
                                function ($resp) {
                                    return ('<small class="badge-primary mb-1">' . $resp . '</small>'
                                    );
                                },
                                $tmpResponsibles,
                                $tmpArchResponsibles
                            )
                        ));
                    } else {

                        $responsibles = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definiti">N.D.</small>';
                    }

                    $updateAt = !empty($record['updated_at']) ? '<small class="badge badge-info"> 
                                    <i class="far fa-clock"></i> '
                        . date('d-m-Y H:i:s', strtotime($record['updated_at'])) .
                        '</small>' : '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definita">N.D.</small>';

                    // Controllo se l'utente ha i permessi di modifica dei record o di scrittura(e quindi di modifica dei propri record)
                    $permits = ($this->acl->getCreate() && checkRecordOwner($record['owner_id']));
                    $updatePermits = ($this->acl->getUpdate() && checkRecordOwner($record['owner_id']));

                    //Setto i pulsanti delle actions da mostrare in base ai permessi dell'utente
                    $buttonAction = ($this->acl->getUpdate() || $this->acl->getDelete() || $permits) ? ButtonAction::create([
                        'edit' => $this->acl->getUpdate() || $permits,
                        'duplicate' => $this->acl->getCreate() && !$record['archived'],
                        'delete' => $this->acl->getDelete() || $updatePermits || $permits,
                    ])
                        ->addEdit('admin/structure/edit/' . $record['id'], $record['id'])
                        ->addDuplicate('admin/structure/duplicate/' . $record['id'], $record['id'])
                        ->addDelete('admin/structure/delete/' . $record['id'], $record['id'])
                        ->render() : '';

                    $icon = null;

                    //Setto i dati da mostrare nelle colonne del datatable
                    $setTempData = [];

                    $setTempData[] = ($this->acl->getCrud() || checkRecordOwner($record['owner_id']))
                        ? ButtonAction::checkList($record['structure_name'], $record['id'])
                        : '';

                    $link = true;

                    $setTempData[] = !empty($record['structure_name'])
                        ? $icon . (($link) ? '<a href="' . siteUrl('/page/40/details/' . $record['id'] . '/'
                                . urlTitle($record['structure_name'])) . '"target="_blank">' . escapeXss($record['structure_name']) . '</a>' : escapeXss($record['structure_name']))
                        : 'N.D.';

                    $setTempData[] = !empty($record['structure_of_belonging']['structure_name'])
                        ? escapeXss($record['structure_of_belonging']['structure_name'])
                        : '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definita">N.D.</small>';

                    $setTempData[] = $responsibles;
                    $setTempData[] = (empty($record['articulation'])) ? 'No' : 'Si';
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
     * @description Renderizza il form di creazione di una nuova struttura
     *
     * @return void
     * @throws Exception
     * @url /admin/structure/create.html
     * @method GET
     */
    public function create(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(__FUNCTION__);

        $data = [];

        //Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $data['is_box'] = uri()->uriString() === 'admin/structure/create-box';

        //Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {

            $this->breadcrumb->push('Strutture organizzative', 'admin/structure');
            $this->breadcrumb->push('Nuova', '/');
            $data['breadcrumbs'] = $this->breadcrumb->show();

            //Dati header della sezione
            $data['titleSection'] = 'Strutture organizzative';
            $data['subTitleSection'] = 'GESTIONE DEGLI UFFICI E DELLE STRUTTURE DELL\'ENTE';
            $data['sectionIcon'] = '<i class="fas fa-building fa-3x"></i>';
        }

        //Setto dati del form
        $data['formAction'] = '/admin/structure/store';
        $data['formSettings'] = [
            'name' => 'form_structure',
            'id' => 'form_structure',
            'class' => 'form_structure',
        ];
        $data['_storageType'] = 'insert';

        $data['responsibleIds'] = null;
        $data['toContactIds'] = null;

        $data['normativeIds'] = null;

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        render('structure/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua lo storage di una nuova Struttura
     *
     * @return void
     * @throws Exception
     * @url /admin/structure/store.html
     * @method POST
     */
    public function store(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new StructureValidator();
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
                    'structure_name' => strip_tags(Input::post('structure_name', true)),
                    'structure_of_belonging_id' => setDefaultData((int)strip_tags(Input::post('structure_of_belonging_id', true)), null, ['', null]),
                    'responsible_not_available' => setDefaultData(strip_tags(Input::post('responsible_not_available', true)), 0, ['', null]),
                    'referent_not_available_txt' => empty(Input::post('responsible_not_available')) ? setDefaultData(strip_tags(Input::post('referent_not_available_txt', true)), null, ['', null]) : null,
                    'ad_interim' => setDefaultData(strip_tags(Input::post('ad_interim', true)), null, ['', null]),
                    'email_not_available' => setDefaultData(strip_tags(Input::post('email_not_available', true)), 0, ['', null]),
                    'reference_email' => !empty(Input::post('email_not_available')) ? setDefaultData(strip_tags(Input::post('reference_email', true)), null, ['', null]) : null,
                    'email_not_available_txt' => empty(Input::post('email_not_available')) ? setDefaultData(strip_tags(Input::post('email_not_available_txt', true)), null, ['', null]) : null,
                    'certified_email' => setDefaultData(strip_tags(Input::post('certified_email', true)), null, ['', null]),
                    'phone' => setDefaultData(strip_tags(Input::post('phone', true)), null, ['', null]),
                    'fax' => setDefaultData(strip_tags(Input::post('fax', true)), null, ['', null]),
                    'description' => setDefaultData(Input::post('description', true), null, ['', null]),
                    'timetables' => setDefaultData(strip_tags(Input::post('timetables', true)), null, ['', null]),
                    'articulation' => setDefaultData(strip_tags(Input::post('articulation', true)), null, ['', null]),
                    'order' => setDefaultData(strip_tags(Input::post('order', true)), null, ['', null]),
                    'based_structure' => setDefaultData(strip_tags(Input::post('based_structure', true)), 0, ['', null]),
                    'address' => setDefaultData((Input::post('address', true)), null, ['', null]),
                    'lat' => strip_tags(Input::post('lat', true)),
                    'lon' => strip_tags(Input::post('lon', true)),
                    'address_detail' => setDefaultData(strip_tags(Input::post('address_detail', true)), null, ['', null]),
                ];

                // Storage nuova Struttura Organizzativa
                $insert = StructuresModel::createWithLogs($arrayValues);

                // Storage nelle tabelle di relazione
                $this->clear(
                    $insert,
                    !empty(Input::post('responsibles')) ? explode(',', strip_tags((string)Input::post('responsibles', true))) : null,
                    !empty(Input::post('toContacts')) ? explode(',', strip_tags((string)Input::post('toContacts', true))) : null,
                    Input::post('normatives', true)
                );

                // Storage allegati associati alla struttura
                $attach->storage('attach_files', 'structures', $insert->id, $insert['structure_name']);

                $json->set('message', __('success_save_operation', null, 'patos'));

                // Generazione nuovo token
                if (!referenceOriginForRegenerateToken()) {
                    Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
                }
            }
        } else {

            $code = $json->bad();
            $json->error('error', $check['errors']);
        }

        $json->setStatusCode($code);
        $json->response();
    }

    /**
     * @description Renderizza il form di modifica/duplicazione di una struttura
     *
     * @return void
     * @throws Exception
     * @url /admin/structure/edit/:id.html
     * @method GET
     */
    public function edit(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['update', 'create']);

        //Validatore che verifica se l'elemento da modificare esiste
        $validator = new StructureValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        $data = [];

        $segments = uri()->segmentArray();
        array_pop($segments);

        //Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $data['is_box'] = implode('/', $segments) === 'admin/structure/edit-box';

        if (!$validate['is_success']) {
            if (!$data['is_box']) {
                sessionSetNotify($validate['errors'], 'danger');
                redirect('admin/structure');
            } else {
                render('access_denied/modal_access_denied', [], 'admin');
                die();
            }
        }

        // Allegati
        $attach = new AttachmentArchive();

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $structure = Registry::get('structure');
        $structure = !empty($structure) ? $structure->toArray() : [];

        // Vedo in sessione se sono arrivato qui da un operazione di ripristino dal versioning
        $session = new Session();
        $restore = $session->getFlash('restore');

        //Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {
            $this->breadcrumb->push('Strutture organizzative', 'admin/structure');
            $this->breadcrumb->push('Modifica', '/');

            $data['breadcrumbs'] = $this->breadcrumb->show();

            //Dati header della sezione
            $data['titleSection'] = 'Strutture organizzative';
            $data['subTitleSection'] = 'GESTIONE DEGLI UFFICI E DELLE STRUTTURE DELL\'ENTE';
            $data['sectionIcon'] = '<i class="fas fa-building fa-3x"></i>';
        }

        // Controllo se si sta duplicando una struttura
        $isDuplicate = uri()->segment(3, 0) === 'duplicate';

        // Setto il tipo di storage
        $data['_storageType'] = $isDuplicate ? 'duplicate' : 'update';

        //In base al tipo di storage setto l'action del form
        $data['formAction'] = ($isDuplicate) ? '/admin/structure/store' : '/admin/structure/update';
        $data['formSettings'] = [
            'name' => 'form_structure',
            'id' => 'form_structure',
            'class' => 'form_structure'
        ];

        $data['structure'] = $structure;

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'structures',
            $structure['id'],
            '*'
        );

        // Labels
        $data['labels'] = [];

        $data['responsibleIds'] = Arr::pluck($structure['responsibles'], 'id');
        $data['toContactIds'] = Arr::pluck($structure['to_contact'], 'id');
        $data['normativeIds'] = Arr::pluck($structure['normatives'], 'id');

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = $structure['institution_id'];

        $data['restore'] = $restore;
        $data['seo'] = $structure['p_s_d_r'] ?? null;

        render('structure/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua l'update di una Struttura
     *
     * @return void
     * @throws Exception
     * @url /admin/structure/update.html
     * @method POST
     */
    public function update(): void
    {
        $json = new JsonResponse();
        $code = $json->success();

        //Validatore form
        $validator = new StructureValidator();
        $check = $validator->check('update');

        if ($check['is_success']) {
            $doAction = true;

            $structureId = (int)strip_tags(Input::post('id', true));

            // Recupero la struttura organizzativa attuale prima di modificarla e la salvo nel versioning
            $structure = StructuresModel::where('id', $structureId)
                ->with('responsibles:id,full_name')
                ->with('to_contact:id,full_name')
                ->with('normatives:id')
                ->with('structure_of_belonging:id,structure_name,structure_of_belonging_id')
                ->with('all_attachs');

            $structure = $structure->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute(__FUNCTION__, (!checkRecordOwner($structure['owner_id']) && $this->acl->getCreate()));

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
                $data['structure_name'] = strip_tags(Input::post('structure_name'), true);
                $data['structure_of_belonging_id'] = setDefaultData(strip_tags(Input::post('structure_of_belonging_id', true)), null, ['', null]);
                $data['responsible_not_available'] = setDefaultData(strip_tags(Input::post('responsible_not_available', true)), 0, ['', null]);
                $data['referent_not_available_txt'] = empty(Input::post('responsible_not_available')) ? strip_tags(escapeXss(Input::post('referent_not_available_txt', true))) : null;
                $data['ad_interim'] = setDefaultData(strip_tags(Input::post('ad_interim', true)), null, ['', null]);
                $data['email_not_available'] = setDefaultData(strip_tags(Input::post('email_not_available', true)), 0, ['', null]);
                $data['reference_email'] = setDefaultData(strip_tags(Input::post('reference_email', true)), null, ['', null]);
                $data['email_not_available_txt'] = setDefaultData(strip_tags(Input::post('email_not_available_txt', true)), null, ['', null]);
                $data['certified_email'] = setDefaultData(strip_tags(Input::post('certified_email', true)), null, ['', null]);
                $data['phone'] = setDefaultData(strip_tags(Input::post('phone', true)), null, ['', null]);
                $data['fax'] = setDefaultData(strip_tags(Input::post('fax', true)), null, ['', null]);
                $data['description'] = Input::post('description', true);
                $data['timetables'] = strip_tags(Input::post('timetables', true));
                $data['articulation'] = setDefaultData(strip_tags(Input::post('articulation', true)), null, ['', null]);
                $data['order'] = setDefaultData(strip_tags(Input::post('order', true)), null, ['', null]);
                $data['based_structure'] = setDefaultData(strip_tags(Input::post('based_structure', true)), 0, ['', null]);
                $data['address'] = setDefaultData(strip_tags(Input::post('address', true)), null, ['', null]);
                $data['lat'] = strip_tags(Input::post('lat', true));
                $data['lon'] = strip_tags(Input::post('lon', true));
                $data['address_detail'] = setDefaultData(strip_tags(Input::post('address_detail', true)), null, ['', null]);

                // Update Struttura
                StructuresModel::where('id', $structureId)->updateWithLogs($structure, $data);

                // Svuoto le tabelle di relazione e le aggiorno
                $this->clear(
                    $structure,
                    !empty(Input::post('responsibles')) ? explode(',', strip_tags(Input::post('responsibles', true))) : null,
                    !empty(Input::post('toContacts')) ? explode(',', strip_tags(Input::post('toContacts', true))) : null,
                    Input::post('normatives', true)
                );

                // Upload allegati associati al personale.
                $attach->update(
                    'attach_files',
                    'structures',
                    $structureId,
                    $structure['institution_id'],
                    $structure['structure_name']
                );

                $json->set('message', __('success_update_operation', null, 'patos'));

                // Generazione nuovo token
                if (!referenceOriginForRegenerateToken(3, 'edit-box.html')) {
                    Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
                }
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
     *
     * @param StructuresModel|null $structure    Struttura per cui inserire i dati di relazione
     * @param array|int|null       $responsibles Personale responsabile della struttura
     * @param array|int|null       $toContacts   Personale da contattare per la struttura
     * @param array|int|null       $normatives   Normative associate alla struttura
     * @return void
     */
    protected function clear(StructuresModel $structure = null, array|int $responsibles = null, array|int $toContacts = null, array|int $normatives = null): void
    {
        $dataResponsibles = [];
        if ($responsibles !== null) {
            foreach ($responsibles as $responsible) {
                $dataResponsibles[] = is_array($responsible) ? $responsible['id'] : $responsible;
            }
        }
        //Insert/Update nella tabella di relazione
        $structure->responsibles()->syncWithPivotValues($dataResponsibles, ['typology' => 'responsible']);

        $dataToContacts = [];
        if ($toContacts !== null) {
            foreach ($toContacts as $toContact) {
                $dataToContacts[] = is_array($toContact) ? $toContact['id'] : $toContact;
            }
        }
        //Insert/Update nella tabella di relazione
        $structure->to_contact()->syncWithPivotValues($dataToContacts, ['typology' => 'toContact']);

        $dataNormatives = [];
        if ($normatives !== null) {
            foreach ($normatives as $normative) {
                $dataNormatives[] = is_array($normative) ? strip_tags($normative['id']) : strip_tags($normative);
            }
        }
        //Insert/Update nella tabella di relazione
        $structure->normatives()->syncWithPivotValues($dataNormatives, ['typology' => 'normative-reference']);

    }

    /**
     * @description Funzione che effettua l'eliminazione di una Struttura
     *
     * @return void
     * @throws Exception
     * @url /admin/structure/delete/:id.html
     * @method GET
     */
    public function delete(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute([__FUNCTION__, 'create', 'update'], true);

        //Validatore che verifica se l'elemento da eliminare esiste
        $validator = new StructureValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getDelete() && $this->acl->getUpdate());

        if (!$validate['is_success']) {
            sessionSetNotify($validate['errors'], 'danger');
            redirect('admin/structure');
            exit();
        }

        // Se la validazione va a buon fine recupero l'elemento da eliminare dal registro
        $structure = Registry::get('structure');

        //Eliminazione della struttura settando deleted = 1
        $structure->deleteWithLogs($structure);

        sessionSetNotify(__('success_delete_operation', null, 'patos'));

        // Generazione nuovo token
        Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        redirect('admin/structure');
    }

    /**
     * @description Funzione per l'eliminazione multipla
     *
     * @return void
     * @throws Exception
     * @url /admin/structure/deletes.html
     * @method GET
     */
    public function deletes(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('delete');

        $validator = new StructureValidator();
        $validate = $validator->multipleSelection();

        if ($validate['is_success']) {

            sessionSetNotify('Operazione avvenuta con successo');

            //Recupero gli elementi da eliminare dal registro
            $structures = Registry::get('__ids__multi_select_profile__');

            //Elimino gli elementi
            foreach ($structures as $structure) {
                $structure->deleteWithLogs($structure);
            }

            // Generazione nuovo token
            Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        } else {

            sessionSetNotify($validate['errors'], 'danger');
        }

        redirect('/admin/structure');
    }
}
