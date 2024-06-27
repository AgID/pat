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
                    <h1 class="page-title">{{e: !empty($h1) ? $h1 : $pageName }}</h1>

                    {{-- Contenuto della pagina --}}
                    {% include v1/layout/partials/page_content %}

                </div>
            </div>
        </div>
    </section>
    @php
        $noData = true;
    @endphp
    <section class="lightgrey-bg-c2 py-5">
        <div class="container">

            @if(!empty($instances['data']))

                @php
                    $noData = false;
                @endphp

                @if(!empty($linkDownloadOpenData) )
                    <button id="btn-open-model-data" type="button" class="btn btn-primary btn-xs open-data-download-btn mb-4" data-bs-toggle="modal" data-bs-target="#modalOpenData">
                        <span class="fas fa-save"></span> Scarica Open data
                    </button>
                @endif

                <div class="griglia griglia-3 mb-4">

                    {{-- Per ogni struttura creo una card con le relative info --}}
                    @foreach($instances['data'] as $commission)
                        <div class="card-icona">
                            <div class="wrapper-contenuto mb-1">
                                <div class="mb-5">
                                    <div class="icona d-inline">
                                        @if(!empty($commission['image']))
                                            {{ getImage($commission['image'], $_institution_info['short_institution_name'], 'Immagine della commissione '.$commission['name']) }}
                                        @else
                                            <span class="fas fa-users"></span>
                                        @endif
                                    </div>
                                    <h3 class="mb-4 d-inline">
                                        <a href="{{ siteUrl('page/'.$currentPageId.'/details/'.$commission['id'].'/'.urlTitle($commission['name'])) }}">
                                            {{e: $commission['name'] }}
                                        </a>
                                    </h3>
                                </div>
                            </div>
                            <a href="{{ siteUrl('page/'.$currentPageId.'/details/'.$commission['id'].'/'.urlTitle($commission['name'])) }}"
                               class="pulsante-freccia" aria-label="Vai alla pagina {{e: $commission['name'] }}">
                                Vai alla pagina
                            </a>
                        </div>
                    @endforeach
                </div>

                {{-- Paginazione della tabella --}}
                {{ paginateBootstrap($instances) }}

                @if(!empty($_institution_info['show_update_date']) && !empty($instances) && !empty($latsUpdatedElement))
                    <p class="data-creazione" style="font-size: 14px;">
                        <span class="icona far fa-clock"></span>
                        <strong>{{ !empty($latsUpdatedElement['created_at']) ? 'Contenuto creato il ' . date('d-m-Y', strtotime((string)$latsUpdatedElement['created_at'])) : null }}
                            {{ !empty($latsUpdatedElement['updated_at']) ? ' - Aggiornato il ' . date('d-m-Y', strtotime((string)$latsUpdatedElement['updated_at'])) : date('d-m-Y', strtotime((string)$latsUpdatedElement['created_at'])) }}</strong>
                    </p>
                @endif
            @endif
        </div>
    </section>


    @if(!empty($personnelInstances['data']))
        <section class="py-5">
            <div class="container">
                @php
                    $noData = false;
                @endphp
                <h3 class="page-subtitle">
                    Personale
                </h3>
                <div class="griglia griglia-3 mb-4">
                    {{-- Per ogni struttura creo una card con le relative info --}}
                    @foreach($personnelInstances['data'] as $personnel)
                        <div class="card-icona">
                            <div class="wrapper-contenuto mb-1">
                                <div class="mb-5">
                                    <div class="icona d-inline">
                                        @if(!empty($personnel['photo']))
                                            <img class="img-evidenza-personale" title="Foto Personale"
                                                 src="{{baseUrl('media/' . $_institution_info['short_institution_name'] . '/assets/images/' . $personnel['photo'])}}"
                                                 alt="Foto di {{escape_xss: $personnel['full_name'] }}">
                                        @else
                                            <span class="fas fa-users"></span>
                                        @endif
                                    </div>
                                    <h3 class="mb-4 d-inline">
                                        <a href="{{ siteUrl('page/4/details/'.$personnel['id'].'/'.urlTitle($personnel['full_name'])) }}">
                                            {{e: $personnel['full_name'] }}
                                        </a>
                                    </h3>
                                </div>
                            </div>
                            <a href="{{ siteUrl('page/4/details/'.$personnel['id'].'/'.urlTitle($personnel['full_name'])) }}"
                               class="pulsante-freccia" aria-label="Vai alla pagina {{e: $personnel['full_name'] }}">
                                Vai alla pagina
                            </a>
                        </div>
                    @endforeach
                </div>

                {{-- Paginazione della tabella --}}
                {{ paginateBootstrap($personnelInstances) }}

                @if(!empty($_institution_info['show_update_date']) && !empty($personnelInstances) && !empty($pLatsUpdatedElement))
                    <p class="data-creazione" style="font-size: 14px;">
                        <span class="icona far fa-clock"></span>
                        <strong>{{ !empty($pLatsUpdatedElement['created_at']) ? 'Contenuto creato il ' . date('d-m-Y', strtotime((string)$pLatsUpdatedElement['created_at'])) : null }}
                            {{ !empty($pLatsUpdatedElement['updated_at']) ? ' - Aggiornato il ' . date('d-m-Y', strtotime((string)$pLatsUpdatedElement['updated_at'])) : date('d-m-Y', strtotime((string)$pLatsUpdatedElement['created_at'])) }}</strong>
                    </p>
                @endif
            </div>
        </section>
    @endif
    @if($noData)
        <section class="lightgrey-bg-c2 pb-5">
            <div class="container">
                <h5 class="font-weight-bold">Nessun elemento presente</h5>
            </div>
        </section>
    @endif

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