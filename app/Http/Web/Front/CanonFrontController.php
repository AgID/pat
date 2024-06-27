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
use Model\LeaseCanonsModel;
use Model\SectionsFoModel;
use System\Input;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller pagina front-end Canoni di Locazione
 */
class CanonFrontController extends BaseFrontController
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
     * Metodo chiamato per la pagina "Canoni di locazione o affitto percepiti"
     * ID sezione 136
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexPerceived(): void
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

        // Recupero i canoni di locazione percepiti da mostrare
        $canons = $this->getDataResults(2, $data);

        $canons = !empty($canons) ? $canons->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($canons);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['table'] = !empty($table) ? $table->generate() : null;
        $data['instances'] = $canons;
        $data['instance'] = $currentPage;
        $data['paragraphs'] = $contents;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/lease_canons/lease_canon', $data, 'frontend');
    }

    /**
     * Funzione che ritorna i dati da mostrare nella tabella
     *
     * @param null $type {la tipologia per cui filtrare i dati}
     * @param array $data Array da passare alla visa
     * @return mixed
     * @throws Exception
     */
    private function getDataResults($type = null, array &$data = []): mixed
    {
        // Recupero i canoni di locazione percepiti da mostrare
        $canons = LeaseCanonsModel::where('canon_type', $type)
            ->with('properties:id,name')
            ->paginate(20, ['object_lease_canons.id', 'amount', 'start_date', 'end_date'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['i', 'start', 'end', 'am', 'sec_token']))
            ->setPath(currentUrl());

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($canons) && !empty($canons->toArray()['data'])) {

            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(LeaseCanonsModel::select(['created_at'])
                ->where('canon_type', $type)
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(LeaseCanonsModel::select(['updated_at'])
                ->where('canon_type', $type)
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        return $canons;
    }

    /**
     * Metodo che crea la tabella con i dati dei risultati della ricerca da mostrare
     *
     * @param $canons {dati da inserire nella tabella}
     * @return Table|null
     * @throws Exception
     */
    private function createTableRows($canons = null): ?Table
    {
        $table = null;

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        if (!empty($canons['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();
            $table->set_heading('Importo', 'Immobile', 'Inizio', 'Fine', '');

            // Creo le righe della tabella settando i dati da mostrare
            foreach ($canons['data'] as $canon) {

                // Creo link per gli immobili
                $properties = $canon['properties'];

                $filter = function ($property) {
                    return '<a href="' . siteUrl('page/133/details/' . $property['id'] . '/' . urlTitle($property['name'])) . '">' . escapeXss($property['name']) . '</a>';
                };
                $linkedProperties = array_map($filter, $properties);

                $table->add_row(
                    '&euro; ' . escapeXss(S::currency($canon['amount'], 2, ',', '.')),
                    implode(', ', $linkedProperties),
                    !empty($canon['start_date']) ? date('d-m-Y', strtotime($canon['start_date'])) : null,
                    !empty($canon['end_date']) ? date('d-m-Y', strtotime($canon['end_date'])) : null,
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $canon['id'] . '/canoni-di-locazione') . '" data-id="' . $canon['id'] . ' "> Dettagli</a>'
                );
            }
        }

        return $table;
    }

    /**
     * Metodo chiamato per la pagina "Canoni di locazione o affitto versati"
     * Id sezione 137
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexPaid(): void
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

        // Recupero i canoni di locazione percepiti da mostrare
        $canons = $this->getDataResults(1, $data);

        $canons = !empty($canons) ? $canons->toArray() : [];

        // Recupero il contenuto della pagina e i richiami dei vari paragrafi
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($canons);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $canons;
        $data['instance'] = $currentPage;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/lease_canons/lease_canon', $data, 'frontend');
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
        $element = LeaseCanonsModel::where('id', $elementId)
            ->with('properties:id,name')
            ->with(['structure' => function ($query) {
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

        $pageName = ($element['canon_type'] == 1)
            ? 'Canoni di locazione o di affitto versati'
            : 'Canoni di locazione o di affitto percepiti';

        // Dati passati alla vista
        $data['pageName'] = $pageName;
        $data['menuPages'] = $sectionFO;

        // aggiungi al Breadcrumbs la pagina del dettaglio
        $data['concatBreadcrumb'] = true;
        $data['bread'] = array();
        $data['bread'][] = array('name' => $pageName, 'link' => '/');
        $data['instance'] = $element;
        $data['currentPageId'] = $currentPageId;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['editRecord'] = true;

        $label = 'lease_canons';
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

        renderFront(config('vfo', null, 'app') . '/lease_canons/details', $data, 'frontend');

    }
}
