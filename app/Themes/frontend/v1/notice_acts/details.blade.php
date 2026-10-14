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
                <div class="col-lg-12">
                    <div class="div-title">
                        <h1 class="page-title">{{e: !empty($h1) ? $h1 : (!empty($instance['object']) ? $instance['object'] : '') }}</h1>
                        <div class="button-normative">
                        </div>
                    </div>
                    <div class="attributi">
                        @if(!empty($instance['date']))
                            <div class="attributo">
                                <span class="titolo">Data:</span>
                                <span class="mr-2 text-muted">
                                    {{date('d/m/Y|date'): $instance['date'] }}
                                </span>
                            </div>
                        @endif

                        @if(!empty($instance['relative_contest_act']))
                            <div class="attributo">
                                <span class="titolo">Procedura relativa:</span>
                                <ul>
                                    @foreach($instance['relative_contest_act'] as $proceeding)
                                        <li>
                                            <a class="text-muted"
                                               href="{{ siteUrl('page/110/details/'. (int)$proceeding['id'].'/'.urlTitle($proceeding['object'])) }}">
                                                {{e: $proceeding['object'] }}
                                            </a>
                                        </li>
                                    @endforeach
                                </ul>
                            </div>
                        @endif

                        @if(!empty($instance['assignments']))
                            @php
                                $i = 0;
                            @endphp
                            <div class="attributo">
                                <span class="titolo">Commissione giudicatrice:</span>
                                @foreach($instance['assignments'] as $assignment)
                                    <a class="mr-1 text-muted"
                                       href="{{ siteUrl('page/3/details/'.$assignment['id'].'/'.urlTitle($assignment['name'])) }}">
                                        {{e: $assignment['name'] }}
                                    </a>
                                    {{ ($i == count($instance['assignments'])-1) ? '' : ',' }}
                                    @php
                                        $i++;
                                    @endphp
                                @endforeach
                            </div>
                        @endif
                    </div>

                    @if(!empty($instance['details']))
                        <h3 id="anchor1" class="testo-blu anchor page-subtitle mt-3">Dettagli</h3>
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
