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

                    @if( empty($formFilter) and !empty($linkDownloadOpenData) && !empty($instances['data']))
                        <button type="button" class="btn btn-xs btn-primary open-data-download-btn" id="btn-open-model-data">
                            <span class="fas fa-save"></span> Scarica Open data
                        </button>
                    @endif
                </div>
            </div>
        </div>
    </section>

    <section class="lightgrey-bg-c2 py-5">

        <div class="container">
            @if(!empty($instances['data']))
                <div class="griglia griglia-3 mb-4">

                    {{-- Per ogni perosnale creo una card con le relative info --}}
                    @foreach($instances['data'] as $personnel)
                        <div class="card-icona">
                            <div class="wrapper-contenuto">
                                <h3>
                                    <span class="icona fas fa-user"></span>
                                    <a href="{{ siteUrl('page/'.$currentPageId.'/details/'.$personnel['id'].'/'.urlTitle($personnel['full_name'])) }}">
                                        {{e: $personnel['full_name'] }}
                                    </a>
                                </h3>
                                <div class="contenuto">
                                    {{-- Incarico politico, mostrato nelle sezioni Consiglio comunale e Giunta e assessori --}}
                                    @if(!empty($personnel['political_role']))
                                        <div class="box-icona">
                                            <p>Incarico di stampo politico:
                                                <span class="grigio">{{e: $personnel['political_role'] }}</span>
                                            </p>
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
                    <p class="data-creazione mt-5" style="font-size: 14px;">
                        <span class="icona far fa-clock"></span>
                        <strong>{{ !empty($latsUpdatedElement['created_at']) ? 'Contenuto creato il ' . date('d-m-Y', strtotime((string)$latsUpdatedElement['created_at'])) : null }}
                            {{ !empty($latsUpdatedElement['updated_at']) ? ' - Aggiornato il ' . date('d-m-Y', strtotime((string)$latsUpdatedElement['updated_at'])) : date('d-m-Y', strtotime((string)$latsUpdatedElement['created_at'])) }}</strong>
                    </p>
                @endif

            @else
                <h5 class="font-weight-bold">Nessun elemento presente</h5>
            @endif

        </div>
    </section>
</main>


{{-- Bottom Menu --}}
{% include v1/layout/partials/bottom_menu %}

{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
{% endblock %}