<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Pagina per Atti di programmazione --}}

{% extends v1/layout/master %}

{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}

<main>
    <section class="my-5">
        <div class="container">
            <div class="row variable-gutters _reverse">
                <div class="col-lg-8">
                    <div class="titolo mb-4">
                        <h1>{{e: !empty($h1) ? $h1 : (!empty($instance['object']) ? $instance['object'] : '') }}</h1>
                    </div>

                    @if(!empty($instance['date']))
                        <p>Data: {{date('d-m-Y|date'): $instance['date'] }}</p>
                    @endif

                    @if(!empty($instance['description']))
                        <h3 id="description" class="anchor testo-blu anchor">Descrizione</h3>
                        {{xss: $instance['description'] }}
                    @endif

                    {{-- Attach List --}}
                    {% include v1/layout/partials/attach_list %}

                    {{--  Created/Update Info --}}
                    {% include v1/layout/partials/created_updated_info %}

                </div>

                {{-- Right Menu --}}
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