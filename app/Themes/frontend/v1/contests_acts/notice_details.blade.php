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
                        <h1 class="page-title">{{!empty($h1) ? $h1 : ( !empty($instance['object']) ? $instance['object'] : '') }}</h1>
                        @if(!empty($instance['type']))
                            <h5 class="text-secondary page-subtitle"><i class="fas fa-caret-right mr-1"></i>
                                {{e: $instance['type'] }}
                            </h5>
                        @endif
                    </div>

                    <p>
                    @if(!empty($instance['relative_lots']))
                        <ul>
                            @foreach($instance['relative_lots'] as $lot)
                                <li>
                                    <span style="font-weight: 600;">CIG: </span> <a class="text-muted"
                                                                                    href="{{ siteUrl('page/581/details/'. (int)$lot['id'].'/'.urlTitle($lot['object'])) }}">{{e: $lot['cig'] }}</a>
                                    <span style="font-weight: 600;"> - Importo dell'appalto: </span>
                                    <span> &euro; {{currency('filter=escape_xss decimal_separator=, thousands_separator=.'): $lot['asta_base_value'] }}</span>
                                </li>
                            @endforeach
                        </ul>
                    @elseif(!empty($instance['cig']))
                        <div class="attributi">
                            <div class="attributo">
                                <span class="titolo">CIG:</span>
                                <span class="mr-2 text-muted">
                                    {{e: $instance['cig'] }}
                                </span>
                            </div>
                        </div>

                        @if(!empty($instance['bdncp_link']))
                            <div class="attributi">
                                <div class="attributo">
                                    <span class="titolo"><!--<i class="fas fa-link"></i>--> Link BDNCP:</span>
                                    <span class="mr-2">
                                        <a class="mt-2 text-muted" href="{{$instance['bdncp_link']}}">
                                            {{$instance['bdncp_link']}}
                                        </a>
                                    </span>
                                </div>
                            </div>>
                        @endif
                    @endif

                    <div class="attributi">
                        @if(!empty($astaValueSum))
                            <div class="attributo">
                                <span class="titolo">Totale importo dell'appalto:</span>
                                <span class="mr-2 text-muted">
                                     &euro; {{currency('filter=escape_xss decimal_separator=, thousands_separator=.'): $astaValueSum }}
                                </span>
                            </div>
                        @endif

                        @if(!empty($instance['adjudicator_name']))
                            <div class="attributo">
                                <span class="titolo">Struttura proponente:</span>
                                <span class="mr-2 text-muted">
                                     {{e: $instance['adjudicator_name'] }}
                                    @if(!empty($instance['adjudicator_data']))
                                        - {{e: $instance['adjudicator_data'] }}
                                    @endif
                                </span>
                            </div>
                        @endif

                        @if(!empty($instance['contraent_choice']))
                            <div class="attributo">
                                <span class="titolo">Procedura di scelta del contraente:</span>
                                <span class="mr-2 text-muted">
                                    {{e: $instance['contraent_choice']['name'] }}
                                </span>
                            </div>
                        @endif
                    </div>

                    <h3 id="anchor5" class="testo-blu anchor page-subtitle mt-3">Altre informazioni sulla procedura</h3>
                    <div class="attributi">
                        @php
                            $anchorsNumber++;
                        @endphp
                        @if($instance['structure'])
                            <div class="attributo">
                                <span class="titolo">Ufficio:</span>
                                <a class="text-muted"
                                   href="{{ siteUrl('page/40/details/'.(int)$instance['structure']['id'].'/'.urlTitle($instance['structure']['structure_name'])) }}">
                                    {{e: $instance['structure']['structure_name'] }}
                                </a>
                            </div>
                        @endif

                        @if($instance['rup'])
                            <div class="attributo">
                                <span class="titolo">RUP:</span>
                                <a class="text-muted"
                                   href="{{ siteUrl('page/58/details/'. (int)$instance['rup']['id'].'/'.urlTitle($instance['rup']['full_name'])) }}">
                                    {{e: $instance['rup']['full_name'] }}
                                </a>
                            </div>
                        @endif

                        @if(!empty($instance['relative_measure']))
                            <div class="attributo">
                                <span class="titolo">Provvedimento:</span>
                                <a class="text-muted"
                                   href="{{ siteUrl('page/9/details/'.(int)$instance['object_measure_id'].'/'.urlTitle($instance['relative_measure']['object'])) }}">
                                    {{e: $instance['relative_measure']['object'] }}
                                </a>
                            </div>
                        @endif

                        @if(!empty($instance['act_date']))
                            <div class="attributo">
                                <span class="titolo">Data dell'atto:</span>
                                <span class="text-muted">
                                    {{date('d-m-Y|date'): $instance['act_date'] }}
                                </span>
                            </div>
                        @endif

                        @if(!empty($instance['activation_date']))
                            <div class="attributo">
                                <span class="titolo">Data di pubblicazione:</span>
                                <span class="text-muted">
                                    {{date('d-m-Y|date'): $instance['activation_date'] }}
                                </span>
                            </div>
                        @endif

                        @if(!empty($instance['expiration_date']))
                            <div class="attributo">
                                <span class="titolo">Data di scadenza:</span>
                                <span class="text-muted">
                                    {{date('d-m-Y|date'): $instance['expiration_date'] }}
                                </span>
                            </div>
                        @endif

                        @if(!empty($instance['guue_date']))
                            <div class="attributo">
                                <span class="titolo">Data di pubblicazione sulla
                                G.U.U.E.:</span>
                                <span class="text-muted">
                                    {{date('d-m-Y|date'): $instance['guue_date'] }}
                                </span>
                            </div>
                        @endif

                        @if(!empty($instance['guri_date']))
                            <div class="attributo">
                                <span class="titolo">Data di pubblicazione sulla
                                G.U.R.I.:</span>
                                <span class="text-muted">
                                    {{date('d-m-Y|date'): $instance['guri_date'] }}
                                </span>
                            </div>
                        @endif
                    </div>

                    @if(!empty($instance['proceedings']))
                        <div class="mt-2">
                            <span style="font-weight: 600;">Procedure relative:</span>
                            <ul>
                                @foreach($instance['proceedings'] as $proceeding)
                                    <li>
                                        <a class="mr-2 text-muted"
                                           href="{{ siteUrl('page/110/details/'. (int)$proceeding['id'].'/'.urlTitle($proceeding['object'])) }}">
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
                    <p><a class="text-muted" href="{{ currentUrl().'?st=1' }}">Tabella delle informazioni
                            d'indicizzazione</a></p>

                    @if(!empty($instance['measures']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="anchor6" class="testo-blu anchor page-subtitle">Provvedimenti amministrativi</h3>
                        <ul>
                            @foreach($instance['measures'] as $measure)
                                <li>
                                    <a class="mr-2 text-muted"
                                       href="{{ siteUrl('page/106/details/'.$measure['id'].'/'.urlTitle($measure['object'])) }}">
                                        {{e: $measure['object'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif

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

                    {{--  Created/Update Info --}}
                    {% include v1/layout/partials/created_updated_info %}


                    @if(!empty($instance['relative_alerts']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="anchor9" class="testo-blu anchor">Avvisi relativi</h3>
                        <div class="griglia griglia-2 mb-5">
                            @foreach( $instance['relative_alerts'] as $result)
                                <div class="card-richiamo">
                                    <span class="fas fa-file-contract"></span>
                                    <a href="{{ siteUrl('page/110/details/'.$result['id'].'/'.urlTitle($result['object'])) }}">{{e: $result['object'] }}</a>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if(!empty($instance['relative_results']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="anchor10" class="testo-blu anchor">Esiti relativi</h3>
                        <div class="griglia griglia-2 mb-5">
                            @foreach( $instance['relative_results'] as $result)
                                <div class="card-richiamo">
                                    <span class="fas fa-file-contract"></span>
                                    <a href="{{ siteUrl('page/110/details/'.$result['id'].'/'.urlTitle($result['object'])) }}">{{e: $result['object'] }}</a>
                                </div>
                            @endforeach
                        </div>
                    @endif
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