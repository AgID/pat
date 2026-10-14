<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Pagina per i Patrimoni immobiliari --}}

{% extends v1/layout/master %}

{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}

<main>
    <section class="my-2">
        <div class="container">
            <div class="row">
                <div class="col-12">

                    {{-- Nome della pagina --}}
                    <h1>{{e: !empty($h1) ? $h1 : $pageName }}</h1>

                    {{-- Inclusione di librerie esterne --}}

                @if(empty($formFilter) && !empty($linkDownloadOpenData) && !empty($instances['data']))
                        <button id="btn-open-model-data" type="button" class="btn btn-primary btn-xs open-data-download-btn" data-bs-toggle="modal" data-bs-target="#modalOpenData">
                            <span class="fas fa-save"></span> Scarica Open data
                        </button>
                    @endif

                    {{-- Contenuto della pagina --}}
                    {% include v1/layout/partials/page_content %}

                </div>
            </div>
        </div>
    </section>


    {{-- Sezione per i risultati della ricerca --}}
    {% include v1/layout/partials/search_results %}

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
