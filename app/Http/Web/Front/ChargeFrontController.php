<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

 /** @noinspection PhpUnused */



namespace Http\Web\Front;

use Exception;
use Helpers\Table;
use Helpers\Utility\AttachmentArchive;
use Model\ChargesModel;
use Model\SectionsFoModel;
use System\Input;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller pagina front-end Oneri Informativi
 */
class ChargeFrontController extends BaseFrontController
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
     * Metodo per la pagina di snodo degli Oneri informativi
     * Id sezione 31
     * @return void
     * @throws Exception
     */
    public function pivot(): void
    {
        $pivot = new PivotController();
        $pivot->index();
    }

    /**
     * Metodo chiamato per la pagina "Scadenzario obblighi amministrativi",
     * nella sezione Disposizioni Generali -> Oneri informativi
     * ID sezione 32
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexSchedule(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'section_fo.id', 'parent_id', 'form_filter', 'controller_open_data', 'archive_name',
            'rel_institution_type_sections_labeling.label', 'section_fo.created_at', 'section_fo.updated_at', 'is_system'])
            ->where('section_fo.id', $currentPageId)
            ->with([
                'contents' => function ($query) {
                    $query->select(['id', 'created_at', 'updated_at', 'section_fo_id', 'last_update_date'])
                        ->orderBy('last_update_date', 'DESC')
                        ->orderBy('updated_at', 'DESC')
                        ->orderBy('created_at', 'DESC')
                        ->first();
                }
            ])
            ->labelfilter((int)$institutionTypeId);


        $currentPage = $currentPage->first()
            ->toArray();

        // Recupero le voci per costruire il menu dei contenuti
        $sectionFO = getRightOrBottomMenu($currentPageId, $currentPage['parent_id']);

        // Recupero i canoni di locazione percepiti da mostrare
        $charges = $this->getDataResults('obbligo', $data);

        $charges = !empty($charges) ? $charges->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($charges);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['table'] = !empty($table) ? $table->generate() : null;
        $data['instances'] = $charges;
        $data['instance'] = $currentPage;
        $data['paragraphs'] = $contents;
        $data['noRequiredPublication'] = !empty($currentPage['no_required']);
        $data['normative_references'] = $currentPage['normatives'] ?? null;
        $data['allowSearch'] = false;
        //Abilita la paginazione AJAX della tabella dei risultati (vedi renderFrontFragment)
        $data['ajaxPagination'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        // Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;

        //Inclusione di eventuali librerie esterne
        $data = $data ?? null;
        $data = $data ?? null;

        renderFront(config('vfo', null, 'app') . '/charges/charges', $data, 'frontend');
    }

    /**
     * Funzione che ritorna i dati da mostrare nella tabella
     *
     * @param null  $type {la tipologia per cui filtrare i dati}
     * @param array $data Dati da passare alla vista
     * @return mixed
     * @throws Exception
     */
    private function getDataResults($type = null, array &$data = []): mixed
    {
        // Recupero i canoni di locazione percepiti da mostrare
        $charges =  ChargesModel::where('type', $type)
            ->paginate(20, ['id', 'title', 'citizen', 'companies', 'expiration_date'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            //->appends(Input::get(['operator', 'service', 'type', 'municipal', 'service', 'start', 'end', 'customer', 'per_page']))
            ->setPath(currentUrl());

        // Richiesta del solo frammento (paginazione AJAX): essendo getDataResults() chiamato da
        // tutte le pagine di questo controller prima del lavoro pesante (render completo), un
        // solo controllo qui copre tutte quelle pagine.
        if (isFrontFragmentRequest()) {
            $resultsArray = $charges->toArray();
            $table = $this->createTableRows($resultsArray);
            renderFrontFragment(['table' => !empty($table) ? $table->generate() : null, 'instances' => $resultsArray]);
            exit;
        }

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($charges) && !empty($charges->toArray()['data'])) {

            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ChargesModel::select(['created_at'])
                ->where('type', $type)
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ChargesModel::select(['updated_at'])
                ->where('type', $type)
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        return $charges;
    }

    /**
     * Metodo che crea la tabella con i dati dei risultati della ricerca da mostrare
     *
     * @param $charges {dati da inserire nella tabella}
     * @return Table|null
     * @throws Exception
     */
    private function createTableRows($charges = null): ?Table
    {
        $table = null;

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        if (!empty($charges['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();
            $table->set_heading('Denominazione', 'Per cittadini', 'Per Imprese', 'Data di scadenza');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($charges['data'] as $charge) {

                // Aggiungo le righe
                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $charge['id'] . '/' . urlTitle($charge['title'])) . '" data-id="' . $charge['id'] . ' " data-id="' . $charge['id'] . ' ">' . escapeXss($charge['title']) . '</a>',
                    (!empty($charge['citizen']) ? 'si' : 'no'),
                    (!empty($charge['companies']) ? 'si' : 'no'),
                    !empty($charge['expiration_date']) ? date('d/m/Y', strtotime($charge['expiration_date'])) : null
                );
            }
        }

        return $table;
    }

    /**
     * Metodo chiamato per la pagina "Scadenzario obblighi amministrativi",
     * nella sezione Disposizioni Generali -> Oneri informativi
     * ID sezione 33
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexForCitizensAndCompanies(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'section_fo.id', 'parent_id', 'form_filter', 'controller_open_data',
            'archive_name', 'rel_institution_type_sections_labeling.label', 'section_fo.created_at', 'section_fo.updated_at', 'is_system'])
            ->where('section_fo.id', $currentPageId)
            ->with([
                'contents' => function ($query) {
                    $query->select(['id', 'created_at', 'updated_at', 'section_fo_id', 'last_update_date'])
                        ->orderBy('last_update_date', 'DESC')
                        ->orderBy('updated_at', 'DESC')
                        ->orderBy('created_at', 'DESC')
                        ->first();
                }
            ])
            ->labelfilter((int)$institutionTypeId);


        $currentPage = $currentPage->first()
            ->toArray();

        // Recupero le voci per costruire il menu dei contenuti
        $sectionFO = getRightOrBottomMenu($currentPageId, $currentPage['parent_id']);

        // Recupero i canoni di locazione percepiti da mostrare
        $charges = $this->getDataResults('onere', $data);

        $charges = !empty($charges) ? $charges->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($charges);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['table'] = !empty($table) ? $table->generate() : null;
        $data['instances'] = $charges;
        $data['instance'] = $currentPage;
        $data['paragraphs'] = $contents;
        $data['noRequiredPublication'] = true;
        $data['allowSearch'] = false;
        //Abilita la paginazione AJAX della tabella dei risultati (vedi renderFrontFragment)
        $data['ajaxPagination'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;


        //Inclusione di eventuali librerie esterne
        $data = $data ?? null;
        $data = $data ?? null;
        renderFront(config('vfo', null, 'app') . '/charges/charges', $data, 'frontend');
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
        $element = ChargesModel::where('id', $elementId)
            ->with(['proceedings' => function ($query) {
                $query->select(['object_charges_id', 'object_proceedings_id', 'p.name']);
            }]);
        $element = $element ?? null;
        $element = $element->with('proceedings:id,name')
            ->with('measures:id,object,type')
            ->with('regulations:id,title')
            ->with(['normative' => function ($query) {
                $query->select(['id', 'name']);
            }]);


        $element = $element->first();

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
        $data['pageName'] = $element['title'];
        $data['menuPages'] = $sectionFO;

        // aggiungi al Breadcrumbs la pagina del dettaglio
        $data['concatBreadcrumb'] = true;
        $data['bread'] = array();
        $data['bread'][] = array('name' => $element['title'], 'link' => '/');
        $data['instance'] = $element;
        $data['noRequiredPublication'] = $element['type'] != 'obbligo';
        $data['currentPageId'] = $currentPageId;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['editRecord'] = true;

        $label = 'charges';
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
        // $showOnlyPublic = true;
        $sectionId = (int)uri()->segment(2, 0);

            // Allegati
            $attach = new AttachmentArchive();
            $data['listAttach'] = $attach->getAllByObject(
                $label,
                $elementId,
                $selectFields,
                true
            );

        //Inclusione di eventuali librerie esterne
        $data = $data ?? null;

        renderFront(config('vfo', null, 'app') . '/charges/details', $data, 'frontend');
    }
}
