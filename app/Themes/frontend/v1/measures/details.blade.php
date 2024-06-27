<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Pagina per Provvedimenti amministrativi --}}

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
                        <h1 class="page-title">{{e: !empty($h1) ? $h1 : $instance['object'] }}</h1>
                    </div>

                    @if(!empty($instance['type']))
                        @php
                        $measureTypologies = config('measureTypologies', null, 'app');
                        @endphp
                        <h5 class="text-secondary page-subtitle">
                            <i class="fas fa-caret-right mr-1"></i>
                            Tipologia: {{e: $measureTypologies[$instance['type']] ?? '' }}
                        </h5>
                    @endif

                    <div class="attributi">
                        @if(!empty($instance['number']))
                            <div class="attributo">
                                <span class="titolo">Provvedimento numero:</span>
                                <span class="text-muted">
                                           {{e: $instance['number'] }}
                                        </span>
                            </div>
                        @endif

                        @if(!empty($instance['structures']))
                            @php
                                $i = 0;
                                $len = count($instance['structures'])-1;
                            @endphp
                            <div class="attributo">
                                <span class="titolo">Struttura responsabile:</span>
                                @foreach($instance['structures'] as $structure)
                                    <a class="text-muted"
                                       href="{{ siteUrl('page/40/details/'. (int) $structure['id'].'/'.urlTitle($structure['structure_name'])) }}"
                                       class="ml-1">
                                        {{e: $structure['structure_name'] }}
                                    </a>
                                    {{ $i++ < $len ? ', ' : '' }}
                                @endforeach
                            </div>
                        @endif

                        @if(!empty($instance['personnel']))
                            @php
                                $i = 0;
                                $len = count($instance['personnel'])-1;
                            @endphp
                            <div class="attributo">
                                <span class="titolo">Responsabile del provvedimento:</span>
                                @foreach($instance['personnel'] as $personnel)
                                    <a class="text-muted"
                                       href="{{ siteUrl('page/58/details/'.$personnel['id'].'/'.urlTitle($personnel['full_name'])) }}"
                                       class="ml-1">
                                        {{e: $personnel['full_name'] }}
                                    </a>
                                    {{ $i++ < $len ? ', ' : '' }}
                                @endforeach
                            </div>
                        @endif

                        @if(!empty($instance['date']))
                            <div class="attributo">
                                <span class="titolo">Data del provvedimento:</span>
                                <span class="text-muted">
                                           {{ date('d-m-Y', strtotime($instance['date'])) }}
                                        </span>
                            </div>
                        @endif
                    </div>

                    @if(!empty($instance['charges']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="information-charges" class="anchor testo-blu anchor mt-3 page-subtitle">Oneri informativi</h3>
                        <ul>
                            @foreach($instance['charges'] as $charge)
                                <li>
                                    <a class="text-muted" href="{{ siteUrl('page/33/details/'.$charge['id'].'/'.urlTitle($charge['title'])) }}"
                                       title="{{e: $charge['title'] }}">
                                        {{e: $charge['title'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @if(!empty($instance['relative_procedure_contraent']) || !empty($instance['relative_bdncp_procedure']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="contraent" class="testo-blu anchor mt-3 page-subtitle">Scelta del contraente</h3>
                        @if(!empty($instance['relative_procedure_contraent']))
                            <a href="{{ siteUrl('page/110/details/'.$instance['relative_procedure_contraent']['id'].'/'.urlTitle($instance['relative_procedure_contraent']['object'])) }}"
                               class="text-muted" style="font-size: 20px;">
                                {{e: $instance['relative_procedure_contraent']['object'] }}
                            </a>
                        @elseif(!empty($instance['relative_bdncp_procedure']))
                            <a href="{{ siteUrl('page/10/details/'.$instance['relative_bdncp_procedure']['id'].'/'.urlTitle($instance['relative_bdncp_procedure']['object'])) }}"
                               class="text-muted" style="font-size: 20px;">
                                {{e: $instance['relative_bdncp_procedure']['object'] }}
                            </a>
                        @endif
                    @endif

                    @if(!empty($instance['choice_of_contractor']))
                        <div>
                            <p>{{xss: $instance['choice_of_contractor'] }}</p>
                        </div>
                    @endif

                    @if(!empty($instance['notes']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="notes" class="testo-blu anchor page-subtitle">Note</h3>
                        {{xss: $instance['notes'] }}
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