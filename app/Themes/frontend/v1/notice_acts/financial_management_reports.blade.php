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
                    <h1 class="page-title">{{e: !empty($h1) ? $h1 : $pageName }}</h1>

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
                        <h5 class="font-weight-bold">Nessun elemento presente</h5>
                    @endif
                </div>
            </section>

            <div class="mb-2">
                <p>Non hai trovato le informazioni soggette alla pubblicazione obbligatoria?</p>
                <a href="" class="btn btn-primary"><span class="far fa-comment"></span> Richiedile ora</a>
            </div>
        </div>

    </section>

    {{-- Bottom Menu --}}
    {% include v1/layout/partials/bottom_menu %}
</main>



{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
{% endblock %}
