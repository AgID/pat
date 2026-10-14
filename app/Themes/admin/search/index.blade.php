{{--  Regolamenti e documentazione index (Paginazione) --}}
<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
{% extends layout/master %}
{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}
@php
    $_tpl = '<div class="text-center" ><div class="fa-2x"><i class="fas fa-spinner fa-spin"></i></div><span class="text-muted">Attendere...</span></div>';
@endphp
<button class="btn btn-outline-primary text-bold d-lg-none d-sm-block mb-2" id="pulsante-filtri">+ Filtri</button>
<div class="row pb-5">
    @if(!empty($permits))
        <div class="col-md-3 d-lg-block d-md-none" id="lista-filtri">
            <ul class="list-group" style="font-size: 1rem;" id="categories-list">
                @foreach($permits AS $permit)
                    @php
                        $permit = $permit ?? null;
                            $queryString = http_build_query([
                                'sid' => $permit['sections_bo_id'],
                                'type' => urlTitle($permit['name'],'_',true),
                                'model' => $permit['model'],
                                's' => htmlEscape(\System\Input::get('s',true)),
                            ]);
                    @endphp
                    <li class="list-group-item justify-content-between align-items-center d-none">
                        <a href="{{ siteUrl('admin/search/result/terms') }}?{{ $queryString }}"
                           data-sid="{{ $permit['sections_bo_id'] }}" data-model="{{ $permit['model'] }}"
                           class="data_search testo_risultato" data-label="{{ urlTitle($permit['name'],'_',true) }}">
                            <!--{{ $permit['icon'] }}--> {{ $permit['name'] }}
                        </a>

                        <span id="current_info_{{ $permit['sections_bo_id'] }}"
                              class="badge badge-primary badge-pill numero-risultato">
                           <i class="fas fa-spinner fa-spin"></i>
                        </span>
                    </li>
                @endforeach
            </ul>
        </div>
    @endif
    @if(empty($errorHtml))
        <div class="col-md-9">
            <div class="row">
                <div class="col-md-12 text-muted">
                    <h3>RISULTATI DI RICERCA PER <small style="color: #444444;" id="filter_section_bo"></small></h3>
                    <hr>
                </div>
                <div class="col-md-12" id="async_search_result_terms">
                    {{ $_tpl }}
                </div>
            </div>
        </div>
    @else
        <div class="col-md-12">
            <div class="row">
                <div class="col-md-12">
                <div class="callout callout-danger">
                    <h5>Attenzione!</h5>
                    <p>Ricerca non eseguita: si prega di inserire almeno 4 caratteri!</p>
                </div>
                </div>
            </div>
        </div>
    @endif
</div>
{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
{{ css('datatables-bs4/css/dataTables.bootstrap4.min.css','common') }}
{{ css('datatables-responsive/css/responsive.bootstrap4.min.css','common') }}
{{ css('datatables-buttons/css/buttons.bootstrap4.min.css','common') }}
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
{{ js('datatables/jquery.dataTables.min.js','common') }}
{{ js('datatables-bs4/js/dataTables.bootstrap4.min.js','common') }}
{{ js('datatables-responsive/js/dataTables.responsive.min.js','common') }}
{{ js('datatables-responsive/js/responsive.bootstrap4.min.js','common') }}
<script type="text/javascript">

    const keywordString = '{{ htmlEscape(\System\Input::get('s',true)) }}';
    let i = 0;
    let find = false;

    function asyncPagination() {
        $('#async_pagination_search_result a').click('click', function (e) {
            e.preventDefault();
            const href = $(this).attr('href');

            if (href.slice(-2) !== '#!') {
                asyncSearchResultTerms($(this).attr('href'));
            }
        })
    }

    /**
     * Carica il numero di risultati per le varie sezioni
     */
    function loadNumbersSearchResult() {

        const args = arguments;
        const selector = '#current_info_' + args[0];
        $('#filter_section_bo').empty().append(`"${keywordString}"`);

        $.ajax({
            url: '<?php echo siteUrl('/admin/search/result/nums') ?>',
            data: {
                sid: args[0],
                type: args[1],
                model: args[2],
                s: keywordString,
                {{ $keyToken }} : '{{ $token }}',
            },
            method: 'GET',
            dataType: 'json',
            beforeSend: function () {
            },
            success: function (response) {

                response = parseJson(response);

                //var selector = '#current_info_' + args[0];
                if (parseInt(response.data.num) > 0) {

                    {{-- Appendo il titolo della richiesta  asincrona --}}
                    const link = $(selector).prev().attr('href');
                    const text = $(selector).prev().text();

                    if (!find) {

                        find = true;

                        $('#filter_section_bo').empty().append('"' + text + '"');

                        {{-- Paginazione dati --}}
                        asyncSearchResultTerms(link);
                    }

                    $(selector).empty().append(response.data.num);
                    $(selector).parent().removeClass("d-none");
                    $(selector).parent().addClass('d-flex');

                }

                const elTotal = parseInt($('.list-group-item').length);

                i++;

                if (i === elTotal) {
                    if (!find) {
                        $('#async_search_result_terms').empty().append('Nessun elemento trovato!');
                    }
                    $('#categories-list').next().hide();
                }

            },
            complete: function () {
            },
            error: function (error) {
            }
        });

    }

    /**
     * Carica i risultati per una singola sezione
     */
    function asyncSearchResultTerms() {

        const arg = arguments;

        $.ajax({
            url: arg[0],
            data: {},
            method: 'GET',
            dataType: 'json',
            async: false,
            beforeSend: function () {
                $('#async_search_result_terms').empty().append('{{ $_tpl }}');
            },
            success: function (response) {

                response = parseJson(response);

                $('#async_search_result_terms').empty().append(response.data.template);
                asyncPagination();
            },
            error: function (error) {
            }
        });
    }

    /**
     * Funzione che nasconde/mostra i filtri
     */
    function filter() {
        $('#lista-filtri').toggle();
        $('#pulsante-filtri').text(function (i, text) {
            return text === "+ Filtri" ? "- Filtri" : "+ Filtri";
        })
    }


    $(document).ready(function () {
        $('#lista-filtri').hide();

        $('#categories-list').after('<div id="__l" style="width: 100%;" class="text-center">' +
            '<div class="spinner-border spinner-border-sm mt-1" role="status" id="spinner">' +
            '<span class="sr-only">Loading...</span>' +
            '</div> Caricamento elementi...</div>').fadeIn(200);

        $('.data_search').each(function (index) {

            {{-- Numeri di occorrente trovate --}}
            loadNumbersSearchResult(
                $(this).attr('data-sid'),
                $(this).attr('data-label'),
                $(this).attr('data-model')
            );

        });

        $('.data_search').bind('click', function (e) {
            e.preventDefault();
            $('#filter_section_bo').empty().append('"' + $(this).text() + '"');
            filter();
            asyncSearchResultTerms($(this).attr('href'));
        });

        $('#pulsante-filtri').on('click', function () {
            filter();
        });

    });
</script>
{% endblock %}
