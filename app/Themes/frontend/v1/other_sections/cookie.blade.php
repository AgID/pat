<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
{{-- Pagina pivot per le pagine di snodo --}}
{% extends v1/layout/master %}
{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}
<main>
    <section class="my-2">
        <div class="container">
            <div class="row variable-gutters _reverse">
                <div class="col-lg-8">
                    {{-- Nome della pagina --}}
                    <h1 class="mb-4 page-title">{{e: $pageName }}</h1>

                    {{-- Contenuto della pagina --}}
                    @if(!empty($paragraphs))
                        {% include v1/layout/partials/page_content %}
                    @else
                        <p>
                            Elementum neque eleifend curae rhoncus habitant dis class etiam laoreet. Eros torquent pharetra ut per maximus curae. Taciti proin ligula lacus facilisis sem tristique. Tempus mauris facilisi nostra pretium porta nisi. Himenaeos euismod ac consectetuer si id velit semper netus mollis eu. Fringilla eros efficitur fames rhoncus mi vehicula metus facilisi mollis euismod. Vestibulum massa nascetur nam habitant dui rutrum nibh rhoncus ullamcorper sit lacus. Luctus metus accumsan odio condimentum viverra integer vehicula vestibulum imperdiet.

                            Consequat lorem torquent curabitur si conubia hendrerit maecenas ullamcorper gravida sagittis netus. Morbi per euismod ad risus nibh ac accumsan gravida. Fringilla volutpat cras ante neque vitae nunc rutrum habitasse sollicitudin. At pulvinar magnis vitae commodo congue sit. At senectus dui litora ultricies consectetuer semper nulla lectus lorem iaculis.
                        </p>

                        <p>
                            Convallis urna vestibulum ante pharetra fames suspendisse ullamcorper ut. Egestas ultricies penatibus vitae natoque vehicula urna nascetur nam cursus. Tincidunt feugiat leo morbi pharetra tortor per hendrerit lacinia. Quis class consequat ipsum orci lacus bibendum ligula dictum letius. Non egestas nec efficitur habitant proin hac ullamcorper praesent.

                            Ad si nec congue sit tortor adipiscing ipsum class euismod. Commodo habitant praesent augue himenaeos vestibulum est magna tortor molestie. Ac luctus consequat eu senectus vel purus. Facilisi euismod imperdiet nullam rhoncus letius ipsum. Sociosqu luctus in curae suscipit molestie parturient aliquet litora.
                        </p>
                    @endif

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