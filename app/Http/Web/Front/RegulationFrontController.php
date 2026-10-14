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
use Model\RegulationsModel;
use Model\SectionsFoModel;
use System\Input;
use System\Validator;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller pagina front-end Regolamenti e documentazione
 */
class RegulationFrontController extends BaseFrontController
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
     * Metodo per la pagina di snodo dei Regolamenti
     * @return void
     * @throws Exception
     */
    public function pivot(): void
    {
        $pivot = new PivotController();
        $pivot->index();
    }

    /**
     * Metodo chiamato per la pagina "Atti amministrativi generali",
     * nella sezione Disposizione generali -> Atti generali
     * ID sezione 25
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexGeneralAdministrativeActs(): void
    {
        $data = [];

        $validation = new Validator();
        if (Input::get('s') != null) {
            $validation->verifyToken();
        }
        $validation->label('Testo da cercare')->value(Input::get('s'))->maxLength(50)->end();

        if (!$validation->isSuccess()) {

            echo showError('Attenzione', $validation->getErrorsHtml(), $validation->getErrorStatusCode());
            die();

        }

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'section_fo.id', 'parent_id', 'form_filter', 'controller_open_data', 'archive_name',
            'rel_institution_type_sections_labeling.label','section_fo.created_at', 'section_fo.updated_at', 'is_system', 'controller_open_data'])
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
        $regulations = $this->getDataResultsForPublicIn('25', $data);
        $regulations = !empty($regulations) ? $regulations->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($regulations);

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
                'prefix' => '<div class="form-group col-lg-12">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 's',
                    'id' => 'search_text',
                    
                    'value' => '{text}',
                ],
                'label' => [
                    'for' => 'search_text',
                    'text' => 'Testo da cercare',
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
            's' => Input::get('s', true)
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value){
            if(!empty($value)){
                $countFilterFields +=1;
            }
        }
        $formBuilder = new FormBuilder([
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'text' => htmlEscape(Input::get('s', true)),
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['instance'] = $currentPage;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $regulations;
        $data['noRequiredPublication'] = !empty($currentPage['no_required']);
        $data['allowSearch'] = true;
        $data['finalSectionId'] = $currentPageId;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['searchFieldsOpenData'] = $formFilterFields;
        $data['filterUsed'] = $countFilterFields;
        $data['rangeOpenData'] = 2000;
        //Abilita la paginazione AJAX della tabella dei risultati (vedi renderFrontFragment)
        $data['ajaxPagination'] = true;
        //Inclusione di eventuali librerie esterne
        $data = $data ?? null;
        $data = $data ?? null;
        renderFront(config('vfo', null, 'app') . '/regulations/regulations', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Documenti di programmazione strategico-gestionale",
     * nella sezione Disposizione generali -> Atti generali
     * ID sezione 26
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexDocumentsStrategicManagement(): void
    {
        $data = [];

        $validation = new Validator();
        if (Input::get('s') != null) {
            $validation->verifyToken();
        }
        $validation->label('Testo da cercare')->value(Input::get('s'))->maxLength(50)->end();

        if (!$validation->isSuccess()) {

            echo showError('Attenzione', $validation->getErrorsHtml(), $validation->getErrorStatusCode());
            die();

        }

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'section_fo.id', 'parent_id', 'form_filter', 'controller_open_data', 'archive_name',
            'rel_institution_type_sections_labeling.label','section_fo.created_at', 'section_fo.updated_at', 'is_system', 'controller_open_data'])
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
        $regulations = $this->getDataResultsForPublicIn('26', $data);
        $regulations = !empty($regulations) ? $regulations->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($regulations);

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
                'prefix' => '<div class="form-group col-lg-12">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 's',
                    'id' => 'search_text',
                    
                    'value' => '{text}',
                ],
                'label' => [
                    'for' => 'search_text',
                    'text' => 'Testo da cercare',
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
            's' => Input::get('s', true)
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value){
            if(!empty($value)){
                $countFilterFields +=1;
            }
        }
        $formBuilder = new FormBuilder([
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'text' => htmlEscape(Input::get('s', true)),
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $regulations;
        $data['instance'] = $currentPage;
$data['noRequiredPublication'] = !empty($currentPage['no_required']);
        $data['allowSearch'] = true;
        $data['finalSectionId'] = $currentPageId;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['searchFieldsOpenData'] = $formFilterFields;
        $data['filterUsed'] = $countFilterFields;
        $data['rangeOpenData'] = 2000;
        //Abilita la paginazione AJAX della tabella dei risultati (vedi renderFrontFragment)
        $data['ajaxPagination'] = true;
        //Inclusione di eventuali librerie esterne
        $data = $data ?? null;
        $data = $data ?? null;
        renderFront(config('vfo', null, 'app') . '/regulations/regulations', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Statuti e leggi regionali",
     * nella sezione Disposizione generali -> Atti generali
     * ID sezione 27
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexStatutesRegionalLaws(): void
    {
        $data = [];

        $validation = new Validator();
        if (Input::get('s') != null) {
            $validation->verifyToken();
        }
        $validation->label('Testo da cercare')->value(Input::get('s'))->maxLength(50)->end();

        if (!$validation->isSuccess()) {

            echo showError('Attenzione', $validation->getErrorsHtml(), $validation->getErrorStatusCode());
            die();

        }

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'section_fo.id', 'parent_id', 'form_filter', 'controller_open_data', 'archive_name',
            'rel_institution_type_sections_labeling.label','section_fo.created_at', 'section_fo.updated_at', 'is_system','controller_open_data'])
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
        $regulations = $this->getDataResultsForPublicIn('27', $data);
        $regulations = !empty($regulations) ? $regulations->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($regulations);

        // Form per i filtri di ricerca
        $form = json_encode([
            [
                'type' => 'form_open',
                'name' => 'regulation',
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
                'prefix' => '<div class="form-group col-lg-12">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 's',
                    'id' => 'search_text',
                    
                    'value' => '{text}',
                ],
                'label' => [
                    'for' => 'search_text',
                    'text' => 'Testo da cercare',
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
            's' => Input::get('s', true)
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value){
            if(!empty($value)){
                $countFilterFields +=1;
            }
        }
        $formBuilder = new FormBuilder([
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'text' => htmlEscape(Input::get('s', true)),
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $regulations;
        $data['instance'] = $currentPage;
$data['noRequiredPublication'] = !empty($currentPage['no_required']);
        $data['allowSearch'] = true;
        $data['finalSectionId'] = $currentPageId;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['searchFieldsOpenData'] = $formFilterFields;
        $data['filterUsed'] = $countFilterFields;
        $data['rangeOpenData'] = 2000;
        //Abilita la paginazione AJAX della tabella dei risultati (vedi renderFrontFragment)
        $data['ajaxPagination'] = true;
        //Inclusione di eventuali librerie esterne
        $data = $data ?? null;
        $data = $data ?? null;
        renderFront(config('vfo', null, 'app') . '/regulations/regulations', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Codice disciplinare e codice di condotta",
     * nella sezione Disposizione generali -> Atti generali
     * ID sezione 28
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexDisciplinaryAndConductCode(): void
    {
        $data = [];

        $validation = new Validator();
        if (Input::get('s') != null) {
            $validation->verifyToken();
        }
        $validation->label('Testo da cercare')->value(Input::get('s'))->maxLength(50)->end();

        if (!$validation->isSuccess()) {

            echo showError('Attenzione', $validation->getErrorsHtml(), $validation->getErrorStatusCode());
            die();

        }

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'section_fo.id', 'parent_id', 'form_filter', 'controller_open_data', 'archive_name',
            'rel_institution_type_sections_labeling.label','section_fo.created_at', 'section_fo.updated_at', 'is_system','controller_open_data'])
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
        $regulations = $this->getDataResultsForPublicIn('28', $data);
        $regulations = !empty($regulations) ? $regulations->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($regulations);

        // Form per i filtri di ricerca
        $form = json_encode([
            [
                'type' => 'form_open',
                'name' => 'regulation',
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
                'prefix' => '<div class="form-group col-lg-12">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 's',
                    'id' => 'search_text',
                    
                    'value' => '{text}',
                ],
                'label' => [
                    'for' => 'search_text',
                    'text' => 'Testo da cercare',
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
            's' => Input::get('s', true)
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value){
            if(!empty($value)){
                $countFilterFields +=1;
            }
        }
        $formBuilder = new FormBuilder([
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'text' => htmlEscape(Input::get('s', true)),
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $regulations;
        $data['instance'] = $currentPage;
$data['noRequiredPublication'] = !empty($currentPage['no_required']);
        $data['allowSearch'] = true;
        $data['finalSectionId'] = $currentPageId;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['searchFieldsOpenData'] = $formFilterFields;
        $data['filterUsed'] = $countFilterFields;
        $data['rangeOpenData'] = 2000;
        //Abilita la paginazione AJAX della tabella dei risultati (vedi renderFrontFragment)
        $data['ajaxPagination'] = true;
        //Inclusione di eventuali librerie esterne
        $data = $data ?? null;
        $data = $data ?? null;
        renderFront(config('vfo', null, 'app') . '/regulations/regulations', $data, 'frontend');
    }

    /**
     * Metodo chiamato per la pagina "Regolamenti",
     * nella sezione Disposizione generali -> Atti generali
     * ID sezione 29
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexRegulations(): void
    {
        $data = [];

        $validation = new Validator();
        if (Input::get('s') != null) {
            $validation->verifyToken();
        }
        $validation->label('Testo da cercare')->value(Input::get('s'))->maxLength(50)->end();

        if (!$validation->isSuccess()) {

            echo showError('Attenzione', $validation->getErrorsHtml(), $validation->getErrorStatusCode());
            die();

        }

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'section_fo.id', 'parent_id', 'form_filter', 'controller_open_data', 'archive_name',
            'rel_institution_type_sections_labeling.label','section_fo.created_at', 'section_fo.updated_at', 'is_system'])
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
        $regulations = $this->getDataResultsForPublicIn('29', $data);
        $regulations = !empty($regulations) ? $regulations->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($regulations);

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
                'prefix' => '<div class="form-group col-lg-12">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 's',
                    'id' => 'search_text',
                    
                    'value' => '{text}',
                ],
                'label' => [
                    'for' => 'search_text',
                    'text' => 'Testo da cercare',
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
            's' => Input::get('s', true)
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value){
            if(!empty($value)){
                $countFilterFields +=1;
            }
        }
        $formBuilder = new FormBuilder([
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'text' => htmlEscape(Input::get('s', true)),
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $regulations;
        $data['instance'] = $currentPage;
$data['noRequiredPublication'] = !empty($currentPage['no_required']);
        $data['allowSearch'] = true;
        $data['finalSectionId'] = $currentPageId;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['searchFieldsOpenData'] = $formFilterFields;
        $data['filterUsed'] = $countFilterFields;
        $data['rangeOpenData'] = 2000;
        //Abilita la paginazione AJAX della tabella dei risultati (vedi renderFrontFragment)
        $data['ajaxPagination'] = true;
        //Inclusione di eventuali librerie esterne
        $data = $data ?? null;
        $data = $data ?? null;
        renderFront(config('vfo', null, 'app') . '/regulations/regulations', $data, 'frontend');
    }

    /**
     * Funzione che ritorna i dati da mostrare nella tabella
     *
     * @param null  $section {le sezioni pubblica in per cui filtrare i dati}
     * @param array $data    Dati da passare alla vista
     * @return mixed
     * @throws Exception
     */
    private function getDataResultsForPublicIn($section = null, array &$data = []): mixed
    {

        // Recupero i canoni di locazione percepiti da mostrare
        $regulations = RegulationsModel::join('rel_regulations_public_in', 'rel_regulations_public_in.object_regulation_id', '=', 'object_regulations.id')
            ->where('rel_regulations_public_in.public_in_id', $section)
            ->filter(
                (Input::get('s', true)),
            )
            ->orderBy('order', 'ASC')
            ->orderBy('name', 'ASC')
            ->paginate(21, ['object_regulations.id', 'title as name'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get(['s', 'sec_token']))
            ->setPath(currentUrl());

        // Richiesta del solo frammento (paginazione AJAX): essendo getDataResultsForPublicIn()
        // chiamato da tutte le pagine di questo controller prima del lavoro pesante (form
        // filtro, render completo), un solo controllo qui copre tutte quelle pagine.
        if (isFrontFragmentRequest()) {
            $resultsArray = $regulations->toArray();
            $table = $this->createTableRows($resultsArray);
            renderFrontFragment(['table' => !empty($table) ? $table->generate() : null, 'instances' => $resultsArray]);
            exit;
        }

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($regulations) && !empty($regulations->toArray()['data'])) {

            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(RegulationsModel::select(['object_regulations.created_at'])
                ->join('rel_regulations_public_in', 'rel_regulations_public_in.object_regulation_id', '=', 'object_regulations.id')
                ->where('rel_regulations_public_in.public_in_id', $section)
                ->orderBy('object_regulations.created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(RegulationsModel::select(['object_regulations.updated_at'])
                ->join('rel_regulations_public_in', 'rel_regulations_public_in.object_regulation_id', '=', 'object_regulations.id')
                ->where('rel_regulations_public_in.public_in_id', $section)
                ->orderBy('object_regulations.updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        return $regulations;
    }

    /**
     * Metodo che crea la tabella con i dati dei regolamenti pubblicati nel front-office.
     *
     * Le colonne riprendono informazioni già pubbliche e già usate negli open data o nel dettaglio,
     * mantenendo il link sul titolo che prima era l'unica azione disponibile nella card.
     *
     * @param null $regulations Dati paginati da inserire nella tabella
     * @return Table|null
     * @throws Exception
     */
    private function createTableRows($regulations = null): ?Table
    {
        $currentPageId = (int)uri()->segment(2, 0);
        $table = null;

        if (!empty($regulations['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();
            $table->set_heading('Titolo');

            // Creo le righe della tabella settando i dati da mostrare
            foreach ($regulations['data'] as $regulation) {

                $table->add_row(
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $regulation['id'] . '/' . urlTitle($regulation['name'])) . '" data-id="' . $regulation['id'] . ' ">' . escapeXss($regulation['name']) . '</a>'
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
        $element = RegulationsModel::where('id', $elementId)
            ->with('proceedings:id,name')
            ->with('structures:id,structure_name')
            ->with('charges:id,title');

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

        $label = 'regulations';
        $elementId = $element['id'];
        $selectFields = [
            'id',
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

        renderFront(config('vfo', null, 'app') . '/regulations/details', $data, 'frontend');
    }
}
