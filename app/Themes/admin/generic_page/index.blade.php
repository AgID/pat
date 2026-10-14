{{--  Pagine Generiche index (Paginazione) --}}
<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
{% extends layout/master %}
{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}
<div class="row">
    <div class="col-lg-4">
        <div class="card card-default" style="overflow:hidden;">
            <div class="card-header modal-header-patos">
                <h3 class="card-title">
                    Scegli la pagina da amministrare
                </h3>
            </div>

            <div class="card-body" style="padding: 1rem .2rem">
                <div id="tree"></div>
            </div>
        </div>
    </div>

    <div class="col-lg-8">

        {{-- Layer iniziale --}}
        <div class="row" id="start_generic_page">
            <div class="col-md-12 text-muted text-center">
                <div class="mb-3">
                    <i style="font-size: 3.5rem;" class="fas fa-info-circle"></i>
                </div>
                <h5>
                    Seleziona una voce dal men&ugrave di sinistra per iniziare ad
                    amministrare le pagine generiche personalizzate.
                </h5>
            </div>
        </div>
    </div>

</div>

<!-- Modal per ordinamento sezioni -->
<style>
    #sortable-sections {
        min-height: 200px;
        list-style: none;
        padding: 0;
        position: relative;
    }

    #sortable-sections .sortable-item {
        border: 1px solid #dee2e6;
        margin-bottom: 5px;
        padding: 15px;
        background-color: #f8f9fa;
        transition: all 0.3s ease;
        user-select: none;
        touch-action: none;
        cursor: default; /* Cursore normale per tutto l'elemento */
    }

    #sortable-sections .sortable-item:hover {
        background-color: #e9ecef;
        box-shadow: 0 2px 4px rgba(0,0,0,0.1);
    }

    #sortable-sections .sortable-item.dragging {
        background-color: #007bff;
        color: white;
        box-shadow: 0 8px 16px rgba(0,0,0,0.3);
        opacity: 0.9;
    }

    .sortable-placeholder {
        height: 60px;
        background-color: #fff3cd;
        border: 2px dashed #ffc107;
        margin-bottom: 5px;
        border-radius: 4px;
        display: flex;
        align-items: center;
        justify-content: center;
        color: #856404;
        font-style: italic;
    }


    .sortable-placeholder::before {
        content: "Rilascia qui";
    }

    .drag-handle {
        cursor: grab !important; /* Cursore grab solo sull'icona */
        color: #6c757d;
        font-size: 1.2em;
        padding: 5px;
        transition: color 0.2s;
    }

    .drag-handle:hover {
        color: #495057;
        cursor: grab !important;
    }

    .drag-handle:active {
        cursor: grabbing !important;
    }

    .section-name {
        font-weight: 500;
        color: #333;
        flex: 1;
        pointer-events: none;
        cursor: default; /* Cursore normale sul testo */
    }

    /* Responsive */
    @media (max-width: 768px) {
        #sortable-sections .sortable-item {
            padding: 10px;
        }

        .drag-handle {
            font-size: 1.4em;
            padding: 8px;
        }
    }
</style>
<div class="modal fade" id="sortSectionsModal" tabindex="-1" role="dialog" aria-labelledby="sortSectionsModalLabel" aria-hidden="true">
    <div class="modal-dialog modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="sortSectionsModalLabel">Ordina Sezioni</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Chiudi">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="alert alert-info">
                    <h5>
                        <i class="fas fa-info-circle"></i>
                        Trascina le sezioni per modificare l'ordine di visualizzazione
                    </h5>
                </div>
                <ul id="sortable-sections" class="list-group"></ul>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                <button type="button" class="btn btn-primary" id="saveSortOrder">Salva Ordine</button>
            </div>
        </div>
    </div>
</div>


<!-- Modal subsection -->
{{-- Modale per l'aggiunta, la modifica e la duplicazione di una sezione --}}
{{ form_open('admin/generic-page/section/register',['name'=>'form_custom_section','id'=>'form_custom_section','class'=>'form_custom_section']) }}
<div class="modal fade zoom-in" id="subsection" tabindex="-1" role="dialog" aria-labelledby="exampleModalSubsection"
     aria-hidden="true">
    <div class="modal-dialog modal-lg modal-dialog-centered" role="document">
        <div class="modal-content" style="min-height: 600px;">
            <div class="modal-header modal-header-patos">
                <h5 class="modal-title" id="exampleModalLabel">Modifica dati Pagina</h5>
                <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="justify-content-center mt-5 pt-5" id="spinner">
                    <div class="col-md-12 text-center" id="loading-l">
                        <div class="spinner-border mb-2" role="status"></div>
                        <img id="" src="<?php echo baseUrl('assets/admin/img/pat_logo_nero.png')?>">
                    </div>
                </div>

                <div id="_form_content_">
                    <div id="custom-page">
                        <div class="form-group">
                            <label for="custom_section">Nome sezione*:</label>
                            {{ form_input([
                                'name' => 'custom_section',
                                'class' => 'form-control custom_section',
                                'id' => 'custom_section',
                                'placeholder' => 'Nome della sezione',
                                'value' => ' ',
                            ]) }}
                        </div>
                        {{-- Select 2 inserita solo in caso di duplicazionie di una sezione --}}
                        <div id="list_section_tree"></div>
                        @if(checkAlternativeInstitutionId() == 0)
                            <div class="text-muted col-md-12 mb-4" id="tree-msg">
                                <i class="fas fa-exclamation-circle"></i> {{ nbs(1) }} Attenzione, puoi scegliere solo
                                una
                                pagina appartenente allo stesso ente di quella che stai duplicando!
                            </div>
                        @endif
                        <div class="form-group">
                            <label for="meta_keywords">Parole chiave:</label>
                            {{ form_input([
                                'name' => 'meta_keywords',
                                'class' => 'form-control meta_keywords',
                                'id' => 'meta_keywords',
                                'placeholder' => "Parole chiave per l'indicizzazione nei motore di ricerca",
                                'value' => ' ',
                            ]) }}
                        </div>
                    </div>

                    <div>
                    </div>

                    <div>
                        {{-- Inclusione di librerie esterne --}}
                    </div>
                </div>
            </div>
            <div class="modal-footer" id="subsection-footer">
                <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Annulla</button>
                <button type="submit" id="register_section" class="btn btn-primary"><i class="far fa-save"></i>&nbsp;
                    Registra
                </button>
            </div>
        </div>
    </div>
</div>
<input type="hidden" id="custom_section_parent_id" class="custom_section_parent_id" value="">
<input type="hidden" id="mode_storage_custom_section" class="mode_storage_custom_section" value="insert">
<input type="hidden" id="custom_section_id" class="custom_section_id" value="">
<input type="hidden" id="custom_section_institution_id" class="custom_section_institution_id" value="">
{{ form_close() }}

@if(!empty($sectionId))
    {{ form_input([
        'type' => 'hidden',
        'name' => 'section_id',
        'value' => !empty($sectionId) ? $sectionId : '',
        'id' => 'section_id',
        'class' => 'section_id',
    ]) }}
@endif


{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
{{ css('jsTree/dist/themes/default/style.min.css','common') }}
{{ css('jquery-ui/jquery-ui.css','common') }}

<style>
</style>
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
{{ js('jsTree/dist/jstree.min.js','common') }}
{{ js('patos/jquery.patOsAjaxPagination.js', 'common') }}

{{ js(verifyFirstOrLastSlash('admin/get/config.js')) }}
<script type="text/javascript">

    {{-- Variabile section ID --}}
    var currentSectionId = null;
    var currentSectionStorageMode = 'insert';
    var currentSourceSectionId = null;

    let responsible = null;

     let $dropdownVisibile = $('.select2-hide');
            $dropdownVisibile.select2({
                minimumResultsForSearch: -1,
                placeholder: 'Seleziona',
            });

    // alla chiusura del modale resetto il valore della select
    $('#subsection').on('hidden.bs.modal', function () {
        $dropdownVisibile.val(null).trigger('change');
    });

    {{-- Templates --}}
    /**
     * Metodo che setta dinamicamente i pulsanti con le operazioni che si possono eseguire sulle pagine
     * @returns {string}
     */
    function btnOperations() {

        const args = arguments;
        const id = args[0];
        const isSystem = args[1];
        let institutionId = args[2];
        let selectedNode = args[3];
        let parentName = args[4];
        let permit = args[5];
        let tag = args[6];
        let pageUrl ='{{ str_replace('.html', '', siteUrl()) }}/page' + (tag ? '_tg/' : '/') + id + '/' + htmlEncode(convertToSlug(selectedNode));


        let html = '';
        html += '<div class="col-md-12 text-right margin">';
        html += `<div style="display: inline-flex;" class="mr-2"><a target="_blank" type="button" href="${pageUrl}" class="btn btn-outline-secondary" title="Naviga pagina">`;
        html += '<span id="icon_current_section_operation"><i class="fas fa-eye mr-1"></i></span> Naviga la pagina';
        html += '</a></div>';
        if (permit) {
            html += '<div class="dropdown" style="display: inline-flex;">';
            html += '<button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown" ';
            html += 'aria-expanded="false">';
            html += '<span id="icon_current_section_operation"><i class="fas fa-plug mr-1"></i></span> Operazioni';
            html += '</button>';
            html += '<ul class="dropdown-menu dropdown-menu-right" style="width: 300px">';
            html += '<li>';
            @if(checkAlternativeInstitutionId() != 0)
                html += '<a href="#!" title="Aggiungi Sottosezione" data-id="' + id + '" id="add_section" data-institution-id="' + institutionId + '" class="dropdown-item add-generic-page" ';
            html += 'type="button">';
            @else
            if (!isSystem) {
                html += '<a href="#!" title="Aggiungi Sottosezione" data-id="' + id + '" id="add_section" data-institution-id="' + institutionId + '" class="dropdown-item add-generic-page" ';
                html += 'type="button">';
            } else {
                html += '<a class="select-institution dropdown-item" type="button" href="#!" title="Aggiungi Sottosezione">';
            }
            @endif
                html += '<i class="fas fa-plus-circle mr-1"></i> Aggiungi Sottosezione';
            html += '</a>';
            html += '</li>';
            html += '<li>';
            html += '<div class="dropdown-divider"></div>';
            html += '</li>';
            html += '<li>';
            @if(checkAlternativeInstitutionId() != 0)
                html += '<a href="{{ rtrim(str_replace('.html', '', siteUrl('admin/generic-page/paragraph/add/')), '/') }}/' + id + '.html" data-id="' + id + '" data-sys="' + isSystem + '" title="Aggiungi Paragrafo" ';
            html += 'id="add_paragraph" class="dropdown-item" type="button"> ';
            @else
            if (!isSystem) {
                html += '<a href="{{ rtrim(str_replace('.html', '',siteUrl('admin/generic-page/paragraph/add/')), '/') }}/' + id + '.html" data-id="' + id + '" data-sys="' + isSystem + '" title="Aggiungi Paragrafo" ';
                html += 'id="add_paragraph" class="dropdown-item" type="button"> ';
            } else {
                html += '<a class="select-institution dropdown-item" type="button" href="#!" title="Aggiungi Paragrafo">';
            }
            @endif
                html += '<i class="fas fa-plus-circle mr-1"></i> Aggiungi Paragrafo nella sezione';
            html += '</a>';
            html += '</li>';

            {{-- BTN action --}}
            if (isSystem === false && tag === false) {

                html += '<li class="drop-down-current-section">';
                html += '<i class="fas fa-puzzle-piece"></i> {{ nbs(2) }}OPERAZIONI SU SEZIONE CORRENTE';
                html += '</li>';
                html += '<li>';
                html += '<li>';
                html += '<a href="#!" data-id="' + id + '" data-institution-id="' + institutionId + '" title="Modifica sezione corrente" ';
                html += 'id="edit_current_section" class="dropdown-item" type="button"> ';
                html += '<i class="far fa-edit mr-1"></i> Modifica sezione';
                html += '</a>';
                html += '</li>';
                html += '<li>';
                html += '<a href="#!" data-id="' + id + '" data-institution-id="' + htmlEncode(institutionId) + '" data-selected-section="' + htmlEncode(selectedNode) + '" title="Duplica sezione corrente" ';
                html += 'id="duplicate_current_section" class="dropdown-item" type="button"> ';
                html += '<i class="fas fa-clone mr-1"></i> Duplica sezione';
                html += '</a>';
                html += '</li>';

                {{-- Inclusione di librerie esterne --}}

                    html += '<li>';
                html += '<li>';
                html += '<a href="#!" data-id="' + id + '" data-institution-id="' + institutionId + '" title="Sposta su la sezione corrente di una posizione" ';
                html += 'id="movie_up_current_section" class="dropdown-item" type="button"> ';
                html += '<i class="fas fa-arrow-up mr-1"></i> Sposta su la sezione di una posizione';
                html += '</a>';
                html += '</li>';
                html += '<li>';
                html += '<a href="#!"  data-id="' + id + '" data-institution-id="' + institutionId + '" title="Sposta gi&ugrave; la sezione corrente di una posizione" ';
                html += 'id="movie_down_current_section" class="dropdown-item" type="button"> ';
                html += '<i class="fas fa-arrow-down mr-1"></i> Sposta gi&ugrave; la sezione di una posizione';
                html += '</a>';
                html += '</li>';
                html += '<li>';
                html += '<a href="#!" data-id="' + id + '" data-section-name="' + htmlEncode(selectedNode) + '" data-parent-name="' + htmlEncode(parentName) + '" data-institution-id="' + institutionId + '" title="Elimina sezione corrente" ';
                html += 'id="delete_current_section" class="dropdown-item" type="button"> ';
                html += '<span class="text-danger"><i class="fas fa-trash mr-1 "></i> Elimina sezione</span>';
                html += '</a>';
                html += '</li>';

            } else {
            }


            html += '</ul>';
            html += '</div>';
        }
        html += '</div>';

        return html;
    }

    /**
     * Metodo che mostra il messaggio nel caso in cui la pagina selezionata non ha contenuti
     *
     * @returns {string}
     */
    function noDataContents() {

        var args = arguments;
        var id = args[0];
        let isSystem = args[1];
        let permit = args[2];
        var html = '';



        html += '<div id="nodata" class="mt-5 col-md-12 text-muted text-center">';
        html += '<div class="mb-3">';
        html += '<i style="font-size: 3.5rem;" class="fas fa-exclamation"></i>';
        html += '<h5 class="mt-2">';
        html += 'Attualmente non ci sono paragrafi associato a questa sezione.';
        html += '</h5>';
        if(permit){
            html += '<div>';
            @if(checkAlternativeInstitutionId() != 0)
                html += '<a class="btn btn-primary" href="{{ baseUrl(verifyFirstOrLastSlash('admin/generic-page/paragraph/add/'), null, '/') }}' + id + '.html">';
            @else
            if (!isSystem) {
                html += '<a class="btn btn-primary" href="{{ baseUrl(verifyFirstOrLastSlash('admin/generic-page/paragraph/add/'), null, '/') }}' + id + '.html">';
            } else {
                html += '<a class="btn btn-primary select-institution" href="#!">';
            }
            @endif
                html += 'Aggiungi nuovo paragrafo';
            html += '</a>';
        }
        html += '</div>';
        html += '</div>';
        html += '</div>';

        return html;
    }

    /**
     * Appende il codice HTML per mostrare i contenuti della pagina selezionata
     *
     * @returns {string}
     */
    function actionSectionAndContents() {
        const args = arguments;
        const id = args[0].id;
        const name = args[0].label ?? args[0].name;
        const guide = args[0].guide;
        const normatives = args[0].normatives;
        const label = args[0].label;
        const checkWF = args[0]?.checkWf ?? false;
        const contents = args[1];
        const sectionIsSystem = args[0].is_system;
        const sectionSelectedNode = args[0].name;
        const institutionName = args[0].institution?.full_name_institution;
        const permit = args[2];
        let html = '';

        html += '<div class="col-md-12 mt-3">';
        html += '<div class="card card-success">';
        html += '<div class="card-body">';
        html += '<div class="row">';
        html += '<div class="col-md-12 col-lg-12 col-xl-12" style="line-height: 1.9rem;">';
        html += `<div class="text-dark">ID:  ${id}</div>`;
        html += '<h2 class="mb-5">' + htmlEncode(name) + '</h2>';
        html += '</div>';
        html += '</div>';


        if(!permit)
        {
            html += '<div class="row">';
            html += '<div class="alert alert-warning alert-dismissible col-md-12 mb-3" style="background-color: #f6edba; font-size: 14px;">';
            html += '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
            html += '<h5 style="color: #9c6c38;" class="text-muted"><i class="fas fa-info-circle"></i> Attenzione</h5>';
            html += '<div class="text-muted">';
            html += '<span>Non hai i permessi necessari per modificare il contenuto della pagina <strong>' + htmlEncode(name) + '</strong>.</span>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
        }

        // Se è presente il contenuto della pagina lo mostro
        if (contents !== null) {

                html += '<div class="row">';

                html += '<div class="col-md-8 col-lg-8 col-xl-8">';
                // html += '<strong class="text-muted">TITOLO</strong>';
                html += '</div>';

                if (permit) {
                    html += '<div class="col-md-4 col-lg-4 col-xl-4 text-center">';
                    html += '<strong class="text-muted">AZIONI</strong>';
                    html += '</div>';
                }

                html += '<div class="col-md-12 col-lg-12 col-xl-12">';
                html += '<hr />';
                html += '</div>';



                {{-- Ciclo sui paragrafi --}}
                $.each(contents, function (i, item) {
                    // console.log(item)
                    {{-- Inserisco il titolo del paragrafo --}}
                    html += `<div class="col-md-9 col-lg-9 col-xl-9"><div class="text-dark col-md-1 bg-gray-light">ID: ${item.id}</div>`;

                    let badge = '';
                    if(item?.workflow_state && item?.workflow_state !== 'finale'){
                        badge = `<span  data-toggle="tooltip" data-placement="top" title="Questo elemento si trova in un workflow di pubblicazione. Id stato attuale: ${item?.workflow_state}"><i class="fas fa-lock"  style="color: #7C3AED;"></i></span>&nbsp;`;
                    }
                    if (item?.publishing_status === 0){
                        badge += '&nbsp;<i class="fas fa-eye-slash" data-toggle="tooltip" data-placement="top" data-original-title="Elemento non pubblicato" style="color: #8E2558"></i>&nbsp;';
                    }

                    html += badge + '<h3 style="display: inline;">' + ((item.name) ? htmlEncode(item.name) : '') + '</h3> &nbsp;' ;
                    html += '</div>';

                    {{-- Inserisco le action sui paragrafi --}}
                    if (permit) {

                        if(checkWF && item?.workflow_state !== 'finale'){

                            if(item.wf_cont_perm){
                                html += '<div class="col-md-3 col-lg-3 col-xl-3 text-center">';
                                html += '<div class="btn-toolbar justify-content-center" role="toolbar" aria-label="Toolbar Azioni">';
                                html += '<div class="btn-group" role="group" aria-label="Azioni">';
                                html += '<a href="#!" title="Sposta su di una posizione" data-section-id="' + id + '" data-section-is-system="' + sectionIsSystem + '" data-section-name="' + htmlEncode(sectionSelectedNode) + '" data-id="' + item.id + '" data-institution-id="' + item.institution_id + '" data-parent-id="' + item.section_fo_parent_id + '"class="btn btn-sm btn-primary paragraph_up " data-toggle="tooltip" data-placement="top" data-original-title="Sposta su di una posizione"><i class="fas fa-chevron-up"></i></a>';
                                html += '<a href="#!" title="Sposta gi&ugrave; di una posizione" data-section-id="' + id + '" data-section-is-system="' + sectionIsSystem + '" data-section-name="' + htmlEncode(sectionSelectedNode) + '" data-id="' + item.id + '" data-institution-id="' + item.institution_id + '" data-parent-id="' + item.section_fo_parent_id + '" class="btn btn-sm btn-primary paragraph_down" data-toggle="tooltip" data-placement="top" data-original-title="Sposta gi&ugrave; di una posizione"><i class="fas fa-chevron-down"></i></a>';
                                html += '<a href="{{ baseUrl(verifyFirstOrLastSlash('admin/generic-page/paragraph/edit/'), null, '/') }}' + id + '.html?id=' + item.id + '" title="Modifica voce" data-id="' + item.id + '" id="edit_' + item.id + '" class="btn btn-sm btn-primary paragraph_edit" data-toggle="tooltip" data-placement="top" data-original-title="Modifica voce"><i class="fas fa-edit"></i></a>';
                                html += '<a href="#!" title="Elimina voce" data-section-id="' + id + '" data-section-is-system="' + sectionIsSystem + '" data-paragraph-name="' + htmlEncode(item.name) + '" data-section-name="' + htmlEncode(sectionSelectedNode) + '" data-id="' + item.id + '" data-institution-id="' + item.institution_id + '" data-parent-id="' + item.section_fo_parent_id + '" class="btn btn-sm btn-danger paragraph_delete" data-toggle="tooltip" data-placement="top" data-original-title="Elimina voce"><i class="fas fa-trash"></i></a>';
                                html += '</div>';
                                html += '</div>';
                                html += '</div>';
                            }else{
                                //non devo poter fare nulla
                                html += '<div class="col-md-3 col-lg-3 col-xl-3 text-center">';
                                html += '<div class="btn-toolbar justify-content-center" role="toolbar" aria-label="Toolbar Azioni">';
                                html += "<div>Questo elemento si trova in un workflow di pubblicazione e non possiedi i permessi necessari a effettuarne la modifica</div>";
                                html += '</div>';
                                html += '</div>';
                            }

                        }else{
                            html += '<div class="col-md-3 col-lg-3 col-xl-3 text-center">';
                            html += '<div class="btn-toolbar justify-content-center" role="toolbar" aria-label="Toolbar Azioni">';
                            html += '<div class="btn-group" role="group" aria-label="Azioni">';
                            html += '<a href="#!" title="Sposta su di una posizione" data-section-id="' + id + '" data-section-is-system="' + sectionIsSystem + '" data-section-name="' + htmlEncode(sectionSelectedNode) + '" data-id="' + item.id + '" data-institution-id="' + item.institution_id + '" data-parent-id="' + item.section_fo_parent_id + '"class="btn btn-sm btn-primary paragraph_up " data-toggle="tooltip" data-placement="top" data-original-title="Sposta su di una posizione"><i class="fas fa-chevron-up"></i></a>';
                            html += '<a href="#!" title="Sposta gi&ugrave; di una posizione" data-section-id="' + id + '" data-section-is-system="' + sectionIsSystem + '" data-section-name="' + htmlEncode(sectionSelectedNode) + '" data-id="' + item.id + '" data-institution-id="' + item.institution_id + '" data-parent-id="' + item.section_fo_parent_id + '" class="btn btn-sm btn-primary paragraph_down" data-toggle="tooltip" data-placement="top" data-original-title="Sposta gi&ugrave; di una posizione"><i class="fas fa-chevron-down"></i></a>';
                            html += '<a href="{{ baseUrl(verifyFirstOrLastSlash('admin/generic-page/paragraph/edit/'), null, '/') }}' + id + '.html?id=' + item.id + '" title="Modifica voce" data-id="' + item.id + '" id="edit_' + item.id + '" class="btn btn-sm btn-primary paragraph_edit" data-toggle="tooltip" data-placement="top" data-original-title="Modifica voce"><i class="fas fa-edit"></i></a>';
                            html += '<a href="#!" title="Duplica voce" data-id="' + item.id + '" data-section-id = "' + id + '" id="duplicate_' + item.id + '" data-section-name="' + htmlEncode(item.name) + '" class="btn btn-sm btn-primary paragraph_duplicate" data-toggle="tooltip" data-placement="top" data-original-title="Duplica voce"><i class="fas fa-clone"></i></a>';


                                html += '<a href="#!" title="Elimina voce" data-section-id="' + id + '" data-section-is-system="' + sectionIsSystem + '" data-paragraph-name="' + htmlEncode(item.name) + '" data-section-name="' + htmlEncode(sectionSelectedNode) + '" data-id="' + item.id + '" data-institution-id="' + item.institution_id + '" data-parent-id="' + item.section_fo_parent_id + '" class="btn btn-sm btn-danger paragraph_delete" data-toggle="tooltip" data-placement="top" data-original-title="Elimina voce"><i class="fas fa-trash"></i></a>';
                            html += '</div>';
                            html += '</div>';
                            html += '</div>';
                        }

                    }
                    html += '<div class="col-md-12 mt-3">';
                    html += truncate(stripTags(item.content), 380, '...');
                    html += '</div>';

                    if (item.recalls !== null || item.recalls.length > 0) {
                        {{-- Ciclo sui richiami(Nome dell'archivio) --}}
                        $.each(item.recalls, function () {
                            html += '<div class="col-md-12 mt-3">';
                            html += '<h4 style="font-weight: 600; font-size: .9rem;">' + htmlEncode(this.title.toUpperCase()) + '</h4>';
                            html += '</div>';
                            if (this.records.length > 0) {
                                html += '<div class="col-md-2" style="border-right: 1px #eaeaea solid;">' + (this.recall_name ? htmlEncode(this.recall_name) : 'TITOLO') + '</div>';
                                html += '<div class="col-md-10">';
                                {{-- Ciclo sui richiami(Singoli record da mostrare) --}}
                                $.each(this.records, function () {
                                    html += '<span style="padding:.1rem .6rem; background:#FAFAFA; border-radius: .5rem; margin: 0 .2rem .2rem 0; border:1px solid #eaeaea; display: inline-block;">' + this.field + '</span>'
                                });
                                html += '</div>';
                            } else {
                                {{-- Se non sono presenti record da mostrare nei richiami mostro un messaggio --}}
                                    html += '<div class="col-md-9 col-lg-9 col-xl-9" style="line-height: 1.9rem;">';
                                html += '<span class="text-muted uppercase"><i class="fas fa-info"></i> {{ nbs(2) }} NESSUNA INFORMAZIONE VIENE ATTUALMENTE RICHIAMATA DA ' + this.title.toUpperCase() + '</span>';
                                html += '</div>';
                            }
                        });
                    }

                    html += '<div class="col-md-12 col-lg-12 col-xl-12">';
                    html += '<hr />';
                    html += '</div>';
                });


        }

        html += '</div>';
        html += '</div>';
        html += '</div>';
        html += '</div>';

        $(function () {
            $('[data-toggle="popover"]').popover()
        })

        return html;

    }

    /**
     * Metodo per il salvataggio della sezione creata o modificata
     */
    function registerSection() {

        $('#register_section').off('click').on('click', function (e) {



            e.preventDefault();

            var btnSend = $('#register_section');

            var formData = {
                'parent_id': $('#custom_section_parent_id').val(),
                'institution_id': $('#custom_section_institution_id').val(),
                'mode': currentSectionStorageMode,
                'section_id': currentSourceSectionId,
                'name': $('#custom_section').val(),
                'url': $('#external_url').val(),
                'hide' : $('#hide').val(),
                'seo_h1': $('#input_seo_h1').val(),
                'seo_title': $('#input_seo_title').val(),
                'seo_description': $('#input_seo_description').val(),
                'meta_keywords': $('#meta_keywords').val(),
                'meta_descriptions': $('#meta_descriptions').val(),
                'responsible': $('#input_responsible_pub').val(),
                'select_tree': $('#select_tree').val(),
                {{ config('csrf_token_name',null,'app') }} : $('input[name="{{ config('csrf_token_name',null,'app') }}"]').val(),
            };

            $.ajax({
                type: 'POST',
                // async: false,
                url: '{{ siteUrl('admin/generic-page/section/register') }}',
                dataType: "JSON",
                data: formData,
                beforeSend: function () {
                    btnSend.empty().append('<i class="fas fa-spinner fa-spin"></i>&nbsp; Attendere ...').attr("disabled", true);
                },
                success: function (response) {

                    formData = {};

                    response = parseJson(response);

                    // Funzione che genera il toast con il messaggio di successo
                    {{-- (vedere nel footer) --}}
                    createValidatorFormSuccessToast(response.data.contents, 'ATTENZIONE', 'Azione');

                    setTimeout(function () {
                        $('#subsection').modal('hide');
                        $('#form_custom_section').trigger("reset");
                        $('#tree').jstree(true).settings.core.data.url = "{{siteUrl('admin/generic-page/list')}}";
                        $('#tree').jstree("refresh");
                    }, 1000)
                },
                complete: function () {
                    btnSend.empty().append('<i class="far fa-save"></i>&nbsp; Salva').attr("disabled", false);
                },
                error: function (jqXHR, status) {

                    formData = {};

                    let response = parseJson(jqXHR.responseText);

                    // Funzione che genera il toast con gli errori
                    {{-- (vedere nel footer) --}}
                    createValidatorFormErrorToast(response.errors.error, 5000, 'Errori');
                }
            });

            e.stopImmediatePropagation();
            return false;
        })
    }

    {{-- Ajax Sort --}}
    //// ----------------
    class SortableList {
        constructor(container) {
            this.container = container;
            this.draggedElement = null;
            this.placeholder = null;
            this.isDragging    = false;
            this.offset        = { x: 0, y: 0 };

            // === ➊ salva i listener per poterli rimuovere ===
            this.boundMouseDown  = this.handleMouseDown.bind(this);
            this.boundTouchStart = this.handleTouchStart.bind(this);

        }


        init() {
            if (this._initialized) return;
            this._initialized = true;

            this.container.addEventListener('mousedown',  this.boundMouseDown);
            this.container.addEventListener('touchstart', this.boundTouchStart, { passive: false });
        }


        handleMouseDown(e) {
            const item = e.target.closest('.sortable-item');
            const handle = e.target.closest('.drag-handle');

            if (!item || !handle) return;

            e.preventDefault();
            const rect = item.getBoundingClientRect();
            this.offset.x = e.clientX - rect.left;
            this.offset.y = e.clientY - rect.top;

            this.startDrag(item, e.clientX, e.clientY);

            this.mouseMoveHandler = this.handleMouseMove.bind(this);
            this.mouseUpHandler = this.handleMouseUp.bind(this);

            document.addEventListener('mousemove', this.mouseMoveHandler);
            document.addEventListener('mouseup', this.mouseUpHandler);
        }

        handleTouchStart(e) {
            const item = e.target.closest('.sortable-item');
            const handle = e.target.closest('.drag-handle');

            if (!item || !handle) return;

            e.preventDefault();
            const touch = e.touches[0];
            const rect = item.getBoundingClientRect();
            this.offset.x = touch.clientX - rect.left;
            this.offset.y = touch.clientY - rect.top;

            this.startDrag(item, touch.clientX, touch.clientY);

            this.touchMoveHandler = this.handleTouchMove.bind(this);
            this.touchEndHandler = this.handleTouchEnd.bind(this);

            document.addEventListener('touchmove', this.touchMoveHandler, { passive: false });
            document.addEventListener('touchend', this.touchEndHandler);
        }

        startDrag(item, x, y) {
            this.draggedElement = item;
            this.isDragging = true;

            this.removePlaceholder();

            this.placeholder = document.createElement('li');
            this.placeholder.className = 'sortable-placeholder';
            this.placeholder.style.height = item.offsetHeight + 'px';

            item.parentNode.insertBefore(this.placeholder, item);

            item.classList.add('dragging');
            item.style.position = 'fixed';
            item.style.zIndex = '1000';
            item.style.pointerEvents = 'none';
            item.style.transform = 'rotate(3deg)';
            item.style.width = item.offsetWidth + 'px';

            this.updateDragPosition(x, y);

            // console.log('Drag iniziato');
        }

        removePlaceholder() {
            const existingPlaceholders = this.container.querySelectorAll('.sortable-placeholder');
            existingPlaceholders.forEach(placeholder => placeholder.remove());
        }

        handleMouseMove(e) {
            if (!this.isDragging) return;
            e.preventDefault();
            this.updateDragPosition(e.clientX, e.clientY);
            this.updatePlaceholderPosition(e.clientY);
        }

        handleTouchMove(e) {
            if (!this.isDragging) return;
            e.preventDefault();
            const touch = e.touches[0];
            this.updateDragPosition(touch.clientX, touch.clientY);
            this.updatePlaceholderPosition(touch.clientY);
        }

        updateDragPosition(x, y) {
            if (!this.draggedElement) return;

            this.draggedElement.style.left = (x - this.offset.x) + 'px';
            this.draggedElement.style.top = (y - this.offset.y) + 'px';
        }

        updatePlaceholderPosition(y) {
            const items = Array.from(this.container.children).filter(item =>
                item !== this.draggedElement &&
                item !== this.placeholder &&
                item.classList.contains('sortable-item')
            );

            let insertBefore = null;

            for (let item of items) {
                const rect = item.getBoundingClientRect();
                const itemMiddle = rect.top + rect.height / 2;

                if (y < itemMiddle) {
                    insertBefore = item;
                    break;
                }
            }

            if (insertBefore) {
                this.container.insertBefore(this.placeholder, insertBefore);
            } else {
                this.container.appendChild(this.placeholder);
            }
        }

        handleMouseUp(e) {
            this.endDrag();
            document.removeEventListener('mousemove', this.mouseMoveHandler);
            document.removeEventListener('mouseup', this.mouseUpHandler);
        }

        handleTouchEnd(e) {
            this.endDrag();
            document.removeEventListener('touchmove', this.touchMoveHandler);
            document.removeEventListener('touchend', this.touchEndHandler);
        }

        endDrag() {
            if (!this.isDragging || !this.draggedElement) return;

            this.draggedElement.classList.remove('dragging');
            this.draggedElement.style.position = '';
            this.draggedElement.style.zIndex = '';
            this.draggedElement.style.pointerEvents = '';
            this.draggedElement.style.transform = '';
            this.draggedElement.style.width = '';
            this.draggedElement.style.left = '';
            this.draggedElement.style.top = '';

            if (this.placeholder && this.placeholder.parentNode) {
                this.placeholder.parentNode.insertBefore(this.draggedElement, this.placeholder);
            }

            this.removePlaceholder();

            this.isDragging = false;
            this.draggedElement = null;
            this.placeholder = null;

            if (this.onUpdate) {
                this.onUpdate();
            }
        }

        destroy() {
            this.container.removeEventListener('mousedown',  this.boundMouseDown);
            this.container.removeEventListener('touchstart', this.boundTouchStart);

            if (this.mouseMoveHandler)  document.removeEventListener('mousemove',  this.mouseMoveHandler);
            if (this.mouseUpHandler)    document.removeEventListener('mouseup',    this.mouseUpHandler);
            if (this.touchMoveHandler)  document.removeEventListener('touchmove',  this.touchMoveHandler);
            if (this.touchEndHandler)   document.removeEventListener('touchend',   this.touchEndHandler);

            this.removePlaceholder();
            this.isDragging     = false;
            this.draggedElement = null;
            this.placeholder    = null;
            this._initialized   = false;
        }

    }

    let sortableInstance = null;

    function openSortModal(childSections) {
        const sortableContainer = document.getElementById('sortable-sections');

        if (sortableInstance) {
            sortableInstance.destroy();
            sortableInstance = null;
        }

        sortableContainer.innerHTML = '';

        childSections.forEach((section, index) => {
            const listItem = document.createElement('li');
            listItem.className = 'list-group-item d-flex justify-content-between align-items-center sortable-item';
            listItem.setAttribute('data-section-id', section.id);
            listItem.setAttribute('data-original-sort', index + 1);

            listItem.innerHTML = `
            <span class="section-name">${section.name}</span>
            <i class="fas fa-grip-vertical text-muted drag-handle"></i>
        `;

            sortableContainer.appendChild(listItem);
        });

        sortableInstance = new SortableList(sortableContainer);
        sortableInstance.onUpdate = function() {
            // console.log('Ordine aggiornato');
            updateVisualOrder();
        };
        sortableInstance.init();

        openModal();
    }


    function openModal() {
        if (typeof $ !== 'undefined' && typeof $.fn.modal !== 'undefined') {
            $('#sortSectionsModal').modal('show');
        } else if (typeof bootstrap !== 'undefined') {
            const modal = new bootstrap.Modal(document.getElementById('sortSectionsModal'));
            modal.show();
        } else {
            const modal = document.getElementById('sortSectionsModal');
            modal.classList.add('show');
            modal.style.display = 'block';
            document.body.classList.add('modal-open');

            const backdrop = document.createElement('div');
            backdrop.className = 'modal-backdrop fade show';
            backdrop.id = 'modal-backdrop-custom';
            document.body.appendChild(backdrop);
        }
    }

    function closeModal() {
        if (sortableInstance) {
            sortableInstance.destroy();
            sortableInstance = null;
        }

        if (typeof $ !== 'undefined' && typeof $.fn.modal !== 'undefined') {
            $('#sortSectionsModal').modal('hide');
        } else if (typeof bootstrap !== 'undefined') {
            const modal = bootstrap.Modal.getInstance(document.getElementById('sortSectionsModal'));
            if (modal) modal.hide();
        } else {
            const modal = document.getElementById('sortSectionsModal');
            modal.classList.remove('show');
            modal.style.display = 'none';
            document.body.classList.remove('modal-open');

            const backdrop = document.getElementById('modal-backdrop-custom');
            if (backdrop) backdrop.remove();
        }
    }

    // Resto delle funzioni...
    function updateVisualOrder() {
        const items = document.querySelectorAll('#sortable-sections .sortable-item');
        items.forEach((item, index) => {
            console.log(`Elemento ${item.getAttribute('data-section-id')} ora in posizione ${index + 1}`);
        });
    }

    function saveSortOrder() {
        const items = document.querySelectorAll('#sortable-sections .sortable-item');
        const sortData = [];

        // Ottieni il riferimento al pulsante
        const saveButton = document.getElementById('saveSortOrder');
        const originalText = saveButton.innerHTML;

        items.forEach((item, index) => {
            sortData.push({
                id: parseInt(item.getAttribute('data-section-id')),
                sort: index + 1
            });
        });

        const endPointReorder = "{{ siteUrl('admin/generic-page/paragraph/manual-reordering') }}";
        const xhr = new XMLHttpRequest();
        xhr.open('POST', endPointReorder, true);
        xhr.setRequestHeader('Content-Type', 'application/json');
        xhr.setRequestHeader('X-Requested-With', 'XMLHttpRequest');

        const csrfToken = document.querySelector('meta[name="csrf-token"]');
        if (csrfToken) {
            xhr.setRequestHeader('X-CSRF-TOKEN', csrfToken.getAttribute('content'));
        }

        // Mostra spinner prima di inviare la richiesta
        saveButton.disabled = true;
        saveButton.innerHTML = '<i class="fas fa-spinner fa-spin"></i>';

        xhr.onreadystatechange = function() {
            if (xhr.readyState === 4) {

                saveButton.disabled = false;
                saveButton.innerHTML = originalText;

                if (xhr.status === 200) {
                    try {
                        const response = JSON.parse(xhr.responseText);
                        if (response.success) {
                            closeModal();
                            createValidatorFormSuccessToast('Ordine salvato con successo!', 'ATTENZIONE', 'Azione');

                            setTimeout(function () {
                                window.location.reload();
                            }, 1000);
                        } else {
                            createValidatorFormErrorToast(response.errors.error, 5000, 'Errori');
                        }
                    } catch (e) {
                        createValidatorFormErrorToast('Errore nella risposta del server', 5000, 'Errori');
                    }
                } else {
                    createValidatorFormErrorToast('Errore di comunicazione con il server', 5000, 'Errori');
                }
            }
        };

        xhr.send(JSON.stringify({
            sections: sortData
        }));
    }


    document.addEventListener('DOMContentLoaded', function() {
        const saveButton = document.getElementById('saveSortOrder');
        if (saveButton) {
            saveButton.addEventListener('click', saveSortOrder);
        }

        const closeButtons = document.querySelectorAll('[data-dismiss="modal"]');
        closeButtons.forEach(button => {
            button.addEventListener('click', closeModal);
        });

        document.addEventListener('keydown', function(e) {
            if (e.key === 'Escape') {
                closeModal();
            }
        });

        if (sortableInstance) {
            sortableInstance.destroy();
            sortableInstance = null;
        }

    });

    function showNotification(message, type = 'info') {
        if (type === 'success') {
            alert(message);
        } else if (type === 'error') {
            alert(message);
        } else {
            alert(message);
        }
    }
    ////---------------


    /**
     * Metodo per modificare l'ordinamento delle pagine o dei paragrafi
     */
    function makeSort() {

        var arg = arguments;
        let id = arg[0];
        let dir = arg[1];
        let thisElement = arg[2];
        let institutionId = arg[2].attr('data-institution-id');
        let parentId = arg[2].attr('data-parent-id');
        let sectionIsSystem = arg[2].attr('data-section-is-system');
        let sectionName = arg[2].attr('data-section-name');
        let sectionId = arg[2].attr('data-section-id');
        let url = arg[3];
        let type = arg[4];
        let iconLoading = $('#icon_current_section_operation');

        let args = [{
            is_system: sectionIsSystem,
            selectedNode: sectionName,
            node_id: sectionId,
            parent_id: parentId,
            institution_id: institutionId

        }];

        //Chiamata Ajax che modifica l'ordinamento dei paragrafi o delle sezioni
        $.ajax({
            type: 'GET',
            url: url,
            dataType: "JSON",
            data: {
                id,
                dir,
                institution_id: institutionId
            },
            beforeSend: function () {

                if (type === 'paragraph') {
                    thisElement.empty().append('<i class="fas fa-spinner fa-spin"></i>');
                }else{
                    iconLoading.empty()
                        .append('<i class="fas fa-spinner fa-spin"></i>')
                        .attr("disabled", true);
                }

            },
            success: function (response) {

                response = parseJson(response);

                if (response.data && response.data.hasOwnProperty('manual_order')) {
                    if (response.data.child_sections && response.data.child_sections.length > 0) {
                        openSortModal(response.data.child_sections);
                    }
                } else {
                    // Funzione che genera il toast con il messaggio di successo
                    {{-- (vedere nel footer) --}}
                    createValidatorFormSuccessToast(response.data.msg, 'ATTENZIONE', 'Azione');
                    setTimeout(function () {
                        if (type === 'paragraph') {
                            // Se l'operazione di ordinamento è stata eseguita sui paragrafi, aggiorno il contenuto della pagina
                            loadContents(args);
                        } else {
                            // Se l'operazione di ordinamento è stata eseguita sulle pagine, aggiorno l'alberatura delle pagine
                            $('#tree').jstree(true).settings.core.data.url = "{{siteUrl('admin/generic-page/list')}}";
                            $('#tree').jstree("refresh");
                        }
                    }, 1000)
                }

            },
            complete: function () {
                if (type === 'paragraph') {
                    $('.paragraph_down').empty().append('<i class="fas fa-chevron-down"></i>');
                    $('.paragraph_up').empty().append('<i class="fas fa-chevron-up"></i>');
                }else{
                    iconLoading.empty().append('<i class="fas fa-plug mr-1"></i>').attr("disabled", false);
                }

            },
            error: function (jqXHR, status) {
                let response = parseJson(jqXHR.responseText);

                // Funzione che genera il toast con gli errori
                {{-- (vedere nel footer) --}}
                createValidatorFormErrorToast(response.errors.error, 5000, 'Errori');
            }
        });
    }

    {{-- Ajax Delete --}}
    /**
     * Metodo per modificare l'ordinamento delle pagine
     */
    function makeDelete() {

        var arg = arguments;
        let id = arg[0];
        let institutionId = arg[1].attr('data-institution-id');
        let parentId = arg[1].attr('data-parent-id');
        let sectionIsSystem = arg[1].attr('data-section-is-system');
        let sectionName = arg[1].attr('data-section-name');
        let sectionId = arg[1].attr('data-section-id');
        let url = arg[2];
        let type = arg[3];
        let msg = arg[4];
        let iconLoading = $('#icon_current_section_operation');

        let args = [{
            is_system: sectionIsSystem,
            selectedNode: sectionName,
            node_id: sectionId,
            parent_id: parentId,
            institution_id: institutionId

        }];

        $.confirm({
            title: 'Attenzione:',
            content: msg,
            type: 'dark',
            columnClass: 'medium',
            closeIcon: true,
            buttons: {
                'Conferma eliminzaione': function () {

                    {{-- Ajax delete --}}
                    $.ajax({
                        type: 'GET',
                        url: url,
                        dataType: "JSON",
                        data: {
                            id,
                            institution_id: institutionId
                        },
                        beforeSend: function () {
                        },
                        success: function (response) {

                            // Funzione che genera il toast con il messaggio di successo
                            {{-- (vedere nel footer) --}}
                            createValidatorFormSuccessToast(response.data.msg, 'ATTENZIONE', 'Azione');

                            setTimeout(function () {
                                if (type === 'paragraph') {
                                    // Se è stato cancellato un paragrafo, aggiorno il contenuto della pagina
                                    loadContents(args);
                                } else {
                                    // Se è stata cancellata una pagina personalizzata, aggiorno la pagina
                                    window.location.href = '{{ currentUrl() }}';
                                }
                            }, 1000)
                        },
                        complete: function () {
                        },
                        error: function (jqXHR, status) {
                            let response = parseJson(jqXHR.responseText);

                            // Funzione che genera il toast con gli errori
                            {{-- (vedere nel footer) --}}
                            createValidatorFormErrorToast(response.errors.error, 5000, 'Errori');
                        }
                    });

                },
                'Annulla': function () {
                }
            }
        });
    }

    /**
     * Metodo che carica i contenuti della pagina selezionata
     */
    function loadContents() {

        args = arguments[0][0];

        //Rimuove i tooltipo quando si ricaricano i contenuti
        $('.tooltip-inner').remove();
        $('.tooltip-arrow').remove();
        $('.arrow').remove();
        $('#start_generic_page').empty()
        //Chiamata Ajax che restitutisce i contenuti della sezione
        $.ajax({
            type: 'GET',
            url: '{{ siteUrl('admin/generic-page/get') }}',
            data: {
                parent_id: (args.is_system === 1) ? args.node_id : args.parent_id,
                id: args.node_id,
            },
            beforeSend: function () {

                $('#list_section_tree').empty()
                $('#layer_current_section').hide();
                $('#start_generic_page').empty().append('<div class="col-md-12 text-center"><div class="spinner-border" role="status"></div><div>Attendere...</div></div>');

            },
            success: function (response) {

                response = parseJson(response);
                let contents = response.data.contents.length >= 1 ? response.data.contents : null;
                let section = response.data.section;
                let permit = response.data.permit;
                let isSystem = parseInt(section.is_system) === 0 ? false : true;
                let tag = (section.tag === null || section.tag === undefined || section.tag === 0 || isNaN(parseInt(section.tag))) ? false : true;

                let parentName = section.parent_name;

                {{-- Assign Section ID variable --}}
                    currentSectionId = args.node_id;

                {{-- Creazione pulsanti di editing sezioni --}}
                let buildHtml = btnOperations(args.node_id, isSystem, args.institution_id, args.selectedNode, parentName, permit, tag, section);

                {{-- Mostro il contenuto della pagina --}}
                // if (contents !== null) {
                buildHtml += actionSectionAndContents(section, contents, permit)
                // }

                if (contents === null) {
                    // Mostro il messaggio che indica che la pagina non ha contenuto
                    buildHtml += noDataContents(args.node_id, isSystem,permit, section);
                }

                $('#start_generic_page').empty().append(buildHtml);

                makeSection();

                // Selezione dell'ente da gestire.
                {{-- Vedere nel footer --}}
                selectInstitution();

                // Metodo per il versioning
                {{-- Vedere nel footer --}}
                getVersionsList('v-spinner');

            },
            error: function (jqXHR, status) {
                let response = parseJson(jqXHR.responseText);
            }
        });
    }

    function makeSection() {

        var iconLoading = $('#icon_current_section_operation');

        $('#add_section').bind('click', function (e) {
            e.preventDefault();

            $('#spinner').hide();
            $('#subsection-footer').show();
            $('#_form_content_').show();

            // Pulisco i campi di input del modale
            $(':input', '#form_custom_section')
                .not(':button, :submit, :reset')
                .val('')
                .prop('checked', false)
                .prop('selected', false);
            $('.patos_ajax_table_data_selected').empty();
            $('.patos_ajax_table_data_selected').hide();

            var institutionId = $(this).attr('data-institution-id');

            $('#custom-page').show();
            $('#custom_section_parent_id').val($(this).attr('data-id'));
            $('#custom_section_institution_id').val(institutionId);
            $('#mode_storage_custom_section').val('insert');
            $('#custom_section_id').val('');
            currentSectionStorageMode = 'insert';
            currentSourceSectionId = null;
            $('#custom_section').val('');
            $('#external_url').val('');
            $('#hide').val('');
            $('#meta_keywords').val('');
            $('#meta_descriptions').val('');
            $('#list_section_tree').empty().hide();
            $('#list_section_tree').empty()
            $('#tree-msg').hide();
            $('#subsection').modal('show');

            registerSection();
        });

        {{-- BTN Delete Section --}}
        $('#delete_current_section').bind('click', function (e) {
            e.preventDefault();
            let sectionName = $(this).attr('data-section-name');
            let parentName = $(this).attr('data-parent-name');
            let msg = '<p>Verrà eliminato l\'intero contenuto della sottosezione di "<strong>' + htmlEncode(parentName) + '</strong>" con titolo <strong>"' + htmlEncode(sectionName) + '"</strong>.</p><p>L\'eliminazione avrà effeto sul testo presente, su tutti i paragrafi, su tutte le informazioni richiamate e su tutte le sue pagine figlie con i relativi contenuti.</p><strong>Continuare?</strong>';
            makeDelete($(this).attr('data-id'), $(this), '{{ siteUrl('admin/generic-page/section/delete') }}', 'section', msg)
        });

        {{-- BTN Delete Paragraph --}}
        $('.paragraph_delete').bind('click', function (e) {
            e.preventDefault();
            let paragraphName = $(this).attr('data-paragraph-name');
            let msg = '<p>Verrà eliminato il paragrafo "<strong>' + htmlEncode(paragraphName) + '</strong>" che include il testo presente e tutte le informazioni richiamate.</p><strong>Continuare?</strong>';
            makeDelete($(this).attr('data-id'), $(this), '{{ siteUrl('admin/generic-page/paragraph/delete') }}', 'paragraph', msg)
        });

        {{--  BTN Sort Up Section --}}
        $('#movie_up_current_section').bind('click', function (e) {
            $('#movie_up_current_section').unbind();
            e.preventDefault();
            e.stopImmediatePropagation();
            makeSort($(this).attr('data-id'), 'up', $(this), '{{siteUrl('admin/generic-page/section/sorting')}}', 'section')
        });

        {{-- BTN Sort Down Section --}}
        $('#movie_down_current_section').bind('click', function (e) {
            $('#movie_down_current_section').unbind();
            e.preventDefault();
            e.stopImmediatePropagation();
            makeSort($(this).attr('data-id'), 'down', $(this), '{{siteUrl('admin/generic-page/section/sorting')}}', 'section')
        });

        {{-- BTN Sort Up Paragraph --}}
        $('.paragraph_up').bind('click', function (e) {
            $('.paragraph_up').unbind();
            e.preventDefault();
            e.stopImmediatePropagation();
            makeSort($(this).attr('data-id'), 'up', $(this), '{{siteUrl('admin/generic-page/section/sort-paragraph')}}', 'paragraph')
        });

        {{-- BTN Sort Down Paragraph --}}
        $('.paragraph_down').bind('click', function (e) {
            $('.paragraph_down').unbind();
            e.preventDefault();
            e.stopImmediatePropagation();
            makeSort($(this).attr('data-id'), 'down', $(this), '{{siteUrl('admin/generic-page/section/sort-paragraph')}}', 'paragraph')
        });

        {{-- BTN edit current section --}}
        /**
         * Metodo per la modifica di una sezione
         */
        $('#edit_current_section').bind('click', function (e) {

            e.preventDefault();
            $('#_form_content_').toggle();
            var id = $(this).attr('data-id');
            var institutionId = $(this).attr('data-institution-id');
            var btnSend = $('.icon_duplicate_current_section');

            //Chiamata Ajax per la modifica di una sezione
            $.ajax({
                // async: false,
                type: 'GET',
                url: '{{ siteUrl('admin/generic-page/section/edit') }}',
                dataType: "JSON",
                data: {
                    id,
                    institution_id: institutionId
                },
                beforeSend: function () {
                    iconLoading.empty().append('<i class="fas fa-spinner fa-spin"></i>')
                        .attr("disabled", true);
                    $('#spinner').show();
                    $('#subsection').modal('show');
                    $('#subsection-footer').hide();
                    $('#_form_content_').hide();
                },
                success: function (response) {

                    // Pulisco i campi di input del modale
                    $(':input', '#form_custom_section')
                        .not(':button, :submit, :reset')
                        .val('')
                        .prop('checked', false)
                        .prop('selected', false);
                    $('.patos_ajax_table_data_selected').empty();
                    $('.patos_ajax_table_data_selected').hide();
                    // Fine pulizia campi input

                    response = parseJson(response);

                    let isSystem = response.data.section.is_system;

                    if (isSystem) {

                        $('#custom-page').hide();

                    } else {
                        $('#custom-page').show();
                        $('#custom_section').val(response.data.section.name);
                        $('#external_url').val(response.data.section.external_url);
                        $('#hide').val(response.data.section.hide).trigger('change');
                        $('#meta_keywords').val(response.data.section.meta_keywords);
                        $('#custom_section_institution_id').val(institutionId)
                    }

                    let seoData = response.data.seo;
                    seoData = Object.entries(seoData);

                    $(seoData).each(function (index, value) {
                        $('#input_' + value[0]).val(value[1]);
                    });

                    let publishingResponsible = response.data.section.p_r;

                    createResponsibleSelect(true, publishingResponsible);

                    $('#mode_storage_custom_section').val('update');
                    currentSectionStorageMode = 'update';
                    $('#custom_section_parent_id').val('');

                    $('#custom_section_id').val(response.data.section.id);
                    currentSourceSectionId = response.data.section.id;

                    $('#list_section_tree').empty();

                    registerSection();

                },
                complete: function () {
                    iconLoading.empty().append('<i class="fas fa-plug mr-1"></i>').attr("disabled", false);
                    setTimeout(function () {
                        $('#spinner').hide();
                        $('#_form_content_').toggle();
                        $('#subsection-footer').toggle();
                    }, 800)
                },
                error: function (jqXHR, status) {
                    let response = parseJson(jqXHR.responseText);

                    // Funzione che genera il toast con gli errori
                    {{-- (vedere nel footer) --}}
                    createValidatorFormErrorToast(response.errors.error, 5000, 'Errori');
                }
            });
        });

        {{-- BTN duplicate --}}
        /**
         * Metodo per la duplicazione di una sezione
         */
        $('#duplicate_current_section').bind('click', function (e) {

            e.preventDefault();

            var id = $(this).attr('data-id');
            var institutionId = $(this).attr('data-institution-id');
            var btnSend = $('.icon_duplicate_current_section');

            let msg = '<p>Verrà copiato l\'intero contenuto della sottosezione "<strong>' +
                $(this).attr('data-selected-section') +
                '</strong>" nella pagina di destinazione che potrà selezionare nel prossimo step.</p><strong>Continuare?</strong>';

            $.confirm({
                title: 'Attenzione:',
                icon: 'fas fa-exclamation-triangle',
                content: msg,
                type: 'dark',
                columnClass: 'medium',
                closeIcon: true,
                buttons: {
                    'Continua': function () {
                        $.ajax({
                            // async: false,
                            type: 'GET',
                            url: '{{ siteUrl('admin/generic-page/section/duplicate') }}',
                            dataType: "JSON",
                            data: {
                                id,
                                institution_id: institutionId
                            },
                            beforeSend: function () {
                                iconLoading.empty().append('<i class="fas fa-spinner fa-spin"></i>')
                                    .attr("disabled", true);
                                $('#spinner').show();
                                $('#subsection').modal('show');
                                $('#subsection-footer').hide();
                                $('#_form_content_').hide();
                            },
                            success: function (response) {

                                // Pulisco i campi di input del modale
                                $(':input', '#form_custom_section')
                                    .not(':button, :submit, :reset')
                                    .val('')
                                    .prop('checked', false)
                                    .prop('selected', false);
                                $('.patos_ajax_table_data_selected').empty();
                                $('.patos_ajax_table_data_selected').hide();
                                // Fine pulizia campi input

                                response = parseJson(response);

                                // Setto i valori della sezione da duplicare nel form
                                $('#custom_section').val('Copia di ' + response.data.section.name);
                                $('#custom_section_institution_id').val(response.data.section.institution_id);
                                $('#external_url').val(response.data.section.external_url);
                                $('#meta_keywords').val(response.data.section.meta_keywords);
                                $('#mode_storage_custom_section').val('duplicate');
                                $('#custom_section_parent_id').val(response.data.section.parent_id);
                                $('#custom_section_id').val(response.data.section.id);
                                currentSectionStorageMode = 'duplicate';
                                currentSourceSectionId = response.data.section.id;
                                $('#list_section_tree').empty().html(response.data.select_tree).show();
                                $('#tree-msg').show();

                                $('#select_tree').select2({
                                    dropdownParent: $('#subsection')
                                });

                                let seoData = response.data.seo;
                                seoData = Object.entries(seoData);

                                $(seoData).each(function (index, value) {
                                    $('#input_' + value[0]).val(value[1]);
                                });

                                let publishingResponsible = response.data.section.p_r;

                                createResponsibleSelect(true, publishingResponsible);

                                registerSection();

                            },
                            complete: function () {
                                iconLoading.empty().append('<i class="fas fa-plug mr-1"></i>').attr("disabled", false);
                                setTimeout(function () {
                                    $('#spinner').hide();
                                    $('#_form_content_').toggle();
                                    $('#subsection-footer').toggle();
                                }, 600)
                            },
                            error: function (jqXHR, status) {
                                let response = parseJson(jqXHR.responseText);

                                // Funzione che genera il toast con gli errori
                                {{-- (vedere nel footer) --}}
                                createValidatorFormErrorToast(response.errors.error, 6000, 'Errori');
                            }
                        });
                    },
                    'Annulla': function () {
                    }
                }
            });

        });

        /**
         * Funzione per la duplicazione di un paragrafo
         */
        $('.paragraph_duplicate').bind('click', function (e) {
            e.preventDefault();

            let msg = '<p>Verrà copiato l\'intero contenuto del paragrafo "<strong>' + $(this).attr('data-section-name') + '</strong>" che include il testo presente e tutte le informazioni richiamate.</p><p>Il paragrafo selezionato verrà inserito nella pagina di destinazione che potrà scegliere nel prossimo step.</p><p>Tale operazione non comporterà l\'eliminazione del paragrafo selezionato.</p><strong>Continuare?</strong>';

            let paragraphId = $(this).attr('data-id');
            let sectionId = $(this).attr('data-section-id');

            $.confirm({
                title: 'Attenzione:',
                icon: 'fas fa-exclamation-triangle',
                content: msg,
                type: 'dark',
                columnClass: 'medium',
                closeIcon: true,

                buttons: {
                    'Continua': function () {
                        window.location.href = '{{ baseUrl(verifyFirstOrLastSlash('admin/generic-page/paragraph/duplicate/'), null, '/') }}' + sectionId + '.html?par=' + paragraphId;
                    },
                    'Annulla': function () {
                    }
                }
            });

        });


        /**
         * Funzione per la duplicazione di un paragrafo
         */
        $('.p_activity_log').bind('click', function (e) {
            e.preventDefault();
            let idPar = $(this).attr('data-id');
            let isPage = $(this).attr('data-page') ?? 0;
            let url = 'generic-page/show-record-log/'+idPar;

            if (idPar !== undefined) {

                $.ajax({
                    type: 'GET',
                    url: url,
                    dataType: "JSON",
                    data: {
                        id: idPar,
                        is_page: isPage,
                        archive: 44
                    },
                    beforeSend: function () {
                        $('#log-spinner').show();
                        $('#modal-log-body').hide();
                        $('#log_modal').modal('show');
                    },
                    success: function (response) {
                        let resp = parseJson(response);
                        let data = resp['data'];
                        setLog(data);
                    },
                    complete: function () {
                        $('#log-spinner').hide('fast');
                        $('#modal-log-body').show();
                    },
                    error: function (jqXHR, status) {
                        let response = parseJson(jqXHR.responseText);
                        $(document).Toasts('create', {
                            class: 'bg-danger',
                            title: 'ATTENZIONE',
                            subtitle: 'Errori',
                            autohide: true,
                            delay: 5000,
                            body: response.error
                        });
                    }
                });

            } else {
                alert('Nessun elemento selezionato');
            }

        });
    }

    /**
     * Metodo per la generazione dell'alberatura delle pagine
     */
    function generateTree() {
        {{-- Creazione alberatura delle sezioni di front office --}}
        $('#tree').jstree({
            "core": {
                "themes": {
                    "theme": 'classic',
                    "responsive": true,
                    "icons": false
                },
                "plugins": [
                    "themes"
                ],
                "data": {
                    type: "GET",
                    url: "{{siteUrl('admin/generic-page/list')}}",
                    dataType: 'JSON',
                    success: function (data) {
                        $(data).each(function (index, value) {
                            return {'id': this.text};
                        });
                    }
                },
            }
        }).on('ready.jstree', function (e, data) {
            if ($('#section_id').val()) {
                data.instance.open_node($('#section_id').val());
                data.instance.select_node($('#section_id').val());
            }
        });
    }

    $(document).ready(function () {

        {{-- Genero l'alberatura delle pagine --}}
        generateTree();

        {{-- Mostro le informazioni della pagina selezionata --}}
        /**
         * Metodo che mostra le informazioni di una pagina quando viene selezionata
         */
        $("#tree").on("select_node.jstree", function (e, data) {
            e.preventDefault();
            arg = [{
                is_system: data.node.original.is_system,
                selectedNode: (data.node.original.is_system === 0) ? data.node.text.split('</span>')[1].trim() : htmlEncode(data.node.text),
                node_id: data.node.id,
                parent_id: data.node.parent,
                institution_id: data.node.original.institution_id

            }];
            loadContents(arg);
        });
    });
</script>
{% endblock %}
