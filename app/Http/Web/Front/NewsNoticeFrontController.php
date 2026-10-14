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
use Model\NewsNoticesModel;
use Model\SectionsFoModel;
use System\Input;
use System\Validator;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * News e avvisi
 */
class NewsNoticeFrontController extends BaseFrontController
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
     * Metodo chiamato per la pagina "News e avvisi"
     * Id sezione 180
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function index(): void
    {
        $data = [];
        $validation = new Validator();

        if (Input::get('title') != null ||
            Input::get('start_news_date') != null ||
            Input::get('end_news_date') != null
        ) {
            $validation->verifyToken();
        }
        $validation->label('Titolo')->value(Input::get('title'))->maxLength(50)->end();
        $validation->label('Data - dal')->value(Input::get('start_news_date'))->isDate('Y-m-d')->end();
        $validation->label('Data - al')->value(Input::get('end_news_date'))->isDate('Y-m-d')->end();

        if (!$validation->isSuccess()) {
            echo showError('Attenzione', $validation->getErrorsHtml(), $validation->getErrorStatusCode());
            die();

        }
        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Data del giorno successivo al corrente
        $tomorrow = date("Y-m-d H:i:s", strtotime('tomorrow'));

        // Recupero i procedimenti(con campo Pubblica automaticamente i dati sul monitoraggio = 1) da mostrare
        $news = NewsNoticesModel::where('start_date', '<=', $tomorrow)
            ->where('end_date', '>', date('Y-m-d H:i:s'))
            ->filter(
                Input::get('title', true),
                Input::get('start_news_date', true),
                Input::get('end_news_date', true),
            );


        $news = $news
            ->orderBy('object_news_notices.news_date', 'DESC')
            ->orderBy('object_news_notices.title', 'DESC')
            ->paginate(
                20,
                [
                    'object_news_notices.id',
                    'object_news_notices.news_date',
                    'object_news_notices.title',
                    'object_news_notices.typology'
                ],
                'p',
                (int)Input::get('p')
            )
            ->onEachSide(2)
            ->appends(Input::get(['title', 'start_news_date', 'end_news_date', 'sec_token', 'public_in']))
            ->setPath(currentUrl());

        $news = !empty($news) ? $news->toArray() : [];

        $table = null;

        if (!empty($news['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();

            $table->set_heading('Titolo', 'Tipologia', 'Data');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($news['data'] as $n) {

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $n['id'] . '/' . urlTitle($n['title'])) . '" data-id="' . $n['id'] . ' ">' . escapeXss($n['title']) . '</a>',
                    escapeXss($n['typology']),
                    date('d/m/Y', strtotime($n['news_date']))
                );
            }

        }

        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $news;

        // Richiesta del solo frammento (paginazione AJAX): tabella e paginazione sono pronte,
        // tutto il resto (pagina corrente, menu, filtri, contenuti, layout) serve solo alla
        // pagina completa e viene saltato.
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
        if (!empty($news['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(NewsNoticesModel::select(['created_at'])
                ->where('start_date', '<=', $tomorrow)
                ->where('end_date', '>', date('Y-m-d H:i:s'))
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(NewsNoticesModel::select(['updated_at'])
                ->where('start_date', '<=', $tomorrow)
                ->where('end_date', '>', date('Y-m-d H:i:s'))
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Dati passati alla vista
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;

        // Form per i filtri di ricerca
        $form = [
            [
                'type' => 'form_open',
                'name' => 'news_notice',
                'action' => '{current_url}',
                'attributes' => [
                    'method' => 'get',
                    'class' => 'set-active-label',
                ]
            ],
            [
                'type' => 'tag',
                'content' => '<div class="row mb-2">',
            ],
            [
                'prefix' => '<div class="form-group col-lg-4 mt-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'title',
                    'id' => 'title',
                    
                    'value' => '{title}',
                ],
                'label' => [
                    'for' => 'title',
                    'text' => 'Titolo',
                ],
            ],
            [
                'prefix' => '<div class="col-lg-4">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'start_news_date',
                    'type' => 'date',
                    'id' => 'start_news_date_id',
                    'value' => '{start_news_date}',
                ],
                'label' => [
                    'for' => 'start_news_date',
                    'text' => 'Data - dal',
                    'class' => 'active'
                ],
            ],
            [
                'prefix' => '<div class="col-lg-4 ">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'end_news_date',
                    'type' => 'date',
                    'id' => 'end_news_date_id',
                    'value' => '{end_news_date}',
                ],
                'label' => [
                    'for' => 'end_news_date',
                    'text' => 'Data - al',
                    'class' => 'active'
                ],
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


        $form = json_encode($form);

        $formFilterFields = [
            'title' => Input::get('title', true),
            'start_news_date' => Input::get('start_news_date', true),
            'end_news_date' => Input::get('end_news_date', true),
        ];


        $countFilterFields = 0;
        foreach ($formFilterFields as $value) {
            if (!empty($value)) {
                $countFilterFields += 1;
            }
        }

        $formBuilder = [
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'title' => htmlEscape(Input::get('title', true)),
            'start_news_date' => htmlEscape(Input::get('start_news_date', true)),
            'end_news_date' => htmlEscape(Input::get('start_news_date', true)),
        ];


        $formBuilder = new FormBuilder($formBuilder);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['paragraphs'] = $contents;
        $data['instance'] = $currentPage;
        $data['allowSearch'] = false;
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
        $data = $data ?? null;
        $data = $data ?? null;
        renderFront(config('vfo', null, 'app') . '/news_notice/news_notice', $data, 'frontend');
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
        $element = NewsNoticesModel::where('id', $elementId);


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
        $data['pageName'] = $element['title'];
        $data['menuPages'] = $sectionFO;

        // aggiungi al Breadcrumbs la pagina del dettaglio
        $data['concatBreadcrumb'] = true;
        $data['bread'] = array();
        $data['bread'][] = array('name' => $element['title'], 'link' => '/');
        $data['instance'] = $element;
        $data['currentPageId'] = $currentPageId;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['editRecord'] = true;

        $label = 'news_notices';
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

        renderFront(config('vfo', null, 'app') . '/news_notice/details', $data, 'frontend');

    }
}
