{{-- Form store Bandi Gare e Contratti --}}
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
                        Aggiunta Determina a contrarre o atto equivalente
                    @elseif($_storageType === 'update')
                        Modifica Determina a contrarre o atto equivalente
                    @else
                        Duplicazione Determina a contrarre o atto equivalente
                    @endif
                </span>
                @if(empty($is_box))
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
                                <div class="form-group col-md-6">
                                    <label for="object">Oggetto *</label>
                                    {{ form_input([
                                        'name' => 'object',
                                        'value' => !empty($deliberation['object']) ? (($_storageType === 'duplicate') ? 'Copia di ' . $deliberation['object'] : $deliberation['object']) : null,
                                        'placeholder' => 'Oggetto',
                                        'id' => 'input_object',
                                        'class' => 'form-control input_object'
                                    ]) }}
                                </div>

                                <div class="form-group col-md-6">
                                    {{-- Campo Codice CIG --}}
                                    <label for="cig">Codice CIG</label>
                                    {{ form_input([
                                        'name' => 'cig',
                                        'value' => !empty($deliberation['cig']) ? $deliberation['cig'] : null,
                                        'placeholder' => 'Codice CIG',
                                        'id' => 'input_cig',
                                        'class' => 'form-control input_cig'
                                    ]) }}
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Pubblica in --}}
                                <div class="form-group col-md-12" id="input_contact_personnel">
                                    <label for="contact_personnel">Pubblica in</label>
                                    <div class="select2-blue">
                                        {{ form_dropdown(
                                            'public_in[]',
                                            @$publicIn,
                                            @$publicInIDs,
                                            'class="form-control select2-public_in" multiple="multiple" data-placeholder="Seleziona" data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-6" id="determined_term">
                                    <label for="determined_term">Settore *
                                    </label>
                                    <div class="select2-blue" id="input_sector">
                                        {{ form_dropdown(
                                            'sector',
                                            [
                                                '' => null,
                                                'O-sotto' => 'Ordinario - Sottosoglia',
                                                'O-sopra' => 'Ordinario - Soprasoglia',
                                                'sponsor' => 'Sponsorizzazioni'
                                            ],
                                            @$deliberation['sector'],
                                            'class="form-control select2-sector" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Data dell'atto --}}
                                <div class="form-group col-md-6">
                                    <label for="act_date">Data dell'atto</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="act_date"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control"
                                               value="{{ @$deliberation['act_date'] }}"
                                               id="input_act_date">
                                    </div>
                                </div>

                                {{-- Campo Data di pubblicazione sul sito --}}
                                <div class="form-group col-md-6">
                                    <label for="activation_date">Data di pubblicazione sul sito *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="datetime-local" name="activation_date"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control"
                                               value="{{ !empty($deliberation['activation_date']) ? date('Y-m-d H:i', strtotime($deliberation['activation_date'])) : null }}"
                                               id="input_activation_date">
                                    </div>
                                </div>
                            </div>

                            {{generateSeparator('Informazioni collegate')}}

                            {{-- Altre procedure relative --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="procedures">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_procedures" id="del_procedures_label">Altre procedure relative
                                        </label>
                                    </div>
                                    <div id="ajax_procedures"></div>
                                    <input type="hidden" value="" name="procedures" id="input_procedures"
                                           class="input_procedures">
                                </div>
                            </div>

                            {{-- Campo Ufficio --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="structure">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_structure" id="del_structure_label">Ufficio</label>
                                    </div>
                                    <div id="ajax_structure"></div>
                                    <input type="hidden" value="" name="object_structures_id"
                                           id="input_object_structures_id"
                                           class="object_structures_id">
                                </div>
                            </div>

                            {{-- Campo RUP --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="rup">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_rup" id="del_rup_label">RUP
                                        </label>
                                    </div>
                                    <div id="ajax_rup"></div>
                                    <input type="hidden" value="" name="object_personnel_id" id="input_personnel_id"
                                           class="object_personnel_id">
                                </div>
                            </div>

                            {{-- Campo Provvedimento --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="measure">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_measure" id="del_measure_label">Provvedimento</label>
                                    </div>
                                    <div id="ajax_measure"></div>
                                    <input type="hidden" value="" name="object_measure_id" id="input_measure_id"
                                           class="object_measure_id">
                                </div>
                            </div>

                            {{generateSeparator('Altre informazioni')}}

                            <div class="form-group">
                                {{-- Campo Note --}}
                                <label for="details">Note</label>
                                {{form_editor([
                                    'name' => 'details',
                                    'value' => !empty($deliberation['details']) ? $deliberation['details'] : null,
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

        {{ form_input([
           'type' => 'hidden',
           'name' => '__ignore_cig',
           'value' => 0,
           'id' => '__ignore_cig',
           'class' => '__ignore_cig',
       ]) }}

        @if(!empty($deliberation['id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'id',
                'value' => $deliberation['id'],
                'id' => 'deliberation_id',
                'class' => 'deliberation_id',
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
{{ js('select2/js/select2.full.min.js','common') }}
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

        //Tabella per la selezione delle procedure relative alla delibera
        let otherProcedures = $('#ajax_procedures').patOsAjaxPagination({
            url: config.notice.url,
            textLoad: config.notice.textLoad,
            selectedLabel: 'Altre procedure selezionate',
            footerTable: config.notice.footerTable,
            classTable: config.notice.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.notice.hideTable,
            showTable: config.notice.showTable,
            search_placeholder: config.notice.search_placeholder,
            setInputDataValue: '#input_procedures',
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
            published: config.notice.published,
            label: '#del_procedures_label'
        });

        //Se sono presenti setto le procedure gia selezionate in modifica o in duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($deliberation['proceedings']))
        @foreach($deliberation['proceedings'] as $proceeding)
        @php
            $tmpActivationTime = !empty($proceeding['activation_date']) ? ' - '.date('d/m/Y', strtotime($proceeding['activation_date'])) : null;
            $tmpExpirationTime = !empty($proceeding['expiration_date']) ? ' - '.date('d/m/Y', strtotime($proceeding['expiration_date'])) : null;
        @endphp
        otherProcedures.patOsAjaxPagination.setValue('{{ $proceeding['id'] }}', '{{ $proceeding['type'].' - '.htmlEscape($proceeding['object']).' - '.$proceeding['cig'].$tmpActivationTime.$tmpExpirationTime }}', true);
        @endforeach
        @endif

        // Tabella per la selezione del rup(dal personale)
        let rup = $('#ajax_rup').patOsAjaxPagination({
            url: config.personnel.url,
            textLoad: config.personnel.textLoad,
            selectedLabel: 'Rup selezionato',
            footerTable: config.personnel.footerTable,
            classTable: config.personnel.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.personnel.hideTable,
            showTable: config.personnel.showTable,
            search_placeholder: config.personnel.search_placeholder,
            setInputDataValue: '#input_personnel_id',
            dataParams: {
                model: 2,
                institution_id: institutionId
            },
            columns: config.personnel.columns,
            action: {
                type: 'radio',
            },
            dataSource: config.personnel.dataSource,
            addRecord: config.personnel.addRecord,
            archived: config.personnel.archived,
            published: config.personnel.published,
            label: '#del_rup_label'
        });

        // Se sono in modifica o in duplicazione setto i valori il presidente della commissione gia selezionato
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($deliberation['rup']))
        rup.patOsAjaxPagination.setValue('{{ $deliberation['rup']['id'] }}', '{{ (!empty($deliberation['rup']['full_name'])?htmlEscape($deliberation['rup']['title']).' - ':'').htmlEscape($deliberation['rup']['full_name']).' - '.htmlEscape($deliberation['rup']['name']).' - '.(!empty($deliberation['rup']['email'])?$deliberation['rup']['email']:'N.D') }}', true);
        @endif

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
            label: '#del_structure_label'
        });

        // Se sono in modifica o in duplicazione setto i valori il presidente della commissione gia selezionato
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($deliberation['structure']))
        structure.patOsAjaxPagination.setValue('{{ $deliberation['object_structures_id'] }}', '{{ htmlEscape($deliberation['structure']['structure_name']).(!empty($deliberation['structure']['parent_name'])?' - '.htmlEscape($deliberation['structure']['parent_name']):'').(!empty($deliberation['structure']['reference_email'])?' - '.$deliberation['structure']['reference_email']:'') }}', true);
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
            label: '#del_measure_label'
        });

        // Setto il bando relativo se sono in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($deliberation['relative_measure']))
        @php
            $tmpStartDate = !empty($deliberation['relative_measure']) ? ' - '.date('d/m/Y', strtotime($deliberation['relative_measure']['date'])) : null;
        @endphp
        measure.patOsAjaxPagination.setValue('{{ $deliberation['object_measure_id'] }}', '{{ htmlEscape($deliberation['relative_measure']['object']).' - '.$deliberation['relative_measure']['number'].''.$tmpStartDate }}', true);
        @endif

        // Se si sta creando un nuovo bando, inizializzo la data di pubblicazione con la data odierna
        if ($('#_storage_type').val() == 'insert' && !$('#input_activation_date').val()) {
            $('#input_activation_date').attr('value', "<?= date('Y-m-d H:i') ?>");
        }
        {{-- End campi data --}}

        {{-- Select2 campo "Stato pubblicazione" --}}
        let $dropdownPublishingStatus = $('.select2-publishing_status');
        $dropdownPublishingStatus.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        {{-- Select2 per campo "Settore" --}}
        let $dropdownSector = $('.select2-sector');
        $dropdownSector.select2({
            placeholder: 'Seleziona',
            allowClear: true,
            minimumResultsForSearch: -1
        });

        {{-- Select2 per campo "Pubblica In" --}}
        let $dropdownPublicIn = $('.select2-public_in');
        $dropdownPublicIn.select2()
        $dropdownPublicIn.on('change', function () {
            $('#public_in').val($(this).val());
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

                let response = parseJson(data);

                // Controlle se l'eventuale cig inserito è gia presente o meno e in caso mostro l'alert
                if (response.data.cigs) {
                    // Funzione che mostra l'alert che notifica che il cig inserito è gia presente
                    {{-- (vedere nel footer) --}}
                    checkIfCigsExist(response.data.cigs);
                } else {
                    btnSend.empty().append('{{ __('brn_save',null,'patos') }}');
                    formModified = false;

                    // Funzione che genera il toast con il messaggio di successo
                    {{-- (vedere nel footer) --}}
                    createValidatorFormSuccessToast(response.data.message, 'Delibera a contrarre o atto equivalente');

                    {{-- Controllo se non sono all'interno di un modale --}}
                    @if(empty($is_box))
                    setTimeout(function () {
                        window.location.href = '{{ siteUrl('admin/contests-act') }}';
                    }, 800);
                    @else
                    setTimeout(function () {
                        window.parent.$('#formModal').modal('hide');
                    }, 800);
                    @endif
                }
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
         * Funzione che controlla se sono arrivato in questa pagina dal versioning
         */
        {{-- Vedere nel footer --}}
        checkIfRestore();

    });
</script>
{% endblock %}