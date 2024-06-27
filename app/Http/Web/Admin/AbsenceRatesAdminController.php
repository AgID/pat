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
use Helpers\Validators\AbsenceRatesValidator;
use Helpers\Validators\DatatableValidator;
use Model\AbsenceRatesModel;
use Model\StructuresModel;
use Scope\DeletedScope;
use System\Input;
use System\JsonResponse;
use System\Registry;
use System\Session;
use System\Token;

/**
 *
 * Controller Tassi di Assenza
 *
 */
class AbsenceRatesAdminController extends BaseAuthController
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
     * @description Renderizza la pagina index dei Tassi di Assenza
     *
     * @return void
     * @throws Exception
     * @url /admin/absence-rates.html
     * @method GET
     */
    public function index(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['read', 'create']);

        $this->breadcrumb->push('Tassi di assenze', '/');
        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Tassi di assenze';
        $data['subTitleSection'] = 'GESTIONE DEI TASSI DI ASSENZA DEL PERSONALE';
        $data['sectionIcon'] = '<i class="fas fa-briefcase fa-3x"></i>';

        $data['formAction'] = '/admin/absence-rates';
        $data['formSettings'] = [
            'name' => 'form_absence-rates',
            'id' => 'form_absence-rates',
            'class' => 'form_absence-rates',
        ];

        $data['include'] = '';

        $data['url'] = uri()->segment(2, 0);

        render('absence_rates/index', $data, 'admin');
    }

    /**
     * @description Funzione per la paginazione dei dati nel datatable
     *
     * @return void
     * @url /admin/absence-rates/list.html
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
                1 => 'structure_name',
                2 => 'year',
                4 => 'users.name',
                5 => 'updated_at'
            ];

            //Se l'utente è super Admin mostro la colonna dell'ente
            if (isSuperAdmin(true)) {
                $orderable[6] = 'i.full_name_institution';
            }

            //Setto proprietà datatable
            $dataTable = [];
            setDataTableData($dataTable, 'structure_name');

            //Query per i dati da mostrare nel datatable
            $totalRecords = AbsenceRatesModel::select('count(id) as allcount')
                ->count();

            $totalRecordsWithFilter = AbsenceRatesModel::search($dataTable['searchValue'])
                ->select(['count(id) as allcount'])
                ->join('users', 'users.id', '=', 'object_absence_rates.owner_id', 'left outer');

            if (!empty($dataTable['searchValue'])) {
                $totalRecordsWithFilter->orWhere('object_absence_rates.id', '=', $dataTable['searchValue']);
            }

            $totalRecordsWithFilter = $totalRecordsWithFilter->count();

            $order = setOrderDatatable($dataTable['columnName'], $orderable, 'structure_name');

            $records = AbsenceRatesModel::search($dataTable['searchValue'])
                ->select(['object_absence_rates.id', 'object_absence_rates.updated_at', 'object_absence_rates.owner_id', 'object_absence_rates.institution_id', 'object_structures_id',
                    'publishing_status', 'year', 'month', 'publishing_status', 'structure_name', 'users.name', 'i.full_name_institution'])
                ->with('structure:id,structure_name,archived')
                ->with(['created_by' => function ($query) {
                    $query->withoutGlobalScopes([DeletedScope::class]);
                    $query->select(['id', 'name', 'deleted']);
                }])
                ->with('institution:id,full_name_institution')
                ->join('institutions as i', 'object_absence_rates.institution_id', '=', 'i.id', 'left outer')
                ->join('users', 'users.id', '=', 'object_absence_rates.owner_id', 'left outer');

            if (!empty($dataTable['searchValue'])) {
                $records->orWhere('object_absence_rates.id', '=', $dataTable['searchValue']);
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

            $periods = config('absenceRatesPeriod', null, 'app');

            if (!empty($records)) {

                foreach ($records as $record) {

                    if (!empty($record['month'])) {
                        $period = str_replace(',', ',' . nbs(2), implode(
                            ',',
                            array_map(
                                function ($number) use ($periods) {
                                    $tmpNumber = '0'.$number;
                                    if(!empty($periods[$number]) || !empty($periods[$tmpNumber])) {
                                        return ('<small class="badge badge-primary mb-1">' . escapeXss($periods[$number] ?? $periods[$tmpNumber]) . '</small>');
                                    }
                                },
                                explode(',', (string)$record['month'])
                            )
                        ));
                    } else {
                        $period = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definito">N.D.</small>';
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
                        ->addEdit('admin/absence-rates/edit/' . $record['id'], $record['id'])
                        ->addDuplicate('admin/absence-rates/duplicate/' . $record['id'], $record['id'])
                        ->addDelete('admin/absence-rates/delete/' . $record['id'], $record['id'])
                        ->render() : '';

                    $tmpStructureName = !empty($record['structure'])
                        ? ('<a href="' . siteUrl('/page/66/details/' . $record['id'] . '/tassi-di-assenza') . '" target="_blank">' . escapeXss($record['structure']['structure_name']) . '</a>')
                        : (!empty($record['structure_name']) ? '<a href="' . siteUrl('/page/66/details/' . $record['id'] . '/tassi-di-assenza') . '" target="_blank">' . escapeXss($record['structure_name']) . '</a>' : null);

                    $icon = null;

                    //Setto i dati da mostrare nelle colonne del datatable
                    $setTempData = [];
                    $setTempData[] = (($this->acl->getCrud() || checkRecordOwner($record['owner_id'])) ? ButtonAction::checkList('item[]', $record['id']) : '') . $icon;
                    $setTempData[] = $tmpStructureName;
                    $setTempData[] = !empty($record['year']) ? escapeXss($record['year']) : 'N.D.';
                    $setTempData[] = $period;
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
     * @description Renderizza il form per la creazione di un nuovo Tasso di Assenza
     *
     * @return void
     * @throws Exception
     * @url /admin/absence-rates/create.html
     * @method GET
     */
    public function create(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(__FUNCTION__);

        $this->breadcrumb->push('Tassi di assenze', 'admin/absence-rates');
        $this->breadcrumb->push('Nuovo', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Tassi di assenze';
        $data['subTitleSection'] = 'GESTIONE DEI TASSI DI ASSENZA DEL PERSONALE';
        $data['sectionIcon'] = '<i class="fas fa-briefcase fa-3x"></i>';

        $data['formAction'] = '/admin/absence-rates/store';
        $data['formSettings'] = [
            'name' => 'form_absence-rates',
            'id' => 'form_absence-rates',
            'class' => 'form_absence-rates',
        ];
        $data['_storageType'] = 'insert';

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        render('absence_rates/form_store', $data, 'admin');
    }

    /**
     * @description Funzione per lo storage di un nuovo tasso di assenza
     *
     * @return void
     * @method POST
     * @throws Exception
     * @url /admin/absence-rates/store.html
     */
    public function store(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new AbsenceRatesValidator();
        $check = $validator->check();

        // Controllo se la validazione è andata a buon fine
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
                // Dati per registrazione ActivityLog
                $getIdentity = authPatOs()->getIdentity(['id', 'name']);

                $structure = StructuresModel::find(strip_tags((int)Input::post('object_structures_id', true)));

                $arrayValues = [
                    'owner_id' => $getIdentity['id'],
                    'institution_id' => strip_tags(checkAlternativeInstitutionId()),
                    'object_structures_id' => strip_tags((int)Input::post('object_structures_id', true)),
                    'structure_name' => !empty($structure->structure_name) ? escapeXss($structure->structure_name) : null,
                    'month' => strip_tags(implode(",", setDefaultData(Input::post('months', true), null, ['']))),
                    'year' => setDefaultData(strip_tags((string)Input::post('year', true)), null, ['']),
                    'presence_percentage' => setDefaultData(toFloat(strip_tags((string)Input::post('presence_percentage', true))), null, ['']),
                    'total_absence' => setDefaultData(toFloat(strip_tags((string)Input::post('total_absence', true))), null, ['']),
                ];

                // Storage nuovo Tasso di assenza
                $insert = AbsenceRatesModel::createWithLogs($arrayValues);

                // Storage allegati associati al tasso di assenza
                $attach->storage('attach_files', 'absence_rates', $insert->id);

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
     * @description Renderizza il form per la modifica/duplicazione di un Tasso di Assenza
     * @return void
     * @method GET
     * @throws Exception
     * @url /admin/absence-rates/edit/:num.html
     */
    public function edit(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['update', 'create']);

        // Validatore che verifica se l'elemento da modificare esiste
        $validator = new AbsenceRatesValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        if (!$validate['is_success']) {
            sessionSetNotify($validate['errors'], 'danger');
            redirect('admin/absence-rates');
            exit();
        }

        // Controllo se si sta duplicando un tasso di assenza
        $isDuplicate = uri()->segment(3, 0) === 'duplicate';

        $data = [];

        // Allegati
        $attach = new AttachmentArchive();

        // Vedo in sessione se sono arrivato qui da un operazione di ripristino dal versioning
        $session = new Session();
        $restore = $session->getFlash('restore');
        $data['restore'] = $restore;

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $absenceRates = Registry::get('absence_rate');

        $absenceRates = !empty($absenceRates) ? $absenceRates->toArray() : [];

        $this->breadcrumb->push('Tassi di assenze', 'admin/absence-rates');
        $this->breadcrumb->push('Modifica', '/');

        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Tassi di assenze';
        $data['subTitleSection'] = 'GESTIONE DEI TASSI DI ASSENZA DEL PERSONALE';
        $data['sectionIcon'] = '<i class="fas fa-briefcase fa-3x"></i>';

        // Setto il tipo di storage
        $data['_storageType'] = $isDuplicate ? 'duplicate' : 'update';

        //In base al tipo di storage setto l'action del form
        $data['formAction'] = ($isDuplicate) ? '/admin/absence-rates/store' : '/admin/absence-rates/update';
        $data['formSettings'] = [
            'name' => 'form_absence_rates',
            'id' => 'form_absence_rates',
            'class' => 'form_absence_rates'
        ];

        $data['absenceRates'] = $absenceRates;

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'absence_rates',
            $absenceRates['id'],
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

        $data['period'] = explode(",", $absenceRates['month']);

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = $absenceRates['institution_id'];
        $data['seo'] = $absenceRates['p_s_d_r'] ?? null;

        render('absence_rates/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua l'update di un tasso di assenza
     *
     * @return void
     * @method POST
     * @throws Exception
     * @url /admin/absence-rates/update.html
     */
    public function update(): void
    {

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new AbsenceRatesValidator();
        $check = $validator->check('update');

        // Controllo se la validazione è andata a buon fine
        if ($check['is_success']) {
            $doAction = true;

            // Recupero il tasso di assenza attuale prima di modificarlo
            $absenceRate = AbsenceRatesModel::where('id', (int)strip_tags(Input::post('id')))
                ->with('all_attachs');
            $absenceRate = $absenceRate->first();

            // Setto il metodo della rotta per i permessi
            $this->acl->setRoute(__FUNCTION__, (!checkRecordOwner($absenceRate['owner_id']) && $this->acl->getCreate()));

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
                $structure = StructuresModel::find(strip_tags((int)Input::post('object_structures_id', true)));

                $data['object_structures_id'] = strip_tags((int)Input::post('object_structures_id', true));
                $data['structure_name'] = !empty($structure->structure_name) ? escapeXss($structure->structure_name) : null;
                $data['month'] = strip_tags(implode(",", setDefaultData(Input::post('months', true), null, [''])));
                $data['year'] = setDefaultData(strip_tags((string)Input::post('year', true)), null, ['']);
                $data['presence_percentage'] = setDefaultData(toFloat(strip_tags((string)Input::post('presence_percentage', true))), null, ['']);
                $data['total_absence'] = setDefaultData(toFloat(strip_tags((string)Input::post('total_absence', true))), null, ['']);
                $absenceRateId = (int)strip_tags(Input::post('id'));

                // Update Tasso di assenza
                AbsenceRatesModel::where('id', $absenceRateId)->updateWithLogs($absenceRate, $data);

                // Upload allegati associati al personale.
                $attach->update(
                    'attach_files',
                    'absence_rates',
                    strip_tags((int)Input::post('id', true)),
                    $absenceRate['institution_id']
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
     * @description Funzione per l'elimina un Tasso di Assenza
     *
     * @return void
     * @method GET
     * @throws Exception
     * @url /admin/absence-rates/delete.html
     */
    public function delete(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute([__FUNCTION__, 'create', 'update'], true);

        //Validatore che verifica se l'elemento da eliminare esiste
        $validator = new AbsenceRatesValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getDelete() && $this->acl->getUpdate());

        // Controllo se la validazione è andata a buon fine
        if (!$validate['is_success']) {
            sessionSetNotify($validate['errors'], 'danger');
            redirect('admin/absence-rates');
            exit();
        }

        // Se la validazione va a buon fine recupero l'elemento da eliminare dal registro
        $absenceRates = Registry::get('absence_rate');

        // Delete Tasso di assenza
        $absenceRates->deleteWithLogs($absenceRates);

        // Generazione nuovo token
        Token::forceRegenerate((int)config('csrf_expire', null, 'app'));

        sessionSetNotify(__('success_delete_operation', null, 'patos'));

        redirect('admin/absence-rates');
    }

    /**
     * @description Funzione per l'eliminazione multipla
     *
     * @return void
     * @method GET
     * @throws Exception
     * @url /admin/absence-rates/deletes.html
     */
    public function deletes(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('delete');

        // Validatore sugli elementi da eliminare
        $validator = new AbsenceRatesValidator();
        $validate = $validator->multipleSelection();

        if ($validate['is_success']) {

            sessionSetNotify('Operazione avvenuta con successo');

            //Recupero gli elementi da eliminare dal registro
            $absenceRates = Registry::get('__ids__multi_select_profile__');

            //Elimino gli elementi
            foreach ($absenceRates as $absenceRate) {
                $absenceRate->deleteWithLogs($absenceRate);
            }

            // Generazione nuovo token
            Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        } else {

            sessionSetNotify($validate['errors'], 'danger');
        }

        redirect('/admin/absence-rates');
    }
}
