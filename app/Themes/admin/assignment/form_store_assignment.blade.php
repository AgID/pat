{{-- Form store Incarichi e consulenze --}}
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
                        Aggiunta Incarico
                    @elseif($_storageType === 'update')
                        Modifica Incarico
                    @else
                        Duplicazione Incarico
                    @endif
                </span>
                @if(empty($is_box))
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
                                {{-- Campo Soggetto incaricato(cognome e nome) --}}
                                <div class="form-group col-md-6">
                                    <label for="name">Soggetto incaricato *</label>
                                    {{ form_input([
                                        'name' => 'name',
                                        'value' => !empty($assignment['name']) ? $assignment['name'] : null,
                                        'placeholder' => 'Cognome e Nome',
                                        'id' => 'input_name',
                                        'class' => 'form-control input_name',
                                    ]) }}
                                </div>

                                {{-- Campo Oggetto incarico o consulenza --}}
                                <div class="form-group col-md-6">
                                    <label for="object">Oggetto incarico o consulenza *</label>
                                    {{ form_input([
                                        'name' => 'object',
                                        'value' => !empty($assignment['object']) ? $assignment['object'] : null,
                                        'placeholder' => 'Oggetto incarico o consulenza',
                                        'id' => 'input_object',
                                        'class' => 'form-control input_object',
                                    ]) }}
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Tipo di incarico --}}
                                <div class="form-group col-md-6">
                                    <label for="assignment_type">Tipo di incarico *</label>
                                    <div class="select2-blue" id="input_assignment_type">
                                        {{ form_dropdown(
                                            'assignment_type',
                                            @$typologies,
                                            @$assignment['assignment_type'],
                                            'class="form-control select2-assignment_type" data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>

                                {{-- Campo Tipo consulenza --}}
                                <div class="form-group col-md-6">
                                    <label for="consulting_type">Tipo consulenza</label>
                                    <div class="select2-blue" id="input_consulting_type">
                                        {{ form_dropdown(
                                            'consulting_type',
                                            ['' => '',1=>'Incarichi professionali',2=>'Incarichi giudiziali',3=>'Incarichi difesa legale danni a terzi',4=>'Incarichi giudiziali giuslavoristici'],
                                            @$assignment['consulting_type'],
                                            'class="form-control select2-consulting_type" data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>
                            </div>

                            {{generateSeparator('Informazioni collegate')}}

                            {{-- Campo Struttura organizzativa responsabile --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="object_structures_id">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label for="ajax_structure" id="a_ajax_structure_label">Struttura organizzativa responsabile *</label>
                                    </div>
                                    <div id="ajax_structure"></div>
                                    <input type="hidden" value="" name="object_structures_id"
                                           id="input_object_structures_id"
                                           class="object_structures_id">
                                </div>
                            </div>

                            {{-- Campo Provvedimenti associati --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_measures" id="a_ajax_measures_label">Provvedimenti associati</label>
                                    </div>
                                    <div id="ajax_measures"></div>
                                    <input type="hidden" value="" name="measures" id="input_measure"
                                           class="measures">
                                </div>
                            </div>

                            {{generateSeparator('Importi e date di riferimento')}}

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Data di inizio incarico --}}
                                <div class="form-group col-md-6">
                                    <label for="assignment_start">Data di inizio incarico *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="assignment_start"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control"
                                               value="{{ @$assignment['assignment_start'] }}"
                                               id="assignment_start">
                                    </div>
                                </div>

                                {{-- Campo Data di fine incarico non disponibile --}}
                                <div class="form-group col-md-6">
                                    <label for="end_of_assignment_not_available">Data di fine incarico non
                                        disponibile</label>
                                    <div class="select2-blue" id="input_end_of_assignment_not_available">
                                        {{ form_dropdown(
                                            'end_of_assignment_not_available',
                                            [0=>'No',1=>'Si'],
                                            @$assignment['end_of_assignment_not_available'],
                                            'class="form-control select2-end_of_assignment_not_available" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Data di fine incarico --}}
                                <div class="form-group col-md-6" id="endDate">
                                    <label for="assignment_end">Data di fine incarico *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="assignment_end"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control"
                                               value="{{ @$assignment['assignment_end'] }}"
                                               id="assignment_end">
                                    </div>
                                </div>

                                {{-- Campo Note data di fine incarico non disponibile --}}
                                <div class="form-group col-md-6" id="endTxt">
                                    <label for="end_of_assignment_not_available_txt">Note data di fine incarico non
                                        disponibile *</label>
                                    {{ form_input([
                                        'name' => 'end_of_assignment_not_available_txt',
                                        'value' => !empty($assignment['end_of_assignment_not_available_txt']) ? $assignment['end_of_assignment_not_available_txt'] : null,
                                        'placeholder' => 'Nota',
                                        'id' => 'input_end_of_assignment_not_available_txt',
                                        'class' => 'form-control input_end_of_assignment_not_available_txt'
                                    ]) }}
                                </div>

                                {{-- Campo Compenso --}}
                                <div class="form-group col-md-6">
                                    <label for="compensation">Compenso *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-euro-sign"></i></span>
                                        </div>
                                        {{ form_input([
                                        'name' => 'compensation',
                                        'value' => !empty($assignment['compensation']) ? $assignment['compensation'] : null,
                                        'placeholder' => '',
                                        'id' => 'input_compensation',
                                        'class' => 'form-control input_compensation a-num-class',
                                    ]) }}
                                    </div>
                                </div>
                            </div>

                            {{-- Campo Componenti variabili del compenso --}}
                            <div class="form-group">
                                <label for="variable_compensation">Componenti variabili del compenso</label>
                                {{ form_textarea([
                                    'name' => 'variable_compensation',
                                    'value' => !empty($assignment['variable_compensation']) ? $assignment['variable_compensation'] : null,
                                    'placeholder' => 'Inserire..',
                                    'id' => 'input_variable_compensation',
                                    'class' => 'form-control input_variable_compensation',
                                    'cols' => '10',
                                    'rows' => '4',
                                ]) }}
                            </div>

                            {{generateSeparator('Altre informazioni')}}

                            {{-- Campo Estremi atto di conferimento --}}
                            <div class="form-group">
                                <label for="acts_extremes">Estremi atto di conferimento *</label>
                                {{form_editor([
                                    'name' => 'acts_extremes',
                                    'value' => !empty($assignment['acts_extremes']) ? $assignment['acts_extremes'] : null,
                                    'id' => 'input_acts_extremes',
                                    'class' => 'form-control input_acts_extremes'
                                ]) }}
                            </div>

                            {{-- Campo Ragione dell'incarico --}}
                            <div class="form-group">
                                <label for="assignment_reason">Ragione dell'incarico *</label>
                                {{ form_input([
                                    'name' => 'assignment_reason',
                                    'value' => !empty($assignment['assignment_reason']) ? $assignment['assignment_reason'] : null,
                                    'placeholder' => 'Ragione dell\'incarico',
                                    'id' => 'input_assignment_reason',
                                    'class' => 'form-control input_assignment_reason',
                                ]) }}
                            </div>

                            {{-- Campo Note --}}
                            <div class="form-group">
                                <label for="notes">Note (incarichi, cariche, altre attività)</label>
                                {{form_editor([
                                    'name' => 'notes',
                                    'value' => !empty($assignment['notes']) ? $assignment['notes'] : null,
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

        @if(!empty($assignment['id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'id',
                'value' => $assignment['id'],
                'id' => 'assignment_id',
                'class' => 'assignment_id',
            ]) }}
        @endif

        @if(!empty($measureIds))
            {{ form_input([
                'type' => 'hidden',
                'name' => '_measureIds',
                'value' => implode(',',$measureIds),
                'id' => '_measureIds',
                'class' => '_measureIds',
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

        document.getElementById("endTxt").style.display = "none";

        {{-- Creazione campi di select --}}
        let $dropdownRegulation = $('.select2-assignment_type');
        $dropdownRegulation.select2({
            placeholder: 'Seleziona il tipo....',
            minimumResultsForSearch: -1,
            allowClear: true
        });

        let $dropdownConsultingType = $('.select2-consulting_type');
        $dropdownConsultingType.select2({
            placeholder: 'Seleziona il tipo....',
            minimumResultsForSearch: -1,
            allowClear: true
        });

        let $dropdownFiscalData = $('.select2-end_of_assignment_not_available');
        $dropdownFiscalData.select2({
            minimumResultsForSearch: -1
        });

        {{-- Select2 campo "Stato pubblicazione" --}}
        let $dropdownPublishingStatus = $('.select2-publishing_status');
        $dropdownPublishingStatus.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        // Tabella per la selezione della struttura
        let structure = $('#ajax_structure').patOsAjaxPagination({
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
            label: '#a_ajax_structure_label',
        });

        // Se sono in modifica o in duplicazione setto i valori il presidente della commissione gia selezionato
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($assignment['structure']))
        structure.patOsAjaxPagination.setValue('{{ $assignment['object_structures_id'] }}', '{{ htmlEscape($assignment['structure']['structure_name']).(!empty($assignment['structure']['parent_name'])?' - '.htmlEscape($assignment['structure']['parent_name']):'').(!empty($assignment['structure']['reference_email'])?' - '.$assignment['structure']['reference_email']:'') }}', true);
        @endif

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
            setInputDataValue: '#input_measure',
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
            label: '#a_ajax_measures_label'
        });

        // Setto il bando relativo se sono in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($assignment['measures']))
        @foreach($assignment['measures'] as $measure)
        @php
            $tmpStartDate = !empty($measure['date']) ? ' - '.date('d/m/Y', strtotime($measure['date'])) : null;
        @endphp
        measures.patOsAjaxPagination.setValue('{{ $measure['id'] }}', '{{ htmlEscape($measure['object']).(!empty($measure['number'])?' - '.$measure['number']:'').''.$tmpStartDate }}', true);
        @endforeach
        @endif

        {{-- Begin gestione data fine non disponibile --}}
        $('#input_end_of_assignment_not_available').on('select2:select', function (e) {
            let data = e.params.data;
            if (data.text === 'Si') {
                document.getElementById("endDate").style.display = "none";
                document.getElementById("endTxt").style.display = "block";
            } else if (data.text === 'No') {
                document.getElementById("endTxt").style.display = "none";
                document.getElementById("endDate").style.display = "block";
            }
        });

        @if(empty($assignment['end_of_assignment_not_available']) || $assignment['end_of_assignment_not_available'] === 0)
        document.getElementById("endTxt").style.display = "none";
        @else
        document.getElementById("endDate").style.display = "none";
        document.getElementById("endTxt").style.display = "block";
        @endif
        {{-- End gestione data fine non disponibile --}}

        {{-- Creazione campi CKEditor --}}
        // Cattura eventuali modifiche fatte sui campi CKEDITOR
        CKEDITOR.on('instanceCreated', function (e) {
            e.editor.on('change', function (event) {
                formModified = true;
            });
        });
        CKEDITOR.replace('input_acts_extremes');
        CKEDITOR.replace('input_contraent_procedure_type');
        // Cattura eventuali modifiche fatte sui campi CKEDITOR
        CKEDITOR.on('instanceCreated', function (e) {
            e.editor.on('change', function (event) {
                formModified = true;
            });
        });

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
                createValidatorFormSuccessToast(response.data.message, 'Incarico');

                {{-- Controllo se non sono all'interno di un modale --}}
                @if(empty($is_box))
                setTimeout(function () {
                    window.location.href = '{{ siteUrl('admin/assignment') }}';
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

        @if(empty($is_box))
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