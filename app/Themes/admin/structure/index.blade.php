{{--  Strutture index (Paginazione) --}}
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
                        <h3 class="card-title">Strutture organizzative presenti</h3>
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
                                    <li>
                                        <a href="{{ empty($class) ? siteUrl('admin/structure/create') : '#!' }}"
                                           title="Crea Nuova Struttura"
                                           id="add-structures" class="dropdown-item {{ $class }}" type="button">
                                            <i class="fas fa-plus-circle mr-1"></i> Aggiungi una nuova Struttura
                                        </a>
                                    </li>

                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                @endif

                                @if(getAclDelete())
                                    <li>
                                        <a href="#!"
                                           title="Cancella strutture selezionate"
                                           id="delete-items" class="dropdown-item" type="button"
                                           data-url="{{ siteUrl('admin/structure/deletes') }}">
                                            <i class="fas fa-trash mr-1"></i> Cancella Strutture selezionate
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


                <table id="structures_list" class="table table-bordered table-striped table-hover">
                    <thead class="bg-table-accessibility">
                    <tr>
                        <th>
                            {{ form_checkbox('item[]', null, false, 'class="checkbox_all"') }}
                        </th>
                        <th>UFFICIO</th>
                        <th>STRUTTURA DI APPARTENENZA</th>
                        <th>RESPONSABILE/I</th>
                        <th>VISUALIZZA IN ARTICOLAZIONE</th>
                        <th>CREATO DA</th>
                        <th>ULTIMA MODIFICA</th>


                        <th>ALLEGATI</th>
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
        let dtable = $("#structures_list").DataTable({
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
                url: "{{siteUrl('admin/structure/list')}}",
                type: "GET",
            },
            order: [
                [6, 'desc']
            ],
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
                {
                    orderable: false
                },
                null,
                null,

                {
                    orderable: false,
                    width: "5%"

                },
                {
                    orderable: false,
                    width: "5%"

                }

            ],
            language: {
                url: '{{ baseUrl('assets/common/datatables/it.json') }}'
            },
        }).on('init.dt', function () {
            //Funzione per la ricerca
            searchDatatable(dtable);
        });

        $.fn.DataTable.ext.pager.numbers_length = 5;
        {{-- End Paginazione records datatable --}}

        {{-- Metodo chiamato ogni volta che viene ricaricato il datatable --}}
        dtable.on('draw responsive-display', function () {
            /**
             * Metodo che effettua la chiamata per ottenere la lista delle versioni presenti per l'elemento
             * selezionato da mostrare nel modale.
             */
            {{-- Vedere nel footer --}}
            getVersionsList('v-spinner');

            /**
             * Metodo che apre il modale per l'archiviazione dell'elemento selezionato
             */
            {{-- Vedere nel footer --}}
            openArchiveModal('structures_list');

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


            /**
             * Metodo che apre il modale per i log
             */
            openLogModal();

            /**
             * Metodo che si occupa di ripubblicare gli elementi
             */
            republish();

            /**
             * workflow
             */
            askEdit();
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
