{{--  Controlli e Rilievi index (Paginazione) --}}
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
                        <h3 class="card-title">Elenco destinatari Report delle pubblicazione</h3>
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
                                        <a href="{{ siteUrl('admin/report-publication-recipients/create') }}"
                                           title="Crea un nuovo elemento"
                                           id="add-profiles" class="dropdown-item" type="button">
                                            <i class="fas fa-plus-circle mr-1"></i> Aggiungi un nuovo destinatario
                                        </a>
                                        @else
                                            <a href="#!"
                                               title="Crea un nuovo elemento"
                                               class="dropdown-item select-institutuion" type="button">
                                                <i class="fas fa-plus-circle mr-1"></i> Aggiungi un nuovo destinatario
                                            </a>
                                        @endif
                                    </li>

                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                @endif

                                @if(getAclDelete()===true)
                                    <li>
                                        <a href="#!"
                                           title="Cancella strutture selezionate"
                                           id="delete-items" class="dropdown-item" type="button" data-url="{{ siteUrl('admin/report-publication-recipients/deletes') }}">
                                            <i class="fas fa-trash mr-1"></i> Cancella elementi selezionati
                                        </a>
                                    </li>

                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">
                <table id="report_list" class="table table-bordered table-striped table-hover">
                    <thead class="bg-table-accessibility">
                    <tr>
                        <th>
                            {{ form_checkbox('item[]', null, false, 'class="checkbox_all"') }}
                        </th>
                        <th>NOME</th>
                        <th>EMAIL</th>
                        <th>ATTIVO</th>
                        <th>DATA CREAZIONE</th>
                        <th>ULTIMA MODIFICA</th>
                        {{-- Ente di appartenenza --}}
                        @if(isSuperAdmin())
                            <th>ENTE</th>
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
        {{-- Begin Paginazione records datatable --}}
        let dtable = $("#report_list").DataTable({
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
            ajax: "{{siteUrl('admin/report-publication-recipients/list')}}",
            order: [
                [5, 'desc'],
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
                null,
                null,
                {{-- Ente di appartenenza se l'utente è super admin --}}
                        @if(isSuperAdmin())
                    null,
                    @endif
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

        {{-- Seleziona deseleziona tutti i records nella tabella --}}
        $('.checkbox_all').bind('click', function () {
            $isChecked = $(this).is(':checked');
            if (parseBool($isChecked) === true) {
                $('.checkbox_item').prop('checked', true);
            } else {
                $('.checkbox_item').prop('checked', false);
            }
        });
    });
</script>
{% endblock %}