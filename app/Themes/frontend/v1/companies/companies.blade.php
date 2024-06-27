<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Pagina per Enti controllati --}}

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

                    @if(!empty($archivedList))
                        <div class="alert alert-info" role="alert">
                            <b>In questa pagina è presente un elenco dell'archivio "Enti controllati"</b>.
                        </div>
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

{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
{% endblock %}