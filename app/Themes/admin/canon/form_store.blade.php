{{-- Form Canoni di locazzione --}}
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
                        Aggiunta Canone di locazione
                    @elseif($_storageType === 'update')
                        Modifica Canone di locazione
                    @else
                        Duplicazione Canone di locazione
                    @endif
                </span>
                <div class="card-tools">
                    <ul class="nav nav-pills ml-auto">
                        <li class="nav-item">
                            <a href="{{ siteUrl('admin/canon') }}" title="Torna indietro"
                               class="btn btn-default btn-sm btn-outline-primary">
                                <i class="fas fa-caret-left"></i> Torna a elenco canoni
                            </a>
                        </li>
                    </ul>
                </div>
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
                                {{-- Campo Tipo canone --}}
                                <div class="form-group col-md-12">
                                    <label for="canon_type">Tipo canone *</label>
                                    <div class="select2-blue" id="input_canon_type">
                                        {{ form_dropdown(
                                            'canon_type',
                                            [null => '',1=>'Canoni di locazione o di affitto versati',2=>'Canoni di locazione o di affitto percepiti'],
                                            @$canon['canon_type'],
                                            'class="form-control input_canon_type" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Informazioni sul beneficiario --}}
                                <div class="form-group col-md-6 _beneficiary-info">
                                    <label for="beneficiary">Informazioni sul beneficiario *</label>
                                    {{ form_input([
                                        'name' => 'beneficiary',
                                        'value' => !empty($canon['beneficiary']) ? (($_storageType === 'duplicate') ? 'Copia di ' . $canon['beneficiary'] : $canon['beneficiary']) : null,
                                        'placeholder' => 'Info',
                                        'id' => 'input_beneficiary',
                                        'class' => 'form-control input_beneficiary',
                                    ]) }}
                                </div>

                                {{-- Campo Partita IVA /Cod. fisc. beneficiario --}}
                                <div class="form-group col-md-6 _beneficiary-info" id="input_fiscal_code">
                                    <label for="fiscal_code">Partita IVA / Cod. fisc. beneficiario</label>
                                    {{ form_input([
                                        'name' => 'fiscal_code',
                                        'value' => !empty($canon['fiscal_code']) ? $canon['fiscal_code'] : null,
                                        'placeholder' => 'Inserire qui',
                                        'id' => 'input_fiscal_code',
                                        'class' => 'form-control input_fiscal_code',
                                    ]) }}
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Importo --}}
                                <div class="form-group col-md-6">
                                    <label for="amount">Importo *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-euro-sign"></i></span>
                                        </div>
                                        {{ form_input([
                                        'name' => 'amount',
                                        'value' => !empty($canon['amount']) ? $canon['amount'] : null,
                                        'placeholder' => '',
                                        'id' => 'input_amount',
                                        'class' => 'form-control input_amount a-num-class',
                                    ]) }}
                                    </div>
                                </div>

                                {{-- Campo Estremi del contratto --}}
                                <div class="form-group col-md-6" id="input_contract_statements">
                                    <label for="contract_statementscontract_statements">Estremi del contratto</label>
                                    {{ form_input([
                                        'name' => 'contract_statements',
                                        'value' => !empty($canon['contract_statements']) ? $canon['contract_statements'] : null,
                                        'placeholder' => 'Estremi',
                                        'id' => 'input_contract_statements',
                                        'class' => 'form-control input_contract_statements',
                                    ]) }}
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Data inizio --}}
                                <div class="form-group col-md-6">
                                    <label for="start_date">Data inizio *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="start_date"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control" value="{{ @$start_date }}"
                                               id="start_date">
                                    </div>
                                </div>

                                {{-- Campo Data fine --}}
                                <div class="form-group col-md-6">
                                    <label for="end_date">Data fine *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="end_date"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control" value="{{ @$end_date }}"
                                               id="end_date">
                                    </div>
                                </div>
                            </div>

                            {{-- Campo Immobile --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-9">
                                    <label for="property">Immobile *</label>
                                    <div class="select2-blue" id="input_property">
                                        {{ form_dropdown(
                                            'properties[]',
                                            '',
                                            '',
                                            'class="form-control select2-property" multiple="multiple" data-placeholder="Seleziona o cerca un immobile..." data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>

                                <div class="form-group col-md-3">
                                    {{ form_button([
                                        'name' => 'add',
                                        'id' => 'btn_addResponsible',
                                        'class' => 'btn btn-outline-primary open-modal',
                                        'style' => 'width:100%;',
                                        'data-url' => siteUrl('admin/real-estate-asset/create-box')
                                    ],'Aggiungi nuovo &nbsp; <i class="fas fa-plus-circle"></i>') }}
                                </div>
                            </div>

                            {{-- Campo Ufficio referente per il contratto --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="president">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_structure" id="canon_structure_label">Ufficio referente per il
                                            contratto</label>
                                    </div>
                                    <div id="ajax_structure"></div>
                                    <input type="hidden" value="" name="object_structures_id"
                                           id="input_object_structures_id"
                                           class="object_structures_id">
                                </div>
                            </div>

                            {{-- Campo Note --}}
                            <div class="form-group">
                                <label for="notes">Note</label>
                                {{form_editor([
                                    'name' => 'notes',
                                    'value' => !empty($canon['notes']) ? $canon['notes'] : null,
                                    'id' => 'input_notes',
                                    'class' => 'form-control input_notes'
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

        @if(!empty($propertyIds))
            {{ form_input([
                'type' => 'hidden',
                'name' => '_propertyIds',
                'value' => implode(',',$propertyIds),
                'id' => '_propertyIds',
                'class' => '_propertyIds',
            ]) }}
        @endif

        @if(!empty($canon['id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'id',
                'value' => $canon['id'],
                'id' => 'canon_id',
                'class' => 'canon_id',
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

        @if(!empty($canon['canon_type']) && $canon['canon_type'] == 2)
        $('._beneficiary-info').hide();
        @endif

        let formModified = false;
        let institutionId = $('#institution_id').val();

        {{-- Begin Select2 campo "Immobile" --}}
        let $dropdownProperty = $('.select2-property');
        $dropdownProperty.select2({
            placeholder: 'Seleziona o cerca tra i gli immobili....',
            //Recupero i dati per le options della select
            ajax: {
                url: '{{siteUrl("/admin/asyncData")}}',
                type: "get",
                dataType: 'json',
                delay: 250,
                data: function (data) {
                    return {
                        model: 7,
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
                id: $('#_propertyIds').val(),
                model: 7,
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
                $dropdownProperty.append(option).trigger('change');
            }
        });
        @endif
        {{-- End Select2 campo "Immobile" --}}

        // Tabella per la selezione della struttura di appartenenza
        let office = $('#ajax_structure').patOsAjaxPagination({
            url: config.structure.url,
            textLoad: config.structure.textLoad,
            selectedLabel: 'Ufficio selezionato',
            footerTable: config.structure.footerTable,
            classTable: config.structure.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.structure.hideTable,
            showTable: config.structure.showTable,
            search_placeholder: config.structure.search_placeholder,
            setInputDataValue: '#input_object_structures_id',
            dataParams: {
                model: 1,
                institution_id: institutionId
            },
            columns: config.structure.columns,
            action: {
                type: 'radio',
            },
            dataSource: config.structure.dataSource,
            addRecord: config.structure.addRecord,
            archived: config.structure.archived,
            label: '#canon_structure_label'
        });

        // Se sono in modifica o in duplicazione setto i valori il presidente della commissione gia selezionato
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($canon['structure']))
        office.patOsAjaxPagination.setValue('{{ $canon['object_structures_id'] }}', '{{ htmlEscape($canon['structure']['structure_name']).' - '.(!empty($canon['object_structures_id'])?htmlEscape($canon['structure']['parent_name']):'').(!empty($canon['structure']['reference_email'])?' - '.$canon['structure']['reference_email']:'N.D') }}', true);
        @endif

        {{-- Begin campi EDITOR --}}
        // Cattura eventuali modifiche fatte sui campi CKEDITOR
        CKEDITOR.on('instanceCreated', function (e) {
            e.editor.on('change', function (event) {
                formModified = true;
            });
        });
        CKEDITOR.replace('input_notes');

        {{-- Select2 campo "Tipo Canone" --}}
        let $dropdownCanon = $('.input_canon_type');
        $dropdownCanon.select2({
            placeholder: 'Seleziona tipo',
            allowClear: true,
            minimumResultsForSearch: -1
        });

        /**
         * Metodo che viene chiamato quando si seleziona un tipo canone
         * Nasconde o mostra i campi di input per i dati del beneficiario
         */
        $dropdownCanon.on('select2:select', function (e) {
            let selected = e.params.data.id
            if (selected == 2) {
                $('._beneficiary-info').hide();
            } else {
                $('._beneficiary-info').show();
            }
        })

        {{-- Select2 campo "Stato pubblicazione" --}}
        let $dropdownPublishingStatus = $('.select2-publishing_status');
        $dropdownPublishingStatus.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

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
                createValidatorFormSuccessToast(response.data.message, 'Canone di locazione');

                setTimeout(function () {
                    window.location.href = '{{ siteUrl('admin/canon') }}';
                }, 800);
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

        /**
         * Controllo se sono arrivato in questa pagina dal versioning
         */
        {{-- Vedere nel footer --}}
        checkIfRestore();
    });

</script>
{% endblock %}