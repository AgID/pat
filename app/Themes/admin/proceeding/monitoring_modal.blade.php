<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

<form id="monitoring_form" name="monitoring_form">
    <div class="modal fade" id="monitoring-modal" tabindex="-1" role="dialog" aria-labelledby="monitoring-modal-label"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content">
                <div class="modal-header modal-header-patos">
                    <h5 class="modal-title" id="monitoring-modal-label">Finestra di dialogo sottosezione</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="year">Anno:</label>
                        {{ form_input([
                            'name' => 'year',
                            'type' => 'number',
                            'class' => 'form-control custom_section',
                            'id' => 'input_year',
                            'value' => '',
                        ]) }}
                    </div>
                    <div class="form-group">
                        <label for="year_concluded_proceedings">Numero procedimenti conclusi nell'anno:</label>
                        {{ form_input([
                            'name' => 'year_concluded_proceedings',
                            'type' => 'number',
                            'class' => 'form-control year_concluded_proceedings',
                            'id' => 'input_year_concluded_proceedings',
                            'value' => '',
                        ]) }}
                    </div>
                    <div class="form-group">
                        <label for="conclusion_days">Numero giorni medi di conclusione nell'anno:</label>
                        {{ form_input([
                            'name' => 'conclusion_days',
                            'type' => 'number',
                            'class' => 'form-control conclusion_days',
                            'id' => 'input_conclusion_days',
                            'value' => '',
                        ]) }}
                    </div>
                    <div class="form-group">
                        <label for="percentage_year_concluded_proceedings">Percentuale procedimenti conclusi nei termini
                            al
                            termine dell'anno:</label>
                        <div class="input-group">
                            {{ form_input([
                                'name' => 'percentage_year_concluded_proceedings',
                                'type' => 'number',
                                'class' => 'form-control percentage_year_concluded_proceedings',
                                'id' => 'input_percentage_year_concluded_proceedings',
                                'value' => '',
                            ]) }}
                            <div class="input-group-prepend">
                                <span class="input-group-text"><i class="fas fa-percentage"></i></span>
                            </div>
                        </div>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Annulla</button>
                    <button type="submit" id="save_monitoring" class="btn btn-primary" data-dismiss="modal" disabled><i
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
    let monitorigData = {{ !empty($proceeding['monitoring_datas']) ? json_encode($proceeding['monitoring_datas']) : 'null'}};

    let storageType = {index: null, type: null};

    $(document).ready(function () {

        // Creo le righe della tabella con i dati se gia presenti
        createMonitoringTable();

        /**
         * Metodo chiamato al click del bottone di salvataggio per i dati del monitoraggio
         */
        $('#save_monitoring').on('click', () => {
            storageType.type == 'new' ? insertMonitoringData() : updateMonitoringData();
        });

        /**
         * Metodo che chiamato alla creazione di un nuovo record dei dati di monitoraggio
         */
        $('#new-data').on('click', () => {
            document.getElementById('input_year').value = '';
            document.getElementById('input_year_concluded_proceedings').value = '';
            document.getElementById('input_conclusion_days').value = '';
            document.getElementById('input_percentage_year_concluded_proceedings').value = '';
            storageType['type'] = $('#new-data').data('storage-type');
        });

        $("input[type='number']").on("keyup", function(){
            if($(this).val() != ""){
                $("button[type='submit']").removeAttr("disabled");
            }
        });

    });

    /**
     * Metodo che inserisce un nuovo record per i dati del monitoraggio procedimentale,
     * e crea la riga nella tabella
     */
    function insertMonitoringData() {
        let tmpData = {};
        let tmpHtml = '';
        if(monitorigData) {
            newIndex = monitorigData.length;
        } else {
            monitorigData = [];
            newIndex = 0;
        }
        $('#monitoring_form').find('input').each(function () {
            tmpData[this.name] = $(this).val() || null;
        });
        // Controlle se il form contiene i dati o meno
        const isNullish = Object.values(tmpData).every(value => {
            if (value === null) {
                return true;
            }

            return false;
        });
        // Se non è vuoto inserisco il record nei dati di monitoraggio
        if (!isNullish) {
            monitorigData.push(tmpData);
            $('#monitoring_form').trigger('reset');
            $('#_monitoring').val(JSON.stringify(monitorigData));
            tmpHtml += '<tr class="text-center bg-success" id="new" data-row-index="' + newIndex + '">';
            tmpHtml += '<td>';
            tmpHtml += htmlEncode(tmpData.year) ?? '';
            tmpHtml += '</td>';
            tmpHtml += '<td>';
            tmpHtml += htmlEncode(tmpData.year_concluded_proceedings) ?? '';
            tmpHtml += '</td>';
            tmpHtml += '<td>';
            tmpHtml += htmlEncode(tmpData.conclusion_days) ?? '';
            tmpHtml += '</td>';
            tmpHtml += '<td>';
            tmpHtml += tmpData.percentage_year_concluded_proceedings ? htmlEncode(tmpData.percentage_year_concluded_proceedings) + '&percnt;' : '';
            tmpHtml += '</td>';
            tmpHtml += '<td class="text-center">';
            tmpHtml += '<button type="button" style="color:#DC3545;" onclick="removeData(' + newIndex + ')" class="btn" title="Elimina record">';
            tmpHtml += '<i class="fas fa-trash"></i>';
            tmpHtml += '</button>|';
            tmpHtml += '<button type="button" data-toggle="modal" data-target="#monitoring-modal" onclick="editData(' + newIndex + ')" class="btn edit-data" title="Modifica record" data-storage-type="update">';
            tmpHtml += '<i class="fas fa-edit"></i>';
            tmpHtml += '</button>';
            tmpHtml += '</td>';
            tmpHtml += '</tr>';
            $(tmpHtml).hide().appendTo('#monitoring-table').fadeIn(350);
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
    function updateMonitoringData() {
        let tmpData = {};
        let tmpHtml = '';
        let index = storageType.index;
        $('#monitoring_form').find('input').each(function () {
            tmpData[this.name] = $(this).val();
        });
        monitorigData[index] = tmpData;
        $('#_monitoring').val(JSON.stringify(monitorigData));
        $('#monitoring_form').trigger('reset');
        $('#monitoring-table').empty();
        createMonitoringTable();
    }

    /**
     * Metodo che elimina un record dei dati di monitoraggio,
     * ed elimina la riga relativa dalla tabella
     * @param index
     */
    function removeData(index) {
        monitorigData.splice(index, 1);
        $('#' + index).addClass('bg-danger').fadeOut(350);
        $('#_monitoring').val(JSON.stringify(monitorigData));
        $('#monitoring_form').trigger('reset');
        $('#monitoring-table').empty();
        createMonitoringTable();
    }

    /**
     * Metodo che setta i dati del record nel modale di modifica prima di aprirlo
     * @param index
     */
    function editData(index) {
        storageType['type'] = $('.edit-data').data('storage-type');
        storageType['index'] = index;
        let currentData = monitorigData[index];
        Object.keys(currentData).forEach(key => {
            $('#input_' + key).val(currentData[key]);
        });
    }

    /**
     * Funzione che crea le righe della tabella con i dati di monitoraggio se presenti,
     * altrimenti mostra un messaggio per notificare che non sono presenti dati
     */
    function createMonitoringTable() {
        let html = '';

        // Controllo se non sono presenti dati del monitoraggio
        if ((monitorigData == null) || (monitorigData != null && Object.keys(monitorigData).length === 0)) {
            html += '<p class="text-center mt-1 pt-1">Non ci sono dati disponibili</p>';
            $('#tabele-monitoring').after(html);
        } else { // Se sono gia presenti dati del monitoraggio costruisco le righe della tabella
            $(monitorigData).each(function (index, value) {
                html += '<tr class="text-center" data-row-index="' + index + '" id="' + index + '">';
                html += '<td>';
                html += value.year ?? '';
                html += '</td>';
                html += '<td>';
                html += value.year_concluded_proceedings ?? '';
                html += '</td>';
                html += '<td>';
                html += value.conclusion_days ?? '';
                html += '</td>';
                html += '<td>';
                html += value.percentage_year_concluded_proceedings ? value.percentage_year_concluded_proceedings + '&percnt;' : '';
                html += '</td>';
                html += '<td class="text-center">';
                html += '<button type="button" style="color:#DC3545;" onclick="removeData(' + index + ')" class="btn" title="Elimina record">';
                html += '<i class="fas fa-trash"></i>';
                html += '</button>|';
                html += '<button type="button" data-toggle="modal" data-target="#monitoring-modal" onclick="editData(' + index + ')" class="btn edit-data" title="Modifica record" data-toggle="modal" data-target="#monitoring-modal" data-storage-type="update">';
                html += '<i class="fas fa-edit"></i>';
                html += '</button>';
                html += '</td>';
                html += '</tr>';
            });
            $('#monitoring-table').append(html);
        }
    }
</script>