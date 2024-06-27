<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Pegina per l'organigramma degli uffici --}}

{% extends v1/layout/master %}

{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}

<main>
    <section class="my-2">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <h1>{{e: !empty($h1) ? $h1 : $pageName }}</h1>
                </div>
            </div>
        </div>
    </section>

    <section class="my-4">
        <div class="container">
            <div class="row variable-gutters _reverse mb-4">
                <div class="col-lg-8">

                    @if(!empty($organigram))
                        <div class="organigramma">
                            {{ treeHtmlStructures($organigram) }}
                        </div>

                        @if(!empty($_institution_info['show_update_date']) && !empty($latsUpdatedElement))
                            <p class="data-creazione mt-5" style="font-size: 14px;">
                                <span class="icona far fa-clock"></span>
                                <strong>{{ !empty($latsUpdatedElement['created_at']) ? 'Contenuto creato il ' . date('d-m-Y', strtotime((string)$latsUpdatedElement['created_at'])) : null }}
                                    {{ !empty($latsUpdatedElement['updated_at']) ? ' - Aggiornato il ' . date('d-m-Y', strtotime((string)$latsUpdatedElement['updated_at'])) : date('d-m-Y', strtotime((string)$latsUpdatedElement['created_at'])) }}</strong>
                            </p>
                        @endif
                    @else
                        <h5 class="font-weight-bold">Nessun elemento presente</h5>
                    @endif
                </div>

                {{-- Bottom Menu --}}
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