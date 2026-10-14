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
                <div class="col-lg-12">
                    {{-- Nome della pagina --}}
                    <div class="div-title">
                        <h1 class="mb-4 page-title">{{e: $pageName }}</h1>
                        <div class="button-normative">
                        </div>
                    </div>
                    {{-- Contenuto della pagina --}}
                    @if(!empty($accessibilityText))
                        {{xss: $accessibilityText }}
                    @else

                        <p>
                            <strong>Il presente sito web è realizzato nel rispetto dei requisiti di accessibilità
                                previsti dalla Legge 9 gennaio 2004, n. 4 e successive modifiche e integrazioni, nonché
                                dalle Linee Guida sull'accessibilità degli strumenti informatici emanate da AgID.
                                <br>
                                Il sito è pienamente conforme ai requisiti previsti dalla norma UNI CEI EN 301549:2021 e
                                alle Web Content Accessibility Guidelines (WCAG) 2.1 del W3C, livello di conformità AA.</strong>
                        </p>

                    @endif

                </div>

            </div>
        </div>
    </section>
</main>
{% include v1/layout/partials/bottom_menu %}

{{-- Cards Link --}}
{% include v1/layout/partials/card_link %}

{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
{% endblock %}
