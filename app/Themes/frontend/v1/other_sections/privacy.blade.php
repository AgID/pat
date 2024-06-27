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
                            Tincidunt vestibulum non feugiat eleifend vel ultricies vulputate mi malesuada. Massa imperdiet turpis pretium mi id. Finibus habitant sodales tempor dictum maecenas erat si lacus. Magnis aliquam penatibus nisl semper tristique nulla viverra. Odio penatibus risus lacinia facilisi id semper conubia auctor. Pharetra purus nullam vehicula hendrerit faucibus habitasse bibendum potenti. Amet habitant pharetra habitasse quam vulputate. Facilisis egestas primis eros leo diam urna maecenas eget platea amet.

                            Eros aenean nostra facilisi vel tempor sed viverra feugiat magnis. Vehicula si dolor himenaeos nunc arcu dis commodo ante parturient nullam lectus. Vel lorem accumsan efficitur phasellus ad penatibus pretium quam. Ridiculus curae class cursus rhoncus elementum aliquam eget. Vulputate mattis etiam himenaeos consectetuer inceptos nullam vel bibendum tortor. Viverra ante maximus eu nunc phasellus elementum egestas in consequat neque. Vestibulum posuere quisque phasellus consequat nascetur inceptos blandit eu venenatis. Habitasse id aenean dolor ad egestas dignissim sapien.
                        </p>

                        <p>
                            Tincidunt vestibulum non feugiat eleifend vel ultricies vulputate mi malesuada. Massa imperdiet turpis pretium mi id. Finibus habitant sodales tempor dictum maecenas erat si lacus. Magnis aliquam penatibus nisl semper tristique nulla viverra. Odio penatibus risus lacinia facilisi id semper conubia auctor. Pharetra purus nullam vehicula hendrerit faucibus habitasse bibendum potenti. Amet habitant pharetra habitasse quam vulputate. Facilisis egestas primis eros leo diam urna maecenas eget platea amet.

                            Eros aenean nostra facilisi vel tempor sed viverra feugiat magnis. Vehicula si dolor himenaeos nunc arcu dis commodo ante parturient nullam lectus. Vel lorem accumsan efficitur phasellus ad penatibus pretium quam. Ridiculus curae class cursus rhoncus elementum aliquam eget. Vulputate mattis etiam himenaeos consectetuer inceptos nullam vel bibendum tortor. Viverra ante maximus eu nunc phasellus elementum egestas in consequat neque. Vestibulum posuere quisque phasellus consequat nascetur inceptos blandit eu venenatis. Habitasse id aenean dolor ad egestas dignissim sapien.
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