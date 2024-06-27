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
use Helpers\Validators\NoticeActValidator;
use Model\NoticesActsModel;
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
 * Controller Atti delle amministrazioni
 *
 */
class NoticesActAdminController extends BaseAuthController
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
     * @description Renderizza la pagina index degli Atti delle Amministrazioni
     * @return void
     * @throws Exception
     * @url /admin/notices-act.html
     * @method GET
     */
    public function index(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['read', 'create']);

        $this->breadcrumb->push('Atti delle amministrazioni', '/');
        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Atti delle amministrazioni';
        $data['subTitleSection'] = 'GESTIONE DEGLI ATTI DELLE AMMINISTRAZIONI AGGIUDICATRICI E DEGLI ENTI AGGIUDICATORI';
        $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';

        $data['formAction'] = '/admin/notices-act';
        $data['formSettings'] = [
            'name' => 'form_notices_act',
            'id' => 'form_notices_act',
            'class' => 'form_notices_act',
        ];

        $data['include'] = '';

        $data['url'] = uri()->segment(2, 0);

        render('notices_act/index', $data, 'admin');
    }

    /**
     * @description Funzione per la paginazione dei dati nel datatable
     *
     * @return void
     * @url /admin/notices-act/list.html
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
                3 => 'relativeObj',
                4 => 'users.name',
                5 => 'updated_at'
            ];

            //Se l'utente è super Admin mostro la colonna dell'ente
            if (isSuperAdmin(true)) {
                $orderable[6] = 'i.full_name_institution';
            }

            //Setto proprietà datatable
            $dataTable = [];
            setDataTableData($dataTable, 'object');

            //Query per i dati da mostrare nel datatable
            $totalRecords = NoticesActsModel::select('count(id) as allcount')
                ->count();

            $totalRecordsWithFilter = NoticesActsModel::search($dataTable['searchValue'])
                ->select(['count(object_notices_acts.id) as allcount'])
                ->join('users', 'users.id', '=', 'object_notices_acts.owner_id', 'left outer')
                ->leftJoin('object_contests_acts as contest_act', function ($join) {
                    $join->on('contest_act.id', '=', 'object_notices_acts.object_contests_acts_id');
                });

            if (!empty($dataTable['searchValue'])) {
                $totalRecordsWithFilter->orWhere('object_notices_acts.id', '=', $dataTable['searchValue']);
            }

            $totalRecordsWithFilter = $totalRecordsWithFilter->count();

            $order = setOrderDatatable($dataTable['columnName'], $orderable, 'object');

            $records = NoticesActsModel::search($dataTable['searchValue'])
                ->select(['object_notices_acts.id', 'object_contests_acts_id', 'object_notices_acts.owner_id', 'object_notices_acts.institution_id',
                    'object_notices_acts.object', 'object_notices_acts.date', 'object_notices_acts.updated_at', 'users.name', 'i.full_name_institution',
                    'contest_act.object as relativeObj', 'object_notices_acts.publishing_status'])
                ->join('users', 'users.id', '=', 'object_notices_acts.owner_id', 'left outer')
                ->leftJoin('object_contests_acts as contest_act', function ($join) {
                    $join->on('contest_act.id', '=', 'object_notices_acts.object_contests_acts_id');
                })
                ->with('relative_contest_act:id,object')
                ->with(['created_by' => function ($query) {
                    $query->withoutGlobalScopes([DeletedScope::class]);
                    $query->select(['id', 'name', 'deleted']);
                }])
                ->with('institution:id,full_name_institution');

            if (!empty($dataTable['searchValue'])) {
                $records->orWhere('object_notices_acts.id', '=', $dataTable['searchValue']);
            }

            $records = $records->join('institutions as i', 'object_notices_acts.institution_id', '=', 'i.id', 'left outer')
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

                foreach ($records as $record) {

                    if (!empty($record['relative_contest_act']) && is_array($record['relative_contest_act'])) {
                        $contests = Arr::pluck($record['relative_contest_act'], 'object');
                    } else {
                        $contests = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definiti">N.D.</small>';
                    }

                    $actDate = !empty($record['date'])
                        ? ('<small class="badge badge-info"> 
                            <i class="far fa-calendar-alt"></i> ' .
                            date('d-m-Y', strtotime($record['date'])) .
                            '</small>')
                        : '<small class="badge badge-danger">N.D.</small>';

                    if (!empty($record['updated_at'])) {
                        $updateAt = '<small class="badge badge-info"> 
                                    <i class="far fa-clock"></i> '
                            . date('d-m-Y H:i:s', strtotime($record['updated_at'])) . '</small>';
                    } else {
                        $updateAt = '<small class="badge badge-danger">N.D.</small>';
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
                        'scp' => getAclProfileInfo('scp'),
                    ])
                        ->addEdit('admin/notices-act/edit/' . $record['id'], $record['id'])
                        ->addDuplicate('admin/notices-act/duplicate/' . $record['id'], $record['id'])
                        ->addDelete('admin/notices-act/delete/' . $record['id'], $record['id'])
                        ->render() : '';

                    $icon = null;

                    //Setto i dati da mostrare nelle colonne del datatable
                    $setTempData = [];
                    $setTempData[] = ($this->acl->getCrud() || checkRecordOwner($record['owner_id'])) ? ButtonAction::checkList('item[]', $record['id']) : '';

                    $setTempData[] = $icon . (!empty($record['object'])
                            ? '<a href="' . siteUrl('/page/114/details/' . $record['id'] . '/' . urlTitle($record['object'])) . '" target="_blank">' . escapeXss($record['object']) . '</a>'
                            : 'N.D.');
                    $setTempData[] = $actDate;
                    //$setTempData[] = !empty($record['relative_contest_act']['object']) ? escapeXss($record['relative_contest_act']['object']) : 'N.D.';
                    $setTempData[] = $contests;
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
     * @description Renderizza il form di creazione di un nuovo Atto
     *
     * @return void
     * @throws Exception
     * @url /admin/notices-act/create.html
     * @method GET
     */
    public function create(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(__FUNCTION__);

        $data = [];

        //Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $data['is_box'] = uri()->uriString() === 'admin/notice-act/create-box';

        //Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {
            $this->breadcrumb->push('Atti delle amministrazioni', 'admin/notices-act');
            $this->breadcrumb->push('Nuova', '/');
            $data['breadcrumbs'] = $this->breadcrumb->show();

            //Dati header della sezione
            $data['titleSection'] = 'Atti delle amministrazioni';
            $data['subTitleSection'] = 'GESTIONE DEGLI ATTI DELLE AMMINISTRAZIONI AGGIUDICATRICI E DEGLI ENTI AGGIUDICATORI';
            $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';
        }

        $data['formAction'] = '/admin/notices-act/store';
        $data['formSettings'] = [
            'name' => 'form_notices_act',
            'id' => 'form_notices_act',
            'class' => 'form_notices_act',
        ];
        $data['_storageType'] = 'insert';

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        // Recupero i ruoli per il personale in base alla tipologia dell'ente
        $institutionTypeId = patOsInstituteInfo(['institution_type_id'])['institution_type_id'];
        $this->setPublicInData($data, $institutionTypeId);

        render('notices_act/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua lo storage di un nuovo Atto
     *
     * @return void
     * @throws Exception
     * @url /admin/notices-act/store.html
     * @method POST
     */
    public function store(): void
    {

        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new NoticeActValidator();
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
                    'date' => !empty(Input::post('date')) ? convertDateToDatabase(strip_tags(Input::post('date', true))) : null,
                    'object' => strip_tags(Input::post('object', true)),
                    'details' => Input::post('details', true),
                ];

                if (in_array(531, Input::post('public_in'))) {
                    $arrayValues [] = [
                        'cup' => setDefaultData(strip_tags(Input::post('cup', true)), null, ['', null]),
                        'total_fin_amount' => setDefaultData(strip_tags(Input::post('total_fin_amount', true)), null, ['', null]),
                        'financial_sources' => setDefaultData(strip_tags(Input::post('financial_sources', true)), null, ['', null]),
                        'implementation_state' => setDefaultData(strip_tags(Input::post('implementation_state', true)), null, ['', null]),
                        'projects_start_date' => !empty(Input::post('projects_start_date')) ? convertDateToDatabase(strip_tags(Input::post('projects_start_date', true))) : null,
                    ];
                }

                // Storage nuovo Atto dell' amministrazione
                $insert = NoticesActsModel::createWithLogs($arrayValues);

                // Svuoto le tabelle di relazione e le aggiorno
                $this->clear(
                    $insert,
                    !empty(Input::post('assignments')) ? explode(',', strip_tags((string)Input::post('assignments', true))) : null,
                    !empty(Input::post('object_contests_acts_id')) ? explode(',', strip_tags((string)Input::post('object_contests_acts_id', true))) : null,
                    Input::post('public_in', true)
                );

                // Storage allegati associati al personale.
                $attach->storage('attach_files', 'notices_acts', $insert->id, $insert['object']);

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
     * @description Renderizza il form di modifica/duplicazione di un Atto
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/notices-act/edit.html
     * @method GET
     */
    public function edit(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['update', 'create']);

        //Validatore che verifica se l'elemento da modificare esiste
        $validator = new NoticeActValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/notices-act', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Allegati
        $attach = new AttachmentArchive();

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $noticeAct = Registry::get('notice_act');
        $noticeAct = !empty($noticeAct) ? $noticeAct->toArray() : [];

        $this->breadcrumb->push('Atti delle amministrazioni', 'admin/notices-act');
        $this->breadcrumb->push('Modifica', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Atti delle amministrazioni';
        $data['subTitleSection'] = 'GESTIONE DEGLI ATTI DELLE AMMINISTRAZIONI AGGIUDICATRICI E DEGLI ENTI AGGIUDICATORI';
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
        $data['formAction'] = ($isDuplicate) ? '/admin/notices-act/store' : '/admin/notices-act/update';
        $data['formSettings'] = [
            'name' => 'form_notices_act',
            'id' => 'form_notices_act',
            'class' => 'form_notices_act',
        ];

        $date = convertDateToForm($noticeAct['date']);
        $noticeAct['date'] = $date['date'];

        $date = convertDateToForm($noticeAct['projects_start_date']);
        $noticeAct['projects_start_date'] = $date['date'];

        $data['notices_act'] = $noticeAct;

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'notices_acts',
            $noticeAct['id']
        );

        // Labels
        $data['labels'] = [];

        // Recupero i ruoli per il personale in base alla tipologia dell'ente
        $institutionTypeId = patOsInstituteInfo(['institution_type_id'])['institution_type_id'];
        $this->setPublicInData($data, $institutionTypeId);

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = $noticeAct['institution_id'];

        $data['assignmentIds'] = Arr::pluck($noticeAct['assignments'], 'id');
        $data['publicInIDs'] = Arr::pluck($noticeAct['public_in'], 'section_fo_id');
        $data['scp'] = $noticeAct['scp'] ?? null;
        $data['seo'] = $noticeAct['p_s_d_r'] ?? null;

        render('notices_act/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua l'update di un Atto
     *
     * @return void
     * @throws Exception
     * @url /admin/notices-act/update.html
     * @method POST
     */
    public function update(): void
    {
        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new NoticeActValidator();
        $check = $validator->check('update');

        if ($check['is_success']) {
            $doAction = true;

            $noticeActId = (int)strip_tags(Input::post('id', true));

            // Recupero l'atto attuale prima di modificarlo e lo salvo nel versioning
            $noticeAct = NoticesActsModel::where('id', $noticeActId)
                ->with('assignments:id,object')
                ->with('relative_contest_act:id,object')
                ->with(['public_in' => function ($query) {
                    $query->select(['section_fo_id', 'section_fo_config_publication_archive.id', 'section_fo.name'])
                        ->join('section_fo', 'section_fo.id', '=', 'section_fo_id')
                        ->groupBy('section_fo_id');
                }])
                ->with('all_attachs');

            $noticeAct = $noticeAct->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute(__FUNCTION__, (!checkRecordOwner($noticeAct['owner_id']) && $this->acl->getCreate()));

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
                $data['date'] = !empty(Input::post('date')) ? convertDateToDatabase(strip_tags(Input::post('date', true))) : null;
                $data['object'] = !empty(Input::post('object')) ? strip_tags(Input::post('object', true)) : null;
                //$data['object_contests_acts_id'] = !empty(Input::post('object_contests_acts_id')) ? (int)strip_tags(Input::post('object_contests_acts_id', true)) : null;
                $data['details'] = !empty(Input::post('details')) ? Input::post('details', true) : null;
                $data['cup'] = null;
                $data['total_fin_amount'] = null;
                $data['financial_sources'] = null;
                $data['implementation_state'] = null;
                $data['projects_start_date'] = null;

                if (in_array(531, Input::post('public_in'))) {
                    $data['cup'] = setDefaultData(strip_tags(Input::post('cup', true)), null, ['', null]);
                    $data['total_fin_amount'] = setDefaultData(strip_tags(Input::post('total_fin_amount', true)), null, ['', null]);
                    $data['financial_sources'] = setDefaultData(strip_tags(Input::post('financial_sources', true)), null, ['', null]);
                    $data['implementation_state'] = setDefaultData(strip_tags(Input::post('implementation_state', true)), null, ['', null]);
                    $data['projects_start_date'] = setDefaultData(strip_tags(Input::post('projects_start_date', true)), null, ['', null]);
                }

                NoticesActsModel::where('id', $noticeActId)->updateWithLogs($noticeAct, $data);

                // Svuoto le tabelle di relazione e le aggiorno
                $this->clear(
                    $noticeAct,
                    !empty(Input::post('assignments')) ? explode(',', strip_tags((string)Input::post('assignments', true))) : null,
                    !empty(Input::post('object_contests_acts_id')) ? explode(',', strip_tags((string)Input::post('object_contests_acts_id', true))) : null,
                    Input::post('public_in')
                );

                // Upload allegati associati al personale.
                $attach->update(
                    'attach_files',
                    'notices_acts',
                    $noticeActId,
                    $noticeAct['institution_id'],
                    $noticeAct['object']
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
     * @param NoticesActsModel|null $noticeAct    Atto
     * @param array|int|null        $commissions  Commissioni associate all'atto
     * @param array|int|null        $contestsActs Bandi associati all'atto
     * @param array|int|null        $publicIn     Sezioni di pubblica in dell'atto
     * @param bool                  $rest         Parametro aggiuntivo
     * @return void
     */
    protected function clear(NoticesActsModel $noticeAct = null, array|int $commissions = null, array|int $contestsActs = null, array|int $publicIn = null, bool $rest = false): void
    {
        if ($rest) {
            $publicInId = Arr::pluck($publicIn, 'section_fo_id');
        } else {
            $publicInId = $publicIn;
        }

        $dataCommissions = [];
        //Solo se è selezionato uno dei pubblica in che permette d'inserire le commissioni
        if (in_array(115, $publicInId) || in_array(530, $publicInId)) {
            if ($commissions) {
                foreach ($commissions as $commission) {

                    $dataCommissions[] = is_array($commission) ? $commission['id'] : $commission;
                }
            }
        }
        //Insert/Update nella tabella di relazione
        $noticeAct->assignments()->sync($dataCommissions);

        $dataPublicIn = [];
        if ($publicIn !== null) {
            foreach ($publicIn as $in) {
                $dataPublicIn[] = is_array($in) ? strip_tags($in['section_fo_id']) : strip_tags($in);
            }
        }
        //Insert/Update nella tabella di relazione
        $noticeAct->public_in()->sync($dataPublicIn);

        $dataContests = [];
        if ($contestsActs !== null) {
            foreach ($contestsActs as $contest) {
                $dataContests[] = is_array($contest) ? $contest['id'] : $contest;
            }
        }
        //Insert/Update nella tabella di relazione
        $noticeAct->relative_contest_act()->sync($dataContests);
    }


    /**
     * @description Funzione che effettua l'eliminazione di un Atto
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/notices-act/delete.html
     * @method GET
     */
    public function delete(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute([__FUNCTION__, 'create', 'update'], true);

        //Validatore che verifica se l'elemento da eliminare esiste
        $validator = new NoticeActValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getDelete() && $this->acl->getUpdate());

        if (!$validate['is_success']) {
            redirect('admin/notices-act', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Se la validazione va a buon fine recupero l'elemento da eliminare dal registro
        $noticesAct = Registry::get('notice_act');

        // Elimino l'atto settando deleted = 1
        $noticesAct->deleteWithLogs($noticesAct);

        sessionSetNotify(__('success_delete_operation', null, 'patos'));
        // Generazione nuovo token
        Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        redirect('admin/notices-act');
    }

    /**
     * @description Funzione per l'eliminazione multipla
     *
     * @return void
     * @throws Exception
     * @url /admin/notices-act/deletes.html
     * @method GET
     */
    public function deletes(): void
    {
        // Setto il metodo della rotta per i permessi
        $this->acl->setRoute('delete');

        $validator = new NoticeActValidator();
        $validate = $validator->multipleSelection();

        if ($validate['is_success']) {

            sessionSetNotify('Operazione avvenuta con successo');

            //Recupero gli elementi da eliminare dal registro
            $noticesAct = Registry::get('__ids__multi_select_profile__');

            //Estraggo gli array degli elementi da eliminare
            $ids = Arr::pluck($noticesAct, 'id');

            //Elimino gli elementi
            foreach ($noticesAct as $noticeAct) {
                $noticeAct->deleteWithLogs($noticeAct);
            }

            // Generazione nuovo token
            Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        } else {

            sessionSetNotify($validate['errors'], 'danger');
        }

        redirect('/admin/notices-act');
    }

    /**
     * @description Funzione che setta i dati per il campo "Pubblica In"
     * @param array $data              Array dei dati da passare alla vista
     * @param int   $institutionTypeId Id tipo ente
     * @return void
     */
    private function setPublicInData(array &$data, int $institutionTypeId): void
    {
        $publicIn = SectionFoConfigPublicationArchive::where('archive_name', '=', 'object_notices_acts')
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
