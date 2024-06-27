{{--  Template paginazione users profile --}}
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
    <div class="col-md-12">
        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6 mt-2">
                        <h3 class="card-title">Profili ACL installati</h3>
                    </div>

                    <div class="col-md-6 text-right">
                        <div class="dropdown">
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown"
                                    aria-expanded="false">
                                <i class="fas fa-plug mr-1"></i> Operazioni
                            </button>

                            <ul class="dropdown-menu dropdown-menu-right">

                                @if(getAclAdd()===true)
                                    <li>
                                        @if(checkAlternativeInstitutionId() != 0)
                                        <a href="{{ siteUrl('admin/acl-users-profile/create') }}"
                                           title="Crea nuovo profilo ACL"
                                           id="add-profiles" class="dropdown-item" type="button">
                                            <i class="fas fa-plus mr-1"></i> Aggiungi nuovo profilo
                                        </a>
                                        @else
                                            <a href="#!"
                                               title="Crea un nuovo profilo ACL"
                                               class="dropdown-item select-institution" type="button">
                                                <i class="fas fa-plus-circle mr-1"></i> Aggiungi nuovo profilo
                                            </a>
                                        @endif
                                    </li>

                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                @endif

                                @if(getAclDelete()===true)
                                    <li>
                                        <a href="#!" id="delete-items" title="Cancella profili selezionati"
                                           class="dropdown-item" type="button" data-url="{{ siteUrl('admin/acl-users-profile/deletes') }}">
                                            <i class="fas fa-trash mr-1"></i> Cancella profili selezionati
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <table id="list_profiles" class="table table-bordered table-striped table-hover">
                    <thead class="bg-table-accessibility">
                    <tr>
                        <th>
                            {{ form_checkbox('_all', null, false, 'class="checkbox_all"') }}
                        </th>
                        <th>PROFILO ACL</th>
                        <th>DESCRIZIONE</th>
                        {{-- Ente di appartenenza se l'utente è super admin--}}
                        @if(isSuperAdmin(true))
                            <th>ENTE</th>
                        @else
                            {{-- Altrimenti la tipologia del profilo, se è di sistema o meno --}}
                            <th>
                                TIPOLOGIA
                            </th>
                        @endif
                        <th class="text-center">AZIONI</th>
                    </tr>
                    </thead>
                </table>
            </div>
        </div>
    </div>
</div>
{% endblock %}
{{--  ************************************************ CSS ************************************************ --}}
{% block css %}
{{ css('datatables-bs4/css/dataTables.bootstrap4.min.css','common') }}
{{ css('datatables-responsive/css/responsive.bootstrap4.min.css','common') }}
{% endblock %}
{{--  ************************************************ JAVASCRIPT ************************************************ --}}
{% block javascript %}
{{ js('datatables/jquery.dataTables.min.js','common') }}
{{ js('datatables-bs4/js/dataTables.bootstrap4.min.js','common') }}
{{ js('datatables-responsive/js/dataTables.responsive.min.js','common') }}
{{ js('datatables-responsive/js/responsive.bootstrap4.min.js','common') }}

<script type="text/javascript">
    $(document).ready(function () {

        {{-- Nascondi navbar --}}
        /*setTimeout(function () {
            $('body').addClass('sidebar-collapse');
        }, 500);*/

        {{-- Begin Paginazione records datatable --}}
        let dtable = $("#list_profiles").DataTable({
            sPaginationType: "full_numbers",
            lengthMenu: [25, 50, 75, 100, 150],
            responsive: true,
            lengthChange: true,
            autoWidth: false,
            processing: true,
            searchDelay: 1500,
            bProcessing: true,
            bServerSide: true,
            bDeferRender: true,
            ajax: "{{siteUrl('admin/acl-users-profile/list')}}",
            order: [
                [1, 'asc'],
            ],
            columns: [
                {
                    orderable: false,
                    width: "3%"
                },
                null,
                null,
                null,
                {
                    orderable: false,
                    width: "5%"

                },
            ],
            language: {
                url: '{{ baseUrl('assets/common/datatables/it.json') }}'
            },
            fnDrawCallback: function (oSettings) {

            }
        }).on('init.dt', function () {

            {{-- La ricerca non inizia con meno di 3 lettere --}}
            let searchWait = 0;
            let searchWaitInterval;
            $(".dataTables_filter input")
                .unbind()
                .keyup('input', function(e){ //leave input
                    let item = $(this);
                    searchWait = 0;
                    searchTerm = $(item).val();
                    if(e.keyCode == 13) {
                        dtable.search(searchTerm).draw();
                    }

                    if(searchTerm.length >= 3) {
                        if(!searchWaitInterval) searchWaitInterval = setInterval(function(){
                            if(searchWait >= 3 ) {
                                clearInterval(searchWaitInterval);
                                searchWaitInterval = '';
                                dtable.search(searchTerm).draw();
                                searchWait = 0;
                            }
                            searchWait++;
                        },300);
                    }

                    if (searchTerm == "") {
                        dtable.search("").draw();
                    }

                    return;
                });
        });

        $.fn.DataTable.ext.pager.numbers_length = 5;
        {{-- End Paginazione records datatable --}}

        {{-- Metodo chiamato ogni volta che viene ricaricato il datatable --}}
        dtable.on('draw', function () {
            /**
             * Metodo che apre il form per la modifica dell'elemento selezionato
             */
            {{-- Vedere nel footer --}}
            editRecord();

            /**
             * Metodo che apre il form per la duplicazione dell'elemento selezionato
             */
            {{-- Vedere nel footer --}}
            duplicateRecord();

            /**
             * Metodo che apre il form per la visualizzazione dell'elemento selezionato
             */
            {{-- Vedere nel footer --}}
            viewRecord();

            /**
             * Metodo che per l'eliminazione dell'elemento selezionato
             */
            {{-- Vedere nel footer --}}
            deleteRecord();
        });

        {{-- Checkbox all --}}
        $('.checkbox_all').bind('click', function () {
            $isChecked = $(this).is(':checked');
            if (parseBool($isChecked) === true) {
                $('.checkbox_item').prop('checked', true);
            } else {
                $('.checkbox_item').prop('checked', false);
            }
        });
    })
</script>
{% endblock %}