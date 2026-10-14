{{--  Utente index (Paginazione) --}}
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
                        <h3 class="card-title">Utenti installati</h3>
                    </div>

                    <div class="col-md-6 text-right">
                        <div class="dropdown">
                            <button type="button" class="btn btn-secondary dropdown-toggle" data-toggle="dropdown"
                                    aria-expanded="false">
                                <i class="fas fa-plug mr-1"></i> Operazioni
                            </button>

                            <ul class="dropdown-menu dropdown-menu-right">

                                @if(getAclAdd())
                                    @php
                                        $class = checkAlternativeInstitutionId() == 0 ? 'select-institution' : null;
                                    @endphp
                                    <li class="check-users-num">
                                        <a href="{{ empty($class) ? siteUrl('admin/user/create') : '#!' }}"
                                           title="Aggiungi un nuovo Utente"
                                           id="add-users" class="dropdown-item {{ $class }}" type="button">
                                            <i class="fas fa-plus-circle mr-1"></i> Aggiungi un nuovo Utente
                                        </a>
                                    </li>
                                @endif

                                @if(getAclDelete())

                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>

                                    <li>
                                        <a href="#!"
                                           title="Cancella Utenti selezionati"
                                           id="delete-items" class="dropdown-item" type="button" data-url="{{ siteUrl('admin/user/deletes') }}">
                                            <i class="fas fa-trash mr-1"></i> Cancella Utenti selezionati
                                        </a>
                                    </li>
                                @endif



                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">

                <table id="users_list" class="table table-bordered table-striped table-hover">
                    <thead class="bg-table-accessibility">
                    <tr>
                        <th>
                            {{ form_checkbox('_all', null, false, 'class="checkbox_all"') }}
                        </th>
                        <th>NOME UTENTE</th>
                        <th>USERNAME</th>
                        <th>PROFILI ACL</th>
                        <th>INDIRIZZO EMAIL</th>



                        <th>AZIONI</th>
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

        let dtable = $("#users_list").DataTable({
            sPaginationType: "full_numbers",
            lengthMenu: [25, 50, 75, 100, 150],
            responsive: true,
            lengthChange: true,
            autoWidth: true,
            processing: true,
            searchDelay: 800,
            bProcessing: true,
            bServerSide: true,
            bDeferRender: true,
            order: [],
            ajax: {
                url: "{{siteUrl('admin/user/list')}}",
                type: "GET",
            },
            columns: [
                {
                    orderable: false,
                    width: "3%"
                },
                null,
                null,
                {
                    orderable: false
                },
                null,
                {
                    orderable: false,
                    width: "5%"
                },
            ],
            language: {
                url: '{{ baseUrl('assets/common/datatables/it.json') }}'
            },
        });

        $.fn.DataTable.ext.pager.numbers_length = 5;
        {{-- End Paginazione records datatable --}}

        {{-- Metodo chiamato ogni volta che viene ricaricato il datatable --}}
        dtable.on('draw responsive-display', function () {

            /**
             * Metodo che apre il form per la modifica dell'elemento selezionato
             */
            {{-- Vedere nel footer --}}
            editRecord();

            /**
             * Metodo che apre il form per la visualizzazione dell'elemento selezionato
             */
            {{-- Vedere nel footer --}}
            viewRecord();


            /**
             * Metodo che per l'eliminazione dell'elemento selezionato
             */
            deleteRecord();

            /**
             * Metodo che per l'eliminazione dell'elemento selezionato
             */
            {{-- Vedere nel footer --}}
            deleteRecord();

        });

        //Seleziona deseleziona tutti i records nella tabella
        document.querySelectorAll('.checkbox_all').forEach(checkbox => {
            checkbox.addEventListener('click', function () {
                const isChecked = this.checked;
                document.querySelectorAll('.checkbox_item').forEach(item => {item.checked = isChecked;});
            });
        });

    });

</script>

{% endblock %}
