<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>


<script type="text/javascript">

    <?php
    if (empty($attId)) {
        $attId = '';
    }
    ?>

    /**
     * Array contenente tutti gli allegati con tutte le relative informazioni
     * @type {*[]}
     */
    if (typeof attachs == 'undefined') {
        var attachs = [];
    }

    if (typeof isUpdate == 'undefined') {
        var isUpdate = [];
    }

    if (typeof attId == 'undefined') {
        var attId = '';
    }

    if (typeof fileBuffer == 'undefined') {
        var fileBuffer = [];
        // var fileBuffer = new DataTransfer();
    }

    if (typeof tmpDone == 'undefined') {
        var tmpDone = [];
    }

    if (typeof tmpFiles == 'undefined') {
        var tmpFiles = new DataTransfer();
    }

    $(document).ready(function () {
        attId = '<?php echo $attId; ?>';
        attachs[attId] = {{ !empty($listAttach) && is_array($listAttach) ? json_encode($listAttach) : json_encode([]) }};

        /**
         * Se siamo in update, controlliamo se sono già presenti degli allegati associati al record
         */
        isUpdate[attId] = {{ !empty($listAttach) && is_array($listAttach) ? 'true' : 'false' }};

        @if (!empty($listAttach))
        let _fileBuffer = new DataTransfer();

        for (let i = 0; i < attachs[attId].length; ++i) {
            let attach = attachs[attId][i].file;

            let file = new File([''], attach.name, {
                type: attach.type,
            });
            Object.defineProperty(file, 'size', {
                value: (attach.size * 1024)
            });
            _fileBuffer.items.add(file);
        }

        document.getElementById(`attach_files${attId}`).files = _fileBuffer.files;
        buildHtmlFiles(isUpdate[attId], attId);
        @endif

        $(`#attach_files${attId}`).change(function () {
            let attId = $(this).data('attid');

            let files = document.getElementById(`attach_files${attId}`).files;

            if (files.length >= 1) {

                // let layer = $(this).parent().parent().parent().next();
                // let className = layer.attr('class');

                // if (className !== 'form-group') {
                //     layer.remove();
                // }

                let html = '';
                let i = 1;

                html +=
                    '<div class="form-group"><div id="list_file_info_attachs' + attId + '" style="border: 1px solid #CED4D4; padding: 0.5rem 0.8rem; border-radius: 0.3rem; background-color: #F2F2F2">';
                $(files).each(function (index, value) {
                    html += '<div style="margin: .2rem auto;">';
                    html +=
                        '<span class="elementBgColor" style="font-size:84%; border-radius: 50%; color: #fff; display: inline-block; padding-left: 6px; padding-right: 6px; text-align: center;" >' +
                        (index + 1) + '</span> ';
                    html += ' &nbsp; ' + ellipsisify(value.name, 20, 35, '.....') +
                        ' &nbsp;  <em style="color:#676767">(' + getReadableFileSizeString(value
                            .size) + ')</em>';
                    html += '</div>';
                    i++;
                });
                html += '</div></div>';

                // if (html.length >= 1) {
                //     layer.html(html);
                // }

                $(`#attach_info_after_upload${attId}`).empty().html(html);

            }

        });

        /**
         * Apro la finestra modale degli allegati al click sul pulsante "Aggiungi allegati"
         */
        $('.modal_attach_file').bind('click', function () {
            let modalId = $(this).data('modal-id');

            $(`#attach_files${modalId}`).val(null);
            $(`#select_name${modalId}`).hide();
            if ($('body').find(`#list_file_info_attachs${modalId}`).length >= 1) {
                $('body').find(`#list_file_info_attachs${modalId}`).remove();
            }
            $(`#modal_attach_file_layer${modalId}`).modal('show');
        });

        /**
         * Al click sul pulsante Aggiungi allegati della finestra modale "Gestione Allegati"
         * Allego i files nella tabella e chiudo la finestra modale
         */
        $('#add_html_attach_file<?php echo $attId ?>').bind('click', function (e) {
            e.preventDefault();
            let attTabId = $(this).data('att_tab_id');

            if ($(`#attach_files${attTabId}`)[0].files.length == 0) {

                alert('Non hai selezionato alcun file da allegare!')

            } else {

                //Informazioni inserite per gli allegati
                let label = $(`#attach_name${attTabId}`).val();
                let omissis = $(`#omissis_privacy${attTabId}`).val();
                let public = $(`#active_attach${attTabId}`).val();
                let files = $(`#attach_files${attTabId}`).get(0).files;
                let fileBuffer = new DataTransfer();
                let tempData = {};

                //Per ogni file caricato, inserisco le relative informazioni in un array
                $(files).each(function (index, value) {

                    attachs[attTabId].push({
                        label,
                        omissis,
                        public,
                        'file': value
                    });

                });

                //Svuoto il campo di input dei files
                $(`#attach_files${attTabId}`).get(0).files = null;
                // $('#btn_num_attahs').html(attachFiles.length);

                //Chiudo il modale
                $(`#modal_attach_file_layer${attTabId}`).modal('hide');

                //Alla chiusura del modale resetto il campo dell'etichetta selezionata in precedenza
                $(`#attach_name${attTabId}`).val('Allegato').prop('disabled', false);
                $(`#clear_attach_name${attTabId}`).hide();
                $(`#select_name${attTabId}`).show();

                buildHtmlFiles(isUpdate[attId], attTabId);
            }
        });

        /**
         * Al click sul pulsante "Seleziona nome" nel modale "Gestione Allegati"
         * Viasualizzo al lista delle etichetta da assegnare agli allegati
         */
        $('.select_default_name').bind('click', function () {
            let attId = $(this).data('attid');
            $(`#select_name${attId}`).show();
        });

        /**
         * Al click sul pulsante "seleziona"
         * Prelevo il nome dell'etichetta da assegnare all'allegato
         */
        $('.btn_select_label').bind('click', function (e) {
            let attId = $(this).data('attid');
            e.preventDefault();
            $(`#attach_name${attId}`).val($(this).prev().text()).prop('disabled', true);
            $(`#clear_attach_name${attId}`).show();
            $(`#select_name${attId}`).hide();
        });

        /**
         * Pulisco il campo di input "Nome Allegato", contenente il nome di etichetta selezionato nella select
         *
         */
        $('.clear_attach_name').bind('click', function () {
            $(this).hide();
            let attId = $(this).data('attid');
            $(`#attach_name${attId}`).prop('disabled', false).val('Allegato');
        });

        $('[data-toggle="tooltip"]').tooltip({
            trigger: 'hover'
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

                    });
                }
            });

        /**
         * Invoco la funzione per il cambio dello stato delle etichette
         */
        changeLabel();
    });

    /**
     * Funzione che aggiorna il campo di input di tipo File prima di inviarlo al submit del form
     */
    function initFiles<?php echo $attId ?>(attId, removedIndex) {

        if (fileBuffer.indexOf(attId) == -1) {
            fileBuffer[attId] = new DataTransfer();
        }

        if (isUpdate[attId]) {

            for (let i = 0; i < attachs[attId].length; ++i) {
                let attach = attachs[attId][i].file;

                if (attachs[attId][i].hasOwnProperty("id")) {

                    let file = new File([''], attach.name, {
                        size: (attach.size * 1024),
                        type: attach.type,
                    });

                    fileBuffer[attId].items.add(file);

                } else {

                    fileBuffer[attId].items.add(attach);

                }
            }

        } else {
            $(attachs[attId]).each(function (index, value) {
                fileBuffer[attId].items.add(value.file);
            });
        }

        document.getElementById(`attach_files${attId}`).files = fileBuffer[attId].files;
    }

    /**
     * Funzione che ha il compito di rimuovere un singolo file nella tabella degli allegati.
     */
    function removeFile(index, attTabId) {

        var fileBuffer = new DataTransfer();

        if (isUpdate[attTabId]) {

            for (let i = 0; i < attachs[attTabId].length; i++) {

                if (index !== i) {
                    let attach = attachs[attTabId][i].file;

                    let file = new File([''], attach.name, {
                        type: attach.type,
                    });
                    Object.defineProperty(file, 'size', {
                        value: (attach.size * 1024)
                    });
                    fileBuffer.items.add(file);
                }

            }

        } else {

            for (let i = 0; i < attachs[attTabId].length; i++) {
                if (index !== i) {
                    fileBuffer.items.add(attachs[attTabId][i].file);
                }
            }

        }

        attachs[attTabId].splice(index, 1);
        document.getElementById(`attach_files${attTabId}`).files = fileBuffer.files;

        $('#btn_num_attahs').html(attachs[attTabId].length);
        buildHtmlFiles(isUpdate[attTabId], attTabId, index);
    }

    /**
     * Funzione che va a riordinare gli indici dei file nel momento in cui si avvia la funzione
     * di spostamento di ordine dei file nella tabella degli allegati
     */
    function reorderFile<?php echo $attId ?>(items, attTabId) {
        let tmpAttachs = [];

        for (let i = 0; i < items.length; i++) {
            tmpAttachs.push({
                ...(attachs[attTabId][items[i]].hasOwnProperty("id") && {
                    id: attachs[attTabId][items[i]].id
                }),
                'label': attachs[attTabId][items[i]].label,
                'omissis': attachs[attTabId][items[i]].omissis,
                'public': attachs[attTabId][items[i]].public,
                'file': attachs[attTabId][items[i]].file
            });
        }

        attachs[attTabId] = [];
        attachs[attTabId] = tmpAttachs.slice();
    }

    /**
     * Funzione che ha il compito di cambiare l'etichetta dell'allegato nel momento in si aggiunge
     * un nuovo file dalla finestra modale di aggiunta allegato.
     */
    function changeLabel() {
        $('.link-change-label').bind('click', function (e) {
            e.preventDefault();
            let selectorText = '#label_attach_' + $('#sto_row_id').val();
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

        let args = arguments;
        let attTabId = args[1];
        let removedIndex = args[2] ?? null;

        let html = '';

        if (attachs[attTabId] !== null && attachs[attTabId].length > 0) {

            $(`#no_attach${attTabId}`).hide();

            html += 'Allegati';
            html += `<div class="table-responsive" id="${attTabId}">`;
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
            html += `<tbody data-attTabId="${attTabId}">`;

            for (var i = 0; i < attachs[attTabId].length; i++) {

                let item = attachs[attTabId][i];

                /**
                 * Se omissis è selezionata, imposto la select sulla singola riga a "selezionata"
                 */
                let omissisTrue = parseInt(item.omissis) == 1 ? ' selected="selected" ' : '';

                /**
                 * Se omissis NON è selezionato, imposto la select sulla singola riga a "NON selezionata"
                 */
                let omissisFalse = parseInt(item.omissis) == 0 ? ' selected="selected" ' : '';

                /**
                 * Se lo stato di pubblicazione è selezionato, imposto la select sulla singola riga a "selezionata"
                 */
                let publishTrue = parseInt(item.public) == 1 ? ' selected="selected" ' : '';

                /**
                 * Se lo stato di pubblicazione NON è selezionato, imposto la select sulla singola riga a "NON selezionato"
                 */
                let publishFalse = parseInt(item.public) == 0 ? ' selected="selected" ' : '';

                /**
                 * In fase di edit, assegnamo l'id del record dell'allegato
                 */
                let id = (item.id != null) ? item.id : null;

                /**
                 * Creo la tabella degli allegati
                 */
                html += '<tr data-id=' + i + ' id="item-' + i + '">';
                html += '<td class="tt text-center"><i class="fas fa-bars"></i></td>';
                html += '<td>';
                html += '<i class="fas fa-paperclip cursor-ns-resize "></i> &nbsp; ';
                html += item.file.name.trim() + " <small> (" + getReadableFileSizeString(item.file.size, args[0]) +
                    ") &nbsp; " + item.file.type.trim() + "</small>";
                html += '</td>';
                html += '<td class="no-drag">';
                html += '<div class="w-100" data-id="' + i + '">';
                html += '<input style="width:90%" type="text" value="' + item.label.trim() +
                    `" name="label_attach${attTabId}[]" id="label_attach_${i}" class="label_attach">`;
                html += `<input type="hidden" name="attach_id${attTabId}[]" value="${id}" id="attach_id_${i}">`;
                html += `<input type="hidden" name="bdncp_cat${attTabId}[]" value="${attTabId}" id="attach_id_${i}">`;
                html += '<button type="button" value="' + i +
                    '" data-toggle="tooltip" data-placement="right" title="Cambia etichetta" class="btn change-label" style="width:10%"><i class="fas fa-tag"></i></button>';
                html += '</div>';
                html += '</td>';
                html += '<td class="no-drag">';
                html += '<div class="" data-id="' + i + '">';
                html += `<select class="omissis-select" name="omissis${attTabId}[]" id="omissis_${i}" class="w-100">`;
                html += '<option value="1"' + omissisTrue + '>Si</option>';
                html += '<option value="0"' + omissisFalse + '>No</option>';
                html += '</select>';
                html += '</div>';
                html += '</td>';
                html += '<td class="no-drag">';
                html += '<div class="" data-id="' + i + '">';
                html += `<select class="public-select" name="publish${attTabId}[]"  id="publish_${i}" class="w-100">`;
                html += '<option value="1"' + publishTrue + '>Pubblico</option>';
                html += '<option value="0"' + publishFalse + '>Nascosto</option>';
                html += '</select>';
                html += '</div>';
                html += '</td>';
                html += '<td class="text-center no-drag">';
                html += `<button style="color:#DC3545;" type="button" onclick="removeFile(${i}, '${attTabId}')" class="btn" data-attTabId = "${attTabId}">`;
                html += '<i class="fas fa-trash"></i>';
                html += '</button>';
                html += '</td>';
            }
            html += '</tbody>';
            html += '</table>';
            html += '</div>';
        } else {
            $(`#no_attach${attTabId}`).show();
        }

        $('#btn_num_attahs').html(attachs[attTabId].length);

        // Appendo il codice HTML generato per la tabella dinamica della lista degli allegati
        $(`#list_last_insert_files${attTabId}`).empty().append(html).show();
        $('#attach_name').val('Allegato');

        /**
         * Trascinamento e ordinamewnto di posizione degli allegati nella tabella
         */
        $('tbody').sortable({
            cancel: '.no-drag',
            revert: true,
            update: function (event, ui) {
                attTabId = $(this).data('atttabid');
                let data = $(this).sortable('toArray');
                let i = 0;
                let items = [];
                data.forEach(function (key) {
                    items.push(parseInt(key.replaceAll('item-', '')));
                })
                $('tr').each(function (index) {
                    $(this).attr('id', 'item-' + String(index - 1));
                    $(this).attr('data-id', index - 1);
                    $(this).attr('name', 'attach_id' + attTabId + '[' + String(index - 1) + ']');
                });


                /**
                 * Rirdino la posizione e visualizzazione degli
                 */
                reorderFile<?php echo $attId ?>(items, attTabId);

                initFiles<?php echo $attId ?>(attTabId, removedIndex);
                changeOmissisAndPublicAndKeyUp();
                buildHtmlFiles(isUpdate[attTabId], attTabId);

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

        initFiles<?php echo $attId ?>(attTabId, removedIndex);
        changeOmissisAndPublicAndKeyUp();
    }

    function changeOmissisAndPublicAndKeyUp() {
        $('.omissis-select, .public-select').change(function () {
            let type = $(this).attr('class') == 'omissis-select' ? 'omissis' : 'public';
            let itemId = $(this).parent().parent().parent().attr('data-id');
            let itemValue = $(this).val();
            attachs[attId][itemId][type] = itemValue;
        });

        $(".label_attach").keyup(function () {
            let itemId = $(this).parent().parent().parent().attr('data-id');
            let itemValue = $(this).val();
            attachs[attId][itemId].label = itemValue;
        });
    }
</script>

{{-- Modale allegati --}}
<div class="modal fade" id="modal_attach_file_layer<?php echo $attId;?>" tabindex="-1" role="dialog"
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
                            'value' => 'Allegato',
                            'placeholder' => 'Nome allegato',
                            'id' => 'attach_name'.$attId,
                            'class' => 'form-control attach_name',
                        ]) }}

                        <span class="input-group-append">
                            <button type="button" class="btn btn-primary btn-flat select_default_name"
                                    data-attID="<?php echo $attId;?>">
                                Seleziona nome
                            </button>
                        </span>
                        <a class="btn btn-flat btn-danger clear_attach_name" id="clear_attach_name<?php echo $attId;?>"
                           style="display: none;" data-attID="<?php echo $attId;?>">
                            <i class="fas fa-times"></i>
                        </a>
                    </div>
                    {{-- Campo nascosto a livello di CSS --}}
                    <div id="select_name<?php echo $attId;?>" class="select_name">
                        <ul>
                            @if (!empty($labels))
                                @foreach ($labels as $l)
                                    <li>
                                        <span><i class="fas fa-caret-right"></i> {{ trim($l['label']['name']) }}</span>
                                        <a href="#!" class="btn btn-outline-primary btn-xs btn_select_label"
                                           data-attId="<?php echo $attId;?>">
                                            Seleziona
                                        </a>
                                    </li>
                                @endforeach
                            @else
                                <li>
                                    <span><i class="fas fa-caret-right"></i> Allegato</span>
                                    <a href="#!" class="btn btn-outline-primary btn-xs btn_select_label"
                                       data-attId="<?php echo $attId;?>">
                                        Seleziona
                                    </a>
                                </li>
                            @endif
                        </ul>
                    </div>
                    {{-- / Campo nascosto a livello di CSS --}}
                </div>

                <div class="form-group">
                    <label for="attach_files<?php echo $attId;?>">Allegato/i:</label>
                    <div class="input-group">
                        <div class="custom-file">
                            {{ form_upload([
                                'name' => 'attach_files'.$attId.'[]',
                                'value' => null,
                                'multiple' => 'multiple',
                                'placeholder' => 'Allegato',
                                'id' => 'attach_files'.$attId,
                                'class' => 'custom-file-input attach_files form-control-sm',
                                'aria-describedby' => 'input_group_attach_files',
                                'data-attId' => $attId
                            ]) }}
                            <label class="custom-file-label" for="input_group_attach_files">Carica
                                allegati</label>
                        </div>
                    </div>
                </div>

                <div id="attach_info_after_upload<?php echo $attId?>"></div>


                <div class="form-group">
                    <label for="omissis_privacy_attach">Omissis: (Privacy, il file non verrà
                        indicizzato)</label>
                    {{ form_dropdown(
                        'omissis_privacy_attach',
                        [
                            1 => 'Non indicizzare allegato',
                            0 => 'Indicizza allegato',
                        ],
                        0,
                        'class="form-control form-control-sm" id="omissis_privacy'.$attId.'"',
                    ) }}
                </div>

                <div class="form-group">
                    <label for="active_attach">Nascondi elemento: (Il file non verrà
                        pubblicato)</label>
                    {{ form_dropdown(
                        'active_attach',
                        [
                            1 => 'Pubblico',
                            0 => 'Nascosto',
                        ],
                        1,
                        'class="form-control form-control-sm" id="active_attach'.$attId.'"',
                    ) }}
                </div>

            </div>
            <div class="modal-footer">
                <button type="button" class="btn btn-secondary" data-dismiss="modal">
                    <i class="fas fa-times"></i>{{ nbs(2) }}Chiudi
                </button>
                <button type="button" id="add_html_attach_file<?php echo $attId;?>"
                        data-att_tab_id="<?php echo $attId;?>" class="btn btn-primary add_html_attach_file">
                    <i class="fas fa-file-upload"></i>{{ nbs(2) }}Aggiungi allegati
                </button>
            </div>
        </div>
    </div>
</div>
{{-- /Modale allegati --}}

{{-- Modale labeling --}}
<div class="modal fade" id="modal_attach_label" tabindex="-1" role="dialog" aria-labelledby="modal_attach_label"
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
                    @if (!empty($labels))
                        @foreach ($labels as $l)
                            <li class="list-group-item d-flex justify-content-between align-items-center">
                                <span>
                                    <i class="fas fa-caret-right"></i> {{ trim($l['label']['name']) }}
                                    {{ nbs(2) }}
                                </span>
                                <a class="btn btn-outline-primary btn-xs link-change-label" href="#!">Seleziona</a>
                            </li>
                        @endforeach
                    @else
                        <li class="list-group-item d-flex justify-content-between align-items-center">
                            <span>
                                <i class="fas fa-caret-right"></i> Allegato {{ nbs(2) }}
                            </span>
                            <a class="btn btn-outline-primary btn-xs link-change-label" href="#!">Seleziona</a>
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

{{-- Async  attach --}}
<div class="col-12">
    <div class="row">
        <div class="col-md-12" id="attach_module<?php echo $attId;?>">
            @if(!empty($noSeparator))
                <div class="custom-separator white">
                    <h5><i class="fas fa-link"></i> Allegati {{ ($title ?? '')  }}</h5>
                    <button type="button" id="modal_attach_file<?php echo $attId;?>"
                            class="btn btn-primary btn-sm modal_attach_file" data-modal-id="<?php echo $attId;?>">
                        <i class="fas fa-plus"></i> Aggiungi allegati
                    </button>
                </div>
            @else
                <div class="custom-separator"><h5><i class="fas fa-paperclip"></i> Allegati </h5>
                    <button type="button" id="modal_attach_file<?php echo $attId;?>"
                            class="btn btn-primary btn-sm modal_attach_file" data-modal-id="<?php echo $attId;?>">
                        <i class="fas fa-plus"></i> Aggiungi allegati
                    </button>
                </div>
            @endif
        </div>
        <div class="col-12 px-0">
            @if(in_array((string)uri()->segment(2, 0),['assignment', 'personnel', 'grant', 'contests-act', 'measure']))
                <div class="col-md-5 mt-2">
                    <i class="fas fa-info-circle"></i>
                    Attenzione ai dati personali e/o sensibili,
                    <a data-toggle="collapse" href="#privateDataInfo" role="button" aria-expanded="false"
                       aria-controls="privateDataInfo">
                        <strong>clicca qui</strong>
                    </a>
                    per maggiori informazioni.
                </div>

                <div class="col-md-1">
                </div>

            @else
                <div class="col-md-6">
                </div>
            @endif


            <div class="collapse mt-4 col-md-12" id="privateDataInfo">
                <div class="alert alert-info">
                    <span>Le informazioni fornite dal <strong>soggetto interessato sono di esclusiva responsabilità dello stesso, il quale</strong>
                        <i>deve preventivamente fornire il consenso ed essere informato sulla pubblicazione dei dati.</i>
                    </span>
                    <p>Si precisa che:</p>
                    <ul>
                        <li>
                            <span>costituiscono <b>dati personali</b> le informazioni che identificano o rendono identificabile, direttamente o indirettamente, una persona fisica e che possono fornire informazioni sulle sue caratteristiche.</span>
                            <ul>
                                <li>
                                    <span>Non vanno pertanto <i>mai pubblicate copie di documenti di identità o i loro numeri identificativi</i>, e limitata la pubblicazione di codici fiscali, indirizzi e telefoni privati.</span>
                                </li>
                            </ul>
                        </li>
                        </br>
                        <li>
                            <span>costituiscono <b>dati sensibili</b> le informazioni che rivelano le convinzioni religiose, filosofiche, politiche, sindacali, salute, genetici, biometrici, penali e quelli relativi all'orientamento sessuale.</span>
                            <ul>
                                <li>
                                    <span><i>Quest'ultima categoria di dati non va mai resa pubblica.</i></span>
                                </li>
                            </ul>
                        </li>
                    </ul>
                </div>
            </div>


            <div class="col-md-12 mt-3">
                <div id="list_last_insert_files<?php echo $attId;?>"></div>
            </div>

            <div class="col-md-12 mt-3 mb-3" id="no_attach<?php echo $attId;?>">
                <div>
                    <div class="alert alert-warning" style="background-color: #f6edba;">
                        <i class="fas fa-info-circle"></i> Attualmente nessun documento allegato
                    </div>
                </div>

            </div>
        </div>
    </div>
</div>
{{-- /Async  attach --}}
