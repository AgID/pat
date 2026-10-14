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
                            <a href="{{ siteUrl('admin/grant') }}" title="Torna indietro"
                               class="btn btn-default btn-sm btn-outline-primary">
                                <i class="fas fa-caret-left"></i> Torna a elenco Sovvenzioni e vantaggi
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
                            </div>
                            <hr class="patos-hr">


                            <div class="form-row d-flex align-items-end">
                                {{-- Inclusione di librerie esterne --}}

                            </div>

                            {{-- Campo Procedura relativa --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_grant_id" id="ajax_grant_id_label">Procedura relativa *</label>
                                    </div>
                                    <div id="ajax_grant_id"></div>
                                    <input type="hidden" value="" name="grant_id"
                                           id="input_grant_id"
                                           class="grant_id">
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Importo del vantaggio economico corrisposto --}}
                                <div class="form-group col-md-4">
                                    <label for="compensation_paid">Importo del vantaggio economico corrisposto *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-euro-sign"></i></span>
                                        </div>
                                        {{ form_input([
                                        'name' => 'compensation_paid',
                                        'value' => !empty($liquidation['compensation_paid']) ? $liquidation['compensation_paid'] : null,
                                        'placeholder' => '',
                                        'id' => 'input_compensation_paid',
                                        'class' => 'form-control input_compensation_paid a-num-class',
                                    ]) }}
                                    </div>
                                </div>

                                {{-- Campo Anno di liquidazione --}}
                                <div class="form-group col-md-4">
                                    <label for="compensation_paid_date">Anno di liquidazione *</label>
                                    <div id="input_compensation_paid_date">
                                            {{ form_dropdown(
                                                'compensation_paid_date',
                                                @$liquidationYears,
                                                !empty($liquidation['compensation_paid_date']) ? $liquidation['compensation_paid_date'] : null,
                                                'class="ts-compensation_paid_date" style="width: 100%;"'
                                            ) }}
                                    </div>
                                </div>

                                {{-- Campo Data di riferimento --}}
                                <div class="form-group col-md-4">
                                    <label for="reference_date">Data di riferimento *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="reference_date"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control"
                                               value="{{ @$reference_date }}"
                                               id="input_reference_date">
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

                        {{-- Inclusione di librerie esterne --}}

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

        @if(!empty($liquidation['grant_id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => '_grant_id',
                'value' => $liquidation['grant_id'],
                'id' => '_grant_id',
                'class' => '_grant_id',
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
</style>
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
{{ js('ckeditor4/ckeditor.js', 'common') }}
{{ js('patos/jquery.patOsAjaxPagination.js', 'common') }}

{{ js(verifyFirstOrLastSlash('admin/get/config.js')) }}

<script type="text/javascript">

    //Previene il salvataggio quando si preme invio e il focus non è sul pulsante di salvataggio
    document.getElementById('{{ $formSettings['id'] }}').addEventListener('keydown', e => {
        if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
            e.preventDefault();
        }
    });

    $(document).ready(function () {

        let formModified = false;
       let institutionId = document.getElementById('institution_id').value ?? '';

        {{-- BEGIN CAMPI SELECT --}}
        {{-- Begin campo "Procedura relativa" --}}

        // Tabella per la selezione della sovvenzione relativa alla liquidazione
        let grant = $('#ajax_grant_id').patOsAjaxPagination({
            url: config.grant.url,
            textLoad: config.grant.textLoad,
            selectedLabel: 'Procedura relativa selezionata',
            footerTable: config.grant.footerTable,
            classTable: config.grant.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.grant.hideTable,
            showTable: config.grant.showTable,
            search_placeholder: config.grant.search_placeholder,
            setInputDataValue: '#input_grant_id',
            dataParams: {
                model: 52,
                institution_id: institutionId,
            },
            dateFormat: config.grant.dateFormat,
            columns: config.grant.columns,
            action: {
                type: 'radio',
            },
            dataSource: config.grant.dataSource,
            label: '#ajax_grant_id_label'
        });

        // Setto il bando relativo se sono in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($liquidation['grant_id']))
        @php
            $tmpConcessionDate = !empty($liquidation['relative_grant']['concession_act_date']) ? ' - '.date('d/m/Y', strtotime($liquidation['relative_grant']['concession_act_date'])) : null;
        @endphp
        grant.patOsAjaxPagination.setValue('{{ $liquidation['relative_grant']['id'] }}', '{{ htmlEscape($liquidation['relative_grant']['beneficiary_name']).' - '.htmlEscape($liquidation['relative_grant']['object']).''.$tmpConcessionDate.''.htmlEscape($liquidation['relative_grant']['concession_amount']) }}', true);
        @endif

        new TomSelect('.ts-compensation_paid_date', TS_SINGLE_CLEAR);

        // Cattura eventuali modifiche fatte sui campi CKEDITOR
        CKEDITOR.on('instanceCreated', function (e) {
            e.editor.on('change', function (event) {
                formModified = true;
            });
        });
        CKEDITOR.replace('input_notes',{
            customConfig: '{{ verifyFirstOrLastSlash('assets/common/ckeditor4/config.js')}}',
        });

        /**
         * Metodo per il salvataggio
         */
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
                    window.location.href = '{{ siteUrl('admin/grant') }}';
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
        document.addEventListener("focusin", function (event) {
            if (event.target.matches(".ts-control, input")) {
                formModified = true;
            }
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
