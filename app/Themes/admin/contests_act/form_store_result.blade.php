{{-- Form store Esito di gara --}}
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
                        Aggiunta Esito di Gara
                    @elseif($_storageType === 'update')
                        Modifica Esito di Gara
                    @else
                        Duplicazione Esito di Gara
                    @endif
                </span>
                @if(empty($is_box))
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
                                {{-- Campo Oggetto --}}
                                <div class="form-group col-md-12">
                                    <label for="object" id="obj-label">Oggetto * </label>
                                    {{ form_input([
                                        'name' => 'object',
                                        'value' => !empty($result['object']) ? (($_storageType === 'duplicate') ? 'Copia di ' . $result['object'] : $result['object']) : null,
                                        'placeholder' => 'Oggetto',
                                        'id' => 'input_object',
                                        'class' => 'form-control input_object'
                                    ]) }}
                                </div>
                            </div>

                            {{-- Campo Pubblica in --}}
                            <div class="form-group" id="input_contact_personnel">
                                <label for="contact_personnel">Pubblica in</label>
                                <div class="select2-blue">
                                    {{ form_dropdown(
                                        'public_in[]',
                                        @$publicIn,
                                        @$publicInIDs,
                                        'class="form-control select2-public_in" multiple="multiple" data-placeholder="Seleziona" data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                    ) }}
                                </div>
                            </div>

                            {{ generateSeparator('Informazioni collegate') }}

                            {{-- Campo Bando relativo --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="input_notice">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_notice" id="r_notice_label">Bando di gara *</label>
                                    </div>
                                    <div id="ajax_notice"></div>
                                    <input type="hidden" value="" name="notice_id" id="relative_notice_id"
                                           class="relative_notice_id">
                                </div>
                            </div>

                            {{-- Campo Altre procedure --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="procedures">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_procedures" id="r_procedures_label">Altre procedure</label>
                                    </div>
                                    <div id="ajax_procedures"></div>
                                    <input type="hidden" value="" name="procedures" id="other_procedures_id"
                                           class="other_procedures_id">
                                </div>
                            </div>

                            {{ generateSeparator('Importo e date di riferimento') }}

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Valore Importo di aggiudicazione (al lordo degli oneri di sicurezza e al netto dell'IVA) --}}
                                <div class="form-group col-md-6">
                                    <label for="award_amount_value" id="award-amount-value-label">
                                        Valore Importo di aggiudicazione <small>(al lordo degli oneri di sicurezza e al
                                            netto dell'IVA)</small> </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-euro-sign"></i></span>
                                        </div>
                                        {{ form_input([
                                        'name' => 'award_amount_value',
                                        'value' => !empty($result['award_amount_value']) ? $result['award_amount_value'] : null,
                                        'placeholder' => '',
                                        'id' => 'input_award_amount_value',
                                        'class' => 'form-control input_award_amount_value a-num-class'
                                    ]) }}
                                    </div>
                                </div>
                            </div>


                            <div class="form-row d-flex align-items-end">
                                {{-- Data dell'atto --}}
                                <div class="form-group col-md-6">
                                    <label for="act_date">Data dell'atto</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="act_date"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control"
                                               value="{{ @$result['act_date'] }}"
                                               id="input_act_date">
                                    </div>
                                </div>

                                {{-- Campo Data di pubblicazione sul sito --}}
                                <div class="form-group col-md-6">
                                    <label for="activation_date">Data di pubblicazione sul sito *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="datetime-local" name="activation_date"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control"
                                               value="{{ !empty($result['activation_date']) ? date('Y-m-d H:i', strtotime($result['activation_date'])) : null }}"
                                               id="input_activation_date">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Data di effettivo inizio dei lavori, servizi o forniture --}}
                                <div class="form-group col-md-6">
                                    <label for="work_start_date">Data di effettivo inizio dei lavori, servizi o
                                        forniture <span
                                                class="far fa-question-circle fa-xs"
                                                data-toggle="tooltip" data-placement="right"
                                                data-original-title="Campo pubblicato e comunicato ad ANAC ai fini
                                                                    dell'art.1 comma 32 Legge n. 190/2012"></span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="work_start_date"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control"
                                               value="{{ @$work_start_date }}"
                                               id="input_work_start_date">
                                    </div>
                                </div>

                                {{-- Campo Data di ultimazione dei lavori, servizi o forniture --}}
                                <div class="form-group col-md-6">
                                    <label for="work_end_date">Data di ultimazione dei lavori, servizi o forniture <span
                                                class="far fa-question-circle fa-xs"
                                                data-toggle="tooltip" data-placement="right"
                                                data-original-title="Campo pubblicato e comunicato ad ANAC ai fini
                                                                    dell'art.1 comma 32 Legge n. 190/2012"></span></label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="work_end_date"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control"
                                               value="{{ @$work_end_date }}"
                                               id="input_work_end_date">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Aggiudicazione appalto - Data di pubblicazione sulla G.U.U.E. --}}
                                <div class="form-group col-md-6">
                                    <label for="guue_date">Aggiudicazione appalto - Data di pubblicazione sulla
                                        G.U.U.E.</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="guue_date"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control"
                                               value="{{ @$guue_date }}"
                                               id="input_guue_date">
                                    </div>
                                </div>

                                {{-- Campo Aggiudicazione appalto - Data di pubblicazione sulla G.U.R.I. --}}
                                <div class="form-group col-md-6">
                                    <label for="guri_date">Aggiudicazione appalto - Data di pubblicazione sulla
                                        G.U.R.I.</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="guri_date"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control"
                                               value="{{ @$guri_date }}"
                                               id="input_guri_date">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Data di pubblicazione sul sito della Stazione Appaltante --}}
                                <div class="form-group col-md-6">
                                    <label for="contracting_stations_publication_date">Data di pubblicazione sul sito
                                        della Stazione Appaltante</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="contracting_stations_publication_date"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control"
                                               value="{{ @$contracting_stations_publication_date }}"
                                               id="input_contracting_stations_publication_date">
                                    </div>
                                </div>

                                {{-- Tipologia esito --}}
                                <div class="form-group col-md-6">
                                    <label for="typology_result">Tipologia esito</label>
                                    <div class="select2-blue" id="input_typology_result">
                                        {{ form_dropdown(
                                            'typology_result',
                                            [
                                                '' => '',
                                                '1' => 'Appalto aggiudicato per gara sopra soglia comunitaria (pubblicazione su G.U.U.E. + G.U.R.I.)',
                                                '2' => 'Appalto aggiudicato per gara nazionale (pubblicazione su G.U.R.I.)'
                                            ],
                                            @$result['typology_result'],
                                            'class="select2-typology_result" data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>
                            </div>

                            {{generateSeparator('Partecipanti e aggiudicatari')}}

                            {{-- Campo Partecipanti alla gara --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_participants" id="ajax_participants_label">Partecipanti alla
                                            gara </label>
                                    </div>
                                    <div id="ajax_participants"></div>
                                    <input type="hidden" value="" name="participants" id="input_participants"
                                           class="participants">
                                </div>
                            </div>

                            {{-- Campo Aggiudicatari della gara --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_awardees" id="ajax_awardees_label">Aggiudicatari della
                                            gara </label>
                                    </div>
                                    <div id="ajax_awardees"></div>
                                    <input type="hidden" value="" name="awardees" id="input_awardees"
                                           class="awardees">
                                </div>
                            </div>

                            {{generateSeparator('Altre informazioni')}}

                            {{-- Campo Provvedimento --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="measure">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_measure" id="r_measure_label">Provvedimento</label>
                                    </div>
                                    <div id="ajax_measure"></div>
                                    <input type="hidden" value="" name="object_measure_id" id="input_measure_id"
                                           class="object_measure_id">
                                </div>
                            </div>

                            <div class="form-group">
                                {{-- Campo Note --}}
                                <label for="details">Note</label>
                                {{form_editor([
                                    'name' => 'details',
                                    'value' => !empty($result['details']) ? $result['details'] : null,
                                    'id' => 'input_details',
                                    'class' => 'form-control input_details'
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

        @if(!empty($result['id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'id',
                'value' => $result['id'],
                'id' => 'result_id',
                'class' => 'result_id',
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
        if (keyCode === 13 && e.target.tagName != 'TEXTAREA') {
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
        $('#participants-label').append(infoSpan);
        $('#award-amount-value-label').append(infoSpan);
        $('#awardees-label').append(infoSpan);
        $('#ajax_participants_label').append(infoSpan);
        $('#ajax_awardees_label').append(infoSpan);


        // Tabella per la selezione del bando relativo all'esito
        let notice = $('#ajax_notice').patOsAjaxPagination({
            url: config.notice.url,
            textLoad: config.notice.textLoad,
            selectedLabel: 'Bando di gara selezionato',
            footerTable: config.notice.footerTable,
            classTable: config.notice.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.notice.hideTable,
            showTable: config.notice.showTable,
            search_placeholder: config.notice.search_placeholder,
            setInputDataValue: '#relative_notice_id',
            dataParams: {
                model: 32,
                institution_id: institutionId
            },
            columns: config.notice.columns,
            action: {
                type: 'radio',
            },
            dataSource: config.notice.dataSource,
            dateFormat: config.notice.dateFormat,
            published: config.notice.published,
            label: '#r_notice_label'
        });

        // Setto il bando relativo se sono in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($result['relative_notice']))
        @php
            $tmpActivationTime = !empty($result['relative_notice']['activation_date']) ? ' - '.date('d/m/Y', strtotime($result['relative_notice']['activation_date'])) : null;
            $tmpExpirationTime = !empty($result['relative_notice']['expiration_date']) ? ' - '.date('d/m/Y', strtotime($result['relative_notice']['expiration_date'])) : null;
        @endphp
        notice.patOsAjaxPagination.setValue('{{ $result['relative_notice']['id'] }}', '{{ str_replace(' -  - ',' - ',($result['relative_notice']['type'].' - '.htmlEscape($result['relative_notice']['object']).' - '.$result['relative_notice']['cig'].$tmpActivationTime.$tmpExpirationTime)) }}', true);
        @endif

        // Tabella per la selezione delle altre procedure relative all'esito
        let otherProcedures = $('#ajax_procedures').patOsAjaxPagination({
            url: config.notice.url,
            textLoad: config.notice.textLoad,
            selectedLabel: 'Altre procedure selezionate',
            footerTable: config.notice.footerTable,
            classTable: config.notice.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.notice.hideTable,
            showTable: config.notice.showTable,
            search_placeholder: config.notice.search_placeholder,
            setInputDataValue: '#other_procedures_id',
            dataParams: {
                model: 30,
                institution_id: institutionId,
                exclude_id: <?php echo ($_storageType == 'insert') ? 0 : $result['id'] ?>
            },
            columns: config.notice.columns,
            action: {
                type: 'checkbox',
            },
            dataSource: config.notice.dataSource,
            dateFormat: config.notice.dateFormat,
            label: '#r_procedures_label'
        });

        // Se sono presenti setto le procedure gia selezionate in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($result['proceedings']))
        @foreach($result['proceedings'] as $proceeding)
        @php
            $tmpActivationTime = !empty($proceeding['activation_date']) ? ' - '.date('d/m/Y', strtotime($proceeding['activation_date'])) : null;
        $tmpExpirationTime = !empty($proceeding['expiration_date']) ? ' - '.date('d/m/Y', strtotime($proceeding['expiration_date'])) : null;
        @endphp
        otherProcedures.patOsAjaxPagination.setValue('{{ $proceeding['id'] }}', '{{ $proceeding['type'].' - '.htmlEscape($proceeding['object']).' - '.$proceeding['cig'].''.$tmpActivationTime.''.$tmpExpirationTime }}', true);
        @endforeach
        @endif

        // Tabella per la selezione dei partecipanti alla gara
        var participants = $('#ajax_participants').patOsAjaxPagination({
            url: config.supplier.url,
            textLoad: config.supplier.textLoad,
            selectedLabel: 'Partecipanti selezionati',
            footerTable: config.supplier.footerTable,
            classTable: config.supplier.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.supplier.hideTable,
            showTable: config.supplier.showTable,
            search_placeholder: config.supplier.search_placeholder,
            setInputDataValue: '#input_participants',
            dataParams: {
                model: 14,
                institution_id: institutionId
            },
            columns: config.supplier.columns,
            action: {
                type: 'checkbox',
            },
            dataSource: config.supplier.dataSource,
            addRecord: config.supplier.addRecord,
            label: '#ajax_participants_label'
        });

        // Se sono presenti setto i partecipanti in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($result['participants']))
        @foreach($result['participants'] as $participant)
        participants.patOsAjaxPagination.setValue('{{ $participant['id'] }}', '{{ htmlEscape($participant['name']).' - '.((!empty($participant['vat']))?$participant['vat']:'').' - '.$participant['type'] }}', true);
        @endforeach
        @endif

        // Tabella per la selezione degli aggiudicatari della gara
        var awardees = $('#ajax_awardees').patOsAjaxPagination({
            url: config.supplier.url,
            textLoad: config.supplier.textLoad,
            selectedLabel: 'Partecipanti selezionati',
            footerTable: config.supplier.footerTable,
            classTable: config.supplier.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.supplier.hideTable,
            showTable: config.supplier.showTable,
            search_placeholder: config.supplier.search_placeholder,
            setInputDataValue: '#input_awardees',
            dataParams: {
                model: 14,
                institution_id: institutionId
            },
            columns: config.supplier.columns,
            action: {
                type: 'checkbox',
            },
            dataSource: config.supplier.dataSource,
            addRecord: config.supplier.addRecord,
            label: '#ajax_awardees_label'
        });

        // Se sono presenti setto gli aggiudicatari gia selezionati in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($result['awardees']))
        @foreach($result['awardees'] as $awardees)
        awardees.patOsAjaxPagination.setValue('{{ $awardees['id'] }}', '{{ htmlEscape($awardees['name']).' - '.((!empty($awardees['vat']))?$awardees['vat']:'').' - '.$awardees['type'] }}', true);
        @endforeach
        @endif

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
            label: '#r_measure_label'
        });

        // Setto il bando relativo se sono in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($result['relative_measure']))
        @php
            $tmpStartDate = !empty($result['relative_measure']) ? ' - '.date('d/m/Y', strtotime($result['relative_measure']['date'])) : null;
        @endphp
        measure.patOsAjaxPagination.setValue('{{ $result['id'] }}', '{{ htmlEscape($result['relative_measure']['object']).' - '.$result['relative_measure']['number'].''.$tmpStartDate }}', true);
        @endif

        {{-- Campo select tipologia esito --}}
        let $dropdownTypologyResult = $('.select2-typology_result');
        $dropdownTypologyResult.select2({
            minimumResultsForSearch: -1,
            placeholder: 'Seleziona',
            allowClear: true
        });

        {{-- Select2 campo "Stato pubblicazione" --}}
        let $dropdownPublishingStatus = $('.select2-publishing_status');
        $dropdownPublishingStatus.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });
        {{-- END CAMPI SELECT --}}

        {{-- Select2 per campo "Pubblica In" --}}
        let $dropdownPublicIn = $('.select2-public_in');
        $dropdownPublicIn.select2()
        $dropdownPublicIn.on('change', function () {
            $('#public_in').val($(this).val());
        });

        // Se si sta creando un nuovo bando, inizializzo la data di pubblicazione con la data odierna
        if ($('#_storage_type').val() == 'insert') {
            @if(empty($notice['activation_date']))
                $('#input_activation_date').attr('value', "<?= date('d-m-Y H:i') ?>");
            @endif
        }

        {{--Campo CKEDITOR --}}
        // Cattura eventuali modifiche fatte sui campi CKEDITOR
        CKEDITOR.on('instanceCreated', function (e) {
            e.editor.on('change', function (event) {
                formModified = true;
            });
        });
        CKEDITOR.replace('input_details');

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
                createValidatorFormSuccessToast(response.data.message, 'Esito di gara');

                @if(empty($is_box))
                setTimeout(function () {
                    window.location.href = '{{ siteUrl('admin/contests-act') }}';
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