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
use Model\PersonnelModel;
use Model\SectionsFoModel;
use System\Input;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller pagina front-end Personale
 */
class PersonnelFrontController extends BaseFrontController
{
    /**
     * @description Costruttore
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        helper(['url', 'image']);
    }

    /**
     * @description Metodo per la pagina di snodo del personale
     * @return void
     * @throws Exception
     */
    public function pivot(): void
    {
        $pivot = new PivotController();
        $pivot->index();
    }

    /**
     * @description Metodo chiamato per la pagina "Dotazione organica",
     * nella sezione del Personale
     * ID sezione 64
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexStaffing(): void
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

        // Recupero i dati sul personale da pubblicare nella pagina secondo i suoi criteri di pubblicazione
        $personnel = PersonnelModel::where('personnel_lists', 1)
            ->with('referent_structures:id,structure_name')
            ->orderBy('full_name', 'ASC')
            ->paginate(20, ['id', 'full_name'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['n', 'sec_token']))
            ->setPath(currentUrl());

        $personnel = !empty($personnel) ? $personnel->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($personnel['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(PersonnelModel::select(['created_at'])
                ->where('personnel_lists', 1)
                ->where('archived', '!=', 1)
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(PersonnelModel::select(['updated_at'])
                ->where('personnel_lists', 1)
                ->where('archived', '!=', 1)
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($personnel);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $personnel;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/personnel/personnel', $data, 'frontend');
    }

    /**
     * @description Metodo chiamato per la pagina "Personale non a tempo indeterminato",
     * nella sezione del Personale
     * ID sezione 65
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexNotIndefinite(): void
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

        // Recupero i dati del personale da pubblicare nella pagina in base ai suoi criteri di pubblicazione
        $personnel = PersonnelModel::where('personnel_lists', 1)
            ->where('archived', '!=', 1)
            ->where('determined_term', 1)
            ->with('referent_structures:id,structure_name')
            ->orderBy('full_name', 'ASC')
            ->paginate(20, ['id', 'full_name'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['n', 'sec_token']))
            ->setPath(currentUrl());
        $personnel = !empty($personnel) ? $personnel->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($personnel['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(PersonnelModel::select(['created_at'])
                ->where('archived', '!=', 1)
                ->where('determined_term', 1)
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(PersonnelModel::select(['updated_at'])
                ->where('archived', '!=', 1)
                ->where('determined_term', 1)
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($personnel);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $personnel;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/personnel/personnel', $data, 'frontend');
    }

    /**
     * @description Metodo chiamato per la pagina "Archivio personale",
     * nella sezione del Personale
     * ID sezione 73
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexArchive(): void
    {
        $this->pivot();
    }

    /**
     * @description Metodo chiamato per la pagina "Titolari di incarichi dirigenziali (dirigenti non generali)",
     * nella sezione del Personale
     * ID sezione 60
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexManagerialPositions(): void
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
        $personnel = $this->getDataResultsForPublicIn('60', null, $data);
        $personnel = !empty($personnel) ? $personnel->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($personnel);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $personnel;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/personnel/personnel', $data, 'frontend');
    }

    /**
     * @description Metodo chiamato per la pagina "Posizioni organizzative",
     * nella sezione del Personale
     * ID sezione 63
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexOrganisationalPositions(): void
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
        $personnel = $this->getDataResultsForPublicIn('63', null, $data);

        $personnel = !empty($personnel) ? $personnel->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($personnel);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $personnel;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/personnel/personnel', $data, 'frontend');
    }

    /**
     * @description Metodo chiamato per la pagina "Titolari di incarichi dirigenziali amministrativi di vertice",
     * nella sezione del Personale
     * ID sezione 58
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexTopPositions(): void
    {
        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        $data = [];

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
        $personnel = $this->getDataResultsForPublicIn('58', 9, $data);
        $personnel = !empty($personnel) ? $personnel->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = null;
        $data['instances'] = $personnel;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = true;
        $data['councilAndCouncillors'] = false;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/personnel/personnel_cards_view', $data, 'frontend');
    }

    /**
     * @description Metodo chiamato per la pagina "Dirigenti cessati",
     * nella sezione del Personale
     * ID sezione 61
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexExecutivesTerminated(): void
    {
        $this->pivot();
    }

    /**
     * @description Metodo chiamato per la pagina "Consiglio Comunale",
     * nella sezione Organizzazione -> Titolari di incarichi politici, di amministrazione, di direzione o di governo
     * ID sezione 241
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexCityCouncil(): void
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
        $personnel = $this->getDataResultsForPublicIn('241', 9, $data);
        $personnel = !empty($personnel) ? $personnel->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = null;
        $data['instances'] = $personnel;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = true;
        $data['councilAndCouncillors'] = true;
        $data['cityCouncil'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/personnel/personnel_cards_view', $data, 'frontend');
    }

    /**
     * @description Metodo chiamato per la pagina "Titolari di incarichi politici, di amministrazione, di direzione o di governo",
     * nella sezione Organizzazione
     * ID sezione 37
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexPositionHolders(): void
    {
        //todo questa pagina dovrebbe essere di sola ricerca
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

        // Recupero il Personale da mostrare
        $personnel = PersonnelModel::with('responsible_structures:id,structure_name,archived')
            ->join('rel_personnel_public_in', 'rel_personnel_public_in.object_personnel_id', '=', 'object_personnel.id', 'left outer')
            ->join('rel_personnel_political_organ', 'rel_personnel_political_organ.object_personnel_id', '=', 'object_personnel.id', 'left outer')
            ->where(
                function ($query) {
                    $query->where('rel_personnel_public_in.public_in_id', '246');
                }
            )
            ->groupBy('object_personnel.id')
            ->orderBy('priority', 'ASC')
            ->orderBy('full_name', 'ASC')
            ->paginate(9, ['object_personnel.id', 'full_name', 'email', 'photo', 'role_id'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['n', 'sec_token']))
            ->setPath(currentUrl());

        $personnel = !empty($personnel) ? $personnel->toArray() : [];
        $existPersonnel = !empty($personnel['data']);

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($personnel['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(PersonnelModel::select(['object_personnel.created_at'])
                ->join('rel_personnel_public_in', 'rel_personnel_public_in.object_personnel_id', '=', 'object_personnel.id', 'left outer')
                ->join('rel_personnel_political_organ', 'rel_personnel_political_organ.object_personnel_id', '=', 'object_personnel.id', 'left outer')
                ->where(
                    function ($query) {
                        $query->where('rel_personnel_public_in.public_in_id', '246');
                    }
                )
                ->orderBy('object_personnel.created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(PersonnelModel::select(['object_personnel.updated_at'])
                ->join('rel_personnel_public_in', 'rel_personnel_public_in.object_personnel_id', '=', 'object_personnel.id', 'left outer')
                ->join('rel_personnel_political_organ', 'rel_personnel_political_organ.object_personnel_id', '=', 'object_personnel.id', 'left outer')
                ->where(
                    function ($query) {
                        $query->where('rel_personnel_public_in.public_in_id', '246');
                    }
                )
                ->orderBy('object_personnel.updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = null;
        $data['instances'] = $personnel;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = true;
        $data['councilAndCouncillors'] = false;
        $data['cityCouncil'] = true;
        $data['allowSearch'] = true;
        // Non applica il margine
        $data['noMargineFilterSearch'] = true;
        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . ($existPersonnel ? '/personnel/personnel_cards_view' : '/pivot/pivot'), $data, 'frontend');
    }

    /**
     * @description Metodo chiamato per la pagina "Titolari di incarichi di amministrazione, di direzione o di governo",
     * nella sezione Organizzazione -> Titolari di incarichi politici, di amministrazione, di direzione o di governo
     * ID sezione 246
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexHoldersOfAdministrativePositions(): void
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
        $personnel = $this->getDataResultsForPublicIn('246', 9, $data);
        $personnel = !empty($personnel) ? $personnel->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = null;
        $data['instances'] = $personnel;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = true;
        $data['councilAndCouncillors'] = false;
        $data['cityCouncil'] = false;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/personnel/personnel_cards_view', $data, 'frontend');
    }

    /**
     * @description Metodo chiamato per la pagina "Segretario Generale",
     * nella sezione Personale -> Titolari di incarichi dirigenziali amministrativi di vertice
     * ID sezione 59
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexGeneralSecretary(): void
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
        $personnel = $this->getDataResultsForPublicIn('59', 9, $data);
        $personnel = !empty($personnel) ? $personnel->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = null;
        $data['instances'] = $personnel;
        $data['instance'] = $currentPage;
        $data['councilAndCouncillors'] = false;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/personnel/personnel_cards_view', $data, 'frontend');
    }

    /**
     * @description Metodo chiamato per la pagina "Direzione Generale",
     * nella sezione Organizzazione -> Titolari di incarichi politici, di amministrazione, di direzione o di governo
     * ID sezione 243
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexGeneralManagement(): void
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
        $personnel = $this->getDataResultsForPublicIn(243, 9, $data);
        $personnel = !empty($personnel) ? $personnel->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = null;
        $data['instances'] = $personnel;
        $data['instance'] = $currentPage;
        $data['councilAndCouncillors'] = false;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/personnel/personnel_cards_view', $data, 'frontend');
    }

    /**
     * @description Funzione che ritorna i dati da mostrare nella tabella
     *
     * @param int|null $publicInId La sezioni "pubblica in" per cui filtrare i dati
     * @param int|null $itemPerPage Numero di elementi da mostrare per pagina nella paginazione delle tabelle
     * @param array $data Dati da passare alla vista
     * @return mixed
     * @throws Exception
     */
    private function getDataResultsForPublicIn(int $publicInId = null, int|null $itemPerPage = 20, array &$data = []): mixed
    {

        // Recupero il personale da mostrare nella pagina in base
        $personnel = PersonnelModel::with('referent_structures:id,structure_name')
            ->with('responsible_structures:id,structure_name,archived')
            ->with('role:id,name')
            ->join('rel_personnel_public_in', 'rel_personnel_public_in.object_personnel_id', '=', 'object_personnel.id')
            ->where('rel_personnel_public_in.public_in_id', $publicInId)
            ->where('archived', '!=', 1)
            ->orderBy('priority', 'ASC')
            ->orderBy('full_name', 'ASC')
            ->paginate($itemPerPage, ['object_personnel.id', 'full_name', 'email', 'photo', 'role_id', 'political_role'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['n', 'sec_token']))
            ->setPath(currentUrl());

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($personnel) && !empty($personnel->toArray()['data'])) {

            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(PersonnelModel::select(['object_personnel.created_at'])
                ->join('rel_personnel_public_in', 'rel_personnel_public_in.object_personnel_id', '=', 'object_personnel.id')
                ->where('rel_personnel_public_in.public_in_id', $publicInId)
                ->where('archived', '!=', 1)
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(PersonnelModel::select(['object_personnel.updated_at'])
                ->join('rel_personnel_public_in', 'rel_personnel_public_in.object_personnel_id', '=', 'object_personnel.id')
                ->where('rel_personnel_public_in.public_in_id', $publicInId)
                ->where('archived', '!=', 1)
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];
        return $personnel;
    }

    /**
     * @description Metodo che crea la tabella con i dati dei risultati della ricerca da mostrare
     *
     * @param mixed|null $personnel Dati da inserire nella tabella
     * @param string|null $type Indica se il personale
     *                               è archiviato o meno
     * @return Table|null
     * @throws Exception
     */
    protected function createTableRows(mixed $personnel = null, string $type = null): ?Table
    {
        $currentPageId = (int)uri()->segment(2, 0);
        $table = null;

        if (!empty($personnel['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();
            if ($type == 'archive') {
                $table->set_heading('Nome', 'Ruolo', 'In carica da', 'In carica fino a', 'Contratto tempo determinato');
            } else {
                $table->set_heading('Nome', 'Referente per');
            }

            // Creo le righe della tabella settando i dati da mostrare
            foreach ($personnel['data'] as $p) {

                $rows = [
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $p['id'] . '/' . urlTitle($p['full_name'])) . '" data-id="' . $p['id'] . ' ">' . escapeXss($p['full_name']) . '</a>'
                ];

                // Righe per la pagina archivio
                if ($type == 'archive') {

                    $rows[] = !empty($p['role']) ? escapeXss($p['role']['name']) : null;
                    $rows[] = !empty($p['in_office_since'])
                        ? date('d-m-Y', strtotime($p['in_office_since']))
                        : null;
                    $rows[] = !empty($p['in_office_until'])
                        ? date('d-m-Y', strtotime($p['in_office_until']))
                        : null;
                    $rows[] = !empty($p['determined_term']) ? 'Si' : 'No';
                } else {

                    // Creo link le strutture
                    $structures = escapeXss($p['referent_structures']);
                    $filter = function ($structure) {
                        return '<a href="' . siteUrl('page/40/details/' . $structure['id'] . '/' . urlTitle($structure['structure_name'])) . '">' . escapeXss($structure['structure_name']) . '</a>';
                    };
                    $linkedStructures = array_map($filter, $structures);

                    $rows[] = implode('</br>', $linkedStructures);
                }

                $table->add_row($rows);
            }
        }

        return $table;
    }

    /**
     * @description Funzione che ritorna i dati da mostrare nella pagina di dettaglio
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
        $element = PersonnelModel::where('id', $elementId)
            ->with('role:id,name')
            ->with('referent_structures:id,structure_name,archived')
            ->with('responsible_structures:id,structure_name,archived')
            ->with('commissions:id,name')
            ->with('assignments:id,object,name')
            ->with('measures:id,object')
            ->with('responsibles:id,name,archived')
            ->with('measure_responsibles:id,name,archived')
            ->with('political_organ')
            ->with('historical_datas')
            ->first();

        // Se l'elemento non esiste mostro la pagina di errore
        if (empty($element)) {
            echo show404('Ops..', 'record not found');
            exit();
        }

        $detailPage = '/personnel/details';
        $archiveBread = '';

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'id', 'parent_id', 'archive_name'])
            ->where('id', $currentPageId)
            ->first()
            ->toArray();

        // Recupero le voci per costruire il menu dei contenuti
        $sectionFO = getRightOrBottomMenu($currentPageId, $currentPage['parent_id']);

        // Dati passati alla vista
        $data['pageName'] = $element['full_name'];
        $data['menuPages'] = $sectionFO;

        // aggiungi al Breadcrumbs la pagina del dettaglio
        $data['concatBreadcrumb'] = true;
        $data['bread'] = array();
        if (!empty($archiveBread)) {
            $data['bread'][] = array('name' => 'Archivio personale', 'link' => $archiveBread);
        }
        $data['bread'][] = array('name' => $element['full_name'], 'link' => '/');
        $data['currentPageId'] = $currentPageId;
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['editRecord'] = true;

        $label = 'personnel';
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
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $element['full_name'])
            ->toHtml();

        $data['instance'] = is_array($element) ? $element : $element->toArray();

        renderFront(config('vfo', null, 'app') . $detailPage, $data, 'frontend');
    }
}
