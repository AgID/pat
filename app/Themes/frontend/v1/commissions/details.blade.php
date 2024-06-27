<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Pagina per le Commissioni --}}

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
                        <h1 class="page-title">{{e: !empty($h1) ? $h1 : $instance['name'] }}</h1>
                    </div>

                    @if(!empty($instance['image']))
                        <img class="img-evidenza-personale"
                             src="{{baseUrl('media/' . $_institution_info['short_institution_name'] . '/assets/images/' . $instance['image'])}}"
                             alt="Foto di {{e: $instance['name'] }}"/>
                    @endif

                    <div class="attributi mb-4">
                        @if(!empty($instance['address']))
                            <div class="attributo">
                                <span class="fas fa-map-marker testo-blu mr-2"></span>
                                <span class="titolo">Indirizzo:</span>
                                <span class="mr-2 text-muted">{{e: $instance['address'] }}</span>
                            </div>
                        @endif

                        @if(!empty($instance['email']))
                            <div class="attributo">
                                <span class="fas fa-envelope testo-blu mr-2"></span>
                                <span class="titolo">Email:</span>
                                <a class="mr-2 text-muted"
                                   href="mailto:{{e: $instance['email'] }}">{{e: $instance['email'] }}</a>
                            </div>
                        @endif

                        @if(!empty($instance['phone']))
                            <div class="attributo">
                                <span class="fas fa-phone-alt testo-blu mr-2"></span>
                                <span class="titolo">Telefono:</span>
                                <span class="mr-2 text-muted">{{e: $instance['phone'] }}</span>
                            </div>
                        @endif

                        @if(!empty($instance['fax']))
                            <div class="attributo">
                                <span class="fas fa-fax testo-blu mr-2"></span>
                                <span class="titolo">Fax:</span>
                                <span class="mr-2 text-muted">{{e: $instance['fax'] }}</span>
                            </div>
                        @endif

                        @if(!empty($instance['description']))
                            @php
                                $anchorsNumber++;
                            @endphp
                            <h3 id="description" class="anchor testo-blu anchor page-subtitle mt-2">Descrizione
                                commissione</h3>
                            <div>
                                {{xss: $instance['description'] }}
                            </div>
                        @endif

                        @if(!empty($instance['president']))
                            @php
                                $anchorsNumber++;
                            @endphp
                            <h3 id="members" class="anchor testo-blu anchor page-subtitle mt-2">Membri</h3>
                            @if(!empty($instance['president']))
                                <div class="attributo">
                                    <span class="fas fa-user testo-blu mr-1"></span>
                                    <span class="titolo">Presidente:</span>
                                    <a class="mr-2 text-muted"
                                       href="{{ siteUrl('page/58/details/'.$instance['president']['id'].'/'.urlTitle($instance['president']['full_name'])) }}">
                                        {{xss: $instance['president']['archived'] ? '<b>[Cessato]</b>' : '' . $instance['president']['full_name'] }}</a>
                                </div>
                            @endif
                        @endif

                        @if(!empty($instance['vicepresidents']))
                            <div class="attributo">
                                @php
                                    $i = 0;
                                    $len = count($instance['vicepresidents'])-1;
                                @endphp
                                <span class="fas fa-users testo-blu"></span>
                                <span class="titolo">Vicepresidente:</span>
                                @foreach($instance['vicepresidents'] as $vicepresident)
                                    <a class="text-muted"
                                       href="{{ siteUrl('page/58/details/'.$vicepresident['id'].'/'.urlTitle($vicepresident['full_name'])) }}">
                                        {{xss: ($vicepresident['archived'] ? '<b>[Cessato]</b>' : '') . $vicepresident['full_name'] }}
                                    </a>
                                    {{ ($i++ < $len ? ', ' : '') }}
                                @endforeach
                            </div>
                        @endif

                        @if(!empty($instance['secretaries']))
                            <div class="attributo">
                                @php
                                    $i = 0;
                                $len = count($instance['secretaries'])-1;
                                @endphp
                                <span class="fas fa-users testo-blu"></span>
                                <span class="titolo">Segretari:</span>
                                @foreach($instance['secretaries'] as $secretarie)
                                    <a class="text-muted"
                                       href="{{ siteUrl('page/58/details/'.$secretarie['id'].'/'.urlTitle($secretarie['full_name'])) }}">
                                        {{xss: ($secretarie['archived'] ? '<b>[Cessato]</b>' : '') . $secretarie['full_name'] }}
                                    </a>
                                    {{ ($i++ < $len ? ', ' : '') }}
                                @endforeach
                            </div>
                        @endif

                        @if(!empty($instance['members']))
                            <div class="attributo">
                                @php
                                    $i = 0;
                                $len = count($instance['members'])-1;
                                @endphp
                                <span class="fas fa-users testo-blu"></span>
                                <span class="titolo">Membri:</span>
                                @foreach($instance['members'] as $member)
                                    <a class="text-muted"
                                       href="{{ siteUrl('page/58/details/'.$member['id'].'/'.urlTitle($member['full_name'])) }}">
                                        {{xss: ($member['archived'] ? '<b>[Cessato]</b>' : '') . $member['full_name'] }}
                                    </a>
                                    {{ ($i++ < $len ? ', ' : '') }}
                                @endforeach
                            </div>
                        @endif

                        @if(!empty($instance['substitutes']))
                            <div class="attributo">
                                @php
                                    $i = 0;
                                $len = count($instance['substitutes'])-1;
                                @endphp
                                <span class="fas fa-users testo-blu"></span>
                                <span class="titolo">Membri supplenti:</span>
                                @foreach($instance['substitutes'] as $substitute)
                                    <a class="text-muted"
                                       href="{{ siteUrl('page/58/details/'.$substitute['id'].'/'.urlTitle($substitute['full_name'])) }}">
                                        {{xss: ($substitute['archived'] ? '<b>[Cessato]</b>' : '') . $substitute['full_name'] }}
                                    </a>
                                    {{ ($i++ < $len ? ', ' : '') }}
                                @endforeach
                            </div>
                        @endif

                        @if(!empty($instance['activation_date']) || !empty($instance['expiration_date']))
                            <div class="attributo">
                                @if(!empty($instance['activation_date']))
                                    <div>
                                        <span class="icona far fa-calendar-alt testo-blu mr-1"></span>
                                        <span class="titolo">Data inizio:</span>
                                        <span class="mr-2 text-muted">{{date('d-m-Y|date'): $instance['activation_date'] }}</span>
                                    </div>

                                @endif
                                @if(!empty($instance['expiration_date']))
                                    <div>
                                        <span class="icona far fa-calendar-alt testo-blu mr-1"></span>
                                        <span class="titolo">Data fine:</span>
                                        <span class="mr-2 text-muted">{{date('d-m-Y|date'): $instance['expiration_date'] }}</span>
                                    </div>
                                @endif
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