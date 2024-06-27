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
use Model\BalanceSheetsModel;
use Model\SectionsFoModel;
use System\Input;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller pagina front-end Bilanci
 */
class BalanceFrontController extends BaseFrontController
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
     * Metodo chiamato per la pagina di snodo dei Bilanci
     * @return void
     * @throws Exception
     */
    public function pivot(): void
    {
        $pivot = new PivotController();
        $pivot->index();
    }

    /**
     * Metodo chiamato per la pagina "Bilancio preventivo e consuntivo",
     * nella sezione dei Bilanci
     * ID sezione 130
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexFinalAndQuote(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'section_fo.id', 'parent_id', 'form_filter', 'controller_open_data',
            'archive_name', 'section_fo.created_at', 'section_fo.updated_at', 'is_system'])
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

        // Recupero i bilanci preventivi e consuntivi da mostrare
        $balanceSheets = $this->getDataResults('piano indicatori e risultati', '!=', $data);

        $balanceSheets = !empty($balanceSheets) ? $balanceSheets->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($balanceSheets);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['table'] = !empty($table) ? $table->generate() : null;
        $data['instances'] = $balanceSheets;
        $data['instance'] = $currentPage;
        $data['paragraphs'] = $contents;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/balance_sheets/balance_sheets', $data, 'frontend');
    }

    /**
     * Funzione che ritorna i dati da mostrare nella tabella
     *
     * @param null $type {la tipologia per cui filtrare i dati}
     * @param null $operator Operatore da utilizzare (= o !=)
     * @param array $data Dati da passare alla vista
     * @return mixed
     * @throws Exception
     */
    private function getDataResults($type = null, $operator = null, array &$data = []): mixed
    {

        // Recupero i bilanci da mostrare
        $balances = BalanceSheetsModel::where('typology', $operator, $type)
            ->paginate(20, ['id', 'name', 'year', 'typology'], 'p', (int)Input::get('p', true))
            ->onEachSide(2)
            ->appends(Input::get(['n', 'y', 'sec_token']))
            ->setPath(currentUrl());

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($balances) && !empty($balances->toArray()['data'])) {

            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(BalanceSheetsModel::select(['object_balance_sheets.created_at'])
                ->where('typology', $operator, $type)
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(BalanceSheetsModel::select(['object_balance_sheets.updated_at'])
                ->where('typology', $operator, $type)
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        return $balances;
    }

    /**
     * Metodo che crea la tabella con i dati dei risultati della ricerca da mostrare
     *
     * @param $balanceSheets {dati da inserire nella tabella}
     * @return Table|null
     * @throws Exception
     */
    private function createTableRows($balanceSheets = null): ?Table
    {
        $table = null;

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        if (!empty($balanceSheets['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();
            $table->set_heading('Nome', 'Anno', 'Tipologia');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($balanceSheets['data'] as $balance) {

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $balance['id'] . '/' . urlTitle($balance['name'])) . '" data-id="' . $balance['id'] . ' ">' . escapeXss($balance['name']) . '</a>',
                    escapeXss($balance['year']),
                    escapeXss($balance['typology'])
                );
            }
        }

        return $table;
    }

    /**
     * Metodo chiamato per la pagina "Piano degli indicatori e dei risultati attesi di bilancio"
     * ID sezione 131
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexExpectedResults(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'section_fo.id', 'parent_id', 'form_filter', 'controller_open_data',
            'archive_name', 'section_fo.created_at', 'section_fo.updated_at', 'is_system'])
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

        // Recupero i bilanci piano degli indicatori e dei risultati attesi di bilancio da mostrare
        $balanceSheets = $this->getDataResults('piano indicatori e risultati', '=', $data);

        $balanceSheets = !empty($balanceSheets) ? $balanceSheets->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($balanceSheets);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['table'] = !empty($table) ? $table->generate() : null;
        $data['instances'] = $balanceSheets;
        $data['instance'] = $currentPage;
        $data['paragraphs'] = $contents;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;

        renderFront(config('vfo', null, 'app') . '/balance_sheets/balance_sheets', $data, 'frontend');
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
        $element = BalanceSheetsModel::where('id', $elementId)
            ->with(['related_measure' => function ($query) {
                $query->select(['id', 'object']);
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
        $data['pageName'] = $element['name'];
        $data['menuPages'] = $sectionFO;
        $data['currentPageId'] = $currentPageId;

        // aggiungi al Breadcrumbs la pagina del dettaglio
        $data['concatBreadcrumb'] = true;
        $data['bread'] = array();
        $data['bread'][] = array('name' => $element['name'], 'link' => '/');

        $data['instance'] = $element;

        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['editRecord'] = true;

        $label = 'balance_sheets';
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
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/balance_sheets/details', $data, 'frontend');
    }
}
