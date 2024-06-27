<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Pagina per Regolamenti e documentazione --}}

{% extends v1/layout/master %}

{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}

@php
    $anchorsNumber = 1;
@endphp

<main>
    <section class="my-5">
        <div class="container">
            <div class="row variable-gutters _reverse">
                <div class="col-lg-8">
                    <p id="generic-info" class="testo-blu anchor sr-only"
                       style="visibility: hidden; margin: unset;padding: unset;">Informazioni generali</p>
                    <div class="titolo mb-4">
                        <h1>{{e: !empty($h1) ? $h1 : $instance['title'] }}</h1>
                    </div>

                    @if(!empty($instance['proceedings']))
                        <p>Procedimenti associati:
                            @php
                                $i = 0;
                                $len = count($instance['proceedings'])-1;
                            @endphp
                            @foreach($instance['proceedings'] as $proceeding)
                                <a href="{{ siteUrl('page/98/details/'.$proceeding['id'].'/'.urlTitle($proceeding['name'])) }}">
                                    {{e: $proceeding['name'] }}
                                </a>
                                {{ $i++ < $len ? ', ' : '' }}
                            @endforeach
                        </p>
                    @endif

                    @if(!empty($instance['structures']))
                        <p>Strutture organizzative associate:
                            @php
                                $i = 0;
                                $len = count($instance['structures'])-1;
                            @endphp
                            @foreach($instance['structures'] as $structure)
                                <a href="{{ siteUrl('page/40/details/'.$structure['id'].'/'.urlTitle($structure['structure_name'])) }}">
                                    {{e: $structure['structure_name'] }}
                                </a>
                                {{ $i++ < $len ? ', ' : '' }}
                            @endforeach
                        </p>
                    @endif

                    @if(!empty($instance['issue_date']))
                        <p>Data emissione: <i class="far fa-calendar-alt"></i> {{date('d-m-Y|date'): $instance['issue_date'] }}</p>
                    @endif

                    @if(!empty($instance['number']))
                        <p>Numero: {{e: $instance['number'] }}</p>
                    @endif

                    @if(!empty($instance['protocol']))
                        <p>Protocollo: {{e: $instance['protocol'] }}</p>
                    @endif

                    @if(!empty($instance['charges']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="charges" class="testo-blu anchor">Oneri informativi relativi</h3>
                        @foreach($instance['charges'] as $charge)
                            <p class="pl-4">
                                <span class="fas fa-caret-right testo-blu"></span>
                                <a class="text-decoration-none"
                                   href="{{ siteUrl('page/33/details/'.$charge['id'].'/'.urlTitle($charge['title'])) }}">
                                    {{e: $charge['title'] }}
                                </a>
                            </p>
                        @endforeach
                    @endif

                    @if(!empty($instance['description']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="description" class="testo-blu anchor">Descrizione</h3>
                        {{xss: $instance['description'] }}
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