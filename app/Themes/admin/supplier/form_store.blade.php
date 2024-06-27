{{-- Form store Elenco partecipanti/aggiudicatari --}}
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
                        Aggiunta Fornitore
                    @elseif($_storageType === 'update')
                        Modifica Fornitore
                    @else
                        Duplicazione Fornitore
                    @endif
                </span>
                @if(empty($is_box))
                    <div class="card-tools">
                        <ul class="nav nav-pills ml-auto">
                            <li class="nav-item">
                                <a href="{{ siteUrl('admin/supplier') }}" title="Torna indietro"
                                   class="btn btn-default btn-sm btn-outline-primary">
                                    <i class="fas fa-caret-left"></i> Torna a elenco partecipanti/aggiudicatari
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
                                {{-- Campo Tipologia fornitore --}}
                                <div class="form-group col-md-6">
                                    <label for="typology">Tipologia fornitore *</label>
                                    <div class="select2-blue" id="input_typology">
                                        {{ form_dropdown(
                                            'typology',
                                            [1=>'Fornitore singolo',2=>'Raggruppamento'],
                                            @$supplier['typology'],
                                            'class="form-control select2-typology" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Nominativo e ragione sociale--}}
                                <div class="form-group col-md-6" id="input_name">
                                    <label for="name">Nominativo e ragione sociale *</label>
                                    {{ form_input([
                                        'name' => 'name',
                                        'value' => !empty($supplier['name']) ? (($_storageType === 'duplicate') ? 'Copia di ' . $supplier['name'] : $supplier['name']) : null,
                                        'placeholder' => 'Nominativo',
                                        'id' => 'input_name',
                                        'class' => 'form-control input_name',
                                    ]) }}
                                </div>

                                {{-- Campo Tipologia fornitore --}}
                                <div class="form-group col-md-6 singleBox">
                                    <label for="supplier_typology">Tipologia fornitore *</label>
                                    <div class="select2-blue" id="input_supplier_typology">
                                        {{ form_dropdown(
                                            'supplier_typology',
                                            [0=>'Italiano',1=>'Estero'],
                                            @$supplier['it'],
                                            'id="supplier_typology" class="form-control select2-supplier_typology" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end" id="_ita">
                                {{-- Campo Codice fiscale--}}
                                <div class="form-group col-md-6" id="input_vat">
                                    <label for="vat">Codice fiscale *
                                    </label>
                                    {{ form_input([
                                        'name' => 'vat',
                                        'value' => !empty($supplier['vat']) ? $supplier['vat'] : null,
                                        'placeholder' => 'Codice fiscale',
                                        'id' => 'input_vat_field',
                                        'class' => 'form-control input_vat'
                                    ]) }}
                                </div>
                            </div>

                            <div class="singleBox">
                                <div id="foreign">
                                    <div class="form-row d-flex align-items-end">

                                        {{-- Campo Identificativo fiscale estero --}}
                                        <div class="form-group col-md-6" id="input_foreign_tax_identification">
                                            <label for="foreign_tax_identification">Identificativo fiscale estero *
                                            </label>
                                            {{ form_input([
                                                'name' => 'foreign_tax_identification',
                                                'value' => !empty($supplier['foreign_tax_identification']) ? $supplier['foreign_tax_identification'] : null,
                                                'placeholder' => 'Identificativo fiscale estero',
                                                'id' => 'input_foreign_tax_identification_field',
                                                'class' => 'form-control input_foreign_tax_identification'
                                            ]) }}
                                        </div>
                                    </div>
                                </div>


                                <div class="form-row d-flex align-items-end">
                                    {{-- Campo Indirizzo sede --}}
                                    <div class="form-group col-md-6" id="input_addrerss">
                                        <label for="addrerss">Indirizzo sede</label>
                                        {{ form_input([
                                            'name' => 'address',
                                            'value' => !empty($supplier['address']) ? $supplier['address'] : null,
                                            'placeholder' => 'Indirizzo sede',
                                            'id' => 'input_address',
                                            'class' => 'form-control input_address'
                                        ]) }}
                                    </div>

                                    {{-- Campo Recapito telefonico --}}
                                    <div class="form-group col-md-6">
                                        <label for="phone">Recapito telefonico</label>
                                        {{ form_input([
                                            'name' => 'phone',
                                            'value' => !empty($supplier['phone']) ? $supplier['phone'] : null,
                                            'placeholder' => 'Recapito telefonico',
                                            'id' => 'input_phone',
                                            'class' => 'form-control input_phone'
                                        ]) }}
                                    </div>
                                </div>

                                <div class="form-row d-flex align-items-end">
                                    {{-- Campo Indirizzo email --}}
                                    <div class="form-group col-md-6">
                                        <label for="email">Indirizzo email</label>
                                        {{ form_input([
                                            'name' => 'email',
                                            'value' => !empty($supplier['email']) ? $supplier['email'] : null,
                                            'placeholder' => 'Indirizzo email',
                                            'id' => 'input_email',
                                            'class' => 'form-control input_email'
                                        ]) }}
                                    </div>

                                    {{-- Campo Recapito fax --}}
                                    <div class="form-group col-md-6">
                                        <label for="fax">Recapito fax</label>
                                        {{ form_input([
                                            'name' => 'fax',
                                            'value' => !empty($supplier['fax']) ? $supplier['fax'] : null,
                                            'placeholder' => 'Recapito fax',
                                            'id' => 'input_fax',
                                            'class' => 'form-control input_fax'
                                        ]) }}
                                    </div>
                                </div>
                            </div>

                            <div id="groupingBox">

                                {{-- Campo Capogruppo --}}
                                <div class="form-row d-flex align-items-end">
                                    <div class="form-group col-md-12">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label for="ajax_group_leaders" id="_group_leaders_label">Capogruppo</label>
                                        </div>
                                        <div id="ajax_group_leaders"></div>
                                        <input type="hidden" value="" name="group_leaders" id="input_group_leader"
                                               class="group_leaders">
                                    </div>
                                </div>

                                {{-- Campo Mandante --}}
                                <div class="form-row d-flex align-items-end">
                                    <div class="form-group col-md-12">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label for="ajax_principals" id="_principals_label">Mandante
                                            </label>
                                        </div>
                                        <div id="ajax_principals"></div>
                                        <input type="hidden" value="" name="principals" id="input_principal"
                                               class="principals">
                                    </div>
                                </div>

                                {{-- Campo Mandantaria --}}
                                <div class="form-row d-flex align-items-end">
                                    <div class="form-group col-md-12">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label for="ajax_mandatarie" id="_mandatarie_label">Mandataria
                                            </label>
                                        </div>
                                        <div id="ajax_mandatarie"></div>
                                        <input type="hidden" value="" name="mandatarie" id="input_mandataryl"
                                               class="mandatarie">
                                    </div>
                                </div>

                                {{-- Campo Associata --}}
                                <div class="form-row d-flex align-items-end">
                                    <div class="form-group col-md-12">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label for="ajax_associates" id="_associates_label">Associata</label>
                                        </div>
                                        <div id="ajax_associates"></div>
                                        <input type="hidden" value="" name="associates" id="input_associate"
                                               class="associates">
                                    </div>
                                </div>

                                {{-- Campo Consorziata --}}
                                <div class="form-row d-flex align-items-end">
                                    <div class="form-group col-md-12">
                                        <div class="d-flex justify-content-between align-items-center mb-2">
                                            <label for="ajax_consortiums" id="_consortiums_label">Consorziata</label>
                                        </div>
                                        <div id="ajax_consortiums"></div>
                                        <input type="hidden" value="" name="consortiums" id="input_consortium"
                                               class="consortiums">
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

        @if(!empty($supplier['id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'id',
                'value' => $supplier['id'],
                'id' => 'supplier_id',
                'class' => 'supplier_id',
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
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
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
        document.getElementById("groupingBox").style.display = "none";

        let formModified = false;
        let institutionId = $('#institution_id').val();

        {{-- Select2 campo "Tipologia fornitore" --}}
        let $dropdownTypology = $('.select2-typology');
        $dropdownTypology.select2({
            minimumResultsForSearch: -1
        });

        {{-- Select2 campo "Tipologia fornitore" --}}
        let $dropdownSupplierTypology = $('.select2-supplier_typology');
        $dropdownSupplierTypology.select2({
            minimumResultsForSearch: -1
        });

        $('#supplier_typology').on("select2:select", function (e) {
            let data = e.params.data;

            if (data.id == 0) {
                $('#input_vat').show();
                $('#foreign').hide();
            } else if (data.id == 1) {
                $('#input_vat').hide();
                $('#foreign').show();
            }

            if(data.text === 'Italiano'){
                document.getElementById("input_foreign_tax_identification_field").value = "";
                @if(!empty($supplier['vat']))
                document.getElementById("input_vat_field").value = '{{ $supplier['vat'] }}';
                @else
                document.getElementById("input_vat_field").value = null;
                @endif

            }else if (data.text === 'Estero'){
                document.getElementById("input_vat_field").value = "";

                @if(!empty($supplier['foreign_tax_identification']))
                document.getElementById("input_foreign_tax_identification_field").value = '{{ $supplier['foreign_tax_identification'] }}';
                @else
                document.getElementById("input_foreign_tax_identification_field").value = null;
                @endif
            }
        });

        {{-- Begin dinamicizzazione campi form --}}
        {{-- In fase di modifica nascondo i campi in base alla tipologia --}}
        @if(empty($supplier['typology']) || $supplier['typology'] === '1')
        document.getElementById("groupingBox").style.display = "none";
        @if(empty($supplier['it']))
        document.getElementById("input_vat").style.display = "block";
        document.getElementById("foreign").style.display = "none";
        @elseif($supplier['it'] == 1)
        document.getElementById("foreign").style.display = "block";
        document.getElementById("input_vat").style.display = "none";
        @endif
        @else
        $(".singleBox").hide();
        document.getElementById("input_vat").style.display = "none";
        document.getElementById("groupingBox").style.display = "block";
        @endif

        /**
         * Funzione che in base alla tipologia mostra i campi di input
         */
        $('#input_typology').on('select2:select', function (e) {
            let data = e.params.data;
            if (data.text === 'Fornitore singolo') {
                $("label[for='name']").html('Nominativo e ragione sociale *');
                $(".singleBox").show();
                document.getElementById("input_vat").style.display = "block";
                document.getElementById("groupingBox").style.display = "none";
            } else if (data.text === 'Raggruppamento') {
                $("label[for='name']").html('Nominativo del raggruppamento *');
                $(".singleBox").hide();
                document.getElementById("input_vat").style.display = "none";
                document.getElementById("groupingBox").style.display = "block";
            }
        });
        {{-- End dinamicizzazione campi form --}}

        {{-- Select2 campo "Stato pubblicazione" --}}
        let $dropdownPublishingStatus = $('.select2-publishing_status');
        $dropdownPublishingStatus.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        // Tabella per la selezione degli aggiudicatari della gara
        var groupLeaders = $('#ajax_group_leaders').patOsAjaxPagination({
            url: config.supplier.url,
            textLoad: config.supplier.textLoad,
            selectedLabel: 'Capogruppo selezionati',
            footerTable: config.supplier.footerTable,
            classTable: config.supplier.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.supplier.hideTable,
            showTable: config.supplier.showTable,
            search_placeholder: config.supplier.search_placeholder,
            setInputDataValue: '#input_group_leader',
            dataParams: {
                model: 42,
                institution_id: institutionId
            },
            columns: config.supplier.columns,
            action: {
                type: 'checkbox',
            },
            dataSource: config.supplier.dataSource,
            addRecord: config.supplier.addRecord,
            label: '#_group_leaders_label'
        });

        // Se sono presenti setto gli aggiudicatari gia selezionati in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($supplier['group_leaders']))
        @foreach($supplier['group_leaders'] as $groupLeader)
        groupLeaders.patOsAjaxPagination.setValue('{{ $groupLeader['id'] }}', '{{ htmlEscape($groupLeader['name']).' - '.((!empty($groupLeader['vat']))?$groupLeader['vat']:'').' - '.$groupLeader['type'] }}', true);
        @endforeach
        @endif

        // Tabella per la selezione degli aggiudicatari della gara
        var principals = $('#ajax_principals').patOsAjaxPagination({
            url: config.supplier.url,
            textLoad: config.supplier.textLoad,
            selectedLabel: 'Mandanti selezionati',
            footerTable: config.supplier.footerTable,
            classTable: config.supplier.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.supplier.hideTable,
            showTable: config.supplier.showTable,
            search_placeholder: config.supplier.search_placeholder,
            setInputDataValue: '#input_principal',
            dataParams: {
                model: 42,
                institution_id: institutionId
            },
            columns: config.supplier.columns,
            action: {
                type: 'checkbox',
            },
            dataSource: config.supplier.dataSource,
            addRecord: config.supplier.addRecord,
            label: '#_principals_label'
        });

        // Se sono presenti setto gli aggiudicatari gia selezionati in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($supplier['principals']))
        @foreach($supplier['principals'] as $principal)
        principals.patOsAjaxPagination.setValue('{{ $principal['id'] }}', '{{ htmlEscape($principal['name']).' - '.((!empty($principal['vat']))?$principal['vat']:'').' - '.$principal['type'] }}', true);
        @endforeach
        @endif

        // Tabella per la selezione degli aggiudicatari della gara
        var mandataries = $('#ajax_mandatarie').patOsAjaxPagination({
            url: config.supplier.url,
            textLoad: config.supplier.textLoad,
            selectedLabel: 'Mandatari selezionati',
            footerTable: config.supplier.footerTable,
            classTable: config.supplier.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.supplier.hideTable,
            showTable: config.supplier.showTable,
            search_placeholder: config.supplier.search_placeholder,
            setInputDataValue: '#input_mandataryl',
            dataParams: {
                model: 42,
                institution_id: institutionId
            },
            columns: config.supplier.columns,
            action: {
                type: 'checkbox',
            },
            dataSource: config.supplier.dataSource,
            addRecord: config.supplier.addRecord,
            label: '#_mandatarie_label'
        });

        // Se sono presenti setto gli aggiudicatari gia selezionati in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($supplier['mandatarie']))
        @foreach($supplier['mandatarie'] as $mandatarie)
        mandataries.patOsAjaxPagination.setValue('{{ $mandatarie['id'] }}', '{{ htmlEscape($mandatarie['name']).' - '.((!empty($mandatarie['vat']))?$mandatarie['vat']:'').' - '.$mandatarie['type'] }}', true);
        @endforeach
        @endif

        // Tabella per la selezione degli aggiudicatari della gara
        var associates = $('#ajax_associates').patOsAjaxPagination({
            url: config.supplier.url,
            textLoad: config.supplier.textLoad,
            selectedLabel: 'Associati selezionati',
            footerTable: config.supplier.footerTable,
            classTable: config.supplier.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.supplier.hideTable,
            showTable: config.supplier.showTable,
            search_placeholder: config.supplier.search_placeholder,
            setInputDataValue: '#input_associate',
            dataParams: {
                model: 42,
                institution_id: institutionId
            },
            columns: config.supplier.columns,
            action: {
                type: 'checkbox',
            },
            dataSource: config.supplier.dataSource,
            addRecord: config.supplier.addRecord,
            label: '#_associates_label'
        });

        // Se sono presenti setto gli aggiudicatari gia selezionati in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($supplier['associates']))
        @foreach($supplier['associates'] as $associate)
        associates.patOsAjaxPagination.setValue('{{ $associate['id'] }}', '{{ htmlEscape($associate['name']).' - '.((!empty($associate['vat']))?$associate['vat']:'').' - '.$associate['type'] }}', true);
        @endforeach
        @endif

        // Tabella per la selezione degli aggiudicatari della gara
        var consortiums = $('#ajax_consortiums').patOsAjaxPagination({
            url: config.supplier.url,
            textLoad: config.supplier.textLoad,
            selectedLabel: 'Consorziati selezionati',
            footerTable: config.supplier.footerTable,
            classTable: config.supplier.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.supplier.hideTable,
            showTable: config.supplier.showTable,
            search_placeholder: config.supplier.search_placeholder,
            setInputDataValue: '#input_consortium',
            dataParams: {
                model: 42,
                institution_id: institutionId
            },
            columns: config.supplier.columns,
            action: {
                type: 'checkbox',
            },
            dataSource: config.supplier.dataSource,
            addRecord: config.supplier.addRecord,
            label: '#_consortiums_label'
        });

        // Se sono presenti setto gli aggiudicatari gia selezionati in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($supplier['consortiums']))
        @foreach($supplier['consortiums'] as $consortium)
        consortiums.patOsAjaxPagination.setValue('{{ $consortium['id'] }}', '{{ htmlEscape($consortium['name']).' - '.((!empty($consortium['vat']))?$consortium['vat']:'').' - '.$consortium['type'] }}', true);
        @endforeach
        @endif

        /**
         * Metodo per il salvataggio
         */
                {{-- Begin salvataggio --}}
        let btnSend = $('#btn_save');
        $('#{{ $formSettings['id'] }}').ajaxForm({
            method: 'POST',
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
                createValidatorFormSuccessToast(response.data.message, 'Fornitore');

                {{-- Controllo se non sono all'interno di un modale --}}
                @if(empty($is_box))
                setTimeout(function () {
                    window.location.href = '{{ siteUrl('admin/supplier') }}';
                }, 800);
                @else
                {{-- Controllo, se sono all'interno di un modale lo chiudo dopo il salvataggio --}}
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