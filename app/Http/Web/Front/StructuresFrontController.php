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
use Model\SectionsFoModel;
use Model\StructuresModel;
use System\Input;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller pagina front-end Articolazione degli uffici
 */
class StructuresFrontController extends BaseFrontController
{

    /**
     * Costruttore
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        helper('url');
        helper('StructureOrganigramHelper');

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
     * Metodo chiamato per la pagina "Articolazione degli uffici"
     * Id sezione 40
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function index(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = getCurrentPageId();

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

        // Recupero le strutture organizzative dell'ente che hanno il campo "Utilizza in articolazione uffici" settato a 1
        $structures = StructuresModel::with('responsibles:id,full_name')
            ->groupBy('object_structures.id')
            ->orderBy('structure_name', 'ASC')
            ->paginate(9, ['object_structures.id', 'structure_name', 'phone', 'archived', 'reference_email', 'email_not_available_txt',
                'address', 'ad_interim', 'referent_not_available_txt'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            //->appends(Input::get(['operator', 'service', 'type', 'municipal', 'service', 'start', 'end', 'customer', 'per_page']))
            ->setPath(currentUrl());

        $structures = !empty($structures) ? $structures->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($structures['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(StructuresModel::select(['created_at'])
                ->where('articulation', 1)
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(StructuresModel::select(['updated_at'])
                ->where('articulation', 1)
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero le voci per costruire il menu dei contenuti
        $sectionFO = getRightOrBottomMenu($currentPageId, $currentPage['parent_id']);

        // Dati passati alla vista
        $data['pageName'] = $currentPage['name'];
        $data['instances'] = $structures;
        $data['menuPages'] = $sectionFO;
        $data['currentPageId'] = $currentPageId;
        $data['instance'] = $currentPage;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/structures/structures', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Telefono e posta elettronica"
     * Id sezione 43
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     * @noinspection DuplicatedCode
     */
    public function indexPhoneMail(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = getCurrentPageId();

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'section_fo.id', 'parent_id', 'form_filter', 'controller_open_data', 'archive_name',
            'section_fo.created_at', 'section_fo.updated_at', 'is_system'])
            ->where('section_fo.id', $currentPageId)
            ->with(['contents' => function ($query) {
                $query->select(['id', 'created_at', 'updated_at', 'section_fo_id'])
                    ->orderBy('updated_at', 'DESC')
                    ->orderBy('created_at', 'DESC')
                    ->first();
            }]);

        $currentPage = $currentPage->first()
            ->toArray();

        // Recupero le strutture organizzative dell'ente che hanno il campo "Utilizza in articolazione uffici" settato a 1
        $structures = StructuresModel::with('responsibles:id,full_name')
            ->orderBy('structure_name', 'ASC')
            ->paginate(20, ['object_structures.id', 'structure_name', 'phone', 'reference_email', 'certified_email', 'email_not_available_txt', 'address', 'ad_interim', 'referent_not_available_txt'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['str', 'resp', 'sec_token']))
            ->setPath(currentUrl());

        $structures = !empty($structures) ? $structures->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($structures['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(StructuresModel::select(['created_at'])
                ->where('articulation', 1)
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(StructuresModel::select(['updated_at'])
                ->where('articulation', 1)
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero le voci per costruire il menu dei contenuti
        $sectionFO = getRightOrBottomMenu($currentPageId, $currentPage['parent_id']);

        $table = null;

        if (!empty($structures['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();

            $table->set_heading('Struttura organizzativa', 'Email', 'PEC', 'Telefono');

            // Creo le righe della tabella settando i dati da mostrare
            foreach ($structures['data'] as $structure) {
                $table->add_row(
                    '<a href="' . siteUrl('page/40/details/' . $structure['id'] . '/' . urlTitle($structure['structure_name'])) . '" data-id="' . $structure['id'] . ' ">' . escapeXss($structure['structure_name']) . '</a>',
                    !empty($structure['reference_email']) ? '<a href="mailto:' . $structure['reference_email'] . '">' . $structure['reference_email'] . '</a>' : null,
                    !empty($structure['certified_email']) ? '<a href="mailto:' . $structure['certified_email'] . '">' . $structure['certified_email'] . '</a>' : null,
                    !empty($structure['phone']) ? '<a href="tel:' . $structure['phone'] . '">' . $structure['phone'] . '</a>' : null,
                );
            }
        }

        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        // Dati passati alla vista
        $this->setDataPassedToTheView($currentPage, $data, $structures, $sectionFO, $table, $currentPageId);
    }

    /**
     * Metodo chiamato per la pagina "Posta elettronica certificata"
     * Id sezione 44
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexCertifiedMail(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = getCurrentPageId();

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'section_fo.id', 'parent_id', 'form_filter', 'controller_open_data',
            'archive_name', 'section_fo.created_at', 'section_fo.updated_at', 'is_system'])
            ->where('section_fo.id', $currentPageId)
            ->with(['contents' => function ($query) {
                $query->select(['id', 'created_at', 'updated_at', 'section_fo_id'])
                    ->orderBy('updated_at', 'DESC')
                    ->orderBy('created_at', 'DESC')
                    ->first();
            }]);

        $currentPage = $currentPage->first()
            ->toArray();

        // Recupero le strutture organizzative dell'ente che hanno il campo "Utilizza in articolazione uffici" settato a 1
        $structures = StructuresModel::where('articulation', 1)
            ->whereNotNull('certified_email')
            ->with('responsibles:id,full_name')
            ->orderBy('structure_name', 'ASC')
            ->paginate(20, ['object_structures.id', 'structure_name', 'phone', 'reference_email', 'certified_email', 'email_not_available_txt',
                'address', 'ad_interim', 'referent_not_available_txt'], 'p', (int)Input::get('p'))
            ->onEachSide(2)
            ->appends(Input::get(['str', 'resp', 'sec_token']))
            ->setPath(currentUrl());

        $structures = !empty($structures) ? $structures->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($structures['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(StructuresModel::select(['created_at'])
                ->where('articulation', 1)
                ->whereNotNull('certified_email')
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(StructuresModel::select(['updated_at'])
                ->where('articulation', 1)
                ->whereNotNull('certified_email')
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero le voci per costruire il menu dei contenuti
        $sectionFO = getRightOrBottomMenu($currentPageId, $currentPage['parent_id']);

        $table = null;

        if (!empty($structures['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();

            $table->set_heading('Struttura organizzativa', 'PEC');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($structures['data'] as $structure) {
                $table->add_row(
                    '<a href="' . siteUrl('page/40/details/' . $structure['id'] . '/' . urlTitle($structure['structure_name'])) . '" data-id="' . $structure['id'] . ' ">' . escapeXss($structure['structure_name']) . '</a>',
                    !empty($structure['certified_email']) ? '<a href="mailto:' . $structure['certified_email'] . '">' . escapeXss($structure['certified_email']) . '</a>' : null,
                );
            }
        }

        // Dati passati alla vista
        $this->setDataPassedToTheView($currentPage, $data, $structures, $sectionFO, $table, $currentPageId);
    }

    /**
     * Metodo chiamato per la pagina "Archivio" delle Strutture organizzative
     * ID sezione 42
     *
     * @param StructuresModel|null $element Struttura
     * @return void
     * @throws Exception
     * @url /page/42/archivio.html
     */
    public function archive(null|StructuresModel $element = null): void
    {
        $this->pivot();
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
        $currentPageId = getCurrentPageId();
        $elementId = (int)uri()->segment(4, 0);

        // Recupero l'elemento da mostrare
        $element = StructuresModel::where('id', $elementId)
            ->with('responsibles:id,full_name,archived')
            ->with('referents:id,full_name,archived')
            ->with('to_contact:id,full_name,archived')
            ->with('structure_of_belonging:id,structure_name,archived')
            ->with('normatives:id,name')
            ->with(['regulations' => function ($query) {
                $query->select(['object_regulations.id', 'title', 'public.public_in_id'])
                    ->join('rel_regulations_public_in as public', 'public.object_regulation_id', '=', 'object_regulations.id');
            }])
            ->with('valid_normatives:id,name')
            ->with(['proceedings' => function ($query) {
                $query->where('rel_proceedings_structures.typology', '=', 'office-responsible');
            }])
            ->with('sub_structures:id,structure_name,structure_of_belonging_id')
            ->first();

        // Se l'elemento non esiste mostro la pagina di errore
        if (empty($element)) {
            echo show404('Ops..', 'record not found');
            exit();
        }

        // Se l'elemento è archiviato mostro la vista apposita per gli elementi archiviati
        if ($element->archived) {
            $this->archive($element);
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
        $data['pageName'] = !empty($element['structure_name']) ? $element['structure_name'] : null;
        $data['menuPages'] = $sectionFO;

        // aggiungi al Breadcrumbs la pagina del dettaglio
        $data['concatBreadcrumb'] = true;
        $data['bread'] = array();
        $data['bread'][] = array('name' => $element['structure_name'], 'link' => '/');
        $data['instance'] = $element;
        $data['currentPageId'] = $currentPageId;

        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['editRecord'] = true;

        $label = 'structures';
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
            ->setDcterms(Meta::dctermsTitle, $element['structure_name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $element['structure_name'])
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/structures/details', $data, 'frontend');
    }

    /**
     * Funzione che setta i dati da passare alla vista
     * @param array $currentPage Informazioni della pagina corrente
     * @param array $data Array dei dati da passare alla vista
     * @param array $structures Strutture da passare alla vista
     * @param mixed $sectionFO Lista delle pagine da inserire nel menu della pagina
     * @param Table|null $table Tabella dei dati da mostrare nella pagina
     * @param int $currentPageId Id della pagina corrente
     * @return void
     * @throws Exception
     */
    private function setDataPassedToTheView(array $currentPage, array $data, array $structures, mixed $sectionFO, ?Table $table, int $currentPageId): void
    {
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['instances'] = $structures;
        $data['menuPages'] = $sectionFO;
        $this->setDataForView($table, $data, $currentPageId, $currentPage);

        renderFront(config('vfo', null, 'app') . '/structures/structures_table', $data, 'frontend');
    }

    /**
     * Funzione che setta i dati utili alla vista
     * @param Table|null $table Tabella dei dati da mostrare nella pagina
     * @param array $data Array dei dati da passare alla vista
     * @param int $currentPageId Id della pagina corrente
     * @param array $currentPage Informazioni della pagina corrente
     * @return void
     */
    protected function setDataForView(?Table $table, array &$data, int $currentPageId, array $currentPage): void
    {
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['currentPageId'] = $currentPageId;
        $data['instance'] = $currentPage;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;

        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, $currentPage['name'])
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - ' . $currentPage['name'])
            ->toHtml();
    }
}
