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
                <div class="col-lg-12">
                    <div class="div-title">
                        <div>
                            <h1 class="page-title">{{e: !empty($h1) ? $h1 :  $instance['title'] }}</h1>
                            @if(!empty($instance['typology']))
                                <h2 class="h5 text-secondary page-subtitle"><i class="fas fa-caret-right mr-1"></i>
                                    {{e: $instance['typology'] }}
                                </h2>
                            @endif
                        </div>
                        <div class="button-normative">
                        </div>
                    </div>                    

                    @if(!empty($instance['news_date']))
                        <p>
                            <span style="font-weight: 600;">Data notizia:</span> {{date('d/m/Y|date'): $instance['news_date'] }}
                        </p>
                    @endif

                   @if(!empty($instance['expiration_date']))
                        <p>
                            <span style="font-weight: 600;">Data scadenza:</span> {{date('d/m/Y|date'): $instance['expiration_date'] }}
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

                    {{-- Inclusione di librerie esterne --}}

                </div>
            </div>
        </div>
    </section>
</main>

{% include v1/layout/partials/bottom_menu %}

{{-- Link utilii --}}
{% include v1/layout/partials/card_link %}

{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}

{% endblock %}
