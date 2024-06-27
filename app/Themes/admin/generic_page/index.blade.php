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
{{ js('admin/get/config.js') }}
<script type="text/javascript">

    {{-- Variabile section ID --}}
    var currentSectionId = null;

    {{-- Templates --}}
    /**
     * Metodo che setta dinamicamente i pulsanti con le operazioni che si possono eseguire sulle pagine
     * @returns {string}
     */
    function btnOperations() {

        var args = arguments;
        var id = args[0];
        var isSystem = args[1];
        let institutionId = args[2];
        let selectedNode = args[3];
        let parentName = args[4];
        let permit = args[5];

        var html = '';
        html += '<div class="col-md-12 text-right margin">';

        if(permit)
        {
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
                html += '<a href="{{ baseUrl('admin/generic-page/paragraph/add/') }}' + id + '.html?" data-id="' + id + '" data-sys="' + isSystem + '" title="Aggiungi Paragrafo" ';
            html += 'id="add_paragraph" class="dropdown-item" type="button"> ';
            @else
            if (!isSystem) {
                html += '<a href="{{ baseUrl('admin/generic-page/paragraph/add/') }}' + id + '.html?" data-id="' + id + '" data-sys="' + isSystem + '" title="Aggiungi Paragrafo" ';
                html += 'id="add_paragraph" class="dropdown-item" type="button"> ';
            } else {
                html += '<a class="select-institution dropdown-item" type="button" href="#!" title="Aggiungi Paragrafo">';
            }
            @endif
                html += '<i class="fas fa-plus-circle mr-1"></i> Aggiungi Paragrafo nella sezione';
            html += '</a>';
            html += '</li>';

            {{-- BTN action --}}
            if (isSystem === false) {

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
        if (permit) {
            html += '<div>';
            @if(checkAlternativeInstitutionId() != 0)
                html += '<a class="btn btn-primary" href="{{ baseUrl('admin/generic-page/paragraph/add/') }}' + id + '.html">';
            @else
            if (!isSystem) {
                html += '<a class="btn btn-primary" href="{{ baseUrl('admin/generic-page/paragraph/add/') }}' + id + '.html">';
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

        var args = arguments;
        let id = args[0].id;
        let name = args[0].label ?? args[0].name;
        let guide = args[0].guide;
        let normatives = args[0].normatives;
        let label = args[0].label;
        let contents = args[1];
        let sectionIsSystem = args[0].is_system;
        let sectionSelectedNode = args[0].name;
        let permit = args[2];
        let html = '';
        html += '<div class="col-md-12 mt-3">';
        html += '<div class="card card-success">';
        html += '<div class="card-body">';
        html += '<div class="row">';
        html += '<div class="col-md-12 col-lg-12 col-xl-12" style="line-height: 1.9rem;">';
        html += '<h2 class="mb-5">' + htmlEncode(name) + '</h2>';
        html += '</div>';
        html += '</div>';

        if(!permit)
        {
            html += '<div class="row">';
            html += '<div class="alert alert-warning alert-dismissible col-md-12 mb-3" style="background-color: #f6edba; font-size: 14px;">';
            html += '<button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>';
            html += '<h5 style="color: #9c6c38;" clas="text-muted"><i class="fas fa-info-circle"></i> Attenzione</h5>';
            html += '<div class="text-muted">';
            html += '<span>Non hai i permessi necessari per modificare il contenuto della pagina <strong>' + htmlEncode(name) + '</strong>.</span>';
            html += '</div>';
            html += '</div>';
            html += '</div>';
        }

        // Se è presente il contenuto della pagina lo mostro
        if (contents !== null) {
            {{-- Appendo i paragrafi della pagina e i suoi richiami --}}
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
                    {{-- Inserisco il titolo del paragrafo --}}
                        html += '<div class="col-md-9 col-lg-9 col-xl-9">';
                    html += '<h3 style="display: inline;">' + ((item.name) ? htmlEncode(item.name) : '') + '</h3> &nbsp;' <?php if (isSuperAdmin(true)): ?> + '[' + htmlEncode(item.institution.full_name_institution) + ']'<?php else: ?> + '' <?php endif; ?>;
                    html += '</div>';

                    {{-- Inserisco le action sui paragrafi --}}
                    if (permit) {
                        html += '<div class="col-md-3 col-lg-3 col-xl-3 text-center">';
                        html += '<div class="btn-toolbar justify-content-center" role="toolbar" aria-label="Toolbar Azioni">';
                        html += '<div class="btn-group" role="group" aria-label="Azioni">';
                        html += '<a href="#!" title="Sposta su di una posizione" data-section-id="' + id + '" data-section-is-system="' + sectionIsSystem + '" data-section-name="' + htmlEncode(sectionSelectedNode) + '" data-id="' + item.content_id + '" data-institution-id="' + item.institution_id + '" data-parent-id="' + item.section_fo_parent_id + '"class="btn btn-sm btn-primary paragraph_up " data-toggle="tooltip" data-placement="top" data-original-title="Sposta su di una posizione"><i class="fas fa-chevron-up"></i></a>';
                        html += '<a href="#!" title="Sposta gi&ugrave; di una posizione" data-section-id="' + id + '" data-section-is-system="' + sectionIsSystem + '" data-section-name="' + htmlEncode(sectionSelectedNode) + '" data-id="' + item.content_id + '" data-institution-id="' + item.institution_id + '" data-parent-id="' + item.section_fo_parent_id + '" class="btn btn-sm btn-primary paragraph_down" data-toggle="tooltip" data-placement="top" data-original-title="Sposta gi&ugrave; di una posizione"><i class="fas fa-chevron-down"></i></a>';
                        html += '<a href="{{ baseUrl('admin/generic-page/paragraph/edit/') }}' + id + '.html?id=' + item.content_id + '" title="Modifica voce" data-id="' + item.content_id + '" id="edit_' + item.content_id + '" class="btn btn-sm btn-primary paragraph_edit" data-toggle="tooltip" data-placement="top" data-original-title="Modifica voce"><i class="fas fa-edit"></i></a>';
                        html += '<a href="#!" title="Duplica voce" data-id="' + item.content_id + '" data-section-id = "' + id + '" id="duplicate_' + item.content_id + '" data-section-name="' + htmlEncode(item.name) + '" class="btn btn-sm btn-primary paragraph_duplicate" data-toggle="tooltip" data-placement="top" data-original-title="Duplica voce"><i class="fas fa-clone"></i></a>';

                        html += '<a href="#!" title="Elimina voce" data-section-id="' + id + '" data-section-is-system="' + sectionIsSystem + '" data-paragraph-name="' + htmlEncode(item.name) + '" data-section-name="' + htmlEncode(sectionSelectedNode) + '" data-id="' + item.content_id + '" data-institution-id="' + item.institution_id + '" data-parent-id="' + item.section_fo_parent_id + '" class="btn btn-sm btn-danger paragraph_delete" data-toggle="tooltip" data-placement="top" data-original-title="Elimina voce"><i class="fas fa-trash"></i></a>';
                        html += '</div>';
                        html += '</div>';
                        html += '</div>';
                    }
                    html += '<div class="col-md-12 mt-3">';
                    html += truncate(stripTags(item.content), 380, '...');
                    html += '</div>';

                    html += '<div class="col-md-12 col-lg-12 col-xl-12">';
                    html += '<hr />';
                    html += '</div>';
                });
            }
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
                'mode': $('#mode_storage_custom_section').val(),
                'section_id': $('#custom_section_id').val(), /* ID update */
                'name': $('#custom_section').val(),
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
                } else {
                    iconLoading.empty()
                        .append('<i class="fas fa-spinner fa-spin"></i>')
                        .attr("disabled", true);
                }

            },
            success: function (response) {

                response = parseJson(response);

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
            },
            complete: function () {
                if (type === 'paragraph') {
                    $('.paragraph_down').empty().append('<i class="fas fa-chevron-down"></i>');
                    $('.paragraph_up').empty().append('<i class="fas fa-chevron-up"></i>');
                } else {
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
                var contents = response.data.contents.length >= 1 ? response.data.contents : null;
                var section = response.data.section;
                var permit = response.data.permit;
                var isSystem = parseInt(section.is_system) === 0 ? false : true;
                var parentName = section.parent_name;

                {{-- Assign Section ID variable --}}
                    currentSectionId = args.node_id;

                {{-- Creazione pulsanti di editing sezioni --}}
                var buildHtml = btnOperations(args.node_id, isSystem, args.institution_id, args.selectedNode, parentName, permit);

                {{-- Mostro il contenuto della pagina --}}
                // if (contents !== null) {
                buildHtml += actionSectionAndContents(section, contents, permit)
                // }

                if (contents === null) {
                    // Mostro il messaggio che indica che la pagina non ha contenuto
                    buildHtml += noDataContents(args.node_id, isSystem, permit);
                }

                $('#start_generic_page').empty().append(buildHtml);

                makeSection();

                // Metodo per selezionare l'ente che si vuole gestire se si è super admin
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
            $('#custom_section').val('');
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
                        $('#meta_keywords').val(response.data.section.meta_keywords);
                        $('#custom_section_institution_id').val(institutionId)
                    }

                    $('#mode_storage_custom_section').val('update');
                    $('#custom_section_parent_id').val('');

                    $('#custom_section_id').val(response.data.section.id);

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

        /**
         * Funzione per la duplicazione di un paragrafo
         */
        $('.paragraph_duplicate').bind('click', function (e) {
            e.preventDefault();

            let msg = '<p>Verrà copiato l\'intero contenuto del paragrafo "<strong>' + $(this).attr('data-section-name') + '</strong>" che include il testo presente e tutte le informazioni richiamate.</p><strong>Continuare?</strong>';

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
                        window.location.href = '{{ baseUrl('admin/generic-page/paragraph/duplicate/') }}' + sectionId + '.html?par=' + paragraphId;
                    },
                    'Annulla': function () {
                    }
                }
            });

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

        let selectedNode = '';

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
    })
</script>
{% endblock %}