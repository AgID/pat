<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Pagina per News e avvisi --}}

{% extends v1/layout/master %}

{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}

<main>
    <section class="my-5">
        <div class="container">
            <div class="row variable-gutters _reverse">
                <div class="col-lg-8">
                    <div class="titolo mb-4">
                        <h1 class="page-title">{{e: !empty($h1) ? $h1 :  $instance['title'] }}</h1>
                        @if(!empty($instance['typology']))
                            <h5 class="text-secondary page-subtitle"><i class="fas fa-caret-right mr-1"></i>
                                {{e: $instance['typology'] }}
                            </h5>
                        @endif
                    </div>

                    @if(!empty($instance['news_date']))
                        <p>
                            <span style="font-weight: 600;">Data notizia:</span> {{date('d-m-Y|date'): $instance['news_date'] }}
                        </p>
                    @endif

                    @if(!empty($instance['content']))
                        <div>
                            {{xss: $instance['content'] }}
                        </div>
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