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
                </div>
            </div>
        </div>
    </section>

    <section class="py-5">
        <div class="container">
            @if(!empty($instances['data']))

                @if(!empty($linkDownloadOpenData) )
                    <button id="btn-open-model-data" type="button" class="btn btn-primary btn-xs open-data-download-btn mb-4" data-bs-toggle="modal" data-bs-target="#modalOpenData">

                        <span class="fas fa-save"></span> Scarica Open data
                    </button>
                @endif

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
                                        @if(!empty($structure['reference_email']))
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
                                                <a href="{{ siteUrl('page/58/details/'.$resp['id'].'/'.urlTitle($resp['full_name'])) }}">
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