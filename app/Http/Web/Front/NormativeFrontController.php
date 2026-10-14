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
use Model\NormativesModel;
use Model\SectionsFoModel;
use Model\StructuresModel;
use System\Input;
use System\Validator;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller Normative
 */
class NormativeFrontController extends BaseFrontController
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
     * Metodo chiamato per la pagina "Criteri e modalità" nella sezione delle Sovvenzioni
     * Id sezione 125
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexCriteriaAndModalities(): void
    {
        $data = [];

        $validation = new Validator();
        if (Input::get('norm') != null ||
            Input::get('t') != null ||
            Input::get('n') != null ||
            Input::get('s') != null ||
            Input::get('e') != null ||
            Input::get('u') != null
        ) {
            $validation->verifyToken();
        }
        $validation->label('Denominazione')->value(Input::get('norm'))->maxLength(50)->end();
        $validation->label('Tipologia')->value(Input::get('t'))->isInt()->end();
        $validation->label('Numero')->value(Input::get('n'))->isInt()->end();
        $validation->label('Data emissione - dal')->value(Input::get('s'))->isDate('Y-m-d')->end();
        $validation->label('Data emissione - al')->value(Input::get('e'))->isDate('Y-m-d')->end();
        $validation->label('Uffici')->value(Input::get('u'))->isInt()->end();

        if (!$validation->isSuccess()) {

            echo showError('Attenzione', $validation->getErrorsHtml(), $validation->getErrorStatusCode());
            die();

        }

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Recupero i procedimenti(con campo Pubblica automaticamente i dati sul monitoraggio = 1) da mostrare
        $normatives = NormativesModel::where('normative_topic', 2)
            ->filter(
                Input::get('norm', true),
                (int)Input::get('t', true),
                (int)Input::get('n', true),
                Input::get('s', true),
                Input::get('e', true),
                (int)Input::get('u', true)
            )
            ->orderBy('issue_date', 'DESC')
            ->orderBy('name', 'ASC')
            ->paginate(20, ['object_normatives.id', 'name'], 'p', (int)Input::get('p', true))
            ->onEachSide(2)
            ->appends(Input::get(['norm', 't', 'n', 's', 'e', 'u', 'sec_token']))
            ->setPath(currentUrl());

        $normatives = !empty($normatives) ? $normatives->toArray() : [];

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($normatives);

        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $normatives;

        // Richiesta del solo frammento (paginazione AJAX): tabella e paginazione sono pronte,
        // tutto il resto (pagina corrente, menu, filtri, contenuti) serve solo alla pagina
        // completa e viene saltato.
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
        if (!empty($normatives['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(NormativesModel::select(['created_at'])
                ->where('normative_topic', 2)
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(NormativesModel::select(['updated_at'])
                ->where('normative_topic', 2)
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
                'name' => 'absence_rates',
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
                    'name' => 'norm',
                    'id' => 'norm',
                    'value' => '{normative}',
                ],
                'label' => [
                    'for' => 'norm',
                    'text' => 'Denominazione o titolo',
                ],
            ],
            [
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'n',
                    'id' => 'n',
                    'value' => '{number}',
                ],
                'label' => [
                    'for' => 'n',
                    'text' => 'Numero',
                ],
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-4 mb-5">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{typology}',
                'name' => 't',
                'extra' => 'id="typology"',
                'label' => [
                    'for' => 't',
                    'text' => 'Tipologia',
                ],
            ],
            [
                'prefix' => '<div class="form-group col-lg-4 mb-5">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 's',
                    'type' => 'date',
                    'id' => 'start_date',
                    'value' => '{start_date}',
                ],
                'label' => [
                    'for' => 's',
                    'text' => 'Data emissione - dal',
                    'class' => 'active'
                ],
            ],
            [
                'prefix' => '<div class="form-group col-lg-4 mb-5">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'e',
                    'type' => 'date',
                    'id' => 'end_date',
                    'value' => '{end_date}',
                ],
                'label' => [
                    'for' => 'e',
                    'text' => 'Data emissione - al',
                    'class' => 'active'
                ],
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-4 mb-5">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{office}',
                'name' => 'u',
                'extra' => 'id="office"',
                'label' => [
                    'for' => 'o',
                    'text' => 'Uffici',
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
            'norm' => Input::get('norm', true),
            't' => Input::get('t', true),
            'n' => Input::get('n', true),
            's' => Input::get('s', true),
            'e' => Input::get('e', true),
            'u' => Input::get('u', true),
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value) {
            if (!empty($value)) {
                $countFilterFields += 1;
            }
        }

        // Per il filtro
        $normTypes = [null => 'Qualunque'] + config('normativeTypologies', [], 'app');

        // Filtro per strutture
        $structureIds = NormativesModel::select(['object_structures_id'])
            ->where('normative_topic', 2)
            ->join('rel_normatives_structures as rel', 'rel.object_normatives_id', '=', 'object_normatives.id')
            ->groupBy('rel.object_structures_id')
            ->get()
            ->toArray();
        $structures = [null => 'Seleziona una struttura'] + optional(StructuresModel::whereIn('id', $structureIds)
                ->where(function ($query) {
                })
                ->orderBy('structure_name', 'ASC')
                ->pluck('structure_name', 'id'))
                ->toArray();

        $formBuilder = new FormBuilder([
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'normative' => htmlEscape(Input::get('norm', true)),
            'typology' => FormBuilder::parseOptions($normTypes, htmlEscape(Input::get('t', true))),
            'office' => FormBuilder::parseOptions($structures, htmlEscape(Input::get('u', true))),
            'number' => htmlEscape(Input::get('n', true)),
            'start_date' => htmlEscape(Input::get('s', true)),
            'end_date' => htmlEscape(Input::get('e', true)),
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['finalSectionId'] = 125;
        $data['paragraphs'] = $contents;
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
        $data = $data ?? null;

        //Inclusione di eventuali librerie esterne
        $currentPage = $currentPage ?? null;
        $data = $data ?? null;
        $data = $data ?? null;

        renderFront(config('vfo', null, 'app') . '/grants/grants', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Riferimenti normativi su organizzazione e attività",
     * nella sezione delle Disposizioni Generali -> Atti generali
     * Id sezione 24
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexReferencesOnOrganizationAndActivities(): void
    {
        $data = [];

        $validation = new Validator();

        if (Input::get('norm') != null ||
            Input::get('t') != null ||
            Input::get('n') != null ||
            Input::get('s') != null ||
            Input::get('e') != null ||
            Input::get('u') != null
        ) {
            $validation->verifyToken();
        }
        $validation->label('Denominazione')->value(Input::get('norm'))->maxLength(50)->end();
        $validation->label('Tipologia')->value(Input::get('t'))->isInt()->end();
        $validation->label('Numero')->value(Input::get('n'))->isInt()->end();
        $validation->label('Data emissione - dal')->value(Input::get('s'))->isDate('Y-m-d')->end();
        $validation->label('Data emissione - al')->value(Input::get('e'))->isDate('Y-m-d')->end();
        $validation->label('Uffici')->value(Input::get('u'))->isInt()->end();

        if (!$validation->isSuccess()) {

            echo showError('Attenzione', $validation->getErrorsHtml(), $validation->getErrorStatusCode());
            die();

        }

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Recupero i procedimenti(con campo Pubblica automaticamente i dati sul monitoraggio = 1) da mostrare
        $normatives = NormativesModel::whereIn('normative_topic', [1,4,50,51,52])
            ->filter(
                (Input::get('norm', true)),
                (int)Input::get('t', true),
                (int)Input::get('n', true),
                Input::get('s', true),
                Input::get('e', true),
                (int)Input::get('u', true)
            )
            ->orderBy('issue_date', 'DESC')
            ->orderBy('name', 'ASC')
            ->paginate(21, ['object_normatives.id', 'name'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get(['norm', 't', 'n', 's', 'e', 'u', 'sec_token']))
            ->setPath(currentUrl());

        $normatives = !empty($normatives) ? $normatives->toArray() : [];

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($normatives);

        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $normatives;

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

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($normatives['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(NormativesModel::select(['created_at'])
                ->whereIn('normative_topic', [1,4,50,51,52])
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(NormativesModel::select(['updated_at'])
                ->whereIn('normative_topic', [1,4,50,51,52])
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
                'name' => 'absence_rates',
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
                    'name' => 'norm',
                    'id' => 'norm',
                    'value' => '{normative}',
                ],
                'label' => [
                    'for' => 'norm',
                    'text' => 'Denominazione o titolo',
                ],
            ],
            [
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'n',
                    'id' => 'n',
                    'value' => '{number}',
                ],
                'label' => [
                    'for' => 'n',
                    'text' => 'Numero',
                ],
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-4 mb-5">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{typology}',
                'name' => 't',
                'extra' => 'id="typology"',
                'label' => [
                    'for' => 't',
                    'text' => 'Tipologia',
                ],
            ],
            [
                'prefix' => '<div class="form-group col-lg-4 mb-5">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 's',
                    'type' => 'date',
                    'id' => 'start_date',
                    'value' => '{start_date}',
                ],
                'label' => [
                    'for' => 's',
                    'text' => 'Data emissione - dal',
                    'class' => 'active'
                ],
            ],
            [
                'prefix' => '<div class="form-group col-lg-4 mb-5">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'e',
                    'type' => 'date',
                    'id' => 'end_date',
                    'value' => '{end_date}',
                ],
                'label' => [
                    'for' => 'e',
                    'text' => 'Data emissione - al',
                    'class' => 'active'
                ],
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-4 mb-5">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{office}',
                'name' => 'u',
                'extra' => 'id="office"',
                'label' => [
                    'for' => 'o',
                    'text' => 'Uffici',
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
            'norm' => Input::get('norm', true),
            't' => Input::get('t', true),
            'n' => Input::get('n', true),
            's' => Input::get('s', true),
            'e' => Input::get('e', true),
            'u' => Input::get('u', true),
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value) {
            if (!empty($value)) {
                $countFilterFields += 1;
            }
        }

        // Per il filtro
        $normTypes = [null => 'Qualunque'] + config('normativeTypologies', [], 'app');

        // Filtro per strutture
        $structureIds = NormativesModel::select(['object_structures_id'])
            ->whereIn('normative_topic', [1,4])
            ->join('rel_normatives_structures as rel', 'rel.object_normatives_id', '=', 'object_normatives.id')
            ->groupBy('rel.object_structures_id')
            ->get()
            ->toArray();
        $structures = [null => 'Seleziona una struttura'] + optional(StructuresModel::whereIn('id', $structureIds)
                ->where(function ($query) {
                })
                ->orderBy('structure_name', 'ASC')
                ->pluck('structure_name', 'id'))
                ->toArray();

        $formBuilder = new FormBuilder([
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'normative' => htmlEscape(Input::get('norm', true)),
            'typology' => FormBuilder::parseOptions($normTypes, htmlEscape(Input::get('t', true))),
            'office' => FormBuilder::parseOptions($structures, htmlEscape(Input::get('u', true))),
            'number' => htmlEscape(Input::get('n', true)),
            'start_date' => htmlEscape(Input::get('s', true)),
            'end_date' => htmlEscape(Input::get('e', true)),
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['finalSectionId'] = 24;
        $data['paragraphs'] = $contents;
        $data['instance'] = $currentPage;
        $data['allowSearch'] = true;
        //Abilita la paginazione AJAX della tabella dei risultati (vedi renderFrontFragment)
        $data['ajaxPagination'] = true;
        $data['noRequiredPublication'] = !empty($currentPage['no_required']);
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


        renderFront(config('vfo', null, 'app') . '/normatives/normatives', $data, 'frontend');
    }

    /**
     * Metodo che crea la tabella con i dati delle normative pubblicate nel front-office.
     *
     * @param null $normatives Dati paginati da inserire nella tabella
     * @return Table|null
     * @throws Exception
     */
    private function createTableRows($normatives = null): ?Table
    {
        $currentPageId = (int)uri()->segment(2, 0);
        $table = null;

        if (!empty($normatives['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();
            $table->set_heading('Normativa');

            // Creo le righe della tabella settando i dati da mostrare
            foreach ($normatives['data'] as $normative) {

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $normative['id'] . '/' . urlTitle($normative['name'])) . '" data-id="' . $normative['id'] . ' ">' . escapeXss($normative['name']) . '</a>'
                );
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
        $element = NormativesModel::where('id', $elementId)
            ->with('structures:id,structure_name');


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

        $actTypes = [null => ''] + config('normativeTypologies', [], 'app');

        $normativeTopics = ['' => '', 1 => 'Organizzazione dell\'Ente',
            2 => 'Sovvenzioni e contributi', 3 => 'Altro', 4=> 'Attività'];
        //Argomenti della normativa

        // aggiungi al Breadcrumbs la pagina del dettaglio
        $data['concatBreadcrumb'] = true;
        $data['bread'] = array();
        $data['bread'][] = array('name' => $element['name'], 'link' => '/');
        $data['instance'] = $element;
        $data['type'] = $actTypes[$element['act_type']];
        $data['topic'] = !empty($normativeTopics[$element['normative_topic']]) ? $normativeTopics[$element['normative_topic']] : '';
        $data['currentPageId'] = $currentPageId;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['editRecord'] = true;

        $label = 'normatives';
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
            'label',
            'file_ext',
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

        renderFront(config('vfo', null, 'app') . '/normatives/details', $data, 'frontend');

    }
}
