{{-- Form store Avviso --}}
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
                        Aggiunta Avviso
                    @elseif($_storageType === 'update')
                        Modifica Avviso
                    @else
                        Duplicazione Avviso
                    @endif
                </span>
                @if(empty($is_box))
                    <div class="card-tools">
                        <ul class="nav nav-pills ml-auto">
                            <li class="nav-item">
                                <a href="{{ siteUrl('admin/bdncp-procedure') }}" title="Torna indietro"
                                   class="btn btn-default btn-sm btn-outline-primary">
                                    <i class="fas fa-caret-left"></i> Torna a elenco procedure
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
                            </div>
                            <hr class="patos-hr">


                            <div class="form-row d-flex align-items-end">
                                {{-- Inclusione di librerie esterne --}}

                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Oggetto --}}
                                <div class="form-group col-md-12">
                                    <label for="object">Oggetto *</label>
                                    {{ form_input([
                                        'name' => 'object',
                                        'value' => !empty($alert['object']) ? (($_storageType === 'duplicate') ? 'Copia di ' . $alert['object'] : $alert['object']) : null,
                                        'placeholder' => 'Oggetto',
                                        'id' => 'input_object',
                                        'class' => 'form-control input_object'
                                    ]) }}
                                </div>
                            </div>


                            {{-- Campo Bando di gara relativo --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="bdncp_procedure">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_bdncp_procedure" id="m_ajax_bdncp_procedure_label">Procedura relativa (dal 01/01/2024)
                                        </label>
                                    </div>
                                    <div id="ajax_bdncp_procedure"></div>
                                    <input type="hidden" value="" name="object_bdncp_procedure_id"
                                           id="object_bdncp_procedure_id"
                                           class="object_bdncp_procedure_id">
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Data dell'atto --}}
                                <div class="form-group col-md-4">
                                    <label for="alert_date">Data dell'avviso
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="alert_date"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control"
                                               value="{{ @$alert['alert_date'] }}"
                                               id="input_alert_date">
                                    </div>
                                </div>


                            </div>


                            <div class="form-group">
                                {{-- Campo Note --}}
                                <label for="notes">Note</label>
                                {{form_editor([
                                    'name' => 'notes',
                                    'value' => !empty($alert['notes']) ? $alert['notes'] : null,
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

                        <div class="col-md-12 mb-3">
                            <div class="custom-separator">
                                <h5>
                                    <svg xmlns="http://www.w3.org/2000/svg" viewBox="0 0 512 512" width="16" height="16" fill="currentColor">
                                        <path d="M496 384H64V80c0-8.8-7.2-16-16-16H16C7.2 64 0 71.2 0 80v336c0 26.5 21.5 48 48 48h448c8.8 0 16-7.2 16-16v-32c0-8.8-7.2-16-16-16zM464 96H345.9c-21.4 0-32.1 25.9-17 41l32.4 32.4L288 242.7l-73.4-73.4c-12.5-12.5-32.8-12.5-45.3 0l-68.7 68.7c-6.2 6.2-6.2 16.4 0 22.6l22.6 22.6c6.2 6.2 16.4 6.2 22.6 0L192 237.3l73.4 73.4c12.5 12.5 32.8 12.5 45.3 0l96-96 32.4 32.4c15.1 15.1 41 4.4 41-17V112c0-8.8-7.2-16-16-16z"/>
                                    </svg>
                                    Dati SEO
                                </h5>
                            </div>
                        </div>

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

        {{ form_input([
           'type' => 'hidden',
           'name' => '_typology',
           'value' => 'alert',
           'id' => '_typology',
           'class' => '_typology',
       ]) }}

        @if(!empty($alert['id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'id',
                'value' => $alert['id'],
                'id' => 'alert_id',
                'class' => 'alert_id',
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

        // Tabella per la selezione della Procedura relativa(scelta del contraente)
        let relativeBdncpProcedure = $('#ajax_bdncp_procedure').patOsAjaxPagination({
            url: config.bdncp_notice.url,
            textLoad: config.bdncp_notice.textLoad,
            selectedLabel: 'Procedura (dal 01/01/2024) selezionata',
            footerTable: config.bdncp_notice.footerTable,
            classTable: config.bdncp_notice.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.bdncp_notice.hideTable,
            showTable: config.bdncp_notice.showTable,
            search_placeholder: config.bdncp_notice.search_placeholder,
            'setInputDataValue': '#object_bdncp_procedure_id',
            'dataParams': {
                model: 47,
                institution_id: institutionId
            },
            columns: config.bdncp_notice.columns,
            action: {
                type: 'radio',
            },
            dataSource: config.bdncp_notice.dataSource,
            dateFormat: config.bdncp_notice.dateFormat,
            label: '#m_ajax_bdncp_procedure_label'
        });

        // Setto la procedura relativa se è presente e sono in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($alert['relative_bdncp_procedure']))
        relativeBdncpProcedure.patOsAjaxPagination.setValue('{{ $alert['relative_bdncp_procedure']['id'] }}', '{{ htmlEscape($alert['relative_bdncp_procedure']['object']).' - '.$alert['relative_bdncp_procedure']['cig'] }}', true);
        @endif

        // Se si sta creando un nuovo bando, inizializzo la data di pubblicazione con la data odierna
        if ($('#_storage_type').val() === 'insert' && !$('#input_alert_date').val()) {
            $('#input_alert_date').attr('value', "<?php echo date('Y-m-d') ?>");
        }

        {{-- Campo CKEDITOR --}}
        // Cattura eventuali modifiche fatte sui campi CKEDITOR
        CKEDITOR.on('instanceCreated', function (e) {
            e.editor.on('change', function (event) {
                formModified = true;
            });
        });
        CKEDITOR.replace('input_notes', {
            customConfig: '{{ verifyFirstOrLastSlash('assets/common/ckeditor4/config.js')}}',
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
                createValidatorFormSuccessToast(response.data.message, 'Avviso');

                {{-- Controllo se non sono all'interno di un modale --}}
                @if(empty($is_box))
                setTimeout(function () {
                    window.location.href = '{{ siteUrl('admin/bdncp-procedure') }}';
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
                let response = {};
                try {
                    response = parseJson(jqXHR.responseText) || {};
                } catch (error) {
                    // Una risposta non JSON non deve impedire di riprovare il salvataggio.
                }

                //Messaggi di errore del validatore campi e degli allegati
                let msg = [
                    response.data && response.data.error_partial_attach,
                    response.errors && response.errors.error
                ].filter(Boolean).join(", ");
                if (!msg) {
                    msg = 'Impossibile completare il salvataggio. Riprova.';
                }

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
