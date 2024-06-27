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
                    <h1 class="mb-4 page-title">{{e: $pageName }}</h1>

                    {{-- Contenuto della pagina --}}
                    @if(!empty($accessibilityText))
                        {{e: $accessibilityText }}
                    @else

                        <p>
                            <strong>Il presente sito web è realizzato seguendo le direttive sancite dal W3C ed è
                                pienamente conforme alla Legge n. 4/04 aggiornata dal decreto legislativo 10 agosto
                                2018, n. 106 e successive disposizioni attuative, ivi inclusi i requisiti di cui al
                                punto 9 della norma UNI EN 301549:2018 che equivalgono alla conformità con il livello AA
                                delle WCAG 2.1.</strong>
                        </p>

                    @endif

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