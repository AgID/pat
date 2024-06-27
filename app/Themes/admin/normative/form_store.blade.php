{{-- Form store Normative --}}
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
                        Aggiunta Normativa
                    @elseif($_storageType === 'update')
                        Modifica Normativa
                    @else
                        Duplicazione Normativa
                    @endif
                </span>
                @if(empty($is_box))
                    <div class="card-tools">
                        <ul class="nav nav-pills ml-auto">
                            <li class="nav-item">
                                <a href="{{ siteUrl('admin/normative') }}" title="Torna indietro"
                                   class="btn btn-default btn-sm btn-outline-primary">
                                    <i class="fas fa-caret-left"></i> Torna a elenco normative
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

                            {{-- Campo Tipologia atto --}}
                            <div class="form-group">
                                <label for="act_type">Tipologia atto *</label>
                                <div class="select2-blue" id="input_act_type">
                                    {{ form_dropdown(
                                        'act_type',
                                        @$actTypes,
                                        @$normative['act_type'],
                                        'class="form-control select2-typology" id="act_type" style="width: 100%;"'
                                    ) }}
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Numero --}}
                                <div class="form-group col-md-4">
                                    <label for="number">Numero</label>
                                    {{ form_input([
                                        'name' => 'number',
                                        'type' => 'number',
                                        'value' => !empty($normative['number']) ? $normative['number'] : null,
                                        'placeholder' => 'Numero',
                                        'id' => 'input_number',
                                        'class' => 'form-control input_number',
                                    ]) }}
                                </div>

                                {{-- Campo Protocollo --}}
                                <div class="form-group col-md-4">
                                    <label for="protocol">Protocollo</label>
                                    {{ form_input([
                                        'name' => 'protocol',
                                        'type' => 'number',
                                        'value' => !empty($normative['protocol']) ? $normative['protocol'] : null,
                                        'placeholder' => 'Protocollo',
                                        'id' => 'input_protocol',
                                        'class' => 'form-control input_protocol'
                                    ]) }}
                                </div>

                                {{-- Campo Data promulgazione--}}
                                <div class="form-group col-md-4">
                                    <label for="issue_date">Data promulgazione *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="issue_date"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control" value="{{ @$issue_date }}"
                                               id="issue_date">
                                    </div>
                                </div>
                            </div>

                            {{-- Campo Titolo della norma --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-9">
                                    <label for="name">Titolo della norma *</label>
                                    {{ form_input([
                                        'name' => 'name',
                                        'value' => !empty($normative['name']) ? (($_storageType === 'duplicate') ? 'Copia di ' . $normative['name'] : $normative['name']) : null,
                                        'placeholder' => 'Titolo della norma',
                                        'id' => 'input_name',
                                        'class' => 'form-control input_name',
                                    ]) }}
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Argomento della Normativa --}}
                                <div class="form-group col-md-6">
                                    <label for="normative_topic">Argomento della Normativa *</label>
                                    <div class="select2-blue" id="input_normative_topic">
                                        {{ form_dropdown(
                                            'normative_topic',
                                            ['' => '',1=>'Organizzazione dell\'Ente (pubblica in Riferimenti normativi su organizzazione e attività)',
                                                2=>'Sovvenzioni e contributi (pubblica in Criteri e modalità)',3=>'Altro'],
                                            @$normative['normative_topic'],
                                            'class="form-control select2-normative_topic" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>

                                {{-- Campo Inserisci link a Normattiva --}}
                                <div class="form-group col-md-6" id="input_normative_link">
                                    <label for="normative_link">Inserisci link a Normativa</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-link"></i></span>
                                        </div>
                                        {{ form_input([
                                        'name' => 'normative_link',
                                        'value' => !empty($normative['normative_link']) ? $normative['normative_link'] : null,
                                        'placeholder' => 'https://www.',
                                        'id' => 'input_normative_link',
                                        'class' => 'form-control input_normative_link',
                                    ]) }}
                                    </div>
                                </div>
                            </div>

                            {{-- Campo Valida per le strutture organizzative --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="president">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label for="ajax_structures" id="nor_structures_label">Valida per le strutture organizzative</label>
                                    </div>
                                    <div id="ajax_structures"></div>
                                    <input type="hidden" value="" name="structures"
                                           id="input_structures"
                                           class="structures">
                                </div>
                            </div>

                            <div class="form-group">
                                {{-- Campo Testo di descrizione --}}
                                <label for="description">Testo di descrizione</label>
                                {{form_editor([
                                    'name' => 'description',
                                    'value' => !empty($normative['description']) ? $normative['description'] : null,
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

        @if(!empty($normative['id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'id',
                'value' => $normative['id'],
                'id' => 'normative_id',
                'class' => 'normative_id',
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

    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Firefox */
    input[type=number] {
        -moz-appearance: textfield;
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

        // Tabella per la selezione delle strutture
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
            setInputDataValue: '#input_structures',
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
            label: '#nor_structures_label'
        });

        // Se sono in modifica o in duplicazione setto i valori il presidente della commissione gia selezionato
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($normative['structures']))
        @foreach($normative['structures'] as $structure)
        structures.patOsAjaxPagination.setValue('{{ $structure['id'] }}', '{{ htmlEscape($structure['structure_name']).(!empty($structure['parent_name'])?' - '.htmlEscape($structure['parent_name']):'').(!empty($structure['reference_email'])?' - '.$structure['reference_email']:'') }}', true);
        @endforeach
        @endif

        {{-- Campo CKEDITOR --}}
        // Cattura eventuali modifiche fatte sui campi CKEDITOR
        CKEDITOR.on('instanceCreated', function (e) {
            e.editor.on('change', function (event) {
                formModified = true;
            });
        });
        CKEDITOR.replace('input_description');

        {{-- Select2 per campo "Tipologia atto" --}}
        let $dropdownTypology = $('.select2-typology');
        $dropdownTypology.select2({
            placeholder: 'Seleziona la tipologia',
            minimumResultsForSearch: -1
        });

        {{-- Select2 per campo "Argomento della Normativa" --}}
        let $dropdownNormativeTopic = $('.select2-normative_topic');
        $dropdownNormativeTopic.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        {{-- Select2 campo "Stato pubblicazione" --}}
        let $dropdownPublishingStatus = $('.select2-publishing_status');
        $dropdownPublishingStatus.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
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
                createValidatorFormSuccessToast(response.data.message, 'Normativa');

                {{-- Controllo se non sono all'interno di un modale --}}
                @if(empty($is_box))
                setTimeout(function () {
                    window.location.href = '{{ siteUrl('admin/normative') }}';
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