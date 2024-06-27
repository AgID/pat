<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Pagina per le Strutture organizzative --}}

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
                        <h1 class="page-title">{{e: !empty($h1) ? $h1 : ( !empty($instance['structure_name']) ? $instance['structure_name'] : '') }}</h1>
                    </div>

                    <div class="attributi">
                        @if(!empty($instance['responsibles']))
                            @php
                                $i = 0;
                            @endphp
                            <div class="attributo">
                                <span class="titolo">Responsabili:</span>
                                @foreach($instance['responsibles'] as $responsible)
                                    <a class="text-muted"
                                       href="{{ siteUrl('page/4/details/'.$responsible['id'].'/'.urlTitle($responsible['full_name'])) }}">
                                        {{ ($responsible['archived'] ? '<b>[Cessato]</b>  ' : '') }} {{e: $responsible['full_name'] }}
                                    </a>
                                    {{ $i++ == count($instance['responsibles'])-1 ? nbs(1) : ', ' }}
                                @endforeach
                            </div>

                        @elseif(!empty($instance['referent_not_available_txt']))
                            <div class="attributo">
                                <span class="titolo">Responsabili:</span>
                                <span class="mr-2 text-muted">
                                {{e: $instance['referent_not_available_txt'] }}
                                </span>
                            </div>
                        @endif

                        @if(!empty($instance['structure_of_belonging']))
                            <div class="attributo">
                                <span class="titolo">Struttura organizzativa di appartenenza:</span>
                                <a class="mr-2 text-muted"
                                   href="{{ siteUrl('page/40/details/'.$instance['structure_of_belonging']['id'].'/'.urlTitle($instance['structure_of_belonging']['structure_name'])) }}">
                                    {{e: $instance['structure_of_belonging']['structure_name'] }}
                                </a>
                            </div>
                        @endif

                        @if(!empty($instance['description']))
                            <div class="mt-2 attributo">
                                <span class="titolo">Descrizione attività:</span>
                                {{xss: $instance['description'] }}
                            </div>
                        @endif

                        @if(!empty($instance['sub_structures']))
                            @php
                                $anchorsNumber++;
                            @endphp
                            <h3 id="anchor1" class="testo-blu anchor page-subtitle mt-2">Strutture organizzative in
                                quest'area</h3>
                            <ul>
                                @foreach($instance['sub_structures'] as $subStructure)
                                    <li>
                                        <a class="text-muted"
                                           href="{{ siteUrl('page/40/details/'.$subStructure['id'].'/'.urlTitle($subStructure['structure_name'])) }}">
                                            {{e: $subStructure['structure_name'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif

                        @if(!empty($instance['certified_email']) || !empty($instance['reference_email']) || !empty($instance['phone']) || !empty($instance['fax']) || !empty($instance['email_not_available_txt']))
                            @php
                                $anchorsNumber++;
                            @endphp
                            <h3 id="anchor2" class="testo-blu anchor page-subtitle mt-2">Contatti</h3>
                            @if(!empty($instance['certified_email']))
                                <div class="attributo">
                                    <span class="titolo">Email certificata:</span>
                                    <a href="mailto:{{e: $instance['certified_email'] }}">
                                        {{e: $instance['certified_email'] }}
                                    </a>
                                </div>
                            @endif

                            <div class="attributo">
                                <span class="titolo">Email:</span>
                                {{ !empty($instance['reference_email'])
                                        ? '<a class="text-muted" href="mailto:' .$instance['reference_email'] . '">' . escapeXss($instance['reference_email']) . '</a>'
                                        : '<span class="mr-2 text-muted">'.$instance['email_not_available_txt'].'</span>' }}
                            </div>

                            @if(!empty($instance['phone']))
                                <div class="attributo">
                                    <span class="titolo">Telefono:</span>
                                    <a class="text-muted mr-2" href="{{escape_xss: $instance['phone'] }}">
                                        {{e: $instance['phone'] }}
                                    </a>
                                </div>
                            @endif

                            @if(!empty($instance['address']))
                                <div class="attributo">
                                    <span class="titolo">Indirizzo:</span>
                                    <span class="text-muted mr-2">
                                        {{e: $instance['address'] }}
                                    </span>
                                </div>
                            @endif

                            @if(!empty($instance['fax']))
                                <div class="attributo">
                                    <span class="titolo">Fax:</span>
                                    <a class="text-muted mr-2" href="{{escape_xss: $instance['fax'] }}">
                                        {{e:$instance['fax'] }}
                                    </a>
                                </div>
                            @endif
                        @endif


                        @if(!empty($instance['to_contact']))
                            @php
                                $anchorsNumber++;
                            @endphp
                            <h3 id="anchor3" class="testo-blu anchor page-subtitle mt-2">Personale da contattare</h3>
                            <ul>
                                @foreach($instance['to_contact'] as $contact)
                                    <li>
                                        <a class="text-muted"
                                           href="{{ siteUrl('page/4/details/'.$contact['id'].'/'.urlTitle($contact['full_name'])) }}">
                                            {{ ($contact['archived'] ? '<b>[Cessato]</b> ' : '') }}  {{e: $contact['full_name'] }}
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </div>

                    @if(!empty($instance['referents']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="anchor4" class="testo-blu anchor page-subtitle mt-2">In questa struttura</h3>
                        <ul>
                            @foreach($instance['referents'] as $referent)
                                <li>
                                    <a class="text-muted"
                                       href="{{ siteUrl('page/4/details/'.$referent['id'].'/'.urlTitle($referent['full_name'])) }}">
                                        {{e: $referent['full_name'] }}
                                    </a>
                                </li>
                            @endforeach
                        </ul>
                    @endif

                    @if(!empty($instance['timetables']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="anchor5" class="testo-blu anchor page-subtitle mt-2">Orari al pubblico</h3>
                        <div>
                            {{ nl2br(escapeXss($instance['timetables'])) }}
                        </div>
                    @endif

                    @if(!empty($instance['based_structure']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="anchor6" class="testo-blu anchor page-subtitle mt-2">Come raggiungerci</h3>
                        <div id="container-address-map" class="mb-4">
                            @if(!empty($instance['lat']) && !empty($instance['lon']))
                                <div id="map" style="min-height: 250px; z-index:0;">
                                </div>
                                @php
                                    $urlMaps = "https://www.google.com/maps?z=8&q=" .$instance['lat'].",".$instance['lon'];
                                @endphp
                            @else
                                @php
                                    $urlMaps = "https://www.google.com/maps?z=8&q=" .urlencode($instance['address']??'');
                                @endphp
                            @endif
                            <a href="{{ $urlMaps }}"
                               title="Apri indirizzo su Google Maps" target="_blank">
                                Apri indirizzo su Google Maps
                            </a>
                        </div>
                    @elseif(!empty($instance['address_detail']))
                        <h3 id="anchor6" class="testo-blu anchor page-subtitle mt-2">Indirizzo</h3>
                        <p>
                            <span class="fas fa-map-marker testo-blu mr-2"></span>{{ escapeXss($instance['address_detail']) }}
                        </p>
                    @endif

                    @if(!empty($instance['regulations']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="anchor7" class="testo-blu anchor page-subtitle mt-2">Regolamenti e documenti di questa
                            struttura</h3>
                        <div class="griglia griglia-2 mb-5">
                            @foreach( $instance['regulations'] as $regulation)
                                <div class="card-richiamo">
                                    <span class="fas fa-file-contract text-black"></span>
                                    <a class="text-muted"
                                       href="{{ siteUrl('page/'.$regulation['public_in_id'].'/details/'.$regulation['id'].'/'.urlTitle($regulation['title'])) }}">
                                        {{ escapeXss($regulation['title']) }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if(!empty($instance['valid_normatives']) && !empty($_institution_info['show_regulation_in_structure']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="anchor8" class="testo-blu anchor page-subtitle mt-2">Normative</h3>
                        <div class="griglia griglia-2 mb-5">
                            @foreach( $instance['valid_normatives'] as $normative)
                                <div class="card-richiamo">
                                    <span class="fas fa-file-contract text-black"></span>
                                    <a class="text-muted"
                                       href="{{ siteUrl('page/24/details/'.$normative['id'].'/'.urlTitle($normative['name'])) }}">
                                        {{ escapeXss($normative['name']) }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if(!empty($instance['normatives']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="anchor9" class="testo-blu anchor page-subtitle mt-2">Riferimenti normativi </h3>
                        <div class="griglia griglia-2 mb-5">
                            @foreach( $instance['normatives'] as $normative)
                                <div class="card-richiamo">
                                    <span class="fas fa-file-contract text-black"></span>
                                    <a class="text-muted"
                                       href="{{ siteUrl('page/24/details/'.$normative['id'].'/'.urlTitle($normative['name'])) }}">
                                        {{ escapeXss($normative['name']) }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
                    @endif

                    @if(!empty($instance['proceedings']))
                        @php
                            $anchorsNumber++;
                        @endphp
                        <h3 id="anchor10" class="testo-blu anchor page-subtitle mt-2">Procedimenti gestiti da questa struttura</h3>
                        <div class="griglia griglia-2 mb-5">
                            @foreach( $instance['proceedings'] as $proceeding)
                                <div class="card-richiamo">
                                    <span class="fas fa-file-contract text-black"></span>
                                    <a class="text-muted" href="{{ siteUrl('page/98/details/'.$proceeding['id'].'/'.urlTitle($proceeding['name'])) }}">
                                        {{e: $proceeding['name'] }}
                                    </a>
                                </div>
                            @endforeach
                        </div>
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
{{ css('leaflet/leaflet.css', 'common') }}
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
@if(!empty($instance['based_structure']))
    {{ js('leaflet/leaflet.js', 'common') }}
    <script>
        let map = L.map('map');
        setTimeout(function () {
            map.invalidateSize();
        }, 150)

        let baseLayers = {
            'mappa': L.tileLayer('https://{s}.tile.openstreetmap.org/{z}/{x}/{y}.png', {
                attribution: '&copy; <a href="https://www.openstreetmap.org/copyright">OpenStreetMap</a> contributors',
            })
        }

        map.setView([{{ $instance['lat'] }}, {{ $instance['lon'] }}], 13);
        baseLayers['mappa'].addTo(map);
        marker = L.marker([{{ $instance['lat'] }}, {{ $instance['lon'] }}]).addTo(map);
        marker.bindPopup("<b>{{ $instance['structure_name'] }}</b></br>{{ $instance['address'] }}").openPopup();

    </script>
@endif
{% endblock %}