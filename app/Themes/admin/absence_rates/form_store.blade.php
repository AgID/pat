{{--  Form store Tassi di assenza --}}
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
                        Aggiunta Tasso di assenza
                    @elseif($_storageType === 'update')
                        Modifica Tasso di assenza
                    @else
                        Duplicazione Tasso di assenza
                    @endif
                </span>
                <div class="card-tools">
                    <ul class="nav nav-pills ml-auto">
                        <li class="nav-item">
                            <a href="{{ siteUrl('admin/absence-rates') }}" title="Torna indietro"
                               class="btn btn-default btn-sm btn-outline-primary">
                                <i class="fas fa-caret-left"></i> Torna a elenco tassi di assenza
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

                            {{-- Campo Struttura --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="structure">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label for="ajax_structure" id="abs_structure_label">Struttura *</label>
                                    </div>
                                    <div id="ajax_structure"></div>
                                    <input type="hidden" value="" name="object_structures_id"
                                           id="input_object_structures_id"
                                           class="object_structures_id">
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Periodo --}}
                                <div class="form-group col-md-6" id="input_month">
                                    <label for="months">Periodo *</label>
                                    <div class="select2-blue">
                                        {{ form_dropdown(
                                            'months[]',
                                            @config('absenceRatesPeriod', null, 'app'),
                                            @$period,
                                            'class="form-control select2-month" multiple="multiple" data-placeholder="Seleziona periodo" data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>

                                {{-- Campo Anno --}}
                                <div class="form-group col-md-6" id="input_year">
                                    <label for="year">Anno *</label>
                                    {{ form_input([
                                        'name' => 'year',
                                        'value' => !empty($absenceRates['year']) ? $absenceRates['year'] : null,
                                        'placeholder' => 'Anno',
                                        'id' => 'input_year',
                                        'class' => 'form-control input_year'
                                    ]) }}
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Percentuale di presenze --}}
                                <div class="form-group col-md-6" id="input_presence_percentage">
                                    <label for="presence_percentage">Percentuale di presenze *</label>
                                    <div class="input-group">
                                        {{ form_input([
                                            'name' => 'presence_percentage',
                                            'value' => !empty($absenceRates['presence_percentage']) ? $absenceRates['presence_percentage'] : null,
                                            'placeholder' => '',
                                            'type' => 'number',
                                            'step'=> '.01',
                                            'max' => '100',
                                            'min' => '0',
                                            'id' => 'input_presence_percentage2',
                                            'class' => 'form-control input_presence_percentage',
                                        ]) }}
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-percentage"></i></span>
                                        </div>
                                    </div>
                                </div>

                                {{-- Campo Percentuale di assenze totali --}}
                                <div class="form-group col-md-6" id="input_total_absence">
                                    <label for="total_absence">Percentuale di assenze totali *</label>
                                    <div class="input-group">
                                        {{ form_input([
                                            'name' => 'total_absence',
                                            'value' => !empty($absenceRates['total_absence']) ? $absenceRates['total_absence'] : null,
                                            'placeholder' => '',
                                            'type' => 'number',
                                            'step'=> '.01',
                                            'max' => '100',
                                            'min' => '0',
                                            'id' => 'input_total_absence2',
                                            'class' => 'form-control input_total_absence',
                                        ]) }}
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-percentage"></i></span>
                                        </div>
                                    </div>
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

        @if(!empty($absenceRates['id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'id',
                'value' => $absenceRates['id'],
                'id' => 'absence_rates_id',
                'class' => 'absence_rates_id',
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

{{-- Includo il modale per l'aggiunta di oggetti direttamente dal form  --}}
{% include layout/partials/form_modal %}
{% endblock %}

{{--  ************************************************ CSS ************************************************ --}}

{% block css %}
{{ css('datatables-bs4/css/dataTables.bootstrap4.min.css','common') }}
{{ css('datatables-responsive/css/responsive.bootstrap4.min.css','common') }}
{{ css('datatables-buttons/css/buttons.bootstrap4.min.css','common') }}
{% endblock %}

{{--  ************************************************ JAVASCRIPT ************************************************ --}}

{% block javascript %}
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

        {{-- Vedere nel footer --}}
        /**
         * Funzione che controlla se sono arrivato in questa pagina dal versioning
         */
        checkIfRestore();

        let formModified = false;

        let institutionId = $('#institution_id').val();

        // Tabella per la selezione della struttura
        let structure = $('#ajax_structure').patOsAjaxPagination({
            url: config.structure.url,
            textLoad: config.structure.textLoad,
            selectedLabel: 'Struttura selezionata',
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
            label: '#abs_structure_label'
        });

        // Se sono in modifica o in duplicazione setto i valori il presidente della commissione gia selezionato
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($absenceRates['structure']))
        structure.patOsAjaxPagination.setValue('{{ $absenceRates['object_structures_id'] }}', '{{ htmlEscape($absenceRates['structure']['structure_name']).(!empty($absenceRates['structure']['parent_name'])?' - '.htmlEscape($absenceRates['structure']['parent_name']):'').(!empty($absenceRates['structure']['reference_email'])?' - '.$absenceRates['structure']['reference_email']:'N.D') }}', true);
        @endif

        {{-- Select2 per campo "Periodo" --}}
        let $dropdownMonth = $('.select2-month');
        $dropdownMonth.select2()
        $dropdownMonth.on('change', function () {
            $('#month').val($(this).val());
        });

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
                createValidatorFormSuccessToast(response.data.message, 'Tasso di Assenza');

                setTimeout(function () {
                    window.location.href = '{{ siteUrl('admin/absence-rates') }}';
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
         * Funzione che cambia il valore del campo percentuale di presenze
         */
        $("#input_total_absence2").on('change keyup',
            function (event) {
                let assenze = document.getElementById("input_total_absence2").value;
                if (assenze != '' && !isNaN(assenze)) {
                    document.getElementById("input_presence_percentage2").value = 100 - assenze;
                } else {
                    document.getElementById("input_presence_percentage2").value = '';
                }
            }
        )

        /**
         * Funzione che cambia il valore del campo percentuale di assenze totali
         */
        $("#input_presence_percentage2").on('change keyup',
            function (event) {
                let presenze = document.getElementById("input_presence_percentage2").value;
                if (presenze != '' && !isNaN(presenze)) {
                    document.getElementById("input_total_absence2").value = 100 - presenze;
                } else {
                    document.getElementById("input_total_absence2").value = '';
                }
            }
        )
    });
</script>
{% endblock %}