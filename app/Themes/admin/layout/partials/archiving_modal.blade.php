<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

{{-- Begin Modale Archiviazione --}}
<div class="modal fade zoom-in" id="archive_modal" tabindex="-1" role="dialog" aria-labelledby="archiveModalLabel"
     aria-hidden="true">
    <div class="modal-dialog modal-dialog-centered modal-lg modal-dialog-scrollable" role="document">
        <div class="modal-content" style="min-height: 600px;">
            <div class="modal-header">
                <h5 class="modal-title" id="archiveModalLabel"><i class="fas fa-folder"></i> {{nbs(2)}}Archiviazione
                    elemento</h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body " id="modal-form">
                <div class="justify-content-center mt-5 pt-5" id="spinner-loading">
                    <div class="col-md-12 text-center" id="loading-l">
                        <div class="spinner-border mb-2" role="status"></div>
                        <img id="" src="{{baseUrl('assets/admin/img/pat_logo_nero.png')}}">
                    </div>
                </div>

                <div id="archiving-content">
                    <div class="p-2" id="a-table">
                        <h5 id="item_name"></h5>
                        <p id="info-txt">
                            Le seguenti informazioni, correlate all'elemento che si sta archiviando, verranno riportate
                            in un campo
                            descrittivo e le correlazioni verranno rimosse.
                        </p>
                        <p>
                        <h6 id="related-info"><strong>INFORMAZIONI CORRELATE</strong></h6>
                        <p style="list-style-type:none;" id="related-list">
                        </p>
                        </p>
                        {{ form_open('',[
                            'name' => 'form_archiving',
                            'id' => 'form_archiving',
                            'class' => 'form_archiving',
                        ]) }}
                        <div class="row" id="to-exclude">
                            <div class="form-group col-md-6" id="only-person">
                                <label for="active_from" id="from-date">Attiva dal</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    </div>
                                    <input type="date" name="active_from"
                                           placeholder="GG/MM/AAAA"
                                           autocomplete="off" class="form-control"
                                           value="{{ @$active_from }}"
                                           id="input_active_from">
                                </div>
                            </div>

                            <div class="form-group col-md-6">
                                <label for="active_to" id="to-date">Attiva fino al</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    </div>
                                    <input type="date" name="active_to"
                                           placeholder="GG/MM/AAAA"
                                           autocomplete="off" class="form-control"
                                           value="{{ @$act_date }}"
                                           id="input_active_to">
                                </div>
                            </div>
                        </div>

                        <div class="row">
                            <div class="form-group col-md-6">
                                <label for="end_date">Data di fine pubblicazione in archivio *</label>
                                <div class="input-group">
                                    <div class="input-group-prepend">
                                        <span class="input-group-text"><i class="fas fa-calendar-alt"></i></span>
                                    </div>
                                    <input type="date" name="end_date"
                                           placeholder="GG/MM/AAAA"
                                           autocomplete="off" class="form-control"
                                           value="{{ @$act_date }}"
                                           id="input_end_date">
                                    <span id="end_date_error" class="error invalid-feedback">Campo obbligatorio!</span>
                                </div>
                            </div>
                        </div>
                        <p>Procedere con l'archiviazione?</p>
                    </div>
                </div>

                <div id="second_step">
                    <div id="__subStructures">
                    </div>

                    {{-- Campo Struttura di appartenenza --}}
                    <div class="form-row align-items-end" id="__structures">
                        <div class="form-group col-md-12" id="structures">
                            <label for="ajax_structure_of_belonging">Nuova Struttura di appartenenza</label>
                            <div id="ajax_structure_of_belonging"></div>
                            <input type="hidden" value="" name="structure_of_belonging_id"
                                   id="input_structure_of_belonging_id"
                                   class="structure_of_belonging_id">
                        </div>
                    </div>
                </div>

                {{ form_input([
                    'type' => 'hidden',
                    'name' => 'itemId',
                    'value' => '',
                    'id' => '__item_id',
                ]) }}

                {{ form_input([
                    'type' => 'hidden',
                    'name' => 'itemType',
                    'value' => '',
                    'id' => '__item_type',
                ]) }}

                {{ form_input([
                    'type' => 'hidden',
                    'name' => 'institution_id',
                    'value' => '',
                    'id' => 'institution_id',
                    'class' => 'institution_id',
                ]) }}

            </div>
            <div class="modal-footer" id="buttons">
                <button name="back" id="back_btn" class="btn btn-secondary" type="button">&nbsp; Torna indietro
                </button>

                <button name="cancel" id="cancel_btn" class="btn btn-secondary" type="button">&nbsp; Annulla
                </button>

                <button name="next" id="btn_next" class="btn btn-secondary" type="button">&nbsp; Avanti
                </button>

                <button name="archive" id="btn_archive" class="btn btn-secondary" type="submit">&nbsp; Procedi
                </button>
            </div>
            {{ form_close() }}
        </div>
    </div>
</div>


<style>
    #spinner {
        display: flex;
    }

    .error-toast {
        z-index: 999999;
    }

    .modal.fade .modal-dialog {
        -webkit-transform: translate(0, 0);
        transform: translate(0, 0);
    }

    .zoom-in {
        transform: scale(0) !important;
        opacity: 0;
        -webkit-transition: .25s all 0s;
        -moz-transition: .25s all 0s;
        -ms-transition: .25s all 0s;
        -o-transition: .25s all 0s;
        transition: .25s all 0s;
        display: block !important;
    }

    .zoom-in.show {
        opacity: 1;
        transform: scale(1) !important;
        transform: none;
    }
</style>

{{ js('patos/jquery.patOsAjaxPagination.js', 'common') }}
{{ js('admin/get/config.js?box=true') }}

<script>
    $(document).ready(function () {

        {{-- Nascondo gli elementi da non mostrare all'apertura del modale --}}
        $('#second_step').hide();
        $('#__structures').hide();
        $('#back_btn').hide();
        $('#btn_next').hide();
        $('#btn_archive').hide();

        //$('#input_active_to').attr('value', "<?= date('Y-m-d') ?>");

        let itemType = '';
        let itemId = '';
        let institutionId = $('#institution_id').val();

        {{-- Chiusura del modale al click del pulsante Annulla --}}
        $('#cancel_btn').on('click', function () {
            $('#archive_modal').modal('hide');
        });

        {{-- Azioni eseguite al click del pulsante Indietro (nel caso di più step) --}}
        $('#back_btn').on('click', function () {
            $('#second_step').hide();
            $('#__structures').hide();
            $('#archiving-content').show();
            $('#cancel_btn').show();
            $('#back_btn').hide();
            $("#input_reassign_structures").select2().val("0").trigger("change");
        });

        {{-- All'apertura del modale --}}
        $('#archive_modal').on('show.bs.modal', function (e) {
            {{-- Mostro le info correlate all'elemento che si sta archiviando --}}
            $('#info-txt').show();
            $('#related-info').show();

            $('#only-person').hide();

            {{-- Info dell'elemento che si sta archiviando --}}
            $('#item_name').empty().append(e.relatedTarget.hasOwnProperty('name') ? 'Archiviazione di: <strong>' + htmlEncode(e.relatedTarget.name) + '</strong>' : null);
            itemId = e.relatedTarget.hasOwnProperty('id') ? e.relatedTarget.id : null;
            $('#__item_id').val(itemId);
            itemType = e.relatedTarget.hasOwnProperty('type') ? e.relatedTarget.type : null;
            $('#__item_type').val(itemType);
            $('#form_archiving').attr('action', '<?php echo baseUrl('admin/') ?>' + itemType + '/archive/' + itemId);

            {{-- In base alla tipologia dell'elemento che si sta archiviando setto i campi di input necessari all'archiviazione --}}
                    {{-- Effettuo la chiamata AJAX per recuperare le informazioni correlate --}}
            if (itemId && itemType) {
                if (['personnel', 'commission'].includes(itemType)) {
                    $('#only-person').show();
                    $tmpTxtFrom = itemType == 'personnel' ? 'In carica dal' : 'Attiva dal';
                    $tmpTxtTo = itemType == 'personnel' ? 'In carica fino al' : 'Attiva fino al';
                    $('#from-date').text($tmpTxtFrom);
                    $('#to-date').text($tmpTxtTo);
                }

                if (['company', 'proceeding', 'real-estate-asset'].includes(itemType)) {
                    $('#to-exclude').hide();
                }

                $.ajax({
                    type: 'GET',
                    // async: false,
                    url: '<?php echo baseUrl('admin/') ?>' + itemType + '/relatedinfo',
                    dataType: "JSON",
                    data: {
                        id: itemId
                    },
                    beforeSend: function () {
                    },
                    success: function (response) {

                        var response = parseJson(response);

                        // Informazioni correlate all'elemento da archiviare
                        data = response.data.related[0];

                        //Date di inizio e fine
                        archivingDate = response.data.dates;

                        //Strutture figlie(solo per le strutture organizzative)
                        subStructures = response.data.subStructures;
                        institutionId = response.data.institutionId;

                        $('#related-list').empty();

                        if (data) {
                            $.each(data, function (i, item) {
                                if (item) {
                                    el = '<span><b>' + i + ': </b>' + item + '</span><br>';
                                    $('#related-list').append(el);
                                }
                            });
                        }

                        if (archivingDate) {
                            $.each(archivingDate, function (i, item) {
                                if (item) {
                                    $('#input_' + i).attr('value', item)
                                }
                            });
                        }

                        if (!$('#related-list').children().length > 0) {
                            $('#info-txt').hide();
                            $('#related-info').hide();
                        }

                        if (institutionId) {
                            $('#institutionId').val(institutionId);
                        }

                        {{-- Se sono nelle strutture, e la struttura ha strutture che le appartengono, imposto i due step nel modale --}}
                        if (subStructures) {

                            // Tabella per la selezione della struttura di appartenenza
                            let structureOfBelonging = $('#ajax_structure_of_belonging').patOsAjaxPagination({
                                url: config.structure.url,
                                textLoad: config.structure.textLoad,
                                selectedLabel: 'Struttura selezionata',
                                footerTable: config.structure.footerTable,
                                classTable: config.structure.classTable,
                                hideShowTable: true,
                                hideTable: config.structure.hideTable,
                                showTable: config.structure.showTable,
                                search_placeholder: config.structure.search_placeholder,
                                setInputDataValue: '#input_structure_of_belonging_id',
                                dataParams: {
                                    model: 1,
                                    institution_id: institutionId,
                                    item_id: itemId
                                },
                                columns: config.structure.columns,
                                action: {
                                    type: 'radio',
                                },
                                dataSource: config.structure.dataSource,
                                addRecord: config.structure.addRecord,
                                archived: config.structure.archived,
                                showNumberItems: false,
                            });

                            $('#btn_next').show();
                            $('#btn_archive').hide();

                            $('#__subStructures').empty().append('<p>Stai archiviando la struttura di appartenenza delle seguenti strutture: <strong>' + subStructures
                                + '</strong>.</p>'
                                + '<div class="form-group col-md-6">' +
                                '<label for="input_reassign_structures">Vuoi riassegnarle ad un altra struttura?</label>' +
                                '<div class="select2-blue" id="reassign_structures">' +
                                '<select name="reassign_structures" id="input_reassign_structures" class="form-control select2-reassign_structures select2-hidden-accessible" style="width: 100%;" data-select2-id="4" tabindex="-1" aria-hidden="true">' +
                                '<option value="0" selected="selected" data-select2-id="6">No</option>' +
                                '<option value="1" data-select2-id="32">Si</option>' +
                                '</select>' +
                                '</div>' +
                                '</div>');

                            $('#input_reassign_structures').select2({
                                allowClear: true,
                            });

                            reassignStructures();

                        } else {
                            $('#btn_archive').show();
                        }
                    },
                    complete: function () {
                        $('#spinner-loading').hide();
                        $('#archiving-content').show();
                    },
                    error: function (jqXHR, status) {

                        let response = parseJson(jqXHR.responseText);

                        //Funzione che genera il toast con gli errori
                        //(vedere nel footer)
                        createValidatorFormErrorToast(response.error.error, 5000, 'Errori');
                    }
                });
            }
        });

        {{-- Chiamata AJAX per effettuare l'archiviazione dell'elemento --}}
        let btnSend = $('#btn_archive');
        $('#form_archiving').ajaxForm({
            method: 'POST',
            beforeSend: function () {
                btnSend.attr("disabled", true);
                $('.error-toast').remove();
            },
            success: function (data) {
                let response = parseJson(data);

                // Funzione che genera il toast con il messaggio di successo
                {{-- (vedere nel footer) --}}
                createValidatorFormSuccessToast(response.data.message);

                setTimeout(function () {
                    $('#archive_modal').modal('hide');
                    window.location.href = '{{ baseUrl('admin/') }}' + itemType;
                }, 800);

            },
            complete: function (xhr) {
            },
            error: function (jqXHR, status) {
                let response = parseJson(jqXHR.responseText);

                // Funzione che genera il toast con gli errori
                {{-- (vedere nel footer) --}}
                createValidatorFormErrorToast(response.errors.error, 2500);

                btnSend.empty().append('Procedi').attr("disabled", false);
            }
        });

        // Reset del from alla chiusura del modale
        $('#archive_modal').on('hidden.bs.modal', function () {
            $('#archive_modal form')[0].reset();
            $('#archive_modal').find('button').attr('disabled', false);
            $('.error-toast').parent().remove();
            $('#second_step').hide();
            $('#__structures').hide();
            $('#input_end_date').removeClass('is-invalid');
            $('#end_date_error').hide();
        });

        // Validazione del campo obbligatorio
        $('#input_end_date').on('change', function () {
            $('#input_end_date').removeClass('is-invalid');
            $('#end_date_error').hide();
        });

        {{-- Evento scatenato al click del pulsante Avanti(solo per le strutture) --}}
        $('#btn_next').on('click', function () {
            if (itemType == 'structure') {
                // Se il campo obbligatorio non è valorizzato, mostro l'errore
                if (!$('#input_end_date').val()) {
                    $('#input_end_date').addClass('is-invalid');
                    $('#end_date_error').show();
                    throw new Error("Campo obbligatorio!");
                } else { //Altrimenti vado allo step successivo
                    $('#archiving-content').hide();
                    $('#second_step').show();
                    $('#back_btn').show();
                    $('#cancel_btn').hide();
                    $('#btn_next').hide();
                    $('#btn_archive').show();
                }
            }

        });
    });

    {{-- Funzione chiamata se si vogliono riassegnare le strutture figlie di quella attuale ad un'altra --}}
    function reassignStructures() {
        $('#input_reassign_structures').on('select2:select', function (e) {
            let data = e.params.data;
            if (data.text === 'Si') {
                $('#__structures').show();
                $('#ajax_structure_of_belonging').show();
            } else {
                $('#__structures').hide();
                $('#ajax_structure_of_belonging').hide();
            }
        });
    }
</script>




