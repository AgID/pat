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
use Helpers\Utility\Meta;
use Model\ContestsActsModel;
use Model\ContraentChoice;
use Model\SectionsFoModel;
use Model\StructuresModel;
use System\Arr;
use System\Input;
use System\Validator;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller pagina front-end Bandi di gara
 */
class ContestsActsFrontController extends BaseFrontController
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
     * Metodo per la pagina di snodo dei bandi di gara e contratti
     * Id sezione 581
     *
     * @return void
     * @throws Exception
     */
    public function pivot(): void
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
            ]);


        $currentPage = $currentPage->labelfilter((int)$institutionTypeId)
            ->first();

        $currentPage = !empty($currentPage) ? $currentPage->toArray() : [];

        // Recupero il contenuto della pagina e i richiami dei vari paragrafi
        $contents = getPageContents($currentPageId);

        // Genero le voci per il menu
        $sectionFO = getRightOrBottomMenu($currentPageId, $currentPage['parent_id']);

        // Dati passati alla vista
        $data['paragraphs'] = $contents;
        $data['instance'] = $currentPage;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = (!empty($sectionFO)) ? $sectionFO : [];
        $data['currentPageId'] = $currentPageId;


        renderFront(config('vfo', null, 'app') . '/contests_acts/index', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Pagamenti di Consulenti e collaboratori",
     * nella sezione dei Pagamenti dell'amministrazione -> DATI SUI PAGAMENTI
     * ID sezione 157
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexPaymentsContestsActs(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        $tgCondition = '';

        // Recupero le informazioni della pagina corrente
        $currentPage = getCurrentPageInfo($data, $tgCondition);

        // Recupero le voci per costruire il menu dei contenuti
        $sectionFO = getRightOrBottomMenu($currentPageId, $currentPage['parent_id']);

        // Validazione dei campi d'input per la ricerca
        $validator = new Validator();
        if (Input::get('structures') != null ||
            Input::get('object') != null ||
            Input::get('contraent') != null ||
            Input::get('cig') != null ||
            Input::get('start_p_date') != null ||
            Input::get('end_p_date') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Struttura')->value(Input::get('structures'))->isInt()->end();
        $validator->label('Oggetto')->value(Input::get('object'))->maxLength(30)->end();
        $validator->label('Scelta del contraente')->value(Input::get('contraent'))->isInt()->end();
        $validator->label('Cig')->value(Input::get('cig'))->maxLength(12)->end();
        $validator->label('Data di pubblicazione dal')->value(Input::get('start_p_date'))->isDate('Y-m-d')->end();
        $validator->label('Data di pubblicazione al')->value(Input::get('end_p_date'))->isDate('Y-m-d')->end();

        if (!$validator->isSuccess()) {
            echo showError('Errore nella validazione dei dati', $validator->getErrorsHtml(), $validator->getErrorStatusCode());
            die();
        }

        $liquidationDate = "2025-07-01 00:00:00";

        // Recupero le liquidazioni dei bandi da mostrare in base al criterio di pubblicazione della pagina
        $contestActs = ContestsActsModel::where('object_contests_acts.typology', '=', 'liquidation')
            ->where(function ($query) {
                $query->where('object_contests_acts.amount_liquidated', '!=', '')
                    ->orWhereNotNull('object_contests_acts.amount_liquidated');
            })
            ->where('object_contests_acts.activation_date', '<=', $liquidationDate)
            ->with(['relative_procedure' => function ($query) {
                $query->select(['id', 'relative_procedure_id', 'object', 'cig']);
                $query->with('awardees:id,name');
            }]);

        //Eventuale condizione aggiuntiva
        if (!empty($tgCondition)) {
            $contestActs = $contestActs->where($tgCondition);
        }

        $contestActs = $contestActs->filter(
            (int)Input::get('structures', true),
            (Input::get('object', true)),
            (int)Input::get('contraent', true),
            (Input::get('cig', true)),
            Input::get('start_p_date', true),
            Input::get('end_p_date', true),
        )
            ->groupBy('object_contests_acts.id')
            ->orderBy('object_contests_acts.activation_date', 'DESC')
            ->paginate(20, ['object_contests_acts.id', 'object_contests_acts.amount_liquidated', 'object_contests_acts.object', 'object_contests_acts.activation_date',
                'object_contests_acts.relative_procedure_id'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->setPath(currentUrl())
            ->appends(Input::get(['structures', 'object', 'contraent', 'cig', 'start_p_date', 'end_p_date', 'sec_token'], true));

        $contestActs = !empty($contestActs) ? $contestActs->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($contestActs['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ContestsActsModel::select(['created_at'])
                ->where('object_contests_acts.typology', '=', 'liquidation')
                ->where(function ($query) {
                    $query->where('object_contests_acts.amount_liquidated', '!=', '')
                        ->orWhereNotNull('object_contests_acts.amount_liquidated');
                })
                ->where('object_contests_acts.activation_date', '<=', $liquidationDate)
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ContestsActsModel::select(['updated_at'])
                ->where('object_contests_acts.typology', '=', 'liquidation')
                ->where(function ($query) {
                    $query->where('object_contests_acts.amount_liquidated', '!=', '')
                        ->orWhereNotNull('object_contests_acts.amount_liquidated');
                })
                ->where('object_contests_acts.activation_date', '<=', $liquidationDate)
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Filtro per strutture
        $flagArchived = false;

        $query = StructuresModel::query();

        if (!$flagArchived) {
            $query->where(function ($query) {
            });
        }
        $structures = [null => 'Seleziona una struttura'] + $query
                ->orderBy('structure_name', 'ASC')
                ->pluck('structure_name', 'id')
                ->toArray();

        // Filtro per scelta del contraente
        $contraentChoices = [null => 'Qualunque'] +
            optional(ContraentChoice::orderBy('name', 'ASC')
                ->pluck('name', 'id'))
                ->toArray();

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
                'prefix' => '<div class="select-wrapper col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{structures}',
                'name' => 'structures',
                'extra' => 'id="structures"',
                'label' => [
                    'for' => 's',
                    'text' => 'Struttura da cercare',
                ]
            ],
            [
                'prefix' => '<div class="form-group col-lg-4">',
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
                'prefix' => '<div class="select-wrapper col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{contraent}',
                'name' => 'contraent',
                'extra' => 'id="contraent"',
                'label' => [
                    'for' => 'c',
                    'text' => 'Scelta del contraente',
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
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'cig',
                    'value' => '{cig}',
                    'id' => 'cig',
                ],
                'label' => [
                    'for' => 'cig',
                    'text' => 'Cig',
                ]
            ],
            [
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'start_p_date',
                    'value' => '{start_p_date}',
                    'type' => 'date',
                    'id' => 'start_p_date',
                ],
                'label' => [
                    'for' => 'start_p_date',
                    'text' => 'Data di scadenza - dal',
                    'class' => 'active'
                ]
            ],
            [
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'end_p_date',
                    'value' => '{end_p_date}',
                    'type' => 'date',
                    'id' => 'end_p_date',
                ],
                'label' => [
                    'for' => 'end_p_date',
                    'text' => 'Data di scadenza - al',
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
            'structures' => Input::get('structures'),
            'object' => Input::get('object'),
            'contraent' => Input::get('contraent'),
            'cig' => Input::get('cig'),
            'start_p_date' => Input::get('start_p_date'),
            'end_p_date' => Input::get('end_p_date')
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
            'structures' => FormBuilder::parseOptions($structures, htmlEscape(Input::get('structures', true))),
            'object' => htmlEscape(Input::get('object', true)),
            'contraent' => FormBuilder::parseOptions($contraentChoices, htmlEscape(Input::get('contraent', true))),
            'cig' => htmlEscape(Input::get('cig', true)),
            'start_p_date' => htmlEscape(Input::get('start_p_date', true)),
            'end_p_date' => htmlEscape(Input::get('end_p_date', true)),
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        $table = null;

        if (!empty($contestActs['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();

            $table->set_heading('Oggetto', 'Importo', 'Data di pubblicazione', 'Beneficiario');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($contestActs['data'] as $contestAct) {

                $awardees = !empty($contestAct['relative_procedure']['awardees']) ? Arr::pluck($contestAct['relative_procedure']['awardees'], 'name') : null;

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $contestAct['id'] . '/' . urlTitle($contestAct['object'])) . '" data-id="' . $contestAct['id'] . ' ">' . escapeXss($contestAct['object']) . '</a>',
                    !empty($contestAct['amount_liquidated']) ? '&euro; ' . number_format((float)$contestAct['amount_liquidated'], 2, ',', '.') : null,
                    !empty($contestAct['activation_date']) ? date('d/m/Y', strtotime($contestAct['activation_date'])) : null,
                    !empty($awardees) ? implode('</br>', escapeXss($awardees)) : null,
                );
            }
        }

        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $contestActs;

        // Richiesta del solo frammento (paginazione AJAX): tabella e paginazione sono pronte,
        // tutto il resto serve solo alla pagina completa e viene saltato.
        if (renderFrontFragment($data)) {

            return;

        }

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
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

        renderFront(config('vfo', null, 'app') . '/contests_acts/contests_acts', $data, 'frontend');
    }


    /**
     * Metodo chiamato per la pagina "Dati previsti dall'articolo 1, comma 32, della legge 6 novembre 2012, n. 190. Informazioni sulle singole procedure",
     * nella sezione dei Bandi di gara e contratti
     * ID sezione 110
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexIndividualProceduresTabularFormat(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Validazione dei campi d'input per la ricerca
        $validator = new Validator();
        if (Input::get('str') != null ||
            Input::get('obj') != null ||
            Input::get('c') != null ||
            Input::get('_cig') != null ||
            Input::get('s') != null ||
            Input::get('e') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Struttura')->value(Input::get('str'))->isInt()->end();
        $validator->label('Oggetto')->value(Input::get('obj'))->maxLength(30)->end();
        $validator->label('Scelta del contraente')->value(Input::get('c'))->isInt()->end();
        $validator->label('Cig')->value(Input::get('_cig'))->maxLength(12)->end();
        $validator->label('Data di pubblicazione dal')->value(Input::get('s'))->isDate('Y-m-d')->end();
        $validator->label('Data di pubblicazione al')->value(Input::get('e'))->isDate('Y-m-d')->end();

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

        // Recupero i bandi da mostrare in base al criterio di pubblicazione della pagina
        $contestActs = ContestsActsModel::where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
            ->where(
                function ($query) {
                    $query->orWhere('object_contests_acts.typology', '=', 'notice') //bando
                    ->orWhere('object_contests_acts.typology', '=', 'foster') // affidamento
                    ->orWhere('object_contests_acts.typology', '=', 'result') //esito
                    ->orWhere('object_contests_acts.typology', '=', 'delibere e determine a contrarre'); // delibera(per retro compatibilità)
                }
            )
            ->contestactsfilter(
                (int)Input::get('str', true),
                (Input::get('obj', true)),
                Input::get('c', true),
                (Input::get('_cig', true)),
                Input::get('s', true),
                Input::get('e', true),
            )
            ->with(['relative_lots' => function ($query) {
                $query->select(['relative_notice_id', 'id', 'cig']);
            }])
            ->with(['relative_notice' => function ($query) {
                $query->select(['relative_notice_id', 'id', 'cig']);
            }])
            ->orderBy('activation_date', 'DESC')
            ->paginate(20, ['object_contests_acts.id', 'object_contests_acts.object', 'object_contests_acts.cig', 'object_contests_acts.typology',
                'object_contests_acts.activation_date', 'object_contests_acts.relative_notice_id'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get(['str', 'obj', 'c', '_cig', 's', 'e', 'sec_token'], true))
            ->setPath(currentUrl());

        $contestActs = !empty($contestActs) ? $contestActs->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($contestActs['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ContestsActsModel::select(['created_at'])
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->where(
                    function ($query) {
                        $query->orWhere('object_contests_acts.typology', '=', 'notice') //bando
                        ->orWhere('object_contests_acts.typology', '=', 'foster') // affidamento
                        ->orWhere('object_contests_acts.typology', '=', 'result') //esito
                        ->orWhere('object_contests_acts.typology', '=', 'delibere e determine a contrarre'); // delibera(per retro compatibilità)
                    }
                )
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ContestsActsModel::select(['updated_at'])
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->where(
                    function ($query) {
                        $query->orWhere('object_contests_acts.typology', '=', 'notice') //bando
                        ->orWhere('object_contests_acts.typology', '=', 'foster') // affidamento
                        ->orWhere('object_contests_acts.typology', '=', 'result') //esito
                        ->orWhere('object_contests_acts.typology', '=', 'delibere e determine a contrarre'); // delibera(per retro compatibilità)
                    }
                )
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Filtro per strutture
        $flagArchived = false;

        $query = StructuresModel::query();

        if (!$flagArchived) {
            $query->where(function ($query) {
            });
        }
        $structures = [null => 'Seleziona una struttura'] + $query
                ->orderBy('structure_name', 'ASC')
                ->pluck('structure_name', 'id')
                ->toArray();

        // Filtro per scelta del contraente
        $contraentChoices = [null => 'Qualunque'] +
            optional(ContraentChoice::orderBy('name', 'ASC')
                ->pluck('name', 'id'))
                ->toArray();

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
                'prefix' => '<div class="select-wrapper col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{structures}',
                'name' => 'str',
                'extra' => 'id="str"',
                'label' => [
                    'for' => 'str',
                    'text' => 'Struttura da cercare',
                ]
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
                'options' => '{contraent}',
                'name' => 'c',
                'extra' => 'id="c"',
                'label' => [
                    'for' => 'c',
                    'text' => 'Scelta del contraente',
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
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => '_cig',
                    'value' => '{cig}',
                    'id' => '_cig',
                ],
                'label' => [
                    'for' => '_cig',
                    'text' => 'Cig',
                ]
            ],
            [
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 's',
                    'value' => '{start_p_date}',
                    'type' => 'date',
                    'id' => 's',
                ],
                'label' => [
                    'for' => 's',
                    'text' => 'Data di scadenza - dal',
                    'class' => 'active'
                ]
            ],
            [
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'e',
                    'value' => '{end_p_date}',
                    'type' => 'date',
                    'id' => 'e',
                ],
                'label' => [
                    'for' => 'e',
                    'text' => 'Data di scadenza - al',
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
            'str' => Input::get('str'),
            'obj' => Input::get('obj'),
            'c' => Input::get('c'),
            '_cig' => Input::get('_cig'),
            's' => Input::get('s'),
            'e' => Input::get('e')
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
            'structures' => FormBuilder::parseOptions($structures, htmlEscape(Input::get('str', true))),
            'object' => htmlEscape(Input::get('obj', true)),
            'contraent' => FormBuilder::parseOptions($contraentChoices, htmlEscape(Input::get('c', true))),
            'cig' => htmlEscape(Input::get('_cig', true)),
            'start_p_date' => htmlEscape(Input::get('s', true)),
            'end_p_date' => htmlEscape(Input::get('e', true)),
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        // Creo le righe della tabella settando i dati
        $table = null;

        if (!empty($contestActs['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();

            $table->set_heading('Oggetto', 'CIG', 'Data di pubblicazione');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($contestActs['data'] as $contestAct) {

                if ($contestAct['typology'] == 'notice') {
                    $tmpCig = !empty($contestAct['relative_lots'])
                        ? escapeXss(implode(', ', Arr::pluck($contestAct['relative_lots'], 'cig')))
                        : escapeXss($contestAct['cig']);
                } elseif ($contestAct['typology'] == 'result') {
                    $tmpCig = !empty($contestAct['relative_notice'])
                        ? escapeXss($contestAct['relative_notice']['cig'])
                        : null;
                } else {
                    $tmpCig = !empty($contestAct['cig'])
                        ? escapeXss($contestAct['cig'])
                        : null;
                }

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $contestAct['id'] . '/' . urlTitle($contestAct['object'])) . '" data-id="' . $contestAct['id'] . ' ">' . escapeXss($contestAct['object']) . '</a>',
                    $tmpCig,
                    !empty($contestAct['activation_date']) ? date('d/m/Y', strtotime($contestAct['activation_date'])) : null
                );
            }
        }

        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $contestActs;

        // Richiesta del solo frammento (paginazione AJAX): tabella e paginazione sono pronte,
        // tutto il resto serve solo alla pagina completa e viene saltato.
        if (renderFrontFragment($data)) {

            return;

        }

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
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

        renderFront(config('vfo', null, 'app') . '/contests_acts/contests_acts', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Atti delle amministrazioni aggiudicatrici e degli enti aggiudicatari distintamente per ogni procedura",
     * nella sezione dei Bandi di gara e contratti
     * ID sezione 112
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexActsOfContractingAuthorities(): void
    {
        $data = [];

        // Validazione dei campi d'input per la ricerca
        $validator = new Validator();
        if (Input::get('str') != null ||
            Input::get('obj') != null ||
            Input::get('c') != null ||
            Input::get('_cig') != null ||
            Input::get('s') != null ||
            Input::get('e') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Struttura')->value(Input::get('str'))->isInt()->end();
        $validator->label('Oggetto')->value(Input::get('obj'))->maxLength(30)->end();
        $validator->label('Scelta del contraente')->value(Input::get('c'))->isInt()->end();
        $validator->label('Cig')->value(Input::get('_cig'))->maxLength(12)->end();
        $validator->label('Data di pubblicazione dal')->value(Input::get('s'))->isDate('Y-m-d')->end();
        $validator->label('Data di pubblicazione al')->value(Input::get('e'))->isDate('Y-m-d')->end();

        if (!$validator->isSuccess()) {
            echo showError('Errore nella validazione dei dati', $validator->getErrorsHtml(), $validator->getErrorStatusCode());
            die();
        }

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        $tgCondition = '';

        // Recupero le informazioni della pagina corrente
        $currentPage = getCurrentPageInfo($data, $tgCondition);

        // Recupero le voci per costruire il menu dei contenuti
        $sectionFO = getRightOrBottomMenu($currentPageId, $currentPage['parent_id']);

        // Recupero tutti i bandi(liquidazioni escluse) da mostrare in base al criterio di pubblicazione della pagina
        $contestActs = ContestsActsModel::where('object_contests_acts.typology', '!=', 'liquidation')
            ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')));

        //Eventuali condizioni aggiuntive
        if (!empty($tgCondition)) {
            $contestActs = $contestActs->where($tgCondition);
        }

        $contestActs = $contestActs->with(['structure' => function ($query) {
            $query->select(['id', 'structure_name']);
        }])
            ->contestactsfilter(
                (int)Input::get('str', true),
                (Input::get('obj', true)),
                (int)Input::get('c', true),
                (Input::get('_cig', true)),
                Input::get('s', true),
                Input::get('e', true),
            )
            ->orderBy('object_contests_acts.activation_date', 'DESC')
            ->paginate(15, ['object_contests_acts.id', 'object_contests_acts.cig', 'object_contests_acts.object', 'object_contests_acts.activation_date',
                'object_contests_acts.object_structures_id'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get(['str', 'obj', 'c', '_cig', 's', 'e', 'sec_token'], true))
            ->setPath(currentUrl());

        $contestActs = !empty($contestActs) ? $contestActs->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($contestActs['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ContestsActsModel::select(['created_at'])
                ->where('object_contests_acts.typology', '!=', 'liquidation')
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ContestsActsModel::select(['updated_at'])
                ->where('object_contests_acts.typology', '!=', 'liquidation')
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Filtro per strutture
        $flagArchived = false;

        $query = StructuresModel::query();

        if (!$flagArchived) {
            $query->where(function ($query) {
            });
        }
        $structures = [null => 'Seleziona una struttura'] + $query
                ->orderBy('structure_name', 'ASC')
                ->pluck('structure_name', 'id')
                ->toArray();

        // Filtro per scelta del contraente
        $contraentChoices = [null => 'Qualunque'] + ContraentChoice::orderBy('name', 'ASC')
                ->pluck('name', 'id')
                ->toArray();

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
                'prefix' => '<div class="select-wrapper col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{structures}',
                'name' => 'str',
                'extra' => 'id="str"',
                'label' => [
                    'for' => 'str',
                    'text' => 'Struttura da cercare',
                ]
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
                'options' => '{contraent}',
                'name' => 'c',
                'extra' => 'id="c"',
                'label' => [
                    'for' => 'c',
                    'text' => 'Scelta del contraente',
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
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => '_cig',
                    'value' => '{cig}',
                    'id' => '_cig',
                ],
                'label' => [
                    'for' => '_cig',
                    'text' => 'Cig',
                ]
            ],
            [
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 's',
                    'value' => '{start_p_date}',
                    'type' => 'date',
                    'id' => 's',
                ],
                'label' => [
                    'for' => 's',
                    'text' => 'Data di scadenza - dal',
                    'class' => 'active'
                ]
            ],
            [
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'e',
                    'value' => '{end_p_date}',
                    'type' => 'date',
                    'id' => 'e',
                ],
                'label' => [
                    'for' => 'e',
                    'text' => 'Data di scadenza - al',
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
            'str' => Input::get('str'),
            'obj' => Input::get('obj'),
            'c' => Input::get('c'),
            '_cig' => Input::get('_cig'),
            's' => Input::get('s'),
            'e' => Input::get('e')
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
            'structures' => FormBuilder::parseOptions($structures, htmlEscape(Input::get('str', true))),
            'object' => htmlEscape(Input::get('obj', true)),
            'contraent' => FormBuilder::parseOptions($contraentChoices, htmlEscape(Input::get('c', true))),
            'cig' => htmlEscape(Input::get('_cig', true)),
            'start_p_date' => htmlEscape(Input::get('s', true)),
            'end_p_date' => htmlEscape(Input::get('e', true)),
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        $table = null;

        if (!empty($contestActs['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();

            $table->set_heading('Oggetto', 'Cig', 'Struttura competente', 'Data di pubblicazione');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($contestActs['data'] as $contestAct) {

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $contestAct['id'] . '/' . urlTitle($contestAct['object'])) . '" data-id="' . $contestAct['id'] . ' ">' . escapeXss($contestAct['object']) . '</a>',
                    escapeXss($contestAct['cig']),
                    !empty($contestAct['structure']['structure_name'])
                        ? '<a href="' . siteUrl('page/40/details/' . $contestAct['structure']['id'] . '/' . urlTitle($contestAct['structure']['structure_name']))
                        . '" data-id="' . $contestAct['object_structures_id'] . ' ">' . escapeXss($contestAct['structure']['structure_name']) . '</a>'
                        : null,
                    !empty($contestAct['activation_date']) ?
                        date('d/m/Y', strtotime($contestAct['activation_date']))
                        : null
                );
            }
        }

        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $contestActs;

        // Richiesta del solo frammento (paginazione AJAX): tabella e paginazione sono pronte,
        // tutto il resto serve solo alla pagina completa e viene saltato.
        if (renderFrontFragment($data)) {

            return;

        }

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
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

        renderFront(config('vfo', null, 'app') . '/contests_acts/contests_acts', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Procedure scadute",
     * nella sezione dei Bandi di gara e contratti
     * -> Atti delle amministrazioni aggiudicatrici e degli enti aggiudicatari distintamente per ogni procedura
     * ID sezione 119
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexExpiredProcedures(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Validazione dei campi di input per la ricerca
        $validator = new Validator();
        if (Input::get('str') != null ||
            Input::get('obj') != null ||
            Input::get('c') != null ||
            Input::get('_cig') != null ||
            Input::get('s') != null ||
            Input::get('e') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Struttura')->value(Input::get('str'))->isInt()->end();
        $validator->label('Oggetto')->value(Input::get('obj'))->maxLength(30)->end();
        $validator->label('Scelta del contraente')->value(Input::get('c'))->isInt()->end();
        $validator->label('Cig')->value(Input::get('_cig'))->maxLength(12)->end();
        $validator->label('Data di pubblicazione dal')->value(Input::get('s'))->isDate('Y-m-d')->end();
        $validator->label('Data di pubblicazione al')->value(Input::get('e'))->isDate('Y-m-d')->end();

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

        // Recupero i bandi da mostrare in base al criterio di pubblicazione della pagina
        $contestActs = ContestsActsModel::where('object_contests_acts.typology', '!=', 'liquidation')
            ->where('object_contests_acts.expiration_date', '<=', date("Y-m-d H:i:s"))
            ->whereNotNull('object_contests_acts.expiration_date')
            ->contestactsfilter(
                (int)Input::get('str', true),
                (Input::get('obj', true)),
                (int)Input::get('c', true),
                (Input::get('_cig', true)),
                Input::get('s', true),
                Input::get('e', true),
            )
            ->orderBy('expiration_date', 'DESC')
            ->paginate(15, ['object_contests_acts.id', 'object_contests_acts.object', 'object_contests_acts.expiration_date'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get(['str', 'obj', 'c', '_cig', 's', 'e', 'sec_token'], true))
            ->setPath(currentUrl());

        $contestActs = !empty($contestActs) ? $contestActs->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($contestActs['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ContestsActsModel::select(['created_at'])
                ->where('object_contests_acts.typology', '!=', 'liquidation')
                ->where('object_contests_acts.expiration_date', '<=', date("Y-m-d H:i:s"))
                ->whereNotNull('object_contests_acts.expiration_date')
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ContestsActsModel::select(['updated_at'])
                ->where('object_contests_acts.typology', '!=', 'liquidation')
                ->where('object_contests_acts.expiration_date', '<=', date("Y-m-d H:i:s"))
                ->whereNotNull('object_contests_acts.expiration_date')
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        $table = null;

        if (!empty($contestActs['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();

            $table->set_heading('Oggetto', 'Data di scadenza');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($contestActs['data'] as $contestAct) {

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $contestAct['id'] . '/' . urlTitle($contestAct['object'])) . '" data-id="' . $contestAct['id'] . ' ">' . escapeXss($contestAct['object']) . '</a>',
                    !empty($contestAct['expiration_date']) ?
                        date('d/m/Y', strtotime($contestAct['expiration_date']))
                        : null
                );
            }
        }

        // Filtro per strutture
        $flagArchived = false;

        $query = StructuresModel::query();

        if (!$flagArchived) {
            $query->where(function ($query) {
            });
        }
        $structures = [null => 'Seleziona una struttura'] + $query
                ->orderBy('structure_name', 'ASC')
                ->pluck('structure_name', 'id')
                ->toArray();

        // Filtro per scelta del contraente
        $contraentChoices = [null => 'Qualunque'] + ContraentChoice::orderBy('name', 'ASC')
                ->pluck('name', 'id')
                ->toArray();

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
                'prefix' => '<div class="select-wrapper col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{structures}',
                'name' => 'str',
                'extra' => 'id="str"',
                'label' => [
                    'for' => 'str',
                    'text' => 'Struttura da cercare',
                ]
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
                'options' => '{contraent}',
                'name' => 'c',
                'extra' => 'id="c"',
                'label' => [
                    'for' => 'c',
                    'text' => 'Scelta del contraente',
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
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => '_cig',
                    'value' => '{cig}',
                    'id' => '_cig',
                ],
                'label' => [
                    'for' => '_cig',
                    'text' => 'Cig',
                ]
            ],
            [
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 's',
                    'value' => '{start_p_date}',
                    'type' => 'date',
                    'id' => 's',
                ],
                'label' => [
                    'for' => 's',
                    'text' => 'Data di scadenza - dal',
                    'class' => 'active'
                ]
            ],
            [
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'e',
                    'value' => '{end_p_date}',
                    'type' => 'date',
                    'id' => 'e',

                ],
                'label' => [
                    'for' => 'e',
                    'text' => 'Data di scadenza - al',
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
            'str' => Input::get('str'),
            'obj' => Input::get('obj'),
            'c' => Input::get('c'),
            '_cig' => Input::get('_cig'),
            's' => Input::get('s'),
            'e' => Input::get('e')
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
            'structures' => FormBuilder::parseOptions($structures, htmlEscape(Input::get('str', true))),
            'object' => htmlEscape(Input::get('obj', true)),
            'contraent' => FormBuilder::parseOptions($contraentChoices, htmlEscape(Input::get('c', true))),
            'cig' => htmlEscape(Input::get('_cig', true)),
            'start_p_date' => htmlEscape(Input::get('s', true)),
            'end_p_date' => htmlEscape(Input::get('e', true)),
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $contestActs;

        // Richiesta del solo frammento (paginazione AJAX): tabella e paginazione sono pronte,
        // tutto il resto serve solo alla pagina completa e viene saltato.
        if (renderFrontFragment($data)) {

            return;

        }

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
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

        renderFront(config('vfo', null, 'app') . '/contests_acts/contests_acts', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Avvisi di preinformazione",
     * nella sezione dei Bandi di gara e contratti
     * ID sezione 257
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexPreinformationNotices(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        $alertSectors = config('sectorAlert', null, 'noticeActs');

        // Validazione dei campi di input per la ricerca
        $validator = new Validator();
        if (Input::get('str') != null ||
            Input::get('obj') != null ||
            Input::get('_cig') != null ||
            Input::get('s') != null ||
            Input::get('e') != null ||
            Input::get('sec') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Struttura')->value(Input::get('str'))->isInt()->end();
        $validator->label('Oggetto')->value(Input::get('obj'))->maxLength(30)->end();
        $validator->label('Settore')->value(Input::get('sec'))->in(implode(',', array_keys($alertSectors)))->end();
        $validator->label('Cig')->value(Input::get('_cig'))->maxLength(12)->end();
        $validator->label('Data di pubblicazione dal')->value(Input::get('s'))->isDate('Y-m-d')->end();
        $validator->label('Data di pubblicazione al')->value(Input::get('e'))->isDate('Y-m-d')->end();

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

        // Recupero i bandi da mostrare in base al criterio di pubblicazione della pagina
        $contestActs = ContestsActsModel::where('object_contests_acts.typology', 'alert')
            ->where(function ($query) {
                $query->where('object_contests_acts.expiration_date', '>=', date('Y-m-d H:i:s'))
                    ->orWhereNull('object_contests_acts.expiration_date');
            })
            ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
            ->whereHas('public_in_section', function ($query) use ($currentPageId) {
                $query->where('public_in_id', $currentPageId);
            })
            ->with(['structure' => function ($query) {
                $query->select(['id', 'structure_name']);
            }])
            ->contestactsfilter(
                (int)Input::get('str', true),
                (Input::get('obj', true)),
                null,
                (Input::get('_cig', true)),
                Input::get('s', true),
                Input::get('e', true),
                Input::get('sec', true)
            )
            ->paginate(15, ['object_contests_acts.id', 'object_contests_acts.object', 'object_contests_acts.activation_date',
                'object_contests_acts.sector', 'object_contests_acts.cig', 'object_contests_acts.object_structures_id'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get(['str', 'obj', 'c', '_cig', 's', 'e', 'sec', 'sec_token'], true))
            ->setPath(currentUrl());

        $contestActs = !empty($contestActs) ? $contestActs->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($contestActs['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ContestsActsModel::select(['created_at'])
                ->where('object_contests_acts.typology', 'alert')
                ->where(function ($query) {
                    $query->where('object_contests_acts.expiration_date', '>=', date('Y-m-d H:i:s'))
                        ->orWhereNull('object_contests_acts.expiration_date');
                })
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->whereHas('public_in_section', function ($query) use ($currentPageId) {
                    $query->where('public_in_id', $currentPageId);
                })
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ContestsActsModel::select(['updated_at'])
                ->where('object_contests_acts.typology', 'alert')
                ->where(function ($query) {
                    $query->where('object_contests_acts.expiration_date', '>=', date('Y-m-d H:i:s'))
                        ->orWhereNull('object_contests_acts.expiration_date');
                })
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
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

        $table = null;

        if (!empty($contestActs['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();

            $table->set_heading('Oggetto', 'Settore', 'CIG', 'Struttura Competente', 'Data di pubblicazione');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($contestActs['data'] as $contestAct) {

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $contestAct['id'] . '/' . urlTitle($contestAct['object'])) . '" data-id="' . $contestAct['id'] . ' ">' . escapeXss($contestAct['object']) . '</a>',
                    !empty($contestAct['sector']) ? config('sector', null, 'noticeActs')[$contestAct['sector']] : '',
                    !empty($contestAct['cig']) ? $contestAct['cig'] : '',
                    !empty($contestAct['structure']['structure_name'])
                        ? '<a href="' . siteUrl('page/40/details/' . $contestAct['structure']['id'] . '/' . urlTitle($contestAct['structure']['structure_name']))
                        . '" data-id="' . $contestAct['object_structures_id'] . ' ">' . escapeXss($contestAct['structure']['structure_name']) . '</a>'
                        : null,
                    !empty($contestAct['activation_date']) ?
                        date('d/m/Y', strtotime($contestAct['activation_date']))
                        : null
                );
            }
        }

        // Filtro per strutture
        $flagArchived = false;

        $query = StructuresModel::query();

        if (!$flagArchived) {
            $query->where(function ($query) {
            });
        }
        $structures = [null => 'Seleziona una struttura'] + $query
                ->orderBy('structure_name', 'ASC')
                ->pluck('structure_name', 'id')
                ->toArray();

        // Filtro per scelta del contraente
        $sectors = [null => 'Qualunque'] + $alertSectors;

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
                'prefix' => '<div class="select-wrapper col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{structures}',
                'name' => 'str',
                'extra' => 'id="str"',
                'label' => [
                    'for' => 'str',
                    'text' => 'Struttura da cercare',
                ]
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
                'options' => '{sector}',
                'name' => 'sec',
                'extra' => 'id="sec"',
                'label' => [
                    'for' => 'sec',
                    'text' => 'Settore',
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
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => '_cig',
                    'value' => '{cig}',
                    'id' => '_cig',

                ],
                'label' => [
                    'for' => '_cig',
                    'text' => 'Cig',
                ]
            ],
            [
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 's',
                    'value' => '{start_p_date}',
                    'type' => 'date',
                    'id' => 's',

                ],
                'label' => [
                    'for' => 's',
                    'text' => 'Data di scadenza - dal',
                    'class' => 'active'
                ]
            ],
            [
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'e',
                    'value' => '{end_p_date}',
                    'type' => 'date',
                    'id' => 'e',

                ],
                'label' => [
                    'for' => 'e',
                    'text' => 'Data di scadenza - al',
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
            'str' => Input::get('str'),
            'obj' => Input::get('obj'),
            'c' => Input::get('c'),
            '_cig' => Input::get('_cig'),
            's' => Input::get('s'),
            'e' => Input::get('e'),
            'sec' => Input::get('sec')
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
            'structures' => FormBuilder::parseOptions($structures, htmlEscape(Input::get('str', true))),
            'object' => htmlEscape(Input::get('obj', true)),
            'sector' => FormBuilder::parseOptions($sectors, htmlEscape(Input::get('sec', true))),
            'cig' => htmlEscape(Input::get('_cig', true)),
            'start_p_date' => htmlEscape(Input::get('s', true)),
            'end_p_date' => htmlEscape(Input::get('e', true)),
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $contestActs;

        // Richiesta del solo frammento (paginazione AJAX): tabella e paginazione sono pronte,
        // tutto il resto serve solo alla pagina completa e viene saltato.
        if (renderFrontFragment($data)) {

            return;

        }

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
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

        renderFront(config('vfo', null, 'app') . '/contests_acts/contests_acts', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Avvisi e bandi",
     * nella sezione dei Bandi di gara e contratti
     * ID sezione 524
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexNoticesAndAdvertisements(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        $noticeSectors = config('sector', null, 'noticeActs');

        // Validazione dei campi di input per la ricerca
        $validator = new Validator();
        if (Input::get('str') != null ||
            Input::get('obj') != null ||
            Input::get('sec') != null ||
            Input::get('_cig') != null ||
            Input::get('s') != null ||
            Input::get('e') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Struttura')->value(Input::get('str'))->isInt()->end();
        $validator->label('Oggetto')->value(Input::get('obj'))->maxLength(30)->end();
        $validator->label('Settore')->value(Input::get('sec'))->in(implode(',', array_keys($noticeSectors)))->end();
        $validator->label('Cig')->value(Input::get('_cig'))->maxLength(12)->end();
        $validator->label('Data di pubblicazione dal')->value(Input::get('s'))->isDate('Y-m-d')->end();
        $validator->label('Data di pubblicazione al')->value(Input::get('e'))->isDate('Y-m-d')->end();

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

        // Recupero i bandi da mostrare in base al criterio di pubblicazione della pagina

        $contestActs = ContestsActsModel::where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
            ->where(function ($query) use ($currentPageId) {
                $query->whereHas('public_in_section', function ($query) use ($currentPageId) {
                    $query->where('public_in_id', $currentPageId);
                })
                    ->orWhere(function ($query) {
                        $query->where('object_contests_acts.typology', 'alert')
                            ->orWhere('object_contests_acts.typology', 'notice');
                    });
            })
            ->with('structure:id,structure_name')
            ->contestactsfilter(
                (int)Input::get('str', true),
                (Input::get('obj', true)),
                null,
                (Input::get('_cig', true)),
                Input::get('s', true),
                Input::get('e', true),
                Input::get('sec', true)
            )
            ->orderBy('object_contests_acts.activation_date', 'DESC')
            ->groupBy('object_contests_acts.id')
            ->paginate(15, ['object_contests_acts.id', 'object_contests_acts.object', 'object_contests_acts.activation_date', 'object_contests_acts.sector',
                'object_contests_acts.cig', 'object_contests_acts.typology', 'object_contests_acts.object_structures_id'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get(['str', 'obj', '_cig', 's', 'e', 'sec', 'sec_token']))
            ->setPath(currentUrl());

        $contestActs = !empty($contestActs) ? $contestActs->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        $table = null;

        if (!empty($contestActs['data'])) {

            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ContestsActsModel::select(['created_at'])
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->where(function ($query) use ($currentPageId) {
                    $query->whereHas('public_in_section', function ($query) use ($currentPageId) {
                        $query->where('public_in_id', $currentPageId);
                    })
                        ->orWhere(function ($query) {
                            $query->where('object_contests_acts.typology', 'alert')
                                ->orWhere('object_contests_acts.typology', 'notice');
                        });
                })
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ContestsActsModel::select(['updated_at'])
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->where(function ($query) use ($currentPageId) {
                    $query->whereHas('public_in_section', function ($query) use ($currentPageId) {
                        $query->where('public_in_id', $currentPageId);
                    })
                        ->orWhere(function ($query) {
                            $query->where('object_contests_acts.typology', 'alert')
                                ->orWhere('object_contests_acts.typology', 'notice');
                        });
                })
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();

            $table->set_heading('Oggetto', 'Settore', 'CIG', 'Struttura Competente', 'Data di pubblicazione');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($contestActs['data'] as $contestAct) {

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $contestAct['id'] . '/' . urlTitle($contestAct['object'])) . '" data-id="' . $contestAct['id'] . ' ">' . escapeXss($contestAct['object']) . '</a>',
                    !empty($contestAct['sector']) ? config('sector', null, 'noticeActs')[$contestAct['sector']] : '',
                    !empty($contestAct['cig']) ? $contestAct['cig'] : '',
                    !empty($contestAct['structure']['structure_name'])
                        ? '<a href="' . siteUrl('page/40/details/' . $contestAct['structure']['id'] . '/' . urlTitle($contestAct['structure']['structure_name'])) .
                        '" data-id="' . $contestAct['object_structures_id'] . ' ">' . escapeXss($contestAct['structure']['structure_name']) . '</a>'
                        : null,
                    !empty($contestAct['activation_date']) ?
                        date('d/m/Y', strtotime($contestAct['activation_date']))
                        : null
                );
            }
        }

        // Filtro per strutture
        $flagArchived = false;

        $query = StructuresModel::query();

        if (!$flagArchived) {
            $query->where(function ($query) {
            });
        }
        $structures = [null => 'Seleziona una struttura'] + $query
                ->orderBy('structure_name', 'ASC')
                ->pluck('structure_name', 'id')
                ->toArray();

        // Filtro per scelta del contraente
        $sectors = [null => 'Qualunque'] + $noticeSectors;

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
                'prefix' => '<div class="select-wrapper col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{structures}',
                'name' => 'str',
                'extra' => 'id="str"',
                'label' => [
                    'for' => 'str',
                    'text' => 'Struttura da cercare',
                ]
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
                'options' => '{sector}',
                'name' => 'sec',
                'extra' => 'id="sec"',
                'label' => [
                    'for' => 'sec',
                    'text' => 'Settore',
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
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => '_cig',
                    'value' => '{cig}',
                    'id' => '_cig',

                ],
                'label' => [
                    'for' => '_cig',
                    'text' => 'Cig',
                ]
            ],
            [
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 's',
                    'value' => '{start_p_date}',
                    'type' => 'date',
                    'id' => 's',

                ],
                'label' => [
                    'for' => 's',
                    'text' => 'Data di scadenza - dal',
                    'class' => 'active'
                ]
            ],
            [
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'e',
                    'value' => '{end_p_date}',
                    'type' => 'date',
                    'id' => 'e',

                ],
                'label' => [
                    'for' => 'e',
                    'text' => 'Data di scadenza - al',
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
            'str' => Input::get('str'),
            'obj' => Input::get('obj'),
            'c' => Input::get('c'),
            '_cig' => Input::get('_cig'),
            's' => Input::get('s'),
            'e' => Input::get('e')
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
            'structures' => FormBuilder::parseOptions($structures, htmlEscape(Input::get('str', true))),
            'object' => htmlEscape(Input::get('obj', true)),
            'sector' => FormBuilder::parseOptions($sectors, htmlEscape(Input::get('sec', true))),
            'cig' => htmlEscape(Input::get('_cig', true)),
            'start_p_date' => htmlEscape(Input::get('s', true)),
            'end_p_date' => htmlEscape(Input::get('e', true)),
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $contestActs;

        // Richiesta del solo frammento (paginazione AJAX): tabella e paginazione sono pronte,
        // tutto il resto serve solo alla pagina completa e viene saltato.
        if (renderFrontFragment($data)) {

            return;

        }

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
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

        renderFront(config('vfo', null, 'app') . '/contests_acts/contests_acts', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Delibera a contrarre",
     * nella sezione dei Bandi di gara e contratti
     * ID sezione 528
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexDeliberation(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        $deliberationSectors = config('sectorDeliberation', null, 'noticeActs');

        // Validazione dei campi d'input per la ricerca
        $validator = new Validator();
        if (Input::get('str') != null ||
            Input::get('obj') != null ||
            Input::get('sec') != null ||
            Input::get('_cig') != null ||
            Input::get('s') != null ||
            Input::get('e') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Struttura')->value(Input::get('str'))->isInt()->end();
        $validator->label('Oggetto')->value(Input::get('obj'))->maxLength(30)->end();
        $validator->label('Settore')->value(Input::get('sec'))->in(implode(',', array_keys($deliberationSectors)))->end();
        $validator->label('Cig')->value(Input::get('_cig'))->maxLength(12)->end();
        $validator->label('Data di pubblicazione dal')->value(Input::get('s'))->isDate('Y-m-d')->end();
        $validator->label('Data di pubblicazione al')->value(Input::get('e'))->isDate('Y-m-d')->end();

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

        // Recupero i bandi da mostrare in base al criterio di pubblicazione della pagina
        // Recupero tutte le delibere con la data di attivazione <= a oggi
        $contestActs = ContestsActsModel::where('object_contests_acts.typology', 'deliberation')
            ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
            ->with(['structure' => function ($query) {
                $query->select(['id', 'structure_name']);
            }])
            ->contestactsfilter(
                (int)Input::get('str', true),
                (Input::get('obj', true)),
                null,
                (Input::get('_cig', true)),
                Input::get('s', true),
                Input::get('e', true),
                Input::get('sec', true)
            )
            ->groupBy('object_contests_acts.id')
            ->orderBy('object_contests_acts.activation_date', 'DESC')
            ->paginate(15, ['object_contests_acts.id', 'object_contests_acts.object', 'object_contests_acts.activation_date',
                'object_contests_acts.sector', 'object_contests_acts.cig', 'object_contests_acts.typology', 'object_contests_acts.object_structures_id'],
                'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get(['str', 'obj', 'c', '_cig', 's', 'e', 'sec_token'], true))
            ->setPath(currentUrl());

        $contestActs = !empty($contestActs) ? $contestActs->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        $table = null;

        if (!empty($contestActs['data'])) {

            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ContestsActsModel::select(['created_at'])
                ->where('object_contests_acts.typology', 'deliberation')
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ContestsActsModel::select(['updated_at'])
                ->where('object_contests_acts.typology', 'deliberation')
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();

            $table->set_heading('Oggetto', 'Settore', 'CIG', 'Struttura Competente', 'Data di pubblicazione');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($contestActs['data'] as $contestAct) {

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $contestAct['id'] . '/' . urlTitle($contestAct['object'])) . '" data-id="' . $contestAct['id'] . ' ">' . escapeXss($contestAct['object']) . '</a>',
                    !empty($contestAct['sector']) ? config('sector', null, 'noticeActs')[$contestAct['sector']] : '',
                    !empty($contestAct['cig']) ? $contestAct['cig'] : '',
                    !empty($contestAct['structure']['structure_name'])
                        ? '<a href="' . siteUrl('page/40/details/' . $contestAct['structure']['id'] . '/' . urlTitle($contestAct['structure']['structure_name'])) . '" 
                        data-id="' . $contestAct['object_structures_id'] . '">' . escapeXss($contestAct['structure']['structure_name']) . '</a>'
                        : null,
                    !empty($contestAct['activation_date']) ?
                        date('d/m/Y', strtotime($contestAct['activation_date']))
                        : null
                );
            }
        }

        // Filtro per strutture
        $flagArchived = false;

        $query = StructuresModel::query();

        if (!$flagArchived) {
            $query->where(function ($query) {
            });
        }
        $structures = [null => 'Seleziona una struttura'] + $query
                ->orderBy('structure_name', 'ASC')
                ->pluck('structure_name', 'id')
                ->toArray();

        // Filtro per scelta del contraente
        $sectors = [null => 'Qualunque'] + $deliberationSectors;

        // Form per i filtri di ricerca
        $form = json_encode([
            [
                'type' => 'form_open',
                'name' => 'contest_acts',
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
                'prefix' => '<div class="select-wrapper col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{structures}',
                'name' => 'str',
                'extra' => 'id="str"',
                'label' => [
                    'for' => 'str',
                    'text' => 'Struttura da cercare',
                ]
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
                'options' => '{sector}',
                'name' => 'sec',
                'extra' => 'id="sec"',
                'label' => [
                    'for' => 'sec',
                    'text' => 'Settore',
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
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => '_cig',
                    'value' => '{cig}',
                    'id' => '_cig',

                ],
                'label' => [
                    'for' => '_cig',
                    'text' => 'Cig',
                ]
            ],
            [
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 's',
                    'value' => '{start_p_date}',
                    'type' => 'date',
                    'id' => 's',

                ],
                'label' => [
                    'for' => 's',
                    'text' => 'Data di scadenza - dal',
                    'class' => 'active'
                ]
            ],
            [
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'e',
                    'value' => '{end_p_date}',
                    'type' => 'date',
                    'id' => 'e',

                ],
                'label' => [
                    'for' => 'e',
                    'text' => 'Data di scadenza - al',
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
            'str' => Input::get('str'),
            'obj' => Input::get('obj'),
            'c' => Input::get('c'),
            '_cig' => Input::get('_cig'),
            's' => Input::get('s'),
            'e' => Input::get('e')
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
            'structures' => FormBuilder::parseOptions($structures, htmlEscape(Input::get('str', true))),
            'object' => htmlEscape(Input::get('obj', true)),
            'sector' => FormBuilder::parseOptions($sectors, htmlEscape(Input::get('sec', true))),
            'cig' => htmlEscape(Input::get('_cig', true)),
            'start_p_date' => htmlEscape(Input::get('s', true)),
            'end_p_date' => htmlEscape(Input::get('e', true)),
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $contestActs;

        // Richiesta del solo frammento (paginazione AJAX): tabella e paginazione sono pronte,
        // tutto il resto serve solo alla pagina completa e viene saltato.
        if (renderFrontFragment($data)) {

            return;

        }

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
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

        renderFront(config('vfo', null, 'app') . '/contests_acts/contests_acts', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Concessioni e partenariato pubblico privato",
     * nella sezione dei Bandi di gara e contratti
     * ID sezione 525
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexConcessionsAndPartnership(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        $noticeSectors = config('sector', null, 'noticeActs');

        // Validazione dei campi d'input per la ricerca
        $validator = new Validator();
        if (Input::get('str') != null ||
            Input::get('obj') != null ||
            Input::get('sec') != null ||
            Input::get('_cig') != null ||
            Input::get('s') != null ||
            Input::get('e') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Struttura')->value(Input::get('str'))->isInt()->end();
        $validator->label('Oggetto')->value(Input::get('obj'))->maxLength(30)->end();
        $validator->label('Settore')->value(Input::get('sec'))->in(implode(',', array_keys($noticeSectors)))->end();
        $validator->label('Cig')->value(Input::get('_cig'))->maxLength(12)->end();
        $validator->label('Data di pubblicazione dal')->value(Input::get('s'))->isDate('Y-m-d')->end();
        $validator->label('Data di pubblicazione al')->value(Input::get('e'))->isDate('Y-m-d')->end();

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

        // Recupero i bandi da mostrare in base al criterio di pubblicazione della pagina
        $contestActs = ContestsActsModel::where(function ($query) {
            $query->where('object_contests_acts.expiration_date', '>=', date('Y-m-d H:i:s'))
                ->orWhereNull('object_contests_acts.expiration_date');
        })
            ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
            ->where(function ($query) use ($currentPageId) {
                $query->whereHas('public_in_section', function ($query) use ($currentPageId) {
                    $query->where('public_in_id', $currentPageId);
                })
                    ->whereIn('object_contests_acts.typology', ['alert', 'notice', 'deliberation', 'result']);
            })
            ->with(['structure' => function ($query) {
                $query->select(['id', 'structure_name']);
            }])
            ->contestactsfilter(
                (int)Input::get('str', true),
                (Input::get('obj', true)),
                null,
                (Input::get('_cig', true)),
                Input::get('s', true),
                Input::get('e', true),
                Input::get('sec', true)
            )
            ->groupBy('object_contests_acts.id')
            ->paginate(15, ['object_contests_acts.id', 'object_contests_acts.object', 'object_contests_acts.activation_date', 'object_contests_acts.sector',
                'object_contests_acts.cig', 'object_contests_acts.typology', 'object_contests_acts.object_structures_id'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get(['str', 'obj', '_cig', 's', 'e', 'sec', 'sec_token']))
            ->setPath(currentUrl());

        $contestActs = !empty($contestActs) ? $contestActs->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        $table = null;

        if (!empty($contestActs['data'])) {

            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ContestsActsModel::select(['created_at'])
                ->where(function ($query) {
                    $query->where('object_contests_acts.expiration_date', '>=', date('Y-m-d H:i:s'))
                        ->orWhereNull('object_contests_acts.expiration_date');
                })
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->where(function ($query) use ($currentPageId) {
                    $query->whereHas('public_in_section', function ($query) use ($currentPageId) {
                        $query->where('public_in_id', $currentPageId);
                    })
                        ->whereIn('object_contests_acts.typology', ['alert', 'notice', 'deliberation', 'result']);
                })
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ContestsActsModel::select(['updated_at'])
                ->where(function ($query) {
                    $query->where('object_contests_acts.expiration_date', '>=', date('Y-m-d H:i:s'))
                        ->orWhereNull('object_contests_acts.expiration_date');
                })
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->where(function ($query) use ($currentPageId) {
                    $query->whereHas('public_in_section', function ($query) use ($currentPageId) {
                        $query->where('public_in_id', $currentPageId);
                    })
                        ->whereIn('object_contests_acts.typology', ['alert', 'notice', 'deliberation', 'result']);
                })
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();

            $table->set_heading('Oggetto', 'Settore', 'CIG', 'Struttura Competente', 'Data di pubblicazione');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($contestActs['data'] as $contestAct) {

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $contestAct['id'] . '/' . urlTitle($contestAct['object'])) . '" data-id="' . $contestAct['id'] . ' ">' . escapeXss($contestAct['object']) . '</a>',
                    !empty($contestAct['sector']) ? config('sector', null, 'noticeActs')[$contestAct['sector']] : '',
                    !empty($contestAct['cig']) ? $contestAct['cig'] : '',
                    !empty($contestAct['structure']['structure_name'])
                        ? '<a href="' . siteUrl('page/40/details/' . $contestAct['structure']['id'] . '/' . urlTitle($contestAct['structure']['structure_name']))
                        . '" data-id="' . $contestAct['object_structures_id'] . ' ">' . escapeXss($contestAct['structure']['structure_name']) . '</a>'
                        : null,
                    !empty($contestAct['activation_date']) ?
                        date('d/m/Y', strtotime($contestAct['activation_date']))
                        : null
                );
            }
        }

        // Filtro per strutture
        $flagArchived = false;

        $query = StructuresModel::query();

        if (!$flagArchived) {
            $query->where(function ($query) {
            });
        }
        $structures = [null => 'Seleziona una struttura'] + $query
                ->orderBy('structure_name', 'ASC')
                ->pluck('structure_name', 'id')
                ->toArray();

        // Filtro per scelta del contraente
        $sectors = [null => 'Qualunque'] + $noticeSectors;

        // Form per i filtri di ricerca
        $form = json_encode([
            [
                'type' => 'form_open',
                'name' => 'contest_acts',
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
                'prefix' => '<div class="select-wrapper col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{structures}',
                'name' => 'str',
                'extra' => 'id="str"',
                'label' => [
                    'for' => 'str',
                    'text' => 'Struttura da cercare',
                ]
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
                'options' => '{sector}',
                'name' => 'sec',
                'extra' => 'id="sec"',
                'label' => [
                    'for' => 'sec',
                    'text' => 'Settore',
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
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => '_cig',
                    'value' => '{cig}',
                    'id' => '_cig',

                ],
                'label' => [
                    'for' => '_cig',
                    'text' => 'Cig',
                ]
            ],
            [
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 's',
                    'value' => '{start_p_date}',
                    'type' => 'date',
                    'id' => 's',

                ],
                'label' => [
                    'for' => 's',
                    'text' => 'Data di scadenza - dal',
                    'class' => 'active'
                ]
            ],
            [
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'e',
                    'value' => '{end_p_date}',
                    'type' => 'date',
                    'id' => 'e',

                ],
                'label' => [
                    'for' => 'e',
                    'text' => 'Data di scadenza - al',
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
            'str' => Input::get('str'),
            'obj' => Input::get('obj'),
            'c' => Input::get('c'),
            '_cig' => Input::get('_cig'),
            's' => Input::get('s'),
            'e' => Input::get('e')
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
            'structures' => FormBuilder::parseOptions($structures, htmlEscape(Input::get('str', true))),
            'object' => htmlEscape(Input::get('obj', true)),
            'sector' => FormBuilder::parseOptions($sectors, htmlEscape(Input::get('sec', true))),
            'cig' => htmlEscape(Input::get('_cig', true)),
            'start_p_date' => htmlEscape(Input::get('s', true)),
            'end_p_date' => htmlEscape(Input::get('e', true)),
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $contestActs;

        // Richiesta del solo frammento (paginazione AJAX): tabella e paginazione sono pronte,
        // tutto il resto serve solo alla pagina completa e viene saltato.
        if (renderFrontFragment($data)) {

            return;

        }

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
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

        renderFront(config('vfo', null, 'app') . '/contests_acts/contests_acts', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Affidamenti diretti di lavori, servizi e forniture di somma urgenza e di protezione civile",
     * nella sezione dei Bandi di gara e contratti
     * ID sezione 532
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexDirectFoster(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        $noticeSectors = config('sector', null, 'noticeActs');

        // Validazione dei campi d'input per la ricerca
        $validator = new Validator();
        if (Input::get('str') != null ||
            Input::get('obj') != null ||
            Input::get('sec') != null ||
            Input::get('_cig') != null ||
            Input::get('s') != null ||
            Input::get('e') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Struttura')->value(Input::get('str'))->isInt()->end();
        $validator->label('Oggetto')->value(Input::get('obj'))->maxLength(30)->end();
        $validator->label('Settore')->value(Input::get('sec'))->in(implode(',', array_keys($noticeSectors)))->end();
        $validator->label('Cig')->value(Input::get('_cig'))->maxLength(12)->end();
        $validator->label('Data di pubblicazione dal')->value(Input::get('s'))->isDate('Y-m-d')->end();
        $validator->label('Data di pubblicazione al')->value(Input::get('e'))->isDate('Y-m-d')->end();

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
        $contestActs = ContestsActsModel::where('object_contests_acts.decree_163', 1)
            ->where('object_contests_acts.typology', 'foster')
            ->where(function ($query) {
                $query->where('object_contests_acts.expiration_date', '>=', date('Y-m-d H:i:s'))
                    ->orWhereNull('object_contests_acts.expiration_date');
            })
            ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
            ->with(['structure' => function ($query) {
                $query->select(['id', 'structure_name']);
            }])
            ->contestactsfilter(
                (int)Input::get('str', true),
                (Input::get('obj', true)),
                null,
                (Input::get('_cig', true)),
                Input::get('s', true),
                Input::get('e', true),
                Input::get('sec', true)
            )
            ->groupBy('object_contests_acts.id')
            ->paginate(15, ['object_contests_acts.id', 'object_contests_acts.object', 'object_contests_acts.activation_date', 'object_contests_acts.sector',
                'object_contests_acts.cig', 'object_contests_acts.typology', 'object_contests_acts.object_structures_id'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get(['str', 'obj', '_cig', 's', 'e', 'sec', 'sec_token']))
            ->setPath(currentUrl());

        $contestActs = !empty($contestActs) ? $contestActs->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        $table = null;

        if (!empty($contestActs['data'])) {

            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ContestsActsModel::select(['created_at'])
                ->where('object_contests_acts.decree_163', 1)
                ->where('object_contests_acts.typology', 'foster')
                ->where(function ($query) {
                    $query->where('object_contests_acts.expiration_date', '>=', date('Y-m-d H:i:s'))
                        ->orWhereNull('object_contests_acts.expiration_date');
                })
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ContestsActsModel::select(['updated_at'])
                ->where('object_contests_acts.decree_163', 1)
                ->where('object_contests_acts.typology', 'foster')
                ->where(function ($query) {
                    $query->where('object_contests_acts.expiration_date', '>=', date('Y-m-d H:i:s'))
                        ->orWhereNull('object_contests_acts.expiration_date');
                })
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();

            $table->set_heading('Oggetto', 'Settore', 'CIG', 'Struttura Competente', 'Data di pubblicazione');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($contestActs['data'] as $contestAct) {

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $contestAct['id'] . '/' . urlTitle($contestAct['object'])) . '" data-id="' . $contestAct['id'] . ' ">' . escapeXss($contestAct['object']) . '</a>',
                    !empty($contestAct['sector']) ? config('sector', null, 'noticeActs')[$contestAct['sector']] : '',
                    !empty($contestAct['cig']) ? $contestAct['cig'] : '',
                    !empty($contestAct['structure']['structure_name'])
                        ? '<a href="' . siteUrl('page/40/details/' . $contestAct['structure']['id'] . '/' . urlTitle($contestAct['structure']['structure_name'])) .
                        '" data-id="' . $contestAct['object_structures_id'] . ' ">' . escapeXss($contestAct['structure']['structure_name']) . '</a>'
                        : null,
                    !empty($contestAct['activation_date']) ?
                        date('d/m/Y', strtotime($contestAct['activation_date']))
                        : null
                );
            }
        }

        // Filtro per strutture
        $flagArchived = false;

        $query = StructuresModel::query();

        if (!$flagArchived) {
            $query->where(function ($query) {
            });
        }
        $structures = [null => 'Seleziona una struttura'] + $query
                ->orderBy('structure_name', 'ASC')
                ->pluck('structure_name', 'id')
                ->toArray();

        // Filtro per scelta del contraente
        $sectors = [null => 'Qualunque'] + $noticeSectors;

        // Form per i filtri di ricerca
        $form = json_encode([
            [
                'type' => 'form_open',
                'name' => 'contest_acts',
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
                'prefix' => '<div class="select-wrapper col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{structures}',
                'name' => 'str',
                'extra' => 'id="str"',
                'label' => [
                    'for' => 'str',
                    'text' => 'Struttura da cercare',
                ]
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
                'options' => '{sector}',
                'name' => 'sec',
                'extra' => 'id="sec"',
                'label' => [
                    'for' => 'sec',
                    'text' => 'Settore',
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
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => '_cig',
                    'value' => '{cig}',
                    'id' => '_cig',

                ],
                'label' => [
                    'for' => '_cig',
                    'text' => 'Cig',
                ]
            ],
            [
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 's',
                    'value' => '{start_p_date}',
                    'type' => 'date',
                    'id' => 's',

                ],
                'label' => [
                    'for' => 's',
                    'text' => 'Data di scadenza - dal',
                    'class' => 'active'
                ]
            ],
            [
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'e',
                    'value' => '{end_p_date}',
                    'type' => 'date',
                    'id' => 'e',

                ],
                'label' => [
                    'for' => 'e',
                    'text' => 'Data di scadenza - al',
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
            'str' => Input::get('str'),
            'obj' => Input::get('obj'),
            'c' => Input::get('c'),
            '_cig' => Input::get('_cig'),
            's' => Input::get('s'),
            'e' => Input::get('e')
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
            'structures' => FormBuilder::parseOptions($structures, htmlEscape(Input::get('str', true))),
            'object' => htmlEscape(Input::get('obj', true)),
            'sector' => FormBuilder::parseOptions($sectors, htmlEscape(Input::get('sec', true))),
            'cig' => htmlEscape(Input::get('_cig', true)),
            'start_p_date' => htmlEscape(Input::get('s', true)),
            'end_p_date' => htmlEscape(Input::get('e', true)),
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $contestActs;

        // Richiesta del solo frammento (paginazione AJAX): tabella e paginazione sono pronte,
        // tutto il resto serve solo alla pagina completa e viene saltato.
        if (renderFrontFragment($data)) {

            return;

        }

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
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

        renderFront(config('vfo', null, 'app') . '/contests_acts/contests_acts', $data, 'frontend');
    }


    /**
     * Metodo chiamato per la pagina "Affidamenti in house",
     * nella sezione dei Bandi di gara e contratti
     * ID sezione 533
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexInHouseContracting(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        $noticeSectors = config('sector', null, 'noticeActs');

        // Validazione dei campi d'input per la ricerca
        $validator = new Validator();
        if (Input::get('str') != null ||
            Input::get('obj') != null ||
            Input::get('sec') != null ||
            Input::get('_cig') != null ||
            Input::get('s') != null ||
            Input::get('e') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Struttura')->value(Input::get('str'))->isInt()->end();
        $validator->label('Oggetto')->value(Input::get('obj'))->maxLength(30)->end();
        $validator->label('Settore')->value(Input::get('sec'))->in(implode(',', array_keys($noticeSectors)))->end();
        $validator->label('Cig')->value(Input::get('_cig'))->maxLength(12)->end();
        $validator->label('Data di pubblicazione dal')->value(Input::get('s'))->isDate('Y-m-d')->end();
        $validator->label('Data di pubblicazione al')->value(Input::get('e'))->isDate('Y-m-d')->end();

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
        $contestActs = ContestsActsModel::where('object_contests_acts.typology', 'foster')
            ->where(function ($query) {
                $query->where('object_contests_acts.expiration_date', '>=', date('Y-m-d H:i:s'))
                    ->orWhereNull('object_contests_acts.expiration_date');
            })
            ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
            ->whereHas('contraent_choice', function ($query) {
                $query->where('id', '=', 14);
            })
            ->with(['structure' => function ($query) {
                $query->select(['id', 'structure_name']);
            }])
            ->contestactsfilter(
                (int)Input::get('str', true),
                (Input::get('obj', true)),
                null,
                (Input::get('_cig', true)),
                Input::get('s', true),
                Input::get('e', true),
                Input::get('sec', true)
            )
            ->groupBy('object_contests_acts.id')
            ->paginate(15, ['object_contests_acts.id', 'object_contests_acts.object', 'object_contests_acts.activation_date', 'object_contests_acts.sector',
                'object_contests_acts.cig', 'object_contests_acts.typology', 'object_contests_acts.object_structures_id'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get(['str', 'obj', '_cig', 's', 'e', 'sec', 'sec_token']))
            ->setPath(currentUrl());

        $contestActs = !empty($contestActs) ? $contestActs->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        $table = null;

        if (!empty($contestActs['data'])) {

            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ContestsActsModel::select(['created_at'])
                ->where('object_contests_acts.typology', 'foster')
                ->where(function ($query) {
                    $query->where('object_contests_acts.expiration_date', '>=', date('Y-m-d H:i:s'))
                        ->orWhereNull('object_contests_acts.expiration_date');
                })
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->whereHas('contraent_choice', function ($query) {
                    $query->where('id', '=', 14);
                })
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ContestsActsModel::select(['updated_at'])
                ->where('object_contests_acts.typology', 'foster')
                ->where(function ($query) {
                    $query->where('object_contests_acts.expiration_date', '>=', date('Y-m-d H:i:s'))
                        ->orWhereNull('object_contests_acts.expiration_date');
                })
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->whereHas('contraent_choice', function ($query) {
                    $query->where('id', '=', 14);
                })
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();

            $table->set_heading('Oggetto', 'Settore', 'CIG', 'Struttura Competente', 'Data di pubblicazione');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($contestActs['data'] as $contestAct) {

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $contestAct['id'] . '/' . urlTitle($contestAct['object'])) . '" data-id="' . $contestAct['id'] . ' ">' . escapeXss($contestAct['object']) . '</a>',
                    !empty($contestAct['sector']) ? config('sector', null, 'noticeActs')[$contestAct['sector']] : '',
                    !empty($contestAct['cig']) ? $contestAct['cig'] : '',
                    !empty($contestAct['structure']['structure_name'])
                        ? '<a href="' . siteUrl('page/40/details/' . $contestAct['structure']['id'] . '/' . urlTitle($contestAct['structure']['structure_name']))
                        . '" data-id="' . $contestAct['object_structures_id'] . ' ">' . escapeXss($contestAct['structure']['structure_name']) . '</a>'
                        : null,
                    !empty($contestAct['activation_date']) ?
                        date('d/m/Y', strtotime($contestAct['activation_date']))
                        : null
                );
            }
        }

        // Filtro per strutture
        $flagArchived = false;

        $query = StructuresModel::query();

        if (!$flagArchived) {
            $query->where(function ($query) {
            });
        }
        $structures = [null => 'Seleziona una struttura'] + $query
                ->orderBy('structure_name', 'ASC')
                ->pluck('structure_name', 'id')
                ->toArray();

        // Filtro per scelta del contraente
        $sectors = [null => 'Qualunque'] + $noticeSectors;

        // Form per i filtri di ricerca
        $form = json_encode([
            [
                'type' => 'form_open',
                'name' => 'contest_acts',
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
                'prefix' => '<div class="select-wrapper col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{structures}',
                'name' => 'str',
                'extra' => 'id="str"',
                'label' => [
                    'for' => 'str',
                    'text' => 'Struttura da cercare',
                ]
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
                'options' => '{sector}',
                'name' => 'sec',
                'extra' => 'id="sec"',
                'label' => [
                    'for' => 'sec',
                    'text' => 'Settore',
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
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => '_cig',
                    'value' => '{cig}',
                    'id' => '_cig',

                ],
                'label' => [
                    'for' => '_cig',
                    'text' => 'Cig',
                ]
            ],
            [
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 's',
                    'value' => '{start_p_date}',
                    'type' => 'date',
                    'id' => 's',

                ],
                'label' => [
                    'for' => 's',
                    'text' => 'Data di scadenza - dal',
                    'class' => 'active'
                ]
            ],
            [
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'e',
                    'value' => '{end_p_date}',
                    'type' => 'date',
                    'id' => 'e',

                ],
                'label' => [
                    'for' => 'e',
                    'text' => 'Data di scadenza - al',
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
            'str' => Input::get('str'),
            'obj' => Input::get('obj'),
            'c' => Input::get('c'),
            '_cig' => Input::get('_cig'),
            's' => Input::get('s'),
            'e' => Input::get('e')
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
            'structures' => FormBuilder::parseOptions($structures, htmlEscape(Input::get('str', true))),
            'object' => htmlEscape(Input::get('obj', true)),
            'sector' => FormBuilder::parseOptions($sectors, htmlEscape(Input::get('sec', true))),
            'cig' => htmlEscape(Input::get('_cig', true)),
            'start_p_date' => htmlEscape(Input::get('s', true)),
            'end_p_date' => htmlEscape(Input::get('e', true)),
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $contestActs;

        // Richiesta del solo frammento (paginazione AJAX): tabella e paginazione sono pronte,
        // tutto il resto serve solo alla pagina completa e viene saltato.
        if (renderFrontFragment($data)) {

            return;

        }

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
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

        renderFront(config('vfo', null, 'app') . '/contests_acts/contests_acts', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Procedure negoziate afferenti agli investimenti pubblici finanziati, in tutto o in parte,
     * con le risorse previste dal PNRR e dal PNC e dai programmi cofinanziati dai fondi strutturali dell'Unione europea",
     * nella sezione dei Bandi di gara e contratti
     * ID sezione 526
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexPnrAndPncAndEuropeanFinancing(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        $noticeSectors = config('sector', null, 'noticeActs');

        // Validazione dei campi d'input per la ricerca
        $validator = new Validator();
        if (Input::get('str') != null ||
            Input::get('obj') != null ||
            Input::get('sec') != null ||
            Input::get('_cig') != null ||
            Input::get('s') != null ||
            Input::get('e') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Struttura')->value(Input::get('str'))->isInt()->end();
        $validator->label('Oggetto')->value(Input::get('obj'))->maxLength(30)->end();
        $validator->label('Settore')->value(Input::get('sec'))->in(implode(',', array_keys($noticeSectors)))->end();
        $validator->label('Cig')->value(Input::get('_cig'))->maxLength(12)->end();
        $validator->label('Data di pubblicazione dal')->value(Input::get('s'))->isDate('Y-m-d')->end();
        $validator->label('Data di pubblicazione al')->value(Input::get('e'))->isDate('Y-m-d')->end();

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
        $contestActs = ContestsActsModel::where(function ($query) {
            $query->where('object_contests_acts.expiration_date', '>=', date('Y-m-d H:i:s'))
                ->orWhereNull('object_contests_acts.expiration_date');
        })
            ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
            ->whereHas('public_in_section', function ($query) use ($currentPageId) {
                $query->where('public_in_id', $currentPageId);
            })
            ->with(['structure' => function ($query) {
                $query->select(['id', 'structure_name']);
            }])
            ->contestactsfilter(
                (int)Input::get('str', true),
                (Input::get('obj', true)),
                null,
                (Input::get('_cig', true)),
                Input::get('s', true),
                Input::get('e', true),
                Input::get('sec', true)
            )
            ->groupBy('object_contests_acts.id')
            ->paginate(15, ['object_contests_acts.id', 'object_contests_acts.object', 'object_contests_acts.activation_date', 'object_contests_acts.sector',
                'object_contests_acts.cig', 'object_contests_acts.typology', 'object_contests_acts.object_structures_id'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get(['str', 'obj', '_cig', 's', 'e', 'sec', 'sec_token']))
            ->setPath(currentUrl());

        $contestActs = !empty($contestActs) ? $contestActs->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        $table = null;

        if (!empty($contestActs['data'])) {

            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ContestsActsModel::select(['created_at'])
                ->where(function ($query) {
                    $query->where('object_contests_acts.expiration_date', '>=', date('Y-m-d H:i:s'))
                        ->orWhereNull('object_contests_acts.expiration_date');
                })
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->whereHas('public_in_section', function ($query) use ($currentPageId) {
                    $query->where('public_in_id', $currentPageId);
                })
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ContestsActsModel::select(['updated_at'])
                ->where(function ($query) {
                    $query->where('object_contests_acts.expiration_date', '>=', date('Y-m-d H:i:s'))
                        ->orWhereNull('object_contests_acts.expiration_date');
                })
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->whereHas('public_in_section', function ($query) use ($currentPageId) {
                    $query->where('public_in_id', $currentPageId);
                })
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();

            $table->set_heading('Oggetto', 'Settore', 'CIG', 'Struttura Competente', 'Data di pubblicazione');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($contestActs['data'] as $contestAct) {

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $contestAct['id'] . '/' . urlTitle($contestAct['object'])) . '" data-id="' . $contestAct['id'] . ' ">' . escapeXss($contestAct['object']) . '</a>',
                    !empty($contestAct['sector']) ? config('sector', null, 'noticeActs')[$contestAct['sector']] : '',
                    !empty($contestAct['cig']) ? $contestAct['cig'] : '',
                    !empty($contestAct['structure']['structure_name'])
                        ? '<a href="' . siteUrl('page/40/details/' . $contestAct['structure']['id'] . '/' . urlTitle($contestAct['structure']['structure_name'])) . '" data-id="' . $contestAct['object_structures_id'] . ' ">' . escapeXss($contestAct['structure']['structure_name']) . '</a>'
                        : null,
                    !empty($contestAct['activation_date']) ?
                        date('d/m/Y', strtotime($contestAct['activation_date']))
                        : null
                );
            }
        }

        // Filtro per strutture
        $flagArchived = false;

        $query = StructuresModel::query();

        if (!$flagArchived) {
            $query->where(function ($query) {
            });
        }
        $structures = [null => 'Seleziona una struttura'] + $query
                ->orderBy('structure_name', 'ASC')
                ->pluck('structure_name', 'id')
                ->toArray();

        // Filtro per scelta del contraente
        $sectors = [null => 'Qualunque'] + $noticeSectors;

        // Form per i filtri di ricerca
        $form = json_encode([
            [
                'type' => 'form_open',
                'name' => 'contest_acts',
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
                'prefix' => '<div class="select-wrapper col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{structures}',
                'name' => 'str',
                'extra' => 'id="str"',
                'label' => [
                    'for' => 'str',
                    'text' => 'Struttura da cercare',
                ]
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
                'options' => '{sector}',
                'name' => 'sec',
                'extra' => 'id="sec"',
                'label' => [
                    'for' => 'sec',
                    'text' => 'Settore',
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
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => '_cig',
                    'value' => '{cig}',
                    'id' => '_cig',

                ],
                'label' => [
                    'for' => '_cig',
                    'text' => 'Cig',
                ]
            ],
            [
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 's',
                    'value' => '{start_p_date}',
                    'type' => 'date',
                    'id' => 's',

                ],
                'label' => [
                    'for' => 's',
                    'text' => 'Data di scadenza - dal',
                    'class' => 'active'
                ]
            ],
            [
                'prefix' => '<div class="form-group col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'e',
                    'value' => '{end_p_date}',
                    'type' => 'date',
                    'id' => 'e',

                ],
                'label' => [
                    'for' => 'e',
                    'text' => 'Data di scadenza - al',
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
            'str' => Input::get('str'),
            'obj' => Input::get('obj'),
            'c' => Input::get('c'),
            '_cig' => Input::get('_cig'),
            's' => Input::get('s'),
            'e' => Input::get('e')
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
            'structures' => FormBuilder::parseOptions($structures, htmlEscape(Input::get('str', true))),
            'object' => htmlEscape(Input::get('obj', true)),
            'sector' => FormBuilder::parseOptions($sectors, htmlEscape(Input::get('sec', true))),
            'cig' => htmlEscape(Input::get('_cig', true)),
            'start_p_date' => htmlEscape(Input::get('s', true)),
            'end_p_date' => htmlEscape(Input::get('e', true)),
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $contestActs;

        // Richiesta del solo frammento (paginazione AJAX): tabella e paginazione sono pronte,
        // tutto il resto serve solo alla pagina completa e viene saltato.
        if (renderFrontFragment($data)) {

            return;

        }

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
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

        renderFront(config('vfo', null, 'app') . '/contests_acts/contests_acts', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Avvisi relativi all'esito della procedura",
     * nella sezione dei Bandi di gara e contratti
     * ID sezione 575
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexResultNoticesAlert(): void
    {
        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);
        $this->alertPna($currentPageId);

    }

    /**
     * Metodo chiamato per la pagina "Avviso sui risultati della procedura di affidamento diretto
     * (ove la determina a contrarre o atto equivalente sia adottato entro il 30.6.2023)",
     * nella sezione dei Bandi di gara e contratti
     * ID sezione 576
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexResultDirectFosterAlert(): void
    {
        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);
        $this->alertPna($currentPageId);

    }

    /**
     * Metodo chiamato per la pagina "Avviso di avvio della procedura e avviso sui risultati della aggiudicazione di
     * procedure negoziate senza bando (ove la determina a contrarre o atto equivalente sia adottato entro il 30.6.2023)",
     * nella sezione dei Bandi di gara e contratti
     * ID sezione 576
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexResultNegotiatedProceduresAlert(): void
    {
        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);
        $this->alertPna($currentPageId);

    }

    /**
     * Funzione utilizzata per le sezioni del PNA relative agli Avvisi:
     * "Avvisi relativi all'esito della procedura"
     * "Avviso sui risultati della procedura di affidamento diretto (ove la determina a contrarre o atto equivalente sia adottato entro il 30.6.2023)"
     * @param int|null $currentPageId Id della pagina
     * @return void
     * @throws Exception
     */
    private function alertPna(int $currentPageId = null): void
    {
        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        $alertSectors = config('sectorAlert', null, 'noticeActs');

        // Validazione dei campi di input per la ricerca
        $validator = new Validator();
        if (Input::get('str') != null ||
            Input::get('obj') != null ||
            Input::get('_cig') != null ||
            Input::get('s') != null ||
            Input::get('e') != null ||
            Input::get('sec') != null
        ) {
            $validator->verifyToken();
        }
        $validator->label('Struttura')->value(Input::get('str'))->isInt()->end();
        $validator->label('Oggetto')->value(Input::get('obj'))->maxLength(30)->end();
        $validator->label('Settore')->value(Input::get('sec'))->in(implode(',', array_keys($alertSectors)))->end();
        $validator->label('Cig')->value(Input::get('_cig'))->maxLength(12)->end();
        $validator->label('Data di pubblicazione dal')->value(Input::get('s'))->isDate('Y-m-d')->end();
        $validator->label('Data di pubblicazione al')->value(Input::get('e'))->isDate('Y-m-d')->end();

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

        // Recupero i bandi da mostrare in base al criterio di pubblicazione della pagina
        $contestActs = ContestsActsModel::where('object_contests_acts.typology', 'alert')
            ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
            ->whereHas('public_in_section', function ($query) use ($currentPageId) {
                $query->where('public_in_id', $currentPageId);
            })
            ->with(['structure' => function ($query) {
                $query->select(['id', 'structure_name']);
            }])
            ->contestactsfilter(
                (int)Input::get('str', true),
                (Input::get('obj', true)),
                null,
                (Input::get('_cig', true)),
                Input::get('s', true),
                Input::get('e', true),
                Input::get('sec', true)
            )
            ->paginate(15, ['object_contests_acts.id', 'object_contests_acts.object', 'object_contests_acts.activation_date',
                'object_contests_acts.sector', 'object_contests_acts.cig', 'object_contests_acts.object_structures_id'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get(['str', 'obj', 'c', '_cig', 's', 'e', 'sec', 'sec_token'], true))
            ->setPath(currentUrl());

        $contestActs = !empty($contestActs) ? $contestActs->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($contestActs['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ContestsActsModel::select(['created_at'])
                ->where('object_contests_acts.typology', 'alert')
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
                ->whereHas('public_in_section', function ($query) use ($currentPageId) {
                    $query->where('public_in_id', $currentPageId);
                })
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ContestsActsModel::select(['updated_at'])
                ->where('object_contests_acts.typology', 'alert')
                ->where('object_contests_acts.activation_date', '<=', date("Y-m-d H:i:s", strtotime('now')))
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

        $table = null;

        if (!empty($contestActs['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();

            $table->set_heading('Oggetto', 'Settore', 'CIG', 'Struttura Competente', 'Data di pubblicazione');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($contestActs['data'] as $contestAct) {

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $contestAct['id'] . '/' . urlTitle($contestAct['object'])) . '" data-id="' . $contestAct['id'] . ' ">' . escapeXss($contestAct['object']) . '</a>',
                    !empty($contestAct['sector']) ? config('sector', null, 'noticeActs')[$contestAct['sector']] : '',
                    !empty($contestAct['cig']) ? $contestAct['cig'] : '',
                    !empty($contestAct['structure']['structure_name'])
                        ? '<a href="' . siteUrl('page/40/details/' . $contestAct['structure']['id'] . '/' . urlTitle($contestAct['structure']['structure_name']))
                        . '" data-id="' . $contestAct['object_structures_id'] . ' ">' . escapeXss($contestAct['structure']['structure_name']) . '</a>'
                        : null,
                    !empty($contestAct['activation_date']) ?
                        date('d/m/Y', strtotime($contestAct['activation_date']))
                        : null
                );
            }
        }

        // Filtro per strutture
        $structures = [null => 'Seleziona una struttura'] + StructuresModel::where(function ($query) {
            })
                ->orderBy('structure_name', 'ASC')
                ->pluck('structure_name', 'id')
                ->toArray();

        // Filtro per scelta del contraente
        $sectors = [null => 'Qualunque'] + $alertSectors;

        // Form per i filtri di ricerca
        $form = json_encode([
            [
                'type' => 'form_open',
                'name' => 'contest_acts',
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
                'prefix' => '<div class="select-wrapper col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{structures}',
                'name' => 'str',
                'extra' => 'id="str"',
                'label' => [
                    'for' => 'str',
                    'text' => 'Struttura da cercare',
                ]
            ],
            [
                'prefix' => '<div class="col-lg-4">',
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
                'options' => '{sector}',
                'name' => 'sec',
                'extra' => 'id="sec"',
                'label' => [
                    'for' => 'sec',
                    'text' => 'Settore',
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
                'prefix' => '<div class="col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => '_cig',
                    'value' => '{cig}',
                    'id' => '_cig',

                ],
                'label' => [
                    'for' => '_cig',
                    'text' => 'Cig',
                ]
            ],
            [
                'prefix' => '<div class="col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 's',
                    'value' => '{start_p_date}',
                    'type' => 'date',
                    'id' => 's',

                ],
                'label' => [
                    'for' => 's',
                    'text' => 'Data di scadenza - dal',
                    'class' => 'active'
                ]
            ],
            [
                'prefix' => '<div class="col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'e',
                    'value' => '{end_p_date}',
                    'type' => 'date',
                    'id' => 'e',

                ],
                'label' => [
                    'for' => 'e',
                    'text' => 'Data di scadenza - al',
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
            'str' => Input::get('str'),
            'obj' => Input::get('obj'),
            'c' => Input::get('c'),
            '_cig' => Input::get('_cig'),
            's' => Input::get('s'),
            'e' => Input::get('e'),
            'sec' => Input::get('sec')
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
            'structures' => FormBuilder::parseOptions($structures, htmlEscape(Input::get('str', true))),
            'object' => htmlEscape(Input::get('obj', true)),
            'sector' => FormBuilder::parseOptions($sectors, htmlEscape(Input::get('sec', true))),
            'cig' => htmlEscape(Input::get('_cig', true)),
            'start_p_date' => htmlEscape(Input::get('s', true)),
            'end_p_date' => htmlEscape(Input::get('e', true)),
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $contestActs;

        // Richiesta del solo frammento (paginazione AJAX): tabella e paginazione sono pronte,
        // tutto il resto serve solo alla pagina completa e viene saltato.
        if (renderFrontFragment($data)) {

            return;

        }

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
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

        renderFront(config('vfo', null, 'app') . '/contests_acts/contests_acts', $data, 'frontend');
    }

    /**
     * Funzione che ritorna i dati da mostrare nella pagina di dettaglio
     *
     * @return void
     * @throws Exception
     */
    public function details(): void
    {

        $elementId = (int)uri()->segment(4, 0);

        // Recupero l'elemento da mostrare
        $element = ContestsActsModel::select(['id', 'typology', 'activation_date']);

        $element = $element->where('id', $elementId)
            ->first();

        // Se l'elemento non esiste mostro la pagina di errore
        if (empty($element)) {
            echo show404('Ops..', 'record not found');
            exit();
        }

        $element = $element->toArray();

        // Se è presente il parametro in get, allora renderizzo la pagina Informazioni d'indicizzazione
        if (Input::get('st', true)) {
            $this->indexingInformation();
        } else { //Altrimenti chiamo il metodo details per lo specifico tipo di bando
            $func = $element['typology'] . 'Details';
            if (!method_exists($this, $func)) {
                echo showError('Metodo non trovato');
                die();
            }
            $this->$func();
        }
    }

    /**
     * Metodo per Informazioni d'indicizzazione
     * @return void
     * @throws Exception
     */
    private function indexingInformation(): void
    {
        $data = [];

        $elementId = (int)uri()->segment(4, 0);

        // Recupero l'elemento da mostrare
        $element = ContestsActsModel::where('id', $elementId)
            ->with(['relative_lots' => function ($query) {
                $query->select(['relative_notice_id', 'id', 'object', 'asta_base_value', 'cig']);
            }])
            ->with('requirements:id,denomination,code')
            ->first();

        $element = !empty($element) ? $element->toArray() : [];
        $isMulticig = (bool)$element['is_multicig'];

        $astaValueSum = ($isMulticig) ? collect(Arr::pluck($element['relative_lots'], 'asta_base_value'))
            ->map(function ($items) {
                return (float)S::currency($items, 2, null, null, false);
            })->sum() : $element['asta_base_value'];
        $astaValueSum = S::currency((string)$astaValueSum, 2, ',', '.');

        $administrationType = ['' => '', 1 => 'Regioni', 2 => 'Provincie', 3 => 'Comuni', 4 => 'Università', 5 => 'Ministeri', 6 => 'Organi istituzionali', 7 => 'Altri soggetti pubblici e privati'];
        $contractType = ['' => '', 1 => 'Lavori', 2 => 'Servizi', 3 => 'Forniture'];

        $data['element'] = $element;

        $requirements = [];
        if (!empty($element['requirements'])) {
            foreach ($element['requirements'] as $requirement) {
                $requirements [] = $requirement['denomination'];
            }
        }

        // Informazioni per la tabella d'indicizzazione delle informazioni
        $data['headers'] = [
            'Tipo' => !empty($element['type']) ? escapeXss($element['type']) : '',
            'Contratto' => !empty($element['contract']) ? escapeXss($contractType[$element['contract']]) : '',
            'Denominazione dell\'amministrazione aggiudicatrice' => !empty($element['adjudicator_name']) ? escapeXss($element['adjudicator_name']) : '',
            'Tipo di amministrazione' => !empty($element['administration_type'] && !empty($administrationType[$element['administration_type']])) ? escapeXss($administrationType[$element['administration_type']]) : '',
            'Provincia sede di gara' => !empty($element['province_office']) ? escapeXss($element['province_office']) : '',
            'Comune sede di gara' => !empty($element['municipality_office']) ? escapeXss($element['municipality_office']) : '',
            'Indirizzo sede di gara' => !empty($element['office_address']) ? escapeXss($element['office_address']) : '',
            'Senza importo' => !empty($element['no_amount']) ? (escapeXss($element['no_amount']) == 1 ? 'NO' : 'SI') : '',
            'Valore importo dell\'appalto' => !empty($astaValueSum) ? '€&nbsp;' . $astaValueSum : '',
            'Valore importo di aggiudicazione' => !empty($element['award_amount_value']) ? '€&nbsp;' . S::currency($element['award_amount_value'], 2, ',', '.') : null,
            'Data pubblicazione' => !empty($element['activation_date']) ? date('d/m/Y', strtotime($element['activation_date'])) : null,
            'Data scadenza bando' => !empty($element['expiration_date']) ? date('d/m/Y', strtotime($element['expiration_date'])) : null,
            'Data scadenza pubblicazione esito' => null,
            'Requisiti di qualificazione' => !empty($requirements) ? implode(' | ', $requirements) : null,
            'Codice cpv' => !empty($element['cpv_code_id']) ? escapeXss($element['cpv_code_id']) : '',
            'Codice scp' => !empty($element['codice_scp']) ? escapeXss($element['codice_scp']) : '',
            'Url di pubblicazione su www.Serviziocontrattipubblici.It' => !empty($element['url_scp']) ? escapeXss($element['url_scp']) : '',
            'Codice CIG' => !empty($element['relative_lots']) ? implode(', ', Arr::pluck($element['relative_lots'], 'cig')) : $element['cig'],
        ];

        $data['_meta_page'] = Meta::getInstance()
            ->setDcterms(Meta::dctermsTitle, 'Informazioni d\'indicizzazione')
            ->tagTitle('Portale Trasparenza ' . patOsInstituteInfo(['full_name_institution'])['full_name_institution'] . ' - Informazioni d\'indicizzazione')
            ->toHtml();

        renderFront(config('vfo', null, 'app') . '/contests_acts/indexing_information', $data, 'frontend');
    }

    /**
     * Funzione che ritorna i dati da mostrare nella pagina di dettaglio di una delibera
     *
     * @return void
     * @throws Exception
     * @noinspection PhpUnusedPrivateMethodInspection
     */
    private function deliberationDetails(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);
        $elementId = (int)uri()->segment(4, 0);

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'id', 'parent_id', 'archive_name'])
            ->where('id', $currentPageId)
            ->first()
            ->toArray();

        // Allegati
        $attach = new AttachmentArchive();

        // Recupero l'elemento da mostrare
        $element = ContestsActsModel::where('id', $elementId)
            ->with('proceedings:id,object')
            ->with(['structure' => function ($query) {
                $query->select(['id', 'structure_name']);
            }])
            ->with(['rup' => function ($query) {
                $query->select(['id', 'full_name']);
            }])
            ->with(['relative_measure' => function ($query) {
                $query->select(['id', 'object']);
            }]);


        $element = $element->first();

        $element = !empty($element) ? $element->toArray() : [];

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
        $data['hasDifferentType'] = 'deliberation';

        //Allegati
            $data['listAttach'] = $attach->getAllByObject(
                'contest_acts',
                $element['id'],
                ['*'],
                true
            );

        renderFront(config('vfo', null, 'app') . '/contests_acts/deliberation_details', $data, 'frontend');
    }

    /**
     * Funzione che ritorna i dati da mostrare nella pagina di dettaglio di un esito di gara
     *
     * @return void
     * @throws Exception
     * @noinspection PhpUnusedPrivateMethodInspection
     */
    private function resultDetails(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);
        $elementId = (int)uri()->segment(4, 0);

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'id', 'parent_id', 'archive_name'])
            ->where('id', $currentPageId)
            ->first()
            ->toArray();

        // Allegati
        $attach = new AttachmentArchive();

        // Recupero l'elemento da mostrare
        $element = ContestsActsModel::where('id', $elementId)
            ->with('participants:id,name,vat')
            ->with('awardees:id,name,vat')
            ->with('proceedings:id,object')
            ->with(['notice_acts' => function ($query) {
                $query->select(['object_contests_acts_id', 'id', 'object']);
            }])
            ->with(['relative_measure' => function ($query) {
                $query->select(['id', 'object']);
            }])
            ->with(['relative_notice' => function ($query) {
                $query->select(['object_contests_acts.id', 'object', 'cig', 'adjudicator_name', 'adjudicator_data', 'expiration_date',
                    'relative_notice_id', 'contraent_choice', 'object_structures_id', 'object_personnel_id']);
                $query->with('contraent_choice:id,name');
                $query->with('structure:id,structure_name');
                $query->with('rup:id,full_name');
            }])
            ->with('relative_liquidation:relative_procedure_id,id,object,anac_year,amount_liquidated');


        $element = $element->first();

        $element = !empty($element) ? $element->toArray() : [];
        
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
        $data['hasDifferentType'] = 'result';

            $data['listAttach'] = $attach->getAllByObject(
                'contest_acts',
                $element['id'],
                ['*'],
                true
            );

        renderFront(config('vfo', null, 'app') . '/contests_acts/result_details', $data, 'frontend');
    }

    /**
     * Funzione che ritorna i dati da mostrare nella pagina di dettaglio di un bando di gara
     *
     * @return void
     * @throws Exception
     * @noinspection PhpUnusedPrivateMethodInspection
     */
    private function noticeDetails(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);
        $elementId = (int)uri()->segment(4, 0);

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'id', 'parent_id', 'archive_name'])
            ->where('id', $currentPageId)
            ->first()
            ->toArray();

        // Allegati
        $attach = new AttachmentArchive();

        // Recupero l'elemento da mostrare
        $element = ContestsActsModel::where('id', $elementId)
            ->with(['contraent_choice' => function ($query) {
                $query->select(['id', 'name']);
            }])
            ->with('proceedings:id,object')
            ->with(['relative_measure' => function ($query) {
                $query->select(['id', 'object']);
            }])
            ->with('relative_deliberation:id,object')
            ->with(['relative_results' => function ($query) {
                $query->select(['relative_notice_id', 'id', 'object']);
            }])
            ->with(['relative_alerts' => function ($query) {
                $query->select(['relative_notice_id', 'id', 'object']);
            }])
            ->with(['notice_acts' => function ($query) {
                $query->select(['object_contests_acts_id', 'id', 'object']);
            }])
            ->with(['measures' => function ($query) {
                $query->select(['object_contests_acts_id', 'id', 'object']);
            }])
            ->with(['structure' => function ($query) {
                $query->select(['id', 'structure_name']);
            }])
            ->with(['relative_foster' => function ($query) {
                $query->select(['relative_procedure_id', 'id', 'object']);
            }])
            ->with(['rup' => function ($query) {
                $query->select(['id', 'full_name']);
            }])
            ->with(['relative_lots' => function ($query) {
                $query->select(['relative_notice_id', 'id', 'object', 'asta_base_value', 'cig']);
            }]);


        $element = $element->first();

        $element = !empty($element) ? $element->toArray() : [];
        $isMulticig = (bool)$element['is_multicig'];

        $astaValueSum = ($isMulticig) ? collect(Arr::pluck($element['relative_lots'], 'asta_base_value'))
            ->map(function ($items) {
                return (float)S::currency($items, 2, null, null, false);
            })->sum() : $element['asta_base_value'];
        $astaValueSum = S::currency((string)$astaValueSum, 2, ',', '.');

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
        $data['astaValueSum'] = $astaValueSum;
        $data['currentPageId'] = $currentPageId;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['editRecord'] = true;
        $data['hasDifferentType'] = 'notice';

        //Allegati
            $data['listAttach'] = $attach->getAllByObject(
                'contest_acts',
                $element['id'],
                ['*'],
                true
            );

        renderFront(config('vfo', null, 'app') . '/contests_acts/notice_details', $data, 'frontend');
    }

    /**
     * Funzione che ritorna i dati da mostrare nella pagina di dettaglio di un esito affidamento
     *
     * @return void
     * @throws Exception
     * @noinspection PhpUnusedPrivateMethodInspection
     */
    private function fosterDetails(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);
        $elementId = (int)uri()->segment(4, 0);

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'id', 'parent_id', 'archive_name'])
            ->where('id', $currentPageId)
            ->first()
            ->toArray();

        // Allegati
        $attach = new AttachmentArchive();

        // Recupero l'elemento da mostrare
        $element = ContestsActsModel::where('id', $elementId)
            ->with(['contraent_choice' => function ($query) {
                $query->select(['id', 'name']);
            }])
            ->with('participants:id,name,vat')
            ->with('awardees:id,name,vat')
            ->with('other_proceedings:id,object')
            ->with('relative_deliberation:id,object')
            ->with(['notice_acts' => function ($query) {
                $query->select(['object_contests_acts_id', 'id', 'object']);
            }])
            ->with(['measures' => function ($query) {
                $query->select(['object_contests_acts_id', 'id', 'object']);
            }])
            ->with(['structure' => function ($query) {
                $query->select(['id', 'structure_name']);
            }])
            ->with(['relative_procedure' => function ($query) {
                $query->select(['id', 'object']);
            }])
            ->with(['relative_measure' => function ($query) {
                $query->select(['id', 'object']);
            }])
            ->with(['relative_foster' => function ($query) {
                $query->select(['relative_procedure_id', 'id', 'object']);
            }])
            ->with(['relative_liquidation' => function ($query) {
                $query->select(['relative_procedure_id', 'id', 'object', 'anac_year', 'amount_liquidated']);
            }])
            ->with('rup:id,full_name');


        $element = $element->first();

        $element = !empty($element) ? $element->toArray() : [];

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
        $data['hasDifferentType'] = 'foster';

        //Allegati
            $data['listAttach'] = $attach->getAllByObject(
                'contest_acts',
                $element['id'],
                ['*'],
                true
            );

        renderFront(config('vfo', null, 'app') . '/contests_acts/foster_details', $data, 'frontend');
    }

    /**
     * Funzione che ritorna i dati da mostrare nella pagina di dettaglio di un avviso
     *
     * @return void
     * @throws Exception
     * @noinspection PhpUnusedPrivateMethodInspection
     */
    private function alertDetails(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);
        $elementId = (int)uri()->segment(4, 0);

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'id', 'parent_id', 'archive_name'])
            ->where('id', $currentPageId)
            ->first()
            ->toArray();

        // Allegati
        $attach = new AttachmentArchive();

        // Recupero l'elemento da mostrare
        $element = ContestsActsModel::where('id', $elementId)
            ->with('proceedings:id,object')
            ->with('other_proceedings:id,object')
            ->with('relative_deliberation:id,object')
            ->with(['notice_acts' => function ($query) {
                $query->select(['object_contests_acts_id', 'id', 'object']);
            }])
            ->with(['structure' => function ($query) {
                $query->select(['id', 'structure_name']);
            }])
            ->with(['relative_notice' => function ($query) {
                $query->select(['object_contests_acts.id', 'object', 'cig', 'expiration_date']);
            }])
            ->with(['relative_foster' => function ($query) {
                $query->select(['relative_procedure_id', 'id', 'object']);
            }])
            ->with(['rup' => function ($query) {
                $query->select(['id', 'full_name']);
            }])
            ->with(['relative_measure' => function ($query) {
                $query->select(['id', 'object']);
            }]);


        $element = $element->first();

        $element = !empty($element) ? $element->toArray() : [];

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
        $data['hasDifferentType'] = 'alert';

        //Allegati
            $data['listAttach'] = $attach->getAllByObject(
                'contest_acts',
                $element['id'],
                ['*'],
                true
            );

        renderFront(config('vfo', null, 'app') . '/contests_acts/alert_details', $data, 'frontend');
    }

    /**
     * Funzione che ritorna i dati da mostrare nella pagina di dettaglio di un lotto
     *
     * @return void
     * @throws Exception
     * @noinspection PhpUnusedPrivateMethodInspection
     */
    private function lotDetails(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);
        $elementId = (int)uri()->segment(4, 0);

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'id', 'parent_id', 'archive_name'])
            ->where('id', $currentPageId)
            ->first()
            ->toArray();

        // Allegati
        $attach = new AttachmentArchive();

        // Recupero l'elemento da mostrare
        $element = ContestsActsModel::where('id', $elementId)
            ->with(['relative_procedure' => function ($query) {
                $query->select(['id', 'object']);
            }])
            ->with(['relative_notice' => function ($query) {
                $query->select(['object_contests_acts.id', 'object', 'cig', 'adjudicator_name', 'adjudicator_data', 'relative_notice_id',
                    'contraent_choice', 'object_structures_id']);
                $query->with(['contraent_choice' => function ($query) {
                    $query->select(['id', 'name']);
                }]);
                $query->with(['structure' => function ($query) {
                    $query->select(['id', 'structure_name']);
                }]);
            }]);


        $element = $element->first();

        $element = !empty($element) ? $element->toArray() : [];

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
        $data['hasDifferentType'] = 'lot';

        //Allegati
            $data['listAttach'] = $attach->getAllByObject(
                'contest_acts',
                $element['id'],
                ['*'],
                true
            );

        renderFront(config('vfo', null, 'app') . '/contests_acts/lot_details', $data, 'frontend');
    }

    /**
     * Funzione che ritorna i dati da mostrare nella pagina di dettaglio di una liquidazione
     *
     * @return void
     * @throws Exception
     * @noinspection PhpUnusedPrivateMethodInspection
     */
    private function liquidationDetails(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);
        $elementId = (int)uri()->segment(4, 0);

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'id', 'parent_id', 'archive_name'])
            ->where('id', $currentPageId)
            ->first()
            ->toArray();

        // Recupero l'elemento da mostrare
        $element = ContestsActsModel::where('id', $elementId)
            ->with(['relative_procedure' => function ($query) {
                $query->select(['id', 'object', 'cig']);
                $query->select(['object_contests_acts.id', 'object', 'cig', 'relative_procedure_id', 'relative_notice_id', 'object_structures_id']);
                $query->with(['structure' => function ($query) {
                    $query->select(['id', 'structure_name']);
                }]);
                $query->with(['relative_notice' => function ($query) {
                    $query->select(['object_contests_acts.id', 'object', 'cig', 'relative_notice_id', 'object_structures_id']);
                    $query->with(['structure' => function ($query) {
                        $query->select(['id', 'structure_name']);
                    }]);
                }]);
            }])
            ->with('relative_procedure_awardees:id,name');


        $element = $element->first();

        $element = !empty($element) ? $element->toArray() : [];

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
        $data['hasDifferentType'] = 'liquidation';

        $elementId = $element['id'];

        //Allegati
            // Allegati
            $attach = new AttachmentArchive();
            $data['listAttach'] = $attach->getAllByObject(
                'contest_acts',
                $element['id'],
                ['*'],
                true
            );

        renderFront(config('vfo', null, 'app') . '/contests_acts/liquidation_details', $data, 'frontend');
    }

    /**
     * Funzione che ritorna i dati da mostrare nella pagina di dettaglio di un avviso
     *
     * @return void
     * @throws Exception
     * @noinspection PhpUnusedPrivateMethodInspection
     */
    private function variationDetails(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);
        $elementId = (int)uri()->segment(4, 0);

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'id', 'parent_id', 'archive_name'])
            ->where('id', $currentPageId)
            ->first()
            ->toArray();

        // Allegati
        $attach = new AttachmentArchive();

        // Recupero l'elemento da mostrare
        $element = ContestsActsModel::where('id', $elementId)
            ->with('proceedings:id,object');


        $element = $element->first();

        $element = !empty($element) ? $element->toArray() : [];

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
        $data['hasDifferentType'] = 'alert';

        //Allegati
            $data['listAttach'] = $attach->getAllByObject(
                'contest_acts',
                $element['id'],
                ['*'],
                true
            );

        renderFront(config('vfo', null, 'app') . '/contests_acts/variation_details', $data, 'frontend');
    }


}
