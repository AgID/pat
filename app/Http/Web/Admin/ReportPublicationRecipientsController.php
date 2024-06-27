<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Admin;

use Exception;
use Helpers\Addons;
use Helpers\Utility\ButtonAction;
use Helpers\Validators\DatatableValidator;
use Helpers\Validators\ReportPublicationValidator;
use Model\ActivityLogModel;
use Model\ReportPublicationModel;
use Model\SectionsBoModel;
use Scope\DeletedScope;
use System\Arr;
use System\Email;
use System\Input;
use System\JsonResponse;
use System\Log;
use System\Registry;
use System\Security;
use System\Token;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 *
 * Report Pubblicazioni - Elenco destinatari
 *
 */
class ReportPublicationRecipientsController extends BaseAuthController
{
    /**
     * Costruttore
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct(__CLASS__);
    }

    /**
     * @return void
     * @throws Exception
     * @url /admin/report-publication-recipients.html
     * @method GET
     */
    public function index(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['read', 'create']);

        $this->breadcrumb->push('Report Pubblicazioni', '/');
        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Report Pubblicazioni - Elenco destinatari';
        $data['subTitleSection'] = 'GESTIONE DESTINATARI REPORT PUBBLICAZIONI';
        $data['sectionIcon'] = '<i class="fas fa-tasks fa-3x"></i>';

        $data['formAction'] = '/admin/report_publication_recipients';
        $data['formSettings'] = [
            'name' => 'form_report_publication_recipients',
            'id' => 'form_report_publication_recipients',
            'class' => 'form_report_publication_recipients',
        ];

        $data['include'] = '';

        $data['url'] = uri()->segment(2, 0);

        render('report_publication_recipients/index', $data, 'admin');
    }

    /**
     * Funzione per la paginazione dei dati nel datatable
     *
     * @return void
     * @url /admin/report-publication-recipients/list.html
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
                1 => 'report_publication.name',
                2 => 'email',
                3 => 'active',
                4 => 'created_at',
                5 => 'updated_at',
            ];

            //Se l'utente è super Admin mostro la colonna dell'ente
            if (isSuperAdmin(true)) {
                $orderable[6] = 'i.full_name_institution';
            }

            // Setto proprietà datatable
            $draw = !empty(Input::get('draw')) ? Input::get('draw', true) : 1;
            $start = !empty(Input::get("start")) ? (int)Input::get("start", true) : 0;
            $rowPerPage = !empty(Input::get("length")) ? Input::get("length", true) : 25;

            $security = new Security();

            $columnIndexArr = Input::get('order', true);
            $columnNameArr = Input::get('columns', true);
            $orderArr = Input::get('order', true);
            $searchArr = !empty($_GET['search']) ? $security->xssClean(removeInvisibleCharacters($_GET['search'])) : null;

            $columnIndex = !empty($columnIndexArr[0]['column']) ? $columnIndexArr[0]['column'] : null;
            $columnName = !empty($columnNameArr[$columnIndex]['data']) ? (int)$columnNameArr[$columnIndex]['data'] : 'structure_name';
            $columnSortOrder = !empty($orderArr[0]['dir']) ? $orderArr[0]['dir'] : 'ASC';
            $searchValue = !empty($searchArr['value']) ? $searchArr['value'] : null;

            // Query per i dati da mostrare nel datatable
            $totalRecords = ReportPublicationModel::select(['count(id) as allcount'])
                ->count();

            $totalRecordsWithFilter = ReportPublicationModel::search($searchValue)
                ->select(['count(report_publication.id) as allcount'])
                ->count();

            $order = setOrderDatatable($columnName, $orderable, 'structure_name');

            $records = ReportPublicationModel::search($searchValue)
                ->select(['report_publication.id', 'report_publication.institution_id', 'report_publication.name', 'report_publication.owner_id',
                    'report_publication.updated_at', 'report_publication.email', 'report_publication.active', 'i.full_name_institution'])
                ->with(['created_by' => function ($query) {
                    $query->withoutGlobalScopes([DeletedScope::class]);
                    $query->select(['id', 'name', 'deleted']);
                }])
                ->with('institution:id,full_name_institution')
                ->join('institutions as i', 'report_publication.institution_id', '=', 'i.id', 'left outer')
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
                    if (!empty($record['created_at'])) {
                        $createdAt = '<small class="badge badge-info"> 
                                    <i class="far fa-clock"></i> '
                            . date('d-m-Y H:i:s', strtotime($record['created_at'])) .
                            '</small>';
                    } else {
                        $createdAt = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definita">N.D.</small>';
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
                        ->addEdit('admin/report-publication-recipients/edit/' . $record['id'], $record['id'])
                        ->addDuplicate('admin/report-publication-recipients/duplicate/' . $record['id'], $record['id'])
                        ->addDelete('admin/report-publication-recipients/delete/' . $record['id'], $record['id'])
                        ->render() : '';

                    // Setto i dati da mostrare nelle colonne del datatable
                    $setTempData = [];
                    $setTempData[] = (($this->acl->getCrud() || checkRecordOwner($record['owner_id']))
                        ? ButtonAction::checkList('item[]', $record['id']) : '');
                    $setTempData[] = !empty($record['name']) ? escapeXss($record['name']) : 'N.D.';
                    $setTempData[] = !empty($record['email']) ? escapeXss($record['email']) : 'N.D.';
                    $setTempData[] = !empty($record['active']) ? escapeXss($record['active']) : 0;
                    $setTempData[] = $createdAt;
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
     * @return void
     * @throws Exception
     * @url /admin/report-publication-recipients/create.html
     * @method GET
     */
    public function create(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(__FUNCTION__);

        $this->breadcrumb->push('Elenco Destinatari Report', 'admin/report-publication-recipients');
        $this->breadcrumb->push('Nuovo', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Report Pubblicazione - Elenco Destinatari';
        $data['subTitleSection'] = 'GESTIONE DESTINATARI REPORT PUBBLICAZIONI';
        $data['sectionIcon'] = '<i class="fas fa-briefcase fa-3x"></i>';

        $data['formAction'] = '/admin/report-publication-recipients/store';
        $data['formSettings'] = [
            'name' => 'form_report-publication-recipients',
            'id' => 'form_report-publication-recipients',
            'class' => 'form_report-publication-recipients',
        ];
        $data['_storageType'] = 'insert';

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        render('report_publication_recipients/form_store', $data, 'admin');
    }

    /**
     * Funzione che effettua lo storage di un nuovo destinatario per il report
     *
     * @return void
     * @throws Exception
     * @url /admin/report-publication-recipients/store.html
     * @method POST
     */
    public function store(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new ReportPublicationValidator();
        $check = $validator->check();

        // Controllo se la validazione è andata a buon fine
        if ($check['is_success']) {


            // Dati per registrazione ActivityLog
            $getIdentity = authPatOs()->getIdentity(['id', 'name']);

            $arrayValues = [
                'owner_id' => $getIdentity['id'],
                'institution_id' => strip_tags(checkAlternativeInstitutionId()),
                'name' => strip_tags(Input::post('name', true)),
                'email' => strip_tags(Input::post('email', true)),
                'active' => !empty(Input::post('active')) ? (int)Input::post('active') : 0,
            ];

            // Storage nuovo Tasso di assenza
            ReportPublicationModel::createWithLogs($arrayValues);

            // Generazione nuovo token
            Token::forceRegenerate((int)config('csrf_expire', null, 'app'));

            $json->set('message', __('success_save_operation', null, 'patos'));

        } else {

            $code = $json->bad();
            $json->error('error', $check['errors']);
        }

        $json->setStatusCode($code);
        $json->response();
    }

    /**
     * @return void
     * @throws Exception
     * @url /admin/report-publication-recipients/edit/:num.html
     * @method GET
     */
    public function edit(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['update', 'create']);

        // Validatore che verifica se l'elemento da modificare esiste
        $validator = new ReportPublicationValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/report-publication-recipients', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Controllo se si sta duplicando un tasso di assenza
        $isDuplicate = uri()->segment(3, 0) === 'duplicate';

        $data = [];

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $recipient = Registry::get('report_publication');

        $recipient = !empty($recipient) ? $recipient->toArray() : [];

        $this->breadcrumb->push('Elenco Destinatari Report', 'admin/report-publication-recipients');
        $this->breadcrumb->push('Modifica', '/');

        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Report Pubblicazione - Elenco Destinatari';
        $data['subTitleSection'] = 'GESTIONE DESTINATARI REPORT PUBBLICAZIONI';
        $data['sectionIcon'] = '<i class="fas fa-briefcase fa-3x"></i>';

        // Setto il tipo di storage
        $data['_storageType'] = $isDuplicate ? 'duplicate' : 'update';

        //In base al tipo di storage setto l'action del form
        $data['formAction'] = ($isDuplicate) ? '/admin/report-publication-recipients/store' : '/admin/report-publication-recipients/update';
        $data['formSettings'] = [
            'name' => 'form_report-publication-recipients',
            'id' => 'form_report-publication-recipients',
            'class' => 'form_report-publication-recipients',
        ];

        $data['recipient'] = $recipient;

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = $recipient['institution_id'];

        render('report_publication_recipients/form_store', $data, 'admin');
    }

    /**
     * Funzione che effettua l'update di un destinatario del report di pubblicazione
     *
     * @return void
     * @throws Exception
     * @url /admin/absence-rates/update.html
     * @method POST
     */
    public function update(): void
    {

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new ReportPublicationValidator();
        $check = $validator->check('update');

        // Controllo se la validazione è andata a buon fine
        if ($check['is_success']) {

            $recepientId = (int)strip_tags((string)Input::post('id', true));

            // Recupero il tasso di assenza attuale prima di modificarlo e lo salvo nel versioning
            $recepient = ReportPublicationModel::where('id', $recepientId)
                ->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute(__FUNCTION__, (!checkRecordOwner($recepient['owner_id']) && $this->acl->getCreate()));

            $data = [];

            $data['name'] = strip_tags((string)Input::post('name', true));
            $data['email'] = strip_tags((string)Input::post('email', true));
            $data['active'] = setDefaultData(strip_tags((string)Input::post('active', true)), 0, ['', null]);

            // Update Destinatario Report di Pubblicazione
            ReportPublicationModel::where('id', $recepientId)->updateWithLogs($recepient, $data);

            // Generazione nuovo token
            Token::forceRegenerate((int)config('csrf_expire', null, 'app'));

            $json->set('message', __('success_update_operation', null, 'patos'));

        } else {

            $code = $json->bad();
            $json->error('error', $check['errors']);
        }

        $json->setStatusCode($code);
        $json->response();
    }

    /**
     * @return void
     * @throws Exception
     * @url /admin/report-publication-recipients/delete.html
     * @method GET
     */
    public function delete(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute([__FUNCTION__, 'create', 'update'], true);

        //Validatore che verifica se l'elemento da eliminare esiste
        $validator = new ReportPublicationValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getDelete() && $this->acl->getUpdate());

        // Controllo se la validazione è andata a buon fine
        if (!$validate['is_success']) {

            redirect('admin/report-publication-recipients', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Se la validazione va a buon fine recupero l'elemento da eliminare dal registro
        $recepient = Registry::get('report_publication');

        // Delete Tasso di assenza
        $recepient->deleteWithLogs($recepient);

        // Generazione nuovo token
        Token::forceRegenerate((int)config('csrf_expire', null, 'app'));

        sessionSetNotify(__('success_delete_operation', null, 'patos'));

        redirect('admin/report-publication-recipients');
    }

    /**
     * Funzione per l'eliminazione multipla
     *
     * @return void
     * @throws Exception
     * @url /admin/report-publication-recipients/deletes.html
     * @method GET
     */
    public function deletes(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('delete');

        // Validatore sugli elementi da eliminare
        $validator = new ReportPublicationValidator();
        $validate = $validator->multipleSelection();

        if ($validate['is_success'] === true) {

            sessionSetNotify('Operazione avvenuta con successo');

            $reports = Registry::get('__ids__multi_select_profile__');

            //Elimino gli elementi
            foreach ($reports as $report) {
                $report->deleteWithLogs($report);
            }

            // Generazione nuovo token
            Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        } else {

            sessionSetNotify($validate['errors'], 'danger');
        }

        redirect('/admin/report-publication-recipients');
    }

    /**
     * @descriotion Funzione che genera il report giornaliero delle pubblicazioni
     * @return void
     * @throws Exception
     */
    public function generateReport(): void
    {
        $sections = [];

        //Recupero l'elenco dei destinatari del report
        $receivers = optional(ReportPublicationModel::where('active', 1)
            ->select(['email'])
            ->get())
            ->toArray();

        //Se non ci sono destinatari non creo il report
        if (!empty($receivers)) {
            $receivers = Arr::pluck($receivers, 'email');

            $messaggioReport = '<h2>Report Attività</h2>';

            //Campi da mostrare nel report per ogni oggetto
            $fields = Addons::config(
                'fields',
                'agid',
                'publicationReportSectionsConfig'
            );

            //Intervallo di tempo giornaliero in cui prendere le attività
            $startDate = date('Y-m-d', time()) . ' 00:00:00';
            $endDate = date('Y-m-d', strtotime($startDate) + (24 * 60 * 60)) . ' 00:00:00';

            //Recupero le attività giornaliere
            $activity = optional(ActivityLogModel::where('platform', 'pat')
                ->where('area', 'object')
                ->whereIn('action_type', ['updateObjectInstance', 'addObjectInstance'])
                ->whereIn('object_id', array_keys($fields))
                ->where(function ($query) use ($startDate, $endDate) {
                    $query->where('created_at', '>=', $startDate)
                        ->where('created_at', '<=', $endDate);
                })
                ->get())
                ->toArray();

            //Controllo se ci sono pubblicazioni da inserire nel report o meno
            if (!empty($activity)) {

                //Recupero le sezioni
                $tmpSections = SectionsBoModel::select(['id', 'name', 'model_class'])
                    ->whereIn('id', array_keys($fields))
                    ->with('sectionFo')
                    ->get()
                    ->toArray();

                foreach ($tmpSections as $section) {
                    $sections [$section['id']] = $section;
                }

                $arrayOggettiReport = [];
                $report = [];

                $tmpIdOggetto = null;


                foreach ($activity as $item) {
                    $idOggetto = $item['object_id'];
                    $idIstanza = $item['record_id'];

                    if ($tmpIdOggetto != $idOggetto) {
                        $model = '\\Model\\' . $sections[$item['object_id']]['model_class'];
                    }

                    $istanza = $model::where('id', $idIstanza)
                        ->first()
                        ->toArray();

                    if (!empty($istanza)) {
                        $azione = ($item['action_type'] == 'addObjectInstance' ? 'insert' : 'update');
                        $report[$idOggetto][$azione][$idIstanza] = $istanza[$fields[$idOggetto]];
                        $arrayOggettiReport[$idOggetto] = $sections[$idOggetto]['name'];
                    }
                    $tmpIdOggetto = $idOggetto;
                }

                //Creo il report delle attività
                foreach ($report as $key => $value) {
                    $messaggioReport .= '<h3>' . $arrayOggettiReport[$key] . ':</h3>';

                    if (!empty($value['insert'])) {
                        $messaggioReport .= '<p style="font-size: 12px;">Nuovi inserimenti: </p>';
                        $messaggioReport .= '<ul>';
                        foreach ($value['insert'] as $k => $v) {
                            $messaggioReport .= '<li><a href="' . siteUrl('page/' . $sections[$key]['section_fo']['id'] . '/details/' . $k . '/' . urlTitle($v)) . '">' . $v . '</a></li>';
                        }
                        $messaggioReport .= '</ul>';
                    }

                    if (!empty($value['update'])) {
                        $messaggioReport .= '<p style="font-size: 12px;" >Aggiornati: </p>';
                        $messaggioReport .= '<ul>';
                        foreach ($value['update'] as $k => $v) {
                            $messaggioReport .= '<li><a href="' . siteUrl('page/' . $sections[$key]['section_fo']['id'] . '/details/' . $k . '/' . urlTitle($v)) . '">' . $v . '</a></li>';
                        }
                        $messaggioReport .= '</ul>';
                    }

                }

            } else { //Se non ci sono attività
                $messaggioReport = '<h1 style="font-size: 12px;">Nessuna attività registrata</h1>';
                $nomeOggetti = [];
            }

            echo $messaggioReport;

            $configs = patOsConfigMail(true);

            foreach ($receivers as $receiver) {
                //Invio le email ai destinatari
                $email = new Email($configs);
                $send = $email->from($configs['smtp_user'])
                    ->to($receiver)
                    ->set_newline("\r\n")
                    ->subject('Portale Amministrazione Trasparente - Aggiornamenti del ' . date('d/m/Y'))
                    ->message($messaggioReport)
                    ->send();

                if (!$send) {

                    Log::danger($email->print_debugger());
                }
            }

            exit();
        }
    }
}
