<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

<div class="row mt-3">
    <div class="col-md-6">

        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    Gestione links testata
                </h3>
            </div>
            <div class="card-body" id="links_header"></div>
            <div class="card-footer">
                <button type="button" class="btn btn-outline-primary btn-xs open-modal-link"
                        data-type="header" data-name="Link testata">
                    <i class="fas fa-plus"></i>{{ nbs(2) }}Aggiungi
                </button>
            </div>
        </div>
    </div>

    <div class="col-md-6">
        <div class="card">
            <div class="card-header">
                <h3 class="card-title">
                    Gestione links footer
                </h3>
            </div>
            <div class="card-body" id="links_footer"></div>
            <div class="card-footer">
                <button type="button" class="btn btn-outline-primary btn-xs open-modal-link"
                        data-type="footer" data-name="Link footer">
                    <i class="fas fa-plus"></i>{{ nbs(2) }}Aggiungi
                </button>
            </div>
        </div>
    </div>
</div>


<div class="modal fade" id="modal-link-header" tabindex="-1" role="dialog" aria-labelledby="modal-link-header"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="title-action-heading-links"></h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">

                    <div id="error_custom_link" class="alert alert-danger display-hidden"></div>
                    <div id="success_custom_link" class="alert alert-success display-hidden"></div>

                    <label for="_link_name">Nome</label>
                    {{ form_input([
                        'name' => '_link_name',
                        'value' => null,
                        'placeholder' => 'Titolo',
                        'id' => '_link_name',
                        'class' => 'form-control _link_name',
                    ]) }}
                </div>
                <div class="form-group">
                    <label for="_link_url">Url</label>
                    {{ form_input([
                        'name' => '_link_url',
                        'value' => null,
                        'placeholder' => 'http://www.',
                        'id' => '_link_url',
                        'class' => 'form-control _link_url',
                    ]) }}
                </div>
            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">Chiudi</button>
                <button name="send" type="submit" id="btn_save_link" class="btn btn-outline-primary save-data-link">
                    <i class="far fa-save"></i>&nbsp; Salva
                </button>
            </div>
        </div>
    </div>
    <input type="hidden" name="_link_action" id="_link_action" value="">
    <input type="hidden" name="_link_id" id="_link_id" value="">
    <input type="hidden" name="_link_type" id="_link_type" value="">
</div>

<script type="text/javascript">
    $(document).ready(function () {

        loadLinks('links_header');
        loadLinks('links_footer');

        $('.open-modal-link').bind('click', function (e) {
            e.preventDefault();

            $('#title-action-heading-links').empty().append($(this).attr('data-name'));
            $('#_link_type').val($(this).attr('data-type'));
            $('#_link_action').val('insert');
            {{--  Reset --}}
            $('#_link_name').val('');
            $('#_link_url').val('');
            $('#modal-link-header').modal('show');
        });
        $('#modal-link-header').on('hidden.bs.modal', function () {
            $('.save-data-link').attr("disabled", false);
        });

        $('.save-data-link').bind('click', function (e) {

            e.preventDefault();
            let btnSend = $(this);

            $.ajax({
                type: 'POST',
                url: '{{ siteUrl('admin/institution/link/save') }}',
                dataType: "JSON",
                data: {
                    name: $('#_link_name').val(),
                    url: $('#_link_url').val(),
                    action: $('#_link_action').val(),
                    type: $('#_link_type').val(),
                    id: $('#_link_id').val(),
                    institution_id: '<?php echo @$institution['id'];?>'
                },

                beforeSend: function () {
                    btnSend.empty().append('{{ __('brn_save_spinner',null,'patos') }}').attr("disabled", true);
                    removeLayerErrorCustomLink();
                    removeLayerSuccessCustomLink();
                },

                success: function (data) {
                    btnSend.empty().append('{{ __('brn_save',null,'patos') }}');
                    let response = parseJson(data);
                    $('#success_custom_link').empty().append(response.data.message).show();
                    let type = $('#_link_type').val();

                    if (type == 'header') {
                        loadLinks('links_header');
                    }

                    if (type == 'footer') {
                        loadLinks('links_footer');
                    }
                },

                complete: function () {
                    btnSend.empty().append('{{ __('brn_save',null,'patos') }}');
                    setTimeout(function () {
                        $('#modal-link-header').modal('hide');
                        removeLayerSuccessCustomLink();
                    }, 800);
                },

                error: function (jqXHR, status) {
                    let response = parseJson(jqXHR.responseText);
                    $('#error_custom_link').empty().append(response.errors.error).show();
                    setTimeout(function () {
                        removeLayerErrorCustomLink()
                    }, 6000)
                }

            });

        });

        function loadLinks() {

            let args = arguments;
            let selector = args[0];

            $.ajax({
                type: 'GET',
                url: '{{ siteUrl('admin/institution/link/list') }}',
                dataType: "JSON",
                data: {
                    type: (selector == 'links_header') ? 'header' : 'footer',
                    institution_id: '<?php echo @$institution['id'];?>',
                    storage_type: '<?php echo @$_storageType ;?>'
                },

                beforeSend: function () {
                    $(selector).empty().append(getSpinner());
                },

                success: function (data) {
                    $('#' + selector).empty().append(data.data.message);
                    initButtonGroupAction();
                },

                complete: function () {
                },

                error: function (jqXHR, status) {
                }

            });
        }

        function removeLayerErrorCustomLink() {
            $('#error_custom_link').empty().hide();
        }

        function removeLayerSuccessCustomLink() {
            $('#success_custom_link').empty().hide();
        }

        function getSpinner() {
            let html = '<div class="spinner-border" role="status">';
            html += '<span class="visually-hidden">Attentere...</span>';
            html += '</div>';

            return html;
        }

        function initButtonGroupAction() {

            $('.a_up').bind('click', function (e) {

                e.preventDefault();
                let id = $(this).attr('href');
                let reload = $(this).attr('data-reload');
                let sortId = $(this).attr('data-sort');
                operationItem(id, reload, 'sort', 'up', sortId, reload);

            });

            $('.a_down').bind('click', function (e) {

                e.preventDefault();
                let id = $(this).attr('href');
                let reload = $(this).attr('data-reload');
                let sortId = $(this).attr('data-sort');
                operationItem(id, reload, 'sort', 'down', sortId, reload);

            });

            $('.a_edit').bind('click', function (e) {

                e.preventDefault();
                let id = $(this).attr('href');
                let reload = $(this).attr('data-reload');
                operationItem(id, reload, 'edit');

            });

            $('.a_delete').bind('click', function (e) {

                e.preventDefault();
                let id = $(this).attr('href');
                let reload = $(this).attr('data-reload');
                operationItem(id, reload, 'delete');

            });
        }

        function operationItem() {

            let args = arguments;
            let action = {}
            let currentAction = '';
            let reload = 'links_' + args[1];

            action.id = encodeURI(args[0]);
            action.action = encodeURI(args[2]);
            action.institution_id = encodeURI('<?php echo @$institution['id'];?>');

            if (typeof args[3] !== 'undefined') {
                action.direction = encodeURI(args[3]);
                action.sort_id = encodeURI(args[4]);
                action.position = encodeURI(args[5]);
                currentAction = encodeURI(args[3]);
            } else {
                currentAction = encodeURI(args[2]);
            }

            $.ajax({
                type: 'GET',
                url: '{{ siteUrl('admin/institution/link/save') }}?' + $.param(action),
                dataType: "JSON",

                beforeSend: function () {

                    let currentSelector = '#' + currentAction + '_' + action.id;
                    $(currentSelector).empty().append('<i class="fas fa-spinner fa-spin"></i>').attr("disabled", false);

                },

                success: function (data) {

                    if (typeof selector !== 'undefined') {

                        $('#' + selector).empty().append(data.data.message);
                    }

                    let actions = ['delete', 'sort', 'insert'];

                    if (inArray(action.action, actions)) {

                        loadLinks(reload);

                    } else if (action.action === 'edit') {

                        $('#' + currentAction + '_' + action.id).empty().append('<i class="fas fa-edit"></i>');

                        $('#_link_url').val(data.data.record.url);
                        $('#_link_name').val(data.data.record.title);
                        $('#_link_id').val(data.data.record.id);
                        $('#_link_type').val(data.data.record.position);
                        $('#_link_action').val('update');

                        $('#title-action-heading-links').empty().append('Modifica link testata');
                        $('#modal-link-header').modal('show');

                    }

                },

                complete: function () {

                },

                error: function (jqXHR, status) {
                }

            });

        }

        function btnSpinner() {
            return '<i class="fas fa-spinner-third"></i>';
        }

    });
</script>
