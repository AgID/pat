<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>

<form id="qualifications_form" name="qualifications_form">
    <div class="modal fade" id="qualifications-modal" tabindex="-1" role="dialog" aria-labelledby="qualifications-modal-label"
         aria-hidden="true">
        <div class="modal-dialog modal-dialog-centered" role="document">
            <div class="modal-content" style="display: flex">
                <div class="modal-header modal-header-patos">
                    <h5 class="modal-title" id="qualifications-modal-label">Aggiunta nuova qualifica</h5>
                    <button type="button" class="close text-white" data-dismiss="modal" aria-label="Close">
                        <span aria-hidden="true">&times;</span>
                    </button>
                </div>
                <div class="modal-body">
                    <div class="form-group">
                        <label for="qualification_name">Nome qualifica * </label>
                        <?php echo form_input([
                            'name' => 'qualification_name',
                            'type' => 'string',
                            'class' => 'form-control custom_section qualifications_keyup',
                            'id' => 'input_qualification_name',
                            'value' => '',
                            'required' => 'required',
                        ]) ?>
                    </div>
                    <div class="form-group">
                        <label for="qualification_visible">Visibile nel form del personale? </label>
                        <?php echo  form_dropdown(
                            'qualification_visible',
                            [
                                null => 'Seleziona',
                                0 => 'No',
                                1 => 'Si',
                            ],
                            '',
                            'class="form-control select2-qualification_visible qualifications_keyup" data-placeholder="Seleziona" id="input_qualification_visible" style="width: 100%;"'
                        ) ?>
                    </div>
                </div>
                <div class="modal-footer">
                    <button type="button" class="btn btn-outline-danger" data-dismiss="modal">Annulla</button>
                    <button type="submit" id="save_qualifications" class="btn btn-primary" data-dismiss="modal" disabled><i
                                class="far fa-save"></i>&nbsp;
                        Salva
                    </button>
                </div>
            </div>
        </div>

    </div>
</form>


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
    let qualificationsData = null;

    <?php if(!empty($institutionqualifications)){ ?>
        qualificationsData = <?php echo json_encode($institutionqualifications); ?>;
    $('#_qualifications').val(JSON.stringify(qualificationsData));
    <?php } ?>

    let qualificationsStorageType = {index: null, type: null};
        

    $(document).ready(function () {

        let $dropdownVisible = $('.select2-qualification_visible');
        $dropdownVisible.select2({
            allowClear: true,
            minimumResultsForSearch: -1
        });

        // Creo le righe della tabella con i dati se gia presenti
        createqualificationsTable('create');

        /**
         * Metodo chiamato al click del bottone di salvataggio per i dati del monitoraggio
         */
        $('#save_qualifications').on('click', function (e) {
            qualificationsStorageType.type == 'new' ? insertqualificationsData() : updatequalificationsData();
        });

        /**
         * Metodo che chiamato alla creazione di una nuova qualifica
         */
        $('#new-data-qualifications').on('click', () => {
            document.getElementById('input_qualification_name').value = '';
            $('.select2-qualification_visible').val(null);
            $('.select2-qualification_visible').trigger('change');

            qualificationsStorageType['type'] = $('#new-data-qualifications').data('storage-type');

            if (!$("button[type='submit']").attr("disabled")) {
                $("button[type='submit']").attr("disabled", "true");
            }
        });

        //abilito il salvataggio del record solo se tutti e 3 i campi necessari  sono stati compilati
        $(".qualifications_keyup").on("change", function() {
            let check = 0;
            $(".qualifications_keyup").each(function (hist) {
                // console.log(hist)
                console.log('value', $(".qualifications_keyup")[hist].value)
                if ($(".qualifications_keyup")[hist].value !== '' && $(".qualifications_keyup")[hist].value !== undefined && $(".qualifications_keyup")[hist].value !== null) {
                    //$("button[type='submit']").removeAttr("disabled");
                    check += 1;
                }
            });

            if (check ==2) {
                $("button[type='submit']").removeAttr("disabled");
                $("button[type='submit']").on('click', function (e) {
                    $('#qualifications_no_elements').text('');
                });
            } else if (!$("button[type='submit']").attr("disabled")) {
                $("button[type='submit']").attr("disabled", "true");
            }
        });
    });

    /**
     * Metodo che inserisce un nuovo record per le qualifiche
     * e crea la riga nella tabella
     */
    function insertqualificationsData() {

        let tmpData = {};
        let tmpHtml = '';

        if(qualificationsData) {
            newIndex = qualificationsData.length;
        } else {
            qualificationsData = [];
            newIndex = 0;
        }

        //Nome qualifica
        tmpData['qualification_name'] = $('#input_qualification_name').val();

        //Ruolo politico
        tmpData['qualification_visible'] =  $('.select2-qualification_visible').val();
        tmpData['id'] =  null;

        // Controllo se il form contiene i dati o meno
        const isNullish = Object.values(tmpData).every(value => {
            if (value === null) {
                return true;
            }

            return false;
        });

        // Se non è vuoto inserisco il record
        if (!isNullish) {
            qualificationsData.push(tmpData);
            $('#qualifications_form').trigger('reset');
            $('#_qualifications').val(JSON.stringify(qualificationsData));
            tmpHtml += '<tr class="text-center bg-success" id="new" data-row-index="' + newIndex + '">';
            tmpHtml += '<td style="display: none;">';
            tmpHtml += '';
            tmpHtml += '</td>';
            tmpHtml += '<td>';
            tmpHtml += tmpData.qualification_name ? htmlEncode(tmpData.qualification_name) : '';
            tmpHtml += '</td>';
            tmpHtml += '<td>';
            tmpHtml += tmpData.qualification_visible != '0' ? 'Si' : 'No';
            tmpHtml += '</td>';
            tmpHtml += '<td class="text-center">';
            // tmpHtml += '<button type="button" style="color:#DC3545;" onclick="removeData(' + newIndex + ')" class="btn" title="Elimina ruolo">';
            // tmpHtml += '<i class="fas fa-trash"></i>';
            // tmpHtml += '</button>|';
            tmpHtml += '<button type="button" data-toggle="modal" data-target="#qualifications-modal" onclick="editData(' + newIndex + ')" class="btn edit-data" title="Modifica qualifica" data-storage-type="update">';
            tmpHtml += '<i class="fas fa-edit"></i>';
            tmpHtml += '</button>';
            tmpHtml += '</td>';
            tmpHtml += '</tr>';
            $(tmpHtml).hide().appendTo('#qualifications-table').fadeIn(350);
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
    function updatequalificationsData() {
        let tmpData = {};
        let index = qualificationsStorageType.index;

        //Nome ruolo
        tmpData['qualification_name'] = $('#input_qualification_name').val();

        //Ruolo politico
        tmpData['qualification_visible'] =  $('.select2-qualification_visible').val();

        const qualification_id = qualificationsData[index]['id'];
        tmpData['id'] = qualification_id;

        qualificationsData[index] = tmpData;
        $('#_qualifications').val(JSON.stringify(qualificationsData));
        $('#qualifications_form').trigger('reset');
        $('#qualifications-table').empty();
        createqualificationsTable('update');
    }

    /**
     * Metodo che elimina un ruolo
     * ed elimina la riga relativa dalla tabella
     * @param index
     */
    /*function removeData(index) {

    }*/

    /**
     * Metodo che setta i dati del record nel modale di modifica prima di aprirlo
     * @param index
     */
    function editData(index) {
        qualificationsStorageType['type'] = $('.edit-data').data('storage-type');
        qualificationsStorageType['index'] = index;
        let currentData = qualificationsData[index];
        $("button[type='submit']").removeAttr("disabled");

        //Nome ruolo
        $('#input_qualification_name').val(currentData['qualification_name']);

        //Ruolo politico
        $('.select2-qualification_visible').val(currentData['qualification_visible']);
        $('.select2-qualification_visible').trigger('change');
    }

    /**
     * Funzione che crea le righe della tabella con i dati di monitoraggio se presenti,
     * altrimenti mostra un messaggio per notificare che non sono presenti dati
     */
    function createqualificationsTable() {
        let html = '';

        // Controllo se non sono presenti dati del monitoraggio
        if ((qualificationsData == null) || (qualificationsData != null && Object.keys(qualificationsData).length === 0)) {
            if(arguments[0] === 'create'){
                html += '<p id = "qualifications_no_elements" class="text-center mt-1 pt-1">Non è stata ancora aggiunta alcuna qualifica</p>';
                $('#table-qualifications').after(html);
            }else{
                $('#qualifications_no_elements').text('Non ci sono ruoli aggiuntivi');
            }

        } else { // Se sono gia presenti dati del monitoraggio costruisco le righe della tabella
            $(qualificationsData).each(function (index, value) {
                html += '<tr class="text-center" data-row-index="' + index + '" id="' + index + '">';
                html += '<td style="display: none;">';
                html += value.id ?? '';
                html += '</td>';
                html += '<td>';
                html += value.qualification_name ?? '';
                html += '</td>';
                html += '<td>';
                html += value.qualification_visible != '0' ? 'Si' : 'No';
                html += '</td>';
                html += '<td class="text-center">';
                // html += '<button type="button" style="color:#DC3545;" onclick="removeData(' + index + ')" class="btn" title="Elimina ruolo">';
                // html += '<i class="fas fa-trash"></i>';
                // html += '</button>|';
                html += '<button type="button" data-toggle="modal" data-target="#qualifications-modal" onclick="editData(' + index + ')" class="btn edit-data" title="Modifica ruolo" data-toggle="modal" data-target="#qualifications-modal" data-storage-type="update">';
                html += '<i class="fas fa-edit"></i>';
                html += '</button>';
                html += '</td>';
                html += '</tr>';
            });
            $('#qualifications-table').append(html);
        }
    }
</script>
