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


    <section id="risultati-ricerca">
        <div class="container">
            @if($title)
                <h3 class="page-subtitle">
                    Affidamenti
                </h3>
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


        <div class="container">
            @if($titleNotices)
                <h3 class="page-subtitle">
                    Atti
                </h3>
                <hr>
            @endif
            <section id="risultati-ricerca" class="mb-5">
                <div class="container">
                    {{-- Tabella generata dal server --}}
                    @if(!empty($actsTable))
                        {{ $actsTable }}

                        {{-- Paginazione della tabella --}}
                        @if(!empty($notices))
                            {{ paginateBootstrap($notices) }}
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
