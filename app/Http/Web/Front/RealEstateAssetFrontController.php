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
use Model\RealEstateAssetModel;
use Model\SectionsFoModel;
use System\Input;
use System\Validator;

/**
 * Controller pagina front-end Patrimonio Immobiliare
 */
class RealEstateAssetFrontController extends BaseFrontController
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

        // Recupero l'elemento da mostrare
        $element = RealEstateAssetModel::where('id', $elementId)
            ->with('offices:id,structure_name,archived')
            ->with('canons:id,canon_type,amount,start_date,end_date');


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

        $data['currentPageId'] = $currentPageId;
        $detailPage = '/real_estate_asset/details';

        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['editRecord'] = true;
        $archiveBread = '';


        if(!is_array($element)) {
            $element = $element->toArray();
        }
        // aggiungi al Breadcrumbs la pagina del dettaglio
        $data['concatBreadcrumb'] = true;
        $data['bread'] = array();
        if (!empty($archiveBread)) {
            $data['bread'][] = array('name' => 'Archivio', 'link' => $archiveBread);
        }
        $data['bread'][] = array('name' => $element['name'], 'link' => '/');

        if ($getAttachments) { //Se l'elemento non è archiviato recupero anche gli allegati

            $label = 'real_estate_asset';
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
        }

        $data['instance'] = $element;

        renderFront(config('vfo', null, 'app') . $detailPage, $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Archivio" degli Immobili
     * ID sezione 134
     *
     * @return void
     * @throws Exception
     * @url /page/page_id/archivio.html
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

    /**
     * Metodo chiamato per la pagina "Canoni di locazione o affitto percepiti"
     * ID sezione 133
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

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Validazione dei campi di input per la ricerca
        $validator = new Validator();
        if (Input::get('name') != null || Input::get('address') != null) {
            $validator->verifyToken();
        }
        $validator->label('Nome')->value(Input::get('name'))->maxLength(30)->end();
        $validator->label('Indirizzo')->value(Input::get('address'))->maxLength(120)->end();

        if (!$validator->isSuccess()) {
            echo showError('Errore nella validazione dei dati', $validator->getErrorsHtml(), $validator->getErrorStatusCode());
            die();
        }

        // Recupero gli immobili da mostrare
        $assets = RealEstateAssetModel::where(function ($query) {
        })
            ->filter(
                (Input::get('name', true)),
                (Input::get('address', true))
            )
            ->paginate(20, ['id', 'name', 'address'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get(['name', 'address', 'sec_token']))
            ->setPath(currentUrl());

        $assets = !empty($assets) ? $assets->toArray() : [];

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($assets);

        $data['table'] = !empty($table) ? $table->generate() : null;
        $data['instances'] = $assets;

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
        if (!empty($assets['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(RealEstateAssetModel::select(['created_at'])
                ->where(function ($query) {
                })
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(RealEstateAssetModel::select(['updated_at'])
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
                    'name' => 'name',
                    'id' => 'name',

                    'value' => '{name}'
                ],
                'label' => [
                    'for' => 'name',
                    'text' => 'Nome',
                ],
            ],
            [
                'prefix' => '<div class="form-group col-lg-6" style="margin-bottom: 1rem;">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'address',
                    'id' => 'address',

                    'value' => '{address}'
                ],
                'label' => [
                    'for' => 'address',
                    'text' => 'Indirizzo',
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
            'name' => Input::get('name', true),
            'address' => Input::get('address', true)
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
            'name' => htmlEscape(Input::get('object', true)),
            'address' => htmlEscape(Input::get('address', true))
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['instance'] = $currentPage;
        $data['paragraphs'] = $contents;
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

        renderFront(config('vfo', null, 'app') . '/real_estate_asset/real_estate_asset', $data, 'frontend');
    }

    /**
     * Metodo che crea la tabella con i dati dei risultati della ricerca da mostrare
     *
     * @param null $assets Records da inserire nella tabella
     * @return Table|null
     * @throws Exception
     */
    protected function createTableRows($assets = null): ?Table
    {
        $currentPageId = (int)uri()->segment(2, 0);
        $table = null;

        if (!empty($assets['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();
            $table->set_heading('Nome', 'Indirizzo');

            // Creo le riche della tabella settando i dati da mostrare
            foreach ($assets['data'] as $asset) {

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $asset['id'] . '/' . urlTitle($asset['name'])) . '" data-id="' . $asset['id'] . ' ">' . escapeXss($asset['name']) . '</a>',
                    !empty($asset['address']) ? escapeXss($asset['address']) : null
                );
            }
        }

        return $table;
    }
}
