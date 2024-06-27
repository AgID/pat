<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
{{-- Pagina pivot per le pagine di snodo --}}
{% extends v1/layout/master %}
{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}
<main>
    <section class="my-2">
        <div class="container">
            <div class="row variable-gutters _reverse">
                <div class="col-lg-8">
                    {{-- Nome della pagina --}}
                    <h1 class="mb-4 page-title">{{e: !empty($h1) ? $h1 : $pageName }}</h1>

                    {{-- Contenuto della pagina --}}
                    {% include v1/layout/partials/page_content %}
                    {% include v1/layout/partials/page_no_content %}

                </div>

                {{-- Right Drawer --}}
                {% include v1/layout/partials/right_menu %}

            </div>
        </div>
    </section>
</main>

{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
{% endblock %}