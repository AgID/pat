<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Helpers\Table;
use Helpers\Utility\AttachmentArchive;
use Helpers\Utility\Meta;
use Model\ContestModel;
use Model\SectionsFoModel;
use System\Input;

/**
 * Controller pagina front-end Bandi di Concorso
 */
class ContestsFrontController extends BaseFrontController
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
     * Metodo chiamato per la pagina "Bandi di concorso",
     * nella sezione dei Bandi di concorso
     * ID sezione 5
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function index(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = uri()->segment(2, 0);

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'section_fo.id', 'parent_id', 'controller_open_data', 'archive_name',
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

        // Recupero i concorsi attivi da mostrare
        $contests = ContestModel::where('activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
            ->orderBy('activation_date', 'DESC')
            ->paginate(20, ['id', 'object', 'activation_date', 'expiration_date'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['object', 'start_p_date', 'end_p_date', 'start_s_date', 'end_s_date', 'sec_token']))
            ->setPath(currentUrl());

        $contests = !empty($contests) ? $contests->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($contests['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ContestModel::select(['created_at'])
                ->where('activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ContestModel::select(['updated_at'])
                ->where('activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($contests);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['table'] = !empty($table) ? $table->generate() : null;
        $data['instances'] = $contests;
        $data['instance'] = $currentPage;
        $data['paragraphs'] = $contents;
        $data['allowSearch'] = true;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/contests/contests', $data, 'frontend');
    }

    /**
     * Metodo che crea la tabella con i dati dei risultati della ricerca da mostrare
     *
     * @param null $contests {dati da inserire nella tabella}
     * @param string $typology Tipologia dell'elemento (concorso, esito, avviso)
     * @return Table|null
     * @throws Exception
     */
    private function createTableRows($contests = null, string $typology = ''): ?Table
    {

        $currentPageId = uri()->segment(2, 0);
        $table = null;

        if (!empty($contests['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();
            $column = ['Oggetto', 'Data di pubblicazione'];

            if ($typology !== 'esito') {
                $column[] = 'Data di scadenza';
            }

            $table->set_heading($column);

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($contests['data'] as $contest) {

                $row = [
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $contest['id'] . '/' . urlTitle($contest['object'])) . '" data-id="' . $contest['id'] . ' ">' . escapeXss($contest['object']) . '</a>',
                    !empty($contest['activation_date']) ? date('d-m-Y', strtotime($contest['activation_date'])) : null,
                ];

                if ($typology !== 'esito') {
                    $row[] = !empty($contest['expiration_date']) ? date('d-m-Y', strtotime($contest['expiration_date'])) : null;
                }

                // Aggiungo le righe
                $table->add_row($row);
            }
        }

        return $table;
    }

    /**
     * Metodo chiamato per la pagina "Concorsi attivi",
     * nella sezione dei Bandi di concorso
     * Id sezione 75
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexActive(): void
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

        // Recupero i concorsi attivi da mostrare
        $contests = ContestModel::where('typology', 'concorso')
            ->where('activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
            ->where(
                function ($query) {
                    $query->where('expiration_date', '>=', date('Y-m-d H:i:s'))->orWhereNull('expiration_date');
                }
            )
            ->orderBy('expiration_date', 'ASC')
            ->paginate(20, ['id', 'object', 'activation_date', 'expiration_date'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['object', 'start_p_date', 'end_p_date', 'start_s_date', 'end_s_date', 'sec_token']))
            ->setPath(currentUrl());

        $contests = !empty($contests) ? $contests->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($contests['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ContestModel::select(['created_at'])
                ->where('typology', 'concorso')
                ->where('activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->where(
                    function ($query) {
                        $query->where('expiration_date', '>=', date('Y-m-d H:i:s'))->orWhereNull('expiration_date');
                    }
                )
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ContestModel::select(['updated_at'])
                ->where('typology', 'concorso')
                ->where('activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->where(
                    function ($query) {
                        $query->where('expiration_date', '>=', date('Y-m-d H:i:s'))->orWhereNull('expiration_date');
                    }
                )
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($contests, 'concorso');

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['table'] = !empty($table) ? $table->generate() : null;
        $data['instances'] = $contests;
        $data['instance'] = $currentPage;
        $data['paragraphs'] = $contents;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/contests/contests', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Concorsi scaduti",
     * nella sezione dei Bandi di concorso
     * Id sezione 76
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexExpired(): void
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

        // Data del giorno successivo al corrente
        $tomorrow = date("Y-m-d H:i:s", strtotime('tomorrow'));

        // Recupero i concorsi scaduti da mostrare
        $contests = ContestModel::where('typology', 'concorso')
            ->where('activation_date', '<', $tomorrow)
            ->where('expiration_date', '<', date('Y-m-d H:i:s'))
            ->orderBy('expiration_date', 'ASC')
            ->paginate(20, ['id', 'object', 'activation_date', 'expiration_date'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['object', 'start_p_date', 'end_p_date', 'start_s_date', 'end_s_date', 'sec_token']))
            ->setPath(currentUrl());

        $contests = !empty($contests) ? $contests->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($contests['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ContestModel::select(['created_at'])
                ->where('typology', 'concorso')
                ->where('activation_date', '<', $tomorrow)
                ->where('expiration_date', '<', date('Y-m-d H:i:s'))
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ContestModel::select(['updated_at'])
                ->where('typology', 'concorso')
                ->where('activation_date', '<', $tomorrow)
                ->where('expiration_date', '<', date('Y-m-d H:i:s'))
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($contests, 'concorso');

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['table'] = !empty($table) ? $table->generate() : null;
        $data['instances'] = $contests;
        $data['instance'] = $currentPage;
        $data['paragraphs'] = $contents;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/contests/contests', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Avvisi",
     * nella sezione dei Bandi di concorso
     * Id sezione 77
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexAlert(): void
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

        // Recupero gli avvisi da mostrare
        $contests = ContestModel::where('typology', 'avviso')
            ->where('activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
            ->orderBy('activation_date', 'DESC')
            ->paginate(20, ['id', 'object', 'activation_date', 'expiration_date'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['object', 'start_p_date', 'end_p_date', 'start_s_date', 'end_s_date', 'sec_token']))
            ->setPath(currentUrl());

        $contests = !empty($contests) ? $contests->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($contests['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ContestModel::select(['created_at'])
                ->where('typology', 'avviso')
                ->where('activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ContestModel::select(['updated_at'])
                ->where('typology', 'avviso')
                ->where('activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($contests, 'avviso');

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['table'] = !empty($table) ? $table->generate() : null;
        $data['instances'] = $contests;
        $data['instance'] = $currentPage;
        $data['paragraphs'] = $contents;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/contests/contests', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Esiti",
     * nella sezione dei Bandi di concorso
     * Id sezione 78
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexResult(): void
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

        // Recupero gli esiti da mostrare
        $contests = ContestModel::where('typology', 'esito')
            ->where('activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
            ->orderBy('activation_date', 'DESC')
            ->paginate(20, ['id', 'object', 'activation_date'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['id', 'object', 'sec_token', 'start_p_date', 'end_p_date']))
            ->setPath(currentUrl());

        $contests = !empty($contests) ? $contests->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($contests['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ContestModel::select(['created_at'])
                ->where('typology', 'esito')
                ->where('activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ContestModel::select(['updated_at'])
                ->where('typology', 'esito')
                ->where('activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($contests, 'esito');

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['table'] = !empty($table) ? $table->generate() : null;
        $data['instances'] = $contests;
        $data['instance'] = $currentPage;
        $data['paragraphs'] = $contents;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/contests/contests', $data, 'frontend');
    }

    /**
     * Metodo che ritorna i dati da mostrare nella pagina di dettaglio di un bando di concorso
     * @return void
     * @throws Exception
     */
    public function details(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = uri()->segment(2, 0);
        $elementId = uri()->segment(4, 0);

        // Recupero l'elemento da mostrare
        $element = ContestModel::select(['object_contest.*'])
            ->where('object_contest.id', $elementId)
            ->with('assignments:id,name,object,assignment_type')
            ->with(['office' => function ($query) {
                $query->select(['id', 'structure_name']);
            }])
            ->with(['related_contest' => function ($query) {
                $query->select(['id', 'object', 'typology', 'expiration_date', 'expiration_time']);
            }])
            ->with(['alerts' => function ($query) {
                $query->select(['id', 'object', 'related_contest_id']);
            }])
            ->with(['outcomes' => function ($query) {
                $query->select(['id', 'object', 'related_contest_id']);
            }])
            ->with(['relative_measure' => function ($query) {
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
        $data['pageName'] = $element['object'];
        $data['menuPages'] = $sectionFO;
        $data['currentPageId'] = $currentPageId;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['editRecord'] = true;

        // aggiungi al Breadcrumbs la pagina del dettaglio
        $data['concatBreadcrumb'] = true;
        $data['bread'] = array();
        $data['bread'][] = array('name' => $element['object'], 'link' => '/');

        $data['instance'] = $element;

        $label = 'contest';
        $elementId = $element['id'];
        $selectFields = ['*'];

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

        renderFront(config('vfo', null, 'app') . '/contests/details', $data, 'frontend');
    }
}
