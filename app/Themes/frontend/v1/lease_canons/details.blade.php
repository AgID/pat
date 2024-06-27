<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Pagina per Canoni di locazione --}}

{% extends v1/layout/master %}

{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}

<main>
    <section class="my-5">
        <div class="container">
            <div class="row variable-gutters _reverse">
                <div class="col-lg-8">
                    <div class="titolo mb-4">
                        <h1 class="page-title">{{ !empty($h1) ? $h1 : ($instance['canon_type'] == 1 ? 'Canoni di locazione o di affitto versati' : 'Canoni di locazione o di affitto percepiti' )}}</h1>
                    </div>

                    <div class="attributi">
                        @if(!empty($instance['beneficiary']))
                            <div class="attributo">
                                <span class="titolo">Informazioni sul beneficiario:</span>
                                <span class="text-muted">
                                            {{e: $instance['beneficiary'] }}
                                        </span>
                            </div>
                        @endif

                        @if(!empty($instance['fiscal_code']))
                            <div class="attributo">
                                <span class="titolo">Partita IVA/codice fiscale beneficiario:</span>
                                <span class="text-muted">
                                            {{e: $instance['fiscal_code'] }}
                                        </span>
                            </div>
                        @endif

                        @if(!empty($instance['amount']))
                            <div class="attributo">
                                <span class="titolo">Importo:</span>
                                <span class="text-muted">
                                             &euro; {{currency('filter=escape decimal_separator=, thousands_separator=.'): $instance['amount'] }}
                                        </span>
                            </div>
                        @endif

                        @if(!empty($instance['contract_statements']))
                            <div class="attributo">
                                <span class="titolo">Estremi del contratto:</span>
                                <span class="text-muted">
                                             {{e: $instance['contract_statements'] }}
                                        </span>
                            </div>
                        @endif

                        @if(!empty($instance['start_date']))
                            <div class="attributo">
                                <span class="titolo">Data inizio:</span>
                                <span class="text-muted">
                                             {{date('d-m-Y|date'): $instance['start_date'] }}
                                        </span>
                            </div>
                        @endif

                        @if(!empty($instance['end_date']))
                            <div class="attributo">
                                <span class="titolo">Data fine:</span>
                                <span class="text-muted">
                                             {{date('d-m-Y|date'): $instance['end_date'] }}
                                        </span>
                            </div>
                        @endif

                        @if(!empty($instance['properties']))
                            @php
                                $i = 0;
                            @endphp
                            <div class="attributo">
                                <span class="titolo">Immobili:</span>
                                @foreach($instance['properties'] as $property)
                                    <a class="text-muted"
                                       href="{{ siteUrl('page/133/details/'. (int) $property['id'].'/'.urlTitle($property['name'])) }}">
                                        {{e: $property['name'] }}
                                    </a>
                                    {{ $i == count($instance['properties'])-1 ? '' : ', ' }}
                                    @php
                                        $i++;
                                    @endphp
                                @endforeach
                            </div>
                        @endif

                        @if(!empty($instance['structure']))
                            <div class="attributo">
                                <span class="titolo">Ufficio referente per il contratto:</span>
                                <a class="text-muted"
                                   href="{{ siteUrl('page/40/details/'. (int) $instance['structure']['id'].'/'.urlTitle($instance['structure']['structure_name'])) }}">
                                    {{e: $instance['structure']['structure_name'] }}
                                </a>
                            </div>
                        @endif
                    </div>

                    @if(!empty($instance['notes']))
                        <h3 id="anchor1" class="testo-blu anchor page-subtitle mt-3">Note</h3>
                        {{xss: $instance['notes'] }}
                    @endif

                    {{-- Attach List --}}
                    {% include v1/layout/partials/attach_list %}

                    {{--  Created/Update Info --}}
                    {% include v1/layout/partials/created_updated_info %}

                </div>

                {{-- Right Menu --}}
                {% include v1/layout/partials/right_menu %}

            </div>
        </div>
    </section>
</main>



{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}

{% endblock %}