<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

 /** @noinspection PhpUnused */



namespace Http\Web\Front;

use Exception;
use Helpers\FormBuilder;
use Helpers\Table;
use Helpers\Utility\AttachmentArchive;
use Model\PersonnelModel;
use Model\RelPersonnelForStructuresModel;
use Model\SectionsFoModel;
use Model\StructuresModel;
use System\Input;
use System\Validator;

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

        // Validatore per i filtri di ricerca
        $validation = new Validator();
        if (Input::get('str') !== null && Input::get('resp') != null) {
            $validation->verifyToken();
        }

        $validation->label('Nome ufficio')->value(Input::get('str'))->isInt()->end();
        $validation->label('Responsabile')->value(Input::get('resp'))->isInt()->end();

        if (!$validation->isSuccess()) {

            echo showError('Attenzione', $validation->getErrorsHtml(), $validation->getErrorStatusCode());
            die();
        }

        // Id della pagina corrente
        $currentPageId = getCurrentPageId();

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

        //Strutture per il filtro di ricerca
        $offices = [null => 'Seleziona una struttura'] + StructuresModel::where('articulation', 1)
                ->where(function ($query) {
                })
                ->orderBy('structure_name', 'ASC')
                ->pluck('structure_name', 'id')
                ->toArray();

        // Responsabili per il filtro
        $responsibleIds = RelPersonnelForStructuresModel::select('object_personnel_id')
            ->where('typology', '=', 'responsible')
            ->groupBy('object_personnel_id')
            ->get()
            ->toArray();
        $responsibles = [null => 'Seleziona un responsabile'] + PersonnelModel::select(['id', 'full_name'])
                ->where(function ($query) {
                })
                ->whereIn('id', $responsibleIds)
                ->orderBy('full_name', 'ASC')
                ->pluck('full_name', 'id')
                ->toArray();

        // Recupero le strutture organizzative dell'ente che hanno il campo "Utilizza in articolazione uffici" settato a 1
        $structures = StructuresModel::where('articulation', 1)
            ->with('responsibles:id,full_name')
            ->filter(
                (int)Input::get('str', true),
                (int)Input::get('resp', true)
            )
            ->where(function ($query) {
            })
            ->groupBy('object_structures.id')
            ->orderBy('order')
            ->orderBy('structure_name', 'ASC')
            ->paginate(9, ['object_structures.id', 'structure_name', 'phone', 'archived', 'reference_email', 'email_not_available_txt', 'email_not_available',
                'address', 'ad_interim', 'referent_not_available_txt'], 'p', boundedQueryInt('p', 10000))
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

        // Form per i filtri di ricerca
        $this->setSearchFilter($offices, $responsibles, $data);

        $formFilterFields = [
            'str' => Input::get('str', true),
            'resp' => Input::get('resp', true),
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value){
            if(!empty($value)){
                $countFilterFields +=1;
            }
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Dati passati alla vista
        $data['allowSearch'] = true;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['instances'] = $structures;
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['currentPageId'] = $currentPageId;
        $data['instance'] = $currentPage;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['searchFieldsOpenData'] = $formFilterFields;
        $data['filterUsed'] = $countFilterFields;
        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        //Inclusione di eventuali librerie esterne
        $currentPage = $currentPage ?? null;
        $data = $data ?? null;
        $data = $data ?? null;
        renderFront(config('vfo', null, 'app') . '/structures/structures', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Organigramma"
     * Id sezione 41
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function organigram(): void
    {
        $this->pivot();
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

        // Validatore per i filtri di ricerca
        $validation = new Validator();
        if (Input::get('str') != null ||
            Input::get('resp') != null
        ) {
            $validation->verifyToken();
        }
        $validation->label('Nome ufficio')->value(Input::get('str'))->isInt()->end();
        $validation->label('Responsabile')->value(Input::get('resp'))->isInt()->end();

        if (!$validation->isSuccess()) {
            echo showError('Attenzione', $validation->getErrorsHtml(), $validation->getErrorStatusCode());
            die();
        }

        // Id della pagina corrente
        $currentPageId = getCurrentPageId();

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

        //Strutture per il filtro di ricerca
        $offices = [null => 'Seleziona una struttura'] + StructuresModel::where('articulation', 1)
                ->where(function ($query) {
                })
                ->orderBy('structure_name', 'ASC')
                ->pluck('structure_name', 'id')
                ->toArray();

        // Responsabili per il filtro
        $responsibleIds = RelPersonnelForStructuresModel::select('object_personnel_id')
            ->groupBy('object_personnel_id')
            ->get()
            ->toArray();
        $responsibles = [null => 'Seleziona un responsabile'] + PersonnelModel::select(['id', 'full_name'])
                ->where(function ($query) {
                })
                ->whereIn('id', $responsibleIds)
                ->orderBy('full_name', 'ASC')
                ->pluck('full_name', 'id')
                ->toArray();

        // Recupero le strutture organizzative dell'ente che hanno il campo "Utilizza in articolazione uffici" settato a 1
        $structures = StructuresModel::where('articulation', 1)
            ->where(function ($query) {
            })
            ->with('responsibles:id,full_name')
            ->filter(
                (int)Input::get('str', true),
                (int)Input::get('resp', true)
            )
            ->orderBy('order', 'ASC')
            ->orderBy('structure_name', 'ASC')
            ->paginate(20, ['object_structures.id', 'structure_name', 'phone', 'reference_email', 'certified_email', 'email_not_available_txt', 'address', 'ad_interim', 'referent_not_available_txt'], 'p', boundedQueryInt('p', 10000))
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

        // Form per i filtri di ricerca
        $this->setSearchFilter($offices, $responsibles, $data);

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

        // Recupero le strutture organizzative dell'ente che hanno il campo "Utilizza in articolazione uffici" settato a 1
        $structures = StructuresModel::where('articulation', 1)
            ->whereNotNull('certified_email')
            ->where(function ($query) {
            })
            ->with('responsibles:id,full_name')
            ->filter(
                (int)Input::get('str', true),
                (int)Input::get('resp', true)
            )
            ->orderBy('structure_name', 'ASC')
            ->paginate(20, ['object_structures.id', 'structure_name', 'phone', 'reference_email', 'certified_email', 'email_not_available_txt',
                'address', 'ad_interim', 'referent_not_available_txt'], 'p', boundedQueryInt('p', 10000))
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
        $element = StructuresModel::where('id', $elementId);

        $element = $element->with('responsibles:id,full_name,archived')
            ->with('referents:id,full_name,archived')
            ->with('to_contact:id,full_name,archived')
            ->with('structure_of_belonging:id,structure_name,archived')
            ->with('management_structure:id,structure_name,archived')
            ->with('non_management_structure:id,structure_name,archived')
            ->with('normatives:id,name')
            ->with(['regulations' => function ($query) {
                $query->select(['object_regulations.id', 'title', 'public.public_in_id'])
                    ->join('rel_regulations_public_in as public', 'public.object_regulation_id', '=', 'object_regulations.id');
            }])
            ->with('valid_normatives:id,name')
            ->with(['proceedings' => function ($query) {
                $query->where('rel_proceedings_structures.typology', '=', 'office-responsible');
            }]);

        $subStructures = optional(StructuresModel::select(['id', 'structure_name', 'structure_of_belonging_id', 'non_management_structure_id', 'management_structure_id'])
            ->where(function ($query) use($elementId) {
                //nuova struttura - struttura aggiornata
                $query->where(function ($query) use($elementId) {
                    $query->where('non_management_structure_id', $elementId)
                        ->orWhere('management_structure_id', $elementId);
                });
            })
            ->orWhere(function ($query) use($elementId) {
                //vecchia struttura non aggiornata
                $query->where('structure_of_belonging_id', $elementId)
                    ->where(function ($query) {
                        $query->whereNull('non_management_structure_id')
                            ->whereNull('management_structure_id');
                    });
            })
            ->orderBy('structure_name')
            ->get())
            ->toarray();

        $element = $element->first();

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

        $element['sub_structures'] = $subStructures;
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
        $sectionId = (int)uri()->segment(2, 0);

            // Allegati
            $attach = new AttachmentArchive();
            $data['listAttach'] = $attach->getAllByObject(
                $label,
                $elementId,
                $selectFields,
                true
            );

        $data = $data ?? null;

        //Inclusione di eventuali librerie esterne
        $data = $data ?? null;

        //Inclusione del nodo JSON-LD della pagina
        $data = $data ?? null;

        renderFront(config('vfo', null, 'app') . '/structures/details', $data, 'frontend');
    }

    /**
     * Funzione per settare i filtri di ricerca di una pagina
     * @param array $offices      Strutture per il filtro
     * @param array $responsibles Responsabili delle strutture
     * @param array $data         Array di dati da passare alla vista
     * @return void
     * @throws Exception
     */
    protected function setSearchFilter(array $offices, array $responsibles, array &$data): void
    {
        $form = json_encode([
            [
                'type' => 'form_open',
                'name' => 'absence_rates',
                'action' => '{current_url}',

                'attributes' => [
                    'method' => 'get',
                ]
            ],
            [
                'type' => 'tag',
                'content' => '<div class="row my-4">',
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-6">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{structure}',
                'name' => 'str',
                'extra' => 'id="office"',
                'label' => [
                    'for' => 'o',
                    'text' => 'Ufficio',
                ],
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-6">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{responsible}',
                'name' => 'resp',
                'extra' => 'id="responsible"',
                'label' => [
                    'for' => 'r',
                    'text' => 'Responsabile',
                ],
            ],
            [
                'type' => 'tag',
                'content' => '</div>',
            ],
            [

                'type' => 'form_button',
                'attributes' => [
                    'class' => 'btn btn-primary',
                    'name' => 'search',
                    'type' => 'submit',
                    'content' => '<span class="fas fa-search"></span> Cerca'
                ]
            ],
            [
                'type' => 'form_button',
                'attributes' => [
                    'class' => 'btn btn-secondary',
                    'id' => '__reset_form',
                    'name' => 'reset',
                    'type' => 'button',
                    'content' => '<span class="far fa-times-circle"></span> Reset'
                ]
            ],
            [
                'type' => 'form_hidden',
                'attributes' => [
                    'class' => 'btn btn-secondary',
                    'id' => '__redirect_url',
                    'value' => '{redirect_url}',
                ]
            ],
            [
                'type' => 'form_close',
            ],
        ]);

        $formBuilder = new FormBuilder([
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'structure' => FormBuilder::parseOptions($offices, htmlEscape(Input::get('str', true))),
            'responsible' => FormBuilder::parseOptions($responsibles, htmlEscape(Input::get('resp', true)))
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();
    }

    /**
     * Funzione che setta i dati da passare alla vista
     * @param array      $currentPage   Informazioni della pagina corrente
     * @param array      $data          Array dei dati da passare alla vista
     * @param array      $structures    Strutture da passare alla vista
     * @param mixed      $sectionFO     Lista delle pagine da inserire nel menu della pagina
     * @param Table|null $table         Tabella dei dati da mostrare nella pagina
     * @param int        $currentPageId Id della pagina corrente
     * @return void
     * @throws Exception
     */
    private function setDataPassedToTheView(array $currentPage, array $data, array $structures, mixed $sectionFO, ?Table $table, int $currentPageId): void
    {
        // Table::generate() svuota la tabella (Table::clear() in coda): va chiamata una volta sola
        // per istanza, altrimenti la seconda chiamata restituisce 'Undefined table data'. Genero
        // qui l'HTML una volta e riuso la stringa sia per il frammento sia per la pagina intera.
        $tableHtml = !empty($table) ? $table->generate() : $table;

        $data['instances'] = $structures;
        $data['table'] = $tableHtml;

        // Richiesta del solo frammento (paginazione AJAX): tabella e paginazione sono pronte,
        // tutto il resto serve solo alla pagina completa e viene saltato. Essendo questo metodo
        // condiviso da indexPhoneMail e indexCertifiedMail, un solo controllo qui copre entrambe.
        if (renderFrontFragment($data)) {

            return;

        }

        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['allowSearch'] = true;
        //Abilita la paginazione AJAX della tabella dei risultati (vedi renderFrontFragment)
        $data['ajaxPagination'] = true;
        // Recupero il contenuto della pagina e i richiami dei vari paragrafi
        $contents = getPageContents($currentPageId);

        $data['paragraphs'] = $contents;

        $this->setDataForView($table, $data, $currentPageId, $currentPage);

        // setDataForView() richiama $table->generate() su una tabella gia' consumata: ripristino
        // l'HTML generato sopra.
        $data['table'] = $tableHtml;

        $data = $data ?? null;

        renderFront(config('vfo', null, 'app') . '/structures/structures_table', $data, 'frontend');
    }

    /**
     * Funzione che setta i dati utili alla vista
     * @param Table|null $table         Tabella dei dati da mostrare nella pagina
     * @param array      $data          Array dei dati da passare alla vista
     * @param int        $currentPageId Id della pagina corrente
     * @param array      $currentPage   Informazioni della pagina corrente
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

        //Inclusione di eventuali librerie esterne
        $currentPage = $currentPage ?? null;
        $data = $data ?? null;
    }
}
