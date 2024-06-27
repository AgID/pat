<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Pagina per Canoni di locazione --}}

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
                    <p id="generic-info" class="anchor sr-only"
                       style="visibility: hidden; margin: unset;padding: unset;">Informazioni generali</p>
                    <div class="titolo mb-4">
                        <h1 class="page-title">{{e: !empty($h1) ? $h1 : $pageName }}</h1>
                        @if(!empty($instance['type']))
                            <h5 class="text-secondary page-subtitle"><i
                                        class="fas fa-caret-right mr-1"></i> {{e: $instance['type'] }}
                            </h5>
                        @endif
                    </div>

                    <h3 class="testo-blu page-subtitle mt-2">Informazioni generali</h3>
                    <div class="attributi">
                        @if(!empty($instance['assignment_start']) || !empty($instance['assignment_end']) || !empty($instance['end_of_assignment_not_available']))
                            <div class="attributo">
                                <span class="titolo">Inizio incarico:</span>
                                <span class="text-muted">{{xss: date('d-m-Y',strtotime($instance['assignment_start']))}}</span>

                                <span class="titolo"> - Fine incarico:</span>
                                <span class="mr-2 text-muted">{{e: !empty($instance['assignment_end']) ? date('d-m-Y', strtotime($instance['assignment_end'])) : $instance['end_of_assignment_not_available_txt'] }}</span>
                            </div>
                        @endif

                        @if(!empty($assignmentType))
                            <div class="attributo">
                                <span class="titolo">Tipo d'incarico:</span>
                                <span class="text-muted">
                                    {{ $assignmentType}}</span>
                            </div>
                        @endif

                        @if(!empty($name))
                            <div class="attributo">
                                <span class="titolo">Nominativo:</span>
                                <span class="text-muted">{{e: $name }}</span>
                            </div>
                        @endif

                        {{-- Solo per le liquidazioni --}}
                        @if(!empty($instance['related_assignment']))
                            <div class="attributo">
                                <span class="titolo">Procedura relativa:</span>
                                {{-- Se l'incarico relativo è visibile sul front-office metto il link, altrimenti solo il nome --}}
                                @if($instance['related_assignment']['assignment_end'] >= date('Y-m-d H:i:s', strtotime('-3 year')))
                                    <a class="text-muted"
                                       href="{{ siteUrl('page/46/details/'.$instance['related_assignment']['id'].'/'.urlTitle($instance['related_assignment']['name'])) }}">
                                        {{e: $instance['related_assignment']['name'] }}
                                    </a>
                                @else
                                    <span class="text-muted">
                                    {{e: $instance['related_assignment']['name'] }}
                                </span>
                                @endif
                            </div>
                        @endif
                        {{-- Solo per le liquidazioni --}}

                        @if(!empty($structure))
                            <div class="attributo">
                                <span class="titolo">Struttura organizzativa:</span>
                                <a class="text-muted"
                                   href="{{ siteUrl('page/40/details/'.$structure['id'].'/'.urlTitle($structure['structure_name'])) }}">
                                    {{e: $structure['structure_name'] }}
                                </a>
                            </div>
                        @endif

                        @if(!empty($instance['compensation']))
                            <div class="attributo">
                                <span class="titolo">Compenso:</span>
                                <span class="text-muted">
                                        &euro; {{currency('filter=escape decimal_separator=, thousands_separator=.'): $instance['compensation'] }}
                                    </span>
                            </div>
                        @endif

                        @if(!empty($instance['compensation_provided']))
                            <div class="attributo">
                                <span class="titolo">Compenso erogato:</span>
                                <span class="text-muted">
                                         &euro; {{currency('filter=escape decimal_separator=, thousands_separator=.'): $instance['compensation_provided']}}
                                    </span>
                            </div>
                        @endif

                        @if(!empty($instance['liquidation_date']))
                            <div class="attributo">
                                <span class="titolo">Data liquidazione:</span>
                                <span class="text-muted">
                                         {{e: date('d-m-Y',strtotime($instance['liquidation_date'])) }}
                                    </span>
                            </div>
                        @endif

                        @if(!empty($instance['liquidation_year']))
                            <div class="attributo">
                                <span class="titolo">Anno:</span>
                                <span class="text-muted">
                                         {{e: $instance['liquidation_year'] }}
                                    </span>
                            </div>
                        @endif

                        @if(!empty($instance['variable_compensation']))
                            <div class="attributo">
                                <span class="titolo">Componenti variabili del compenso:</span>
                                <p>
                                    {{e: $instance['variable_compensation'] }}
                                </p>
                            </div>
                        @endif
                    </div>

                    @if(!empty($instance['acts_extremes']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="anchor1" class="testo-blu anchor page-subtitle mt-2">Estremi atto di conferimento</h3>
                        <p>{{xss: $instance['acts_extremes'] }}</p>
                    @endif

                    @if(!empty($instance['measures']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="anchor2" class="testo-blu anchor page-subtitle mt-2">Provvedimenti associati</h3>
                        <ul>
                            @foreach($instance['measures'] as $measure)
                                <li>
                                    <a class="text-muted"
                                       href="{{ siteUrl('page/9/details/'.$measure['id']).'/'.urlTitle($measure['object']) }}">
                                        {{e: $measure['object'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    {{-- Tabella delle liquidazioni per gli esiti --}}
                    @if(!empty($instance['relative_liquidation']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="anchor11" class="testo-blu anchor page-subtitle mt-2">Dettaglio somme liquidate</h3>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
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
                                            &euro; {{currency('filter=escape decimal_separator=, thousands_separator=.'): $liquidation['compensation_provided'] }}</td>
                                        <td>{{date('d-m-Y|date'): $liquidation['liquidation_date'] }}</td>
                                        <td>{{e: $liquidation['liquidation_year'] }}</td>
                                        <td class="text-center">
                                            <a href="{{ siteUrl('page/46/details/'.$liquidation['id'].'/'.urlTitle($pageName)) }}"
                                               title="Visualizza dettaglio liquidazione">
                                                <i class="fas fa-search"></i>
                                            </a>
                                        </td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    @if(!empty($instance['assignment_reason']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="anchor5" class="testo-blu anchor page-subtitle mt-2">Ragione dell'incarico</h3>
                        <p>{{e: $instance['assignment_reason'] }}</p>
                    @endif

                    @if(!empty($instance['notes']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="anchor6" class="testo-blu anchor page-subtitle mt-2">Note</h3>
                        <p>{{xss: $instance['notes'] }}</p>
                    @endif

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