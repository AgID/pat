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
use Model\CompanyModel;
use Model\SectionsFoModel;
use System\Input;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller pagina front-end Enti Controllati
 */
class CompanyFrontController extends BaseFrontController
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
     * Metodo chiamato per la pagina "Enti pubblici vigilati",
     * nella sezione Enti Controllati
     * ID sezione 89
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexPublicEntities(): void
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

        // Recupero i concorsi attivi da mostrare
        $companies = $this->getDataResults('ente pubblico vigilato', 0, $data);
        $companies = !empty($companies) ? $companies->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($companies);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['table'] = !empty($table) ? $table->generate() : null;
        $data['instances'] = $companies;
        $data['instance'] = $currentPage;
        $data['paragraphs'] = $contents;
        $data['allowSearch'] = false;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;

        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        //Abilita la paginazione AJAX della tabella dei risultati (vedi renderFrontFragment)
        $data['ajaxPagination'] = true;

        //Inclusione di eventuali librerie esterne
        $data = $data ?? null;
        $data = $data ?? null;

        renderFront(config('vfo', null, 'app') . '/companies/companies', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Società partecipate",
     * nella sezione degli Enti controllati
     * ID sezione 91
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexParticipatedCompanies(): void
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

        // Recupero i concorsi attivi da mostrare
        $companies = $this->getDataResults('societa partecipata', 0, $data);
        $companies = !empty($companies) ? $companies->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($companies);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['table'] = !empty($table) ? $table->generate() : null;
        $data['instances'] = $companies;
        $data['instance'] = $currentPage;
        $data['paragraphs'] = $contents;
        $data['allowSearch'] = false;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        //Abilita la paginazione AJAX della tabella dei risultati (vedi renderFrontFragment)
        $data['ajaxPagination'] = true;

        //Inclusione di eventuali librerie esterne
        $data = $data ?? null;
        $data = $data ?? null;

        renderFront(config('vfo', null, 'app') . '/companies/companies', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Enti di diritto privato controllati",
     * nella sezione degli Enti controllati
     * ID sezione 93
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexControlledPrivateEntities(): void
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

        // Recupero i concorsi attivi da mostrare
        $companies = $this->getDataResults('ente di diritto privato controllato', 0, $data);

        $companies = !empty($companies) ? $companies->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($companies);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['table'] = !empty($table) ? $table->generate() : null;
        $data['instances'] = $companies;
        $data['instance'] = $currentPage;
        $data['paragraphs'] = $contents;
        $data['allowSearch'] = false;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        //Abilita la paginazione AJAX della tabella dei risultati (vedi renderFrontFragment)
        $data['ajaxPagination'] = true;

        //Inclusione di eventuali librerie esterne
        $data = $data ?? null;
        $data = $data ?? null;

        renderFront(config('vfo', null, 'app') . '/companies/companies', $data, 'frontend');
    }


    /**
     * Funzione che ritorna i dati da mostrare nella tabella
     *
     * @param null     $type     {la tipologia per cui filtrare i dati}
     * @param int|null $archived {Parametro per il filtraggio}
     * @param array    $data     {Dati da passare alla vista}
     * @return mixed
     * @throws Exception
     */
    protected function getDataResults($type = null, int|null $archived = 0, array &$data = []): mixed
    {

        // Recupero i canoni di locazione percepiti da mostrare
        $companies =  CompanyModel::where(function ($query) use ($archived) {
                if ((int)$archived === 1) {
                    $query->where('object_company.archived', '=', 1);
                } else {
                }
            })
            ->where('typology', $type)
            ->filter(
                $archived
            )
            ->orderBy('company_name',"ASC")
            ->paginate(20, ['id', 'company_name', 'participation_measure', 'duration'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            //->appends(Input::get(['operator', 'service', 'type', 'municipal', 'service', 'start', 'end', 'customer', 'per_page']))
            ->setPath(currentUrl());

        // Richiesta del solo frammento (paginazione AJAX): essendo getDataResults() chiamato da
        // tutte le pagine di questo controller prima del lavoro pesante (render completo), un
        // solo controllo qui copre tutte quelle pagine.
        if (isFrontFragmentRequest()) {
            $resultsArray = $companies->toArray();
            $table = $this->createTableRows($resultsArray);
            renderFrontFragment(['table' => !empty($table) ? $table->generate() : null, 'instances' => $resultsArray]);
            exit;
        }

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($companies) && !empty($companies->toArray()['data'])) {

            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(CompanyModel::select(['created_at'])
                ->where(function ($query) use ($archived) {
                    if ((int)$archived === 1) {
                        $query->where('object_company.archived', '=', 1);
                    } else {
                    }
                })
                ->where('typology', $type)
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(CompanyModel::select(['updated_at'])
                ->where(function ($query) use ($archived) {
                    if ((int)$archived === 1) {
                        $query->where('object_company.archived', '=', 1);
                    } else {
                    }
                })
                ->where('typology', $type)
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        return $companies;
    }

    /**
     * Metodo che crea la tabella con i dati dei risultati della ricerca da mostrare
     *
     * @param $companies {dati da inserire nella tabella}
     * @return Table|null
     * @throws Exception
     */
    protected function createTableRows($companies = null): ?Table
    {
        $table = null;

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        if (!empty($companies['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();
            $table->set_heading('Ragione Sociale', 'Misura di partecipazione', 'Durata dell\'impegno');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($companies['data'] as $company) {

                // Aggiungo le righe
                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $company['id'] . '/' . urlTitle($company['company_name'])) . '" data-id="' . $company['id'] . ' ">' . escapeXss($company['company_name']) . '</a>',
                    escapeXss($company['participation_measure']),
                    escapeXss($company['duration'])
                );
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
        $getAttachments = true;

        // Recupero l'elemento da mostrare
        $element = CompanyModel::where('id', $elementId)
            ->with('representatives:id,full_name,archived');

        $element = $element->first();

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
        $data['pageName'] = $element['company_name'];
        $data['menuPages'] = $sectionFO;

        $data['currentPageId'] = $currentPageId;

        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['editRecord'] = true;
        $detailPage = '/companies/details';
        $archiveBread = '';


        if(!is_array($element)) {
            $element = $element->toArray();
        }

        $data['concatBreadcrumb'] = true;
        $data['bread'] = array();
        if (!empty($archiveBread)) {
            $data['bread'][] = array('name' => 'Archivio', 'link' => $archiveBread);
        }
        $data['bread'][] = array('name' => $element['company_name'], 'link' => '/');
        $data['currentPageId'] = $currentPageId;

        if ($getAttachments) { // Se l'elemento non è archiviato, recupero anche i suoi allegati e renderizzo la normale pagina di dettaglio

            $label = 'company';
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
                'label',
                'indexable',
                'file_ext',
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
        }

        //Inclusione del nodo JSON-LD della pagina
        $data['instance'] = $element;

        renderFront(config('vfo', null, 'app') . $detailPage, $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Archivio" degli enti pubblici vigilati
     * ID sezione 90
     *
     * @return void
     * @throws Exception
     */
    public function archivedPublicEntities(): void
    {
        $this->indexPublicEntities();
    }

    /**
     * Metodo chiamato per la pagina "Archivio" delle società partecipate
     * ID sezione 92
     *
     * @return void
     * @throws Exception
     */
    public function archivedParticipatedCompanies(): void
    {
        $this->indexParticipatedCompanies();
    }

    /**
     * Metodo chiamato per la pagina "Archivio" degli Enti di diritto privato controllati
     * ID sezione 94
     *
     * @return void
     * @throws Exception
     */
    public function archivedControlledPrivateEntities(): void
    {
        $this->indexControlledPrivateEntities();
    }
}
