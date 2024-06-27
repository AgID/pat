<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
<script type="text/javascript">
    /**
     * Lista degli allegati che viene visualizzato nel campo di Input "attach_files"
     **/
    var attachFiles = null;

    /**
     * Tiene traccia dei vecchi file per il confronto con quelli caricati successivamente dal
     * campo di input "attach_files"
     **/
    var oldAttachFiles = {};

    /**
     * Tiene traccia dei campi label, omissis, publish e id da associare alla lista dei file
     * Caricati.
     **/
    var beforeDataValueOnMerge = {};

    {{-- Update --}}
    @if(!empty($listAttach))

    /**
     * Lista dei file stampati sotto forma di oggetto da ciclare nella tabella nella lista dei file
     **/
    var listFiles = {{ json_encode($listAttach) }};

    $(document).ready(function () {
        /**
         * Questo If crea in modo fittizio emulando la lista degli allegati in fase di edit del
         * record e li appende nel campo di Input files multi upload i files.
         **/
        if (listFiles.length >= 1) {
            var fileBuffer = new DataTransfer();
            for (var i = 0; i < listFiles.length; ++i) {
                let file = new File([''], ellipsisify(listFiles[i].client_name, 25, 10), {
                    type: listFiles[i].file_type,
                });
                Object.defineProperty(file, 'size', {
                    value: (listFiles[i].file_size * 1024)
                });
                fileBuffer.items.add(file);
            }

            document.getElementById("attach_files").files = fileBuffer.files;
            attachFiles = $('#attach_files').get(0).files;
        }
        /**
         * Crea l'HTML tabulare di tutti i file in modalita' edit.
         */
        buildHtmlFiles(listFiles, 'update');
    })
    @else
    /**
     * Instanzia un pggetto vuoto list files in fase di inserimento del record
     **/
    var listFiles = {};

    @endif

    /**
     * Funzione che ha il compito di rimuovere un singolo file nella tabella degli allegati.
     */
    function removeFile(index) {
        var attachments = document.getElementById("attach_files").files;
        var fileBuffer = new DataTransfer();
        for (let i = 0; i < attachments.length; i++) {
            if (index !== i) {
                fileBuffer.items.add(attachments[i]);
            }
        }
        document.getElementById("attach_files").files = fileBuffer.files;
        attachFiles = $('#attach_files').get(0).files;
        $('#btn_num_attahs').html(attachFiles.length);
        buildHtmlFiles(false);
    }

    /**
     * Funzione che ha il compito di unire i files nel momento in cui
     * si va a valorizzare in piu' passaggi uno o più file allegati sul campo di Input files
     * "attach_files"
     */
    function mergeFiles() {
        if (oldAttachFiles.length >= 1) {
            var attach = document.getElementById("attach_files").files;
            var joined = Array.from(attach).concat(Array.from(oldAttachFiles));
            var fileBuffer = new DataTransfer();
            for (let i = 0; i < joined.length; i++) {
                fileBuffer.items.add(joined[i]);
                if ($('#attach_id_' + i).length >= 1 && $('#omissis_' + i).length >= 1 && $('#omissis_' + i).length >= 1) {
                    beforeDataValueOnMerge[i] = {
                        'attach_id': $('#attach_id_' + i).val(),
                        'label_attach': $('#label_attach_' + i).val(),
                        'omissis': $('#omissis_' + i + ' option:selected').val(),
                        'publish': $('#publish_' + i + ' option:selected').val(),
                    }
                }
            }

            document.getElementById("attach_files").files = fileBuffer.files;
            attachFiles = $('#attach_files').get(0).files;
            oldAttachFiles = fileBuffer.files;
            $('#btn_num_attahs').html(oldAttachFiles.length);
        }
    }

    /**
     * Funzione che va a riordinare gli indici dei file nel momento in cui si avvia la funionalista
     * di spostamento di ordine dei file nella tabella degli allegati
     */
    function reorderFile(items) {
        var attachments = document.getElementById("attach_files").files;
        var fileBuffer = new DataTransfer();
        for (let i = 0; i < items.length; i++) {
            var item = items[i]
            fileBuffer.items.add(attachments[item]);
            document.getElementById("attach_files").files = fileBuffer.files;
            attachFiles = $('#attach_files').get(0).files;
        }
    }

    /**
     * Funzione che ha il compito di cambiare l'etichetta dell'allegato nel momento in si aggiunge
     * un nuovo file dalla finestra modale di aggiunta allegato.
     */
    function changeLabel() {
        $('.link-change-label').bind('click', function (e) {
            e.preventDefault();
            var selectorText = '#label_attach_' + $('#sto_row_id').val();
            if ($(this).prev().text().length > 0) {
                $(selectorText).val($(this).prev().text().trim());
            }
            $('#modal_attach_label').modal('hide');
            $('#sto_row_id').val('');
        });
    }

    /**
     * Funzione che ha il compito di creare la tabella in html della lista degli allegati
     */
    function buildHtmlFiles() {

        var args = arguments;

        /**
         * Unisco i files
         */
        if (args[0] !== false) {
            mergeFiles();
        }

        /**
         * Verifico se è in modalità edit del record
         */
        var hasUpdate = (args.length === 2 && String(args[1]) == 'update')
            ? true
            : false;

        var html = '';
        if ((attachFiles !== null && attachFiles.length >= 1) || listFiles.length >= 1) {
            $('#no_attach').hide();

            html += 'Documenti da allegare';
            html += '<table class="table table-hover table-bordered table-striped table-sm table-attach">';
            html += '<thead class="thead-dark">';
            html += '<tr>';
            html += '<th style="width: 3%;" scope="col" class="text-center">';
            html += '#';
            html += '</th>';
            html += '<th style="width: 35%;" scope="col">';
            html += 'File';
            html += '</th>';
            html += '<th style="width: 35%;">';
            html += 'Etichetta';
            html += '</th>';
            html += '<th style="width: 10%;">';
            html += 'Omissis';
            html += '</th>';
            html += '<th style="width: 10%;">';
            html += 'Nascondi';
            html += '</th>';
            html += '<th style="width: 7%;" class="text-center">';
            html += 'Azioni';
            html += '</th>';
            html += '</tr>';
            html += '</thead>';
            html += '<tbody>';

            var j = attachFiles.length - 1;

            if (attachFiles !== null && attachFiles.length >= 1) {
                for (var i = 0; i < attachFiles.length; i++) {

                    /**
                     * Identificativo del record. Valorizzato solo in fase di edinting del record
                     **/
                    var id = null;

                    /**
                     * Nome dell'allegato
                     */
                    var name = attachFiles[i].name;

                    /**
                     * Grandezza dell'allegato
                     */
                    var size = attachFiles[i].size;

                    /**
                     * Estenzione dell'allegato
                     */
                    var ext = attachFiles[i].type;

                    /**
                     * Etichetta allegato
                     */
                    var label = $('#attach_name').val();

                    /**
                     * Se l'allegato deve essere indicizzato o meno
                     */
                    var omissis = $("#omissis_privacy option:selected").val();

                    /**
                     * Se l'allegato è visibile o meno nel fromt office
                     */
                    var visibleAttach = $("#active_attach option:selected").val();

                    /**
                     * Se omissis è selezionata, imposto la select sulla singola riga a "selezionata"
                     */
                    var omissisTrue = parseInt(omissis) == 1 ? ' selected="selected" ' : '';

                    /**
                     * Se omissis NON è selezionato, imposto la select sulla singola riga a "NON selezionata"
                     */
                    var omissisFalse = parseInt(omissis) == 0 ? ' selected="selected" ' : '';

                    /**
                     * Se lo stato di pubblicazione è selezionato, imposto la select sulla singola riga a "selezionata"
                     */
                    var publishTrue = parseInt(visibleAttach) == 1 ? ' selected="selected" ' : '';

                    /**
                     * Se lo stato di pubblicazione NON è selezionato, imposto la select sulla singola riga a "NON selezionato"
                     */
                    var publishFalse = parseInt(visibleAttach) == 0 ? ' selected="selected" ' : '';

                    /**
                     * Verifico ed eventualmente riassegno il valore alle varie etichette
                     * di ogni singola riga della tabella degli allegati.
                     * La riassegnazione dei valori viene controllata solo in fase di editing del record.
                     */
                    if (Object.keys(beforeDataValueOnMerge).length > 0 && hasUpdate) {

                        if (beforeDataValueOnMerge[i] !== undefined) {

                            id = (beforeDataValueOnMerge[i].attach_id != undefined)
                                ? beforeDataValueOnMerge[i].attach_id
                                : null;
                            label = beforeDataValueOnMerge[i].label_attach;
                            omissis = beforeDataValueOnMerge[i].omissis;
                            visibleAttach = beforeDataValueOnMerge[i].publish;
                            omissisTrue = parseInt(omissis) == 1 ? ' selected="selected" ' : '';
                            omissisFalse = parseInt(omissis) == 0 ? ' selected="selected" ' : '';
                            publishTrue = parseInt(visibleAttach) == 1 ? ' selected="selected" ' : '';
                            publishFalse = parseInt(visibleAttach) == 0 ? ' selected="selected" ' : '';

                        } else {
                            beforeDataValueOnMerge[i] = {
                                'label_attach': $('#attach_name').val(),
                                'omissis': $("#omissis_privacy option:selected").val(),
                                'publish': $("#active_attach option:selected").val(),
                            }

                            label = beforeDataValueOnMerge[i].label_attach;
                            omissis = beforeDataValueOnMerge[i].omissis;
                            visibleAttach = beforeDataValueOnMerge[i].publish;
                            omissisTrue = parseInt(omissis) == 1 ? ' selected="selected" ' : '';
                            omissisFalse = parseInt(omissis) == 0 ? ' selected="selected" ' : '';
                            publishTrue = parseInt(visibleAttach) == 1 ? ' selected="selected" ' : '';
                            publishFalse = parseInt(visibleAttach) == 0 ? ' selected="selected" ' : '';
                        }

                    } else {

                        if (hasUpdate) {
                            if (Object.keys(args[0]).length > 0) {
                                id = args[0][i].id;
                                label = args[0][i].label;
                                omissis = args[0][i].indexable;
                                visibleAttach = args[0][i].active;
                            }

                        } else {

                            var k = j - i;
                            label = (beforeDataValueOnMerge[k] !== undefined)
                                ? beforeDataValueOnMerge[k].label_attach
                                : label;

                            omissis = (beforeDataValueOnMerge[k] !== undefined)
                                ? beforeDataValueOnMerge[k].omissis
                                : omissis;

                            visibleAttach = (beforeDataValueOnMerge[k] !== undefined)
                                ? beforeDataValueOnMerge[k].publish
                                : visibleAttach;

                        }

                        omissisTrue = parseInt(omissis) == 1 ? ' selected="selected" ' : '';
                        omissisFalse = parseInt(omissis) == 0 ? ' selected="selected" ' : '';
                        publishTrue = parseInt(visibleAttach) == 1 ? ' selected="selected" ' : '';
                        publishFalse = parseInt(visibleAttach) == 0 ? ' selected="selected" ' : '';

                    }

                    /**
                     * Creo la tabella degli allegati
                     */
                    html += '<tr data-id=' + i + ' id="item-' + i + '">';
                    html += '<td class="tt text-center"><i class="fas fa-bars"></i></td>';
                    html += '<td>';
                    html += '<i class="fas fa-paperclip cursor-ns-resize "></i> &nbsp; ';
                    html += name.trim() + " <small> (" + getReadableFileSizeString(size) + ") &nbsp; " + ext.trim() + "</small>";
                    html += '</td>';
                    html += '<td class="no-drag">';
                    html += '<div class="w-100" data-id="' + i + '">';
                    html += '<input style="width:90%" type="text" value="' + label + '" name="label_attach[' + i + ']" id="label_attach_' + i + '" class="label_attach">';
                    html += '<input type="hidden" name="attach_id[' + i + ']" value="' + id + '" id="attach_id_' + i + '">';
                    html += '<button type="button" value="' + i + '" data-toggle="tooltip" data-placement="right" title="Cambia etichetta" class="btn change-label" style="width:10%"><i class="fas fa-tag"></i></button>';
                    html += '</div>';
                    html += '</td>';
                    html += '<td class="no-drag">';
                    html += '<div class="" data-id="' + i + '">';
                    html += '<select name="omissis[]" id="omissis_' + i + '" class="w-100">';
                    html += '<option value="1"' + omissisTrue + '>Si</option>';
                    html += '<option value="0"' + omissisFalse + '>No</option>';
                    html += '</select>';
                    html += '</div>';
                    html += '</td>';
                    html += '<td class="no-drag">';
                    html += '<div class="" data-id="' + i + '">';
                    html += '<select name="publish[]"  id="publish_' + i + '" class="w-100">';
                    html += '<option value="1"' + publishTrue + '>Pubblico</option>';
                    html += '<option value="0"' + publishFalse + '>Nascosto</option>';
                    html += '</select>';
                    html += '</div>';
                    html += '</td>';
                    html += '<td class="text-center no-drag">';
                    html += '<button style="color:#DC3545;" type="button" onclick="removeFile(' + i + ')" class="btn">';
                    html += '<i class="fas fa-trash"></i>';
                    html += '</button>';
                    html += '</td>';

                    if (!hasUpdate) {
                        /**
                         * Valorizzo gli indici delle etichette solo in fase di insert.
                         */
                        if (beforeDataValueOnMerge[j - i] === undefined) {

                            beforeDataValueOnMerge[j - i] = {
                                'label_attach': label,
                                'omissis': omissis,
                                'publish': visibleAttach,
                            }

                        }

                    }

                }
            }
            html += '</tbody>';
            html += '</table>';

        } else {
            $('#no_attach').show();
            attachFiles = null;
        }
        if (html.length >= 10) {
            $('#list_last_insert_files').empty().append(html).show();
        }

        /**
         * Trascinamento e ordinamewnto di posizione degli allegati nella tabella
         */
        $('tbody').sortable({
            cancel: '.no-drag',
            revert: true,
            update: function (event, ui) {
                var data = $(this).sortable('toArray');
                var i = 0;
                var items = [];
                data.forEach(function (key) {
                    items.push(parseInt(key.replaceAll('item-', '')));
                })
                /**
                 * Rirdino la posizione e visualizzazione degli
                 */
                reorderFile(items);
            }
        });

        /**
         * Modale che si apre ad ogni singola riga nella tabella degli allegati al fine di cambiare
         * l'etichetta per ogni singolo record.
         */
        $('.change-label').bind('click', function () {
            $('#sto_row_id').val($(this).val());
            $('#modal_attach_label').modal('show');
        });
        changeLabel();

        /**
         * Riassegnazione dei degli allegati attuali in questa variabile al fine di unire questi
         * con i nuovi allegati da caricare,
         */
        oldAttachFiles = {};
        for (var attr in attachFiles) {
            oldAttachFiles[attr] = attachFiles[attr];
        }
    }

    $(document).ready(function () {

        $('[data-toggle="tooltip"]').tooltip({
            trigger : 'hover'
        });

        /**
         *  Cattura il click sul bottone degli allegati,
         *  e scrolla la pagina fino alla tabella degli allegati.
         */
        $('a[href*="#attach_module"]')
            .click(function (event) {
                $('[data-toggle="tooltip"]').tooltip('hide');

                // Recupero l'elemento dove scrollare
                var target = $('#attach_module');
                target = target.length ? target : $('[name=' + this.hash.slice(1) + ']');
                // Verifico che l'elemento dove scrollare esiste
                if (target.length) {
                    event.preventDefault();
                    $('html, body').animate({
                        scrollTop: target.offset().top
                    }, 1000, function () {
                        var $target = $(target);
                        $target.focus();
                        if ($target.is(":focus")) {
                            return false;
                        } else {
                            $target.attr('tabindex', '-1');
                            $target.focus();
                        }
                        ;
                    });
                }
            });

        /**
         * Invoco la funzione per il cambio dello stato delle etichette
         */
        changeLabel();

        /**
         * Riassegno alla variabile "attachFiles" la lsytav degli allegati ogni volta che cambia
         * il campo di input "attach_files"
         */
        $("#attach_files").change(function () {
            attachFiles = $(this).get(0).files;
        });
        /**
         * Viasualizzo al lista delle etichetta da assegnare agli allegati
         */
        $('#select_default_name').bind('click', function () {
            $('#select_name').show();
        });

        /**
         * Prelevo il nome dell'etichetta da assegnare all'allegato
         */
        $('.btn_select_label').bind('click', function (e) {
            e.preventDefault();
            $('#attach_name').val($(this).prev().text()).prop('disabled', true);
            $('#clear_attach_name').show();
            $('#select_name').hide();
        });

        /**
         * Cancello il nome dell'etichetta nel campo di selezione della stessa
         * nella finestra modale.
         */
        $('#clear_attach_name').bind('click', function () {
            $(this).hide();
            $('#attach_name').prop('disabled', false).val('');
        });

        /**
         * Apro la finestra modale degli allegati
         */
        $('#modal_attach_file').bind('click', function () {
            $('#select_name').hide();
            $('#modal_attach_file_layer').modal('show');
        });

        /**
         * Allego i files nella tabella e chiudo la finestra modale
         */
        $('#add_html_attach_file').bind('click', function (e) {
            e.preventDefault();
            if (attachFiles === null) {
                alert('Non hai selezionato alcun file da allegare!')
            } else {
                $('#btn_num_attahs').html(attachFiles.length);
                $('#modal_attach_file_layer').modal('hide');
                @if(empty($listAttach))
                buildHtmlFiles();
                @else
                buildHtmlFiles(beforeDataValueOnMerge, 'update');
                @endif
            }
        });

        /**
         * Iniziallizzo lo spostamento degli allegati
         */
        $('tbody').sortable();
    })
</script>

{{-- Modale allegati --}}
<div class="modal fade" id="modal_attach_file_layer" tabindex="-1" role="dialog"
     aria-labelledby="modal_attach_file_layer"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exampleModalLabel">
                    <i class="fas fa-cloud-upload-alt"></i>
                    Gestione allegati
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <div class="form-group">
                    <label for="information_archive">Nome allegato:</label>
                    <div class="input-group">
                        {{ form_input([
                            'name' => 'attach_name',
                            'value' => null,
                            'placeholder' => 'Nome allegato',
                            'id' => 'attach_name',
                            'class' => 'form-control attach_name'
                        ]) }}

                        <span class="input-group-append">
                                            <button type="button" class="btn btn-primary btn-flat"
                                                    id="select_default_name">
                                                Seleziona nome
                                            </button>
                                        </span>
                        <a class="btn btn-flat btn-danger" id="clear_attach_name"
                           style="display: none;">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                    {{-- Campo nascosto a livello di CSS --}}
                    <div id="select_name">
                        <ul>
                            @if(!empty($labels))
                                @foreach($labels AS $l)
                                    <li>
                                        <span><i class="fas fa-caret-right"></i> {{ trim($l['label']['name']) }}</span>
                                        <a href="#!"
                                           class="btn btn-outline-primary btn-xs btn_select_label">
                                            Seleziona
                                        </a>
                                    </li>
                                @endforeach
                            @else
                                <li>
                                    <span><i class="fas fa-caret-right"></i> Allegato</span>
                                    <a href="#!"
                                       class="btn btn-outline-primary btn-xs btn_select_label">
                                        Seleziona
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                    {{-- / Campo nascosto a livello di CSS --}}
                </div>

                <div class="form-group">
                    <label for="attach_files">Allegato/i:</label>
                    <div class="input-group">
                        <div class="custom-file">
                            {{ form_upload([
                                'name' => 'attach_files[]',
                                'value' => null,
                                'multiple' => 'multiple',
                                'placeholder' => 'Allegato',
                                'id' => 'attach_files',
                                'class' => 'custom-file-input attach_files form-control-sm',
                                'aria-describedby' => 'input_group_attach_files'
                            ]) }}
                            <label class="custom-file-label" for="input_group_attach_files">Carica
                                allegati</label>
                        </div>
                    </div>
                </div>

                <div class="form-group">
                    <label for="omissis_privacy_attach">Omissis: (Privacy, il file non verrà
                        indicizzato)</label>
                    {{ form_dropdown(
                       'omissis_privacy_attach',
                       [
                            1 => 'Si',
                            0 => 'No'
                       ],
                       0,
                       'class="form-control form-control-sm" id="omissis_privacy"'
                   ) }}
                </div>

                <div class="form-group">
                    <label for="active_attach">Nascondi elemento: (Il file non verrà
                        pubblicato)</label>
                    {{ form_dropdown(
                       'active_attach',
                       [
                            1 => 'Pubblico',
                            0 => 'Nascosto'
                       ],
                       1,
                       'class="form-control form-control-sm" id="active_attach"'
                   ) }}
                </div>


            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i>{{ nbs(2) }}Chiudi
                </button>
                <button type="button" id="add_html_attach_file" class="btn btn-primary">
                    <i class="fas fa-file-upload"></i>{{ nbs(2) }}Aggiungi allegati
                </button>
            </div>
        </div>
    </div>
</div>
{{-- /Modale allegati --}}

{{-- Modale labeling --}}
<div class="modal fade" id="modal_attach_label" tabindex="-1" role="dialog"
     aria-labelledby="modal_attach_label"
     aria-hidden="true">
    <div class="modal-dialog" role="document">
        <div class="modal-content">
            <div class="modal-header">
                <h5 class="modal-title" id="exModalLabel">
                    <i class="fas fa-tags"></i>
                    Cambia etichetta
                </h5>
                <button type="button" class="close" data-dismiss="modal" aria-label="Close">
                    <span aria-hidden="true">&times;</span>
                </button>
            </div>
            <div class="modal-body">
                <ul class="list-group">
                    @if(!empty($labels))
                        @foreach($labels AS $l)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                                    <span>
                                                        <i class="fas fa-caret-right"></i> {{ trim($l['label']['name']) }} {{ nbs(2) }}
                                                    </span>
                                <a class="btn btn-outline-primary btn-xs link-change-label"
                                   href="#!">Seleziona</a>
                            </li>
                        @endforeach
                    @else
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <i class="fas fa-caret-right"></i> Allegato {{ nbs(2) }}
                            </span>
                            <a class="btn btn-outline-primary btn-xs" href="#!">Seleziona</a>
                        </li>
                    @endif
                </ul>
            </div>

            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i>{{ nbs(2) }}Chiudi
                </button>
                <input type="hidden" name="sto_row_id" id="sto_row_id" value="">
            </div>
        </div>
    </div>
</div>

{{-- Async  attach--}}
<div class="col-md-12" id="attach_module">
    <div class="row">
        <div class="col-md-6">
            <h5><i class="fas fa-link"></i> Allegati</h5>
        </div>
        <div class="col-md-6 text-right">
            <button type="button" id="modal_attach_file"
                    class="btn btn-primary btn-sm">
                <i class="fas fa-plus"></i> Aggiungi allegati
            </button>
        </div>

        <div class="col-md-12 mt-3">
            <div id="list_last_insert_files"></div>
        </div>

        <div class="col-md-12 mt-3" id="no_attach">
            <div>
                <strong>
                    <i class="fas fa-check"></i> Attualmente nessun documento allegato
                </strong>
            </div>

        </div>
    </div>
</div>
{{-- /Async  attach--}}