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
use Model\ContestModel;
use Model\SectionsFoModel;
use Model\StructuresModel;
use System\Input;
use System\Log;
use System\Validator;

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
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Validazione dei campi di input per la ricerca
        $validator = new Validator();
        if (Input::get('object') != null ||
            Input::get('str') != null ||
            Input::get('start_p_date') != null ||
            Input::get('end_p_date') != null ||
            Input::get('start_s_date') != null ||
            Input::get('end_s_date') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Oggetto')->value(Input::get('object'))->maxLength(150)->end();
        $validator->label('Struttura di riferimento')->value(Input::get('str'))->isArray()->end();
        $validator->label('Data di pubblicazione dal')->value(Input::get('start_p_date'))->isDate('Y-m-d')->end();
        $validator->label('Data di pubblicazione al')->value(Input::get('end_p_date'))->isDate('Y-m-d')->end();
        $validator->label('Data di scadenza dal')->value(Input::get('start_s_date'))->isDate('Y-m-d')->end();
        $validator->label('Data di scadenza al')->value(Input::get('end_s_date'))->isDate('Y-m-d')->end();

        if (!$validator->isSuccess()) {
            echo showError('Errore nella validazione dei dati', $validator->getErrorsHtml(), $validator->getErrorStatusCode());
            die();
        }

        $filters = ['object', 'str', 'start_p_date', 'end_p_date', 'start_s_date', 'end_s_date', 'sec_token'];

        // Recupero i concorsi attivi da mostrare
        $contests = ContestModel::where('activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
            ->filter(
                Input::get('object', true),
                Input::get('str', true, true, true, [Input::CAST_INT]),
                Input::get('start_p_date', true),
                Input::get('end_p_date', true),
                Input::get('start_s_date', true),
                Input::get('end_s_date', true),
            );

        $contests = $contests->orderBy('activation_date', 'DESC')
            ->distinct(['object_contest.id'])
            ->paginate(20, ['object_contest.id', 'object_contest.object', 'object_contest.activation_date', 'object_contest.expiration_date', 'object_contest.typology'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get($filters))
            ->setPath(currentUrl());

        $contests = !empty($contests) ? $contests->toArray() : [];

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($contests, "", true);

        $data['table'] = !empty($table) ? $table->generate() : null;
        $data['instances'] = $contests;

        // Richiesta del solo frammento (paginazione AJAX): tabella e paginazione sono pronte,
        // tutto il resto (pagina corrente, menu, filtri, contenuti, layout) serve solo alla
        // pagina completa e viene saltato.
        if (renderFrontFragment($data)) {

            return;

        }

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'section_fo.id', 'parent_id', 'controller_open_data', 'archive_name',
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

        //Strutture per il filtro di ricerca
        $offices = StructuresModel::where('articulation', 1)
            ->where(function ($query) {
            })
            ->orderBy('structure_name', 'ASC')
            ->pluck('structure_name', 'id')
            ->toArray();

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

        $filter = [
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
                'prefix' => '<div class="form-group col-lg-6" style="margin-bottom: 1rem;">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'object',
                    'id' => 'object',
                    'value' => '{object}'
                ],
                'label' => [
                    'for' => 'object',
                    'text' => 'Oggetto',
                ],
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-6">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{structure}',
                'name' => 'str[]',
                'extra' => 'id="str" multiple class="ts-select-pat"',
                'label' => [
                    'for' => 'str',
                    'text' => 'Struttura di riferimento',
                ],
            ],
            [
                'prefix' => '<div class="col-lg-6 mt-2">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'start_p_date',
                    'value' => '{start_p_date}',
                    'type' => 'date',
                    'id' => 'start_p_date',
                    'class' => 'active',

                ],
                'label' => [
                    'for' => 'start_p_date',
                    'text' => 'Data di pubblicazione - dal',
                ]
            ],
            [
                'prefix' => '<div class="col-lg-6 mt-2">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'end_p_date',
                    'value' => '{end_p_date}',
                    'type' => 'date',
                    'id' => 'end_p_date',
                    'class' => 'active',

                ],
                'label' => [
                    'for' => 'end_p_date',
                    'text' => 'Data di pubblicazione - al',
                ]
            ],
            [
                'prefix' => '<div class="col-lg-6 mt-2">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'start_s_date',
                    'value' => '{start_s_date}',
                    'type' => 'date',
                    'id' => 'start_s_date',
                    'class' => 'active',

                ],
                'label' => [
                    'for' => 'start_s_date',
                    'text' => 'Data di scadenza - dal',
                ]
            ],
            [
                'prefix' => '<div class="col-lg-6 mt-2">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'end_s_date',
                    'value' => '{end_s_date}',
                    'type' => 'date',
                    'id' => 'end_s_date',
                    'class' => 'active',

                ],
                'label' => [
                    'for' => 'end_s_date',
                    'text' => 'Data di scadenza - al',
                ]
            ],
            "extra_filter" => [],
            "extra_filter2" => [],
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
        ];

        // Form per i filtri di ricerca
        $form = json_encode($filter);

        $formBuilderFields = [
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'object' => htmlEscape(Input::get('object', true)),
            'structure' => FormBuilder::parseOptions($offices, htmlEscape(Input::get('str', true))),
            'start_p_date' => htmlEscape(Input::get('start_p_date', true)),
            'end_p_date' => htmlEscape(Input::get('end_p_date', true)),
            'start_s_date' => htmlEscape(Input::get('start_s_date', true)),
            'end_s_date' => htmlEscape(Input::get('end_s_date', true)),
        ];

        $formBuilder = new FormBuilder($formBuilderFields);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        $formFilterFields = [
            'object' => Input::get('object', true),
            'str' => Input::get('str', true),
            'start_p_date' => Input::get('start_p_date', true),
            'end_p_date' => Input::get('end_p_date', true),
            'start_s_date' => Input::get('start_s_date', true),
            'end_s_date' => Input::get('end_s_date', true),
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value) {
            if (!empty($value)) {
                $countFilterFields += 1;
            }
        }

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['instance'] = $currentPage;
        $data['ajaxPagination'] = true;
        $data['paragraphs'] = $contents;
        $data['allowSearch'] = true;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['rangeOpenData'] = 2000;
        $data['searchFieldsOpenData'] = $formFilterFields;
        $data['filterUsed'] = $countFilterFields;

        $data['archive'] = null;

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
    protected function createTableRows($contests = null, string $typology = '', bool $showTypology = false): ?Table
    {

        $currentPageId = (int)uri()->segment(2, 0);
        $table = null;

        if (!empty($contests['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();
            $column = ['Oggetto', 'Data di pubblicazione'];

            if ($typology !== 'esito') {
                $column[] = 'Data di scadenza';
            }

            if ($showTypology) {
                array_unshift($column, 'Tipologia');
            }

            $table->set_heading($column);

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($contests['data'] as $contest) {

                $row = [
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $contest['id'] . '/' . urlTitle($contest['object'])) . '" data-id="' . $contest['id'] . ' ">' . html_entity_decode(escapeXss($contest['object'])) . '</a>',
                    !empty($contest['activation_date']) ? date('d/m/Y', strtotime($contest['activation_date'])) : null,
                ];

                if ($typology !== 'esito') {
                    $row[] = !empty($contest['expiration_date']) ? date('d/m/Y', strtotime($contest['expiration_date'])) : null;
                }

                if ($showTypology) {
                    $typ = !empty($contest['typology']) ? ucfirst($contest['typology']) : '';
                    array_unshift($row, $typ);
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
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Validazione dei campi di input per la ricerca
        $validator = new Validator();
        if (Input::get('object') != null ||
            Input::get('str') != null ||
            Input::get('start_p_date') != null ||
            Input::get('end_p_date') != null ||
            Input::get('start_s_date') != null ||
            Input::get('end_s_date') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Oggetto')->value(Input::get('object'))->maxLength(150)->end();
        $validator->label('Struttura di riferimento')->value(Input::get('str'))->isInt()->end();
        $validator->label('Data di pubblicazione dal')->value(Input::get('start_p_date'))->isDate('Y-m-d')->end();
        $validator->label('Data di pubblicazione al')->value(Input::get('end_p_date'))->isDate('Y-m-d')->end();
        $validator->label('Data di scadenza dal')->value(Input::get('start_s_date'))->isDate('Y-m-d')->end();
        $validator->label('Data di scadenza al')->value(Input::get('end_s_date'))->isDate('Y-m-d')->end();

        if (!$validator->isSuccess()) {
            echo showError('Errore nella validazione dei dati', $validator->getErrorsHtml(), $validator->getErrorStatusCode());
            die();
        }

        // Recupero i concorsi attivi da mostrare
        $contests = ContestModel::where('typology', 'concorso')
            ->where('activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
            ->where(
                function ($query) {
                    $query->where('expiration_date', '>=', date('Y-m-d H:i:s'))
                        ->orWhereNull('expiration_date');
                }
            )
            ->where(function ($query) {
                $query->where('expiration_contest_date', '>=', date('Y-m-d H:i:s'))
                    ->orWhereNull('expiration_contest_date');
            })
            ->filter(
                (Input::get('object', true)),
                (int)Input::get('str', true),
                Input::get('start_p_date', true),
                Input::get('end_p_date', true),
                Input::get('start_s_date', true),
                Input::get('end_s_date', true),
            );

        $filters = ['object', 'start_p_date', 'end_p_date', 'start_s_date', 'end_s_date', 'sec_token'];

        $contests = $contests->orderBy('expiration_date', 'DESC')
            ->orderBy('activation_date', 'DESC')
            ->paginate(20, ['id', 'object', 'activation_date', 'expiration_date'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get($filters))
            ->setPath(currentUrl());


        $contests = !empty($contests) ? $contests->toArray() : [];

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($contests, 'concorso');

        $data['table'] = !empty($table) ? $table->generate() : null;
        $data['instances'] = $contests;

        if (renderFrontFragment($data)) {

            return;

        }

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

        //Strutture per il filtro di ricerca
        $offices = [null => 'Seleziona una struttura'] + StructuresModel::where('articulation', 1)
                ->where(function ($query) {
                })
                ->orderBy('structure_name', 'ASC')
                ->pluck('structure_name', 'id')
                ->toArray();

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($contests['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ContestModel::select(['created_at'])
                ->where('typology', 'concorso')
                ->where('activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->where(
                    function ($query) {
                        $query->where('expiration_date', '>=', date('Y-m-d H:i:s'))
                            ->orWhereNull('expiration_date');
                    }
                )
                ->where(function ($query) {
                    $query->where('expiration_contest_date', '>=', date('Y-m-d H:i:s'))
                        ->orWhereNull('expiration_contest_date');
                })
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ContestModel::select(['updated_at'])
                ->where('typology', 'concorso')
                ->where('activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->where(
                    function ($query) {
                        $query->where('expiration_date', '>=', date('Y-m-d H:i:s'))
                            ->orWhereNull('expiration_date');
                    }
                )
                ->where(function ($query) {
                    $query->where('expiration_contest_date', '>=', date('Y-m-d H:i:s'))
                        ->orWhereNull('expiration_contest_date');
                })
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Form per i filtri di ricerca
        $filter = [
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
                'prefix' => '<div class="form-group col-lg-6" style="margin-bottom: 1rem;;">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'object',
                    'id' => 'object',

                    'value' => '{object}'
                ],
                'label' => [
                    'for' => 'object',
                    'text' => 'Oggetto',
                ],
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
                    'text' => 'Struttura di riferimento',
                ],
            ],
            [
                'prefix' => '<div class="col-lg-6 mt-2">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'start_p_date',
                    'value' => '{start_p_date}',
                    'type' => 'date',
                    'id' => 'start_p_date',
                    'class' => 'active',

                ],
                'label' => [
                    'for' => 'start_p_date',
                    'text' => 'Data di pubblicazione - dal',
                ]
            ],
            [
                'prefix' => '<div class="col-lg-6 mt-2">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'end_p_date',
                    'value' => '{end_p_date}',
                    'type' => 'date',
                    'id' => 'end_p_date',
                    'class' => 'active',

                ],
                'label' => [
                    'for' => 'end_p_date',
                    'text' => 'Data di pubblicazione - al',
                ]
            ],
            [
                'prefix' => '<div class="col-lg-6 mt-2">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'start_s_date',
                    'value' => '{start_s_date}',
                    'type' => 'date',
                    'id' => 'start_s_date',
                    'class' => 'active',

                ],
                'label' => [
                    'for' => 'start_s_date',
                    'text' => 'Data di scadenza - dal',
                ]
            ],
            [
                'prefix' => '<div class="col-lg-6 mt-2">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'end_s_date',
                    'value' => '{end_s_date}',
                    'type' => 'date',
                    'id' => 'end_s_date',
                    'class' => 'active',

                ],
                'label' => [
                    'for' => 'end_s_date',
                    'text' => 'Data di scadenza - al',
                ]
            ],
            "extra_filter" => [],
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
        ];

        $form = json_encode($filter);

        $formFilterFields = [
            'object' => Input::get('object', true),
            'structure' => Input::get('str', true),
            'start_p_date' => Input::get('start_p_date', true),
            'end_p_date' => Input::get('end_p_date', true),
            'start_s_date' => Input::get('start_s_date', true),
            'end_s_date' => Input::get('end_s_date', true),
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value) {
            if (!empty($value)) {
                $countFilterFields += 1;
            }
        }

        $formFilterFields = [
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'object' => htmlEscape(Input::get('object', true)),
            'structure' => FormBuilder::parseOptions($offices, htmlEscape(Input::get('str', true))),
            'start_p_date' => htmlEscape(Input::get('start_p_date', true)),
            'end_p_date' => htmlEscape(Input::get('end_p_date', true)),
            'start_s_date' => htmlEscape(Input::get('start_s_date', true)),
            'end_s_date' => htmlEscape(Input::get('end_s_date', true)),
        ];

        $formBuilder = new FormBuilder($formFilterFields);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
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
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

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
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Validazione dei campi di input per la ricerca
        $validator = new Validator();
        if (Input::get('object') != null ||
            Input::get('str') != null ||
            Input::get('start_p_date') != null ||
            Input::get('end_p_date') != null ||
            Input::get('start_s_date') != null ||
            Input::get('end_s_date') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Oggetto')->value(Input::get('object'))->maxLength(150)->end();
        $validator->label('Struttura di riferimento')->value(Input::get('str'))->isInt()->end();
        $validator->label('Data di pubblicazione dal')->value(Input::get('start_p_date'))->isDate('Y-m-d')->end();
        $validator->label('Data di pubblicazione al')->value(Input::get('end_p_date'))->isDate('Y-m-d')->end();
        $validator->label('Data di scadenza dal')->value(Input::get('start_s_date'))->isDate('Y-m-d')->end();
        $validator->label('Data di scadenza al')->value(Input::get('end_s_date'))->isDate('Y-m-d')->end();

        if (!$validator->isSuccess()) {
            echo showError('Errore nella validazione dei dati', $validator->getErrorsHtml(), $validator->getErrorStatusCode());
            die();
        }

        // Data del giorno successivo al corrente
        $tomorrow = date("Y-m-d H:i:s", strtotime('tomorrow'));

        // Recupero i concorsi scaduti da mostrare
        $contests = ContestModel::where('typology', 'concorso')
            ->where(function ($query) {
                $query->where('expiration_contest_date', '>=', date('Y-m-d H:i:s'))
                    ->orWhereNull('expiration_contest_date');
            })
            ->where('expiration_date', '<', date('Y-m-d H:i:s'))
            ->filter(
                (Input::get('object', true)),
                (int)Input::get('str', true),
                Input::get('start_p_date', true),
                Input::get('end_p_date', true),
                Input::get('start_s_date', true),
                Input::get('end_s_date', true),
            );

        $filters = ['object', 'start_p_date', 'end_p_date', 'start_s_date', 'end_s_date', 'sec_token'];

        $contests = $contests->orderBy('expiration_date', 'DESC')
            ->paginate(20, ['id', 'object', 'activation_date', 'expiration_date', 'expiration_contest_date'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get($filters))
            ->setPath(currentUrl());

        $contests = !empty($contests) ? $contests->toArray() : [];

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($contests, 'concorso');

        $data['table'] = !empty($table) ? $table->generate() : null;
        $data['instances'] = $contests;

        if (renderFrontFragment($data)) {

            return;

        }

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

        //Strutture per il filtro di ricerca
        $offices = [null => 'Seleziona una struttura'] + StructuresModel::where('articulation', 1)
                ->where(function ($query) {
                })
                ->orderBy('structure_name', 'ASC')
                ->pluck('structure_name', 'id')
                ->toArray();

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

        // Form per i filtri di ricerca
        $filter = [
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
                'prefix' => '<div class="form-group col-lg-6" style="margin-bottom: 1rem;">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'object',
                    'id' => 'object',

                    'value' => '{object}'
                ],
                'label' => [
                    'for' => 'object',
                    'text' => 'Oggetto',
                ],
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
                    'text' => 'Struttura di riferimento',
                ],
            ],
            [
                'prefix' => '<div class="col-lg-6 mt-2">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'start_p_date',
                    'value' => '{start_p_date}',
                    'type' => 'date',
                    'id' => 'start_p_date',
                    'class' => 'active',

                ],
                'label' => [
                    'for' => 'start_p_date',
                    'text' => 'Data di pubblicazione - dal',
                ]
            ],
            [
                'prefix' => '<div class="col-lg-6 mt-2">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'end_p_date',
                    'value' => '{end_p_date}',
                    'type' => 'date',
                    'id' => 'end_p_date',
                    'class' => 'active',

                ],
                'label' => [
                    'for' => 'end_p_date',
                    'text' => 'Data di pubblicazione - al',
                ]
            ],
            [
                'prefix' => '<div class="col-lg-6 mt-2">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'start_s_date',
                    'value' => '{start_s_date}',
                    'type' => 'date',
                    'id' => 'start_s_date',
                    'class' => 'active',

                ],
                'label' => [
                    'for' => 'start_s_date',
                    'text' => 'Data di scadenza - dal',
                ]
            ],
            [
                'prefix' => '<div class="col-lg-6 mt-2">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'end_s_date',
                    'value' => '{end_s_date}',
                    'type' => 'date',
                    'id' => 'end_s_date',
                    'class' => 'active',

                ],
                'label' => [
                    'for' => 'end_s_date',
                    'text' => 'Data di scadenza - al',
                ]
            ],
            "extra_filter" => [],
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
        ];

        $form = json_encode($filter);

        $formFilterFields = [
            'object' => Input::get('object', true),
            'structure' => Input::get('str', true),
            'start_p_date' => Input::get('start_p_date', true),
            'end_p_date' => Input::get('end_p_date', true),
            'start_s_date' => Input::get('start_s_date', true),
            'end_s_date' => Input::get('end_s_date', true),
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value) {
            if (!empty($value)) {
                $countFilterFields += 1;
            }
        }

        $formFilterFields = [
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'object' => htmlEscape(Input::get('object', true)),
            'structure' => FormBuilder::parseOptions($offices, htmlEscape(Input::get('str', true))),
            'start_p_date' => htmlEscape(Input::get('start_p_date', true)),
            'end_p_date' => htmlEscape(Input::get('end_p_date', true)),
            'start_s_date' => htmlEscape(Input::get('start_s_date', true)),
            'end_s_date' => htmlEscape(Input::get('end_s_date', true)),
        ];

        $formBuilder = new FormBuilder($formFilterFields);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
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
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        renderFront(config('vfo', null, 'app') . '/contests/contests', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Concorsi terminati",
     * nella sezione dei Bandi di concorso
     * Id sezione 303
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexTerminated(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Validazione dei campi di input per la ricerca
        $validator = new Validator();
        if (Input::get('object') != null ||
            Input::get('str') != null ||
            Input::get('start_p_date') != null ||
            Input::get('end_p_date') != null ||
            Input::get('start_s_date') != null ||
            Input::get('end_s_date') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Oggetto')->value(Input::get('object'))->maxLength(150)->end();
        $validator->label('Struttura di riferimento')->value(Input::get('str'))->isInt()->end();
        $validator->label('Data di pubblicazione dal')->value(Input::get('start_p_date'))->isDate('Y-m-d')->end();
        $validator->label('Data di pubblicazione al')->value(Input::get('end_p_date'))->isDate('Y-m-d')->end();
        $validator->label('Data di scadenza dal')->value(Input::get('start_s_date'))->isDate('Y-m-d')->end();
        $validator->label('Data di scadenza al')->value(Input::get('end_s_date'))->isDate('Y-m-d')->end();

        if (!$validator->isSuccess()) {
            echo showError('Errore nella validazione dei dati', $validator->getErrorsHtml(), $validator->getErrorStatusCode());
            die();
        }

        // Recupero i concorsi scaduti da mostrare
        $contests = ContestModel::where('typology', 'concorso')
            ->where('expiration_contest_date', '<', date('Y-m-d'))
            ->filter(
                (Input::get('object', true)),
                (int)Input::get('str', true),
                Input::get('start_p_date', true),
                Input::get('end_p_date', true),
                Input::get('start_s_date', true),
                Input::get('end_s_date', true),
            );

        $filters = ['object', 'start_p_date', 'end_p_date', 'start_s_date', 'end_s_date', 'sec_token'];

        $contests = $contests->orderBy('expiration_contest_date', 'DESC')
            ->paginate(20, ['id', 'object', 'activation_date', 'expiration_date', 'expiration_contest_date'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get($filters))
            ->setPath(currentUrl());

        $contests = !empty($contests) ? $contests->toArray() : [];

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($contests, 'concorso');

        $data['table'] = !empty($table) ? $table->generate() : null;
        $data['instances'] = $contests;

        // Richiesta del solo frammento (paginazione AJAX): tabella e paginazione sono pronte,
        // tutto il resto (pagina corrente, menu, filtri, contenuti) serve solo alla pagina
        // completa e viene saltato.
        if (renderFrontFragment($data)) {

            return;

        }

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

        //Strutture per il filtro di ricerca
        $offices = [null => 'Seleziona una struttura'] + StructuresModel::where('articulation', 1)
                ->where(function ($query) {
                })
                ->orderBy('structure_name', 'ASC')
                ->pluck('structure_name', 'id')
                ->toArray();

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($contests['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ContestModel::select(['created_at'])
                ->where('typology', 'concorso')
                ->where('expiration_contest_date', '<', date('Y-m-d'))
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ContestModel::select(['updated_at'])
                ->where('typology', 'concorso')
                ->where('expiration_contest_date', '<', date('Y-m-d'))
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Form per i filtri di ricerca
        $filter = [
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
                'prefix' => '<div class="form-group col-lg-6" style="margin-bottom: 1rem;">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'object',
                    'id' => 'object',

                    'value' => '{object}'
                ],
                'label' => [
                    'for' => 'object',
                    'text' => 'Oggetto',
                ],
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
                    'text' => 'Struttura di riferimento',
                ],
            ],
            [
                'prefix' => '<div class="col-lg-6 mt-2">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'start_p_date',
                    'value' => '{start_p_date}',
                    'type' => 'date',
                    'id' => 'start_p_date',
                    'class' => 'active',

                ],
                'label' => [
                    'for' => 'start_p_date',
                    'text' => 'Data di pubblicazione - dal',
                ]
            ],
            [
                'prefix' => '<div class="col-lg-6 mt-2">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'end_p_date',
                    'value' => '{end_p_date}',
                    'type' => 'date',
                    'id' => 'end_p_date',
                    'class' => 'active',

                ],
                'label' => [
                    'for' => 'end_p_date',
                    'text' => 'Data di pubblicazione - al',
                ]
            ],
            [
                'prefix' => '<div class="col-lg-6 mt-2">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'start_s_date',
                    'value' => '{start_s_date}',
                    'type' => 'date',
                    'id' => 'start_s_date',
                    'class' => 'active',

                ],
                'label' => [
                    'for' => 'start_s_date',
                    'text' => 'Data di scadenza - dal',
                ]
            ],
            [
                'prefix' => '<div class="col-lg-6 mt-2">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'end_s_date',
                    'value' => '{end_s_date}',
                    'type' => 'date',
                    'id' => 'end_s_date',
                    'class' => 'active',

                ],
                'label' => [
                    'for' => 'end_s_date',
                    'text' => 'Data di scadenza - al',
                ]
            ],
            "extra_filter" => [],
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
        ];

        $form = json_encode($filter);

        $formFilterFields = [
            'object' => Input::get('object', true),
            'structure' => Input::get('str', true),
            'start_p_date' => Input::get('start_p_date', true),
            'end_p_date' => Input::get('end_p_date', true),
            'start_s_date' => Input::get('start_s_date', true),
            'end_s_date' => Input::get('end_s_date', true),
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value) {
            if (!empty($value)) {
                $countFilterFields += 1;
            }
        }

        $formFilterFields = [
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'object' => htmlEscape(Input::get('object', true)),
            'structure' => FormBuilder::parseOptions($offices, htmlEscape(Input::get('str', true))),
            'start_p_date' => htmlEscape(Input::get('start_p_date', true)),
            'end_p_date' => htmlEscape(Input::get('end_p_date', true)),
            'start_s_date' => htmlEscape(Input::get('start_s_date', true)),
            'end_s_date' => htmlEscape(Input::get('end_s_date', true)),
        ];

        $formBuilder = new FormBuilder($formFilterFields);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
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
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

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
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Validazione dei campi di input per la ricerca
        $validator = new Validator();
        if (Input::get('object') != null ||
            Input::get('str') != null ||
            Input::get('start_p_date') != null ||
            Input::get('end_p_date') != null ||
            Input::get('start_s_date') != null ||
            Input::get('end_s_date') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Oggetto')->value(Input::get('object'))->maxLength(150)->end();
        $validator->label('Struttura di riferimento')->value(Input::get('str'))->isInt()->end();
        $validator->label('Data di pubblicazione dal')->value(Input::get('start_p_date'))->isDate('Y-m-d')->end();
        $validator->label('Data di pubblicazione al')->value(Input::get('end_p_date'))->isDate('Y-m-d')->end();
        $validator->label('Data di scadenza dal')->value(Input::get('start_s_date'))->isDate('Y-m-d')->end();
        $validator->label('Data di scadenza al')->value(Input::get('end_s_date'))->isDate('Y-m-d')->end();

        if (!$validator->isSuccess()) {
            echo showError('Errore nella validazione dei dati', $validator->getErrorsHtml(), $validator->getErrorStatusCode());
            die();
        }

        // Recupero gli avvisi da mostrare
        $contests = ContestModel::where('typology', 'avviso')
            ->where('activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
            ->filter(
                (Input::get('object', true)),
                (int)Input::get('str', true),
                Input::get('start_p_date', true),
                Input::get('end_p_date', true),
                Input::get('start_s_date', true),
                Input::get('end_s_date', true),
            );

        $filters = ['object', 'start_p_date', 'end_p_date', 'start_s_date', 'end_s_date', 'sec_token'];

        $contests = $contests->orderBy('activation_date', 'DESC')
            ->paginate(20, ['id', 'object', 'activation_date', 'expiration_date'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get($filters))
            ->setPath(currentUrl());

        $contests = !empty($contests) ? $contests->toArray() : [];

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($contests, 'avviso');

        $data['table'] = !empty($table) ? $table->generate() : null;
        $data['instances'] = $contests;

        // Richiesta del solo frammento (paginazione AJAX): tabella e paginazione sono pronte,
        // tutto il resto (pagina corrente, menu, filtri, contenuti) serve solo alla pagina
        // completa e viene saltato.
        if (renderFrontFragment($data)) {

            return;

        }

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

        //Strutture per il filtro di ricerca
        $offices = [null => 'Seleziona una struttura'] + StructuresModel::where('articulation', 1)
                ->where(function ($query) {
                })
                ->orderBy('structure_name', 'ASC')
                ->pluck('structure_name', 'id')
                ->toArray();

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

        // Form per i filtri di ricerca
        $filter = [
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
                'prefix' => '<div class="form-group col-lg-6" style="margin-bottom: 1rem;">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'object',
                    'id' => 'object',

                    'value' => '{object}'
                ],
                'label' => [
                    'for' => 'object',
                    'text' => 'Oggetto',
                ],
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
                    'text' => 'Struttura di riferimento',
                ],
            ],
            [
                'prefix' => '<div class="col-lg-6 mt-2">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'start_p_date',
                    'value' => '{start_p_date}',
                    'type' => 'date',
                    'id' => 'start_p_date',
                    'class' => 'active',

                ],
                'label' => [
                    'for' => 'start_p_date',
                    'text' => 'Data di pubblicazione - dal',
                ]
            ],
            [
                'prefix' => '<div class="col-lg-6 mt-2">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'end_p_date',
                    'value' => '{end_p_date}',
                    'type' => 'date',
                    'id' => 'end_p_date',
                    'class' => 'active',

                ],
                'label' => [
                    'for' => 'end_p_date',
                    'text' => 'Data di pubblicazione - al',
                ]
            ],
            [
                'prefix' => '<div class="col-lg-6 mt-2">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'start_s_date',
                    'value' => '{start_s_date}',
                    'type' => 'date',
                    'id' => 'start_s_date',
                    'class' => 'active',

                ],
                'label' => [
                    'for' => 'start_s_date',
                    'text' => 'Data di scadenza - dal',
                ]
            ],
            [
                'prefix' => '<div class="col-lg-6 mt-2">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'end_s_date',
                    'value' => '{end_s_date}',
                    'type' => 'date',
                    'id' => 'end_s_date',
                    'class' => 'active',

                ],
                'label' => [
                    'for' => 'end_s_date',
                    'text' => 'Data di scadenza - al',
                ]
            ],
            "extra_filter" => [],
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
        ];

        $form = json_encode($filter);

        $formFilterFields = [
            'object' => Input::get('object', true),
            'structure' => Input::get('str', true),
            'start_p_date' => Input::get('start_p_date', true),
            'end_p_date' => Input::get('end_p_date', true),
            'start_s_date' => Input::get('start_s_date', true),
            'end_s_date' => Input::get('end_s_date', true),
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value) {
            if (!empty($value)) {
                $countFilterFields += 1;
            }
        }

        $formFilterFields = [
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'object' => htmlEscape(Input::get('object', true)),
            'structure' => FormBuilder::parseOptions($offices, htmlEscape(Input::get('str', true))),
            'start_p_date' => htmlEscape(Input::get('start_p_date', true)),
            'end_p_date' => htmlEscape(Input::get('end_p_date', true)),
            'start_s_date' => htmlEscape(Input::get('start_s_date', true)),
            'end_s_date' => htmlEscape(Input::get('end_s_date', true)),
        ];

        $formBuilder = new FormBuilder($formFilterFields);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
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
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

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
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Validazione dei campi di input per la ricerca
        $validator = new Validator();
        if (Input::get('object') != null ||
            Input::get('str') != null ||
            Input::get('start_p_date') != null ||
            Input::get('end_p_date') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Oggetto')->value(Input::get('object'))->maxLength(150)->end();
        $validator->label('Struttura di riferimento')->value(Input::get('str'))->isInt()->end();
        $validator->label('Data di pubblicazione dal')->value(Input::get('start_p_date'))->isDate('Y-m-d')->end();
        $validator->label('Data di pubblicazione al')->value(Input::get('end_p_date'))->isDate('Y-m-d')->end();

        if (!$validator->isSuccess()) {
            echo showError('Errore nella validazione dei dati', $validator->getErrorsHtml(), $validator->getErrorStatusCode());
            die();
        }

        // Recupero gli esiti da mostrare
        $contests = ContestModel::where('typology', 'esito')
            ->where('activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
            ->filter(
                (Input::get('object', true)),
                (int)Input::get('str', true),
                Input::get('start_p_date', true),
                Input::get('end_p_date', true)
            );

        $filters = ['id', 'object', 'sec_token', 'start_p_date', 'end_p_date'];

        $contests = $contests->orderBy('activation_date', 'DESC')
            ->paginate(20, ['id', 'object', 'activation_date'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get($filters))
            ->setPath(currentUrl());

        $contests = !empty($contests) ? $contests->toArray() : [];

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($contests, 'esito');

        $data['table'] = !empty($table) ? $table->generate() : null;
        $data['instances'] = $contests;

        // Richiesta del solo frammento (paginazione AJAX): tabella e paginazione sono pronte,
        // tutto il resto (pagina corrente, menu, filtri, contenuti) serve solo alla pagina
        // completa e viene saltato.
        if (renderFrontFragment($data)) {

            return;

        }

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

        //Strutture per il filtro di ricerca
        $offices = [null => 'Seleziona una struttura'] + StructuresModel::where('articulation', 1)
                ->where(function ($query) {
                })
                ->orderBy('structure_name', 'ASC')
                ->pluck('structure_name', 'id')
                ->toArray();

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

        // Form per i filtri di ricerca
        $filter = [
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
                'prefix' => '<div class="form-group col-lg-6" style="margin-bottom: 1rem;">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'object',
                    'id' => 'object',

                    'value' => '{object}'
                ],
                'label' => [
                    'for' => 'object',
                    'text' => 'Oggetto',
                ],
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
                    'text' => 'Struttura di riferimento',
                ],
            ],
            [
                'prefix' => '<div class="col-lg-6 mt-2">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'start_p_date',
                    'value' => '{start_p_date}',
                    'type' => 'date',
                    'id' => 'start_p_date',
                    'class' => 'active',

                ],
                'label' => [
                    'for' => 'start_p_date',
                    'text' => 'Data di pubblicazione - dal',
                ]
            ],
            [
                'prefix' => '<div class="col-lg-6 mt-2">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'end_p_date',
                    'value' => '{end_p_date}',
                    'type' => 'date',
                    'id' => 'end_p_date',
                    'class' => 'active',

                ],
                'label' => [
                    'for' => 'end_p_date',
                    'text' => 'Data di pubblicazione - al',
                ]
            ],
            "extra_filter" => [],
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
        ];

        $form = json_encode($filter);

        $formFilterFields = [
            'object' => Input::get('object', true),
            'structure' => Input::get('str', true),
            'start_p_date' => Input::get('start_p_date', true),
            'end_p_date' => Input::get('end_p_date', true),
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value) {
            if (!empty($value)) {
                $countFilterFields += 1;
            }
        }
        $formFilterFields = [
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'object' => htmlEscape(Input::get('object', true)),
            'structure' => FormBuilder::parseOptions($offices, htmlEscape(Input::get('str', true))),
            'start_p_date' => htmlEscape(Input::get('start_p_date', true)),
            'end_p_date' => htmlEscape(Input::get('end_p_date', true)),
            'start_s_date' => htmlEscape(Input::get('start_s_date', true)),
            'end_s_date' => htmlEscape(Input::get('end_s_date', true)),
        ];

        $formBuilder = new FormBuilder($formFilterFields);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
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
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

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
        $currentPageId = (int)uri()->segment(2, 0);
        $elementId = (int)uri()->segment(4, 0);

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
        $data['pageName'] = $element['object'];
        $data['menuPages'] = $sectionFO;
        $data['currentPageId'] = $currentPageId;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['editRecord'] = true;

        // aggiungi al Breadcrumbs la pagina del dettaglio
        $data['concatBreadcrumb'] = true;
        $data['bread'] = array();
        $data['bread'][] = array('name' => html_entity_decode($element['object']), 'link' => '/');

        $data['instance'] = $element;

        $label = 'contest';
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

        renderFront(config('vfo', null, 'app') . '/contests/details', $data, 'frontend');
    }
}
