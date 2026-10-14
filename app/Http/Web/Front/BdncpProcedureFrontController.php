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
use Model\BdncpProcedureModel;
use Model\ContestsActsModel;
use Model\SectionsFoModel;
use System\Input;
use System\Log;
use System\Validator;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller pagina front-end PROCEDURE DELIBERA 261/2023
 */
class BdncpProcedureFrontController extends BaseFrontController
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
     * Metodo chiamato per la pagina index dei Bandi di gara e contratti
     * ID sezione 10
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

        // Validazione dei campi d'input per la ricerca
        $validator = new Validator();
        if (Input::get('o') != null ||
            Input::get('c') != null ||
            Input::get('start') != null ||
            Input::get('end') != null ||
            Input::get('fase') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Oggetto')->value(Input::get('o'))->maxLength(255)->end();
        $validator->label('Cig')->value(Input::get('c'))->maxLength(15)->end();
        $validator->label('Data dal')->value(Input::get('start'))->isDate('Y-m-d')->end();
        $validator->label('Data al')->value(Input::get('end'))->isDate('Y-m-d')->end();

        if (!$validator->isSuccess()) {
            echo showError('Errore nella validazione dei dati', $validator->getErrorsHtml(), $validator->getErrorStatusCode());
            die();
        }

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'section_fo.id', 'parent_id', 'form_filter', 'controller_open_data', 'archive_name',
            'rel_institution_type_sections_labeling.label', 'section_fo.created_at', 'section_fo.updated_at', 'is_system', 'controller_open_data'])
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

        $filters = ['o', 'c', 'start', 'end', 'sec_token', 'str', 'fase'];

        // Recupero i procedimenti(con campo Pubblica automaticamente i dati sul monitoraggio = 1) da mostrare
        $procedureFields = [
            'object_bdncp_procedure.id',
            'object_bdncp_procedure.object',
            'object_bdncp_procedure.cig',
            'object_bdncp_procedure.bdncp_link',
            'object_bdncp_procedure.publishing_start',
            'object_bdncp_procedure.updated_at',
        ];

        $procedure = BdncpProcedureModel::filter(
            (Input::get('o', true)),
            Input::get('c', true),
            Input::get('start', true),
            Input::get('end', true),
            Input::get('fase', true)
        )->where('typology', 'procedure');

        $procedure = $procedure->orderBy('publishing_start', 'DESC')
            ->orderBy('object', 'ASC')
            ->groupBy('object_bdncp_procedure.id')
            ->paginate(20, $procedureFields, 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get($filters))
            ->setPath(currentUrl());

        $procedure = !empty($procedure) ? $procedure->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($procedure['data'])) {

            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(BdncpProcedureModel::selectRaw('COALESCE(publishing_start, created_at) as created_at')
                ->where('typology', 'procedure')
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(BdncpProcedureModel::select(['updated_at'])
                ->where('typology', 'procedure')
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($procedure, 'procedure');

        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $procedure;

        // Richiesta del solo frammento (paginazione AJAX): tabella e paginazione sono pronte,
        // tutto il resto serve solo alla pagina completa e viene saltato.
        if (renderFrontFragment($data)) {

            return;

        }

        // Form per i filtri di ricerca
        $filter = [
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
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'o',
                    'value' => '{object}',
                    'id' => 'object',
                    'class' => 'active',

                ],
                'label' => [
                    'for' => 'object',
                    'text' => 'Oggetto',
                ]
            ],
            [
                'prefix' => '<div class="form-group col-lg-2">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'c',
                    'value' => '{cig}',
                    'id' => 'cig',
                    'class' => 'active',

                ],
                'label' => [
                    'for' => 'cig',
                    'text' => 'CIG',
                ]
            ],
            [
                'prefix' => '<div class="form-group col-lg-3">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'start',
                    'value' => '{start}',
                    'type' => 'date',
                    'id' => 'start',

                ],
                'label' => [
                    'for' => 'start',
                    'text' => 'Data dal',
                    'class' => 'active'
                ]
            ],
            [
                'prefix' => '<div class="form-group col-lg-3">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'end',
                    'value' => '{end}',
                    'type' => 'date',
                    'id' => 'end',

                ],
                'label' => [
                    'for' => 'end',
                    'text' => 'Data al',
                    'class' => 'active'
                ]
            ],
            [
                'type' => 'tag',
                'content' => '</div>',
            ],
            [
                'type' => 'tag',
                'content' => '<div class="row mb-4">',
            ],
            [
                'prefix' => '<div class="form-group select-wrapper col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{fase}',
                'name' => 'fase',
                'extra' => 'id="fase"',
                'label' => [
                    'for' => 'fase',
                    'text' => 'Fase',
                    'class' => 'active'
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

        $form = json_encode($filter);
        $formFilterFields = [
            'o' => Input::get('o', true),
            'c' => Input::get('c', true),
            'start' => Input::get('start', true),
            'end' => Input::get('end', true),
            'fase' => Input::get('fase', true)
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value) {
            if (!empty($value)) {
                $countFilterFields += 1;
            }
        }

        $faseConfig = config('config', null, 'bdncp_procedure_config');
        uasort($faseConfig, function($a, $b){
            return ($a['fo_filter_order'] < $b['fo_filter_order']) ? -1 : 1;
        });
        $fase = [null => 'Seleziona'];
        foreach ($faseConfig as $f){
            $fase[$f['field']."_check"] = $f['fase']." - ".$f['title'];
        }


        $formBuilderFields = [
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'object' => htmlEscape(Input::get('o', true)),
            'cig' => htmlEscape(Input::get('c', true)),
            'start' => htmlEscape(Input::get('start', true)),
            'end' => htmlEscape(Input::get('end', true)),
            'fase' => FormBuilder::parseOptions($fase, htmlEscape(Input::get('fase', true)))
        ];

        $formBuilder = new FormBuilder($formBuilderFields);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['items'] = $procedure;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = !empty($currentPage['no_required']);
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

        renderFront(config('vfo', null, 'app') . '/bdncp_procedure/index', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina Avvisi
     * ID sezione 588
     * @return void
     * @throws Exception
     */
    public function alerts(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Validazione dei campi d'input per la ricerca
        $validator = new Validator();
        if (Input::get('o') != null ||
            Input::get('start') != null ||
            Input::get('end') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Oggetto')->value(Input::get('o'))->maxLength(255)->end();
        $validator->label('Data dal')->value(Input::get('start'))->isDate('Y-m-d')->end();
        $validator->label('Data al')->value(Input::get('end'))->isDate('Y-m-d')->end();

        if (!$validator->isSuccess()) {
            echo showError('Errore nella validazione dei dati', $validator->getErrorsHtml(), $validator->getErrorStatusCode());
            die();
        }

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'section_fo.id', 'parent_id', 'form_filter', 'controller_open_data', 'archive_name',
            'rel_institution_type_sections_labeling.label', 'section_fo.created_at', 'section_fo.updated_at', 'is_system', 'controller_open_data'])
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

        $filters = ['o', 'c', 'sec_token', 'str'];

        // Recupero i procedimenti(con campo Pubblica automaticamente i dati sul monitoraggio = 1) da mostrare
        $alerts = BdncpProcedureModel::where('typology', 'alert')
            ->alertFilter(
            (Input::get('o', true)),
            Input::get('start', true),
            Input::get('end', true)
        );

        $alerts= $alerts->orderBy('updated_at', 'DESC')
            ->orderBy('object', 'ASC')
            ->groupBy('object_bdncp_procedure.id')
            ->paginate(20, ['object_bdncp_procedure.id', 'object_bdncp_procedure.object', 'object_bdncp_procedure.alert_date'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get($filters))
            ->setPath(currentUrl());

        $alerts = !empty($alerts) ? $alerts->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($alerts['data'])) {

            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(BdncpProcedureModel::select(['created_at'])
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(BdncpProcedureModel::select(['updated_at'])
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($alerts, 'alert');

        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $alerts;

        // Richiesta del solo frammento (paginazione AJAX): tabella e paginazione sono pronte,
        // tutto il resto serve solo alla pagina completa e viene saltato.
        if (renderFrontFragment($data)) {

            return;

        }

        $filter = [
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
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'o',
                    'value' => '{object}',
                    'id' => 'object',
                    'class' => 'active',

                ],
                'label' => [
                    'for' => 'object',
                    'text' => 'Oggetto',
                ]
            ],

            [
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'start',
                    'value' => '{start}',
                    'type' => 'date',
                    'id' => 'start',

                ],
                'label' => [
                    'for' => 'start',
                    'text' => 'Data dal',
                    'class' => 'active'
                ]
            ],
            [
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'end',
                    'value' => '{end}',
                    'type' => 'date',
                    'id' => 'end',

                ],
                'label' => [
                    'for' => 'end',
                    'text' => 'Data al',
                    'class' => 'active'
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

        $formFilterFields = [
            'o' => Input::get('o', true),
            'start' => Input::get('start', true),
            'end' => Input::get('end', true)
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value) {
            if (!empty($value)) {
                $countFilterFields += 1;
            }
        }

        $formBuilderFields = [
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'object' => htmlEscape(Input::get('o', true)),
            'start' => htmlEscape(Input::get('start', true)),
            'end' => htmlEscape(Input::get('end', true)),
        ];

        $formBuilder = new FormBuilder($formBuilderFields);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['items'] = $alerts;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = !empty($currentPage['no_required']);
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

        renderFront(config('vfo', null, 'app') . '/bdncp_procedure/index', $data, 'frontend');
    }


    /**
     * Metodo chiamato per la pagina Varaizioni in corso d'opera
     * ID sezione 603
     * @return void
     * @throws Exception
     */
    public function variations(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Validazione dei campi d'input per la ricerca
        $validator = new Validator();
        if (Input::get('o') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Oggetto')->value(Input::get('o'))->maxLength(255)->end();

        if (!$validator->isSuccess()) {
            echo showError('Errore nella validazione dei dati', $validator->getErrorsHtml(), $validator->getErrorStatusCode());
            die();
        }

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'section_fo.id', 'parent_id', 'form_filter', 'controller_open_data', 'archive_name',
            'rel_institution_type_sections_labeling.label', 'section_fo.created_at', 'section_fo.updated_at', 'is_system', 'controller_open_data'])
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


        //VARIAZIONI BDNCP
        if(true){

            // Recupero i procedimenti(con campo Pubblica automaticamente i dati sul monitoraggio = 1) da mostrare
            $variations = BdncpProcedureModel::where('typology', 'variation')
                ->variationFilter(
                    (Input::get('o', true))
                );

            $variations= $variations->orderBy('updated_at', 'DESC')
                ->orderBy('object', 'ASC')
                ->paginate(20, ['object_bdncp_procedure.id', 'object'], 'p', boundedQueryInt('p', 10000))
                ->onEachSide(2)
                ->appends(Input::get(['o', 'sec_token', 'str']))
                ->setPath(currentUrl());

            $variations = !empty($variations) ? $variations->toArray() : [];

            //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
            if (!empty($variations['data'])) {

                //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
                //da pubblicare nella pagina
                $firstCreated = optional(BdncpProcedureModel::select(['created_at'])
                    ->orderBy('created_at', 'ASC')
                    ->first())
                    ->toArray();
                $lasUpdated = optional(BdncpProcedureModel::select(['updated_at'])
                    ->orderBy('updated_at', 'DESC')
                    ->first())
                    ->toArray();

                $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
            }

            // Recupero il contenuto della pagina e i relativi richiami
            $contents = getPageContents($currentPageId);

            // Creo le righe della tabella settando i dati
            $table = $this->createTableRows($variations, 'variation');

            $filter = [
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
                    'prefix' => '<div class="form-group col-lg-4">',
                    'suffix' => '</div>',
                    'type' => 'form_input',
                    'attributes' => [
                        'name' => 'o',
                        'value' => '{object}',
                        'id' => 'object',
                        'class' => 'active',

                    ],
                    'label' => [
                        'for' => 'object',
                        'text' => 'Oggetto',
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

            // Form per i filtri di ricerca
            $form = json_encode($filter);

            $formFilterFields = [
                'o' => Input::get('o', true)
            ];

            $countFilterFields = 0;
            foreach ($formFilterFields as $value) {
                if (!empty($value)) {
                    $countFilterFields += 1;
                }
            }

            $formBuilderFields = [
                'current_url' => currentUrl(),
                'redirect_url' => currentUrl(),
                'object' => htmlEscape(Input::get('o', true))
            ];

            $formBuilder = new FormBuilder($formBuilderFields);

            $formBuilder->loadJsonFromVar($form);
            $data['formFilter'] = $formBuilder->render();

        }

        /* ---------------------------------------------------------------------------------------------- */

        //VARIAZIONI CONTESTS-ACT
        if(true){

            // Recupero i procedimenti(con campo Pubblica automaticamente i dati sul monitoraggio = 1) da mostrare
            $variationsPre2024 = ContestsActsModel::where('typology', 'variation')
                ->variationFilter(
                    (Input::get('o', true))
                )->orderBy('updated_at', 'DESC')
                ->orderBy('object', 'ASC')
                ->paginate(20, ['object_contests_acts.id', 'object'], 'p', boundedQueryInt('p', 10000))
                ->onEachSide(2)
                ->appends(Input::get(['o', 'sec_token']))
                ->setPath(currentUrl());

            $variationsPre2024 = !empty($variationsPre2024) ? $variationsPre2024->toArray() : [];

            // Creo le righe della tabella settando i dati
            $tablePre2024 = $this->createTableRows($variationsPre2024, 'variation', true);

            $data['tablePre2024'] = !empty($tablePre2024) ? $tablePre2024->generate() : $tablePre2024;
            $data['allowSearchPre2024'] = true;
            $data['varationPre2024'] = $variationsPre2024;
        }


        $data['title'] = true;
        $data['titlePre2024'] = true;


        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['items'] = $variations;
        $data['instance'] = $currentPage;
        $data['instances'] = $variations;
        $data['noRequiredPublication'] = !empty($currentPage['no_required']);
        $data['allowSearch'] = true;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['searchFieldsOpenData'] = $formFilterFields;
        $data['filterUsed'] = $countFilterFields;
        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];


        renderFront(config('vfo', null, 'app') . '/bdncp_procedure/index_variation', $data, 'frontend');
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
        $element = BdncpProcedureModel::where('id', $elementId);

        $element = $element->with('commission:id,object,name')
            ->with('board:id,object,name')
            ->with('relative_bdncp_procedure')
            ->with('children_procedure:id,object_procedure_id,object,cig,bdncp_link,type')
            ->with('measures:id,object,object_bdncp_procedure_id,type')
            ->with('rel_variation_procedure:id,object,cig')
            ->with('variations:id,object');

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
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['editRecord'] = true;

        $label = 'bdncp_procedure';
        $elementId = $element['id'];
        $selectFields = [
            'id',
            'cat_id',
            'archive_name',
            'archive_id',
            'client_name',
            'orig_name',
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

        $sectionId = (int)uri()->segment(2, 0);

            // Allegati
            $attach = new AttachmentArchive();
            $selectFields [] = 'bdncp_cat';
            $data['listAttach'] = $attach->getAllByObject(
                $label,
                $elementId,
                $selectFields,
                true
            );

        $tmpAttachs = [];

        foreach ($data['listAttach'] as $attach) {
            $tmpAttachs[$attach['bdncp_cat']??''][] = $attach;
        }

        $data['attachs'] = $tmpAttachs;

        $tmpCat = [];
        $bdncpProcedureCat = config('config', null, 'bdncp_procedure_config');
        if(!empty($bdncpProcedureCat) && is_array($bdncpProcedureCat)) {
            foreach ($bdncpProcedureCat as $k => $v) {
                if($element[$v['field'].'_check']) {
                    $tmpCat [$v['step']][$k] = $v;
                }
            }
        }

        $data['procedureCat'] = $tmpCat;

        $data['currentPageId'] = $currentPageId;

        $viewToRender = '/bdncp_procedure/details';
        if(in_array($element['typology'], ['alert','variation'])){
            $viewToRender = '/bdncp_procedure/'.$element['typology'].'_details';
        }

        renderFront(config('vfo', null, 'app') . $viewToRender , $data, 'frontend');
    }

    /**
     * Metodo che crea la tabella con i dati dei risultati della ricerca da mostrare
     *
     * @param null $procedures {dati da inserire nella tabella}
     * @param string $typology {indica la tipologia (procedure o avvisi)}
     * @param bool $pre2024
     * @return Table|null
     * @throws Exception
     */
    //non mettere private
    protected function createTableRows($procedures = null, string $typology = '', bool $pre2024 = false): ?Table
    {
        $currentPageId = (int)uri()->segment(2, 0);

        if($pre2024){
            //sovrascrivo solo per i bandi vecchi
            $currentPageId = 112;
        }

        $table = null;

        if (!empty($procedures['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();

            $headers = ['Oggetto'];

            if($typology === 'procedure') {
                array_unshift($headers, 'CIG');
                $headers [] = 'Link BDNCP';
                $headers[] = 'Data di pubblicazione';
            } elseif($typology == 'alert') {
                $headers [] = 'Data avviso';
            }

            $table->set_heading($headers);

            // Creo le righe della tabella settando i dati da mostrare
            foreach ($procedures['data'] as $procedure) {

                $rows = [
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $procedure['id'] . '/' . urlTitle(escapeXss($procedure['object']))) . '" data-id="' . $procedure['id'] . ' ">' . escapeXss($procedure['object']) . '</a>'
                ];

                if($typology === 'procedure') {
                    array_unshift($rows, '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $procedure['id'] . '/' . urlTitle($procedure['object'])) . '" data-id="' . $procedure['id'] . ' ">' . escapeXss($procedure['cig']) . '</a>');
                    $rows [] = (!empty($procedure['bdncp_link']) ? '<a href="'.escapeXss($procedure['bdncp_link']).'" target="_blank">Link</a>' : 'N/A');
                    $rows [] = !empty($procedure['publishing_start']) ?  date('d/m/Y', strtotime($procedure['publishing_start'])) : '';
                } elseif($typology == 'alert') {
                    $rows [] = (!empty($procedure['alert_date']) ? date('d/m/Y', strtotime($procedure['alert_date'])) : 'N/A');
                }

                $table->add_row(
                    $rows
                );
            }
        }

        return $table;
    }
}
