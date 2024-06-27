{{--  Archivio file index (Paginazione) --}}
<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
{% extends layout/master %}
{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}
@if($perms===true)
<div class="modal" id="message" tabindex="-1" role="dialog">
    <div class="modal-dialog modal-dialog-centered modal-lg" role="document">
        <div class="modal-content">
            <div class="modal-header" style="background-color: #343a40; color: #FFFFFF;">
                <h3 class="modal-title">Attenzione</h3>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close" style="color:#FFFFFF;">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body p-5" style="font-weight: 500; font-size: 18px;">
                <p>
                    Le seguenti operazioni su file e cartelle causano la <b style="color: red;">ROTTURA</b> (non
                    funzionamento) DEI COLLEGAMENTI:
                </p>
                <ul>
                    <li>
                        spostamento
                    </li>
                    <li>
                        modifica del nome
                    </li>
                    <li>
                        utilizzo di uno dei seguenti caratteri
                        <ul>
                            <li>
                                ' " “ ” ` ‘
                            </li>
                            <li>
                                % $ &amp; / = ? ! \ * &lt; &gt; | ^ £ +
                            </li>
                            <li>
                                lettere accentate
                            </li>
                        </ul>
                    </li>
                </ul>
                <p>
                    Scegliete accuratamente il nome di documenti e cartelle per evitare di modificarli successivamente.
                </p>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Close</button>
            </div>
        </div>
    </div>
</div>
{{-- File Manager --}}
<div id="elfinder">

</div>
@else
    {% include access_denied/index %}
@endif
{{-- ********************************************************************************************************************************************** --}}
{{-- ************************************************ Sistemare bug del tooltip, vedere sul footer ************************************************ --}}
{{-- ********************************************************************************************************************************************** --}}

{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
{{css('jquery-ui/jquery-ui.min.css', 'common')}}
{{css('jquery-ui/jquery-ui.structure.min.css', 'common')}}
{{css('jquery-ui/jquery-ui.theme.min.css', 'common')}}
{{ css('elfinder/css/elfinder.min.css', 'common') }}
{{ css('elfinder/css/theme.css', 'common') }}
<style>
    .modal-open {
        overflow: auto !important;
    / / you will need important here to override
    }

    .modal-backdrop {
        display: none !important;
    }
</style>
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
{{ js('jquery-ui-1.13.2/jquery-ui.min.js', 'common') }}
{{ js('elfinder/js/elfinder.min.js', 'common') }}
{{ js('elfinder/js/i18n/elfinder.it.js', 'common') }}

<script type="text/javascript">
    $(document).ready(function () {

        $('#message').modal('show');

    });
</script>

{{ loadElfinderJs() }}
{% endblock %}











