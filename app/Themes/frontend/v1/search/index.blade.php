@php
    /**
     * Nome applicativo: PAT
     * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
     */

    defined('_FRAMEWORK_') OR exit('No direct script access allowed');
@endphp
{{-- Pagina per i risultati della ricerca --}}
{% extends v1/layout/master %}
{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}
@php
    $_tpl = '<div class="text-center" ><div class="fa-2x"><i class="fas fa-spinner fa-spin"></i></div><span class="text-muted">Attendere...</span></div>';
@endphp
<main>
    <div class="container px-4 my-4">
        <div class="row">
            <div class="col px-lg-2">
                <nav class="breadcrumb-container" aria-label="breadcrumb">
                    <ol class="breadcrumb">
                        <li class="breadcrumb-item">
                            <a href="{{siteUrl()}}">Home</a><span class="separator">/</span>
                        </li>
                        <li class="breadcrumb-item active">
                            <a href="#">Risultato della ricerca</a>
                        </li>
                    </ol>
                </nav>
            </div>
        </div>
        <div class="row">
            <div class="col">
                <div class="row">
                    <div class="col py-3 py-lg-5">
                        <h1>Risultato di ricerca</h1>
                    </div>
                </div>
                <div class="d-block d-lg-none d-xl-none">
                    <div class="row pb-3">
                        <div class="col-6"><small>Trovati <span class="result-total">0</span> risultati totali</small></div>
                        <div class="col-6">
                            <div class="float-right">
                                <a href="#categoryCollapse" role="button" class="font-weight-bold text-uppercase"
                                   data-toggle="collapse" aria-expanded="false" aria-controls="categoryCollapse">
                                    Filtri
                                </a>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
        <div class="row border-top">
            <aside class="col-lg-3 py-lg-5">
                <div class="collapse d-lg-block d-xl-block" id="categoryCollapse">
                    @if(!empty($sections))
                        <div class="pt-4 pt-lg-0">
                            <h6 class="text-uppercase">Categorie</h6>
                            <div class="mt-4 list-categories">
                                <ul>
                                    @php
                                        $i=0;
                                    @endphp
                                    @foreach($sections AS $s)

                                        <li data-node="{{ (int) $s['id'] }}" class="hidden-btn-section-search"
                                            id="{{'section_'. (int) $s['id']}}"
                                            data-section-name="{{escape_xss: $s['name'] }}">
                                            <a href="{{ siteUrl('/search/terms') }}" data-id="{{ (int) $s['id'] }}"
                                               data-m="{{ $s['model'] }}" class="link-filter">
                                                {{e: $s['name'] }}
                                                <span class="badge bg-primary text-right">
                                                    <i class="fas fa-spinner fa-spin"></i>
                                                </span>
                                            </a>
                                        </li>
                                        @php
                                            $i++;
                                        @endphp
                                    @endforeach
                                </ul>
                            </div>

                            <div class="mt-4">
                                <a class="font-weight-bold" role="button" id="all" data-expand="false">
                                    Mostra tutto <i class="far fa-plus-square"></i>
                                </a>
                            </div>
                        </div>
                    @endif
                </div>
            </aside>

            <section class="col-lg-9 py-lg-5">
                <div class="d-none d-lg-block d-xl-block">
                    <div class="row pb-3 px-4 border-bottom">
                        <div class="col-12"><small>Trovati <span class="result-total">0</span> risultati totali</small>
                        </div>
                        <div class="col-12">
                            {{ form_open('search',['method'=>'GET','id'=>'det_form_search','name'=>'det_form_search','class'=>'det_form_search']) }}
                            {{ form_input([
                                'id'=>'_text',
                                'name'=>'s',
                                'class'=>'_text',
                                'value'=> \System\Input::get('s',true)
                            ]) }}
                            {{ form_close() }}
                        </div>
                    </div>
                </div>
                @if(empty($errorHtml))
                    <div class="row" class="async_search_result_terms">
                        <div id="spinner-loading" class="col-md-12 text-center mt-4 mb-4">
                            {{ $_tpl }}
                        </div>

                    </div>
                @else
                    <div class="alert alert-danger mt-3" role="alert">
                        <h5 id="importante">Attenzione!</h5>
                        <b>Ricerca non eseguita: si prega di inserire almeno 3 caratteri!</b>.
                    </div>
                @endif
            </section>
        </div>
    </div>
</main>
{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}

{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
<script type="text/javascript">
    let count = 0;
    let resultTotal = 0;
    let sectionNum = $('.list-categories > ul > li').length;
    let searchIndex = 0;

    $(document).ready(() => {
        $('#all').hide();

        /**
         * Metodo chiamato al click su una sezione
         */
        $('.list-categories > ul > li > a').bind('click', (e) => {
            e.preventDefault();
            let getHref = e.target.href
            let getId = e.target.getAttribute('data-id')
            let getModel = e.target.getAttribute('data-m')
            const params = new URLSearchParams({
                'sid': getId,
                'model': getModel,
                's': encodeURIComponent('{{ htmlEscape(\System\Input::get('s',true)) }}'),
                '{{ $keyToken }}': '{{ $token }}',
            });
            const str = params.toString();
            $('li').css('font-weight', '');
            // Evidenzio la sezione di cui si stanno vedendo i risultati
            $('#section_' + getId).css('font-weight', '600');
            asyncSearchResultTerms(getHref + "?" + str, getId);
            $('#categoryCollapse').removeClass('show');
        })

        let hideNode = false;

        /**
         * Metodo chiamato al caricamento della pagina, effettua la query su tutte le sezioni e ritorna quelle in cui
         * trova risultati con il numero di riscontri per ognuna
         */
        $('.list-categories > ul > li').each((i, v) => {

            let children = v.children;

            $.ajax({
                type: 'GET',
                url: '{{ siteUrl('search/nums') }}',
                dataType: "JSON",
                data: {
                    'sid': children[0].getAttribute('data-id'),
                    'model': children[0].getAttribute('data-m'),
                    's': '{{e: $keyword }}',
                    '{{ $keyToken }}': '{{ $token }}',
                },
                beforeSend: () => {
                },
                success: (data) => {
                    let response;

                    try {
                        response = $.parseJSON(data);
                    } catch (e) {
                        // Se non viene trovato alcun elemento mostro il messaggio
                        ++searchIndex;
                        if (searchIndex == sectionNum && (resultTotal == 0 || isNaN(resultTotal))) {
                            $('#spinner-loading').empty().append('Nessun elemento trovato!');
                        }
                        response = data;
                    }
                    let isNumRows = parseInt(response.data.num);

                    resultTotal += isNumRows || 0;

                    if (isNumRows === 0 || isNaN(isNumRows)) {
                        v.remove();
                    } else {

                        if (count === 0) {

                            let getHref = children[0].getAttribute('href')
                            let getId = children[0].getAttribute('data-id')
                            let getModel = children[0].getAttribute('data-m')
                            const params = new URLSearchParams({
                                'sid': getId,
                                'model': getModel,
                                's': encodeURIComponent('{{escape_xss: \System\Input::get('s') }}')
                            });
                            const str = params.toString();
                            $('#section_' + getId).css("font-weight", "600");

                            // Mostro i risultati della ricerca della prima sezione che viene processata
                            asyncSearchResultTerms(getHref + "?" + str, getId);
                        }

                        if (count <= 9) {
                            v.style.display = 'block';
                        } else {
                            v.classList.add('node-hidden');
                        }

                        if (!hideNode && document.getElementsByClassName("node-hidden").length > 0) {
                            $('#all').show();
                            hideNode = true;
                        }

                        setTimeout(() => {
                            children[0].children[0].innerHTML = isNumRows;
                        }, 200)

                        expandedAll()
                        count++

                        $('.result-total').empty().append(resultTotal);

                    }
                },
                complete: () => {
                },
                error: (jqXHR, status) => {
                }
            });
        });
    })

    /**
     * Metodo chiamato per espandere o comprimere le sezioni da mostrare
     */
    function expandedAll() {
        $('#all').on('click', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            if ($(this).attr('data-expand') === 'false') {
                $(this).html('Mostra meno <i class="far fa-minus-square"></i>');
                $(this).attr('data-expand', true);
                $('.node-hidden').show();
            } else {
                $(this).html('Mostra tutto <i class="far fa-plus-square"></i>');
                $(this).attr('data-expand', false);
                $('.node-hidden').hide();
            }
        });
    }

    /**
     * Metodo che viene chiamato quando si clicca su una sezione. Ritorna i risultati della ricerca sulla sezione
     * selezionata
     */
    function asyncSearchResultTerms() {

        var arg = arguments;
        let sectionId = arg[1];

        $.ajax({
            url: arg[0],
            data: {},
            method: 'GET',
            dataType: 'json',
            async: false,
            beforeSend: () => {
                $('#spinner-loading').empty().append('{{ $_tpl }}');
            },
            success: (data) => {
                let response;
                try {
                    response = $.parseJSON(data);
                } catch (e) {
                    response = data;
                }

                // Recupero il nome della sezione selezionata
                let section = $('#section_' + sectionId).attr('data-section-name');
                $('#spinner-loading').empty().append(`<p style="text-align: initial;">Risultati per <strong>"${section || ''}"</strong></p>`);
                $('#spinner-loading').append(response.data.template);

                asyncPagination();
            },
            complete: () => {
            },
            error: (jqXHR, status) => {
            }
        });
    }

    function asyncPagination() {
        $('#async_pagination_search_result a').click('click', function (e) {
            e.preventDefault();
            var href = $(this).attr('href');

            if (href.slice(-2) !== '#!') {
                asyncSearchResultTerms($(this).attr('href'));
            }
        })
    }
</script>
{% endblock %}