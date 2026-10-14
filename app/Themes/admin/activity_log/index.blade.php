{{-- Log delle attività index (Paginazione) --}}
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
                        <h3 class="card-title">Log delle attività</h3>
                    </div>

                    <div class="col-md-6 text-right">
                        <div>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">


                <table id="activity_list" class="table table-bordered table-striped table-hover">
                    <thead class="bg-table-accessibility">
                    <tr>
                        <th>NOME UTENTE</th>
                        <th>DATA</th>
                        <th>AZIONE</th>
                        <th>DESCRIZIONE</th>


                        <th>ORIGINE</th>

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
        let dtable = $("#activity_list").DataTable({
            sPaginationType: "full_numbers",
            lengthMenu: [50, 75, 100, 150, 200],
            responsive: true,
            lengthChange: true,
            autoWidth: false,
            processing: true,
            searchDelay: 1200,
            bProcessing: true,
            bServerSide: true,
            bDeferRender: true,
            ajax: {
                url: "{{siteUrl('admin/activity-log/list')}}",
                type: "GET",
            },
            order: [
                [1, 'DESC']
            ],
            columns: [
                null,
                null,
                null,
                null,
                {{-- null, --}}

                null,
            ],
            {{-- Nome utente, descrizione e origine non sono ordinabili lato server (il nome e' cifrato sul DB) --}}
            columnDefs: [
                { targets: -1, className: 'text-center', orderable: false },
                { targets: [0, 3], orderable: false }
            ],
            language: {
                url: '{{ baseUrl('assets/common/datatables/it.json') }}'
            },
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
                    if(e.keyCode === 13) {
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

        dtable.on('draw responsive-display', function () {
        });

    });
</script>
{% endblock %}
