<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Pagina per Attività e procedimenti --}}

{% extends v1/layout/master %}

{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}

<main>
    <section>
        <div class="container">
            <div class="row variable-gutters _reverse">
                <div class="col-lg-8">

                    {{-- Nome pagina --}}
                    <h1>{{e:  !empty($h1) ? $h1 : $pageName }}</h1>

                    {{-- Contenuto della pagina --}}
                    {% include v1/layout/partials/page_content %}

                    @if($noRequiredPublication)
                        {{-- Alert contenuti non più obbligatori --}}
                        {% include v1/layout/partials/no_required_publication_message %}
                    @endif

                    <aside class="sidebar-sticky">
                        <div>
                            @if(!empty($instances['data']))
                                <ul class="lista-anchor">
                                    @foreach($instances['data'] as $instance)
                                        <li><i class="far fa-edit mr-1 text-muted"></i>
                                            <a
                                                    href="{{ siteUrl('page/'.(!empty($finalSectionId) ? $finalSectionId : '#!').'/details/'.$instance['id'].'/'.urlTitle($instance['name'])) }}"
                                                    data-id="{{e: $instance['id'] }}"
                                                    style="display: unset;">{{e: $instance['name'] }}
                                            </a>

                                            @if(!empty($instance['monitoring_datas']))

                                                <h3 id="monitoring_data" class="anchor testo-blu anchor mt-4">
                                                    Monitoraggio tempi procedimentali</h3>
                                                <div class="table-responsive">
                                                    <table class="table table-bordered table-sm mb-4">
                                                        <thead>
                                                        <tr class="intestazione-tabella">
                                                            <th scope="col">Anno</th>
                                                            <th scope="col">Numero Procedimenti Conclusi</th>
                                                            <th scope="col">Giorni Medi Conclusione</th>
                                                            <th scope="col">Percentuale Procedimenti Conclusi</th>
                                                        </tr>
                                                        </thead>
                                                        <tbody>
                                                        @foreach($instance['monitoring_datas'] as $monitoring)
                                                            <tr>
                                                                <td>{{e: !empty($monitoring['year']) ? $monitoring['year'] : null }}</td>
                                                                <td>
                                                                    {{e: !empty($monitoring['year_concluded_proceedings']) ? $monitoring['year_concluded_proceedings'] : null }}
                                                                </td>
                                                                <td>{{e: !empty($monitoring['conclusion_days']) ? $monitoring['conclusion_days'] : null }}</td>
                                                                <td>{{e: !empty($monitoring['percentage_year_concluded_proceedings']) ? $monitoring['percentage_year_concluded_proceedings'] : null }}</td>
                                                            </tr>
                                                        @endforeach
                                                        </tbody>
                                                    </table>
                                                </div>
                                            @endif
                                        </li>
                                    @endforeach
                                </ul>

                                {{ paginateBootstrap($instances) }}

                                @if(!empty($_institution_info['show_update_date']) && !empty($instances) && !empty($latsUpdatedElement))
                                    <p class="data-creazione mt-5" style="font-size: 14px;">
                                        <span class="icona far fa-clock"></span>
                                        <strong>{{ !empty($latsUpdatedElement['created_at']) ? 'Contenuto creato il ' . date('d-m-Y', strtotime((string)$latsUpdatedElement['created_at'])) : null }}
                                            {{ !empty($latsUpdatedElement['updated_at']) ? ' - Aggiornato il ' . date('d-m-Y', strtotime((string)$latsUpdatedElement['updated_at'])) : date('d-m-Y', strtotime((string)$latsUpdatedElement['created_at'])) }}</strong>
                                    </p>
                                @endif

                            @else
                                <h5 class="font-weight-bold">Nessun elemento presente</h5>
                            @endif
                        </div>
                    </aside>
                </div>

                {{-- Right Drawer --}}
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