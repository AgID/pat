<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

<style>
    .econnect-icon a {
        display: flex;
        align-items: center;
        gap: .2rem;
    }
    .econnect-icon img {
        height:1rem;
        width:auto;
    }
</style>

@php
    $identity = authPatOs()->getIdentity();
@endphp

<nav class="main-header navbar navbar-expand navbar-white navbar-light">
    <ul class="navbar-nav">
        <li class="nav-item">
            <a class="nav-link" id="draw_left_menu_patos"
               data-widget="pushmenu" href="#" role="button"><i class="fas fa-bars"></i></a>
        </li>
        <li class="nav-item d-none d-sm-inline-block header-nav-item">
            <a href="{{ siteUrl('admin/dashboard') }}" class="nav-link" title="Dashboard" aria-label="Dashboard">
                <i class="fas fa-tachometer-alt"></i><span class="d-none d-xl-inline ml-1 header-nav-label">Dashboard</span>
            </a>
        </li>




        <li class="nav-item d-none d-sm-inline-block header-nav-item">
            <a href="{{ baseUrl() }}" target="_blank" class="nav-link" title="Front office" aria-label="Front office">
                <i class="fas fa-globe"></i><span class="d-none d-xl-inline ml-1 header-nav-label">Front office</span>
            </a>
        </li>

        {{-- Inclusione di librerie esterne --}}



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
                            'value' => System\Input::get('s'),
                            'required' => 'required'
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

        {{-- Inclusione di librerie esterne --}}

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
                        {{ checkDecrypt($identity['name']) }}
                    </p>
                    <p class="mt-0">
                    {{ checkDecrypt($identity['email']) }}

                    <div>
                        <small>Accesso {{ $identity['options']['last_date_access'] }} alle
                            ore {{ $identity['options']['last_hour_access'] }} </small>
                        <br/>
                    </div>
                    </p>
                </li>
                <li class="user-footer">

                    <a href="{{ siteUrl('admin/profile/') }}" class="btn btn-default btn-flat">
                        <i class="fas fa-user-circle"></i>
                        Profilo
                    </a>

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
