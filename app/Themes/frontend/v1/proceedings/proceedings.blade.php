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
            <div class="row">
                <div class="col-<?php echo (empty($table) ?  8 : 12) ?>">

                    {{-- Nome pagina --}}
                    <h1>{{e: !empty($h1) ? $h1 :  $pageName }}</h1>

                    {{-- Contenuto della pagina --}}
                    {% include v1/layout/partials/page_content %}

                    @if($noRequiredPublication)
                        {{-- Alert contenuti non più obbligatori --}}
                        {% include v1/layout/partials/no_required_publication_message %}
                    @endif

                    @if(!empty($archivedList))
                        <div class="alert alert-info" role="alert">
                            <b>In questa pagina è presente un elenco dell'archivio "Procedimenti"</b>.
                        </div>
                    @endif

                </div>
            </div>
        </div>
    </section>

    {{-- Sezione per i risultati della ricerca --}}
    {% include v1/layout/partials/search_results %}

</main>



{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
{% endblock %}