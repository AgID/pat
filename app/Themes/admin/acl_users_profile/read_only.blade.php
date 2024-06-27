{{--  Template storage users profile --}}
<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
{% extends layout/master %}
{{--  ************************************************ CONTENT ************************************************ --}}
{% block content %}

<div class="row">
    <div class="col-xl-12">
        <div class="card mb-4" id="card-filter">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-shield mr-2"></i> Visualizzazione Profilo ACL
                </h3>

                <div class="card-tools">
                    <ul class="nav nav-pills ml-auto">
                        <li class="nav-item">
                            <a href="{{ siteUrl('admin/acl-users-profile') }}" title="Torna indietro"
                               class="btn btn-default btn-sm btn-outline-primary">
                                <i class="fas fa-caret-left"></i> Torna a elenco profili
                            </a>
                        </li>
                    </ul>
                </div>
            </div>

            <div class="card-body card-primary">
                <div class="row">
                    <div class="col-md-12">
                        <p class="heading-form-title"><i class="fas fa-sliders-h"></i> {{ nbs(1) }} Dati generali</p>
                    </div>
                    <div class="col-md-12">
                        <h1>{{$title}}</h1>
                    </div>

                    <div class="col-md-12">
                        <p>{{ $description }}</p>
                    </div>

                    <div class="col-md-12">
                        <hr/>
                    </div>

                    <div class="col-sm-12">
                        <div class="form-group">
                            <label for="lock_user">Permesso delle operazioni di blocco/sblocco utenti</label>
                            <div>
                                @if($lock_user === 1)
                                    <small class="badge badge-success" style="font-size: 16px;" data-toggle="tooltip"
                                           data-placement="top" data-original-title="Permesso attivo">Si</small>
                                @else
                                    <small class="badge badge-danger" style="font-size: 16px;" data-toggle="tooltip"
                                           data-placement="top" data-original-title="Permesso non attivo">No</small>
                                @endif
                            </div>
                        </div>
                    </div>
                </div>

                {{-- Sezioni BackOffice --}}
                @if(!empty($sectionBackOffice))
                    <div class="row">
                        <div class="mt-2 mb-2 col-md-12 text-right">
                            <button type="button" id="btn_action_toggle_all" data-mode="expand"
                                    class="btn btn-outline-primary">
                                <i class="fas fa-minus"></i> {{ nbs(1) }} {{ __('label_comprime',null,'acl_profiles') }}
                            </button>
                        </div>

                    </div>

                    @php
                        $i = 0;
                    @endphp
                    @foreach($sectionBackOffice AS $section)
                        @if($section['hidden_profile_acl']==0)
                            <div class="row">
                                <div class="col-md-12">
                                    <p class="heading-form-title">
                                        {{ $section['icon'] }} {{ nbs(1) }} {{ $section['name'] }}
                                        &nbsp
                                        <button type="button" id="btn_action_toggle_{{ $i }}"
                                                class="exp_row btn btn-outline-primary btn-xs" data-id="{{ $i }}"
                                                data-toggle="tooltip" data-placement="top"
                                                title="{{ __('label_comprime',null,'acl_profiles') }}"
                                                aria-label="{{ __('label_comprime',null,'acl_profiles') }}">
                                            <i class="fas fa-minus"></i>
                                        </button>
                                    </p>
                                </div>
                            </div>

                            @if(!empty($section['children']))
                                <div class="row mt-2 mb-3 tbl_section_bo" id="tbl_{{ $i }}">
                                    <div class="col-md-12 table-responsive">
                                        <table class="table table-striped">
                                            <thead class="bg-table-accessibility">
                                            <tr>
                                                <th>
                                                    {{ mb_strtoupper(__('label_section_name',null,'acl_profiles'), CHARSET) }}
                                                </th>
                                                <th class="text-center">
                                                <span class="ttip" data-toggle="tooltip" data-placement="top"
                                                      title="{{ __('input_add',null,'acl_profiles') }}">
                                                    &nbsp;
                                                    {{ mb_strtoupper(__('label_add',null,'acl_profiles'), CHARSET) }}
                                                        <i class="fas fa-question-circle"></i>
                                                </span>
                                                </th>
                                                <th class="text-center">
                                                <span class="ttip" data-toggle="tooltip"
                                                      data-placement="top"
                                                      title="{{ __('input_read',null,'acl_profiles') }}">
                                                    &nbsp;
                                                    {{ mb_strtoupper(__('label_read',null,'acl_profiles'), CHARSET) }}
                                                     <i class="fas fa-question-circle"></i>
                                                </span>
                                                </th>
                                                <th class="text-center">
                                                <span class="ttip" data-toggle="tooltip" data-placement="top"
                                                      title="{{ __('input_edit',null,'acl_profiles') }}">
                                                    &nbsp;
                                                   {{ mb_strtoupper(__('label_modify',null,'acl_profiles'), CHARSET) }}
                                                    <i class="fas fa-question-circle"></i>
                                                </span>
                                                </th>
                                                <th class="text-center">
                                                <span class="ttip" data-toggle="tooltip" data-placement="top"
                                                      title="{{ __('input_delete',null,'acl_profiles') }}">
                                                    &nbsp;
                                                    {{ mb_strtoupper(__('label_delete',null,'acl_profiles'), CHARSET) }}
                                                    <i class="fas fa-question-circle"></i>
                                                </span>
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($section['children'] AS $children)
                                                @if($children['hidden_profile_acl']==0)
                                                    @php
                                                        $item = [];
                                                        $store = 'No';
                                                        $read = 'No';
                                                        $update = 'No';
                                                        $delete = 'No';

                                                        if (!empty($permits)) {

                                                            $item = multiSearch($permits, ['sections_bo_id' => $children['id']]);

                                                            if (!empty($item)) {

                                                                $item = $item[array_keys($item)[0]];
                                                                $store = ($item['create'] === 1) ? 'Si' : 'No';
                                                                $read = ($item['read'] === 1) ? 'Si' : 'No';
                                                                $update = ($item['update'] === 1) ? 'Si' : 'No';
                                                                $delete = ($item['delete'] === 1) ? 'Si' : 'No';
                                                            }

                                                        }
                                                    @endphp
                                                    <tr>
                                                        @php
                                                            $allowedSection = true;
                                                        @endphp

                                                        <td style="width: 30%;">
                                                            {{ $children['name'] }}
                                                        </td>
                                                        <td class="text-center">
                                                            {{ badgeAclUserProfile($store) }}
                                                        </td>
                                                        <td class="text-center">
                                                            {{ badgeAclUserProfile($read) }}
                                                        </td>
                                                        <td class="text-center">
                                                            {{ badgeAclUserProfile($update) }}
                                                        </td>
                                                        <td class="text-center">
                                                            {{ badgeAclUserProfile($delete) }}
                                                        </td>
                                                    </tr>
                                                @endif
                                            @endforeach
                                            </tbody>
                                        </table>
                                    </div>
                                </div>
                            @endif
                            @php
                                $i++;
                            @endphp
                        @endif
                    @endforeach
                @endif

                @if(!empty($sectionFrontOffice))
                    {{-- Sezioni Front Office --}}
                    <div class="row mt-4">
                        <div class="col-md-12 table-responsive">

                            <button type="button" class="btn btn-outline-primary" id="expander">
                                Espandi tutto
                            </button>
                            <button type="button" class="btn btn-outline-primary" id="collapser">
                                Comprimi tutto
                            </button>

                            <table id="basic" class="mt-3 table table-bordered simple-tree-table">
                                <thead class="bg-table-accessibility">
                                <tr>
                                    <th style="width: 80%">
                                        {{ mb_strtoupper(__('label_front_office_section',null,'acl_profiles'), CHARSET) }}
                                    </th>
                                    <th style="width: 20%" class="text-center">&nbsp;
                                        {{ mb_strtoupper(__('label_front_office_action',null,'acl_profiles'), CHARSET) }}
                                    </th>
                                </tr>
                                </thead>
                                <tbody>
                                {{-- Funzione ricorsiva per creare le righe delle sezioni --}}
                                {{ treeTableReadOnlyACL($sectionFrontOffice,!empty($permits) ? $permits : null) }}
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif
            </div>

            <div class="card-footer">
                {{-- Funzione che henera il pulsante formtatto --}}
                <a href="{{ siteUrl('admin/acl-users-profile') }}" title="Torna indietro"
                   class="btn btn-default btn-sm btn-outline-primary">
                    <i class="fas fa-caret-left"></i> Torna a elenco profili
                </a>
            </div>
        </div>
    </div>
</div>

{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
{{ js('tree-table/jquery-simple-tree-table.js','common') }}

<script type="text/javascript">
    $(document).ready(function () {

        {{-- Tree Table --}}
        $('#basic').simpleTreeTable({
            collapsed: true,
            opened: false,
            expander: $('#expander'),
            collapser: $('#collapser'),
            store: 'session',
            storeKey: 'simple-tree-table-basic',

        });

        {{-- Auto Close navbar : .1 second--}}
        setTimeout(function () {
            $('body').addClass('sidebar-collapse');
        }, 100);

        {{-- Clisk seleziona deseleziona tutto Sezioni BackOffice --}}
        $('.all_sbo').bind('click', function (e) {
            var subSelector = $('.sbo');
            var mode = $(this).is(':checked') ? true : false;
            setChecked(subSelector, mode);
        });

        {{-- Click Toogle table Sections BO --}}
        $('.exp_row').bind('click', function (e) {
            var dataId = $(this).attr('data-id');
            $('#tbl_' + dataId).slideToggle(200, function () {
                var btn = $('#btn_action_toggle_' + dataId);
                var icon = '<i class="fas fa-plus"></i>';
                var textTitleAriaLabel = '{{ __('label_expand',null,'acl_profiles') }}';
                if ($(this).is(":visible")) {
                    icon = '<i class="fas fa-minus"></i>';
                    textTitleAriaLabel = '{{ __('label_comprime',null,'acl_profiles') }}'
                }
                btn.empty().append(icon)
                    .attr('title', textTitleAriaLabel)
                    .attr('aria-label', textTitleAriaLabel)
                    .attr('data-original-title', textTitleAriaLabel);
            });
        });

        {{-- Click Toogle ALL table Sections BO --}}
        $('#btn_action_toggle_all').bind('click', function () {
            var mode = $(this).attr('data-mode');
            if (mode === 'expand') {
                $(this).attr('data-mode', 'comprime');
                var icon = '<i class="fas fa-plus"></i>';
                $('.tbl_section_bo').slideUp(400, function () {

                    $('#btn_action_toggle_all').empty().append(icon + ' {{ nbs(1) }} {{ __('label_expand',null,'acl_profiles') }}');
                    $('.exp_row').empty().append(icon)
                        .attr('title', '{{ __('label_expand',null,'acl_profiles') }}')
                        .attr('aria-label', '{{ __('label_expand',null,'acl_profiles') }}')
                        .attr('data-original-title', '{{ __('label_expand',null,'acl_profiles') }}');
                });
            } else {
                $(this).attr('data-mode', 'expand');
                var icon = '<i class="fas fa-minus"></i>';
                $('.tbl_section_bo').slideDown(400, function () {
                    $('#btn_action_toggle_all').empty().append(icon + ' {{ nbs(1) }} {{ __('label_comprime',null,'acl_profiles') }}');
                    $('.exp_row').empty().append(icon)
                        .attr('title', '{{ __('label_comprime',null,'acl_profiles') }}')
                        .attr('aria-label', '{{ __('label_comprime',null,'acl_profiles') }}')
                        .attr('data-original-title', '{{ __('label_comprime',null,'acl_profiles') }}');
                });
            }

        });
    });
</script>
{% endblock %}
