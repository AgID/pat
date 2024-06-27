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
                        Aggiunta Intervento straordinario e di emergenza
                    @elseif($_storageType === 'update')
                        Modifica Intervento straordinario e di emergenza
                    @else
                        Duplicazione Intervento straordinario e di emergenza
                    @endif
                </span>
                @if(empty($is_box))
                    <div class="card-tools">
                        <ul class="nav nav-pills ml-auto">
                            <li class="nav-item">
                                <a href="{{ siteUrl('admin/intervention') }}" title="Torna indietro"
                                   class="btn btn-default btn-sm btn-outline-primary">
                                    <i class="fas fa-caret-left"></i> Torna a elenco Interventi
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
                            {{-- Campo Nome --}}
                            <div class="form-group">
                                <label for="title">Nome *</label>
                                {{ form_input([
                                    'name' => 'name',
                                    'value' => !empty($intervention['name']) ? (($_storageType === 'duplicate') ? 'Copia di ' . $intervention['name'] : $intervention['name']) : null,
                                    'placeholder' => 'Nome',
                                    'id' => 'input_name',
                                    'class' => 'form-control input_name',
                                ]) }}
                            </div>

                            <div class="form-group">
                                {{-- Campo Descrizione --}}
                                <label for="description">Descrizione</label>
                                {{form_editor([
                                    'name' => 'description',
                                    'value' => !empty($intervention['description']) ? $intervention['description'] : null,
                                    'id' => 'input_description',
                                    'class' => 'form-control input_description'
                                ]) }}
                            </div>

                            {{generateSeparator('Informazioni collegate')}}

                            {{-- Campo Provvedimenti correlati --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_measures" id="i_ajax_measures_label">Provvedimenti correlati</label>
                                    </div>
                                    <div id="ajax_measures"></div>
                                    <input type="hidden" value="" name="measures" id="input_measure"
                                           class="measures">
                                </div>
                            </div>

                            {{-- Campo Regolamenti correlati --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-9">
                                    <label for="regulations">Regolamenti correlati</label>
                                    <div class="select2-blue" id="input_regulations">
                                        {{ form_dropdown(
                                            'regulations[]',
                                            '',
                                            '',
                                            'class="form-control select2-regulations" multiple="multiple" data-placeholder="Seleziona o cerca regolamento..." data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>

                                <div class="form-group col-md-3">
                                    {{ form_button([
                                        'name' => 'add',
                                        'id' => 'btn_addRegulation',
                                        'class' => 'btn btn-outline-primary open-modal',
                                        'style' => 'width:100%;',
                                        'data-url' => siteUrl('admin/regulation/create-box')
                                    ],'Aggiungi nuovo &nbsp; <i class="fas fa-plus-circle"></i>') }}
                                </div>
                            </div>

                            {{generateSeparator('Altre informazioni')}}

                            <div class="form-group">
                                {{-- Campo Norme derogate e motivazione --}}
                                <label for="derogations">Norme derogate e motivazione</label>
                                {{form_editor([
                                    'name' => 'derogations',
                                    'value' => !empty($intervention['derogations']) ? $intervention['derogations'] : null,
                                    'id' => 'input_derogations',
                                    'class' => 'form-control input_derogations'
                                ]) }}
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Termini temporali per i provvedimenti straordinari --}}
                                <div class="form-group col-md-4">
                                    <label for="time_limits">Termini temporali per i provvedimenti straordinari</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="time_limits"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control"
                                               value="{{ @$time_limits }}"
                                               id="time_limits">
                                    </div>
                                </div>

                                {{-- Campo Costo interventi stimato --}}
                                <div class="form-group col-md-4">
                                    <label for="estimated_cost">Costo interventi stimato</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-euro-sign"></i></span>
                                        </div>
                                        {{ form_input([
                                        'name' => 'estimated_cost',
                                        'value' => !empty($intervention['estimated_cost']) ? $intervention['estimated_cost'] : null,
                                        'placeholder' => '',
                                        'id' => 'input_estimated_cost',
                                        'class' => 'form-control input_estimated_cost a-num-class'
                                    ]) }}
                                    </div>
                                </div>

                                {{-- Campo Costo interventi effettivo --}}
                                <div class="form-group col-md-4">
                                    <label for="effective_cost">Costo interventi effettivo</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-euro-sign"></i></span>
                                        </div>
                                        {{ form_input([
                                        'name' => 'effective_cost',
                                        'value' => !empty($intervention['effective_cost']) ? $intervention['effective_cost'] : null,
                                        'placeholder' => '',
                                        'id' => 'input_effective_cost',
                                        'class' => 'form-control input_effective_cost a-num-class'
                                    ]) }}
                                    </div>
                                </div>
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

        @if(!empty($intervention['id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'id',
                'value' => $intervention['id'],
                'id' => 'intervention_id',
                'class' => 'intervention_id',
            ]) }}
        @endif

        @if(!empty($regulationIds))
            {{ form_input([
                'type' => 'hidden',
                'name' => '_regulationIds',
                'value' => implode(',',$regulationIds),
                'id' => '_regulationIds',
                'class' => '_regulationIds',
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
        if (keyCode === 13) {
            e.preventDefault();
            return false;
        }
    });

    $(document).ready(function () {

        let formModified = false;
        let institutionId = $('#institution_id').val();

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
            label: '#i_ajax_measures_label'
        });

        // Setto il bando relativo se sono in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($intervention['measures']))
        @foreach($intervention['measures'] as $measure)
        @php
            $tmpStartDate = !empty($measure['date']) ? ' - '.date('d/m/Y', strtotime($measure['date'])) : null;
        @endphp
        measures.patOsAjaxPagination.setValue('{{ $measure['id'] }}', '{{ htmlEscape($measure['object']).(!empty($measure['number'])?' - '.$measure['number']:'').''.$tmpStartDate }}', true);
        @endforeach
        @endif

        {{-- Begin Select2 campo "Regolamenti correlati" --}}
        let $dropdownRegulation = $('.select2-regulations');
        $dropdownRegulation.select2({
            //Recupero i dati per le options della select
            ajax: {
                url: '{{siteUrl("/admin/asyncData")}}',
                type: "get",
                dataType: 'json',
                delay: 250,
                data: function (data) {
                    return {
                        model: 10,
                        institution_id: institutionId,
                        searchTerm: data.term
                    };
                },
                error: function (jqXHR, status) {
                    let response = parseJson(jqXHR.responseText);
                },
                processResults: function (response) {
                    return {
                        results: response.data.options
                    };
                },
                cache: true
            }
        });
        @if(in_array($_storageType,['update', 'duplicate']))
        // Recupero gli elementi gia selezionati e li setto nella select
        $.ajax({
            type: 'GET',
            url: '{{siteUrl("/admin/asyncSelectedData")}}',
            data: {
                id: $('#_regulationIds').val(),
                model: 10,
                institution_id: institutionId,
            },
            error: function (jqXHR, status) {
                let response = parseJson(jqXHR.responseText);

                // Funzione che genera il toast con gli errori
                {{-- (vedere nel footer) --}}
                createValidatorFormErrorToast(
                    'Ops c\'è un problema.Si prega di contattare l\'assistenza clienti.',
                    5000,
                    'Validatore select'
                );
            },
        }).then(function (data) {
            let item = data.data.selected;
            // Creo l'opzione e l'appendo alla select
            for (const el of item) {
                var option = new Option(String(el.text), el.id, true, true);
                $dropdownRegulation.append(option).trigger('change');
            }
        });
        @endif
        {{-- End Select2 campo "Regolamenti correlati" --}}

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
        CKEDITOR.replace('input_description');
        CKEDITOR.replace('input_derogations');

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
                createValidatorFormSuccessToast(response.data.message, 'Interventi straordinari');

                {{-- Controllo se non sono all'interno di un modale --}}
                @if(empty($is_box))
                setTimeout(function () {
                    window.location.href = '{{ siteUrl('admin/intervention') }}';
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