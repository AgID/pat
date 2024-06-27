<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Pagina per i Procedimenti dell'ente --}}

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
                        <h1 class="page-title">{{e: !empty($h1) ? $h1 : $instance['name'] }}</h1>
                    </div>

                    <div class="attributi">
                        @if(!empty($instance['responsibles']))
                            @php
                                $i = 0;
                                $len = count($instance['responsibles'])-1;
                            @endphp
                            <div class="attributo">
                                <span class="fas fa-user testo-blu"></span>
                                <span class="titolo">Responsabile di procedimento:</span>
                                @foreach($instance['responsibles'] as $responsible)
                                    <a class="text-muted"
                                       href="{{ siteUrl('page/58/details/'.$responsible['id'].'/'.urlTitle($responsible['full_name'])) }}">
                                        {{ ($responsible['archived'] ? '<b>[Elemento archiviato]</b>' : '') }} {{e: $responsible['full_name'] }}
                                    </a>
                                    {{ ($i++ < $len ? ', ' : '') }}
                                @endforeach
                            </div>
                        @endif

                        @if(!empty($instance['measure_responsibles']))
                            @php
                                $i = 0;
                                $len = count($instance['measure_responsibles'])-1;
                            @endphp
                            <div class="attributo">
                                <span class="fas fa-user testo-blu"></span>
                                <span class="titolo">Responsabile di provvedimento:</span>
                                @foreach($instance['measure_responsibles'] as $measure_responsible)
                                    <a class="text-muted"
                                       href="{{ siteUrl('page/58/details/'.$measure_responsible['id'].'/'.urlTitle($measure_responsible['full_name'])) }}">
                                        {{ ($measure_responsible['archived'] ? '<b>[Elemento archiviato]</b>' : '') }}  {{e: $measure_responsible['full_name'] }}
                                    </a>
                                    {{ ($i++ < $len ? ', ' : '') }}
                                @endforeach
                            </div>
                        @endif

                        @if(!empty($instance['substitute_responsibles']))
                            @php
                                $i = 0;
                                $len = count($instance['substitute_responsibles'])-1;
                            @endphp
                            <div class="attributo">
                                <span class="fas fa-user testo-blu"></span>
                                <span class="titolo">Responsabile sostitutivo:</span>
                                @foreach($instance['substitute_responsibles'] as $substitute_responsible)
                                    <a class="text-muted"
                                       href="{{ siteUrl('page/58/details/'.$substitute_responsible['id'].'/'.urlTitle($substitute_responsible['full_name'])) }}">
                                        {{ ($substitute_responsible['archived'] ? '<b>[Elemento archiviato]</b>' : '') }} {{e: $substitute_responsible['full_name'] }}
                                    </a>
                                    {{ ($i++ < $len ? ', ' : '') }}
                                @endforeach
                            </div>
                        @endif
                    </div>

                    @if(!empty($instance['offices_responsibles']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="offices" class="anchor testo-blu page-subtitle mt-3">Uffici responsabili</h3>
                        <ul>
                            @foreach($instance['offices_responsibles'] as $office)
                                <li>
                                    <a class="text-muted"
                                       href="{{ siteUrl('page/40/details/'.$office['id'].'/'.urlTitle($office['structure_name'])) }}">
                                        {{ ($office['archived'] ? '<b>[Elemento archiviato]</b>' : '')  }} {{e: $office['structure_name'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @if(!empty($instance['description']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="description" class="anchor testo-blu page-subtitle">Descrizione</h3>
                        {{xss: $instance['description'] }}
                    @endif

                    @if(!empty($instance['offices_responsibles']) || !empty($instance['to_contacts']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="to-contact" class="anchor testo-blu page-subtitle">Chi contattare</h3>
                        @if(!empty($instance['offices_responsibles']))
                            @php
                                $anchorsNumber++;
                            @endphp
                            <div id="office-to-contact">
                                <ul>
                                    @foreach($instance['offices_responsibles'] as $office)
                                        <li>
                                            <a class="text-muted"
                                               href="{{ siteUrl('page/40/details/'.$office['id'].'/'.urlTitle($office['structure_name'])) }}">
                                                {{ ($office['archived'] ? '<b>[Elemento archiviato]</b>' : '') }} {{e: $office['structure_name'] }}
                                            </a>
                                        </li>
                                        <p class="pl-2">
                                            @if(!empty($office['belong_name']))
                                                <span class="fas fa-landmark testo-blu mr-2"></span>Struttura di
                                                appartenenza:
                                                <a class="text-decoration-none"
                                                   href="{{ siteUrl('page/40/details/'.$office['belong_id'].'/'.urlTitle($office['belong_name'])) }}">
                                                    {{e: $office['belong_name'] }}
                                                </a>
                                                <br>
                                            @endif

                                            @if(!empty($office['address']))
                                                <span class="fas fa-map-marker testo-blu mr-2"></span>Indirizzo:
                                                {{e: $office['address'] }}<br>

                                            @endif

                                            @if(!empty($office['reference_email']))
                                                <span class="fas fa-envelope testo-blu mr-2"></span>Email:
                                                <a href="mailto:{{$office['reference_email']}}">
                                                    {{e: $office['reference_email'] }}
                                                </a>
                                            @endif
                                        </p>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(!empty($instance['to_contacts']))
                            <div id="personnel-to-contact">
                                <h6>
                                    Personale da contattare:
                                </h6>
                                <p class="pl-2">
                                    @php
                                        $i = 0;
                                        $len = count($instance['to_contacts'])-1;
                                    @endphp
                                    @foreach($instance['to_contacts'] as $personnel)
                                        <a class="text-muted"
                                           href="{{ siteUrl('page/58/details/'.$personnel['id'].'/'.urlTitle($personnel['full_name'])) }}">
                                            {{ ($personnel['archived'] ? '<b>[Elemento archiviato]</b>' : '') }} {{e: $personnel['full_name'] }}
                                        </a>
                                        {{ $i++ < $len ? ', ' : '' }}
                                    @endforeach
                                </p>
                            </div>
                        @endif
                    @endif

                    @if(!empty($instance['other_structures']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="other-offices" class="anchor testo-blu page-subtitle">Altre strutture che si occupano
                            del
                            procedimento</h3>
                        <ul>
                            @foreach($instance['other_structures'] as $otherStructure)
                                <li>
                                    <a class="text-muted"
                                       href="{{ siteUrl('page/40/details/'.$otherStructure['id'].'/'.urlTitle($otherStructure['structure_name'])) }}">
                                        {{ ($otherStructure['archived'] ? '<b>[Elemento archiviato]</b>' : '')  }} {{e: $otherStructure['structure_name'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @if(!empty($instance['silence_consent']) || !empty($instance['declaration']) || !empty($instance['deadline']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="conclusion" class="anchor testo-blu page-subtitle">Termine di conclusione</h3>
                        <div class="attributi">
                            <div class="attributo">
                                <span class="titolo">Conclusione tramite silenzio assenso:</span>
                                <span class="mr-2 text-muted">
                                   {{ !empty($instance['silence_consent']) ? 'Si' : 'No' }}
                                </span>
                            </div>
                            <div class="attributo">
                                <span class="titolo">Conclusione tramite dichiarazione dell'interessato:</span>
                                <span class="mr-2 text-muted">
                                   {{ !empty($instance['declaration']) ? 'Si' : 'No' }}
                                </span>
                            </div>

                            <div class="attributo">
                                   {{e: $instance['deadline'] }}
                            </div>
                        </div>
                    @endif

                    @if(!empty($instance['costs']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="costs" class="anchor testo-blu page-subtitle mt-3">Costi per l'utenza</h3>
                        {{xss: $instance['costs'] }}
                    @endif

                    @if(!empty($instance['modules']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="modules" class="anchor testo-blu page-subtitle mt-3">Modulistica per il procedimento</h3>
                        <div class="griglia griglia-2 mb-5">
                            @foreach( $instance['modules'] as $module)
                                <div class="card-richiamo">
                                    <span class="fas fa-file-contract text-black"></span>
                                    <a class="text-muted" href="{{ siteUrl('page/101/details/'.$module['id'].'/'.urlTitle($module['title'])) }}"
                                       data-toggle="tooltip"
                                       data-placement="top"
                                       title="{{ characterLimiter(strip_tags($module['title']), 60) }}"
                                    >
                                        {{e: characterLimiter($module['title'], 60) }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if(!empty($instance['regulations']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="regulations" class="anchor testo-blu page-subtitle mt-3">Regolamenti per il procedimento</h3>
                        <div class="griglia griglia-2 mb-5">
                            @foreach( $instance['regulations'] as $regulation)
                                <div class="card-richiamo">
                                    <span class="fas fa-file-contract text-black"></span>
                                    <a class="text-muted" href="{{ siteUrl('page/'.$regulation['public_in_id'].'/details/'.$regulation['id'].'/'.urlTitle($regulation['title'])) }}"
                                       data-toggle="tooltip"
                                       data-placement="top"
                                       title="{{ characterLimiter(strip_tags($regulation['title']), 60) }}"
                                    >
                                        {{e: characterLimiter($regulation['title'],60) }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if(!empty($instance['normatives']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="normative-references" class="anchor testo-blu page-subtitle mt-3">Riferimenti normativi</h3>
                        <div class="griglia griglia-2 mb-5">
                            @foreach( $instance['normatives'] as $normative)
                                <div class="card-richiamo">
                                    <span class="fas fa-file-contract text-black"></span>
                                    <a class="text-muted" href="{{ siteUrl('page/24/details/'.$normative['id'].'/'.urlTitle($normative['name'])) }}"
                                       data-toggle="tooltip"
                                       data-placement="top"
                                       title="{{ characterLimiter(strip_tags($normative['name']), 60) }}"
                                    >
                                        {{e: characterLimiter($normative['name'], 60) }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if(!empty($instance['monitoring_datas']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="monitoring_data" class="anchor testo-blu anchor mt-4 page-subtitle">Monitoraggio tempi
                            procedimentali</h3>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm mb-4">
                                <thead>
                                <tr class="intestazione-tabella">
                                    <th scope="col">Anno</th>
                                    <th scope="col">Numero Procedimenti Conclusi</th>
                                    <th scope="col">Giorni Medi Conclusione</th>
                                    <th scope="col">Percentuale Procedimenti Conclusi</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($instance['monitoring_datas'] as $monitoring)
                                    <tr>
                                        <td>{{e: !empty($monitoring['year']) ? $monitoring['year'] : '' }}</td>
                                        <td>
                                            {{e: !empty($monitoring['year_concluded_proceedings']) ? $monitoring['year_concluded_proceedings'] : '' }}
                                        </td>
                                        <td>{{e: !empty($monitoring['conclusion_days']) ? $monitoring['conclusion_days'] : '' }}</td>
                                        <td>{{e: !empty($monitoring['percentage_year_concluded_proceedings']) ? $monitoring['percentage_year_concluded_proceedings'] : '' }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
                    @endif

                    @if(!empty($instance['url_service']) || !empty($instance['service_time']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="url_service" class="anchor testo-blu page-subtitle">Servizio online</h3>
                        @if(!empty($instance['url_service']))
                            <span class="fas fa-link testo-blu mr-1"></span>
                            <a href="{{ $instance['url_service'] }}" title="Link servizio online" target="_blank">
                                {{e: $instance['url_service'] }}
                            </a>
                        @else
                            <div>
                                <span style="font-weight: 600;">Tempi previsti per attivazione servizio
                                    online:</span> {{e: $instance['service_time'] }}</div>
                        @endif
                    @endif

                    @if(!empty($instance['charges']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="information-charges" class="anchor testo-blu mt-4 page-subtitle mt-3">Oneri informativi</h3>
                        <ul>
                            @foreach($instance['charges'] as $charge)
                                <li>
                                    <a href="{{ siteUrl('page/33/details/'.$charge['id'].'/'.urlTitle($charge['title'])) }}"
                                       title="{{ characterLimiter(strip_tags($charge['title']), 60) }}"
                                    >
                                        {{e: $charge['title'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @if(!empty($instance['protection_instruments']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="instruments-protection" class="anchor testo-blu mt-4 page-subtitle mt-3">Strumenti di tutela</h3>
                        <div class="mb-4">
                            {{e: $instance['protection_instruments'] }}
                        </div>

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
<script>
    {{-- Per la gestione della visualizzazione del chi contattare --}}
    {{-- In base al valore del campo Visualizzazione del Chi Contattare del procedimento --}}
    // Per la gestione della visualizzazione del chi contattare
    let order = {{ $instance['contact'] }};
    let elements = {
        3: $('#personnel-to-contact'),
        4: $('#office-to-contact')
    };

    if (order != 1) {
        if (order == 2) {
            $('#personnel-to-contact').insertBefore($('#office-to-contact'))
        }

        if ([3, 4].includes(order)) {
            elements[order].remove();
        }
    }
    {{-- Fine gestione della visualizzazione del chi contattare --}}

</script>
{% endblock %}