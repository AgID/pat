{{-- Form store Sovvenzioni e vantaggi economici --}}
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
                            <a href="{{ siteUrl('admin/assignment') }}" title="Torna indietro"
                               class="btn btn-default btn-sm btn-outline-primary">
                                <i class="fas fa-caret-left"></i> Torna a elenco Incarichi e consulenze
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

                            {{-- Campo Incarico relativo --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label for="ajax_related_assignment" id="ajax_related_assignment_label">Incarico relativo *</label>
                                    </div>
                                    <div id="ajax_related_assignment"></div>
                                    <input type="hidden" value="" name="related_assignment_id"
                                           id="input_related_assignment_id"
                                           class="related_assignment_id">
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Compenso erogato --}}
                                <div class="form-group col-md-4">
                                    <label for="compensation_provided">Compenso erogato *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-euro-sign"></i></span>
                                        </div>
                                        {{ form_input([
                                        'name' => 'compensation_provided',
                                        'value' => !empty($liquidation['compensation_provided']) ? $liquidation['compensation_provided'] : null,
                                        'placeholder' => '',
                                        'id' => 'input_compensation_provided',
                                        'class' => 'form-control input_compensation_provided a-num-class',
                                    ]) }}
                                    </div>
                                </div>

                                {{-- Campo Anno di liquidazione --}}
                                <div class="form-group col-md-4">
                                    <label for="liquidation_year">Anno di liquidazione *</label>
                                    <div class="select2-blue" id="input_liquidation_year">
                                        {{ form_dropdown(
                                            'liquidation_year',
                                            @$liquidationYears,
                                            !empty($liquidation['liquidation_year']) ? $liquidation['liquidation_year'] : null,
                                            'class="form-control select2-liquidation_year" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>

                                {{-- Campo Data di riferimento --}}
                                <div class="form-group col-md-4">
                                    <label for="liquidation_date">Data di riferimento *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="liquidation_date"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control"
                                               value="{{ @$liquidation_date }}"
                                               id="input_liquidation_date">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                {{-- Campo Note --}}
                                <label for="notes">Note</label>
                                {{form_editor([
                                    'name' => 'notes',
                                    'value' => !empty($liquidation['notes']) ? $liquidation['notes'] : null,
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

{{-- Includo il modale per l'aggiunta di oggetti direttamente dal form  --}}
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
        if (keyCode === 13 && e.target.tagName!='TEXTAREA') {
            e.preventDefault();
            return false;
        }
    });

    $(document).ready(function () {

        let formModified = false;
        let institutionId = $('#institution_id').val();

        {{-- Begin campo "Incarico relativo" --}}
        // Tabella per la selezione dell'incarico relativo alla liquidazoine
        let assignment = $('#ajax_related_assignment').patOsAjaxPagination({
            url: config.assignment.url,
            textLoad: config.assignment.textLoad,
            selectedLabel: 'Incarico relativo selezionato',
            footerTable: config.assignment.footerTable,
            classTable: config.assignment.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.assignment.hideTable,
            showTable: config.assignment.showTable,
            search_placeholder: config.assignment.search_placeholder,
            setInputDataValue: '#input_related_assignment_id',
            dataParams: {
                model: 36,
                institution_id: institutionId,
            },
            dateFormat: config.assignment.dateFormat,
            columns: config.assignment.columns,
            action: {
                type: 'radio',
            },
            dataSource: config.assignment.dataSource,
            addRecord: config.assignment.addRecord,
            label: '#ajax_related_assignment_label'
        });

        // Setto il bando relativo se sono in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($liquidation['related_assignment']))
        @php
            $tmpStartDate = !empty($liquidation['related_assignment']['assignment_start']) ? ' - '.date('d/m/Y', strtotime($liquidation['related_assignment']['assignment_start'])) : null;
            $tmpEndDate = !empty($liquidation['related_assignment']['assignment_end']) ? ' - '.date('d/m/Y', strtotime($liquidation['related_assignment']['assignment_end'])) : null;
        @endphp
        assignment.patOsAjaxPagination.setValue('{{ $liquidation['related_assignment']['id'] }}', '{{ htmlEscape($liquidation['related_assignment']['name']).' - '.htmlEscape($liquidation['related_assignment']['object']).''.$tmpStartDate.''.$tmpEndDate }}', true);
        @endif

        {{-- Campo select per anno liquidazione --}}
        let $dropdownPaidDate = $('.select2-liquidation_year');
        $dropdownPaidDate.select2({
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
        CKEDITOR.replace('input_notes');

        /**
         * Metodo per il salvataggio
         */
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
                createValidatorFormSuccessToast(response.data.message, 'Liquidazione');

                setTimeout(function () {
                    window.location.href = '{{ siteUrl('admin/assignment') }}';
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