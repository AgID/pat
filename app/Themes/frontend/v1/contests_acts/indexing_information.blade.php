<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Pagina per INFORMAZIONI D'INDICIZZAZIONE --}}

{% extends v1/layout/master %}

{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}
<main>
    <section class="my-5">
        <div class="container">
            <div class="row variable-gutters">
                <div class="col-lg-8">
                    <div class="titolo mb-4">
                        <h1 class="page-title">Informazioni d'indicizzazione</h1>
                    </div>
                </div>
            </div>

            <h4 class="page-subtitle">
                <a class="text-muted" href="{{ currentUrl() }}">{{e: !empty($element['object']) ? $element['object'] : null }}</a>
            </h4>
            <h5 class="text-muted page-subtitle">Tabella informativa d'indicizzazione per: Bandi, esiti ed avvisi</h5>
            <hr>

            <div class="form-row mb-3 p-1">
                <button type="button" id="btn_vertical" class="btn mr-3">
                    Tabella standard
                </button>
                <button type="submit" id="btn_horizontal" class="btn">
                    Tabella conforme al DPCM 26/04/2011
                </button>
            </div>

            <div class="table-responsive">
                <table class="table table-striped table-bordered">
                    <tbody style="border:1px solid #d6dce3" id="vertical-table">
                    @foreach($headers as $key => $value)
                        <tr>
                            <th scope="row">
                                {{ $key }}
                            </th>
                            <td>
                                {{ $value }}
                            </td>
                        </tr>
                    @endforeach
                    </tbody>

                    <tbody style="border:1px solid #d6dce3; display: none;" id="horizontal-table">
                    <tr>
                        @foreach($headers as $key => $value)
                            <th scope="col" style="min-width: 15rem;">
                                {{ $key }}
                            </th>
                        @endforeach
                    </tr>
                    <tr>
                        @foreach($headers as $key => $value)
                            <td>
                                {{ $value }}
                            </td>
                        @endforeach
                    </tr>
                    </tbody>
                </table>
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

<script type="text/javascript">
    $(document).ready(function () {
        $('#btn_vertical').addClass('btn-primary');
        $('#btn_horizontal').addClass('btn-outline-primary');

        $('#btn_vertical').on('click', function () {
            $('#btn_vertical').removeClass('btn-outline-primary').addClass('btn-primary');
            $('#btn_horizontal').removeClass('btn-primary').addClass('btn-outline-primary');
            $('#vertical-table').show();
            $('#horizontal-table').hide();
        });

        $('#btn_horizontal').on('click', function () {
            $('#btn_horizontal').removeClass('btn-outline-primary').addClass('btn-primary');
            $('#btn_vertical').removeClass('btn-primary').addClass('btn-outline-primary');
            $('#vertical-table').hide();
            $('#horizontal-table').show();
        });
    });
</script>
{% endblock %}