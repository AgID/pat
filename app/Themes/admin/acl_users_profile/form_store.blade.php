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

{{ form_open($formAction,$formSettings) }}
<div class="row">
    <div class="col-xl-12">
        <div class="card mb-4" id="card-filter">
            <div class="card-header">
                <h3 class="card-title">
                    <i class="fas fa-user-shield mr-2"></i>
                    @if($_storageType === 'insert')
                        Aggiunta nuovo Profilo ACL
                    @else
                        Modifica Profilo ACL
                    @endif
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
                        <div class="form-group">
                            <label for="title">Nome da assegnare al ruolo</label>
                            {{ form_input([
                                'placeholder' => 'Nome da assegnare al ruolo',
                                'name' => 'title',
                                'value' => !empty($title) ? (($_storageType === 'clone') ? 'Copia di ' . $title : $title) : null,
                                'id' => 'input_title',
                                'class' => 'form-control input_title',
                            ]) }}
                        </div>
                    </div>

                    <div class="col-md-12">
                        <div class="form-group">
                            <label for="description">Descrizione</label>
                            {{ form_textarea([
                                'placeholder' => 'Descrizione del ruolo ACL',
                                'name' => 'description',
                                'value' => !empty($description) ? @$description : null,
                                'id' => 'input_description',
                                'class' => 'form-control input_description',
                                'cols' => '24',
                                'rows' => '5',
                            ]) }}
                        </div>
                    </div>

                    <div class="col-md-12">
                        <hr/>
                    </div>
                </div>

                {{-- Sezioni BackOffice --}}
                @if(!empty($sectionBackOffice))
                    <div class="row">
                        <div class="mt-2 mb-2 col-md-12">
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
                        @if($section['hidden_profile_acl']===0)
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
                                @php
                                    $allowed = true;
                                @endphp
                                <div class="row mt-2 mb-3 tbl_section_bo" id="tbl_{{ $i }}">
                                    <div class="col-md-12 table-responsive">
                                        <table class="table table-striped">
                                            <thead class="bg-table-accessibility">

                                            <tr>
                                                <th style="width: 22%;">
                                                    {{ mb_strtoupper(__('label_section_name',null,'acl_profiles'), CHARSET) }}
                                                </th>

                                                <th class="text-center" style="width: 13%;">
                                                <span class="ttip" data-toggle="tooltip" data-placement="top"
                                                      title="{{ __('input_add',null,'acl_profiles') }}">
                                                    @if($allowed)
                                                        <input name="input_add" value="{{ $i }}" type="checkbox"
                                                               class="input_add" id="{{ $i }}">
                                                    @endif
                                                    {{ mb_strtoupper(__('label_add',null,'acl_profiles'), CHARSET) }}
                                                        <i class="fas fa-question-circle"></i>
                                                </span>
                                                </th>

                                                <th class="text-center" style="width: 13%;">
                                                <span class="ttip" data-toggle="tooltip"
                                                      data-placement="top"
                                                      title="{{ __('input_read',null,'acl_profiles') }}">
                                                    @if($allowed)
                                                        <input name="input_read" value="{{ $i }}" type="checkbox"
                                                               class="input_read" id="{{ $i }}">
                                                    @endif
                                                    {{ mb_strtoupper(__('label_read',null,'acl_profiles'), CHARSET) }}
                                                     <i class="fas fa-question-circle"></i>
                                                </span>
                                                </th>

                                                <th class="text-center" style="width: 13%;">
                                                <span class="ttip" data-toggle="tooltip" data-placement="top"
                                                      title="{{ __('input_edit',null,'acl_profiles') }}">
                                                    @if($allowed)
                                                        <input name="input_edit" value="{{ $i }}" type="checkbox"
                                                               class="input_edit" id="{{ $i }}">
                                                    @endif
                                                    {{ mb_strtoupper(__('label_modify',null,'acl_profiles'), CHARSET) }}
                                                    <i class="fas fa-question-circle"></i>
                                                </span>
                                                </th>

                                                <th class="text-center" style="width: 13%;">
                                                <span class="ttip" data-toggle="tooltip" data-placement="top"
                                                      title="{{ __('input_delete',null,'acl_profiles') }}">
                                                    @if($allowed)
                                                        <input name="input_delete" value="{{ $i }}" type="checkbox"
                                                               class="input_delete" id="{{ $i }}">
                                                    @endif
                                                    {{ mb_strtoupper(__('label_delete',null,'acl_profiles'), CHARSET) }}
                                                    <i class="fas fa-question-circle"></i>
                                                </span>
                                                </th>
                                            </tr>
                                            </thead>
                                            <tbody>
                                            @foreach($section['children'] AS $children)
                                                @if($children['hidden_profile_acl']===0)
                                                    @php
                                                        $item = [];
                                                        $store = null;
                                                        $read = null;
                                                        $update = null;
                                                        $delete = null;

                                                        if (!empty($permits) && !empty($_storageType) && ($_storageType==='clone' || $_storageType==='edit')) {

                                                            $item = multiSearch($permits, ['sections_bo_id' => $children['id']]);

                                                            if (!empty($item)) {

                                                                $item = $item[array_keys($item)[0]];
                                                                $store = ($item['create'] === 1) ? 'checked="checked"' : null;
                                                                $read = ($item['read'] === 1) ? 'checked="checked"' : null;
                                                                $update = ($item['update'] === 1) ? 'checked="checked"' : null;
                                                                $delete = ($item['delete'] === 1) ? 'checked="checked"' : null;

                                                            }

                                                        }
                                                    @endphp
                                                    <tr>
                                                        @php
                                                            $allowedSection = true;
                                                        @endphp

                                                        {{-- Nome sesione --}}
                                                        <td>
                                                            {{ $children['name'] }}
                                                            @if($allowedSection)
                                                                <input type="hidden"
                                                                       name="acl[{{ $children['id'] }}][name]"
                                                                       value="{{ $children['name'] }}" type="checkbox"
                                                                       class="column_name_{{ $i }}">
                                                            @endif
                                                        </td>

                                                        {{-- Aggiungi --}}
                                                        <td class="text-center">
                                                            @if($allowedSection)
                                                                <input name="acl[{{ $children['id'] }}][add]" value="1"
                                                                       type="checkbox" {{ $store }}
                                                                       class="column_add_{{ $i }}">
                                                            @else
                                                                {{ badgeAclUserProfile($store) }}
                                                            @endif
                                                        </td>

                                                        {{-- Leggi --}}
                                                        <td class="text-center">
                                                            @if($allowedSection)
                                                                <input name="acl[{{ $children['id'] }}][read]" value="1"
                                                                       type="checkbox" {{ $read }}
                                                                       class="column_read_{{ $i }}">
                                                            @else
                                                                {{ badgeAclUserProfile($read) }}
                                                            @endif
                                                        </td>

                                                        {{-- Modifica --}}
                                                        <td class="text-center">
                                                            @if($allowedSection)
                                                                <input name="acl[{{ $children['id'] }}][modify]"
                                                                       value="1"
                                                                       type="checkbox" {{ $update }}
                                                                       class="column_modify_{{ $i }}">
                                                            @else
                                                                {{ badgeAclUserProfile($update) }}
                                                            @endif
                                                        </td>

                                                        {{-- Elimina --}}
                                                        <td class="text-center">
                                                            @if($allowedSection)
                                                                <input name="acl[{{ $children['id'] }}][delete]"
                                                                       value="1"
                                                                       type="checkbox" {{ $delete }}
                                                                       class="column_delete_{{ $i }}">
                                                            @else
                                                                {{ badgeAclUserProfile($delete) }}
                                                            @endif
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
                                <i class="fas fa-plus"></i> &nbsp; Espandi tutto
                            </button>
                            <button type="button" class="btn btn-outline-primary" id="collapser">
                                <i class="fas fa-minus"></i> &nbsp; Comprimi tutto
                            </button>

                            <table id="basic" class="mt-3 table table-bordered simple-tree-table">
                                <thead class="bg-table-accessibility">
                                <tr>
                                    <th style="width: 80%">
                                        {{ mb_strtoupper(__('label_front_office_section',null,'acl_profiles'), CHARSET) }}
                                    </th>
                                    <th style="width: 20%" class="text-center">
                                        <input name="all_sbo" value="5" type="checkbox" class="all_sbo">
                                        &nbsp;
                                        {{ mb_strtoupper(__('label_front_office_action',null,'acl_profiles'), CHARSET) }}
                                    </th>
                                </tr>
                                </thead>
                                <tbody>

                                {{-- Funzione ricorsiva per creare le righe delle sezioni --}}
                                {{ treeTableACL($sectionFrontOffice,!empty($permits) ? $permits : null) }}
                                </tbody>
                            </table>
                        </div>
                    </div>
                @endif

            </div>

            <div class="card-footer">
                {{-- Funzione che henera il pulsante formtatto --}}
                {{ btnSave() }}
            </div>
        </div>
    </div>
</div>

{{ form_input([
    'type' => 'hidden',
    'name' => '_storage_type',
    'value' => $_storageType,
    'id' => '_storage_type',
    'class' => '_storage_type',
]) }}



@if(!empty($id))
    {{-- Identifica se la sezione in questione è in modifica o inserimento dati --}}
    {{ form_input([
        'type' => 'hidden',
        'name' => 'id',
        'value' => $id,
        'id' => 'id',
        'class' => 'id',
    ]) }}

    {{-- ID Pat OS --}}
    {{ form_input([
        'type' => 'hidden',
        'name' => 'institute_id',
        'value' => PatOsInstituteId(),
        'id' => 'institute_id',
        'class' => 'institute_id',
    ]) }}
@endif

{{ form_close() }}

{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
{{ js('tree-table/jquery-simple-tree-table.js','common') }}

<script type="text/javascript">
    //Previene il salvataggio quando si preme invio e il focus non è sul pulsante di salvataggio
    $('#{{ $formSettings['id'] }}').on('keyup keypress', function (e) {
        var keyCode = e.keyCode || e.which;
        if (keyCode === 13 && e.target.tagName != 'TEXTAREA') {
            e.preventDefault();
            return false;
        }
    });

    function setChecked() {
        var args = arguments;
        var selector = args[0];
        var mode = args[1];

        if (mode) {
            selector.prop('checked', true);
        } else {
            selector.prop('checked', false);
        }
    }

    $(document).ready(function () {

        let formModified = false;

        {{-- Btn send Ajax--}}
        var btnSend = $('#btn_save');
        $('#{{ $formSettings['id'] }}').ajaxForm({
            method: 'POST',
            resetForm: true,

            beforeSend: function () {
                btnSend.empty().append('{{ __('brn_save_spinner',null,'patos') }}').attr("disabled", true);
                $('.error-toast').remove();
            },
            success: function (data) {
                btnSend.empty().append('{{ __('brn_save',null,'patos') }}');
                var response = parseJson(data);
                formModified = false;

                // Funzione che genera il toast con il messaggio di successo
                {{-- (vedere nel footer) --}}
                createValidatorFormSuccessToast(response.data.message, 'PROFILO ACL');

                setTimeout(function () {
                    window.location.href = '{{ siteUrl('admin/acl-users-profile') }}';
                }, 800);

            },
            complete: function (xhr) {
                btnSend.empty().append('{{ __('brn_save',null,'patos') }}');
            },
            error: function (jqXHR, status) {
                var response = parseJson(jqXHR.responseText);
                $(document).Toasts('create', {
                    class: 'bg-danger error-toast',
                    title: 'ATTENZIONE',
                    subtitle: 'Validatore modulo',
                    autohide: true,
                    delay: 3000,
                    body: response.errors.error
                });

                btnSend.empty().append('{{ __('brn_save',null,'patos') }}').attr("disabled", false);

            }
        });

        {{-- Tree Table --}}
        $('#basic').simpleTreeTable({
            collapsed: true,
            opened: false,
            expander: $('#expander'),
            collapser: $('#collapser'),
            store: 'session',
            storeKey: 'simple-tree-table-basic',

        });

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

        {{-- Click add --}}
        $('.input_add').bind('click', function (e) {
            var id = $(this).val();
            var subSelector = $('.column_add_' + id);
            var mode = $(this).is(':checked') ? true : false;
            setChecked(subSelector, mode);
        });

        {{-- Click read --}}
        $('.input_read').bind('click', function (e) {
            var id = $(this).val();
            var subSelector = $('.column_read_' + id);
            var mode = $(this).is(':checked') ? true : false;
            setChecked(subSelector, mode);
        });

        {{-- Click edit --}}
        $('.input_edit').bind('click', function (e) {
            var id = $(this).val();
            var subSelector = $('.column_modify_' + id);
            var mode = $(this).is(':checked') ? true : false;
            setChecked(subSelector, mode);
        });

        {{-- Click delete --}}
        $('.input_delete').bind('click', function (e) {
            var id = $(this).val();
            var subSelector = $('.column_delete_' + id);
            var mode = $(this).is(':checked') ? true : false;
            setChecked(subSelector, mode);
        });

        {{-- Click advacente --}}
        $('.input_advs').bind('click', function (e) {
            var id = $(this).val();
            var subSelector = $('.column_adv_' + id);
            var mode = $(this).is(':checked') ? true : false;
            setChecked(subSelector, mode);
        });

        {{-- Click App IO --}}
        $('.input_app_io').bind('click', function (e) {
            var id = $(this).val();
            var subSelector = $('.column_appio_' + id);
            var mode = $(this).is(':checked') ? true : false;
            setChecked(subSelector, mode);
        });

        {{-- Auto Close navbar : .1 second--}}
        setTimeout(function () {
            $('body').addClass('sidebar-collapse');
        }, 100);

        {{-- Controllo per l'uscita dal form se i campi di input sono stati toccati --}}
        $(document).on('focus', 'input, textarea', function (e) {
            formModified = true;
        });

        {{-- Messaggio di uscita senza salvare dal form --}}
        window.addEventListener('beforeunload', (event) => {
            if (formModified) {
                event.returnValue = 'Vuoi uscire dalla pagina?';
            }
        });
    })
</script>
{% endblock %}
