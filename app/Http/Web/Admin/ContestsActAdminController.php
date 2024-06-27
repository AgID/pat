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
use Helpers\Validators\AlertValidator;
use Helpers\Validators\ContestActValidator;
use Helpers\Validators\DatatableValidator;
use Helpers\Validators\DeliberationValidator;
use Helpers\Validators\FosterValidator;
use Helpers\Validators\LotValidator;
use Helpers\Validators\NoticeLiquidationValidator;
use Helpers\Validators\ResultValidator;
use Model\ContestsActsModel;
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
 * Controller Bandi di Gare e Contratti
 *
 */
class ContestsActAdminController extends BaseAuthController
{
    protected array|null $institutionTypeId;

    /**
     * @description Costruttore
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct(__CLASS__);
        $this->institutionTypeId = patOsInstituteInfo(['institution_type_id']);
    }

    /**
     * @description Renderizza la pagina index dei bandi di gara
     * @return void
     * @throws Exception
     * @url /admin/contests-act.html
     * @method GET
     */
    public function index(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['read', 'create']);

        $this->breadcrumb->push('Bandi Gare e Contratti', '/');
        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Bandi Gare e Contratti';
        $data['subTitleSection'] = 'GESTIONE DELLE PUBBLICAZIONI DEI BANDI, GARE E CONTRATTI';
        $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';

        $data['formAction'] = '/admin/contests-act';
        $data['formSettings'] = [
            'name' => 'form_contests_act',
            'id' => 'form_contests_act',
            'class' => 'form_contests_act',
        ];

        $data['include'] = '';

        $data['url'] = uri()->segment(2, 0);

        render('contests_act/index', $data, 'admin');
    }

    /**
     * @description Funzione per la paginazione dei dati nel datatable
     *
     * @return void
     * @throws Exception
     * @url /admin/contests-act/list.html
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
                2 => 'type',
                3 => 'cig',
                4 => 'amount_liquidated',
                5 => 'structure.structure_name',
                6 => 'work_start_date',
                8 => 'users.name',
                9 => 'updated_at'
            ];

            //Se l'utente è super Admin mostro la colonna dell'ente
            if (isSuperAdmin(true)) {
                $orderable[10] = 'i.full_name_institution';
            }

            //Setto proprietà datatable
            $dataTable = [];
            setDataTableData($dataTable, 'object');

            //Query per i dati da mostrare nel datatable
            $totalRecords = ContestsActsModel::select('count(object_contests_acts.id) as allcount')
                ->count();

            $searchValue = $dataTable['searchValue'];

            // Results contest act
            $queryCount = ContestsActsModel::search($searchValue);
            $queryCount->select(['object_contests_acts.id as allcount'])
                ->join('users', 'users.id', '=', 'object_contests_acts.owner_id', 'left outer');

            if (!empty($dataTable['searchValue'])) {
                $queryCount->orWhere('object_contests_acts.id', '=', $dataTable['searchValue']);
            }

            $totalRecordsWithFilter = $queryCount->count();

            // Ordinamento
            $order = setOrderDatatable($dataTable['columnName'], $orderable, 'object_contests_acts.object');

            // Results contest act
            $queryRecords = ContestsActsModel::search($searchValue);
            $queryRecords->select('object_contests_acts.*', 'users.name', 'i.full_name_institution');
            $queryRecords->join('users', 'users.id', '=', 'object_contests_acts.owner_id', 'left outer');
            $queryRecords->with('awardees:id,name');
            $queryRecords->with('relative_procedure_awardees:id,name');
            $queryRecords->with('structure:id,structure_name,archived');
//            $queryRecords->with('proceedings');
            $queryRecords->with(['relative_notice' => function ($query) {
                $query->select(['id', 'object', 'cig', 'contraent_choice', 'relative_notice_id', 'anac_year', 'adjudicator_name', 'adjudicator_data']);
                $query->with('relative_lots:id,object,relative_notice_id,asta_base_value,cig');
                $query->with('contraent_choice:id,name');
            }]);
            $queryRecords->with('contraent_choice:id,name');
            $queryRecords->with(['created_by' => function ($query) {
                $query->withoutGlobalScopes([DeletedScope::class]);
                $query->select(['id', 'name', 'deleted']);
            }]);
            $queryRecords->with(['institution' => function ($query) {
                $query->select(['id', 'full_name_institution']);
            }]);
            $queryRecords->with('relative_lots:id,relative_notice_id,object,cig,asta_base_value');
            $queryRecords->with('relative_liquidation:id,relative_procedure_id,amount_liquidated');
            $queryRecords->with(['relative_procedure' => function ($query) {
                $query->select(['relative_procedure_id', 'object_contests_acts.id', 'relative_notice_id', 'cig', 'object_structures_id']);
                $query->with('relative_notice:id,cig');
                $query->with('structure:id,structure_name,archived');
            }]);

            if (!empty($dataTable['searchValue'])) {
                $queryRecords->orWhere('object_contests_acts.id', '=', $dataTable['searchValue']);
            }

            $queryRecords->join('institutions as i', 'object_contests_acts.institution_id', '=', 'i.id', 'left outer');
            $queryRecords->leftJoin('object_structures as structure', function ($join) {
                $join->on('structure.id', '=', 'object_contests_acts.object_structures_id');
            });
            $queryRecords->limit(100);
            $queryRecords->orderBy($order, $dataTable['columnSortOrder']);
            $queryRecords->offset($dataTable['start']);
            $queryRecords->limit($dataTable['rowPerPage']);
            $queryRecords->groupBy('object_contests_acts.id');

            $results = $queryRecords->get();
            $records = !empty($results) ? $results->toArray() : [];

            $response ['draw'] = intval($dataTable['draw']);
            $response ['iTotalRecords'] = ($totalRecords);
            $response ['iTotalDisplayRecords'] = ($totalRecordsWithFilter);
            $response ['aaData'] = [];

            if (!empty($records)) {

                foreach ($records as $record) {

                    // Aggiudicatari
                    if (!empty($record['awardees']) && is_array($record['awardees'])) {

                        $tmpSuppliers = Arr::pluck($record['awardees'], 'name');
                        $awardees = str_replace(',', ',' . nbs(2), implode(',',
                            array_map(
                                function ($number) {
                                    return ('<small class="badge badge-primary mb-1">' . escapeXss($number) . '</small>');
                                }, $tmpSuppliers)));

                    } elseif (!empty($record['relative_procedure_awardees']) && is_array($record['awardees'])) {

                        $tmpSuppliers = Arr::pluck($record['relative_procedure_awardees'], 'name');
                        $awardees = str_replace(',', ',' . nbs(2), implode(',',
                            array_map(
                                function ($number) {
                                    return ('<small class="badge badge-primary mb-1">' . escapeXss($number) . '</small>');
                                }, $tmpSuppliers)));


                    } else {
                        $awardees = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definiti">N.D.</small>';
                    }

                    // Data di pubblicazione
                    $dataField = ($record['typology'] === 'result') ? 'work_start_date' : 'activation_date';
                    $workStartDate = !empty($record[$dataField])
                        ? ('<small class="badge badge-info"> 
                            <i class="far fa-calendar-alt"></i> ' .
                            date('d-m-Y', strtotime($record[$dataField])) .
                            '</small>')
                        : '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definita">N.D.</small>';

                    if (!empty($record['updated_at'])) {
                        $updateAt = '<small class="badge badge-info"> 
                                    <i class="far fa-clock"></i> '
                            . date('d-m-Y H:i:s', strtotime($record['updated_at'])) . '</small>';
                    } else {
                        $updateAt = '<small class="badge badge-danger" data-toggle="tooltip" data-placement="top" data-original-title="Non Definita">N.D.</small>';
                    }

                    // Cig
                    if (in_array($record['typology'], ['result', 'alert'])) {
                        $tmpCig = null;
                        if (!empty($record['relative_notice'])) {
                            $tmpCig = escapeXss($record['relative_notice']['cig']);
                        } else if (!empty($record['cig'])) {
                            $tmpCig = escapeXss($record['cig']);
                        }
                    } elseif ($record['typology'] == 'notice' && !empty($record['relative_lots'])) {
                        $tmpCig = implode(', ', Arr::pluck($record['relative_lots'], 'cig'));
                    } elseif ($record['typology'] == 'liquidation') {
                        $tmpCig = !empty($record['relative_procedure'] && !empty($record['relative_procedure']['relative_notice'])) ? $record['relative_procedure']['relative_notice']['cig'] : $record['relative_procedure']['cig'] ?? '';
                    } else {
                        $tmpCig = !empty($record['cig']) ? escapeXss($record['cig']) : null;
                    }

                    // Controllo se l'utente ha i permessi di modifica dei record o di scrittura(e quindi di modifica dei propri record)
                    $permits = ($this->acl->getCreate() && checkRecordOwner($record['owner_id']));
                    $updatePermits = ($this->acl->getUpdate() && checkRecordOwner($record['owner_id']));

                    $tempTypology = $record['typology'];

                    //Setto i pulsanti delle actions da mostrare in base ai permessi dell'utente
                    $buttonAction = ($this->acl->getUpdate() || $this->acl->getDelete() || $permits) ? ButtonAction::create([
                        'edit' => $this->acl->getUpdate() || $permits,
                        'duplicate' => $this->acl->getCreate(),
                        'versioning' => getAclVersioning(),
                        'delete' => $this->acl->getDelete() || $updatePermits || $permits,
                        'scp' => getAclProfileInfo('scp'),
                    ])
                        ->addEdit('admin/contests-act/edit-' . $tempTypology . '/' . $record['id'], $record['id'])
                        ->addDuplicate('admin/contests-act/duplicate-' . $tempTypology . '/' . $record['id'], $record['id'])
                        ->addDelete('admin/contests-act/delete-' . $tempTypology . '/' . $record['id'], $record['id'])
                        ->render() : '';

                    $icon = null;

                    //Setto i dati da mostrare nelle colonne del datatable
                    $setTempData = [];
                    $checkbox = ($this->acl->getCrud() || checkRecordOwner($record['owner_id'])) ? ButtonAction::checkList('item[]', $record['id']) : '';
                    $setTempData[] = $checkbox;

                    $setTempData[] = $icon . (!empty($record['object'])
                            ? '<a href="' . siteUrl('/page/581/details/' . $record['id'] . '/' . urlTitle($record['object'])) . '" target="_blank">' . escapeXss($record['object']) . '</a>'
                            : 'N.D.');
                    $setTempData[] = !empty($record['type']) ? escapeXss($record['type']) : 'N.D.';
                    $setTempData[] = $tmpCig;
                    $setTempData[] = !empty($record['amount_liquidated'])
                        ? '<small class="badge badge-success">' . escapeXss(S::currency($record['amount_liquidated'], 2, ',', '.')) . ' &euro; </small>'
                        : null;

                    $structureName = '';
                    if (!empty($record['structure'])) {
                        $structureName = escapeXss($record['structure']['structure_name']);
                    } elseif (!empty($record['relative_procedure']) && !empty($record['relative_procedure']['structure']) && $record['typology'] != 'foster') {
                        $structureName = escapeXss($record['relative_procedure']['structure']['structure_name']);
                    }

                    $setTempData[] = $structureName;
                    $setTempData[] = $workStartDate;
                    $setTempData[] = $awardees;
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

                $response ['aaData'] = $data;

            }

            echo json_encode($response);

        }

    }

    /**
     * @description Renderizza il form di creazione di una nuova Delibera
     * @return void
     * @throws Exception
     * @url /admin/contests-act/create-deliberation.html
     * @method GET
     */
    public function createDeliberation(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $data = [];

        //Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $data['is_box'] = uri()->uriString() === 'admin/contests-deliberation/create-box';

        //Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {
            $this->breadcrumb->push('Determina a contrarre o atto equivalente', 'admin/contests-act');
            $this->breadcrumb->push('Nuova', '/');

            //Dati header della sezione
            $data['titleSection'] = 'Bandi Gare e Contratti';
            $data['subTitleSection'] = 'GESTIONE DELLE PUBBLICAZIONI DEI BANDI, GARE E CONTRATTI';
            $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';

            $data['breadcrumbs'] = $this->breadcrumb->show();
        }

        $data['formAction'] = '/admin/contests-act/store-deliberation';
        $data['formSettings'] = [
            'name' => 'form_contests_act',
            'id' => 'form_contests_act',
            'class' => 'form_contests_act',
        ];
        $data['_storageType'] = 'insert';

        // Labels
        $data['labels'] = [];

        //Recupero le sezioni per il "pubblica in"
        $data['publicIn'] = $this->setPublicInData($this->institutionTypeId, 'object_contest_acts_deliberation');

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        render('contests_act/form_store_deliberation', $data, 'admin');
    }

    /**
     * @description Renderizza il form di creazione di un nuovo Bando di gara
     * @return void
     * @throws Exception
     * @url /admin/contests-act/create-notice.html
     * @method GET
     */
    public function createNotice(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $data = [];

        //Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $data['is_box'] = uri()->uriString() === 'admin/contests-act/create-box';

        //Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {
            $this->breadcrumb->push('Bando di gara', 'admin/contests-act');
            $this->breadcrumb->push('Nuovo', '/');
            $data['breadcrumbs'] = $this->breadcrumb->show();

            //Dati header della sezione
            $data['titleSection'] = 'Bandi Gare e Contratti';
            $data['subTitleSection'] = 'GESTIONE DELLE PUBBLICAZIONI DEI BANDI, GARE E CONTRATTI';
            $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';
        }

        $data['notice'] = [];

        $data['formAction'] = '/admin/contests-act/store-notice';
        $data['formSettings'] = [
            'name' => 'form_store_notices',
            'id' => 'form_store_notices',
            'class' => 'form_store_notices',
        ];
        $data['_storageType'] = 'insert';

        $data['provinceShort'] = [null => 'Seleziona'] + config('province_short', null, 'locations');

        // Anno anac
        $years = [null => 'Seleziona un anno'];
        for ((int)$i = date('Y'); $i >= 2012; $i--) {
            $years [$i] = $i;
        }

        // Labels
        $data['labels'] = [];

        $data['anacYears'] = $years;

        //Recupero le sezioni per il "pubblica in"
        $data['publicIn'] = $this->setPublicInData($this->institutionTypeId, 'object_contest_acts_notice');

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        render('contests_act/form_store_notice', $data, 'admin');
    }

    /**
     * @description Renderizza il form di creazione di un nuovo Lotto
     * @return void
     * @throws Exception
     * @url /admin/contests-act/create-lot.html
     * @method GET
     */
    public function createLot(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $this->breadcrumb->push('Lotto', 'admin/contests-act');
        $this->breadcrumb->push('Nuovo', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Bandi Gare e Contratti';
        $data['subTitleSection'] = 'GESTIONE DELLE PUBBLICAZIONI DEI BANDI, GARE E CONTRATTI';
        $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';

        $data['formAction'] = '/admin/contests-act/store-lot';
        $data['formSettings'] = [
            'name' => 'form_store_lotto',
            'id' => 'form_store_lotto',
            'class' => 'form_store_lotto',
        ];
        $data['_storageType'] = 'insert';

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        render('contests_act/form_store_lot', $data, 'admin');
    }

    /**
     * @description Renderizza il form di creazione di un nuovo Esito di gara
     * @return void
     * @throws Exception
     * @url /admin/contests-act/create-result.html
     * @method GET
     */
    public function createResult(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $data = [];

        //Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $data['is_box'] = uri()->uriString() === 'admin/contests-result/create-box';

        //Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {
            $this->breadcrumb->push('Esito di Gara', 'admin/contests-act');
            $this->breadcrumb->push('Nuovo', '/');

            $data['breadcrumbs'] = $this->breadcrumb->show();

            //Dati header della sezione
            $data['titleSection'] = 'Bandi Gare e Contratti';
            $data['subTitleSection'] = 'GESTIONE DELLE PUBBLICAZIONI DEI BANDI, GARE E CONTRATTI';
            $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';
        }

        $data['formAction'] = '/admin/contests-act/store-result';
        $data['formSettings'] = [
            'name' => 'form_store_result',
            'id' => 'form_store_result',
            'class' => 'form_store_result',
        ];
        $data['_storageType'] = 'insert';

        // Labels
        $data['labels'] = [];

        //Recupero le sezioni per il "pubblica in"
        $data['publicIn'] = $this->setPublicInData($this->institutionTypeId, 'object_contest_acts_result');

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        render('contests_act/form_store_result', $data, 'admin');
    }

    /**
     * @description Renderizza il form di creazione di un nuovo Avviso
     * @return void
     * @throws Exception
     * @url /admin/contests-act/create-alert.html
     * @method POST
     */
    public function createAlert(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $data = [];

        //Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $data['is_box'] = uri()->uriString() === 'admin/contests-alert/create-box';

        //Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {
            $this->breadcrumb->push('Avviso', 'admin/contests-act');
            $this->breadcrumb->push('Nuovo', '/');

            //Dati header della sezione
            $data['titleSection'] = 'Bandi Gare e Contratti';
            $data['subTitleSection'] = 'GESTIONE DELLE PUBBLICAZIONI DEI BANDI, GARE E CONTRATTI';
            $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';

            $data['breadcrumbs'] = $this->breadcrumb->show();
        }

        $data['formAction'] = '/admin/contests-act/store-alert';
        $data['formSettings'] = [
            'name' => 'form_store_alert',
            'id' => 'form_store_alert',
            'class' => 'form_store_alert',
        ];
        $data['_storageType'] = 'insert';

        //Recupero le sezioni per il "pubblica in"
        $data['publicIn'] = $this->setPublicInData($this->institutionTypeId, 'object_contest_acts_alert');

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        render('contests_act/form_store_alert', $data, 'admin');
    }

    /**
     * @description Renderizza il form di creazione di un nuovo Affidamento
     * @return void
     * @throws Exception
     * @url /admin/contests-act/create-foster.html
     * @method POST
     */
    public function createFoster(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $data = [];

        //Controllo se il metodo viene chiamato dalla creazione di un altro oggetto
        $data['is_box'] = uri()->uriString() === 'admin/contests-foster/create-box';

        //Se non siamo nella form di creazione di un altro oggetto mostro il breadcrumb e le info dell'header della sezione
        if (!$data['is_box']) {
            $data['breadcrumbs'] = $this->breadcrumb->show();
            $this->breadcrumb->push('Esito/Affidamento', 'admin/contests-act');
            $this->breadcrumb->push('Nuovo', '/');

            //Dati header della sezione
            $data['titleSection'] = 'Bandi Gare e Contratti';
            $data['subTitleSection'] = 'GESTIONE DELLE PUBBLICAZIONI DEI BANDI, GARE E CONTRATTI';
            $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';
        }

        $data['formAction'] = '/admin/contests-act/store-foster';
        $data['formSettings'] = [
            'name' => 'form_store_foster',
            'id' => 'form_store_foster',
            'class' => 'form_store_foster',
        ];
        $data['_storageType'] = 'insert';

        // Anno anac
        $years = [null => 'Seleziona un anno'];
        for ((int)$i = date('Y'); $i >= 2012; $i--) {
            $years [$i] = $i;
        }

        $data['years'] = $years;
        $data['provinceShort'] = [null => 'Seleziona'] + config('province_short', null, 'locations');

        //Recupero le sezioni per il "pubblica in"
        $data['publicIn'] = $this->setPublicInData($this->institutionTypeId, 'object_contest_acts_foster');

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        render('contests_act/form_store_foster', $data, 'admin');
    }

    /**
     * @description Renderizza il form di creazione di una nuova Liquidazione
     * @return void
     * @throws Exception
     * @url /admin/contests-act/create-liquidation.html
     * @method POST
     */
    public function createLiquidation(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $this->breadcrumb->push('Liquidazione', 'admin/contests-act');
        $this->breadcrumb->push('Nuova', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Bandi Gare e Contratti';
        $data['subTitleSection'] = 'GESTIONE DELLE PUBBLICAZIONI DEI BANDI, GARE E CONTRATTI';
        $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';

        $data['formAction'] = '/admin/contests-act/store-liquidation';
        $data['formSettings'] = [
            'name' => 'form_store_liquidation',
            'id' => 'form_store_liquidation',
            'class' => 'form_store_liquidation',
        ];
        $data['_storageType'] = 'insert';

        // Anno anac
        $years = [null => 'Seleziona un anno'];
        for ((int)$i = date('Y'); $i >= 2012; $i--) {
            $years [$i] = $i;
        }
        $data['years'] = $years;

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = (checkAlternativeInstitutionId() !== 0) ? checkAlternativeInstitutionId() : PatOsInstituteId();

        render('contests_act/form_store_liquidation', $data, 'admin');
    }

    /**
     * @description Funzione che effettua lo storage di una nuova Delibera
     *
     * @return void
     * @throws Exception
     * @url /admin/contests-act/store-deliberation.html
     * @method POST
     */
    public function storeDeliberation(): void
    {
        //Setto il metodo della rotta
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new DeliberationValidator();
        $check = $validator->check();

        if ($check['is_success']) {

            //Controllo se il cig inserito è gia presente o meno
            $checkCig = $this->checkCig(array(strip_tags(Input::post('cig', true))));
            if (!empty($checkCig) && empty(Input::post('__ignore_cig', true))) {
                $json->set('cigs', $checkCig);

            } else {
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
                        'type' => 'Determina a contrarre o atto equivalente',
                        'typology' => 'deliberation',
                        'object' => strip_tags(Input::post('object', true)),
                        'cig' => strip_tags(Input::post('cig', true)),
                        'act_date' => !empty(Input::post('act_date')) ? convertDateToDatabase(strip_tags(Input::post('act_date', true))) : null,
                        'activation_date' => !empty(Input::post('activation_date')) ? strip_tags(Input::post('activation_date', true)) : null,
                        'object_structures_id' => setDefaultData(strip_tags(Input::post('object_structures_id', true)), null, ['']),
                        'object_personnel_id' => setDefaultData(strip_tags(Input::post('object_personnel_id', true)), null, ['']),
                        'object_measure_id' => setDefaultData(strip_tags(Input::post('object_measure_id', true)), null, ['', null]),
                        'details' => Input::post('details', true),
                        'sector' => setDefaultData(strip_tags(Input::post('sector', true)), null, ['', null, 0]),
                    ];

                    // Storage nuova Delibera
                    $insert = ContestsActsModel::createWithLogs($arrayValues);

                    // Svuoto le tabelle di relazione e le aggiorno
                    $this->clear(
                        $insert,
                        null,
                        null,
                        !empty(Input::post('procedures')) ? explode(',', strip_tags((string)Input::post('procedures', true))) : null,
                        Input::post('public_in', true),
                    );

                    // Storage allegati associati al personale.
                    $attach->storage('attach_files', 'contest_acts', $insert->id, $insert['object']);

                    // Generazione nuovo token
                    Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
                    $json->set('message', sprintf(__('success_save_operation', null, 'patos'), 'Determina '));
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
     * @description Funzione che effettua lo storage di un nuovo Bando di gara
     *
     * @return void
     * @throws Exception
     * @url /admin/contests-act/store-notice.html
     * @method POST
     */
    public function storeNotice(): void
    {
        //Setto il metodo della rotta
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new ContestActValidator();
        $check = $validator->check();

        if ($check['is_success']) {

            //Controllo se il cig inserito è gia presente o meno
            $checkCig = $this->checkCig(Input::post('cig', true));

            if (!empty($checkCig) && empty(Input::post('__ignore_cig', true))) {
                $json->set('cigs', $checkCig);

            } else {

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

                    // Controllo se il bando è multicig o meno
                    $isMulticig = !empty(Input::post('cig')) && count(Input::post('cig', true)) > 1;
                    $cig = Input::post('cig');
                    $astaBaseValue = $_POST['asta_base_value'];

                    $arrayValues = [
                        'owner_id' => $getIdentity['id'],
                        'institution_id' => checkAlternativeInstitutionId(),
                        'type' => 'Bando di gara',
                        'typology' => 'notice',
                        'anac_year' => setDefaultData(strip_tags(Input::post('anac_year', true)), null, ['']),
                        'contract' => setDefaultData(strip_tags(Input::post('contract', true)), null, ['']),
                        'object' => strip_tags(Input::post('object', true)),
                        'no_amount' => strip_tags(Input::post('no_amount', true)),
                        'contraent_choice' => setDefaultData(strip_tags(Input::post('contraent_choice', true)), null, ['']),
                        'adjudicator_name' => strip_tags(Input::post('adjudicator_name', true)),
                        'adjudicator_data' => strip_tags(Input::post('adjudicator_data', true)),
                        'administration_type' => setDefaultData(strip_tags(Input::post('administration_type', true)), null, ['']),
                        'province_office' => setDefaultData(strip_tags(Input::post('province_office', true)), null, ['']),
                        'municipality_office' => strip_tags(Input::post('municipality_office', true)),
                        'office_address' => strip_tags(Input::post('office_address', true)),
                        'istat_office' => strip_tags(Input::post('istat_office', true)),
                        'nuts_office' => strip_tags(Input::post('nuts_office', true)),
                        'object_structures_id' => setDefaultData(strip_tags(Input::post('object_structures_id', true)), null, ['']),
                        'guue_date' => !empty(Input::post('guue_date')) ? convertDateToDatabase(strip_tags(Input::post('guue_date', true))) : null,
                        'guri_date' => !empty(Input::post('guri_date')) ? convertDateToDatabase(strip_tags(Input::post('guri_date', true))) : null,
                        'act_date' => !empty(Input::post('act_date')) ? convertDateToDatabase(strip_tags(Input::post('act_date', true))) : null,
                        'activation_date' => !empty(Input::post('activation_date')) ? strip_tags(Input::post('activation_date', true)) : null,
                        'expiration_date' => !empty(Input::post('expiration_date')) ? convertDateToDatabase(strip_tags(Input::post('expiration_date', true))) : null,
                        'object_personnel_id' => setDefaultData(strip_tags(Input::post('object_personnel_id', true)), null, ['']),
                        'object_measure_id' => setDefaultData(strip_tags(Input::post('object_measure_id', true)), null, ['', null]),
                        'cpv_code_id' => setDefaultData(strip_tags(Input::post('cpv_code_id', true)), null, ['', 0]),
                        'codice_scp' => strip_tags(Input::post('codice_scp', true)),
                        'url_scp' => strip_tags(Input::post('url_scp', true)),
                        'details' => Input::post('details', true),
                        'is_multicig' => $isMulticig,
                        'cig' => ($isMulticig) ? null : strip_tags(escapeXss($cig[0])),
                        'asta_base_value' => ($isMulticig) ? null : toFloat(strip_tags($astaBaseValue[0])),
                        'sector' => setDefaultData(strip_tags(Input::post('sector', true)), null, ['', null, 0]),
                    ];

                    // Storage nuovo Bando di gara
                    $insert = ContestsActsModel::createWithLogs($arrayValues);

                    // Se sono presenti creo i lotti associati al bando
                    if ($isMulticig && (count(Input::post('cig', true)) > 1)) {
                        $i = 0;
                        foreach (Input::post('cig', true) as $cig) {
                            // Inserisco i lotti associati al bando di gara
                            ContestsActsModel::createWithLogs([
                                'relative_notice_id' => $insert->id,
                                'type' => 'Lotto',
                                'typology' => 'lot',
                                'object' => $insert->object,
                                'cig' => strip_tags(escapeXss($cig)),
                                'asta_base_value' => toFloat(strip_tags($_POST['asta_base_value'][$i++])),
                                'activation_date' => $insert->activation_date,
                                'institution_id' => $insert->institution_id,
                                'owner_id' => $getIdentity['id'],
                                'created_at' => date('Y-m-d H:i:s'),
                                'updated_at' => date('Y-m-d H:i:s')
                            ]);
                        }
                    }

                    // Svuoto le tabelle di relazione e le aggiorno
                    $this->clear(
                        $insert,
                        null,
                        null,
                        !empty(Input::post('procedures')) ? explode(',', strip_tags((string)Input::post('procedures', true))) : null,
                        Input::post('public_in', true),
                        Input::post('requirements', true)
                    );

                    // Storage allegati associati al personale.
                    $attach->storage('attach_files', 'contest_acts', $insert->id, $insert['object']);

                    if (!referenceOriginForRegenerateToken(3, 'create-box')) {
                        // Generazione nuovo token
                        Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
                    }

                    $json->set('message', sprintf(__('success_save_operation', null, 'patos'), 'Bando di gara '));
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
     * @description Funzione che effettua lo storage di un nuovo Lotto
     *
     * @return void
     * @throws Exception
     * @url /admin/contests-act/store-lot.html
     * @method POST
     */
    public function storeLot(): void
    {
        //Setto il metodo della rotta
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new LotValidator();
        $check = $validator->check();

        if ($check['is_success']) {

            //Controllo se il cig inserito è gia presente o meno
            $checkCig = $this->checkCig(array(strip_tags(Input::post('cig', true))));
            if (!empty($checkCig) && empty(Input::post('__ignore_cig', true))) {
                $json->set('cigs', $checkCig);

            } else {
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

                    $relativeNotice = ContestsActsModel::select(['id', 'activation_date'])
                        ->where('id', Input::post('relative_notice_id', true))
                        ->where('typology', 'notice')
                        ->first()
                        ->toArray();

                    $arrayValues = [
                        'owner_id' => $getIdentity['id'],
                        'institution_id' => checkAlternativeInstitutionId(),
                        'type' => 'Lotto',
                        'typology' => 'lot',
                        'relative_notice_id' => setDefaultData(strip_tags(Input::post('relative_notice_id', true)), null, ['']),
                        'object' => strip_tags(Input::post('object', true)),
                        'cig' => strip_tags(Input::post('cig', true)),
                        'asta_base_value' => !empty(Input::post('asta_base_value')) ? toFloat(strip_tags(Input::post('asta_base_value', true))) : null,
                        'activation_date' => $relativeNotice['activation_date']
                    ];

                    // Storage nuovo Lotto
                    $insert = ContestsActsModel::createWithLogs($arrayValues);

                    // Storage allegati associati al personale.
                    $attach->storage('attach_files', 'contest_acts', $insert->id, $insert->relative_notice['object']);

                    // Generazione nuovo token
                    Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
                    $json->set('message', sprintf(__('success_save_operation', null, 'patos'), 'Lotto '));
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
     * @description Funzione che effettua lo storage di un nuovo Esito di gara
     *
     * @return void
     * @throws Exception
     * @url /admin/contests-act/store-result.html
     * @method POST
     */
    public function storeResult(): void
    {

        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new ResultValidator();
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
                    'type' => 'Esito di gara',
                    'typology' => 'result',
                    'relative_notice_id' => setDefaultData(strip_tags(Input::post('notice_id', true)), null, ['']),
                    'object_measure_id' => setDefaultData(strip_tags(Input::post('object_measure_id', true)), null, ['', null]),
                    'object' => strip_tags(Input::post('object', true)),
                    'award_amount_value' => !empty(Input::post('award_amount_value')) ? toFloat(strip_tags(Input::post('award_amount_value', true))) : null,
                    'guue_date' => !empty(Input::post('guue_date')) ? convertDateToDatabase(strip_tags(Input::post('guue_date', true))) : null,
                    'guri_date' => !empty(Input::post('guri_date')) ? convertDateToDatabase(strip_tags(Input::post('guri_date', true))) : null,
                    'act_date' => !empty(Input::post('act_date')) ? convertDateToDatabase(strip_tags(Input::post('act_date', true))) : null,
                    'activation_date' => !empty(Input::post('activation_date')) ? strip_tags(Input::post('activation_date', true)) : null,
                    'contracting_stations_publication_date' => !empty(Input::post('contracting_stations_publication_date')) ? convertDateToDatabase(strip_tags(Input::post('contracting_stations_publication_date', true))) : null,
                    'work_start_date' => !empty(Input::post('work_start_date')) ? convertDateToDatabase(strip_tags(Input::post('work_start_date', true))) : null,
                    'work_end_date' => !empty(Input::post('work_end_date')) ? convertDateToDatabase(strip_tags(Input::post('work_end_date', true))) : null,
                    'typology_result' => setDefaultData(strip_tags(Input::post('typology_result', true)), null, ['']),
                    'details' => Input::post('details', true),
                ];

                // Storage nuovo Esito di gara
                $insert = ContestsActsModel::createWithLogs($arrayValues);

                // Svuoto le tabelle di relazione e le aggiorno
                $this->clear(
                    $insert,
                    !empty(Input::post('participants')) ? explode(',', strip_tags((string)Input::post('participants', true))) : null,
                    !empty(Input::post('awardees')) ? explode(',', strip_tags((string)Input::post('awardees', true))) : null,
                    !empty(Input::post('procedures')) ? explode(',', strip_tags((string)Input::post('procedures', true))) : null,
                    Input::post('public_in', true),
                );

                // Storage allegati associati al personale.
                $attach->storage('attach_files', 'contest_acts', $insert->id, $insert['object']);

                // Generazione nuovo token
                Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
                $json->set('message', sprintf(__('success_save_operation', null, 'patos'), 'Esito di gara '));
            }

        } else {

            $code = $json->bad();
            $json->error('error', $check['errors']);

        }

        $json->setStatusCode($code);
        $json->response();

    }

    /**
     * @description Funzione che effettua lo storage di un nuovo Avviso
     *
     * @return void
     * @throws Exception
     * @url /admin/contests-act/store-alert.html
     * @method POST
     */
    public function storeAlert(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new AlertValidator();
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

                if (!empty(Input::post('cig', true))) {
                    $cig = strip_tags(Input::post('cig', true));
                }

                $arrayValues = [
                    'owner_id' => $getIdentity['id'],
                    'institution_id' => checkAlternativeInstitutionId(),
                    'type' => 'Avviso',
                    'typology' => 'alert',
                    'relative_notice_id' => setDefaultData(strip_tags(Input::post('notice_id', true)), null, ['']),
                    'object_structures_id' => setDefaultData(strip_tags(Input::post('object_structures_id', true)), null, ['']),
                    'object_measure_id' => setDefaultData(strip_tags(Input::post('object_measure_id', true)), null, ['', null]),
                    'object' => strip_tags(Input::post('object', true)),
                    'act_date' => !empty(Input::post('act_date')) ? convertDateToDatabase(strip_tags(Input::post('act_date', true))) : null,
                    'activation_date' => !empty(Input::post('activation_date')) ? strip_tags(Input::post('activation_date', true)) : null,
                    'expiration_date' => !empty(Input::post('expiration_date')) ? convertDateToDatabase(strip_tags(Input::post('expiration_date', true))) : null,
                    'object_personnel_id' => setDefaultData(strip_tags(Input::post('object_personnel_id', true)), null, ['', null, 0]),
                    'details' => Input::post('details', true),
                    'cig' => (!empty($cig)) ? $cig : null,
                    'adjudicator_data' => (!empty(Input::post('adjudicator_data', true))) ? strip_tags(Input::post('adjudicator_data', true)) : null,
                    'sector' => setDefaultData(strip_tags(Input::post('sector', true)), null, ['', null, 0]),
                ];

                // Storage nuovo Avviso
                $insert = ContestsActsModel::createWithLogs($arrayValues);

                // Svuoto le tabelle di relazione e le aggiorno
                $this->clear(
                    $insert,
                    null,
                    null,
                    !empty(Input::post('procedures')) ? explode(',', strip_tags((string)Input::post('procedures', true))) : null,
                    Input::post('public_in', true)
                );

                // Storage allegati associati al personale.
                $attach->storage('attach_files', 'contest_acts', $insert->id, $insert['object']);

                // Generazione nuovo token
                Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
                $json->set('message', sprintf(__('success_save_operation', null, 'patos'), 'Avviso '));
            }

        } else {

            $code = $json->bad();
            $json->error('error', $check['errors']);

        }

        $json->setStatusCode($code);
        $json->response();

    }

    /**
     * @description Funzione che effettua lo storage di un nuovo Esito/Affidamento
     *
     * @return void
     * @throws Exception
     * @url /admin/contests-act/store-foster.html
     * @method POST
     */
    public function storeFoster(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new FosterValidator();
        $check = $validator->check();

        if ($check['is_success']) {

            //Controllo se il cig inserito è gia presente o meno
            $checkCig = $this->checkCig(array(strip_tags(Input::post('cig', true))));
            if (!empty($checkCig) && empty(Input::post('__ignore_cig', true))) {
                $json->set('cigs', $checkCig);

            } else {
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
                        'type' => 'Esito/Affidamento',
                        'typology' => 'foster',
                        'anac_year' => setDefaultData(strip_tags(Input::post('anac_year', true)), null, ['']),
                        'object' => strip_tags(Input::post('object', true)),
                        'relative_procedure_id' => setDefaultData(strip_tags(Input::post('relative_procedure_id', true)), null, ['', null]),
                        'object_measure_id' => setDefaultData(strip_tags(Input::post('object_measure_id', true)), null, ['', null]),
                        'cig' => strip_tags(Input::post('cig', true)),
                        'no_amount' => strip_tags(Input::post('no_amount', true)),
                        'asta_base_value' => !empty(Input::post('asta_base_value')) ? toFloat(strip_tags(Input::post('asta_base_value', true))) : null,
                        'award_amount_value' => !empty(Input::post('award_amount_value')) ? toFloat(strip_tags(Input::post('award_amount_value', true))) : null,
                        'contraent_choice' => setDefaultData(strip_tags(Input::post('contraent_choice', true)), null, ['']),
                        'contract' => !empty(Input::post('contract')) ? (int)Input::post('contract', true) : null, //Attenzione
                        'adjudicator_name' => strip_tags(Input::post('adjudicator_name', true)),
                        'adjudicator_data' => strip_tags(Input::post('adjudicator_data', true)),
                        'administration_type' => setDefaultData(strip_tags(Input::post('administration_type', true))),
                        'province_office' => setDefaultData(strip_tags(Input::post('province_office', true)), null, ['']),
                        'municipality_office' => strip_tags(Input::post('municipality_office', true)),
                        'office_address' => strip_tags(Input::post('office_address', true)),
                        'istat_office' => strip_tags(Input::post('istat_office', true)),
                        'nuts_office' => strip_tags(Input::post('nuts_office', true)),
                        'object_structures_id' => setDefaultData(strip_tags(Input::post('object_structures_id', true)), null, ['']),
                        'act_date' => !empty(Input::post('act_date')) ? convertDateToDatabase(strip_tags(Input::post('act_date', true))) : null,
                        'activation_date' => !empty(Input::post('activation_date')) ? strip_tags(Input::post('activation_date', true)) : null,
                        'publication_date_type' => !empty(Input::post('publication_date_type')) ? strip_tags(Input::post('publication_date_type', true)) : null,
                        'work_start_date' => !empty(Input::post('work_start_date')) ? convertDateToDatabase(strip_tags(Input::post('work_start_date', true))) : null,
                        'work_end_date' => !empty(Input::post('work_end_date')) ? convertDateToDatabase(strip_tags(Input::post('work_end_date', true))) : null,
                        'guue_date' => !empty(Input::post('guue_date')) ? convertDateToDatabase(strip_tags(Input::post('guue_date', true))) : null,
                        'guri_date' => !empty(Input::post('guri_date')) ? convertDateToDatabase(strip_tags(Input::post('guri_date', true))) : null,
                        'contracting_stations_publication_date' => !empty(Input::post('contracting_stations_publication_date')) ? convertDateToDatabase(strip_tags(Input::post('contracting_stations_publication_date', true))) : null,
                        'typology_result' => setDefaultData(strip_tags(Input::post('typology_result', true)), null, ['']),
                        'object_personnel_id' => setDefaultData(strip_tags(Input::post('object_personnel_id', true)), null, ['']),
                        'cpv_code_id' => setDefaultData(strip_tags(Input::post('cpv_code_id', true)), null, ['', 0]),
                        'codice_scp' => strip_tags(Input::post('codice_scp', true)),
                        'url_scp' => strip_tags(Input::post('url_scp', true)),
                        'details' => Input::post('details', true),
                        'decree_163' => setDefaultData(strip_tags(Input::post('decree_163', true)), 0, ['', null]),
                        'sector' => setDefaultData(strip_tags(Input::post('sector', true)), null, ['', null, 0]),
                    ];

                    // Storage nuovo Esito/Affidamento
                    $insert = ContestsActsModel::createWithLogs($arrayValues);

                    // Svuoto le tabelle di relazione e le aggiorno
                    $this->clear(
                        $insert,
                        !empty(Input::post('participants')) ? explode(',', strip_tags((string)Input::post('participants', true))) : null,
                        !empty(Input::post('awardees')) ? explode(',', strip_tags((string)Input::post('awardees', true))) : null,
                        null,
                        Input::post('public_in', true),
                        Input::post('requirements', true),
                    );

                    // Storage allegati associati al personale.
                    $attach->storage('attach_files', 'contest_acts', $insert->id, $insert['object']);


                    // Generazione nuovo token
                    Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
                    $json->set('message', sprintf(__('success_save_operation', null, 'patos'), 'Esito/Affidamento '));
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
     * @description Funzione che effettua lo storage di una nuova Liquidazione
     *
     * @return void
     * @throws Exception
     * @url /admin/contests-act/store-liquidation.html
     * @method POST
     */
    public function storeLiquidation(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('create');

        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new NoticeLiquidationValidator();
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
                    'type' => 'Liquidazione',
                    'typology' => 'liquidation',
                    'object' => strip_tags(Input::post('object', true)),
                    'relative_procedure_id' => setDefaultData(strip_tags(Input::post('relative_procedure_id', true)), null, ['']),
                    'amount_liquidated' => !empty(Input::post('amount_liquidated')) ? toFloat(strip_tags(Input::post('amount_liquidated', true))) : null,
                    'anac_year' => setDefaultData(strip_tags(Input::post('anac_year', true)), null, ['']),
                    'activation_date' => !empty(Input::post('activation_date')) ? convertDateToDatabase(strip_tags(Input::post('activation_date', true))) : null,
                    'details' => Input::post('details', true)
                ];

                // Storage nuova Liquidazione
                $insert = ContestsActsModel::createWithLogs($arrayValues);

                // Storage allegati associati al personale.
                $attach->storage('attach_files', 'contest_acts', $insert->id, $insert['object']);

                // Generazione nuovo token
                Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
                $json->set('message', sprintf(__('success_save_operation', null, 'patos'), 'Liquidazione '));
            }

        } else {

            $code = $json->bad();
            $json->error('error', $check['errors']);

        }

        $json->setStatusCode($code);
        $json->response();

    }

    /**
     * @description Renderizza il form di modifica/duplicazione di una Delibera
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/contests-act/edit-deliberation/:id.html
     * @method GET
     */
    public function editDeliberation(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['update', 'create']);

        //Validatore che verifica se l'elemento da modificare esiste
        $validator = new DeliberationValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/contests-act', sessionSetNotify($validate['errors'], 'danger'));
            exit();

        }

        // Allegati
        $attach = new AttachmentArchive();

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $deliberation = Registry::get('deliberation');
        $deliberation = !empty($deliberation) ? $deliberation->toArray() : [];

        $this->breadcrumb->push('Determina a contrarre o atto equivalente', 'admin/contests-act');
        $this->breadcrumb->push('Modifica', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Bandi Gare e Contratti';
        $data['subTitleSection'] = 'GESTIONE DELLE PUBBLICAZIONI DEI BANDI, GARE E CONTRATTI';
        $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';

        // Vedo in sessione se sono arrivato qui da un operazione di ripristino dal versioning
        $session = new Session();
        $restore = $session->getFlash('restore');
        $data['restore'] = $restore;

        // Controllo se si sta duplicando un tasso di assenza
        $isDuplicate = uri()->segment(3, 0) === 'duplicate-deliberation';

        // Setto il tipo di storage
        $data['_storageType'] = $isDuplicate ? 'duplicate' : 'update';

        //In base al tipo di storage setto l'action del form
        $data['formAction'] = ($isDuplicate) ? '/admin/contests-act/store-deliberation' : '/admin/contests-act/update-deliberation';
        $data['formSettings'] = [
            'name' => 'form_deliberation',
            'id' => 'form_deliberation',
            'class' => 'form_deliberation',
        ];

        $actDate = convertDateToForm($deliberation['act_date']);
        $activationDate = convertDateToForm($deliberation['activation_date']);

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'contest_acts',
            $deliberation['id'],
            [
                'id',
                'cat_id',
                'archive_name',
                'archive_id',
                'client_name',
                'file_name',
                'file_type',
                'file_ext',
                'file_size',
                'label',
                'indexable',
                'active',
                'created_at',
                'updated_at'
            ]);

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = $deliberation['institution_id'];

        //Recupero le sezioni per il "pubblica in"
        $data['publicIn'] = $this->setPublicInData($this->institutionTypeId, 'object_contest_acts_deliberation');
        $data['publicInIDs'] = Arr::pluck($deliberation['public_in'], 'public_in_id');

        $data['procedureIds'] = Arr::pluck($deliberation['proceedings'], 'id');
        $deliberation['act_date'] = $actDate['date'];
        $deliberation['activation_date'] = $activationDate['date'].' '.$activationDate['hours'];

        $data['deliberation'] = $deliberation;

        $data['seo'] = $deliberation['p_s_d_r'] ?? null;

        render('contests_act/form_store_deliberation', $data, 'admin');
    }

    /**
     * @description Renderizza il form di modifica/duplicazione di un Bando di gara
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/contests-act/edit-notice/:id.html
     * @method GET
     */
    public function editNotice(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['update', 'create']);

        //Validatore che verifica se l'elemento da modificare esiste
        $validator = new ContestActValidator();

        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/contests-act', sessionSetNotify($validate['errors'], 'danger'));
            exit();

        }

        // Allegati
        $attach = new AttachmentArchive();

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $notice = Registry::get('notice');
        $notice = !empty($notice) ? $notice->toArray() : [];

        // Somma delle somme liquidate per il bando di gara
        $astaValueSum = collect(Arr::pluck($notice['relative_lots'], 'asta_base_value'))
            ->map(function ($items) {
                return (float)S::currency($items, 2, null, null, false);
            })->sum();

        $astaValueSum = ($astaValueSum > 0) ? $astaValueSum : $notice['asta_base_value'];
        $astaValueSum = S::currency((string)$astaValueSum, 2, ',', '.');

        $this->breadcrumb->push('Bando di gara', 'admin/contests-act');
        $this->breadcrumb->push('Modifica', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Bandi Gare e Contratti';
        $data['subTitleSection'] = 'GESTIONE DELLE PUBBLICAZIONI DEI BANDI, GARE E CONTRATTI';
        $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';

        // Vedo in sessione se sono arrivato qui da un operazione di ripristino dal versioning
        $session = new Session();
        $restore = $session->getFlash('restore');
        $data['restore'] = $restore;

        // Controllo se si sta duplicando un tasso di assenza
        $isDuplicate = uri()->segment(3, 0) === 'duplicate-notice';

        // Setto il tipo di storage
        $data['_storageType'] = $isDuplicate ? 'duplicate' : 'update';

        //In base al tipo di storage setto l'action del form
        $data['formAction'] = ($isDuplicate) ? '/admin/contests-act/store-notice' : '/admin/contests-act/update-notice';
        $data['formSettings'] = [
            'name' => 'form_notice',
            'id' => 'form_notice',
            'class' => 'form_notice',
        ];

        $actDate = convertDateToForm($notice['act_date']);
        $guueDate = convertDateToForm($notice['guue_date']);
        $guriDate = convertDateToForm($notice['guri_date']);
        $activationDate = convertDateToForm($notice['activation_date']);
        $expirationDate = convertDateToForm($notice['expiration_date']);

        $notice['activation_date'] = $activationDate['date'].' '.$activationDate['hours'];
        $notice['expiration_date'] = $expirationDate['date'];

        $data['notice'] = $notice;

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'contest_acts',
            $notice['id'],
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
            ]);

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = $notice['institution_id'];

        $data['provinceShort'] = [null => 'Seleziona'] + config('province_short', null, 'locations');

        // Anno anac
        $years = [null => 'Seleziona un anno'];
        for ((int)$i = date('Y'); $i >= 2012; $i--) {
            $years [$i] = $i;
        }

        //Recupero le sezioni per il "pubblica in"
        $data['publicIn'] = $this->setPublicInData($this->institutionTypeId, 'object_contest_acts_notice');
        $data['publicInIDs'] = Arr::pluck($notice['public_in'], 'public_in_id');

        $data['anacYears'] = $years;
        $data['requirementIds'] = Arr::pluck($notice['requirements'], 'id');
        $data['procedureIds'] = Arr::pluck($notice['proceedings'], 'id');
        $data['act_date'] = $actDate['date'];
        $data['guue_date'] = $guueDate['date'];
        $data['guri_date'] = $guriDate['date'];
        $data['astaValueSum'] = $astaValueSum;
        $data['scp'] = $notice['scp'] ?? null;
        $data['seo'] = $notice['p_s_d_r'] ?? null;

        render('contests_act/form_store_notice', $data, 'admin');

    }

    /**
     * @description Renderizza il form di modifica/duplicazione di un Lotto
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/contests-act/edit-lot/:id.html
     * @method GET
     */
    public function editLot(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['update', 'create']);

        //Validatore che verifica se l'elemento da modificare esiste
        $validator = new LotValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/contests-act', sessionSetNotify($validate['errors'], 'danger'));
            exit();

        }

        // Allegati
        $attach = new AttachmentArchive();

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $lot = Registry::get('lot');
        $lot = !empty($lot) ? $lot->toArray() : [];

        $this->breadcrumb->push('Lotto', 'admin/contests-act');
        $this->breadcrumb->push('Modifica', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Bandi Gare e Contratti';
        $data['subTitleSection'] = 'GESTIONE DELLE PUBBLICAZIONI DEI BANDI, GARE E CONTRATTI';
        $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';

        // Vedo in sessione se sono arrivato qui da un operazione di ripristino dal versioning
        $session = new Session();
        $restore = $session->getFlash('restore');
        $data['restore'] = $restore;

        // Controllo se si sta duplicando un tasso di assenza
        $isDuplicate = uri()->segment(3, 0) === 'duplicate-lot';

        // Setto il tipo di storage
        $data['_storageType'] = $isDuplicate ? 'duplicate' : 'update';

        //In base al tipo di storage setto l'action del form
        $data['formAction'] = ($isDuplicate) ? '/admin/contests-act/store-lot' : '/admin/contests-act/update-lot';
        $data['formSettings'] = [
            'name' => 'form_deliberation',
            'id' => 'form_deliberation',
            'class' => 'form_deliberation',
        ];

        $data['lot'] = $lot;

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'contest_acts',
            $lot['id'],
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
            ]);

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = $lot['institution_id'];
        $data['seo'] = $lot['p_s_d_r'] ?? null;

        render('contests_act/form_store_lot', $data, 'admin');
    }

    /**
     * @description Renderizza il form di modifica/duplicazione di Esito di gara
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/contests-act/edit-result/:id.html
     * @method GET
     */
    public function editResult(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['update', 'create']);

        //Validatore che verifica se l'elemento da modificare esiste
        $validator = new ResultValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/contests-act', sessionSetNotify($validate['errors'], 'danger'));
            exit();

        }

        // Allegati
        $attach = new AttachmentArchive();

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $result = Registry::get('result');
        $result = !empty($result) ? $result->toArray() : [];

        $this->breadcrumb->push('Esito di gara', 'admin/contests-act');
        $this->breadcrumb->push('Modifica', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Bandi Gare e Contratti';
        $data['subTitleSection'] = 'GESTIONE DELLE PUBBLICAZIONI DEI BANDI, GARE E CONTRATTI';
        $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';

        // Vedo in sessione se sono arrivato qui da un operazione di ripristino dal versioning
        $session = new Session();
        $restore = $session->getFlash('restore');
        $data['restore'] = $restore;

        // Controllo se si sta duplicando un tasso di assenza
        $isDuplicate = uri()->segment(3, 0) === 'duplicate-result';

        // Setto il tipo di storage
        $data['_storageType'] = $isDuplicate ? 'duplicate' : 'update';

        //In base al tipo di storage setto l'action del form
        $data['formAction'] = ($isDuplicate) ? '/admin/contests-act/store-result' : '/admin/contests-act/update-result';
        $data['formSettings'] = [
            'name' => 'form_result',
            'id' => 'form_result',
            'class' => 'form_result',
        ];

        $actDate = convertDateToForm($result['act_date']);
        $guueDate = convertDateToForm($result['guue_date']);
        $guriDate = convertDateToForm($result['guri_date']);
        $activationDate = convertDateToForm($result['activation_date']);
        $workStartDate = convertDateToForm($result['work_start_date']);
        $workEndDate = convertDateToForm($result['work_end_date']);
        $contStatPublicationDate = convertDateToForm($result['contracting_stations_publication_date']);

        $data['result'] = $result;

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'contest_acts',
            $result['id'],
            [
                'id',
                'cat_id',
                'archive_name',
                'archive_id',
                'client_name',
                'file_name',
                'file_type',
                'file_ext',
                'file_size',
                'label',
                'indexable',
                'active',
                'created_at',
                'updated_at'
            ]);

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = $result['institution_id'];

        //Recupero le sezioni per il "pubblica in"
        $data['publicIn'] = $this->setPublicInData($this->institutionTypeId, 'object_contest_acts_result');
        $data['publicInIDs'] = Arr::pluck($result['public_in'], 'public_in_id');

        $data['procedureIds'] = Arr::pluck($result['proceedings'], 'id');
        $data['participantIds'] = Arr::pluck($result['participants'], 'id');
        $data['awardeeIds'] = Arr::pluck($result['awardees'], 'id');

        $data['act_date'] = $actDate['date'];
        $data['guue_date'] = $guueDate['date'];
        $data['guri_date'] = $guriDate['date'];
        $data['activation_date'] = $activationDate['date'].' '.$activationDate['hours'];;
        $data['work_start_date'] = $workStartDate['date'];
        $data['work_end_date'] = $workEndDate['date'];
        $data['contracting_stations_publication_date'] = $contStatPublicationDate['date'];
        $data['scp'] = $result['scp'] ?? null;
        $data['seo'] = $result['p_s_d_r'] ?? null;

        render('contests_act/form_store_result', $data, 'admin');
    }

    /**
     * @description Renderizza il form di modifica/duplicazione di un Avviso
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/contests-act/edit-alert/:id.html
     * @method GET
     */
    public function editAlert(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['update', 'create']);

        //Validatore che verifica se l'elemento da modificare esiste
        $validator = new AlertValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/contests-act', sessionSetNotify($validate['errors'], 'danger'));
            exit();

        }

        // Allegati
        $attach = new AttachmentArchive();

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $alert = Registry::get('alert');
        $alert = !empty($alert) ? $alert->toArray() : [];

        $this->breadcrumb->push('Avviso', 'admin/contests-act');
        $this->breadcrumb->push('Modifica', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Bandi Gare e Contratti';
        $data['subTitleSection'] = 'GESTIONE DELLE PUBBLICAZIONI DEI BANDI, GARE E CONTRATTI';
        $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';

        // Vedo in sessione se sono arrivato qui da un operazione di ripristino dal versioning
        $session = new Session();
        $restore = $session->getFlash('restore');
        $data['restore'] = $restore;

        // Controllo se si sta duplicando un tasso di assenza
        $isDuplicate = uri()->segment(3, 0) === 'duplicate-alert';

        // Setto il tipo di storage
        $data['_storageType'] = $isDuplicate ? 'duplicate' : 'update';

        //In base al tipo di storage setto l'action del form
        $data['formAction'] = ($isDuplicate) ? '/admin/contests-act/store-alert' : '/admin/contests-act/update-alert';
        $data['formSettings'] = [
            'name' => 'form_alert',
            'id' => 'form_alert',
            'class' => 'form_alert',
        ];

        $actDate = convertDateToForm($alert['act_date']);
        $activationDate = convertDateToForm($alert['activation_date']);
        $expirationDate = convertDateToForm($alert['expiration_date']);

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'contest_acts',
            $alert['id'],
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
            ]);

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = $alert['institution_id'];

        //Recupero le sezioni per il "pubblica in"
        $data['publicIn'] = $this->setPublicInData($this->institutionTypeId, 'object_contest_acts_alert');
        $data['publicInIDs'] = Arr::pluck($alert['public_in'], 'public_in_id');

        $data['procedureIds'] = Arr::pluck($alert['proceedings'], 'id');
        $alert['act_date'] = $actDate['date'];
        $alert['activation_date'] = $activationDate['date'].' '.$activationDate['hours'];;
        $alert['expiration_date'] = $expirationDate['date'];

        $data['alert'] = $alert;

        $data['scp'] = $alert['scp'] ?? null;
        $data['seo'] = $alert['p_s_d_r'] ?? null;

        render('contests_act/form_store_alert', $data, 'admin');

    }

    /**
     * @description Renderizza il form di modifica/duplicazione di un Affidamento
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/contests-act/edit-foster/:id.html
     * @method GET
     */
    public function editFoster(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['update', 'create']);

        //Validatore che verifica se l'elemento da modificare esiste
        $validator = new FosterValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/contests-act', sessionSetNotify($validate['errors'], 'danger'));
            exit();

        }

        // Allegati
        $attach = new AttachmentArchive();

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $foster = Registry::get('foster');
        $foster = !empty($foster) ? $foster->toArray() : [];

        $this->breadcrumb->push('Esito/Affidamento', 'admin/contests-act');
        $this->breadcrumb->push('Modifica', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Bandi Gare e Contratti';
        $data['subTitleSection'] = 'GESTIONE DELLE PUBBLICAZIONI DEI BANDI, GARE E CONTRATTI';
        $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';

        // Vedo in sessione se sono arrivato qui da un operazione di ripristino dal versioning
        $session = new Session();
        $restore = $session->getFlash('restore');
        $data['restore'] = $restore;

        // Controllo se si sta duplicando un tasso di assenza
        $isDuplicate = uri()->segment(3, 0) === 'duplicate-foster';

        // Setto il tipo di storage
        $data['_storageType'] = $isDuplicate ? 'duplicate' : 'update';

        //In base al tipo di storage setto l'action del form
        $data['formAction'] = ($isDuplicate) ? '/admin/contests-act/store-foster' : '/admin/contests-act/update-foster';
        $data['formSettings'] = [
            'name' => 'form_foster',
            'id' => 'form_foster',
            'class' => 'form_foster',
        ];

        $actDate = convertDateToForm($foster['act_date']);
        $guueDate = convertDateToForm($foster['guue_date']);
        $guriDate = convertDateToForm($foster['guri_date']);
        $activationDate = convertDateToForm($foster['activation_date']);
        $workStartDate = convertDateToForm($foster['work_start_date']);
        $workEndDate = convertDateToForm($foster['work_end_date']);
        $contractingStationsDate = convertDateToForm($foster['contracting_stations_publication_date']);

        $data['foster'] = $foster;

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'contest_acts',
            $foster['id'],
            [
                'id',
                'cat_id',
                'archive_name',
                'archive_id',
                'client_name',
                'file_name',
                'file_type',
                'file_ext',
                'file_size',
                'label',
                'indexable',
                'active',
                'created_at',
                'updated_at'
            ]);

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = $foster['institution_id'];

        //Recupero le sezioni per il "pubblica in"
        $data['publicIn'] = $this->setPublicInData($this->institutionTypeId, 'object_contest_acts_foster');
        $data['publicInIDs'] = Arr::pluck($foster['public_in'], 'public_in_id');

        $data['years'] = [null => ''];

        $startYear = 2013;
        $thisYear = date("Y");
        for ($i = $startYear; $i <= $thisYear; $i++) {
            $data['years'] += [$i => '' . $i];
        }

        $data['provinceShort'] = [null => 'Seleziona'] + config('province_short', null, 'locations');

        $data['participantIds'] = Arr::pluck($foster['participants'], 'id');
        $data['awardeeIds'] = Arr::pluck($foster['awardees'], 'id');
        $data['requirementIds'] = Arr::pluck($foster['requirements'], 'id');

        $foster['act_date'] = $actDate['date'];
        $data['guue_date'] = $guueDate['date'];
        $data['guri_date'] = $guriDate['date'];
        $data['contracting_stations_publication_date'] = $contractingStationsDate['date'];
        $foster['activation_date'] = $activationDate['date'].' '.$activationDate['hours'];;
        $data['work_start_date'] = $workStartDate['date'];
        $data['work_end_date'] = $workEndDate['date'];
        $data['scp'] = $foster['scp'] ?? null;
        $data['seo'] = $foster['p_s_d_r'] ?? null;

        render('contests_act/form_store_foster', $data, 'admin');
    }

    /**
     * @description Renderizza il form di modifica/duplicazione di una Liquidazione
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/contests-act/edit-liquidation/:id.html
     * @method GET
     */
    public function editLiquidation(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['update', 'create']);

        //Validatore che verifica se l'elemento da modificare esiste
        $validator = new NoticeLiquidationValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/contests-act', sessionSetNotify($validate['errors'], 'danger'));
            exit();

        }

        // Allegati
        $attach = new AttachmentArchive();

        // Se la validazione va a buon fine recupero l'elemento da modificare dal registro
        $liquidation = Registry::get('notice_liquidation');
        $liquidation = !empty($liquidation) ? $liquidation->toArray() : [];

        $this->breadcrumb->push('Liquidazione', 'admin/contests-act');
        $this->breadcrumb->push('Modifica', '/');

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();

        //Dati header della sezione
        $data['titleSection'] = 'Bandi Gare e Contratti';
        $data['subTitleSection'] = 'GESTIONE DELLE PUBBLICAZIONI DEI BANDI, GARE E CONTRATTI';
        $data['sectionIcon'] = '<i class="fas fa-gavel fa-3x"></i>';

        // Controllo se si sta duplicando un tasso di assenza
        $isDuplicate = uri()->segment(3, 0) === 'duplicate-liquidation';

        // Setto il tipo di storage
        $data['_storageType'] = $isDuplicate ? 'duplicate' : 'update';

        //In base al tipo di storage setto l'action del form
        $data['formAction'] = ($isDuplicate) ? '/admin/contests-act/store-liquidation' : '/admin/contests-act/update-liquidation';
        $data['formSettings'] = [
            'name' => 'form_store_liquidation',
            'id' => 'form_store_liquidation',
            'class' => 'form_store_liquidation',
        ];

        // Vedo in sessione se sono arrivato qui da un operazione di ripristino dal versioning
        $session = new Session();
        $restore = $session->getFlash('restore');
        $data['restore'] = $restore;

        $data['liquidation'] = $liquidation;

        // Lista degli allegati
        $data['listAttach'] = $attach->getAllByObject(
            'contest_acts',
            $liquidation['id'],
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
            ]);

        // Labels
        $data['labels'] = [];

        // Id dell'ente utilizzato per la gestione da parte del super admin
        $data['institution_id'] = $liquidation['institution_id'];

        $startYear = 2013;
        $thisYear = date("Y");

        $tmp = [null => ''];
        for ($i = $startYear; $i <= $thisYear; $i++) {
            $tmp[$i] = $i;
        }
        $data['years'] = $tmp;

        $liquidationDate = convertDateToForm($liquidation['activation_date']);
        $data['activation_date'] = $liquidationDate['date'];
        $data['seo'] = $liquidation['p_s_d_r'] ?? null;

        render('contests_act/form_store_liquidation', $data, 'admin');

    }


    /**
     * @description Funzione che effettua l'update di una Delibera
     *
     * @return void
     * @throws Exception
     * @url /admin/contests-act/update-deliberation.html
     * @method POST
     */
    public function updateDeliberation(): void
    {
        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new DeliberationValidator();
        $check = $validator->check('update');

        if ($check['is_success']) {
            $doAction = true;

            $deliberationId = (int)strip_tags(Input::post('id'));

            // Recupero la delibera attuale prima di modificarla e la salvo nel versioning
            $deliberation = ContestsActsModel::where('typology', 'deliberation')
                ->where('id', $deliberationId)
                ->with('proceedings:id,object')
                ->with('structure:id,structure_name')
                ->with('rup:id,full_name')
                ->with('relative_measure:id,object')
                ->with(['public_in' => function ($query) {
                    $query->select(['public_in_id', 'contest_act_id', 'section_fo.name'])
                        ->join('section_fo', 'section_fo.id', '=', 'public_in_id');
                }])
                ->with('all_attachs')
                ->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute('update', (!checkRecordOwner($deliberation['owner_id']) && $this->acl->getCreate()));

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
                $data['cig'] = strip_tags(Input::post('cig', true));
                $data['act_date'] = !empty(Input::post('act_date')) ? convertDateToDatabase(strip_tags(Input::post('act_date', true))) : null;
                $data['activation_date'] = !empty(Input::post('activation_date')) ? strip_tags(Input::post('activation_date', true)) : null;
                $data['object_structures_id'] = setDefaultData(strip_tags(Input::post('object_structures_id', true)), null, ['']);
                $data['object_personnel_id'] = setDefaultData(strip_tags(Input::post('object_personnel_id', true)), null, ['']);
                $data['object_measure_id'] = setDefaultData(strip_tags(Input::post('object_measure_id', true)), null, ['', null]);
                $data['details'] = Input::post('details', true);
                $data['sector'] = setDefaultData(strip_tags(Input::post('sector', true)), null, ['', null]);

                // Update Delibera
                ContestsActsModel::where('id', $deliberationId)->updateWithLogs($deliberation, $data);

                // Svuoto le tabelle di relazione e le aggiorno
                $this->clear(
                    $deliberation,
                    null,
                    null,
                    !empty(Input::post('procedures')) ? explode(',', strip_tags((string)Input::post('procedures', true))) : null,
                    Input::post('public_in', true)
                );

                // Upload allegati associati al personale.
                $attach->update(
                    'attach_files',
                    'contest_acts',
                    $deliberationId,
                    $deliberation['institution_id'],
                    $deliberation['object']
                );

                // Generazione nuovo token
                Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
                $json->set('message', sprintf(__('success_update_operation', null, 'patos'), 'Determina '));
            }

        } else {

            $code = $json->bad();
            $json->error('error', $check['errors']);

        }

        $json->setStatusCode($code);
        $json->response();
    }

    /**
     * @description Funzione che effettua l'update di un Bando di gara
     *
     * @return void
     * @throws Exception
     * @url /admin/contests-act/update-notice.html
     * @method POST
     */
    public function updateNotice(): void
    {
        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new ContestActValidator();
        $check = $validator->check('update');

        if ($check['is_success']) {
            $doAction = true;

            $noticeId = (int)strip_tags(Input::post('id', true));

            // Recupero il bando di gara attuale prima di modificarlo e lo salvo nel versioning
            $notice = ContestsActsModel::where('typology', 'notice')
                ->where('id', $noticeId)
                ->with('proceedings:id,object')
                ->with('contraent_choice:id,name')
                ->with('structure:id,structure_name')
                ->with('rup:id,full_name')
                ->with('requirements:id,code,denomination')
                ->with('relative_measure:id,object')
                ->with(['public_in' => function ($query) {
                    $query->select(['public_in_id', 'contest_act_id', 'section_fo.name'])
                        ->join('section_fo', 'section_fo.id', '=', 'public_in_id');
                }])
                ->with('all_attachs')
                ->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute('update', (!checkRecordOwner($notice->owner_id) && $this->acl->getCreate()));

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

                $isMulticig = $notice->is_multicig;

                $data = [];
                $data['anac_year'] = setDefaultData(strip_tags(Input::post('anac_year', true)));
                $data['contract'] = setDefaultData(strip_tags(Input::post('contract', true)), null, ['']);
                $data['object'] = strip_tags(Input::post('object', true));
                $data['asta_base_value'] = !empty(Input::post('asta_base_value_sum')) ? toFloat(strip_tags(Input::post('asta_base_value_sum', true))) : null;
                $data['no_amount'] = setDefaultData(strip_tags(Input::post('no_amount', true)), null, ['']);
                $data['contraent_choice'] = setDefaultData(strip_tags(Input::post('contraent_choice', true)), null, ['']);
                $data['adjudicator_name'] = strip_tags(Input::post('adjudicator_name', true));
                $data['adjudicator_data'] = strip_tags(Input::post('adjudicator_data', true));
                $data['administration_type'] = setDefaultData(strip_tags(Input::post('administration_type', true)));
                $data['province_office'] = setDefaultData(strip_tags(Input::post('province_office', true)), null, ['']);
                $data['municipality_office'] = strip_tags(Input::post('municipality_office', true));
                $data['office_address'] = strip_tags(Input::post('office_address', true));
                $data['istat_office'] = strip_tags(Input::post('istat_office', true));
                $data['nuts_office'] = strip_tags(Input::post('nuts_office', true));
                $data['object_structures_id'] = setDefaultData(strip_tags(Input::post('object_structures_id', true)), null, ['']);
                $data['guue_date'] = !empty(Input::post('guue_date')) ? convertDateToDatabase(strip_tags(Input::post('guue_date', true))) : null;
                $data['guri_date'] = !empty(Input::post('guri_date')) ? convertDateToDatabase(strip_tags(Input::post('guri_date', true))) : null;
                $data['act_date'] = !empty(Input::post('act_date')) ? convertDateToDatabase(strip_tags(Input::post('act_date', true))) : null;
                $data['activation_date'] = !empty(Input::post('activation_date')) ? strip_tags(Input::post('activation_date', true)) : null;
                $data['expiration_date'] = !empty(Input::post('expiration_date')) ? convertDateToDatabase(strip_tags(Input::post('expiration_date', true))) : null;
                $data['object_personnel_id'] = setDefaultData(strip_tags(Input::post('object_personnel_id', true)), null, ['']);
                $data['object_measure_id'] = setDefaultData(strip_tags(Input::post('object_measure_id', true)), null, ['', null]);
                $data['cpv_code_id'] = setDefaultData(strip_tags(Input::post('cpv_code_id', true)), null, ['', 0]);
                $data['codice_scp'] = strip_tags(Input::post('codice_scp', true));
                $data['url_scp'] = strip_tags(Input::post('url_scp', true));
                $data['details'] = Input::post('details', true);
                $data['cig'] = ($isMulticig) ? null : strip_tags(escapeXss(Input::post('cig_code')[0]));
                $data['sector'] = setDefaultData(strip_tags(Input::post('sector', true)), null, ['', null]);

                // Update Bando di gara
                ContestsActsModel::where('id', $noticeId)->updateWithLogs($notice, $data);

                // Aggiorno la data di pubblicazione nei lotti relativi al bando di gara
                ContestsActsModel::withoutTimestamps()
                    ->where('typology', 'lot')
                    ->where('relative_notice_id', $noticeId)
                    ->update([
                        'activation_date' => !empty(Input::post('activation_date')) ? convertDateToDatabase(strip_tags(Input::post('activation_date', true))) : null
                    ]);

                // Svuoto le tabelle di relazione e le aggiorno
                $this->clear(
                    $notice,
                    null,
                    null,
                    !empty(Input::post('procedures')) ? explode(',', strip_tags((string)Input::post('procedures', true))) : null,
                    Input::post('public_in', true),
                    Input::post('requirements', true),
                );

                // Upload allegati associati al personale.
                $attach->update(
                    'attach_files',
                    'contest_acts',
                    $noticeId,
                    $notice->institution_id,
                    $notice['object'],
                );

                // Generazione nuovo token
                Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
                $json->set('message', sprintf(__('success_update_operation', null, 'patos'), 'Bando di gara '));
            }

        } else {

            $code = $json->bad();
            $json->error('error', $check['errors']);

        }

        $json->setStatusCode($code);
        $json->response();

    }

    /**
     * @description Funzione che effettua l'update di una Lotto
     *
     * @return void
     * @throws Exception
     * @url /admin/contests-act/update-lot.html
     * @method POST
     */
    public function updateLot(): void
    {
        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new LotValidator();
        $check = $validator->check('update');

        if ($check['is_success']) {
            $doAction = true;

            $lotId = (int)strip_tags(Input::post('id', true));

            // Recupero il bando associato al lotto
            $relativeNotice = ContestsActsModel::select(['id', 'activation_date'])
                ->where('id', (int)strip_tags(Input::post('relative_notice_id', true)))
                ->where('typology', 'notice')
                ->first();

            // Recupero il lotto attuale prima di modificarlo e lo salvo nel versioning
            $lot = ContestsActsModel::where('typology', 'lot')
                ->where('id', $lotId)
                ->with(['relative_notice' => function ($query) {
                    $query->select(['object_contests_acts.id', 'object', 'cig', 'activation_date', 'adjudicator_name', 'adjudicator_data', 'contraent_choice']);
                    $query->with(['contraent' => function ($query) {
                        $query->select(['id', 'name']);
                    }]);
                }])
                ->with('all_attachs')
                ->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute('update', (!checkRecordOwner($lot->owner_id) && $this->acl->getCreate()));

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
                $data['relative_notice_id'] = setDefaultData(strip_tags(Input::post('relative_notice_id', true)));
                $data['activation_date'] = setDefaultData($relativeNotice->activation_date);
                $data['object'] = strip_tags(Input::post('object', true));
                $data['cig'] = trim(strip_tags(Input::post('cig', true)));
                $data['asta_base_value'] = !empty(Input::post('asta_base_value')) ? toFloat(strip_tags(Input::post('asta_base_value', true))) : null;

                // Update Lotto
                ContestsActsModel::where('id', $lotId)->updateWithLogs($lot, $data);

                // Upload allegati associati al personale.
                $attach->update(
                    'attach_files',
                    'contest_acts',
                    $lotId,
                    $lot->institution_id,
                    $lot['object']
                );

                // Generazione nuovo token
                Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
                $json->set('message', sprintf(__('success_update_operation', null, 'patos'), 'Lotto '));
            }

        } else {

            $code = $json->bad();
            $json->error('error', $check['errors']);

        }

        $json->setStatusCode($code);
        $json->response();

    }

    /**
     * @description Funzione che effettua l'update di un Esito di gara
     *
     * @return void
     * @throws Exception
     * @url /admin/contests-act/update-notice.html
     * @method POST
     */
    public function updateResult(): void
    {
        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new ResultValidator();
        $check = $validator->check('update');

        if ($check['is_success']) {
            $doAction = true;

            $resultId = (int)strip_tags(Input::post('id', true));

            // Recupero l'esito di gara attuale prima di modificarlo e lo salvo nel versioning
            $result = ContestsActsModel::where('typology', 'result')
                ->where('id', $resultId)
                ->with('proceedings:id,object')
                ->with('participants:id,name')
                ->with('awardees:id,name')
                ->with('relative_liquidation:relative_procedure_id,id,amount_liquidated,typology,type,object,anac_year')
                ->with(['relative_notice' => function ($query) {
                    $query->select(['object_contests_acts.id', 'object', 'cig', 'activation_date', 'anac_year', 'adjudicator_name',
                        'contraent_choice', 'adjudicator_data', 'relative_notice_id', 'typology', 'object_structures.structure_name as structure_name'])
                        ->with(['contraent' => function ($query) {
                            $query->select(['id', 'name']);
                        }])
                        ->join('object_structures', 'object_structures.id', '=', 'object_contests_acts.object_structures_id', 'left outer');
                }])
                ->with(['public_in' => function ($query) {
                    $query->select(['public_in_id', 'contest_act_id', 'section_fo.name'])
                        ->join('section_fo', 'section_fo.id', '=', 'public_in_id');
                }])
                ->with('relative_measure:id,object')
                ->with('all_attachs')
                ->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute('update', (!checkRecordOwner($result->owner_id) && $this->acl->getCreate()));

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
                $data['relative_notice_id'] = setDefaultData(strip_tags(Input::post('notice_id', true)), null, ['']);
                $data['object'] = strip_tags(Input::post('object', true));
                $data['award_amount_value'] = !empty(Input::post('award_amount_value')) ? toFloat(strip_tags(Input::post('award_amount_value', true))) : null;
                $data['guue_date'] = !empty(Input::post('guue_date')) ? convertDateToDatabase(strip_tags(Input::post('guue_date', true))) : null;
                $data['guri_date'] = !empty(Input::post('guri_date')) ? convertDateToDatabase(strip_tags(Input::post('guri_date', true))) : null;
                $data['act_date'] = !empty(Input::post('act_date')) ? convertDateToDatabase(strip_tags(Input::post('act_date', true))) : null;
                $data['activation_date'] = !empty(Input::post('activation_date')) ? strip_tags(Input::post('activation_date', true)) : null;
                $data['work_start_date'] = !empty(Input::post('work_start_date')) ? convertDateToDatabase(strip_tags(Input::post('work_start_date', true))) : null;
                $data['work_end_date'] = !empty(Input::post('work_end_date')) ? convertDateToDatabase(strip_tags(Input::post('work_end_date', true))) : null;
                $data['contracting_stations_publication_date'] = !empty(Input::post('contracting_stations_publication_date')) ? convertDateToDatabase(strip_tags(Input::post('contracting_stations_publication_date', true))) : null;
                $data['typology_result'] = setDefaultData(strip_tags(Input::post('typology_result', true)));
                $data['object_measure_id'] = setDefaultData(strip_tags(Input::post('object_measure_id', true)), null, ['', null]);
                $data['details'] = Input::post('details', true);

                // Update Esito di gara
                ContestsActsModel::where('id', $resultId)->updateWithLogs($result, $data);

                // Svuoto le tabelle di relazione e le aggiorno
                $this->clear(
                    $result,
                    !empty(Input::post('participants')) ? explode(',', strip_tags((string)Input::post('participants', true))) : null,
                    !empty(Input::post('awardees')) ? explode(',', strip_tags((string)Input::post('awardees', true))) : null,
                    !empty(Input::post('procedures')) ? explode(',', strip_tags((string)Input::post('procedures', true))) : null,
                    Input::post('public_in', true)
                );

                // Upload allegati associati al personale.
                $attach->update(
                    'attach_files',
                    'contest_acts',
                    $resultId,
                    $result->institution_id,
                    $result['object']
                );

                // Generazione nuovo token
                Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
                $json->set('message', sprintf(__('success_update_operation', null, 'patos'), 'Esito di gara '));
            }

        } else {

            $code = $json->bad();
            $json->error('error', $check['errors']);

        }

        $json->setStatusCode($code);
        $json->response();

    }

    /**
     * @description Funzione che effettua l'update di un Avviso
     *
     * @return void
     * @throws Exception
     * @url /admin/contests-act/update-notice.html
     * @method POST
     */
    public function updateAlert(): void
    {
        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new AlertValidator();
        $check = $validator->check('update');

        if ($check['is_success']) {
            $doAction = true;

            $alertId = (int)strip_tags(Input::post('id', true));

            // Recupero l'avviso attuale prima di modificarlo e lo salvo nel versioning
            $alert = ContestsActsModel::where('typology', 'alert')
                ->where('id', $alertId)
                ->with('proceedings:id,object')
                ->with('structure:id,structure_name')
                ->with('rup:id,full_name')
                ->with(['relative_notice' => function ($query) {
                    $query->select(['object_contests_acts.id', 'object', 'cig', 'activation_date', 'adjudicator_name', 'adjudicator_data', 'contraent_choice']);
                    $query->with(['contraent' => function ($query) {
                        $query->select(['id', 'name']);
                    }]);
                }])
                ->with(['public_in' => function ($query) {
                    $query->select(['public_in_id', 'contest_act_id', 'section_fo.name'])
                        ->join('section_fo', 'section_fo.id', '=', 'public_in_id');
                }])
                ->with('relative_measure:id,object')
                ->with('all_attachs')
                ->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute('update', (!checkRecordOwner($alert['owner_id']) && $this->acl->getCreate()));

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

                if (!empty(Input::post('cig', true))) {
                    $cig = strip_tags(Input::post('cig', true));
                }

                $data = [];
                $data['relative_notice_id'] = setDefaultData(strip_tags(Input::post('notice_id', true)));
                $data['object'] = strip_tags(Input::post('object', true));
                $data['act_date'] = !empty(Input::post('act_date')) ? convertDateToDatabase(strip_tags(Input::post('act_date', true))) : null;
                $data['activation_date'] = !empty(Input::post('activation_date')) ? strip_tags(Input::post('activation_date', true)) : null;
                $data['expiration_date'] = !empty(Input::post('expiration_date')) ? convertDateToDatabase(strip_tags(Input::post('expiration_date', true))) : null;
                $data['object_structures_id'] = setDefaultData(strip_tags(Input::post('object_structures_id', true)), null, ['']);
                $data['object_personnel_id'] = setDefaultData(strip_tags(Input::post('object_personnel_id', true)), null, ['']);
                $data['object_measure_id'] = setDefaultData(strip_tags(Input::post('object_measure_id', true)), null, ['', null]);
                $data['details'] = Input::post('details', true);
                $data['cig'] = (!empty($cig)) ? $cig : null;
                $data['adjudicator_data'] = (empty(Input::post('notice_id', true)) && !empty(Input::post('adjudicator_data', true))) ? strip_tags(Input::post('adjudicator_data', true)) : null;
                $data['sector'] = setDefaultData(strip_tags(Input::post('sector', true)), null, ['', null]);

                // Update Avviso
                ContestsActsModel::where('id', $alertId)->updateWithLogs($alert, $data);

                // Svuoto le tabelle di relazione e le aggiorno
                $this->clear(
                    $alert,
                    null,
                    null,
                    !empty(Input::post('procedures')) ? explode(',', strip_tags((string)Input::post('procedures', true))) : null,
                    Input::post('public_in', true)
                );

                // Upload allegati associati al personale.
                $attach->update(
                    'attach_files',
                    'contest_acts',
                    $alertId,
                    $alert['institution_id'],
                    $alert['object'],
                );

                // Generazione nuovo token
                Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
                $json->set('message', sprintf(__('success_update_operation', null, 'patos'), 'Avviso '));
            }

        } else {

            $code = $json->bad();
            $json->error('error', $check['errors']);

        }

        $json->setStatusCode($code);
        $json->response();

    }

    /**
     * @description Funzione che effettua l'update di un Esito/Affidamento
     *
     * @return void
     * @throws Exception
     * @url /admin/contests-act/update-foster.html
     * @method POST
     */
    public function updateFoster(): void
    {
        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new FosterValidator();
        $check = $validator->check('update');

        if ($check['is_success']) {
            $doAction = true;

            $fosterId = (int)strip_tags(Input::post('id', true));

            // Recupero l'esito/affidamento attuale prima di modificarlo e lo salvo nel versioning
            $foster = ContestsActsModel::where('typology', 'foster')
                ->where('id', $fosterId)
                ->with('structure:id,structure_name')
                ->with('awardees:id,name')
                ->with('participants:id,name')
                ->with('contraent:id,name')
                ->with('rup:id,full_name')
                ->with('requirements:id,code,denomination')
                ->with(['relative_procedure' => function ($query) {
                    $query->select(['id', 'object', 'cig', 'relative_procedure_id', 'relative_notice_id', 'typology', 'anac_year', 'updated_at',
                        'award_amount_value', 'amount_liquidated', 'work_start_date', 'work_end_date', 'contraent_choice', 'adjudicator_name',
                        'adjudicator_data', 'typology']);
                }])
                ->with(['public_in' => function ($query) {
                    $query->select(['public_in_id', 'contest_act_id', 'section_fo.name'])
                        ->join('section_fo', 'section_fo.id', '=', 'public_in_id');
                }])
                ->with('relative_measure:id,object')
                ->with('all_attachs')
                ->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute('update', (!checkRecordOwner($foster->owner_id) && $this->acl->getCreate()));

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
                $data['anac_year'] = setDefaultData(strip_tags(Input::post('anac_year', true)), null, ['']);
                $data['object'] = strip_tags(Input::post('object', true));
                $data['relative_procedure_id'] = setDefaultData(Input::post('relative_procedure_id', true), null, ['']);
                $data['cig'] = strip_tags(Input::post('cig', true));
                $data['no_amount'] = setDefaultData(strip_tags(Input::post('no_amount', true)), null, ['']);
                $data['asta_base_value'] = !empty(Input::post('asta_base_value')) ? toFloat(strip_tags(Input::post('asta_base_value', true))) : null;
                $data['award_amount_value'] = !empty(Input::post('award_amount_value')) ? toFloat(strip_tags(Input::post('award_amount_value', true))) : null;
                $data['contraent_choice'] = setDefaultData(strip_tags(Input::post('contraent_choice', true)), null, ['']);
                $data['contract'] = !empty(Input::post('contract')) ? (int)Input::post('contract', true) : null; //Attenzione
                $data['adjudicator_name'] = strip_tags(Input::post('adjudicator_name', true));
                $data['adjudicator_data'] = strip_tags(Input::post('adjudicator_data', true));
                $data['administration_type'] = setDefaultData(strip_tags(Input::post('administration_type', true)), null, ['']);
                $data['province_office'] = setDefaultData(strip_tags(Input::post('province_office', true)), null, ['']);
                $data['municipality_office'] = strip_tags(Input::post('municipality_office', true));
                $data['office_address'] = strip_tags(Input::post('office_address', true));
                $data['istat_office'] = strip_tags(Input::post('istat_office', true));
                $data['nuts_office'] = strip_tags(Input::post('nuts_office', true));
                $data['object_structures_id'] = setDefaultData(strip_tags(Input::post('object_structures_id', true)), null, ['']);
                $data['guue_date'] = !empty(Input::post('guue_date')) ? convertDateToDatabase(strip_tags(Input::post('guue_date', true))) : null;
                $data['guri_date'] = !empty(Input::post('guri_date')) ? convertDateToDatabase(strip_tags(Input::post('guri_date', true))) : null;
                $data['act_date'] = !empty(Input::post('act_date')) ? convertDateToDatabase(strip_tags(Input::post('act_date', true))) : null;
                $data['activation_date'] = !empty(Input::post('activation_date')) ? strip_tags(Input::post('activation_date', true)) : null;
                $data['publication_date_type'] = setDefaultData(strip_tags(Input::post('publication_date_type', true)), null, ['']);
                $data['work_start_date'] = !empty(Input::post('work_start_date')) ? convertDateToDatabase(strip_tags(Input::post('work_start_date', true))) : null;
                $data['work_end_date'] = !empty(Input::post('work_end_date')) ? convertDateToDatabase(strip_tags(Input::post('work_end_date', true))) : null;
                $data['contracting_stations_publication_date'] = !empty(Input::post('contracting_stations_publication_date')) ? convertDateToDatabase(strip_tags(Input::post('contracting_stations_publication_date', true))) : null;
                $data['typology_result'] = setDefaultData(strip_tags(Input::post('typology_result', true)));
                $data['object_personnel_id'] = setDefaultData(strip_tags(Input::post('object_personnel_id', true)), null, ['']);
                $data['object_measure_id'] = setDefaultData(strip_tags(Input::post('object_measure_id', true)), null, ['', null]);
                $data['cpv_code_id'] = setDefaultData(strip_tags(Input::post('cpv_code_id', true)), null, ['', 0]);
                $data['codice_scp'] = strip_tags(Input::post('codice_scp', true));
                $data['url_scp'] = strip_tags(Input::post('url_scp', true));
                $data['details'] = Input::post('details', true);
                $data['decree_163'] = setDefaultData(strip_tags(Input::post('decree_163', true)), 0, ['', null]);
                $data['sector'] = setDefaultData(strip_tags(Input::post('sector', true)), null, ['', null]);

                // Update Esito/Affidamento
                ContestsActsModel::where('id', $fosterId)->updateWithLogs($foster, $data);

                // Svuoto le tabelle di relazione e le aggiorno
                $this->clear(
                    $foster,
                    !empty(Input::post('participants')) ? explode(',', strip_tags((string)Input::post('participants', true))) : null,
                    !empty(Input::post('awardees')) ? explode(',', strip_tags((string)Input::post('awardees', true))) : null,
                    null,
                    Input::post('public_in', true),
                    Input::post('requirements', true),
                );

                // Upload allegati associati al personale.
                $attach->update(
                    'attach_files',
                    'contest_acts',
                    $fosterId,
                    $foster->institution_id,
                    $foster['object'],
                );

                // Generazione nuovo token
                Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
                $json->set('message', sprintf(__('success_update_operation', null, 'patos'), 'Esito/Affidamento '));
            }

        } else {

            $code = $json->bad();
            $json->error('error', $check['errors']);

        }

        $json->setStatusCode($code);
        $json->response();

    }

    /**
     * @description Funzione che effettua l'update di una Liquidazione
     *
     * @return void
     * @throws Exception
     * @url /admin/contests-act/update-liquidation.html
     * @method POST
     */
    public function updateLiquidation(): void
    {
        $json = new JsonResponse();
        $code = $json->success();

        // Validatore form
        $validator = new NoticeLiquidationValidator();
        $check = $validator->check('update');

        if ($check['is_success']) {
            $doAction = true;

            $liquidationId = (int)strip_tags(Input::post('id', true));

            // Recupero la liquidazione attuale prima di modificarla e la salvo nel versioning
            $liquidation = ContestsActsModel::where('typology', 'liquidation')
                ->where('id', $liquidationId)
                ->with(['relative_procedure' => function ($query) {
                    $query->select(['id', 'object', 'cig', 'relative_procedure_id', 'relative_notice_id', 'typology', 'anac_year', 'updated_at',
                        'award_amount_value', 'amount_liquidated', 'work_start_date', 'work_end_date', 'contraent_choice', 'adjudicator_name',
                        'adjudicator_data', 'typology']);
                    $query->with(['relative_notice' => function ($query) {
                        $query->select(['object_contests_acts.id', 'object', 'cig', 'relative_notice_id', 'object_structures_id', 'contraent_choice', 'adjudicator_data',
                            'adjudicator_name', 'anac_year', 'typology']);
                        $query->with(['structure' => function ($query) {
                            $query->select(['id', 'structure_name']);
                        }]);
                    }]);
                }])
                ->with('all_attachs')
                ->first();

            //Setto il metodo della rotta per i permessi
            $this->acl->setRoute('update', (!checkRecordOwner($liquidation['owner_id']) && $this->acl->getCreate()));

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
                $data['relative_procedure_id'] = setDefaultData(strip_tags(Input::post('relative_procedure_id', true)), null, ['']);
//                $data['amount_liquidated'] = !empty(Input::post('amount_liquidated')) ? toFloat(strip_tags(Input::post('amount_liquidated', true))) : null;
                $data['anac_year'] = setDefaultData(strip_tags(Input::post('anac_year', true)), null, ['']);
                $data['activation_date'] = !empty(Input::post('activation_date')) ? convertDateToDatabase(strip_tags(Input::post('activation_date', true))) : null;
                $data['details'] = Input::post('details', true);

                // Update Liquidazione
                ContestsActsModel::where('id', $liquidationId)->updateWithLogs($liquidation, $data);

                // Upload allegati associati al personale.
                $attach->update(
                    'attach_files',
                    'contest_acts',
                    $liquidationId,
                    $liquidation['institution_id'],
                    $liquidation['object']
                );

                // Generazione nuovo token
                Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
                $json->set('message', sprintf(__('success_update_operation', null, 'patos'), 'Liquidazione '));
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
     * @param ContestsActsModel|null $contestAct   Elemento (bando, esito...)
     * @param array|int|null         $participants Partecipanti
     * @param array|int|null         $awardees     Aggiudicatari
     * @param array|int|null         $procedures   Procedure relative
     * @param array|int|null         $publicIn     Sezioni per il pubblica in
     * @param array|int|null         $requirements Requisiti di qualificazione
     * @return void
     */
    protected function clear(ContestsActsModel $contestAct = null, array|int $participants = null, array|int $awardees = null, array|int $procedures = null, array|int $publicIn = null, array|int $requirements = null): void
    {
        $dataParticipant = [];
        if ($participants !== null) {
            foreach ($participants as $participant) {
                $dataParticipant[] = is_array($participant) ? $participant['id'] : $participant;
            }
        }

        $dataAwardees = [];
        if ($awardees !== null) {
            foreach ($awardees as $awardee) {
                $dataAwardees[] = is_array($awardee) ? $awardee['id'] : $awardee;
            }
        }

        $dataProcedures = [];
        if ($procedures !== null) {
            foreach ($procedures as $procedure) {
                $dataProcedures[] = is_array($procedure) ? $procedure['id'] : $procedure;
            }
        }

        $dataPublicIn = [];
        if ($publicIn !== null) {
            foreach ($publicIn as $in) {
                $dataPublicIn[] = is_array($in) ? strip_tags($in['public_in_id']) : strip_tags($in);
            }
        }

        $dataRequirements = [];
        if ($requirements !== null) {
            foreach ($requirements as $requirement) {
                $dataRequirements[] = is_array($requirement) ? strip_tags($requirement['id']) : strip_tags($requirement);
            }
        }

        if (in_array($contestAct['typology'], ['deliberation', 'notice', 'foster'])) {
            $contestAct->requirements()->sync($dataRequirements);
        }

        if (in_array($contestAct['typology'], ['deliberation', 'notice', 'result', 'alert', 'foster'])) {
            //Insert/Update nella tabella di relazione
            $contestAct->public_in()->sync($dataPublicIn);

            if ($contestAct['typology'] !== 'foster') {
                //Insert/Update nella tabella di relazione
                $contestAct->proceedings()->syncWithPivotValues($dataProcedures, ['typology' => 'other-procedures']);
            }
        }

        if (in_array($contestAct['typology'], ['result', 'foster'])) {
            //Insert/Update nella tabella di relazione
            $contestAct->participants()->syncWithPivotValues($dataParticipant, ['typology' => 'participant']);
            //Insert/Update nella tabella di relazione
            $contestAct->awardees()->syncWithPivotValues($dataAwardees, ['typology' => 'awardee']);
        }

    }

    /**
     * @description Funzione che effettua l'eliminazione di una Delibera
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/contests-act/delete-deliberation/:id.html
     * @method GET
     */
    public function deleteDeliberation(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['delete', 'create', 'update'], true);

        //Validatore che verifica se l'elemento da eliminare esiste
        $validator = new DeliberationValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getDelete() && $this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/contests-act', sessionSetNotify($validate['errors'], 'danger'));
            exit();

        }

        // Se la validazione va a buon fine recupero l'elemento da eliminare dal registro
        $deliberation = Registry::get('deliberation');

        //Elimino la delibera settando deleted = 1
        $deliberation->deleteWithLogs($deliberation);

        sessionSetNotify(sprintf(__('success_delete_operation', null, 'patos'), 'Determina '));
        // Generazione nuovo token
        Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        redirect('admin/contests-act');

    }

    /**
     * @description Funzione che effettua l'eliminazione di un Bando di gara
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/contests-act/delete-notice/:id.html
     * @method GET
     */
    public function deleteNotice(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['delete', 'create', 'update'], true);

        //Validatore che verifica se l'elemento da eliminare esiste
        $validator = new ContestActValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getDelete() && $this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/contests-act', sessionSetNotify($validate['errors'], 'danger'));
            exit();

        }

        $notice = Registry::get('notice');

        //Elimino il bando di gara settando deleted = 1
        $notice->deleteWithLogs($notice);

        // Elimino i lotti relativi al bando di gara
        ContestsActsModel::where('typology', 'lot')
            ->where('relative_notice_id', $notice->id)
            ->delete();

        sessionSetNotify(sprintf(__('success_delete_operation', null, 'patos'), 'Bando di gara '));

        // Generazione nuovo token
        Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        redirect('admin/contests-act');
    }

    /**
     * @description Funzione che effettua l'eliminazione di un Lotto
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/contests-act/delete-lot/:id.html
     * @method GET
     */
    public function deleteLot(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['delete', 'create', 'update'], true);

        //Validatore che verifica se l'elemento da eliminare esiste
        $validator = new LotValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getDelete() && $this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/contests-act', sessionSetNotify($validate['errors'], 'danger'));
            exit();

        }

        // Se la validazione va a buon fine recupero l'elemento da eliminare dal registro
        $lot = Registry::get('lot');

        //Elimino il lotto settando deleted = 1
        $lot->deleteWithLogs($lot);

        sessionSetNotify(sprintf(__('success_delete_operation', null, 'patos'), 'Lotto '));

        // Generazione nuovo token
        Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        redirect('admin/contests-act');
    }

    /**
     * @description Funzione che effettua l'eliminazione di un Esito di gara
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/contests-act/delete-result/:id.html
     * @method GET
     */
    public function deleteResult(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['delete', 'create', 'update'], true);

        //Validatore che verifica se l'elemento da eliminare esiste
        $validator = new ResultValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getDelete() && $this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/contests-act', sessionSetNotify($validate['errors'], 'danger'));
            exit();

        }

        // Se la validazione va a buon fine recupero l'elemento da eliminare dal registro
        $result = Registry::get('result');

        //Elimino il personale settando deleted = 1
        $result->deleteWithLogs($result);

        sessionSetNotify(sprintf(__('success_delete_operation', null, 'patos'), 'Esito di gara '));

        // Generazione nuovo token
        Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        redirect('admin/contests-act');
    }

    /**
     * @description Funzione che effettua l'eliminazione di un Avviso
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/contests-act/delete-alert/:id.html
     * @method GET
     */
    public function deleteAlert(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['delete', 'create', 'update'], true);

        //Validatore che verifica se l'elemento da eliminare esiste
        $validator = new AlertValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getDelete() && $this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/contests-act', sessionSetNotify($validate['errors'], 'danger'));
            exit();

        }

        // Se la validazione va a buon fine recupero l'elemento da eliminare dal registro
        $alert = Registry::get('alert');

        //Elimino il personale settando deleted = 1
        $alert->deleteWithLogs($alert);

        sessionSetNotify(sprintf(__('success_delete_operation', null, 'patos'), 'Avviso '));
        // Generazione nuovo token
        Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        redirect('admin/contests-act');
    }

    /**
     * @description Funzione che effettua l'eliminazione di un Esito/Affidamento
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/contests-act/delete-foster/:id.html
     * @method GET
     */
    public function deleteFoster(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['delete', 'create', 'update'], true);

        $validator = new FosterValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getDelete() && $this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/contests-act', sessionSetNotify($validate['errors'], 'danger'));
            exit();

        }

        // Se la validazione va a buon fine recupero l'elemento da eliminare dal registro
        $foster = Registry::get('foster');

        $foster->deleteWithLogs($foster);

        sessionSetNotify(sprintf(__('success_delete_operation', null, 'patos'), 'Esito/Affidamento '));
        // Generazione nuovo token
        Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        redirect('admin/contests-act');
    }

    /**
     * @description Funzione che effettua l'eliminazione di una Liquidazione
     *
     * @return void
     * @noinspection PhpVoidFunctionResultUsedInspection
     * @throws Exception
     * @url /admin/contests-act/delete-liquidation/:id.html
     * @method GET
     */
    public function deleteLiquidation(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute(['delete', 'create', 'update'], true);

        $validator = new NoticeLiquidationValidator();
        $validate = $validator->validateUriSegmentId(!$this->acl->getDelete() && $this->acl->getUpdate());

        if (!$validate['is_success']) {

            redirect('admin/contests-act', sessionSetNotify($validate['errors'], 'danger'));
            exit();
        }

        // Se la validazione va a buon fine recupero l'elemento da eliminare dal registro
        $liquidation = Registry::get('notice_liquidation');

        $liquidation->deleteWithLogs($liquidation);

        sessionSetNotify(sprintf(__('success_delete_operation', null, 'patos'), 'Liquidazione '));
        // Generazione nuovo token
        Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        redirect('admin/contests-act');
    }

    /**
     * @description Funzione per l'eliminazione multipla
     *
     * @return void
     * @throws Exception
     * @url /admin/contests-act/deletes.html
     * @method GET
     */
    public function deletes(): void
    {
        //Setto il metodo della rotta per i permessi
        $this->acl->setRoute('delete');

        $validator = new ContestActValidator();
        $validate = $validator->multipleSelection();

        if ($validate['is_success']) {

            sessionSetNotify('Operazione avvenuta con successo');

            //Recupero gli elementi da eliminare dal registro
            $contestsActs = Registry::get('__ids__multi_select_profile__');

            //Estraggo gli array degli elementi da eliminare
            $ids = Arr::pluck($contestsActs, 'id');

            //Elimino gli elementi
            foreach ($contestsActs as $contestsAct) {
                $contestsAct->deleteWithLogs($contestsAct);
            }

            // Generazione nuovo token
            Token::forceRegenerate((int)config('csrf_expire', null, 'app'));
        } else {

            sessionSetNotify($validate['errors'], 'danger');

        }

        redirect('/admin/contests-act');
    }

    /**
     * @description Verifica se già esistono elementi con il cig passato nel parametro
     * @param array|null $cigs Cig da verificare
     * @return array|null
     */
    protected function checkCig(array $cigs = null): ?array
    {
        $ret = null;

        if (!empty(implode(',', $cigs))) {
            $checkCig = ContestsActsModel::select('id', 'cig')
                ->whereIn('cig', $cigs)
                ->groupBy('cig')
                ->get();

            if (!empty($checkCig)) {
                $ret = Arr::pluck($checkCig->toArray(), 'cig');
            }
        }
        return $ret;
    }

    /**
     * @description Funzione che setta i dati per il campo "Pubblica In"
     * @param bool|array|string|null $institutionTypeId Id del tipo ente
     * @param string|null            $archiveName       Nome dell'archivio
     * @return array
     */
    protected function setPublicInData(bool|array|string|null $institutionTypeId, null|string $archiveName): array
    {
        $publicIn = SectionFoConfigPublicationArchive::where('archive_name', '=', $archiveName)
            ->with('section:id,name')
            ->get()
            ->toArray();

        $dataPublicIn = [];

        foreach ($publicIn as $tmp) {
            if (!empty($tmp['section'])) {
                $dataPublicIn[$tmp['section']['id']] = !empty($tmp['section']['name']) ? $tmp['section']['name'] : '';
            }
        }

        return $dataPublicIn;
    }
}
