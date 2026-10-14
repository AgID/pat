<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Pegina per Articolazione degli uffici --}}

{% extends v1/layout/master %}

{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}

<main>
    <section class="my-2">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>{{e: !empty($h1) ? $h1 : $pageName }}</h1>

                    {{-- Inclusione di librerie esterne --}}

                    {{-- Contenuto della pagina --}}
                    {% include v1/layout/partials/page_content %}

                </div>
            </div>
        </div>
    </section>
    <section class="py-5">
        <div class="container">
            @if(!empty($instances['data']))

                @if(!empty($linkDownloadOpenData) && $instances['data'] != '_hide')
                    {% include v1/layout/partials/open_data %}
                @endif

                @if($instances['data'] != '_hide')
                        <div class="griglia griglia-3 mb-4">


                            {{-- Per ogni struttura creo una card con le relative info --}}
                            @foreach($instances['data'] as $structure)
                                <div class="card-icona">
                                    <div class="wrapper-contenuto">
                                        <div class="icona"><span class="fas fa-university"></span></div>
                                        <h3 class="mb-4">
                                            <a href="{{ siteUrl('page/'.$currentPageId.'/details/'.$structure['id'].'/'.urlTitle($structure['structure_name'])) }}">
                                                {{e: $structure['structure_name'] }}
                                            </a>
                                        </h3>
                                        <div class="contenuto">
                                            <div class="contatti">
                                                @if((is_null($structure['email_not_available']) or $structure['email_not_available'] == 1) and  !empty($structure['reference_email']))
                                                    <div>
                                                        <a href="mailto:{{ $structure['reference_email'] }}" target="_blank">
                                                            <span class="fas fa-envelope"></span>
                                                            {{e: $structure['reference_email'] }}
                                                        </a>
                                                    </div>
                                                @endif

                                                @if(!empty($structure['phone']))
                                                    <div>
                                                        <a href="tel: {{ $structure['phone'] }}">
                                                            <span class="fas fa-phone"></span>
                                                            {{e: $structure['phone'] }}
                                                        </a>
                                                    </div>
                                                @endif

                                                @if(!empty($structure['address']))
                                                    <div>
                                                        <a href="#">
                                                            <span class="fas fa-map-marker"></span>
                                                            {{e: $structure['address'] }}
                                                        </a>
                                                    </div>
                                                @endif

                                                @foreach($structure['responsibles'] as $resp)
                                                    <div>
                                                        <a href="{{ siteUrl('page/4/details/'.$resp['id'].'/'.urlTitle($resp['full_name'])) }}">
                                                            <span class="fas fa-user"></span>
                                                            {{e: $resp['full_name'] }}
                                                        </a>
                                                        {{ !empty($structure['ad_interim']) ? ' (responsabile ad interim)' : null }}
                                                    </div>
                                                @endforeach

                                            </div>
                                        </div>
                                    </div>
                                    <a href="{{ siteUrl('page/'.$currentPageId.'/details/'.$structure['id'].'/'.urlTitle($structure['structure_name'])) }}"
                                       class="pulsante-freccia"
                                       aria-label="Vai alla pagina {{e: $structure['structure_name'] }}">Vai alla pagina</a>
                                </div>
                            @endforeach
                        </div>

                        {{-- Paginazione della tabella --}}
                        {{ paginateBootstrap($instances) }}

                        @if(!empty($_institution_info['show_update_date']) && !empty($latsUpdatedElement))
                        @php
                            $hideCreatedAt = false;
                        @endphp
                            <p class="data-creazione mt-5" style="font-size: 14px;">
                                <span class="icona far fa-clock"></span>
                                <strong>
                                    @if(!empty($hideCreatedAt))
                                        {{ !empty($latsUpdatedElement['updated_at']) ? 'Contenuto aggiornato il ' . date('d/m/Y', strtotime((string)$latsUpdatedElement['updated_at'])) : date('d/m/Y', strtotime((string)$latsUpdatedElement['created_at'])) }}
                                        @else
                                    {{ !empty($latsUpdatedElement['created_at']) ? 'Contenuto creato il ' . date('d/m/Y', strtotime((string)$latsUpdatedElement['created_at'])) : null }}
                                    {{ !empty($latsUpdatedElement['updated_at']) ? ' - Aggiornato il ' . date('d/m/Y', strtotime((string)$latsUpdatedElement['updated_at'])) : date('d/m/Y', strtotime((string)$latsUpdatedElement['created_at'])) }}
                                    @endif
                                </strong>
                            </p>
                        @endif
                @endif

            @else
                <div class="box-card-shadow my-4">
                <h5 class="title">
                    <svg class="icon">
                        <use href="/assets/frontend/v1/css/sprites.svg#it-note"></use>
                    </svg>
                    Nessun elemento presente
                </h5>
            </div>
            @endif
        </div>
    </section>
</main>


{{-- Bottom Menu --}}
{% include v1/layout/partials/bottom_menu %}

{{-- Cards Link --}}
{% include v1/layout/partials/card_link %}

{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
{% endblock %}
