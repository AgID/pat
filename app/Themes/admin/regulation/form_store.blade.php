{{-- Form store Regolamenti e documentazione --}}
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
                        Aggiunta Regolamento e documentazione
                    @elseif($_storageType === 'update')
                        Modifica Regolamento e documentazione
                    @else
                        Duplicazione Regolamento e documentazione
                    @endif
                </span>
                @if(empty($is_box))
                    <div class="card-tools">
                        <ul class="nav nav-pills ml-auto">
                            <li class="nav-item">
                                <a href="{{ siteUrl('admin/regulation') }}" title="Torna indietro"
                                   class="btn btn-default btn-sm btn-outline-primary">
                                    <i class="fas fa-caret-left"></i> Torna a elenco regolamenti e documentazione
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
                                {{-- Campo Nome del documento --}}
                                <div class="form-group col-md-6" id="input_title">
                                    <label for="title">Nome del documento *</label>
                                    {{ form_input([
                                        'name' => 'title',
                                        'value' => !empty($regulation['title']) ? (($_storageType === 'duplicate') ? 'Copia di ' . $regulation['title'] : $regulation['title']) : null,
                                        'placeholder' => 'Nome del documento',
                                        'id' => 'input_title',
                                        'class' => 'form-control input_title',
                                    ]) }}
                                </div>

                                {{-- Campo Tipologia Documento --}}
                                <div class="form-group col-md-6">
                                    <label for="public_in">Tipo di documento (pubblica in) *</label>
                                    <div class="select2-blue" id="public_in">
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
                                {{-- Campo Numero--}}
                                <div class="form-group col-md-6" id="input_number">
                                    <label for="number">Numero</label>
                                    {{ form_input([
                                        'name' => 'number',
                                        'value' => !empty($regulation['number']) ? $regulation['number'] : null,
                                        'placeholder' => 'Numero',
                                        'id' => 'input_number',
                                        'class' => 'form-control input_number'
                                    ]) }}
                                </div>

                                {{-- Campo Protocollo--}}
                                <div class="form-group col-md-6" id="input_protocol">
                                    <label for="protocol">Protocollo</label>
                                    {{ form_input([
                                        'name' => 'protocol',
                                        'type' => 'number',
                                        'value' => !empty($regulation['protocol']) ? $regulation['protocol'] : null,
                                        'placeholder' => 'Protocollo',
                                        'id' => 'input_protocol',
                                        'class' => 'form-control input_protocol'
                                    ]) }}
                                </div>
                            </div>

                            {{-- Campo Valido per le strutture --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="president">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label for="ajax_structures" id="reg_structures_label">Valido per le strutture</label>
                                    </div>
                                    <div id="ajax_structures"></div>
                                    <input type="hidden" value="" name="structures"
                                           id="input_structures"
                                           class="structures">
                                </div>
                            </div>

                            {{-- Campo Valido per i procedimenti --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-9">
                                    <label for="proceedings">Valido per i procedimenti</label>
                                    <div class="select2-blue" id="input_proceedings">
                                        {{ form_dropdown(
                                            'proceedings[]',
                                            @$proceedings,
                                            @$proceedingIds,
                                            'class="form-control select2-proceedings" multiple="multiple" data-placeholder="Seleziona o cerca procedimenti..." data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>

                                @if(empty($is_box))
                                    <div class="form-group col-md-3">
                                        {{ form_button([
                                            'name' => 'add',
                                            'id' => 'btn_addProceedings',
                                            'class' => 'btn btn-outline-primary open-modal',
                                            'style' => 'width:100%;',
                                            'data-url' => siteUrl('admin/proceeding/create-box')
                                        ],'Aggiungi nuovo &nbsp; <i class="fas fa-plus-circle"></i>') }}
                                    </div>
                                @endif
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Data Emissione--}}
                                <div class="form-group col-md-6">
                                    <label for="issue_date">Data Emissione</label>
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

                                {{-- Campo Ordine --}}
                                <div class="form-group col-md-6">
                                    <label for="order">Ordine di visualizzazione *</label>
                                    {{ form_input([
                                        'type' => 'number',
                                        'name' => 'order',
                                        'value' => !empty($regulation['order']) ? $regulation['order'] : 1,
                                        'placeholder' => 'Ordine di visualizzazione',
                                        'id' => 'input_order',
                                        'class' => 'form-control input_order',
                                    ]) }}
                                </div>
                            </div>

                            <div class="form-group">
                                {{-- Campo Testo di descrizione --}}
                                <label for="description">Testo di descrizione</label>
                                {{form_editor([
                                    'name' => 'description',
                                    'value' => !empty($regulation['description']) ? $regulation['description'] : null,
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

        @if(!empty($regulation['id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'id',
                'value' => $regulation['id'],
                'id' => 'regulation_id',
                'class' => 'regulation_id',
            ]) }}
        @endif

        @if(!empty($proceedingIds))
            {{ form_input([
                'type' => 'hidden',
                'name' => '_proceedingIds',
                'value' => implode(',',$proceedingIds),
                'id' => '_proceedingIds',
                'class' => '_proceedingIds',
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
            label: '#reg_structures_label'
        });

        // Se sono in modifica o in duplicazione setto i valori il presidente della commissione gia selezionato
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($regulation['structures']))
        @foreach($regulation['structures'] as $structure)
        structures.patOsAjaxPagination.setValue('{{ $structure['id'] }}', '{{ htmlEscape($structure['structure_name']).(!empty($structure['parent_name'])?' - '.htmlEscape($structure['parent_name']):'').(!empty($structure['reference_email'])?' - '.$structure['reference_email']:'') }}', true);
        @endforeach
        @endif

        {{-- Begin Select2 campo "Valido per i procedimenti" --}}
        let $dropdownProceedings = $('.select2-proceedings');
        $dropdownProceedings.select2({
            //Recupero i dati per le options della select
            ajax: {
                url: '{{siteUrl("/admin/asyncData")}}',
                type: "get",
                dataType: 'json',
                delay: 250,
                data: function (data) {
                    return {
                        model: 6,
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
            },
            escapeMarkup: function (text) {
                return text;
            },
        });
        @if(in_array($_storageType,['update', 'duplicate']))
        // Recupero gli elementi gia selezionati e li setto nella select
        $.ajax({
            type: 'GET',
            url: '{{siteUrl("/admin/asyncSelectedData")}}',
            data: {
                id: $('#_proceedingIds').val(),
                model: 6,
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
                $dropdownProceedings.append(option).trigger('change');
            }
        });
        @endif
        {{-- End Select2 campo "Valido per i procedimenti" --}}

        {{-- Select2 per campo "Tipo di documento (pubblica in) *" --}}
        let $dropdownPublicIn = $('.select2-public_in');
        $dropdownPublicIn.select2()
        $dropdownPublicIn.on('change', function () {
            $('#public_in').val($(this).val());
        });

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
                createValidatorFormSuccessToast(response.data.message, 'Regolamenti o documentazione');

                {{-- Controllo se non sono all'interno di un modale --}}
                @if(empty($is_box))
                setTimeout(function () {
                    window.location.href = '{{ siteUrl('admin/regulation') }}';
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