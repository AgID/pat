<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */
?>
@php
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
                        <div class="col-6"><small class="result-summary fw-bold">Trovati risultati in 0 sezioni</small>
                        </div>
                        <div class="col-6">
                            <div class="float-right">
                                <a href="#categoryCollapse" role="button" class="font-weight-bold text-uppercase"
                                   data-bs-toggle="collapse" aria-expanded="false" aria-controls="categoryCollapse">
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
                                               data-m="{{ $s['model'] }}" data-exact="{{ (int)\System\Input::get('exact_search',true) ?? 0 }}" class="link-filter">
                                                {{e: $s['name'] }}
                                            </a>
                                        </li>
                                        @php
                                            $i++;
                                        @endphp
                                    @endforeach
                                </ul>
                            </div>

                            <div class="text-left mt-2" id="spinner_sections">
                                <div class="fa-1x">
                                    <i class="fas fa-spinner fa-spin"></i>
                                    <span class="text-muted">Caricamento sezioni in corso...</span>
                                </div>
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
                        <div class="col-12"><small class="result-summary fw-bold">Trovati risultati in 0 sezioni</small>
                        </div>
                        <div class="col-12">
                            {{ form_open('search',['method'=>'GET','id'=>'det_form_search','name'=>'det_form_search','class'=>'det_form_search']) }}
                            {{ form_input([
                                'id'=>'_text',
                                'name'=>'s',
                                'class'=>'_text',
                                'value'=> \System\Input::get('s',true)
                            ]) }}
                            <div class="mt-2">
                             {{ form_checkbox([
                                'id' => 'exact_search',
                                'name' => 'exact_search',
                                'value' => '1',
                                'class' => 'form-check-input'
                            ]) }}
                                <label class="form-check-label" for="exact_search">
                                    Cerca frase esatta
                                </label>
                            </div>


                            {{ form_close() }}
                        </div>
                    </div>
                </div>
                @if(empty($errorHtml))
                    <div class="row async_search_result_terms">
                        <div id="spinner-loading" class="col-md-12 text-center mt-4 mb-4">
                            {{ $_tpl }}
                        </div>

                    </div>
                @else
                    <div class="alert alert-danger mt-3" role="alert">
                        <h5 id="importante">Attenzione!</h5>
                        <span id="error_search"><b>Ricerca non eseguita: si prega di inserire almeno 4 caratteri!</b>.</span>
                    </div>
                @endif

                <div class="alert alert-warning mt-3" role="alert" style="display: none;" id="error_search">
                </div>
            </section>
        </div>
    </div>
</main>
{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
<style>
    .disabled {
        color: gray;
        text-decoration: none;
    }

    a.disabled span {
        background-color: gray !important;
    }
</style>

{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
<script type="text/javascript">
    let sectionCount = 0;
    let sectionNum = $('.list-categories > ul > li').length;
    let searchIndex = 0;

    $(document).ready(() => {

        @if(!empty((int)\System\Input::get('exact_search',true)))
            $("#exact_search").prop('checked', true)
        @endif

        $('#all').hide();

        document.getElementById("_text").disabled = true;

        const sections = $('.list-categories > ul > li > a');
        /**
         * Metodo chiamato al click su una sezione
         */
        sections.bind('click', (e) => {
            e.preventDefault();
            let getHref = e.target.href
            let getId = e.target.getAttribute('data-id')
            let getModel = e.target.getAttribute('data-m')
            let getExactSearch = $("#exact_search").prop('checked') ? 1 : 0;
            const params = new URLSearchParams({
                'sid': getId,
                'model': getModel,
                's': encodeURIComponent('{{ htmlEscape(\System\Input::get('s',true)) }}'),
                'exact_search': getExactSearch,
                '{{ $keyToken }}': '{{ $token }}',
            });
            const str = params.toString();
            $('li').css('font-weight', '');
            $('li').css('padding-left', '');
            $('li').css('border-left', '');

            // Evidenzio la sezione di cui si stanno vedendo i risultati
            $('#section_' + getId).css('font-weight', '600');
            $('#section_' + getId).css('padding-left', '.3rem');
            $('#section_' + getId).css('border-left', '2px solid var(--primary)');
            asyncSearchResultTerms(getHref + "?" + str, getId, true);
            $('#categoryCollapse').removeClass('show');
        });

        let hideNode = false;
        let firstShown = false; // rimpiazza la logica "count === 0" ma in parallelo

        const $items = $('.list-categories > ul > li');
        sectionNum = $items.length;

        sections.addClass('disabled').css('pointer-events', 'none');

        const stopwords = [
            'a','ad','al','allo','ai','agli','all','agl',
            'alla','alle','con','col','coi','da','dal','dallo',
            'dai','dagli','dalla','dalle','di','del','dello',
            'dei','degli','della','delle','in','nel','nello',
            'nei','negli','nella','nelle','su','sul','sullo',
            'sui','sugli','sulla','sulle','per','tra','fra',
            'il','lo','la','i','gli','le','un','uno','una',
            'ma','ed','se','non','più','anche','come','dove',
            'che','chi','cui','quando','quale','quali','quello',
            'quella','questo','questa','questi','queste','loro',
            'mio','mia','tuo','tua','suo','sua','nostro','nostra',
            'vostro','vostra','miei','mie','tuoi','tue','suoi','sue',
            'nostri','nostre','vostri','vostre','io','tu','lui','lei',
            'noi','voi','loro','mi','ti','ci','vi','ne','si'
        ];

        const keyWord = '{{e: $keyword }}';

        const words = keyWord.split(/\s+/).filter(word => word.length >= 4);
        const isOnlyStopwords = words.length > 0 && words.every(word => stopwords.includes(word));

        if(isOnlyStopwords) {
            const errorSearch = document.getElementById('error_search');

            $('#spinner-loading').remove();
            document.getElementById("_text").disabled = false;

            sections.removeClass('disabled').css('pointer-events', 'auto');
            $('#spinner_sections').remove();

            errorSearch.innerHTML = `<h5 id="importante">Attenzione!</h5>Ops! Hai effettuato una ricerca con termini troppo comuni o non specifici.<br>
                    Prova a inserire parole chiave più significative o rilevanti per ottenere risultati migliori.<br>`;
            errorSearch.style.display = 'block';
        } else {

        (async function runParallel() {

            try {
                const promises = $items.toArray().map((v, i) => handleItem(i, v));
                await Promise.all(promises);

                // Dopo che tutte le chiamate sono complete, se non è stato trovato nulla
                if (sectionCount === 0) {
                    $('#spinner-loading').empty().append('Nessun elemento trovato!');
                }
            } catch (err) {
                console.error('Errore generale nelle richieste:', err);
            } finally {
                document.getElementById("_text").disabled = false;

                sections.removeClass('disabled').css('pointer-events', 'auto');

                $('#spinner_sections').remove();

                if (!hideNode && document.getElementsByClassName('node-hidden').length > 0) {
                    $('#all').show();
                    hideNode = true;
                }
            }
        })();}

        // ---- helper per singolo item (i, v) ----
        async function handleItem(i, v) {
            const children = v.children;


            try {
                const data = await $.ajax({
                    type: 'GET',
                    url: '{{ siteUrl('search/nums') }}',
                    dataType: 'json',
                    data: {
                        'sid': children[0].getAttribute('data-id'),
                        'model': children[0].getAttribute('data-m'),
                        's': keyWord,
                        'exact_search': $("#exact_search").prop('checked') ? 1 : 0,
                        '{{ $keyToken }}': '{{ $token }}',
                    }
                });

                let response;
                try {
                    response = (typeof data === 'string') ? $.parseJSON(data) : data;
                } catch (e) {
                    response = data;
                }

                const hasResults = response?.data?.has_results === true || parseInt(response?.data?.has_results) === 1;

                if (!hasResults) {
                    // nessun risultato per questa sezione -> rimuovi nodo
                    v.remove();
                } else {
                    // prima sezione con risultati > 0, mostra risultati iniziali
                    if (!firstShown) {
                        firstShown = true;

                        const getHref = children[0].getAttribute('href');
                        const getId = children[0].getAttribute('data-id');
                        const getModel = children[0].getAttribute('data-m');
                        const getExactSearch = $("#exact_search").prop('checked') ? 1 : 0;

                        const params = new URLSearchParams({
                            'sid': getId,
                            'model': getModel,
                            'exact_search': getExactSearch,
                            's': encodeURIComponent('{{escape_xss: \System\Input::get('s') }}')
                        });
                        const str = params.toString();

                        $('#section_' + getId).css('font-weight', '600');
                        $('#section_' + getId).css('padding-left', '.3rem');
                        $('#section_' + getId).css('border-left', '2px solid var(--primary)');
                        // come nel tuo codice: mostro la prima sezione valida trovata
                        await asyncSearchResultTerms(getHref + '?' + str, getId);
                        v.style.display = 'block';
                    }

                    // visibilità delle prime 10 sezioni con risultati
                    if (sectionCount <= 9) {
                        v.style.display = 'block';
                        // children[0].classList.add('disabled').css('pointer-events', 'none');
                    } else {
                        v.classList.add('node-hidden');
                    }

                    expandedAll();
                    sectionCount++;

                    $('.result-summary').text(sectionCount === 1
                        ? 'Trovati risultati in 1 sezione'
                        : 'Trovati risultati in ' + sectionCount + ' sezioni');
                }
            } catch (xhrErr) {
                // errore ajax per questo item (mantengo silenzioso come il tuo)
                // console.error('Errore richiesta item', i, xhrErr);
            } finally {
                // incremento il contatore di completamento (come facevi con searchIndex)
                searchIndex++;
                if (searchIndex === sectionNum && sectionCount === 0) {
                    $('#spinner-loading').empty().append('Nessun elemento trovato!');
                }
            }
        }
    });

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
    async function asyncSearchResultTerms() {

        const arg = arguments;
        let sectionId = arg[1];

        $.ajax({
            url: arg[0],
            data: {},
            method: 'GET',
            dataType: 'json',
            beforeSend: () => {
                $('#spinner-loading').empty().append('{{ $_tpl }}');
                $('.list-categories > ul > li > a').css('pointer-events', 'none');
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
                if (arg[2]) {
                    setTimeout(() => $('.list-categories > ul > li > a').css('pointer-events', 'auto'), 1000);
                }
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
            const href = $(this).attr('href');

            if (href.slice(-2) !== '#!') {
                asyncSearchResultTerms($(this).attr('href'));
            }
        })
    }
</script>
{% endblock %}
