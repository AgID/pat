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
use Helpers\Validators\DatatableValidator;
use Helpers\Validators\InterventionValidator;
use Model\InterventionsModel;
use Scope\DeletedScope;
use System\Arr;
use System\Input;
use System\JsonResponse;
use System\Registry;
use System\Security;
use System\Session;
use System\Token;

/**
 *
 * Controller Interventi straordinari e di emergenza
 *
 */
class InterventionAdminController extends BaseAuthController
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
     * @description Renderizza la pagina index per gli Interventi
     *
     * @return void
     * @throws Exception
     * @url /admin/intervention.html
     * @method GET
     */
    public function index(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['read', 'create']);

        $this->breadcrumb->push('Interventi straordinari e di emergenza', '/');
        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Interventi straordinari e di emergenza';
        $data['subTitleSection'] = 'GESTIONE DEGLI INTERVENTI STRAORDINARI E DI EMERGENZA';
        $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';

        $data['formAction'] = '/admin/intervention';
        $data['formSettings'] = [
            'name' => 'form_intervention',
            'id' => 'form_intervention',
            'class' => 'form_intervention',
        ];

        $data['include'] = '';

        $data['url'] = uri()->segment(2, 0);

        render('intervention/index', $data, 'admin');
    }

    /**
     * @description Funzione per la paginazione dei dati nel datatable
     *
     * @return void
     * @url /admin/intervention/list.html
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
                4 => 'time_limits',
                5 => 'estimated_cost',
                6 => 'effective_cost',
                7 => 'userName',
                8 => 'updated_at'
            ];

            //Se l'utente è super Admin mostro la colonna dell'ente
            if (isSuperAdmin(true)) {
                $orderable[9] = 'i.full_name_institution';
            }

            //Setto proprietà datatable
            $draw = !empty(Input::get('draw')) ? Input::get('draw', true) : 1;
            $start = !empty(Input::get("start")) ? (int)Input::get("start", true) : 0;
            $rowPerPage = !empty(Input::get("length")) ? Input::get("length", true) : 25;

            $security = new Security();

            $columnIndexArr = Input::get('order', true);
            $columnNameArr = Input::get('columns', true);
            $orderArr = Input::get('order', true);
            $searchArr = !empty($_GET['search']) ? $security->xssClean(removeInvisibleCharacters($_GET['search'])) : null;

            $columnIndex = !empty($columnIndexArr[0]['column']) ? $columnIndexArr[0]['column'] : null;
            $columnName = !empty($columnNameArr[$columnIndex]['data']) ? (int)$columnNameArr[$columnIndex]['data'] : 'name';
            $columnSortOrder = !empty($orderArr[0]['dir']) ? $orderArr[0]['dir'] : 'ASC';
            $searchValue = !empty($searchArr['value']) ? $searchArr['value'] : null;

            //Query per i dati da mostrare nel datatable
            $totalRecords = InterventionsModel::select('count(id) as allcount')
                ->count();

            $totalRecordsWithFilter = InterventionsModel::search($searchValue)
                ->select('count(object_interventions.id) as allcount')
                ->join('users', 'users.id', '=', 'object_interventions.owner_id');

            if (!empty($dataTable['searchValue'])) {
                $totalRecordsWithFilter->orWhere('object_interventions.id', '=', $dataTable['searchValue']);
            }

            $totalRecordsWithFilter = $totalRecordsWithFilter->count();

            $order = setOrderDatatable($columnName, $orderable, 'name');

            $records = InterventionsModel::search($searchValue)
                ->select(['object_interventions.id', 'object_interventions.institution_id', 'owner_id', 'object_interventions.name',
                    'time_limits', 'estimated_cost', 'effective_cost', 'object_interventions.updated_at', 'publishing_status',
                    'users.name as userName', 'i.full_name_institution'])
                ->join('users', 'users.id', '=', 'object_interventions.owner_id')
                ->with('measures:id,object')
                ->with('regulations:id,title')
                ->with(['created_by' => function ($query) {
                    $query->withoutGlobalScopes([DeletedScope::class]);
                    $query->select(['id', 'name', 'deleted']);
                }])
                ->with(['institution' => function ($query) {
                    $query->select(['id', 'full_name_institution']);
                }]);

            if (!empty($dataTable['searchValue'])) {
                $records->orWhere('object_interventions.id', '=', $dataTable['searchValue']);
            }

            $records = $records->join('institutions as i', 'object_interventions.institution_id', '=', 'i.id', 'left outer')
                ->orderBy($order, $columnSortOrder)
                ->offset($start)
                ->limit($rowPerPage)
                ->get()
                ->toArray();

            $response['draw'] = intval($draw);
            $response['iTotalRecords'] = ($totalRecords);
            $response['iTotalDisplayRecords'] = ($totalRecordsWithFilter);
            $response['aaData'] = [];

            if (!empty($records)) {

                foreach ($records as $record) {

                    if (!empty($record['measures']) && is_array($record['measures'])) {

                        $tmpMeasures = Arr::pluck($record['measures'], 'object');
                        $measures = str_replace(',', ',' . nbs(2), implode(
                            ',',
                            array_map(
                                function ($measure) {
                                    return ('<small class="badge badge-primary mb-1">' . escapeXss($measure) . '</small>');
                                },
                                $tmpMeasures
                            )
                        ));
                    } else {

                        $measures = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definiti">N.D.</small>';
                    }

                    if (!empty($record['regulations']) && is_array($record['regulations'])) {

                        $tmpRegulations = Arr::pluck($record['regulations'], 'title');
                        $regulations = str_replace(',', ',' . nbs(2), implode(
                            ',',
                            array_map(
                                function ($regulation) {
                                    return ('<small class="badge badge-primary mb-1">' . escapeXss($regulation) . '</small>');
                                },
                                $tmpRegulations
                            )
                        ));
                    } else {

                        $regulations = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definiti">N.D.</small>';
                    }

                    $timeLimits = !empty($record['time_limits'])
                        ? ('<small class="badge badge-info"> 
                            <i class="far fa-calendar-alt"></i> ' .
                            date('d-m-Y', strtotime($record['time_limits'])) .
                            '</small>')
                        : '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definiti">N.D.</small>';

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
                        ->addEdit('admin/intervention/edit/' . $record['id'], $record['id'])
                        ->addDuplicate('admin/intervention/duplicate/' . $record['id'], $record['id'])
                        ->addDelete('admin/intervention/delete/' . $record['id'], $record['id'])
                        ->render() : '';

                    $icon = null;

                    //Setto i dati da mostrare nelle colonne del datatable
                    $setTempData = [];
                    $setTempData[] = ($this->acl->getCrud() || checkRecordOwner($record['owner_id'])) ? ButtonAction::checkList('item[]', $record['id']) : '';
                    $setTempData[] = $icon . (!empty($record['name'])
                            ? '<a href="' . siteUrl('/page/187/details/' . $record['id'] . '/' . urlTitle($record['name'])) . '" target="_blank">' . escapeXss($record['name']) . '</a>'
                            : 'N.D.');
                    $setTempData[] = $measures;
                    $setTempData[] = $regulations;
                    $setTempData[] = $timeLimits;
                    $setTempData[] = !empty($record['estimated_cost'])
                        ? '<small class="badge badge-success">' . S::currency($record['estimated_cost'], 2, ',', '.') . ' &euro; </small>'
                        : '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definito">N.D.</small>';
                    $setTempData[] = !empty($record['effective_cost'])
                        ? '<small class="badge badge-success">' . S::currency($record['effective_cost'], 2, ',', '.') . ' &euro; </small>'
                        : '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definito">N.D.</small>';
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
     * @description Renderizza il form di creazione di un nuovo Intervento
     *
     * @return void
     * @throws Exception
     * @url /admin/intervention/create.html
     * @method GET
     */
    public function create(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(__FUNCTION__);

        $data = [];

        //Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $data['is_box'] = uri()->uriString() === 'admin/intervention/create-box';

        //Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {
            $this->breadcrumb->push('Interventi straordinari e di emergenza', 'admin/intervention');
            $this->breadcrumb->push('Nuovo', '/');
            $data['breadcrumbs'] = $this->breadcrumb->show();

            //Dati header della sezione
            $data['titleSection'] = 'Interventi straordinari e di emergenza';
            $data['subTitleSection'] = 'GESTIONE DEGLI INTERVENTI STRAORDINARI E DI EMERGENZA';
            $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';
        }

        $data['formAction'] = '/admin/intervention/store';
        $data['formSettings'] = [
            'name' => 'form_intervention',
            'id' => 'form_intervention',
            'class' => 'form_intervention',
        ];
        $data['_storageType'] = 'insert';

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        render('intervention/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua lo storage di un nuovo Intervento
     *
     * @return void
     * @throws Exception
     * @url /admin/intervention/store.html
     * @method POST
     */
    public function store(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new InterventionValidator();
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
                    'description' => Input::post('description', true),
                    'derogations' => Input::post('derogations', true),
                    'time_limits' => !empty(Input::post('time_limits')) ? convertDateToDatabase(strip_tags(Input::post('time_limits', true))) : null,
                    'estimated_cost' => !empty(Input::post('estimated_cost')) ? toFloat(strip_tags(Input::post('estimated_cost', true))) : null,
                    'effective_cost' => !empty(Input::post('effective_cost')) ? toFloat(strip_tags(Input::post('effective_cost', true))) : null,
                ];

                // Storage nuovo Intervento
                $insert = InterventionsModel::createWithLogs($arrayValues);

                // Svuoto le tabelle di relazione e le aggiorno
                $this->clear(
                    $insert,
                    !empty(Input::post('measures')) ? explode(',', strip_tags((string)Input::post('measures', true))) : null,
                    Input::post('regulations', true)
                );

                // Storage allegati associati al personale.
                $attach->storage('attach_files', 'interventions', $insert->id, $insert['name']);

                if (!referenceOriginForRegenerateToken(3, 'create-box')) {
                    // Generazione nuovo token
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
     * @description Renderizza il form di modifica/duplicazione degli Interventi
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/intervention/edit/:id.html
     * @method GET
     */
    public function edit(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['update', 'create']);

        //Validatore che verifica se l'elemento da modificare esiste
        $validator = new InterventionValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/intervention', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Allegati
        $attach = new AttachmentArchive();

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $intervention = Registry::get('intervention');
        $intervention = !empty($intervention) ? $intervention->toArray() : [];

        $this->breadcrumb->push('Interventi straordinari e di emergenza', 'admin/intervention');
        $this->breadcrumb->push('Modifica', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();
        $data['titleSection'] = 'Interventi straordinari e di emergenza';
        $data['subTitleSection'] = 'GESTIONE DEGLI INTERVENTI STRAORDINARI E DI EMERGENZA';
        $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';

        // Vedo in sessione se sono arrivato qui da un operazione di ripristino dal versioning
        $session = new Session();
        $restore = $session->getFlash('restore');
        $data['restore'] = $restore;

        // Controllo se si sta duplicando un tasso di assenza
        $isDuplicate = uri()->segment(3, 0) === 'duplicate';

        // Setto il tipo di storage
        $data['_storageType'] = $isDuplicate ? 'duplicate' : 'update';

        //In base al tipo di storage setto l'action del form
        $data['formAction'] = ($isDuplicate) ? '/admin/intervention/store' : '/admin/intervention/update';
        $data['formSettings'] = [
            'name' => 'form_intervention',
            'id' => 'form_intervention',
            'class' => 'form_intervention',
        ];

        $timeLimits = convertDateToForm($intervention['time_limits']);

        $data['intervention'] = $intervention;

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'interventions',
            $intervention['id']
        );

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = $intervention['institution_id'];

        $data['time_limits'] = $timeLimits['date'];
        $data['measureIds'] = Arr::pluck($intervention['measures'], 'id');
        $data['regulationIds'] = Arr::pluck($intervention['regulations'], 'id');
        $data['seo'] = $intervention['p_s_d_r'] ?? null;
        $data['is_box'] = false;

        render('intervention/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua l'update di un Intervento
     *
     * @return void
     * @throws Exception
     * @url /admin/intervention/update.html
     * @method POST
     */
    public function update(): void
    {
        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new InterventionValidator();
        $check = $validator->check('update');

        if ($check['is_success']) {
            $doAction = true;

            $interventionId = (int)strip_tags(Input::post('id', true));

            // Recupero l'intervento attuale prima di modificarlo e lo salvo nel versioning
            $intervention = InterventionsModel::where('id', $interventionId)
                ->with('measures:id,object')
                ->with('regulations:id,title')
                ->with('all_attachs');

            $intervention = $intervention->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute(__FUNCTION__, (!checkRecordOwner($intervention['owner_id']) && $this->acl->getCreate()));

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
                $data['description'] = Input::post('description', true);
                $data['derogations'] = Input::post('derogations', true);
                $data['time_limits'] = !empty(Input::post('time_limits')) ? convertDateToDatabase(strip_tags(Input::post('time_limits', true))) : null;
                $data['estimated_cost'] = !empty(Input::post('estimated_cost')) ? toFloat(strip_tags(Input::post('estimated_cost', true))) : null;
                $data['effective_cost'] = !empty(Input::post('effective_cost')) ? toFloat(strip_tags(Input::post('effective_cost', true))) : null;

                // Update Intervento
                InterventionsModel::where('id', $interventionId)->updateWithLogs($intervention, $data);

                // Svuoto le tabelle di relazione e le aggiorno
                $this->clear(
                    $intervention,
                    !empty(Input::post('measures')) ? explode(',', strip_tags((string)Input::post('measures', true))) : null,
                    Input::post('regulations', true)
                );

                // Upload allegati associati al personale.
                $attach->update(
                    'attach_files',
                    'interventions',
                    $interventionId,
                    $intervention['institution_id'],
                    $intervention['name']
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
     * @param InterventionsModel|null $intervention Intervento
     * @param array|int|null          $measures     Provvedimenti associati all'intervento
     * @param array|int|null          $regulations  Regolamenti associati all'intervento
     * @return void
     */
    protected function clear(InterventionsModel $intervention = null, array|int $measures = null, array|int $regulations = null): void
    {
        $dataMeasures = [];
        if ($measures !== null) {
            foreach ($measures as $measure) {
                $dataMeasures[] = is_array($measure) ? $measure['id'] : $measure;
            }
        }
        //Insert/Update nella tabella di relazione
        $intervention->measures()->sync($dataMeasures);

        $dataRegulations = [];
        if ($regulations !== null) {
            foreach ($regulations as $regulation) {

                $dataRegulations[] = is_array($regulation) ? $regulation['id'] : $regulation;
            }
        }
        //Insert/Update nella tabella di relazione
        $intervention->regulations()->sync($dataRegulations);
    }


    /**
     * @description Funzione che effettua l'eliminazione di un Intervento
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/intervention/delete.html
     * @method GET
     */
    public function delete(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute([__FUNCTION__, 'create', 'update'], true);

        //Validatore che verifica se l'elemento da eliminare esiste
        $validator = new InterventionValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getDelete() && $this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/grant', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Se la validazione va a buon fine recupero l'elemento da eliminare dal registro
        $intervention = Registry::get('intervention');

        //Elimino l'intervento
        $intervention->deleteWithLogs($intervention);

        sessionSetNotify(__('success_delete_operation', null, 'patos'));

        // Generazione nuovo token
        if (!referenceOriginForRegenerateToken(3, 'create-box')) {
            // Generazione nuovo token
            Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        }

        redirect('admin/intervention');
    }

    /**
     * @description Funzione per l'eliminazione multipla
     *
     * @return void
     * @method GET
     * @throws Exception
     * @url /admin/intervention/deletes.html
     */
    public function deletes(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('delete');

        $validator = new InterventionValidator();
        $validate = $validator->multipleSelection();

        if ($validate['is_success']) {

            sessionSetNotify('Operazione avvenuta con successo');

            //Recupero gli elementi da eliminare dal registro
            $interventions = Registry::get('__ids__multi_select_profile__');

            //Estraggo gli array degli elementi da eliminare
            $ids = Arr::pluck($interventions, 'id');

            //Elimino gli elementi
            foreach ($interventions as $intervention) {
                $intervention->deleteWithLogs($intervention);
            }

            // Generazione nuovo token
            Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        } else {

            sessionSetNotify($validate['errors'], 'danger');
        }

        redirect('/admin/intervention');
    }
}
