<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

<section id="c-a-r">
    <div class="container">
    </div>
</section>

{{-- Card contenenti i link: Settori e servizi, Contattaci, Accesso Civico e Albo Pretorio --}}
<nav id="banner-cards" aria-label="Accesso rapido">
    <div class="container">
        <div class="{{ (!empty($_institution_info['bulletin_board_url'])) ?   'wrapper' : 'wrapper_3' }}">
            <div class="box" id="card-box-uffici">
                <div class="wrapper-contenuto">
                    <div class="titolo">
                        <span class="icona fas fa-building"></span>
                        <h3><a href="{{ siteUrl('page/40/' . urlTitle('Articolazione degli uffici')) }}" title="Vai alla pagina Articolazione degli uffici">Settori e servizi</a></h3>
                    </div>
                    <div class="contenuto">Strutture organizzative dell'ente</div>
                </div>
                <a href="{{ siteUrl('page/40/' . urlTitle('Articolazione degli uffici')) }}" class="pulsante-freccia" aria-label="Vai alla pagina Articolazione degli uffici">Vai alla pagina</a>
            </div>
            <div class="box" id="card-box-contattaci">
                <div class="wrapper-contenuto">
                    <div class="titolo">
                        <span class="icona fas fa-phone-alt"></span>
                        <h3><a href="{{ siteUrl('page/43/' . urlTitle('Telefono e posta elettronica')) }}" title="Vai alla pagina di contatto">Contattaci</a></h3>
                    </div>
                    <div class="contenuto">Tutti i recapiti degli uffici</div>
                </div>
                <a href="{{ siteUrl('page/43/' . urlTitle('Telefono e posta elettronica')) }}" class="pulsante-freccia" aria-label="Vai alla pagina di contatto">Vai alla pagina</a>
            </div>
            <div class="box" id="card-box-acc-civico">
                <div class="wrapper-contenuto">
                    <div class="titolo">
                        <span class="icona fas fa-info-circle"></span>
                        <h3><a href="{{ siteUrl('page/175/' . urlTitle('Accesso civico')) }}" title="Vai alla pagina Accesso civico">Accesso civico</a></h3>
                    </div>
                    <div class="contenuto">Informazioni sull’accesso civico</div>
                </div>
                <a href="{{ siteUrl('page/175/' . urlTitle('Accesso civico')) }}" class="pulsante-freccia" aria-label="Vai alla pagina Accesso civico">Vai alla pagina</a>
            </div>

            <!-- La card dell'albo pretorio la mostro solo se è presente il link dell'albo nelle informazioni dell'ente -->
            @if(!empty($_institution_info['bulletin_board_url']))
                <div class="box" id="card-box-albo">
                    <div class="wrapper-contenuto">
                        <div class="titolo">
                            <span class="icona fas fa-folder-open"></span>
                            <h3><a href="{{ $_institution_info['bulletin_board_url'] }}" title="Vai alla pagina Albo pretorio">{{ !empty($_institution_info['bulletin_board_url_name']) ? $_institution_info['bulletin_board_url_name'] : 'ALbo pretorio' }}</a></h3>
                        </div>
                        <div class="contenuto">Atti in pubblicazione</div>
                    </div>
                    <a href="{{ $_institution_info['bulletin_board_url'] }}" class="pulsante-freccia" aria-label="Vai alla pagina Albo pretorio">Vai alla pagina</a>
                </div>
            @endif
        </div>
    </div>
</nav>

<nav id="banner-cards-mobile">
    <div class="container">
        <div class="swiper slider-cards">
            <div class="swiper-wrapper">
                <div class="swiper-slide">
                    <div class="box" id="card-box-uffici-mob">
                        <div class="wrapper-contenuto">
                            <div class="titolo">
                                <span class="icona fas fa-building"></span>
                                <h3><a href="{{ siteUrl('page/40/' . urlTitle('Articolazione degli uffici')) }}" title="Vai alla pagina Articolazione degli uffici">Settori e servizi</a></h3>
                            </div>
                            <div class="contenuto">Strutture organizzative dell'ente</div>
                        </div>
                        <a href="{{ siteUrl('page/40/' . urlTitle('Articolazione degli uffici')) }}" class="pulsante-freccia" aria-label="Vai alla pagina Articolazione degli uffici">Vai alla pagina</a>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="box" id="card-box-contattaci-mob">
                        <div class="wrapper-contenuto">
                            <div class="titolo">
                                <span class="icona fas fa-phone-alt"></span>
                                <h3><a href="{{ siteUrl('page/43/' . urlTitle('Telefono e posta elettronica')) }}" title="Vai alla pagina di contatto">Contattaci</a></h3>
                            </div>
                            <div class="contenuto">Tutti i recapiti degli uffici</div>
                        </div>
                        <a href="{{ siteUrl('page/43/' . urlTitle('Telefono e posta elettronica')) }}" class="pulsante-freccia" aria-label="Vai alla pagina di contatto">Vai alla pagina</a>
                    </div>
                </div>
                <div class="swiper-slide">
                    <div class="box" id="card-box-acc-civico-mob">
                        <div class="wrapper-contenuto">
                            <div class="titolo">
                                <span class="icona fas fa-info-circle"></span>
                                <h3><a href="{{ siteUrl('page/175/' . urlTitle('Accesso civico')) }}" title="Vai alla pagina Accesso civico">Accesso civico</a></h3>
                            </div>
                            <div class="contenuto">Informazioni sull’accesso civico</div>
                        </div>
                        <a href="{{ siteUrl('page/175/' . urlTitle('Accesso civico')) }}" class="pulsante-freccia" aria-label="Vai alla pagina Accesso civico">Vai alla pagina</a>
                    </div>
                </div>
                <!-- La card dell'albo pretorio la mostro solo se è presente il link dell'albo nelle informazioni dell'ente -->
                @if(!empty($_institution_info['bulletin_board_url']))
                    <div class="swiper-slide">
                        <div class="box" id="card-box-albo-mob">
                            <div class="wrapper-contenuto">
                                <div class="titolo">
                                    <span class="icona fas fa-folder-open"></span>
                                    <h3><a href="{{ $_institution_info['bulletin_board_url'] }}" title="Vai alla pagina Albo pretorio">{{ !empty($_institution_info['bulletin_board_url_name']) ? $_institution_info['bulletin_board_url_name'] : 'ALbo pretorio' }}</a></h3>
                                </div>
                                <div class="contenuto">Atti in pubblicazione</div>
                            </div>
                            <a href="{{ $_institution_info['bulletin_board_url'] }}" class="pulsante-freccia" aria-label="Vai alla pagina Albo pretorio">Vai alla pagina</a>
                        </div>
                    </div>
                @endif
            </div>
            <div class="swiper-button-next slider-cards-next"></div>
            <div class="swiper-button-prev slider-cards-prev"></div>
        </div>
    </div>
</nav>
