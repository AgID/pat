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
                        <!--<th>AREA</th>-->
                        {{-- Ente di appartenenza se l'utente è super admin --}}
                        @if(isSuperAdmin(true))
                            <th>ENTE</th>
                        @endif
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
            lengthMenu: [25, 50, 75, 100, 150],
            responsive: true,
            lengthChange: true,
            autoWidth: false,
            processing: true,
            searchDelay: 1200,
            bProcessing: true,
            bServerSide: true,
            bDeferRender: true,
            ajax: "{{siteUrl('admin/activity-log/list')}}",
            order: [
                [2, 'DESC']
            ],
            columns: [
                null,
                null,
                null,
                null,
                {{-- null, --}}
                {{-- Ente di appartenenza se l'utente è super admin --}}
                        @if(isSuperAdmin(true))
                    null,
                @endif
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

    });
</script>
{% endblock %}