{{--  Form store Bilanci --}}
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
                        Aggiunta Bilancio
                    @elseif($_storageType === 'update')
                        Modifica Bilancio
                    @else
                        Duplicazione Bilancio
                    @endif
                </span>
                @if(empty($is_box))
                    <div class="card-tools">
                        <ul class="nav nav-pills ml-auto">
                            <li class="nav-item">
                                <a href="{{ siteUrl('admin/balance') }}" title="Torna indietro"
                                   class="btn btn-default btn-sm btn-outline-primary">
                                    <i class="fas fa-caret-left"></i> Torna a elenco bilanci
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

                            {{-- Campo Tipologia bilancio --}}
                            <div class="form-group">
                                <label for="typology">Tipologia *</label>
                                <div class="select2-blue" id="input_typology">
                                    {{ form_dropdown(
                                        'typology',
                                        @$typologies,
                                        @$balance['typology'],
                                        'class="form-control select2-typology" style="width: 100%;"'
                                    ) }}
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Nome--}}
                                <div class="form-group col-md-6">
                                    <label for="name">Nome *</label>
                                    {{ form_input([
                                        'name' => 'name',
                                        'value' => !empty($balance['name']) ? (($_storageType === 'duplicate') ? 'Copia di ' . $balance['name'] : $balance['name']) : null,
                                        'placeholder' => 'Nome',
                                        'id' => 'input_name',
                                        'class' => 'form-control input_name',
                                    ]) }}
                                </div>

                                {{-- Campo Anno--}}
                                <div class="form-group col-md-6">
                                    <label for="year">Anno *</label>
                                    {{ form_input([
                                        'name' => 'year',
                                        'value' => !empty($balance['year']) ? $balance['year'] : null,
                                        'placeholder' => 'Anno',
                                        'id' => 'input_year',
                                        'class' => 'form-control input_year',
                                    ]) }}
                                </div>
                            </div>

                            {{-- Campo Provvedimento --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="measure">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label for="ajax_measure" id="b_measure_label">Provvedimento</label>
                                    </div>
                                    <div id="ajax_measure"></div>
                                    <input type="hidden" value="" name="object_measure_id" id="input_measure_id"
                                           class="object_measure_id">
                                </div>
                            </div>

                            <div class="form-group">
                                {{-- Campo Testo di descrizione --}}
                                <label for="description">Descrizione</label>
                                {{form_editor([
                                    'name' => 'description',
                                    'value' => !empty($balance['description']) ? $balance['description'] : null,
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

        @if(!empty($balance['id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'id',
                'value' => $balance['id'],
                'id' => 'balance_id',
                'class' => 'balance_id',
            ]) }}
        @endif

        {{ form_hidden('institute_id',checkAlternativeInstitutionId()) }}
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
{{ js('admin/get/config.js?') }}
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

        {{-- Campo CKEDITOR --}}
        // Cattura eventuali modifiche fatte sui campi CKEDITOR
        CKEDITOR.on('instanceCreated', function (e) {
            e.editor.on('change', function (event) {
                formModified = true;
            });
        });
        CKEDITOR.replace('input_description');

        {{-- Select2 per campo "Tipologia" --}}
        let $dropdownTypology = $('.select2-typology');
        $dropdownTypology.select2({
            placeholder: 'Seleziona il tipo di documento',
            minimumResultsForSearch: -1
        });

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
            label: '#b_measure_label'
        });

        // Setto il bando relativo se sono in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($balance['related_measure']))
        @php
            $tmpStartDate = (!empty($balance['related_measure']) && !empty($balance['related_measure']['date']) ) ? ' - '.date('d/m/Y', strtotime($balance['related_measure']['date'])) : null;
        @endphp
        measure.patOsAjaxPagination.setValue('{{ $balance['object_measure_id'] }}', '{{ htmlEscape($balance['related_measure']['object']).' - '.$balance['related_measure']['number'].''.$tmpStartDate }}', true);
        @endif

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
                createValidatorFormSuccessToast(response.data.message, 'Bilancio');

                {{-- Controllo se non sono all'interno di un modale --}}
                @if(empty($is_box))
                setTimeout(function () {
                    window.location.href = '{{ siteUrl('admin/balance') }}';
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

    });
</script>
{% endblock %}