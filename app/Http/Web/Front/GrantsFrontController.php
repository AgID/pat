<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

use Exception;
use Helpers\S;
use Helpers\Table;
use Helpers\Utility\AttachmentArchive;
use Helpers\Utility\Meta;
use Model\GrantsModel;
use Model\SectionsFoModel;
use System\Input;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller Sovvenzioni
 */
class GrantsFrontController extends BaseFrontController
{
    /**
     * @description Costruttore
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        helper('url');
    }

    /**
     * Metodo per la pagina di snodo delle sovvenzioni
     * @return void
     * @throws Exception
     */
    public function pivot(): void
    {
        $pivot = new PivotController();
        $pivot->index();
    }

    /**
     * Metodo chiamato per la pagina "Atti di concessione" nella sezione delle Sovvenzioni
     * ID sezione 126
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexConcessionActs(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'section_fo.id', 'parent_id', 'form_filter', 'controller_open_data', 'archive_name',
            'section_fo.created_at', 'section_fo.updated_at', 'is_system'])
            ->where('section_fo.id', $currentPageId)
            ->with(['contents' => function ($query) {
                $query->select(['id', 'created_at', 'updated_at', 'section_fo_id'])
                    ->orderBy('updated_at', 'DESC')
                    ->orderBy('created_at', 'DESC')
                    ->first();
            }])
            ->first()
            ->toArray();

        // Recupero le voci per costruire il menu dei contenuti
        $sectionFO = getRightOrBottomMenu($currentPageId, $currentPage['parent_id']);

        // Recupero i procedimenti(con campo Pubblica automaticamente i dati sul monitoraggio = 1) da mostrare
        $grants = $this->getDataResults('grant', $data);

        $grants = !empty($grants) ? $grants->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($grants, 'grant');

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $grants;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/grants/grants', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Elenco soggetti beneficiari" nella sezione delle Sovvenzioni
     * ID sezione 127
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexBeneficiary(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'section_fo.id', 'parent_id', 'form_filter', 'controller_open_data', 'archive_name',
            'section_fo.created_at', 'section_fo.updated_at', 'is_system'])
            ->where('section_fo.id', $currentPageId)
            ->with(['contents' => function ($query) {
                $query->select(['id', 'created_at', 'updated_at', 'section_fo_id'])
                    ->orderBy('updated_at', 'DESC')
                    ->orderBy('created_at', 'DESC')
                    ->first();
            }])
            ->first()
            ->toArray();

        // Recupero le voci per costruire il menu dei contenuti
        $sectionFO = getRightOrBottomMenu($currentPageId, $currentPage['parent_id']);

        // Recupero i procedimenti(con campo Pubblica automaticamente i dati sul monitoraggio = 1) da mostrare
        $grants = $this->getDataResults('grant', $data);

        $grants = !empty($grants) ? $grants->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($grants, 'beneficiary');

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $grants;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/grants/grants', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Pagamenti di Sovvenzioni, contributi, sussidi, vantaggi economici ",
     * nella sezione dei Pagamenti dell'amministrazione
     * Id sezione 155
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexLiquidation(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'section_fo.id', 'parent_id', 'form_filter', 'controller_open_data', 'archive_name',
            'section_fo.created_at', 'section_fo.updated_at', 'is_system'])
            ->where('section_fo.id', $currentPageId)
            ->with(['contents' => function ($query) {
                $query->select(['id', 'created_at', 'updated_at', 'section_fo_id'])
                    ->orderBy('updated_at', 'DESC')
                    ->orderBy('created_at', 'DESC')
                    ->first();
            }])
            ->first()
            ->toArray();

        // Recupero le voci per costruire il menu dei contenuti
        $sectionFO = getRightOrBottomMenu($currentPageId, $currentPage['parent_id']);

        // Recupero i procedimenti(con campo Pubblica automaticamente i dati sul monitoraggio = 1) da mostrare
        $grants = $this->getDataResults('liquidation', $data);

        $grants = !empty($grants) ? $grants->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($grants, 'liquidation');

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $grants;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/grants/grants', $data, 'frontend');
    }


    /**
     * Funzione che ritorna i dati da mostrare nella tabella
     *
     * @param string|null $type Tipologia (Sovvenzione o liquidazione)
     * @param array $data Dati da passare alla vista
     * @return mixed
     * @throws Exception
     */
    private function getDataResults(string $type = null, array &$data = []): mixed
    {

        // Recupero i provvedimenti da mostrare
        $grants = GrantsModel::where('object_grants.type', $type)
            ->with('relative_grant:id,object,beneficiary_name,concession_act_date,privacy')
            ->join('object_structures as structure', 'structure.id', '=', 'object_grants.object_structures_id', 'left outer')
            ->orderBy('object_grants.concession_act_date', 'DESC')
            ->orderBy('object_grants.reference_date', 'DESC')
            ->orderBy('object_grants.created_at', 'DESC')
            ->paginate(20, ['object_grants.id', 'object_grants.object', 'object_grants.beneficiary_name', 'object_grants.concession_act_date', 'object_grants.privacy',
                'object_grants.compensation_paid_date', 'object_grants.compensation_paid', 'structure.structure_name', 'object_grants.object_structures_id',
                'object_grants.grant_id'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['b', 'str', 'resp', 'year', 'obj', 'y', 'ben', 's', 'r', 'sec_token']))
            ->setPath(currentUrl());

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($grants) && !empty($grants->toArray()['data'])) {

            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(GrantsModel::select(['created_at'])
                ->where('type', $type)
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(GrantsModel::select(['updated_at'])
                ->where('type', $type)
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        return $grants;
    }


    /**
     * Metodo che crea la tabella con i dati dei risultati della ricerca da mostrare
     *
     * @param null $grants {dati da inserire nella tabella}
     * @param null $type Indica la tipologia (Sovvenzione/Liquidazione)
     * @return Table|null
     * @throws Exception
     */
    private function createTableRows($grants = null, $type = null): ?Table
    {
        $currentPageId = (int)uri()->segment(2, 0);
        $table = null;

        if (!empty($grants['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();

            // Setto le colonne in base al tipo(Liquidazione o Sovvenzione)
            if ($type == 'grant' || $type == 'liquidation') {

                $colName = ($type == 'grant') ? 'Struttura organizzativa' : 'Importo corrisposto';

                $table->set_heading('Oggetto', 'Beneficiario', $colName, 'Anno');

                // Creo le riche della tabella settando i dati da mostrare
                foreach ($grants['data'] as $grant) {

                    // Setto i valori delle colonne in base alla tipologia del dato
                    $colVal = ($type == 'grant')
                        ? '<a href="' . siteUrl('page/40/details/' . $grant['object_structures_id'] . '/' . urlTitle($grant['structure_name'])) . '" data-id="' . $grant['object_structures_id'] . ' ">' . escapeXss($grant['structure_name']) . '</a>'
                        : '&euro; ' . escapeXss(S::currency($grant['compensation_paid'], 2, ',', '.'));

                    // Se è una liquidazione prendo la data della sovvenzione associata,
                    // altrimenti prendo la data della sovvenzione stessa
                    $date = null;
                    if (!empty($grant['compensation_paid_date'])) {

                        $date = $grant['compensation_paid_date'];
                    } else {

                        if (!empty($grant['concession_act_date'])) {

                            $date = $grant['concession_act_date'];
                        }
                    }

                    // Se è una liquidazione prendo l'oggetto della sovvenzione associata,
                    // altrimenti prendo l'oggetto della sovvenzione stessa
                    $object = escapeXss(!empty($grant['relative_grant']['object'])
                        ? $grant['relative_grant']['object']
                        : $grant['object']);

                    $elementId = $grant['id'];

                    $finalPageId = !empty($grant['relative_grant'])
                        ? 155
                        : $currentPageId;

                    if (!empty($grant['relative_grant']['privacy']) || !empty($grant['privacy'])) {
                        $beneficiaryName = 'Omissis';
                    } else {
                        $beneficiaryName = !empty($grant['relative_grant']['beneficiary_name']) ? $grant['relative_grant']['beneficiary_name'] : $grant['beneficiary_name'];
                    }

                    $table->add_row(
                        '<a href="' . siteUrl('page/' . $finalPageId . '/details/' . $elementId . '/' . urlTitle($object)) . '" data-id="' . $elementId . ' ">' . $object . '</a>',
                        $beneficiaryName,
                        $colVal,
                        !empty($date) ? (!is_int($date) ? date('Y', strtotime($date)) : $date) : null
                    );
                }
            } else {
                // Colonne se è sono solo i beneficiari
                $table->set_heading('Nominativo', 'Data');

                // Creo le riche della tabella settando i dati da mostrare
                foreach ($grants['data'] as $grant) {

                    //Controllo il campo Omissis per vedere se devo mostrare il nome del soggetto beneficiario oppure no
                    if (!empty($grant['privacy'])) {
                        $beneficiaryName = 'Omissis';
                    } else {
                        $beneficiaryName = '<a href="' . siteUrl('page/126/details/' . $grant['id'] . '/' . urlTitle($grant['beneficiary_name'])) . '" data-id="' . $grant['id'] . ' ">' . escapeXss($grant['beneficiary_name']) . '</a>';
                    }

                    $table->add_row(
                        $beneficiaryName,
                        !empty($grant['concession_act_date']) ? date('d-m-Y', strtotime($grant['concession_act_date'])) : null
                    );
                }
            }
        }

        return $table;
    }

    /**
     * Funzione che ritorna i dati da mostrare nella pagina di dettaglio
     *
     * @return void
     * @throws Exception
     */
    public function details(): void
    {

        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);
        $elementId = (int)uri()->segment(4, 0);

        // Recupero l'elemento da mostrare
        $element = GrantsModel::where('object_grants.id', $elementId)
            ->with(['relative_grant' => function ($query) {
                $query->withoutGlobalScopes();
                $query->select(['object_grants.id', 'object', 'beneficiary_name', 'concession_act_date', 'fiscal_data', 'privacy',
                    'object_structures_id', 'notes', 'fiscal_data', 'fiscal_data_not_available'])
                    ->with(['structure' => function ($query) {
                        $query->select(['id', 'structure_name']);
                    }]);
            }])
            ->with('normatives:id,name')
            ->with(['regulation' => function ($query) {
                $query->select(['id', 'title']);
            }])
            ->with(['structure' => function ($query) {
                $query->select(['id', 'structure_name']);
            }])
            ->with('personnel:id,full_name')
            ->with(['relative_liquidation' => function ($query) {
                $query->select(['grant_id', 'id', 'compensation_paid', 'compensation_paid_date', 'reference_date']);
            }])
            ->first();

        // Se l'elemento non esiste mostro la pagina di errore
        if (empty($element)) {
            echo show404('Ops..', 'record not found');
            exit();
        }

        $element = $element->toArray();

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'id', 'parent_id', 'archive_name'])
            ->where('id', $currentPageId)
            ->first()
            ->toArray();

        // Se è una liquidazione prendo l'oggetto della sovvenzione associata,
        // altrimenti prendo l'oggetto della sovvenzione stessa
        $object = !empty($element['relative_grant']['object'])
            ? $element['relative_grant']['object']
            : $element['object'];

        $privacy = !empty($element['relative_grant']['privacy'])
            ? $element['relative_grant']['privacy']
            : $element['privacy'];

        $beneficiaryName = !empty($element['relative_grant']['beneficiary_name'])
            ? $element['relative_grant']['beneficiary_name']
            : $element['beneficiary_name'];

        $fiscalDataAvaiable = !empty($element['relative_grant'])
            ? $element['relative_grant']['fiscal_data_not_available']
            : $element['fiscal_data_not_available'];

        $fiscalData = !empty($element['relative_grant']['fiscal_data'])
            ? $element['relative_grant']['fiscal_data']
            : $element['fiscal_data'];

        $structure = !empty($element['relative_grant']['structure'])
            ? $element['relative_grant']['structure']
            : $element['structure'];

        $notes = !empty($element['relative_grant']['structure'])
            ? $element['relative_grant']['notes']
            : $element['notes'];

        // Recupero le voci per costruire il menu dei contenuti
        $sectionFO = getRightOrBottomMenu($currentPageId, $currentPage['parent_id']);

        // Dati passati alla vista
        $data['pageName'] = $object;
        $data['menuPages'] = $sectionFO;

        // aggiungi al Breadcrumbs la pagina del dettaglio
        $data['concatBreadcrumb'] = true;
        $data['bread'] = array();
        $data['bread'][] = array('name' => $object, 'link' => '/');
        $data['instance'] = $element;
        $data['privacy'] = $privacy;
        $data['fiscalData'] = $fiscalData;
        $data['fiscalDataAvaiable'] = $fiscalDataAvaiable;
        $data['beneficiaryName'] = $beneficiaryName;
        $data['structure'] = $structure;
        $data['notes'] = $notes;
        $data['currentPageId'] = $currentPageId;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['editRecord'] = true;
        $data['hasDifferentType'] = ($element['typology'] == 'liquidation') ? 'liquidation' : 'grant';

        $label = 'grants';
        $elementId = $element['id'];
        $selectFields = [
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
        ];

        // Allegati
        $attach = new AttachmentArchive();
        $data['listAttach'] = $attach->getAllByObject(
            $label,
            $elementId,
            $selectFields,
            true
        );

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $object)
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/grants/details', $data, 'frontend');
    }
}
