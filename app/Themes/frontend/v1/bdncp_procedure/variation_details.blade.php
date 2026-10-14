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
                <div class="col-lg-8">
                    <div class="titolo mb-4">
                        <h1 class="page-title">{{e: !empty($h1) ? $h1 : (!empty($instance['object']) ? $instance['object'] : '') }}</h1>
                    </div>

                    <div class="attributi">

                        @if(!empty($instance['rel_variation_procedure']))

                            <h3  class="testo-blu anchor page-subtitle">Procedure relative</h3>
                            <ul>
                                @foreach($instance['rel_variation_procedure'] as $proc)
                                    <li>
                                        <a class="mr-2 text-muted"
                                           href="{{ siteUrl('page/10/details/'. (int) $proc['id'].'/'.urlTitle($proc['object'])) }}">
                                            {{e: !empty($proc['cig']) ? $proc['cig'] : 'N.D.' }} - {{e: $proc['object'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                    </div>

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

                {{-- Indice della pagina --}}
                {% include v1/layout/partials/bottom_menu %}

            </div>
        </div>
    </section>
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
