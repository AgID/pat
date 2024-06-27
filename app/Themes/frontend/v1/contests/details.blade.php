<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Pagina per dettaglio Bando di concorso --}}

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
                    <div class="titolo">
                        {{-- Nome pagina --}}
                        <h1 class="page-title">{{e: !empty($h1) ? $h1 : $instance['object'] }}</h1>
                        <h3 class="text-secondary page-subtitle">{{e: $instance['typology'] }}</h3>
                    </div>

                    @if(!empty($instance['activation_date']) or !empty($instance['expiration_date']) or !empty($instance['expiration_time']) or !empty($instance['office']) )
                        <div class="attributi">
                            @php
                                $anchorsNumber++;
                            @endphp
                            <h3 id="generic-info" class="testo-blu anchor sr-only"
                                style="visibility: hidden; margin: unset;padding: unset;">Informazioni generali</h3>
                            <div class="mb-4">
                                @if(!empty($instance['activation_date']))
                                    <div class="attributo">
                                        <span class="titolo">Data di pubblicazione:</span>
                                        <span class="mr-2 text-muted">
                                            {{date('d-m-Y|date'): $instance['activation_date'] }}
                                        </span>
                                    </div>
                                @endif
                                @if(!empty($instance['expiration_date']))
                                    <div class="attributo">
                                        <span class="titolo">Data di scadenza:</span>
                                        <span class="mr-2 text-muted">
                                            {{date('d-m-Y|date'): $instance['expiration_date'] }}
                                        </span>
                                    </div>
                                @endif
                                @if(!empty($instance['expiration_time']))
                                    <div class="attributo">
                                        <span class="titolo">Orario scadenza:</span>
                                        <span class="mr-2 text-muted">
                                            {{e: $instance['expiration_time'] }}
                                        </span>
                                    </div>
                                @endif
                                @if(!empty($instance['related_contest']))
                                    <div class="attributo">
                                        <span class="titolo">Procedura relativa:</span>
                                        <a class="text-muted"
                                           href="{{ siteUrl('page/'.$currentPageId.'/details/'. (int) $instance['related_contest']['id'].'/'.urlTitle($instance['related_contest']['object'])) }}">
                                            {{e: $instance['related_contest']['object'] }}
                                        </a>
                                    </div>
                                @endif
                                @if(!empty($instance['office']))
                                    <div class="attributo">
                                        <span class="titolo">Ufficio di riferimento:</span>
                                        <a class="text-muted"
                                           href="{{ siteUrl('page/40/details/'. (int) $instance['office']['id'].'/'.urlTitle($instance['office']['structure_name'])) }}">
                                            {{e: $instance['office']['structure_name'] }}
                                        </a>
                                    </div>
                                @endif
                                @if(!empty($instance['relative_measure']))
                                    <div class="attributo">
                                        <span class="titolo">Provvedimento:</span>
                                        <a class="text-muted"
                                           href="{{ siteUrl('page/9/details/'. (int)$instance['object_measure_id'].'/'.urlTitle($instance['relative_measure']['object'])) }}">
                                            {{e: $instance['relative_measure']['object'] }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        </div>
                    @endif

                    @if(!empty($instance['province_office']) or !empty($instance['city_office']) or !empty($instance['office_address']) )
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="sede" class="anchor testo-blu anchor page-subtitle">Sede di prova</h3>
                        <ul>
                            @if(!empty($instance['province_office']))
                                <li>
                                    <span class="text-black" style="font-weight: 600;">Provincia:</span>
                                    <span class="text-muted">{{e: $instance['province_office'] }}</span>
                                </li>
                            @endif
                            @if(!empty($instance['city_office']))
                                <li>
                                    <span class="text-black" style="font-weight: 600;">Comune:</span>
                                    <span class="text-muted">{{e: $instance['city_office'] }}</span>
                                </li>
                            @endif
                            @if(!empty($instance['office_address']))
                                <li>
                                    <span class="text-black" style="font-weight: 600;">Indirizzo:</span>
                                    <span class="text-muted">{{e: $instance['office_address'] }}</span>
                                </li>
                            @endif
                        </ul>
                    @endif

                    @if(!empty($instance['test_calendar']) )
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="calendario" class="testo-blu anchor page-subtitle">Calendario delle prove</h3>
                        <div class="mb-4">
                            {{xss: $instance['test_calendar'] }}
                        </div>
                    @endif

                    @if(!empty($instance['evaluation_criteria']) )
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="criterio" class="testo-blu anchor page-subtitle">Criteri di valutazione</h3>
                        <div class="mb-4">
                            {{xss: $instance['evaluation_criteria'] }}
                        </div>
                    @endif

                    @if(!empty($instance['traces_written_tests']) )
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="tracce" class="testo-blu anchor page-subtitle">Tracce delle prove selettive</h3>
                        <div class="mb-4">
                            {{xss: $instance['traces_written_tests'] }}
                        </div>
                    @endif

                    @if(!empty($instance['assignments']) )
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="commissione" class="testo-blu anchor page-subtitle">Commissione giudicatrice</h3>
                        <ul>
                            @foreach( $instance['assignments'] as $assignment)
                                @php
                                    $destinationPageId = ($assignment['assignment_type'] == 1) ? 67 : 46;
                                @endphp
                                <li>
                                    <a class="text-muted"
                                       href="{{ siteUrl('page/'.$destinationPageId.'/details/'. (int)$assignment['id'].'/'.urlTitle($assignment['name'])) }}">
                                        {{e: $assignment['name'] }}</a>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @if(!empty($instance['hired_employees']) or !empty($instance['expenditures_made']) or !empty($instance['hired_employees']) or !empty($instance['description']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="information" class="testo-blu anchor page-subtitle">Altre informazioni</h3>
                        <div class="attributi">
                            @if(!empty($instance['hired_employees']) )
                                <div class="attributo">
                                    <span class="titolo">Numero dipendenti assunti:</span>
                                    <span class="mr-2 text-muted">
                                        {{e: $instance['hired_employees'] }}
                                    </span>
                                </div>
                            @endif
                            @if(!empty($instance['expected_expenditure']) )
                                <div class="attributo">
                                    <span class="titolo">Eventuale spesa prevista:</span>
                                    <span class="mr-2 text-muted">
                                        &euro; {{currency('filter=escape decimal_separator=, thousands_separator=.'): $instance['expected_expenditure'] }}
                                    </span>
                                </div>
                            @endif
                            @if(!empty($instance['expenditures_made']) )
                                <div class="attributo">
                                    <span class="titolo">Spese effettuate:</span>
                                    <span class="mr-2 text-muted">
                                        &euro; {{currency('filter=escape decimal_separator=, thousands_separator=.'): $instance['expenditures_made'] }}
                                    </span>
                                </div>
                            @endif
                            @if(!empty($instance['description']) )
                                <div>
                                    <span class="text-black" style="font-weight: 600;">Descrizione:</span>

                                    {{xss: $instance['description'] }}
                                    <br>
                                    @endif
                                </div>
                            @endif

                            {{-- Attach List --}}
                            {% include v1/layout/partials/attach_list %}

                            {{--  Created/Update Info --}}
                            {% include v1/layout/partials/created_updated_info %}

                            @if(!empty($instance['alerts']))
                                @php
                                    $anchorsNumber++;
                                @endphp
                                <h3 id="avvisi_relativi" class="testo-blu anchor page-subtitle">Avvisi relativi</h3>
                                <div class="griglia griglia-2 mb-5">
                                    @foreach( $instance['alerts'] as $element)
                                        <div class="card-richiamo">
                                            <span class="fas fa-file-contract"></span>
                                            <a href="{{ siteUrl('page/5/details/'. (int) $element['id'].'/'.urlTitle($element['object'])) }}">
                                                {{e: $element['object'] }}</a>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if(!empty($instance['outcomes']))
                                @php
                                    $anchorsNumber++;
                                @endphp
                                <h3 id="esiti_relativi" class="testo-blu anchor page-subtitle">Esiti relativi</h3>
                                <div class="griglia griglia-2 mb-5">
                                    @foreach( $instance['outcomes'] as $element)
                                        <div class="card-richiamo">
                                            <span class="fas fa-file-contract"></span>
                                            <a href="{{ siteUrl('page/5/details/'.$element['id'].'/'.urlTitle($element['object'])) }}">
                                                {{e: $element['object'] }}
                                            </a>
                                        </div>
                                    @endforeach
                                </div>
                            @endif

                            @if(!empty($instance['related_contest']) and $instance['related_contest']['typology'] == 'concorso')
                                @php
                                    $anchorsNumber++;
                                @endphp
                                <h3 id="concorso_relativo" class="testo-blu anchor page-subtitle">Bando di Concorso
                                    relativo</h3>
                                <div class="griglia griglia-2 mb-5">
                                    <div class="card-richiamo">
                                        <span class="fas fa-file-contract text-black"></span>
                                        <a class="text-muted"
                                           href="{{ siteUrl('page/5/details/'.$instance['related_contest']['id'].'/'.urlTitle($instance['related_contest']['object'])) }}">
                                            {{ $instance['related_contest']['object'] }}</a>
                                        <div>
                                            <small>
                                                @if(!empty($instance['related_contest']['expiration_date']))
                                                    <span style="font-weight: 600;" class="text-black">Data di
                                                        scadenza:</span> {{ date('d-m-Y', strtotime($instance['related_contest']['expiration_date'])) }}
                                                    <br>
                                                @endif

                                                @if(!empty($instance['related_contest']['expiration_time']))
                                                    <span style="font-weight: 600;" class="text-black">Orario
                                                            scadenza:</span> {{e: $instance['related_contest']['expiration_time'] }}
                                                @endif
                                            </small>
                                        </div>
                                    </div>
                                </div>
                            @endif

                        </div>
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