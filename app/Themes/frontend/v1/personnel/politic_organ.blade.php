<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Pagina per Personale --}}

{% extends v1/layout/master %}

{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}

<main>

    <section class="my-2">
        <div class="container">
            <div class="row">
                <div class="col-12">

                    {{-- Nome pagina --}}
                    <h1>{{e: !empty($h1) ? $h1 : $pageName }}</h1>

                    {{-- Inclusione di librerie esterne --}}

                    {{-- Contenuto della pagina --}}
                    {% include v1/layout/partials/page_content %}

                </div>
            </div>
        </div>
    </section>


    @if(!empty($politic_organ))

        <section>
            <div class="container">

                @if(!empty($linkDownloadOpenData) and !empty($politic_organ['skills']))

                    {% include v1/layout/partials/open_data %}
                @endif

                @if(!empty($politic_organ['skills']))
                    <h3 class="testo-blu page-subtitle">Competenze</h3>{{xss: $politic_organ['skills'] }}
                @endif

                @if(!empty($structure_table))
                     <h3 class="testo-blu page-subtitle">Articolazione degli uffici</h3>
                     <div class="table-container-{{ $currentPageId ?? '' }}">
                         {{ $structure_table }}
                     </div>
                @endif

                @if(!empty($politic_organ['absence_office_reason']))
                    <h3 class="testo-blu page-subtitle">Motivazioni assenza uffici</h3>
                    <div class="my-2">{{xss: $politic_organ['absence_office_reason'] }}</div>
                @endif

                @if($politic_organ['show_personnel'] && !empty($personnel_table))
                         <h3 class="testo-blu page-subtitle">Personale associato</h3>
                    <div class="table-container-{{ $currentPageId ?? '' }}">
                        {{ $personnel_table }}
                    </div>
                @endif

                @if(!empty($_institution_info['show_update_date']) && !empty($politic_organ) && !empty($politic_organ['skills']))
                    <p class="data-creazione mt-5" style="font-size: 14px;">
                        <span class="icona far fa-clock"></span>
                        <strong>{{ !empty($politic_organ['created_at']) ? 'Contenuto creato il ' . date('d/m/Y', strtotime((string)$politic_organ['created_at'])) : null }}
                            {{ !empty($politic_organ['updated_at']) ? ' - Aggiornato il ' . date('d/m/Y', strtotime((string)$politic_organ['updated_at'])) : date('d/m/Y', strtotime((string)$politic_organ['created_at'])) }}</strong>
                    </p>
                @endif
            </div>
        </section>

    @endif

    {{-- Bottom Menu --}}
    {% include v1/layout/partials/bottom_menu %}

</main>

{{-- Link utilii --}}
{% include v1/layout/partials/card_link %}

{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
{% endblock %}
