{{-- Form store Bando di  Gare --}}
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
                        Aggiunta Bando di gara
                    @elseif($_storageType === 'update')
                        Modifica Bando di gara
                    @else
                        Duplicazione Bando di gara
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
                                {{-- Campo ANAC - Anno di riferimento --}}
                                <div class="form-group col-md-6">
                                    <label for="anac_year">ANAC - Anno di riferimento * <span
                                                class="far fa-question-circle fa-xs"
                                                data-toggle="tooltip" data-placement="right"
                                                data-original-title="Selezionare l'anno di riferimento di questa procedura. Questo valore verrà preso in considerazione per includere o meno l'informazione nella creazione del file XML per la comunicazione all'ANAC"></span></label>
                                    <div class="select2-blue" id="input_anac_year">
                                        {{ form_dropdown(
                                            'anac_year',
                                            @$anacYears,
                                            @$notice['anac_year'],
                                            'class="select2-anac_year" data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>

                                {{-- Campo Contratto --}}
                                <div class="form-group col-md-6">
                                    <label for="contract">Contratto
                                    </label>
                                    <div class="select2-blue" id="input_contract">
                                        {{ form_dropdown(
                                            'contract',
                                            ['' => '',1=>'Lavori',2=>'Servizi',3=>'Forniture'],
                                            @$notice['contract'],
                                            'class="select2-contract" data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>
                            </div>

                            {{-- Campo Oggetto --}}
                            <div class="form-group">
                                <label for="object" id="obj-label">Oggetto *
                                </label>
                                {{ form_input([
                                    'name' => 'object',
                                    'value' => !empty($notice['object']) ? (($_storageType === 'duplicate') ? 'Copia di ' . $notice['object'] : $notice['object']) : null,
                                    'placeholder' => 'Oggetto',
                                    'id' => 'input_object',
                                    'class' => 'form-control input_object'
                                ]) }}
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

                            @if(in_array($_storageType, ['insert', 'duplicate']))
                                <div class="form-row d-flex align-items-end lots">
                                    {{-- Campo Codice CIG --}}
                                    <div class="form-group col-md-6 cig">
                                        <label for="cig">Codice CIG</label>
                                        {{ form_input([
                                            'name' => 'cig[]',
                                            'value' => !empty($notice['cig']) ? $notice['cig'] : null,
                                            'placeholder' => 'Codice CIG',
                                            'id' => 'input_cig',
                                            'class' => 'form-control input_cig'
                                        ]) }}
                                    </div>

                                    {{-- Campo Importo dell'appalto (al netto dell'IVA) --}}
                                    <div class="form-group col-md-4 asta_base_value">
                                        <label for="asta_base_value">
                                            Importo dell'appalto <small>(al netto dell'IVA)</small></label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-euro-sign"></i></span>
                                            </div>
                                            {{ form_input([
                                            'name' => 'asta_base_value[]',
                                            'value' => (!empty($notice['asta_base_value']) && ($_storageType == 'update' || ($_storageType == 'duplicate' && !$notice['is_multicig']))) ? $notice['asta_base_value'] : null,
                                            'placeholder' => '',
                                            'id' => 'input_asta_base_value',
                                            'class' => 'form-control input_asta_base_value a-num-class',
                                        ]) }}
                                        </div>
                                    </div>
                                    <div class="form-group col-md-2">
                                        {{ form_button([
                                            'name' => 'add',
                                            'id' => 'btn_addLot',
                                            'class' => 'btn btn-outline-primary',
                                            'style' => 'width:100%;'
                                        ],'Aggiungi Lotto') }}
                                    </div>
                                </div>
                            @endif

                            <div class="form-row d-flex align-items-end">
                                @if($_storageType == 'update')
                                    {{-- Campo Codice CIG --}}
                                    <div class="form-group col-md-6 cig">
                                        <label for="cig">Codice CIG</label>
                                        @php
                                            $cigCode['name'] = 'cig_code[]';
                                            $cigCode['value'] = !empty($notice['relative_lots']) ?  implode(', ', \System\Arr::pluck($notice['relative_lots'], 'cig')) : $notice['cig'];
                                            $cigCode['placeholder'] = 'Codice CIG';
                                            $cigCode['id'] = 'input_cig_code';
                                            $cigCode['class'] = 'form-control input_cig_code ' . (($notice['is_multicig']) ? 'readonly' : '');
                                            if($notice['is_multicig']) {
                                                $cigCode['readonly'] = 'readonly';
                                            }
                                        @endphp
                                        {{ form_input($cigCode) }}
                                    </div>

                                    <div class="form-group col-md-6">
                                        <label for="asta_base_value" id="asta_base_value_sum_label">
                                            Valore Importo dell'appalto <small>(al netto dell'IVA)</small>
                                        </label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-euro-sign"></i></span>
                                            </div>
                                            @php
                                                $astaBaseValueSum['name'] = 'asta_base_value_sum';
                                                $astaBaseValueSum['value'] = !empty($astaValueSum) ?  $astaValueSum : $notice['asta_base_value'];
                                                $astaBaseValueSum['id'] = 'input_asta_base_value_sum';
                                                $astaBaseValueSum['class'] = 'form-control input_asta_base_value_sum ' . (($notice['is_multicig']) ? 'readonly' : 'a-num-class');
                                                if($notice['is_multicig']) {
                                                    $astaBaseValueSum['readonly'] = 'readonly';
                                                }
                                            @endphp
                                            {{ form_input($astaBaseValueSum) }}
                                        </div>
                                    </div>
                                @endif
                                {{-- Campo Senza importo --}}
                                <div class="form-group col-md-6">
                                    <label for="no_amount">Senza importo</label>
                                    <div class="select2-blue" id="input_no_amount">
                                        {{ form_dropdown(
                                            'no_amount',
                                            [''=>'',1=>'No',2=>'Si'],
                                            @$notice['no_amount'],
                                            'class="form-control select2-no_amount" data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-6" id="sector">
                                    <label for="sector">Settore
                                    </label>
                                    <div class="select2-blue" id="input_sector">
                                        {{ form_dropdown(
                                            'sector',
                                            [
                                                '' => null,
                                                'O-sotto' => 'Ordinario - Sottosoglia',
                                                'O-sopra' => 'Ordinario - Soprasoglia',
                                                'S' => 'Speciale',
                                                'sponsor' => 'Sponsorizzazioni'
                                            ],
                                            @$notice['sector'],
                                            'class="form-control select2-sector" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>
                            </div>

                            {{-- Campo Procedura di scelta del contraente --}}
                            <div class="form-group">
                                <label for="contraent_choice" id="contraent-choice-label">Procedura di scelta del
                                    contraente * </label>
                                <div class="select2-blue" id="input_contraent_choice">
                                    {{ form_dropdown(
                                        'contraent_choice',
                                        ['' => ''],
                                        @$notice['contraent_choice'],
                                        'class="select2-contraent_choice" data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                    ) }}
                                </div>
                            </div>

                            {{ generateSeparator('Amministrazione aggiudicatrice') }}

                            <div class="form-row d-flex align-items-end">

                                <div class="form-group col-md-6">
                                    <label for="adjudicator_name" id="adjudicator-name-label">Amministrazione
                                        aggiudicatrice
                                        * </label>
                                    {{ form_input([
                                        'name' => 'adjudicator_name',
                                        'value' => !empty($notice['adjudicator_name']) ? $notice['adjudicator_name'] : null,
                                        'placeholder' => 'Amministrazione aggiudicatrice',
                                        'id' => 'input_adjudicator_name',
                                        'class' => 'form-control input_adjudicator_name custom_data'
                                    ]) }}
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Codice Fiscale Amministrazione aggiudicatrice --}}
                                <div class="form-group col-md-6">
                                    <label for="adjudicator_data" id="adjudicator-data-label">Codice Fiscale
                                        Amministrazione aggiudicatrice * </label>
                                    {{ form_input([
                                        'name' => 'adjudicator_data',
                                        'value' => !empty($notice['adjudicator_data']) ? $notice['adjudicator_data'] : null,
                                        'placeholder' => 'Codice Fiscale Amministrazione aggiudicatrice',
                                        'id' => 'input_adjudicator_data',
                                        'class' => 'form-control input_adjudicator_data custom_data'
                                    ]) }}
                                </div>

                                {{-- Campo Tipo di amministrazione --}}
                                <div class="form-group col-md-6">
                                    <label for="administration_type">Tipo di amministrazione</label>
                                    <div class="select2-blue" id="input_administration_type">
                                        {{ form_dropdown(
                                            'administration_type',
                                            config('administrationType', null, 'app') ?? [],
                                            @$notice['administration_type'],
                                            'class="select2-administration_type custom_data" data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Sede di gara - Provincia --}}
                                <div class="form-group col-md-4">
                                    <label for="province_office">Sede di gara - Provincia</label>
                                    <div class="select2-blue" id="input_province_office">
                                        {{ form_dropdown(
                                            'province_office',
                                            $provinceShort,
                                            !empty($notice['province_office']) ? $notice['province_office'] : null,
                                            'class="select2-province_office select2 custom_data" data-dropdown-css-class="select2-blue" style="width: 100%; height: unset;"'
                                        ) }}
                                    </div>
                                </div>

                                {{-- Campo Sede di gara - Comune --}}
                                <div class="form-group col-md-4">
                                    <label for="municipality_office">Sede di gara - Comune</label>
                                    {{ form_input([
                                        'name' => 'municipality_office',
                                        'value' => !empty($notice['municipality_office']) ? $notice['municipality_office'] : null,
                                        'placeholder' => 'Sede Comune',
                                        'id' => 'input_municipality_office',
                                        'class' => 'form-control input_municipality_office custom_data'
                                    ]) }}
                                </div>

                                {{-- Campo Sede di gara - Indirizzo --}}
                                <div class="form-group col-md-4">
                                    <label for="office_address">Sede di gara - Indirizzo</label>
                                    {{ form_input([
                                        'name' => 'office_address',
                                        'value' => !empty($notice['office_address']) ? $notice['office_address'] : null,
                                        'placeholder' => 'Indirizzo',
                                        'id' => 'input_office_address',
                                        'class' => 'form-control input_office_address custom_data'
                                    ]) }}
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Sede di gara - Codice Istat --}}
                                <div class="form-group col-md-6">
                                    <label for="istat_office">Sede di gara - Codice Istat <small>( 3 per Regione + 6 per
                                            Comune )</small>
                                    </label>
                                    {{ form_input([
                                        'name' => 'istat_office',
                                        'value' => !empty($notice['istat_office']) ? $notice['istat_office'] : null,
                                        'placeholder' => 'Sede di gara - Codice Istat',
                                        'id' => 'input_istat_office',
                                        'class' => 'form-control input_istat_office custom_data'
                                    ]) }}
                                </div>

                                {{-- Campo Sede di gara - Codice NUTS --}}
                                <div class="form-group col-md-6">
                                    <label for="nuts_office">Sede di gara - Codice NUTS
                                    </label>
                                    {{ form_input([
                                        'name' => 'nuts_office',
                                        'value' => !empty($notice['nuts_office']) ? $notice['nuts_office'] : null,
                                        'placeholder' => 'Sede di gara - Codice NUTS',
                                        'id' => 'input_nuts_office',
                                        'class' => 'form-control input_nuts_office custom_data'
                                    ]) }}
                                </div>
                            </div>

                            {{ generateSeparator('Date di riferimento') }}

                            <div class="form-row d-flex align-items-end">
                                {{-- Data di pubblicazione del bando di gara sulla G.U.U.E. --}}
                                <div class="form-group col-md-6">
                                    <label for="guue_date">Data di pubblicazione del bando di gara sulla
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

                                {{-- Campo Data di pubblicazione del bando di gara sulla G.U.R.I. --}}
                                <div class="form-group col-md-6">
                                    <label for="guri_date">Data di pubblicazione del bando di gara sulla
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
                                {{-- Data dell'atto --}}
                                <div class="form-group col-md-4">
                                    <label for="act_date">Data dell'atto</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="act_date"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control"
                                               value="{{ @$act_date }}"
                                               id="input_act_date">
                                    </div>
                                </div>

                                {{-- Campo Data di pubblicazione sul sito --}}
                                <div class="form-group col-md-4">
                                    <label for="activation_date">Data di pubblicazione sul sito *</label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="datetime-local" name="activation_date"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control"
                                               value="{{ !empty($notice['activation_date']) ? date('Y-m-d H:i', strtotime($notice['activation_date'])) : null }}"
                                               id="input_activation_date">
                                    </div>
                                </div>

                                {{-- Campo Data di scadenza presentazione offerte --}}
                                <div class="form-group col-md-4">
                                    <label for="expiration_date">Data di scadenza presentazione offerte
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                        </div>
                                        <input type="date" name="expiration_date"
                                               placeholder="GG/MM/AAAA"
                                               autocomplete="off" class="form-control"
                                               value="{{ @$notice['expiration_date'] }}"
                                               id="input_expiration_date">
                                    </div>
                                </div>
                            </div>

                            {{ generateSeparator('Informazioni collegate') }}

                            {{-- Campo Ufficio --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="object_structures_id">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_structure" id="n_structure_label">Ufficio</label>
                                    </div>
                                    <div id="ajax_structure"></div>
                                    <input type="hidden" value="" name="object_structures_id"
                                           id="input_object_structures_id"
                                           class="object_structures_id">
                                </div>
                            </div>

                            {{-- Campo RUP --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="rup">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_rup" id="n_rup_label">RUP
                                        </label>
                                    </div>
                                    <div id="ajax_rup"></div>
                                    <input type="hidden" value="" name="object_personnel_id" id="input_personnel_id"
                                           class="object_personnel_id">
                                </div>
                            </div>

                            {{-- Campo Altre procedure --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="procedures">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_procedures" id="n_procedures_label">Altre procedure</label>
                                    </div>
                                    <div id="ajax_procedures"></div>
                                    <input type="hidden" value="" name="procedures" id="other_procedures_id"
                                           class="other_procedures_id">
                                </div>
                            </div>

                            {{ generateSeparator('Altre informazioni') }}

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Requisiti di qualificazione --}}
                                <div class="form-group col-md-9" id="input_requirement">
                                    <label for="requirement">Requisiti di qualificazione
                                    </label>
                                    <div class="select2-blue">
                                        {{ form_dropdown(
                                            'requirements[]',
                                            '',
                                            '',
                                            'class="form-control select2-requirement" multiple="multiple" data-placeholder="Seleziona o cerca requisito..." data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Codice CPV --}}
                                <div class="form-group col-md-6" id="input_cpv_code">
                                    <label for="cpv_code_id">Codice CPV
                                    </label>
                                    <div class="select2-blue">
                                        {{ form_dropdown(
                                            'cpv_code_id',
                                            ['' => ''],
                                            @$notice['cpv_code_id'],
                                            'class="form-control select2-cpv_code_id" data-placeholder="Seleziona o cerca codice CPV..." data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>

                                {{-- Campo Codice SCP --}}
                                <div class="form-group col-md-6">
                                    <label for="codice_scp">Codice SCP</label>
                                    {{ form_input([
                                        'name' => 'codice_scp',
                                        'value' => !empty($notice['codice_scp']) ? $notice['codice_scp'] : null,
                                        'placeholder' => 'Codice SCP',
                                        'id' => 'input_codice_scp',
                                        'class' => 'form-control input_codice_scp'
                                    ]) }}
                                </div>
                            </div>

                            {{-- Campo URL di Pubblicazione su www.serviziocontrattipubblici.it --}}
                            <div class="form-group" id="input_url_service">
                                <label for="url_scp">URL di Pubblicazione su www.serviziocontrattipubblici.it</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-link"></i></span>
                                    </div>
                                    {{ form_input([
                                    'name' => 'url_scp',
                                    'value' => !empty($notice['url_scp']) ? $notice['url_scp'] : null,
                                    'placeholder' => 'https://www.',
                                    'id' => 'input_url_scp',
                                    'class' => 'form-control input_url_scp',
                                ]) }}
                                </div>
                            </div>

                            {{-- Campo Provvedimento --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="measure">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_measure" id="n_measure_label">Provvedimento</label>
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
                                    'value' => !empty($notice['details']) ? $notice['details'] : null,
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

        {{ form_input([
           'type' => 'hidden',
           'name' => '__ignore_cig',
           'value' => 0,
           'id' => '__ignore_cig',
           'class' => '__ignore_cig',
       ]) }}

        @if(!empty($notice['id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'id',
                'value' => $notice['id'],
                'id' => 'notice_id',
                'class' => 'notice_id',
            ]) }}
        @endif

        @if(!empty($notice['contraent_choice']))
            {{ form_input([
                'type' => 'hidden',
                'name' => '_contraent_choice',
                'value' => $notice['contraent_choice'],
                'id' => '_contraent_choice',
                'class' => '_contraent_choice',
            ]) }}
        @endif

        @if(!empty($requirementIds))
            {{ form_input([
                'type' => 'hidden',
                'name' => '_requirementIds',
                'value' => implode(',',$requirementIds),
                'id' => '_requirementIds',
                'class' => '_requirementIds',
            ]) }}
        @endif

        @if(!empty($notice['cpv_code_id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => '_code_cpvId',
                'value' => $notice['cpv_code_id'],
                'id' => '_code_cpvId',
                'class' => '_code_cpvId',
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

    .readonly {
        cursor: not-allowed;
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

        // Per il tooltip di info nel form
        let infoSpan = '<span class="far fa-question-circle fa-xs" data-toggle="tooltip" data-placement="right" ';
        infoSpan += 'data-original-title="Campo pubblicato e comunicato ad ANAC ai fini dell\'art.1 comma 32 Legge n. 190/2012"></span>';
        $('#obj-label').append(infoSpan);
        $('#contraent-choice-label').append(infoSpan);
        $('#adjudicator-name-label').append(infoSpan);
        $('#adjudicator-data-label').append(infoSpan);

        let formModified = false;
        let institutionId = $('#institution_id').val();

        {{-- Begin campi select --}}
        {{-- Campo select anno anac --}}
        let $dropdownAnacYear = $('.select2-anac_year');
        $dropdownAnacYear.select2({
            minimumResultsForSearch: -1,
            placeholder: 'Seleziona',
            allowClear: true
        });

        {{-- Campo select contratto --}}
        let $dropdownContract = $('.select2-contract');
        $dropdownContract.select2({
            minimumResultsForSearch: -1,
            placeholder: 'Seleziona',
            allowClear: true
        });

        {{-- Campo select senza importo --}}
        let $dropdownNoAmount = $('.select2-no_amount');
        $dropdownNoAmount.select2({
            minimumResultsForSearch: -1,
            placeholder: 'Seleziona',
            allowClear: true,
        });

        {{-- Select2 campo "Stato pubblicazione" --}}
        let $dropdownPublishingStatus = $('.select2-publishing_status');
        $dropdownPublishingStatus.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        {{-- Select2 per campo "Settore" --}}
        let $dropdownSector = $('.select2-sector');
        $dropdownSector.select2({
            placeholder: 'Seleziona',
            allowClear: true,
            minimumResultsForSearch: -1
        });

        {{-- Select2 per campo "Pubblica In" --}}
        let $dropdownPublicIn = $('.select2-public_in');
        $dropdownPublicIn.select2()
        $dropdownPublicIn.on('change', function () {
            $('#public_in').val($(this).val());
        });

        {{-- Begin Select2 campo "Procedura di scelta del contraente" --}}
        let $dropdownChoice = $('.select2-contraent_choice');
        $dropdownChoice.select2({
            minimumResultsForSearch: -1,
            placeholder: 'Seleziona',
            allowClear: true,
            ajax: {
                url: '{{siteUrl("/admin/asyncData")}}',
                type: "get",
                dataType: 'json',
                delay: 250,
                data: function (data) {
                    return {
                        model: 28,
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
                id: $('#_contraent_choice').val(),
                model: 28,
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
            }
        }).then(function (data) {
            let item = data.data.selected;
            // Creo l'opzione e l'appendo alla select
            for (const el of item) {
                var option = new Option(String(el.text), el.id, true, true);
                $dropdownChoice.append(option).trigger('change');
            }
        });
        @endif
        {{-- End Select2 campo "Procedura di scelta del contraente" --}}

        {{-- Campo select Tipo di amministrazione --}}
        let $dropdownAdministrationType = $('.select2-administration_type');
        $dropdownAdministrationType.select2({
            minimumResultsForSearch: -1,
            placeholder: 'Seleziona',
            allowClear: true
        });

        {{-- Campo select Sede di gara - Provincia --}}
        let $dropdownProvinceOffice = $('.select2-province_office');
        $dropdownProvinceOffice.select2({
            placeholder: 'Seleziona provincia',
            allowClear: true
        });

        // Tabella per la selezione della struttura
        let structure = $('#ajax_structure').patOsAjaxPagination({
            url: config.structure.url,
            textLoad: config.structure.textLoad,
            selectedLabel: 'Ufficio selezionato',
            footerTable: config.structure.footerTable,
            classTable: config.structure.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.structure.hideTable,
            showTable: config.structure.showTable,
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
            label: '#n_structure_label'
        });

        // Se sono in modifica o in duplicazione setto i valori il presidente della commissione gia selezionato
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($notice['structure']))
        structure.patOsAjaxPagination.setValue('{{ $notice['object_structures_id'] }}', '{{ htmlEscape($notice['structure']['structure_name']).(!empty($notice['structure']['parent_name'])?' - '.htmlEscape($notice['structure']['parent_name']):'').(!empty($notice['structure']['reference_email'])?' - '.$notice['structure']['reference_email']:'') }}', true);
        @endif
        {{-- END CAMPI SELECT --}}

        // Se si sta creando un nuovo bando, inizializzo la data di pubblicazione con la data odierna
        if ($('#_storage_type').val() == 'insert' && !$('#input_activation_date').val()) {
            $('#input_activation_date').attr('value', "<?= date('Y-m-d H:i') ?>");
        }

        // Tabella per la selezione delle altre procedure relative al bando di gara
        var otherProcedures = $('#ajax_procedures').patOsAjaxPagination({
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
                exclude_id: <?php echo ($_storageType == 'insert') ? 0 : $notice['id'] ?>
            },
            columns: config.notice.columns,
            action: {
                type: 'checkbox',
            },
            dataSource: config.notice.dataSource,
            dateFormat: config.notice.dateFormat,
            published: config.notice.published,
            label: '#n_procedures_label'
        });

        // Se sono presenti setto le procedure gia selezionate in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($notice['proceedings']))
        @foreach($notice['proceedings'] as $proceeding)
        @php
            $tmpActivationTime = !empty($proceeding['activation_date']) ? ' - '.date('d/m/Y', strtotime($proceeding['activation_date'])) : null;
        $tmpExpirationTime = !empty($proceeding['expiration_date']) ? ' - '.date('d/m/Y', strtotime($proceeding['expiration_date'])) : null;
        @endphp
        otherProcedures.patOsAjaxPagination.setValue({{ $proceeding['id'] }}, '{{ $proceeding['type'].' - '.htmlEscape($proceeding['object']).' - '.$proceeding['cig'].''.$tmpActivationTime.''.$tmpExpirationTime }}', true);
        @endforeach
        @endif

        // Tabella per la selezione del rup(dal personale)
        let rup = $('#ajax_rup').patOsAjaxPagination({
            url: config.personnel.url,
            textLoad: config.personnel.textLoad,
            selectedLabel: 'Rup selezionato',
            footerTable: config.personnel.footerTable,
            classTable: config.personnel.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.personnel.hideTable,
            showTable: config.personnel.showTable,
            search_placeholder: config.personnel.search_placeholder,
            setInputDataValue: '#input_personnel_id',
            dataParams: {
                model: 2,
                institution_id: institutionId
            },
            columns: config.personnel.columns,
            action: {
                type: 'radio',
            },
            dataSource: config.personnel.dataSource,
            addRecord: config.personnel.addRecord,
            archived: config.personnel.archived,
            published: config.personnel.published,
            label: '#n_rup_label'
        });

        // Se sono in modifica o in duplicazione setto i valori il presidente della commissione gia selezionato
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($notice['rup']))
        rup.patOsAjaxPagination.setValue('{{ $notice['rup']['id'] }}', '{{ (!empty($notice['rup']['title'])?htmlEscape($notice['rup']['title']).' - ':'').htmlEscape($notice['rup']['full_name']).' - '.htmlEscape($notice['rup']['name']).' - '.(!empty($notice['rup']['email'])?$notice['rup']['email']:'N.D') }}', true);
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
            label: '#n_measure_label'
        });

        // Setto il bando relativo se sono in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($notice['relative_measure']))
        @php
            $tmpStartDate = !empty($notice['relative_measure']) ? ' - '.date('d/m/Y', strtotime($notice['relative_measure']['date'])) : null;
        @endphp
        measure.patOsAjaxPagination.setValue('{{ $notice['object_measure_id'] }}', '{{ htmlEscape($notice['relative_measure']['object']).' - '.$notice['relative_measure']['number'].''.$tmpStartDate }}', true);
        @endif

        {{-- Begin Select2 campo "Requisiti di qualificazione" --}}
        let $dropdownRequirement = $('.select2-requirement');
        $dropdownRequirement.select2({
            allowClear: true,
            ajax: {
                url: '{{siteUrl("/admin/asyncData")}}',
                type: "get",
                dataType: 'json',
                delay: 250,
                data: function (data) {
                    return {
                        model: 29,
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
                id: $('#_requirementIds').val(),
                model: 29,
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
                $dropdownRequirement.append(option).trigger('change');
            }
        });
        @endif
        {{-- End Select2 campo "Requisiti di qualificazione" --}}

        {{-- Begin Select2 campo "Codice CPV" --}}
        let $dropdownCPVCode = $('.select2-cpv_code_id');
        $dropdownCPVCode.select2({
            allowClear: true,
            ajax: {
                url: '{{siteUrl("/admin/asyncData")}}',
                type: "get",
                dataType: 'json',
                delay: 250,
                data: function (data) {
                    return {
                        model: 44,
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
                id: $('#_code_cpvId').val(),
                model: 44,
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
                $dropdownCPVCode.append(option).trigger('change');
            }
        });
        @endif
        {{-- End Select2 campo "Codice CPV" --}}

        {{-- Campo CKEDITOR --}}
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
            beforeSend: function (jqXHR, settings) {
                btnSend.empty().append('{{ __('brn_save_spinner',null,'patos') }}').attr("disabled", true);
                $('.error-toast').remove();
            },
            success: function (data) {
                let response = parseJson(data);

                if (response.data.cigs) {
                    // Funzione che mostra l'alert che notifica che il cig inserito è gia presente
                    {{-- (vedere nel footer) --}}
                    checkIfCigsExist(response.data.cigs);
                } else {
                    btnSend.empty().append('{{ __('brn_save',null,'patos') }}');
                    formModified = false;

                    // Funzione che genera il toast con il messaggio di successo
                    {{-- (vedere nel footer) --}}
                    createValidatorFormSuccessToast(response.data.message, 'Bando di gara');

                    {{-- Controllo se non sono all'interno di un modale --}}
                    @if(empty($is_box))
                    setTimeout(function () {
                        window.location.href = '{{ siteUrl('admin/contests-act') }}';
                    }, 800);
                    @else
                    setTimeout(function () {
                        window.parent.$('#formModal').modal('hide');
                    }, 800);
                    @endif
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

        let count = 0;

        // Aggiunta di un lotto
        $('#btn_addLot').on('click', function () {

            let formField = `<div class="form-group col-md-6 _lot_${count}"><label for="cig">Codice CIG</label>`;
            formField += '<input type="text" name="cig[]" value="" placeholder="Codice CIG" id="" class="form-control input_cig"></div>';
            formField += `<div class="form-group col-md-4 asta_base_value _lot_${count}"><label for="asta_base_value">`;
            formField += 'Importo dell\'appalto <small>(al netto dell\'IVA)</small></label>';
            formField += '<div class="input-group"><div class="input-group-prepend"><span class="input-group-text">';
            formField += '<i class="fas fa-euro-sign"></i></span></div>';
            formField += '<input type="text" name="asta_base_value[]" value="" placeholder="" id=""';
            formField += 'class="form-control input_asta_base_value a-num-class"> </div></div>';
            formField += `<div class="form-group col-md-2 _lot_${count}">`;
            formField += `<button name="delete" type="button" class="btn btn-outline-primary btn_delete_lot" data-remove="${count++}" style="width:100%;">`;
            formField += 'Elimina Lotto</button></div>';

            $(formField).appendTo('.lots');
            $('.a-num-class').each(function (i, e) {
                $(this).autoNumeric({aSep: '.', aDec: ',', vMax: '999999999999.99', aForm: false});
            });

            // Rimozione di un lotto
            $('.btn_delete_lot').on('click', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                let toRemove = $(this).data('remove');

                $.confirm({
                    title: 'Attenzione:',
                    type: 'dark',
                    closeIcon: true,
                    content: 'Sei sicuro di voler eliminare il CIG?',
                    buttons: {
                        'Ok': function () {
                            $(`._lot_${toRemove}`).remove();
                        },
                        'Annulla': function () {
                        }
                    }
                });
            });
        });
    });
</script>
{% endblock %}