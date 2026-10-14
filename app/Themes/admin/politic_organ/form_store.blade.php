{{--  Form store organi politici --}}
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
                        Aggiunta organo politico
                    @elseif($_storageType === 'update')
                        Modifica organo politico
                    @else
                        Duplicazione organo politico
                    @endif
                </span>
                <div class="card-tools">
                    <ul class="nav nav-pills ml-auto">
                        <li class="nav-item">
                            <a href="{{ siteUrl('admin/politic-organ') }}" title="Torna indietro"
                               class="btn btn-default btn-sm btn-outline-primary">
                                <i class="fas fa-caret-left"></i> Torna a elenco organi politici
                            </a>
                        </li>
                    </ul>
                </div>
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

                            <div class="form-group">
                                {{-- Campo Denominazione o titolo --}}
                                <label for="denomination">Denominazione *</label>
                                {{ form_input([
                                    'name' => 'denomination',
                                    'value' => @$politicOrgan['denomination'],
                                    'placeholder' => 'Denominazione',
                                    'id' => 'input_denomination',
                                    'class' => 'form-control input_denomination',
                                    'readonly' => 'readonly'
                                ]) }}
                            </div>


                            <div class="form-group col-md-3">
                                <label for="show_personnel">Mostra personale associato all'organo *</label>
                                <div id="input_show_personnel">
                                    {{ form_dropdown(
                                        'show_personnel',
                                        [
                                            0 => 'No',
                                            1 => 'Si',
                                        ],
                                        empty($politicOrgan['show_personnel']) ? 0 : 1,
                                        'class="ts-show_personnel" style="width: 100%;"'
                                    ) }}
                                </div>
                            </div>

                            <div id="personnel-box">

                                <div class="form-row d-flex align-items-end">
                                    <div class="form-group col-md-12">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label for="ajax_personnel" id="m_ajax_personnel_label">Personale associato all'organo</label>
                                        </div>
                                        <div id="ajax_personnel"></div>
                                        <input type="hidden" value="" name="personnel"
                                               id="input_personnel"
                                               class="personnel">
                                    </div>
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="skills">Competenze *</label>
                                {{form_editor([
                                    'name' => 'skills',
                                    'value' => !empty($politicOrgan['skills']) ? $politicOrgan['skills'] : null,
                                    'id' => 'input_skills',
                                    'class' => 'form-control input_skills'
                                ]) }}
                            </div>

                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="management_structure">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_management_structure" id="management_structure_label">Ufficio di livello
                                            dirigenziale</label>
                                    </div>
                                    <div id="ajax_management_structure"></div>
                                    <input type="hidden" value="" name="management_structure"
                                           id="input_management_structure"
                                           class="">
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="non_management_structure">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_non_management_structure" id="non_management_structure_label">Ufficio di
                                            livello non dirigenziale</label>
                                    </div>
                                    <div id="ajax_non_management_structure"></div>
                                    <input type="hidden" value="" name="non_management_structure"
                                           id="input_non_management_structure"
                                           class="">
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="absence_office_reason">Motivazione assenza uffici <small>Da compilare in assenza di uffici di riferimento</small></label>
                                {{ form_input([
                                    'name' => 'absence_office_reason',
                                    'value' => @$politicOrgan['absence_office_reason'],
                                    'placeholder' => 'Motivazioni',
                                    'id' => 'input_absence_office_reason',
                                    'class' => 'form-control input_absence_office_reason'
                                ]) }}
                            </div>


                        </div>
                    </div>
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

        @if(!empty($politicOrgan['section_fo_id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'section_fo_id',
                'value' => $politicOrgan['section_fo_id'],
                'id' => 'section_fo_id',
                'class' => 'section_fo_id',
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
{{ css('datatables-bs4/css/dataTables.bootstrap4.min.css','common') }}
{{ css('datatables-responsive/css/responsive.bootstrap4.min.css','common') }}
{{ css('datatables-buttons/css/buttons.bootstrap4.min.css','common') }}
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

        new TomSelect(".ts-show_personnel", TS_SINGLE_PRESELECT);

        // Cattura eventuali modifiche fatte sui campi CKEDITOR
        CKEDITOR.on('instanceCreated', function (e) {
            e.editor.on('change', function (event) {
                formModified = true;
            });
        });
        CKEDITOR.replace('input_skills',{
            customConfig: '{{ verifyFirstOrLastSlash('assets/common/ckeditor4/config.js')}}',
        });


        let management_structure = $('#ajax_management_structure').patOsAjaxPagination({
            url: config.structure.url,
            textLoad: config.structure.textLoad,
            selectedLabel: 'Struttura selezionata',
            footerTable: config.structure.footerTable,
            classTable: config.structure.classTable,
            hideShowTable: Boolean(<?php echo empty($politicOrgan['management_structure']); ?>),
            hideTable: config.structure.hideTable,
            showTable: config.structure.showTable,
            search_placeholder: config.structure.search_placeholder,
            setInputDataValue: '#input_management_structure',
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
            label: '#management_structure_label'
        });

        // Se sono in modifica o in duplicazione setto i valori il presidente della commissione gia selezionato
        @if(!empty($politicOrgan['management_structure']))
            @foreach($politicOrgan['management_structure'] as $structure)
                management_structure.patOsAjaxPagination.setValue('{{ $structure['id'] }}', '{{ htmlEscape($structure['structure_name']) }}', true);
            @endforeach
        @endif

        let non_management_structure = $('#ajax_non_management_structure').patOsAjaxPagination({
            url: config.structure.url,
            textLoad: config.structure.textLoad,
            selectedLabel: 'Struttura selezionata',
            footerTable: config.structure.footerTable,
            classTable: config.structure.classTable,
            hideShowTable: Boolean(<?php echo empty($politicOrgan['non_management_structure']); ?>),
            hideTable: config.structure.hideTable,
            showTable: config.structure.showTable,
            search_placeholder: config.structure.search_placeholder,
            setInputDataValue: '#input_non_management_structure',
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
            label: '#non_management_structure_label'
        });

        // Se sono in modifica o in duplicazione setto i valori il presidente della commissione gia selezionato
        @if(!empty($politicOrgan['non_management_structure']))
            @foreach($politicOrgan['non_management_structure'] as $structure)
                non_management_structure.patOsAjaxPagination.setValue('{{ $structure['id'] }}', '{{ htmlEscape($structure['structure_name']) }}', true);
            @endforeach
        @endif

        // Tabella per la selezione dei responsabili del provvedimento
        let personnel = $('#ajax_personnel').patOsAjaxPagination({
            url: config.personnel.url,
            textLoad: config.personnel.textLoad,
            selectedLabel: 'Personale associato',
            footerTable: config.personnel.footerTable,
            classTable: config.personnel.classTable,
            hideShowTable: Boolean(<?php echo empty($politicOrgan['politic_personnel']); ?>),
            hideTable: config.personnel.hideTable,
            showTable: config.personnel.showTable,
            search_placeholder: config.personnel.search_placeholder,
            setInputDataValue: '#input_personnel',
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
            label: '#m_ajax_personnel_label'
        });

        // Se sono in modifica o in duplicazione setto i valori dei membri supplenti gia selezionati
        @if(!empty($politicOrgan['politic_personnel']))
            @foreach($politicOrgan['politic_personnel'] as $person)
                personnel.patOsAjaxPagination.setValue('{{ $person['id'] }}', '{{ htmlEscape($person['full_name']) }}', true);
            @endforeach
        @endif


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
                createValidatorFormSuccessToast(response.data.message, 'organo politico');

                setTimeout(function () {
                    window.location.href = '{{ siteUrl('admin/politic-organ') }}';
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
