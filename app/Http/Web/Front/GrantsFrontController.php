<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

use Exception;
use Helpers\FormBuilder;
use Helpers\S;
use Helpers\Table;
use Helpers\Utility\AttachmentArchive;
use Model\GrantsModel;
use Model\PersonnelModel;
use Model\RelGrantsPersonnelModel;
use Model\SectionsFoModel;
use Model\StructuresModel;
use System\Input;
use System\Validator;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller Sovvenzioni
 */
class GrantsFrontController extends BaseFrontController
{
    /**
     * @description Costruttore
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        helper('url');
    }

    /**
     * Metodo per la pagina di snodo delle sovvenzioni
     * @return void
     * @throws Exception
     */
    public function pivot(): void
    {
        $pivot = new PivotController();
        $pivot->index();
    }

    /**
     * Metodo chiamato per la pagina "Atti di concessione" nella sezione delle Sovvenzioni
     * ID sezione 126
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexConcessionActs(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Validatore filtri di ricerca
        $validator = new Validator();
        if (Input::get('obj') != null ||
            Input::get('y') != null ||
            Input::get('ben') != null ||
            Input::get('s') != null ||
            Input::get('r') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Oggetto')->value(Input::get('obj'))->maxLength(120)->end();
        $validator->label('Anno')->value(Input::get('y'))->isDate('Y')->end();
        $validator->label('Nome beneficiario')->value(Input::get('ben'))->maxLength(50)->end();
        $validator->label('Struttura')->value(Input::get('s'))->isInt()->end();
        $validator->label('Responsabile')->value(Input::get('r'))->maxLength(50)->end();

        if (!$validator->isSuccess()) {
            echo showError('Errore nella validazione dei dati', $validator->getErrorsHtml(), $validator->getErrorStatusCode());
            die();
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

        // Recupero i procedimenti(con campo Pubblica automaticamente i dati sul monitoraggio = 1) da mostrare
        $grants = $this->getDataResults('grant', $data);

        $grants = !empty($grants) ? $grants->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($grants, 'grant');

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
                'options' => '{years}',
                'name' => 'y',
                'extra' => 'id="year"',
                'label' => [
                    'for' => 'y',
                    'text' => 'Anno',
                ],
            ],
            [
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'ben',
                    'id' => 'ben',

                    'value' => '{beneficiary}'
                ],
                'label' => [
                    'for' => 'ben',
                    'text' => 'Nome beneficiario',
                ],
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-6 mt-2">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{structures}',
                'name' => 's',
                'extra' => 'id="s"',
                'label' => [
                    'for' => 's',
                    'text' => 'Struttura responsabile',
                ],
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-6 mt-2">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{responsibles}',
                'name' => 'r',
                'extra' => 'id="r"',
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

        $formFilterFields = [
            'obj' => Input::get('obj', true),
            'y' => Input::get('y', true),
            'ben' => Input::get('ben', true),
            's' => Input::get('s', true),
            'r' => Input::get('r', true),
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value) {
            if (!empty($value)) {
                $countFilterFields += 1;
            }
        }

        // Anni per la ricerca
        $resultYears = GrantsModel::select('concession_act_date')
            ->where('type', 'grant')
            ->whereNotNull('concession_act_date')
            ->orderBy('concession_act_date', 'ASC')
            ->first();

        $resultYears = !empty($resultYears) ? $resultYears->toArray() : ['concession_act_date' => date('Y')];
        $years = [null => 'Seleziona un anno'];
        for ($i = (int)date('Y', strtotime($resultYears['concession_act_date'])); $i <= (int)date('Y'); $i++) {
            $years[$i] = $i;
        }

        // Filtro per strutture
        $structureIds = GrantsModel::select(['object_structures_id'])
            ->where('object_grants.type', '=', 'grant')
            ->groupBy('object_structures_id')
            ->get()
            ->toArray();
        $structures = [null => 'Seleziona una struttura'] + StructuresModel::whereIn('id', $structureIds)
                ->where(function ($query) {
                })
                ->orderBy('structure_name', 'ASC')
                ->pluck('structure_name', 'id')
                ->toArray();

        // Responsabili per il filtro
        $responsibleIds = GrantsModel::select(['rel.object_personnel_id'])
            ->where('object_grants.type', '=', 'grant')
            ->join('rel_grants_personnel as rel', 'rel.object_grants_id', '=', 'object_grants.id')
            ->groupBy('rel.object_personnel_id')
            ->get()
            ->toArray();
        $responsibles = [null => 'Seleziona un responsabile'] + PersonnelModel::select(['id', 'full_name'])
                ->whereIn('id', $responsibleIds)
                ->orderBy('full_name', 'ASC')
                ->pluck('full_name', 'id')
                ->toArray();

        $formBuilder = new FormBuilder([
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'object' => htmlEscape(Input::get('obj', true)),
            'beneficiary' => htmlEscape(Input::get('ben', true)),
            'years' => FormBuilder::parseOptions($years, htmlEscape(Input::get('y', true))),
            'structures' => FormBuilder::parseOptions($structures, htmlEscape(Input::get('s', true))),
            'responsibles' => FormBuilder::parseOptions($responsibles, htmlEscape(Input::get('r', true)))
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();


        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $grants;
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
        //Inclusione di eventuali librerie esterne
        $currentPage = $currentPage ?? null;
        $data = $data ?? null;
        $data = $data ?? null;
        renderFront(config('vfo', null, 'app') . '/grants/grants', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Elenco soggetti beneficiari" nella sezione delle Sovvenzioni
     * ID sezione 127
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexBeneficiary(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        $validator = new Validator();
        if (Input::get('y') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('anno')->value(Input::get('y', true))->isDate('Y')->end();

        if (!$validator->isSuccess()) {
            echo showError('Errore nella validazione dei dati', $validator->getErrorsHtml(), $validator->getErrorStatusCode());
            die();
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

        // Recupero i procedimenti(con campo Pubblica automaticamente i dati sul monitoraggio = 1) da mostrare
        // Il terzo argomento e' la tipologia di TABELLA (qui diversa dal tipo di dato): serve al
        // ramo frammento di getDataResults() per generare le stesse colonne della pagina intera.
        $grants = $this->getDataResults('grant', $data, 'beneficiary');

        $grants = !empty($grants) ? $grants->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($grants, 'beneficiary');

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
                'prefix' => '<div class="select-wrapper col-lg-6">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{years}',
                'name' => 'y',
                'extra' => 'id="year"',
                'label' => [
                    'for' => 'y',
                    'text' => 'Anno',
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
            'y' => Input::get('y', true),
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value) {
            if (!empty($value)) {
                $countFilterFields += 1;
            }
        }

        // Anni per la ricerca
        $resultYears = GrantsModel::select('concession_act_date')
            ->where('type', 'grant')
            ->whereNotNull('concession_act_date')
            ->orderBy('concession_act_date', 'ASC')
            ->first();

        $years = [null => 'Seleziona un anno'];

        if (!empty($resultYears)) {
            $resultYears = $resultYears->toArray();
            for ($i = (int)date('Y'); $i >= (int)date('Y', strtotime($resultYears['concession_act_date'])); $i--) {
                $years[$i] = $i;
            }
        }

        $formBuilder = new FormBuilder([
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'years' => FormBuilder::parseOptions($years, htmlEscape(Input::get('y', true)))
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $grants;
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
        //Inclusione di eventuali librerie esterne
        $data = $data ?? null;
        $data = $data ?? null;
        renderFront(config('vfo', null, 'app') . '/grants/grants', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Pagamenti di Sovvenzioni, contributi, sussidi, vantaggi economici ",
     * nella sezione dei Pagamenti dell'amministrazione
     * Id sezione 155
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexLiquidation(): void
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
        if (Input::get('b') != null ||
            Input::get('year') != null ||
            Input::get('str') != null ||
            Input::get('resp') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Beneficiario')->value(Input::get('b'))->maxLength(30)->end();
        $validator->label('Anno')->value(Input::get('year'))->isDate('Y')->end();
        $validator->label('Ufficio')->value(Input::get('str'))->isInt()->end();
        $validator->label('Responsabile')->value(Input::get('resp'))->isInt()->end();

        if (!$validator->isSuccess()) {
            echo showError('Errore nella validazione dei dati', $validator->getErrorsHtml(), $validator->getErrorStatusCode());
            die();
        }

        // Recupero i procedimenti(con campo Pubblica automaticamente i dati sul monitoraggio = 1) da mostrare
        $grants = $this->getDataResults('liquidation', $data);

        $grants = !empty($grants) ? $grants->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Form per i filtri di ricerca
        $form = json_encode([
            [
                'type' => 'form_open',
                'name' => 'absence_rates',
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
                'prefix' => '<div class="form-group col-lg-6">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'b',
                    'id' => 'b',

                    'value' => '{beneficiary}'
                ],
                'label' => [
                    'for' => 'b',
                    'text' => 'Beneficiario',
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
                    'text' => 'Ufficio',
                ],
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-6 mt-2">',
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
                'prefix' => '<div class="select-wrapper col-lg-6 mt-2">',
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
            'b' => Input::get('object', true),
            'year' => Input::get('start_p_date', true),
            'str' => Input::get('end_p_date', true),
            'resp' => Input::get('start_s_date', true)
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value) {
            if (!empty($value)) {
                $countFilterFields += 1;
            }
        }

        // Filtro per strutture
        $structures = [null => 'Seleziona una struttura'] + StructuresModel::where(function ($query) {
            })
                ->orderBy('structure_name', 'ASC')
                ->pluck('structure_name', 'id')
                ->toArray();

        // Responsabili per il filtro
        $responsibleIds = RelGrantsPersonnelModel::select('object_personnel_id')
            ->groupBy('object_personnel_id')
            ->get()
            ->toArray();
        $responsibles = [null => 'Seleziona un responsabile'] + PersonnelModel::select(['id', 'full_name'])
                ->whereIn('id', $responsibleIds)
                ->orderBy('full_name', 'ASC')
                ->pluck('full_name', 'id')
                ->toArray();

        // Anni per la ricerca
        $resultYears = optional(GrantsModel::select('compensation_paid_date as liquidation_year')
            ->where('type', '=', 'liquidation')
            ->whereNotNull('compensation_paid_date')
            ->orderBy('liquidation_year', 'ASC')
            ->first())
            ->toArray();
        $resultYears = !empty($resultYears) ? ([null => 'Seleziona un anno'] + $resultYears) : ['liquidation_year' => (int)date('Y')];
        $years = [null => 'Seleziona un anno'];
        for ($i = (int)$resultYears['liquidation_year']; $i <= (int)date('Y'); $i++) {
            $years[$i] = $i;
        }

        $formBuilder = new FormBuilder([
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'beneficiary' => htmlEscape(Input::get('b', true)),
            'structure' => FormBuilder::parseOptions($structures, htmlEscape(Input::get('str', true))),
            'responsible' => FormBuilder::parseOptions($responsibles, htmlEscape(Input::get('resp', true))),
            'year' => FormBuilder::parseOptions($years, htmlEscape(Input::get('year', true))),
            'object' => htmlEscape(Input::get('object', true)),
        ]);

        //        if (!empty($currentPage['form_filter'])) {
        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();
        //        }

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($grants, 'liquidation');

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $grants;
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
        //Inclusione di eventuali librerie esterne
        $currentPage = $currentPage ?? null;
        $data = $data ?? null;
        $data = $data ?? null;
        renderFront(config('vfo', null, 'app') . '/grants/grants', $data, 'frontend');
    }


    /**
     * Funzione che ritorna i dati da mostrare nella tabella
     *
     * @param string|null $type Tipologia (Sovvenzione o liquidazione)
     * @param array $data Dati da passare alla vista
     * @return mixed
     * @throws Exception
     */
    private function getDataResults(string $type = null, array &$data = [], ?string $tableType = null): mixed
    {

        // Recupero le sovvenzioni da mostrare
        $grants = GrantsModel::where('object_grants.type', $type)
            ->with('relative_grant:id,object,beneficiary_name,concession_act_date,privacy');

        $liquidationDate = "2025-07-01 00:00:00";


        if ($type == 'liquidation') {
            $grants = $grants->where('object_grants.reference_date', '<=', $liquidationDate);
        }

        $grants = $grants->join('object_structures as structure', 'structure.id', '=', 'object_grants.object_structures_id', 'left outer')
            ->liquidationfilter(
                (Input::get('b', true)),
                (int)Input::get('str', true),
                (int)Input::get('resp', true),
                (int)Input::get('year', true)
            )
            ->grantfilter(
                (Input::get('obj', true)),
                (int)Input::get('y', true),
                (Input::get('ben', true)),
                (int)Input::get('s', true),
                (int)Input::get('r', true),
            )
            ->beneficiaryfilter(
                (int)Input::get('y', true)
            )
            ->orderBy('object_grants.concession_act_date', 'DESC')
            ->orderBy('object_grants.reference_date', 'DESC')
            ->orderBy('object_grants.created_at', 'DESC')
            ->paginate(20, ['object_grants.id', 'object_grants.object', 'object_grants.beneficiary_name', 'object_grants.concession_act_date', 'object_grants.privacy',
                'object_grants.compensation_paid_date', 'object_grants.compensation_paid', 'structure.structure_name', 'object_grants.object_structures_id',
                'object_grants.grant_id'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get(['b', 'str', 'resp', 'year', 'obj', 'y', 'ben', 's', 'r', 'sec_token']))
            ->setPath(currentUrl());

        // Richiesta del solo frammento (paginazione AJAX): essendo getDataResults() chiamato da
        // tutte le pagine di questo controller prima del lavoro pesante (form filtro, render
        // completo), un solo controllo qui copre tutte quelle pagine.
        if (isFrontFragmentRequest()) {
            $resultsArray = $grants->toArray();
            $table = $this->createTableRows($resultsArray, $tableType ?? $type);
            renderFrontFragment(['table' => !empty($table) ? $table->generate() : null, 'instances' => $resultsArray]);
            exit;
        }

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($grants) && !empty($grants->toArray()['data'])) {

            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = GrantsModel::select(['created_at'])
                ->where('type', $type);
            if ($type == 'liquidation') {
                $firstCreated = $firstCreated->where('object_grants.reference_date', '<=', $liquidationDate);
            }
            $firstCreated = optional($firstCreated->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();

            $lasUpdated = GrantsModel::select(['updated_at'])
                ->where('type', $type);
            if ($type == 'liquidation') {
                $lasUpdated = $lasUpdated->where('object_grants.reference_date', '<=', $liquidationDate);
            }
            $lasUpdated = optional($lasUpdated->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        return $grants;
    }


    /**
     * Metodo che crea la tabella con i dati dei risultati della ricerca da mostrare
     *
     * @param null $grants {dati da inserire nella tabella}
     * @param null $type Indica la tipologia (Sovvenzione/Liquidazione)
     * @return Table|null
     * @throws Exception
     */
    private function createTableRows($grants = null, $type = null): ?Table
    {
        $currentPageId = (int)uri()->segment(2, 0);
        $table = null;

        if (!empty($grants['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();

            // Setto le colonne in base al tipo(Liquidazione o Sovvenzione)
            if ($type == 'grant' || $type == 'liquidation') {

                $colName = ($type == 'grant') ? 'Struttura organizzativa' : 'Importo corrisposto';

                $table->set_heading('Oggetto', 'Beneficiario', $colName, 'Anno');

                // Creo le riche della tabella settando i dati da mostrare
                foreach ($grants['data'] as $grant) {

                    // Setto i valori delle colonne in base alla tipologia del dato
                    $colVal = ($type == 'grant')
                        ? '<a href="' . siteUrl('page/40/details/' . $grant['object_structures_id'] . '/' . urlTitle($grant['structure_name'])) . '" data-id="' . $grant['object_structures_id'] . ' ">' . escapeXss($grant['structure_name']) . '</a>'
                        : '&euro; ' . escapeXss(S::currency($grant['compensation_paid'], 2, ',', '.'));

                    // Se è una liquidazione prendo la data della sovvenzione associata,
                    // altrimenti prendo la data della sovvenzione stessa
                    $date = null;
                    if (!empty($grant['compensation_paid_date'])) {

                        $date = $grant['compensation_paid_date'];
                    } else {

                        if (!empty($grant['concession_act_date'])) {

                            $date = $grant['concession_act_date'];
                        }
                    }

                    // Se è una liquidazione prendo l'oggetto della sovvenzione associata,
                    // altrimenti prendo l'oggetto della sovvenzione stessa
                    $object = escapeXss(!empty($grant['relative_grant']['object'])
                        ? $grant['relative_grant']['object']
                        : $grant['object']);

                    $elementId = $grant['id'];

                    $finalPageId = !empty($grant['relative_grant'])
                        ? 155
                        : $currentPageId;

                    if (!empty($grant['relative_grant']['privacy']) || !empty($grant['privacy'])) {
                        $beneficiaryName = 'Omissis';
                    } else {
                        $beneficiaryName = !empty($grant['relative_grant']['beneficiary_name']) ? $grant['relative_grant']['beneficiary_name'] : $grant['beneficiary_name'];
                    }

                    $table->add_row(
                        '<a href="' . siteUrl('page/' . $finalPageId . '/details/' . $elementId . '/' . urlTitle($object)) . '" data-id="' . $elementId . ' ">' . $object . '</a>',
                        $beneficiaryName,
                        $colVal,
                        !empty($date) ? (!is_int($date) ? date('Y', strtotime($date)) : $date) : null
                    );
                }
            } else {
                // Colonne se è sono solo i beneficiari
                $table->set_heading('Nominativo', 'Data');

                // Creo le riche della tabella settando i dati da mostrare
                foreach ($grants['data'] as $grant) {

                    //Controllo il campo Omissis per vedere se devo mostrare il nome del soggetto beneficiario oppure no
                    if (!empty($grant['privacy'])) {
                        $beneficiaryName = 'Omissis';
                    } else {
                        $beneficiaryName = '<a href="' . siteUrl('page/126/details/' . $grant['id'] . '/' . urlTitle($grant['beneficiary_name'])) . '" data-id="' . $grant['id'] . ' ">' . escapeXss($grant['beneficiary_name']) . '</a>';
                    }

                    $table->add_row(
                        $beneficiaryName,
                        !empty($grant['concession_act_date']) ? date('d/m/Y', strtotime($grant['concession_act_date'])) : null
                    );
                }
            }
        }

        return $table;
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
        $element = GrantsModel::where('object_grants.id', $elementId)
            ->with(['relative_grant' => function ($query) {
                $query->withoutGlobalScopes();
                $query->select(['object_grants.id', 'object', 'beneficiary_name', 'concession_act_date', 'fiscal_data', 'privacy',
                    'object_structures_id', 'notes', 'fiscal_data', 'fiscal_data_not_available'])
                    ->with(['structure' => function ($query) {
                        $query->select(['id', 'structure_name']);
                    }]);
            }])
            ->with('normatives:id,name')
            ->with(['regulation' => function ($query) {
                $query->select(['id', 'title']);
            }])
            ->with(['structure' => function ($query) {
                $query->select(['id', 'structure_name']);
            }])
            ->with('personnel:id,full_name')
            ->with(['relative_liquidation' => function ($query) {
                $query->select(['grant_id', 'id', 'compensation_paid', 'compensation_paid_date', 'reference_date']);
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

        // Se è una liquidazione prendo l'oggetto della sovvenzione associata,
        // altrimenti prendo l'oggetto della sovvenzione stessa
        $object = !empty($element['relative_grant']['object'])
            ? $element['relative_grant']['object']
            : $element['object'];

        $privacy = !empty($element['relative_grant']['privacy'])
            ? $element['relative_grant']['privacy']
            : $element['privacy'];

        $beneficiaryName = !empty($element['relative_grant']['beneficiary_name'])
            ? $element['relative_grant']['beneficiary_name']
            : $element['beneficiary_name'];

        $fiscalDataAvaiable = !empty($element['relative_grant'])
            ? $element['relative_grant']['fiscal_data_not_available']
            : $element['fiscal_data_not_available'];

        $fiscalData = !empty($element['relative_grant']['fiscal_data'])
            ? $element['relative_grant']['fiscal_data']
            : $element['fiscal_data'];

        $structure = !empty($element['relative_grant']['structure'])
            ? $element['relative_grant']['structure']
            : $element['structure'];

        $notes = !empty($element['relative_grant']['structure'])
            ? $element['relative_grant']['notes']
            : $element['notes'];

        // Recupero le voci per costruire il menu dei contenuti
        $sectionFO = getRightOrBottomMenu($currentPageId, $currentPage['parent_id']);

        $data['date_only_year'] = false;
        $data = $data ?? null;

        // Dati passati alla vista
        $data['pageName'] = $object;
        $data['menuPages'] = $sectionFO;

        // aggiungi al Breadcrumbs la pagina del dettaglio
        $data['concatBreadcrumb'] = true;
        $data['bread'] = array();
        $data['bread'][] = array('name' => $object, 'link' => '/');
        $data['instance'] = $element;
        $data['privacy'] = $privacy;
        $data['fiscalData'] = $fiscalData;
        $data['fiscalDataAvaiable'] = $fiscalDataAvaiable;
        $data['beneficiaryName'] = $beneficiaryName;
        $data['structure'] = $structure;
        $data['notes'] = $notes;
        $data['currentPageId'] = $currentPageId;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['editRecord'] = true;
        $data['hasDifferentType'] = ($element['typology'] == 'liquidation') ? 'liquidation' : 'grant';

        $label = 'grants';
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

        $data['listAttach'] = $data['listAttach'] ?? null;

        $data['labeling'] = [
            'concession_amount' => "Importo atto di concessione"
        ];
        $data = $data ?? null;

        //Inclusione di eventuali librerie esterne
        $data = $data ?? null;

        //Inclusione del nodo JSON-LD della pagina
        $data = $data ?? null;

        renderFront(config('vfo', null, 'app') . '/grants/details', $data, 'frontend');
    }
}
