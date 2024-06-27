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
use Model\ProceedingsModel;
use Model\SectionsFoModel;
use System\Input;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller pagina front-end Attività e procedimenti
 */
class ProceedingsFrontController extends BaseFrontController
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
     * Metodo chiamato per la pagina "Monitoraggio tempi procedimentali"
     * ID sezione 100
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexMonitoring(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = uri()->segment(2, 0);

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
        $proceedings = ProceedingsModel::where('public_monitoring_proceeding', 1)
            ->where('archived', '!=', 1)
            ->orWhereNull('archived')
            ->with('offices_responsibles:id,structure_name')
            ->with('monitoring_datas')
            ->orderBy('name', 'asc')
            ->paginate(15, ['id', 'name'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->setPath(currentUrl());

        $proceedings = !empty($proceedings) ? $proceedings->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($proceedings['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ProceedingsModel::select(['created_at'])
                ->where('public_monitoring_proceeding', 1)
                ->where('archived', '!=', 1)
                ->orWhereNull('archived')
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ProceedingsModel::select(['updated_at'])
                ->where('public_monitoring_proceeding', 1)
                ->where('archived', '!=', 1)
                ->orWhereNull('archived')
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = null;

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $proceedings;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = true;
        $data['finalSectionId'] = 100;
        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/proceedings/index_monitoring', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Tipologie di procedimento"
     * ID sezione 98
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexProceedingsType(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = uri()->segment(2, 0);

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

        // Recupero i procedimenti da mostrare
        $proceedings = ProceedingsModel::where('archived', '!=', 1)
            ->orWhereNull('archived')
            ->with('offices_responsibles:id,structure_name')
            ->paginate(20, ['object_proceedings.id', 'name', 'description'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['n', 'str', 'sec_token']))
            ->setPath(currentUrl());

        $proceedings = !empty($proceedings) ? $proceedings->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($proceedings['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ProceedingsModel::select(['created_at'])
                ->where('archived', '!=', 1)
                ->orWhereNull('archived')
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ProceedingsModel::select(['updated_at'])
                ->where('archived', '!=', 1)
                ->orWhereNull('archived')
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($proceedings);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $proceedings;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = false;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/proceedings/proceedings', $data, 'frontend');
    }

    /**
     * Metodo che crea la tabella con i dati dei risultati della ricerca da mostrare
     *
     * @param $proceedings {dati da inserire nella tabella}
     * @return Table|null
     * @throws Exception
     */
    protected function createTableRows($proceedings = null): ?Table
    {
        $currentPageId = uri()->segment(2, 0);
        $table = null;

        if (!empty($proceedings['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();
            $table->set_heading('Procedimento', 'Descrizione', 'Struttura di riferimento');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($proceedings['data'] as $proceeding) {

                // Creo link per le strutture
                $offices = escapeXss($proceeding['offices_responsibles'], true, false);

                $filter = function ($office) {
                    return '<a href="' . siteUrl('page/40/details/' . $office['id'] . '/' . urlTitle($office['structure_name'])) . '">' . escapeXss($office['structure_name']) . '</a>';
                };
                $linkedOffices = array_map($filter, $offices);

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $proceeding['id'] . '/' . urlTitle($proceeding['name'])) . '" data-id="' . $proceeding['id'] . ' ">' . escapeXss($proceeding['name']) . '</a>',
                    !empty($proceeding['description']) ? characterLimiter(strip_tags($proceeding['description'], '<br>'), 210) : null,
                    implode(', ', $linkedOffices)
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
     * @url /page/page_id/details/element_id/element_name
     */
    public function details(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = uri()->segment(2, 0);
        $elementId = uri()->segment(4, 0);
        $getAttachments = true;
        $archiveBread = '';

        // Recupero l'elemento da mostrare
        $element = ProceedingsModel::where('id', $elementId)
            ->with('responsibles:id,full_name,archived')
            ->with('measure_responsibles:id,full_name,archived')
            ->with('substitute_responsibles:id,full_name,archived')
            ->with('offices_responsibles:id,structure_name,archived')
            ->with('to_contacts:id,full_name,archived')
            ->with('other_structures:id,structure_name,archived')
            ->with('modules:id,title')
            ->with(['regulations' => function ($query) {
                $query->select(['object_regulations.id', 'title', 'public.public_in_id as public_in_id'])
                    ->join('rel_regulations_public_in as public', 'public.object_regulation_id', '=', 'object_regulations.id', 'left outer')
                    ->groupBy('public.object_regulation_id');
            }])
            ->with('normatives:id,name')
            ->with('charges:id,title')
            ->with('monitoring_datas')
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

        // Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['editRecord'] = true;

        $detailPage = '/proceedings/details';

        // Aggiungi al Breadcrumbs la pagina del dettaglio
        $data['concatBreadcrumb'] = true;
        $data['bread'] = array();
        if (!empty($archiveBread)) {
            $data['bread'][] = array('name' => 'Archivio procedimenti', 'link' => $archiveBread);
        }
        $data['bread'][] = array('name' => $element['name'], 'link' => '/');
        $data['currentPageId'] = $currentPageId;

        if ($getAttachments) {
            $label = 'proceedings';
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
                ->setDcterms(Meta::dctermsTitle, $element['structure_name'])
                ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $element['name'])
                ->toHtml();
        }

        $data['instance'] = is_array($element) ? $element : $element->toArray();

        renderFront(config('vfo', null, 'app') . $detailPage, $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Archivio" dei Procedimenti
     * ID sezione 99
     *
     * @return void
     * @throws Exception
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
}
