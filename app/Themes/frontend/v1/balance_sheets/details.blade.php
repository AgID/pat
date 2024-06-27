<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Pagina per dettaglio Bilanci --}}

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
                    <div class="titolo mb-4">
                        {{-- Nome pagina --}}
                        <h1 class="page-title">{{e: !empty($h1) ? $h1 : $pageName }}</h1>
                    </div>

                    <div class="attributi">
                        @if(!empty($instance['typology']) or !empty($instance['year']) )
                            @php
                                $anchorsNumber++;
                            @endphp
                            <h3 id="generic-info" class="anchor testo-blu anchor sr-only" hidden>Informazioni generali</h3>
                            <div class="mb-4">
                                @if(!empty($instance['typology']))
                                    <div class="attributo">
                                        <span class="titolo">Tipologia:</span>
                                        <span class="mr-2 text-muted"><strong>{{e: ucfirst($instance['typology']) }}</strong><br></span>
                                    </div>
                                @endif
                                @if(!empty($instance['year']))
                                    <div class="attributo">
                                        <span class="titolo">Anno:</span>
                                        <span class="mr-2 text-muted"><strong>{{ $instance['year'] }}</strong><br></span>
                                    </div>
                                @endif
                                @if(!empty($instance['related_measure']))
                                    <div class="attributo">
                                        <span class="titolo">Provvedimento:</span>
                                        <a class="mr-2 text-muted"
                                           href="{{ siteUrl('page/9/details/'.$instance['object_measure_id'].'/'.urlTitle($instance['related_measure']['object'])) }}">
                                            {{e: $instance['related_measure']['object'] }}
                                        </a>
                                    </div>
                                @endif
                            </div>
                        @endif

                        @if(!empty($instance['description']))
                            @php
                                $anchorsNumber++;
                            @endphp
                            <h3 id="information" class="testo-blu anchor page-subtitle" hidden>Altre informazioni</h3>
                            <div>
                                {{xss: $instance['description'] }}
                                <br>
                            </div>
                        @endif
                    </div>

                    {{-- Attach List --}}
                    {% include v1/layout/partials/attach_list %}

                    {{--  Created/Update Info --}}
                    {% include v1/layout/partials/created_updated_info %}

                </div>

                @if($anchorsNumber > 4)
                    {{-- Index anchor --}}
                    {% include v1/layout/partials/anchor_index %}
                @else
                    {{-- Right Menu --}}
                    {% include v1/layout/partials/right_menu %}
                @endif
            </div>
        </div>
    </section>

</main>

@if($anchorsNumber > 4)
    {{-- Bottom Menu --}}
    {% include v1/layout/partials/bottom_menu %}
@endif

{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
{% endblock %}