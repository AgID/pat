<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

/** @noinspection PhpUnused */


namespace Http\Web\Front;

use Exception;
use Helpers\FormBuilder;
use Helpers\Table;
use Helpers\Utility\AttachmentArchive;
use Model\AbsenceRatesModel;
use Model\SectionsFoModel;
use System\Input;
use System\Validator;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller pagina front-end Tassi di assenza
 */
class AbsenceRatesFrontController extends BaseFrontController
{
    /**
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        helper('url');

    }

    /**
     * Metodo chiamato per la pagina "Tassi di assenza",
     * nella sezione del Personale
     * ID sezione 66
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
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Validatore dei filtri di ricerca
        $validator = new Validator();
        if (
            //Input::get('structures') != null ||
            Input::get('start') != null ||
            Input::get('structure_name') != null ||
            Input::get('months') != null
        ) {
            $validator->verifyToken();
        }
        //$validator->label('struttura')->value(Input::get('structures'))->isInt()->end();
        $validator->label('Nome struttura')->value(Input::get('structure_name'))->maxLength(30)->end();
        $validator->label('anno')->value(Input::get('start'))->isDate('Y')->end();
        $validator->label('mese')->value(Input::get('months'))->in(
            '01,02,03,04,05,06,07,08,09,10,11,12,13,14,15,16'
        )->end();

        if (!$validator->isSuccess()) {
            echo showError('Errore nella validazione dei dati', $validator->getErrorsHtml(), $validator->getErrorStatusCode());
            die();
        }

        // Recupero i procedimenti(con campo Pubblica automaticamente i dati sul monitoraggio = 1) da mostrare
        $absenceRates = AbsenceRatesModel::filter(
            null,
            (int)Input::get('start', true),
            Input::get('months', true),
            Input::get('structure_name', true),
        )
            ->with('structure:id,structure_name')
            ->orderBy('year', 'DESC')
            ->orderBy('month', 'DESC')
            ->orderBy('structure_name')
            ->paginate(20, ['id', 'year', 'month', 'presence_percentage', 'total_absence', 'object_structures_id', 'structure_name'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            //->appends(Input::get(['structures','structure_name', 'start', 'months', 'sec_token']))
            ->appends(Input::get(['structure_name', 'start', 'months', 'sec_token']))
            ->setPath(currentUrl());

        $absenceRates = !empty($absenceRates) ? $absenceRates->toArray() : [];

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($absenceRates);

        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $absenceRates;

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
                'prefix' => '<div class="form-group col-lg-6 mb-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'structure_name',
                    'id' => 'structure_name',

                    'value' => '{structure_name}'
                ],
                'label' => [
                    'for' => 'structure_name',
                    'text' => 'Nome struttura',
                ],
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-3 mb-4">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{years}',
                'name' => 'start',
                'extra' => 'id="start"',
                'label' => [
                    'for' => 'start',
                    'text' => 'Anno',
                    'class' => 'active'
                ],
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-3">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{months}',
                'name' => 'months',
                'extra' => 'id="months"',
                'label' => [
                    'for' => 'months',
                    'text' => 'Mese',
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

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle date di creazione e di aggiornamento
        if (!empty($absenceRates['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(AbsenceRatesModel::select(['created_at'])
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(AbsenceRatesModel::select(['updated_at'])
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        $formFilterFields = [
            //'structures' => Input::get('structures'),
            'start' => Input::get('start'),
            'months' => Input::get('months'),
            'structure_name' => Input::get('structure_name', true),
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
        $data['paragraphs'] = $contents;
        $data['instance'] = $currentPage;
        $data['allowSearch'] = true;
        //Abilita la paginazione AJAX della tabella dei risultati (vedi renderFrontFragment)
        $data['ajaxPagination'] = true;
        $data['noRequiredPublication'] = !empty($currentPage['no_required']);
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['normative_references'] = $currentPage['normatives'] ?? null;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['searchFieldsOpenData'] = $formFilterFields;
        $data['filterUsed'] = $countFilterFields;
        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        // Filtro per strutture
//        $structureIds = AbsenceRatesModel::select(['object_structures_id'])
//            ->groupBy('object_structures_id')
//            ->get()
//            ->toArray();
//        $structures = [null => 'Seleziona una struttura'] + StructuresModel::whereIn('id', $structureIds)
//                ->where(function ($query) {
//                    $query->where('archived', 0)
//                        ->orWhereNull('archived');
//                })
//                ->orderBy('structure_name', 'ASC')
//                ->pluck('structure_name', 'id')
//                ->toArray();

        $resultYears = AbsenceRatesModel::select('year')
            ->orderBy('year', 'ASC')
            ->first();

        $resultYears = !empty($resultYears) ? $resultYears->toArray() : ['year' => date('Y')];
        $years = [null => 'Seleziona un anno'];
        for ($i = (int)$resultYears['year']; $i <= (int)date('Y'); $i++) {
            $years[$i] = $i;
        }

        $months = [null => 'Seleziona'] + config('absenceRatesPeriod', null, 'app');

        $formBuilder = new FormBuilder([
            'current_url' => currentUrl(),
            //'structures' => FormBuilder::parseOptions($structures, htmlEscape(Input::get('structures', true))),
            'years' => FormBuilder::parseOptions($years, htmlEscape(Input::get('start', true))),
            'months' => FormBuilder::parseOptions($months, htmlEscape(Input::get('months', true))),
            'structure_name' => htmlEscape(Input::get('structure_name', true)),
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();


        //Inclusione di eventuali librerie esterne
        $data = $data ?? null;
        $data = $data ?? null;
        renderFront(config('vfo', null, 'app') . '/absence_rates/absence_rates', $data, 'frontend');
    }

    /**
     * Metodo che crea la tabella con i dati dei risultati della ricerca da mostrare
     *
     * @param $absenceRates {dati da inserire nella tabella}
     * @return Table|null
     * @throws Exception
     */
    private function createTableRows($absenceRates = null): ?Table
    {

        $currentPageId = (int)uri()->segment(2, 0);

        $table = null;

        if (!empty($absenceRates['data'])) {

            $periods = config('absenceRatesPeriod', null, 'app');

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();
            $table->set_heading('Struttura', 'Anno', 'Periodo', '% Presenza', '% Assenza totale');

            // Creo le righe della tabella settando i dati da mostrare
            foreach ($absenceRates['data'] as $absenceRate) {

                $months = !empty($absenceRate['month']) ? explode(',', $absenceRate['month']) : null;

                $tmpMonth = [];
                foreach ($months as $month) {
                    $tmpMonth [] = $periods[$month] ?? $periods['0' . $month];
                }

                $months = implode(', ', $tmpMonth);

                $name = !empty($absenceRate['structure_name']) ? escapeXss($absenceRate['structure_name']) : 'tasso di assenza';

                $tmpName = !empty($absenceRate['structure']) ? escapeXss($absenceRate['structure']['structure_name']) : $name;

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $absenceRate['id'] . '/' . urlTitle($tmpName)) . '"' . ' data-id="' . $absenceRate['id'] . ' ">' . $tmpName . '</a>',
                    escapeXss($absenceRate['year']),
                    $months,
                    escapeXss($absenceRate['presence_percentage']),
                    escapeXss($absenceRate['total_absence'])
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
        $element = AbsenceRatesModel::where('id', $elementId)
            ->with('structure:id,structure_name');

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
        $tmpName = $element['structure_name'] ?? 'Tasso di assenza';
        $data['pageName'] = $element['structure']['structure_name'] ?? $tmpName;
        $data['menuPages'] = $sectionFO;

        // aggiungi al Breadcrumbs la pagina del dettaglio
        $data['concatBreadcrumb'] = true;
        $data['bread'] = array();
        $data['bread'][] = array('name' => $element['structure']['structure_name'] ?? $tmpName, 'link' => '/');
        $data['instance'] = $element;
        $data['currentPageId'] = $currentPageId;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['editRecord'] = true;

        $label = 'absence_rates';
        $elementId = $element['id'];
        $selectFields = [
            'id',
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

        renderFront(config('vfo', null, 'app') . '/absence_rates/details', $data, 'frontend');
    }
}
