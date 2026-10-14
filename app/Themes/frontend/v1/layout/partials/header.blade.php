<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
{{-- Header --}}
@php
    $customLinks = getCustomLinks();
    $identity = authPatOs()->getIdentity();
@endphp
<header id="header-full">
    <div class="it-header-slim-wrapper">
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="it-header-slim-wrapper-content">
                        @if(!empty($_institution_info['top_level_institution_url']))
                            <a class="d-lg-block navbar-brand"
                               href="{{xss: $_institution_info['top_level_institution_url'] }}">
                                {{e: $_institution_info['top_level_institution_name'] }}
                            </a>
                        @else
                            <div></div>
                        @endif
                        <div class="header-slim-right-zone">

                            @if(isAuth()==null)
                                @php
                                    $testoBottoneAccedi = "Accedi all'area personale";
                                    $testoBottoneAccedi = $testoBottoneAccedi ?? null;
                                @endphp
                                <a class="btn btn-primary btn-icon btn-full" href="{{ siteUrl('/auth') }}"
                                   title="{{ $testoBottoneAccedi }}">
                                    <span class="fas fa-user-circle mr-2"></span>
                                    <span class="d-none d-lg-block">&nbsp; {{ $testoBottoneAccedi }}</span>
                                </a>
                            @else
                                <a class="btn-icon mr-lg-3 mr-sm-0" href="{{ siteUrl('admin/dashboard') }}"
                                   title="Amministrazione">
                                    <small class="header-profile">
                                        <i class="fas fa-tachometer-alt"></i>
                                        Ciao {{xss: checkDecrypt($identity['name']) }}, entra in Amministrazione
                                    </small>
                                </a>

                                <a class="btn-icon mr-lg-3 mr-sm-0" href="{{ siteUrl('admin/profile') }}"
                                   title="Profilo">
                                    <small class="header-profile">
                                        <i class="far fa-user-circle"></i> Profilo
                                    </small>
                                </a>
                                <a class="btn-icon" href="{{ siteUrl('logout') }}" title="Disconnetti">
                                    <small class="header-profile">
                                        <i class="fas fa-sign-out-alt"></i> Disconnetti
                                    </small>
                                </a>
                            @endif
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    <div class="it-header-center-wrapper position-relative">

        @if(\System\Registry::get('show_burger_menu')===true && empty($register))
            <!-- NAVIGAZIONE POPUP -->
            <div class="hamburger">
                <button role="navigation" aria-controls="menu-popup" title="Apri il menu" class="menuButton btn-hamburger"><i class="fas fa-bars"></i></button>
            </div>
            <!-- fine NAVIGAZIONE POPUP -->
        @endif

        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="it-header-center-content-wrapper">
                        <div class="it-brand-wrapper">
                            <a href="@php echo !empty($_institution_info['link_site_home']) ? $_institution_info['link_site_home'] : siteUrl(); @endphp">

                                @if((!empty($_institution_info['simple_logo_file']) && file_exists('media/' . $_institution_info['short_institution_name'] . '/assets/images/' . $_institution_info['simple_logo_file'])))
                                <img class="logo" {{xss: getInstitutionLogo() }}>
                                @endif
                                <div class="it-brand-text">
                                    <@php echo uri()->segment(1, 0) == 0 ? 'h1' : 'span' @endphp class
                                    ="titolo-sito">
                                    {{e: $_institution_info['full_name_institution'] }}
                                    <@php echo uri()->segment(1, 0) == 0 ? '/h1' : '/span' @endphp>
                                    @if(empty($register))
                                        <span class="d-none d-md-block sottotitolo-sito">
                                        @php
                                            $data['title'] = 'Amministrazione trasparente';
                                            $data = $data ?? null;
                                        @endphp
                                            {{ $data['title'] }}
                                    </span>
                                    @endif
                                </div>
                            </a>
                        </div>
                        @php
                            $getSocialLinks = getSocialLinks();
                        @endphp
                        <div class="it-right-zone">
                            <div class="social-and-search">
                                @if($getSocialLinks!=null)
                                    <div class="it-socials d-none d-md-flex">
                                        <span>Seguici su</span>
                                        <ul>
                                            @foreach($getSocialLinks AS $item)
                                                <li>
                                                    <a title="{{escape_xss: $item['name'] }} (Apre una nuova scheda)"
                                                       href="{{xss: $item['url'] }}" target="_blank">
                                                        {{xss: $item['icon'] }}
                                                    </a>
                                                </li>
                                            @endforeach
                                        </ul>
                                    </div>
                                @endif
                                @if(empty($register))
                                    <div class="it-search-wrapper">
                                        <span class="d-none d-md-block">Cerca</span>
                                        <button id="modalRicercaBtn" type="button" aria-label="Cerca" class="pulsante-ricerca" data-bs-toggle="modal" data-bs-target="#modalRicerca">
                                            <span class="fas fa-search"></span>
                                        </button>
                                    </div>
                                @endif
                            </div>
                            <div>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
    @if(!empty($customLinks))
        <div class="it-header-navbar-wrapper">
            <div class="container">
                <div class="row">
                    <div class="col-12">
                        <!--start nav-->
                        <nav class="navbar navbar-expand-lg has-megamenu">
                            <div class="navbar-collapsable" id="nav1" style="display: none;">
                                <div class="overlay" style="display: none;"></div>
                                <div class="close-div sr-only">
                                    <button class="btn close-menu" type="button"><span
                                                class="it-close"></span>close
                                    </button>
                                </div>
                                <div class="menu-wrapper">
                                    <ul class="navbar-nav">
                                        {{ $customLinks }}
                                    </ul>
                                </div>
                            </div>
                        </nav>
                    </div>
                </div>
            </div>
        </div>
    @endif
</header>

<style>
    #header-compact .it-brand-text h1, #header-compact .it-brand-text span {
        font-size:1.5rem;
        margin:0;
        font-weight: bold;
    }
</style>

@if(\System\Registry::get('show_burger_menu')===true)
    <header id="header-compact">
        @if(empty($register))
            <div class="hamburger">
                <button role="navigation" aria-controls="menu-popup" title="Apri il menu" class="menuButton btn-hamburger"><i class="fas fa-bars"></i></button>
            </div>
        @endif

        <div class="container">
            <div class="wrapper">
                <div class="c-1">
                    <div class="box-logo">
                        <a href="@php echo !empty($_institution_info['link_site_home']) ? $_institution_info['link_site_home'] : siteUrl(); @endphp">
                            <img class="logo" {{xss: getInstitutionLogo() }}>
                        </a>
                    </div>
                    <div class="it-brand-text">
                        <a href="@php echo !empty($_institution_info['link_site_home']) ? $_institution_info['link_site_home'] : siteUrl(); @endphp">
                            <@php echo uri()->segment(1, 0) == 0 ? 'h1' : 'span' @endphp>
                            {{e: $_institution_info['full_name_institution'] }}
                            <@php echo uri()->segment(1, 0) == 0 ? '/h1' : '/span' @endphp>
                        </a>
                    </div>

                </div>
                @if(empty($register))
                    <div class="c-2">
                        <div class="it-search-wrapper">
                            <button id="modalRicercaCompactBtn" type="button" aria-label="Cerca" class="pulsante-ricerca" data-bs-toggle="modal" data-bs-target="#modalRicerca">
                                <span class="fas fa-search"></span>
                            </button>
                        </div>
                    </div>
                @endif
            </div>
        </div>
    </header>

    @if(empty($register))
        <!-- Menu Popup -->
        <nav id="menu-popup" aria-label="primario" class="menu-popup" aria-expanded="false">
            <h2 id="menuTitle" class="sr-only">
                Menu di navigazione
            </h2>
            <div id="barraMenu" class="menu">
                <div class="header-menu">
                    <button id="closeMenuButton" class="chiudi" aria-label="Chiudi il menu"><em class="fas fa-times"></em></button>
                </div>
                {{-- Menu --}}
                {{ burgerMenuHtml() }}
            </div>
            <div id="overlayMenu" class="overlay"></div>
        </nav>
        <!-- fine Menu Popup -->
    @endif
@endif
