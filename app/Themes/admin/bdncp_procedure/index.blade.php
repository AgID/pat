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



                                <span>
                                    <a type="button" class="btn btn-outline-primary"
                                       title="Consulta la guida all'utilizzo - eConnect"
                                       href="https://www.isweb.it/guida-econnect-da-trasparenza" target="_blank">
                                        <i class="fas fa-bolt"></i>&nbsp; Guida rapida eConnect
                                    </a>

                                    <a type="button"
                                       class="btn btn-outline-primary openEconnectModal">
                                        <i class="fas fa-cloud-download-alt mr-1"></i>
                                        <span>Avvia eConnect</span>
                                    </a>
                                </span>


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
                                        <a href="{{ empty($class) ? siteUrl('admin/bdncp-procedure/create') : '#!' }}"
                                           title="Crea nuova procedura"
                                           id="add-profiles" class="dropdown-item {{ $class }}" type="button">
                                            <i class="fas fa-plus-circle mr-1"></i> Aggiungi nuova Procedura
                                        </a>
                                    </li>

                                    <li>
                                        <a href="{{ empty($class) ? siteUrl('admin/bdncp-procedure/create-alert') : '#!' }}"
                                           title="Crea Nuovo Avviso"
                                           id="add-profiles" class="dropdown-item {{ $class }}" type="button">
                                            <i class="fas fa-plus-circle mr-1"></i> Aggiungi nuovo Avviso
                                        </a>
                                    </li>

                                    <li>
                                        <a href="{{ empty($class) ? siteUrl('admin/bdncp-procedure/create-variation') : '#!' }}"
                                           title="Crea nuova variante in corso d'opera"
                                           id="add-profiles" class="dropdown-item {{ $class }}" type="button">
                                            <i class="fas fa-plus-circle mr-1"></i> Aggiungi nuova Variante in corso
                                            d'opera
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


                                @if(getAclAdd())
                                @endif

                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">

                <div class="overlay-wrapper">
                    <div style="display:none;" class="overlay" id="sync-loading"><i
                                class="fas fa-3x fa-sync-alt fa-spin"></i>
                        <div class="text-bold pt-2">Recupero dati in corso...</div>
                    </div>


                    <table id="bdncp_contests_act_list" class="table table-bordered table-striped table-hover">
                        <thead class="bg-table-accessibility">
                        <tr>
                            <th>
                                {{ form_checkbox('item[]', null, false, 'class="checkbox_all"') }}
                            </th>

                            <th>TIPO</th>
                            <th>CIG (Link BDNCP)</th>
                            <th>OGGETTO</th>
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

    .select-attach option,
    .select-attach optgroup {
        background-color: #fff !important;
        color: #000 !important;
    }

    .select-attach.bg-success {
        background-color: #28a745 !important;
        color: #fff !important;
        border-color: #28a745 !important;
    }

    .select-attach.bg-danger {
        background-color: #dc3545 !important;
        color: #fff !important;
        border-color: #dc3545 !important;
    }

    .select-attach {
        height: 30px;
        padding-top: .25rem;
        padding-bottom: .25rem;
        padding-left: .5rem;
        padding-right: 1.2rem;
        font-size: 1rem;
        font-weight: 600;
        background-position: right .35rem center;
        border-radius: 0.375rem;
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
                {
                    width: "9%",
                    orderable: true
                },
                {
                    width: "10%",
                    orderable: true
                },
                {
                    orderable: false
                },
                {
                    orderable: false,
                    width: "5%"

                },
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

            /**
             * Plugins aggiuntivi
             */
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
