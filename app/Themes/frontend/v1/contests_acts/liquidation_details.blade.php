<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Pagina per Bandi di gara --}}

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
                        <h1 class="page-title">{{e: !empty($h1) ? $h1 : (!empty($instance['object']) ? $instance['object'] : '') }}</h1>
                        @if(!empty($instance['type']))
                            <h5 class="text-secondary page-subtitle"><i
                                        class="fas fa-caret-right mr-1"></i> {{e: $instance['type'] }}
                            </h5>
                        @endif

                        @if(!empty($instance['relative_procedure']['cig']) || !empty($instance['relative_procedure']['relative_notice']['cig']))
                            @php
                                $tmpCig = !empty($instance['relative_procedure']['cig'])
                                                ? $instance['relative_procedure']['cig']
                                                : $instance['relative_procedure']['relative_notice']['cig'];
                            @endphp
                            <div class="attributi">
                                <div class="attributo">
                                    <span class="titolo">Codice CIG:</span>
                                    <span class="mr-2 text-muted">
                                    {{e: $tmpCig }}
                                </span>
                                </div>
                            </div>
                        @endif
                    </div>

                    {{-- Per le liquidazioni --}}
                    @if(!empty($instance['relative_procedure_awardees']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="anchor2" class="testo-blu anchor page-subtitle">Aggiudicatari</h3>
                        <ul>
                            @foreach($instance['relative_procedure_awardees'] as $awardee)
                                <li>
                                    {{e: $awardee['name'] }}
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    <div class="attributi">
                        @if(!empty($instance['amount_liquidated']))
                            @php
                                $anchorsNumber++;
                            @endphp
                            <h3 id="anchor4" class="testo-blu anchor page-subtitle">Importi</h3>
                            @if(!empty($instance['amount_liquidated']))
                                <div class="attributo">
                                    <span class="titolo">Valore importo liquidato:</span>
                                    <span class="mr-2 text-muted">
                                    &euro; {{currency('filter=escape_xss decimal_separator=, thousands_separator=.'): $instance['amount_liquidated'] }}
                                </span>
                                </div>
                            @endif
                        @endif

                        <h3 id="anchor5" class="testo-blu anchor page-subtitle mt-3">Altre informazioni sulla
                            procedura</h3>
                        @php
                            $anchorsNumber++;
                        @endphp
                        @if(!empty($instance['relative_procedure']['structure']) || !empty($instance['relative_procedure']['relative_notice']['structure']))
                            @php
                                $tmpStructure = !empty($instance['relative_procedure']['structure'])
                                                ? $instance['relative_procedure']['structure']
                                                : $instance['relative_procedure']['relative_notice']['structure'];
                            @endphp
                            <div class="attributo">
                                <span class="titolo">Ufficio:</span>
                                <a class="text-muted"
                                   href="{{ siteUrl('page/40/details/'. (int) $tmpStructure['id'].'/'.urlTitle($tmpStructure['structure_name'])) }}">
                                    {{e: $tmpStructure['structure_name'] }}
                                </a>
                            </div>
                        @endif

                        @if(!empty($instance['activation_date']))
                            <div class="attributo">
                                <span class="titolo">Data liquidazione:</span>
                                <span class="text-muted">
                                        {{date('d-m-Y|date'): $instance['activation_date'] }}
                                    </span>
                            </div>
                        @endif

                        @if(!empty($instance['relative_procedure']) || !empty($instance['relative_notice']))
                            <div class="attributo">
                                <span class="titolo">Procedura relativa:</span>
                                <a class="text-muted"
                                   href="{{ siteUrl('page/110/details/'.(!empty($instance['relative_procedure']) ? $instance['relative_procedure']['id'] : $instance['relative_notice']['id']).'/'.urlTitle(!empty($instance['relative_procedure']) ? $instance['relative_procedure']['object'] : $instance['relative_notice']['object'])) }}">
                                    {{e: !empty($instance['relative_procedure']) ? $instance['relative_procedure']['object'] : $instance['relative_notice']['object'] }}
                                </a>
                            </div>
                        @endif

                        <p class="mt-2"><a class="text-muted" href="{{ currentUrl().'?st=1' }}">Tabella delle informazioni d'indicizzazione</a>
                        </p>
                    </div>

                    @if($instance['details'])
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="anchor8" class="testo-blu anchor page-subtitle">Note</h3>
                        {{xss: $instance['details'] }}
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