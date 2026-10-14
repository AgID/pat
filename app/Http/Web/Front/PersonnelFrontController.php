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
use Model\PersonnelModel;
use Model\SectionFoPoliticOrganModel;
use Model\SectionsFoModel;
use System\Input;
use System\Validator;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Controller pagina front-end Personale
 */
class PersonnelFrontController extends BaseFrontController
{
    private bool $orgIndPolTabularDisplay = false;

    /**
     * @description Costruttore
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        helper(['url', 'image']);

        $this->orgIndPolTabularDisplay =  false;
    }

    /**
     * @description Metodo per la pagina di snodo del personale
     * @return void
     * @throws Exception
     */
    public function pivot(): void
    {
        $pivot = new PivotController();
        $pivot->index();
    }

    /**
     * Tutte le pagine ID sezione 221, 222, 223, 224, 225, 226, 227, 228, 229, 230, 231, 232, 238, 239, 240, 241, 242, 243
     * @return void
     * @throws Exception
     */

    public function indexPoliticPage(){

        $data = [];

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'section_fo.id', 'parent_id', 'form_filter', 'controller_open_data', 'archive_name',
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

        $structuresTable = null;
        $personnelTable = null;
        $data['politic_organ'] = [];

        $politicOrganInfo = SectionFoPoliticOrganModel::where('section_fo_id', $currentPageId)->first();

        if(!empty($politicOrganInfo)){

            $data['politic_organ'] = $politicOrganInfo->toArray();

            $relationInfo = SectionsFoModel::select(['id'])->where('id', $currentPageId )
                ->with('management_structure:id,structure_name,archived')
                ->with('non_management_structure:id,structure_name,archived')
                ->with(['politic_personnel' => function ($query) {
                    $query->with('role:id,name')
                        ->orderBy('priority', 'ASC')
                        ->orderBy('full_name', 'ASC');
                }])
                ->first()
                ->toArray();

            $data['organ_relation'] = $relationInfo;

            if(!empty($relationInfo['management_structure']) || !empty($relationInfo['non_management_structure'])){
                $structuresTable = $this->createPoliticTableRows($relationInfo);
            }

            if($data['politic_organ']['show_personnel'] && !empty($relationInfo['politic_personnel'])){
                $personnelTable = $this->createPoliticTableRows($relationInfo['politic_personnel'], 'personnel');
            }

        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['personnel_table'] = !empty($personnelTable) ? $personnelTable->generate() : $personnelTable ;
        $data['structure_table'] = !empty($structuresTable) ? $structuresTable->generate() : $structuresTable ;
        $data['instance'] = $currentPage;
        $data['councilAndCouncillors'] = false;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['rangeOpenData'] = 2000;

        renderFront(config('vfo', null, 'app') .  '/personnel/politic_organ', $data, 'frontend');
    }


    public function indexArchivePoliticPage(): void
    {
        $this->pivot();
    }


    public function getSectionName($sectionID = 0)
    {

        if(empty($sectionID)){
            return "";
        }

        $nameTmp = SectionsFoModel::select(['id', 'name'])->where('id', $sectionID)->first();

        if(!empty($nameTmp)){
            $name = $nameTmp->toArray()['name'];
            return "<a href='".siteUrl("/page/".$sectionID."/".urlTitle($name))."'>$name</a>";
        }
        return "";

    }


    /**
     * @description Metodo chiamato per la pagina "Archivio",
     * nella sezione Organizzazione -> Titolari di incarichi politici, di amministrazione, di direzione o di governo -> Sindaco
     * ID sezione 248
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     * @noinspection DuplicatedCode
     */
    public function mayorArchive(): void
    {
        $this->pivot();
    }

    /**
     * @description Metodo chiamato per la pagina "Archivio",
     * nella sezione Organizzazione -> Titolari di incarichi politici, di amministrazione, di direzione o di governo -> Vicesindaco
     * ID sezione 249
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     * @noinspection DuplicatedCode
     */
    public function deputyMayorArchive(): void
    {
        $this->pivot();
    }

    /**
     * @description Metodo chiamato per la pagina "Archivio",
     * nella sezione Organizzazione -> Titolari di incarichi politici, di amministrazione, di direzione o di governo -> Presidente Consiglio Comunale
     * ID sezione 252
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     * @noinspection DuplicatedCode
     */
    public function presidentCityCouncilArchive(): void
    {
        $this->pivot();
    }

    /**
     * @description Metodo chiamato per la pagina "Dotazione organica",
     * nella sezione del Personale
     * ID sezione 64
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexStaffing(): void
    {
        $data = [];

        $validation = new Validator();
        if (Input::get('n') != null) {
            $validation->verifyToken();
        }
        $validation->label('Nominativo')->value(Input::get('n'))->maxLength(50)->end();

        if (!$validation->isSuccess()) {

            echo showError('Attenzione', $validation->getErrorsHtml(), $validation->getErrorStatusCode());
            die();
        }

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Recupero i dati sul personale da pubblicare nella pagina secondo i suoi criteri di pubblicazione
        $personnel = PersonnelModel::where('personnel_lists', 1)
            ->where(function ($query) {
            })
            ->with('responsible_structures:id,structure_name')
            ->filter(
                (Input::get('n', true)),
            )
            ->orderBy('full_name', 'ASC')
            ->paginate(20, ['id', 'full_name'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get(['n', 'sec_token']))
            ->setPath(currentUrl());

        $personnel = !empty($personnel) ? $personnel->toArray() : [];

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($personnel);

        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $personnel;

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
        if (!empty($personnel['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(PersonnelModel::select(['created_at'])
                ->where('personnel_lists', 1)
                ->where(function ($query) {
                })
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(PersonnelModel::select(['updated_at'])
                ->where('personnel_lists', 1)
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
                'prefix' => '<div class="form-group col-lg-12">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'n',
                    'id' => 'name',

                    'value' => '{name}',
                ],
                'label' => [
                    'for' => 'name',
                    'text' => 'Nominativo',
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
            'n' => Input::get('n', true)
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
            'name' => htmlEscape(Input::get('n', true)),
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

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

        renderFront(config('vfo', null, 'app') . '/personnel/personnel', $data, 'frontend');
    }

    /**
     * @description Metodo chiamato per la pagina "Personale non a tempo indeterminato",
     * nella sezione del Personale
     * ID sezione 65
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexNotIndefinite(): void
    {
        $data = [];

        $validation = new Validator();
        if (Input::get('n') != null) {
            $validation->verifyToken();
        }
        $validation->label('Nominativo')->value(Input::get('n'))->maxLength(50)->end();

        if (!$validation->isSuccess()) {

            echo showError('Attenzione', $validation->getErrorsHtml(), $validation->getErrorStatusCode());
            die();
        }


        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Recupero i dati del personale da pubblicare nella pagina in base ai suoi criteri di pubblicazione
        $personnel = PersonnelModel::where('personnel_lists', 1)
            ->where(function ($query) {
            })
            ->where('determined_term', 1)
            ->with('referent_structures:id,structure_name')
            ->with('responsible_structures:id,structure_name')
            ->filter(
                (Input::get('n', true)),
            )
            ->orderBy('full_name', 'ASC')
            ->paginate(20, ['id', 'full_name'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get(['n', 'sec_token']))
            ->setPath(currentUrl());
        $personnel = !empty($personnel) ? $personnel->toArray() : [];

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($personnel);

        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $personnel;

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
        if (!empty($personnel['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(PersonnelModel::select(['created_at'])
                ->where(function ($query) {
                })
                ->where('determined_term', 1)
                ->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(PersonnelModel::select(['updated_at'])
                ->where(function ($query) {
                })
                ->where('determined_term', 1)
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
                'prefix' => '<div class="form-group col-lg-12">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'n',
                    'id' => 'name',

                    'value' => '{name}',
                ],
                'label' => [
                    'for' => 'name',
                    'text' => 'Nominativo',
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
            'n' => Input::get('n', true)
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
            'name' => htmlEscape(Input::get('n', true)),
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

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

        renderFront(config('vfo', null, 'app') . '/personnel/personnel', $data, 'frontend');
    }

    /**
     * @description Metodo chiamato per la pagina "Archivio personale",
     * nella sezione del Personale
     * ID sezione 73
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexArchive(): void
    {
        $this->pivot();
    }

    /**
     * @description Metodo chiamato per la pagina "Cessati dall'incarico",
     * nella sezione del Personale
     * ID sezione 247
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexArchivePositionHolders(): void
    {
        $this->pivot();
    }

    /**
     * @description Metodo chiamato per la pagina "Titolari di incarichi dirigenziali (dirigenti non generali)",
     * nella sezione del Personale
     * ID sezione 60
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexManagerialPositions(): void
    {
        $data = [];

        $validation = new Validator();
        if (Input::get('n') != null) {
            $validation->verifyToken();
        }
        $validation->label('Nominativo')->value(Input::get('n'))->maxLength(50)->end();

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
        $personnel = $this->getDataResultsForPublicIn('60', null, $data);
        $personnel = !empty($personnel) ? $personnel->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($personnel);

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
                    'name' => 'n',
                    'id' => 'name',

                    'value' => '{name}',
                ],
                'label' => [
                    'for' => 'name',
                    'text' => 'Nominativo',
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
            'n' => Input::get('n', true)
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
            'name' => htmlEscape(Input::get('n', true)),
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $personnel;
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

        renderFront(config('vfo', null, 'app') . '/personnel/personnel', $data, 'frontend');
    }

    /**
     * @description Metodo chiamato per la pagina "Posizioni organizzative",
     * nella sezione del Personale
     * ID sezione 63
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexOrganisationalPositions(): void
    {
        $data = [];

        $validation = new Validator();
        if (Input::get('n') != null) {
            $validation->verifyToken();
        }
        $validation->label('Nominativo')->value(Input::get('n'))->maxLength(50)->end();

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
        $personnel = $this->getDataResultsForPublicIn('63', null, $data, false, null, 63);

        $personnel = !empty($personnel) ? $personnel->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Creo le righe della tabella settando i dati
        $table = $this->createTableRows($personnel, null,63);

        $formFilterFields = [
            'n' => Input::get('n', true)
        ];

        $countFilterFields = 0;
        foreach ($formFilterFields as $value) {
            if (!empty($value)) {
                $countFilterFields += 1;
            }
        }

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
                    'name' => 'n',
                    'id' => 'name',

                    'value' => '{name}',
                ],
                'label' => [
                    'for' => 'name',
                    'text' => 'Nominativo',
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

        $formBuilder = new FormBuilder([
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'name' => htmlEscape(Input::get('n', true)),
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $personnel;
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

        renderFront(config('vfo', null, 'app') . '/personnel/personnel', $data, 'frontend');
    }

    /**
     * @description Metodo chiamato per la pagina "Titolari di incarichi dirigenziali amministrativi di vertice",
     * nella sezione del Personale
     * ID sezione 58
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexTopPositions(): void
    {
        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        $data = [];

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
        $personnel = $this->getDataResultsForPublicIn('58', 9, $data);
        $personnel = !empty($personnel) ? $personnel->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = null;
        $data['instances'] = $personnel;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = !empty($currentPage['no_required']);
        // Mostra le strutture per cui è referente il personale nelle card sul front-end
        $data['councilAndCouncillors'] = false;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;

        $data['archive'] = null;
        $data['rangeOpenData'] = 2000;


        renderFront(config('vfo', null, 'app') . '/personnel/personnel_cards_view', $data, 'frontend');
    }

    /**
     * @description Metodo chiamato per la pagina "Dirigenti cessati",
     * nella sezione del Personale
     * ID sezione 61
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexExecutivesTerminated(): void
    {
        $this->pivot();
    }


    /**
     * @description Metodo chiamato per la pagina "Archivio",
     * nella sezione Organizzazione -> Titolari di incarichi politici, di amministrazione, di direzione o di governo -> Giunta ed assessori
     * ID sezione 250
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function councilAndCouncillorsArchive(): void
    {
        $this->pivot();
    }


    /**
     * @description Metodo chiamato per la pagina "Archivio",
     * nella sezione Organizzazione -> Titolari di incarichi politici, di amministrazione, di direzione o di governo -> Consiglio comunale
     * ID sezione 251
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function cityCouncilArchive(): void
    {
        $this->pivot();
    }

    /**
     * @description Metodo chiamato per la pagina "Titolari di incarichi politici, di amministrazione, di direzione o di governo",
     * nella sezione Organizzazione
     * ID sezione 37
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexPositionHolders(): void
    {
        $data = [];

        $validation = new Validator();
        if (Input::get('n') != null) {
            $validation->verifyToken();
        }

        $validation->label('Nominativo')->value(Input::get('n'))->maxLength(100)->end();

        if (!$validation->isSuccess()) {
            echo showError('Attenzione', $validation->getErrorsHtml(), $validation->getErrorStatusCode());
            die();
        }

        // Id della pagina corrente
        $currentPageId = (int)uri()->segment(2, 0);

        //Id tipo ente
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];

        // Recupero il Personale da mostrare
        $personnel = PersonnelModel::where(function ($query) {
            })
            ->join('rel_personnel_public_in', 'rel_personnel_public_in.object_personnel_id', '=', 'object_personnel.id', 'left outer')
            ->filter(
                (Input::get('n', true)),
            )
            ->where(function ($query){
                //tutti coloro che sono associati a un organo politico o che hanno pubblica in questa pagina.
                $query->whereHas('political_organ')
                    ->orWhere('rel_personnel_public_in.public_in_id', '37');
            })
            ->with('political_organ')
            ->with('responsible_structures:id,structure_name,archived')
            ->with('role:id,name')
            ->groupBy('object_personnel.id')
            ->orderBy('priority', 'ASC')
            ->orderBy('full_name', 'ASC')
            ->paginate(9, ['object_personnel.id', 'full_name', 'email', 'photo', 'role_id', 'political_role', 'title', 'phone'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get(['n', 'sec_token']))
            ->setPath(currentUrl());
        $personnel = !empty($personnel) ? $personnel->toArray() : [];

        $existPersonnel = !empty($personnel['data']);

        $table = $this->createTableRows($personnel, 'political_organ_tab',240);

        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $personnel;

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
        if (!empty($personnel['data'])) {
            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = optional(PersonnelModel::select(['object_personnel.created_at'])
                ->join('rel_personnel_public_in', 'rel_personnel_public_in.object_personnel_id', '=', 'object_personnel.id', 'left outer')
                ->where(function ($query){
                    //tutti coloro che sono associati a un organo politico o che hanno pubblica in questa pagina.
                    $query->whereHas('political_organ')
                        ->orWhere('rel_personnel_public_in.public_in_id', '37');
                })
                ->orderBy('object_personnel.created_at', 'ASC')
                ->first())
                ->toArray();
            $lasUpdated = optional(PersonnelModel::select(['object_personnel.updated_at'])
                ->join('rel_personnel_public_in', 'rel_personnel_public_in.object_personnel_id', '=', 'object_personnel.id', 'left outer')
                ->where(function ($query){
                    //tutti coloro che sono associati a un organo politico o che hanno pubblica in questa pagina.
                    $query->whereHas('political_organ')
                        ->orWhere('rel_personnel_public_in.public_in_id', '37');
                })
                ->orderBy('object_personnel.updated_at', 'DESC')
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
                'prefix' => '<div class="form-group col-lg-12">',
                'suffix' => '</div>',
                'type' => 'form_input',
                'attributes' => [
                    'name' => 'n',
                    'id' => 'name',

                    'value' => '{name}',
                ],
                'label' => [
                    'for' => 'name',
                    'text' => 'Nominativo',
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

        $formBuilder = new FormBuilder([
            'current_url' => currentUrl(),
            'redirect_url' => currentUrl(),
            'name' => htmlEscape(Input::get('n', true)),
        ]);

        $formBuilder->loadJsonFromVar($form);
        $data['formFilter'] = $formBuilder->render();

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = !empty($currentPage['no_required']);
        $data['councilAndCouncillors'] = false;
        $data['cityCouncil'] = true;
        $data['allowSearch'] = true;
        //Abilita la paginazione AJAX della tabella dei risultati (vedi renderFrontFragment)
        $data['ajaxPagination'] = true;
        // Non applica il margine
        $data['noMargineFilterSearch'] = true;
        //Mostra il link alla gestione degli elementi di quest'archivio nel front-office se si è loggati
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['rangeOpenData'] = 2000;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];

        renderFront(config('vfo', null, 'app') . ($existPersonnel ? '/personnel/personnel' : '/pivot/pivot'), $data, 'frontend');
    }

    /**
     * @description Metodo chiamato per la pagina "Titolari di incarichi di amministrazione, di direzione o di governo",
     * nella sezione Organizzazione -> Titolari di incarichi politici, di amministrazione, di direzione o di governo
     * ID sezione 246
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexHoldersOfAdministrativePositions(): void
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
        $personnel = $this->getDataResultsForPublicIn('246', 9, $data);
        $personnel = !empty($personnel) ? $personnel->toArray() : [];

        $table = null;
        if($this->orgIndPolTabularDisplay) {
            $table = $this->createTableRows($personnel, 'political_organ_tab',246);
        }

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = !empty($table) ? $table->generate() : $table;
        $data['instances'] = $personnel;
        $data['instance'] = $currentPage;
        $data['noRequiredPublication'] = !empty($currentPage['no_required']);
        $data['councilAndCouncillors'] = false;
        $data['cityCouncil'] = false;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;

        $data['archive'] = null;
        $data['rangeOpenData'] = 2000;

        renderFront(config('vfo', null, 'app') . ($this->orgIndPolTabularDisplay ? '/personnel/personnel' :'/personnel/personnel_cards_view'), $data, 'frontend');
    }

    /**
     * @description Metodo chiamato per la pagina "Segretario Generale",
     * nella sezione Personale -> Titolari di incarichi dirigenziali amministrativi di vertice
     * ID sezione 59
     *
     * @url /page/page_id/page_name
     * @return void
     * @throws Exception
     */
    public function indexGeneralSecretary(): void
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
        $personnel = $this->getDataResultsForPublicIn('59', 9, $data);
        $personnel = !empty($personnel) ? $personnel->toArray() : [];

        // Recupero il contenuto della pagina e i relativi richiami
        $contents = getPageContents($currentPageId);

        // Dati passati alla vista
        $data['currentPageId'] = $currentPageId;
        $data['pageName'] = !empty($currentPage['label']) ? $currentPage['label'] : $currentPage['name'];
        $data['menuPages'] = $sectionFO;
        $data['paragraphs'] = $contents;
        $data['table'] = null;
        $data['instances'] = $personnel;
        $data['instance'] = $currentPage;
        $data['councilAndCouncillors'] = false;
        $data['linkDownloadOpenData'] = !empty($currentPage['controller_open_data']) ? $currentPage['id'] : null;
        $data['archive'] = null;
        $data['rangeOpenData'] = 2000;

        renderFront(config('vfo', null, 'app') . '/personnel/personnel_cards_view', $data, 'frontend');
    }


    /**
     * @description Funzione che ritorna i dati da mostrare nella tabella
     *
     * @param int|null $publicInId La sezioni "pubblica in" per cui filtrare i dati
     * @param int|null $itemPerPage Numero di elementi da mostrare per pagina nella paginazione delle tabelle
     * @param array $data Dati da passare alla vista
     * @return mixed
     * @throws Exception
     */
    protected function getDataResultsForPublicIn(int $publicInId = null, int|null $itemPerPage = 20, array &$data = [], $archived = false, ?string $type = null, int $pageId = 0): mixed
    {

        // Recupero il personale da mostrare nella pagina in base
        $personnel = PersonnelModel::filter(
            (Input::get('n', true)),
        )
            ->with('referent_structures:id,structure_name')
            ->with('responsible_structures:id,structure_name,archived')
            ->with('role:id,name')
            ->with('political_organ')
            ->join('rel_personnel_public_in', 'rel_personnel_public_in.object_personnel_id', '=', 'object_personnel.id')
            ->where('rel_personnel_public_in.public_in_id', $publicInId);

        if($archived) {
            $applied = false;

            if ($applied === false) {
                $personnel->whereRaw('1 = 0');
            }
        } else {
            $personnel->where(function ($query) {
            });
        }

        $personnel= $personnel->orderBy('priority', 'ASC')
            ->orderBy('full_name', 'ASC')
            ->groupBy('object_personnel.id')
            ->paginate($itemPerPage, ['object_personnel.id', 'full_name', 'email', 'photo', 'role_id', 'political_role', 'title'], 'p', boundedQueryInt('p', 10000))
            ->onEachSide(2)
            ->appends(Input::get(['n', 'sec_token']))
            ->setPath(currentUrl());

        // Richiesta del solo frammento (paginazione AJAX): essendo getDataResultsForPublicIn()
        // chiamato dalle pagine con tabella dei risultati prima del lavoro pesante (form filtro,
        // render completo), un solo controllo qui copre quelle pagine. $type/$pageId riproducono
        // gli stessi argomenti che il chiamante passa al proprio createTableRows().
        if (isFrontFragmentRequest()) {
            $resultsArray = $personnel->toArray();
            $table = $this->createTableRows($resultsArray, $type, $pageId);
            renderFrontFragment(['table' => !empty($table) ? $table->generate() : null, 'instances' => $resultsArray]);
            exit;
        }

        //Se ci sono elementi da pubblicare allora eseguo la query per le informazioni sulle dati di creazione e di aggiornamento
        if (!empty($personnel) && !empty($personnel->toArray()['data'])) {

            //Prendo la data di creazione del primo elemento creato e di modifica dell'ultimo elemento modificato tra quelli
            //da pubblicare nella pagina
            $firstCreated = PersonnelModel::select(['object_personnel.created_at'])
                ->join('rel_personnel_public_in', 'rel_personnel_public_in.object_personnel_id', '=', 'object_personnel.id')
                ->where('rel_personnel_public_in.public_in_id', $publicInId);

            if($archived) {
                $applied = false;

                if ($applied === false) {
                    $firstCreated->whereRaw('1 = 0');
                }
            } else {
                $firstCreated->where(function ($query) {
                });
            }


            $firstCreated = optional($firstCreated->orderBy('created_at', 'ASC')
                ->first())
                ->toArray();

            $lasUpdated = PersonnelModel::select(['object_personnel.updated_at'])
                ->join('rel_personnel_public_in', 'rel_personnel_public_in.object_personnel_id', '=', 'object_personnel.id')
                ->where('rel_personnel_public_in.public_in_id', $publicInId);

            if($archived) {
                $applied = false;

                if ($applied === false) {
                    $lasUpdated->whereRaw('1 = 0');
                }
            } else {
                $lasUpdated->where(function ($query) {
                });
            }

            $lasUpdated = optional($lasUpdated->orderBy('updated_at', 'DESC')
                ->first())
                ->toArray();

            $createdUpdatedInfo = array_merge($firstCreated, $lasUpdated);
        }

        $data['latsUpdatedElement'] = $createdUpdatedInfo ?? [];
        return $personnel;
    }

    /**
     * @description Metodo che crea la tabella con i dati dei risultati della ricerca da mostrare
     *
     * @param mixed|null $personnel Dati da inserire nella tabella
     * @param string|null $type Indica se il personale
     *                               è archiviato o meno
     * @return Table|null
     * @throws Exception
     */
    protected function createTableRows(mixed $personnel = null, string $type = null, int $pageId = 0): ?Table
    {
        $currentPageId = (int)uri()->segment(2, 0);
        $table = null;

        if (!empty($personnel['data'])) {

            // Creo la tabella da mostrare nella pagina con i risultati della ricerca
            $table = new Table();
            if ($type == 'archive') {
                $table->set_heading('Nome', 'Ruolo', 'In carica da', 'In carica fino a', 'Contratto tempo determinato');
            } elseif($type == 'archive_position_holders') { //Cessati dall'incarico
                $table->set_heading('Nome', 'Organo', 'In carica da', 'In carica fino a');
            } elseif($type == 'political_organ') {
                $table->set_heading('Nome', 'In carica da', 'In carica fino a');
            } elseif($type == 'political_organ_tab') {
                $table->set_heading('Nome', 'Organo');
            } else {
                $hideResponsibleFor = null;

                $table->set_heading('Nome', empty($hideResponsibleFor) ? 'Responsabile per' : '');
            }

            // Creo le righe della tabella settando i dati da mostrare
            foreach ($personnel['data'] as $p) {

                $name = !empty($p['title']) ? $p['title'] . ' ' : '';
                $name .= escapeXss($p['full_name']);
                $rows = [
                    '<a href="' . siteUrl('page/' . $currentPageId . '/details/' . $p['id'] . '/' . urlTitle($p['full_name'])) . '" data-id="' . $p['id'] . ' ">' . $name . '</a>'
                ];

                // Righe per la pagina archivio
                if ($type == 'archive') {

                    $rows[] = !empty($p['role']) ? escapeXss($p['role']['name']) : null;
                    $rows[] = !empty($p['in_office_since'])
                        ? date('d/m/Y', strtotime($p['in_office_since']))
                        : null;
                    $rows[] = !empty($p['in_office_until'])
                        ? date('d/m/Y', strtotime($p['in_office_until']))
                        : null;
                    $rows[] = !empty($p['determined_term']) ? 'Si' : 'No';

                } elseif($type == 'archive_position_holders') {

                    $politicalOrgans = array_map(fn($item) => $this->getSectionName($item['section_fo_id']), (!empty($p['political_organ']) ? $p['political_organ'] : []));
                    $rows[] = implode(', ', $politicalOrgans);
                    $rows[] = !empty($p['in_office_since'])
                        ? date('d/m/Y', strtotime($p['in_office_since']))
                        : null;
                    $rows[] = !empty($p['in_office_until'])
                        ? date('d/m/Y', strtotime($p['in_office_until']))
                        : null;
                } elseif($type == 'political_organ') {

                    $rows[] = !empty($p['in_office_since'])
                        ? date('d/m/Y', strtotime($p['in_office_since']))
                        : null;
                    $rows[] = !empty($p['in_office_until'])
                        ? date('d/m/Y', strtotime($p['in_office_until']))
                        : null;
                } elseif($type == 'political_organ_tab') {

                    $politicalOrgans = array_map(fn($item) => $this->getSectionName($item['section_fo_id']), (!empty($p['political_organ']) ? $p['political_organ'] : []));
                    $rows[] = implode(', ', $politicalOrgans);

                } else {

                    if(empty($hideResponsibleFor)) {
                        // Creo link le strutture
                        $structures = escapeXss($p['responsible_structures'], true, false);
                        $filter = function ($structure) {
                            return '<a href="' . siteUrl('page/40/details/' . $structure['id'] . '/' . urlTitle($structure['structure_name'])) . '">' . escapeXss($structure['structure_name']) . '</a>';
                        };
                        $linkedStructures = array_map($filter, $structures);

                        $rows[] = implode('</br>', $linkedStructures);
                    } else {
                        $rows[] = '';
                    }
                }

                $table->add_row($rows);
            }
        }

        return $table;
    }

    /**
     * @description Funzione che ritorna i dati da mostrare nella pagina di dettaglio
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
        $element = PersonnelModel::where('id', $elementId);

        $element = $element->with('role:id,name,political,po_dir_fields')
            ->with('qualification:id,name')
            ->with('referent_structures:id,structure_name,archived')
            ->with('responsible_structures:id,structure_name,archived')
            ->with('president_of', function ($q) {
                $q->select(['object_commissions.id', 'name', 'president_id', 'object_commissions.typology', 'object_commissions.order']);
                $q->orderBy('order');
            })
            ->with('commissions', function ($q) {
                $q->select(['object_commissions.id', 'name', 'object_commissions.typology', 'object_commissions.order']);
                $q->orderBy('order');
            })
            ->with('assignments:id,object,name')
            ->with('measures:id,object')
            ->with('responsibles:id,name,archived')
            ->with('measure_responsibles:id,name,archived')
            ->with('political_organ')
            ->with('historical_datas');

        $element = $element->first();

        // Se l'elemento non esiste mostro la pagina di errore
        if (empty($element)) {
            echo show404('Ops..', 'record not found');
            exit();
        }


        $detailPage = '/personnel/details';
        $archiveBread = '';

        // Recupero le informazioni della pagina corrente
        $currentPage = SectionsFoModel::select(['name', 'id', 'parent_id', 'archive_name'])
            ->where('id', $currentPageId)
            ->first()
            ->toArray();

        // Recupero le voci per costruire il menu dei contenuti
        $sectionFO = getRightOrBottomMenu($currentPageId, $currentPage['parent_id']);

        if(!is_array($element)){
            $element = $element->toArray();
        }

        $commissions = array_merge($element['commissions'], $element['president_of']);

        $data['commissions'] = [];

        if(!empty($commissions)){
            foreach ($commissions as $comm){
                if(!array_key_exists($comm['order'].'_'.$comm['id'],$data['commissions'])){
                    $data['commissions'][$comm['order'].'_'.$comm['id']] = [
                        'name' => $comm['name'],
                        'typology' => $comm['typology'],
                        'order' => $comm['order'],
                        'roles' => !empty($comm['president_id']) ? 'Presidente/Capogruppo' : getCommissionRole($comm['pivot']['typology'])
                    ];
                } else{
                    if(!empty($comm['president_id'])) {
                        $data['commissions'][$comm['order'].'_'.$comm['id']]['roles'] = 'Presidente/Capogruppo, ' . $data['commissions'][$comm['order'].'_'.$comm['id']]['roles'];
                    } else {
                        $data['commissions'][$comm['order'].'_'.$comm['id']]['roles'] .= ", ". getCommissionRole($comm['pivot']['typology']);
                    }
                }

            }
        }

        //Ordino le commissioni in base al campo ordine
        if(!empty($data['commissions'])) {
            array_multisort(array_column($data['commissions'], 'order'), SORT_ASC, $data['commissions']);
        }

        //organi politici
        if(!empty($element['political_organ'])){
            foreach ($element['political_organ'] as $key => $val){
                $element['political_organ'][$key]['value'] = $this->getSectionName($val['section_fo_id']);
            }
        }

        // Dati passati alla vista
        $data['pageName'] = $element['full_name'];
        $data['menuPages'] = $sectionFO;

        // aggiungi al Breadcrumbs la pagina del dettaglio
        $data['concatBreadcrumb'] = true;
        $data['bread'] = array();
        if (!empty($archiveBread)) {
            $data['bread'][] = array('name' => 'Archivio personale', 'link' => $archiveBread);
        }
        $data['bread'][] = array('name' => $element['full_name'], 'link' => '/');
        $data['currentPageId'] = $currentPageId;
        $data['archive'] = !empty($currentPage['archive_name']) ? $currentPage['archive_name'] : null;
        $data['editRecord'] = true;

        $label = 'personnel';
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
            'file_ext',
            'label',
            'indexable',
            'active',
            'created_at',
            'updated_at'];

        //$showOnlyPublic = true;
        $sectionId = (int)uri()->segment(2, 0);

            // Allegati
            $attach = new AttachmentArchive();
            $data['listAttach'] = $attach->getAllByObject(
                $label,
                $elementId,
                $selectFields,
                true
            );

        $data['labeling'] = [
            'referent_structures' => "Referente per le strutture"
        ];

        $data['instance'] = is_array($element) ? $element : $element->toArray();

        renderFront(config('vfo', null, 'app') . $detailPage, $data, 'frontend');
    }

    /**
     * @description Funzione che setta le informazioni sulle pagine Sindaco e Vicesindaco
     * @return array
     */
    protected function setMayorPagesData(): array
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
        return array($data, $currentPageId, $currentPage, $sectionFO);
    }

    private function createPoliticTableRows($values, $type = 'structures'): ?Table
    {
        // Creo la tabella da mostrare nella pagina con i risultati della ricerca
        $table = "";

        if($type == 'structures'){
            $table = new Table();
            $table->set_heading('Denominazione', 'Livello');

            if(!empty($values['management_structure'])){
                foreach ($values['management_structure'] as $str){

                    $link = !empty($str['archived']) ?
                        escapeXss($str['structure_name'])." [Elemento archiviato]" :
                        '<a target="_blank" href="' . siteUrl('page/40/details/' . $str['id'] . '/' . urlTitle($str['structure_name'])) . '">'.escapeXss($str['structure_name']).'</a>';

                    $table->add_row([$link,'Dirigenziale']);
                }

            }

            if(!empty($values['non_management_structure'])){
                foreach ($values['non_management_structure'] as $str){

                    $link = !empty($str['archived']) ?
                        escapeXss($str['structure_name'])." [Elemento archiviato]" :
                        '<a target="_blank" href="' . siteUrl('page/40/details/' . $str['id'] . '/' . urlTitle($str['structure_name'])) . '">'.escapeXss($str['structure_name']).'</a>';

                    $table->add_row([$link,'Non dirigenziale']);
                }

            }

        }elseif ($type == 'personnel'){

            $table = new Table();
            $table->set_heading('Nome', 'Ruolo');
            foreach ($values as $pers){
                $link = !empty($pers['archived']) ?
                    escapeXss($pers['title']." ".$pers['full_name'])." [Elemento archiviato]" :
                    '<a target="_blank" href="' . siteUrl('page/37/details/' . $pers['id'] . '/' . urlTitle($pers['full_name'])) . '">'.$pers['title']." ".escapeXss($pers['full_name']).'</a>';

                $role = !empty($pers['role']) ? $pers['role']['name'] : '';
                $table->add_row([$link, $role]);
            }
        }

        return $table;

    }

}
