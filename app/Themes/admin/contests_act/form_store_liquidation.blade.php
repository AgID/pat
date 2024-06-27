{{-- Form store Liquidazione --}}
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
                        Aggiunta Liquidazione
                    @elseif($_storageType === 'update')
                        Modifica Liquidazione
                    @else
                        Duplicazione Liquidazione
                    @endif
                </span>
                <div class="card-tools">
                    <ul class="nav nav-pills ml-auto">
                        <li class="nav-item">
                            <a href="{{ siteUrl('admin/contests-act') }}" title="Torna indietro"
                               class="btn btn-default btn-sm btn-outline-primary">
                                <i class="fas fa-caret-left"></i> Torna a elenco Bandi, Gare e Contratti
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

                            {{-- Campo Procedura relativa --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="input_procedure">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label for="ajax_procedure" id="lq_procedure_label">Procedura relativa *</label>
                                    </div>
                                    <div id="ajax_procedure"></div>
                                    <input type="hidden" value="" name="relative_procedure_id"
                                           id="relative_procedure_id"
                                           class="relative_procedure_id">
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Oggetto --}}
                                <div class="form-group col-md-9">
                                    <label for="object" id="obj-label">Oggetto * </label>
                                    {{ form_input([
                                        'name' => 'object',
                                        'value' => !empty($liquidation['object']) ? (($_storageType === 'duplicate') ? 'Copia di ' . $liquidation['object'] : $liquidation['object']) : null,
                                        'placeholder' => 'Oggetto',
                                        'id' => 'input_object',
                                        'class' => 'form-control input_object'
                                    ]) }}
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Valore Importo liquidato (al netto dell'IVA) --}}
                                <div class="form-group col-md-4">
                                    <label for="amount_liquidated" id="amount-liquidated-label">
                                        Valore Importo liquidato <small>(al netto dell'IVA)</small> * </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-euro-sign"></i></span>
                                        </div>
                                        {{ form_input([
                                        'name' => 'amount_liquidated',
                                        'value' => !empty($liquidation['amount_liquidated']) ? $liquidation['amount_liquidated'] : null,
                                        'placeholder' => '',
                                        'id' => 'input_amount_liquidated',
                                        'class' => 'form-control input_amount_liquidated a-num-class ' . (($_storageType == 'update') ? 'readonly' : ''),
                                        (($_storageType == 'update') ? 'readonly' : '') => (($_storageType == 'update') ? 'readonly' : ''),
                                    ]) }}
                                    </div>
                                </div>

                                {{-- Campo ANAC - Anno di riferimento --}}
                                <div class="form-group col-md-4">
                                    <label for="anac_year" id="anac-year-label">ANAC - Anno di riferimento * </label>
                                    <div class="select2-blue" id="input_anac_year">
                                        {{ form_dropdown(
                                            'anac_year',
                                            @$years,
                                            @$liquidation['anac_year'],
                                            'class="select2-anac_year" data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>

                                {{-- Campo Data della liquidazione --}}
                                <div class="form-group col-md-4">
                                    <label for="activation_date">Data della liquidazione *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="activation_date"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control"
                                               value="{{ @$activation_date }}"
                                               id="input_activation_date">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                {{-- Campo Note --}}
                                <label for="details">Note</label>
                                {{form_editor([
                                    'name' => 'details',
                                    'value' => !empty($liquidation['details']) ? $liquidation['details'] : null,
                                    'id' => 'input_details',
                                    'class' => 'form-control input_details'
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

        @if(!empty($liquidation['id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'id',
                'value' => $liquidation['id'],
                'id' => 'liquidation_id',
                'class' => 'liquidation_id',
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
{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
<style type="text/css">
    .ck-editor__editable_inline {
        min-height: 200px;
    }

    .readonly {
        cursor: not-allowed;
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
        if (keyCode === 13 && e.target.tagName!='TEXTAREA') {
            e.preventDefault();
            return false;
        }
    });

    $(document).ready(function () {

        let formModified = false;
        let institutionId = $('#institution_id').val();

        // Per il tooltip di info nel form
        let infoSpan = '<span class="far fa-question-circle fa-xs" data-toggle="tooltip" data-placement="right" ';
        infoSpan += 'data-original-title="Campo pubblicato e comunicato ad ANAC ai fini dell\'art.1 comma 32 Legge n. 190/2012"></span>';
        $('#obj-label').append(infoSpan);
        $('#anac-year-label').append(infoSpan);
        $('#amount-liquidated-label').append(infoSpan);

        // Se si sta creando un nuovo bando, inizializzo la data di pubblicazione con la data odierna
        if ($('#_storage_type').val() == 'insert') {
            $('#input_activation_date').attr('value', "<?= date('Y-m-d') ?>");
        }

        // Tabella per la selezione del bando relativo alla liquidazione
        const procedure = $('#ajax_procedure').patOsAjaxPagination({
            url: config.notice.url,
            textLoad: config.notice.textLoad,
            selectedLabel: 'Procedura relativa selezionata',
            footerTable: config.notice.footerTable,
            classTable: config.notice.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.notice.hideTable,
            showTable: config.notice.showTable,
            search_placeholder: config.notice.search_placeholder,
            setInputDataValue: '#relative_procedure_id',
            dataParams: {
                model: 33,
                institution_id: institutionId
            },
            columns: config.notice.columns,
            action: {
                type: 'radio',
            },
            dataSource: config.notice.dataSource,
            dateFormat: config.notice.dateFormat,
            published: config.notice.published,
            label: '#lq_procedure_label'
        });

        // Setto il bando relativo se sono in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($liquidation['relative_procedure']))
        procedure.patOsAjaxPagination.setValue('{{ $liquidation['relative_procedure']['id'] }}', '{{ $liquidation['relative_procedure']['type'].' - '.htmlEscape($liquidation['relative_procedure']['object']).' - '.$liquidation['relative_procedure']['cig'] }}', true);
        @endif

        {{-- Campo select per anno anac --}}
        let $dropdownAnacYear = $('.select2-anac_year');
        $dropdownAnacYear.select2({
            minimumResultsForSearch: -1,
            placeholder: 'Seleziona',
            allowClear: true
        });

        {{-- Select2 campo "Stato pubblicazione" --}}
        let $dropdownPublishingStatus = $('.select2-publishing_status');
        $dropdownPublishingStatus.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        {{-- Campo CKEDITOR --}}
        // Cattura eventuali modifiche fatte sui campi CKEDITOR
        CKEDITOR.on('instanceCreated', function (e) {
            e.editor.on('change', function (event) {
                formModified = true;
            });
        });
        CKEDITOR.replace('input_details');

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
                createValidatorFormSuccessToast(response.data.message, 'Liquidazione');

                setTimeout(function () {
                    window.location.href = '{{ siteUrl('admin/contests-act') }}';
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
         * Funzione che controlla se sono arrivato in questa pagina dal versioning
         */
        {{-- Vedere nel footer --}}
        checkIfRestore();

    });
</script>
{% endblock %}