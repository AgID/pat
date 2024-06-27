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
     * Metodo che ricarica il datatable utilizzando i filtri
     */
    function filterRecord(dtable) {

        $('.dataTablesFilter').on('change', function (e) {
            e.preventDefault();
            e.stopImmediatePropagation();
            filter[e.target.id] = e.target.value;
            if(filter !== undefined){
                dtable.context[0].ajax.data = {"filter":filter};
                dtable.ajax.reload();
            }
        })

        $('#filters').on('shown.bs.collapse', function(){
            $('#showFilter').html('<i class="fas fa-minus"></i> &nbsp Nascondi Filtri');
        });
        $('#filters').on('hidden.bs.collapse', function(){
            $('#showFilter').html('<i class="fas fa-plus"></i> &nbsp Mostra Filtri');
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
     * Funzione che mostra l'avviso che si deve selezionare un ente prima di poter aggiungere un nuovo record quando
     * si è super admin
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

    //Bug con il file manager, compaiono i tooltip grandi su ogno voce del menu
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

    $(document).ready(function () {

        $('.a-num-class').each(function (i, e) {
            $(this).autoNumeric({aSep: '.', aDec: ',', vMax: '999999999999.99'});
        });

        {{-- Auto Close navbar : .1 second--}}
        /*setTimeout(function () {
            $('body').addClass('sidebar-collapse');
        }, 100);*/

        {{-- Per far rimanere aperta la sezione corrente nella barra di navigazione sinistra --}}
        let url = window.location;
        let element = $('ul.sidebar-menu a').filter(function () {
            return this.href == url || url.href.indexOf(this.href) == 0;
        }).addClass('active');
        if (element.parent().is('li')) {
            element.parent().parent().parent('li').addClass('menu-open')
        }

        {{-- IsSuperAdmin: Lista Istitutions --}}
        @if(isSuperAdmin(true)===true)

        let $changeInstitutions = $('._cinst');

        $changeInstitutions.select2({
            allowClear: true,
            ajax: {
                url: '{{  siteUrl('admin/system/list/institutions') }}',
                type: "get",
                dataType: 'json',
                delay: 250,
                data: function (data) {
                    return {
                        searchTerm: data.term
                    };
                },
                processResults: function (response) {
                    return {
                        results: response
                    };
                }
            }
        })

        $changeInstitutions.on('change', function (e) {

            let id = $(this).val();

            $.ajax({
                url: '{{ siteUrl('admin/system/change/institutions') }}',
                data: {
                    id: id
                },
                method: 'GET',
                dataType: 'json',
                beforeSend: function () {
                    $('.grow-change-institutions').show();
                },
                success: function (dataResp) {
                    window.location.href = '{{ siteUrl('admin/dashboard') }}';
                },
                error: function (error) {
                    alert('Attenzione, la funzionalità richiesta non è momentaneamente disponibile');
                }
            });

        });

        $('#__restore_institute_default_').bind('click', function (e) {
            e.preventDefault();

            let id = $(this).attr('href');

            $.ajax({
                url: '{{ siteUrl('admin/system/restore/institution') }}',
                data: {
                    id: id,
                },
                method: 'GET',
                dataType: 'json',
                beforeSend: function () {
                    $('#spinner-grow-sm-restore').css({'display': 'inline-block'});
                },
                success: function (dataResp) {
                    window.location.href = '{{ siteUrl('admin/dashboard') }}';
                },
                error: function (error) {
                    alert('Attenzione, la funzionalità richiesta non è momentaneamente disponibile');
                }
            });

        });

        $('#__current-administration_').bind('click', function (e) {
            e.preventDefault();

            let id = $(this).attr('href');

            $.ajax({
                url: '{{ siteUrl('admin/system/administrator/current') }}',
                data: {
                    id: id,
                },
                method: 'GET',
                dataType: 'json',
                beforeSend: function () {
                    $('#spinner-grow-sm-adminstator-current').css({'display': 'inline-block'});
                },
                success: function (dataResp) {
                    window.location.href = '{{ currentQueryStringUrl() }}';
                },
                error: function (error) {
                    alert('Attenzione, la funzionalità richiesta non è momentaneamente disponibile');
                }
            });

        });

        @endif

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

    })
</script>
