{{-- Form store Patrimonio Immobiliare --}}
<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
{% extends layout/master %}
{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}
<div class="row justify-content-center">
    <div class="col-xl-10">
        {{ form_open($formAction,$formSettings) }}
        <div class="card mb-4" id="card-filter">
            <h4 class="card-header">
                <span>
                    <i class="fas fa-pencil-alt fa-sm mr-1"></i>
                    @if($_storageType === 'insert')
                        Aggiunta Patrimonio immobiliare
                    @elseif($_storageType === 'update')
                        Modifica Patrimonio immobiliare
                    @else
                        Duplicazione Patrimonio immobiliare
                    @endif
                </span>
                @if(empty($is_box))
                    <div class="card-tools">
                        <ul class="nav nav-pills ml-auto">
                            <li class="nav-item">
                                <a href="{{ siteUrl('admin/real-estate-asset') }}" title="Torna indietro"
                                   class="btn btn-default btn-sm btn-outline-primary">
                                    <i class="fas fa-caret-left"></i> Torna a elenco immobili
                                </a>
                            </li>
                        </ul>
                    </div>
                @endif
            </h4>

            <div class="card-body card-primary">

                <div class="row">
                    <div class="text-muted col-md-9 mb-4">
                        <i class="fas fa-exclamation-circle"></i> {{ nbs(1) }} I Campi contrassegnati dal
                        simbolo asterisco (*) sono obbligatori.
                    </div>

                    <div class="col-md-12 mb-3">
                        {{-- BEGIN: Form --}}
                        <div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Nome identificativo --}}
                                <div class="form-group col-md-6" id="input_name">
                                    <label for="name">Nome identificativo *</label>
                                    {{ form_input([
                                        'name' => 'name',
                                        'value' => !empty($real_estate_asset['name']) ? (($_storageType === 'duplicate') ? 'Copia di ' . $real_estate_asset['name'] : $real_estate_asset['name']) : null,
                                        'placeholder' => 'Nome',
                                        'id' => 'input_name',
                                        'class' => 'form-control input_name'
                                    ]) }}
                                </div>

                                {{-- Campo Indirizzo --}}
                                <div class="form-group col-md-6">
                                    <label for="address">Indirizzo</label>
                                    <div class="select2-blue" id="input_address">
                                        {{ form_input([
                                        'name' => 'address',
                                        'value' => !empty($real_estate_asset['address']) ? $real_estate_asset['address'] : null,
                                        'placeholder' => 'Indirizzo',
                                        'id' => 'input_address',
                                        'class' => 'form-control input_address'
                                    ]) }}
                                    </div>
                                </div>
                            </div>

                            {{-- Campo Ufficio utilizzatore --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="president">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label for="ajax_offices" id="r_offices_label">Ufficio utilizzatore</label>
                                    </div>
                                    <div id="ajax_offices"></div>
                                    <input type="hidden" value="" name="user_offices"
                                           id="input_user_office"
                                           class="user_offices">
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Foglio --}}
                                <div class="form-group col-md-4" id="input_sheet">
                                    <label for="sheet">Foglio</label>
                                    {{ form_input([
                                        'name' => 'sheet',
                                        'value' => !empty($real_estate_asset['sheet']) ? $real_estate_asset['sheet'] : null,
                                        'placeholder' => 'Foglio',
                                        'id' => 'input_sheet',
                                        'class' => 'form-control input_sheet'
                                    ]) }}
                                </div>

                                {{-- Campo Foglio --}}
                                <div class="form-group col-md-4" id="input_particle">
                                    <label for="particle">Particella</label>
                                    {{ form_input([
                                        'name' => 'particle',
                                        'value' => !empty($real_estate_asset['particle']) ? $real_estate_asset['particle'] : null,
                                        'placeholder' => 'Particella',
                                        'id' => 'input_particle',
                                        'class' => 'form-control input_particle'
                                    ]) }}
                                </div>

                                {{-- Campo Foglio --}}
                                <div class="form-group col-md-4" id="input_subalterno">
                                    <label for="subaltern">Subalterno</label>
                                    {{ form_input([
                                        'name' => 'subaltern',
                                        'value' => !empty($real_estate_asset['subaltern']) ? $real_estate_asset['subaltern'] : null,
                                        'placeholder' => 'Subalterno',
                                        'id' => 'input_subaltern',
                                        'class' => 'form-control input_subaltern'
                                    ]) }}
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Superficie lorda (mq) --}}
                                <div class="form-group col-md-6" id="input_gross_surface">
                                    <label for="gross_surface">Superficie lorda <small>(mq)</small></label>
                                    {{ form_input([
                                        'name' => 'gross_surface',
                                        'value' => !empty($real_estate_asset['gross_surface']) ? $real_estate_asset['gross_surface'] : null,
                                        'placeholder' => 'Superficie lorda (mq)',
                                        'id' => 'input_gross_surface',
                                        'class' => 'form-control input_gross_surface'
                                    ]) }}
                                </div>

                                {{-- Campo Superficie scoperta (mq) --}}
                                <div class="form-group col-md-6">
                                    <label for="discovered_surface">Superficie scoperta <small>(mq)</small></label>
                                    <div class="select2-blue" id="input_discovered_surface">
                                        {{ form_input([
                                        'name' => 'discovered_surface',
                                        'value' => !empty($real_estate_asset['discovered_surface']) ? $real_estate_asset['discovered_surface'] : null,
                                        'placeholder' => 'Superficie scoperta (mq)',
                                        'id' => 'input_discovered_surface',
                                        'class' => 'form-control input_discovered_surface'
                                    ]) }}
                                    </div>
                                </div>
                            </div>

                            {{-- Campo Descrizione e note --}}
                            <div class="form-group">
                                <label for="description">Descrizione e note</label>
                                {{form_editor([
                                    'name' => 'description',
                                    'value' => !empty($real_estate_asset['description']) ? $real_estate_asset['description'] : null,
                                    'id' => 'input_description',
                                    'class' => 'form-control input_description'
                                ]) }}
                            </div>
                        </div>
                        {{-- END: Form --}}
                    </div>
                    {{-- ***** BEGIN: include attach**** --}}
                    {% include layout/partials/attach %}
                    {{-- ***** END: include attach**** --}}
                </div>
            </div>

            {{-- Card Footer --}}
            <div class="card-footer" id="__save_">
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

        @if(!empty($real_estate_asset['id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'id',
                'value' => $real_estate_asset['id'],
                'id' => 'real_estate_asset_id',
                'class' => 'real_estate_asset_id',
            ]) }}
        @endif

        @if(!empty($institution_id))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'institution_id',
                'value' => $institution_id,
                'id' => 'institution_id',
                'class' => 'institution_id',
            ]) }}
        @endif

        {{ form_hidden('institute_id',PatOsInstituteId()) }}
        {{ form_close() }}
    </div>
</div>

{% include layout/partials/form_modal %}


{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
<style type="text/css">
    .ck-editor__editable_inline {
        min-height: 200px;
    }

    @if(!empty($is_box) && !$is_box)
    #modal-toast {
        z-index: 1;
    }
    @endif
</style>
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
{{ js('ckeditor4/ckeditor.js', 'common') }}
{{ js('patos/jquery.patOsAjaxPagination.js', 'common') }}
{{ js('admin/get/config.js') }}

<script type="text/javascript">

    //Previene il salvataggio quando si preme invio e il focus non è sul pulsante di salvataggio
    $('#{{ $formSettings['id'] }}').on('keyup keypress', function (e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13 && e.target.tagName!='TEXTAREA') {
            e.preventDefault();
            return false;
        }
    });

    $(document).ready(function () {

        let formModified = false;
        let institutionId = $('#institution_id').val();

        // Tabella per la selezione degli uffici utilizzatori
        let offices = $('#ajax_offices').patOsAjaxPagination({
            url: config.structure.url,
            textLoad: config.structure.textLoad,
            selectedLabel: 'Uffici selezionati',
            footerTable: config.structure.footerTable,
            classTable: config.structure.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.structure.hideTable,
            showTable: config.structure.showTable,
            search_placeholder: config.structure.search_placeholder,
            setInputDataValue: '#input_user_office',
            dataParams: {
                model: 1,
                institution_id: institutionId
            },
            columns: config.structure.columns,
            action: {
                type: 'checkbox',
            },
            dataSource: config.structure.dataSource,
            addRecord: config.structure.addRecord,
            archived: config.structure.archived,
            label: '#r_offices_label'
        });

        // Se sono in modifica o in duplicazione setto i valori il presidente della commissione gia selezionato
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($real_estate_asset['offices']))
        @foreach($real_estate_asset['offices'] as $structure)
        offices.patOsAjaxPagination.setValue('{{ $structure['id'] }}', '{{ htmlEscape($structure['structure_name']).(!empty($structure['parent_name'])?' - '.htmlEscape($structure['parent_name']):'').(!empty($structure['reference_email'])?' - '.$structure['reference_email']:'') }}', true);
        @endforeach
        @endif

        {{-- Begin Select2 campo "Ufficio utilizzatore" --}}
        let $dropdownUserOffice = $('.select2-user_office');
        $dropdownUserOffice.select2({
            //Recupero i dati per le options della select
            ajax: {
                url: '{{siteUrl("/admin/asyncData")}}',
                type: "get",
                dataType: 'json',
                delay: 250,
                data: function (data) {
                    return {
                        model: 1,
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
        @if(in_array($_storageType,['update', 'duplicate']))
        // Recupero gli elementi gia selezionati e li setto nella select
        $.ajax({
            type: 'GET',
            url: '{{siteUrl("/admin/asyncSelectedData")}}',
            data: {
                id: $('#_userOfficeIds').val(),
                model: 1,
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
            },
        }).then(function (data) {
            let item = data.data.selected;
            // Creo l'opzione e l'appendo alla select
            for (const el of item) {
                var option = new Option(String(el.text), el.id, true, true);
                $dropdownUserOffice.append(option).trigger('change');
            }
        });
        @endif
        {{-- End Select2 campo "Ufficio utilizzatore" --}}

        {{-- Campo CKEDITOR --}}
        // Cattura eventuali modifiche fatte sui campi CKEDITOR
        CKEDITOR.on('instanceCreated', function (e) {
            e.editor.on('change', function (event) {
                formModified = true;
            });
        });
        CKEDITOR.replace('input_description');

        {{-- Select2 campo "Stato pubblicazione" --}}
        let $dropdownPublishingStatus = $('.select2-publishing_status');
        $dropdownPublishingStatus.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        /**
         * Metodo per il salvataggio
         */
                {{-- Begin salvataggio --}}
        let btnSend = $('#btn_save');
        $('#{{ $formSettings['id'] }}').ajaxForm({
            method: 'POST',
            // Aggiorno il valore dei campi CKEDITOR prima che vengono recuperati per l'invio
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
                createValidatorFormSuccessToast(response.data.message, 'Patrimonio immobiliare');

                {{-- Controllo se non sono all'interno di un modale --}}
                @if(empty($is_box))
                setTimeout(function () {
                    window.location.href = '{{ siteUrl('admin/real-estate-asset') }}';
                }, 800);

                @else
                setTimeout(function () {
                    window.parent.$('#formModal').modal('hide');
                }, 800);
                @endif

            },
            complete: function (xhr) {
                btnSend.empty().append('{{ __('brn_save',null,'patos') }}');
            },
            error: function (jqXHR, status) {
                let response = parseJson(jqXHR.responseText);

                //Messaggi di errore del validatore campi e degli allegati
                let msg = [response.data.error_partial_attach, response.errors.error].filter(Boolean).join(", ");

                // Funzione che genera il toast con gli errori
                {{-- (vedere nel footer) --}}
                createValidatorFormErrorToast(msg, 6000);

                btnSend.empty().append('{{ __('brn_save',null,'patos') }}').attr("disabled", false);
            }
        });
        {{-- End salvataggio --}}

        /**
         * Funzione che apre il modale per l'aggiunta di nuovi elementi direttamente all'interno del form
         */
        {{-- Vedere nel footer --}}
        openModalForm();

        /**
         * Funzione che viene eseguita alla chiusura del modale di aggiunta di nuovi elementi direttamente dal form.
         * Pulisce l'iframe.
         */
        {{-- Vedere nel footer --}}
        closeModalForm();

        /**
         * Controllo per l'uscita dal form se i campi di input sono stati toccati
         */
        $(document).on('focus',
            '.select2-selection.select2-selection--single, .select2-selection.select2-selection--multiple, input',
            function (e) {
                formModified = true;
            });

        @if(!$is_box)
        {{-- Messaggio di uscita senza salvare dal form --}}
        window.addEventListener('beforeunload', (event) => {
            if (formModified) {
                event.returnValue = 'Vuoi uscire dalla pagina?';
            }
        });
        @endif

        /**
         * Funzione che controlla se sono arrivato in questa pagina dal versioning
         */
        {{-- Vedere nel footer --}}
        checkIfRestore();

    });
</script>
{% endblock %}