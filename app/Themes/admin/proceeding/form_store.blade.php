{{-- Form store Procedimento --}}
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
                        Aggiunta Procedimento
                    @elseif($_storageType === 'update')
                        Modifica Procedimento
                    @else
                        Duplicazione Procedimento
                    @endif
                </span>
                @if(empty($is_box))
                    <div class="card-tools">
                        <ul class="nav nav-pills ml-auto">
                            <li class="nav-item">
                                <a href="{{ siteUrl('admin/proceeding') }}" title="Torna indietro"
                                   class="btn btn-default btn-sm btn-outline-primary">
                                    <i class="fas fa-caret-left"></i> Torna a elenco procedimenti
                                </a>
                            </li>
                        </ul>
                    </div>
                @endif
            </h4>

            <div class="card-body card-primary">

                {{-- Inclusione di librerie esterne --}}

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

                            {{-- Campo Nome del procedimento --}}
                            <div class="form-group" id="input_name">
                                <label for="name">Nome del procedimento *</label>
                                {{ form_input([
                                    'name' => 'name',
                                    'value' => !empty($proceeding['name']) ? (($_storageType === 'duplicate') ? 'Copia di ' . $proceeding['name'] : $proceeding['name']) : null,
                                    'placeholder' => 'Nome del procedimento',
                                    'id' => 'input_name',
                                    'class' => 'form-control input_name'
                                ]) }}
                            </div>

                            {{-- Campo Descrizione del procedimento --}}
                            <div class="form-group">
                                <label for="description">Descrizione del procedimento</label>
                                {{form_editor([
                                    'name' => 'description',
                                    'value' => !empty($proceeding['description']) ? $proceeding['description'] : null,
                                    'id' => 'input_description',
                                    'class' => 'form-control input_description'
                                ]) }}
                            </div>

                            {{-- Campo Documenti da allegare all'istanza e modulistica necessaria --}}
                            <div class="form-group">
                                <label for="instance_documents">Documenti da allegare all'istanza e modulistica
                                    necessaria</label>
                                {{form_editor([
                                    'name' => 'instance_documents',
                                    'value' => !empty($proceeding['instance_documents']) ? $proceeding['instance_documents'] : null,
                                    'id' => 'input_instance_documents',
                                    'class' => 'form-control input_instance_documents'
                                ]) }}
                            </div>

                            {{ generateSeparator('Personale collegato') }}

                            {{-- Campo Responsabile/i di procedimento --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_responsibles" id="p_responsibles_label">Responsabile/i di
                                            procedimento</label>
                                    </div>
                                    <div id="ajax_responsibles"></div>
                                    <input type="hidden" value="" name="responsibles" id="input_responsible"
                                           class="responsibles">
                                </div>
                            </div>

                            {{-- Campo Responsabile/i di provvedimento --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_measure_responsibles" id="p_measure_responsibles_label">Responsabile/i
                                            di provvedimento</label>
                                    </div>
                                    <div id="ajax_measure_responsibles"></div>
                                    <input type="hidden" value="" name="measure_responsibles"
                                           id="input_measure_responsible"
                                           class="measure_responsibles">
                                </div>
                            </div>

                            {{-- Campo Responsabile/i sostitutivi --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_substitute_responsibles" id="p_substitute_responsibles_label">Responsabile/i
                                            sostitutivi </label>
                                    </div>
                                    <div id="ajax_substitute_responsibles"></div>
                                    <input type="hidden" value="" name="substitute_responsibles"
                                           id="input_substitute_responsibles"
                                           class="substitute_responsibles">
                                </div>
                            </div>

                            {{-- Campo Personale di riferimento (Chi Contattare) --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_to_contacts" id="p_to_contacts">Personale di riferimento (Chi
                                            Contattare)</label>
                                    </div>
                                    <div id="ajax_to_contacts"></div>
                                    <input type="hidden" value="" name="to_contacts" id="input_to_contact"
                                           class="to_contacts">
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-4">
                                    <label for="contact">Visualizzazione del Chi Contattare</label>
                                    <div id="input_contact">
                                        {{ form_dropdown(
                                            'contact',
                                            ['' => '',1=>'Prima l\'ufficio responsabile poi il personale',2=>'Prima il personale poi l\'ufficio responsabile',
                                                3=>'Visualizza solo l\'ufficio responsabile',4=>'Visualizza solo il personale di riferimento'],
                                            @$proceeding['contact'],
                                            'class="ts-contact" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>
                            </div>

                            {{ generateSeparator('Strutture collegate') }}

                            {{-- Campo Uffici responsabili --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="president">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_offices_responsibles" id="p_offices_responsibles_label">Uffici
                                            responsabili *</label>
                                    </div>
                                    <div id="ajax_offices_responsibles"></div>
                                    <input type="hidden" value="" name="offices_responsibles"
                                           id="input_offices_responsible"
                                           class="offices_responsibles">
                                </div>
                            </div>

                            {{-- Campo Ufficio competente all'adozione del provvedimento finale --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="final_measure_office_container">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_final_measure_office" id="p_final_measure_office_label">Ufficio
                                            competente all'adozione del provvedimento finale</label>
                                    </div>
                                    <div id="ajax_final_measure_office"></div>
                                    <input type="hidden" value="" name="final_measure_office"
                                           id="input_final_measure_office"
                                           class="final_measure_office">
                                </div>
                            </div>

                            {{-- Campo Altre strutture associate --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="president">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_other_offices" id="p_other_offices_label">Altre strutture
                                            associate</label>
                                    </div>
                                    <div id="ajax_other_offices"></div>
                                    <input type="hidden" value="" name="other_offices"
                                           id="input_other_office"
                                           class="other_offices">
                                </div>
                            </div>

                            {{generateSeparator('Altre informazioni')}}

                            {{-- Campo Costi e modalità di pagamento --}}
                            <div class="form-group">
                                <label for="costs">Costi e modalità di pagamento </label>
                                {{form_editor([
                                    'name' => 'costs',
                                    'value' => !empty($proceeding['costs']) ? $proceeding['costs'] : null,
                                    'id' => 'input_costs',
                                    'class' => 'form-control input_costs'
                                ]) }}
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Conclusione tramite silenzio assenso --}}
                                <div class="form-group col-md-6">
                                    <label for="silence_consent">Conclusione tramite silenzio assenso</label>
                                    <div id="input_silence_consent">
                                        {{ form_dropdown(
                                            'silence_consent',
                                            [''=>'',0=>'No',1=>'Si'],
                                            @$proceeding['silence_consent'],
                                            'class="ts-silence_consent" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>

                                {{-- Campo Conclusione tramite dichiarazione dell'interessato --}}
                                <div class="form-group col-md-6">
                                    <label for="declaration">Conclusione tramite dichiarazione dell'interessato</label>
                                    <div id="input_declaration">
                                        {{ form_dropdown(
                                            'declaration',
                                            [''=>'',0=>'No',1=>'Si'],
                                            @$proceeding['declaration'],
                                            'class=" ts-declaration" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>
                            </div>

                            {{-- Campo Riferimenti normativi --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-9">
                                    <label for="normative"> Riferimenti normativi </label>
                                    <div id="input_normative">
                                        {{ form_dropdown(
                                            'normatives[]',
                                            '',
                                            '',
                                            'class="ts-normative" multiple="multiple" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>

                                @if(empty($is_box))
                                    <div class="form-group col-md-3">
                                        {{ form_button([
                                            'name' => 'add',
                                            'id' => 'btn_addNormative',
                                            'class' => 'btn btn-outline-primary open-modal',
                                            'style' => 'width:100%;',
                                            'data-url' => siteUrl('admin/normative/create-box')
                                        ],'Aggiungi nuova &nbsp; <i class="fas fa-plus-circle"></i>') }}
                                    </div>
                                @endif
                            </div>

                            {{-- Campo Riferimenti normativi (altro) --}}
                            <div class="form-group">
                                <label for="regulation">Riferimenti normativi (altro)</label>
                                {{form_editor([
                                    'name' => 'regulation',
                                    'value' => !empty($proceeding['regulation']) ? $proceeding['regulation'] : null,
                                    'id' => 'input_regulation',
                                    'class' => 'form-control input_regulation'
                                ]) }}
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Termine di conclusione --}}
                                <div class="form-group col-md-6" id="input_deadline">
                                    <label for="deadline">Termine di conclusione</label>
                                    {{ form_input([
                                        'name' => 'deadline',
                                        'value' => !empty($proceeding['deadline']) ? $proceeding['deadline'] : null,
                                        'placeholder' => 'Termine di conclusione',
                                        'id' => 'input_deadline',
                                        'class' => 'form-control input_deadline'
                                    ]) }}
                                </div>

                                {{-- Campo Strumenti di tutela --}}
                                <div class="form-group col-md-6">
                                    <label for="protection_instruments">Strumenti di tutela</label>
                                    {{ form_input([
                                        'name' => 'protection_instruments',
                                        'value' => !empty($proceeding['protection_instruments']) ? $proceeding['protection_instruments'] : null,
                                        'placeholder' => 'Strumenti di tutela',
                                        'id' => 'input_protection_instruments',
                                        'class' => 'form-control input_protection_instruments'
                                    ]) }}
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Disponibilità del servizio online --}}
                                <div class="form-group col-md-6">
                                    <label for="service_available">Disponibilità del servizio online</label>
                                    <div  id="input_service_available">
                                        {{ form_dropdown(
                                            'service_available',
                                            [0=>'No', 1=>'Si'],
                                            @$proceeding['service_available'],
                                            'class="ts-service_available" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>
                                <?php

                                $isServiceRequired = true;

                                $serviceAsterisk = $isServiceRequired !== false ? ' *' : '';
                                ?>
                                {{-- Campo Url per il servizio online relativo --}}
                                <div class="form-group col-md-6" id="input_url_service">
                                    <label for="url_service">Url per il servizio online
                                        relativo{{ $serviceAsterisk }}</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-link"></i></span>
                                        </div>
                                        {{ form_input([
                                        'name' => 'url_service',
                                        'value' => !empty($proceeding['url_service']) ? $proceeding['url_service'] : null,
                                        'placeholder' => 'https://www.',
                                        'id' => 'input_url_service',
                                        'class' => 'form-control input_url_service',
                                    ]) }}
                                    </div>
                                </div>

                                {{-- Campo Tempi previsti per attivazione del servizio online --}}
                                <div class="form-group col-md-6" id="input_service_time">
                                    <label for="service_time">Tempi previsti per attivazione del servizio
                                        online{{ $serviceAsterisk }}</label>
                                    {{ form_input([
                                        'name' => 'service_time',
                                        'value' => !empty($proceeding['service_time']) ? $proceeding['service_time'] : null,
                                        'placeholder' => 'Tempi previsti per attivazione del servizio online',
                                        'id' => 'input_service_time',
                                        'class' => 'form-control input_service_time'
                                    ]) }}
                                </div>
                            </div>

                            <div class="form-group">
                                <label for="obtaining_info">Modalità con le quali gli interessati possono ottenere le
                                    informazioni relative al procedimento</label>
                                {{form_editor([
                                    'name' => 'obtaining_info',
                                    'value' => !empty($proceeding['obtaining_info']) ? $proceeding['obtaining_info'] : null,
                                    'id' => 'obtaining_info',
                                    'class' => 'form-control obtaining_info'
                                ]) }}
                            </div>

                            {{-- Campo Note --}}
                            <div class="form-group">
                                <label for="notes">Note</label>
                                {{form_editor([
                                    'name' => 'notes',
                                    'value' => !empty($proceeding['notes']) ? $proceeding['notes'] : null,
                                    'id' => 'input_notes',
                                    'class' => 'form-control input_notes'
                                ]) }}
                            </div>


                            <!-- mostro i tempi procedimentali solo per chi li ha già  -->
                            @if($_storageType === 'update' && !empty($proceeding['monitoring_datas']))

                                {{-- Campo Monitoraggio tempi procedimentali --}}
                                <div class="form-group">
                                    <div class="row">
                                        <div class="col-md-12">
                                            <label for="monitoring">Monitoraggio tempi procedimentali</label>
                                        </div>
                                        <div class="col-md-6">
                                            <h5>Dati sul monitoraggio</h5>
                                        </div>
                                        <div class="col-md-6 text-right">
                                            <button type="button" class="btn btn-default btn-sm" data-toggle="modal"
                                                    data-target="#monitoring-modal" data-storage-type="new"
                                                    id="new-data">
                                                Crea nuovo record
                                                <i class="fas fa-plus"></i>
                                            </button>
                                        </div>
                                        <div class="col-md-12 mt-3 table-responsive">
                                            <table class="table table-hover table-bordered table-striped table-sm"
                                                   id="tabele-monitoring">
                                                <thead class="thead-dark">
                                                <tr>
                                                    <th style="width:8%;" class="text-center">Anno</th>
                                                    <th style="width:10%;" class="text-center">Procedimenti conclusi
                                                    </th>
                                                    <th style="width:10%;" class="text-center">Giorni medi conclusione
                                                    </th>
                                                    <th style="width:8%;" class="text-center">Percentuale</th>
                                                    <th style="width:5%;" class="text-center">Azioni</th>
                                                </tr>
                                                </thead>
                                                <tbody id="monitoring-table">
                                                </tbody>
                                            </table>
                                        </div>
                                    </div>
                                </div>

                                <div class="form-row d-flex align-items-end">
                                    {{-- Campo Pubblica automaticamente i dati sul monitoraggio --}}
                                    <div class="form-group col-md-6">
                                        <label for="public_monitoring_proceeding">Pubblica automaticamente i dati sul
                                            monitoraggio</label>
                                        <div id="input_public_monitoring_proceeding">
                                            {{ form_dropdown(
                                                'public_monitoring_proceeding',
                                                [
                                                    '0' => 'No',
                                                    '1' => 'Si',
                                                ],
                                                @$public_monitoring_proceeding,
                                                'class="ts-public_monitoring_proceeding" style="width: 100%;"'
                                            ) }}
                                        </div>
                                    </div>
                                </div>

                            @endif

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

        {{ form_input([
            'type' => 'hidden',
            'name' => '_monitoring',
            'value' => null,
            'id' => '_monitoring',
            'class' => '_monitoring',
        ]) }}

        @if(!empty($proceeding['id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'id',
                'value' => $proceeding['id'],
                'id' => 'proceeding_id',
                'class' => 'proceeding_id',
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

{% include layout/partials/form_modal%}

<!-- mostro i tempi procedimentali solo per chi li ha già  -->
@if($_storageType === 'update' && !empty($proceeding['monitoring_datas']))
    {% include proceeding/monitoring_modal %}
@endif

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
{{ js(verifyFirstOrLastSlash('admin/get/config.js?box='.$is_box)) }}

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

        //Controllo se sono arrivato in questa pagina dal versioning
        {{-- Vedere nel footer --}}
        checkIfRestore();

        //Tabella per la selezione dei responsabili di procedimento
        let responsibles = $('#ajax_responsibles').patOsAjaxPagination({
            url: config.personnel.url,
            textLoad: config.personnel.textLoad,
            selectedLabel: 'Responsabili di procedimento selezionati',
            footerTable: config.personnel.footerTable,
            classTable: config.personnel.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.personnel.hideTable,
            showTable: config.personnel.showTable,
            search_placeholder: config.personnel.search_placeholder,
            setInputDataValue: '#input_responsible',
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
            label: '#p_responsibles_label'
        });

        //Se sono in modifica o in duplicazione setto i valori dei responsabili del procedimento
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($proceeding['responsibles']))
        @foreach($proceeding['responsibles'] as $responsible)
        responsibles.patOsAjaxPagination.setValue('{{ $responsible['id'] }}', '{{ (!empty($responsible['title'])?htmlEscape($responsible['title']).' - ':'').htmlEscape($responsible['full_name'].' - '.$responsible['name']).' - '.(!empty($responsible['email'])?$responsible['email']:'N.D') }}', true);
        @endforeach
        @endif

        // Tabella per la selezione dei responsabili del provvedimento
        let measureResponsibles = $('#ajax_measure_responsibles').patOsAjaxPagination({
            url: config.personnel.url,
            textLoad: config.personnel.textLoad,
            selectedLabel: 'Responsabili di provvedimento selezionati',
            footerTable: config.personnel.footerTable,
            classTable: config.personnel.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.personnel.hideTable,
            showTable: config.personnel.showTable,
            search_placeholder: config.personnel.search_placeholder,
            setInputDataValue: '#input_measure_responsible',
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
            label: '#p_measure_responsibles_label'
        });

        //Se sono in modifica o in duplicazione setto i valori dei responsabili del provvedimento
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($proceeding['measure_responsibles']))
        @foreach($proceeding['measure_responsibles'] as $measureResponsible)
        measureResponsibles.patOsAjaxPagination.setValue('{{ $measureResponsible['id'] }}', '{{ (!empty($measureResponsible['title'])?htmlEscape($measureResponsible['title']).' - ':'').htmlEscape($measureResponsible['full_name']).' - '.htmlEscape($measureResponsible['name']).' - '.(!empty($measureResponsible['email'])?$measureResponsible['email']:'N.D') }}', true);
        @endforeach
        @endif

        //Tabella per la selezione dei responsabili del provvedimento
        let substituteResponsibles = $('#ajax_substitute_responsibles').patOsAjaxPagination({
            url: config.personnel.url,
            textLoad: config.personnel.textLoad,
            selectedLabel: 'Responsabili sostitutivi selezionati',
            footerTable: config.personnel.footerTable,
            classTable: config.personnel.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.personnel.hideTable,
            showTable: config.personnel.showTable,
            search_placeholder: config.personnel.search_placeholder,
            setInputDataValue: '#input_substitute_responsibles',
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
            label: '#p_substitute_responsibles_label'
        });

        //Se sono in modifica o in duplicazione setto i valori dei responsabili del provvedimento
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($proceeding['substitute_responsibles']))
        @foreach($proceeding['substitute_responsibles'] as $substituteResponsible)
        substituteResponsibles.patOsAjaxPagination.setValue('{{ $substituteResponsible['id'] }}', '{{ (!empty($substituteResponsible['title'])?htmlEscape($substituteResponsible['title']).' - ':'').htmlEscape($substituteResponsible['full_name']).' - '.htmlEscape($substituteResponsible['name']).' - '.(!empty($substituteResponsible['email'])?$substituteResponsible['email']:'N.D') }}', true);
        @endforeach
        @endif

        //Tabella per la selezione dei responsabili del provvedimento
        let toContact = $('#ajax_to_contacts').patOsAjaxPagination({
            url: config.personnel.url,
            textLoad: config.personnel.textLoad,
            selectedLabel: 'Personale di riferimento selezionato',
            footerTable: config.personnel.footerTable,
            classTable: config.personnel.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.personnel.hideTable,
            showTable: config.personnel.showTable,
            search_placeholder: config.personnel.search_placeholder,
            setInputDataValue: '#input_to_contact',
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
            label: '#p_to_contacts'
        });

        //Se sono in modifica o in duplicazione setto i valori dei responsabili del provvedimento
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($proceeding['to_contacts']))
        @foreach($proceeding['to_contacts'] as $toContact)
        toContact.patOsAjaxPagination.setValue('{{ $toContact['id'] }}', '{{ (!empty($toContact['title'])?htmlEscape($toContact['title']).' - ':'').htmlEscape($toContact['full_name'].' - '.$toContact['name']).' - '.(!empty($toContact['email'])?$toContact['email']:'N.D') }}', true);
        @endforeach
        @endif

        // Tabella per la selezione della struttura di appartenenza
        let officesResponsibles = $('#ajax_offices_responsibles').patOsAjaxPagination({
            url: config.structure.url,
            textLoad: config.structure.textLoad,
            selectedLabel: 'Uffici selezionati',
            footerTable: config.structure.footerTable,
            classTable: config.structure.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.structure.hideTable,
            showTable: config.structure.showTable,
            search_placeholder: config.structure.search_placeholder,
            setInputDataValue: '#input_offices_responsible',
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
            label: '#p_offices_responsibles_label'
        });

        // Se sono in modifica o in duplicazione setto i valori il presidente della commissione gia selezionato
        @if(!empty($proceeding['offices_responsibles']))
        @foreach($proceeding['offices_responsibles'] as $responsible)
        officesResponsibles.patOsAjaxPagination.setValue('{{ $responsible['id'] }}', '{{ htmlEscape($responsible['structure_name']).(!empty($responsible['parent_name'])?' - '.htmlEscape($responsible['parent_name']):'').(!empty($responsible['reference_email'])?' - '.$responsible['reference_email']:'') }}', true);
        @endforeach
        @endif

        // Tabella per la selezione dell'ufficio competente all'adozione del provvedimento finale
        let finalMeasureOffice = $('#ajax_final_measure_office').patOsAjaxPagination({
            url: config.structure.url,
            textLoad: config.structure.textLoad,
            selectedLabel: 'Ufficio competente selezionato',
            footerTable: config.structure.footerTable,
            classTable: config.structure.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.structure.hideTable,
            showTable: config.structure.showTable,
            search_placeholder: config.structure.search_placeholder,
            setInputDataValue: '#input_final_measure_office',
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
            label: '#p_final_measure_office_label'
        });

        // Se sono in modifica o in duplicazione setto i valori dell'ufficio competente gia selezionato
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($proceeding['final_measure_office']))
        @foreach($proceeding['final_measure_office'] as $structure)
        finalMeasureOffice.patOsAjaxPagination.setValue('{{ $structure['id'] }}', '{{ htmlEscape($structure['structure_name']).(!empty($structure['parent_name'])?' - '.htmlEscape($structure['parent_name']):'').(!empty($structure['reference_email'])?' - '.$structure['reference_email']:'') }}', true);
        @endforeach
        @endif

        // Tabella per la selezione della struttura di appartenenza
        let otherOffices = $('#ajax_other_offices').patOsAjaxPagination({
            url: config.structure.url,
            textLoad: config.structure.textLoad,
            selectedLabel: 'Strutture selezionate',
            footerTable: config.structure.footerTable,
            classTable: config.structure.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.structure.hideTable,
            showTable: config.structure.showTable,
            search_placeholder: config.structure.search_placeholder,
            setInputDataValue: '#input_other_office',
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
            label: '#p_other_offices_label'
        });

        // Se sono in modifica o in duplicazione setto i valori il presidente della commissione gia selezionato
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($proceeding['other_structures']))
        @foreach($proceeding['other_structures'] as $responsible)
        otherOffices.patOsAjaxPagination.setValue('{{ $responsible['id'] }}', '{{ htmlEscape($responsible['structure_name']).(!empty($responsible['parent_name'])?' - '.htmlEscape($responsible['parent_name']):'').(!empty($responsible['reference_email'])?' - '.$responsible['reference_email']:'') }}', true);
        @endforeach
        @endif

        const normativeTomSelect = createAjaxTomSelect({
            selector: '.ts-normative',

            searchUrl: '{{ siteUrl("/admin/asyncData") }}',
            selectedUrl: '{{ siteUrl("/admin/asyncSelectedData") }}',
            selectedIdsSelector: '#_normativeIds',

            model: 12,
            institutionId,
            placeholder: 'Seleziona o cerca...',
            loadSelected: {{ in_array($_storageType, ['update', 'duplicate']) ? 'true' : 'false' }},

            onError(error) {
                console.error('Errore durante il caricamento dei riferimenti normativi:', error);
                createValidatorFormErrorToast('Ops c\'è un problema. Si prega di contattare l\'assistenza clienti.', 5000, 'Validatore select');
            }
        });

        @if($_storageType === 'update' && !empty($proceeding['monitoring_datas']))
            new TomSelect('.ts-public_monitoring_proceeding', TS_SINGLE);
        @endif

        function toggleServiceFields(value) {
            const isAvailable = value !== '0';
            document.getElementById('input_service_time').style.display = isAvailable ? 'none' : 'block';
            document.getElementById('input_url_service').style.display = isAvailable ? 'block' : 'none';
        }
        const serviceAvailable = new TomSelect('.ts-service_available', {
            ...TS_SINGLE,
            onChange(value) {
                toggleServiceFields(value);
            }
        });
        toggleServiceFields(serviceAvailable.getValue());

        new TomSelect('.ts-contact', TS_SINGLE_CLEAR);
        new TomSelect('.ts-silence_consent', TS_SINGLE_CLEAR);
        new TomSelect('.ts-declaration', TS_SINGLE_CLEAR);

        // Cattura eventuali modifiche fatte sui campi CKEDITOR
        CKEDITOR.on('instanceCreated', function (e) {
            e.editor.on('change', function (event) {
                formModified = true;
            });
        });
        CKEDITOR.replace('input_description', {
            customConfig: '{{ verifyFirstOrLastSlash('assets/common/ckeditor4/config.js')}}',
        });
        CKEDITOR.replace('input_instance_documents', {
            customConfig: '{{ verifyFirstOrLastSlash('assets/common/ckeditor4/config.js')}}',
        });
        CKEDITOR.replace('input_costs', {
            customConfig: '{{ verifyFirstOrLastSlash('assets/common/ckeditor4/config.js')}}',
        });
        CKEDITOR.replace('input_regulation', {
            customConfig: '{{ verifyFirstOrLastSlash('assets/common/ckeditor4/config.js')}}',
        });
        CKEDITOR.replace('obtaining_info', {
            customConfig: '{{ verifyFirstOrLastSlash('assets/common/ckeditor4/config.js')}}',
        });
        CKEDITOR.replace('input_notes', {
            customConfig: '{{ verifyFirstOrLastSlash('assets/common/ckeditor4/config.js')}}',
        });
        {{-- Fine metodi per campi CKEditor --}}


        {{-- In fase di modifica nascondo i tempi previsti per l'attivazione del servizio online, in caso non sia disponibile e viceversa --}}
        @if(!empty($proceeding['service_available']) && $proceeding['service_available'] === 1)
        document.getElementById("input_service_time").style.display = "none";
        @else
        document.getElementById("input_service_time").style.display = "block";
        document.getElementById("input_url_service").style.display = "none";
        @endif
        {{-- End dinamicizzazione campi form --}}

        /**
         * Metodo per il salvataggio
         */
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
                createValidatorFormSuccessToast(response.data.message, 'Procedimento');

                @if(empty($is_box))
                setTimeout(function () {
                    window.location.href = '{{ siteUrl('admin/proceeding') }}';
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
                createValidatorFormErrorToast(msg, 6000, '', false);

                btnSend.empty().append('{{ __('brn_save',null,'patos') }}').attr("disabled", false);

            }
        });
        {{-- End salvataggio --}}

        /**
         * Funzione che apre il modale per l'aggiunta di nuovi elementi direttamente all'interno del form
         */
        openModalForm();

        /**
         * Funzione che viene eseguita alla chiusura del modale di aggiunta di nuovi elementi direttamente dal form.
         * Pulisce l'iframe.
         */
        closeModalForm();

        /**
         * Controllo per l'uscita dal form se i campi di input sono stati toccati
         */
        document.addEventListener("focusin", function (event) {
            if (event.target.matches(".ts-control, input")) {
                formModified = true;
            }
        });

        @if(!$is_box)
        {{-- Messaggio di uscita senza salvare dal form --}}
        window.addEventListener('beforeunload', (event) => {
            if (formModified) {
                event.returnValue = 'Vuoi uscire dalla pagina?';
            }
        });
        @endif
    });
</script>
{% endblock %}
