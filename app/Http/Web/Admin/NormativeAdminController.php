<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Admin;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Helpers\ActivityLog;
use Helpers\S;
use Helpers\Utility\AttachmentArchive;
use Helpers\Utility\ButtonAction;
use Helpers\Validators\DatatableValidator;
use Helpers\Validators\NormativeValidator;
use Model\NormativesModel;
use Scope\DeletedScope;
use System\Arr;
use System\Input;
use System\JsonResponse;
use System\Registry;
use System\Session;

/**
 *
 * Controller Normative
 *
 */
class NormativeAdminController extends BaseAuthController
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
     * @description Renderizza la pagina index delle Normative
     *
     * @return void
     * @throws Exception
     * @url /admin/normative.html
     * @method GET
     */
    public function index(): void
    {
        // Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['read', 'create']);

        $this->breadcrumb->push('Normativa', '/');
        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        // Dati header della sezione
        $data['titleSection'] = 'Normativa';
        $data['subTitleSection'] = 'GESTIONE DELLA NORMATIVA DELL\'ENTE';
        $data['sectionIcon'] = '<i class="fas fa-paste fa-3x"></i>';

        $data['formAction'] = '/admin/normative';
        $data['formSettings'] = [
            'name' => 'form_normativa',
            'id' => 'form_normativa',
            'class' => 'form_normativa',
        ];

        $data['include'] = '';

        $data['url'] = uri()->segment(2, 0);

        render('normative/index', $data, 'admin');
    }

    /**
     * @description Funzione per la paginazione dei dati nel datatable
     *
     * @return void
     * @url /admin/normative/list.html
     * @method AJAX
     * @throws Exception
     */
    public function asyncPaginateDatatable(): void
    {
        // Setto il metodo della rotta per i permessi
        $this->acl->setRoute('read');

        //Validatore datatable
        $datatableValidator = new DatatableValidator();
        $validator = $datatableValidator->validate();

        // Controllo se è una richiesta Ajax e se il datatable è stato validato correttamente
        if (Input::isAjax() === true && $validator['is_success'] === true) {

            $data = [];

            // Colonne su cui poter effettuare l'ordinamento
            $orderable = [
                1 => 'name',
                4 => 'userName',
                5 => 'updated_at'
            ];

            //Setto proprietà datatable
            $dataTable = [];
            setDataTableData($dataTable, 'name');

            //Query per i dati da mostrare nel datatable
            $totalRecords = NormativesModel::select('count(id) as allcount')
                ->count();

            $totalRecordsWithFilter = NormativesModel::search($dataTable['searchValue'])
                ->select('count(object_normatives.id) as allcount')
                ->join('users', 'users.id', '=', 'object_normatives.owner_id', 'left outer');

            if (!empty($dataTable['searchValue'])) {
                $totalRecordsWithFilter->orWhere('object_normatives.id', '=', $dataTable['searchValue']);
            }
            // Inclusione di librerie esterne
            $totalRecordsWithFilter = $totalRecordsWithFilter->count();

            $order = setOrderDatatable($dataTable['columnName'], $orderable, 'name');

            $records = NormativesModel::search($dataTable['searchValue'])
                ->select(['object_normatives.id', 'object_normatives.owner_id', 'object_normatives.institution_id', 'object_normatives.name', 'normative_topic',
                    'normative_link', 'object_normatives.updated_at',
                    'users.name as userName', 'i.full_name_institution'])
                ->join('users', 'users.id', '=', 'object_normatives.owner_id', 'left outer')
                ->with('structures:id,structure_name,archived')
                ->with(['created_by' => function ($query) {
                    $query->withoutGlobalScopes([DeletedScope::class]);
                    $query->select(['id', 'name', 'deleted']);
                }])
                ->with(['institution:id,full_name_institution'])
                ->with('all_attachs:id,archive_id');

            if (!empty($dataTable['searchValue'])) {
                $records->orWhere('object_normatives.id', '=', $dataTable['searchValue']);
            }

            $records = $records->join('institutions as i', 'object_normatives.institution_id', '=', 'i.id', 'left outer')
                ->orderBy($order, $dataTable['columnSortOrder'])
                ->offset($dataTable['start'])
                ->limit($dataTable['rowPerPage'])
                ->get()
                ->toArray();

            $checkWf = false;
            $manageableStates = [];
            $getIdentity = authPatOs()->getIdentity(['id']);

            foreach ($records as $record) {

                if ($record['normative_topic'] == 2) {
                    $recordUrl = siteUrl('/page/125/details/' . $record['id'] . '/' . urlTitle($record['name']));
                } else {
                    $recordUrl = siteUrl('/page/24/details/' . $record['id'] . '/' . urlTitle($record['name']));
                }

                if (!empty($record['structures']) && is_array($record['structures'])) {

                    $tmpStructures = Arr::pluck($record['structures'], 'structure_name');
                    $tmpArchStructures = Arr::pluck($record['structures'], 'archived');
                    $structures = str_replace(',', ',' . nbs(2), implode(
                        ',',
                        array_map(
                            function ($structure, $archived) {
                                return ('<small class="badge-primary mb-1">'
                                    . ($archived
                                        ? '<i class="fas fa-folder archived" data-toggle="tooltip" data-placement="top" data-original-title="Questo elemento è stato archiviato"></i> '
                                        : '') . escapeXss($structure) . '</small>');
                            },
                            $tmpStructures,
                            $tmpArchStructures
                        )
                    ));
                } else {

                    $structures = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definite">N.D.</small>';
                }

                if (!empty($record['updated_at'])) {
                    $updateAt = '<small class="badge badge-info"> 
                                    <i class="far fa-clock"></i> '
                        . date('d-m-Y H:i:s', strtotime($record['updated_at'])) .
                        '</small>';
                } else {
                    $updateAt = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definita">N.D.</small>';
                }

                // Controllo se l'utente ha i permessi di modifica dei record o di scrittura (e quindi di modifica dei propri record)
                $permits = ($this->acl->getCreate() && checkRecordOwner($record['owner_id'],$getIdentity['id']));
                $updatePermits = ($this->acl->getUpdate() && checkRecordOwner($record['owner_id'],$getIdentity['id']));
                $wfPermits = false;
                $workflowState = $record['workflow_state'] ?? 'finale';
                $wfOwnerPermits = $permits && $workflowState == 'iniziale';

                // Setto i pulsanti delle actions da mostrare in base ai permessi dell'utente
                $buttonAction = ($this->acl->getUpdate() || $this->acl->getDelete() || $permits) ? ButtonAction::create([
                    'edit' => $checkWf ? (($this->acl->getUpdate() && $wfPermits) || $wfOwnerPermits) : $this->acl->getUpdate() || $permits,
                    'duplicate' => $this->acl->getCreate() && $workflowState == 'finale',
                    'versioning' => getAclVersioning(),
                    'delete' => $checkWf ? (($this->acl->getDelete() && $wfPermits) || $wfOwnerPermits) : $this->acl->getDelete() || $updatePermits || $permits,
                ])
                    ->addEdit('admin/normative/edit/' . $record['id'], $record['id'], '', true)
                    ->addDuplicate('admin/normative/duplicate/' . $record['id'], $record['id'])
                    ->addCustomButton('b_a_v', siteUrl('admin/normative/versioning'), $record['id'], '', '', 'versioning')
                    ->addCustomButton('s_r_l',siteUrl('admin/normative/show-record-log/'.$record['id']), $record['id'], '14', 'show-record-log', 'view')
                    ->addCustomButton('wf_a_f_e',"", $record['id'], $workflowState, '', 'view')
                    ->addDelete('admin/normative/delete/' . $record['id'], $record['id'])
                    ->render() : '';

                $icon = null;

                // Setto i dati da mostrare nelle colonne del datatable
                $setTempData = [];

                $checkBox = ($this->acl->getCrud() || checkRecordOwner($record['owner_id'])) ? ButtonAction::checkList('item[]', $record['id']) : '';

                $setTempData[] = $checkBox;
                $setTempData[] = $icon . (!empty($record['name'])
                        ? '<a href="' . $recordUrl . '" target="_blank">' . escapeXss(html_entity_decode($record['name'])) . '</a>'
                        : 'N.D.');
                $setTempData[] = $structures;
                $setTempData[] = !empty($record['normative_link']) ? escapeXss($record['normative_link']) : 'N.D.';
                $setTempData[] = createdByCheckDeleted(@$record['created_by']['name'], @$record['created_by']['deleted']);
                $setTempData[] = $updateAt;
                $setTempData[] = !empty($record['all_attachs'])
                    ? '<small class="badge badge-success" style="background-color:#28a745;color:#fff; width: 60px;" data-toggle="tooltip" title="Sono presenti allegati">Si</small>'
                    : '<small class="badge badge-danger"  style="background-color:#dc3545;color:#fff; width: 60px;" data-toggle="tooltip" title="Non sono presenti allegati">No</small>';

                $setTempData[] = $buttonAction;

                $data[] = $setTempData;
            }

            $response = [
                "draw" => intval($dataTable['draw']),
                "iTotalRecords" => $totalRecords,
                "iTotalDisplayRecords" => $totalRecordsWithFilter,
                "aaData" => $data,
            ];
            jsonOut($response);
        }
    }

    /**
     * @description Renderizza il form di creazione di una nuova Normativa
     *
     * @return void
     * @throws Exception
     * @url /admin/normative/create.html
     * @method GET
     */
    public function create(): void
    {
        // Setto il metodo della rotta per i permessi
        $this->acl->setRoute(__FUNCTION__);

        $data = [];

        // Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $data['is_box'] = uri()->uriString() === verifyFirstOrLastSlash('admin/normative/create-box', true);

        // Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {

            $this->breadcrumb->push('Normativa', 'admin/normative');
            $this->breadcrumb->push('Nuova', '/');

            $data['breadcrumbs'] = $this->breadcrumb->show();

            // Dati header della sezione
            $data['titleSection'] = 'Normativa';
            $data['subTitleSection'] = 'GESTIONE DELLA NORMATIVA DELL\'ENTE';
            $data['sectionIcon'] = '<i class="fas fa-paste fa-3x"></i>';
        }

        // Setto dati del form
        $data['formAction'] = '/admin/normative/store';
        $data['formSettings'] = [
            'name' => 'form_normativa',
            'id' => 'form_normativa',
            'class' => 'form_normativa',
        ];
        $data['_storageType'] = 'insert';

        // Labels
        $data['labels'] = [];

        $data['actTypes'] = [null => ''] + config('normativeTypologies', [], 'app');

        // ID dell'ente corrente.
        $data['institution_id'] = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        $normativeTopic = [
            '' => '',
            1=>'Organizzazione dell\'Ente (pubblica in Riferimenti normativi su organizzazione e attività)',
            4=>'Attività (pubblica in Riferimenti normativi su organizzazione e attività)',
            2=>'Sovvenzioni e contributi (pubblica in Criteri e modalità)',
            3=>'Altro (disponibile solo in back-end e non pubblicato)'
        ];

        $data['normativeTopic'] = $normativeTopic;

        render('normative/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua lo storage di una nuova Normativa
     *
     * @return void
     * @throws Exception
     * @url /admin/normative/store.html
     * @method POST
     */
    public function store(): void
    {
        // Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create', true);

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new NormativeValidator();
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
                    'act_type' => setDefaultData(strip_tags(Input::post('act_type', true)), null, ['']),
                    'number' => strip_tags(Input::post('number', true)),
                    'protocol' => setDefaultData(strip_tags(Input::post('protocol', true)), null, ['']),
                    'issue_date' => !empty(Input::post('issue_date')) ? convertDateToDatabase(strip_tags(Input::post('issue_date', true))) : null,
                    'name' => strip_tags(Input::post('name', true)),
                    'normative_topic' => strip_tags(Input::post('normative_topic', true)),
                    'normative_link' => strip_tags(Input::post('normative_link', true)),
                    'description' => Input::post('description', true)
                ];

                // Storage nuova Normativa
                $insert = NormativesModel::createWithLogs($arrayValues);

                // Svuoto le tabelle di relazione e le aggiorno
                $this->clear(
                    $insert,
                    !empty(Input::post('structures')) ? explode(',', strip_tags((string)Input::post('structures', true))) : null,
                );

                // Storage allegati associati al personale.
                $attach->storage('attach_files', 'normatives', $insert->id, $insert['name']);

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
     * @description Renderizza il form di modifica/duplicazione di una Normativa
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/normative/edit/:id.html
     * @method GET
     */
    public function edit(): void
    {
        // Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['update', 'create']);

        // Validatore che verifica se l'elemento da modificare esiste
        $validator = new NormativeValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        $data = [];

        $segments = uri()->segmentArray();
        array_pop($segments);

        // Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $data['is_box'] = implode('/', $segments) === 'admin/normative/edit-box';

        if (!$validate['is_success']) {
            if(!$data['is_box']){
                redirect('admin/normative', sessionSetNotify($validate['errors'], 'danger'));
            } else {
                render('access_denied/modal_access_denied', [], 'admin');
                die();
            }
        }

        // Allegati
        $attach = new AttachmentArchive();

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $normative = Registry::get('normative');
        $normative = !empty($normative) ? $normative->toArray() : [];

        // Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {
            $this->breadcrumb->push('Normativa', 'admin/normative');
            $this->breadcrumb->push('Modifica', '/');

            $data['breadcrumbs'] = $this->breadcrumb->show();

            //Dati header della sezione
            $data['titleSection'] = 'Normativa';
            $data['subTitleSection'] = 'GESTIONE DELLA NORMATIVA DELL\'ENTE';
            $data['sectionIcon'] = '<i class="fas fa-paste fa-3x"></i>';
        }

        // Vedo in sessione se sono arrivato qui da un operazione di ripristino dal versioning
        $session = new Session();
        $restore = $session->getFlash('restore');
        $data['restore'] = $restore;

        // Controllo se si sta duplicando un tasso di assenza
        $isDuplicate = uri()->segment(3, 0) === 'duplicate';

        // Setto il tipo di storage
        $data['_storageType'] = $isDuplicate ? 'duplicate' : 'update';

        // In base al tipo di storage setto l'action del form
        $data['formAction'] = ($isDuplicate) ? '/admin/normative/store' : '/admin/normative/update';
        $data['formSettings'] = [
            'name' => 'form_normative',
            'id' => 'form_normative',
            'class' => 'form_normative',
        ];

        $date = convertDateToForm($normative['issue_date']);

        $data['normative'] = $normative;

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'normatives',
            $normative['id'],
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

        // ID dell'ente corrente.
        $data['institution_id'] = $normative['institution_id'];

        $data['issue_date'] = $date['date'];

        $data['structureIds'] = Arr::pluck($normative['structures'], 'id');
        $data['seo'] = $normative['p_s_d_r'] ?? null;

        $data['actTypes'] = [null => ''] + config('normativeTypologies', [], 'app');

        $normativeTopic = [
            '' => '',
            1=>'Organizzazione dell\'Ente (pubblica in Riferimenti normativi su organizzazione e attività)',
            4=>'Attività (pubblica in Riferimenti normativi su organizzazione e attività)',
            2=>'Sovvenzioni e contributi (pubblica in Criteri e modalità)',
            3=>'Altro (disponibile solo in back-end e non pubblicato)'
        ];

        //Argomenti della normativa
        $data['normativeTopic'] = $normativeTopic;

        render('normative/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua l'update di una Normativa
     *
     * @return void
     * @throws Exception
     * @url /admin/normative/update.html
     * @method POST
     */
    public function update(): void
    {
        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new NormativeValidator();
        $check = $validator->check('update');

        if ($check['is_success']) {
            $doAction = true;

            $normativeId = (int)strip_tags(Input::post('id', true));

            // Recupero la normativa attuale prima di modificarla e la salvo nel versioning
            $normative = NormativesModel::where('id', $normativeId)
                ->with('structures:id,structure_name')
                ->with('all_attachs');


            $normative = $normative->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute(checkRecordOwner($normative['owner_id']) ? ['update', 'create'] : 'update', true);

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
                $data['act_type'] = setDefaultData(strip_tags(Input::post('act_type', true)), null, ['']);
                $data['number'] = strip_tags(Input::post('number', true));
                $data['protocol'] = setDefaultData(strip_tags(Input::post('protocol', true)), null, ['']);
                $data['issue_date'] = !empty(Input::post('issue_date')) ? convertDateToDatabase(strip_tags(Input::post('issue_date', true))) : null;
                $data['name'] = strip_tags(Input::post('name', true));
                $data['normative_topic'] = strip_tags(Input::post('normative_topic', true));
                $data['normative_link'] = strip_tags(Input::post('normative_link', true));
                $data['description'] = Input::post('description', true);

                // Update Normativa
                NormativesModel::where('id', $normativeId)->updateWithLogs($normative, $data);

                // Svuoto le tabelle di relazione e le aggiorno
                $this->clear(
                    $normative,
                    !empty(Input::post('structures')) ? explode(',', strip_tags((string)Input::post('structures', true))) : null
                );

                // Upload allegati associati al personale.
                $attach = new AttachmentArchive();
                $dataAttach = $attach->update(
                    'attach_files',
                    'normatives',
                    $normativeId,
                    $normative['institution_id'],
                    $normative['name']
                );


                // Errore di alcuni o tutti gli allegati nella pagina.
                if (!empty($dataAttach['error'])) {
                    $code = $json->bad();
                    $json->set('error_partial_attach', $attach->errorsToString());
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
     * @param NormativesModel|null $normative  Id della Normativa
     * @param int|array|null       $structures Strutture associate alla normativa
     * @return void
     */
    protected function clear(NormativesModel $normative = null, int|array $structures = null): void
    {
        $dataStructures = [];
        if (!empty($structures)) {
            foreach ($structures as $structure) {
                $dataStructures[] = is_array($structure) ? $structure['id'] : $structure;
            }
        }
        //Insert/Update nella tabella di relazione
        $normative->structures()->syncWithPivotValues($dataStructures, ['typology' => 'valid-normatives']);
    }


    /**
     * @description Funzione che effettua l'eliminazione di una Normativa
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/normative/delete.html
     * @method GET
     */
    public function delete(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute([__FUNCTION__, 'create', 'update'], true);

        // Validatore che verifica se l'elemento da eliminare esiste
        $validator = new NormativeValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getDelete() && $this->acl->getUpdate());

        if (!$validate['is_success']) {
            redirect('admin/normative', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Se la validazione va a buon fine recupero l'elemento da eliminare dal registro
        $normative = Registry::get('normative');

        // Elimino la normativa
        $normative->deleteWithLogs($normative);

        sessionSetNotify(__('success_delete_operation', null, 'patos'));

        redirect('admin/normative');
    }

    /**
     * @description Funzione per l'eliminazione multipla
     *
     * @return void
     * @throws Exception
     * @url /admin/normative/deletes.html
     * @method GET
     */
    public function deletes(): void
    {
        // Setto il metodo della rotta per i permessi
        $this->acl->setRoute('delete', true);

        $validator = new NormativeValidator();
        $validate = $validator->multipleSelection();

        if ($validate['is_success']) {

            sessionSetNotify('Operazione avvenuta con successo');

            //Recupero gli elementi da eliminare dal registro
            $normatives = Registry::get('__ids__multi_select_profile__');

            //Elimino gli elementi
            foreach ($normatives as $normative) {
                $normative->deleteWithLogs($normative);
            }

        } else {

            sessionSetNotify($validate['errors'], 'danger');
        }

        redirect('/admin/normative');
    }

}
