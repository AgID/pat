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
use Model\ContestsActsModel;
use Model\NoticesActsModel;
use Model\SectionsFoModel;
use System\Input;
use System\Validator;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller Atti delle amministrazioni
 */
class NoticeActsFrontController extends BaseFrontController
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
     * Metodo chiamato per la pagina "Contratti",
     * nella sezione dei Bandi di gara
     * -> Atti delle amministrazioni aggiudicatrici e degli enti aggiudicatari distintamente per ogni procedura
     * ID sezione 116
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexManagerialPositions(): void
    {
        $this->getDataResultsForPublicIn();
    }

    /**
     * Funzione per le pagine che hanno come criterio di pubblicazione
     * il solo campo pubblica in
     *
     * @return void
     * @throws Exception
     */
    private function getDataResultsForPublicIn(): void
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
        $validator->label('Oggetto')->value(Input::get('o'))->maxLength(100)->end();
        $validator->label('data dal')->value(Input::get('start'))->isDate('Y-m-d')->end();
        $validator->label('data al')->value(Input::get('end'))->isDate('Y-m-d')->end();

        if (!$validator->isSuccess()) {
            echo showError('Errore nella validazione dei dati', $validator->getErrorsHtml(), $validator->getErrorStatusCode());
            die();
        }

        // Recupero i procedimenti(con campo Pubblica automaticamente i dati sul monitoraggio = 1) da mostrare
        $noticeActs = NoticesActsModel::whereHas('public_in_section', function ($query) use ($currentPageId) {
            $query->where('public_in_id', $currentPageId);
        })
            ->filter(
                (Input::get('o', true)),
                Input::get('start', true),
                Input::get('end', true)
            )
            ->orderBy('date', 'DESC')
            ->orderBy('object', 'ASC')
            ->paginate(20, ['object_notices_acts.id', 'object_notices_acts.object', 'date'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get(['o', 'start', 'end', 'sec_token']))
            ->setPath(currentUrl());

        $noticeActs = !empty($noticeActs) ? $noticeActs->toArray() : [];

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($noticeActs);

        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $noticeActs;

        // Richiesta del solo frammento (paginazione AJAX): tabella e paginazione sono pronte,
        // tutto il resto (pagina corrente, menu, filtri, contenuti) serve solo alla pagina
        // completa e viene saltato. Essendo questo metodo condiviso da tutte le pagine che lo
        // chiamano, un solo controllo qui copre tutte quelle pagine.
        if (renderFrontFragment($data)) {

            return;

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

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($noticeActs['data'])) {

            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(NoticesActsModel::select(['created_at'])
                ->whereHas('public_in_section', function ($query) use ($currentPageId) {
                    $query->where('public_in_id', $currentPageId);
                })
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(NoticesActsModel::select(['updated_at'])
                ->whereHas('public_in_section', function ($query) use ($currentPageId) {
                    $query->where('public_in_id', $currentPageId);
                })
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

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

        $formBuilder = new FormBuilder([
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'object' => htmlEscape(Input::get('o', true)),
            'start' => htmlEscape(Input::get('start', true)),
            'end' => htmlEscape(Input::get('end', true)),

        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['items'] = $noticeActs;
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

        //Inclusione di eventuali librerie esterne
        $currentPage = $currentPage ?? null;
        $data = $data ?? null;
        $data = $data ?? null;

        renderFront(config('vfo', null, 'app') . '/notice_acts/notice_acts', $data, 'frontend');
    }

    /**
     * Metodo che crea la tabella con i dati dei risultati della ricerca da mostrare
     *
     * @param $noticeActs {dati da inserire nella tabella}
     * @return Table|null
     * @throws Exception
     */
    private function createTableRows($noticeActs = null): ?Table
    {
        $currentPageId = (int)uri()->segment(2, 0);
        $table = null;

        if (!empty($noticeActs['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();
            $table->set_heading('Oggetto', 'Data');

            // Creo le righe della tabella settando i dati da mostrare
            foreach ($noticeActs['data'] as $noticeAct) {

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $noticeAct['id'] . '/' . urlTitle($noticeAct['object'])) . '" data-id="' . $noticeAct['id'] . ' ">' . escapeXss($noticeAct['object']) . '</a>',
                    !empty($noticeAct['date']) ? date('d/m/Y', strtotime($noticeAct['date'])) : null,
                );
            }
        }

        return $table;
    }

    /**
     * Metodo chiamato per la pagina "Composizioni delle commissioni giudicatrici e i curricula dei suoi componenti",
     * nella sezione dei Bandi di gara
     * -> Atti delle amministrazioni aggiudicatrici e degli enti aggiudicatari distintamente per ogni procedura
     * ID sezione 115
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexCommissionComposition(): void
    {
        $this->getDataResultsForPublicIn();
    }

    /**
     * Metodo chiamato per la pagina "Provvedimenti che determinano le esclusioni dalla procedura di affidamento e le
     * ammissioni all'esito delle valutazioni dei requisiti soggettivi, economico-finanziari e tecnico-professionali",
     * nella sezione dei Bandi di gara -> Atti delle amministrazioni aggiudicatrici e degli enti aggiudicatari distintamente per ogni procedura
     * ID sezione 114
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexMeasuresExclusions(): void
    {
        $this->getDataResultsForPublicIn();
    }

    /**
     * Metodo chiamato per la pagina "Resoconti della gestione finanziaria dei contratti al termine della loro esecuzione",
     * nella sezione dei Bandi di gara
     * -> Atti delle amministrazioni aggiudicatrici e degli enti aggiudicatari distintamente per ogni procedura
     * ID sezione 117
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexFinancialManagementReports(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Validazione dei campi d'input per la ricerca
        $validator = new Validator();
        if (Input::get('o') != null ||
            Input::get('cig') != null ||
            Input::get('wss') != null ||
            Input::get('wse') != null ||
            Input::get('cdws') != null ||
            Input::get('cdwe') != null ||
            Input::get('obj') != null ||
            Input::get('start') != null ||
            Input::get('end') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Oggetto affidamento')
            ->value(Input::get('o'))
            ->maxLength(100)
            ->end();

        $validator->label('Cig')
            ->value(Input::get('cig'))
            ->isAlphaNumSpaces()
            ->maxLength(30)
            ->end();

        $validator->label('data di inizio dei lavori - dal')
            ->value(Input::get('wss'))
            ->isDate('Y-m-d')
            ->end();

        $validator->label('data di inizio dei lavori - al')
            ->value(Input::get('wse'))
            ->isDate('Y-m-d')
            ->end();

        $validator->label('data di ultimazione dei lavori - dal')
            ->value(Input::get('cdws'))
            ->isDate('Y-m-d')
            ->end();

        $validator->label('data di ultimazione dei lavori - al')
            ->value(Input::get('cdwe'))
            ->isDate('Y-m-d')->end();

        $validator->label('Oggetto atto')->value(Input::get('obj'))->maxLength(100)->end();
        $validator->label('data dal')->value(Input::get('start'))->isDate('Y-m-d')->end();
        $validator->label('data al')->value(Input::get('end'))->isDate('Y-m-d')->end();

        if (!$validator->isSuccess()) {
            echo showError('Errore nella validazione dei dati', $validator->getErrorsHtml(), $validator->getErrorStatusCode());
            die();
        }

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'section_fo.id', 'parent_id', 'form_filter', 'controller_open_data', 'archive_name',
            'rel_institution_type_sections_labeling.label', 'section_fo.created_at', 'section_fo.updated_at', 'is_system','controller_open_data'])
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
        $noticeActs = NoticesActsModel::whereHas('public_in_section', function ($query) use ($currentPageId) {
            $query->where('public_in_id', $currentPageId);
        })
            ->filter(
                (Input::get('obj', true)),
                Input::get('start', true),
                Input::get('end', true)
            )
            ->orderBy('date', 'DESC')
            ->orderBy('object', 'ASC')
            ->paginate(20, ['object_notices_acts.id', 'object_notices_acts.object', 'date'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get(['o', 'cig', 'wss', 'wse', 'cdws', 'cdwe', 'obj', 'start', 'end', 'sec_token']))
            ->setPath(currentUrl());
        $noticeActs = !empty($noticeActs) ? $noticeActs->toArray() : [];

        // Recupero i procedimenti(con campo Pubblica automaticamente i dati sul monitoraggio = 1) da mostrare
        $contestActs = ContestsActsModel::whereIn('object_contests_acts.typology', ['result', 'foster'])
            ->where('object_contests_acts.work_end_date', '<', date("Y-m-d"))
            ->with(['relative_liquidation' => function ($query) {
                $query->select(['relative_procedure_id', 'id', 'object', 'anac_year', 'amount_liquidated']);
            }])
            ->with(['relative_notice' => function ($query) {
                $query->select(['object_contests_acts.id', 'object', 'cig', 'adjudicator_name', 'adjudicator_data', 'relative_notice_id', 'contraent_choice']);
                $query->with(['contraent_choice' => function ($query) {
                    $query->select(['id', 'name']);
                }]);
            }])
            ->financialmanagementreports(
                (Input::get('o', true)),
                (Input::get('cig', true)),
                Input::get('wss', true),
                Input::get('wse', true),
                Input::get('cdws', true),
                Input::get('cdwe', true)
            )
            ->orderBy('object_contests_acts.work_end_date', 'DESC')
            ->paginate(15, ['object_contests_acts.id', 'object_contests_acts.object', 'object_contests_acts.expiration_date', 'object_contests_acts.award_amount_value',
                'object_contests_acts.work_start_date', 'object_contests_acts.work_end_date', 'object_contests_acts.cig', 'object_contests_acts.relative_procedure_id',
                'object_contests_acts.relative_notice_id'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get(['o', 'cig', 'wss', 'wse', 'cdws', 'cdwe', 'sec_token']))
            ->setPath(currentUrl());

        $contestActs = !empty($contestActs) ? $contestActs->toArray() : [];

        $table = null;

        if (!empty($contestActs['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();

            $table->set_heading('Cig', 'Oggetto', 'Data inizio lavori', 'Data fine lavori', 'Importo contratto', 'Importo liquidato', 'Scostamento');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($contestActs['data'] as $contestAct) {

                // Somme liquidate
                $sumLiquidated = array_reduce($contestAct['relative_liquidation'], function ($i, $obj) {
                    return $i + ((float)$obj['amount_liquidated']);
                });

                $table->add_row(
                    !empty($contestAct['cig']) ? $contestAct['cig'] : $contestAct['relative_notice']['cig'] ?? null,
                    '<a href="' . siteUrl('page/' . 110 . '/details/' . $contestAct['id'] . '/' . urlTitle($contestAct['object'])) . '" data-id="' . $contestAct['id'] . ' ">' . $contestAct['object'] . '</a>',
                    !empty($contestAct['work_start_date']) ?
                        date('d/m/Y', strtotime($contestAct['work_start_date']))
                        : null,
                    !empty($contestAct['work_end_date']) ?
                        date('d/m/Y', strtotime($contestAct['work_end_date']))
                        : null,
                    !empty($contestAct['award_amount_value']) ? '&euro; ' . S::currency($contestAct['award_amount_value'], 2, ',', '.') : null,
                    !empty($contestAct['relative_liquidation']) ? '&euro; ' . S::currency($sumLiquidated, 2, ',', '.') : null,
                    '&euro; ' . S::currency((float)$contestAct['award_amount_value'] - $sumLiquidated, 2, ',', '.')
                );
            }

        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $actsTable = $this->createTableRows($noticeActs);

        // Form per i filtri di ricerca dei bandi
        $form = json_encode([
            [
                'type' => 'tag',
                'content' => '<h3 class="page-subtitle">Ricerca - Affidamenti</h3>',
            ],
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
                'prefix' => '<div class="form-group col-lg-6" style="margin-bottom: 1rem;">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'o',
                    'value' => '{object}',
                    'id' => 'o',
                    'class' => 'active',

                ],
                'label' => [
                    'for' => 'o',
                    'text' => 'Oggetto',
                ]
            ],
            [
                'prefix' => '<div class="form-group col-lg-6" style="margin-bottom: 1rem;">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'cig',
                    'value' => '{cig}',
                    'id' => 'cig',

                ],
                'label' => [
                    'for' => 'cig',
                    'text' => 'Codice CIG',
                ]
            ],
            [
                'prefix' => '<div class="col-lg-6 mt-2">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'wss',
                    'value' => '{wss}',
                    'type' => 'date',
                    'id' => 'work_start_date_start',

                ],
                'label' => [
                    'for' => 'wss',
                    'text' => 'Data di inizio dei lavori - dal',
                    'class' => 'active'
                ]
            ],
            [
                'prefix' => '<div class="col-lg-6 mt-2">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'wse',
                    'value' => '{wse}',
                    'type' => 'date',
                    'id' => 'work_start_date',

                ],
                'label' => [
                    'for' => 'wse',
                    'text' => 'Data di inizio dei lavori -  al',
                    'class' => 'active'
                ]
            ],
            [
                'prefix' => '<div class="col-lg-6 mt-2">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'cdws',
                    'value' => '{cdws}',
                    'type' => 'date',
                    'id' => 'completion_date_works_start',

                ],
                'label' => [
                    'for' => 'cdws',
                    'text' => 'Data di ultimazione dei lavori - dal',
                    'class' => 'active'
                ]
            ],
            [
                'prefix' => '<div class="col-lg-6 mt-2">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'cdwe',
                    'value' => '{cdwe}',
                    'type' => 'date',
                    'id' => 'completion_date_works_end',

                ],
                'label' => [
                    'for' => 'cdwe',
                    'text' => 'Data di ultimazione dei lavori -  al',
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
            'o' => Input::get('o', true),
            'cig' => Input::get('cig', true),
            'wss' => Input::get('wss', true),
            'wse' => Input::get('wse', true),
            'cdws' => Input::get('cdws', true),
            'cdwe' => Input::get('cdwe', true),
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
            'object' => htmlEscape(Input::get('o', true)), // Oggetto
            'cig' => htmlEscape(Input::get('cig', true)), // Codice CIG
            'wss' => htmlEscape(Input::get('wss', true)),// Data di inizio dei lavori - dal
            'wse' => htmlEscape(Input::get('wse', true)), // Data di inizio dei lavori - al
            'cdws' => htmlEscape(Input::get('cdws', true)), // Data di ultimazione dei lavori - dal
            'cdwe' => htmlEscape(Input::get('cdwe', true)), // Data di ultimazione dei lavori - al
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        // Form per i filtri di ricerca degli atti
        $formNoticeActs = json_encode([
            [
                'type' => 'tag',
                'content' => '<h3 class="page-subtitle">Ricerca - Atti</h3>',
            ],
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
                    'value' => '{obj}',
                    'id' => 'obj',
                    'class' => 'active',

                ],
                'label' => [
                    'for' => 'obj',
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
                    'id' => '__reset_form_n',
                    'name' => 'reset_n',
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


        $formFilterFieldsNotice = [
            'obj' => Input::get('obj', true),
            'start' => Input::get('start', true),
            'end' => Input::get('end', true),
        ];

        $countFilterFieldsNotice = 0;
        foreach ($formFilterFieldsNotice as $value) {
            if (!empty($value)) {
                $countFilterFieldsNotice += 1;
            }
        }

        $formBuilderNoticeActs = new FormBuilder([
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'obj' => htmlEscape(Input::get('obj', true)),
            'start' => htmlEscape(Input::get('start', true)),
            'end' => htmlEscape(Input::get('end', true)),

        ]);

        $formBuilderNoticeActs->loadJsonFromVar($formNoticeActs);
        $data['formFilterNoticeActs'] = $formBuilderNoticeActs->render();
        $data['searchFieldsOpenDataNotice'] = $formFilterFieldsNotice;
        $data['filterUsedNotice'] = $countFilterFieldsNotice;

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['actsTable'] = !empty($actsTable) ? $actsTable->generate() : $actsTable;
        $data['instances'] = $contestActs;
        $data['notices'] = $noticeActs;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = !empty($currentPage['no_required']);
        $data['allowSearch'] = true;
        $data['allowSearchNotices'] = true;
        $data['title'] = true;
        $data['titleNotices'] = true;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['doubleIssue'] = true;
        $data['rangeOpenData'] = 2000;
        $data['openDataPublication'] = 'contest-act';
        $data['searchFieldsOpenData'] = $formFilterFields;
        $data['filterUsed'] = $countFilterFields;

        //Inclusione di eventuali librerie esterne
        $data = $data ?? null;

        //Inclusione di eventuali librerie esterne
        $currentPage = $currentPage ?? null;
        $data = $data ?? null;

        renderFront(config('vfo', null, 'app') . '/notice_acts/financial_management_reports', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Trasparenza nella partecipazione di portatori di interessi e dibattito pubblico",
     * nella sezione dei Bandi di gara
     * ID sezione 529
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexTransparencyOfParticipation(): void
    {
        $this->getDataResultsForPublicIn();
    }

    /**
     * Metodo chiamato per la pagina "Collegi consultivi tecnici",
     * nella sezione dei Bandi di gara e contratti
     * ID sezione 530
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexTechnicalAdvisoryColleges(): void
    {
        $this->getDataResultsForPublicIn();
    }

    /**
     * Metodo chiamato per la pagina "Progetti di investimento pubblico",
     * nella sezione dei Bandi di gara e contratti
     * ID sezione 531
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexPublicInvestmentProjects(): void
    {
        $this->getDataResultsForPublicIn();
    }

    /**
     * Metodo chiamato per la pagina "Fase esecutiva",
     * nella sezione dei Bandi di gara e contratti
     * ID sezione 527
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexExecutiveStage(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Validazione dei campi d'input per la ricerca
        $validator = new Validator();
        if (Input::get('o') != null ||
            Input::get('cig') != null ||
            Input::get('wss') != null ||
            Input::get('wse') != null ||
            Input::get('cdws') != null ||
            Input::get('cdwe') != null ||
            Input::get('obj') != null ||
            Input::get('start') != null ||
            Input::get('end') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Oggetto affidamento')
            ->value(Input::get('o'))
            ->maxLength(100)
            ->end();

        $validator->label('Cig')
            ->value(Input::get('cig'))
            ->isAlphaNumSpaces()
            ->maxLength(30)
            ->end();

        $validator->label('data di inizio dei lavori - dal')
            ->value(Input::get('wss'))
            ->isDate('Y-m-d')
            ->end();

        $validator->label('data di inizio dei lavori - al')
            ->value(Input::get('wse'))
            ->isDate('Y-m-d')
            ->end();

        $validator->label('data di ultimazione dei lavori - dal')
            ->value(Input::get('cdws'))
            ->isDate('Y-m-d')
            ->end();

        $validator->label('data di ultimazione dei lavori - al')
            ->value(Input::get('cdwe'))
            ->isDate('Y-m-d')->end();

        $validator->label('Oggetto atto')->value(Input::get('obj'))->maxLength(100)->end();
        $validator->label('data dal')->value(Input::get('start'))->isDate('Y-m-d')->end();
        $validator->label('data al')->value(Input::get('end'))->isDate('Y-m-d')->end();

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

        // Recupero i procedimenti(con campo Pubblica automaticamente i dati sul monitoraggio = 1) da mostrare
        $noticeActs = NoticesActsModel::whereHas('public_in_section', function ($query) use ($currentPageId) {
            $query->where('public_in_id', $currentPageId);
        })
            ->filter(
                (Input::get('obj', true)),
                Input::get('start', true),
                Input::get('end', true)
            )
            ->orderBy('date', 'DESC')
            ->orderBy('object', 'ASC')
            ->paginate(20, ['object_notices_acts.id', 'object_notices_acts.object', 'date'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get(['o', 'cig', 'wss', 'wse', 'cdws', 'cdwe', 'obj', 'start', 'end', 'sec_token']))
            ->setPath(currentUrl());
        $noticeActs = !empty($noticeActs) ? $noticeActs->toArray() : [];

        // Recupero i procedimenti(con campo Pubblica automaticamente i dati sul monitoraggio = 1) da mostrare
        $contestActs = ContestsActsModel::where('object_contests_acts.typology', 'foster')
            ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
            ->where(
                function ($query) {
                    $query->where('object_contests_acts.expiration_date', '<=', date("Y-m-d H:i:s"))
                        ->orWhereNull('object_contests_acts.expiration_date');
                }
            )
            ->whereHas('public_in_section', function ($query) use ($currentPageId) {
                $query->where('public_in_id', $currentPageId);
            })
            ->with(['relative_liquidation' => function ($query) {
                $query->select(['relative_procedure_id', 'id', 'object', 'anac_year', 'amount_liquidated']);
            }])
            ->with(['relative_notice' => function ($query) {
                $query->select(['object_contests_acts.id', 'object', 'cig', 'adjudicator_name', 'adjudicator_data', 'relative_notice_id', 'contraent_choice']);
                $query->with(['contraent_choice' => function ($query) {
                    $query->select(['id', 'name']);
                }]);
            }])
            ->financialmanagementreports(
                (Input::get('o', true)),
                (Input::get('cig', true)),
                Input::get('wss', true),
                Input::get('wse', true),
                Input::get('cdws', true),
                Input::get('cdwe', true)
            )
            ->orderBy('expiration_date', 'DESC')
            ->paginate(15, ['object_contests_acts.id', 'object_contests_acts.object', 'object_contests_acts.expiration_date', 'object_contests_acts.award_amount_value',
                'object_contests_acts.work_start_date', 'object_contests_acts.work_end_date', 'object_contests_acts.cig', 'object_contests_acts.relative_procedure_id',
                'object_contests_acts.relative_notice_id'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get(['o', 'cig', 'wss', 'wse', 'cdws', 'cdwe', 'sec_token']))
            ->setPath(currentUrl());

        $contestActs = !empty($contestActs) ? $contestActs->toArray() : [];

        $table = null;

        if (!empty($contestActs['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();

            $table->set_heading('Cig', 'Oggetto', 'Data inizio', 'Data fine', 'Importo contratto', 'Importo liquidato');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($contestActs['data'] as $contestAct) {

                // Somme liquidate
                $sumLiquidated = array_reduce($contestAct['relative_liquidation'], function ($i, $obj) {
                    return $i + ((float)$obj['amount_liquidated']);
                });

                $table->add_row(
                    !empty($contestAct['cig']) ? $contestAct['cig'] : $contestAct['relative_notice']['cig'] ?? null,
                    '<a href="' . siteUrl('page/' . 110 . '/details/' . $contestAct['id'] . '/' . urlTitle($contestAct['object'])) . '" data-id="' . $contestAct['id'] . ' ">' . $contestAct['object'] . '</a>',
                    !empty($contestAct['work_start_date']) ?
                        date('d/m/Y', strtotime($contestAct['work_start_date']))
                        : null,
                    !empty($contestAct['work_end_date']) ?
                        date('d/m/Y', strtotime($contestAct['work_end_date']))
                        : null,
                    !empty($contestAct['award_amount_value']) ? '&euro; ' . S::currency($contestAct['award_amount_value'], 2, ',', '.') : null,
                    !empty($contestAct['relative_liquidation']) ? '&euro; ' . S::currency($sumLiquidated, 2, ',', '.') : null,
                );
            }

        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $actsTable = $this->createTableRows($noticeActs);

        // Form per i filtri di ricerca dei bandi
        $form = json_encode([
            [
                'type' => 'tag',
                'content' => '<h3 class="page-subtitle">Ricerca - Affidamenti</h3>',
            ],
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
                'prefix' => '<div class="form-group col-lg-6">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'o',
                    'value' => '{object}',
                    'id' => 'o',
                    'class' => 'active',

                ],
                'label' => [
                    'for' => 'o',
                    'text' => 'Oggetto',
                ]
            ],
            [
                'prefix' => '<div class="form-group col-lg-6">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'cig',
                    'value' => '{cig}',
                    'id' => 'cig',

                ],
                'label' => [
                    'for' => 'cig',
                    'text' => 'Codice CIG',
                ]
            ],
            [
                'prefix' => '<div class="col-lg-6 form-group">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'wss',
                    'value' => '{wss}',
                    'type' => 'date',
                    'id' => 'work_start_date_start',

                ],
                'label' => [
                    'for' => 'wss',
                    'text' => 'Data di inizio dei lavori - dal',
                    'class' => 'active'
                ]
            ],
            [
                'prefix' => '<div class="col-lg-6 form-group">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'wse',
                    'value' => '{wse}',
                    'type' => 'date',
                    'id' => 'work_start_date',

                ],
                'label' => [
                    'for' => 'wse',
                    'text' => 'Data di inizio dei lavori -  al',
                    'class' => 'active'
                ]
            ],
            [
                'prefix' => '<div class="col-lg-6 form-group">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'cdws',
                    'value' => '{cdws}',
                    'type' => 'date',
                    'id' => 'completion_date_works_start',

                ],
                'label' => [
                    'for' => 'cdws',
                    'text' => 'Data di ultimazione dei lavori - dal',
                    'class' => 'active'
                ]
            ],
            [
                'prefix' => '<div class="col-lg-6 form-group">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'cdwe',
                    'value' => '{cdwe}',
                    'type' => 'date',
                    'id' => 'completion_date_works_end',

                ],
                'label' => [
                    'for' => 'cdwe',
                    'text' => 'Data di ultimazione dei lavori -  al',
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
            'o' => Input::get('o', true),
            'cig' => Input::get('cig', true),
            'wss' => Input::get('wss', true),
            'wse' => Input::get('wse', true),
            'cdws' => Input::get('cdws', true),
            'cdwe' => Input::get('cdwe', true),
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
            'object' => htmlEscape(Input::get('o', true)), // Oggetto
            'cig' => htmlEscape(Input::get('cig', true)), // Codice CIG
            'wss' => htmlEscape(Input::get('wss', true)),// Data di inizio dei lavori - dal
            'wse' => htmlEscape(Input::get('wse', true)), // Data di inizio dei lavori - al
            'cdws' => htmlEscape(Input::get('cdws', true)), // Data di ultimazione dei lavori - dal
            'cdwe' => htmlEscape(Input::get('cdwe', true)), // Data di ultimazione dei lavori - al
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        // Form per i filtri di ricerca degli atti
        $formNoticeActs = json_encode([
            [
                'type' => 'tag',
                'content' => '<h3 class="page-subtitle">Ricerca - Atti</h3>',
            ],
            [
                'type' => 'form_open',
                'name' => 'relief_check',
                'action' => '{current_url}',
                'attributes' => [
                    'method' => 'get',
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
                    'value' => '{obj}',
                    'id' => 'obj',
                    'class' => 'active',

                ],
                'label' => [
                    'for' => 'obj',
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
                    'id' => '__reset_form_n',
                    'name' => 'reset_n',
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

        $formFilterFieldsNotice = [
            'obj' => Input::get('obj', true),
            'start' => Input::get('start', true),
            'end' => Input::get('end', true),
        ];

        $countFilterFieldsNotice = 0;
        foreach ($formFilterFieldsNotice as $value) {
            if (!empty($value)) {
                $countFilterFieldsNotice += 1;
            }
        }

        $formBuilderNoticeActs = new FormBuilder([
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'obj' => htmlEscape(Input::get('obj', true)),
            'start' => htmlEscape(Input::get('start', true)),
            'end' => htmlEscape(Input::get('end', true)),

        ]);

        $formBuilderNoticeActs->loadJsonFromVar($formNoticeActs);
        $data['formFilterNoticeActs'] = $formBuilderNoticeActs->render();
        $data['searchFieldsOpenDataNotice'] = $formFilterFieldsNotice;
        $data['filterUsedNotice'] = $countFilterFieldsNotice;

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['actsTable'] = !empty($actsTable) ? $actsTable->generate() : $actsTable;
        $data['instances'] = $contestActs;
        $data['notices'] = $noticeActs;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = !empty($currentPage['no_required']);
        $data['allowSearch'] = true;
        $data['allowSearchNotices'] = true;
        $data['title'] = true;
        $data['titleNotices'] = true;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['rangeOpenData'] = 2000;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['doubleIssue'] = true;
        $data['openDataPublication'] = 'contest-act';
        $data['searchFieldsOpenData'] = $formFilterFields;
        $data['filterUsed'] = $countFilterFields;
        //Inclusione di eventuali librerie esterne
        $currentPage = $currentPage ?? null;
        $data = $data ?? null;
        //Inclusione di eventuali librerie esterne
        $data = $data ?? null;

        renderFront(config('vfo', null, 'app') . '/notice_acts/financial_management_reports', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Verbali delle commissioni di gara",
     * nella sezione dei Bandi di gara e contratti
     * ID sezione 578
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexCommissionMinutes(): void
    {
        $this->getDataResultsForPublicIn();
    }

    /**
     * Metodo chiamato per la pagina "Pari opportunità e inclusione lavorativa nei contratti pubblici, nel PNRR e nel PNC",
     * nella sezione dei Bandi di gara e contratti
     * ID sezione 579
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexEqualOpportunities(): void
    {
        $this->getDataResultsForPublicIn();
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
        $element = NoticesActsModel::where('id', $elementId)
            ->with('relative_contest_act:id,object')
            ->with('assignments:id,name');


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

        $label = 'notices_acts';
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

        renderFront(config('vfo', null, 'app') . '/notice_acts/details', $data, 'frontend');
    }
}
