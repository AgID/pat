<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed');
helper('form');
//
?>

{{-- Footer del patos --}}
<footer>
    <div class="container">
        <div class="logo">
            <img {{xss: getInstitutionLogo() }}>
            <h2>{{e: $_institution_info['full_name_institution'] }}</h2>
        </div>
        <div class="wrapper">
            {{-- Colonna Contatti --}}
            <div class="colonna">
                <h3>Contatti</h3>
                <p>{{e: getInstitutionFullAddress() }} <br>
                    @if(!empty($_institution_info['email_address']))
                        EMAIL <a href="mailto:{{xss: $_institution_info['email_address'] }}">
                            {{e: $_institution_info['email_address'] }}
                        </a>
                        <br>
                    @endif
                    @if(!empty($_institution_info['certified_email_address']))
                        PEC <a href="mailto:{{xss: $_institution_info['certified_email_address'] }}">
                            {{e: $_institution_info['certified_email_address'] }}
                        </a>
                        <br>
                    @endif
                    Centralino {{e: $_institution_info['phone'] }} <br>
                    P. IVA {{e: $_institution_info['vat'] }}
                </p>
            </div>

            {{-- Colonna Link Utili --}}
            <div class="colonna">
                <h3>Link utili</h3>
                <ul>
                    @if(!empty($_institution_info['institutional_website_url']))
                        <li>
                            <a href="{{xss: $_institution_info['institutional_website_url'] }}"
                               title="Vai al sito istituzionale">
                                Sito istituzionale
                            </a>
                        </li>
                    @endif

                    @if(empty($_institution_info['privacy_url']))
                        <li>
                            <a href="{{ siteUrl('/page/182/privacy') }}"
                               title="Vai alla pagina di Privacy e Cookie policy">
                                Privacy e Cookie policy
                            </a>
                        </li>
                    @else
                        <li>
                            <a href="{{xss: $_institution_info['privacy_url'] }}" title="Vai alla pagina Privacy">
                                Privacy
                            </a>
                        </li>
                        <li>
                            <a href="{{ siteUrl('/page/181/cookie-policy') }}" title="Vai alla pagina Cookie policy">
                                Cookie policy
                            </a>
                        </li>
                    @endif

                    <li>
                        <a href="{{ siteUrl('/page/256/accessibiltà') }}" title="Vai alla pagina Accessibilità">
                            Accessibilità
                        </a>
                    </li>
                </ul>
            </div>

            @if(!empty($_institution_info['footer_text']))
                {{-- Colonna personalizzata per l'ente --}}
                <div class="colonna">
                    <h3>Informazioni</h3>
                    <p>{{xss: $_institution_info['footer_text'] }}</p>
                </div>
            @endif
            <div class="colonna">
                <img class="logo-it" src="{{ baseUrl('assets/frontend/v1/img/logo_lg_it.png') }}"
                     alt="Il presente sito applica le nuove linee guida di design per i servizi web della PA">
            </div>
        </div>
    </div>
</footer>

{{-- Modale ricerca --}}
<div class="modal fade" tabindex="-1" role="dialog" id="modalRicerca" aria-labelledby="modalRicercaTitle">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="container py-5">
                <div class="modal-header">
                    <button data-bs-dismiss="modal" type="button"><i class="fas fa-arrow-left"></i></button>
                    <h2 class="modal-title " id="modalRicercaTitle">Cerca</h2>
                </div>
                {{ form_open('search',['id'=>'prev_search','name'=>'prev_search','class'=>'prev_search','method'=>'GET']) }}
                <div class="form-row position-relative mb-4">
                    <label class="sr-only" for="testo_ricerca">Inserisci il testo da cercare</label>
                    {{ form_input([
                        'id' => 'testo_ricerca',
                        'placeholder' => 'Cerca informazioni, persone, servizi',
                        'autocomplete' => 'off',
                        'name' => 's'
                    ]) }}
                    <span class="icona-cerca fas fa-search"></span>
                </div>

                <div class="form-row">
                    <button type="submit" id="btn_search" class="btn btn-outline-primary">
                        Cerca
                    </button>
                </div>
                {{ form_close() }}
            </div>
        </div>
    </div>
</div>

<script>

    // Autofocus sul campo di ricerca
    $(document).ready(function () {
        $('#modalRicercaBtn').on('click', function (e) {
            e.preventDefault();
            setTimeout(() => {
                $('#testo_ricerca').focus();
            }, "480")
        });
    });
</script>