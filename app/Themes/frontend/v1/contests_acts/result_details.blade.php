<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Pagina per Bandi di gara --}}

{% extends v1/layout/master %}

{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}

@php
    $anchorsNumber = 0;
@endphp

<main>
    <section class="my-5">
        <div class="container">
            <div class="row variable-gutters _reverse">
                <div class="col-lg-8">
                    <p id="generic-info" class="testo-blu anchor sr-only"
                       style="visibility: hidden; margin: unset;padding: unset;">Informazioni generali</p>
                    <div class="titolo mb-4">
                        <h1 class="page-title">{{e: !empty($h1) ? $h1 : (!empty($instance['object']) ? $instance['object'] : '') }}</h1>
                        @if(!empty($instance['type']))
                            <h5 class="text-secondary page-subtitle"><i class="fas fa-caret-right mr-1"></i>
                                {{e: $instance['type'] }}
                            </h5>
                        @endif
                    </div>

                    <div class="attributi">
                        @if(!empty($instance['relative_notice']['cig']))
                            <div class="attributo">
                                <span class="titolo">Codice CIG:</span>
                                <span class="mr-2 text-muted">
                                    {{e: $instance['relative_notice']['cig'] }}
                                </span>
                            </div>
                        @endif

                        @if(!empty($instance['relative_notice']['adjudicator_name']))
                            @php
                                $tmpName = $instance['relative_notice']['adjudicator_name'];
                                $tmpData = $instance['relative_notice']['adjudicator_data'];
                            @endphp
                            <div class="attributo">
                                <span class="titolo">Struttura proponente:</span>
                                <span class="mr-2 text-muted">
                                    {{e: $tmpName }}
                                    @if(!empty($tmpData))
                                        - {{e: $tmpData }}
                                    @endif
                                </span>
                            </div>
                        @endif

                        @if(!empty($instance['relative_notice']['contraent_choice']))
                            @php
                                $tmpContraent = $instance['relative_notice']['contraent_choice']['name'];
                            @endphp
                            <div class="attributo">
                                <span class="titolo">Procedura di scelta del contraente:</span>
                                <span class="mr-2 text-muted">
                                    {{e: $tmpContraent }}
                                </span>
                            </div>
                        @endif
                    </div>

                    @if(!empty($instance['participants']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="anchor1" class="testo-blu anchor page-subtitle mt-3">Partecipanti</h3>
                        <ul>
                            @foreach($instance['participants'] as $participant)
                                <li>
                                    {{e: $participant['name'] }}
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @if(!empty($instance['awardees']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="anchor2" class="testo-blu anchor page-subtitle mt-3">Aggiudicatari</h3>
                        <ul>
                            @foreach($instance['awardees'] as $awardee)
                                <li>
                                    {{e: $awardee['name'] }}
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @if(!empty($instance['work_start_date']) || !empty($instance['work_end_date']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="anchor3" class="testo-blu anchor page-subtitle mt-3">
                            Tempi di completamento dell'opera, servizio o fornitura
                        </h3>
                        <div class="attributi">
                            @if(!empty($instance['work_start_date']))
                                <div class="attributo">
                                    <span class="titolo">Data di effettivo inizio dei lavori o forniture:</span>
                                    <span class="mr-2 text-muted">
                                    {{date('d-m-Y|date'): $instance['activation_date'] }}
                                </span>
                                </div>
                            @endif
                            @if(!empty($instance['work_end_date']))
                                <div class="attributo">
                                    <span class="titolo">Data di ultimazione dei lavori o forniture:</span>
                                    <span class="mr-2 text-muted">
                                    {{date('d-m-Y|date'): $instance['work_end_date'] }}
                                </span>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if(!empty($instance['award_amount_value']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="anchor4" class="testo-blu anchor page-subtitle mt-3">Importi</h3>
                        <div class="attributi">
                            @if(!empty($instance['award_amount_value']))
                                <div class="attributo">
                                    <span class="titolo">Importo di
                                    aggiudicazione:</span>
                                    <span class="mr-2 text-muted">
                                     &euro; {{currency('filter=escape_xss decimal_separator=, thousands_separator=.'): $instance['award_amount_value'] }}
                                </span>
                                </div>
                            @endif

                            @if(!empty($instance['relative_liquidation']))
                                @php
                                    $sum = 0;
                                @endphp
                                @foreach($instance['relative_liquidation'] as $liquidation)
                                    @php
                                        $sum += $liquidation['amount_liquidated'];
                                    @endphp
                                @endforeach
                                @if($sum > 0)
                                    <div class="attributo">
                                        <span class="titolo">Importo delle somme liquidate:</span>
                                        <span class="mr-2 text-muted">
                                     &euro; {{currency('filter=escape_xss decimal_separator=, thousands_separator=.'): $sum }}
                                </span>
                                    </div>
                                @endif
                            @endif
                        </div>
                    @endif

                    <h3 id="anchor5" class="testo-blu anchor page-subtitle mt-3">Altre informazioni sulla procedura</h3>
                    <div class="attributi">
                        @php
                            $anchorsNumber++;
                        @endphp
                        @if(!empty($instance['relative_notice']['structure']))
                            @php
                                $tmpStructure = $instance['relative_notice']['structure'];
                            @endphp
                            <div class="attributo">
                                <span class="titolo">Ufficio:</span>
                                <a class="text-muted"
                                   href="{{ siteUrl('page/40/details/'. (int)$tmpStructure['id'].'/'.urlTitle($tmpStructure['structure_name'])) }}">
                                    {{e: $tmpStructure['structure_name'] }}
                                </a>
                            </div>
                        @endif

                        @if(!empty($instance['relative_measure']))
                            <div class="attributo">
                                <span class="titolo">Provvedimento:</span>
                                <a class="text-muted"
                                   href="{{ siteUrl('page/9/details/'.$instance['object_measure_id'].'/'.urlTitle($instance['relative_measure']['object'])) }}">
                                    {{e: $instance['relative_measure']['object'] }}
                                </a>
                            </div>
                        @endif

                        @if($instance['relative_notice']['rup'])
                            @php
                                $tmpRup = $instance['relative_notice']['rup'];
                            @endphp
                            <div class="attributo">
                                <span class="titolo">RUP:</span>
                                <a class="text-muted"
                                   href="{{ siteUrl('page/58/details/'.$tmpRup['id'].'/'.urlTitle($tmpRup['full_name'])) }}">
                                    {{e: $tmpRup['full_name'] }}
                                </a>
                            </div>
                        @endif

                        @if(!empty($instance['act_date']))
                            <div class="attributo">
                                <span class="titolo">Data dell'atto:</span>
                                <span class="mr-2 text-muted">
                                    {{date('d-m-Y|date'): $instance['act_date'] }}
                                </span>
                            </div>
                        @endif

                        @if(!empty($instance['activation_date']))
                            <div class="attributo">
                                <span class="titolo">Data di pubblicazione:</span>
                                <span class="mr-2 text-muted">
                                    {{date('d-m-Y|date'): $instance['activation_date'] }}
                                </span>
                            </div>
                        @endif

                        @if(!empty($instance['guue_date']))
                            <div class="attributo">
                                <span class="titolo">Data di pubblicazione sulla
                                G.U.U.E.:</span>
                                <span class="mr-2 text-muted">
                                    {{date('d-m-Y|date'): $instance['guue_date'] }}
                                </span>
                            </div>
                        @endif

                        @if(!empty($instance['guri_date']))
                            <div class="attributo">
                                <span class="titolo">Data di pubblicazione sulla
                                G.U.R.I.:</span>
                                <span class="mr-2 text-muted">
                                    {{date('d-m-Y|date'): $instance['guri_date'] }}
                                </span>
                            </div>
                        @endif

                        @if(!empty($instance['contracting_stations_publication_date']))
                            <div class="attributo">
                                <span class="titolo">Data pubblicazione sul sito della Stazione
                                Appaltante:</span>
                                <span class="mr-2 text-muted">
                                    {{date('d-m-Y|date'): $instance['contracting_stations_publication_date'] }}
                                </span>
                            </div>
                        @endif

                        @if(!empty($instance['typology_result']))
                            <div class="attributo">
                                <span class="titolo">Tipologia esito:</span>
                                <span class="mr-2 text-muted">
                                    {{ $instance['typology_result'] == 1
                                    ? 'Appalto aggiudicato per gara sopra soglia comunitaria (pubblicazione su G.U.U.E. + G.U.R.I.)'
                                    : 'Appalto aggiudicato per gara nazionale (pubblicazione su G.U.R.I.)'
                                }}
                                </span>
                            </div>
                        @endif

                        @if(!empty($instance['relative_notice']))
                            <div class="attributo">
                                <span class="titolo">Procedura relativa:</span>
                                <a class="text-muted"
                                   href="{{ siteUrl('page/110/details/'.$instance['relative_notice']['id'].'/'.urlTitle($instance['relative_notice']['object'])) }}">
                                    {{e: $instance['relative_notice']['object'] }}
                                </a>
                            </div>
                        @endif
                    </div>

                    @if(!empty($instance['proceedings']))
                        <div class="mt-2">
                            <span style="font-weight: 600;">Procedure relative</span>:
                            <ul>
                                @foreach($instance['proceedings'] as $proceeding)
                                    <li>
                                        <a class="mr-2 text-muted"
                                           href="{{ siteUrl('page/110/details/'.$proceeding['id'].'/'.urlTitle($proceeding['object'])) }}">
                                            {{e: $proceeding['object'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif

                    @if(!empty($instance['relative_deliberation']) || !empty($instance['relative_foster']))
                        <div class="mt-2">
                            <span style="font-weight: 600;">Altre procedure di riferimento:</span>
                            <ul>
                                @if(!empty($instance['relative_foster']))
                                    @foreach($instance['relative_foster'] as $foster)
                                        <li>
                                            <a class="mr-2 text-muted"
                                               href="{{ siteUrl('page/110/details/'.$foster['id'].'/'.urlTitle($foster['object'])) }}">
                                                {{e: $foster['object'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                @endif
                                @foreach($instance['relative_deliberation'] as $contestAct)
                                    <li>
                                        <a class="mr-2 text-muted"
                                           href="{{ siteUrl('page/110/details/'.$contestAct['id'].'/'.urlTitle($contestAct['object'])) }}">
                                            {{e: $contestAct['object'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        </div>
                    @endif
                    <p>
                        <a class="text-muted" href="{{ currentUrl().'?st=1' }}">
                            Tabella delle informazioni d'indicizzazione
                        </a>
                    </p>

                    @if(!empty($instance['notice_acts']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="anchor7" class="testo-blu anchor page-subtitle">
                            Atti delle amministrazioni aggiudicatrici e degli enti aggiudicatori
                        </h3>
                        <ul>
                            @foreach($instance['notice_acts'] as $notice_act)
                                <li>
                                    <a class="mr-2 text-muted"
                                       href="{{ siteUrl('page/114/details/'.$notice_act['id'].'/'.urlTitle($notice_act['object'])) }}">
                                        {{e: $notice_act['object'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @if($instance['details'])
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="anchor8" class="testo-blu anchor page-subtitle">Note</h3>
                        {{xss: $instance['details'] }}
                    @endif

                    {{-- Attach List --}}
                    {% include v1/layout/partials/attach_list %}

                    @if(!empty($instance['relative_notice']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="anchor11" class="testo-blu anchor page-subtitle">Procedura relativa</h3>
                        <p>
                            @if(!empty($instance['relative_notice']['expiration_date']))
                                <span style="font-weight: 600;"> Data di
                                    scadenza:</span> {{date('d-m-Y|date'): $instance['relative_notice']['expiration_date'] }}
                                <br>
                            @endif
                            <a class="text-muted"
                               href="{{ siteUrl('page/110/details/'.$instance['relative_notice']['id'].'/'.urlTitle($instance['relative_notice']['object'])) }}">
                                {{e: $instance['relative_notice']['object'] }}
                            </a>
                        </p>
                    @endif

                    {{-- Tabella delle liquidazioni per gli esiti --}}
                    @if(!empty($instance['relative_liquidation']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="anchor12" class="testo-blu anchor">
                            Dettaglio somme liquidate
                        </h3>
                        <div class="table-responsive mb-3">

                            <table class="table table-bordered table-sm">
                                <thead>
                                <tr class="intestazione-tabella">
                                    <th scope="col">Anno</th>
                                    <th scope="col">Oggetto</th>
                                    <th scope="col">Importo liquidato</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($instance['relative_liquidation'] as $liquidation)
                                    <tr>
                                        <th scope="row">{{ $liquidation['anac_year'] }}</th>
                                        <td>
                                            <a href="{{ siteUrl('page/110/details/'.$liquidation['id'].'/'.urlTitle($liquidation['object'])) }}">
                                                {{e: $liquidation['object'] }}
                                            </a>
                                        </td>
                                        <td>
                                            &euro; {{currency('filter=escape_xss decimal_separator=, thousands_separator=.'): $liquidation['amount_liquidated'] }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    {{--  Created/Update Info --}}
                    {% include v1/layout/partials/created_updated_info %}
                </div>

                @if($anchorsNumber > 4)
                    {{-- Index anchor --}}
                    {% include v1/layout/partials/anchor_index %}
                @else
                    {{-- Right Menu --}}
                    {% include v1/layout/partials/right_menu %}
                @endif

            </div>
        </div>
    </section>
</main>

@if($anchorsNumber > 4)
    {{-- Bottom Menu --}}
    {% include v1/layout/partials/bottom_menu %}
@endif

{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}

{% endblock %}