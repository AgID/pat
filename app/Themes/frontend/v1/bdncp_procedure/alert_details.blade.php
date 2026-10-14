<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Pagina per Atti delle amministrazioni --}}

{% extends v1/layout/master %}

{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}

<main>
    <section class="my-5">
        <div class="container">
            <div class="row variable-gutters _reverse">
                <div class="col-lg-12">
                    <div class="div-title">
                        <h1 class="page-title">{{e: !empty($h1) ? $h1 : (!empty($instance['object']) ? $instance['object'] : '') }}</h1>
                        <div class="button-normative">
                        </div>
                    </div>


                    <div class="attributi">
                        @if(!empty($instance['alert_date']))
                            <div class="attributo">
                                <span class="titolo">Data avviso:</span>
                                <span class="mr-2 text-muted">
                                    {{date('d-m-Y|date'): $instance['alert_date'] }}
                                </span>
                            </div>
                        @endif

                        @if(!empty($instance['relative_bdncp_procedure']))
                            <div class="attributo">
                                <span class="titolo">Procedura relativa:</span>
                                <a class="mr-2 text-muted"
                                   href="{{ siteUrl('page/10/details/'. (int) $instance['relative_bdncp_procedure']['id'].'/'.urlTitle($instance['relative_bdncp_procedure']['object'])) }}">
                                    {{e: $instance['relative_bdncp_procedure']['object'] }}
                                </a>
                            </div>
                        @endif

                    </div>

                    @if(!empty($instance['ac_extra_info']))
                        <span class="mr-2 text-muted">{{xss: $instance['ac_extra_info'] }}</span>
                    @endif

                    @if(!empty($instance['notes']))
                        <h3 id="anchor1" class="testo-blu anchor page-subtitle mt-3">Dettagli</h3>
                        {{xss: $instance['notes'] }}
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
