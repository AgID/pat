<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Pagina per Patrimoni Immobiliari --}}

{% extends v1/layout/master %}

{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}

<main>
    <section class="my-5">
        <div class="container">
            <div class="row variable-gutters _reverse">
                <div class="col-lg-12">
                    <div class="div-title">
                        <h1 class="page-title">{{e: !empty($h1) ? $h1 : $instance['name'] }}</h1>
                        <div class="button-normative">
                        </div>
                    </div>
                    @if(!empty($instance['address']))
                        <div class="attributi">
                            <div class="attributo">
                                <span class="fas fa-map-marker testo-blu mr-1"></span>
                                <span class="titolo">Indirizzo:</span>
                                <span class="text-muted">{{e: $instance['address'] }}</span>
                            </div>
                        </div>
                    @endif

                    @if(!empty($instance['offices']))
                        <div class="attributi">
                            <div class="attributo">
                                <span class="titolo">Ufficio utilizzatore:</span>
                                <ul>
                                    @foreach($instance['offices'] as $office)
                                        <li>
                                            <a class="text-muted"
                                               href="{{ siteUrl('page/40/details/'.$office['id'].'/'.urlTitle($office['structure_name'])) }}">
                                                {{ ($office['archived'] ? '<b>[Elemento archiviato]</b>' : '')  }} {{e: $office['structure_name'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        </div>
                    @endif

                    <div class="attributi">
                        @if(!empty($instance['sheet']))
                            <div class="attributo">
                                <span class="titolo">Foglio:</span>
                                <span class="text-muted">{{e: $instance['sheet'] }}</span>
                            </div>
                        @endif

                        @if(!empty($instance['particle']))
                            <div class="attributo">
                                <span class="titolo">Particella:</span>
                                <span class="text-muted">{{e: $instance['particle'] }}</span>
                            </div>
                        @endif

                        @if(!empty($instance['subaltern']))
                            <div class="attributo">
                                <span class="titolo">Subalterno:</span>
                                <span class="text-muted">{{e: $instance['subaltern'] }}</span>
                            </div>
                        @endif

                        @if(!empty($instance['gross_surface']))
                            <div class="attributo">
                                <span class="titolo">Superficie lorda <small>(mq)</small>:</span>
                                <span class="text-muted">{{e: $instance['gross_surface'] }}</span>
                            </div>
                        @endif

                        @if(!empty($instance['discovered_surface']))
                            <div class="attributo">
                                <span class="titolo">Superficie scoperta <small>(mq)</small>:</span>
                                <span class="text-muted">{{e: $instance['discovered_surface'] }}</span>
                            </div>
                        @endif
                    </div>

                    @if(!empty($instance['description']))
                        <h3 class="testo-blu page-subtitle mt-3">Note</h3>
                        {{xss: $instance['description'] }}
                    @endif


                    @if(!empty($instance['canons']))
                        <h3 class="testo-blu page-subtitle mt-3">Canoni di locazione</h3>
                        <div class="table-responsive">
                            <table class="table table-bordered table-sm">
                                <thead class="">
                                <tr class="intestazione-tabella">
                                    <th scope="col">Tipologia</th>
                                    <th scope="col">Importo</th>
                                    <th scope="col">Data Inizio</th>
                                    <th scope="col">Data Fine</th>
                                </tr>
                                </thead>
                                <tbody>
                                @foreach($instance['canons'] as $canon)
                                    <tr>
                                        @php
                                        $type = ($canon['canon_type'] == 1) ? 'Canoni di locazione o di affitto versati': 'Canoni di locazione o di affitto percepiti';
                                        $page = ($canon['canon_type'] == 1) ? 137 : 136;
                                        $amount = !empty($canon['amount']) ? $canon['amount'] : '';
                                        @endphp
                                        <td>
                                            <a href="{{ siteUrl('page/'.$page.'/details/'. (int) $canon['id'].'/canoni-di-locazione') }}">
                                                {{ $type }}
                                            </a>
                                        </td>
                                        <td>&euro; {{currency('filter=xss decimal_separator=, thousands_separator=.'): $amount }}</td>
                                        <td>{{ (empty($canon['start_date']) ? '' : date("d-m-Y", strtotime($canon['start_date']))) }}</td>
                                        <td>{{ (empty($canon['end_date']) ? '' : date("d-m-Y", strtotime($canon['end_date']))) }}</td>
                                    </tr>
                                @endforeach
                                </tbody>
                            </table>
                        </div>
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
