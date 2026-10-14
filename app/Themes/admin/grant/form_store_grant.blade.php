{{-- Form store Sovvenzioni e vantaggi economici --}}
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
                        Aggiunta Sovvenzione
                    @elseif($_storageType === 'update')
                        Modifica Sovvenzione
                    @else
                        Duplicazione Sovvenzione
                    @endif
                </span>
                @if(empty($is_box))
                    <div class="card-tools">
                        <ul class="nav nav-pills ml-auto">
                            <li class="nav-item">
                                <a href="{{ siteUrl('admin/grant') }}" title="Torna indietro"
                                   class="btn btn-default btn-sm btn-outline-primary">
                                    <i class="fas fa-caret-left"></i> Torna a elenco Sovvenzioni e vantaggi
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

                            {{-- Campo Oggetto --}}
                            <div class="form-group" id="input_object">
                                <label for="object">Oggetto *</label>
                                {{ form_input([
                                    'name' => 'object',
                                    'value' => !empty($grant['object']) ? (($_storageType === 'duplicate') ? 'Copia di ' . $grant['object'] : $grant['object']) : null,
                                    'placeholder' => 'Oggetto',
                                    'id' => 'input_object',
                                    'class' => 'form-control input_object',
                                ]) }}
                            </div>

                            {{ generateSeparator('Dati del beneficiario') }}

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Nominativo del beneficiario --}}
                                <div class="form-group col-md-6">
                                    <label for="beneficiary_name">Nominativo del beneficiario *</label>
                                    {{ form_input([
                                        'name' => 'beneficiary_name',
                                        'value' => !empty($grant['beneficiary_name']) ? $grant['beneficiary_name'] : null,
                                        'placeholder' => 'Nominativo del beneficiario',
                                        'id' => 'input_beneficiary_name',
                                        'class' => 'form-control input_beneficiary_name',
                                    ]) }}
                                </div>

                                {{-- Campo Dati fiscali non disponibili --}}
                                <div class="form-group col-md-6">
                                    <label for="fiscal_data_not_available">Dati fiscali non disponibili</label>
                                    <div id="input_fiscal_data_not_available">
                                        {{ form_dropdown(
                                            'fiscal_data_not_available',
                                            [0=>'No',1=>'Si'],
                                            @$grant['fiscal_data_not_available'],
                                            'class="ts-fiscal_data_not_available" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>
                            </div>

                            {{-- Campo Dati fiscali --}}
                            <div class="form-group" id="fiscal_data">
                                <label for="fiscal_data">Dati fiscali *</label>
                                {{ form_textarea([
                                    'name' => 'fiscal_data',
                                    'value' => !empty($grant['fiscal_data']) ? $grant['fiscal_data'] : null,
                                    'placeholder' => 'Inserire dati fiscali',
                                    'id' => 'input_fiscal_data',
                                    'class' => 'form-control input_fiscal_data',
                                    'cols' => '10',
                                    'rows' => '4',
                                ]) }}
                            </div>

                            {{-- Campo Omissis --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-7">
                                    <label for="privacy">Omissis (Privacy)</label>
                                    <div id="input_privacy">
                                        {{ form_dropdown(
                                            'privacy',
                                            [0=>'No',1=>'Si'],
                                            @$grant['privacy'],
                                            'class="ts-privacy" style="width: 100%;"'
                                        ) }}
                                        <small class="form-text text-muted">
                                            (
                                            Nasconde Nominativo, Dati fiscali
                                            )
                                        </small>
                                    </div>
                                </div>
                            </div>

                            {{generateSeparator('Informazioni collegate')}}

                            {{-- Campo Struttura organizzativa responsabile --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="object_structures_id">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label for="ajax_structure" id="g_ajax_structure_label">Struttura organizzativa responsabile *</label>
                                    </div>
                                    <div id="ajax_structure"></div>
                                    <input type="hidden" value="" name="object_structures_id"
                                           id="input_object_structures_id"
                                           class="object_structures_id">
                                </div>
                            </div>

                            {{-- Campo Dirigente o funzionario responsabile --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label for="ajax_managers" id="g_ajax_managers_label">Dirigente o funzionario responsabile *</label>
                                    </div>
                                    <div id="ajax_managers"></div>
                                    <input type="hidden" value="" name="managers" id="input_managers"
                                           class="managers">
                                </div>
                            </div>

                            {{generateSeparator('Importo e date di riferimento')}}

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Importo atto di concessione --}}
                                <div class="form-group col-md-6">
                                    <label for="concession_amount">Importo atto di concessione *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-euro-sign"></i></span>
                                        </div>
                                        {{ form_input([
                                        'name' => 'concession_amount',
                                        'value' => !empty($grant['concession_amount']) ? $grant['concession_amount'] : null,
                                        'placeholder' => '',
                                        'id' => 'input_concession_amount',
                                        'class' => 'form-control input_concession_amount a-num-class',
                                    ]) }}
                                    </div>
                                </div>

                                {{-- Campo Data atto di concessione --}}
                                <div class="form-group col-md-6">
                                    <label for="concession_act_date">Data atto di concessione *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="concession_act_date"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control"
                                               value="{{ @$concession_act_date }}"
                                               id="concession_act_date">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Data inizio --}}
                                <div class="form-group col-md-6">
                                    <label for="start_date">Data inizio</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="start_date"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control" value="{{ @$grant['start_date'] }}"
                                               id="start_date">
                                    </div>
                                </div>

                                {{-- Campo Data fine --}}
                                <div class="form-group col-md-6">
                                    <label for="end_date">Data fine</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="end_date"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control" value="{{ @$end_date }}"
                                               id="end_date">
                                    </div>
                                </div>
                            </div>

                            {{ generateSeparator('Altre informazioni') }}

                            {{-- Campo Normativa alla base dell'attribuzione --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-9">
                                    <label for="normatives">Normativa alla base dell'attribuzione</label>
                                    <div id="input_normatives">
                                        {{ form_dropdown(
                                            'normatives[]',
                                            '',
                                            '',
                                            'class="ts-normatives" multiple="multiple" data-placeholder="Seleziona o cerca normativa..." style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>

                                <div class="form-group col-md-3">
                                    {{ form_button([
                                        'name' => 'add',
                                        'id' => 'btn_addNormative',
                                        'class' => 'btn btn-outline-primary open-modal',
                                        'style' => 'width:100%;',
                                        'data-url' => siteUrl('admin/normative/create-box')
                                    ],'Aggiungi nuova &nbsp; <i class="fas fa-plus-circle"></i>') }}
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Regolamento alla base dell'attribuzione --}}
                                <div class="form-group col-md-9">
                                    <label for="object_regulations_id">Regolamento alla base dell'attribuzione</label>
                                    <div id="input_regulation">
                                        {{ form_dropdown(
                                            'object_regulations_id',
                                            ['' => ''],
                                            @$grant['object_regulations_id'],
                                            'class="ts-object_regulations_id" style="width: 100%; height: unset;"'
                                        ) }}
                                    </div>
                                </div>

                                <div class="form-group col-md-3">
                                    {{ form_button([
                                        'name' => 'add',
                                        'id' => 'btn_addRegulation',
                                        'class' => 'btn btn-outline-primary open-modal',
                                        'style' => 'width:100%;',
                                        'data-url' => !empty($grant['object_regulations_id']) ? siteUrl('admin/regulation/edit-box/' . $grant['object_regulations_id']) : siteUrl('admin/regulation/create-box'),
                                    ],!empty($grant['object_regulations_id']) ? 'Modifica &nbsp; <i class="far fa-edit"></i>' : 'Aggiungi nuovo &nbsp; <i class="fas fa-plus-circle"></i>') }}
                                </div>
                            </div>

                            <div class="form-group">
                                {{-- Campo Modalità seguita per l'individuazione del beneficiario --}}
                                <label for="detection_mode">Modalità seguita per l'individuazione del beneficiario
                                    *</label>
                                {{form_editor([
                                    'name' => 'detection_mode',
                                    'value' => !empty($grant['detection_mode']) ? $grant['detection_mode'] : null,
                                    'id' => 'input_detection_mode',
                                    'class' => 'form-control input_test_calendar'
                                ]) }}
                            </div>

                            <div class="form-group">
                                {{-- Campo Note --}}
                                <label for="notes">Note</label>
                                {{form_editor([
                                    'name' => 'notes',
                                    'value' => !empty($grant['notes']) ? $grant['notes'] : null,
                                    'id' => 'input_notes',
                                    'class' => 'form-control input_notes'
                                ]) }}
                            </div>

                            {{-- Campo Inserisci link a Normattiva --}}
                            <div class="form-group" id="input_project_link">
                                <label for="project_link">Link al progetto</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-link"></i></span>
                                    </div>
                                    {{ form_input([
                                    'name' => 'project_link',
                                    'value' => !empty($grant['project_link']) ? $grant['project_link'] : null,
                                    'placeholder' => 'https://www.',
                                    'id' => 'input_project_link',
                                    'class' => 'form-control input_project_link',
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

        @if(!empty($grant['id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'id',
                'value' => $grant['id'],
                'id' => 'grant_id',
                'class' => 'grant_id',
            ]) }}
        @endif

        @if(!empty($normativeIds))
            {{ form_input([
                'type' => 'hidden',
                'name' => '_normativeIds',
                'value' => implode(',',$normativeIds),
                'id' => '_normativeIds',
                'class' => '_normativeIds',
            ]) }}
        @endif

        @if(!empty($grant['object_regulations_id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => '_object_regulations_id',
                'value' => $grant['object_regulations_id'],
                'id' => '_object_regulations_id',
                'class' => '_object_regulations_id',
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

        {{-- In fase di modifica nascondo i dati fiscali, in caso non siano disponibili e viceversa --}}
        @if(empty($grant['fiscal_data_not_available']) || $grant['fiscal_data_not_available'] === 0)
        document.getElementById("fiscal_data").style.display = "block";
        @else
        document.getElementById("fiscal_data").style.display = "none";
        @endif

        {{-- Begin creazione campi TomSelect --}}
        {{-- Campo TomSelect Dati fiscali non disponibili --}}
        new TomSelect('.ts-fiscal_data_not_available', {
            ...TS_SINGLE_PRESELECT,
            onChange(value) {
                if (value === '1') {
                    document.getElementById("fiscal_data").style.display = "none";
                } else if (value === '0') {
                    document.getElementById("fiscal_data").style.display = "block";
                }
            }
        });

        {{-- Campo TomSelect Omissis --}}
        new TomSelect('.ts-privacy', TS_SINGLE_PRESELECT);

        // Tabella per la selezione della struttura organizzativa responsabile
        let structure = $('#ajax_structure').patOsAjaxPagination({
            url: config.structure.url,
            textLoad: config.structure.textLoad,
            selectedLabel: 'Struttura selezionata',
            footerTable: config.structure.footerTable,
            classTable: config.structure.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.structure.hideTable,
            showTable: config.structure.showTable,
            institution_id: institutionId,
            selectedIDs: '<?php echo !empty($grant['structure']) ? implode(",",array_column($grant['structure'], 'id')) : ''; ?>',
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
            label: '#g_ajax_structure_label',
        });

        // Se sono in modifica o in duplicazione setto i valori il presidente della commissione gia selezionato
        @if(!empty($grant['structure']))
        structure.patOsAjaxPagination.setValue('{{ $grant['structure']['id'] }}', '{{ htmlEscape($grant['structure']['structure_name']).(!empty($grant['structure']['parent_name'])?' - '.htmlEscape($grant['structure']['parent_name']):'').(!empty($grant['structure']['reference_email'])?' - '.$grant['structure']['reference_email']:'') }}', true);
        @endif

        // Tabella per la selezione dei vice-presidenti della commissione(da archivio del personale)
        let managers = $('#ajax_managers').patOsAjaxPagination({
            url: config.personnel.url,
            textLoad: config.personnel.textLoad,
            selectedLabel: 'Dirigenti o responsabili selezionati',
            footerTable: config.personnel.footerTable,
            classTable: config.personnel.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.personnel.hideTable,
            showTable: config.personnel.showTable,
            search_placeholder: config.personnel.search_placeholder,
            setInputDataValue: '#input_managers',
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
            label: '#g_ajax_managers_label',
        });

        // Se sono in modifica o in duplicazione setto i valori dei vicepresidenti gia selezionati
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($grant['personnel']))
        @foreach($grant['personnel'] as $manager)
        managers.patOsAjaxPagination.setValue('{{ $manager['id'] }}', '{{ (!empty($manager['title'])?htmlEscape($manager['title']).' - ':'').htmlEscape($manager['full_name']).' - '.htmlEscape($manager['name']).' - '.(!empty($manager['email'])?$manager['email']:'N.D') }}', true);
        @endforeach
        @endif

        createAjaxTomSelect({
            selector: '.ts-normatives',
            searchUrl: '{{ siteUrl("/admin/asyncData") }}',
            selectedUrl: '{{ siteUrl("/admin/asyncSelectedData") }}',
            selectedIdsSelector: '#_normativeIds',
            model: 12,
            institutionId,
            placeholder: 'Seleziona o cerca normativa...',
            loadSelected: {{ in_array($_storageType, ['update', 'duplicate']) ? 'true' : 'false' }},
            onError: function () {
                createValidatorFormErrorToast(
                    'Ops c\'è un problema.Si prega di contattare l\'assistenza clienti.',
                    5000,
                    'Validatore select'
                );
            }
        });

        const regulationTomSelect = createAjaxTomSelect({
            selector: ".ts-object_regulations_id",
            searchUrl: '{{ siteUrl("/admin/asyncData") }}',
            selectedUrl: '{{ siteUrl("/admin/asyncSelectedData") }}',
            selectedIdsSelector: "#_object_regulations_id",
            model: 10,
            institutionId,
            maxItems: 1,
            placeholder: "Seleziona o cerca regolamento...",
            loadSelected: <?php echo (in_array($_storageType, ['update', 'duplicate']) && !empty($grant['object_regulations_id'])) ? 'true' : 'false' ?>,
            onChange(value) {
                const button = document.getElementById("btn_addRegulation");

                if (!button) {
                    return;
                }
                if (value) {
                    button.dataset.url = '{{ baseUrl('admin/regulation/edit-box/') }}' + value;
                    button.innerHTML = 'Modifica &nbsp; <i class="far fa-edit"></i>';
                } else {
                    button.dataset.url = '{{ siteUrl('admin/regulation/create-box') }}';
                    button.innerHTML = 'Aggiungi nuovo &nbsp; <i class="fas fa-plus-circle"></i>';
                }
            },
            onError() {
                createValidatorFormErrorToast("Ops c'è un problema. Si prega di contattare l'assistenza clienti.", 5000, "Validatore select");
            }
        });

        {{-- Begin creazione campi CKEDITOR --}}
        // Cattura eventuali modifiche fatte sui campi CKEDITOR
        CKEDITOR.on('instanceCreated', function (e) {
            e.editor.on('change', function (event) {
                formModified = true;
            });
        });
        CKEDITOR.replace('input_detection_mode', {
             customConfig: '{{ verifyFirstOrLastSlash('assets/common/ckeditor4/config.js')}}',
        });
        CKEDITOR.replace('input_notes', {
            customConfig: '{{ verifyFirstOrLastSlash('assets/common/ckeditor4/config.js')}}',
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
                createValidatorFormSuccessToast(response.data.message, 'Sovvenzione');

                {{-- Controllo se non sono all'interno di un modale --}}
                @if(empty($is_box))
                setTimeout(function () {
                    window.location.href = '{{ siteUrl('admin/grant') }}';
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
</script>
{% endblock %}
