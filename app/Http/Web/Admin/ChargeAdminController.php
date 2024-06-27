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
use Helpers\Validators\ChargeValidator;
use Helpers\Validators\DatatableValidator;
use Model\ChargesModel;
use Scope\DeletedScope;
use System\Arr;
use System\Input;
use System\JsonResponse;
use System\Registry;
use System\Session;
use System\Token;

/**
 *
 * Controller Oneri Informativi
 *
 */
class ChargeAdminController extends BaseAuthController
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
     * @description Renderizza la pagina index degli Oneri
     * @return void
     * @throws Exception
     * @url /admin/charge.html
     * @method GET
     */
    public function index(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['read', 'create']);

        $this->breadcrumb->push('Oneri informativi e obblighi', '/');
        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Oneri informativi e obblighi';
        $data['subTitleSection'] = 'GESTIONE DEGLI ONERI INFORMATIVI E DEGLI OBBLIGHI AMMINISTRATIVI';
        $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';

        $data['formAction'] = '/admin/charge';
        $data['formSettings'] = [
            'name' => 'form_charge',
            'id' => 'form_charge',
            'class' => 'form_charge',
        ];

        $data['include'] = '';

        $data['url'] = uri()->segment(2, 0);

        render('charge/index', $data, 'admin');
    }

    /**
     * @description Funzione per la paginazione dei dati nel datatable
     *
     * @return void
     * @url /admin/charge/list.html
     * @throws Exception
     */
    public function asyncPaginateDatatable(): void
    {
        // Setto il metodo della rotta per i permessi
        $this->acl->setRoute('read');

        // Validatore datatable
        $datatableValidator = new DatatableValidator();
        $validator = $datatableValidator->validate();
        $response = [];

        // Controllo se è una richiesta Ajax e se il datatable è stato validato correttamente
        if (Input::isAjax() === true && $validator['is_success'] === true) {

            $data = [];

            // Colonne su cui poter effettuare l'ordinamento
            $orderable = [
                1 => 'title',
                2 => 'type',
                5 => 'users.name',
                6 => 'updated_at'
            ];

            // Se l'utente è super Admin mostro la colonna dell'ente
            if (isSuperAdmin(true)) {
                $orderable[7] = 'i.full_name_institution';
            }

            //Setto proprietà datatable
            $dataTable = [];
            setDataTableData($dataTable, 'title');

            // Query per i dati da mostrare nel datatable
            $totalRecords = ChargesModel::select(['count(id) as allcount'])
                ->count();

            $totalRecordsWithFilter = ChargesModel::search($dataTable['searchValue'])
                ->select(['count(object_charges.id) as allcount'])
                ->join('users', 'users.id', '=', 'object_charges.owner_id', 'left outer')
                ->count();

            $order = setOrderDatatable($dataTable['columnName'], $orderable, 'title');

            $records = ChargesModel::search($dataTable['searchValue'])
                ->select(['object_charges.id', 'object_charges.owner_id', 'object_charges.institution_id', 'type', 'title', 'object_charges.updated_at',
                    'publishing_status', 'users.name', 'i.full_name_institution'])
                ->join('users', 'users.id', '=', 'object_charges.owner_id', 'left outer')
                ->with('proceedings:id,name,archived')
                ->with('regulations:id,title')
                ->with(['created_by' => function ($query) {
                    $query->withoutGlobalScopes([DeletedScope::class]);
                    $query->select(['id', 'name', 'deleted']);
                }])
                ->with('institution:id,full_name_institution')
                ->join('institutions as i', 'object_charges.institution_id', '=', 'i.id', 'left outer')
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

                    if (!empty($record['proceedings']) && is_array($record['proceedings'])) {

                        $tmpProceedings = Arr::pluck($record['proceedings'], 'name');
                        $tmpArchProceedings = Arr::pluck($record['proceedings'], 'archived');
                        $proceedings = str_replace(',', ',' . nbs(2), implode(
                            ',',
                            array_map(
                                function ($proceeding) {
                                    return ('<small class="badge badge-primary mb-1">'
                                        . escapeXss($proceeding) . '</small>');
                                },
                                $tmpProceedings,
                                $tmpArchProceedings
                            )
                        ));
                    } else {

                        $proceedings = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definiti">N.D.</small>';
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
                        ->addEdit('admin/charge/edit/' . $record['id'], $record['id'])
                        ->addDuplicate('admin/charge/duplicate/' . $record['id'], $record['id'])
                        ->addDelete('admin/charge/delete/' . $record['id'], $record['id'])
                        ->render() : '';

                    $icon = null;

                    //Setto i dati da mostrare nelle colonne del datatable
                    $setTempData = [];
                    $setTempData[] = ($this->acl->getCrud() || checkRecordOwner($record['owner_id'])) ? ButtonAction::checkList('item[]', $record['id']) : '';
                    $setTempData[] = $icon . (!empty($record['title'])
                            ? '<a href="' . siteUrl('/page/31/details/' . $record['id'] . '/' . urlTitle($record['title'])) . '" target="_blank">' . escapeXss($record['title']) . '</a>'
                            : 'N.D.');
                    $setTempData[] = !empty($record['type']) ? ucfirst(escapeXss($record['type'])) : 'N.D.';
                    $setTempData[] = $proceedings;
                    $setTempData[] = $regulations;
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
     * @description Renderizza il form di creazione di un nuovo Onere
     * @return void
     * @throws Exception
     * @url /admin/charge/create.html
     * @method GET
     */
    public function create(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(__FUNCTION__);

        $data = [];

        //Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $data['is_box'] = uri()->uriString() === 'admin/charge/create-box';

        //Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {
            $this->breadcrumb->push('Oneri informativi e obblighi', 'admin/charge');
            $this->breadcrumb->push('Nuovo', '/');
            $data['breadcrumbs'] = $this->breadcrumb->show();

            //Dati header della sezione
            $data['titleSection'] = 'Oneri informativi e obblighi';
            $data['subTitleSection'] = 'GESTIONE DEGLI ONERI INFORMATIVI E DEGLI OBBLIGHI AMMINISTRATIVI';
            $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';
        }

        $data['formAction'] = '/admin/charge/store';
        $data['formSettings'] = [
            'name' => 'form_charge',
            'id' => 'form_charge',
            'class' => 'form_charge',
        ];
        $data['_storageType'] = 'insert';

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        // Labels
        $data['labels'] = [];

        render('charge/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua lo storage di un nuovo Onere informativo
     *
     * @return void
     * @throws Exception
     * @url /admin/charge/store.html
     * @method POST
     */
    public function store(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new ChargeValidator();
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
                    'type' => setDefaultData(strip_tags((string)Input::post('type', true)), null, ['']),
                    'citizen' => setDefaultData(strip_tags((string)Input::post('citizen', true)), null, ['']),
                    'companies' => setDefaultData(strip_tags((string)Input::post('companies', true)), null, ['']),
                    'title' => strip_tags((string)Input::post('title', true)),
                    'expiration_date' => !empty(Input::post('expiration_date')) ? convertDateToDatabase(strip_tags((string)Input::post('expiration_date', true))) : null,
                    'normative_id' => setDefaultData(strip_tags((string)Input::post('normative_id', true)), null, ['']),
                    'description' => Input::post('description', true),
                    'info_url' => strip_tags((string)Input::post('info_url', true))
                ];

                // Storage nuovo Onere informativo
                $insert = ChargesModel::createWithLogs($arrayValues);

                // Storage nelle tabelle di relazione
                $this->clear(
                    $insert,
                    Input::post('proceedings', true),
                    !empty(Input::post('measures')) ? explode(',', strip_tags((string)Input::post('measures', true))) : null,
                    Input::post('regulations', true)
                );

                // Storage allegati associati al personale.
                $attach->storage('attach_files', 'charges', $insert->id, $arrayValues['title']);

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
     * @description Renderizza il form di modifica/duplicazione di un Onere
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/charge/edit/:id.html
     * @method GET
     */
    public function edit(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['update', 'create']);

        //Validatore che verifica se l'elemento da modificare esiste
        $validator = new ChargeValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        if (!$validate['is_success']) {
            redirect('admin/charge', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Allegati
        $attach = new AttachmentArchive();

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $charge = Registry::get('charge');
        $charge = !empty($charge) ? $charge->toArray() : [];

        $this->breadcrumb->push('Oneri informativi e obblighi', 'admin/charge');
        $this->breadcrumb->push('Modifica', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Oneri informativi e obblighi';
        $data['subTitleSection'] = 'GESTIONE DEGLI ONERI INFORMATIVI E DEGLI OBBLIGHI AMMINISTRATIVI';
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
        $data['formAction'] = ($isDuplicate) ? '/admin/charge/store' : '/admin/charge/update';
        $data['formSettings'] = [
            'name' => 'form_charge',
            'id' => 'form_charge',
            'class' => 'form_charge',
        ];

        $expirationDate = convertDateToForm($charge['expiration_date']);

        $data['charge'] = $charge;

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'charges',
            $charge['id']
        );

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = $charge['institution_id'];

        $data['expiration_date'] = $expirationDate['date'];

        $data['proceedingIds'] = Arr::pluck($charge['proceedings'], 'id');
        $data['measureIds'] = Arr::pluck($charge['measures'], 'id');
        $data['regulationIds'] = Arr::pluck($charge['regulations'], 'id');
        $data['seo'] = $charge['p_s_d_r'] ?? null;

        render('charge/form_store', $data, 'admin');
    }

    /**
     * @description Funzione che effettua l'update di un Onere informativo
     *
     * @return void
     * @throws Exception
     * @url /admin/charge/update.html
     * @method POST
     */
    public function update(): void
    {

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new ChargeValidator();
        $check = $validator->check('update');

        if ($check['is_success']) {
            $doAction = true;

            $chargeId = (int)strip_tags(Input::post('id'));

            // Recupero l'onere attuale prima di modificarlo e lo salvo nel versioning
            $charge = ChargesModel::where('id', $chargeId)
                ->with('proceedings:id,name')
                ->with('measures:id,object')
                ->with('regulations:id,title')
                ->with('normative:id,name')
                ->with('all_attachs');

            $charge = $charge->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute(__FUNCTION__, (!checkRecordOwner($charge['owner_id']) && $this->acl->getCreate()));

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
                $data = [];

                $data['type'] = setDefaultData(strip_tags((string)Input::post('type', true)), null, ['']);
                $data['citizen'] = setDefaultData(strip_tags((string)Input::post('citizen', true)), null, ['']);
                $data['companies'] = setDefaultData(strip_tags((string)Input::post('companies', true)), null, ['']);
                $data['title'] = strip_tags((string)Input::post('title', true));
                $data['expiration_date'] = !empty((string)Input::post('expiration_date')) ? convertDateToDatabase(strip_tags((string)Input::post('expiration_date', true))) : null;
                $data['normative_id'] = setDefaultData(strip_tags((string)Input::post('normative_id', true)), null, ['']);
                $data['description'] = Input::post('description', true);
                $data['info_url'] = strip_tags((string)Input::post('info_url', true));

                // Update Onere
                ChargesModel::where('id', $chargeId)->updateWithLogs($charge, $data);

                // Svuoto le tabelle di relazione e le aggiorno
                $this->clear(
                    $charge,
                    Input::post('proceedings', true),
                    !empty(Input::post('measures')) ? explode(',', strip_tags((string)Input::post('measures', true))) : null,
                    Input::post('regulations', true)
                );

                // Upload allegati associati al personale.
                $attach->update(
                    'attach_files',
                    'charges',
                    $chargeId,
                    $charge['institution_id'],
                    $charge['title']
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
     * @param ChargesModel|null $charge      Onere
     * @param array|int|null    $proceedings Procedimenti associati all'Onere
     * @param array|int|null    $measures    Provvedimenti associati all'Onere
     * @param array|int|null    $regulations Regolamenti associati all'Onere
     * @return void
     */
    protected function clear(ChargesModel $charge = null, array|int $proceedings = null, array|int $measures = null, array|int $regulations = null): void
    {
        $dataProceedings = [];
        if ($proceedings !== null) {
            foreach ($proceedings as $proceeding) {
                $dataProceedings[] = is_array($proceeding) ? $proceeding['id'] : $proceeding;
            }
        }
        //Insert/Update nella tabella di relazione
        $charge->proceedings()->sync($dataProceedings);

        $dataMeasures = [];
        if ($measures !== null) {
            foreach ($measures as $measure) {
                $dataMeasures[] = is_array($measure) ? $measure['id'] : $measure;
            }
        }
        //Insert/Update nella tabella di relazione
        $charge->measures()->sync($dataMeasures);

        $dataRegulations = [];
        if ($regulations !== null) {
            foreach ($regulations as $regulation) {
                $dataRegulations[] = is_array($regulation) ? $regulation['id'] : $regulation;
            }
        }
        //Insert/Update nella tabella di relazione
        $charge->regulations()->sync($dataRegulations);
    }


    /**
     * @description Funzione che effettua l'eliminazione di un Onere informativo
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/charge/delete/:id.html
     * @method GET
     */
    public function delete(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute([__FUNCTION__, 'create', 'update'], true);

        //Validatore che verifica se l'elemento da eliminare esiste
        $validator = new ChargeValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getDelete() && $this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/charge', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Se la validazione va a buon fine recupero l'elemento da eliminare dal registro
        $charge = Registry::get('charge');

        $chargeId = $charge->id;

        //Elimino l'onere
        $charge->deleteWithLogs($charge);

        sessionSetNotify(__('success_delete_operation', null, 'patos'));

        // Generazione nuovo token
        Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        redirect('admin/charge');
    }

    /**
     * @description Funzione per l'eliminazione multipla
     *
     * @return void
     * @throws Exception
     * @url /admin/charge/deletes.html
     * @method GET
     */
    public function deletes(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('delete');

        $validator = new ChargeValidator();
        $validate = $validator->multipleSelection();

        if ($validate['is_success']) {

            sessionSetNotify('Operazione avvenuta con successo');

            //Recupero gli elementi da eliminare dal registro
            $charges = Registry::get('__ids__multi_select_profile__');

            //Estraggo gli array degli elementi da eliminare
            $ids = Arr::pluck($charges, 'id');

            //Elimino gli elementi
            foreach ($charges as $charge) {
                $charge->deleteWithLogs($charge);
            }

            // Generazione nuovo token
            Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        } else {

            sessionSetNotify($validate['errors'], 'danger');
        }

        redirect('/admin/charge');
    }
}
