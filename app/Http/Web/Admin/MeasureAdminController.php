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
use Helpers\Validators\MeasureValidator;
use Model\MeasuresModel;
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
 * Controller Provvedimenti Amministrativi
 *
 */
class MeasureAdminController extends BaseAuthController
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
     * @description Renderizza la pagina index dei Provvedimenti
     * @return void
     * @throws Exception
     * @url /assignment/measure.html
     * @method GET
     */
    public function index(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['read', 'create']);

        $this->breadcrumb->push('Provvedimenti Amministrativi', '/');
        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Provvedimenti Amministrativi';
        $data['subTitleSection'] = 'GESTIONE DEI PROVVEDIMENTI POLITICI E DIRIGENZIALI';
        $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';

        $data['formAction'] = '/admin/measure';
        $data['formSettings'] = [
            'name' => 'form_measure',
            'id' => 'form_measure',
            'class' => 'form_measure',
        ];

        $data['include'] = '';

        $data['url'] = uri()->segment(2, 0);

        render('measure/index', $data, 'admin');
    }

    /**
     * @description Funzione per la paginazione dei dati nel datatable
     *
     * @return void
     * @url /admin/measure/list.html
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
                2 => 'number',
                3 => 'object_measures.type',
                5 => 'date',
                6 => 'users.name',
                7 => 'updated_at'
            ];

            //Setto proprietà datatable
            $dataTable = [];
            setDataTableData($dataTable, 'object');

            //Query per i dati da mostrare nel datatable
            $totalRecords = MeasuresModel::select('count(id) as allcount')
                ->count();

            $totalRecordsWithFilter = MeasuresModel::search($dataTable['searchValue'])
                ->select(['count(object_measures.id) as allcount'])
                ->join('users', 'users.id', '=', 'object_measures.owner_id', 'left outer');

            if (!empty($dataTable['searchValue'])) {
                $totalRecordsWithFilter->orWhere('object_measures.id', '=', $dataTable['searchValue']);
            }

            // Inclusione di librerie esterne

            $totalRecordsWithFilter = $totalRecordsWithFilter->count();

            $order = setOrderDatatable($dataTable['columnName'], $orderable, 'object');

            $records = MeasuresModel::search($dataTable['searchValue'])
                ->select([
                    'object_measures.id',
                    'object_measures.owner_id',
                    'object_measures.institution_id',
                    'object',
                    'object_measures.updated_at',
                    'date',
                    'object_measures.type',
                    'number',
                    'users.name as userName',
                    'i.full_name_institution'
                ])
                ->join('users', 'users.id', '=', 'object_measures.owner_id', 'left outer')
                ->with('structures:id,structure_name,archived')
                ->with(['created_by' => function ($query) {
                    $query->withoutGlobalScopes([DeletedScope::class]);
                    $query->select(['id', 'name', 'deleted']);
                }])
                ->with(['institution' => function ($query) {
                    $query->select(['id', 'full_name_institution']);
                }])
                ->with('all_attachs:id,archive_id');

            if (!empty($dataTable['searchValue'])) {
                $records->orWhere('object_measures.id', '=', $dataTable['searchValue']);
            }

            $records = $records->join('institutions as i', 'object_measures.institution_id', '=', 'i.id', 'left outer')
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

                $checkWf = false;
                $manageableStates = [];
                $getIdentity = authPatOs()->getIdentity(['id']);

                foreach ($records as $record) {

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

                    $measureDate = !empty($record['date'])
                        ? ('<small class="badge badge-info"> 
                            <i class="far fa-calendar-alt"></i> ' .
                            date('d-m-Y', strtotime($record['date'])) .
                            '</small>')
                        : '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definita">N.D.</small>';

                    if (!empty($record['updated_at'])) {
                        $updateAt = '<small class="badge badge-info"> 
                                    <i class="far fa-clock"></i> '
                            . date('d-m-Y H:i:s', strtotime($record['updated_at'])) . '</small>';
                    } else {
                        $updateAt = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definita">N.D.</small>';
                    }

                    // Controllo se l'utente ha i permessi di modifica dei record o di scrittura (e quindi di modifica dei propri record)
                    $permits = ($this->acl->getCreate() && checkRecordOwner($record['owner_id'], $getIdentity['id']));
                    $updatePermits = ($this->acl->getUpdate() && checkRecordOwner($record['owner_id'], $getIdentity['id']));
                    $wfPermits = false;
                    $workflowState = $record['workflow_state'] ?? 'finale';
                    $wfOwnerPermits = $permits && $workflowState == 'iniziale';

                    $editUrl = 'edit';
                    $duplicateUrl = 'duplicate';

                    //Setto i pulsanti delle actions da mostrare in base ai permessi dell'utente
                    $buttonAction = ($this->acl->getUpdate() || $this->acl->getDelete() || $permits) ? ButtonAction::create([
                        'edit' => $checkWf ? (($this->acl->getUpdate() && $wfPermits) || $wfOwnerPermits) : $this->acl->getUpdate() || $permits,
                        'duplicate' => $this->acl->getCreate() && $workflowState == 'finale',
                        'versioning' => getAclVersioning(),
                        'delete' => $checkWf ? (($this->acl->getDelete() && $wfPermits) || $wfOwnerPermits) : $this->acl->getDelete() || $updatePermits || $permits,
                        'scp' => getAclProfileInfo('scp') && $workflowState == 'finale',
                    ])
                        ->addEdit('admin/measure/' . $editUrl . '/' . $record['id'], $record['id'],'',true)
                        ->addDuplicate('admin/measure/' . $duplicateUrl . '/' . $record['id'], $record['id'])
                        ->addCustomButton('b_a_v', siteUrl('admin/measure/versioning'), $record['id'], '', '', 'versioning')
                        ->addCustomButton('b_a_scp', siteUrl('admin/measure/scp'), $record['id'], 'measure', '', 'scp')
                        ->addCustomButton('s_r_l', siteUrl('admin/measure/show-record-log/' . $record['id']), $record['id'], '26', 'show-record-log', 'view')
                        ->addCustomButton('wf_a_f_e', "", $record['id'], $workflowState, '', 'view')
                        ->addCustomButton(
                            's_no_app_io',
                            '#!',
                            $record['id'],
                            'measure',
                            'Invia notifica App IO',
                            'view'
                        )
                        ->addDelete('admin/measure/delete/' . $record['id'], $record['id'])
                        ->render() : '';

                    $icon = null;

                    //Setto i dati da mostrare nelle colonne del datatable
                    $setTempData = [];

                    $checkBox = ($this->acl->getCrud() || checkRecordOwner($record['owner_id'])) ? ButtonAction::checkList('item[]', $record['id']) : '';

                    $setTempData[] = $checkBox;

                    $setTempData[] = $icon . (!empty($record['object'])
                        ? '<a href="' . siteUrl('/page/9/details/' . $record['id'] . '/' . urlTitle($record['object'])) . '" target="_blank">' . escapeXss(html_entity_decode($record['object'])) . '</a>'
                        : 'N.D.');
                    $setTempData[] = !empty($record['number']) ? escapeXss($record['number']) : 'N.D.';
                    $setTempData[] = escapeXss(config('measureTypologies', [], 'app')[$record['type']] ?? 'N.D.');
                    $setTempData[] = $structures;
                    $setTempData[] = $measureDate;
                    $setTempData[] = createdByCheckDeleted(@$record['created_by']['name'], @$record['created_by']['deleted']);
                    $setTempData[] = $updateAt;
                    $setTempData[] = !empty($record['all_attachs'])
                        ? '<small class="badge badge-success" style="background-color:#28a745;color:#fff; width: 60px;" data-toggle="tooltip" title="Sono presenti allegati">Si</small>'
                        : '<small class="badge badge-danger"  style="background-color:#dc3545;color:#fff; width: 60px;" data-toggle="tooltip" title="Non sono presenti allegati">No</small>';

                    $setTempData[] = $buttonAction;

                    $data[] = $setTempData;
                }

                $response['aaData'] = $data;
            }
            jsonOut($response);
        }
    }

    /**
     * @description Renderizza il form di creazione di un nuovo Provvedimento
     *
     * @return void
     * @method GET
     * @throws Exception
     * @url /admin/measure/create.html
     */
    public function create(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(__FUNCTION__);

        $data = [];

        //Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $data['is_box'] = uri()->uriString() === verifyFirstOrLastSlash('admin/measure/create-box', true);

        //Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {

            $this->breadcrumb->push('Provvedimenti Amministrativi', 'admin/measure');
            $this->breadcrumb->push('Nuovo', '/');

            $data['breadcrumbs'] = $this->breadcrumb->show();

            //Dati header della sezione
            $data['titleSection'] = 'Provvedimenti Amministrativi';
            $data['subTitleSection'] = 'GESTIONE DEI PROVVEDIMENTI POLITICI E DIRIGENZIALI';
            $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';
        }

        $data['formAction'] = '/admin/measure/store';
        $data['formSettings'] = [
            'name' => 'form_measure',
            'id' => 'form_measure',
            'class' => 'form_measure',
        ];
        $data['_storageType'] = 'insert';


        $data['typologies'] = [null => ''] + config('measureTypologies', [], 'app');

        // Labels
        $data['labels'] = [];

        // ID dell'ente corrente.
        $data['institution_id'] = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        render('measure/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua lo storage di un nuovo Provvedimento
     *
     * @return void
     * @method POST
     * @throws Exception
     * @url /admin/measure/store.html
     */
    public function store(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new MeasureValidator();
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
                    'number' => strip_tags(Input::post('number', true)),
                    'object' => strip_tags(Input::post('object', true)),
                    'type' => setDefaultData(strip_tags(Input::post('type', true)), null, ['']),
                    'date' => !empty(Input::post('date')) ? convertDateToDatabase(strip_tags(Input::post('date', true))) : null,
                    'object_contests_acts_id' => setDefaultData(strip_tags(Input::post('object_contests_acts_id', true)), null, ['']),
                    'choice_of_contractor' => Input::post('choice_of_contractor', true),
                    'notes' => Input::post('notes', true)
                ];

                // Storage nuovo Provvedimento amministrativo
                $insert = MeasuresModel::createWithLogs($arrayValues);

                // Storage nelle tabelle di relazione
                $this->clear(
                    $insert,
                    !empty(Input::post('structures')) ? explode(',', strip_tags((string)Input::post('structures', true))) : null,
                    !empty(Input::post('personnel')) ? explode(',', strip_tags((string)Input::post('personnel', true))) : null,
                    !empty(Input::post('object_bdncp_procedure_id')) ? explode(',', strip_tags((string)Input::post('object_bdncp_procedure_id', true))) : null
                );

                // Storage allegati associati al personale.
                $attach->storage('attach_files', 'measures', $insert->id, $insert['object']);

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
     * @description Renderizza il form di modifica/duplicazione di un Provvedimento
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/measure/edit.html
     * @method GET
     */
    public function edit(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['update', 'create']);

        //Validatore che verifica se l'elemento da modificare esiste
        $validator = new MeasureValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        $data = [];

        //Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $segments = uri()->segmentArray();
        array_pop($segments);
        $data['is_box'] = implode('/', $segments) === 'admin/measure/edit-box';

        if (!$validate['is_success']) {
            if (!$data['is_box']) {
                redirect('admin/measure', sessionSetNotify($validate['errors'], 'danger'));
            } else {
                render('access_denied/modal_access_denied', [], 'admin');
                die();
            }
        }

        // Allegati
        $attach = new AttachmentArchive();

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $measure = Registry::get('measure');
        $measure = !empty($measure) ? $measure->toArray() : [];

        //Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {

            $this->breadcrumb->push('Provvedimenti Amministrativi', 'admin/measure');
            $this->breadcrumb->push('Modifica', '/');

            $data['breadcrumbs'] = $this->breadcrumb->show();

            //Dati header della sezione
            $data['titleSection'] = 'Provvedimenti Amministrativi';
            $data['subTitleSection'] = 'GESTIONE DEI PROVVEDIMENTI POLITICI E DIRIGENZIALI';
            $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';
        }

        // Vedo in sessione se sono arrivato qui da un operazione di ripristino dal versioning
        $session = new Session();
        $restore = $session->getFlash('restore');
        $data['restore'] = $restore;

        // Controllo se si sta duplicando un tasso di assenza
        $isDuplicate = uri()->segment(3, 0) === 'duplicate';

        // Setto il tipo di storage
        $data['_storageType'] = $isDuplicate ? 'duplicate' : 'update';

        //In base al tipo di storage setto l'action del form
        $data['formAction'] = ($isDuplicate) ? '/admin/measure/store' : '/admin/measure/update';
        $data['formSettings'] = [
            'name' => 'form_measure',
            'id' => 'form_measure',
            'class' => 'form_measure',
        ];

        $date = convertDateToForm($measure['date']);
        $measure['date'] = $date['date'];

        $data['measure'] = $measure;

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'measures',
            $measure['id']
        );

        // Labels
        $data['labels'] = [];

        // ID dell'ente corrente.
        $data['institution_id'] = $measure['institution_id'];

        $data['structureIds'] = Arr::pluck($measure['structures'], 'id');
        $data['personnelIds'] = Arr::pluck($measure['personnel'], 'id');
        $data['bdncpProceduresIds'] = Arr::pluck($measure['bdncp_procedures'], 'id');

        $data['typologies'] = [null => ''] + config('measureTypologies', [], 'app');


        $data['scp'] = $measure['scp'] ?? null;
        $data['seo'] = $measure['p_s_d_r'] ?? null;

        render('measure/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua l'update di un Provvedimento
     *
     * @return void
     * @method POST
     * @throws Exception
     * @url /admin/measure/update.html
     */
    public function update(): void
    {
        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new MeasureValidator();
        $check = $validator->check('update');

        if ($check['is_success']) {
            $doAction = true;

            $measureId = (int)strip_tags(Input::post('id', true));

            // Recupero il provvedimento attuale prima di modificarlo e lo salvo nel versioning
            $measure = MeasuresModel::where('id', $measureId)
                ->with('structures:id,structure_name')
                ->with('personnel:id,full_name')
                ->with(['relative_procedure_contraent' => function ($query) {
                    $query->select(['id', 'object']);
                }])
//                ->with(['relative_bdncp_procedure' => function ($query) {
//                    $query->select(['id', 'object']);
//                }])
                ->with(['bdncp_procedures' => function ($query) {
                    // Specifica la tabella di destinazione per evitare ambiguità con l'id
                    $query->select(['object_bdncp_procedure.id', 'object']);
                }])
                ->with('all_attachs');


            $measure = $measure->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute(checkRecordOwner($measure['owner_id']) ? ['update', 'create'] : 'update', true);

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
                $data['number'] = strip_tags(Input::post('number', true));
                $data['object'] = strip_tags(Input::post('object', true));
                $data['type'] = setDefaultData(strip_tags(Input::post('type', true)), null, ['']);
                $data['date'] = !empty(Input::post('date')) ? convertDateToDatabase(strip_tags(Input::post('date', true))) : null;
                $data['object_contests_acts_id'] = setDefaultData(strip_tags(Input::post('object_contests_acts_id', true)), null, ['']);
                $data['choice_of_contractor'] = Input::post('choice_of_contractor', true);
                $data['notes'] = Input::post('notes', true);

                // Update Provvedimento
                MeasuresModel::where('id', $measureId)->updateWithLogs($measure, $data);

                // Svuoto le tabelle di relazione e le aggiorno
                $this->clear(
                    $measure,
                    !empty(Input::post('structures')) ? explode(',', strip_tags((string)Input::post('structures', true))) : null,
                    !empty(Input::post('personnel')) ? explode(',', strip_tags((string)Input::post('personnel', true))) : null,
                    !empty(Input::post('object_bdncp_procedure_id')) ? explode(',', strip_tags((string)Input::post('object_bdncp_procedure_id', true))) : null
                );

                // Upload allegati associati al personale.
                $attach = new AttachmentArchive();
                $attach->update(
                    'attach_files',
                    'measures',
                    $measureId,
                    $measure['institution_id'],
                    $measure['object']
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
     * @param MeasuresModel|null $measure    Provvedimento
     * @param array|int|null     $structures Strutture associate al Provvedimento
     * @param array|int|null     $personnel  Personale associato al Provvedimento
     * @param array|int|null     $bdncpProcedures  Bandi di gara associati
     * @return void
     */
    protected function clear(MeasuresModel $measure = null, array|int $structures = null, array|int $personnel = null, array|int $bdncpProcedures = null): void
    {
        $dataStructures = [];
        if ($structures !== null) {
            foreach ($structures as $structure) {
                $dataStructures[] = is_array($structure) ? $structure['id'] : $structure;
            }
        }
        //Insert/Update nella tabella di relazione
        $measure->structures()->sync($dataStructures);

        $dataPersonnel = [];
        if ($personnel !== null) {
            foreach ($personnel as $person) {
                $dataPersonnel[] = is_array($person) ? $person['id'] : $person;
            }
        }
        //Insert/Update nella tabella di relazione
        $measure->personnel()->sync($dataPersonnel);

        $dataProcedures = [];
        if ($bdncpProcedures !== null) {
            foreach ($bdncpProcedures as $procedure) {
                $dataProcedures[] = is_array($procedure) ? $procedure['id'] : $procedure;
            }
        }
        //Insert/Update nella tabella di relazione
        $measure->bdncp_procedures()->sync($dataProcedures);
    }


    /**
     * @description Funzione che effettua l'eliminazione di un Provvedimento
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/measure/delete.html
     * @method GET
     */
    public function delete(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute([__FUNCTION__, 'create', 'update'], true);

        //Validatore che verifica se l'elemento da eliminare esiste
        $validator = new MeasureValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getDelete() && $this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/measure', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Se la validazione va a buon fine recupero l'elemento da eliminare dal registro
        $measure = Registry::get('measure');

        //Elimino il provvedimento settando deleted = 1
        $measure->deleteWithLogs($measure);

        sessionSetNotify(__('success_delete_operation', null, 'patos'));

        redirect('admin/measure');
    }

    /**
     * @description Funzione per l'eliminazione multipla
     *
     * @return void
     * @throws Exception
     * @url /admin/measure/deletes.html
     * @method GET
     */
    public function deletes(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('delete', true);

        $validator = new MeasureValidator();
        $validate = $validator->multipleSelection();

        if ($validate['is_success']) {

            sessionSetNotify('Operazione avvenuta con successo');

            //Recupero gli elementi da eliminare dal registro
            $measures = Registry::get('__ids__multi_select_profile__');

            //Estraggo gli array degli elementi da eliminare
            $ids = Arr::pluck($measures, 'id');

            //Elimino gli elementi
            foreach ($measures as $measure) {
                $measure->deleteWithLogs($measure);
            }

        } else {

            sessionSetNotify($validate['errors'], 'danger');
        }

        redirect('/admin/measure');
    }
}
