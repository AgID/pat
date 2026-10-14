<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

 /** @noinspection PhpUnused */



namespace Http\Web\Front;

use Exception;
use Helpers\FormBuilder;
use Helpers\S;
use Helpers\Table;
use Helpers\Utility\AttachmentArchive;
use Model\LeaseCanonsModel;
use Model\RealEstateAssetModel;
use Model\SectionsFoModel;
use System\Input;
use System\Validator;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller pagina front-end Canoni di Locazione
 */
class CanonFrontController extends BaseFrontController
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
     * Metodo chiamato per la pagina "Canoni di locazione o affitto percepiti"
     * ID sezione 136
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexPerceived(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Validazione filtri di ricerca.
        $validator = new Validator();

        if (Input::get('i') != null ||
            Input::get('am') != null ||
            Input::get('start') != null ||
            Input::get('end') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Immobile')->value(Input::get('i'))->isInt()->end();
        $validator->label('Importo')->value(Input::get('am'))
            // ->regex('/^[€-]{0,2}\d{1,3}(?:.?\d{3})*(?:\,\d{0,2})?$/') // Regex con il simbolo €
            ->regex('/^\d{1,3}(?:.?\d{3})*(?:,\d{0,2})?$/')  // Regex SENZA il simbolo €
            ->end();
        $validator->label('Data inizio')->value(Input::get('start'))->isDate('Y-m-d')->end();
        $validator->label('Data fine')->value(Input::get('end'))->isDate('Y-m-d')->end();

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

        // Recupero i canoni di locazione percepiti da mostrare
        $canons = $this->getDataResults(2, $data);

        $canons = !empty($canons) ? $canons->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($canons);

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
                'prefix' => '<div class="select-wrapper col-lg-6" style="margin-bottom: 1rem;">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{i}',
                'name' => 'i',
                'extra' => 'id="i"',
                'label' => [
                    'for' => 'i',
                    'text' => 'Immobile',
                ],
            ],
            [
                'prefix' => '<div class="form-group col-lg-6" style="margin-bottom: 1rem;">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'am',
                    'id' => 'am',
                    'class' => 'a-num-class',
                    
                    'value' => '{am}'
                ],
                'label' => [
                    'for' => 'am',
                    'text' => 'Importo',
                ],
            ],


            [
                'prefix' => '<div class="col-lg-6">',
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
                    'text' => 'Data inizio',
                    'class' => 'active'
                ]
            ],
            [
                'prefix' => '<div class="col-lg-6">',
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
                    'text' => 'Data fine',
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
            'i' => Input::get('i', true),
            'am' => Input::get('am'),
            'start' => Input::get('start', true),
            'end' => Input::get('end', true)
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value) {
            if (!empty($value)) {
                $countFilterFields += 1;
            }
        }

        // Responsabili per il filtro
        $assetsIds = LeaseCanonsModel::select(['rel.object_real_estate_asset_id'])
            ->where('canon_type', 2)
            ->join('rel_lease_canons_real_estate_asset as rel', 'rel.object_lease_canons_id', '=', 'object_lease_canons.id')
            ->groupBy('rel.object_real_estate_asset_id')
            ->get()
            ->toArray();
        $assets = [null => 'Seleziona un immobile'] +
            optional(RealEstateAssetModel::select(['id', 'name'])
                ->where(function ($query) {
                })
                ->whereIn('id', $assetsIds)
                ->orderBy('name', 'ASC')
                ->pluck('name', 'id'))
                ->toArray();

        $formBuilder = new FormBuilder([
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'am' => htmlEscape(Input::get('am', true)),
            'i' => FormBuilder::parseOptions($assets, htmlEscape(Input::get('i', true))),
            'start' => htmlEscape(Input::get('start', true)),
            'end' => htmlEscape(Input::get('end', true)),
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['table'] = !empty($table) ? $table->generate() : null;
        $data['instances'] = $canons;
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
        renderFront(config('vfo', null, 'app') . '/lease_canons/lease_canon', $data, 'frontend');
    }

    /**
     * Funzione che ritorna i dati da mostrare nella tabella
     *
     * @param null  $type {la tipologia per cui filtrare i dati}
     * @param array $data Array da passare alla visa
     * @return mixed
     * @throws Exception
     */
    private function getDataResults($type = null, array &$data = []): mixed
    {

        //Modifico il formato dell'importo per renderlo conforme a quello del db
        if (Input::get('am') != null) {
            $formattedAmount = str_replace('.', '', Input::get('am'));
            $formattedAmount = str_replace(',', '.', $formattedAmount);
        } else {
            $formattedAmount = null;
        }

        // Recupero i canoni di locazione percepiti da mostrare
        $canons = LeaseCanonsModel::where('canon_type', $type)
            ->with('properties:id,name')
            ->filter(
                (int)(Input::get('i', true)),
                $formattedAmount,
                Input::get('start', true),
                Input::get('end', true)
            )
            ->paginate(20, ['object_lease_canons.id', 'amount', 'start_date', 'end_date'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get(['i', 'start', 'end', 'am', 'sec_token']))
            ->setPath(currentUrl());

        // Richiesta del solo frammento (paginazione AJAX): essendo getDataResults() chiamato da
        // tutte le pagine di questo controller prima del lavoro pesante (form filtro, render
        // completo), un solo controllo qui copre tutte quelle pagine.
        if (isFrontFragmentRequest()) {
            $resultsArray = $canons->toArray();
            $table = $this->createTableRows($resultsArray);
            renderFrontFragment(['table' => !empty($table) ? $table->generate() : null, 'instances' => $resultsArray]);
            exit;
        }

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($canons) && !empty($canons->toArray()['data'])) {

            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(LeaseCanonsModel::select(['created_at'])
                ->where('canon_type', $type)
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(LeaseCanonsModel::select(['updated_at'])
                ->where('canon_type', $type)
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        return $canons;
    }

    /**
     * Metodo che crea la tabella con i dati dei risultati della ricerca da mostrare
     *
     * @param $canons {dati da inserire nella tabella}
     * @return Table|null
     * @throws Exception
     */
    private function createTableRows($canons = null): ?Table
    {
        $table = null;

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        if (!empty($canons['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();
            $table->set_heading('Importo', 'Immobile', 'Inizio', 'Fine', '');

            // Creo le righe della tabella settando i dati da mostrare
            foreach ($canons['data'] as $canon) {

                // Creo link per gli immobili
                $properties = $canon['properties'];

                $filter = function ($property) {
                    return '<a href="' . siteUrl('page/133/details/' . $property['id'] . '/' . urlTitle($property['name'])) . '">' . escapeXss($property['name']) . '</a>';
                };
                $linkedProperties = array_map($filter, $properties);

                $table->add_row(
                    '&euro; ' . escapeXss(S::currency($canon['amount'], 2, ',', '.')),
                    implode(', ', $linkedProperties),
                    !empty($canon['start_date']) ? date('d/m/Y', strtotime($canon['start_date'])) : null,
                    !empty($canon['end_date']) ? date('d/m/Y', strtotime($canon['end_date'])) : null,
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $canon['id'] . '/canoni-di-locazione') . '" data-id="' . $canon['id'] . ' "> Dettagli</a>'
                );
            }
        }

        return $table;
    }

    /**
     * Metodo chiamato per la pagina "Canoni di locazione o affitto versati"
     * Id sezione 137
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexPaid(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Validazione filtri di ricerca.
        $validator = new Validator();

        if (Input::get('i') != null ||
            Input::get('am') != null ||
            Input::get('start') != null ||
            Input::get('end') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Immobile')->value(Input::get('i'))->isInt()->end();
        $validator->label('Importo')->value(Input::get('am'))
            // ->regex('/^[€-]{0,2}\d{1,3}(?:.?\d{3})*(?:\,\d{0,2})?$/') // Regex con il simbolo €
            ->regex('/^\d{1,3}(?:.?\d{3})*(?:,\d{0,2})?$/')  // Regex SENZA con il simbolo €
            ->end();
        $validator->label('Data inizio')->value(Input::get('start'))->isDate('Y-m-d')->end();
        $validator->label('Data fine')->value(Input::get('end'))->isDate('Y-m-d')->end();

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

        // Recupero i canoni di locazione percepiti da mostrare
        $canons = $this->getDataResults(1, $data);

        $canons = !empty($canons) ? $canons->toArray() : [];

        // Recupero il contenuto della pagina e i richiami dei vari paragrafi
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($canons);

        // Form per i filtri di ricerca
        $form = json_encode([
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
                'prefix' => '<div class="select-wrapper col-lg-6" style="margin-bottom: 1rem;">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{i}',
                'name' => 'i',
                'extra' => 'id="i"',
                'label' => [
                    'for' => 'i',
                    'text' => 'Immobile',
                ],
            ],
            [
                'prefix' => '<div class="form-group col-lg-6" style="margin-bottom: 1rem;">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'am',
                    'id' => 'am',
                    
                    'value' => '{am}'
                ],
                'label' => [
                    'for' => 'am',
                    'text' => 'Importo',
                ],
            ],


            [
                'prefix' => '<div class="col-lg-6">',
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
                    'text' => 'Data inizio',
                    'class' => 'active'
                ]
            ],
            [
                'prefix' => '<div class="col-lg-6">',
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
                    'text' => 'Data fine',
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
            'i' => Input::get('i', true),
            'am' => Input::get('am'),
            'start' => Input::get('start', true),
            'end' => Input::get('end', true)
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value) {
            if (!empty($value)) {
                $countFilterFields += 1;
            }
        }

        // Immobili per il filtro
        $assetsIds = LeaseCanonsModel::select(['rel.object_real_estate_asset_id'])
            ->where('canon_type', 2)
            ->join('rel_lease_canons_real_estate_asset as rel', 'rel.object_lease_canons_id', '=', 'object_lease_canons.id')
            ->groupBy('rel.object_real_estate_asset_id')
            ->get()
            ->toArray();
        $assets = [null => 'Seleziona un immobile'] +
            optional(RealEstateAssetModel::select(['id', 'name'])
                ->where(function ($query) {
                })
                //->whereIn('id', $assetsIds)
                ->orderBy('name', 'ASC')
                ->pluck('name', 'id'))
                ->toArray();

        $formBuilder = new FormBuilder([
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'am' => htmlEscape(Input::get('am', true)),
            'i' => FormBuilder::parseOptions($assets, htmlEscape(Input::get('i', true))),
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
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $canons;
        $data['instance'] = $currentPage;
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

        renderFront(config('vfo', null, 'app') . '/lease_canons/lease_canon', $data, 'frontend');
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
        $element = LeaseCanonsModel::where('id', $elementId)
            ->with('properties:id,name')
            ->with(['structure' => function ($query) {
                $query->select(['id', 'structure_name']);
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

        $pageName = ($element['canon_type'] == 1)
            ? 'Canoni di locazione o di affitto versati'
            : 'Canoni di locazione o di affitto percepiti';

        // Dati passati alla vista
        $data['pageName'] = $pageName;
        $data['menuPages'] = $sectionFO;

        // aggiungi al Breadcrumbs la pagina del dettaglio
        $data['concatBreadcrumb'] = true;
        $data['bread'] = array();
        $data['bread'][] = array('name' => $pageName, 'link' => '/');
        $data['instance'] = $element;
        $data['currentPageId'] = $currentPageId;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['editRecord'] = true;

        $label = 'lease_canons';
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

        //Inclusione di eventuali librerie esterne
        $data = $data ?? null;

        renderFront(config('vfo', null, 'app') . '/lease_canons/details', $data, 'frontend');

    }
}
