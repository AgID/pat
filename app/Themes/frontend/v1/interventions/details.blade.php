<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Pagina per Interventi --}}

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
                    @if(!empty($genericInfo))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <p id="generic-info" class="testo-blu anchor sr-only"
                           style="visibility: hidden; margin: unset;padding: unset;">Informazioni generali</p>
                    @endif
                    <div class="titolo mb-4">
                        <h1 class="page-title">{{e: !empty($h1) ? $h1 : $instance['name'] }}</h1>
                    </div>

                    @if(!empty($genericInfo))
                        <div class="attributi">
                            @if(!empty($instance['time_limits']))
                                <div class="attributo">
                                    <span class="titolo">Termini temporali per i provvedimenti straordinari:</span>
                                    <span class="text-muted">
                                            {{date('d-m-Y|date'): $instance['time_limits'] }}
                                        </span>
                                </div>
                            @endif

                            @if(!empty($instance['estimated_cost']))
                                <div class="attributo">
                                    <span class="titolo">Costo interventi stimato:</span>
                                    <span class="text-muted">
                                            &euro;  {{currency('filter=escape_xss decimal_separator=, thousands_separator=.'): $instance['estimated_cost'] }}
                                        </span>
                                </div>
                            @endif

                            @if(!empty($instance['effective_cost']))
                                <div class="attributo">
                                        <span class="titolo">Costo interventi effettivo:</span>
                                    <span class="text-muted">
                                            &euro; {{currency('filter=escape_xss decimal_separator=, thousands_separator=.'): $instance['effective_cost'] }}
                                        </span>
                                </div>
                            @endif
                        </div>
                    @endif

                    @if(!empty($instance['description']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="description" class="testo-blu anchor page-subtitle mt-2">Descrizione</h3>
                        {{xss: $instance['description'] }}
                    @endif

                    @if(!empty($instance['measures']))
                        @php
                            $anchorsNumber++;
                            $i = 0;
                            $len = count($instance['measures'])-1;
                        @endphp
                        <h3 id="measures" class="testo-blu anchor page-subtitle mt-2">Provvedimenti</h3>
                        <p>
                            @foreach($instance['measures'] as $measure)
                                <a class="text-muted" href="{{ siteUrl('page/9/details/'.$measure['id'].'/'.urlTitle($measure['object'])) }}">
                                    {{e: $measure['object'] }}
                                </a>
                                {{ $i++ < $len ? ', ' : '' }}
                            @endforeach
                        </p>
                    @endif

                    @if(!empty($instance['regulations']))
                        @php
                            $anchorsNumber++;
                            $i = 0;
                            $len = count($instance['regulations'])-1;
                        @endphp
                        <h3 id="regulations" class="testo-blu anchor page-subtitle mt-2">Regolamenti</h3>
                        <p>
                            @foreach($instance['regulations'] as $regulation)
                                <a class="text-muted" href="{{ siteUrl('page/29/details/'.$regulation['id'].'/'.urlTitle($regulation['title'])) }}">
                                    {{e: $regulation['title'] }}
                                </a>
                                {{ $i++ < $len ? ', ' : '' }}
                            @endforeach
                        </p>
                    @endif

                    @if(!empty($instance['derogations']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="normative" class="testo-blu anchor page-subtitle mt-2">Norme derogate e Motivazioni</h3>
                        {{xss: $instance['derogations'] }}
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