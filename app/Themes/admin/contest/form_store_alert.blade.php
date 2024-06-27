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
                        Aggiunta Avviso per {{ $archiveName }}
                    @elseif($_storageType === 'update')
                        Modifica Avviso per {{ $archiveName }}
                    @else
                        Duplicazione Avviso per {{ $archiveName }}
                    @endif
                </span>
                @if(empty($is_box))
                    <div class="card-tools">
                        <ul class="nav nav-pills ml-auto">
                            <li class="nav-item">
                                <a href="{{ siteUrl('admin/contest') }}" title="Torna indietro"
                                   class="btn btn-default btn-sm btn-outline-primary">
                                    <i class="fas fa-caret-left"></i> Torna a elenco {{ $archiveName }}
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
                                <div class="form-group col-md-12">
                                    <label for="object">Oggetto *</label>
                                    {{ form_input([
                                        'name' => 'object',
                                        'value' => !empty($alert['object']) ? (($_storageType === 'duplicate') ? 'Copia di ' . $alert['object'] : $alert['object']) : null,
                                        'placeholder' => 'Oggetto',
                                        'id' => 'input_object',
                                        'class' => 'form-control input_object',
                                    ]) }}
                                </div>
                            </div>

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
                                            riferimento</label>
                                    </div>
                                    <div id="ajax_structure"></div>
                                    <input type="hidden" value="" name="object_structures_id"
                                           id="input_object_structures_id"
                                           class="object_structures_id">
                                </div>
                            </div>

                            {{generateSeparator('Date di riferimento')}}

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Data di pubblicazione --}}
                                <div class="form-group col-md-6">
                                    <label for="activation_date">Data di pubblicazione *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="datetime-local" name="activation_date"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control"
                                               value="{{ @$alert['activation_date'] }}"
                                               id="activation_date">
                                    </div>
                                </div>

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
                                               value="{{ @$alert['expiration_date'] }}"
                                               id="expiration_date">
                                    </div>
                                </div>
                            </div>

                            {{generateSeparator('Altre informazioni')}}

                            <div class="form-group">
                                {{-- Campo Maggiori informazioni sul bando --}}
                                <label for="description">Maggiori informazioni sul bando</label>
                                {{form_editor([
                                    'name' => 'description',
                                    'value' => !empty($alert['description']) ? $alert['description'] : null,
                                    'id' => 'input_description',
                                    'class' => 'form-control input_description'
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

        @if(!empty($alert['id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'id',
                'value' => $alert['id'],
                'id' => 'contest_id',
                'class' => 'contest_id',
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
{{ css('select2/css/select2.min.css','common') }}
{{ css('select2-bootstrap4-theme/select2-bootstrap4.min.css','common') }}
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
            label: '#c_ajax_structure_label'
        });

        // Se sono in modifica o in duplicazione setto i valori il presidente della commissione gia selezionato
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($alert['office']))
        structure.patOsAjaxPagination.setValue('{{ $alert['object_structures_id'] }}', '{{ htmlEscape($alert['office']['structure_name']).(!empty($alert['office']['parent_name'])?' - '.htmlEscape($alert['office']['parent_name']):'').(!empty($alert['office']['reference_email'])?' - '.$alert['office']['reference_email']:'') }}', true);
        @endif

        {{-- Begin Select2 campo "Concorso o Avviso relativo" --}}
        // Tabella per la selezione del Concorso relativo
        let contest = $('#ajax_contest').patOsAjaxPagination({
            url: config.contest.url,
            textLoad: config.contest.textLoad,
            selectedLabel: 'Concorso relativo selezionato',
            footerTable: config.contest.footerTable,
            classTable: config.contest.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.contest.hideTable,
            showTable: config.contest.showTable,
            search_placeholder: config.contest.search_placeholder,
            setInputDataValue: '#input_related_contest',
            dataParams: {
                model: 34,
                institution_id: institutionId,
                exclude_id: <?php echo ($_storageType == 'insert') ? 0 : $alert['id'] ?>
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
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($alert['related_contest']))
        @php
            $tmpActivationDate = !empty($alert['related_contest']['activation_date']) ? ' - '.date('d/m/Y', strtotime($alert['related_contest']['activation_date'])) : null;
            $tmpExpirationDate = !empty($alert['related_contest']['expiration_date']) ? ' - '.date('d/m/Y', strtotime($alert['related_contest']['expiration_date'])) : null;
        @endphp
        contest.patOsAjaxPagination.setValue('{{ $alert['related_contest']['id'] }}', '{{htmlEscape($alert['related_contest']['object']).' - '.$alert['related_contest']['typology'].''.$tmpActivationDate.''.$tmpExpirationDate }}', true);
        @endif

        {{-- Select2 campo "Stato pubblicazione" --}}
        let $dropdownPublishingStatus = $('.select2-publishing_status');
        $dropdownPublishingStatus.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        {{-- Begin creazione campi CKEDITOR --}}
        // Cattura eventuali modifiche fatte sui campi CKEDITOR
        CKEDITOR.on('instanceCreated', function (e) {
            e.editor.on('change', function (event) {
                formModified = true;
            });
        });

        CKEDITOR.replace('input_description');
        {{-- End creazione campi CKEDITOR --}}

        // Se si sta creando un nuovo bando, inizializzo la data di pubblicazione con la data odierna
        if ($('#_storage_type').val() == 'insert') {
            @if(empty($alert['activation_date']))
            $('#activation_date').attr('value', "<?= date('Y-m-d H:i') ?>");
            @endif
        }
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