<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Pagina per gli Avvisi --}}

{% extends v1/layout/master %}

{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}

@php
    $anchorsNumber = 0;
@endphp

<main>
    <section class="my-5">
        <div class="container">
            <div class="row variable-gutters _reverse">
                <div class="col-lg-8">
                    <p id="generic-info" class="testo-blu anchor sr-only"
                       style="visibility: hidden; margin: unset;padding: unset;">Informazioni generali</p>
                    <div class="titolo mb-4">
                        <h1 class="page-title">{{ !empty($h1) ? $h1 : ( !empty($instance['object']) ? $instance['object'] : '') }}</h1>
                    </div>

                    <div class="attributi">

                        @if(!empty($instance['proceedings']))
                            <div class="mt-2">
                                <h3  class="testo-blu page-subtitle">Procedure relative:</h3>
                                <ul>
                                    @foreach($instance['proceedings'] as $proceeding)
                                        <li>
                                            <a class="mr-2 text-muted"
                                               href="{{ siteUrl('page/110/details/'. (int)$proceeding['id'].'/'.urlTitle($proceeding['object'])) }}">
                                                {{e: $proceeding['object'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif


                        </div>
                        @if($instance['details'])
                            @php
                                $anchorsNumber++;
                            @endphp
                            <h3  class="testo-blu page-subtitle">Dettagli</h3>
                            {{xss: $instance['details'] }}
                        @endif


                    {{-- Attach List --}}
                    {% include v1/layout/partials/attach_list %}

                    {{--  Created/Update Info --}}
                    {% include v1/layout/partials/created_updated_info %}

                    {{-- Inclusione di librerie esterne --}}

                    </div>


                </div>
            </div>
        </div>
    </section>
</main>


{{-- Bottom Menu --}}
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
