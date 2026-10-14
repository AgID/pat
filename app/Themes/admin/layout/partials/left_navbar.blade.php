<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') or exit('No direct script access allowed'); ?>
<aside class="main-sidebar sidebar-dark-primary elevation-4">
    <!-- Brand Logo -->
    <span class="brand-link navbar-light">
        <img src="{{ baseUrl('assets/admin/img/pat_logo_nero_small.png') }}"
             alt="<?php echo config('pat_os_title', null, 'custom'); ?>"
             class="brand-image" id="header_logo_patos"
             style="opacity: .8">
        <span class="brand-text">&nbsp;</span>
    </span>

    @php
        $identity = authPatOs()->getIdentity();
    @endphp

    <div class="sidebar">
        <div class="user-panel mt-3 pb-3 mb-3">
            <div class="d-flex align-items-center mb-4">
                <div class="image">
                    @php
                        $logo = patOsInstituteInfo(['simple_logo_file'])['simple_logo_file'] ?? null;
                        $shotInstitutionName = patOsInstituteInfo(['short_institution_name'])['short_institution_name'];
                        $fullInstitutionName = patOsInstituteInfo(['full_name_institution'])['full_name_institution'];
                        if (!empty($logo) && file_exists('media/' . $shotInstitutionName . '/assets/images/' . $logo)) {
                        $src = baseUrl('media/' . $shotInstitutionName . '/assets/images/' . $logo);
                        } else {
                        $src = '';
                        }
                    @endphp
                    <img src="{{ $src }}" class="img-circle elevation-2" alt="logo dell'ente">
                </div>
                <div class="info">
                    {{$fullInstitutionName}}
                </div>
            </div>
            <div style="margin-left: 13px;">
                <a href="{{ siteUrl('admin/profile/') }}" class="d-block">
                    <div class="info" style="padding: 0px 5px 0px 0px; color: #e1e1e1;">
                        <i class="fa-solid fa-user mr-2"></i>{{ checkDecrypt($identity['name']) }}

                    </div>
                </a>
            </div>
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
                           class="nav-link {{ $section['html_class'] ?? ''}}">
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

                                    @php
                                        $flag_i_s_s_a = false;

                                        $flag_s_t_a_n = true;
                                        $children = $children ?? null; $flag_s_t_a_n = $flag_s_t_a_n ?? null;

                                        $flag_i_rg_a_s_n = true;
                                        $children = $children ?? null; $flag_i_rg_a_s_n = $flag_i_rg_a_s_n ?? null;

                                        $isResponsibleSection = $children['id'] == 52;

                                        $responsible = patOsInstituteInfo(['trasp_responsible_user_id']);
                                        $responsible = $responsible['trasp_responsible_user_id'] ?? null;

                                        $canShowSection = (!$isResponsibleSection || $identity['id'] == $responsible || $flag_i_s_s_a || $identity['technical_user'])
                                        && $flag_s_t_a_n
                                        && $flag_i_rg_a_s_n ;
                                    @endphp

                                    @if($canShowSection)
                                        <li class="nav-item">
                                            <a href="{{ siteUrl( removeDotHtml($children['url'])) }}"
                                               title="{{ $children['name'] }}"
                                               class="nav-link">
                                                <i class="fa-regular fa-circle"></i>

                                                <p>{{ $children['name'] }}</p>
                                            </a>
                                        </li>
                                    @endif

                                @endforeach
                            </ul>
                        @endif
                    </li>
                @endforeach


            </ul>
        </nav>
    </div>
</aside>
