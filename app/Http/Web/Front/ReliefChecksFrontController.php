<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

use Exception;
use Helpers\FormBuilder;
use Helpers\Table;
use Helpers\Utility\AttachmentArchive;
use Model\ReliefChecksModel;
use Model\SectionsFoModel;
use Model\StructuresModel;
use System\Input;
use System\Validator;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller pagina front-end Controlli e Rilievi
 */
class ReliefChecksFrontController extends BaseFrontController
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
     * Metodo per la pagina si snodo dei Controlli e rilievi
     * @return void
     * @throws Exception
     */
    public function pivot(): void
    {
        $pivot = new PivotController();
        $pivot->index();
    }

    /**
     * Metodo chiamato per la pagina
     * "Organismi indipendenti di valutazione, nuclei di valutazione o altri organismi con funzioni analoghe",
     * nella sezione CONTROLLI E RILIEVI SULL'AMMINISTRAZIONE
     * ID sezione 139
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexIndependentOrganisms(): void
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

        // Validazione dei campi di input per la ricerca
        $validator = new Validator();
        if (Input::get('obj') != null ||
            Input::get('year') != null ||
            Input::get('str') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Oggetto')->value(Input::get('obj'))->maxLength(30)->end();
        $validator->label('Anno')->value(Input::get('year'))->isDate('Y')->end();
        $validator->label('Ufficio')->value(Input::get('str'))->isInt()->end();

        if (!$validator->isSuccess()) {
            echo showError('Errore nella validazione dei dati', $validator->getErrorsHtml(), $validator->getErrorStatusCode());
            die();
        }

        // Recupero i controlli rilievi da mostrare in base al campo pubblica in
        $reliefChecks = $this->getDataResults('139', $data);

        $reliefChecks = !empty($reliefChecks) ? $reliefChecks->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Form per i filtri di ricerca
        $form = json_encode([
            [
                'type' => 'form_open',
                'name' => 'relief_check',
                'action' => '{current_url}',
                'attributes' => [
                    'method' => 'get',
                    'class' => 'set-active-label',
                ]
            ],
            [
                'type' => 'tag',
                'content' => '<div class="row mb-4">',
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-12 mb-4">',
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
                'prefix' => '<div class="form-group col-lg-6">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'obj',
                    'id' => 'obj',
                    'value' => '{object}'
                ],
                'label' => [
                    'for' => 'obj',
                    'text' => 'Oggetto',
                ],
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-6">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{year}',
                'name' => 'year',
                'extra' => 'id="year"',
                'label' => [
                    'for' => 'year',
                    'text' => 'Anno',
                    'class' => 'active'
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

        $formFilterFields = [
            'str' => Input::get('str', true),
            'obj' => Input::get('obj', true),
            'year' => Input::get('year', true)
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value) {
            if (!empty($value)) {
                $countFilterFields += 1;
            }
        }
        // Filtro per strutture
        $structureIds = optional(ReliefChecksModel::select(['object_structures_id'])
            ->join('rel_relief_check_public_in', 'rel_relief_check_public_in.relief_check_id', '=', 'object_relief_checks.id')
            ->where('rel_relief_check_public_in.public_in_id', 139)
            ->groupBy('object_structures_id')
            ->get())
            ->toArray();
        $structures = [null => 'Seleziona una struttura'] +
            optional(StructuresModel::whereIn('id', $structureIds)
                ->where(function ($query) {
                })
                ->orderBy('structure_name', 'ASC')
                ->pluck('structure_name', 'id'))
                ->toArray();

        // Anni per la ricerca
        $resultYears = ReliefChecksModel::select('date')
            ->join('rel_relief_check_public_in', 'rel_relief_check_public_in.relief_check_id', '=', 'object_relief_checks.id')
            ->where('rel_relief_check_public_in.public_in_id', '139')
            ->orderBy('date', 'ASC')
            ->first();

        $resultYears = !empty($resultYears) ? ([null => 'Seleziona un anno'] + $resultYears->toArray()) : ['date' => date('Y')];
        $years = [null => 'Seleziona un anno'];
        for ($i = date('Y'); $i >= date('Y', strtotime($resultYears['date'])); $i--) {
            $years[$i] = $i;
        }

        $formBuilder = new FormBuilder([
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'object' => htmlEscape(Input::get('obj')),
            'structure' => FormBuilder::parseOptions($structures, htmlEscape(Input::get('str', true))),
            'year' => FormBuilder::parseOptions($years, htmlEscape(Input::get('year', true)))
        ]);

        //        if (!empty($currentPage['form_filter'])) {
        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();
        //        }

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($reliefChecks);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $reliefChecks;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = !empty($currentPage['no_required']);
        $data['allowSearch'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['searchFieldsOpenData'] = $formFilterFields;
        $data['filterUsed'] = $countFilterFields;
        $data['rangeOpenData'] = 2000;
        //Abilita la paginazione AJAX della tabella dei risultati (vedi renderFrontFragment)
        $data['ajaxPagination'] = true;

        if (empty($data['table'])) {
            $data['allowSearch'] = false;
            $data['table'] = '<p></p>';
        }

        $currentPage = $currentPage ?? null;
        $data = $data ?? null;
        $data = $data ?? null;
        renderFront(config('vfo', null, 'app') . '/relief_checks/relief_checks', $data, 'frontend');
    }

    /**
     * Funzione che ritorna i dati da mostrare nella tabella
     *
     * @param null $type {la tipologia per cui filtrare i dati}
     * @param array $data Dati da passare alla vista
     * @return mixed
     * @throws Exception
     */
    private function getDataResults($type = null, array &$data = []): mixed
    {

        // Recupero i canoni di locazione percepiti da mostrare
        $reliefChecks = ReliefChecksModel::join('rel_relief_check_public_in', 'rel_relief_check_public_in.relief_check_id', '=', 'object_relief_checks.id')
            ->where('rel_relief_check_public_in.public_in_id', $type)
            ->filter(
                (int)Input::get('str', true),
                (Input::get('obj', true)),
                (int)Input::get('year', true)
            )
            ->orderBy('date', 'DESC')
            ->paginate(20, ['object_relief_checks.id', 'object', 'date'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get(['str', 'obj', 'year', 'sec_token']))
            ->setPath(currentUrl());

        // Richiesta del solo frammento (paginazione AJAX): essendo getDataResults() chiamato da
        // tutte le pagine di questo controller prima del lavoro pesante (query strutture/anni,
        // form builder, render completo), un solo controllo qui copre tutte quelle pagine.
        if (isFrontFragmentRequest()) {
            $resultsArray = $reliefChecks->toArray();
            $table = $this->createTableRows($resultsArray);
            renderFrontFragment(['table' => !empty($table) ? $table->generate() : null, 'instances' => $resultsArray]);
            exit;
        }

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($reliefChecks) && !empty($reliefChecks->toArray()['data'])) {

            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ReliefChecksModel::select(['object_relief_checks.created_at'])
                ->join('rel_relief_check_public_in', 'rel_relief_check_public_in.relief_check_id', '=', 'object_relief_checks.id')
                ->where('rel_relief_check_public_in.public_in_id', $type)
                ->orderBy('object_relief_checks.created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ReliefChecksModel::select(['object_relief_checks.updated_at'])
                ->join('rel_relief_check_public_in', 'rel_relief_check_public_in.relief_check_id', '=', 'object_relief_checks.id')
                ->where('rel_relief_check_public_in.public_in_id', $type)
                ->orderBy('object_relief_checks.updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        return $reliefChecks;
    }

    /**
     * Metodo che crea la tabella con i dati dei risultati della ricerca da mostrare
     *
     * @param $reliefChecks {dati da inserire nella tabella}
     * @return Table|null
     * @throws Exception
     */
    private function createTableRows($reliefChecks = null): ?Table
    {
        $currentPageId = (int)uri()->segment(2, 0);
        $table = null;

        if (!empty($reliefChecks['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();
            $table->set_heading('Oggetto', 'Data del documento', '');

            // Creo le righe della tabella settando i dati da mostrare
            foreach ($reliefChecks['data'] as $reliefCheck) {

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $reliefCheck['id'] . '/' . urlTitle($reliefCheck['object'])) . '" data-id="' . $reliefCheck['id'] . ' ">' . escapeXss($reliefCheck['object']) . '</a>',
                    !empty($reliefCheck['date']) ? date('d/m/Y', strtotime($reliefCheck['date'])) : null,
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $reliefCheck['id'] . '/' . urlTitle($reliefCheck['object'])) . '" data-id="' . $reliefCheck['id'] . ' "> Dettagli </a>'
                );
            }
        }

        return $table;
    }

    /**
     * Metodo chiamato per la pagina "Organi di revisione amministrativa e contabile",
     * nella sezione CONTROLLI E RILIEVI SULL'AMMINISTRAZIONE
     * ID sezione 144
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexReviewOrganisms(): void
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

        // Validazione dei campi d'input per la ricerca
        $validator = new Validator();
        if (Input::get('obj') != null ||
            Input::get('year') != null ||
            Input::get('str') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Oggetto')->value(Input::get('obj'))->maxLength(30)->end();
        $validator->label('Anno')->value(Input::get('year'))->isDate('Y')->end();
        $validator->label('Ufficio')->value(Input::get('str'))->isInt()->end();

        if (!$validator->isSuccess()) {
            echo showError('Errore nella validazione dei dati', $validator->getErrorsHtml(), $validator->getErrorStatusCode());
            die();
        }

        // Recupero i controlli rilievi da mostrare in base al campo pubblica in
        $reliefChecks = $this->getDataResults('144', $data);
        
        $reliefChecks = !empty($reliefChecks) ? $reliefChecks->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Form per i filtri di ricerca
        $form = json_encode([
            [
                'type' => 'form_open',
                'name' => 'relief_check',
                'action' => '{current_url}',
                'attributes' => [
                    'method' => 'get',
                    'class' => 'set-active-label',
                ]
            ],
            [
                'type' => 'tag',
                'content' => '<div class="row mb-4">',
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-12 mb-4">',
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
                'prefix' => '<div class="form-group col-lg-6">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'obj',
                    'id' => 'obj',
                    'value' => '{object}'
                ],
                'label' => [
                    'for' => 'obj',
                    'text' => 'Oggetto',
                ],
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-6">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{year}',
                'name' => 'year',
                'extra' => 'id="year"',
                'label' => [
                    'for' => 'year',
                    'text' => 'Anno',
                    'class' => 'active'
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

        $formFilterFields = [
            'str' => Input::get('str', true),
            'obj' => Input::get('obj', true),
            'year' => Input::get('year', true)
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value) {
            if (!empty($value)) {
                $countFilterFields += 1;
            }
        }
        // Filtro per strutture
        $structureIds = optional(ReliefChecksModel::select(['object_structures_id'])
            ->join('rel_relief_check_public_in', 'rel_relief_check_public_in.relief_check_id', '=', 'object_relief_checks.id')
            ->where('rel_relief_check_public_in.public_in_id', 144)
            ->groupBy('object_structures_id')
            ->get())
            ->toArray();
        $structures = [null => 'Seleziona una struttura'] + optional(StructuresModel::whereIn('id', $structureIds)
                ->where(function ($query) {
                })
                ->orderBy('structure_name', 'ASC')
                ->pluck('structure_name', 'id'))
                ->toArray();

        // Anni per la ricerca
        $resultYears = optional(ReliefChecksModel::select('date')
            ->join('rel_relief_check_public_in', 'rel_relief_check_public_in.relief_check_id', '=', 'object_relief_checks.id')
            ->where('rel_relief_check_public_in.public_in_id', '144')
            ->orderBy('date', 'ASC'))
            ->first();
        $resultYears = !empty($resultYears) ? [null => 'Seleziona un anno'] + $resultYears->toArray() : ['date' => date('Y')];
        $years = [null => 'Seleziona un anno'];
        for ($i = (int)date('Y'); $i >= (int)date('Y', strtotime($resultYears['date'])); $i--) {
            $years[$i] = $i;
        }

        $formBuilder = new FormBuilder([
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'object' => htmlEscape(Input::get('obj')),
            'structure' => FormBuilder::parseOptions($structures, htmlEscape(Input::get('str', true))),
            'year' => FormBuilder::parseOptions($years, htmlEscape(Input::get('year', true)))
        ]);

        //        if (!empty($currentPage['form_filter'])) {
        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();
        //        }

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($reliefChecks);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $reliefChecks;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = !empty($currentPage['no_required']);
        $data['allowSearch'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['searchFieldsOpenData'] = $formFilterFields;
        $data['filterUsed'] = $countFilterFields;
        $data['rangeOpenData'] = 2000;
        //Abilita la paginazione AJAX della tabella dei risultati (vedi renderFrontFragment)
        $data['ajaxPagination'] = true;

        //Non è piu possibile pubblicare qui dentro per le PA. Le soc trasp possono
        if (empty($data['table'])) {
            $data['allowSearch'] = false;
            $data['table'] = '<p></p>';
        }

        $currentPage = $currentPage ?? null;
        $data = $data ?? null;
        $data = $data ?? null;
        renderFront(config('vfo', null, 'app') . '/relief_checks/relief_checks', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Corte dei conti",
     * nella sezione CONTROLLI E RILIEVI SULL'AMMINISTRAZIONE
     * ID sezione 145
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexCourtOfAuditors(): void
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

        // Validazione dei campi di input per la ricerca
        $validator = new Validator();
        if (Input::get('obj') != null ||
            Input::get('year') != null ||
            Input::get('str') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Oggetto')->value(Input::get('obj'))->maxLength(30)->end();
        $validator->label('Anno')->value(Input::get('year'))->isDate('Y')->end();
        $validator->label('Ufficio')->value(Input::get('str'))->isInt()->end();

        if (!$validator->isSuccess()) {
            echo showError('Errore nella validazione dei dati', $validator->getErrorsHtml(), $validator->getErrorStatusCode());
            die();
        }

        // Recupero i controlli rilievi da mostrare in base al campo pubblica in
        $reliefChecks = $this->getDataResults('145', $data);

        $reliefChecks = !empty($reliefChecks) ? $reliefChecks->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Form per i filtri di ricerca
        $form = json_encode([
            [
                'type' => 'form_open',
                'name' => 'relief_check',
                'action' => '{current_url}',
                'attributes' => [
                    'method' => 'get',
                    'class' => 'set-active-label',
                ]
            ],
            [
                'type' => 'tag',
                'content' => '<div class="row mb-4">',
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-12 mb-4">',
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
                'prefix' => '<div class="form-group col-lg-6">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'obj',
                    'id' => 'obj',
                    'value' => '{object}'
                ],
                'label' => [
                    'for' => 'obj',
                    'text' => 'Oggetto',
                ],
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-6">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{year}',
                'name' => 'year',
                'extra' => 'id="year"',
                'label' => [
                    'for' => 'year',
                    'text' => 'Anno',
                    'class' => 'active'
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

        $formFilterFields = [
            'str' => Input::get('str', true),
            'obj' => Input::get('obj', true),
            'year' => Input::get('year', true)
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value) {
            if (!empty($value)) {
                $countFilterFields += 1;
            }
        }

        // Filtro per strutture
        $structureIds = optional(ReliefChecksModel::select(['object_structures_id'])
            ->join('rel_relief_check_public_in', 'rel_relief_check_public_in.relief_check_id', '=', 'object_relief_checks.id')
            ->where('rel_relief_check_public_in.public_in_id', 145)
            ->groupBy('object_structures_id')
            ->get())
            ->toArray();
        $structures = [null => 'Seleziona una struttura'] +
            optional(StructuresModel::whereIn('id', $structureIds)
                ->where(function ($query) {
                })
                ->orderBy('structure_name', 'ASC')
                ->pluck('structure_name', 'id'))
                ->toArray();

        // Anni per la ricerca
        $resultYears = ReliefChecksModel::select('date')
            ->join('rel_relief_check_public_in', 'rel_relief_check_public_in.relief_check_id', '=', 'object_relief_checks.id')
            ->where('rel_relief_check_public_in.public_in_id', '145')
            ->orderBy('date', 'ASC')
            ->first();
        $resultYears = !empty($resultYears) ? [null => 'Seleziona un anno'] + $resultYears->toArray() : ['date' => date('Y')];
        $years = [null => 'Seleziona un anno'];
        for ($i = (int)date('Y'); $i >= (int)date('Y', strtotime($resultYears['date'])); $i--) {
            $years[$i] = $i;
        }

        $formBuilder = new FormBuilder([
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'object' => htmlEscape(Input::get('obj')),
            'structure' => FormBuilder::parseOptions($structures, htmlEscape(Input::get('str', true))),
            'year' => FormBuilder::parseOptions($years, htmlEscape(Input::get('year', true)))
        ]);

        //        if (!empty($currentPage['form_filter'])) {
        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();
        //        }

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($reliefChecks);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $reliefChecks;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = !empty($currentPage['no_required']);
        $data['allowSearch'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['searchFieldsOpenData'] = $formFilterFields;
        $data['filterUsed'] = $countFilterFields;
        $data['rangeOpenData'] = 2000;
        //Abilita la paginazione AJAX della tabella dei risultati (vedi renderFrontFragment)
        $data['ajaxPagination'] = true;

        //Non è piu possibile pubblicare qui dentro per le PA. Le soc trasp possono
        if (empty($data['table'])) {
            $data['allowSearch'] = false;
            $data['table'] = '<p></p>';
        }

        $currentPage = $currentPage ?? null;
        $data = $data ?? null;
        $data = $data ?? null;
        renderFront(config('vfo', null, 'app') . '/relief_checks/relief_checks', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina
     * "Attestazione dell'OIV o di altra struttura analoga nell'assolvimento degli obblighi di pubblicazione",
     * nella sezione CONTROLLI E RILIEVI SULL'AMMINISTRAZIONE
     * ID sezione 140
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexOIVCertification(): void
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

        // Validazione dei campi di input per la ricerca
        $validator = new Validator();
        if (Input::get('obj') != null ||
            Input::get('year') != null ||
            Input::get('str') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Oggetto')->value(Input::get('obj'))->maxLength(30)->end();
        $validator->label('Anno')->value(Input::get('year'))->isDate('Y')->end();
        $validator->label('Ufficio')->value(Input::get('str'))->isInt()->end();

        if (!$validator->isSuccess()) {
            echo showError('Errore nella validazione dei dati', $validator->getErrorsHtml(), $validator->getErrorStatusCode());
            die();
        }

        // Recupero i controlli rilievi da mostrare in base al campo pubblica in
        $reliefChecks = $this->getDataResults('140', $data);

        $reliefChecks = !empty($reliefChecks) ? $reliefChecks->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Form per i filtri di ricerca
        $form = json_encode([
            [
                'type' => 'form_open',
                'name' => 'relief_check',
                'action' => '{current_url}',
                'attributes' => [
                    'method' => 'get',
                    'class' => 'set-active-label',
                ]
            ],
            [
                'type' => 'tag',
                'content' => '<div class="row mb-4">',
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-12 mb-4">',
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
                'prefix' => '<div class="form-group col-lg-6">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'obj',
                    'id' => 'obj',
                    'value' => '{object}'
                ],
                'label' => [
                    'for' => 'obj',
                    'text' => 'Oggetto',
                ],
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-6">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{year}',
                'name' => 'year',
                'extra' => 'id="year"',
                'label' => [
                    'for' => 'year',
                    'text' => 'Anno',
                    'class' => 'active'
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

        $formFilterFields = [
            'str' => Input::get('str', true),
            'obj' => Input::get('obj', true),
            'year' => Input::get('year', true)
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value) {
            if (!empty($value)) {
                $countFilterFields += 1;
            }
        }
        // Filtro per strutture
        $structureIds = optional(ReliefChecksModel::select(['object_structures_id'])
            ->join('rel_relief_check_public_in', 'rel_relief_check_public_in.relief_check_id', '=', 'object_relief_checks.id')
            ->where('rel_relief_check_public_in.public_in_id', 140)
            ->groupBy('object_structures_id')
            ->get())
            ->toArray();
        $structures = [null => 'Seleziona una struttura'] +
            optional(StructuresModel::whereIn('id', $structureIds)
                ->where(function ($query) {
                })
                ->orderBy('structure_name', 'ASC')
                ->pluck('structure_name', 'id'))
                ->toArray();

        // Anni per la ricerca
        $resultYears = ReliefChecksModel::select('date')
            ->join('rel_relief_check_public_in', 'rel_relief_check_public_in.relief_check_id', '=', 'object_relief_checks.id')
            ->where('rel_relief_check_public_in.public_in_id', '140')
            ->orderBy('date', 'ASC')
            ->first();
        $resultYears = !empty($resultYears) ? [null => 'Seleziona un anno'] + $resultYears->toArray() : ['date' => date('Y')];
        $years = [null => 'Seleziona un anno'];
        for ($i = (int)date('Y'); $i >= (int)date('Y', strtotime($resultYears['date'])); $i--) {
            $years[$i] = $i;
        }

        $formBuilder = new FormBuilder([
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'object' => htmlEscape(Input::get('obj')),
            'structure' => FormBuilder::parseOptions($structures, htmlEscape(Input::get('str', true))),
            'year' => FormBuilder::parseOptions($years, htmlEscape(Input::get('year', true)))
        ]);

        //        if (!empty($currentPage['form_filter'])) {
        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();
        //        }

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($reliefChecks);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $reliefChecks;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = !empty($currentPage['no_required']);
        $data['allowSearch'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['searchFieldsOpenData'] = $formFilterFields;
        $data['filterUsed'] = $countFilterFields;
        $data['rangeOpenData'] = 2000;
        //Abilita la paginazione AJAX della tabella dei risultati (vedi renderFrontFragment)
        $data['ajaxPagination'] = true;
        //Inclusione di eventuali librerie esterne
        $currentPage = $currentPage ?? null;
        $data = $data ?? null;
        $data = $data ?? null;
        renderFront(config('vfo', null, 'app') . '/relief_checks/relief_checks', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina
     * "Documento dell'OIV di validazione della Relazione sulla Performance",
     * nella sezione CONTROLLI E RILIEVI SULL'AMMINISTRAZIONE
     * ID sezione 141
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexOIVDocument(): void
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

        // Validazione dei campi di input per la ricerca
        $validator = new Validator();
        if (Input::get('obj') != null ||
            Input::get('year') != null ||
            Input::get('str') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Oggetto')->value(Input::get('obj'))->maxLength(30)->end();
        $validator->label('Anno')->value(Input::get('year'))->isDate('Y')->end();
        $validator->label('Ufficio')->value(Input::get('str'))->isInt()->end();

        if (!$validator->isSuccess()) {
            echo showError('Errore nella validazione dei dati', $validator->getErrorsHtml(), $validator->getErrorStatusCode());
            die();
        }

        // Recupero i controlli rilievi da mostrare in base al campo pubblica in
        $reliefChecks = $this->getDataResults('141', $data);

        $reliefChecks = !empty($reliefChecks) ? $reliefChecks->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Form per i filtri di ricerca
        $form = json_encode([
            [
                'type' => 'form_open',
                'name' => 'relief_check',
                'action' => '{current_url}',
                'attributes' => [
                    'method' => 'get',
                    'class' => 'set-active-label',
                ]
            ],
            [
                'type' => 'tag',
                'content' => '<div class="row mb-4">',
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-12 mb-4">',
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
                'prefix' => '<div class="form-group col-lg-6">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'obj',
                    'id' => 'obj',
                    'value' => '{object}'
                ],
                'label' => [
                    'for' => 'obj',
                    'text' => 'Oggetto',
                ],
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-6">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{year}',
                'name' => 'year',
                'extra' => 'id="year"',
                'label' => [
                    'for' => 'year',
                    'text' => 'Anno',
                    'class' => 'active'
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

        $formFilterFields = [
            'str' => Input::get('str', true),
            'obj' => Input::get('obj', true),
            'year' => Input::get('year', true)
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value) {
            if (!empty($value)) {
                $countFilterFields += 1;
            }
        }

        // Filtro per strutture
        $structureIds = optional(ReliefChecksModel::select(['object_structures_id'])
            ->join('rel_relief_check_public_in', 'rel_relief_check_public_in.relief_check_id', '=', 'object_relief_checks.id')
            ->where('rel_relief_check_public_in.public_in_id', 141)
            ->groupBy('object_structures_id')
            ->get())
            ->toArray();
        $structures = [null => 'Seleziona una struttura'] +
            optional(StructuresModel::whereIn('id', $structureIds)
                ->where(function ($query) {
                })
                ->orderBy('structure_name', 'ASC')
                ->pluck('structure_name', 'id'))
                ->toArray();

        // Anni per la ricerca
        $resultYears = ReliefChecksModel::select('date')
            ->join('rel_relief_check_public_in', 'rel_relief_check_public_in.relief_check_id', '=', 'object_relief_checks.id')
            ->where('rel_relief_check_public_in.public_in_id', '141')
            ->orderBy('date', 'ASC')
            ->first();
        $resultYears = !empty($resultYears) ? [null => 'Seleziona un anno'] + $resultYears->toArray() : ['date' => date('Y')];
        $years = [null => 'Seleziona un anno'];
        for ($i = (int)date('Y'); $i >= (int)date('Y', strtotime($resultYears['date'])); $i--) {
            $years[$i] = $i;
        }

        $formBuilder = new FormBuilder([
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'object' => htmlEscape(Input::get('obj')),
            'structure' => FormBuilder::parseOptions($structures, htmlEscape(Input::get('str', true))),
            'year' => FormBuilder::parseOptions($years, htmlEscape(Input::get('year', true)))
        ]);

        //        if (!empty($currentPage['form_filter'])) {
        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();
        //        }

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($reliefChecks);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $reliefChecks;
        $data['noRequiredPublication'] = !empty($currentPage['no_required']);
        $data['allowSearch'] = true;
        $data['instance'] = $currentPage;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['searchFieldsOpenData'] = $formFilterFields;
        $data['filterUsed'] = $countFilterFields;
        $data['rangeOpenData'] = 2000;
        //Abilita la paginazione AJAX della tabella dei risultati (vedi renderFrontFragment)
        $data['ajaxPagination'] = true;
        //Inclusione di eventuali librerie esterne
        $currentPage = $currentPage ?? null;
        $data = $data ?? null;
        $data = $data ?? null;
        renderFront(config('vfo', null, 'app') . '/relief_checks/relief_checks', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina
     * "Relazione dell'OIV sul funzionamento complessivo del Sistema di valutazione trasparenza e integrità dei controlli interni",
     * nella sezione CONTROLLI E RILIEVI SULL'AMMINISTRAZIONE
     * ID sezione 142
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexOIReport(): void
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

        // Validazione dei campi di input per la ricerca
        $validator = new Validator();
        if (Input::get('obj') != null ||
            Input::get('year') != null ||
            Input::get('str') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Oggetto')->value(Input::get('obj'))->maxLength(30)->end();
        $validator->label('Anno')->value(Input::get('year'))->isDate('Y')->end();
        $validator->label('Ufficio')->value(Input::get('str'))->isInt()->end();

        if (!$validator->isSuccess()) {
            echo showError('Errore nella validazione dei dati', $validator->getErrorsHtml(), $validator->getErrorStatusCode());
            die();
        }

        // Recupero i controlli rilievi da mostrare in base al campo pubblica in
        $reliefChecks = $this->getDataResults('142', $data);

        $reliefChecks = !empty($reliefChecks) ? $reliefChecks->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Form per i filtri di ricerca
        $form = json_encode([
            [
                'type' => 'form_open',
                'name' => 'relief_check',
                'action' => '{current_url}',
                'attributes' => [
                    'method' => 'get',
                    'class' => 'set-active-label',

                ]
            ],
            [
                'type' => 'tag',
                'content' => '<div class="row mb-4">',
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-12 mb-4">',
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
                'prefix' => '<div class="form-group col-lg-6">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'obj',
                    'id' => 'obj',
                    'value' => '{object}'
                ],
                'label' => [
                    'for' => 'obj',
                    'text' => 'Oggetto',
                ],
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-6">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{year}',
                'name' => 'year',
                'extra' => 'id="year"',
                'label' => [
                    'for' => 'year',
                    'text' => 'Anno',
                    'class' => 'active'
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

        $formFilterFields = [
            'str' => Input::get('str', true),
            'obj' => Input::get('obj', true),
            'year' => Input::get('year', true)
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value) {
            if (!empty($value)) {
                $countFilterFields += 1;
            }
        }

        // Filtro per strutture
        $structureIds = optional(ReliefChecksModel::select(['object_structures_id'])
            ->join('rel_relief_check_public_in', 'rel_relief_check_public_in.relief_check_id', '=', 'object_relief_checks.id')
            ->where('rel_relief_check_public_in.public_in_id', 142)
            ->groupBy('object_structures_id')
            ->get())
            ->toArray();
        $structures = [null => 'Seleziona una struttura'] +
            optional(StructuresModel::whereIn('id', $structureIds)
                ->where(function ($query) {
                })
                ->orderBy('structure_name', 'ASC')
                ->pluck('structure_name', 'id'))
                ->toArray();

        // Anni per la ricerca
        $resultYears = ReliefChecksModel::select('date')
            ->join('rel_relief_check_public_in', 'rel_relief_check_public_in.relief_check_id', '=', 'object_relief_checks.id')
            ->where('rel_relief_check_public_in.public_in_id', '142')
            ->orderBy('date', 'ASC')
            ->first();
        $resultYears = !empty($resultYears) ? [null => 'Seleziona un anno'] + $resultYears->toArray() : ['date' => date('Y')];
        $years = [null => 'Seleziona un anno'];
        for ($i = (int)date('Y'); $i >= (int)date('Y', strtotime($resultYears['date'])); $i--) {
            $years[$i] = $i;
        }

        $formBuilder = new FormBuilder([
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'object' => htmlEscape(Input::get('obj')),
            'structure' => FormBuilder::parseOptions($structures, htmlEscape(Input::get('str', true))),
            'year' => FormBuilder::parseOptions($years, htmlEscape(Input::get('year', true)))
        ]);

        //        if (!empty($currentPage['form_filter'])) {
        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();
        //        }

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($reliefChecks);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $reliefChecks;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = !empty($currentPage['no_required']);
        $data['allowSearch'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['searchFieldsOpenData'] = $formFilterFields;
        $data['filterUsed'] = $countFilterFields;
        $data['rangeOpenData'] = 2000;
        //Abilita la paginazione AJAX della tabella dei risultati (vedi renderFrontFragment)
        $data['ajaxPagination'] = true;
        //Inclusione di eventuali librerie esterne
        $currentPage = $currentPage ?? null;
        $data = $data ?? null;
        $data = $data ?? null;
        renderFront(config('vfo', null, 'app') . '/relief_checks/relief_checks', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina
     * "Altri atti degli organismi indipendenti di valutazione, nuclei di valutazione o altri organismi con funzioni analoghe",
     * nella sezione CONTROLLI E RILIEVI SULL'AMMINISTRAZIONE
     * ID sezione 143
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexOtherActs(): void
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

        // Validazione dei campi di input per la ricerca
        $validator = new Validator();
        if (Input::get('obj') != null ||
            Input::get('year') != null ||
            Input::get('str') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Oggetto')->value(Input::get('obj'))->maxLength(30)->end();
        $validator->label('Anno')->value(Input::get('year'))->isDate('Y')->end();
        $validator->label('Ufficio')->value(Input::get('str'))->isInt()->end();

        if (!$validator->isSuccess()) {
            echo showError('Errore nella validazione dei dati', $validator->getErrorsHtml(), $validator->getErrorStatusCode());
            die();
        }

        // Form per i filtri di ricerca
        $form = json_encode([
            [
                'type' => 'form_open',
                'name' => 'relief_check',
                'action' => '{current_url}',
                'attributes' => [
                    'method' => 'get',
                    'class' => 'set-active-label',
                ]
            ],
            [
                'type' => 'tag',
                'content' => '<div class="row mb-4">',
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-12 mb-4">',
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
                'prefix' => '<div class="form-group col-lg-6">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'obj',
                    'id' => 'obj',
                    'value' => '{object}'
                ],
                'label' => [
                    'for' => 'obj',
                    'text' => 'Oggetto',
                ],
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-6">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{year}',
                'name' => 'year',
                'extra' => 'id="year"',
                'label' => [
                    'for' => 'year',
                    'text' => 'Anno',
                    'class' => 'active'
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

        $formFilterFields = [
            'str' => Input::get('str', true),
            'obj' => Input::get('obj', true),
            'year' => Input::get('year', true)
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value) {
            if (!empty($value)) {
                $countFilterFields += 1;
            }
        }

        // Filtro per strutture
        $structureIds = optional(ReliefChecksModel::select(['object_structures_id'])
            ->join('rel_relief_check_public_in', 'rel_relief_check_public_in.relief_check_id', '=', 'object_relief_checks.id')
            ->where('rel_relief_check_public_in.public_in_id', 143)
            ->groupBy('object_structures_id')
            ->get())
            ->toArray();
        $structures = [null => 'Seleziona una Struttura'] +
            optional(StructuresModel::whereIn('id', $structureIds)
                ->where(function ($query) {
                })
                ->orderBy('structure_name', 'ASC')
                ->pluck('structure_name', 'id'))
                ->toArray();

        // Anni per la ricerca
        $resultYears = ReliefChecksModel::select('date')
            ->join('rel_relief_check_public_in', 'rel_relief_check_public_in.relief_check_id', '=', 'object_relief_checks.id')
            ->where('rel_relief_check_public_in.public_in_id', '143')
            ->orderBy('date', 'ASC')
            ->first();
        $resultYears = !empty($resultYears) ? [null => 'Seleziona un anno'] + $resultYears->toArray() : ['date' => date('Y')];
        $years = [null => 'Seleziona un anno'];
        for ($i = (int)date('Y'); $i >= (int)date('Y', strtotime($resultYears['date'])); $i--) {
            $years[$i] = $i;
        }

        $formBuilder = new FormBuilder([
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'object' => htmlEscape(Input::get('obj')),
            'structure' => FormBuilder::parseOptions($structures, htmlEscape(Input::get('str', true))),
            'year' => FormBuilder::parseOptions($years, htmlEscape(Input::get('year', true)))
        ]);

        //        if (!empty($currentPage['form_filter'])) {
        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();
        //        }

        // Recupero i controlli rilievi da mostrare in base al campo pubblica in
        $reliefChecks = $this->getDataResults('143', $data);

        $reliefChecks = !empty($reliefChecks) ? $reliefChecks->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($reliefChecks);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $reliefChecks;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = !empty($currentPage['no_required']);
        $data['allowSearch'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['searchFieldsOpenData'] = $formFilterFields;
        $data['filterUsed'] = $countFilterFields;
        $data['rangeOpenData'] = 2000;
        //Abilita la paginazione AJAX della tabella dei risultati (vedi renderFrontFragment)
        $data['ajaxPagination'] = true;
        //Inclusione di eventuali librerie esterne
        $currentPage = $currentPage ?? null;
        $data = $data ?? null;
        $data = $data ?? null;
        renderFront(config('vfo', null, 'app') . '/relief_checks/relief_checks', $data, 'frontend');
    }


    /**
     * Metodo chiamato per la pagina
     * "Relazioni degli organi di revisione amministrativa e contabile al bilancio di previsione o budget e alle relative variazioni",
     * nella sezione Organi di revisione amministrativa e contabile
     * ID sezione 606
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexBudgetForecastReports(): void
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
            ->with(['contents' => function ($query) {
                $query->select(['id', 'created_at', 'updated_at', 'section_fo_id'])
                    ->orderBy('updated_at', 'DESC')
                    ->orderBy('created_at', 'DESC')
                    ->first();
            }])
            ->labelfilter((int)$institutionTypeId);


        $currentPage = $currentPage->first()
            ->toArray();

        // Recupero le voci per costruire il menu dei contenuti
        $sectionFO = getRightOrBottomMenu($currentPageId, $currentPage['parent_id']);

        // Validazione dei campi di input per la ricerca
        $validator = new Validator();
        if (Input::get('obj') != null ||
            Input::get('year') != null ||
            Input::get('str') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Oggetto')->value(Input::get('obj'))->maxLength(30)->end();
        $validator->label('Anno')->value(Input::get('year'))->isDate('Y')->end();
        $validator->label('Ufficio')->value(Input::get('str'))->isInt()->end();

        if (!$validator->isSuccess()) {
            echo showError('Errore nella validazione dei dati', $validator->getErrorsHtml(), $validator->getErrorStatusCode());
            die();
        }

        // Recupero i controlli rilievi da mostrare in base al campo pubblica in
        $reliefChecks = $this->getDataResults('606', $data);

        $reliefChecks = !empty($reliefChecks) ? $reliefChecks->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Form per i filtri di ricerca
        $form = json_encode([
            [
                'type' => 'form_open',
                'name' => 'relief_check',
                'action' => '{current_url}',
                'attributes' => [
                    'method' => 'get',
                    'class' => 'set-active-label',
                ]
            ],
            [
                'type' => 'tag',
                'content' => '<div class="row mb-4">',
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-12 mb-4">',
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
                'prefix' => '<div class="form-group col-lg-6">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'obj',
                    'id' => 'obj',
                    'value' => '{object}'
                ],
                'label' => [
                    'for' => 'obj',
                    'text' => 'Oggetto',
                ],
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-6">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{year}',
                'name' => 'year',
                'extra' => 'id="year"',
                'label' => [
                    'for' => 'year',
                    'text' => 'Anno',
                    'class' => 'active'
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

        $formFilterFields = [
            'str' => Input::get('str', true),
            'obj' => Input::get('obj', true),
            'year' => Input::get('year', true)
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value) {
            if (!empty($value)) {
                $countFilterFields += 1;
            }
        }

        // Filtro per strutture
        $structureIds = optional(ReliefChecksModel::select(['object_structures_id'])
            ->join('rel_relief_check_public_in', 'rel_relief_check_public_in.relief_check_id', '=', 'object_relief_checks.id')
            ->where('rel_relief_check_public_in.public_in_id', 606)
            ->groupBy('object_structures_id')
            ->get())
            ->toArray();
        $structures = [null => 'Seleziona una struttura'] +
            optional(StructuresModel::whereIn('id', $structureIds)
                ->where(function ($query) {
                })
                ->orderBy('structure_name', 'ASC')
                ->pluck('structure_name', 'id'))
                ->toArray();

        // Anni per la ricerca
        $resultYears = ReliefChecksModel::select('date')
            ->join('rel_relief_check_public_in', 'rel_relief_check_public_in.relief_check_id', '=', 'object_relief_checks.id')
            ->where('rel_relief_check_public_in.public_in_id', '606')
            ->orderBy('date', 'ASC')
            ->first();
        $resultYears = !empty($resultYears) ? [null => 'Seleziona un anno'] + $resultYears->toArray() : ['date' => date('Y')];
        $years = [null => 'Seleziona un anno'];
        for ($i = (int)date('Y'); $i >= (int)date('Y', strtotime($resultYears['date'])); $i--) {
            $years[$i] = $i;
        }

        $formBuilder = new FormBuilder([
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'object' => htmlEscape(Input::get('obj')),
            'structure' => FormBuilder::parseOptions($structures, htmlEscape(Input::get('str', true))),
            'year' => FormBuilder::parseOptions($years, htmlEscape(Input::get('year', true)))
        ]);

        //        if (!empty($currentPage['form_filter'])) {
        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();
        //        }

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($reliefChecks);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $reliefChecks;
        $data['noRequiredPublication'] = !empty($currentPage['no_required']);
        $data['allowSearch'] = true;
        $data['instance'] = $currentPage;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['searchFieldsOpenData'] = $formFilterFields;
        $data['filterUsed'] = $countFilterFields;
        $data['rangeOpenData'] = 2000;
        //Abilita la paginazione AJAX della tabella dei risultati (vedi renderFrontFragment)
        $data['ajaxPagination'] = true;
        //Inclusione di eventuali librerie esterne
        $currentPage = $currentPage ?? null;
        $data = $data ?? null;
        $data = $data ?? null;
        renderFront(config('vfo', null, 'app') . '/relief_checks/relief_checks', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina
     * "Relazioni degli organi di revisione amministrativa e contabile al conto consuntivo o al bilancio di esercizio",
     * nella sezione Organi di revisione amministrativa e contabile
     * ID sezione 607
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexFinancialStatementReports(): void
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
            ->with(['contents' => function ($query) {
                $query->select(['id', 'created_at', 'updated_at', 'section_fo_id'])
                    ->orderBy('updated_at', 'DESC')
                    ->orderBy('created_at', 'DESC')
                    ->first();
            }])
            ->labelfilter((int)$institutionTypeId);


        $currentPage = $currentPage->first()
            ->toArray();

        // Recupero le voci per costruire il menu dei contenuti
        $sectionFO = getRightOrBottomMenu($currentPageId, $currentPage['parent_id']);

        // Validazione dei campi di input per la ricerca
        $validator = new Validator();
        if (Input::get('obj') != null ||
            Input::get('year') != null ||
            Input::get('str') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Oggetto')->value(Input::get('obj'))->maxLength(30)->end();
        $validator->label('Anno')->value(Input::get('year'))->isDate('Y')->end();
        $validator->label('Ufficio')->value(Input::get('str'))->isInt()->end();

        if (!$validator->isSuccess()) {
            echo showError('Errore nella validazione dei dati', $validator->getErrorsHtml(), $validator->getErrorStatusCode());
            die();
        }

        // Recupero i controlli rilievi da mostrare in base al campo pubblica in
        $reliefChecks = $this->getDataResults('607', $data);

        $reliefChecks = !empty($reliefChecks) ? $reliefChecks->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Form per i filtri di ricerca
        $form = json_encode([
            [
                'type' => 'form_open',
                'name' => 'relief_check',
                'action' => '{current_url}',
                'attributes' => [
                    'method' => 'get',
                    'class' => 'set-active-label',
                ]
            ],
            [
                'type' => 'tag',
                'content' => '<div class="row mb-4">',
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-12 mb-4">',
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
                'prefix' => '<div class="form-group col-lg-6">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'obj',
                    'id' => 'obj',
                    'value' => '{object}'
                ],
                'label' => [
                    'for' => 'obj',
                    'text' => 'Oggetto',
                ],
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-6">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{year}',
                'name' => 'year',
                'extra' => 'id="year"',
                'label' => [
                    'for' => 'year',
                    'text' => 'Anno',
                    'class' => 'active'
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

        $formFilterFields = [
            'str' => Input::get('str', true),
            'obj' => Input::get('obj', true),
            'year' => Input::get('year', true)
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value) {
            if (!empty($value)) {
                $countFilterFields += 1;
            }
        }

        // Filtro per strutture
        $structureIds = optional(ReliefChecksModel::select(['object_structures_id'])
            ->join('rel_relief_check_public_in', 'rel_relief_check_public_in.relief_check_id', '=', 'object_relief_checks.id')
            ->where('rel_relief_check_public_in.public_in_id', 607)
            ->groupBy('object_structures_id')
            ->get())
            ->toArray();
        $structures = [null => 'Seleziona una struttura'] +
            optional(StructuresModel::whereIn('id', $structureIds)
                ->where(function ($query) {
                })
                ->orderBy('structure_name', 'ASC')
                ->pluck('structure_name', 'id'))
                ->toArray();

        // Anni per la ricerca
        $resultYears = ReliefChecksModel::select('date')
            ->join('rel_relief_check_public_in', 'rel_relief_check_public_in.relief_check_id', '=', 'object_relief_checks.id')
            ->where('rel_relief_check_public_in.public_in_id', '607')
            ->orderBy('date', 'ASC')
            ->first();
        $resultYears = !empty($resultYears) ? [null => 'Seleziona un anno'] + $resultYears->toArray() : ['date' => date('Y')];
        $years = [null => 'Seleziona un anno'];
        for ($i = (int)date('Y'); $i >= (int)date('Y', strtotime($resultYears['date'])); $i--) {
            $years[$i] = $i;
        }

        $formBuilder = new FormBuilder([
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'object' => htmlEscape(Input::get('obj')),
            'structure' => FormBuilder::parseOptions($structures, htmlEscape(Input::get('str', true))),
            'year' => FormBuilder::parseOptions($years, htmlEscape(Input::get('year', true)))
        ]);

        //        if (!empty($currentPage['form_filter'])) {
        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();
        //        }

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($reliefChecks);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $reliefChecks;
        $data['noRequiredPublication'] = !empty($currentPage['no_required']);
        $data['allowSearch'] = true;
        $data['instance'] = $currentPage;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['searchFieldsOpenData'] = $formFilterFields;
        $data['filterUsed'] = $countFilterFields;
        $data['rangeOpenData'] = 2000;
        //Abilita la paginazione AJAX della tabella dei risultati (vedi renderFrontFragment)
        $data['ajaxPagination'] = true;
        //Inclusione di eventuali librerie esterne
        $currentPage = $currentPage ?? null;
        $data = $data ?? null;
        $data = $data ?? null;
        renderFront(config('vfo', null, 'app') . '/relief_checks/relief_checks', $data, 'frontend');
    }


    /**
     * Metodo chiamato per la pagina
     * "Rilievi - recepiti e non - della Corte dei conti riguardanti l'organizzazione e l'attività delle amministrazioni stesse e dei loro uffici",
     * nella sezione Corte dei conti
     * ID sezione 608
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexReviewOrganizationsOrganisms(): void
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
            ->with(['contents' => function ($query) {
                $query->select(['id', 'created_at', 'updated_at', 'section_fo_id'])
                    ->orderBy('updated_at', 'DESC')
                    ->orderBy('created_at', 'DESC')
                    ->first();
            }])
            ->labelfilter((int)$institutionTypeId);


        $currentPage = $currentPage->first()
            ->toArray();

        // Recupero le voci per costruire il menu dei contenuti
        $sectionFO = getRightOrBottomMenu($currentPageId, $currentPage['parent_id']);

        // Validazione dei campi di input per la ricerca
        $validator = new Validator();
        if (Input::get('obj') != null ||
            Input::get('year') != null ||
            Input::get('str') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Oggetto')->value(Input::get('obj'))->maxLength(30)->end();
        $validator->label('Anno')->value(Input::get('year'))->isDate('Y')->end();
        $validator->label('Ufficio')->value(Input::get('str'))->isInt()->end();

        if (!$validator->isSuccess()) {
            echo showError('Errore nella validazione dei dati', $validator->getErrorsHtml(), $validator->getErrorStatusCode());
            die();
        }

        // Recupero i controlli rilievi da mostrare in base al campo pubblica in
        $reliefChecks = $this->getDataResults('608', $data);

        $reliefChecks = !empty($reliefChecks) ? $reliefChecks->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Form per i filtri di ricerca
        $form = json_encode([
            [
                'type' => 'form_open',
                'name' => 'relief_check',
                'action' => '{current_url}',
                'attributes' => [
                    'method' => 'get',
                    'class' => 'set-active-label',
                ]
            ],
            [
                'type' => 'tag',
                'content' => '<div class="row mb-4">',
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-12 mb-4">',
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
                'prefix' => '<div class="form-group col-lg-6">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'obj',
                    'id' => 'obj',
                    'value' => '{object}'
                ],
                'label' => [
                    'for' => 'obj',
                    'text' => 'Oggetto',
                ],
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-6">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{year}',
                'name' => 'year',
                'extra' => 'id="year"',
                'label' => [
                    'for' => 'year',
                    'text' => 'Anno',
                    'class' => 'active'
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

        $formFilterFields = [
            'str' => Input::get('str', true),
            'obj' => Input::get('obj', true),
            'year' => Input::get('year', true)
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value) {
            if (!empty($value)) {
                $countFilterFields += 1;
            }
        }

        // Filtro per strutture
        $structureIds = optional(ReliefChecksModel::select(['object_structures_id'])
            ->join('rel_relief_check_public_in', 'rel_relief_check_public_in.relief_check_id', '=', 'object_relief_checks.id')
            ->where('rel_relief_check_public_in.public_in_id', 608)
            ->groupBy('object_structures_id')
            ->get())
            ->toArray();
        $structures = [null => 'Seleziona una struttura'] +
            optional(StructuresModel::whereIn('id', $structureIds)
                ->where(function ($query) {
                })
                ->orderBy('structure_name', 'ASC')
                ->pluck('structure_name', 'id'))
                ->toArray();

        // Anni per la ricerca
        $resultYears = ReliefChecksModel::select('date')
            ->join('rel_relief_check_public_in', 'rel_relief_check_public_in.relief_check_id', '=', 'object_relief_checks.id')
            ->where('rel_relief_check_public_in.public_in_id', '608')
            ->orderBy('date', 'ASC')
            ->first();
        $resultYears = !empty($resultYears) ? [null => 'Seleziona un anno'] + $resultYears->toArray() : ['date' => date('Y')];
        $years = [null => 'Seleziona un anno'];
        for ($i = (int)date('Y'); $i >= (int)date('Y', strtotime($resultYears['date'])); $i--) {
            $years[$i] = $i;
        }

        $formBuilder = new FormBuilder([
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'object' => htmlEscape(Input::get('obj')),
            'structure' => FormBuilder::parseOptions($structures, htmlEscape(Input::get('str', true))),
            'year' => FormBuilder::parseOptions($years, htmlEscape(Input::get('year', true)))
        ]);

        //        if (!empty($currentPage['form_filter'])) {
        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();
        //        }

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($reliefChecks);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $reliefChecks;
        $data['noRequiredPublication'] = !empty($currentPage['no_required']);
        $data['allowSearch'] = true;
        $data['instance'] = $currentPage;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['searchFieldsOpenData'] = $formFilterFields;
        $data['filterUsed'] = $countFilterFields;
        $data['rangeOpenData'] = 2000;
        //Abilita la paginazione AJAX della tabella dei risultati (vedi renderFrontFragment)
        $data['ajaxPagination'] = true;
        //Inclusione di eventuali librerie esterne
        $currentPage = $currentPage ?? null;
        $data = $data ?? null;
        $data = $data ?? null;
        renderFront(config('vfo', null, 'app') . '/relief_checks/relief_checks', $data, 'frontend');
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
        $element = ReliefChecksModel::where('id', $elementId)
            ->with(['office' => function ($query) {
                $query->select(['id', 'structure_name']);
            }])
            ->with('public_in:section_fo_id');

        $element = $element ?? null;

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
        $data['pageName'] = $element['object'];
        $data['menuPages'] = $sectionFO;

        $data['publishedIn608'] = in_array('608', array_column($element['public_in'], 'section_fo_id'));

        // aggiungi al Breadcrumbs la pagina del dettaglio
        $data['concatBreadcrumb'] = true;
        $data['bread'] = array();
        $data['bread'][] = array('name' => $element['object'], 'link' => '/');
        $data['instance'] = $element;
        $data['currentPageId'] = $currentPageId;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['editRecord'] = true;

        $label = 'relief_checks';
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

        //Inclusione del nodo JSON-LD della pagina
        $data = $data ?? null;

        renderFront(config('vfo', null, 'app') . '/relief_checks/details', $data, 'frontend');
    }
}
