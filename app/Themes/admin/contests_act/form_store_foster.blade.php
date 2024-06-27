{{-- Form store Esito/Affidamento --}}
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
                        Aggiunta Esito/Affidamento
                    @elseif($_storageType === 'update')
                        Modifica Esito/Affidamento
                    @else
                        Duplicazione Esito/Affidamento
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
                                        'value' => !empty($foster['object']) ? (($_storageType === 'duplicate') ? 'Copia di ' . $foster['object'] : $foster['object']) : null,
                                        'placeholder' => 'Oggetto',
                                        'id' => 'input_object',
                                        'class' => 'form-control input_object'
                                    ]) }}
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo ANAC - Anno di riferimento --}}
                                <div class="form-group col-md-6">
                                    <label for="anac_year">ANAC - Anno di riferimento * <span
                                                class="far fa-question-circle fa-xs" data-toggle="tooltip"
                                                data-placement="right"
                                                data-original-title="Selezionare l'anno di riferimento di questa procedura. Questo valore verrà preso in considerazione per includere o meno l'informazione nella creazione del file XML per la comunicazione all'ANAC"></span></label>
                                    <div class="select2-blue" id="input_anac_year">
                                        {{ form_dropdown(
                                            'anac_year',
                                            @$years,
                                            @$foster['anac_year'],
                                            'class="select2-anac_year" data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>

                                <div class="form-group col-md-6" id="decree_163">
                                    <label for="decree_163">Decreto o determina di affidamento di lavori, servizi e
                                        forniture di somma urgenza e di protezione civile (art.163) *
                                        <span class="far fa-question-circle fa-xs" data-toggle="tooltip"
                                              data-placement="right" data-html="true"
                                              data-original-title="Selezionando Si l'affidamento verrà pubblicato anche nella pagina <b>&quot;Affidamenti diretti di lavori, servizi e forniture di somma urgenza e di protezione civile&quot;</b>"></span>
                                    </label>
                                    <div class="select2-blue" id="input_decree_163">
                                        {{ form_dropdown(
                                            'decree_163',
                                            [
                                                0 => 'No',
                                                1 => 'Si'
                                            ],
                                            @$foster['decree_163'],
                                            'class="form-control select2-decree_163" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Pubblica in --}}
                                <div class="form-group col-md-6" id="input_contact_personnel">
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

                                {{-- Campo Codice CIG --}}
                                <div class="form-group col-md-6">
                                    <label for="cig" id="cig-label">Codice CIG
                                    </label>
                                    {{ form_input([
                                        'name' => 'cig',
                                        'value' => !empty($foster['cig']) ? $foster['cig'] : null,
                                        'placeholder' => 'Codice CIG',
                                        'id' => 'input_cig',
                                        'class' => 'form-control input_cig'
                                    ]) }}
                                </div>

                            </div>


                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-6" id="input_sector">
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
                                                'sponsor' => 'Sponsorizzazioni',
                                            ],
                                            @$foster['sector'],
                                            'class="form-control select2-sector" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>

                                {{-- Campo Procedura di scelta del contraente --}}
                                <div class="form-group col-md-6">
                                    <label for="contraent_choice" id="contraent-choice-label">Procedura di scelta del
                                        contraente * </label>
                                    <div class="select2-blue" id="input_contraent_choice">
                                        {{ form_dropdown(
                                            'contraent_choice',
                                            ['' => ''],
                                            '',
                                            'class="select2-contraent_choice" data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>
                            </div>

                            {{generateSeparator('Importi')}}

                            <div class="form-row d-flex align-items-end">

                                {{-- Campo Senza importo --}}
                                <div class="form-group col-md-6">
                                    <label for="no_amount">Senza importo</label>
                                    <div class="select2-blue" id="input_no_amount">
                                        {{ form_dropdown(
                                            'no_amount',
                                            [''=>'',1=>'No',2=>'Si'],
                                            @$foster['no_amount'],
                                            'class="form-control select2-no_amount" data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Valore Importo dell'appalto (al netto dell'IVA) --}}
                                <div class="form-group col-md-6">
                                    <label for="asta_base_value">
                                        Valore Importo dell'appalto <small>(al netto dell'IVA)</small>
                                    </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-euro-sign"></i></span>
                                        </div>
                                        {{ form_input([
                                        'name' => 'asta_base_value',
                                        'value' => !empty($foster['asta_base_value']) ? $foster['asta_base_value'] : null,
                                        'placeholder' => '',
                                        'id' => 'input_asta_base_value',
                                        'class' => 'form-control input_asta_base_value a-num-class'
                                    ]) }}
                                    </div>
                                </div>

                                {{-- Campo Valore Importo di aggiudicazione (al lordo degli oneri di sicurezza e al netto dell'IVA) --}}
                                <div class="form-group col-md-6">
                                    <label for="award_amount_value" id="award-amount-value-label">
                                        Valore Importo di aggiudicazione <small>(al lordo degli oneri di sicurezza e al
                                            netto
                                            dell'IVA)</small> </label>
                                    <div class="input-group">
                                        <div class="input-group-prepend">
                                            <span class="input-group-text"><i class="fas fa-euro-sign"></i></span>
                                        </div>
                                        {{ form_input([
                                        'name' => 'award_amount_value',
                                        'value' => !empty($foster['award_amount_value']) ? $foster['award_amount_value'] : null,
                                        'placeholder' => '',
                                        'id' => 'input_award_amount_value',
                                        'class' => 'form-control input_award_amount_value a-num-class'
                                    ]) }}
                                    </div>
                                </div>
                            </div>

                            {{generateSeparator('Informazioni collegate')}}

                            {{-- Campo Procedura relativa --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="procedure">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_procedure" id="f_procedure_label">Procedura relativa</label>
                                    </div>
                                    <div id="ajax_procedure"></div>
                                    <input type="hidden" value="" name="relative_procedure_id"
                                           id="relative_procedure_id"
                                           class="relative_procedure_id">
                                </div>
                            </div>

                            {{-- Campo Ufficio --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="object_structures_id">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_structure" id="f_structure_label">Ufficio</label>
                                    </div>
                                    <div id="ajax_structure"></div>
                                    <input type="hidden" value="" name="object_structures_id"
                                           id="input_object_structures_id"
                                           class="object_structures_id">
                                </div>
                            </div>

                            {{generateSeparator('Partecipanti e aggiudicatari')}}

                            {{-- Campo Partecipanti alla gara --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_participants" id="f_participants_label">Partecipanti alla
                                            gara</label>
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
                                        <label for="ajax_awardees" id="f_awardees_label">Aggiudicatari della
                                            gara</label>
                                    </div>
                                    <div id="ajax_awardees"></div>
                                    <input type="hidden" value="" name="awardees" id="input_awardees"
                                           class="awardees">
                                </div>
                            </div>

                            {{generateSeparator('Amministrazione aggiudicatrice')}}

                            {{-- Campo Amministrazione aggiudicatrice --}}
                            <div class="form-group">
                                <label for="adjudicator_name" id="adjudicator-name-label">Amministrazione aggiudicatrice
                                    * </label>
                                {{ form_input([
                                    'name' => 'adjudicator_name',
                                    'value' => !empty($foster['adjudicator_name']) ? $foster['adjudicator_name'] : null,
                                    'placeholder' => 'Amministrazione aggiudicatrice',
                                    'id' => 'input_adjudicator_name',
                                    'class' => 'form-control input_adjudicator_name custom_data'
                                ]) }}
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Codice Fiscale Amministrazione aggiudicatrice --}}
                                <div class="form-group col-md-6">
                                    <label for="adjudicator_data" id="adjudicator-data-label">Codice Fiscale
                                        Amministrazione aggiudicatrice * </label>
                                    {{ form_input([
                                        'name' => 'adjudicator_data',
                                        'value' => !empty($foster['adjudicator_data']) ? $foster['adjudicator_data'] : null,
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
                                            @$foster['administration_type'],
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
                                            !empty($foster['province_office']) ? $foster['province_office'] : null,
                                            'class="select2-province_office custom_data" data-dropdown-css-class="select2-blue" style="width: 100%; height: unset;"'
                                        ) }}
                                    </div>
                                </div>

                                {{-- Campo Sede di gara - Comune --}}
                                <div class="form-group col-md-4">
                                    <label for="municipality_office">Sede di gara - Comune</label>
                                    {{ form_input([
                                        'name' => 'municipality_office',
                                        'value' => !empty($foster['municipality_office']) ? $foster['municipality_office'] : null,
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
                                        'value' => !empty($foster['office_address']) ? $foster['office_address'] : null,
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
                                        'value' => !empty($foster['istat_office']) ? $foster['istat_office'] : null,
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
                                        'value' => !empty($foster['nuts_office']) ? $foster['nuts_office'] : null,
                                        'placeholder' => 'Sede di gara - Codice NUTS',
                                        'id' => 'input_nuts_office',
                                        'class' => 'form-control input_nuts_office custom_data'
                                    ]) }}
                                </div>
                            </div>

                            {{generateSeparator('Date di riferimento')}}

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
                                               value="{{ !empty($foster['act_date']) ? date('Y-m-d', strtotime($foster['act_date'])) : null }}"
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
                                               value="{{ !empty($foster['activation_date']) ? date('Y-m-d H:i', strtotime($foster['activation_date'])) : null }}"
                                               id="input_activation_date">
                                    </div>
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Specifica tipologia data di pubblicazione --}}
                                <div class="form-group col-md-6">
                                    <label for="publication_date_type">Specifica tipologia data di pubblicazione</label>
                                    <div class="select2-blue" id="input_publication_date_type">
                                        {{ form_dropdown(
                                            'publication_date_type',
                                            [''=>'','data perfezionamento contratto'=>'Data perfezionamento contratto',
                                            'data perfezionamento adesione ad accordo quadro'=>'Data perfezionamento adesione ad accordo quadro',
                                                'data convenzione'=>'Data convenzione','data acquisto su MEPA'=>'Data acquisto su MEPA'],
                                            @$foster['publication_date_type'],
                                            'class="select2-publication_date_type" data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Data di effettivo inizio dei lavori, servizi o forniture --}}
                                <div class="form-group col-md-6">
                                    <label for="work_start_date" id="work-start-date-label">Data di effettivo inizio dei
                                        lavori, servizi o
                                        forniture </label>
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
                                    <label for="work_end_date" id="work-end-date-label">Data di ultimazione dei lavori,
                                        servizi o forniture </label>
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
                                            [''=>'',1=>'Appalto aggiudicato per gara sopra soglia comunitaria (pubblicazione su G.U.U.E. + G.U.R.I.)',
                                                2=>'Appalto aggiudicato per gara nazionale (pubblicazione su G.U.R.I.)'],
                                            @$foster['typology_result'],
                                            'class="select2-typology_result" data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>
                            </div>

                            {{generateSeparator('Altre informazioni')}}

                            {{-- Campo RUP --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-12" id="rup">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_rup" id="f_rup_label">RUP
                                        </label>
                                    </div>
                                    <div id="ajax_rup"></div>
                                    <input type="hidden" value="" name="object_personnel_id" id="input_personnel_id"
                                           class="object_personnel_id">
                                </div>
                            </div>

                            <div class="form-row d-flex align-items-end">
                                {{-- Campo Requisiti di qualificazione --}}
                                <div class="form-group col-md-12" id="input_procedures">
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
                                        'value' => !empty($foster['codice_scp']) ? $foster['codice_scp'] : null,
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
                                    'value' => !empty($foster['url_scp']) ? $foster['url_scp'] : null,
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
                                        <label for="ajax_measure" id="f_measure_label">Provvedimento</label>
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
                                    'value' => !empty($foster['details']) ? $foster['details'] : null,
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


        @if(!empty($foster['id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'id',
                'value' => $foster['id'],
                'id' => 'foster_id',
                'class' => 'foster_id',
            ]) }}
        @endif

        @if(!empty($foster['contraent_choice']))
            {{ form_input([
                'type' => 'hidden',
                'name' => '_contraent_choice',
                'value' => $foster['contraent_choice'],
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

        @if(!empty($foster['cpv_code_id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => '_code_cpvId',
                'value' => $foster['cpv_code_id'],
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

        {{ form_hidden('institute_id',!empty($institution_id) ? $institution_id : PatOsInstituteId()) }}
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
        $('#cig-label').append(infoSpan);
        $('#award-amount-value-label').append(infoSpan);
        $('#participants-label').append(infoSpan);
        $('#awardees-label').append(infoSpan);
        $('#contraent-choice-label').append(infoSpan);
        $('#adjudicator-name-label').append(infoSpan);
        $('#adjudicator-data-label').append(infoSpan);
        $('#work-start-date-label').append(infoSpan);
        $('#work-end-date-label').append(infoSpan);

        {{-- Begin campi select --}}
        let $dropdownAnacYear = $('.select2-anac_year');
        $dropdownAnacYear.select2({
            minimumResultsForSearch: -1,
            placeholder: 'Seleziona',
            allowClear: true
        });

        let $dropdownNoAmount = $('.select2-no_amount');
        $dropdownNoAmount.select2({
            minimumResultsForSearch: -1,
            placeholder: 'Seleziona',
            allowClear: true
        });

        let $dropdownProvinceOffice = $('.select2-province_office');
        $dropdownProvinceOffice.select2({
            placeholder: 'Seleziona provincia',
            allowClear: true,
        });

        {{-- Select2 per campo "Pubblica In" --}}
        let $dropdownPublicIn = $('.select2-public_in');
        $dropdownPublicIn.select2()
        $dropdownPublicIn.on('change', function () {
            $('#public_in').val($(this).val());
        });

        let $dropdownAdministrationType = $('.select2-administration_type');
        $dropdownAdministrationType.select2({
            minimumResultsForSearch: -1,
            placeholder: 'Seleziona',
            allowClear: true
        });

        let $dropdownTypologyResult = $('.select2-typology_result');
        $dropdownTypologyResult.select2({
            minimumResultsForSearch: -1,
            placeholder: 'Seleziona',
            allowClear: true
        });

        let $dropdownPublicationDateType = $('.select2-publication_date_type');
        $dropdownPublicationDateType.select2({
            minimumResultsForSearch: -1,
            placeholder: 'Seleziona',
            allowClear: true
        });

        {{-- Select2 per campo "Settore" --}}
        let $dropdownSector = $('.select2-sector');
        $dropdownSector.select2({
            placeholder: 'Seleziona',
            allowClear: true,
            minimumResultsForSearch: -1
        });

        {{-- Select2 campo "Stato pubblicazione" --}}
        let $dropdownPublishingStatus = $('.select2-publishing_status');
        $dropdownPublishingStatus.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        {{-- Select2 per campo "Tipo atto" --}}
        let $dropdownActTypology = $('.select2-decree_163');
        $dropdownActTypology.select2({
            placeholder: 'Seleziona',
            allowClear: true,
            minimumResultsForSearch: -1
        });
        {{-- End campi select --}}

        // Tabella per la selezione della Procedura relativa all'affidamento
        let relativeProcedure = $('#ajax_procedure').patOsAjaxPagination({
            url: config.notice.url,
            textLoad: config.notice.textLoad,
            selectedLabel: 'Procedura relativa selezionata',
            footerTable: config.notice.footerTable,
            classTable: config.notice.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.notice.hideTable,
            showTable: config.notice.showTable,
            search_placeholder: config.notice.search_placeholder,
            setInputDataValue: '#relative_procedure_id',
            dataParams: {
                model: 30,
                institution_id: institutionId
            },
            columns: config.notice.columns,
            action: {
                type: 'radio',
            },
            dataSource: config.notice.dataSource,
            dateFormat: config.notice.dateFormat,
            published: config.notice.published,
            label: '#f_procedure_label'
        });

        // Setto la procedura relativa se è presente e sono in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($foster['relative_procedure']))
        @php
            $tmpActivationTime = !empty($foster['relative_procedure']['activation_date']) ? ' - '.date('d/m/Y', strtotime($foster['relative_procedure']['activation_date'])) : '';
            $tmpExpirationTime = !empty($foster['relative_procedure']['expiration_date']) ? ' - '.date('d/m/Y', strtotime($foster['relative_procedure']['expiration_date'])) : '';
        @endphp
        relativeProcedure.patOsAjaxPagination.setValue('{{ $foster['relative_procedure']['id'] }}', '{{ $foster['relative_procedure']['type'].' - '.htmlEscape($foster['relative_procedure']['object']).' - '.$foster['relative_procedure']['cig'].$tmpActivationTime.$tmpExpirationTime }}', true);
        @endif

        //Tabella per la selezione del rup(dal personale)
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
            label: '#f_rup_label'
        });

        // Se sono in modifica o in duplicazione setto i valori il presidente della commissione gia selezionato
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($foster['rup']))
        rup.patOsAjaxPagination.setValue('{{ $foster['rup']['id'] }}', '{{ (!empty($foster['rup']['title'])?htmlEscape($foster['rup']['title']).' - ':'').htmlEscape($foster['rup']['full_name']).' - '.htmlEscape($foster['rup']['name']).' - '.(!empty($foster['rup']['email'])?$foster['rup']['email']:'N.D') }}', true);
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
            label: '#f_measure_label'
        });

        // Setto il bando relativo se sono in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($foster['relative_measure']))
        @php
            $tmpStartDate = !empty($foster['relative_measure']) ? ' - '.date('d/m/Y', strtotime($foster['relative_measure']['date'])) : null;
        @endphp
        measure.patOsAjaxPagination.setValue('{{ $foster['object_measure_id'] }}', '{{ htmlEscape($foster['relative_measure']['object']).' - '.$foster['relative_measure']['number'].''.$tmpStartDate }}', true);
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
            label: '#f_participants_label'
        });

        // Se sono presenti setto i partecipanti in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($foster['participants']))
        @foreach($foster['participants'] as $participant)
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
            label: '#f_awardees_label'
        });

        // Se sono presenti setto gli aggiudicatari gia selezionati in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($foster['awardees']))
        @foreach($foster['awardees'] as $awardees)
        awardees.patOsAjaxPagination.setValue('{{ $awardees['id'] }}', '{{ htmlEscape($awardees['name']).' - '.((!empty($awardees['vat']))?$awardees['vat']:'').' - '.$awardees['type'] }}', true);
        @endforeach
        @endif

        {{-- Begin Select2 campo "Procedura di scelta del contraente" --}}
        let $dropdownChoice = $('.select2-contraent_choice');
        $dropdownChoice.select2({
            minimumResultsForSearch: -1,
            placeholder: 'Seleziona',
            allowClear: true,
            //Recupero i dati per le options della select
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
            var option = new Option(String(item[0].text), item[0].id, true, true);
            $dropdownChoice.append(option).trigger('change');
        });
        @endif
        {{-- End Select2 campo "Procedura di scelta del contraente" --}}

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
            label: '#f_structure_label'
        });

        // Se sono in modifica o in duplicazione setto i valori il presidente della commissione gia selezionato
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($foster['structure']))
        structure.patOsAjaxPagination.setValue('{{ $foster['object_structures_id'] }}', '{{ htmlEscape($foster['structure']['structure_name']).(!empty($foster['structure']['parent_name'])?' - '.htmlEscape($foster['structure']['parent_name']):'').(!empty($foster['structure']['reference_email'])?' - '.$foster['structure']['reference_email']:'') }}', true);
        @endif


        {{-- Begin Select2 campo "Requisiti di qualificazione" --}}
        let $dropdownRequirement = $('.select2-requirement');
        $dropdownRequirement.select2({
            allowClear: true,
            //Recupero i dati per le options della select
            ajax: {
                url: '{{siteUrl("/admin/asyncData")}}',
                type: "get",
                dataType: 'json',
                delay: 250,
                data: function (data) {
                    return {
                        model: 29,
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
                id: $('#_requirementIds').val(),
                model: 29,
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

        // Se si sta creando un nuovo bando, inizializzo la data di pubblicazione con la data odierna
        if ($('#_storage_type').val() == 'insert' && !$('#input_activation_date').val()) {
            $('#input_activation_date').attr('value', "<?= date('Y-m-d H:i') ?>");
        }

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
                    createValidatorFormSuccessToast(response.data.message, 'Esito/Affidamento');

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

    });
</script>
{% endblock %}