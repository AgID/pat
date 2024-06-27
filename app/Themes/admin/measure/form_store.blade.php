{{-- Form store Provvedimenti Amministrativi --}}
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
                        Aggiunta Provvedimento Amministrativo
                    @elseif($_storageType === 'update')
                        Modifica Provvedimento Amministrativo
                    @else
                        Duplicazione Provvedimento Amministrativo
                    @endif
                </span>
                @if(empty($is_box))
                    <div class="card-tools">
                        <ul class="nav nav-pills ml-auto">
                            <li class="nav-item">
                                <a href="{{ siteUrl('admin/measure') }}" title="Torna indietro"
                                   class="btn btn-default btn-sm btn-outline-primary">
                                    <i class="fas fa-caret-left"></i> Torna a elenco Provvedimenti amministrativi
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

                                {{-- Campo Oggetto del provvedimento --}}
                                <div class="form-group  col-md-12">
                                    <label for="object">Oggetto del provvedimento *</label>
                                    {{ form_input([
                                        'name' => 'object',
                                        'value' => !empty($measure['object']) ? (($_storageType === 'duplicate') ? 'Copia di ' . $measure['object'] : $measure['object']) : null,
                                        'placeholder' => 'Oggetto del provvedimento',
                                        'id' => 'input_object',
                                        'class' => 'form-control input_object',
                                    ]) }}
                                </div>

                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Numero del provvedimento --}}
                                <div class="form-group col-md-6">
                                    <label for="number">Numero del provvedimento</label>
                                    {{ form_input([
                                        'name' => 'number',
                                        'value' => !empty($measure['number']) ? $measure['number'] : null,
                                        'placeholder' => 'Numero del provvedimento',
                                        'id' => 'input_number',
                                        'class' => 'form-control input_number'
                                    ]) }}
                                </div>

                                {{-- Campo Data del provvedimento --}}
                                <div class="form-group col-md-6">
                                    <label for="date">Data del provvedimento *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="date"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control"
                                               value="{{ @$measure['date'] }}"
                                               id="date">
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
                                            @$typologies,
                                            @$measure['type'],
                                            'class="form-control select2-type" data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>

                            </div>

                            {{generateSeparator('Informazioni collegate')}}

                            {{-- Campo Strutture organizzative responsabili --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="structures">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_structures" id="m_ajax_structures_label">Strutture
                                            organizzative responsabili</label>
                                    </div>
                                    <div id="ajax_structures"></div>
                                    <input type="hidden" value="" name="structures"
                                           id="input_structure"
                                           class="structures">
                                </div>
                            </div>

                            {{-- Campo Responsabili del provvedimento --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_personnel" id="m_ajax_personnel_label">Responsabili del
                                            provvedimento</label>
                                    </div>
                                    <div id="ajax_personnel"></div>
                                    <input type="hidden" value="" name="personnel"
                                           id="input_personnel"
                                           class="personnel">
                                </div>
                            </div>

                            {{-- Campo Procedura relativa (scelta del contraente) --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="procedure">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label for="ajax_procedure" id="m_ajax_procedure_label">Procedura relativa (scelta del contraente)
                                    </label>
                                    </div>
                                    <div id="ajax_procedure"></div>
                                    <input type="hidden" value="" name="object_contests_acts_id"
                                           id="object_contests_acts_id"
                                           class="object_contests_acts_id">
                                </div>
                            </div>

                            {{-- Campo Procedura relativa (dal 01/01/2024) --}}
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

                            {{generateSeparator('Altre informazioni')}}

                            <div class="form-group">
                                {{-- Campo Note (scelta del contraente) --}}
                                <label for="choice_of_contractor">Note (scelta del contraente)</label>
                                {{form_editor([
                                    'name' => 'choice_of_contractor',
                                    'value' => !empty($measure['choice_of_contractor']) ? $measure['choice_of_contractor'] : null,
                                    'id' => 'input_choice_of_contractor',
                                    'class' => 'form-control input_choice_of_contractor'
                                ]) }}
                            </div>

                            <div class="form-group">
                                {{-- Campo Note --}}
                                <label for="notes">Note</label>
                                {{form_editor([
                                    'name' => 'notes',
                                        'value' => !empty($measure['notes']) ? $measure['notes'] : null,
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

        @if(!empty($measure['id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'id',
                'value' => $measure['id'],
                'id' => 'measure_id',
                'class' => 'measure_id',
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
{{ js('admin/get/config.js?box='.$is_box) }}

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

        {{-- BEGIN CAMPI SELECT --}}
        {{-- Campo select Tipologia --}}
        let $dropdownType = $('.select2-type');
        $dropdownType.select2({
            placeholder: 'Seleziona il tipo....',
            minimumResultsForSearch: -1,
            allowClear: true
        });

        // Tabella per la selezione della struttura di appartenenza
        let structures = $('#ajax_structures').patOsAjaxPagination({
            url: config.structure.url,
            textLoad: config.structure.textLoad,
            selectedLabel: 'Strutture selezionate',
            footerTable: config.structure.footerTable,
            classTable: config.structure.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.structure.hideTable,
            showTable: config.structure.showTable,
            search_placeholder: config.structure.search_placeholder,
            setInputDataValue: '#input_structure',
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
            label: '#m_ajax_structures_label'
        });

        // Se sono in modifica o in duplicazione setto i valori il presidente della commissione gia selezionato
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($measure['structures']))
        @foreach($measure['structures'] as $structure)
        structures.patOsAjaxPagination.setValue('{{ $structure['id'] }}', '{{ htmlEscape($structure['structure_name']).(!empty($structure['parent_name'])?' - '.htmlEscape($structure['parent_name']):'').(!empty($structure['reference_email'])?' - '.$structure['reference_email']:'') }}', true);
        @endforeach
        @endif

        // Tabella per la selezione della Procedura relativa(scelta del contraente)
        let relativeProcedure = $('#ajax_procedure').patOsAjaxPagination({
            url: config.notice.url,
            textLoad: config.notice.textLoad,
            selectedLabel: 'Procedura scelta del contraente selezionata',
            footerTable: config.notice.footerTable,
            classTable: config.notice.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.notice.hideTable,
            showTable: config.notice.showTable,
            search_placeholder: config.notice.search_placeholder,
            'setInputDataValue': '#object_contests_acts_id',
            'dataParams': {
                model: 38,
                institution_id: institutionId
            },
            columns: config.notice.columns,
            action: {
                type: 'radio',
            },
            dataSource: config.notice.dataSource,
            dateFormat: config.notice.dateFormat,
            label: '#m_ajax_procedure_label'
        });

        // Setto la procedura relativa se è presente e sono in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($measure['relative_procedure_contraent']))
        @php
            $tmpActivationTime = !empty($measure['relative_procedure_contraent']['activation_date']) ? ' - '.date('d/m/Y', strtotime($measure['relative_procedure_contraent']['activation_date'])) : '';
            $tmpExpirationTime = !empty($measure['relative_procedure_contraent']['expiration_date']) ? ' - '.date('d/m/Y', strtotime($measure['relative_procedure_contraent']['expiration_date'])) : '';
        @endphp
        relativeProcedure.patOsAjaxPagination.setValue('{{ $measure['relative_procedure_contraent']['id'] }}', '{{ $measure['relative_procedure_contraent']['type'].' - '.htmlEscape($measure['relative_procedure_contraent']['object']).' - '.$measure['relative_procedure_contraent']['cig'].$tmpActivationTime.$tmpExpirationTime }}', true);
        @endif

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
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($measure['relative_bdncp_procedure']))
        relativeBdncpProcedure.patOsAjaxPagination.setValue('{{ $measure['relative_bdncp_procedure']['id'] }}', '{{ htmlEscape($measure['relative_bdncp_procedure']['object']).' - '.$measure['relative_bdncp_procedure']['cig'] }}', true);
        @endif

        // Tabella per la selezione dei responsabili del provvedimento
        let responsibles = $('#ajax_personnel').patOsAjaxPagination({
            url: config.personnel.url,
            textLoad: config.personnel.textLoad,
            selectedLabel: 'Responsabili del provvedimento selezionati',
            footerTable: config.personnel.footerTable,
            classTable: config.personnel.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.personnel.hideTable,
            showTable: config.personnel.showTable,
            search_placeholder: config.personnel.search_placeholder,
            setInputDataValue: '#input_personnel',
            dataParams: {
                model: 2,
                institution_id: institutionId
            },
            columns: config.personnel.columns,
            action: {
                type: 'checkbox',
            },
            dataSource: config.personnel.dataSource,
            addRecord: config.personnel.addRecord,
            archived: config.personnel.archived,
            label: '#m_ajax_personnel_label'
        });

        // Se sono in modifica o in duplicazione setto i valori dei membri supplenti gia selezionati
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($measure['personnel']))
        // Setto i membri supplenti gia selezionati
        @foreach($measure['personnel'] as $responsible)
        responsibles.patOsAjaxPagination.setValue('{{ $responsible['id'] }}', '{{ (!empty($responsible['title'])?htmlEscape($responsible['title']).' - ':'').htmlEscape($responsible['full_name']).' - '.htmlEscape($responsible['name']).' - '.(!empty($responsible['email'])?$responsible['email']:'N.D') }}', true);
        @endforeach
        @endif

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
        CKEDITOR.replace('input_choice_of_contractor');
        CKEDITOR.replace('input_notes');

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
                createValidatorFormSuccessToast(response.data.message, 'Provvedimento');

                {{-- Controllo se non sono all'interno di un modale --}}
                @if(empty($is_box))
                setTimeout(function () {
                    window.location.href = '{{ siteUrl('admin/measure') }}';
                }, 800);
                @else
                {{-- Controllo se sono all'interno di un modale lo chiudo dopo il salvataggio --}}
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