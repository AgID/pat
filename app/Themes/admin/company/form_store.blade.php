{{-- Form store Enti e società controllate --}}
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
                        Aggiunta Società
                    @elseif($_storageType === 'update')
                        Modifica Società
                    @else
                        Duplicazione Società
                    @endif
                </span>
                @if(empty($is_box))
                    <div class="card-tools">
                        <ul class="nav nav-pills ml-auto">
                            <li class="nav-item">
                                <a href="{{ siteUrl('admin/company') }}" title="Torna indietro"
                                   class="btn btn-default btn-sm btn-outline-primary">
                                    <i class="fas fa-caret-left"></i> Torna a elenco enti e società
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

                        <div class="form-row d-flex align-items-end">
                            {{-- Campo Ragione sociale --}}
                            <div class="form-group col-md-6" id="input_company_name">
                                <label for="company_name">Ragione sociale *</label>
                                {{ form_input([
                                    'name' => 'company_name',
                                    'value' => !empty($company['company_name']) ? (($_storageType === 'duplicate') ? 'Copia di ' . $company['company_name'] : $company['company_name']) : null,
                                    'placeholder' => 'Nome',
                                    'id' => 'input_company_name',
                                    'class' => 'form-control input_company_name'
                                ]) }}
                            </div>

                            {{-- Campo Tipo --}}
                            <div class="form-group col-md-6" id="input_typology">
                                <label for="typology">Tipo *</label>
                                {{ form_dropdown(
                                    'typology',
                                    [''=>'',
                                    'ente pubblico vigilato'=>'Ente pubblico vigilato',
                                    'societa partecipata'=>'Società partecipata',
                                    'ente di diritto privato controllato'=>'Ente di diritto privato controllato'],
                                    @$company['typology'],
                                    'class="form-control select2-typology"')
                                }}
                            </div>
                        </div>

                        <div class="form-row d-flex align-items-end">
                            {{-- Campo Misura di partecipazione --}}
                            <div class="form-group col-md-6" id="input_participation_measure">
                                <label for="participation_measure">Misura di partecipazione</label>
                                {{ form_input([
                                    'name' => 'participation_measure',
                                    'value' => !empty($company['participation_measure']) ? $company['participation_measure'] : null,
                                    'placeholder' => 'Misura di partecipazione',
                                    'id' => 'input_participation_measure',
                                    'class' => 'form-control input_participation_measure',
                                ]) }}
                            </div>

                            {{-- Campo Durata dell'impegno --}}
                            <div class="form-group col-md-6" id="input_duration">
                                <label for="duration">Durata dell'impegno</label>
                                {{ form_input([
                                    'name' => 'duration',
                                    'value' => !empty($company['duration']) ? $company['duration'] : null,
                                    'placeholder' => 'Durata dell \'impegno',
                                    'id' => 'input_duration',
                                    'class' => 'form-control input_duration',
                                ]) }}
                            </div>
                        </div>

                        <div class="form-group">
                            {{-- Campo Oneri complessivi (annuale) --}}
                            <label for="year_charges">Oneri complessivi (annuale)</label>
                            {{form_editor([
                                'name' => 'year_charges',
                                'value' => !empty($company['year_charges']) ? $company['year_charges'] : null,
                                'id' => 'input_year_charges',
                                'class' => 'form-control input_year_charges'
                            ]) }}
                        </div>

                        <div class="form-group">
                            {{-- Campo Descrizione delle attività --}}
                            <label for="description">Descrizione delle attività</label>
                            {{form_editor([
                                'name' => 'description',
                                'value' => !empty($company['description']) ? $company['description'] : null,
                                'id' => 'input_description',
                                'class' => 'form-control input_description'
                            ]) }}
                        </div>

                        {{-- Campo Rappresentanti negli organi di governo --}}
                        <div class="form-row d-flex align-items-end">
                            <div class="form-group col-md-12">
                                <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label for="ajax_representatives" id="c_representatives_label">Rappresentanti negli
                                        organi di governo</label>
                                </div>
                                <div id="ajax_representatives"></div>
                                <input type="hidden" value="" name="representatives" id="input_representatives"
                                       class="representatives">
                            </div>
                        </div>

                        <div class="form-group">
                            {{-- Campo Incarichi amministrativi e relativo trattamento economico --}}
                            <label for="treatment_assignments">Incarichi amministrativi e relativo trattamento
                                economico</label>
                            {{form_editor([
                                'name' => 'treatment_assignments',
                                'value' => !empty($company['treatment_assignments']) ? $company['treatment_assignments'] : null,
                                'id' => 'input_treatment_assignments',
                                'class' => 'form-control input_treatment_assignments'
                            ]) }}
                        </div>

                        {{-- Campo Url sito web --}}
                        <div class="form-group" id="input_website_url">
                            <label for="website_url"> Url sito web</label>
                            <div class="input-group">
                                <div class="input-group-prepend">
                                    <span class="input-group-text"><i class="fas fa-link"></i></span>
                                </div>
                                {{ form_input([
                                'name' => 'website_url',
                                'value' => !empty($company['website_url']) ? $company['website_url'] : null,
                                'placeholder' => 'https://www.',
                                'id' => 'input_website_url',
                                'class' => 'form-control input_website_url',
                            ]) }}
                            </div>
                        </div>

                        <div class="form-group">
                            {{-- Campo Risultati di bilancio (ultimi 3 anni) --}}
                            <label for="balance">Risultati di bilancio (ultimi 3 anni)</label>
                            {{form_editor([
                                'name' => 'balance',
                                'value' => !empty($company['balance']) ? $company['balance'] : null,
                                'id' => 'input_balance',
                                'class' => 'form-control input_balance'
                            ]) }}
                        </div>

                        <div class="form-row d-flex align-items-end">
                            {{-- Campo Dichiarazione sulla insussistenza di una delle cause di inconferibilità dell'incarico (link) --}}
                            <div class="form-group col-md-6">
                                <label for="inconferability_dec_link">Dichiarazione sulla insussistenza di una delle
                                    cause di inconferibilità dell'incarico (link)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-link"></i></span>
                                    </div>
                                    {{ form_input([
                                    'name' => 'inconferability_dec_link',
                                    'value' => !empty($company['inconferability_dec_link']) ? $company['inconferability_dec_link'] : null,
                                    'placeholder' => 'https://www.',
                                    'id' => 'input_inconferability_dec_link',
                                    'class' => 'form-control input_inconferability_dec_link',
                                ]) }}
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                {{-- Campo Dichiarazione sulla insussistenza di una delle cause di incompatibilità al conferimento dell'incarico (link) --}}
                                <label for="incompatibility_dec_link">Dichiarazione sulla insussistenza di una delle
                                    cause di incompatibilità al conferimento dell'incarico (link)</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-link"></i></span>
                                    </div>
                                    {{ form_input([
                                    'name' => 'incompatibility_dec_link',
                                    'value' => !empty($company['incompatibility_dec_link']) ? $company['incompatibility_dec_link'] : null,
                                    'placeholder' => 'https://www.',
                                    'id' => 'input_incompatibility_dec_link',
                                    'class' => 'form-control input_incompatibility_dec_link',
                                ]) }}
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
            <div class="card-footer" id="__save_">
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

        @if(!empty($company['id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'id',
                'value' => $company['id'],
                'id' => 'company_id',
                'class' => 'company_id',
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

        /**
         * Funzione che controlla se sono arrivato in questa pagina dal versioning
         */
        {{-- Vedere nel footer --}}
        checkIfRestore();

        let formModified = false;
        let institutionId = $('#institution_id').val();

        {{-- Begin metodi per CKEDITOR --}}
        // Cattura eventuali modifiche fatte sui campi CKEDITOR
        CKEDITOR.on('instanceCreated', function (e) {
            e.editor.on('change', function (event) {
                formModified = true;
            });
        });
        CKEDITOR.replace('input_year_charges');
        CKEDITOR.replace('input_description');
        CKEDITOR.replace('input_treatment_assignments');
        CKEDITOR.replace('input_balance');
        {{-- End metodi per CKEDITOR --}}

        // Tabella per la selezione dei "Rappresentanti negli organi di governo"
        let representatives = $('#ajax_representatives').patOsAjaxPagination({
            url: config.personnel.url,
            textLoad: config.personnel.textLoad,
            selectedLabel: 'Rappresentanti selezionati',
            footerTable: config.personnel.footerTable,
            classTable: config.personnel.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.personnel.hideTable,
            showTable: config.personnel.showTable,
            search_placeholder: config.personnel.search_placeholder,
            setInputDataValue: '#input_representatives',
            // 'setInputDataValueOnlyId' : false,
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
            label: '#c_representatives_label'
        });

        // Setto il bando relativo se sono in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($company['representatives']))
        @foreach($company['representatives'] as $representatives)
        representatives.patOsAjaxPagination.setValue('{{ $representatives['id'] }}', '{{ (!empty($representatives['title'])?htmlEscape($representatives['title']).' - ':'').htmlEscape($representatives['full_name']).' - '.htmlEscape($representatives['name']).' - '.(!empty($representatives['email'])?$representatives['email']:'N.D') }}', true);
        @endforeach
        @endif

        {{-- Begin metodi per campi Select --}}
        {{-- Select2 campo "Tipo" --}}
        let $dropdownTypology = $('.select2-typology');
        $dropdownTypology.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        {{-- Select2 campo "Stato pubblicazione" --}}
        let $dropdownPublishingStatus = $('.select2-publishing_status');
        $dropdownPublishingStatus.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });
        {{-- End metodi per campi Select --}}

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
                createValidatorFormSuccessToast(response.data.message, 'Società');

                setTimeout(function () {
                    window.location.href = '{{ siteUrl('admin/company') }}';
                }, 800);

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

    });
</script>
{% endblock %}