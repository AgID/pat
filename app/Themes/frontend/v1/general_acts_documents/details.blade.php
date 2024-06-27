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
                <div class="col-lg-8">
                    <div class="titolo mb-4">
                        <h1 class="page-title">{{e: !empty($h1) ? $h1 : (!empty($instance['object']) ? $instance['object'] : '') }}</h1>
                    </div>

                    <div class="attributi">
                        @if(!empty($instance))
                            <div class="attributo">
                                <span class="titolo">Data documento:</span>
                                <span class="mr-2 text-muted">
                                    {{date('d-m-Y|date'): $instance['document_date'] }}
                                </span>
                            </div>

                            @if($currentPageId == 586)
                                @if(!empty($instance['cup']))
                                    <div class="attributo">
                                        <span class="titolo">CUP:</span>
                                        <span class="mr-2 text-muted">
                                    {{e: $instance['cup'] }}
                                </span>
                                    </div>
                                @endif

                                @if(!empty($instance['start_date']))
                                    <div class="attributo">
                                        <span class="titolo">Data di avvio:</span>
                                        <span class="mr-2 text-muted">
                                    {{date('d-m-Y|date'): $instance['start_date'] }}
                                </span>
                                    </div>
                                @endif

                                @if(!empty($instance['financing_amount']))
                                    <div class="attributo">
                                        <span class="titolo">Importo finanziamento:</span>
                                        <span class="mr-2 text-muted">
                                    &euro; {{currency('filter=escape_xss decimal_separator=, thousands_separator=.'): $instance['financing_amount'] }}
                                </span>
                                    </div>
                                @endif

                                @if(!empty($instance['financial_sources']))
                                    <div class="attributo">
                                        <span class="titolo">Fonti finanziarie:</span>
                                        <span class="mr-2 text-muted">
                                    {{e: $instance['financial_sources'] }}
                                </span>
                                    </div>
                                @endif

                                @if(!empty($instance['procedural_implementation_status']))
                                    <div class="attributo">
                                        <span class="titolo">Stato di attuazione procedurale:</span>
                                        <span class="mr-2 text-muted">
                                    {{e: $instance['procedural_implementation_status'] }}
                                </span>
                                    </div>
                                @endif
                            @endif

                            @if($currentPageId == 583)
                                @php
                                    $typologies = [
                                        'lavori' => 'Lavori pubblici, per assenza di lavori',
                                        'acquisti' => 'Acquisti di forniture e servizi, per assenza di acquisti di forniture e servizi',
                                    ];
                                @endphp
                                <div class="attributo">
                                    <span class="titolo">Tipologia:</span>
                                    <span class="mr-2 text-muted">
                                    {{e: $typologies[$instance['typology']] }}
                                </span>
                                </div>
                            @endif
                        @endif
                    </div>

                    @if(!empty($instance['notes']))
                        <h3 id="anchor1" class="testo-blu anchor page-subtitle mt-3">Dettagli</h3>
                        {{xss: $instance['notes'] }}
                    @endif

                    {{-- Attach List --}}
                    {% include v1/layout/partials/attach_list %}

                    {{--  Created/Update Info --}}
                    {% include v1/layout/partials/created_updated_info %}

                </div>

                {{-- Indice della pagina --}}
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