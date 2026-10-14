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
use Model\ProgrammingActsModel;
use Model\SectionsFoModel;
use System\Input;
use System\Validator;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller Atti di programmazione
 */
class ProgrammingActFrontController extends BaseFrontController
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
     * Metodo chiamato per la pagina "Composizioni delle commissioni giudicatrici e i curricula dei suoi componenti",
     * nella sezione dei Bandi di gara
     * -> Atti delle amministrazioni aggiudicatrici e degli enti aggiudicatari distintamente per ogni procedura
     * ID sezione 113
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexActsRelatingToProgramming(): void
    {
        $data = [];

        // Validazione dei campi di input per la ricerca
        $validator = new Validator();
        if (Input::get('obj') != null ||
            Input::get('d') != null ||
            Input::get('s') != null ||
            Input::get('e') != null
        ) {
            $validator->verifyToken();
        }

        $validator->label('Oggetto')->value(Input::get('obj'))->maxLength(30)->end();
        $validator->label('Descrizione')->value(Input::get('d'))->maxLength(120)->end();
        $validator->label('Data - dal')->value(Input::get('s'))->isDate('Y-m-d')->end();
        $validator->label('Data - al')->value(Input::get('e'))->isDate('Y-m-d')->end();

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

        // Recupero i procedimenti(con campo Pubblica automaticamente i dati sul monitoraggio = 1) da mostrare
        $programmingActs = $this->getDataResults($data);
        $programmingActs = !empty($programmingActs) ? $programmingActs->toArray() : [];

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
                'prefix' => '<div class="form-group col-lg-6" style="margin-bottom: 1rem;">',
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
                'prefix' => '<div class="form-group col-lg-6" style="margin-bottom: 1rem;">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'd',
                    'id' => 'd',

                    'value' => '{description}'
                ],
                'label' => [
                    'for' => 'd',
                    'text' => 'Descrizione',
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
                    'id' => 's',

                ],
                'label' => [
                    'for' => 's',
                    'text' => 'Data - dal',
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
                    'text' => 'Data - al',
                    'class' => 'active'
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
            'obj' => Input::get('obj', true),
            'd' => Input::get('d', true),
            's' => Input::get('s', true),
            'e' => Input::get('e', true)
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value) {
            if (!empty($value)) {
                $countFilterFields += 1;
            }
        }

        $formBuilder = new FormBuilder([
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'object' => htmlEscape(Input::get('obj', true)),
            'description' => htmlEscape(Input::get('d', true)),
            'start_date' => htmlEscape(Input::get('s', true)),
            'end_date' => htmlEscape(Input::get('e', true)),
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($programmingActs);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $programmingActs;
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
        renderFront(config('vfo', null, 'app') . '/programming_acts/programming_acts', $data, 'frontend');
    }

    /**
     * Funzione che ritorna i dati da mostrare nella tabella
     *
     * @param array $data Dati da passare alla vista
     * @param null $type {le sezioni pubblica in per cui filtrare i dati}
     * @param array $actType {Tipologia atto}
     * @return mixed
     * @throws Exception
     */
    private function getDataResults(array &$data = [], $type = null, array $actType = []): mixed
    {

        // Recupero i canoni di locazione percepiti da mostrare
        $programmingActs = ProgrammingActsModel::where(
            function ($query) use ($type, $actType) {
                if (!empty($type)) {
                    $query->orWhere('public_in_public_works', 1);
                }
                if (!empty($actType)) {
                    $query->orWhereIn('act_type', $actType);
                }
            }
        )
            ->filter(
                (Input::get('obj', true)),
                (Input::get('d', true)),
                Input::get('s', true),
                Input::get('e', true),
            )
            ->orderBy('date', 'DESC')
            ->orderBy('object', 'ASC')
            ->paginate(20, ['id', 'object', 'date'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get(['obj', 'd', 's', 'e', 'sec_token']))
            ->setPath(currentUrl());

        // Richiesta del solo frammento (paginazione AJAX): essendo getDataResults() chiamato da
        // tutte le pagine di questo controller prima del lavoro pesante (form filtro, render
        // completo), un solo controllo qui copre tutte quelle pagine.
        if (isFrontFragmentRequest()) {
            $resultsArray = $programmingActs->toArray();
            $table = $this->createTableRows($resultsArray);
            renderFrontFragment(['table' => !empty($table) ? $table->generate() : null, 'instances' => $resultsArray]);
            exit;
        }

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($programmingActs) && !empty($programmingActs->toArray()['data'])) {

            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ProgrammingActsModel::select(['created_at'])
                ->where(
                    function ($query) use ($type, $actType) {
                        if (!empty($type)) {
                            $query->orWhere('public_in_public_works', 1);
                        }
                        if (!empty($actYype)) {
                            $query->orWhere('act_type', $actType);
                        }
                    }
                )
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ProgrammingActsModel::select(['updated_at'])
                ->where(
                    function ($query) use ($type, $actType) {
                        if (!empty($type)) {
                            $query->orWhere('public_in_public_works', 1);
                        }
                        if (!empty($actYype)) {
                            $query->orWhere('act_type', $actType);
                        }
                    }
                )
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        return $programmingActs;
    }

    /**
     * Metodo che crea la tabella con i dati dei risultati della ricerca da mostrare
     *
     * @param $programmingActs {dati da inserire nella tabella}
     * @return Table|null
     * @throws Exception
     */
    private function createTableRows($programmingActs = null): ?Table
    {
        $currentPageId = (int)uri()->segment(2, 0);
        $table = null;

        if (!empty($programmingActs['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();
            $table->set_heading('Oggetto', 'Data');

            // Creo le righe della tabella settando i dati da mostrare
            foreach ($programmingActs['data'] as $programmingAct) {

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $programmingAct['id'] . '/' . urlTitle($programmingAct['object'])) . '" data-id="' . $programmingAct['id'] . ' ">' . escapeXss($programmingAct['object']) . '</a>',
                    ['data' => !empty($programmingAct['date']) ? date('d/m/Y', strtotime($programmingAct['date'])) : null, 'style' => 'width: 12%;']
                );
            }
        }

        return $table;
    }

    /**
     * Metodo chiamato per la pagina "Atti di programmazione delle opere pubbliche",
     * nella sezione delle Opere pubbliche
     * ID sezione 167
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexActsPublicWorks(): void
    {
        $data = [];

        // Validazione dei campi di input per la ricerca
        $validator = new Validator();
        if (Input::get('obj') != null ||
            Input::get('d') != null ||
            Input::get('s') != null ||
            Input::get('e') != null
        ) {
            $validator->verifyToken();
        }

        $validator->label('Oggetto')->value(Input::get('obj'))->maxLength(30)->end();
        $validator->label('Descrizione')->value(Input::get('d'))->maxLength(120)->end();
        $validator->label('Data - dal')->value(Input::get('s'))->isDate('Y-m-d')->end();
        $validator->label('Data - al')->value(Input::get('e'))->isDate('Y-m-d')->end();

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

        // Recupero i procedimenti(con campo Pubblica automaticamente i dati sul monitoraggio = 1) da mostrare
        $programmingActs = $this->getDataResults($data, 'public_in_public_works');
        $programmingActs = !empty($programmingActs) ? $programmingActs->toArray() : [];

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
                'prefix' => '<div class="form-group col-lg-6" style="margin-bottom: 1rem;">',
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
                'prefix' => '<div class="form-group col-lg-6" style="margin-bottom: 1rem;">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'd',
                    'id' => 'd',

                    'value' => '{description}'
                ],
                'label' => [
                    'for' => 'd',
                    'text' => 'Descrizione',
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
                    'id' => 's',

                ],
                'label' => [
                    'for' => 's',
                    'text' => 'Data - dal',
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
                    'text' => 'Data - al',
                    'class' => 'active'
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
            'obj' => Input::get('obj', true),
            'd' => Input::get('d', true),
            's' => Input::get('s', true),
            'e' => Input::get('e', true)
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value) {
            if (!empty($value)) {
                $countFilterFields += 1;
            }
        }

        $formBuilder = new FormBuilder([
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'object' => htmlEscape(Input::get('obj', true)),
            'description' => htmlEscape(Input::get('d', true)),
            'start_date' => htmlEscape(Input::get('s', true)),
            'end_date' => htmlEscape(Input::get('e', true)),
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($programmingActs);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $programmingActs;
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

        renderFront(config('vfo', null, 'app') . '/programming_acts/programming_acts', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Programma biennale degli acquisti di beni e servizi"
     * ID sezione 297 e 298
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexBiannualProgram(): void
    {
        $data = [];

        // Validazione dei campi di input per la ricerca
        $validator = new Validator();
        if (Input::get('obj') != null ||
            Input::get('d') != null ||
            Input::get('s') != null ||
            Input::get('e') != null
        ) {
            $validator->verifyToken();
        }

        $validator->label('Oggetto')->value(Input::get('obj'))->maxLength(30)->end();
        $validator->label('Descrizione')->value(Input::get('d'))->maxLength(120)->end();
        $validator->label('Data - dal')->value(Input::get('s'))->isDate('Y-m-d')->end();
        $validator->label('Data - al')->value(Input::get('e'))->isDate('Y-m-d')->end();

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

        $actType = $currentPageId == 297 ? [1, 3] : [2];

        // Recupero i procedimenti(con campo Pubblica automaticamente i dati sul monitoraggio = 1) da mostrare
        $programmingActs = $this->getDataResults($data, null, $actType);
        $programmingActs = !empty($programmingActs) ? $programmingActs->toArray() : [];

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
                'prefix' => '<div class="form-group col-lg-6" style="margin-bottom: 1rem;">',
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
                'prefix' => '<div class="form-group col-lg-6" style="margin-bottom: 1rem;">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'd',
                    'id' => 'd',

                    'value' => '{description}'
                ],
                'label' => [
                    'for' => 'd',
                    'text' => 'Descrizione',
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
                    'id' => 's',

                ],
                'label' => [
                    'for' => 's',
                    'text' => 'Data - dal',
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
                    'text' => 'Data - al',
                    'class' => 'active'
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
            'obj' => Input::get('obj', true),
            'd' => Input::get('d', true),
            's' => Input::get('s', true),
            'e' => Input::get('e', true),
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value) {
            if (!empty($value)) {
                $countFilterFields += 1;
            }
        }

        $formBuilder = new FormBuilder([
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'object' => htmlEscape(Input::get('obj', true)),
            'description' => htmlEscape(Input::get('d', true)),
            'start_date' => htmlEscape(Input::get('s', true)),
            'end_date' => htmlEscape(Input::get('e', true)),
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($programmingActs);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $programmingActs;
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
        renderFront(config('vfo', null, 'app') . '/programming_acts/programming_acts', $data, 'frontend');
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
        $element = ProgrammingActsModel::where('id', $elementId);

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

        $label = 'programming_acts';
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
        $showOnlyPublic = true;
        $sectionId = (int)uri()->segment(2, 0);

        // Allegati
        $attach = new AttachmentArchive();
        $data['listAttach'] = $attach->getAllByObject(
            $label,
            $elementId,
            $selectFields,
            $showOnlyPublic
        );

        renderFront(config('vfo', null, 'app') . '/programming_acts/details', $data, 'frontend');
    }
}
