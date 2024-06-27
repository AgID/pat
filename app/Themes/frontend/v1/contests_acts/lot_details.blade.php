<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Pagina per i Lotti --}}

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
                    <div class="titolo mb-2">
                        <h1 class="page-title">{{!empty($h1) ? $h1 : ( !empty($instance['object']) ? $instance['object'] : '') }}</h1>
                        @if(!empty($instance['type']))
                            <h5 class="text-secondary page-subtitle"><i class="fas fa-caret-right mr-1"></i>
                                {{e: $instance['type'] }}
                            </h5>
                        @endif
                    </div>

                    <div class="attributi">
                        @if(!empty($instance['cig']))
                            <div class="attributo">
                                <span class="titolo">Codice CIG:</span>
                                <span class="mr-2 text-muted">
                                    {{e: $instance['cig'] }}
                                </span>
                            </div>
                        @endif

                        @if(!empty($instance['bdncp_link']))
                            <div class="attributo">
                                <span class="titolo"><!--<i class="fas fa-link"></i>--> Link BDNCP:</span>
                                <span class="mr-2">
                                        <a class="mt-2 text-muted" href="{{$instance['bdncp_link']}}">
                                            {{$instance['bdncp_link']}}
                                        </a>
                                    </span>
                            </div>
                        @endif

                        @if(!empty($instance['relative_notice']['adjudicator_name']))
                            <div class="attributo">
                                <span class="titolo">Struttura proponente:</span>
                                <span class="mr-2 text-muted">
                                    {{e: $instance['relative_notice']['adjudicator_name'] }}
                                    @if(!empty($instance['relative_notice']['adjudicator_data']))
                                        - {{e: $instance['relative_notice']['adjudicator_data'] }}
                                    @endif
                                </span>
                            </div>
                        @endif

                        @if(!empty($instance['relative_notice']['contraent_choice']))
                            @php
                                $tmpContraent = $instance['relative_notice']['contraent_choice']['name'];
                            @endphp
                            <div class="attributo">
                                <span class="titolo">Procedura di scelta del contraente:</span>
                                <span class="mr-2 text-muted">
                                    {{e: $tmpContraent }}
                                </span>
                            </div>
                        @endif
                    </div>

                    @if(!empty($instance['asta_base_value']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="anchor4" class="testo-blu anchor page-subtitle">Importi</h3>
                        @if(!empty($instance['asta_base_value']))
                            <div class="attributi">
                                <div class="attributo">
                                    <span class="titolo">Importo dell'appalto:</span>
                                    <span class="mr-2 text-muted">
                                     &euro; {{e: $instance['asta_base_value'] }}
                                </span>
                                </div>
                            </div>
                        @endif
                    @endif

                    <h3 id="anchor5" class="testo-blu anchor page-subtitle mt-3">Altre informazioni sulla procedura</h3>
                    <div class="attributi">
                        @php
                            $anchorsNumber++;
                        @endphp

                        @if(!empty($instance['activation_date']))

                            <div class="attributo">
                                <span class="titolo">Data di pubblicazione:</span>
                                <span class="mr-2 text-muted">
                                   {{date('d-m-Y|date'): $instance['activation_date'] }}
                                </span>
                            </div>
                        @endif
                    </div>

                    @if(!empty($instance['relative_notice']))
                        <p class="mt-2">Il presente lotto fa parte della procedura:
                            <a class="text-muted"
                               href="{{ siteUrl('page/110/details/'. (int) $instance['relative_notice']['id'].'/'.urlTitle($instance['relative_notice']['object'])) }}">
                                {{e: $instance['relative_notice']['object'] }}
                            </a>
                        </p>
                    @endif

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