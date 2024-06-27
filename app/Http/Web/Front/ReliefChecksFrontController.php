<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

use Exception;
use Helpers\Table;
use Helpers\Utility\AttachmentArchive;
use Helpers\Utility\Meta;
use Model\ReliefChecksModel;
use Model\SectionsFoModel;
use System\Input;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller pagina front-end Controlli e Rilievi
 */
class ReliefChecksFrontController extends BaseFrontController
{
    /**
     * Costruttore
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        helper('url');
    }

    /**
     * Metodo per la pagina si snodo dei Controlli e rilievi
     * @return void
     * @throws Exception
     */
    public function pivot(): void
    {
        $pivot = new PivotController();
        $pivot->index();
    }

    /**
     * Metodo chiamato per la pagina
     * "Organismi indipendenti di valutazione, nuclei di valutazione o altri organismi con funzioni analoghe",
     * nella sezione CONTROLLI E RILIEVI SULL'AMMINISTRAZIONE
     * ID sezione 139
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexIndependentOrganisms(): void
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

        // Recupero i controlli rilievi da mostrare in base al campo pubblica in
        $reliefChecks = $this->getDataResults('139', $data);

        $reliefChecks = !empty($reliefChecks) ? $reliefChecks->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($reliefChecks);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $reliefChecks;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/relief_checks/relief_checks', $data, 'frontend');
    }

    /**
     * Funzione che ritorna i dati da mostrare nella tabella
     *
     * @param null $type {la tipologia per cui filtrare i dati}
     * @param array $data Dati da passare alla vista
     * @return mixed
     * @throws Exception
     */
    private function getDataResults($type = null, array &$data = []): mixed
    {

        // Recupero i canoni di locazione percepiti da mostrare
        $reliefChecks = ReliefChecksModel::join('rel_relief_check_public_in', 'rel_relief_check_public_in.relief_check_id', '=', 'object_relief_checks.id')
            ->where('rel_relief_check_public_in.public_in_id', $type)
            ->orderBy('date', 'DESC')
            ->paginate(20, ['object_relief_checks.id', 'object', 'date'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['str', 'obj', 'year', 'sec_token']))
            ->setPath(currentUrl());

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($reliefChecks) && !empty($reliefChecks->toArray()['data'])) {

            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ReliefChecksModel::select(['object_relief_checks.created_at'])
                ->join('rel_relief_check_public_in', 'rel_relief_check_public_in.relief_check_id', '=', 'object_relief_checks.id')
                ->where('rel_relief_check_public_in.public_in_id', $type)
                ->orderBy('object_relief_checks.created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ReliefChecksModel::select(['object_relief_checks.updated_at'])
                ->join('rel_relief_check_public_in', 'rel_relief_check_public_in.relief_check_id', '=', 'object_relief_checks.id')
                ->where('rel_relief_check_public_in.public_in_id', $type)
                ->orderBy('object_relief_checks.updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        return $reliefChecks;
    }

    /**
     * Metodo che crea la tabella con i dati dei risultati della ricerca da mostrare
     *
     * @param $reliefChecks {dati da inserire nella tabella}
     * @return Table|null
     * @throws Exception
     */
    private function createTableRows($reliefChecks = null): ?Table
    {
        $currentPageId = (int)uri()->segment(2, 0);
        $table = null;

        if (!empty($reliefChecks['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();
            $table->set_heading('Oggetto', 'Anno', '');

            // Creo le righe della tabella settando i dati da mostrare
            foreach ($reliefChecks['data'] as $reliefCheck) {

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $reliefCheck['id'] . '/' . urlTitle($reliefCheck['object'])) . '" data-id="' . $reliefCheck['id'] . ' ">' . escapeXss($reliefCheck['object']) . '</a>',
                    !empty($reliefCheck['date']) ? date('Y', strtotime($reliefCheck['date'])) : null,
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $reliefCheck['id'] . '/' . urlTitle($reliefCheck['object'])) . '" data-id="' . $reliefCheck['id'] . ' "> Dettagli </a>'
                );
            }
        }

        return $table;
    }

    /**
     * Metodo chiamato per la pagina "Organi di revisione amministrativa e contabile",
     * nella sezione CONTROLLI E RILIEVI SULL'AMMINISTRAZIONE
     * ID sezione 144
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexReviewOrganisms(): void
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

        // Recupero i controlli rilievi da mostrare in base al campo pubblica in
        $reliefChecks = $this->getDataResults('144', $data);

        $reliefChecks = !empty($reliefChecks) ? $reliefChecks->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($reliefChecks);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $reliefChecks;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/relief_checks/relief_checks', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Corte dei conti",
     * nella sezione CONTROLLI E RILIEVI SULL'AMMINISTRAZIONE
     * ID sezione 145
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexCourtOfAuditors(): void
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

        // Recupero i controlli rilievi da mostrare in base al campo pubblica in
        $reliefChecks = $this->getDataResults('145', $data);

        $reliefChecks = !empty($reliefChecks) ? $reliefChecks->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($reliefChecks);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $reliefChecks;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/relief_checks/relief_checks', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina
     * "Attestazione dell'OIV o di altra struttura analoga nell'assolvimento degli obblighi di pubblicazione",
     * nella sezione CONTROLLI E RILIEVI SULL'AMMINISTRAZIONE
     * ID sezione 140
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexOIVCertification(): void
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

        // Recupero i controlli rilievi da mostrare in base al campo pubblica in
        $reliefChecks = $this->getDataResults('140', $data);

        $reliefChecks = !empty($reliefChecks) ? $reliefChecks->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($reliefChecks);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $reliefChecks;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/relief_checks/relief_checks', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina
     * "Documento dell'OIV di validazione della Relazione sulla Performance",
     * nella sezione CONTROLLI E RILIEVI SULL'AMMINISTRAZIONE
     * ID sezione 141
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexOIVDocument(): void
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

        // Recupero i controlli rilievi da mostrare in base al campo pubblica in
        $reliefChecks = $this->getDataResults('141', $data);

        $reliefChecks = !empty($reliefChecks) ? $reliefChecks->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($reliefChecks);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $reliefChecks;
        $data['noRequiredPublication'] = true;
        $data['instance'] = $currentPage;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/relief_checks/relief_checks', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina
     * "Relazione dell'OIV sul funzionamento complessivo del Sistema di valutazione trasparenza e integrità dei controlli interni",
     * nella sezione CONTROLLI E RILIEVI SULL'AMMINISTRAZIONE
     * ID sezione 142
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexOIReport(): void
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

        // Recupero i controlli rilievi da mostrare in base al campo pubblica in
        $reliefChecks = $this->getDataResults('142', $data);

        $reliefChecks = !empty($reliefChecks) ? $reliefChecks->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($reliefChecks);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $reliefChecks;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/relief_checks/relief_checks', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina
     * "Altri atti degli organismi indipendenti di valutazione, nuclei di valutazione o altri organismi con funzioni analoghe",
     * nella sezione CONTROLLI E RILIEVI SULL'AMMINISTRAZIONE
     * ID sezione 143
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexOtherActs(): void
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

        // Recupero i controlli rilievi da mostrare in base al campo pubblica in
        $reliefChecks = $this->getDataResults('143', $data);

        $reliefChecks = !empty($reliefChecks) ? $reliefChecks->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($reliefChecks);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $reliefChecks;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/relief_checks/relief_checks', $data, 'frontend');
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
        $element = ReliefChecksModel::where('id', $elementId)
            ->with(['office' => function ($query) {
                $query->select(['id', 'structure_name']);
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

        // Recupero le voci per costruire il menu dei contenuti
        $sectionFO = getRightOrBottomMenu($currentPageId, $currentPage['parent_id']);

        // Dati passati alla vista
        $data['pageName'] = $element['object'];
        $data['menuPages'] = $sectionFO;

        // aggiungi al Breadcrumbs la pagina del dettaglio
        $data['concatBreadcrumb'] = true;
        $data['bread'] = array();
        $data['bread'][] = array('name' => $element['object'], 'link' => '/');
        $data['instance'] = $element;
        $data['currentPageId'] = $currentPageId;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['editRecord'] = true;

        $label = 'relief_checks';
        $elementId = $element['id'];
        $selectFields = [
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
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $element['object'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/relief_checks/details', $data, 'frontend');
    }
}
