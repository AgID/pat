<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>


{{-- Sezione contenente la tabella con i risultati della ricerca e la paginazione --}}

<section id="risultati-ricerca">
    <div class="container">

        @if(!empty($showTableOnSearch) && isset($filterUsed) && empty($filterUsed))
            <br/>
            @php $instances = null; @endphp
        @elseif(!empty($table))

            {% include v1/layout/partials/open_data %}

            <div class="table-container-{{ $currentPageId ?? '' }}" id="risultati-tabella">
                {{ $table }}
            </div>

        @else
            <div class="box-card-shadow my-4">
                <h5 class="title">
                    <svg class="icon">
                        <use href="/assets/frontend/v1/css/sprites.svg#it-note"></use>
                    </svg>
                    Nessun elemento presente
                </h5>
            </div>
        @endif

        {{-- Paginazione della tabella --}}
        @if(!empty($instances))
            <div id="risultati-paginazione">
                {{ paginateBootstrap($instances) }}
            </div>
        @endif

        @if(!empty($_institution_info['show_update_date']) && !empty($instances) && !empty($latsUpdatedElement))
            <p class="data-creazione mt-5" style="font-size: 14px;">
                <span class="icona far fa-clock"></span>
                <strong>
                    @php
                        $hideCreatedAt = false;
                    @endphp
                    @if(!empty($hideCreatedAt))
                        {{ !empty($latsUpdatedElement['updated_at']) ? 'Contenuto aggiornato il ' . date('d/m/Y', strtotime((string)$latsUpdatedElement['updated_at'])) : date('d/m/Y', strtotime((string)$latsUpdatedElement['created_at'])) }}
                    @else
                        @if(!empty($latsUpdatedElement['publishing_start']))
                            {{ 'Contenuto pubblicato il ' . date('d/m/Y', strtotime((string)$latsUpdatedElement['publishing_start']))." - " }}
                        @elseif(!empty($latsUpdatedElement['created_at']))
                            {{ 'Contenuto creato il ' . date('d/m/Y', strtotime((string)$latsUpdatedElement['created_at']))." - " }}
                        @endif
                        {{ !empty($latsUpdatedElement['updated_at']) ? 'Aggiornato il ' . date('d/m/Y', strtotime((string)$latsUpdatedElement['updated_at'])) : date('d/m/Y', strtotime((string)$latsUpdatedElement['created_at'])) }}
                    @endif
                </strong>
            </p>
        @endif
    </div>
</section>

@if(!empty($ajaxPagination))
{{-- La paginazione AJAX sostituisce solo tabella e paginazione, senza ricaricare il layout.
     I link restano <a href> reali, quindi la navigazione funziona anche senza JavaScript. --}}
<style>
    /* Overlay di caricamento della sola area risultati. Usa il progress-spinner di
       Bootstrap Italia, il cui CSS e' gia' incluso nel tema. */
    #risultati-ricerca { position: relative; }

    .patos-loading-overlay {
        position: absolute;
        inset: 0;
        z-index: 5;
        display: none;
        background: rgba(255, 255, 255, .72);
    }

    .patos-loading-overlay[data-visible="1"] { display: block; }

    /* Lo spinner resta visibile anche quando si usa la paginazione in fondo a una
       tabella lunga. L'offset evita che venga coperto dall'header sticky. */
    .patos-loading-overlay .patos-loading-box {
        position: sticky;
        top: 4rem;
        display: flex;
        justify-content: center;
        padding-top: 1rem;
    }

    /* senza questo il contenitore flex comprime il cerchio, che ha larghezza fissa */
    .patos-loading-overlay .progress-spinner { flex: 0 0 auto; }

    @media (prefers-reduced-motion: reduce) {
        .patos-loading-overlay .progress-spinner { animation: none; }
    }
</style>
<script>
(function () {

    var results = document.getElementById('risultati-ricerca');

    // Senza fetch/history/closest non si intercetta nulla: i link continuano a funzionare da soli.
    if (!results || !window.fetch || !window.history || !window.history.pushState || !Element.prototype.closest) {
        return;
    }

    var live = document.createElement('div');
    live.className = 'sr-only';
    live.setAttribute('aria-live', 'polite');
    results.appendChild(live);

    // Overlay di caricamento: fuori da #risultati-tabella, che viene riscritto via innerHTML.
    // aria-hidden perche' lo stato di caricamento e' gia' comunicato da aria-busy sulla
    // tabella e dal messaggio nella live region qui sopra.
    var overlay = document.createElement('div');
    overlay.className = 'patos-loading-overlay';
    overlay.setAttribute('aria-hidden', 'true');
    overlay.innerHTML = '<div class="patos-loading-box">'
        + '<div class="progress-spinner progress-spinner-active size-sm"></div>'
        + '</div>';
    results.appendChild(overlay);

    // Ritardo prima di mostrare lo spinner: su risposte veloci un lampo di overlay
    // si nota piu' dell'attesa che dovrebbe segnalare.
    var SPINNER_DELAY = 180;
    var spinnerTimer = null;
    var inFlight = false;

    function spinnerOn() {
        spinnerTimer = window.setTimeout(function () {
            overlay.setAttribute('data-visible', '1');
        }, SPINNER_DELAY);
    }

    function spinnerOff() {
        window.clearTimeout(spinnerTimer);
        overlay.removeAttribute('data-visible');
    }

    function load(url, push) {

        var table = document.getElementById('risultati-tabella');
        var pager = document.getElementById('risultati-paginazione');

        if (!table || !pager) {
            window.location.href = url;
            return;
        }

        // Una richiesta alla volta: senza questa guardia due click ravvicinati sulla
        // paginazione lanciano due fetch, e vince quella che risponde per ultima.
        if (inFlight) {
            return;
        }
        inFlight = true;

        table.setAttribute('aria-busy', 'true');
        pager.setAttribute('aria-busy', 'true');
        spinnerOn();
        live.textContent = 'Caricamento dei risultati in corso';

        fetch(url, {
            headers: {'X-Requested-With': 'XMLHttpRequest', 'X-Patos-Fragment': '1'},
            credentials: 'same-origin'
        }).then(function (response) {

            if (!response.ok) {
                throw new Error(response.status);
            }

            return response.json();

        }).then(function (fragment) {

            if (!fragment || !fragment.table) {
                throw new Error('frammento vuoto');
            }

            table.innerHTML = fragment.table;
            pager.innerHTML = fragment.pagination || '';
            table.removeAttribute('aria-busy');
            pager.removeAttribute('aria-busy');
            spinnerOff();
            inFlight = false;

            if (push) {
                window.history.pushState({patosPage: fragment.current_page}, '', url);
            }

            live.textContent = 'Pagina ' + fragment.current_page + ' di ' + fragment.last_page;

            results.setAttribute('tabindex', '-1');
            results.focus();

        }).catch(function () {

            // Qualunque problema (rete, 500, JSON inatteso): navigazione normale, l'utente non resta bloccato.
            // Lo spinner resta acceso fino al cambio pagina: spegnerlo mostrerebbe la tabella
            // vecchia come se il caricamento fosse finito bene.
            inFlight = false;
            window.location.href = url;

        });
    }

    results.addEventListener('click', function (event) {

        if (event.button !== 0 || event.metaKey || event.ctrlKey || event.shiftKey || event.altKey) {
            return;
        }

        var link = event.target.closest('#risultati-paginazione a.page-link');

        if (!link || !link.getAttribute('href') || link.getAttribute('href') === '#!') {
            return;
        }

        event.preventDefault();
        load(link.href, true);
    });


    window.addEventListener('popstate', function () {
        load(window.location.href, false);
    });

})();
</script>
@endif

{{-- Bottom Menu --}}
{% include v1/layout/partials/bottom_menu %}
