<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" id="draw_left_menu_patos"
               data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ baseUrl('admin/dashboard') }}" class="nav-link">
                <i class="fas fa-tachometer-alt"></i> Dashboard
            </a>
        </li>

        <li class="nav-item d-none d-sm-inline-block">
            <a href="{{ baseUrl() }}" target="_blank" class="nav-link">
                <i class="fas fa-globe"></i>
                Front office
            </a>
        </li>
    </ul>

    <ul class="navbar-nav ml-auto">

        {{-- Motore di ricerca globale--}}
        <li class="nav-item">
            {{ form_open('admin/search',['method'=>'GET','name'=>'global_form_search','class'=>'global_form_search','id'=>'global_form_search',]) }}
            <a class="nav-link" data-widget="navbar-search" href="#" role="button" title="Cerca">
                <i class="fas fa-search"></i>
            </a>
            <div class="navbar-search-block">
                <form class="form-inline">
                    <div class="input-group input-group-sm">
                        {{ form_input([
                            'name' => 's',
                            'class' => 'form-control form-control-navbar',
                            'placeholder' => 'Ricerca globale',
                            'aria-label' => 'Search',
                            'id' => 'global_search',
                            'value' => System\Input::get('s')
                        ]) }}
                        <div class="input-group-append">
                            <button class="btn btn-navbar" type="submit" title="Effettua ricerca">
                                <i class="fas fa-search"></i>
                            </button>
                            <button class="btn btn-navbar" type="button" data-widget="navbar-search" title="Chiudi pannello ricerca">
                                <i class="fas fa-times"></i>
                            </button>
                        </div>
                    </div>
                </form>
            </div>
            {{ form_close() }}
        </li>
        {{-- Motore di ricerca blobale --}}

        {{-- FullScreen --}}
        <li class="nav-item">
            <a class="nav-link" data-widget="fullscreen" href="#" role="button" title="Passa a schermo intero">
                <i class="fas fa-expand-arrows-alt"></i>
            </a>
        </li>

        {{-- Inizio: Layer Profilo utente --}}
        <li class="nav-item dropdown user-menu">
            <a href="#" class="nav-link dropdown-toggle" data-toggle="dropdown" title="Menu utente">
                <img src="{{ avatar() }}" class="user-image img-circle elevation-2" alt="Avatar">
                <!-- <span class="d-none d-md-inline"></span> -->
            </a>
            <ul class="dropdown-menu dropdown-menu-lg dropdown-menu-right">
                <!-- User image -->
                <li class="user-header bg-gray-dark" style="height: auto;">
                    <img src="{{ avatar() }}" class="img-circle elevation-2" alt="User Image">
                    <p>
                    {{ checkDecrypt(getIdentity('email')) }}
                    @if(isSuperAdmin(true))
                    <div>
                        <small>
                            Amministratore di Sistema
                        </small>
                    </div>
                    @endif
                    <div>
                        <small>Accesso {{ getIdentity('last_date_access') }} alle
                            ore {{ getIdentity('last_hour_access') }} </small>
                        <br/>
                    </div>
                    </p>
                </li>
                <li class="user-footer">
                    @if(!isSuperAdmin())
                    <a href="{{ siteUrl('admin/profile/') }}" class="btn btn-default btn-flat">
                        <i class="fas fa-user-circle"></i>
                        Profilo
                    </a>
                    @endif
                    <a href="{{ siteUrl('logout') }}" class="btn btn-default btn-flat float-right">
                        <i class="fas fa-sign-out-alt"></i>
                        Logout
                    </a>
                </li>
            </ul>
        </li>
        {{-- Fine: Layer Profilo utente --}}
    </ul>
</nav>
