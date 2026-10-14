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
use Model\PaymentModel;
use Model\SectionsFoModel;
use System\Input;
use System\Validator;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller pagina front-end Tassi di assenza
 */
class PaymentFrontController extends BaseFrontController
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
            Input::get('year') != null ||
            Input::get('category') != null ||
            Input::get('typology') != null ||
            Input::get('supplier') != null ||
            Input::get('trimester') != null

        ) {
            $validator->verifyToken();
        }
        $validator->label('anno')->value(Input::get('year'))->isDate('Y')->end();
        $validator->label('categoria spesa')->value(Input::get('category'))->in('1,2')->end();

        if (!$validator->isSuccess()) {
            echo showError('Errore nella validazione dei dati', $validator->getErrorsHtml(), $validator->getErrorStatusCode());
            die();
        }

        $categories = [1 => 'Uscite correnti', 2 => 'Uscite in conto capitale'];

        // Recupero i procedimenti(con campo Pubblica automaticamente i dati sul monitoraggio = 1) da mostrare
        $payments = PaymentModel::filter(
            (int)Input::get('year', true),
            (int)Input::get('category', true),
            Input::get('typology', true),
            Input::get('trimester', true),
            Input::get('supplier', true),
        )
            ->orderBy('year', 'DESC')
            ->orderBy('trimester', 'DESC')
            ->orderBy('id', 'DESC')
            ->paginate(20, ['id', 'supplier', 'year', 'trimester', 'compensation', 'expense_typology', 'expense_category', 'notes'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get([ 'year', 'category', 'typology', 'trimester', 'supplier', 'sec_token']))
            ->setPath(currentUrl());

        $payments = !empty($payments) ? $payments->toArray() : [];

        $typologies = [null => 'Tutte le tipologie'] + config('paymentTypologyCorrenti', [], 'app') + config('paymentTypologyCapitale', [], 'app');

        $details = false;

        $table = $this->createTableRows($payments, $typologies, $categories, $details);

        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $payments;

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
            ->with(['contents' => function ($query) {
                $query->select(['id', 'created_at', 'updated_at', 'section_fo_id'])
                    ->orderBy('updated_at', 'DESC')
                    ->orderBy('created_at', 'DESC')
                    ->first();
            }])
            ->labelfilter((int)$institutionTypeId);


        $currentPage = $currentPage->first()
            ->toArray();

        // Recupero le voci per costruire il menu dei contenuti
        $sectionFO = getRightOrBottomMenu($currentPageId, $currentPage['parent_id']);

        // Form per i filtri di ricerca
        $form = json_encode([
            [
                'type' => 'form_open',
                'name' => 'payment',
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
                'prefix' => '<div class="select-wrapper col-lg-2">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{years}',
                'name' => 'year',
                'extra' => 'id="year"',
                'label' => [
                    'for' => 'year',
                    'text' => 'Anno',
                    'class' => 'active'
                ],
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-2">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{trimesters}',
                'name' => 'trimester',
                'extra' => 'id="trimester"',
                'label' => [
                    'for' => 'trimester',
                    'text' => 'Trimestre',
                    'class' => 'active'
                ],
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-2">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{categories}',
                'name' => 'category',
                'extra' => 'id="category"',
                'label' => [
                    'for' => 'category',
                    'text' => 'Categoria spesa',
                    'class' => 'active'
                ],
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-3">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{typologies}',
                'name' => 'typology',
                'extra' => 'id="typology"',
                'label' => [
                    'for' => 'typology',
                    'text' => 'Tipologia',
                    'class' => 'active'
                ],
            ],
            [
                'prefix' => '<div class="select-wrapper col-lg-3">',
                'suffix' => '</div>',
                'type' => 'form_dropdown',
                'options' => '{supplierOpt}',
                'name' => 'supplier',
                'extra' => 'id="supplier"',
                'label' => [
                    'for' => 'supplier',
                    'text' => 'Beneficiario',
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
        if (!empty($payments['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(PaymentModel::select(['created_at'])
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(PaymentModel::select(['updated_at'])
                ->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        $formFilterFields = [
            'year' => Input::get('year'),
            'category' => Input::get('category'),
            'typology' => Input::get('typology'),
            'trimester' => Input::get('trimester'),
            'supplier' => Input::get('supplier'),
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value) {
            if (!empty($value)) {
                $countFilterFields += 1;
            }
        }

        $resultYears = PaymentModel::select('year')
            ->orderBy('year', 'ASC')
            ->first();

        $resultYears = !empty($resultYears) ? $resultYears->toArray() : ['year' => date('Y')];
        $years = [null => 'Seleziona un anno'];
        for ($i = (int)$resultYears['year']; $i <= (int)date('Y'); $i++) {
            $years[$i] = $i;
        }

        $supplierTypologyOptions = [null => 'Tutte le tipologie'] + config('paymentSupplierTypology', [], 'app');

        $formBuilder = new FormBuilder([
            'current_url' => currentUrl(),
            'years' => FormBuilder::parseOptions($years, htmlEscape(Input::get('year', true))),
            'categories' => FormBuilder::parseOptions([null => 'Tutte le categorie'] + $categories, htmlEscape(Input::get('category', true))),
            'supplierOpt' => FormBuilder::parseOptions($supplierTypologyOptions, htmlEscape(Input::get('supplier', true))),
            'typologies' => FormBuilder::parseOptions($typologies, htmlEscape(Input::get('typology', true))),
            'trimesters' => FormBuilder::parseOptions([null => 'Seleziona il trimestre', 1=> '1', 2=>'2', 3=>'3', 4=>'4'],htmlEscape(Input::get('trimester', true)))
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['instance'] = $currentPage;
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

        //Inclusione di eventuali librerie esterne
        $data = $data ?? null;
        $data = $data ?? null;

        renderFront(config('vfo', null, 'app') . '/payment/payment', $data, 'frontend');
    }

    /**
     * Metodo che crea la tabella con i dati dei risultati della ricerca da mostrare
     *
     * @param $payments {dati da inserire nella tabella}
     * @param array $typologies Elenco delle tipologie di spesa indicizzate per identificativo
     * @param array $categories Elenco delle categorie di spesa indicizzate per identificativo
     * @param bool $details Indica se visualizzare la colonna di dettaglio
     * @return Table|null
     * @throws Exception
     */
    private function createTableRows($payments = null, $typologies = [], $categories = [], $details = true): ?Table
    {

        $currentPageId = (int)uri()->segment(2, 0);

        $table = null;

        if (!empty($payments['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();
            $head = ['Beneficiario', 'Anno', 'Trimestre', 'Importo', 'Categoria di spesa', 'Tipologia di spesa'];
            if($details){
                $head[] = 'Dettaglio';
            }
            $table->set_heading($head);

            $paymentSupplier = config('paymentSupplierTypology', [], 'app');

            // Creo le righe della tabella settando i dati da mostrare
            foreach ($payments['data'] as $paym) {

                $row = [
                    ($paymentSupplier[$paym['supplier']] ?? ''),
                    !empty($paym['year']) ?  escapeXss($paym['year']) : '',
                    !empty($paym['trimester']) ?  $paym['trimester'] : '',
                    !empty($paym['compensation']) ? '€ '.escapeXss(S::currency($paym['compensation'], 2, ',', '.')) : '',
                    (!empty($paym['expense_category']) && isset($categories[$paym['expense_category']])) ? $categories[$paym['expense_category']] : '',
                    !empty($paym['expense_typology'] && isset($typologies[$paym['expense_typology']])) ? $typologies[$paym['expense_typology']] : '',
                ];

                if($details){
                    $detail = '<a class="mx-4" href="' . siteUrl('page/' . $currentPageId . '/details/' . $paym['id'] . '/dettaglio.html"') . ' data-id="' . $paym['id'] . '"><i class="fas fa-search"></i></a>';
                    $row[] = $detail;
                }

                $table->add_row($row);
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
        echo show404('Ops..', 'record not found');
        exit();

        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);
        $elementId = (int)uri()->segment(4, 0);

        // Recupero l'elemento da mostrare
        $element = PaymentModel::where('id', $elementId);

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
        $data['bread'][] = array('name' => $data['pageName'], 'link' => '/');
        $data['instance'] = $element;
        $data['currentPageId'] = $currentPageId;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['editRecord'] = true;

        $data['typologies'] = [null => ''] + config('paymentTypologyCorrenti', [], 'app') + config('paymentTypologyCapitale', [], 'app');

        $data['paymentSupplier'] = config('paymentSupplierTypology', [], 'app');

        $label = 'payment';
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

        renderFront(config('vfo', null, 'app') . '/payment/details', $data, 'frontend');
    }
}
