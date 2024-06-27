<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Pagina per Enti e società controllate --}}

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
                    <p id="description" class="testo-blu anchor sr-only"
                       style="visibility: hidden; margin: unset;padding: unset;">Informazioni generali</p>
                    <div class="titolo mb-4">
                        <h1 class="page-title">{{e: !empty($h1) ? $h1 : ( !empty($instance['company_name']) ? $instance['company_name'] : '') }}</h1>
                    </div>

                    @if(!empty($instance['website_url']))
                        <p>
                            <span class="fas fa-link testo-blu mr-1"></span>
                            <span>Sito web:</span>
                            <a class="text-muted" href="{{ $instance['website_url'] }}">
                                {{ parse_url($instance['website_url'], PHP_URL_HOST) }}
                            </a>
                        </p>
                    @endif

                    @if(!empty($instance['description']))
                        {{xss: $instance['description'] }}
                    @endif


                    @if(!empty($instance['participation_measure']) || !empty($instance['duration']) || !empty($instance['year_charges']))
                        <div class="attributi">
                            @php
                                $anchorsNumber++;
                            @endphp
                            <h3 id="anchor1" class="testo-blu anchor page-subtitle">Partecipazione dell'ente</h3>
                            @if(!empty($instance['participation_measure']))
                                <div class="attributo">
                                    <span class="titolo">Misura di partecipazione:</span>
                                    <span class="mr-2 text-muted">{{e: $instance['participation_measure'] }}</span>
                                </div>
                            @endif

                            @if(!empty($instance['duration']))
                                <div class="attributo">
                                    <span class="titolo">Durata dell'impegno:</span>
                                    <span class="mr-2 text-muted">{{e: $instance['duration'] }}</span>
                                </div>
                            @endif

                            @if(!empty($instance['year_charges']))
                                <h3 id="anchor5" class="testo-blu anchor page-subtitle mt-2">Oneri complessivi</h3>
                                {{xss: $instance['year_charges'] }}
                            @endif
                        </div>
                    @endif

                    @if(!empty($instance['treatment_assignments']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="anchor2" class="testo-blu anchor page-subtitle">Incarichi amministrativi e relativo trattamento
                            economico</h3>
                        {{xss: $instance['treatment_assignments'] }}
                    @endif

                    @if(!empty($instance['representatives']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="anchor3" class="testo-blu anchor page-subtitle">Rappresentati negli organi di governo</h3>
                        <ul>
                            @foreach($instance['representatives'] as $representative)
                                <li>
                                    <a class="text-muted" href="{{ siteUrl('page/58/details/'.$representative['id'].'/'.urlTitle($representative['full_name'])) }}">
                                        {{ ($representative['archived'] ? '<b>[Cessato]</b>' : '') }} {{e: $representative['full_name'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @if(!empty($instance['balance']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="anchor4" class="testo-blu anchor page-subtitle">Risultati di bilancio</h3>
                        {{xss: $instance['balance'] }}
                    @endif

                    @if(!empty($instance['inconferability_dec_link']))
                        <p>
                            <span class="fas fa-link testo-blu mr-1"></span>
                            Dichiarazione sulla insussistenza di una delle cause di inconferibilità dell'incarico:
                            <a class="text-muted" href="{{escape_xss: $instance['inconferability_dec_link'] }}">
                                {{ parse_url($instance['inconferability_dec_link'], PHP_URL_HOST) }}
                            </a>
                        </p>
                    @endif

                    @if(!empty($instance['incompatibility_dec_link']))
                        <p>
                            <span class="fas fa-link testo-blu mr-1"></span>
                            Dichiarazione sulla insussistenza di una delle cause di incompatibilità al conferimento
                            dell'incarico:
                            <a class="text-muted" href="{{escape_xss: $instance['incompatibility_dec_link'] }}">
                                {{ parse_url($instance['incompatibility_dec_link'], PHP_URL_HOST) }}
                            </a>
                        </p>
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