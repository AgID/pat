{{-- Form store Lotto --}}
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
                        Aggiunta Lotto
                    @elseif($_storageType === 'update')
                        Modifica Lotto
                    @else
                        Duplicazione Lotto
                    @endif
                </span>
                <div class="card-tools">
                    <ul class="nav nav-pills ml-auto">
                        <li class="nav-item">
                            <a href="{{ siteUrl('admin/contests-act') }}" title="Torna indietro"
                               class="btn btn-default btn-sm btn-outline-primary">
                                <i class="fas fa-caret-left"></i> Torna a elenco Bandi, Gare e Contratti
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

                            {{-- Campo Bando di gara relativo --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="input_notice">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                    <label for="ajax_notice" id="l_notice_label">Bando relativo *</label>
                                    </div>
                                    <div id="ajax_notice"></div>
                                    <input type="hidden" value="" name="relative_notice_id" id="relative_notice_id"
                                           class="relative_notice_id">
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Oggetto --}}
                                <div class="form-group col-md-9">
                                    <label for="object" id="obj-label">Oggetto * </label>
                                    {{ form_input([
                                        'name' => 'object',
                                        'value' => !empty($lot['object']) ? (($_storageType === 'duplicate') ? 'Copia di ' . $lot['object'] : $lot['object']) : null,
                                        'placeholder' => 'Oggetto',
                                        'id' => 'input_object',
                                        'class' => 'form-control input_object'
                                    ]) }}
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Codice CIG --}}
                                <div class="form-group col-md-6">
                                    <label for="cig" id="cig-label">Codice CIG </label>
                                    {{ form_input([
                                        'name' => 'cig',
                                        'value' => !empty($lot['cig']) ? $lot['cig'] : null,
                                        'placeholder' => 'Codice CIG',
                                        'id' => 'input_cig',
                                        'class' => 'form-control input_cig'
                                    ]) }}
                                </div>

                                {{-- Campo Importo dell'appalto (al netto dell'IVA) --}}
                                <div class="form-group col-md-6">
                                    <label for="asta_base_value">
                                        Importo dell'appalto <small>(al netto dell'IVA)</small>* </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-euro-sign"></i></span>
                                        </div>
                                        {{ form_input([
                                            'name' => 'asta_base_value',
                                            'value' => ( !empty($lot['asta_base_value']) || (!empty($lot['asta_base_value']) && $lot['asta_base_value'] == '0') ) ? $lot['asta_base_value'] : null,
                                            'placeholder' => '',
                                             'id' => 'input_asta_base_value',
                                             'class' => 'form-control input_asta_base_value a-num-class',
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

        {{ form_input([
           'type' => 'hidden',
           'name' => '__ignore_cig',
           'value' => 0,
           'id' => '__ignore_cig',
           'class' => '__ignore_cig',
       ]) }}

        @if(!empty($lot['id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'id',
                'value' => $lot['id'],
                'id' => 'lot_id',
                'class' => 'lot_id',
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
{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
{{ css('datetimepicker/jquery.datetimepicker.min.css','common') }}
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
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

        // Per il tooltip di info nel form
        let infoSpan = '<span class="far fa-question-circle fa-xs" data-toggle="tooltip" data-placement="right" ';
        infoSpan += 'data-original-title="Campo pubblicato e comunicato ad ANAC ai fini dell\'art.1 comma 32 Legge n. 190/2012"></span>';
        $('#obj-label').append(infoSpan);
        $('#cig-label').append(infoSpan);

        // Tabella per la selezione del bando relativo al lotto
        let noticeTable = $('#ajax_notice').patOsAjaxPagination({
            url: config.notice.url,
            textLoad: config.notice.textLoad,
            selectedLabel: 'Bando di gara relativo selezionato',
            footerTable: config.notice.footerTable,
            classTable: config.notice.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.notice.hideTable,
            showTable: config.notice.showTable,
            search_placeholder: config.notice.search_placeholder,
            setInputDataValue: '#relative_notice_id',
            dataParams: {
                model: 31,
                institution_id: institutionId
            },
            columns: config.notice.columns,
            action: {
                type: 'radio',
            },
            dataSource: config.notice.dataSource,
            dateFormat: config.notice.dateFormat,
            published: config.notice.published,
            label: '#l_notice_label'
        });

        // Setto il bando relativo se sono in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($lot['relative_notice']))
        @php
            $tmpActivationTime = !empty($lot['relative_notice']['activation_date']) ? date('d/m/Y', strtotime($lot['relative_notice']['activation_date'])) : null;
            $tmpExpirationTime = !empty($lot['relative_notice']['expiration_date']) ? date('d/m/Y', strtotime($lot['relative_notice']['expiration_date'])) : null;
        @endphp
        noticeTable.patOsAjaxPagination.setValue('{{ $lot['relative_notice']['id'] }}', '{{ $lot['relative_notice']['type'].' - '.htmlEscape($lot['relative_notice']['object']).' - '.$tmpActivationTime.' - '.$tmpExpirationTime }}', true);
        @endif

        {{-- Select2 campo "Stato pubblicazione" --}}
        let $dropdownPublishingStatus = $('.select2-publishing_status');
        $dropdownPublishingStatus.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        {{-- Begin salvataggio --}}
        let btnSend = $('#btn_save');
        $('#{{ $formSettings['id'] }}').ajaxForm({
            method: 'POST',
            beforeSend: function () {
                btnSend.empty().append('{{ __('brn_save_spinner',null,'patos') }}').attr("disabled", true);
                $('.error-toast').remove();
            },
            success: function (data) {
                let response = parseJson(data);

                // Controlle se l'eventuale cig inserito è gia presente o meno e in caso mostro l'alert
                if (response.data.cigs) {
                    // Funzione che mostra l'alert che notifica che il cig inserito è gia presente
                    {{-- (vedere nel footer) --}}
                    checkIfCigsExist(response.data.cigs);
                } else {
                    btnSend.empty().append('{{ __('brn_save',null,'patos') }}');
                    formModified = false;

                    // Funzione che genera il toast con il messaggio di successo
                    {{-- (vedere nel footer) --}}
                    createValidatorFormSuccessToast(response.data.message, 'Lotto');

                    setTimeout(function () {
                        window.location.href = '{{ siteUrl('admin/contests-act') }}';
                    }, 800);
                }
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