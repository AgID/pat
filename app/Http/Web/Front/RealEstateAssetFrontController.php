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
use Model\RealEstateAssetModel;
use Model\SectionsFoModel;
use System\Input;

/**
 * Controller pagina front-end Patrimonio Immobiliare
 */
class RealEstateAssetFrontController extends BaseFrontController
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
     * Funzione che ritorna i dati da mostrare nella pagina di dettaglio
     *
     * @return void
     * @throws Exception
     * @url /page/page_id/details/element_id/element_name
     */
    public function details(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);
        $elementId = (int)uri()->segment(4, 0);
        $getAttachments = true;

        // Recupero l'elemento da mostrare
        $element = RealEstateAssetModel::where('id', $elementId)
            ->with('offices:id,structure_name,archived')
            ->with('canons:id,canon_type,amount,start_date,end_date')
            ->first();

        // Se l'elemento non esiste mostro la pagina di errore
        if (empty($element)) {
            echo show404('Ops..', 'record not found');
            exit();
        }

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
        $detailPage = '/real_estate_asset/details';

        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['editRecord'] = true;
        $archiveBread = '';

        // aggiungi al Breadcrumbs la pagina del dettaglio
        $data['concatBreadcrumb'] = true;
        $data['bread'] = array();
        if (!empty($archiveBread)) {
            $data['bread'][] = array('name' => 'Archivio', 'link' => $archiveBread);
        }
        $data['bread'][] = array('name' => $element['name'], 'link' => '/');

        if ($getAttachments) { //Se l'elemento non è archiviato recupero anche gli allegati

            $label = 'real_estate_asset';
            $elementId = $element['id'];
            $selectFields = [
                'id',
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
        }

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $element['name'])
            ->toHtml();

        $data['instance'] = is_array($element) ? $element : $element->toArray();

        renderFront(config('vfo', null, 'app') . $detailPage, $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Archivio" degli Immobili
     * ID sezione 134
     *
     * @return void
     * @throws Exception
     * @url /page/page_id/archivio.html
     */
    public function archived(): void
    {
        $this->pivot();
    }

    /**
     * Metodo per la pagina di snodo del personale
     * @return void
     * @throws Exception
     */
    public function pivot(): void
    {
        $pivot = new PivotController();
        $pivot->index();
    }

    /**
     * Metodo chiamato per la pagina "Canoni di locazione o affitto percepiti"
     * ID sezione 133
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function index(): void
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

        // Recupero gli immobili da mostrare
        $assets = RealEstateAssetModel::where('archived', 0)
            ->paginate(20, ['id', 'name', 'address'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['name', 'address', 'sec_token']))
            ->setPath(currentUrl());

        $assets = !empty($assets) ? $assets->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($assets['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(RealEstateAssetModel::select(['created_at'])
                ->where('archived', 0)
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(RealEstateAssetModel::select(['updated_at'])
                ->where('archived', 0)
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($assets);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['table'] = !empty($table) ? $table->generate() : null;
        $data['instances'] = $assets;
        $data['instance'] = $currentPage;
        $data['paragraphs'] = $contents;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/real_estate_asset/real_estate_asset', $data, 'frontend');
    }

    /**
     * Metodo che crea la tabella con i dati dei risultati della ricerca da mostrare
     *
     * @param null $assets Records da inserire nella tabella
     * @return Table|null
     * @throws Exception
     */
    protected function createTableRows($assets = null): ?Table
    {
        $currentPageId = (int)uri()->segment(2, 0);
        $table = null;

        if (!empty($assets['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();
            $table->set_heading('Nome', 'Indirizzo');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($assets['data'] as $asset) {

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $asset['id'] . '/' . urlTitle($asset['name'])) . '" data-id="' . $asset['id'] . ' ">' . escapeXss($asset['name']) . '</a>',
                    !empty($asset['address']) ? escapeXss($asset['address']) : null
                );
            }
        }

        return $table;
    }
}
