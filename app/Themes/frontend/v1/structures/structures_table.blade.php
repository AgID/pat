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
            <div class="header-structures">
                {{-- Nome pagina --}}
                <h1>{{e: !empty($h1) ? $h1 : $pageName }}</h1>

                @if( empty($formFilter) and !empty($linkDownloadOpenData) && !empty($table))
                    <button type="button" class="btn btn-xs btn-primary open-data-download-btn" id="btn-open-model-data">
                        <span class="fas fa-save"></span> Scarica Open data
                    </button>
                @endif

                {{-- Contenuto della pagina --}}
                {% include v1/layout/partials/page_content %}
            </div>
        </div>
    </section>

    {{-- Sezione per i risultati della ricerca --}}
    {% include v1/layout/partials/search_results %}

</main>

{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
{% endblock %}