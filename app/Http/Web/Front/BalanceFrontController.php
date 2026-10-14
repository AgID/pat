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
use Model\BalanceSheetsModel;
use Model\SectionsFoModel;
use System\Input;
use System\Validator;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller pagina front-end Bilanci
 */
class BalanceFrontController extends BaseFrontController
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
     * Metodo chiamato per la pagina di snodo dei Bilanci
     * @return void
     * @throws Exception
     */
    public function pivot(): void
    {
        $pivot = new PivotController();
        $pivot->index();
    }

    /**
     * Metodo chiamato per la pagina "Bilancio preventivo e consuntivo",
     * nella sezione dei Bilanci
     * ID sezione 130
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexFinalAndQuote(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Validatore dei filtri di ricerca
        $validator = new Validator();

        if (Input::get('n') != null ||
            Input::get('y') != null ||
            Input::get('t') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('nome')->value(Input::get('n'))->maxLength(100)->end();
        $validator->label('anno')->value(Input::get('y'))->isDate('Y')->end();

        if (!$validator->isSuccess()) {
            echo showError('Errore nella validazione dei dati', $validator->getErrorsHtml(), $validator->getErrorStatusCode());
            die();
        }

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

        // Recupero i bilanci preventivi e consuntivi da mostrare
        $balanceSheets = $this->getDataResults(['piano indicatori e risultati', 'provvedimenti'], 'NotIn', $data);

        $balanceSheets = !empty($balanceSheets) ? $balanceSheets->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($balanceSheets);

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
                'content' => '<div class="row">',
            ],

            [
                'prefix' => '<div class=" form-group col-lg-6 mb-5">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'n',
                    'id' => 'n',
                    'value' => '{n}'
                ],
                'label' => [
                    'for' => 'n',
                    'text' => 'Nome',
                ],
            ],

            [
                'prefix' => '<div class="form-group select-wrapper col-lg-2 mb-5">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{y}',
                'name' => 'y',
                'extra' => 'id="y"',
                'label' => [
                    'for' => 'y',
                    'text' => 'Anno',
                    'class' => 'active'
                ],
            ],
            [
                'prefix' => '<div class="form-group select-wrapper col-lg-4 mb-5">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{t}',
                'name' => 't',
                'extra' => 'id="t"',
                'label' => [
                    'for' => 't',
                    'text' => 'Tipologia',
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
            'n' => Input::get('n'),
            'y' => Input::get('y'),
            't' => Input::get('t')
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value) {
            if (!empty($value)) {
                $countFilterFields += 1;
            }
        }

        // Anni per la ricerca
        $resultYears = BalanceSheetsModel::select('year')
            ->where('typology', '!=', 'piano indicatori e risultati')
            ->groupBy('year')
            ->get()
            ->toArray();

        $years = [null => 'Seleziona un anno'];

        foreach ($resultYears as $resYear) {
            $years[$resYear['year']] = $resYear['year'];
        }

        $balanceTyp = $this->getBalanceTypologies();

        $formBuilder = new FormBuilder([
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'n' => htmlEscape(Input::get('n', true)),
            'y' => FormBuilder::parseOptions($years, htmlEscape(Input::get('y', true))),
            't' => FormBuilder::parseOptions($balanceTyp, htmlEscape(Input::get('t', true)))
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['table'] = !empty($table) ? $table->generate() : null;
        $data['instances'] = $balanceSheets;
        $data['instance'] = $currentPage;
        $data['paragraphs'] = $contents;
        $data['allowSearch'] = true;
        //Abilita la paginazione AJAX della tabella dei risultati (vedi renderFrontFragment)
        $data['ajaxPagination'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['searchFieldsOpenData'] = $formFilterFields;
        $data['filterUsed'] = $countFilterFields;
        $data['rangeOpenData'] = 2000;
        //Inclusione di eventuali librerie esterne
        $data = $data ?? null;
        $data = $data ?? null;

        renderFront(config('vfo', null, 'app') . '/balance_sheets/balance_sheets', $data, 'frontend');
    }

    /**
     * Funzione che ritorna i dati da mostrare nella tabella
     *
     * @param array       $type     {la tipologia per cui filtrare i dati}
     * @param string|null $operator Operatore da utilizzare (IN o NOTIN)
     * @param array       $data     Dati da passare alla vista
     * @return mixed
     * @throws Exception
     */
    private function getDataResults(array $type = [], string $operator = null, array &$data = []): mixed
    {

        // Recupero i bilanci da mostrare
        $balances = BalanceSheetsModel::filter(
                Input::get('n', true),
                (int)Input::get('y', true),
                Input::get('t', true)
            );

        if($operator === 'IN') {
            $balances->whereIn('typology', $type);
        } elseif($operator === 'NotIn') {
            $balances->whereNotIn('typology', $type);
        }

        $balances = $balances->orderBy('year', 'DESC')
            ->orderBy('id', 'DESC')
            ->paginate(20, ['id', 'name', 'year', 'typology'], 'p', (int)Input::get('p', true))
            ->onEachSide(2)
            ->appends(Input::get(['n', 'y', 'sec_token']))
            ->setPath(currentUrl());

        // Richiesta del solo frammento (paginazione AJAX): essendo getDataResults() chiamato da
        // tutte le pagine di questo controller prima del lavoro pesante (form filtro, render
        // completo), un solo controllo qui copre tutte quelle pagine.
        if (isFrontFragmentRequest()) {
            $resultsArray = $balances->toArray();
            $table = $this->createTableRows($resultsArray);
            renderFrontFragment(['table' => !empty($table) ? $table->generate() : null, 'instances' => $resultsArray]);
            exit;
        }

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($balances) && !empty($balances->toArray()['data'])) {

            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = BalanceSheetsModel::select(['object_balance_sheets.created_at']);

            if($operator === 'IN') {
                $firstCreated->whereIn('typology', $type);
            } elseif($operator === 'NotIn') {
                $firstCreated->whereNotIn('typology', $type);
            }

            $firstCreated = optional($firstCreated->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();

            $lasUpdated = BalanceSheetsModel::select(['object_balance_sheets.updated_at']);

            if($operator === 'IN') {
                $lasUpdated->whereIn('typology', $type);
            } elseif($operator === 'NotIn') {
                $lasUpdated->whereNotIn('typology', $type);
            }

            $lasUpdated = optional($lasUpdated->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        return $balances;
    }

    /**
     * Metodo che crea la tabella con i dati dei risultati della ricerca da mostrare
     *
     * @param $balanceSheets {dati da inserire nella tabella}
     * @return Table|null
     * @throws Exception
     */
    private function createTableRows($balanceSheets = null): ?Table
    {
        $table = null;

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        if (!empty($balanceSheets['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();
            $table->set_heading('Nome', 'Anno', 'Tipologia');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($balanceSheets['data'] as $balance) {

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $balance['id'] . '/' . urlTitle($balance['name'])) . '" data-id="' . $balance['id'] . ' ">' . escapeXss($balance['name']) . '</a>',
                    escapeXss($balance['year']),
                    escapeXss($balance['typology'])
                );
            }
        }

        return $table;
    }

    /**
     * Metodo chiamato per la pagina "Piano degli indicatori e dei risultati attesi di bilancio"
     * ID sezione 131
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexExpectedResults(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Validatore dei filtri di ricerca
        $validator = new Validator();
        if (Input::get('n') != null ||
            Input::get('y') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('nome')->value(Input::get('n'))->maxLength(40)->end();
        $validator->label('anno')->value(Input::get('y'))->isDate('Y')->end();

        if (!$validator->isSuccess()) {
            echo showError('Errore nella validazione dei dati', $validator->getErrorsHtml(), $validator->getErrorStatusCode());
            die();
        }

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

        // Recupero i bilanci piano degli indicatori e dei risultati attesi di bilancio da mostrare
        $balanceSheets = $this->getDataResults(['piano indicatori e risultati', 'provvedimenti'], 'IN', $data);

        $balanceSheets = !empty($balanceSheets) ? $balanceSheets->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($balanceSheets);

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
                'content' => '<div class="row">',
            ],

            [
                'prefix' => '<div class="form-group col-lg-6 mb-5">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'n',
                    'id' => 'n',
                    
                    'value' => '{n}'
                ],
                'label' => [
                    'for' => 'n',
                    'text' => 'Nome',
                ],
            ],

            [
                'prefix' => '<div class="form-group select-wrapper col-lg-6 mb-5">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{y}',
                'name' => 'y',
                'extra' => 'id="y"',
                'label' => [
                    'for' => 'y',
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
            'n' => Input::get('n'),
            'y' => Input::get('y')
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value) {
            if (!empty($value)) {
                $countFilterFields += 1;
            }
        }

        // Anni per la ricerca
        $resultYears = BalanceSheetsModel::select('year')
            ->where('typology', '=', 'piano indicatori e risultati')
            ->groupBy('year')
            ->get()
            ->toArray();

        $years = [null => 'Seleziona un anno'];

        foreach ($resultYears as $resYear) {
            $years[$resYear['year']] = $resYear['year'];
        }

        $formBuilder = new FormBuilder([
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'n' => htmlEscape(Input::get('n', true)),
            'y' => FormBuilder::parseOptions($years, htmlEscape(Input::get('y', true)))
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['table'] = !empty($table) ? $table->generate() : null;
        $data['instances'] = $balanceSheets;
        $data['instance'] = $currentPage;
        $data['paragraphs'] = $contents;
        $data['allowSearch'] = true;
        //Abilita la paginazione AJAX della tabella dei risultati (vedi renderFrontFragment)
        $data['ajaxPagination'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['searchFieldsOpenData'] = $formFilterFields;
        $data['filterUsed'] = $countFilterFields;
        $data['rangeOpenData'] = 2000;
        //Inclusione di eventuali librerie esterne
        $data = $data ?? null;
        $data = $data ?? null;

        renderFront(config('vfo', null, 'app') . '/balance_sheets/balance_sheets', $data, 'frontend');
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
        $element = BalanceSheetsModel::where('id', $elementId)
            ->with(['related_measure' => function ($query) {
                $query->select(['id', 'object']);
            }]);
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
        $data['pageName'] = $element['name'];
        $data['menuPages'] = $sectionFO;
        $data['currentPageId'] = $currentPageId;

        // aggiungi al Breadcrumbs la pagina del dettaglio
        $data['concatBreadcrumb'] = true;
        $data['bread'] = array();
        $data['bread'][] = array('name' => $element['name'], 'link' => '/');

        $data['instance'] = $element;

        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['editRecord'] = true;

        $label = 'balance_sheets';
        $elementId = $element['id'];
        $selectFields = [
            'id',
            'cat_id',
            'archive_name',
            'client_name',
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

        //Inclusione del nodo JSON-LD della pagina
        $data = $data ?? null;

        renderFront(config('vfo', null, 'app') . '/balance_sheets/details', $data, 'frontend');
    }


    protected function getBalanceTypologies(){

        $data = [];
        $data['typologies'] = [
            '' => 'Tutte le tipologie',
            'bilancio preventivo'=>'Bilancio preventivo',
            'bilancio consuntivo'=>'Bilancio consuntivo',
            'piano indicatori e risultati'=>'Piano degli indicatori e risultati attesi',
            'variazioni di bilancio'=>'Variazione di Bilancio'
        ];


        return $data['typologies'];
    }

}
