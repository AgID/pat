{{-- Form store Oneri informativi e obblighi --}}
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
                        Aggiunta Oneri informativi e obblighi
                    @elseif($_storageType === 'update')
                        Modifica Oneri informativi e obblighi
                    @else
                        Duplicazione Oneri informativi e obblighi
                    @endif
                </span>
                @if(empty($is_box))
                    <div class="card-tools">
                        <ul class="nav nav-pills ml-auto">
                            <li class="nav-item">
                                <a href="{{ siteUrl('admin/charge') }}" title="Torna indietro"
                                   class="btn btn-default btn-sm btn-outline-primary">
                                    <i class="fas fa-caret-left"></i> Torna a elenco Oneri
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

                            <div class="form-group">
                                {{-- Campo Denominazione o titolo --}}
                                <label for="title">Denominazione o titolo *</label>
                                {{ form_input([
                                    'name' => 'title',
                                    'value' => !empty($charge['title']) ? (($_storageType === 'duplicate') ? 'Copia di ' . $charge['title'] : $charge['title']) : null,
                                    'placeholder' => 'Denominazione o titolo',
                                    'id' => 'input_title',
                                    'class' => 'form-control input_title',
                                ]) }}
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Per cittadini --}}
                                <div class="form-group col-md-6">
                                    <label for="citizen">Per Cittadini</label>
                                    <div class="select2-blue" id="input_citizen">
                                        {{ form_dropdown(
                                            'citizen',
                                            [''=>'',0=>'No',1=>'Si'],
                                            @$charge['citizen'],
                                            'class="form-control select2-citizen" data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>

                                {{-- Campo Per Imprese --}}
                                <div class="form-group col-md-6">
                                    <label for="companies">Per Imprese</label>
                                    <div class="select2-blue" id="input_companies">
                                        {{ form_dropdown(
                                            'companies',
                                            [''=>'',0=>'No',1=>'Si'],
                                            @$charge['companies'],
                                            'class="form-control select2-companies" data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Tipologia --}}
                                <div class="form-group col-md-6">
                                    <label for="type">Tipologia *</label>
                                    <div class="select2-blue" id="input_type">
                                        {{ form_dropdown(
                                            'type',
                                            [
                                                ''=>'',
                                                'onere'=>'Pubblica in Oneri informativi per cittadini ed imprese',
                                                'obbligo'=>'Pubblica in Scadenzario obblighi amministrativi'
                                            ],
                                            @$charge['type'],
                                            'class="form-control select2-type" data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>

                                {{-- Campo Data di scadenza --}}
                                <div class="form-group col-md-6">
                                    <label for="expiration_date">Data di scadenza</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="expiration_date"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control"
                                               value="{{ @$expiration_date }}"
                                               id="expiration_date">
                                    </div>
                                </div>
                            </div>

                            {{generateSeparator('Informazioni collegate')}}

                            {{-- Campo Procedimenti associati --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-9">
                                    <label for="proceedings">Procedimenti associati</label>
                                    <div class="select2-blue" id="input_proceedings">
                                        {{ form_dropdown(
                                            'proceedings[]',
                                            '',
                                            '',
                                            'class="form-control select2-proceedings" multiple="multiple" data-placeholder="Seleziona o cerca procedimenti..." data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>

                                <div class="form-group col-md-3">
                                    {{ form_button([
                                        'name' => 'add',
                                        'id' => 'btn_addProceeding',
                                        'class' => 'btn btn-outline-primary open-modal',
                                        'style' => 'width:100%;',
                                        'data-url' => siteUrl('admin/proceeding/create-box')
                                    ],'Aggiungi nuovo &nbsp; <i class="fas fa-plus-circle"></i>') }}
                                </div>
                            </div>

                            {{-- Campo Provvedimenti associati --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_measures" id="c_ajax_measures">Provvedimenti associati</label>
                                    </div>
                                    <div id="ajax_measures"></div>
                                    <input type="hidden" value="" name="measures" id="input_measures"
                                           class="measures">
                                </div>
                            </div>

                            {{-- Campo Regolamenti associati --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-9">
                                    <label for="regulations">Regolamenti associati</label>
                                    <div class="select2-blue" id="input_regulations">
                                        {{ form_dropdown(
                                            'regulations[]',
                                            '',
                                            '',
                                            'class="form-control select2-regulations" multiple="multiple" data-placeholder="Seleziona o cerca regolamento..." data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>

                                <div class="form-group col-md-3">
                                    {{ form_button([
                                        'name' => 'add',
                                        'id' => 'btn_addRegulation',
                                        'class' => 'btn btn-outline-primary open-modal',
                                        'style' => 'width:100%;',
                                        'data-url' => siteUrl('admin/regulation/create-box')
                                    ],'Aggiungi nuovo &nbsp; <i class="fas fa-plus-circle"></i>') }}
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Riferimenti normativi --}}
                                <div class="form-group col-md-9">
                                    <label for="normative_id">Riferimenti normativi</label>
                                    <div class="select2-blue" id="input_normative_id">
                                        {{ form_dropdown(
                                            'normative_id',
                                            ['' => ''],
                                            @$charge['normative_id'],
                                            'class="select2-normative_id" data-dropdown-css-class="select2-blue" style="width: 100%; height: unset;"'
                                        ) }}
                                    </div>
                                </div>

                                <div class="form-group col-md-3">
                                    {{ form_button([
                                        'name' => 'add',
                                        'id' => 'btn_addNormative',
                                        'class' => 'btn btn-outline-primary open-modal',
                                        'style' => 'width:100%;',
                                        'data-url' => !empty($charge['normative_id']) ? siteUrl('admin/normative/edit-box/' . $charge['normative_id']) : siteUrl('admin/normative/create-box')
                                    ],!empty($charge['normative_id']) ? 'Modifica &nbsp; <i class="far fa-edit"></i>' : 'Aggiungi nuovo &nbsp; <i class="fas fa-plus-circle"></i>') }}
                                </div>
                            </div>

                            {{generateSeparator('Altre informazioni')}}

                            <div class="form-group">
                                {{-- Campo Contenuto --}}
                                <label for="description">Contenuto</label>
                                {{form_editor([
                                    'name' => 'description',
                                    'value' => !empty($charge['description']) ? $charge['description'] : null,
                                    'id' => 'input_description',
                                    'class' => 'form-control input_description'
                                ]) }}
                            </div>

                            {{-- Campo URL per maggiori informazioni --}}
                            <div class="form-group" id="input_website_url">
                                <label for="info_url">URL per maggiori informazioni</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-link"></i></span>
                                    </div>
                                    {{ form_input([
                                    'name' => 'info_url',
                                    'value' => !empty($charge['info_url']) ? $charge['info_url'] : null,
                                    'placeholder' => 'https://www.',
                                    'id' => 'input_info_url',
                                    'class' => 'form-control input_info_url',
                                ]) }}
                                </div>
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

        @if(!empty($charge['id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'id',
                'value' => $charge['id'],
                'id' => 'charge_id',
                'class' => 'charge_id',
            ]) }}
        @endif

        @if(!empty($proceedingIds))
            {{ form_input([
                'type' => 'hidden',
                'name' => '_proceedingIds',
                'value' => implode(',',$proceedingIds),
                'id' => '_proceedingIds',
                'class' => '_proceedingIds',
            ]) }}
        @endif

        @if(!empty($regulationIds))
            {{ form_input([
                'type' => 'hidden',
                'name' => '_regulationIds',
                'value' => implode(',',$regulationIds),
                'id' => '_regulationIds',
                'class' => '_regulationIds',
            ]) }}
        @endif

        @if(!empty($charge['normative_id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => '_normative_id',
                'value' => $charge['normative_id'],
                'id' => '_normative_id',
                'class' => '_normative_id',
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

        {{ form_hidden('institute_id',checkAlternativeInstitutionId()) }}
        {{ form_close() }}
    </div>
</div>

{% include layout/partials/form_modal %}

{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
{{ css('select2/css/select2.min.css','common') }}
{{ css('select2-bootstrap4-theme/select2-bootstrap4.min.css','common') }}
<style type="text/css">
    .ck-editor__editable_inline {
        min-height: 200px;
    }
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
        if (keyCode === 13 && e.target.tagName != 'TEXTAREA') {
            e.preventDefault();
            return false;
        }
    });

    $(document).ready(function () {

        let formModified = false;
        let institutionId = $('#institution_id').val();

        {{-- Campo select per tipo Onere --}}
        let $dropdownType = $('.select2-type');
        $dropdownType.select2({
            placeholder: 'Seleziona il tipo....',
            minimumResultsForSearch: -1,
            allowClear: true
        });

        {{-- Begin campi select --}}
        {{-- Begin Select2 campo "Procedimenti associati" --}}
        let $dropdownProceedings = $('.select2-proceedings');
        $dropdownProceedings.select2({
            //Recupero i dati per le options della select
            ajax: {
                url: '{{siteUrl("/admin/asyncData")}}',
                type: "get",
                dataType: 'json',
                delay: 250,
                data: function (data) {
                    return {
                        model: 6,
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
            },
            escapeMarkup: function (text) {
                return text;
            },
        });
        @if(in_array($_storageType,['update', 'duplicate']))
        // Recupero gli elementi gia selezionati e li setto nella select
        $.ajax({
            type: 'GET',
            url: '{{siteUrl("/admin/asyncSelectedData")}}',
            data: {
                id: $('#_proceedingIds').val(),
                model: 6,
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
                $dropdownProceedings.append(option).trigger('change');
            }
        });
        @endif
        {{-- End Select2 campo "Procedimenti associati" --}}

        // Tabella per la selezione dei Provvedimenti associati
        let measures = $('#ajax_measures').patOsAjaxPagination({
            url: config.measure.url,
            textLoad: config.measure.textLoad,
            selectedLabel: 'Provvedimenti selezionati',
            footerTable: config.measure.footerTable,
            classTable: config.measure.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.measure.hideTable,
            showTable: config.measure.showTable,
            search_placeholder: config.measure.search_placeholder,
            setInputDataValue: '#input_measures',
            dataParams: {
                model: 22,
                institution_id: institutionId,
            },
            dateFormat: config.measure.dateFormat,
            columns: config.measure.columns,
            action: {
                type: 'checkbox',
            },
            dataSource: config.measure.dataSource,
            addRecord: config.measure.addRecord,
            label: '#c_ajax_measures'
        });

        // Setto il bando relativo se sono in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($charge['measures']))
        @foreach($charge['measures'] as $measure)
        @php
            $tmpStartDate = !empty($measure['date']) ? ' - '.date('d/m/Y', strtotime($measure['date'])) : null;
        @endphp
        measures.patOsAjaxPagination.setValue('{{ $measure['id'] }}', '{{ htmlEscape($measure['object']).' - '.$measure['number'].''.$tmpStartDate }}', true);
        @endforeach
        @endif

        {{-- Begin Select2 campo "Regolamenti associati" --}}
        let $dropdownRegulation = $('.select2-regulations');
        $dropdownRegulation.select2({
            //Recupero i dati per le options della select
            ajax: {
                url: '{{siteUrl("/admin/asyncData")}}',
                type: "get",
                dataType: 'json',
                delay: 250,
                data: function (data) {
                    return {
                        model: 10,
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
                id: $('#_regulationIds').val(),
                model: 10,
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
                $dropdownRegulation.append(option).trigger('change');
            }
        });
        @endif
        {{-- End Select2 campo "Regolamenti associati" --}}

        {{-- Begin Select2 campo "Riferimenti normativi" --}}
        let $dropdownNormative = $('.select2-normative_id');
        $dropdownNormative.select2({
            placeholder: 'Seleziona o cerca normativa....',
            allowClear: true,
            //Recupero i dati per le options della select
            ajax: {
                url: '{{siteUrl("/admin/asyncData")}}',
                type: "get",
                dataType: 'json',
                delay: 250,
                data: function (data) {
                    return {
                        model: 12,
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
                id: $('#_normative_id').val(),
                model: 12,
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
                $dropdownNormative.append(option).trigger('change');
            }
        });
        @endif

        /**
         * Metodo che viene chiamato quando si pulisce la select2.
         * Cambia il testo e l'azione del pulsante, settandoli per l'aggiunta di un nuovo elemento
         */
        $dropdownNormative.on('select2:clearing', function (e) {
            $('#btn_addNormative').attr('data-url', '{{ siteUrl('admin/normative/create-box') }}')
            $('#btn_addNormative').empty().append('Aggiungi nuovo &nbsp; <i class="fas fa-plus-circle"></i>');
        });

        /**
         * Metodo che viene chiamato quando si seleziona una voce nella select2.
         * Cambia il testo e l'azione del pulsante, settandoli per la modifica dell'elemento selezionato
         */
        $dropdownNormative.on('select2:selecting', function (e) {
            $('#btn_addNormative').attr('data-url', '{{ baseUrl('admin/normative/edit-box/') }}' + e.params.args.data.id);
            $('#btn_addNormative').empty().append('Modifica &nbsp; <i class="far fa-edit"></i>');
        });

        {{-- Select2 campo "Stato pubblicazione" --}}
        let $dropdownPublishingStatus = $('.select2-publishing_status');
        $dropdownPublishingStatus.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });
        {{-- End Select2 campo "Riferimenti normativi" --}}

        {{-- Campo select Per i cittadini --}}
        let $dropdownCitizen = $('.select2-citizen');
        $dropdownCitizen.select2({
            placeholder: 'Seleziona',
            allowClear: true,
            minimumResultsForSearch: -1
        });

        {{-- Campo select per le società --}}
        let $dropdownCompanies = $('.select2-companies');
        $dropdownCompanies.select2({
            placeholder: 'Seleziona',
            allowClear: true,
            minimumResultsForSearch: -1
        });
        {{-- End campi select --}}

        {{-- Campo CKEDITOR --}}
        // Cattura eventuali modifiche fatte sui campi CKEDITOR
        CKEDITOR.on('instanceCreated', function (e) {
            e.editor.on('change', function (event) {
                formModified = true;
            });
        });
        CKEDITOR.replace('input_description');

        {{-- Begin salvataggio --}}
        /**
         * Metodo per il salvataggio
         */
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
                createValidatorFormSuccessToast(response.data.message, 'Oneri informativi e obblighi');

                {{-- Controllo se non sono all'interno di un modale --}}
                @if(empty($is_box))
                setTimeout(function () {
                    window.location.href = '{{ siteUrl('admin/charge') }}';
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

        {{-- Messaggio di uscita senza salvare dal form --}}
        window.addEventListener('beforeunload', (event) => {
            if (formModified) {
                event.returnValue = 'Vuoi uscire dalla pagina?';
            }
        });

        {{-- Vedere nel footer --}}
        /**
         * Funzione che controlla se sono arrivato in questa pagina dal versioning
         */
        checkIfRestore();
    });
</script>
{% endblock %}