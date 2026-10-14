{{-- Form store Oneri informativi e obblighi --}}
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
                        Aggiunta Oneri informativi e obblighi
                    @elseif($_storageType === 'update')
                        Modifica Oneri informativi e obblighi
                    @else
                        Duplicazione Oneri informativi e obblighi
                    @endif
                </span>
                @if(empty($is_box))
                    <div class="card-tools">
                        <ul class="nav nav-pills ml-auto">
                            <li class="nav-item">
                                <a href="{{ siteUrl('admin/charge') }}" title="Torna indietro"
                                   class="btn btn-default btn-sm btn-outline-primary">
                                    <i class="fas fa-caret-left"></i> Torna a elenco Oneri
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
                            </div>
                            <hr class="patos-hr">


                            <div class="form-row d-flex align-items-end">
                                {{-- Inclusione di librerie esterne --}}

                            </div>

                            <div class="form-group">
                                {{-- Campo Denominazione o titolo --}}
                                <label for="title">Denominazione o titolo *</label>
                                {{ form_input([
                                    'name' => 'title',
                                    'value' => !empty($charge['title']) ? (($_storageType === 'duplicate') ? 'Copia di ' . $charge['title'] : $charge['title']) : null,
                                    'placeholder' => 'Denominazione o titolo',
                                    'id' => 'input_title',
                                    'class' => 'form-control input_title',
                                ]) }}
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Per cittadini --}}
                                <div class="form-group col-md-6">
                                    <label for="citizen">Per Cittadini</label>
                                    <div id="input_citizen">
                                        {{ form_dropdown(
                                            'citizen',
                                            [''=>'',0=>'No',1=>'Si'],
                                            @$charge['citizen'],
                                            'class="ts-citizen" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>

                                {{-- Campo Per Imprese --}}
                                <div class="form-group col-md-6">
                                    <label for="companies">Per Imprese</label>
                                    <div id="input_companies">
                                        {{ form_dropdown(
                                            'companies',
                                            [''=>'',0=>'No',1=>'Si'],
                                            @$charge['companies'],
                                            'class="ts-companies" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>
                            </div>


                            <div class="form-row d-flex align-items-end">
                                @if($_storageType == 'update' && strtotime($charge['created_at']) < '1687277700')
                                    {{-- Campo Tipologia --}}
                                    <div class="form-group col-md-6">
                                        <label for="type">Tipologia *</label>
                                        <div id="input_type">
                                            {{ form_dropdown(
                                                'type',
                                                [
                                                    ''=>'',
                                                    'onere'=>'Pubblica in Oneri informativi per cittadini ed imprese',
                                                    'obbligo'=>'Pubblica in Scadenzario obblighi amministrativi'
                                                ],
                                                @$charge['type'],
                                                'class="ts-type" style="width: 100%;"'
                                            ) }}
                                        </div>
                                    </div>
                                @else
                                    <input type="hidden" id="input_type" name="type" value="obbligo" />
                                @endif

                                {{-- Campo Data di scadenza --}}
                                <div class="form-group col-md-6">
                                    <label for="expiration_date">Data di scadenza</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="expiration_date"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control"
                                               value="{{ @$expiration_date }}"
                                               id="expiration_date">
                                    </div>
                                </div>
                            </div>

                            {{generateSeparator('Informazioni collegate')}}

                            {{-- Campo Procedimenti associati --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-9">
                                    <label for="proceedings">Procedimenti associati</label>
                                    <div id="input_proceedings">
                                        {{ form_dropdown(
                                            'proceedings[]',
                                            '',
                                            '',
                                            'class="ts-proceedings" multiple="multiple" data-placeholder="Seleziona o cerca procedimenti..." style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>

                                <div class="form-group col-md-3">
                                    {{ form_button([
                                        'name' => 'add',
                                        'id' => 'btn_addProceeding',
                                        'class' => 'btn btn-outline-primary open-modal',
                                        'style' => 'width:100%;',
                                        'data-url' => siteUrl('admin/proceeding/create-box')
                                    ],'Aggiungi nuovo &nbsp; <i class="fas fa-plus-circle"></i>') }}
                                </div>
                            </div>

                            {{-- Campo Provvedimenti associati --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_measures" id="c_ajax_measures">Provvedimenti associati</label>
                                    </div>
                                    <div id="ajax_measures"></div>
                                    <input type="hidden" value="" name="measures" id="input_measures"
                                           class="measures">
                                </div>
                            </div>

                            {{-- Campo Regolamenti associati --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-9">
                                    <label for="regulations">Regolamenti associati</label>
                                    <div id="input_regulations">
                                        {{ form_dropdown(
                                            'regulations[]',
                                            '',
                                            '',
                                            'class="ts-regulations" multiple="multiple" data-placeholder="Seleziona o cerca regolamento..." style="width: 100%;"'
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

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Riferimenti normativi --}}
                                <div class="form-group col-md-9">
                                    <label for="normative_id">Riferimenti normativi</label>
                                    <div id="input_normative_id">
                                        {{ form_dropdown(
                                            'normative_id',
                                            ['' => ''],
                                            @$charge['normative_id'],
                                            'class="ts-normative_id" style="width: 100%; height: unset;"'
                                        ) }}
                                    </div>
                                </div>

                                <div class="form-group col-md-3">
                                    {{ form_button([
                                        'name' => 'add',
                                        'id' => 'btn_addNormative',
                                        'class' => 'btn btn-outline-primary open-modal',
                                        'style' => 'width:100%;',
                                        'data-url' => !empty($charge['normative_id']) ? siteUrl('admin/normative/edit-box/' . $charge['normative_id']) : siteUrl('admin/normative/create-box')
                                    ],!empty($charge['normative_id']) ? 'Modifica &nbsp; <i class="far fa-edit"></i>' : 'Aggiungi nuovo &nbsp; <i class="fas fa-plus-circle"></i>') }}
                                </div>
                            </div>

                            {{generateSeparator('Altre informazioni')}}

                            <div class="form-group">
                                {{-- Campo Contenuto --}}
                                <label for="description">Contenuto</label>
                                {{form_editor([
                                    'name' => 'description',
                                    'value' => !empty($charge['description']) ? $charge['description'] : null,
                                    'id' => 'input_description',
                                    'class' => 'form-control input_description'
                                ]) }}
                            </div>

                            {{-- Campo URL per maggiori informazioni --}}
                            <div class="form-group" id="input_website_url">
                                <label for="info_url">URL per maggiori informazioni</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-link"></i></span>
                                    </div>
                                    {{ form_input([
                                    'name' => 'info_url',
                                    'value' => !empty($charge['info_url']) ? $charge['info_url'] : null,
                                    'placeholder' => 'https://www.',
                                    'id' => 'input_info_url',
                                    'class' => 'form-control input_info_url',
                                ]) }}
                                </div>
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

        @if(!empty($charge['id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'id',
                'value' => $charge['id'],
                'id' => 'charge_id',
                'class' => 'charge_id',
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

        @if(!empty($regulationIds))
            {{ form_input([
                'type' => 'hidden',
                'name' => '_regulationIds',
                'value' => implode(',',$regulationIds),
                'id' => '_regulationIds',
                'class' => '_regulationIds',
            ]) }}
        @endif

        @if(!empty($charge['normative_id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => '_normative_id',
                'value' => $charge['normative_id'],
                'id' => '_normative_id',
                'class' => '_normative_id',
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

{{ js(verifyFirstOrLastSlash('admin/get/config.js')) }}

<script type="text/javascript">
    //Previene il salvataggio quando si preme invio e il focus non è sul pulsante di salvataggio
    document.getElementById('{{ $formSettings['id'] }}').addEventListener('keydown', e => {
        if (e.key === 'Enter' && e.target.tagName !== 'TEXTAREA') {
            e.preventDefault();
        }
    });

    $(document).ready(function () {

        let formModified = false;
       let institutionId = document.getElementById('institution_id').value ?? '';

        {{-- Campo TomSelect per tipo Onere --}}
        if (document.querySelector('.ts-type')) {
            new TomSelect('.ts-type', {
                ...TS_SINGLE_CLEAR,
                placeholder: 'Seleziona il tipo....'
            });
        }

        {{-- Begin campi TomSelect --}}
        {{-- Begin TomSelect campo "Procedimenti associati" --}}
        createAjaxTomSelect({
            selector: '.ts-proceedings',
            searchUrl: '{{ siteUrl("/admin/asyncData") }}',
            selectedUrl: '{{ siteUrl("/admin/asyncSelectedData") }}',
            selectedIdsSelector: '#_proceedingIds',
            model: 6,
            institutionId,
            placeholder: 'Seleziona o cerca procedimenti...',
            loadSelected: {{ in_array($_storageType, ['update', 'duplicate']) ? 'true' : 'false' }},
            onError: function () {
                createValidatorFormErrorToast(
                    'Ops c\'è un problema.Si prega di contattare l\'assistenza clienti.',
                    5000,
                    'Validatore select'
                );
            }
        });
        {{-- End TomSelect campo "Procedimenti associati" --}}

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
            setInputDataValue: '#input_measures',
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
            label: '#c_ajax_measures'
        });

        // Setto il bando relativo se sono in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($charge['measures']))
        @foreach($charge['measures'] as $measure)
        @php
            $tmpStartDate = !empty($measure['date']) ? ' - '.date('d/m/Y', strtotime($measure['date'])) : null;
        @endphp
        measures.patOsAjaxPagination.setValue('{{ $measure['id'] }}', '{{ htmlEscape($measure['object']).' - '.htmlEscape($measure['number']).''.$tmpStartDate }}', true);
        @endforeach
        @endif

        {{-- Begin TomSelect campo "Regolamenti associati" --}}
        createAjaxTomSelect({
            selector: '.ts-regulations',
            searchUrl: '{{ siteUrl("/admin/asyncData") }}',
            selectedUrl: '{{ siteUrl("/admin/asyncSelectedData") }}',
            selectedIdsSelector: '#_regulationIds',
            model: 10,
            institutionId,
            placeholder: 'Seleziona o cerca regolamento...',
            loadSelected: {{ in_array($_storageType, ['update', 'duplicate']) ? 'true' : 'false' }},
            onError: function () {
                createValidatorFormErrorToast(
                    'Ops c\'è un problema.Si prega di contattare l\'assistenza clienti.',
                    5000,
                    'Validatore select'
                );
            }
        });
        {{-- End TomSelect campo "Regolamenti associati" --}}

        {{-- Begin TomSelect campo "Riferimenti normativi" --}}
        createAjaxTomSelect({
            selector: '.ts-normative_id',
            searchUrl: '{{ siteUrl("/admin/asyncData") }}',
            selectedUrl: '{{ siteUrl("/admin/asyncSelectedData") }}',
            selectedIdsSelector: '#_normative_id',
            model: 12,
            institutionId,
            maxItems: 1,
            placeholder: 'Seleziona o cerca normativa....',
            loadSelected: {{ in_array($_storageType, ['update', 'duplicate']) ? 'true' : 'false' }},
            onChange(value) {
                if (value) {
                    $('#btn_addNormative').attr('data-url', '{{ baseUrl('admin/normative/edit-box/') }}' + value);
                    $('#btn_addNormative').empty().append('Modifica &nbsp; <i class="far fa-edit"></i>');
                } else {
                    $('#btn_addNormative').attr('data-url', '{{ siteUrl('admin/normative/create-box') }}');
                    $('#btn_addNormative').empty().append('Aggiungi nuovo &nbsp; <i class="fas fa-plus-circle"></i>');
                }
            },
            onError: function () {
                createValidatorFormErrorToast(
                    'Ops c\'è un problema.Si prega di contattare l\'assistenza clienti.',
                    5000,
                    'Validatore select'
                );
            }
        });
        {{-- End TomSelect campo "Riferimenti normativi" --}}

        {{-- Campo TomSelect Per i cittadini --}}
        new TomSelect('.ts-citizen', {
            ...TS_SINGLE_CLEAR,
            placeholder: 'Seleziona'
        });

        {{-- Campo TomSelect per le società --}}
        new TomSelect('.ts-companies', {
            ...TS_SINGLE_CLEAR,
            placeholder: 'Seleziona'
        });
        {{-- End campi TomSelect --}}

        {{-- Campo CKEDITOR --}}
        // Cattura eventuali modifiche fatte sui campi CKEDITOR
        CKEDITOR.on('instanceCreated', function (e) {
            e.editor.on('change', function (event) {
                formModified = true;
            });
        });
        CKEDITOR.replace('input_description', {
            customConfig: '{{ verifyFirstOrLastSlash('assets/common/ckeditor4/config.js')}}',
        });

        {{-- Begin salvataggio --}}
        /**
         * Metodo per il salvataggio
         */
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
                createValidatorFormSuccessToast(response.data.message, 'Oneri informativi e obblighi');

                {{-- Controllo se non sono all'interno di un modale --}}
                @if(empty($is_box))
                setTimeout(function () {
                    window.location.href = '{{ siteUrl('admin/charge') }}';
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

        {{-- Vedere nel footer --}}
        /**
         * Funzione che controlla se sono arrivato in questa pagina dal versioning
         */
        checkIfRestore();
    });
</script>
{% endblock %}
