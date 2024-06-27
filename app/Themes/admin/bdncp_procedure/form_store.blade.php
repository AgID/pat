{{-- Form Canoni di locazzione --}}
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
                        Aggiunta procedura
                    @elseif($_storageType === 'update')
                        Modifica procedura
                    @else
                        Duplicazione procedura
                    @endif
                </span>
                @if(empty($is_box))
                    <div class="card-tools">
                        <ul class="nav nav-pills ml-auto">
                            <li class="nav-item">
                                <a href="{{ siteUrl('admin/bdncp-procedure') }}" title="Torna indietro"
                                   class="btn btn-default btn-sm btn-outline-primary">
                                    <i class="fas fa-caret-left"></i> Torna a elenco procedure
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

                            {{-- Campo Oggetto --}}
                            <div class="form-row d-flex align-items-end">
                                <div class="form-group col-md-8">
                                    <label for="object" id="obj-label">Oggetto * </label>
                                    {{ form_input([
                                        'name' => 'object',
                                        'value' => !empty($procedure['object']) ? (($_storageType === 'duplicate') ? 'Copia di ' . $procedure['object'] : $procedure['object']) : null,
                                        'placeholder' => 'Oggetto',
                                        'id' => 'input_object',
                                        'class' => 'form-control input_object'
                                    ]) }}
                                </div>
                                {{-- Campo Codice CIG --}}
                                <div class="form-group col-md-4" id="cig-box">
                                    <label for="cig" id="cig-label">Codice CIG *
                                        <span
                                                class="far fa-question-circle fa-xs"
                                                data-toggle="tooltip" data-placement="right"
                                                data-original-title="Nel caso in cui il CIG non sia disponibile è possibile inserire una sequenza di dieci zeri"></span>
                                    </label>
                                    {{ form_input([
                                        'name' => 'cig',
                                        'value' => !empty($procedure['cig']) ? $procedure['cig'] : null,
                                        'placeholder' => 'Codice CIG',
                                        'id' => 'input_cig',
                                        'class' => 'form-control input_cig'
                                    ]) }}
                                </div>
                            </div>

                            <div id="bdncp-link-box">
                                <div class="form-row d-flex align-items-end">
                                    {{-- Campo Link alla BDNCP --}}
                                    <div class="form-group col-md-12" id="input_normative_link">
                                        <label for="bdncp_link">Link alla Banca Dati Nazionale Contratti
                                            Pubblici (BDNCP)</label>
                                        <div class="input-group">
                                            <div class="input-group-prepend">
                                                <span class="input-group-text"><i class="fas fa-link"></i></span>
                                            </div>
                                            {{ form_input([
                                                'name' => 'bdncp_link',
                                                'value' => !empty($procedure['bdncp_link']) ? $procedure['bdncp_link'] : null,
                                                'placeholder' => 'https://www.',
                                                'id' => 'input_bdncp_link',
                                                'class' => 'form-control input_bdncp_link',
                                             ]) }}
                                        </div>
                                    </div>
                                </div>
                            </div>

                            {{-- Fase PUBBLICAZIONE --}}
                            {{generateSeparator('FASE PUBBLICAZIONE')}}
                            <div class="form-row align-items-end mt-2">
                                <div class="col-md-12">
                                    <div class="alert alert-info alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×
                                        </button>
                                        <div>
                                            <h5>
                                                <i class="icon fas fa-info"></i>
                                                Informativa - Dibattito pubblico
                                            </h5>
                                            <a class="text-muted"
                                               href="javascript:maggioriInfo('maggioriInfoDibattito');"
                                               id="guide-button-maggioriInfoDibattito">Leggi Contenuto</a>
                                            <ul id="maggioriInfoDibattito" style="display: none;">
                                                <li>
                                                    Riferimenti normativi
                                                    <ul>
                                                        <li>
                                                            Art. 40, co. 3 e co. 5, d.lgs. 36/2023
                                                            Dibattito pubblico
                                                            (da intendersi riferito a quello facoltativo)
                                                        </li>
                                                        <li>
                                                            Allegato I.6 al d.lgs. 36/2023
                                                            Dibattito pubblico obbligatorio
                                                        </li>
                                                    </ul>
                                                </li>
                                                <li>
                                                    Contenuto dell'obbligo
                                                    <ul>
                                                        <li>
                                                            Relazione sul progetto dell'opera (art. 40, co. 3 codice e
                                                            art. 5,
                                                            co.
                                                            1, lett. a) e b) allegato)
                                                        </li>
                                                        <li>
                                                            Relazione conclusiva redatta dal responsabile del dibattito
                                                            (con i
                                                            contenuti specificati dall’art. 40, co. 5 codice e art. 7,
                                                            co. 1
                                                            dell’allegato)
                                                        </li>
                                                        <li>
                                                            Documento conclusivo redatto dalla SA sulla base della
                                                            relazione
                                                            conclusiva del responsabile (solo per il dibattito pubblico
                                                            obbligatorio) ai sensi dell'art. 7, co. 2 dell'allegato
                                                        </li>
                                                    </ul>
                                                    Per il dibattito pubblico obbligatorio, la pubblicazione dei
                                                    documenti
                                                    di
                                                    cui
                                                    ai nn. 2 e 3, è prevista sia per le SA sia per le amministrazioni
                                                    locali
                                                    interessate dall'intervento
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                <?php $cat = $procedureCat['_publicDebate']; $catId = '_publicDebate'; ?>

                                {% include bdncp_procedure/check_field_box %}
                            </div>

                            <div class="form-row align-items-end">
                                <div class="col-md-12">
                                    <div class="alert alert-info alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×
                                        </button>
                                        <div>
                                            <h5>
                                                <i class="icon fas fa-info"></i>
                                                Informativa - Documenti di gara
                                            </h5>
                                            <a class="text-muted"
                                               href="javascript:maggioriInfo('maggioriInfoDocumentiGara');"
                                               id="guide-button-maggioriInfoDocumentiGara">Leggi Contenuto</a>
                                            <ul id="maggioriInfoDocumentiGara" style="display: none;">
                                                <li>
                                                    Riferimenti normativi
                                                    <ul>
                                                        <li>
                                                            Art. 82, d.lgs. 36/2023
                                                            Documenti di gara
                                                        </li>
                                                        <li>
                                                            Art. 85, co. 4, d.lgs. 36/2023
                                                            Pubblicazione a livello nazionale (cfr. anche l’Allegato
                                                            II.7)
                                                        </li>
                                                    </ul>
                                                </li>
                                                <li>
                                                    Contenuto dell'obbligo
                                                    <ul>
                                                        Documenti di gara. Che comprendono, almeno:
                                                        <li>
                                                            Delibera a contrarre
                                                        </li>
                                                        <li>
                                                            Bando/avviso di gara/lettera di invito
                                                        </li>
                                                        <li>
                                                            Disciplinare di gara
                                                        </li>
                                                        <li>
                                                            Capitolato speciale
                                                        </li>
                                                        <li>
                                                            Condizioni contrattuali proposte
                                                        </li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                {{-- Documenti di gara --}}
                                <?php $cat = $procedureCat['_noticeDocuments']; $catId = '_noticeDocuments'; ?>

                                {% include bdncp_procedure/check_field_box %}
                            </div>

                            {{generateSeparator('FASE AFFIDAMENTO')}}
                            {{-- Fase AFFIDAMENTO --}}
                            <div class="form-row align-items-end mt-2">
                                <div class="col-md-12">
                                    <div class="alert alert-info alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×
                                        </button>
                                        <div>
                                            <h5>
                                                <i class="icon fas fa-info"></i>
                                                Informativa - Composizione delle commissioni giudicatrici
                                            </h5>
                                            <a class="text-muted"
                                               href="javascript:maggioriInfo('maggioriInfoCommissioniGiudicatrici');"
                                               id="guide-button-maggioriInfoCommissioniGiudicatrici">Leggi Contenuto</a>
                                            <ul id="maggioriInfoCommissioniGiudicatrici" style="display: none;">
                                                <li>
                                                    Riferimenti normativi
                                                    <ul>
                                                        <li>
                                                            Art. 28, d.lgs. 36/2023
                                                            Trasparenza dei contratti pubblici
                                                        </li>
                                                    </ul>
                                                </li>
                                                <li>
                                                    Contenuto dell'obbligo
                                                    <ul>
                                                        <li>
                                                            Composizione delle commissioni giudicatrici e CV dei
                                                            componenti
                                                        </li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                {{-- Composizione delle commissioni giudicatrici e CV dei componenti --}}
                                <?php $cat = $procedureCat['_judgingCommission']; $catId = '_judgingCommission'; ?>
                                <div class="form-group col-md-6">
                                    <label for="{{$cat['field']}}">Stai pubblicando la
                                        <u>{{  $cat['title'] }}</u>?</label>
                                    <div class="select2-blue" id="input_{{$cat['field']}}_check">
                                        {{ form_dropdown(
                                            $cat['field'].'_check',
                                            [
                                                0 => 'No',
                                                1 => 'Si'
                                            ],
                                            @$procedure[$cat['field'].'_check'],
                                            'class="form-control select2-'.$cat['field'].' check_select" id="'.$catId.'" data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>

                                {{-- Campo Incarichi associati --}}

                                <div class="form-group col-md-12" id="{{$catId}}_box" style="display: none;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_judging_commission"
                                               id="_judging_commission_label">{{ $cat['subTitle'] }}</label>
                                    </div>
                                    <div id="ajax_judging_commission"></div>
                                    <input type="hidden" value="" name="judging_commission"
                                           id="input_judging_commission"
                                           class="judging_commission">

                                    <div class="col-md-12 px-0">
                                        {{-- Campo Testo di descrizione --}}
                                        <label for="{{$cat['field']}}">Note {{ strtolower($cat['subTitle']) }}</label>
                                        {{form_editor([
                                            'name' => $cat['field'].'_notes',
                                            'value' => !empty($procedure[$cat['field'].'_notes']) ? $procedure[$cat['field'].'_notes'] : null,
                                            'id' => 'input_'.$cat['field'].'_notes',
                                            'class' => 'form-control input_'.$cat['field'].'_notes'
                                        ]) }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-row align-items-end mt-2">
                                <div class="col-md-12">
                                    <div class="alert alert-info alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×
                                        </button>
                                        <div>
                                            <h5>
                                                <i class="icon fas fa-info"></i>
                                                Informativa - Pari opportunità e inclusione lavorativa nei contratti
                                                pubblici PNRR e PNC e nei contratti riservati
                                            </h5>
                                            <a class="text-muted"
                                               href="javascript:maggioriInfo('maggioriInfoPariOpportunita');"
                                               id="guide-button-maggioriInfoPariOpportunita">Leggi Contenuto</a>
                                            <ul id="maggioriInfoPariOpportunita" style="display: none;">
                                                <li>
                                                    Riferimenti normativi
                                                    <ul>
                                                        <li>
                                                            Art. 47, co. 2, e 9 d.l. 77/2021, convertito con
                                                            modificazioni dalla l. 108/2021
                                                        </li>
                                                        <li>
                                                            D.P.C.M. 20 giugno 2023 recante Linee guida volte a favorire
                                                            le pari opportunità generazionali e di genere, nonché
                                                            l’inclusione lavorativa delle persone con disabilità nei
                                                            contratti riservati (art. 1, co. 8, allegato II.3, d.lgs.
                                                            36/2023 )
                                                        </li>
                                                    </ul>
                                                </li>
                                                <li>
                                                    Contenuto dell'obbligo - Da pubblicare successivamente alla
                                                    pubblicazione degli avvisi relativi agli esiti delle procedure
                                                    <ul>
                                                        <li>
                                                            Copia dell’ultimo rapporto sulla situazione del personale
                                                            maschile e femminile redatto dall’operatore economico,
                                                            tenuto alla sua redazione ai sensi dell’art. 46, decreto
                                                            legislativo 11 aprile 2006, n. 198 (operatori economici che
                                                            occupano oltre 50 dipendenti). Il documento è prodotto, a
                                                            pena di esclusione, al momento della presentazione della
                                                            domanda di partecipazione o dell'offerta
                                                        </li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                {{-- Pari opportunità e inclusione lavorativa nei contratti pubblici PNRR e PNC e nei contratti riservati --}}
                                <?php $cat = $procedureCat['_equalOpportunitiesAf']; $catId = '_equalOpportunitiesAf'; ?>

                                {% include bdncp_procedure/check_field_box %}

                            </div>

                            <div class="form-row align-items-end">
                                <div class="col-md-12">
                                    <div class="alert alert-info alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×
                                        </button>
                                        <div>
                                            <h5>
                                                <i class="icon fas fa-info"></i>
                                                Informativa - Procedure di affidamento dei servizi pubblici locali
                                            </h5>
                                            <a class="text-muted"
                                               href="javascript:maggioriInfo('maggioriInfoServiziPubbliciLocali');"
                                               id="guide-button-maggioriInfoServiziPubbliciLocali">Leggi Contenuto</a>
                                            <ul id="maggioriInfoServiziPubbliciLocali" style="display: none;">
                                                <li>
                                                    Riferimenti normativi
                                                    <ul>
                                                        <li>Art. 10, co. 5</li>
                                                        <li>art. 14, co. 3</li>
                                                        <li>art. 17, co. 2</li>
                                                        <li>art. 24</li>
                                                        <li>art. 30, co. 2</li>
                                                        <li>art. 31, co. 1 e 2</li>
                                                        <li>D.lgs. 201/2022 Riordino della disciplina dei servizi
                                                            pubblici locali di rilevanza economica
                                                        </li>
                                                    </ul>
                                                </li>
                                                <li>
                                                    Contenuto dell'obbligo
                                                    <ul>
                                                        <li>
                                                            Deliberazione di istituzione del servizio pubblico locale
                                                            (art. 10, co. 5);
                                                        </li>
                                                        <li>
                                                            Relazione contenente la valutazione finalizzata alla scelta
                                                            della modalità di gestione (art. 14, co. 3);
                                                        </li>
                                                        <li>
                                                            Deliberazione di affidamento del servizio a società in house
                                                            (art. 17, co. 2) per affidamenti sopra soglia del servizio
                                                            pubblico locale,
                                                            compresi quelli nei settori del trasporto pubblico locale e
                                                            dei servizi di distribuzione di energia elettrica e gas
                                                            naturale
                                                        </li>
                                                        <li>
                                                            Contratto di servizio sottoscritto dalle parti che definisce
                                                            gli obblighi di servizio pubblico e le condizioni economiche
                                                            del rapporto
                                                            (artt. 24 e 31 co. 2)
                                                        </li>
                                                        <li>
                                                            Relazione periodica contenente le verifiche periodiche sulla
                                                            situazione gestionale (art. 30, co. 2)
                                                        </li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                {{-- Procedure di affidamento dei servizi pubblici locali --}}
                                <?php $cat = $procedureCat['_localPublicServices']; $catId = '_localPublicServices'; ?>

                                {% include bdncp_procedure/check_field_box %}

                            </div>

                            {{generateSeparator('FASE ESECUTIVA')}}
                            {{-- Fase ESECUTIVA --}}
                            <div class="form-row align-items-end mt-2">
                                <div class="col-md-12">
                                    <div class="alert alert-info alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×
                                        </button>
                                        <div>
                                            <h5>
                                                <i class="icon fas fa-info"></i>
                                                Informativa - Composizione del Collegio consultivo tecnico
                                            </h5>
                                            <a class="text-muted"
                                               href="javascript:maggioriInfo('maggioriInfoCollegioConsultivo');"
                                               id="guide-button-maggioriInfoCollegioConsultivo">Leggi Contenuto</a>
                                            <ul id="maggioriInfoCollegioConsultivo" style="display: none;">
                                                <li>
                                                    Riferimenti normativi
                                                    <ul>
                                                        <li>Art. 215 e ss.</li>
                                                        <li>All. V.2, d.lgs 36/2023</li>
                                                    </ul>
                                                </li>
                                                <li>
                                                    Contenuto dell'obbligo
                                                    <ul>
                                                        <li>
                                                            Composizione del Collegio consultivo tecnici (nominativi)
                                                            CV dei componenti
                                                        </li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                {{-- Procedura di affidamento dei servizi pubblici locali --}}
                                <?php $cat = $procedureCat['_advisoryBoardTechnical']; $catId = '_advisoryBoardTechnical'; ?>
                                <div class="form-group col-md-6">
                                    <label for="{{$cat['field']}}">Stai pubblicando la
                                        <u>{{  $cat['title'] }}</u>?</label>
                                    <div class="select2-blue" id="input_{{$cat['field']}}_check">
                                        {{ form_dropdown(
                                            $cat['field'].'_check',
                                            [
                                                0 => 'No',
                                                1 => 'Si'
                                            ],
                                            @$procedure[$cat['field'].'_check'],
                                            'class="form-control select2-'.$cat['field'].' check_select" id="'.$catId.'" data-dropdown-css-class="select2-blue" style="width: 100%;"'
                                        ) }}
                                    </div>
                                </div>

                                <div class="form-group col-md-12" id="{{$catId}}_box"
                                     style="display: none;">
                                    <div class="d-flex justify-content-between align-items-center mb-2">
                                        <label for="ajax_technical_advisory_board" id="_technical_advisory_board_label">Composizione
                                            del Collegio consultivo
                                            tecnico</label>
                                    </div>
                                    <div id="ajax_technical_advisory_board"></div>
                                    <input type="hidden" value="" name="technical_advisory_board"
                                           id="input_technical_advisory_board"
                                           class="technical_advisory_board">

                                    <div class="col-md-12 px-0">
                                        {{-- Campo Testo di descrizione --}}
                                        <label for="{{$cat['field']}}">Note {{ strtolower($cat['subTitle']) }}</label>
                                        {{form_editor([
                                            'name' => $cat['field'].'_notes',
                                            'value' => !empty($procedure[$cat['field'].'_notes']) ? $procedure[$cat['field'].'_notes'] : null,
                                            'id' => 'input_'.$cat['field'].'_notes',
                                            'class' => 'form-control input_'.$cat['field'].'_notes'
                                        ]) }}
                                    </div>
                                </div>
                            </div>

                            <div class="form-row align-items-end mt-2">
                                <div class="col-md-12">
                                    <div class="alert alert-info alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×
                                        </button>
                                        <div>
                                            <h5>
                                                <i class="icon fas fa-info"></i>
                                                Informativa - Pari opportunità e inclusione lavorativa nei contratti
                                                pubblici PNRR e PNC e nei contratti riservati
                                            </h5>
                                            <a class="text-muted"
                                               href="javascript:maggioriInfo('maggioriInfoPariOpportunitaEs');"
                                               id="guide-button-maggioriInfoPariOpportunitaEs">Leggi Contenuto</a>
                                            <ul id="maggioriInfoPariOpportunitaEs" style="display: none;">
                                                <li>
                                                    Riferimenti normativi
                                                    <ul>
                                                        <li>
                                                            Art. 47, co. 3, co. 3-bis, co. 9, l. 77/2021 convertito con
                                                            modificazioni dalla l. 108/2021
                                                            Pari opportunità e inclusione lavorativa nei contratti
                                                            pubblici PNRR e PNC e nei contratti riservati
                                                        </li>
                                                        <li>
                                                            D.P.C.M 20 giugno 2023 recante Linee guida volte a favorire
                                                            le pari opportunità generazionali e di genere, nonché
                                                            l’inclusione lavorativa delle persone con disabilità nei
                                                            contratti riservati (art. 1, co. 8, allegato II.3, d.lgs.
                                                            36/2023)
                                                        </li>
                                                    </ul>
                                                </li>
                                                <li>
                                                    Contenuto dell'obbligo
                                                    <ul>
                                                        <li>
                                                            Relazione di genere sulla situazione del personale maschile
                                                            e femminile consegnata, entro sei mesi dalla conclusione del
                                                            contratto,
                                                            alla stazione appaltante/ente concedente dagli operatori
                                                            economici che occupano un numero pari o superiore a quindici
                                                            dipendenti
                                                        </li>
                                                        <li>
                                                            Certificazione di cui all’art. 17 della legge 12 marzo 1999,
                                                            n. 68 e della relazione relativa all’assolvimento degli
                                                            obblighi di cui alla medesima legge e
                                                            alle eventuali sanzioni e provvedimenti disposti a carico
                                                            dell’operatore economico nel triennio antecedente la data di
                                                            scadenza della presentazione delle
                                                            offerte e consegnate alla stazione appaltante/ente
                                                            concedente entro sei mesi dalla conclusione del contratto
                                                            (per gli operatori economici che occupano un
                                                            numero pari o superiore a quindici dipendenti)
                                                        </li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                {{-- Pari opportunità e inclusione lavorativa nei contratti pubblici PNRR e PNC e nei contratti riservati --}}
                                <?php $cat = $procedureCat['_equalOpportunitiesEs']; $catId = '_equalOpportunitiesEs'; ?>

                                {% include bdncp_procedure/check_field_box %}
                            </div>

                            {{generateSeparator('FASE SPONSORIZZAZIONI')}}
                            {{-- Fase SPONSORIZZAZIONI --}}
                            <div class="form-row align-items-end mt-2">
                                <div class="col-md-12">
                                    <div class="alert alert-info alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×
                                        </button>
                                        <div>
                                            <h5>
                                                <i class="icon fas fa-info"></i>
                                                Informativa - Contratti gratuiti e forme speciali di partenariato
                                            </h5>
                                            <a class="text-muted"
                                               href="javascript:maggioriInfo('maggioriInfoContrattiGratuito');"
                                               id="guide-button-maggioriInfoContrattiGratuito">Leggi Contenuto</a>
                                            <ul id="maggioriInfoContrattiGratuito" style="display: none;">
                                                <li>
                                                    Riferimenti normativi
                                                    <ul>
                                                        <li>
                                                            Art. 134, co. 4, d.lgs. 36/2023
                                                        </li>
                                                    </ul>
                                                </li>
                                                <li>
                                                    Contenuto dell'obbligo
                                                    <ul>
                                                        Affidamento di contratti di sponsorizzazione di lavori, servizi
                                                        o forniture per importi superiori a quarantamila 40.000 euro:
                                                        <li>
                                                            Avviso con il quale si rende nota la ricerca di sponsor per
                                                            specifici interventi, ovvero si comunica l'avvenuto
                                                            ricevimento di una
                                                            proposta di sponsorizzazione, con sintetica indicazione del
                                                            contenuto del contratto proposto
                                                        </li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                {{-- Pari opportunità e inclusione lavorativa nei contratti pubblici PNRR e PNC e nei contratti riservati --}}
                                <?php $cat = $procedureCat['_freeContract']; $catId = '_freeContract'; ?>

                                {% include bdncp_procedure/check_field_box %}
                            </div>

                            {{generateSeparator('FASE PROCEDURE DI SOMMA URGENZA E DI PROTEZIONE CIVILE')}}
                            {{-- Fase PROCEDURE DI SOMMA URGENZA E DI PROTEZIONE CIVILE --}}
                            <div class="form-row align-items-end mt-2">
                                <div class="col-md-12">
                                    <div class="alert alert-info alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×
                                        </button>
                                        <div>
                                            <h5>
                                                <i class="icon fas fa-info"></i>
                                                Informativa - Atti e documenti relativi agli affidamenti di somma
                                                urgenza
                                            </h5>
                                            <a class="text-muted"
                                               href="javascript:maggioriInfo('maggioriInfoSommaUrgenza');"
                                               id="guide-button-maggioriInfoSommaUrgenza">Leggi Contenuto</a>
                                            <ul id="maggioriInfoSommaUrgenza" style="display: none;">
                                                <li>
                                                    Riferimenti normativi
                                                    <ul>
                                                        <li>Art. 140, d.lgs. 36/2023</li>
                                                        <li>Comunicato del Presidente ANAC del 19 settembre 2023</li>
                                                    </ul>
                                                </li>
                                                <li>
                                                    Contenuto dell'obbligo
                                                    <ul>
                                                        Atti e documenti relativi agli affidamenti di somma urgenza a
                                                        prescindere dall'importo di affidamento.
                                                        In particolare:
                                                        <li>
                                                            Verbale di somma urgenza e provvedimento di affidamento; con
                                                            specifica indicazione delle
                                                            modalità della scelta e delle motivazioni che non hanno
                                                            consentito il ricorso alle procedure ordinarie
                                                        </li>
                                                        <li>
                                                            Perizia giustificativa
                                                        </li>
                                                        <li>
                                                            Elenco prezzi unitari, con indicazione di quelli concordati
                                                            tra le parti e di quelli dedotti da prezzari ufficiali
                                                        </li>
                                                        <li>
                                                            Verbale di consegna dei lavori o verbale di avvio
                                                            dell'esecuzione del servizio/fornitura
                                                        </li>
                                                        <li>
                                                            Contratto, ove stipulato
                                                        </li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                {{-- Affidamenti di somma urgenza --}}
                                <?php $cat = $procedureCat['_emergencyFoster']; $catId = '_emergencyFoster'; ?>

                                {% include bdncp_procedure/check_field_box %}
                            </div>

                            {{generateSeparator('FASE FINANZA DI PROGETTO')}}
                            {{-- Fase FINANZA DI PROGETTO --}}
                            <div class="form-row align-items-end mt-2">
                                <div class="col-md-12">
                                    <div class="alert alert-info alert-dismissible">
                                        <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×
                                        </button>
                                        <div>
                                            <h5>
                                                <i class="icon fas fa-info"></i>
                                                Informativa - Procedura di affidamento
                                            </h5>
                                            <a class="text-muted"
                                               href="javascript:maggioriInfo('maggioriInfoProceduraAffidamento');"
                                               id="guide-button-maggioriInfoProceduraAffidamento">Leggi Contenuto</a>
                                            <ul id="maggioriInfoProceduraAffidamento" style="display: none;">
                                                <li>
                                                    Riferimenti normativi
                                                    <ul>
                                                        <li>Art. 193, d.lgs. 36/2023</li>
                                                    </ul>
                                                </li>
                                                <li>
                                                    Contenuto dell'obbligo
                                                    <ul>
                                                        <li>
                                                            Provvedimento conclusivo della procedura di valutazione
                                                            della proposta del promotore relativa alla realizzazione in
                                                            concessione
                                                            di lavori o servizi
                                                        </li>
                                                    </ul>
                                                </li>
                                            </ul>
                                        </div>
                                    </div>
                                </div>

                                {{-- Procedura di affidamento --}}
                                <?php $cat = $procedureCat['_fosterProcedure']; $catId = '_fosterProcedure'; ?>

                                {% include bdncp_procedure/check_field_box %}
                            </div>

                            {{generateSeparator('ALTRE INFORMAZIONI')}}
                            <div class="form-row align-items-end mt-2">
                                <div class="col-md-12 px-0" id="general_info">
                                    <div class="col-md-12 mb-3 px-0">
                                        {{-- ***** BEGIN: include attach**** --}}
                                        <?php $attId = '_generalInfo'; $title = ''; $noSeparator = true; ?>
                                        <?php $listAttach = $attachs[$attId] ?? []; $labels = $attLabels[$attId] ?? []; ?>

                                        {% include layout/partials/attach %}
                                        {{-- ***** END: include attach**** --}}
                                    </div>

                                    <div class="col-md-12 mb-3">
                                        {{-- Campo Testo di descrizione --}}
                                        <label for="foster_procedure_notes">Note</label>
                                        {{form_editor([
                                            'name' => 'notes',
                                            'value' => !empty($procedure['notes']) ? $procedure['notes'] : null,
                                            'id' => 'input_notes',
                                            'class' => 'form-control input_notes'
                                        ]) }}
                                    </div>
                                </div>
                            </div>
                        </div>
                        {{-- END: Form --}}

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

        @if(!empty($procedure['id']))
            {{ form_input([
                'type' => 'hidden',
                'name' => 'id',
                'value' => $procedure['id'],
                'id' => 'canon_id',
                'class' => 'canon_id',
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

        {{ form_input([
           'type' => 'hidden',
           'name' => '_typology',
           'value' => 'procedure',
           'id' => '_typology',
           'class' => '_typology',
       ]) }}

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
{{ js('admin/get/config.js') }}
<script type="text/javascript">

    //Previene il salvataggio quando si preme invio e il focus non è sul pulsante di salvataggio
    $('#{{ $formSettings['id'] }}').on('keyup keypress', function (e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13 && e.target.tagName !== 'TEXTAREA') {
            e.preventDefault();
            return false;
        }
    });

    $(document).ready(function () {

        let formModified = false;
        let institutionId = $('#institution_id').val();

        CKEDITOR.replace('input_judging_commission_notes');

        CKEDITOR.replace('input_advisory_board_technical_notes');

        CKEDITOR.replace('input_notes');

        {{-- Select2 per campo "Commissione Giudicatrice" --}}
        let $dropdownJudgingCommission = $('.select2-judging_commission');
        $dropdownJudgingCommission.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        let $dropdownAdvisoryBoardTechnical = $('.select2-advisory_board_technical');
        $dropdownAdvisoryBoardTechnical.select2({
            placeholder: 'Seleziona',
            minimumResultsForSearch: -1
        });

        $('.check_select').on('change', function () {
            let selected = $(this).attr('id');
            $(`#${selected}_box`).toggle();
        });

        $('.check_select').each(function () {
            let selected = $(this).attr('id');
            let val = $(this).val();

            if (val == 1) {
                $(`#${selected}_box`).toggle();
            }
        });

        let advisoryBoardTechnical = $('#ajax_technical_advisory_board').patOsAjaxPagination({
            url: config.assignment.url,
            textLoad: config.assignment.textLoad,
            selectedLabel: 'Incarichi selezionati',
            footerTable: config.assignment.footerTable,
            classTable: config.assignment.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.assignment.hideTable,
            showTable: config.assignment.showTable,
            search_placeholder: config.assignment.search_placeholder,
            setInputDataValue: '#input_technical_advisory_board',
            dataParams: {
                model: 36,
                institution_id: institutionId,
            },
            dateFormat: config.assignment.dateFormat,
            columns: config.assignment.columns,
            action: {
                type: 'checkbox',
            },
            dataSource: config.assignment.dataSource,
            addRecord: config.assignment.addRecord,
            label: '#_technical_advisory_board_label'
        });
        // Setto il bando relativo se sono in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($procedure['board']))
        @foreach($procedure['board'] as $assignment)
        @php
            $tmpStartDate = !empty($assignment['assignment_start']) ? ' - '.date('d/m/Y', strtotime($assignment['assignment_start'])) : null;
        @endphp
        advisoryBoardTechnical.patOsAjaxPagination.setValue('{{ $assignment['id'] }}', '{{ htmlEscape($assignment['name']).' - '.htmlEscape($assignment['object']).''.$tmpStartDate }}', true);
        @endforeach
        @endif


        // Tabella per la selezione degli Incarichi associati al personale
        let judgingCommission = $('#ajax_judging_commission').patOsAjaxPagination({
            url: config.assignment.url,
            textLoad: config.assignment.textLoad,
            selectedLabel: 'Incarichi selezionati',
            footerTable: config.assignment.footerTable,
            classTable: config.assignment.classTable,
            hideShowTable: Boolean(<?php echo ($_storageType == 'insert') ? 'true' : 'false' ?>),
            hideTable: config.assignment.hideTable,
            showTable: config.assignment.showTable,
            search_placeholder: config.assignment.search_placeholder,
            setInputDataValue: '#input_judging_commission',
            dataParams: {
                model: 36,
                institution_id: institutionId,
            },
            dateFormat: config.assignment.dateFormat,
            columns: config.assignment.columns,
            action: {
                type: 'checkbox',
            },
            dataSource: config.assignment.dataSource,
            addRecord: config.assignment.addRecord,
            label: '#_judging_commission_label'
        });
        // Setto il bando relativo se sono in modifica o duplicazione
        @if(in_array($_storageType,['update', 'duplicate']) && !empty($procedure['commission']))
        @foreach($procedure['commission'] as $assignment)
        @php
            $tmpStartDate = !empty($assignment['assignment_start']) ? ' - '.date('d/m/Y', strtotime($assignment['assignment_start'])) : null;
        @endphp
        judgingCommission.patOsAjaxPagination.setValue('{{ $assignment['id'] }}', '{{ htmlEscape($assignment['name']).' - '.htmlEscape($assignment['object']).''.$tmpStartDate }}', true);
        @endforeach
        @endif

        {{-- Begin salvataggio --}}
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
                console.log($('#{{ $formSettings['id'] }}').serialize())
                btnSend.empty().append('{{ __('brn_save_spinner',null,'patos') }}').attr("disabled", true);
                $('.error-toast').remove();
            },
            success: function (data) {
                btnSend.empty().append('{{ __('brn_save',null,'patos') }}');
                let response = parseJson(data);
                formModified = false;

                // Funzione che genera il toast con il messaggio di successo
                {{-- (vedere nel footer) --}}
                createValidatorFormSuccessToast(response.data.message, 'Procedure BDNCP');

                {{-- Controllo se non sono all'interno di un modale --}}
                @if(empty($is_box))
                setTimeout(function () {
                    window.location.href = '{{ siteUrl('admin/bdncp-procedure') }}';
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
         * Controllo se sono arrivato in questa pagina dal versioning
         */
        {{-- Vedere nel footer --}}
        checkIfRestore();
    });

    function maggioriInfo(elementId) {
        console.log(elementId)
        let guide = $(`#${elementId}`);
        if (guide.is(':visible')) {
            $(`#guide-button-${elementId}`).text('Leggi contenuto');
        } else {
            $(`#guide-button-${elementId}`).text('Nascondi');
        }
        guide.slideToggle();
    }

</script>
{% endblock %}