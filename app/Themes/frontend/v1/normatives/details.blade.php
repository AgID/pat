<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Pagina per Normative --}}

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
                        @if(!empty($instance['act_type']))
                            <div class="attributo">
                                <span class="titolo">Tipologia:</span>
                                <span class="mr-2 text-muted">
                                    {{e: $type  }}
                                    </span>
                            </div>
                        @endif

                        @if(!empty($instance['number']))
                            <div class="attributo">
                                <span class="titolo">Numero:</span>
                                <span class="mr-2 text-muted">
                                    {{ $instance['number']  }}
                                    </span>
                            </div>
                        @endif

                        @if(!empty($instance['protocol']))
                            <div class="attributo">
                                <span class="titolo">Protocollo:</span>
                                <span class="mr-2 text-muted">
                                    {{ $instance['number']  }}
                                    </span>
                            </div>
                        @endif

                        @if(!empty($instance['issue_date']))
                            <div class="attributo">
                                <span class="titolo">Data promulgazione:</span>
                                <span class="mr-2 text-muted">
                                    {{date('d-m-Y|date'): $instance['issue_date'] }}
                                    </span>
                            </div>
                        @endif

                        @if(!empty($instance['normative_topic']))
                            <div class="attributo">
                                <span class="titolo">Argomento della Normativa:</span>
                                <span class="mr-2 text-muted">
                                    {{e: $topic }}
                                    </span>
                            </div>
                        @endif
                    </div>

                    @if(!empty($instance['structures']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="proceedings" class="testo-blu page-subtitle mt-3">
                            Strutture organizzative di riferimento:
                        </h3>
                        @foreach($instance['structures'] as $structure)
                            <p class="pl-4">
                                <span class="fas fa-caret-right"></span>
                                <a class="text-muted" href="{{ siteUrl('page/40/details/'.$structure['id'].'/'.urlTitle($structure['structure_name'])) }}">
                                    {{e: $structure['structure_name'] }}
                                </a>
                            </p>
                        @endforeach
                    @endif

                    @if(!empty($instance['description']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="description" class="testo-blu page-subtitle mt-3">Descrizione:</h3>
                        {{xss: $instance['description'] }}
                    @endif

                    @if(!empty($instance['normative_link']))
                        <p>
                            <span class="fas fa-link mr-1"></span>
                            <strong>Link norma su portale Normattiva: </strong>
                            <a class="text-muted" href="{{escape_xss: $instance['normative_link'] }}">
                                {{ parse_url($instance['normative_link'], PHP_URL_HOST) }}
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