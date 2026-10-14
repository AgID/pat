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

<main>
    <section class="my-2">
        <div class="container">
            <div class="row">
                <div class="col-12">

                    {{-- Nome pagina --}}
                    <div class="div-title">
                        <h1>{{e: !empty($h1) ? $h1 :  $pageName }}</h1>
                        <div class="button-normative">
                        </div>
                    </div>

                    {{-- Inclusione di librerie esterne --}}

                    {{-- Contenuto della pagina --}}
                    {% include v1/layout/partials/page_content %}

                </div>
            </div>
        </div>
    </section>


    <br>
    <!-- Open Data (scarica i dati di entrambi gli archivi) -->

    <section id="risultati-ricerca">
        <div class="container">
            <div class="" style="display: flex">
                <div class=""  style="display: inline-flex;">
                    @if(!empty($linkDownloadOpenData) && !empty($instances['data']))
                        @if( empty($filterUsed) )
                            <button id="btn-open-model-data" type="button" class="btn btn-primary btn-xs open-data-download-btn" data-bs-toggle="modal" data-bs-target="#modalOpenData">
                                <span class="fas fa-save"></span> Scarica Open data
                            </button>{{nbs(2)}}
                        @else
                            <button id="btn-open-model-search-data" type="button" class="btn btn-primary btn-xs open-data-download-btn" data-bs-toggle="modal" data-bs-target="#modalOpenData">
                                <span class="fas fa-save"></span> Scarica Open data (Risultati della ricerca)
                            </button>{{nbs(2)}}
                        @endif
                    @endif
                </div>
            </div>
        </div>
    </section>

    <!-- Open Data FINE -->
    <br>

    <style>
        .callout-pat-fo {
            border-left: 5px solid var(--primary);
            padding-left: .6rem;
        }
    </style>


    <section id="risultati-ricerca">
        <div class="container">
            @if(!empty($title))
                <div class="callout-pat-fo">
                    <h3 class="page-subtitle">
                        Bandi di gara dal 01/01/2024
                    </h3>
                </div>
                <hr>
            @endif

            {{-- Tabella generata dal server --}}
            @if(!empty($table))
                {{ $table }}

                {{-- Paginazione della tabella --}}
                @if(!empty($instances))
                    {{ paginateBootstrap($instances) }}
                @endif
            @else
                <h5 class="font-weight-bold mb-5">Nessun elemento presente</h5>
            @endif
        </div>
    </section>


    <section id="risultati-ricerca" class="mb-5">
        <div class="container">
            @if(!empty($titlePre2024))
                <div class="callout-pat-fo">
                    <h3 class="page-subtitle">
                        Bandi di gara fino al 31/12/2023
                    </h3>
                </div>

                <hr>
            @endif
            {{-- Tabella generata dal server --}}
            @if(!empty($tablePre2024))
                {{ $tablePre2024 }}

                {{-- Paginazione della tabella --}}
                @if(!empty($varationPre2024))
                    {{ paginateBootstrap($varationPre2024) }}
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


    {{-- Bottom Menu --}}
    {% include v1/layout/partials/bottom_menu %}
</main>

{{-- Link utilii --}}
{% include v1/layout/partials/card_link %}

{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
{% endblock %}
