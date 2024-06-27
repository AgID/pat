{{-- Form store Bandi Gara e Contratti - Atti delle amministrazioni --}}
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
                        Aggiunta Atto
                    @elseif($_storageType === 'update')
                        Modifica Atto
                    @else
                        Duplicazione Atto
                    @endif
                </span>
                @if(empty($is_box))
                    <div class="card-tools">
                        <ul class="nav nav-pills ml-auto">
                            <li class="nav-item">
                                <a href="{{ siteUrl('admin/notices-act') }}" title="Torna indietro"
                                   class="btn btn-default btn-sm btn-outline-primary">
                                    <i class="fas fa-caret-left"></i> Torna a elenco Atti delle amministrazioni
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
                                {{-- Campo Oggetto --}}
                                <div class="form-group col-md-12" id="input_object">
                                    <label for="object">Oggetto *</label>
                                    {{ form_input([
                                        'name' => 'object',
                                        'value' => !empty($notices_act['object']) ? (($_storageType === 'duplicate') ? 'Copia di ' . $notices_act['object'] : $notices_act['object']) : null,
                                        'placeholder' => 'Oggetto',
                                        'id' => 'input_object',
                                        'class' => 'form-control input_object',
                                    ]) }}
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">

                                {{-- Campo Data --}}
                                <div class="form-group col-md-6">
                                    <label for="date">Data *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="date"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control"
                                               value="{{ @$notices_act['date'] }}"
                                               id="date">
                                    </div>
                                </div>

                            </div>

                            {{-- Campo Pubblica in --}}
                            <div class="form-group">
                                <label for="public_in">Pubblica in *</label>
                                <div class="select2-blue" id="public_in">
                                    {{ form_dropdown(
                                        'public_in[]',
                                        @$publicIn,
                                        @$publicInIDs,
                                        'class="form-control select2-public_in" multiple="multiple" data-placeholder="Seleziona" data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                    ) }}
                                </div>
                            </div>

                            <div id="single_contest">
                                {{-- Campo Procedura relativa --}}
                                <div class="form-row d-flex align-items-end">
                                    <div class="form-group col-md-12" id="procedure">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label for="ajax_procedure_single" id="na_procedure_label">Procedura relativa *</label>
                                        </div>
                                        <div id="ajax_procedure_single"></div>
                                        <input type="hidden" value="" name="object_contests_acts_id_single"
                                               id="object_contests_acts_id_single"
                                               class="object_contests_acts_id_single">
                                    </div>
                                </div>
                            </div>

                            <div id="multiple_contest">
                                {{-- Campo Procedure relative --}}
                                <div class="form-row d-flex align-items-end">
                                    <div class="form-group col-md-12" id="procedures">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label for="ajax_procedures" id="na_procedures_label">Procedure relative *</label>
                                        </div>
                                        <div id="ajax_procedures"></div>
                                        <input type="hidden" value="" name="object_contests_acts_ids"
                                               id="object_contests_acts_ids"
                                               class="object_contests_acts_ids">
                                    </div>
                                </div>
                            </div>

                            {{-- Campo Commissione giudicatrice - seleziona da archivio incarichi e consulenze --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12"
                                     id="assignments_container" {{(!empty($publicInIDs) && (in_array(115, $publicInIDs) || in_array(530, $publicInIDs))) ? '' : ' style="display:none;"'}}>
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_assignments" id="na_assignments_label">Commissione giudicatrice
                                            - seleziona da archivio
                                            incarichi e
                                            consulenze</label>
                                    </div>
                                    <div id="ajax_assignments"></div>
                                    <input type="hidden" value="" name="assignments" id="input_commission"
                                           class="assignments">
                                </div>
                            </div>

                            <div id="public-investment-projects-container" {{(!empty($publicInIDs) && in_array(531, $publicInIDs)) ? '' : ' style="display:none;"'}}>
                                <div class="form-row d-flex align-items-end">
                                    {{-- Campo CUP --}}
                                    <div class="form-group col-md-6" id="input_cup">
                                        <label for="cup">CUP</label>
                                        {{ form_input([
                                            'name' => 'cup',
                                            'value' => !empty($notices_act['cup']) ? $notices_act['cup'] : null,
                                            'placeholder' => 'CUP',
                                            'id' => 'input_cup',
                                            'class' => 'form-control input_cup',
                                        ]) }}
                                    </div>

                                    {{-- Campo Importo totale del finanziamento --}}
                                    <div class="form-group col-md-6" id="input_total_fin_amount">
                                        <label for="total_fin_amount">Importo totale del finanziamento</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-euro-sign"></i></span>
                                            </div>
                                            {{ form_input([
                                            'name' => 'total_fin_amount',
                                            'value' => !empty($notices_act['total_fin_amount']) ? $notices_act['total_fin_amount'] : null,
                                            'placeholder' => '',
                                            'id' => 'input_total_fin_amount',
                                            'class' => 'form-control input_total_fin_amount a-num-class',
                                        ]) }}
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row d-flex align-items-end">
                                    {{-- Campo Fonti finanziarie --}}
                                    <div class="form-group col-md-6" id="input_financial_sources">
                                        <label for="financial_sources">Fonti finanziarie</label>
                                        {{ form_input([
                                            'name' => 'financial_sources',
                                            'value' => !empty($notices_act['financial_sources']) ? $notices_act['financial_sources'] : null,
                                            'placeholder' => 'Fonti finanziarie...',
                                            'id' => 'input_financial_sources',
                                            'class' => 'form-control input_financial_sources',
                                        ]) }}
                                    </div>

                                    {{-- Campo Stato di attuazione finanziario e procedurale --}}
                                    <div class="form-group col-md-6" id="input_implementation_state">
                                        <label for="implementation_state">Stato di attuazione finanziario e
                                            procedurale</label>
                                        {{ form_input([
                                            'name' => 'implementation_state',
                                            'value' => !empty($notices_act['implementation_state']) ? $notices_act['implementation_state'] : null,
                                            'placeholder' => 'Stato di attuazione finanziario e procedurale....',
                                            'id' => 'input_implementation_state',
                                            'class' => 'form-control input_implementation_state',
                                        ]) }}
                                    </div>
                                </div>

                                <div class="form-row d-flex align-items-end">
                                    {{-- Campo Data avvio progetti --}}
                                    <div class="form-group col-md-6">
                                        <label for="projects_start_date">Data avvio progetti</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i
                                                            class="fas fa-calendar-alt"></i></span>
                                            </div>
                                            <input type="date" name="projects_start_date"
                                                   placeholder="GG/MM/AAAA"
                                                   autocomplete="off" class="form-control"
                                                   value="{{ @$notices_act['projects_start_date'] }}"
                                                   id="input_projects_start_date">
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Campo Note --}}
                            <div class="form-group">
                                <label for="details">Note</label>
                                {{form_editor([
                                    'name' => 'details',
                                    'value' => !empty($notices_act['details']) ? $notices_act['details'] : null,
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

        @if(!empty($notices_act['id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'id',
                'value' => $notices_act['id'],
                'id' => 'notices_act_id',
                'class' => 'notices_act_id',
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


        {{ form_input([
            'type' => 'hidden',
            'name' => 'object_contests_acts_id',
            'value' => '',
            'id' => 'object_contests_acts_id',
            'class' => 'object_contests_acts_id',
        ]) }}

        {{ form_hidden('institute_id',checkAlternativeInstitutionId()) }}
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

        let formModified = false;
        let institutionId = $('#institution_id').val();

        // Tabella per la selezione della Procedura relativa all'affidamento
        let relativeProcedureSingle = $('#ajax_procedure_single').patOsAjaxPagination({
            url: config.notice.url,
            textLoad: config.notice.textLoad,
            selectedLabel: 'Procedura relativa selezionata',
            footerTable: config.notice.footerTable,
            classTable: config.notice.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.notice.hideTable,
            showTable: config.notice.showTable,
            search_placeholder: config.notice.search_placeholder,
            setInputDataValue: '#object_contests_acts_id_single',
            dataParams: {
                model: 30,
                institution_id: institutionId
            },
            columns: config.notice.columns,
            action: {
                type: 'radio',
            },
            dataSource: config.notice.dataSource,
            dateFormat: config.notice.dateFormat,
            label: '#na_procedure_label'
        });

        @if(!empty($publicInIDs) && !(in_array(531, $publicInIDs)))
            @if(in_array($_storageType,['update', 'duplicate']) && !empty($notices_act['relative_contest_act']))
                @php
                $tmpActivationTime = !empty($notices_act['relative_contest_act'][0]['activation_date']) ? ' - '.date('d/m/Y', strtotime($notices_act['relative_contest_act'][0]['activation_date'])) : null;
                $tmpExpirationTime = !empty($notices_act['relative_contest_act'][0]['expiration_date']) ? ' - '.date('d/m/Y', strtotime($notices_act['relative_contest_act'][0]['expiration_date'])) : null;
                @endphp
                relativeProcedureSingle.patOsAjaxPagination.setValue('{{ $notices_act['relative_contest_act'][0]['id'] }}', '{{ $notices_act['relative_contest_act'][0]['type'].' - '.htmlEscape($notices_act['relative_contest_act'][0]['object']).' - '.$notices_act['relative_contest_act'][0]['cig'].$tmpActivationTime.$tmpExpirationTime }}', true);
            @endif
        @endif

        //Tabella per la selezione delle Procedure relative all'affidamento
        let relativeProcedures = $('#ajax_procedures').patOsAjaxPagination({
            url: config.notice.url,
            textLoad: config.notice.textLoad,
            selectedLabel: 'Procedure relative selezionate',
            footerTable: config.notice.footerTable,
            classTable: config.notice.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.notice.hideTable,
            showTable: config.notice.showTable,
            search_placeholder: config.notice.search_placeholder,
            setInputDataValue: '#object_contests_acts_ids',
            dataParams: {
                model: 30,
                institution_id: institutionId
            },
            columns: config.notice.columns,
            action: {
                type: 'checkbox',
            },
            dataSource: config.notice.dataSource,
            dateFormat: config.notice.dateFormat,
            label: '#na_procedures_label'
        });

        // Setto la procedura relativa se è presente e sono in modifica o duplicazione
        @if(!empty($publicInIDs) && (in_array(531, $publicInIDs)))
            @if(in_array($_storageType,['update', 'duplicate']) && !empty($notices_act['relative_contest_act']))
                @foreach($notices_act['relative_contest_act'] as $proceeding)
                    @php
                    $tmpActivationTime = !empty($proceeding['activation_date']) ? ' - '.date('d/m/Y', strtotime($proceeding['activation_date'])) : null;
                    $tmpExpirationTime = !empty($proceeding['expiration_date']) ? ' - '.date('d/m/Y', strtotime($proceeding['expiration_date'])) : null;
                    @endphp
                    relativeProcedures.patOsAjaxPagination.setValue({{ $proceeding['id'] }}, '{{ $proceeding['type'].' - '.htmlEscape($proceeding['object']).' - '.$proceeding['cig'].''.$tmpActivationTime.''.$tmpExpirationTime }}', true);
                @endforeach
            @endif
        @endif

        {{-- Begin creazione campi select --}}
        {{-- Select2 per campo "Pubblica In" --}}
        let $dropdownPublicIn = $('.select2-public_in');
        $dropdownPublicIn.select2();
        $dropdownPublicIn.on('change', function () {
            let selected = $(this).val();
            showFieldFromPublicIn(selected);
        });

        showFieldFromPublicIn($dropdownPublicIn.val());

        //singolo
        relativeProcedureSingle.on('change', function () {
            $('#object_contests_acts_id').val('');
            $('#object_contests_acts_id').val($('#object_contests_acts_id_single').val());
        });

        //multiplo
        relativeProcedures.on('change', function () {
            $('#object_contests_acts_id').val('');
            $('#object_contests_acts_id').val($('#object_contests_acts_ids').val());
        });



        // Tabella per la selezione degli Incarichi che formano la "Commissione giudicatrice - seleziona da archivio incarichi e consulenze"
        let assignments = $('#ajax_assignments').patOsAjaxPagination({
            url: config.assignment.url,
            textLoad: config.assignment.textLoad,
            selectedLabel: 'Incarichi selezionati',
            footerTable: config.assignment.footerTable,
            classTable: config.assignment.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.assignment.hideTable,
            showTable: config.assignment.showTable,
            search_placeholder: config.assignment.search_placeholder,
            setInputDataValue: '#input_commission',
            dataParams: {
                model: 36,
                institution_id: institutionId,
            },
            dateFormat: config.assignment.dateFormat,
            columns: config.assignment.columns,
            action: {
                type: 'checkbox',
            },
            dataSource: config.assignment.dataSource,
            addRecord: config.assignment.addRecord,
            label: '#na_assignments_label'
        });

        // Setto il bando relativo se sono in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($notices_act['assignments']))
        @foreach($notices_act['assignments'] as $assignment)
        @php
            $tmpStartDate = !empty($assignment['assignment_start']) ? ' - '.date('d/m/Y', strtotime($assignment['assignment_start'])) : null;
        @endphp
        assignments.patOsAjaxPagination.setValue('{{ $assignment['id'] }}', '{{ htmlEscape($assignment['name']).' - '.htmlEscape($assignment['object']).''.$tmpStartDate }}', true);
        @endforeach
        @endif

        {{-- Select2 campo "Stato pubblicazione" --}}
        let $dropdownPublishingStatus = $('.select2-publishing_status');
        $dropdownPublishingStatus.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });
        {{-- End creazione campi select --}}


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
                createValidatorFormSuccessToast(response.data.message, 'Atto');

                {{-- Controllo se non sono all'interno di un modale --}}
                @if(empty($is_box))
                setTimeout(function () {
                    window.location.href = '{{ siteUrl('admin/notices-act') }}';
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
                createValidatorFormErrorToast(msg, 6000, '', false);

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
         * Funzione che controlla se sono arrivato in questa pagina dal versioning
         */
        {{-- Vedere nel footer --}}
        checkIfRestore();

    });


    function showFieldFromPublicIn(selected){

        if (['115', '530'].some(e => selected.includes(e))) {
            $('#assignments_container').show();
        } else {
            $('#assignments_container').hide();
        }

        //progetti inv pubblico
        if (selected.includes('531')) {
            $('#public-investment-projects-container').show();
            $('#multiple_contest').show();
            $('#single_contest').hide();
            $('#object_contests_acts_id').val('');
            $('#object_contests_acts_id').val($('#object_contests_acts_ids').val());
        } else {
            $('#public-investment-projects-container').hide();
            $('#multiple_contest').hide();
            $('#single_contest').show();
            $('#object_contests_acts_id').val('');
            $('#object_contests_acts_id').val($('#object_contests_acts_id_single').val());
        }

        //resoconti gestione finanziaria
        if (selected.includes('117') && selected.length === 1) {
            $('#na_procedure_label').text('Procedura relativa')
        } else {
            $('#na_procedure_label').text('Procedura relativa *')
        }


    }

    function loadAssignments() {

    }
</script>
{% endblock %}