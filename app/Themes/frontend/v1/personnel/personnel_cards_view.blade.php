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

                    @if(empty($formFilter) and !empty($linkDownloadOpenData) && !empty($instances['data']) && $instances['data'] != '_hide')
                        {% include v1/layout/partials/open_data %}
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="lightgrey-bg-c2 py-5">

        <div class="container">
            @if(!empty($instances['data']) && $instances['data'] != '_hide')
                <div class="griglia griglia-3 mb-4">

                    {{-- Per ogni perosnale creo una card con le relative info --}}
                    @foreach($instances['data'] as $personnel)
                        <div class="card-icona">
                            <div class="wrapper-contenuto">
                                <h3>
                                    <span class="icona fas fa-user"></span>
                                    <a href="{{ siteUrl('page/'.$currentPageId.'/details/'.$personnel['id'].'/'.urlTitle($personnel['full_name'])) }}">
                                        {{e: (!empty($personnel['title']) ? $personnel['title'].'  ' : '') . $personnel['full_name'] }}
                                    </a>
                                </h3>
                                <div class="contenuto">
                                    {{-- Organo politico, mostrato solo nella sezione consiglio comunale --}}
                                    @if(!empty($personnel['role']))
                                        <div class="box-icona mb-1">
                                            Ruolo:&nbsp;
                                                <span class="grigio">
                                                    {{e: $personnel['role']['name'] }}
                                                </span>
                                        </div>
                                    @endif

                                    {{-- Organo politico, mostrato solo nella sezione consiglio comunale --}}
                                    @if(!empty($cityCouncil) && !empty($personnel['political_organ']))
                                        @php
                                            $i = 0;
                                        @endphp
                                        <div class="box-icona mb-1">
                                            Organo:&nbsp;
                                            @foreach($personnel['political_organ'] as $organ)
                                                <span class="grigio">
                                                    {{e: $organ['denomination'] }}
                                                    {{ ($i++ < count($personnel['political_organ'])-1) ? ',' : '' }}
                                                </span>
                                                &nbsp;
                                            @endforeach
                                        </div>
                                    @endif
                                    {{-- Incarico politico, mostrato nelle sezioni Consiglio comunale e Giunta e assessori --}}
                                    @if(!empty($personnel['political_role']))
                                        <div class="box-icona">
                                            Incarico di stampo politico:
                                            <span class="grigio">{{e: $personnel['political_role'] }}</span>
                                        </div>
                                    @endif
                                    <hr>
                                    <div class="contatti">
                                        @if(!empty($personnel['email']))
                                            <div>
                                                Email:
                                                <a href="mailto:{{escape_xss: $personnel['email'] }}">
                                                    {{e: $personnel['email'] }}
                                                </a>
                                            </div>
                                        @endif
                                        @if(!empty($personnel['phone']))
                                            <div>
                                                Telefono:
                                                <span class="grigio">
                                                    {{e: $personnel['phone'] }}
                                                </span>
                                            </div>
                                        @endif
                                    </div>

                                    @if(!empty($personnel['responsible_structures']) && !$councilAndCouncillors)
                                        <div class="contatti">
                                            <span>Referente per:</span>
                                            @php
                                                $i = 0;
                                            @endphp
                                            @foreach($personnel['responsible_structures'] as $structure)
                                                <a href="{{ siteUrl('page/40/details/'.$structure['id'].'/'.urlTitle($structure['structure_name'])) }}">
                                                    {{e: $structure['structure_name'] }}
                                                </a>
                                                {{ ($i++ < count($personnel['responsible_structures'])-1) ? ', ' : '' }}
                                            @endforeach
                                        </div>
                                    @endif
                                </div>
                            </div>
                            <a href="{{ siteUrl('page/'.$currentPageId.'/details/'.$personnel['id'].'/'.urlTitle($personnel['full_name'])) }}" class="pulsante-freccia" aria-label="Vai alla pagina {{e: $personnel['full_name'] }}">Vai alla pagina</a>
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

            @else
                @if($instances['data'] != '_hide')
                    <div class="box-card-shadow my-4">
                <h5 class="title">
                    <svg class="icon">
                        <use href="/assets/frontend/v1/css/sprites.svg#it-note"></use>
                    </svg>
                    Nessun elemento presente
                </h5>
            </div>
                @endif
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
