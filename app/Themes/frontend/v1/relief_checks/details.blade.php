<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Pagina per Controlli e rilievi --}}

{% extends v1/layout/master %}

{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}

<main>
    <section class="my-5">
        <div class="container">
            <div class="row variable-gutters _reverse">
                <div class="col-lg-8">
                    <div class="titolo mb-4">
                        <h1 class="page-title">{{e: !empty($h1) ? $h1 : (!empty($instance['object']) ? $instance['object'] : '') }}</h1>
                    </div>

                    <div class="attributi">
                        @if(!empty($instance['date']))
                            <div class="attributo">
                                <span class="titolo">Data:</span>
                                <span class="mr-2 text-muted">
                                    {{date('d-m-Y|date'): $instance['date'] }}
                                </span>
                            </div>
                        @endif

                        @if(!empty($instance['office']))
                                <div class="attributo">
                                    <span class="titolo">Ufficio:</span>
                                    <a class="text-muted" href="{{ siteUrl('page/40/details/'.$instance['office']['id'].'/'.urlTitle($instance['office']['structure_name'])) }}">
                                        {{e: $instance['office']['structure_name'] }}
                                    </a>
                                </div>
                        @endif
                    </div>

                    @if(!empty($instance['description']))
                        <h3 id="anchor1" class="testo-blu anchor page-subtitle mt-3">Descrizione</h3>
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