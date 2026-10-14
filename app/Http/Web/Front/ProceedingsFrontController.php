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
use Model\ProceedingsModel;
use Model\RelProceedingsStructuresModel;
use Model\SectionsFoModel;
use Model\StructuresModel;
use System\Input;
use System\Validator;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller pagina front-end Attività e procedimenti
 */
class ProceedingsFrontController extends BaseFrontController
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
     * Metodo chiamato per la pagina "Monitoraggio tempi procedimentali"
     * ID sezione 100
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexMonitoring(): void
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

        // Recupero i procedimenti(con campo Pubblica automaticamente i dati sul monitoraggio = 1) da mostrare
        $proceedings = ProceedingsModel::where('public_monitoring_proceeding', 1)
            ->where(function ($query) {
            })
            ->with('offices_responsibles:id,structure_name')
            ->with('monitoring_datas')
            ->orderBy('name', 'asc')
            ->paginate(15, ['id', 'name'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->setPath(currentUrl());

        $proceedings = !empty($proceedings) ? $proceedings->toArray() : [];

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($proceedings['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ProceedingsModel::select(['created_at'])
                ->where('public_monitoring_proceeding', 1)
                ->where(function ($query) {
                })
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ProceedingsModel::select(['updated_at'])
                ->where('public_monitoring_proceeding', 1)
                ->where(function ($query) {
                })
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = null;

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $proceedings;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = true;
        $data['allowSearch'] = false;
        $data['finalSectionId'] = 100;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        //Inclusione di eventuali librerie esterne
        $currentPage = $currentPage ?? null;
        $data = $data ?? null;
        $data = $data ?? null;

        renderFront(config('vfo', null, 'app') . '/proceedings/index_monitoring', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Tipologie di procedimento"
     * ID sezione 98
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexProceedingsType(): void
    {
        $data = [];

        // Validazione per i filtri di ricerca
        $validation = new Validator();
        if (Input::get('n') != null || Input::get('str') != null) {
            $validation->verifyToken();
        }
        $validation->label('Nome procedimento')->value(Input::get('n'))->maxLength(30)->end();
        $validation->label('Struttura di riferimento')->value(Input::get('str'))->isInt()->end();

        if (!$validation->isSuccess()) {

            echo showError('Attenzione', $validation->getErrorsHtml(), $validation->getErrorStatusCode());
            die();
        }

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Recupero i procedimenti da mostrare
        $proceedings = ProceedingsModel::where(function ($query) {
        })
            ->with('offices_responsibles:id,structure_name')
            ->filter(
                (Input::get('n', true)),
                (int)Input::get('str', true)
            )
            ->orderBy('name', 'ASC')
            ->paginate(20, ['object_proceedings.id', 'name', 'description'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get(['n', 'str', 'sec_token']))
            ->setPath(currentUrl());

        $proceedings = !empty($proceedings) ? $proceedings->toArray() : [];

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($proceedings);

        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $proceedings;

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
        if (!empty($proceedings['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(ProceedingsModel::select(['created_at'])
                ->where(function ($query) {
                })
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(ProceedingsModel::select(['updated_at'])
                ->where(function ($query) {
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
                    'name' => 'n',
                    'id' => 'n',

                    'value' => '{name}',
                ],
                'label' => [
                    'for' => 'n',
                    'text' => 'Nome procedimento',
                ],
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-6">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{structures}',
                'name' => 'str',
                'extra' => 'id="str"',
                'label' => [
                    'for' => 'str',
                    'text' => 'Struttura di riferimento',
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
            'n' => Input::get('n', true),
            'str' => Input::get('str', true)
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value) {
            if (!empty($value)) {
                $countFilterFields += 1;
            }
        }
        // Filtro per strutture
        $proceedingIds = ProceedingsModel::select(['id'])
            ->where(function ($query) {
            })
            ->get()
            ->toArray();
        $relIds = RelProceedingsStructuresModel::select(['object_structures_id'])
            ->whereIn('object_proceedings_id', $proceedingIds)
            ->where('typology', 'office-responsible')
            ->get()
            ->toArray();
        $structures = [null => 'Seleziona una struttura'] + StructuresModel::whereIn('id', $relIds)
                ->where(function ($query) {
                })
                ->orderBy('structure_name', 'ASC')
                ->pluck('structure_name', 'id')
                ->toArray();

        $formBuilder = new FormBuilder([
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'name' => htmlEscape(Input::get('n', true)),
            'structures' => FormBuilder::parseOptions($structures, htmlEscape(Input::get('str', true))),
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

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
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['rangeOpenData'] = 2000;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        //Inclusione di eventuali librerie esterne
        $currentPage = $currentPage ?? null;
        $data = $data ?? null;
        $data = $data ?? null;
        renderFront(config('vfo', null, 'app') . '/proceedings/proceedings', $data, 'frontend');
    }

    /**
     * Metodo che crea la tabella con i dati dei risultati della ricerca da mostrare
     *
     * @param $proceedings {dati da inserire nella tabella}
     * @return Table|null
     * @throws Exception
     */
    protected function createTableRows($proceedings = null): ?Table
    {
        $currentPageId = (int)uri()->segment(2, 0);
        $table = null;

        if (!empty($proceedings['data'])) {

            $hideDescription = null;

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();
            $table->set_heading('Procedimento', !empty($hideDescription) ? '' : 'Descrizione', 'Struttura di riferimento');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($proceedings['data'] as $proceeding) {

                // Creo link per le strutture
                $offices = escapeXss($proceeding['offices_responsibles'], true, false);

                $filter = function ($office) {
                    return '<a href="' . siteUrl('page/40/details/' . $office['id'] . '/' . urlTitle($office['structure_name'])) . '">' . escapeXss($office['structure_name']) . '</a>';
                };
                $linkedOffices = array_map($filter, $offices);

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $proceeding['id'] . '/' . urlTitle($proceeding['name'])) . '" data-id="' . $proceeding['id'] . ' ">' . escapeXss($proceeding['name']) . '</a>',
                    !empty($proceeding['description']) && empty($hideDescription) ? characterLimiter(strip_tags($proceeding['description'], '<br>'), 210) : null,
                    implode(', ', $linkedOffices)
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
     * @url /page/page_id/details/element_id/element_name
     */
    public function details(): void
    {
        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);
        $elementId = (int)uri()->segment(4, 0);
        $getAttachments = true;
        $archiveBread = '';

        // Recupero l'elemento da mostrare
        $element = ProceedingsModel::where('id', $elementId);

        $element = $element->with('responsibles:id,full_name,archived')
            ->with('measure_responsibles:id,full_name,archived')
            ->with('substitute_responsibles:id,full_name,archived')
            ->with('offices_responsibles', function ($query) {
                $query->select(['object_structures.id', 'object_structures.structure_name', 'object_structures.archived', 'object_structures.order'])
                    ->orderBy('object_structures.order', 'ASC')
                    ->orderBy('object_structures.structure_name', 'DESC');
            })
            ->with('final_measure_office:id,structure_name,archived')
            ->with('to_contacts:id,full_name,archived')
            ->with('other_structures:id,structure_name,archived')
            ->with('modules:id,title')
            ->with(['regulations' => function ($query) {
                $query->select(['object_regulations.id', 'title', 'public.public_in_id as public_in_id'])
                    ->join('rel_regulations_public_in as public', 'public.object_regulation_id', '=', 'object_regulations.id', 'left outer')
                    ->groupBy('public.object_regulation_id');
            }])
            ->with('normatives:id,name')
            ->with('charges:id,title')
            ->with('monitoring_datas');


        $element = $element->first();

        // Se l'elemento non esiste mostro la pagina di errore
        if (empty($element)) {
            echo show404('Ops..', 'record not found');
            exit();
        }


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

        // Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['editRecord'] = true;

        $detailPage = '/proceedings/details';

        $data = $data ?? null;
        $getAttachments = $getAttachments ?? null;
        $archiveBread = $archiveBread ?? null;
        $currentPageId = $currentPageId ?? null;

        // Aggiungi al Breadcrumbs la pagina del dettaglio
        $data['concatBreadcrumb'] = true;
        $data['bread'] = array();
        if (!empty($archiveBread)) {
            $data['bread'][] = array('name' => 'Archivio procedimenti', 'link' => $archiveBread);
        }
        $data['bread'][] = array('name' => $element['name'], 'link' => '/');
        $data['currentPageId'] = $currentPageId;

        if ($getAttachments) {
            $label = 'proceedings';
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
        }

        //Inclusione di eventuali librerie esterne
        $data = $data ?? null;

        //Inclusione del nodo JSON-LD della pagina
        $data = $data ?? null;

        $data['instance'] = !empty($data['instance'])
            ? $data['instance'] /* Fix istanze archiviate */
            : (is_array($element) ? $element : $element->toArray());

        $data = $data ?? null;

        renderFront(config('vfo', null, 'app') . $detailPage, $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Archivio" dei Procedimenti
     * ID sezione 99
     *
     * @return void
     * @throws Exception
     */
    public function archived(): void
    {
        $this->pivot();
    }

    /**
     * Metodo per la pagina di snodo del personale
     * @return void
     * @throws Exception
     */
    public function pivot(): void
    {

        $pivot = new PivotController();
        $pivot->index();
    }
}
