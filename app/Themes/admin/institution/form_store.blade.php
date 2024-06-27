{{-- Form store Ente --}}
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
    <div class="col-md-2">
        <div class="card card-secondary card-outline">
            <div class="card-body box-profile">
                <div>
                    @if(!empty($institution['simple_logo_file']))
                        <div style="text-align: center;" class="mb-3">
                            <div class="widget-user-image">
                                <img class="img-circle elevation-2" style="width: 70px; height: auto;"
                                     src="{{ baseUrl('media/'. instituteDir($institution['short_institution_name']) . '/assets/images/' . $institution['simple_logo_file']) }}"
                                     alt="Logo ente">
                            </div>
                        </div>
                    @endif
                </div>
                @if(!empty($institution['full_name_institution']))
                    <h5 style="text-align: center;">
                        {{$institution['full_name_institution']}}
                    </h5>
                    <hr>
                @endif
                <h5 class="profile-username">
                    Configurazione avanzata
                </h5>
                <p class="text-muted">
                    <small>
                        Opzioni di configurazione avanzata del Portale.
                    </small>
                </p>
                <ul class="list-group list-group-unbordered mb-3" style="font-size: .85rem">
                    <span><b>Gestione informazioni Ente</b></span>
                    <li class="list-group-item">
                        <a href="#!" class="item_layer" data-id="1" title="Dati generali">
                            <div class="_flex">
                                <div class="mr-2"><i class="fas fa-screwdriver _black"></i></div>
                                <div>Dati generali</div>
                            </div>
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a href="#!" class="item_layer" data-id="3" title="Recapiti dell'ente">
                            <div class="_flex">
                                <div class="mr-2"><i class="fas fa-phone _black"></i></div>
                                <div>Recapiti dell'ente</div>
                            </div>
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a href="#!" class="item_layer" data-id="6" title="Configurazione server SMTP">
                            <div class="_flex">
                                <div class="mr-2"><i class="fas fa-envelope-open"></i></div>
                                <div>Configurazione server SMTP</div>
                            </div>
                        </a>
                    </li>
                </ul>
                @php
                    $show = true;
                @endphp

                @if($show)
                    <ul class="list-group list-group-unbordered mb-3" style="font-size: .85rem">
                        <span class="text-pat-primary"><b>Gestione PAT</b></span>
                        <li class="list-group-item">
                            <a href="#!" class="item_layer" data-id="2" title="Configurazione e moduli gestibili">
                                <div class="_flex">
                                    <div class="mr-2"><i class="fas fa-cog _black"></i></div>
                                    <div>Configurazione e moduli gestibili</div>
                                </div>
                            </a>
                        </li>
                        <li class="list-group-item">
                            <a href="#!" class="item_layer" data-id="5" title="Altre informazioni">
                                <div class="_flex">
                                    <div class="mr-2"><i class="fas fa-info _black"></i></div>
                                    <div>Altre informazioni</div>
                                </div>
                            </a>
                        </li>
                    </ul>
                @endif

            </div>
            <!-- /.card-body -->
        </div>
    </div>
    <div class="col-xl-10">
        {{ form_open($formAction,$formSettings) }}
        <div class="card mb-4" id="card-filter">
            <div class="card-header">
                <h3 class="card-title">
                    <span id="title_layer"><i class="fas fa-screwdriver _black"></i> {{ nbs(1) }} Dati generali</span>
                </h3>
            </div>

            <div class="card-body card-primary">

                <div class="row">
                    <div class="col-md-12 mb-3">

                        <div class="row">
                            <div class="col-md-12 mb-3">
                                <div class="text-muted">
                                    <i class="fas fa-exclamation-circle"></i> {{ nbs(1) }} I Campi contrassegnati dal
                                    simbolo asterisco (*) sono obbligatori.
                                </div>
                            </div>
                        </div>

                        {{-- BEGIN: Dati Generali --}}
                        <div class="type_layer" id="layer_1">
                            {{-- Includo il blocco del form dei dati generali. --}}
                            {% include institution/include_general_data %}
                        </div>
                        {{-- END: Dati Generali --}}

                        {{-- BEGIN: Configurazione e moduli gestibili --}}
                        <div class="type_layer" id="layer_2">
                            {{-- Inclusione del blocco Configurazione e moduli gestibili --}}
                            {% include institution/include_configuration_manageable_modules %}
                        </div>
                        {{-- END: Configurazione e moduli gestibili --}}

                        {{-- BEGIN: Recapiti dell'ente --}}
                        <div class="type_layer" id="layer_3">
                            {{-- Inclusione del blocco Recapiti dell'Ente --}}
                            {% include institution/include_institution_contacts_detail %}
                        </div>
                        {{-- END: Recapiti dell'ente --}}

                        {{-- BEGIN: Altre informazioni --}}
                        <div class="type_layer" id="layer_5">
                            {{-- Inclusione del blocco Altre Informazioni --}}
                            {% include institution/include_other_info %}
                        </div>
                        {{-- END: Altre informazioni --}}

                        {{-- BEGIN: Configurazione server SMTP --}}
                        <div class="type_layer" id="layer_6">
                            {{-- Inclusione del blocco Configurazione server SMTP --}}
                            {% include institution/include_smtp_configuration %}
                        </div>
                        {{-- END: Configurazione server SMTP --}}
                    </div>
                </div>
            </div>
            <div class="card-footer">
                {{ btnSave() }}
            </div>
        </div>

        {{ form_input([
            'type' => 'hidden',
            'name' => '_storage_type',
            'value' => $_storageType,
            'id' => '_storage_type',
            'class' => '_storage_type',
        ]) }}

        @if(!empty($institution['id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'id',
                'value' => $institution['id'],
                'id' => 'institution_id',
                'class' => 'institution_id',
            ]) }}
        @endif

        @if(!empty($institution['institution_type_id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => '_institution_type_id',
                'value' => $institution['institution_type_id'],
                'id' => '_institution_type_id',
                'class' => '_institution_type_id',
            ]) }}
        @endif

        {{ form_close() }}
    </div>

    {{-- Begin Modale PerlaPa --}}
    <div class="modal fade" id="exampleModal" tabindex="-1" role="dialog" aria-labelledby="exampleModalLabel"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header">
                    <h5 class="modal-title" id="exampleModalLabel">Informazione</h5>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <p>
                        I codici
                    </p>
                    <ul>
                        <li>
                            Cod. univoco IPA amm. dichiarante
                        </li>
                        <li>
                            Cod. univoco IPA amm. conferente
                        </li>
                    </ul>
                    <p>
                        sono scaricabili dal sito <a href="https://www.indicepa.gov.it/ipa-portale/consultazione"
                                                     style="color: #dc3545;">www.indicepa.gov.it</a> alla pagina
                        relativa agli opendata.
                    </p>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-primary" data-dismiss="modal">Ok</button>
                </div>
            </div>
        </div>
    </div>
    {{-- Begin Modale PerlaPa --}}


    <div class="modal fade" id="modal-editor-css" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Editor CSS</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <textarea id="edit_code_css" cols="10" rows="30" class="edit_code_css" name="edit_code_css"
                              style="width: 100%"></textarea>
                </div>
                <div class="modal-footer justify-content-between">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Chiudi</button>
                    <button type="button" id="btn-save-css" class="btn btn-primary">Registra CSS</button>
                </div>
            </div>
        </div>
    </div>

    {{-- Modale CSS custom CKEditor--}}
    <div class="modal fade" id="modal-CKEditor-css" aria-hidden="true">
        <div class="modal-dialog modal-xl">
            <div class="modal-content">
                <div class="modal-header">
                    <h4 class="modal-title">Fogli di stile personalizzati in CKEditor</h4>
                    <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">×</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="row">
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Titolo Style</label>
                                {{ form_input([
                                   'name' => 'ckeditor_custom_style_title',
                                   'value' => null,
                                   'placeholder' => 'Titolo della classe E.S.: Titolo principale',
                                   'id' => 'ckeditor_custom_style_title',
                                   'class' => 'form-control form-control-sm ckeditor_custom_style_title',
                               ]) }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <div class="form-group">
                                <label>Nome classe</label>
                                {{ form_input([
                                   'name' => 'ckeditor_custom_style_classname',
                                   'value' => null,
                                   'placeholder' => 'Nome della classe E.S.: class_name',
                                   'id' => 'ckeditor_custom_style_classname',
                                   'class' => 'form-control form-control-sm ckeditor_custom_style_classname',
                               ]) }}
                            </div>
                        </div>
                        <div class="col-md-4">
                            <label>Selettore TAG</label>
                            {{ form_dropdown(
                               'ckeditor_custom_style_tag_name',
                               [
                                    'h1' => 'h1',
                                    'h2' => 'h2',
                                    'h3' => 'h3',
                                    'h4' => 'h4',
                                    'h5' => 'h5',
                                    'h6' => 'h6',
                                    'div' => 'div',
                                    'address' => 'address',
                                    'a' => 'a',
                                    'p' => 'p',
                                    'pre' => 'pre',
                                    'span' => 'span',
                               ],
                               !empty($selectorTag) ? $selectorTag : null,
                               'id="ckeditor_custom_style_tag_name" class="form-control form-control-sm ckeditor_custom_style_tag_name"'
                           ) }}
                        </div>
                    </div>

                    <textarea id="CKEditor_code_css" cols="10" rows="30" class="CKEditor_code_css"
                              name="CKEditor_code_css"
                              style="width: 100%"></textarea>
                </div>
                <div class="modal-footer justify-content-between">
                    <input type="hidden" id="ckeditor_css_id" class="ckeditor_css_id" value="">
                    <button type="button" class="btn btn-default" data-dismiss="modal">Chiudi</button>
                    <button type="button" id="btn-save-css-ckeditor" class="btn btn-primary">Registra CSS</button>
                </div>
            </div>
        </div>
    </div>
</div>
{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
{{ css('select2/css/select2.min.css','common') }}
<style type="text/css">
    .select2-container--default .select2-selection--single {
        height: 38px !important;
    }

    .select2-container--default .select2-selection--single .select2-selection__arrow {
        top: 6px !important;
    }

    .ck-editor__editable_inline {
        min-height: 200px;
    }

    .info-alert {
        background-color: #d9edf7;
        border-color: #88c4e2;
        color: #3a87ad;
    }

    .list-group-item a {
        color: #343a40;
    }

    .list-group-item a:hover {
        color: #c2c7d0;
    }

    .area_toolbar {
        width: 100%;
        margin: 0;
        padding: 0;
        background-color: #cccccc !important;
        text-align: center;
    }

</style>
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
{{ js('select2/js/select2.full.min.js','common') }}
{{ js('ckeditor4/ckeditor.js', 'common') }}
{{ js('edit_area/edit_area_full.js', 'common') }}

<script type="text/javascript">
    //Previene il salvataggio quando si preme invio e il focus non è sul pulsante di salvataggio
    $('#{{ $formSettings['id'] }}').on('keyup keypress', function (e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13 && e.target.tagName != 'TEXTAREA') {
            e.preventDefault();
            return false;
        }
    });

    $(document).ready(function () {

        let formModified = false;

        $('.type_layer').hide();
        $('#layer_1').show();
        $('#preview-url-favicon').hide();
        $('#preview-url-logo').hide();
        $('#preview-file-name').hide();

        $('.item_layer').bind('click', function (e) {
            e.preventDefault();
            let dataId = $(this).attr('data-id');
            $('.type_layer').hide();
            $('#layer_' + dataId).show();
            let iconSet = $(this).children().children().children();
            let icon = iconSet.hasClass('fas') || iconSet.hasClass('far')
                ? '<i class="' + iconSet.attr('class') + '"></i>'
                : '<img src="' + iconSet.attr('src') + '" width="' + iconSet.attr('width') + '" height="' + iconSet.attr('height') + '" alt="' + $(this).attr('title') + '">';

            $('#title_layer').empty().append(icon + ' {{ nbs(1) }} ' + $(this).attr('title'));
        });

        $('#simple_logo_file').bind('change', function (e) {
            var reader,
                files = document.getElementById("simple_logo_file").files;
            reader = new FileReader();
            reader.onload = function (e) {
                $('#src-logo-ente').attr('src', e.target.result);
            };
            reader.readAsDataURL(files[0]);
            $('#preview-url-logo').fadeIn(200);
        });

        $('#clear-preview-logo').bind('click', function (e) {
            document.getElementById("simple_logo_file").value = null;
            $('#preview-url-logo').hide();
        });

        $('#favicon_file').bind('change', function (e) {
            var reader,
                files = document.getElementById("favicon_file").files;
            reader = new FileReader();
            reader.onload = function (e) {
                $('#src-favicon-ente').attr('src', e.target.result);
            };
            reader.readAsDataURL(files[0]);
            $('#preview-url-favicon').fadeIn(200);
        });

        $('#clear-preview-favicon').bind('click', function (e) {
            document.getElementById("favicon_file").value = null;
            $('#preview-url-favicon').hide();
        });

        $('#custom_css').bind('change', function (e) {
            let fileName = e.target.files[0].name;
            var reader,
                files = document.getElementById("custom_css").files;
            reader = new FileReader();
            reader.onload = function (e) {
                $('#file_name_value').text(fileName);
            };
            reader.readAsDataURL(files[0]);
            $('#preview-file-name').fadeIn(200);
        });

        $('#clear-file').bind('click', function (e) {
            document.getElementById("custom_css").value = null;
            $('#view_file').hide();
        });

        $('#clear-file_new_file').bind('click', function (e) {
            document.getElementById("custom_css").value = null;
            $('#preview-file-name').hide();
        });

        {{-- Campi CKEDITOR --}}
        CKEDITOR.replace('input_welcome_text');
        CKEDITOR.replace('input_footer_text');
        CKEDITOR.replace('input_accessibility_text');
        {{-- End CKEDITOR --}}

        let $dropdownTabularDisplayOrgIndPol = $('.select2-tabular_display_org_ind_pol');
        $dropdownTabularDisplayOrgIndPol.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        let $dropdownIndexable = $('.select2-show_indexable');
        $dropdownIndexable.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        let $dropdownShowUpdateDate = $('.select2-show_update_date');
        $dropdownShowUpdateDate.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        let $dropdownShowRegulationInStructure = $('.select2-show_regulation_in_structure');
        $dropdownShowRegulationInStructure.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        let $dropdownSmtpSecurity = $('.select2-smtp_security');
        $dropdownSmtpSecurity.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        let $dropdownShowSmtpAuth = $('.select2-show_smtp_auth');
        $dropdownShowSmtpAuth.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        let $dropdownSmtpPecSecurity = $('.select2-smtp_pec_security');
        $dropdownSmtpPecSecurity.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        let $dropdownSmtpPecAuth = $('.select2-smtp_pec_auth');
        $dropdownSmtpPecAuth.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        @if($_storageType === 'update')
        {{-- Begin Select2 campo "Responsabile trasparenza" --}}
        let $dropdownUsers = $('.select2-users');
        let institutionId = $('#institution_id').val();
        $dropdownUsers.select2({
            allowClear: true,
            placeholder: "Seleziona o cerca un utente...",
            // Recupero i dati per le options della select
            ajax: {
                url: '{{siteUrl("/admin/asyncData")}}',
                type: "get",
                dataType: 'json',
                delay: 250,
                data: function (data) {
                    return {
                        model: 39,
                        institution_id: institutionId,
                        searchTerm: data.term
                    };
                },
                error: function (jqXHR, status) {
                    let response = parseJson(jqXHR.responseText);
                },
                processResults: function (response) {
                    return {
                        results: response.data.options
                    };
                },
                cache: true
            }
        });
        // Recupero gli elementi gia selezionati e li setto nella select
        $.ajax({
            type: 'GET',
            url: '{{siteUrl("/admin/asyncSelectedData")}}',
            data: {
                id: $('#input_trasp_responsible_user_id').val(),
                model: 39,
                institution_id: institutionId,
            },
            error: function (jqXHR, status) {
                let response = parseJson(jqXHR.responseText);

                // Funzione che genera il toast con gli errori
                {{-- (vedere nel footer) --}}
                createValidatorFormErrorToast(
                    'Ops c\'è un problema.Si prega di contattare l\'assistenza clienti.',
                    5000,
                    'Validatore select'
                );
            }
        }).then(function (data) {
            let item = data.data.selected;
            for (const el of item) {
                // Creo l'opzione e l'appendo alla select
                var option = new Option(String(el.text), el.id, true, true);
                $dropdownUsers.append(option).trigger('change');
            }
        });
        {{-- End Select2 campo "Responsabile trasparenza" --}}
        @endif

        {{-- Begin Select2 campo "Ufficio referente per il contratto" --}}
        let $dropdownInstitutionType = $('.select2-institution_type_id');
        $dropdownInstitutionType.select2({
            placeholder: 'Seleziona struttura',
            allowClear: true,
            // Recupero i dati per le options della select
            ajax: {
                url: '{{siteUrl("/admin/asyncData")}}',
                type: "get",
                dataType: 'json',
                delay: 250,
                data: function (data) {
                    return {
                        model: 40,
                        searchTerm: data.term
                    };
                },
                error: function (jqXHR, status) {
                    let response = parseJson(jqXHR.responseText);
                },
                processResults: function (response) {
                    return {
                        results: response.data.options
                    };
                },
                cache: true
            },
        });
        @if(in_array($_storageType,['update', 'duplicate']))
        // Recupero gli elementi gia selezionati e li setto nella select
        $.ajax({
            type: 'GET',
            url: '{{siteUrl("/admin/asyncSelectedData")}}',
            data: {
                id: $('#_institution_type_id').val(),
                model: 40,
            },
            error: function (jqXHR, status) {
                let response = parseJson(jqXHR.responseText);

                // Funzione che genera il toast con gli errori
                {{-- (vedere nel footer) --}}
                createValidatorFormErrorToast(
                    'Ops c\'è un problema.Si prega di contattare l\'assistenza clienti.',
                    5000,
                    'Validatore select'
                );
            }
        }).then(function (data) {
            let item = data.data.selected;
            // Creo l'opzione e l'appendo alla select
            var option = new Option(String(item[0].text), item[0].id, true, true);
            $dropdownInstitutionType.append(option).trigger('change');
        });
        @endif

        {{-- Inizio metodi per campi Select --}}
        {{-- Begin Select2 campo "Riferimenti normativi" --}}
        let $dropdownSections = $('.select2-excluded_sections');
        $dropdownSections.select2({
            allowClear: true,
            // Recupero i dati per le options della select
            ajax: {
                url: '{{siteUrl("/admin/asyncData")}}',
                type: "get",
                dataType: 'json',
                delay: 250,
                data: function (data) {
                    return {
                        model: 43,
                        searchTerm: data.term
                    };
                },
                error: function (jqXHR, status) {
                    let response = parseJson(jqXHR.responseText);
                },
                processResults: function (response) {
                    return {
                        results: response.data.options
                    };
                },
                cache: true
            }
        });
        // Recupero gli elementi gia selezionati e li setto nella select
        @if(in_array($_storageType,['update', 'duplicate']))
        $.ajax({
            type: 'GET',
            url: '{{siteUrl("/admin/asyncSelectedData")}}',
            data: {
                id: $('#_sectionIds').val(),
                model: 43
            },
            error: function (jqXHR, status) {
                let response = parseJson(jqXHR.responseText);

                // Funzione che genera il toast con gli errori
                {{-- (vedere nel footer) --}}
                createValidatorFormErrorToast(
                    'Ops c\'è un problema.Si prega di contattare l\'assistenza clienti.',
                    5000,
                    'Validatore select'
                );
            }
        }).then(function (data) {
            let item = data.data.selected;
            for (const el of item) {
                // Creo l'opzione e l'appendo alla select
                var option = new Option(String(el.text), el.id, true, true);
                $dropdownSections.append(option).trigger('change');
            }
        });
        @endif
        {{-- End Select2 campo "Riferimenti normativi" --}}

        {{-- Begin salvataggio --}}
        let btnSend = $('#btn_save');
        $('#{{ $formSettings['id'] }}').ajaxForm({
            method: 'POST',
            beforeSerialize: function ($Form, options) {
                for (instance in CKEDITOR.instances) {
                    CKEDITOR.instances[instance].updateElement();
                }
                return true;
            },
            beforeSend: function () {
                btnSend.empty().append('{{ __('brn_save_spinner',null,'patos') }}').attr("disabled", true);
                $('.error-toast').remove();
            },
            success: function (data) {
                btnSend.empty().append('{{ __('brn_save',null,'patos') }}');
                let response = parseJson(data);
                formModified = false;

                // Funzione che genera il toast con il messaggio di successo
                {{-- (vedere nel footer) --}}
                createValidatorFormSuccessToast(response.data.message, 'Ente');

                @if(!empty($is_super_admin) && $is_super_admin)
                setTimeout(function () {
                    window.location.href = '{{ siteUrl('admin/institutions') }}';
                }, 800);
                @else
                setTimeout(function () {
                    window.location.href = '{{ siteUrl('admin/institution') }}';
                }, 400);
                @endif

            },
            complete: function (xhr) {
                btnSend.empty().append('{{ __('brn_save',null,'patos') }}');
            },
            error: function (jqXHR, status) {
                let response = parseJson(jqXHR.responseText);

                // Funzione che genera il toast con gli errori
                {{-- (vedere nel footer) --}}
                createValidatorFormErrorToast(response.errors.error, 10000);

                btnSend.empty().append('{{ __('brn_save',null,'patos') }}').attr("disabled", false);
            }
        });
        {{-- End salvataggio --}}

        {{-- End salvataggio --}}
        function convertAlphaNumDash(Text) {
            return Text.toLowerCase()
                .trim()
                .replace(/[^\w ]+/g, '')
                .replace(/ +/g, '_');
        }

        {{-- Apro la finestra modale per creare o editare lo stile da associare a CKEditor--}}
        $('#btn-CKEditor-file-css').bind('click', function (e) {
            e.preventDefault();

            $('#modal-CKEditor-css').modal('show');
            editAreaLoader.init({
                id: "CKEditor_code_css",
                syntax: "CSS",
                language: "it",
                start_highlight: true,
                allow_toggle: false,
            });

            /* RESET */
            $('#ckeditor_css_id').val('');
            $('#ckeditor_custom_style_title').val('');
            $('#ckeditor_custom_style_classname').val('');
            $('#ckeditor_custom_style_tag_name').val('');
            editAreaLoader.setValue('CKEditor_code_css', '');

            $('#ckeditor_custom_style_classname').bind('focusout', function (e) {
                if ($(this).val() !== '' && editAreaLoader.getValue('CKEditor_code_css') === '') {
                    $(this).val(convertAlphaNumDash($(this).val()));
                    let buildCss = '.' + convertAlphaNumDash($(this).val()) + ' {' + "\n\t" + ' /* Codice CSS custom in CKEditor */' + "\n" + '}';
                    editAreaLoader.setValue("CKEditor_code_css", buildCss);
                }
            });
        });

        {{-- Salvo il css customizzato in CKEditor --}}
        $('#btn-save-css-ckeditor').bind('click', function () {
            let btnCssCkEditor = $(this);

            jQuery.ajax({
                url: '{{ siteUrl('admin/institution/css/custom/ckeditor/save') }}',
                data: {
                    'id': $('#ckeditor_css_id').val(),
                    'title': $('#ckeditor_custom_style_title').val(),
                    'classname': $('#ckeditor_custom_style_classname').val(),
                    'tag': $('#ckeditor_custom_style_tag_name').val(),
                    'css': editAreaLoader.getValue('CKEditor_code_css'),
                    'institution_id': $('input[name="institution_id"]').val(),
                },
                method: 'POST',
                dataType: 'json',
                cache: false,

                beforeSend: function () {
                    btnCssCkEditor.empty()
                        .append('<i class="fas fa-spinner fa-spin"></i>&nbsp; Attendere ...')
                        .attr("disabled", true);
                },

                success: function (dataResp) {

                    var response = null;

                    try {
                        response = jQuery.parseJSON(dataResp);
                    } catch (e) {
                        response = dataResp;
                    }

                    $('#modal-CKEditor-css').modal('hide');

                    $('#ckeditor_css_id').val();
                    $('#ckeditor_custom_style_title').val();
                    $('#ckeditor_custom_style_classname').val();
                    $('#ckeditor_custom_style_tag_name').val();
                    editAreaLoader.setValue('CKEditor_code_css', '');
                    formModified = false;

                    window.location.href = '{{ currentUrl() }}';
                },

                complete: function (data) {
                    btnCssCkEditor.empty()
                        .append('<i class="fas fa-edit"></i> &nbsp; Registra CSS')
                        .attr("disabled", false);
                },
                error: function (error) {

                }
            });
        })

        {{-- Apro la finestra modale per editare il file CSS personalizzato e associato all'ente --}}
        $('#edit-file-css').bind('click', function () {

            var btnEditFileCss = $(this);

            jQuery.ajax({
                url: '{{ siteUrl('admin/institutions/css/edit') }}',
                data: {
                    'id': $('#institution_id').val()
                },
                method: 'GET',
                dataType: 'json',
                cache: false,

                beforeSend: function () {
                    btnEditFileCss.empty()
                        .append('<i class="fas fa-spinner fa-spin"></i>&nbsp; Attendere ...')
                        .attr("disabled", true);
                },

                success: function (dataResp) {

                    var response = null;

                    try {
                        response = jQuery.parseJSON(dataResp);
                    } catch (e) {
                        response = dataResp;
                    }

                    /* Apertura modale */
                    $('#edit_code_css').val('');
                    $('#edit_code_css').val(response['data']['content_css']);
                    $('#modal-editor-css').modal('show');

                    editAreaLoader.init({
                        id: "edit_code_css",
                        syntax: "CSS",
                        language: "it",
                        start_highlight: true,
                        allow_toggle: false,
                        /*toolbar: "search, go_to_line, fullscreen, |, undo, redo, |, select_font,|, change_smooth_selection, highlight, reset_highlight, word_wrap, syntax_selection"*/
                    });
                },

                complete: function (data) {
                    btnEditFileCss.empty()
                        .append('<i class="fas fa-edit"></i> &nbsp; Edita file')
                        .attr("disabled", false);
                },
                error: function (error) {

                }
            });

        });

        {{-- Salvataggio CSS --}}
        $('#btn-save-css').bind('click', function (e) {
            e.preventDefault();

            var btnSaveFileCss = $(this);

            jQuery.ajax({
                url: '{{ siteUrl('admin/institutions/css/save') }}',
                data: {
                    'id': $('#institution_id').val(),
                    'css': editAreaLoader.getValue("edit_code_css")
                },
                method: 'POST',
                dataType: 'json',
                cache: false,

                beforeSend: function () {
                    btnSaveFileCss.empty()
                        .append('<i class="fas fa-spinner fa-spin"></i>&nbsp; Attendere ...')
                        .attr("disabled", true);
                },

                success: function (dataResp) {
                    var response;

                    try {
                        response = jQuery.parseJSON(dataResp);
                    } catch (e) {
                        response = dataResp;
                    }

                    $('#modal-editor-css').modal('hide');

                    $(document).Toasts('create', {
                        class: 'bg-success',
                        title: 'ATTENZIONE',
                        subtitle: 'scrittura CSS',
                        autohide: true,
                        delay: 3000,
                        body: response.data.message
                    });
                },

                complete: function (data) {
                    btnSaveFileCss.empty()
                        .append('<i class="fas fa-edit"></i> &nbsp; Registra CSS')
                        .attr("disabled", false);
                },
                error: function (jqXHR) {
                    let response = parseJson(jqXHR.responseText);

                    $('#modal-editor-css').modal('hide');

                    $(document).Toasts('create', {
                        class: 'bg-danger',
                        title: 'ATTENZIONE',
                        subtitle: 'Scrittura CSS',
                        autohide: true,
                        delay: 3000,
                        body: response.errors.error
                    });

                }
            });
        });

        {{-- Edit --}}
        $('.edit_cutom_css_ckeditor').bind('click', function (e) {

            e.preventDefault();
            let url = $(this).attr('href');
            let anchor = $(this);

            jQuery.ajax({
                url: url,
                data: {
                    'id': parseInt(anchor.attr('data-id'))
                },
                method: 'GET',
                dataType: 'json',
                cache: false,

                beforeSend: function () {
                    anchor.empty()
                        .html('<i class="fas fa-spinner fa-spin"></i>');
                },

                success: function (dataResp) {

                    // var response;
                    let response
                    try {
                        response = jQuery.parseJSON(dataResp);
                    } catch (e) {
                        response = dataResp;
                    }

                    let data = response.data.record;
                    $('#modal-CKEditor-css').modal('show');
                    editAreaLoader.init({
                        id: "CKEditor_code_css",
                        syntax: "CSS",
                        language: "it",
                        start_highlight: true,
                        allow_toggle: false,
                    });

                    $('#ckeditor_css_id').val(data.id);
                    $('#ckeditor_custom_style_title').val(data.title);
                    $('#ckeditor_custom_style_classname').val(data.class_name);
                    $('#ckeditor_custom_style_tag_name').val(data.element);
                    editAreaLoader.setValue('CKEditor_code_css', data.css)
                },

                complete: function (data) {
                    anchor.empty()
                        .html('<i class="fas fa-edit"></i>');
                },
                error: function (jqXHR) {
                    let response = parseJson(jqXHR.responseText);

                    $('#modal-editor-css').modal('hide');

                    $(document).Toasts('create', {
                        class: 'bg-danger',
                        title: 'ATTENZIONE',
                        subtitle: 'Scrittura CSS',
                        autohide: true,
                        delay: 3000,
                        body: response.errors.error
                    });

                }
            });
        });

        {{-- Delete --}}
        $('.delete_cutom_css_ckeditor').bind('click', function (e) {
            e.preventDefault();
            formModified = false;
            window.location.href = $(this).attr('href');
        });

        {{-- Controllo per l'uscita dal form se i campi di input sono stati toccati --}}
        $(document).on('focus', '.select2-selection.select2-selection--single, .select2-selection.select2-selection--multiple, input, textarea', function (e) {
            formModified = true;
        });

        {{-- Messaggio di uscita senza salvare dal form --}}
        window.addEventListener('beforeunload', (event) => {
            if (formModified) {
                event.returnValue = 'Vuoi uscire dalla pagina?';
            }
        });

    });
</script>
{% endblock %}