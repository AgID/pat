{{--  Bandi Gare e Contratti index (Paginazione) --}}
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
                        <h3 class="card-title">Procedure presenti</h3>
                    </div>

                    <div class="col-md-6 text-right">
                        <div class="dropdown">
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown"
                                    aria-expanded="false">
                                <i class="fas fa-plug mr-1"></i> Operazioni
                            </button>

                            <ul class="dropdown-menu dropdown-menu-right">

                                @if(getAclAdd())
                                    <li>
                                        <a href="{{siteUrl('admin/bdncp-procedure/create')}}"
                                           title="Crea nuova procedura"
                                           id="add-profiles" class="dropdown-item" type="button">
                                            <i class="fas fa-plus-circle mr-1"></i> Aggiungi nuova Procedura
                                        </a>
                                    </li>

                                    <li>
                                        <a href="{{siteUrl('admin/bdncp-procedure/create-alert')}}"
                                           title="Crea Nuovo Avviso"
                                           id="add-profiles" class="dropdown-item" type="button">
                                            <i class="fas fa-plus-circle mr-1"></i> Aggiungi nuovo Avviso
                                        </a>
                                    </li>

                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                @endif

                                @if(getAclDelete())
                                    <li>
                                        <a href="#!" id="delete-items" title="Cancella procedure selezionate"
                                           class="dropdown-item" type="button"
                                           data-url="{{ siteUrl('admin/bdncp-procedure/deletes') }}">
                                            <i class="fas fa-trash mr-1"></i> Cancella procedure selezionate
                                        </a>
                                    </li>

                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                @endif

                                @if(getAclExportCsv())
                                    <li>
                                        <a href="{{ siteUrl('admin/bdncp-procedure/export-csv') }}"
                                           title="Esporta in formato CSV"
                                           id="export-csv" class="dropdown-item" type="button">
                                            <i class="fas fa-file-csv mr-1"></i> Esporta dati in CSV
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">

                <table id="bdncp_contests_act_list" class="table table-bordered table-striped table-hover">
                    <thead class="bg-table-accessibility">
                    <tr>
                        <th>
                            {{ form_checkbox('item[]', null, false, 'class="checkbox_all"') }}
                        </th>

                        <th>TIPO</th>
                        <th>CIG</th>
                        <th>OGGETTO</th>
                        <th>LINK BANCA DATI NAZIONALE CONTRATTI PUBBLICI</th>
                        <th>CREATO DA</th>
                        <th>ULTIMA MODIFICA</th>
                        {{-- Ente di appartenenza --}}
                        @if(isSuperAdmin(true))
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
<style>
    .tooltip-inner {
        max-width: 250px !important;
        text-align: left;
    }
</style>
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
        let dtable = $("#bdncp_contests_act_list").DataTable({
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
            ajax: {
                url: "{{siteUrl('admin/bdncp-procedure/list')}}",
                type: "GET",
            },
            order: [
                [6, 'desc']
            ],
            columns: [
                {
                    orderable: false,
                },
                null,
                null,
                null,
                {
                    orderable: false
                },
                {
                    orderable: false,
                    width: "5%"

                },
                null,
                {{-- Ente di appartenenza --}}
                        @if(isSuperAdmin(true))
                    null,
                    @endif
                {
                    orderable: false,
                    width: "5%"

                }
            ],
            language: {
                url: '{{ baseUrl('assets/common/datatables/it.json') }}'
            },
            fnDrawCallback: function (oSettings) {
            }
        }).on('init.dt', function () {
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