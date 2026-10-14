{{-- Form store Bandi di Concorso --}}
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
                        Aggiunta Bando di concorso
                    @elseif($_storageType === 'update')
                        Modifica Bando di concorso
                    @else
                        Duplicazione Bando di concorso
                    @endif
                </span>
                @if(empty($is_box))
                    <div class="card-tools">
                        <ul class="nav nav-pills ml-auto">
                            <li class="nav-item">
                                <a href="{{ siteUrl('admin/contest') }}" title="Torna indietro"
                                   class="btn btn-default btn-sm btn-outline-primary">
                                    <i class="fas fa-caret-left"></i> Torna a elenco Bandi di Concorso
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

                                <div class="form-group col-md-3">
                                    <label for="activation_date">Data di pubblicazione *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="datetime-local" name="activation_date"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control"
                                               value="{{ @$contest['activation_date'] }}"
                                               id="activation_date">
                                    </div>
                                </div>

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
                                        'value' => !empty($contest['object']) ? (($_storageType === 'duplicate') ? 'Copia di ' . $contest['object'] : $contest['object']) : null,
                                        'placeholder' => 'Oggetto',
                                        'id' => 'input_object',
                                        'class' => 'form-control input_object',
                                    ]) }}
                                </div>
                            </div>



                            @php $s_c_t = $s_c_t ?? null; @endphp

                            {{generateSeparator('Informazioni collegate')}}

                            {{-- Campo Concorso o Avviso relativo --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_contest" id="ajax_contest_label">Concorso o Avviso
                                            relativo</label>
                                    </div>
                                    <div id="ajax_contest"></div>
                                    <input type="hidden" value="" name="related_contest_id" id="input_related_contest"
                                           class="related_contest_id">
                                </div>
                            </div>

                            {{-- Campo Ufficio di riferimento --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="object_structures_id">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_structure" id="c_ajax_structure_label">Ufficio di
                                            riferimento </label>
                                    </div>
                                    <div id="ajax_structure"></div>
                                    <input type="hidden" value="" name="object_structures_id"
                                           id="input_object_structures_id"
                                           class="object_structures_id">
                                </div>
                            </div>

                            {{generateSeparator('Sede di prova')}}

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Sede Prova - Provincia --}}
                                <div class="form-group col-md-4">
                                    <label for="province_office">Sede Prova - Provincia</label>
                                    <div id="input_province_office">
                                        {{ form_dropdown(
                                            'province_office',
                                            $provinceShort,
                                            !empty($contest['province_office']) ? $contest['province_office'] : null,
                                            'class="ts-province_office" style="width: 100%; height: unset;"'
                                        ) }}
                                    </div>
                                </div>

                                {{-- Campo Sede Prova - Comune --}}
                                <div class="form-group col-md-4">
                                    <label for="city_office">Sede Prova - Comune</label>
                                    {{ form_input([
                                        'name' => 'city_office',
                                        'value' => !empty($contest['city_office']) ? $contest['city_office'] : null,
                                        'placeholder' => 'Sede Comune',
                                        'id' => 'input_city_office',
                                        'class' => 'form-control input_city_office'
                                    ]) }}
                                </div>

                                {{-- Campo Sede Prova - Indirizzo --}}
                                <div class="form-group col-md-4">
                                    <label for="office_address">Sede Prova - Indirizzo</label>
                                    {{ form_input([
                                        'name' => 'office_address',
                                        'value' => !empty($contest['office_address']) ? $contest['office_address'] : null,
                                        'placeholder' => 'Indirizzo',
                                        'id' => 'input_office_address',
                                        'class' => 'form-control input_office_address'
                                    ]) }}
                                </div>
                            </div>

                            {{generateSeparator('Informazioni sul reclutamento')}}

                            <div class="form-group">
                                {{-- Campo Calendario delle prove --}}
                                <label for="test_calendar">Calendario delle prove</label>
                                {{form_editor([
                                    'name' => 'test_calendar',
                                    'value' => !empty($contest['test_calendar']) ? $contest['test_calendar'] : null,
                                    'id' => 'input_test_calendar',
                                    'class' => 'form-control input_test_calendar'
                                ]) }}
                            </div>

                            <div class="form-group">
                                {{-- Campo Criteri di valutazione --}}
                                <label for="evaluation_criteria">Criteri di valutazione</label>
                                {{form_editor([
                                    'name' => 'evaluation_criteria',
                                    'value' => !empty($contest['evaluation_criteria']) ? $contest['evaluation_criteria'] : null,
                                    'id' => 'input_evaluation_criteria',
                                    'class' => 'form-control input_evaluation_criteria'
                                ]) }}
                            </div>

                            <div class="form-group">
                                {{-- Campo Tracce prove scritte --}}
                                <label for="traces_written_tests">Tracce prove scritte</label>
                                {{form_editor([
                                    'name' => 'traces_written_tests',
                                    'value' => !empty($contest['traces_written_tests']) ? $contest['traces_written_tests'] : null,
                                    'id' => 'input_traces_written_tests',
                                    'class' => 'form-control input_traces_written_tests'
                                ]) }}
                            </div>

                            {{generateSeparator('Date di riferimento')}}

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Data di pubblicazione --}}

                                {{-- Campo Data di scadenza del bando --}}
                                <div class="form-group col-md-6">
                                    <label for="expiration_date">Data di scadenza del bando</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="expiration_date"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control"
                                               value="{{ @$contest['expiration_date'] }}"
                                               id="expiration_date">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Data di termine del concorso --}}
                                <div class="form-group col-md-6">
                                    <label for="expiration_contest_date">Data di termine del concorso</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="expiration_contest_date"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control"
                                               value="{{ @$expiration_contest_date }}"
                                               id="expiration_contest_date">
                                    </div>
                                </div>

                                {{-- Campo Orario di scadenza del bando --}}
                                <div class="form-group col-md-6">
                                    <label for="expiration_time">Orario di scadenza del bando</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="far fa-clock"></i></span>
                                        </div>
                                        <input type="time" name="expiration_time"
                                               placeholder="Inserisci ora fine. Es: 10:00"
                                               autocomplete="off" class="form-control"
                                               value="{{ @$contest['expiration_time'] }}" id="expiration_time">
                                    </div>
                                </div>
                            </div>

                            {{generateSeparator('Altre informazioni')}}

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Numero dipendenti assunti --}}
                                @if($_storageType == 'update' && !empty($contest['hired_employees']))
                                    <div class="form-group col-md-4">
                                        <label for="hired_employees">Numero dipendenti assunti</label>
                                        {{ form_input([
                                            'name' => 'hired_employees',
                                            'type' => 'number',
                                            'value' => !empty($contest['hired_employees']) ? $contest['hired_employees'] : null,
                                            'placeholder' => 'n° dipendenti',
                                            'id' => 'input_hired_employees',
                                            'class' => 'form-control input_hired_employees'
                                        ]) }}
                                    </div>
                                @endif

                                @if($_storageType == 'update' && !empty($contest['expected_expenditure']))
                                {{-- Campo Eventuale spesa prevista --}}
                                    <div class="form-group col-md-4">
                                        <label for="expected_expenditure">Eventuale spesa prevista</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-euro-sign"></i></span>
                                            </div>
                                            {{ form_input([
                                            'name' => 'expected_expenditure',
                                            'value' => !empty($contest['expected_expenditure']) ? $contest['expected_expenditure'] : null,
                                            'placeholder' => '',
                                            'id' => 'input_expected_expenditure',
                                            'class' => 'form-control input_expected_expenditure a-num-class'
                                        ]) }}
                                        </div>
                                    </div>
                                @endif
                                @if($_storageType == 'update' && !empty($contest['expenditures_made']))
                                {{-- Campo Spese effettuate --}}
                                    <div class="form-group col-md-4">
                                        <label for="expenditures_made">Spese effettuate</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-euro-sign"></i></span>
                                            </div>
                                            {{ form_input([
                                            'name' => 'expenditures_made',
                                            'value' => !empty($contest['expenditures_made']) ? $contest['expenditures_made'] : null,
                                            'placeholder' => '',
                                            'id' => 'input_expenditures_made',
                                            'class' => 'form-control input_expenditures_made a-num-class'
                                        ]) }}
                                        </div>
                                    </div>
                                @endif
                            </div>

                            {{-- Campo Provvedimento --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="measure">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_measure" id="c_ajax_measure_label">Provvedimento</label>
                                    </div>
                                    <div id="ajax_measure"></div>
                                    <input type="hidden" value="" name="object_measure_id" id="input_measure_id"
                                           class="object_measure_id">
                                </div>
                            </div>

                            {{-- Campo Commissione giudicatrice - seleziona da archivio incarichi e consulenze --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_assignments" id="c_ajax_assignments_label">Commissione
                                            giudicatrice - seleziona da archivio
                                            incarichi e
                                            consulenze</label>
                                    </div>
                                    <div id="ajax_assignments"></div>
                                    <input type="hidden" value="" name="commissions" id="input_commission"
                                           class="commissions">
                                </div>
                            </div>

                            <div class="form-group">
                                {{-- Campo Tracce prove scritte --}}
                                <label for="traces_written_tests">Incarichi ad esterni</label>
                                {{form_editor([
                                    'name' => 'external_assignment',
                                    'value' => !empty($contest['external_assignment']) ? $contest['external_assignment'] : null,
                                    'id' => 'input_external_assignment',
                                    'class' => 'form-control input_external_assignment'
                                ]) }}
                            </div>

                            <div class="form-group">
                                {{-- Campo Maggiori informazioni sul bando --}}
                                <label for="description">Maggiori informazioni sul bando</label>
                                {{form_editor([
                                    'name' => 'description',
                                    'value' => !empty($contest['description']) ? $contest['description'] : null,
                                    'id' => 'input_description',
                                    'class' => 'form-control input_description'
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

        @if(!empty($contest['id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'id',
                'value' => $contest['id'],
                'id' => 'contest_id',
                'class' => 'contest_id',
            ]) }}
        @endif

        @if(!empty($commissionIds))
            {{ form_input([
                'type' => 'hidden',
                'name' => '_commissionIds',
                'value' => implode(',',$commissionIds),
                'id' => '_commissionIds',
                'class' => '_commissionIds',
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
{{ js(verifyFirstOrLastSlash('admin/get/config.js?box='.$is_box)) }}

<script type="text/javascript">
    //Previene il salvataggio quando si preme invio e il focus non è sul pulsante di salvataggio
    document.getElementById('{{ $formSettings['id'] }}').addEventListener('keydown', e => {
        if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
            e.preventDefault();
        }
    });

    const institutionId = $('#institution_id').val();
    let contest;

    $(document).ready(function () {

        let formModified = false;

        {{-- Begin creazione campi select --}}
        new TomSelect('.ts-province_office', TS_SINGLE_CLEAR);

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
            institution_id: institutionId,
            selectedIDs: '<?php echo !empty($contest['office']) ? implode(",",array_column($contest['office'], 'id')) : ''; ?>',
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
            label: '#c_ajax_structure_label'
        });

        // Se sono in modifica o in duplicazione setto i valori il presidente della commissione gia selezionato
        @if(!empty($contest['office']))
        structure.patOsAjaxPagination.setValue('{{ $contest['office']['id'] }}', '{{ htmlEscape($contest['office']['structure_name']).(!empty($contest['office']['parent_name'])?' - '.htmlEscape($contest['office']['parent_name']):'').(!empty($contest['office']['reference_email'])?' - '.$contest['office']['reference_email']:'') }}', true);
        @endif

        // Tabella per la selezione del Concorso relativo
        contest = refreshContest(institutionId, null, false);

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
            label: '#c_ajax_assignments_label'
        });

        // Setto gli incarichi se sono in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($contest['assignments']))
        @foreach($contest['assignments'] as $assignment)
        @php
            $tmpStartDate = !empty($assignment['assignment_start']) ? ' - '.date('d/m/Y', strtotime($assignment['assignment_start'])) : null;
        @endphp
        assignments.patOsAjaxPagination.setValue('{{ $assignment['id'] }}', '{{ htmlEscape($assignment['name']).' - '.htmlEscape($assignment['object']).''.$tmpStartDate }}', true);
        @endforeach
        @endif

        //Tabella per la selezione del provvedimento
        let measure = $('#ajax_measure').patOsAjaxPagination({
            url: config.measure.url,
            textLoad: config.measure.textLoad,
            selectedLabel: 'Provvedimento selezionato',
            footerTable: config.measure.footerTable,
            classTable: config.measure.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.measure.hideTable,
            showTable: config.measure.showTable,
            search_placeholder: config.measure.search_placeholder,
            setInputDataValue: '#input_measure_id',
            dataParams: {
                model: 22,
                institution_id: institutionId
            },
            columns: config.measure.columns,
            action: {
                type: 'radio',
            },
            dataSource: config.measure.dataSource,
            addRecord: config.measure.addRecord,
            archived: config.measure.archived,
            label: '#c_ajax_measure_label',
        });

        // Setto il bando relativo se sono in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($contest['relative_measure']))
        @php
            $tmpStartDate = !empty($contest['relative_measure']) ? ' - '.date('d/m/Y', strtotime($contest['relative_measure']['date'])) : null;
        @endphp
        measure.patOsAjaxPagination.setValue('{{ $contest['object_measure_id'] }}', '{{ htmlEscape($contest['relative_measure']['object']).' - '.htmlEscape($contest['relative_measure']['number']).''.$tmpStartDate }}', true);
        @endif
        {{-- End creazione campi select --}}

        {{-- Begin creazione campi CKEDITOR --}}
        // Cattura eventuali modifiche fatte sui campi CKEDITOR
        CKEDITOR.on('instanceCreated', function (e) {
            e.editor.on('change', function (event) {
                formModified = true;
            });
        });
        CKEDITOR.replace('input_test_calendar', {
             customConfig: '{{ verifyFirstOrLastSlash('assets/common/ckeditor4/config.js')}}',
        });
        CKEDITOR.replace('input_evaluation_criteria', {
            customConfig: '{{ verifyFirstOrLastSlash('assets/common/ckeditor4/config.js')}}',
        });
        CKEDITOR.replace('input_traces_written_tests', {
             customConfig: '{{ verifyFirstOrLastSlash('assets/common/ckeditor4/config.js')}}',
        });
        CKEDITOR.replace('input_description', {
             customConfig: '{{ verifyFirstOrLastSlash('assets/common/ckeditor4/config.js')}}',
        });
        CKEDITOR.replace('input_external_assignment', {
             customConfig: '{{ verifyFirstOrLastSlash('assets/common/ckeditor4/config.js')}}',
        });
        {{-- End creazione campi CKEDITOR --}}

        // Se si sta creando un nuovo bando, inizializzo la data di pubblicazione con la data odierna
        @if(empty($contest['activation_date']) && $_storageType == 'insert')
            $('#activation_date').attr('value', "<?= date('Y-m-d\TH:i') ?>");
        @endif

        {{-- End creazione campi data --}}

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
                createValidatorFormSuccessToast(response.data.message, 'Bando di concorso');

                {{-- Controllo se non sono all'interno di un modale --}}
                @if(empty($is_box))
                setTimeout(function () {
                    window.location.href = '{{ siteUrl('admin/contest') }}';
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

    function refreshContest(institutionId, tag, showTable) {
        let c = $('#ajax_contest').patOsAjaxPagination({
            url: config.contest.url,
            textLoad: config.contest.textLoad,
            selectedLabel: 'Concorso relativo selezionato',
            footerTable: config.contest.footerTable,
            classTable: config.contest.classTable,
            hideShowTable: showTable ? showTable : Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.contest.hideTable,
            showTable: config.contest.showTable,
            search_placeholder: config.contest.search_placeholder,
            setInputDataValue: '#input_related_contest',
            dataParams: {
                model: 34,
                institution_id: institutionId,
                exclude_id: <?php echo ($_storageType == 'insert') ? 0 : $contest['id'] ?>,
                tag: tag
            },
            dateFormat: config.contest.dateFormat,
            columns: config.contest.columns,
            action: {
                type: 'radio',
            },
            dataSource: config.contest.dataSource,
            addRecord: config.contest.addRecord,
            label: '#ajax_contest_label'
        });

        // Setto il concorso relativo se sono in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($contest['related_contest']))
        @php
            $tmpActivationDate = !empty($contest['related_contest']['activation_date']) ? ' - '.date('d/m/Y', strtotime($contest['related_contest']['activation_date'])) : null;
            $tmpExpirationDate = !empty($contest['related_contest']['expiration_date']) ? ' - '.date('d/m/Y', strtotime($contest['related_contest']['expiration_date'])) : null;
        @endphp
            c.patOsAjaxPagination.setValue('{{ $contest['related_contest']['id'] }}', '{{htmlEscape($contest['related_contest']['object']).' - '.$contest['related_contest']['typology'].''.$tmpActivationDate.''.$tmpExpirationDate }}', true);
        @endif

            return c;
    }

</script>
{% endblock %}
