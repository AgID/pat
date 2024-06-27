<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

<form id="historical_form" name="historical_form">
    <div class="modal fade" id="historical-modal" tabindex="-1" role="dialog" aria-labelledby="historical-modal-label"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="display: flex">
                <div class="modal-header modal-header-patos">
                    <h5 class="modal-title" id="historical-modal-label">Finestra di dialogo sottosezione</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="historical_role">Ruolo </label>
                        {{ form_input([
                            'name' => 'historical_role',
                            'type' => 'string',
                            'class' => 'form-control custom_section historical_keyup',
                            'id' => 'input_historical_role',
                            'value' => '',
                        ]) }}
                    </div>
                    <div class="form-group">
                        <label for="historical_structure">Struttura </label>
                        {{ form_input([
                            'name' => 'historical_structure',
                            'type' => 'string',
                            'class' => 'form-control historical_structure historical_keyup',
                            'id' => 'input_historical_structure',
                            'value' => '',
                        ]) }}
                    </div>
                    <div class="form-group">
                        <label for="historical_from_date">Dal </label>
                        {{ form_input([
                            'name' => 'historical_from_date',
                            'type' => 'date',
                            'class' => 'form-control historical_from_date historical_keyup',
                            'id' => 'input_historical_from_date',
                            'value' => '',
                        ]) }}
                    </div>
                    <div class="form-group">
                        <label for="historical_to_date">Al</label>
                        <div class="input-group">
                            {{ form_input([
                                'name' => 'historical_to_date',
                                'type' => 'date',
                                'class' => 'form-control historical_to_date historical_keyup ',
                                'id' => 'input_historical_to_date',
                                'value' => '',
                            ]) }}
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Annulla</button>
                    <button type="submit" id="save_historical" class="btn btn-primary" data-dismiss="modal" disabled><i
                                class="far fa-save"></i>&nbsp;
                        Salva
                    </button>
                </div>
            </div>
        </div>

    </div>
</form>


{{-- Stile per nascondere le frecce nei campi di tipo numerico --}}
<style>
    /* Chrome, Safari, Edge, Opera */
    input::-webkit-outer-spin-button,
    input::-webkit-inner-spin-button {
        -webkit-appearance: none;
        margin: 0;
    }

    /* Firefox */
    input[type=number] {
        -moz-appearance: textfield;
    }
</style>


<script>
    // Dati del monitoraggio gia presenti
    let historicalData = null;

    @if(!empty($personnel['historical_datas']))
        historicalData = {{ json_encode($personnel['historical_datas'])}};
    @endif

    let storageType = {index: null, type: null};

    $(document).ready(function () {

        // Creo le righe della tabella con i dati se gia presenti
        createhistoricalTable('create');

        /**
         * Metodo chiamato al click del bottone di salvataggio per i dati del monitoraggio
         */
        $('#save_historical').on('click', function (e) {
            storageType.type == 'new' ? inserthistoricalData() : updatehistoricalData();
        });

        /**
         * Metodo che chiamato alla creazione di un nuovo record dei dati di monitoraggio
         */
        $('#new-data').on('click', () => {

            document.getElementById('input_historical_role').value = '';
            document.getElementById('input_historical_structure').value = '';
            document.getElementById('input_historical_from_date').value = '';
            document.getElementById('input_historical_to_date').value = '';
            storageType['type'] = $('#new-data').data('storage-type');
            if (!$("button[type='submit']").attr("disabled")) {
                $("button[type='submit']").attr("disabled", "true");
            }
        });

        @if( in_array($_storageType,['duplicate']) )
            $('#_historical').val(JSON.stringify(historicalData));
        @endif

        //abilito il salvataggio del record solo se tutti e 3 i campi necessari  sono stati compilati
        $(".historical_keyup").on("change", function() {
            let check = 0;
            $(".historical_keyup").each(function (hist) {
                if ($(".historical_keyup")[hist].value !== '' && $(".historical_keyup")[hist].value !== undefined) {
                    //$("button[type='submit']").removeAttr("disabled");
                    check = 0;
                }else{
                    check += 1;
                }
            });
            if (check !== 4) {
                $("button[type='submit']").removeAttr("disabled");
                $("button[type='submit']").on('click', function (e) {
                    $('#historical_no_elements').text('');
                });
            } else if (!$("button[type='submit']").attr("disabled")) {
                $("button[type='submit']").attr("disabled", "true");
            }
        });
    });

    /**
     * Metodo che inserisce un nuovo record per i dati del monitoraggio procedimentale,
     * e crea la riga nella tabella
     */
    function inserthistoricalData() {

        let tmpData = {};
        let tmpHtml = '';
        if(historicalData) {
            newIndex = historicalData.length;
        } else {
            historicalData = [];
            newIndex = 0;
        }
        $('#historical_form').find('input').each(function () {
            tmpData[this.name] = $(this).val() || null;
        });

        // Controlle se il form contiene i dati o meno
        const isNullish = Object.values(tmpData).every(value => {
            if (value === null) {
                return true;
            }

            return false;
        });
        // Se non è vuoto inserisco il record
        if (!isNullish) {
            historicalData.push(tmpData);
            $('#historical_form').trigger('reset');
            $('#_historical').val(JSON.stringify(historicalData));
            tmpHtml += '<tr class="text-center bg-success" id="new" data-row-index="' + newIndex + '">';
            tmpHtml += '<td>';
            tmpHtml += tmpData.historical_role ? htmlEncode(tmpData.historical_role) : '';
            tmpHtml += '</td>';
            tmpHtml += '<td>';
            tmpHtml += tmpData.historical_structure ?htmlEncode(tmpData.historical_structure) : '';
            tmpHtml += '</td>';
            tmpHtml += '<td>';
            tmpHtml += tmpData.historical_from_date ?htmlEncode(tmpData.historical_from_date) : '';
            tmpHtml += '</td>';
            tmpHtml += '<td>';
            tmpHtml += tmpData.historical_to_date ? htmlEncode(tmpData.historical_to_date) :  '';
            tmpHtml += '</td>';
            tmpHtml += '<td class="text-center">';
            tmpHtml += '<button type="button" style="color:#DC3545;" onclick="removeData(' + newIndex + ')" class="btn" title="Elimina record">';
            tmpHtml += '<i class="fas fa-trash"></i>';
            tmpHtml += '</button>|';
            tmpHtml += '<button type="button" data-toggle="modal" data-target="#historical-modal" onclick="editData(' + newIndex + ')" class="btn edit-data" title="Modifica record" data-storage-type="update">';
            tmpHtml += '<i class="fas fa-edit"></i>';
            tmpHtml += '</button>';
            tmpHtml += '</td>';
            tmpHtml += '</tr>';
            $(tmpHtml).hide().appendTo('#historical-table').fadeIn(350);
            setTimeout(() => {
                $('#new').removeClass('bg-success');
                $('#new').removeAttr('id');
            }, 350);
        }
    }

    /**
     * Metodo che aggiorna un record dei dati di monitoraggio già esistente,
     * e aggiorna la riga nella tabella
     */
    function updatehistoricalData() {
        let tmpData = {};
        let tmpHtml = '';
        let index = storageType.index;
        $('#historical_form').find('input').each(function () {

            if(this.name==='historical_from_date' || this.name ==='historical_to_date'){
                let dateToSet = $(this).val() ? $(this).val().split(' ')[0] : '';
                tmpData[this.name] = dateToSet;
            }else{
                tmpData[this.name] = $(this).val();
            }
        });
        historicalData[index] = tmpData;
        $('#_historical').val(JSON.stringify(historicalData));
        $('#historical_form').trigger('reset');
        $('#historical-table').empty();
        createhistoricalTable('update');
    }

    /**
     * Metodo che elimina un record dei dati di monitoraggio,
     * ed elimina la riga relativa dalla tabella
     * @param index
     */
    function removeData(index) {
        historicalData.splice(index, 1);
        $('#' + index).addClass('bg-danger').fadeOut(350);
        $('#_historical').val(JSON.stringify(historicalData));
        $('#historical_form').trigger('reset');
        $('#historical-table').empty();
        createhistoricalTable('delete');
    }

    /**
     * Metodo che setta i dati del record nel modale di modifica prima di aprirlo
     * @param index
     */
    function editData(index) {
        storageType['type'] = $('.edit-data').data('storage-type');
        storageType['index'] = index;
        let currentData = historicalData[index];
        $("button[type='submit']").removeAttr("disabled");
        Object.keys(currentData).forEach(key => {
            if(key === 'historical_from_date' || key ==='historical_to_date'){
                let dateToSet = currentData[key] ? currentData[key].split(' ')[0] : '';
                $('#input_' + key).val(dateToSet);
            }else{
                $('#input_' + key).val(currentData[key]);
            }
        });
    }

    /**
     * Funzione che crea le righe della tabella con i dati di monitoraggio se presenti,
     * altrimenti mostra un messaggio per notificare che non sono presenti dati
     */
    function createhistoricalTable() {
        let html = '';

        // Controllo se non sono presenti dati del monitoraggio
        if ((historicalData == null) || (historicalData != null && Object.keys(historicalData).length === 0)) {
            if(arguments[0] === 'create'){
                html += '<p id = "historical_no_elements" class="text-center mt-1 pt-1">Non ci sono dati disponibili</p>';
                $('#table-historical').after(html);
            }else{
                $('#historical_no_elements').text('Non ci sono dati disponibili');
            }

        } else { // Se sono gia presenti dati del monitoraggio costruisco le righe della tabella
            $(historicalData).each(function (index, value) {
                let fromDate = value.historical_from_date ? value.historical_from_date.split(' ') :  '';
                let toDate = value.historical_to_date ? value.historical_to_date.split(' ') :  '';

                html += '<tr class="text-center" data-row-index="' + index + '" id="' + index + '">';
                html += '<td>';
                html += value.historical_role ?? '';
                html += '</td>';
                html += '<td>';
                html += value.historical_structure ?? '';
                html += '</td>';
                html += '<td>';
                html += fromDate[0] ?? '';
                html += '</td>';
                html += '<td>';
                html += toDate[0] ?? '';
                html += '</td>';
                html += '<td class="text-center">';
                html += '<button type="button" style="color:#DC3545;" onclick="removeData(' + index + ')" class="btn" title="Elimina record">';
                html += '<i class="fas fa-trash"></i>';
                html += '</button>|';
                html += '<button type="button" data-toggle="modal" data-target="#historical-modal" onclick="editData(' + index + ')" class="btn edit-data" title="Modifica record" data-toggle="modal" data-target="#historical-modal" data-storage-type="update">';
                html += '<i class="fas fa-edit"></i>';
                html += '</button>';
                html += '</td>';
                html += '</tr>';
            });
            $('#historical-table').append(html);
        }
    }
</script>
