<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Pagina per Modulistica --}}

{% extends v1/layout/master %}

{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}

<main>
    <section class="my-5">
        <div class="container">
            <div class="row variable-gutters _reverse">
                <div class="col-lg-12">
                    <div class="div-title">
                        <h1 class="page-title">{{e: !empty($h1) ? $h1 : $instance['title'] }}</h1>
                        <div class="button-normative">
                        </div>
                    </div>
                    @if(!empty($instance['proceedings']))
                        <h3 id="proceedings" class="testo-blu page-subtitle">Procedimenti associati:</h3>
                            @foreach($instance['proceedings'] as $proceeding)
                                <p class="pl-4">
                                    <span class="fas fa-caret-right"></span>
                                    <a class="text-muted" href="{{ siteUrl('page/98/details/'.$proceeding['id'].'/'.urlTitle($proceeding['name'])) }}">
                                        {{e: $proceeding['name'] }}
                                    </a>
                                </p>
                            @endforeach
                    @endif

                    @if(!empty($instance['description']))
                        {{xss: $instance['description'] }}
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
