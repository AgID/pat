<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
{{-- Header --}}
@php
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
                                <a class="btn btn-primary btn-icon btn-full" href="{{ siteUrl('/auth') }}"
                                   title="Accedi all'area personale">
                                    <span class="fas fa-user-circle mr-2"></span>
                                    <span class="d-none d-lg-block">&nbsp; Accedi all'area personale</span>
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
        <div class="container">
            <div class="row">
                <div class="col-12">
                    <div class="it-header-center-content-wrapper">
                        <div class="it-brand-wrapper">
                            <a href={{ siteUrl() }}>
                                <img class="logo" {{xss: getInstitutionLogo() }}>
                                <div class="it-brand-text">
                                    <@php echo uri()->segment(1, 0) == 0 ? 'h1' : 'p' @endphp class
                                    ="titolo-sito">
                                    {{e: $_institution_info['full_name_institution'] }}
                                    <@php echo uri()->segment(1, 0) == 0 ? '/h1' : '/p' @endphp>
                                    @if(empty($register))
                                        <span class="d-none d-md-block sottotitolo-sito">
                                        @php
                                            $data['title'] = 'Amministrazione trasparente';
                                        @endphp
                                            {{ $data['title'] }}
                                    </span>
                                    @endif
                                </div>
                            </a>
                        </div>

                        <div class="it-right-zone">
                            <div class="it-search-wrapper">
                                <span class="d-none d-md-block">Cerca</span>
                                <button id="modalRicercaBtn" type="button" aria-label="Cerca" class="pulsante-ricerca"
                                        data-bs-toggle="modal" data-bs-target="#modalRicerca">
                                    <span class="fas fa-search"></span>
                                </button>
                            </div>
                        </div>
                    </div>
                </div>
            </div>
        </div>
    </div>
</header>


<header id="header-compact">
    <div class="container">
        <div class="wrapper">
            <div class="c-1">
                <div class="box-logo">
                    <a href={{ siteUrl() }}>
                        <img class="logo" {{xss: getInstitutionLogo() }}>
                    </a>
                </div>
            </div>
            <div class="c-2">
                <div class="it-search-wrapper">
                    <button id="modalRicercaBtn" type="button" aria-label="Cerca" class="pulsante-ricerca"
                            data-bs-toggle="modal" data-bs-target="#modalRicerca">
                        <span class="fas fa-search"></span>
                    </button>
                </div>
            </div>
        </div>
    </div>
    </div>
</header>
