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
use Helpers\Validators\ProgrammingActValidator;
use Model\ProgrammingActsModel;
use Scope\DeletedScope;
use System\Arr;
use System\Input;
use System\JsonResponse;
use System\Registry;
use System\Session;
use System\Token;

/**
 *
 * Controller Atti di programmazione
 *
 */
class ProgrammingActAdminController extends BaseAuthController
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
     * @description Renderizza la pagina index degli Atti di programmazione
     *
     * @return void
     * @throws Exception
     * @url /admin/programming-act.html
     * @method GET
     */
    public function index(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['read', 'create']);

        $this->breadcrumb->push('Atti di programmazione', '/');
        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Atti di programmazione';
        $data['subTitleSection'] = 'GESTIONE DEGLI ATTI DI PROGRAMMAZIONE';
        $data['sectionIcon'] = '<i class="fas fa-file-contract fa-3x"></i>';

        $data['formAction'] = '/admin/programming-act';
        $data['formSettings'] = [
            'name' => 'form_programming_act',
            'id' => 'form_programming_act',
            'class' => 'form_programming_act',
        ];

        $data['include'] = '';

        $data['url'] = uri()->segment(2, 0);

        render('programming_act/index', $data, 'admin');
    }

    /**
     * @description Funzione per la paginazione dei dati nel datatable
     *
     * @return void
     * @url /admin/programming-act/list.html
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

        //Controllo se è una richiesta Ajax e se il datatable è stato validato correttamente
        if (Input::isAjax() === true && $validator['is_success'] === true) {

            $data = [];

            //Colonne su cui poter effettuare l'ordinamento
            $orderable = [
                1 => 'object',
                2 => 'date',
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
            $totalRecords = ProgrammingActsModel::select('count(id) as allcount')
                ->count();

            $totalRecordsWithFilter = ProgrammingActsModel::search($dataTable['searchValue'])
                ->select(['count(object_programming_acts.id) as allcount'])
                ->join('users', 'users.id', '=', 'object_programming_acts.owner_id', 'left outer');
            if (!empty($dataTable['searchValue'])) {
                $totalRecordsWithFilter->orWhere('object_programming_acts.id', '=', $dataTable['searchValue']);
            }
            $totalRecordsWithFilter = $totalRecordsWithFilter->count();

            $order = setOrderDatatable($dataTable['columnName'], $orderable, 'object');

            $records = ProgrammingActsModel::search($dataTable['searchValue'])
                ->select(['object_programming_acts.id', 'object_programming_acts.owner_id', 'object_programming_acts.institution_id',
                    'object', 'date', 'public_in_public_works', 'publishing_status', 'object_programming_acts.updated_at', 'users.name',
                    'i.full_name_institution'])
                ->join('users', 'users.id', '=', 'object_programming_acts.owner_id', 'left outer')
                ->with(['created_by' => function ($query) {
                    $query->withoutGlobalScopes([DeletedScope::class]);
                    $query->select(['id', 'name', 'deleted']);
                }])
                ->with('institution:id,full_name_institution');

            if (!empty($dataTable['searchValue'])) {
                $records->orWhere('object_programming_acts.id', '=', $dataTable['searchValue']);
            }

            $records = $records->join('institutions as i', 'object_programming_acts.institution_id', '=', 'i.id', 'left outer')
                ->orderBy($order, $dataTable['columnSortOrder'])
                ->offset($dataTable['start'])
                ->limit($dataTable['rowPerPage'])
                ->get()
                ->toArray();

            foreach ($records as $record) {

                $programmingActDate = !empty($record['date'])
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
                    ->addEdit('admin/programming-act/edit/' . $record['id'], $record['id'])
                    ->addDuplicate('admin/programming-act/duplicate/' . $record['id'], $record['id'])
                    ->addDelete('admin/programming-act/delete/' . $record['id'], $record['id'])
                    ->render() : '';

                $icon = null;

                //Setto i dati da mostrare nelle colonne del datatable
                $setTempData = [];
                $setTempData[] = ($this->acl->getCrud() || checkRecordOwner($record['owner_id'])) ? ButtonAction::checkList('item[]', $record['id']) : '';
                $setTempData[] = $icon . (!empty($record['object'])
                        ? '<a href="' . siteUrl('/page/113/details/' . $record['id'] . '/' . urlTitle($record['object'])) . '" target="_blank">' . escapeXss($record['object']) . '</a>'
                        : 'N.D.');
                $setTempData[] = $programmingActDate;
                $setTempData[] = ($record['public_in_public_works'] === 0) ? 'No' : 'Si';
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

            $response = [
                "draw" => intval($dataTable['draw']),
                "iTotalRecords" => $totalRecords,
                "iTotalDisplayRecords" => $totalRecordsWithFilter,
                "aaData" => $data,
            ];

            echo json_encode($response);
        }
    }

    /**
     * @description Renderizza il form di creazione di un nuovo Atto
     *
     * @return void
     * @throws Exception
     * @url /admin/programming-act/create.html
     * @method GET
     */
    public function create(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(__FUNCTION__);

        $data = [];

        //Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $data['is_box'] = uri()->uriString() === 'admin/programming-act/create-box';

        //Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {
            $this->breadcrumb->push('Atti di programmazione', 'admin/programming-act');
            $this->breadcrumb->push('Nuovo', '/');
            $data['breadcrumbs'] = $this->breadcrumb->show();

            //Dati header della sezione
            $data['titleSection'] = 'Atti di programmazione';
            $data['subTitleSection'] = 'GESTIONE DEGLI ATTI DI PROGRAMMAZIONE';
            $data['sectionIcon'] = '<i class="fas fa-file-contract fa-3x"></i>';
        }

        $data['formAction'] = '/admin/programming-act/store';
        $data['formSettings'] = [
            'name' => 'form_programming_act',
            'id' => 'form_programming_act',
            'class' => 'form_programming_act',
        ];
        $data['_storageType'] = 'insert';

        // Labels
        $data['labels'] = [];

        render('programming_act/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua lo storage di un nuovo Atto
     *
     * @return void
     * @throws Exception
     * @url /admin/programming-act/store.html
     * @method POST
     */
    public function store(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $code = $json->success();

        //Validator form
        $validator = new ProgrammingActValidator();
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
                    'object' => strip_tags(Input::post('object', true)),
                    'date' => !empty(Input::post('date')) ? convertDateToDatabase(strip_tags(Input::post('date', true))) : null,
                    'act_type' => setDefaultData(strip_tags(Input::post('act_type', true)), null, ['']),
                    'public_in_public_works' => (int)setDefaultData(strip_tags(Input::post('public_in_public_works', true)), null, ['']),
                    'description' => Input::post('description', true)
                ];

                // Storage nuovo Atto di programmazione
                $insert = ProgrammingActsModel::createWithLogs($arrayValues);

                // Storage allegati associati al personale.
                $attach->storage('attach_files', 'programming_acts', $insert->id, $insert['object']);

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
     * @throws Exception
     * @url /admin/programming-act/edit/:id.html
     * @method GET
     */
    public function edit(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['update', 'create']);

        //Validatore che verifica se l'elemento da modificare esiste
        $validator = new ProgrammingActValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/programming-act', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Allegati
        $attach = new AttachmentArchive();

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $programmingAct = Registry::get('programming_act');
        $programmingAct = !empty($programmingAct) ? $programmingAct->toArray() : [];

        $this->breadcrumb->push('Atti di programmazione', 'admin/programming-act');
        $this->breadcrumb->push('Modifica', '/');

        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Atti di programmazione';
        $data['subTitleSection'] = 'GESTIONE DEGLI ATTI DI PROGRAMMAZIONE';
        $data['sectionIcon'] = '<i class="fas fa-file-contract fa-3x"></i>';

        // Vedo in sessione se sono arrivato qui da un operazione di ripristino dal versioning
        $session = new Session();
        $restore = $session->getFlash('restore');
        $data['restore'] = $restore;

        // Controllo se si sta duplicando un tasso di assenza
        $isDuplicate = uri()->segment(3, 0) === 'duplicate';

        // Setto il tipo di storage
        $data['_storageType'] = $isDuplicate ? 'duplicate' : 'update';

        //In base al tipo di storage setto l'action del form
        $data['formAction'] = ($isDuplicate) ? '/admin/programming-act/store' : '/admin/programming-act/update';
        $data['formSettings'] = [
            'name' => 'form_programming_act',
            'id' => 'form_programming_act',
            'class' => 'form_programming_act',
        ];

        $date = convertDateToForm($programmingAct['date']);
        $programmingAct['date'] = $date['date'];

        $data['programming_act'] = $programmingAct;

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'programming_acts',
            $programmingAct['id']
        );

        // Labels
        $data['labels'] = [];

        render('programming_act/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua l'update di un Atto
     *
     * @return void
     * @throws Exception
     * @url /admin/programming-act/update.html
     * @method POST
     */
    public function update(): void
    {
        $json = new JsonResponse();
        $code = $json->success();

        //Validatore form
        $validator = new ProgrammingActValidator();
        $check = $validator->check('update');

        if ($check['is_success']) {
            $doAction = true;

            $programmingActId = (int)strip_tags(Input::post('id', true));

            // Recupero l'atto di programmazione attuale prima di modificarlo e lo salvo nel versioning
            $programmingAct = ProgrammingActsModel::where('id', $programmingActId)
                ->with('all_attachs');

            $programmingAct = $programmingAct->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute(__FUNCTION__, (!checkRecordOwner($programmingAct['owner_id']) && $this->acl->getCreate()));

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
                $data['object'] = strip_tags(Input::post('object', true));
                $data['date'] = !empty(Input::post('date')) ? convertDateToDatabase(strip_tags(Input::post('date', true))) : null;
                $data['act_type'] = setDefaultData(strip_tags(Input::post('act_type', true)), null, ['']);
                $data['public_in_public_works'] = setDefaultData(strip_tags(Input::post('public_in_public_works', true)), null, ['']);
                $data['description'] = Input::post('description', true);

                // Update Atto di programmazione
                ProgrammingActsModel::where('id', $programmingActId)->updateWithLogs($programmingAct, $data);

                // Upload allegati associati agli Atti di programmazione
                $attach->update(
                    'attach_files',
                    'programming_acts',
                    (int)strip_tags(Input::post('id', true)),
                    $programmingAct['institution_id'],
                    $programmingAct['object']
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
     * @description Funzione che effettua l'eliminazione di un Atto
     *
     * @return void
     * @throws Exception
     * @url /admin/programming-act/delete/:id.html
     * @method GET
     */
    public function delete(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute([__FUNCTION__, 'create', 'update'], true);

        //Validatore che verifica se l'elemento da eliminare esiste
        $validator = new ProgrammingActValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getDelete() && $this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/programming-act', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Se la validazione va a buon fine recupero l'elemento da eliminare dal registro
        $programmingAct = Registry::get('programming_act');

        //Elimino l'atto di programmazione
        $programmingAct->deleteWithLogs($programmingAct);

        sessionSetNotify(__('success_delete_operation', null, 'patos'));
        // Generazione nuovo token
        Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        redirect('admin/programming-act');
    }

    /**
     * @description Funzione per l'eliminazione multipla
     *
     * @return void
     * @throws Exception
     * @url /admin/programming-act/deletes.html
     * @method GET
     */
    public function deletes(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('delete');

        $validator = new ProgrammingActValidator();
        $validate = $validator->multipleSelection();

        if ($validate['is_success']) {

            sessionSetNotify('Operazione avvenuta con successo');

            //Recupero gli elementi da eliminare dal registro
            $programmingActs = Registry::get('__ids__multi_select_profile__');

            //Estraggo gli array degli elementi da eliminare
            $ids = Arr::pluck($programmingActs, 'id');

            //Elimino gli elementi
            foreach ($programmingActs as $programmingAct) {
                $programmingAct->deleteWithLogs($programmingAct);
            }

            // Generazione nuovo token
            Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        } else {

            sessionSetNotify($validate['errors'], 'danger');
        }

        redirect('/admin/programming-act');
    }
}
