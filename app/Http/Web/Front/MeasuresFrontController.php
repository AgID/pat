<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Helpers\FormBuilder;
use Helpers\Table;
use Helpers\Utility\AttachmentArchive;
use Model\MeasuresModel;
use Model\PersonnelModel;
use Model\SectionsFoModel;
use Model\StructuresModel;
use System\Input;
use System\Validator;

/**
 * Controller pagina front-end Provvedimenti
 */
class MeasuresFrontController extends BaseFrontController
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
     *
     * @return void
     * @throws Exception
     */
    public function pivot(): void
    {
        $pivot = new PivotController();
        $pivot->index();
    }

    /**
     * Metodo chiamato per la pagina "Provvedimenti organi indirizzo politico",
     * nella sezione dei Provvedimenti
     * ID sezione 103
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexPolitical(): void
    {
        $data = [];

        // Validazione dei campi di input per la ricerca
        $validator = new Validator();
        if (Input::get('str') != null ||
            Input::get('res') != null ||
            Input::get('obj') != null ||
            Input::get('num') != null ||
            Input::get('s') != null ||
            Input::get('e') != null ||
            Input::get('y') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Struttura')->value(Input::get('str'))->isInt()->end();
        $validator->label('Responsabile')->value(Input::get('res'))->isInt()->end();
        $validator->label('Oggetto')->value(Input::get('obj'))->maxLength(30)->end();
        $validator->label('Numero')->value(Input::get('num'))->maxLength(30)->end();
        $validator->label('Data - dal giorno')->value(Input::get('s'))->isDate('Y-m-d')->end();
        $validator->label('Data - fino al giorno')->value(Input::get('e'))->isDate('Y-m-d')->end();
        $validator->label('Anno')->value(Input::get('y'))->isDate('Y')->end();

        if (!$validator->isSuccess()) {
            echo showError('Errore nella validazione dei dati', $validator->getErrorsHtml(), $validator->getErrorStatusCode());
            die();
        }

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

        // Recupero i provvedimenti organi indirizzo politico da mostrare
        $measures = $this->getDataResults(14, $data);

        $measures = !empty($measures) ? $measures->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Form per i filtri di ricerca
        $form = json_encode([
            [
                'type' => 'form_open',
                'name' => 'contest_acts',
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
                'prefix' => '<div class="form-group col-lg-4" style="margin-bottom: 1rem;">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'num',
                    'id' => 'num',

                    'value' => '{number}'
                ],
                'label' => [
                    'for' => 'num',
                    'text' => 'Numero',
                ],
            ],
            [
                'prefix' => '<div class="form-group col-lg-4">',
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
                'prefix' => '<div class="select-wrapper col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{year}',
                'name' => 'y',
                'extra' => 'id="y"',
                'label' => [
                    'for' => 'y',
                    'text' => 'Anno',
                    'class' => 'active'
                ],
            ],
            [
                'prefix' => '<div class="col-lg-6 mt-2">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 's',
                    'value' => '{start_date}',
                    'type' => 'date',
                    'id' => 'start_date',

                ],
                'label' => [
                    'for' => 's',
                    'text' => 'Data - dal giorno',
                    'class' => 'active'
                ]
            ],
            [
                'prefix' => '<div class="col-lg-6 mt-2">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'e',
                    'value' => '{end_date}',
                    'type' => 'date',
                    'id' => 'e',
                ],
                'label' => [
                    'for' => 'e',
                    'text' => 'Data - fino al giorno',
                    'class' => 'active'
                ]
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-6 mt-5">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{structures}',
                'name' => 'str',
                'extra' => 'id="str"',
                'label' => [
                    'for' => 'str',
                    'text' => 'Struttura',
                ]
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-6 mt-5">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{responsibles}',
                'name' => 'res',
                'extra' => 'id="res"',
                'label' => [
                    'for' => 'res',
                    'text' => 'Responsabile',
                ]
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
            'res' => Input::get('res', true),
            'obj' => Input::get('obj', true),
            'num' => Input::get('num', true),
            's' => Input::get('s', true),
            'e' => Input::get('e', true),
            'y' => Input::get('y', true)
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value) {
            if (!empty($value)) {
                $countFilterFields += 1;
            }
        }

        // Anni per la ricerca
        $resultYears = optional(MeasuresModel::select('date')
            ->where('type', 14)
            ->orderBy('date', 'ASC')
            ->first())
            ->toArray();
        $years = [null => 'Seleziona un anno'];
        if (!empty($resultYears)) {
            for ($i = (int)date('Y'); $i >= (int)date('Y', strtotime($resultYears['date'])); $i--) {
                $years[$i] = $i;
            }
        } else {
            $years[date('Y')] = (int)date('Y');
        }


        // Filtro per strutture

        $flagArchived = false;


        $query = StructuresModel::select(['object_structures.id', 'object_structures.structure_name'])
            ->rightJoin(
                'rel_measures_structures',
                'object_structures.id',
                '=',
                'rel_measures_structures.object_structures_id'
            );

        if (!$flagArchived) {
            $query->where(function ($query) {
            });
        }

        $structures = [null => 'Seleziona una struttura'] + $query
                ->orderBy('structure_name', 'ASC')
                ->pluck('structure_name', 'id')
                ->toArray();

        //Filtro personale
        $responsibles = [null => 'Seleziona un responsabile'] + PersonnelModel::select(['object_personnel.id', 'object_personnel.full_name'])
                ->where(function ($query) {
                })
                ->rightJoin('rel_measures_personnel', 'object_personnel.id', '=', 'rel_measures_personnel.object_personnel_id')
                ->orderBy('full_name', 'ASC')
                ->pluck('full_name', 'id')
                ->toArray();

        $objectFilter = htmlEscape(Input::get('obj', true));
        $form = $form ?? null;

        $formBuilder = new FormBuilder([
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'number' => htmlEscape(Input::get('num', true)),
            'object' => $objectFilter,
            'year' => FormBuilder::parseOptions($years, htmlEscape(Input::get('y', true))),
            'start_date' => htmlEscape(Input::get('s', true)),
            'end_date' => htmlEscape(Input::get('e', true)),
            'structures' => FormBuilder::parseOptions($structures, htmlEscape(Input::get('str', true))),
            'responsibles' => FormBuilder::parseOptions($responsibles, htmlEscape(Input::get('res', true)))
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        // Creo la tabella da mostrare nella pagina con i risultati della ricerca
        $table = $this->createTableRows($measures);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $measures;
        $data['instance'] = $currentPage;
        $data['paragraphs'] = $contents;
        $data['allowSearch'] = true;
        //Abilita la paginazione AJAX della tabella dei risultati (vedi renderFrontFragment)
        $data['ajaxPagination'] = true;
        $data['_page_title'] = $currentPage['name'];
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['searchFieldsOpenData'] = $formFilterFields;
        $data['filterUsed'] = $countFilterFields;
        $data['rangeOpenData'] = 2000;
        //Inclusione di eventuali librerie esterne
        $currentPage = $currentPage ?? null;
        $data = $data ?? null;
        $data = $data ?? null;
        renderFront(config('vfo', null, 'app') . '/measures/measures', $data, 'frontend');
    }

    /**
     * Funzione che ritorna i dati da mostrare nella tabella
     *
     * @param null  $type  Tipologia per cui filtrare i dati
     * @param array $data  Dati da passare alla vista
     * @param bool  $accPA
     * @return mixed
     * @throws Exception
     */
    protected function getDataResults($type = null, array &$data = [], bool $accPA = false, $_tag = null): mixed
    {

        // Recupero i provvedimenti da mostrare
        $measures = MeasuresModel::where('type', $type)
            ->with('structures:id,structure_name')
            ->with('personnel:id,full_name');


        $measures = $measures->filter(
                (Input::get('num', true)),
                (Input::get('obj', true)),
                (int)Input::get('y', true),
                Input::get('s', true),
                Input::get('e', true),
                (int)Input::get('str', true),
                (int)Input::get('res', true)
            );


        $measures = $measures->orderBy('date', 'DESC')
//            ->orderBy('number', 'DESC')
            ->orderByRaw('CONVERT(number, SIGNED) desc')
            ->orderBy('object', 'DESC')
            ->paginate(20, ['object_measures.id', 'object', 'type', 'date', 'number'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get(['num', 'obj', 'y', 's', 'e', 'str', 'res', 'sec_token']))
            ->setPath(currentUrl());

        // Richiesta del solo frammento (paginazione AJAX): essendo getDataResults() chiamato da
        // tutte le pagine di questo controller prima del lavoro pesante (form filtro, render
        // completo), un solo controllo qui copre tutte quelle pagine.
        if (isFrontFragmentRequest()) {
            $resultsArray = $measures->toArray();
            $table = $this->createTableRows($resultsArray);
            renderFrontFragment(['table' => !empty($table) ? $table->generate() : null, 'instances' => $resultsArray]);
            exit;
        }

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($measures) && !empty($measures->toArray()['data'])) {

            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(MeasuresModel::select(['created_at'])
                ->where('type', $type)
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(MeasuresModel::select(['updated_at'])
                ->where('type', $type)
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        return $measures;
    }

    /**
     * Metodo che crea la tabella con i dati dei risultati della ricerca da mostrare
     *
     * @param $measures {dati da inserire nella tabella}
     * @return Table|null
     * @throws Exception
     */
    protected function createTableRows($measures = null): ?Table
    {
        $currentPageId = (int)uri()->segment(2, 0);
        $table = null;

        if (!empty($measures['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();
            $table->set_heading('Numero', 'Oggetto', 'Struttura', 'Responsabile', 'Data');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($measures['data'] as $measure) {

                // Creo link per le strutture
                $structures = $measure['structures'];
                $filter = function ($structure) {
                    return '<a href="' . siteUrl('page/40/details/' . $structure['id'] . '/' . urlTitle($structure['structure_name'])) . '">' . escapeXss($structure['structure_name']) . '</a>';
                };
                $linkedStructures = array_map($filter, $structures);

                // Creo link per i responsabili
                $responsibles = $measure['personnel'];
                $filter = function ($responsible) {
                    return '<a href="' . siteUrl('page/64/details/' . $responsible['id'] . '/' . urlTitle($responsible['full_name'])) . '">' . escapeXss($responsible['full_name']) . '</a>';
                };
                $linkedResponsibles = array_map($filter, $responsibles);

                // Aggiungo le righe
                $table->add_row(
                    $measure['number'],
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $measure['id'] . '/' . urlTitle($measure['object'])) . '" data-id="' . $measure['id'] . ' ">' . escapeXss($measure['object']) . '</a>',
                    implode(', ', $linkedStructures),
                    implode(', ', $linkedResponsibles),
                    !empty($measure['date']) ? date('d/m/Y', strtotime($measure['date'])) : null
                );
            }
        }

        return $table;
    }

    /**
     * Metodo chiamato per la pagina "Provvedimenti dirigenti amministrativi"
     * Id sezione 106
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexAdministrative(): void
    {
        $data = [];

        // Validazione dei campi di input per la ricerca
        $validator = new Validator();
        if (Input::get('str') != null ||
            Input::get('res') != null ||
            Input::get('obj') != null ||
            Input::get('num') != null ||
            Input::get('s') != null ||
            Input::get('e') != null ||
            Input::get('y') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Struttura')->value(Input::get('str'))->isInt()->end();
        $validator->label('Responsabile')->value(Input::get('res'))->isInt()->end();
        $validator->label('Oggetto')->value(Input::get('obj'))->maxLength(30)->end();
        $validator->label('Numero')->value(Input::get('num'))->maxLength(30)->end();
        $validator->label('Data - dal giorno')->value(Input::get('s'))->isDate('Y-m-d')->end();
        $validator->label('Data - fino al giorno')->value(Input::get('e'))->isDate('Y-m-d')->end();
        $validator->label('Anno')->value(Input::get('y'))->isDate('Y')->end();

        if (!$validator->isSuccess()) {
            echo showError('Errore nella validazione dei dati', $validator->getErrorsHtml(), $validator->getErrorStatusCode());
            die();
        }

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

        // Recupero i provvedimenti dirigenti amministrativi da mostrare
        $measures = $this->getDataResults(13, $data);

        $measures = !empty($measures) ? $measures->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Form per i filtri di ricerca
        $form = json_encode([
            [
                'type' => 'form_open',
                'name' => 'contest_acts',
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
                'prefix' => '<div class="form-group col-lg-4" style="margin-bottom: 1rem;">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'num',
                    'id' => 'num',

                    'value' => '{number}'
                ],
                'label' => [
                    'for' => 'num',
                    'text' => 'Numero',
                ],
            ],
            [
                'prefix' => '<div class="form-group col-lg-4" style="margin-bottom: 1rem;">',
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
                'prefix' => '<div class="select-wrapper col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{year}',
                'name' => 'y',
                'extra' => 'id="y"',
                'label' => [
                    'for' => 'y',
                    'text' => 'Anno',
                    'class' => 'active'
                ],
            ],
            [
                'prefix' => '<div class="col-lg-6">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 's',
                    'value' => '{start_date}',
                    'type' => 'date',
                    'id' => 's',

                ],
                'label' => [
                    'for' => 's',
                    'text' => 'Data - dal giorno',
                    'class' => 'active'
                ]
            ],
            [
                'prefix' => '<div class="col-lg-6 ">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'e',
                    'value' => '{end_date}',
                    'type' => 'date',
                    'id' => 'e',

                ],
                'label' => [
                    'for' => 'e',
                    'text' => 'Data - fino al giorno',
                    'class' => 'active'
                ]
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-6 mt-5">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{structures}',
                'name' => 'str',
                'extra' => 'id="str"',
                'label' => [
                    'for' => 'str',
                    'text' => 'Struttura',
                ]
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-6 mt-5">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{responsibles}',
                'name' => 'res',
                'extra' => 'id="res"',
                'label' => [
                    'for' => 'res',
                    'text' => 'Responsabile',
                ]
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
            'res' => Input::get('res', true),
            'obj' => Input::get('obj', true),
            'num' => Input::get('num', true),
            's' => Input::get('s', true),
            'e' => Input::get('e', true),
            'y' => Input::get('y', true)
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value) {
            if (!empty($value)) {
                $countFilterFields += 1;
            }
        }

        // Anni per la ricerca
        $resultYears = optional(MeasuresModel::select('date')
            ->where('type', 13)
            ->orderBy('date', 'ASC')
            ->first())
            ->toArray();
        $years = [null => 'Seleziona un anno'];
        if (!empty($resultYears)) {
            for ($i = (int)date('Y'); $i >= (int)date('Y', strtotime($resultYears['date'])); $i--) {
                $years[$i] = $i;
            }
        } else {
            $years[date('Y')] = (int)date('Y');
        }

        // Filtro per strutture
        $flagArchived = false;


        $query = StructuresModel::select(['object_structures.id', 'object_structures.structure_name'])
            ->rightJoin(
                'rel_measures_structures',
                'object_structures.id',
                '=',
                'rel_measures_structures.object_structures_id'
            );

        if (!$flagArchived) {
            $query->where(function ($query) {
            });
        }

        $structures = [null => 'Seleziona una struttura'] + $query
                ->orderBy('structure_name', 'ASC')
                ->pluck('structure_name', 'id')
                ->toArray();

        //Filtro personale
        $responsibles = [null => 'Seleziona un responsabile'] + PersonnelModel::select(['object_personnel.id', 'object_personnel.full_name'])
                ->where(function ($query) {
                    $archiveConditionHandled = false;


                    if (!$archiveConditionHandled) {
                        $query->where(function ($query) {
                            $query->where('object_personnel.archived', 0)
                                ->orWhereNull('object_personnel.archived');
                        })->orWhere(function ($query) {
                            $query->where('object_personnel.archived', 1)
                                ->where('object_personnel.archived_end_date', '>=', date('Y-m-d H:i:s'));
                        });
                    }
                })
                ->rightJoin('rel_measures_personnel', 'object_personnel.id', '=', 'rel_measures_personnel.object_personnel_id')
                ->orderBy('full_name', 'ASC')
                ->pluck('full_name', 'id')
                ->toArray();

        $formBuilder = new FormBuilder([
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'number' => htmlEscape(Input::get('num', true)),
            'object' => htmlEscape(Input::get('obj', true)),
            'year' => FormBuilder::parseOptions($years, htmlEscape(Input::get('y', true))),
            'start_date' => htmlEscape(Input::get('s', true)),
            'end_date' => htmlEscape(Input::get('e', true)),
            'structures' => FormBuilder::parseOptions($structures, htmlEscape(Input::get('str', true))),
            'responsibles' => FormBuilder::parseOptions($responsibles, htmlEscape(Input::get('res', true)))
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        // Creo la tabella da mostrare nella pagina con i risultati della ricerca
        $table = $this->createTableRows($measures);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $measures;
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
        $currentPage = $currentPage ?? null;
        $data = $data ?? null;
        $data = $data ?? null;

        renderFront(config('vfo', null, 'app') . '/measures/measures', $data, 'frontend');
    }

    /**
     * @description Metodo chiamato per la pagina "Accordi con soggetti privati o altre P.A."
     * ID sezione 107
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexDirectsAgreementsPa(): void
    {
        $this->pivot();
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
        $element = MeasuresModel::where('id', $elementId);

        $element = $element->with('structures:id,structure_name,archived')
            ->with('personnel:id,full_name')
            ->with('relative_procedure_contraent:id,object')
            ->with('bdncp_procedures:object_bdncp_procedure.id,object')
            ->with('charges:id,title');


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

        // aggiungi al Breadcrumbs la pagina del dettaglio
        $data['concatBreadcrumb'] = true;
        $data['bread'] = array();
        $data['bread'][] = array('name' => $element['object'], 'link' => '/');
        $data['instance'] = $element;
        $data['currentPageId'] = $currentPageId;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['editRecord'] = true;

        $label = 'measures';
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

        renderFront(config('vfo', null, 'app') . '/measures/details', $data, 'frontend');
    }
}
