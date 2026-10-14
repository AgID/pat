{{--  Organi politici index (Paginazione) --}}
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
                        <h3 class="card-title">Organi politici inseriti</h3>
                    </div>

                    <div class="col-md-6 text-right">
                        <div class="dropdown">

                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">

                <table id="politic_organ_list" class="table table-bordered table-striped table-hover">
                    <thead class="bg-table-accessibility">
                    <tr>
                        <th>
                            {{ form_checkbox('item[]', null, false, 'class="checkbox_all"') }}
                        </th>
                        <th>DENOMINAZIONE</th>
                        <th>COMPETENZE</th>
                        <th>ULTIMA MODIFICA</th>
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
        let dtable = $("#politic_organ_list").DataTable({
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
                url: "{{siteUrl('admin/politic-organ/list')}}",
                type: "GET",
                data: {}
            },
            order: [
                [1, 'desc'],
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

            },
        }).on('init.dt', function () {
            //Funzione per la ricerca
            searchDatatable(dtable);
        });

        $.fn.DataTable.ext.pager.numbers_length = 5;

        {{-- Metodo chiamato ogni volta che viene ricaricato il datatable --}}
        dtable.on('draw responsive-display', function () {
            /**
             * Metodo che effettua la chiamata per ottenere la lista delle versioni presenti per l'elemento
             * selezionato da mostrare nel modale.
             */
            {{-- Vedere nel footer --}}
            getVersionsList('v-spinner');

            /**
             * Metodo che apre il form per la modifica dell'elemento selezionato
             */
            {{-- Vedere nel footer --}}
            editRecord();

            /**
             * Metodo che apre il modale per i log
             */
            openLogModal();

        });

    });
</script>
{% endblock %}
