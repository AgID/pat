<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Pagina per Sovvenzioni --}}

{% extends v1/layout/master %}

{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}

<main>
    <section>
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

    {{-- Se è presente la tabella la mostro, altrimenti mostro la vista a elenco --}}
    @if(empty($viewMode))

        {{-- Sezione per i risultati della ricerca in formato tabellare --}}
        {% include v1/layout/partials/search_results %}
    @else
        {{-- Visualizzazione ad elenco dei dati --}}
        {% include v1/layout/partials/list_item_view %}
    @endif

</main>

{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
{% endblock %}