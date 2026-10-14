<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Admin;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Helpers\ActivityLog;
use Helpers\Utility\AttachmentArchive;
use Helpers\Utility\ButtonAction;
use Helpers\Validators\DatatableValidator;
use Helpers\Validators\NewsNoticeValidator;
use Model\NewsNoticesModel;
use System\Input;
use System\JsonResponse;
use System\Registry;
use System\Session;

/**
 *
 * Controller News e Avvisi
 *
 */
class NewsNoticeAdminController extends BaseAuthController
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
     * @description Renderizza la pagina index delle Notizie
     *
     * @return void
     * @throws Exception
     * @url /admin/news-notice.html
     * @method GET
     */
    public function index(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['read', 'create']);

        $this->breadcrumb->push('News ed avvisi', '/');
        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'News ed avvisi';
        $data['subTitleSection'] = 'GESTIONE DI NOTIZIE ED AVVISI A SUPPORTO DEI CONTENUTI';
        $data['sectionIcon'] = '<i class="fas fa-bullhorn fa-3x"></i>';

        $data['formAction'] = '/admin/news-notice';
        $data['formSettings'] = [
            'name' => 'form_news-notice',
            'id' => 'form_news-notice',
            'class' => 'form_news-notice',
        ];

        $data['include'] = '';

        $data['url'] = uri()->segment(2, 0);

        render('news_notice/index', $data, 'admin');
    }

    /**
     * @description Funzione per la paginazione dei dati nel datatable
     *
     * @return void
     * @url /admin/news-notice/list.html
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
                1 => 'title',
                2 => 'typology',
                3 => 'news_date',
            ];

            //Setto proprietà datatable
            $dataTable = [];
            setDataTableData($dataTable, 'title');

            //Query per i dati da mostrare nel datatable
            $totalRecords = NewsNoticesModel::select(['count(id) as allcount'])
                ->count();

            $totalRecordsWithFilter = NewsNoticesModel::search($dataTable['searchValue'])
                ->select(['count(object_news_notices.id) as allcount']);
            if (!empty($dataTable['searchValue'])) {
                $totalRecordsWithFilter->orWhere('object_news_notices.id', '=', $dataTable['searchValue']);
            }
            $totalRecordsWithFilter = $totalRecordsWithFilter->count();

            $order = setOrderDatatable($dataTable['columnName'], $orderable, 'title');

            $records = NewsNoticesModel::search($dataTable['searchValue'])
                ->select(['object_news_notices.id', 'institution_id', 'news_date', 'public_in_notice', 'evidence',
                    'object_news_notices.updated_at', 'title', 'typology', 'owner_id', 'i.full_name_institution'])
                ->with('institution:id,full_name_institution');

            if (!empty($dataTable['searchValue'])) {
                $records->orWhere('object_news_notices.id', '=', $dataTable['searchValue']);
            }

            $records = $records->join('institutions as i', 'object_news_notices.institution_id', '=', 'i.id', 'left outer')
                ->orderBy($order, $dataTable['columnSortOrder'])
                ->orderBy('updated_at', 'DESC')
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

                    $newsDate = !empty($record['news_date'])
                        ? ('<small class="badge badge-info"> 
                            <i class="far fa-calendar-alt"></i> ' .
                            date('d-m-Y', strtotime($record['news_date'])) .
                            '</small>')
                        : '<small class="badge badge-danger">N.D.</small>';

                    // Controllo se l'utente ha i permessi di modifica dei record o di scrittura (e quindi di modifica dei propri record)
                    $permits = ($this->acl->getCreate() && checkRecordOwner($record['owner_id'],$getIdentity['id']));
                    $updatePermits = ($this->acl->getUpdate() && checkRecordOwner($record['owner_id'],$getIdentity['id']));
                    $wfPermits = false;
                    $workflowState = $record['workflow_state'] ?? 'finale';
                    $wfOwnerPermits = $permits && $workflowState == 'iniziale';

                    //Setto i pulsanti delle actions da mostrare in base ai permessi dell'utente
                    $buttonAction = ($this->acl->getUpdate() || $this->acl->getDelete() || $permits) ? ButtonAction::create([
                        'edit' => $checkWf ? (($this->acl->getUpdate() && $wfPermits) || $wfOwnerPermits) : $this->acl->getUpdate() || $permits,
                        'duplicate' => $this->acl->getCreate() && $workflowState == 'finale',
                        'versioning' => getAclVersioning(),
                        'delete' => $checkWf ? (($this->acl->getDelete() && $wfPermits) || $wfOwnerPermits) : $this->acl->getDelete() || $updatePermits || $permits,
                    ])
                        ->addEdit('admin/news-notice/edit/' . $record['id'], $record['id'])
                        ->addDuplicate('admin/news-notice/duplicate/' . $record['id'], $record['id'])
                        ->addCustomButton('b_a_v', siteUrl('admin/news-notice/versioning'), $record['id'], '', '', 'versioning')
                        ->addCustomButton('s_r_l',siteUrl('admin/news-notice/show-record-log/'.$record['id']), $record['id'], '49', 'show-record-log', 'view')
                        ->addCustomButton('wf_a_f_e',"", $record['id'], $workflowState, '', 'view')
                        ->addCustomButton(
                            's_no_app_io',
                            '#!',
                            $record['id'],
                            'news',
                            'Invia notifica App IO',
                            'view'
                        )
                        ->addDelete('admin/news-notice/delete/' . $record['id'], $record['id'])
                        ->render() : '';

                    $icon = null;

                    $publicIn  = $record['evidence'] == 2 ? '<small class="badge badge-danger">Homepage</small> ' : '';

                    //Setto i dati da mostrare nelle colonne del datatable
                    $setTempData = [];

                    $checkBox = ($this->acl->getCrud() || checkRecordOwner($record['owner_id'])) ? ButtonAction::checkList('item[]', $record['id']) : '';

                    $setTempData[] = $checkBox;
                    $setTempData[] = $icon . (!empty($record['title']) ? escapeXss($record['title']) : 'N.D.');
                    $setTempData[] = !empty($record['typology']) ? ucfirst(escapeXss($record['typology'])) : 'N.D.';
                    $setTempData[] = $newsDate;
                    $setTempData[] = $publicIn;

                    $setTempData[] = $buttonAction;

                    $data[] = $setTempData;
                }

                $response['aaData'] = $data;
            }

            jsonOut($response);
        }
    }

    /**
     * @description Renderizza il form di creazione di una nuova Notizia
     *
     * @return void
     * @throws Exception
     * @url /admin/news-notice/create.html
     * @method GET
     */
    public function create(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(__FUNCTION__);

        $this->breadcrumb->push('News ed avvisi', 'admin/news-notice');
        $this->breadcrumb->push('Nuovo', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'News ed avvisi';
        $data['subTitleSection'] = 'GESTIONE DI NOTIZIE ED AVVISI A SUPPORTO DEI CONTENUTI';
        $data['sectionIcon'] = '<i class="fas fa-bullhorn fa-3x"></i>';

        $data['formAction'] = '/admin/news-notice/store';
        $data['formSettings'] = [
            'name' => 'form_news-notice',
            'id' => 'form_news-notice',
            'class' => 'form_news-notice',
        ];
        $data['_storageType'] = 'insert';

        // Labels
        $data['labels'] = [];

        render('news_notice/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua lo storage di una nuova Notizia
     *
     * @return void
     * @throws Exception
     * @url /admin/news-notice/store.html
     * @method POST
     */
    public function store(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create', true);

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new NewsNoticeValidator();
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
                    'title' => strip_tags(Input::post('title', true)),
                    'typology' => setDefaultData(strip_tags(Input::post('typology', true)), null, ['']),
                    'evidence' => setDefaultData(strip_tags((string)Input::post('evidence', true)), null, ['']),
                    'public_in_notice' => setDefaultData(strip_tags((string)Input::post('public_in_notice', true)), null, ['']),
                    'news_date' => !empty(Input::post('news_date')) ? convertDateToDatabase(strip_tags(Input::post('news_date', true))) : null,
                    'expiration_date' => !empty(Input::post('expiration_date')) ? convertDateToDatabase(strip_tags(Input::post('expiration_date', true))) : null,
                    'start_date' => !empty(Input::post('start_date')) ? convertDateToDatabase(strip_tags(Input::post('start_date', true))) : null,
                    'end_date' => !empty(Input::post('end_date')) ? convertDateToDatabase(strip_tags(Input::post('end_date', true))) : null,
                    'content' => Input::post('content', true)
                ];

                // Storage nuova News
                $insert = NewsNoticesModel::createWithLogs($arrayValues);

                // Storage allegati associati al personale.
                $attach->storage('attach_files', 'news_notices', $insert->id, $insert['title']);

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
     * @description Renderizza il form di modifica/duplicazione di una Notizia
     *
     * @return void
     * @throws Exception
     * @url /admin/news-notice/edit/:id.html
     * @method GET
     */
    public function edit(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['update', 'create']);

        //Validatore che verifica se l'elemento da modificare esiste
        $validator = new NewsNoticeValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/news-notice', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Allegati
        $attach = new AttachmentArchive();

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $newsNotice = Registry::get('news_notice');
        $newsNotice = !empty($newsNotice) ? $newsNotice->toArray() : [];

        $this->breadcrumb->push('News ed avvisi', 'admin/news-notice');
        $this->breadcrumb->push('Modifica', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'News ed avvisi';
        $data['subTitleSection'] = 'GESTIONE DI NOTIZIE ED AVVISI A SUPPORTO DEI CONTENUTI';
        $data['sectionIcon'] = '<i class="fas fa-bullhorn fa-3x"></i>';

        // Vedo in sessione se sono arrivato qui da un operazione di ripristino dal versioning
        $session = new Session();
        $restore = $session->getFlash('restore');
        $data['restore'] = $restore;

        // Controllo se si sta duplicando un tasso di assenza
        $isDuplicate = uri()->segment(3, 0) === 'duplicate';

        // Setto il tipo di storage
        $data['_storageType'] = $isDuplicate ? 'duplicate' : 'update';

        //In base al tipo di storage setto l'action del form
        $data['formAction'] = ($isDuplicate) ? '/admin/news-notice/store' : '/admin/news-notice/update';
        $data['formSettings'] = [
            'name' => 'form_news-notice',
            'id' => 'form_news-notice',
            'class' => 'form_news-notice',
        ];

        $newsDate = convertDateToForm($newsNotice['news_date']);
        $startDate = convertDateToForm($newsNotice['start_date']);
        $endDate = convertDateToForm($newsNotice['end_date']);
        $expirationDate = convertDateToForm($newsNotice['expiration_date']);

        $data['news_notices'] = $newsNotice;

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'news_notices',
            $newsNotice['id']
        );

        // Labels
        $data['labels'] = [];

        $data['news_date'] = $newsDate['date'];
        $data['start_date'] = $startDate['date'];
        $data['expiration_date'] = $expirationDate['date'];
        $data['end_date'] = $endDate['date'];
        $data['seo'] = $newsNotice['p_s_d_r'] ?? null;

        render('news_notice/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua l'update di una Notizia
     *
     * @return void
     * @throws Exception
     * @url /admin/news-notice/update.html
     * @method POST
     */
    public function update(): void
    {
        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new NewsNoticeValidator();
        $check = $validator->check('update');

        if ($check['is_success']) {
            $doAction = true;

            $newsId = (int)strip_tags(Input::post('id', true));

            // Recupero la news attuale prima di modificarla e la salvo nel versioning
            $newsNotice = NewsNoticesModel::where('id', $newsId)
                ->with('all_attachs');


            $newsNotice = $newsNotice->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute(checkRecordOwner($newsNotice['owner_id']) ? ['update', 'create'] : 'update', true);

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
                $data['title'] = strip_tags(Input::post('title', true));
                $data['typology'] = setDefaultData(strip_tags(Input::post('typology', true)), null, ['']);
                $data['evidence'] = setDefaultData(strip_tags((string)Input::post('evidence', true)), null, ['']);
                $data['public_in_notice'] = setDefaultData(strip_tags((string)Input::post('public_in_notice', true)), null, ['']);
                $data['news_date'] = !empty(Input::post('news_date')) ? convertDateToDatabase(strip_tags(Input::post('news_date', true))) : null;
                $data['start_date'] = !empty(Input::post('start_date')) ? convertDateToDatabase(strip_tags(Input::post('start_date', true))) : null;
                $data['expiration_date'] = !empty(Input::post('expiration_date')) ? convertDateToDatabase(strip_tags(Input::post('expiration_date', true))) : null;
                $data['end_date'] = !empty(Input::post('end_date')) ? convertDateToDatabase(strip_tags(Input::post('end_date', true))) : null;
                $data['content'] = Input::post('content', true);

                // Update News
                NewsNoticesModel::where('id', $newsId)->updateWithLogs($newsNotice, $data);


                // Upload allegati associati al personale.
                $attach->update(
                    'attach_files',
                    'news_notices',
                    $newsId,
                    $newsNotice['institution_id'],
                    $newsNotice['title'],
                );


                // Generazione nuovo token
//                Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
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
     * @description Funzione che effettua l'eliminazione di una Notizia
     *
     * @return void
     * @throws Exception
     * @url /admin/news-notice/delete/:id.html
     * @method GET
     */
    public function delete(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute([__FUNCTION__, 'create', 'update'], true);

        //Validatore che verifica se l'elemento da eliminare esiste
        $validator = new NewsNoticeValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getDelete() && $this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/news-notice', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Se la validazione va a buon fine recupero l'elemento da eliminare dal registro
        $newsNotice = Registry::get('news_notice');

        //Elimino la news
        $newsNotice->deleteWithLogs($newsNotice);

        sessionSetNotify(__('success_delete_operation', null, 'patos'));

        redirect('admin/news-notice');
    }

    /**
     * @description Funzione per l'eliminazione multipla
     *
     * @return void
     * @throws Exception
     * @url /admin/news-notice/deletes.html
     * @method GET
     */
    public function deletes(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('delete', true);

        $validator = new NewsNoticeValidator();
        $validate = $validator->multipleSelection();

        if ($validate['is_success']) {

            sessionSetNotify('Operazione avvenuta con successo');

            //Recupero gli elementi da eliminare dal registro
            $news = Registry::get('__ids__multi_select_profile__');

            //Elimino gli elementi
            foreach ($news as $n) {
                $n->deleteWithLogs($n);
            }

        } else {

            sessionSetNotify($validate['errors'], 'danger');
        }

        redirect('/admin/news-notice');
    }

}
