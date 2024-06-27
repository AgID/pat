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

        <div class="alert alert-info-warning alert-dismissible">
            <button type="button" class="close" data-dismiss="alert" aria-hidden="true">×</button>
            <h5><i class="icon fas fa-info"></i>ATTENZIONE</h5>
            N.B. Se si sta inserendo una procedura avviata a partire dal 01/01/2024, l'archivio da utilizzare è
            "<a href="{{siteUrl('admin/bdncp-procedure')}}"><strong>Bandi Gare e Contratti (dal 1/1/2024)</strong></a>"
        </div>

        <div class="card">
            <div class="card-header">
                <div class="row">
                    <div class="col-md-6 mt-2">
                        <h3 class="card-title">Bandi gara e contratti presenti</h3>
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
                                        <a href="{{siteUrl('admin/contests-act/create-deliberation')}}"
                                           title="Crea nuova delibera a contrarre o atto equivalente"
                                           id="add-profiles" class="dropdown-item" type="button">
                                            <i class="fas fa-plus-circle mr-1"></i> Nuova Determina a contrarre o atto
                                            equivalente
                                        </a>
                                    </li>

                                    <li>
                                        <a href="{{siteUrl('admin/contests-act/create-notice')}}"
                                           title="Crea Nuovo Bando di gara"
                                           id="add-profiles" class="dropdown-item" type="button">
                                            <i class="fas fa-plus-circle mr-1"></i> Nuovo Bando di gara
                                        </a>
                                    </li>

                                    <li>
                                        <a href="{{siteUrl('admin/contests-act/create-lot')}}"
                                           title="Crea Nuovo Lotto"
                                           id="add-profiles" class="dropdown-item" type="button">
                                            <i class="fas fa-plus-circle mr-1"></i> Nuovo Lotto
                                        </a>
                                    </li>

                                    <li>
                                        <a href="{{siteUrl('admin/contests-act/create-result')}}"
                                           title="Crea Nuovo Esito di Gara"
                                           id="add-profiles" class="dropdown-item" type="button">
                                            <i class="fas fa-plus-circle mr-1"></i> Nuovo Esito di Gara
                                        </a>
                                    </li>

                                    <li>
                                        <a href="{{siteUrl('admin/contests-act/create-alert')}}"
                                           title="Crea Nuovo Avviso"
                                           id="add-profiles" class="dropdown-item" type="button">
                                            <i class="fas fa-plus-circle mr-1"></i> Nuovo Avviso
                                        </a>
                                    </li>

                                    <li>
                                        <a href="{{siteUrl('admin/contests-act/create-foster')}}"
                                           title="Crea Nuovo Esito/Affidamento"
                                           id="add-profiles" class="dropdown-item" type="button">
                                            <i class="fas fa-plus-circle mr-1"></i> Nuovo Esito/Affidamento
                                        </a>
                                    </li>

                                    <li>
                                        <a href="{{siteUrl('admin/contests-act/create-liquidation')}}"
                                           title="Crea Nuova Liquidazione"
                                           id="add-profiles" class="dropdown-item" type="button">
                                            <i class="fas fa-plus-circle mr-1"></i> Nuova Liquidazione
                                        </a>
                                    </li>

                                    <li>
                                        <div class="dropdown-divider"></div>
                                    </li>
                                @endif

                                @if(getAclDelete())
                                    <li>
                                        <a href="#!" id="delete-items" title="Cancella profili selezionati"
                                           class="dropdown-item" type="button" data-url="{{ siteUrl('admin/contests-act/deletes') }}">
                                            <i class="fas fa-trash mr-1"></i> Cancella selezionati
                                        </a>
                                    </li>
                                @endif
                            </ul>
                        </div>
                    </div>
                </div>
            </div>

            <div class="card-body">

                <table id="contests_act_list" class="table table-bordered table-striped table-hover">
                    <thead class="bg-table-accessibility">
                    <tr>
                        <th>
                            {{ form_checkbox('item[]', null, false, 'class="checkbox_all"') }}
                        </th>

                        <th>OGGETTO</th>
                        <th>TIPO</th>
                        <th>CIG</th>
                        <th>IMPORTO LIQUIDATO</th>
                        <th>STRUTTURA</th>
                        <th>ATTIVO DA</th>
                        <th>AGGIUDICATARI</th>
                        <th>CREATO DA</th>
                        <th>MODIFICATO</th>
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

        $('#maggioriInfoAnac').hide();

        {{-- Begin Paginazione records datatable --}}
        let dtable = $("#contests_act_list").DataTable({
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
                url: "{{siteUrl('admin/contests-act/list')}}",
                type: "GET"
            },
            order: [
                [9, 'desc']
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
                    orderable: false
                },
                null,
                {
                    orderable: false
                },
                {
                    orderable: false
                },
                null,
                null,
                {{-- Ente di appartenenza --}}
                        @if(isSuperAdmin(true))
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
            fnDrawCallback: function (oSettings) {}
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