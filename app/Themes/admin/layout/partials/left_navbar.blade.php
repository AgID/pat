<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <span class="brand-link navbar-light">
        <img src="{{ baseUrl('assets/admin/img/pat_logo_nero_small.png') }}"
             alt="<?php echo config('pat_os_title',null,'custom'); ?>"
             class="brand-image" id="header_logo_patos"
             style="opacity: .8">
        <span class="brand-text">&nbsp;</span>
    </span>

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3 d-flex">
            <a href="{{ siteUrl('admin/profile/') }}" class="d-block">
                <div class="image">
                    <img src="{{ avatar() }}" class="img-circle elevation-2" alt="Avatar">
                </div>
                <div class="info" style="padding: 0px 5px 0px 10px; ">
                    {{ checkDecrypt(getIdentity('username')) }}
                </div>
            </a>
        </div>

        {{-- Navbar sinistro --}}
        <nav class="mt-2 text-sm">
            <ul class="nav nav-pills nav-sidebar flex-column sidebar-menu" data-widget="treeview" role="menu"
                data-accordion="false" style="overflow: hidden;">

                <li class="nav-item">
                    <a href="{{ siteUrl('admin/dashboard') }}" class="nav-link">
                        <i class="fas fa-tachometer-alt"></i>
                        <p>
                            DASHBOARD
                        </p>
                    </a>
                </li>

                @foreach(getSectionPagesBackOffice() AS $section)
                    <li class="nav-item">
                        <a href="{{ siteUrl( removeDotHtml($section['url'])) }}" title="{{ $section['name'] }}"
                           class="nav-link">
                            {{ $section['icon'] }}
                            <p>
                                {{ $section['name'] }}

                                @if(!empty($section['children']))
                                    <i class="fas fa-angle-left right"></i>
                                @endif
                            </p>
                        </a>
                        @if(!empty($section['children']))
                            <ul class="nav nav-treeview">
                                @foreach($section['children'] AS $children)
                                    <li class="nav-item">
                                        <a href="{{ siteUrl( removeDotHtml($children['url'])) }}"
                                           title="{{ $children['name'] }}"
                                           class="nav-link">
                                            <i class="far fa-circle fa-md"></i>

                                            <p>{{ $children['name'] }}</p>
                                        </a>
                                    </li>
                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach
            </ul>
        </nav>
    </div>
</aside>