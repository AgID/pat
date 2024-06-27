<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Pagina per Oneri informativi e obblighi --}}

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

                    @if($noRequiredPublication)
                        {{-- Alert contenuti non più obbligatori --}}
                        {% include v1/layout/partials/no_required_publication_message %}
                    @endif

                    @if(!empty($instance['citizen']) || !empty($instance['companies']) || !empty($instance['expiration_date']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <p id="generic-info" class="testo-blu anchor sr-only"
                           style="visibility: hidden; margin: unset;padding: unset;">Informazioni generali</p>
                    @endif

                    <div class="titolo mb-4">
                        <h1 class="page-title">{{e: !empty($h1) ? $h1 : $instance['title'] }}</h1>
                    </div>

                    <div class="attributi">
                        @if(!empty($instance['citizen']))
                            <div class="attributo">
                                <span class="titolo">Rivolto a:</span>
                                <span class="mr-2 text-muted"><strong>Cittadini</strong></span>
                            </div>
                        @endif

                        @if(!empty($instance['companies']))
                            <div class="attributo">
                                <span class="titolo">Rivolto a:</span>
                                <span class="mr-2 text-muted"><strong>Imprese</strong></span>
                            </div>
                        @endif

                        @if(!empty($instance['expiration_date']))
                            <div class="attributo">
                                <span class="titolo">Data di scadenza:</span>
                                <span class="mr-2 text-muted"><strong> {{date('d-m-Y|date'): $instance['expiration_date'] }} </strong></span>
                            </div>
                        @endif

                        @if(!empty($instance['proceedings']))
                            @php
                                $anchorsNumber++;
                            @endphp
                            <h3 id="anchor4" class="testo-blu anchor page-subtitle mt-2">Procedimenti relativi</h3>
                            <ul>
                                @foreach($instance['proceedings'] as $proceeding)
                                    <li>
                                        <a class="text-muted" href="{{ siteUrl('page/98/details/'.$proceeding['id'].'/'.urlTitle($proceeding['name'])) }}">
                                            {{e: $proceeding['name'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        @if(!empty($instance['measures']))
                            @php
                                $anchorsNumber++;
                            @endphp
                            <h3 id="measures" class="testo-blu anchor page-subtitle mt-2">Provvedimenti associati</h3>
                            <ul>
                                @foreach($instance['measures'] as $measure)
                                    <li>
                                        <a class="text-muted" href="{{ siteUrl('page/9/details/'.$measure['id'].'/'.urlTitle($measure['object'])) }}">
                                            {{e: $measure['object'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        @if(!empty($instance['regulations']))
                            @php
                                $anchorsNumber++;
                            @endphp
                            <h3 id="regulations" class="testo-blu anchor page-subtitle mt-2">Regolamenti o altra documentazione</h3>
                            <ul>
                                @foreach($instance['regulations'] as $regulation)
                                    <li>
                                        <a class="text-muted" href="{{ siteUrl('page/29/details/'.$regulation['id'].'/'.urlTitle($regulation['title'])) }}">
                                            {{e: $regulation['title'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        @if(!empty($instance['normative']))
                            @php
                                $anchorsNumber++;
                            @endphp
                            <h3 id="normative" class="testo-blu anchor page-subtitle mt-2">Riferimenti normativi</h3>
                            <ul>
                                <li>
                                    <a class="text-muted" href="{{ siteUrl('page/24/details/'.$instance['normative']['id'].'/'.urlTitle($instance['normative']['name'])) }}">
                                        {{e: $instance['normative']['name'] }}
                                    </a>
                                </li>
                            </ul>
                        @endif

                        @if(!empty($instance['description']))
                            <div>
                                {{xss: $instance['description'] }}
                            </div>
                        @endif

                        @if(!empty($instance['info_url']))
                            <div class="mb-4">
                                <span class="fas fa-link mr-1 testo-blu"></span>
                                <span class="text-black"><strong>Link maggiori informazioni: </strong></span>
                                <a class="text-muted" href="{{xss: $instance['info_url'] }}">
                                    {{ parse_url($instance['info_url'], PHP_URL_HOST) }}
                                </a>
                            </div>
                        @endif
                    </div>
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