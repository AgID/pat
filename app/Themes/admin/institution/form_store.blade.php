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
                    <span class="text-pat-primary"><b>Gestione informazioni Ente</b></span>
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
                        <a href="#!" class="item_layer" data-id="4" title="Social Network">
                            <div class="_flex">
                                <div class="mr-2"><i class="fas fa-share-alt _black"></i></div>
                                <div>Social Network</div>
                            </div>
                        </a>
                    </li>
                    <li class="list-group-item">
                        <a href="#!" class="item_layer" data-id="6" title="Configurazione Email">
                            <div class="_flex">
                                <div class="mr-2"><i class="fas fa-envelope-open"></i></div>
                                <div>Configurazione Email</div>
                            </div>
                        </a>
                    </li>
                </ul>

                @php
                    $show = true;
                    $show = $show ?? null;
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

                        {{-- BEGIN: Social Network --}}
                        <div class="type_layer" id="layer_4">
                            {{-- Inclusione del blocco Social Network --}}
                            {% include institution/include_social_info %}
                        </div>
                        {{-- END: Social Network --}}

                        {{-- BEGIN: Altre informazioni --}}
                        <div class="type_layer" id="layer_5">
                            {{-- Inclusione del blocco Altre Informazioni --}}
                            {% include institution/include_other_info %}
                        </div>
                        {{-- END: Altre informazioni --}}

                        {{-- BEGIN: Configurazione Email --}}
                        <div class="type_layer" id="layer_6">
                            {{-- Inclusione del blocco Configurazione Email --}}
                            {% include institution/include_smtp_configuration %}
                        </div>
                        {{-- END: Configurazione Email --}}











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

        {{ form_input([
            'type' => 'hidden',
            'name' => '_roles',
            'value' => null,
            'id' => '_roles',
            'class' => '_roles',
        ]) }}

        {{ form_input([
            'type' => 'hidden',
            'name' => '_qualifications',
            'value' => null,
            'id' => '_qualifications',
            'class' => '_qualifications',
        ]) }}

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
</div>



{% include institution/personnel_qualifications_modal %}

{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
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
    document.getElementById('{{ $formSettings['id'] }}').addEventListener('keydown', e => {
        if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
            e.preventDefault();
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
            let iconSetted = $(this).children().children().children();

            let iconInfo = iconSetted[0].dataset;
            let iconToSet = "<i class='" + iconInfo['prefix'] + " fa-" + iconInfo['icon']+"'></i>";


            //let icon = iconSet.hasClass('fas') || iconSet.hasClass('far')
            //    ? '<i class="' + iconSet.attr('class') + '"></i>'
            //    : '<img src="' + iconSet.attr('src') + '" width="' + iconSet.attr('width') + '" height="' + iconSet.attr('height') + '" alt="' + $(this).attr('title') + '">';

            $('#title_layer').empty().append(iconToSet + ' {{ nbs(1) }} ' + $(this).attr('title'));
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

        {{-- Campi CKEDITOR --}}
        CKEDITOR.replace('input_welcome_text', {
             customConfig: '{{ verifyFirstOrLastSlash('assets/common/ckeditor4/config.js')}}',
        });
        CKEDITOR.replace('input_footer_text', {
             customConfig: '{{ verifyFirstOrLastSlash('assets/common/ckeditor4/config.js')}}',
        });
        CKEDITOR.replace('input_accessibility_text', {
            customConfig: '{{ verifyFirstOrLastSlash('assets/common/ckeditor4/config.js')}}',
        });
        {{-- End CKEDITOR --}}


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
       let institutionId = document.getElementById('institution_id').value ?? '';
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
                let option = new Option(String(el.text), el.id, true, true);
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
            let option = new Option(String(item[0].text), item[0].id, true, true);
            $dropdownInstitutionType.append(option).trigger('change');
        });
        @endif

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

                setTimeout(function () {
                    window.location.href = '{{ siteUrl('admin/institution') }}';
                }, 400);

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

    function rimuoviAtMap() {
        Swal.fire({
            ...SWAL_CONFIRM,
            title: 'Conferma rimozione',
            text: 'Sei sicuro di voler rimuovere la mappa personalizzata?'
        }).then((result) => {
            if (result.isConfirmed) {
                document.getElementById('delete_custom_at_map').value = '1';
                document.getElementById('btn-remove-map').textContent = 'La mappa caricata verrà rimossa';
            }
        });
    }

</script>
{% endblock %}
