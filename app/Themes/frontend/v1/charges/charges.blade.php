<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Pagina per gli Oneri --}}

{% extends v1/layout/master %}

{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}

<main>
    <section class="my-2">
        <div class="container">
            <div class="row">
                <div class="col-12">

                    {{-- Nome della pagina --}}
                    <div class="div-title">
                        <h1>{{e: !empty($h1) ? $h1 :  $pageName }}</h1>
                        <div class="button-normative">
                        </div>
                    </div>
                    {{-- Inclusione di librerie esterne --}}

                    {{-- Contenuto della pagina --}}
                    {% include v1/layout/partials/page_content %}

                    @if($noRequiredPublication)
                    {{-- Alert contenuti non più obbligatori --}}
                    {% include v1/layout/partials/no_required_publication_message %}
                    @endif

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
