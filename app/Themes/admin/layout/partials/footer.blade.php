<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
<style>
    .icona {
        width: 18px;
        margin-right: 0.3rem;
        vertical-align: middle;
        display: inline-block;
    }
</style>

@if(empty($is_box))
    <footer class="main-footer">

        <div class="float-right d-none d-sm-inline">
            <a type="button" class="btn btn-primary btn-sm mr-2" href="https://www.isweb.it/gdpr/allpost24.html">
                <svg class="icona" id="Livello_1" data-name="Livello 1" xmlns="http://www.w3.org/2000/svg"
                     viewBox="0 0 459.81 510.24">
                    <defs>
                        <style>.cls-1 {
                                fill: #fff;
                            }</style>
                    </defs>
                    <path class="cls-1"
                          d="M233.55,510.24h-7c-1.82-.71-3.6-1.54-5.45-2.11-54.65-17-104-43.35-145.51-83.24C31.06,382.06,3.09,330.49,1.06,268-.88,208.33.44,148.52.46,88.76c0-6.67,4.88-11.5,11.54-12.13,4.13-.39,8.29-.4,12.44-.55,44.62-1.61,88-9.59,129.53-26.24,25.17-10.1,48.49-23.1,66-44.77,5.58-6.92,14.32-6.59,20.39-.05,4.29,4.62,8.29,9.56,13,13.71,22.65,19.94,49.82,31.32,78.2,40.08,36.85,11.37,74.66,16.89,113.21,17.6,10.76.2,15.05,4.8,15.05,15.37q0,81.67,0,163.36c-.08,59.46-21.28,110.76-60.7,154.75-36.3,40.5-81.18,68.46-131.41,88.28C256.45,502.59,244.93,506.24,233.55,510.24ZM179,229.45c-4.29,0-7.76,0-11.23,0-9.17.18-14.09,5-14.1,14.3q-.09,49.53,0,99.06c0,9.33,4.78,14.18,14.08,14.2q62.49.09,125,0c9,0,13.84-4.86,13.86-13.9q.12-49.78,0-99.57c0-9.14-5.07-13.93-14.33-14.08-3.57-.06-7.14,0-11,0,0-8.89.14-17,0-25.12-.73-34.25-31.37-58-64-49.7-22.57,5.74-37.87,25.89-38.2,50.41C178.87,213,179,220.9,179,229.45Z"/>
                    <path class="cls-1" d="M179.3,255.31H280.9v76H179.3Z"/>
                    <path class="cls-1"
                          d="M255.54,229.2H205c0-10.13-1-20.14.24-29.88,1.64-13.15,14.26-22,27.3-20.76a25.4,25.4,0,0,1,23,25.08C255.66,212.05,255.54,220.47,255.54,229.2Z"/>
                </svg>

                Documentazione GDPR
            </a>

            Versione: <?php echo config('pat_os_title', null, 'custom'); ?> <strong>{{ VERSION_PATOS }}</strong>
        </div>
        Applicativo sviluppato da <strong><a
                    href="https://isweb.it" target="_blank">ISWEB</a></strong>

        {{-- Inclusione di librerie esterne --}}


    </footer>
@endif
<div class="_layer_spinner_notify"></div>
<div class="_spinner_notify">
    <div class="rect1"></div>
    <div class="rect2"></div>
    <div class="rect3"></div>
    <div class="rect4"></div>
    <div class="rect5"></div>
    <h5>Attendere....</h5>
</div>

<script type="text/javascript">

    {{-- Cacnhe image logo--}}
    $('#draw_left_menu_patos').bind('click', function () {
        if ($('body').hasClass('sidebar-collapse')) {
            $("#header_logo_patos").attr("src", "{{ baseUrl('assets/admin/img/pat_logo_nero.png') }}");
        } else {
            $("#header_logo_patos").attr("src", "{{ baseUrl('assets/admin/img/pat_logo_nero_small.png') }}");
        }
    });

    /**
     * Funzione che converte KB in Byte
     */
    function kbToByte(num) {
        let convert = num * 1024;
        return Math.round(convert);
    }

    /**
     * Metodo che effettua la chiamata per ottenere la lista delle versioni presenti per l'elemento
     * selezionato da mostrare nel modale.
     */
    function getVersionsList(name = 'spinner') {
        $('.record-versioning').on('click', function (e) {
            e.preventDefault();
            $('#versioning-content').hide();
            $('#v-table').show();
            $('#compare').hide();
            $('#restore-buttons').removeClass('d-flex').addClass('d-none');
            let id = $(this).attr('data-id');
            let type = $(this).attr('data-type');
            let typeName = $(this).attr('data-name');
            let url = $(this).attr('data-url');

            $.ajax({
                type: 'GET',
                url: url,
                dataType: "JSON",
                data: {
                    id: id,
                    type: type
                },
                beforeSend: function () {
                    $(`#${name}`).show();
                    $('#versioning_modal').modal('show');
                },
                success: function (response) {
                    let resp = parseJson(response);
                    let versions = resp.data.versions;

                    // Metodi presenti sul modale del versioning
                    setVersions(versions, id, type, typeName);
                    compare();
                    $(`#${name}`).hide();
                    $('#versioning-content').show();

                },
                complete: function () {
                },
                error: function (jqXHR, status) {
                    let response = parseJson(jqXHR.responseText);
                    $(document).Toasts('create', {
                        class: 'bg-danger',
                        title: 'ATTENZIONE',
                        subtitle: 'Errori',
                        autohide: true,
                        delay: 5000,
                        body: response.error
                    });
                }
            });
        });

    }

    /**
     * Metodo che mostra il modale per l'archiviazione dell'elemento selezionato
     */
    function openArchiveModal(dtable) {
        $('.record-archive').on('click', function (e) {
            e.preventDefault();

            $('#archiving-content').hide();
            $('#spinner-loading').show();
            $('#archive_modal').modal('show', {
                name: $(this).data("name"),
                id: $(this).data("id"),
                type: $(this).data("type"),
                dtb: dtable
            });
        });
    }

    /**
     * Metodo che mostra il modale per i dati scp dell'elemento selezionato
     */
    function openScpModal(name = 'spinner') {
        $('.record-scp').on('click', function (e) {
            e.preventDefault();


            let id = $(this).attr('data-id');
            let url = $(this).attr('data-url');
            let type = $(this).attr('data-type');
            let one = $(this).attr('data-one');
            let flagMulti = false;


            if (checkedId.length > 1 && one === "0") {
                id = '';

                for (let indx = 0; indx < checkedId.length; indx++) {
                    if (id !== '') {
                        id = checkedId[indx] + ',' + id;
                    } else {
                        id = checkedId[indx];
                    }
                }
                url = url.replace('.html', '') + '-multi.html';
                flagMulti = true;

            } else if (checkedId.length === 1) {
                id = checkedId[0];
            }

            if (id !== undefined) {

                $.ajax({
                    type: 'GET',
                    url: url,
                    dataType: "JSON",
                    data: {
                        id: id,
                        type: type
                    },
                    beforeSend: function () {

                        $(`#${name}`).show();
                        $('#scp_table').hide();
                        $('#btn_send_scp').hide();
                        $('#scp_errors').hide();
                        $('#scp_modal').modal('show');
                        $(`#scp_send_result`).hide('fast');

                        if (type === 'measure') {
                            $('#list_measure').hide();
                        } else if (type === 'notices-acts') {
                            $('#list_notices_acts').hide();
                        } else {
                            $('#list_contests_acts').hide();
                        }
                    },
                    success: function (response) {
                        let resp = parseJson(response);
                        let scp = resp.data.scpdata;
                        let err = resp.data.scperror;
                        if (!flagMulti) {
                            setScp(scp, id, type, err);
                            $(`#${name}`).hide();
                        } else {
                            setScpMultiSelectTable(scp, id, type, err);
                        }

                    },
                    complete: function () {
                        if (flagMulti) {
                            $('#scp_table').show();
                            $(`#${name}`).hide();
                        }
                    },
                    error: function (jqXHR, status) {
                        let response = parseJson(jqXHR.responseText);
                        $(document).Toasts('create', {
                            class: 'bg-danger',
                            title: 'ATTENZIONE',
                            subtitle: 'Errori',
                            autohide: true,
                            delay: 5000,
                            body: response.error
                        });
                    }
                });

            } else {
                alert('Nessun elemento selezionato');
            }

        });
    }

    function createResponsibleSelect(edit, publishingResponsible) {

       let institutionId = document.getElementById('institution_id')?.value ?? '';

        $('#ajax_responsible').empty();
        $('#publishing_responsible_label').next().remove();
        responsible = null;

        responsible = $('#ajax_responsible').patOsAjaxPagination({
            url: config.personnel.url,
            textLoad: config.personnel.textLoad,
            selectedLabel: 'Responsabile di Pubblicazione Selezionato',
            footerTable: config.personnel.footerTable,
            classTable: config.personnel.classTable,
            hideShowTable: Boolean((edit === 'insert') ? 'true' : 'false'),
            hideTable: config.personnel.hideTable,
            showTable: config.personnel.showTable,
            search_placeholder: config.personnel.search_placeholder,
            setInputDataValue: '#input_responsible_pub',
            dataParams: {
                model: 2,
                institution_id: institutionId
            },
            columns: config.personnel.columns,
            action: {
                type: 'radio',
            },
            dataSource: config.personnel.dataSource,
            addRecord: config.personnel.addRecord,
            archived: config.personnel.archived,
            label: '#publishing_responsible_label'
        });

        if (publishingResponsible) {
            $('.patos_ajax_table_data_selected').show();
            responsible.patOsAjaxPagination.setValue(publishingResponsible.meta_value,
                ((publishingResponsible?.title) ? htmlEncode(publishingResponsible.title) + ' - ' : '') + htmlEncode(publishingResponsible?.full_name) + ' - ' + htmlEncode(publishingResponsible?.name) + ' - ' + ((publishingResponsible?.email) ? publishingResponsible?.email : 'N.D'), true);
        }
    }

    /**
     * Metodo che mostra il modale per i dati scp dell'elemento selezionato
     */
    function openLogModal() {
        $('.show-record-log').on('click', function (e) {
            e.preventDefault();

            let id = $(this).attr('data-id');
            let url = $(this).attr('data-url');
            let type = $(this).attr('data-type');

            if (id !== undefined) {

                $.ajax({
                    type: 'GET',
                    url: url,
                    dataType: "JSON",
                    data: {
                        id: id,
                        archive: type
                    },
                    beforeSend: function () {
                        $('#log-spinner').show();
                        $('#modal-log-body').hide();
                        $('#log_modal').modal('show');
                    },
                    success: function (response) {
                        let resp = parseJson(response);
                        let data = resp['data'];
                        setLog(data);
                    },
                    complete: function () {
                        $('#log-spinner').hide('fast');
                        $('#modal-log-body').show();
                    },
                    error: function (jqXHR, status) {
                        let response = parseJson(jqXHR.responseText);
                        $(document).Toasts('create', {
                            class: 'bg-danger',
                            title: 'ATTENZIONE',
                            subtitle: 'Errori',
                            autohide: true,
                            delay: 5000,
                            body: response.error
                        });
                    }
                });

            } else {
                alert('Nessun elemento selezionato');
            }

        });
    }



    function perlaPAModal() {

        $('.btn-perlapa').on('click', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();

            let id = $(this).attr('data-id');
            let action = $(this).attr('data-action');
            let typology = $(this).attr('data-typology') ?? 'assignment';

            const errori = [0,'0',"", " ", null, undefined];

            if (!errori.includes(id) && !errori.includes(action)) {

                $.ajax({
                    type: 'POST',
                    url: "{{baseUrl("/admin/perlapa/check")}}",
                    dataType: "JSON",
                    data: {
                        id: id,
                        action: action,
                        typology: typology
                    },
                    beforeSend: function () {
                        $('#perlapa-spinner').show();
                        $('#modal-perlapa-body').hide();
                        $('#perlapa_modal').modal('show');
                        $("#btn-do-action").hide();
                    },
                    success: function (response) {
                        let resp = parseJson(response);
                        setPerlapa(resp,id,action,typology);
                    },
                    complete: function () {
                    },
                    error: function (jqXHR, status) {
                        let response = parseJson(jqXHR.responseText);
                        $(document).Toasts('create', {
                            class: 'bg-danger',
                            title: 'ATTENZIONE',
                            subtitle: 'Impossibile procedere con l\'operazione.',
                            autohide: true,
                            delay: 5000,
                            body: response.error
                        });
                    }
                });

            } else {
                alert("Non è possibile effettuare l'operazione richiesta.");
            }

        });
    }


    /**
     * @description Funzione per la ricerca nei datatable
     * @param dtable
     */
    function searchDatatable(dtable) {
        let searchWait = 0;
        let searchWaitInterval;
        let previous = '';

        $('.dataTables_filter input')
            .unbind() // leave empty here
            .keyup('input', function(e){ //leave input
                let item = $(this);
                searchWait = 0;
                searchTerm = $(item).val();
                if(e.keyCode == 13) {
                    dtable.search(searchTerm).draw();
                }

                if(searchTerm.length >= 3) {
                    if(!searchWaitInterval) searchWaitInterval = setInterval(function(){
                        if(searchWait >= 3 ) {
                            clearInterval(searchWaitInterval);
                            searchWaitInterval = '';
                            dtable.search(searchTerm).draw();
                            searchWait = 0;
                        }
                        searchWait++;
                    },300);
                }

                if (searchTerm === "" && searchTerm !== previous) {
                    dtable.search("").draw();
                }

                previous = searchTerm;
                return;
            });
    }


    /**
     * Metodo apre il form per la modifica dell'elemento selezionato
     */
    function editRecord() {
        $('._record-edit').on('click', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            url = $(this).attr('href');
            window.location.href = url;
        });
    }



    /**
     * Metodo che il form per la duplicazione dell'elemento selezionato
     */
    function duplicateRecord() {
        $('._record-duplicate').on('click', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            url = $(this).attr('href');
            window.location.href = url;
        });
    }

    /**
     * Metodo che apre il form per la visualizzazione dell'elemento selezionato
     */
    function viewRecord() {
        $('._record-view').on('click', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            url = $(this).attr('href');
            window.location.href = url;
        });
    }

    /**
     * Metodo per la cancellazione dell'elemento selezionato
     */
    function deleteRecord() {
        $('.record-delete').on('click', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            url = $(this).attr('href');


            $.confirm({
                title: 'Attenzione:',
                content: 'Sei sicuro di voler eliminare questo elemento?',
                buttons: {
                    'Ok': function () {
                        window.location.href = url;
                    },
                    'Chiudi': function () {
                    }
                }
            });

        });
    }



    /**
     * Mostra l'avviso quando occorre selezionare un ente prima di aggiungere un record.
     */
    function selectInstitution() {
        $(".select-institution").click(function () {
            $("#_cinst").select2("open");
            $(document).Toasts('create', {
                class: 'bg-warning',
                title: 'ATTENZIONE',
                position: 'bottomRight',
                autohide: true,
                delay: 5000,
                body: 'Per aggiungere un nuovo record seleziona prima un ente!'
            });
        });
    }

    /**
     * Funzione che in fase di inserimento di un bando, se il cig inserito è già presente mostra l'alert dal quale
     * l'utente può scegliere se proseguire o meno
     *
     * @param cigs Eventuali cig già presenti
     */
    function checkIfCigsExist(cigs) {
        $.confirm({
            title: 'ATTENZIONE',
            type: 'dark',
            closeIcon: true,
            content: 'Il cig:  <strong>' + cigs + ' </strong> inserito è già presente.<br>Continuare comunque?',
            buttons: {
                //Continua con il salvataggio
                'Continua': function () {
                    $('#__ignore_cig').val(1);
                    $('#{{ @$formSettings['id'] }}').submit();
                },
                'Annulla': function () {
                    $('#btn_save').attr("disabled", false);
                }
            }
        });
    }

    {{-- Funzione per la schermata di caricamento --}}
    $(window).on('load', function () {
        setTimeout(function () {
            $("#loading-container").fadeOut(250, function () {
                $("#loading-container").remove();
            });
        }, 800);
    });

    function parseBool(str) {
        if (str.length == null) {
            return str == 1 ? true : false;
        } else {
            return str == "true" ? true : false;
        }
    }

    /**
     * Funzione che restituisce gli elementi selezionati nel datatable per le operazioni multiple
     *
     * @param $className
     * @returns {any[]}
     */
    function listValueByClass($className) {
        let ids = new Array();
        $($className).each(function (index) {
            if ($(this).is(':checked')) {
                ids.push($(this).val());
            }
        })
        return ids;
    }

    @if(uri()->segment(2) != 'file-archive')
    $(function () {
        $("body").tooltip({selector: '[data-toggle=tooltip]'});
    });

    @endif

    /**
     * Funzione che genera il toast con gli errori restituiti dal validatore nei form
     */
    function createValidatorFormErrorToast() {
        let args = arguments;

        let message = args[0];
        let duration = args[1];
        let subtitle = args[2];
        let autohide = args[3];

        if (message !== undefined && message.length > 0) {

            $(document).Toasts('create', {
                class: 'bg-danger error-toast',
                title: 'ATTENZIONE',
                subtitle: (subtitle !== undefined) ? subtitle : 'Validatore modulo',
                autohide: (autohide !== undefined) ? autohide : true,
                delay: (duration !== undefined) ? duration : 5000,
                body: message
            });

        }
    }

    /**
     * Funzione che genera il toast con gli errori restituiti dal validatore nei form
     */
    function createValidatorFormSuccessToast() {
        let args = arguments;

        let message = args[0];
        let title = args[1];
        let subtitle = args[2];

        $(document).Toasts('create', {
            class: 'bg-success succes-toast',
            title: title,
            subtitle: (subtitle !== undefined) ? subtitle : 'Salvataggio',
            autohide: true,
            delay: 2000,
            body: message
        });
    }

    /**
     * Controllo se sono arrivato in questa pagina dal versioning
     * Se si mostro la finestra di dialogo con il messaggio di info
     */
    function checkIfRestore() {
        @if(!empty($restore))
        setTimeout(function () {
            $.confirm({
                title: 'Ripristino versione effettuato con successo.',
                animation: 'zoom',
                type: 'dark',
                columnClass: 'medium',
                content: '<p>Puoi verificare e/o modificare ulteriormente le informazioni ripristinate.</p>' + '<p>Al termine delle operazioni ricordati di salvare i dati.</p>',
                buttons: {
                    'Ok': function () {
                    }
                }
            });
        }, 500);
        @endif
    }

    /**
     * Funzione per l'apertura del modale per creare un oggetto direttamente dall'interno di un form
     */
    function openModalForm() {
        {{-- Funzione che apre il modale per l'aggiunta di nuovi elementi direttamente all'interno del form --}}
        $('.open-modal').on('click', function (event) {
            event.preventDefault();
            event.stopPropagation();
            event.stopImmediatePropagation();

            $('#new-obj-box').hide();
            // if (name) {
            //     $(`#${name}`).show();
            // }
            $('#spinner').show();

            let url = this.getAttribute('data-url');
            $('#formModal').modal('show');
            $('#new-obj-box').attr('src', url);
            setTimeout(function () {
                // if (name) {
                //     $(`#${name}`).hide();
                // }
                $('#spinner').hide();
                $('#new-obj-box').show();
            }, 800);
        });
    }

    /**
     * Funzione eseguita alla chiusura del modale nei form.
     * Pulisce l'iframe.
     */
    function closeModalForm() {
        $("#formModal").on("hidden.bs.modal", function () {
            $('#new-obj-box').hide();
            $('#new-obj-box').attr('src', '');
            $('#new-obj-box').empty();
        })
    }

    function republish(){
        $(".btn-republish").on('click', function (e){
            e.preventDefault();
            e.stopImmediatePropagation();
            let id = $(this).data('id')
            let archive = "<?php echo uri()->segment(2); ?>";

            const wrongIds = [null,undefined,0,'',' ','0'];

            if (!wrongIds.includes(id)) {
                $.confirm({
                    title: "<h4 style='color:var(--colore1)'><strong>Attenzione:</strong></h4>",
                    content: "<h5><strong>Sei sicuro di voler procedere?</strong></h5><p> Cliccando su conferma l'elemento verrà impostato come 'pubblicato' e la <strong>data di fine pubblicazione</strong> impostata al 1 gennaio {{ (string)(date("Y")+1) }}.</p>",
                    containerFluid: true,
                    buttons: {
                        'Conferma': {
                            btnClass: "btn-green",
                            action: function () {
                                $.ajax({
                                    type: 'GET',
                                    url: '<?php echo baseUrl('admin/'); ?>'+archive+'/republish-record',
                                    dataType: "JSON",
                                    data: {
                                        "id": id,
                                        "archive": archive
                                    },
                                    beforeSend: function () {
                                        $(".btn-"+id).addClass('disabled');
                                    },
                                    success: function (response) {
                                        $(".unfix-"+id).remove();

                                        $(document).Toasts('create', {
                                            class: 'bg-success',
                                            title: 'Ripubblicazione',
                                            subtitle: '',
                                            autohide: true,
                                            delay: 3000,
                                            body: 'Operazione effettuata con successo'
                                        });
                                    },
                                    error: function (jqXHR, status) {
                                        $(".btn-"+id).removeClass('disabled');
                                        $(document).Toasts('create', {
                                            class: 'bg-danger',
                                            title: 'Ripubblicazione',
                                            subtitle: 'Errore',
                                            autohide: true,
                                            delay: 3000,
                                            body: 'Operazione non effettuata'
                                        });
                                    }
                                });
                            },
                        },
                        'Chiudi': {
                            btnClass: 'btn-red'
                        }
                    }
                });

            }else{
                $(".btn-republish-"+id).addClass('disabled');
            }
        });
    }

    function askEdit() {
        $(".ask_edit").on('click', function (e){
            e.preventDefault();
            let id = $(this).attr('data-id');
            showAskModal(id);
        });
    }


    $(document).ready(function () {


        $('.a-num-class').each(function (i, e) {
            $(this).autoNumeric({aSep: '.', aDec: ',', vMax: '999999999999.99'});
        });

        {{-- Auto Close navbar : .1 second--}}
        /*setTimeout(function () {
            $('body').addClass('sidebar-collapse');
        }, 100);*/

        $('.openEconnectModal').on('click', function (e) {
            e.preventDefault();
            e.stopPropagation();

           $('#updateBanner').modal('show')
        });

        {{-- Per far rimanere aperta la sezione corrente nella barra di navigazione sinistra --}}
        let url = window.location;
        let element = $('ul.sidebar-menu a').filter(function () {
            return this.href == url || url.href.indexOf(this.href) == 0;
        }).addClass('active');
        if (element.parent().is('li')) {
            element.parent().parent().parent('li').addClass('menu-open')
        }



        selectInstitution();

        /**
         * Funzione per la cancellazione degli elementi selezionati nei datatable
         * Cancellazione multipla di più elementi
         */
        $('#delete-items').click('click', function () {
            let results = listValueByClass('.checkbox_item');
            let url = $(this).attr('data-url');

            if (results.length > 0) {
                let ids = results.join();

                $.confirm({
                    title: 'Attenzione:',
                    content: 'Sei sicuro di voler eliminare le voci selezionate?',
                    buttons: {
                        'Ok': function () {
                            window.location.href = url + '?ids=' + ids;
                        },
                        'Chiudi': function () {
                        }
                    }
                });

            } else {

                $.confirm({
                    title: 'Attenzione:',
                    content: 'Nessuna voce selezionata!',
                    buttons: {
                        'Chiudi': function () {
                        }
                    }
                });

            }
        });


        if(typeof CKEDITOR !== 'undefined'){
            if(CKEDITOR.config.extraPlugins === ""){
                CKEDITOR.config.extraPlugins = "wordcount,notification";
            }else{
                CKEDITOR.config.extraPlugins = CKEDITOR.config.extraPlugins+",wordcount,notification";
            }

            CKEDITOR.config.wordcount = {
                showCharCount: true,
                countSpacesAsChars: true,
                countHTML: true
            };
        }

    })
</script>

