<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Pagina per Sovvenzioni --}}

{% extends v1/layout/master %}

{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}

@php
    $anchorsNumber = 1;
@endphp

<main>
    <section class="my-5">
        <div class="container">
            <div class="row variable-gutters _reverse">
                <div class="col-lg-8">
                    <p id="generic-info" class="testo-blu anchor sr-only"
                       style="visibility: hidden; margin: unset;padding: unset;">Informazioni generali</p>
                    <div class="titolo mb-4">
                        <h1 class="page-title">{{e: !empty($h1) ? $h1 : $pageName }}</h1>
                    </div>
                    <div class="attributi">
                        @if(empty($privacy))
                            <div class="attributo">
                                <span class="titolo">Nominativo:</span>
                                <span class="mr-2 text-muted">
                                    {{e: $beneficiaryName }}
                                </span>
                            </div>
                            @if(empty($fiscalDataAvaiable))
                                <div class="attributo">
                                    <span class="titolo">Dati fiscali:</span>
                                    <span class="mr-2 text-muted">
                                    {{e: $fiscalData }}
                                    </span>
                                </div>
                            @endif
                        @endif
                    </div>

                    {{--Inizio Solo per le liquidazioni --}}
                    @if(!empty($instance['relative_grant']))
                        <div class="attributi">
                            <div class="attributo">
                                <span class="titolo">Procedura relativa:</span>
                                <a class="text-muted"
                                   href="{{ siteUrl('page/126/details/'.$instance['relative_grant']['id'].'/'.urlTitle($instance['relative_grant']['object'])) }}"
                                   title="{{e: $instance['relative_grant']['object'] }}" class="ml-1">
                                    {{e: $instance['relative_grant']['object'] }}
                                </a>
                            </div>
                        </div>
                    @endif
                    {{--Fine  Solo per le liquidazioni --}}

                    <div class="attributi">
                        {{-- Solo per le sovvenzioni --}}
                        @if(!empty($instance['normatives']))
                            @php
                                $i = 0;
                                $len = count($instance['normatives'])-1;
                            @endphp
                            <div class="attributo">
                                <span class="titolo">Normativa alla base dell'attribuzione:</span>
                                @foreach($instance['normatives'] as $normative)
                                    <a class="text-muted"
                                       href="{{ siteUrl('page/24/details/'.$normative['id'].'/'.urlTitle($normative['name'])) }}"
                                       title="{{e: $normative['name'] }}" class="ml-1">
                                        {{e: $normative['name'] }}
                                    </a>
                                    {{ $i++ < $len ? ',  ' : '' }}
                                @endforeach
                            </div>
                        @endif
                        {{-- Solo per le sovvenzioni --}}

                        {{-- Solo per le sovvenzioni --}}
                        @if(!empty($instance['regulation']))
                            <div class="attributo">
                                <span class="titolo">Regolamento alla base dell'attribuzione:</span>
                                <a class="text-muted"
                                   href="{{ siteUrl('page/29/details/'.$instance['regulation']['id'].'/'.urlTitle($instance['regulation']['title'])) }}"
                                   title="{{e: $instance['regulation']['title'] }}" class="ml-1">
                                    {{e: $instance['regulation']['title'] }}
                                </a>
                            </div>
                        @endif
                        {{-- Solo per le sovvenzioni --}}

                        @if(!empty($structure))
                            <div class="attributo">
                                <span class="titolo">Struttura organizzativa responsabile:</span>
                                <a class="text-muted"
                                   href="{{ siteUrl('page/40/details/'.$structure['id'].'/'.urlTitle($structure['structure_name'])) }}"
                                   title="{{e: $structure['structure_name'] }}" class="ml-1">
                                    {{e: $structure['structure_name'] }}
                                </a>
                            </div>
                        @endif

                        {{-- Inizio Solo per le liquidazioni --}}
                        @if(!empty($instance['relative_grant']))
                            <div class="attributi">
                                @if(!empty($instance['compensation_paid']))
                                    <div class="attributo">
                                        <span class="titolo">Importo del vantaggio economico corrisposto:</span>
                                        <span class="text-muted">
                                            &euro; {{currency('filter=escape_xss decimal_separator=, thousands_separator=.'): $instance['compensation_paid'] }}
                                        </span>
                                    </div>
                                @endif

                                @if(!empty($instance['reference_date']))
                                    <div class="attributo">
                                        <span class="titolo">Data:</span>
                                        <span class="text-muted">
                                            {{date('d-m-Y|date'): $instance['reference_date'] }}
                                        </span>
                                    </div>
                                @endif

                                @if(!empty($instance['compensation_paid_date']))
                                    <div class="attributo">
                                        <span class="titolo">Anno:</span>
                                        <span class="text-muted">
                                             {{e: $instance['compensation_paid_date'] }}
                                        </span>
                                    </div>
                                @endif
                            </div>
                        @endif
                        {{-- Fine Solo per le liquidazioni --}}

                        {{-- Solo per le sovvenzioni --}}
                        @if(!empty($instance['personnel']))
                            @php
                                $i = 0;
                                $len = count($instance['personnel'])-1;
                            @endphp
                            <div class="attributo">
                                <span class="titolo">Dirigente o funzionario responsabile:</span>
                                @foreach($instance['personnel'] as $personnel)
                                    <a class="text-muted"
                                       href="{{ siteUrl('page/58/details/'.$personnel['id'].'/'.urlTitle($personnel['full_name'])) }}"
                                       title="{{escape_xss: $personnel['full_name'] }}" class="ml-1">
                                        {{e: $personnel['full_name'] }}
                                    </a>
                                    {{ $i++ < $len ? ',  ' : '' }}
                                @endforeach
                            </div>
                        @endif

                        <div>
                            @if(!empty($instance['concession_amount']))
                                <div class="attributo">
                                    <span class="titolo">Importo atto di concessione:</span>
                                    <span class="mr-2 text-muted">
                                     &euro; {{currency('filter=escape_xss decimal_separator=, thousands_separator=.'): $instance['concession_amount'] }}
                                    </span>
                                </div>
                            @endif

                            @if(!empty($instance['concession_act_date']))
                                <div class="attributo">
                                    <span class="titolo">Data atto di concessione:</span>
                                    <span class="mr-2 text-muted">
                                        {{date('d-m-Y|date'): $instance['concession_act_date'] }}
                                    </span>
                                </div>
                            @endif

                            @if(!empty($instance['start_date']))
                                <div class="attributo">
                                    <span class="titolo">Data inizio:</span>
                                    <span class="mr-2 text-muted">
                                        {{date('d-m-Y|date'): $instance['start_date'] }}
                                    </span>
                                </div>
                            @endif

                            @if(!empty($instance['end_date']))
                                <div class="attributo">
                                    <span class="titolo">Data fine:</span>
                                    <span class="mr-2 text-muted">
                                        {{date('d-m-Y|date'): $instance['end_date'] }}
                                    </span>
                                </div>
                            @endif
                        </div>
                        {{-- Solo per le sovvenzioni --}}
                    </div>

                    @if(!empty($instance['notes']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="notes" class="anchor testo-blu anchor mt-3 page-subtitle">Note</h3>
                        {{xss: $instance['notes'] }}
                    @endif

                    {{-- Solo per le sovvenzioni --}}
                    @if(!empty($instance['detection_mode']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="detection_mode" class="anchor testo-blu anchor mt-3 page-subtitle">Modalità seguita per
                            l'individuazione del
                            beneficiario</h3>
                        {{xss: $instance['detection_mode'] }}
                    @endif

                    @if(!empty($instance['relative_liquidation']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="liquidations" class="anchor testo-blu anchor mt-3 page-subtitle">Importi dei vantaggi
                            economici
                            corrisposti</h3>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-4">
                                <thead>
                                <tr class="intestazione-tabella">
                                    <th scope="col">Importo</th>
                                    <th scope="col">Data</th>
                                    <th scope="col">Anno</th>
                                    <th scope="col"></th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($instance['relative_liquidation'] as $liquidation)
                                    <tr>
                                        <td>
                                            &euro; {{currency('filter=escape_xss decimal_separator=, thousands_separator=.'): $liquidation['compensation_paid'] }}</td>
                                        <td>
                                            {{ !empty($liquidation['reference_date']) ? date('d-m-Y', strtotime($liquidation['reference_date'])) : null }}
                                        </td>
                                        <td>{{ $liquidation['compensation_paid_date'] }}</td>
                                        <td class="text-center">
                                            <a href="{{ siteUrl('page/155/details/'.$liquidation['id'].'/'.urlTitle($instance['object'])) }}"
                                               title="Visualizza dettagli liquidazione">
                                                <i class="fas fa-search"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif
                    {{-- Solo per le sovvenzioni --}}


                    {{-- Attach List --}}
                    {% include v1/layout/partials/attach_list %}

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