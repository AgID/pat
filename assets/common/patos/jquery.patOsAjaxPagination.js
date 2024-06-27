/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

/**
 * AJAX Pagination Data
 * usage:
 *         -- Paginazione per le select singole --
 *       var table = $('#ajax_pagination').patOsAjaxPagination({
 *             'url': '{{siteUrl("/admin/async/get/data/contests/act")}}',
 *             'textLoad': 'Attendere, caricamento bandi di gara in corso..',
 *             'footerTable': true,
 *             'classTable': 'table table-hover table-bordered table-striped table-sm',
 *             'dataParams': {
 *                 model: 41,
 *                 institution_id: institutionId
 *             },
 *             columns: [
 *                 'OGGETTO',
 *                 'CIG'
 *             ],
 *             action: {
 *                 type: 'radio|checkbox'
 *             },
 *             dataSource: [
 *                 'id', 'object', 'cig'
 *             ]
 *         });
 *
 *         table.patOsAjaxPagination.setValue(2,'Altro');
 *         console.log(table.patOsAjaxPagination.getValue());
 *
 *         -- Paginazione per le select singole --
 */
(function ($) {
    $.fn.patOsAjaxPagination = function (options) {

        var html = '';

        var uuid = _getRandomInt(10000);
        var _this = $(this);
        var _textSearchId = 'search_' + uuid;
        var _selectorLoading = 'tbl_' + uuid;
        var valueData = {};
        valueData[uuid] = {};
        var config = {

            /**
             * Appende in un campo di input i valori selezionati.
             */
            'setInputDataValue': null,

            /**
             * Se nel campo dei valori selezionati deve appendere solo gli id oppure anche il label
             */
            'setInputDataValueOnlyId': true,

            /**
             * Classe nella tabella
             */
            'classTable': 'table',

            /**
             * Numeri di righe per pagine
             */
            'numPerPage': 5,

            /**
             * Classe nella tabella
             */
            'classInputSearch': 'table',

            /**
             * Classe nell'intestazione
             */
            'classHead': 'thead-dark',

            /**
             * Creazione colonne tabella
             */
            'columns': [],

            /**
             * Action Ajax
             */
            'type': 'GET',

            /**
             * Url chiama AJAX
             */
            'url': null,

            /**
             * Tipi di dati di ritorno
             */
            'dataType': 'JSON',

            /**
             * Testo precaricamento di default
             */
            'textLoad': 'Attendere...',

            /**
             * Label per il contenitore degli elementi selezionati
             */
            'selectedLabel': 'Elementi selezionati',

            /**
             * Id della label della select,  per indicare dove inserire il pulsante Mostra/Nascondi tabella
             */
            'label': '',

            /**
             * Valori di default
             */
            'setValues': [],

            /**
             * Parametri di stampare in ajax.
             */
            'dataParams': [],

            /**
             * Eventuali campi di tipo data da formattare
             */
            'dateFormat': [],

            /**
             * Indica se sono in un form di sola lettura, quindi mostro solo gli elementi selezionati e nascondo la tabella
             */
            'readOnly': false,

            /**
             * Id dell'elemento da evidenziare in rosso per gli errori di validazione
             */
            'errorFields': '',

            /**
             * Indica se dare o meno la possibilità di cambiare il numero di record per pagina nella tabella
             */
            'showNumberItems': true,

            /**
             * GET ricerca
             */
            'getSearch': 'searchTerm',

            /**
             * Placeholder di default del campo di input per la ricerca
             */
            'search_placeholder': 'Cerca...',

            /**
             * action.type : selection | crud
             * se type = radio: selezione singola
             * se type = checkbox: selezione multipla
             */
            'actionType': {
                'type': 'radio',
            },

            /**
             * Identificativo unico
             */
            'unique_id': 'id',

            /**
             * Campi dell'oggetto da visualizzare nella tabella
             */
            'dataSource': [],

            /**
             * Indica se generare il footer della tabella o meno
             */
            'footerTable': false,

            /**
             * Setting per le operazioni di create/update degli elementi della tabella
             * @show_action: Mostra il pulsante per aggiungere un nuovo record e la colonna con l'action di edit nella tabella
             * @url: Url della action da eseguire per l'operazione di  create di un nuovo record
             * @editUrl: Url della action da eseguire per l'edit di un record
             * @label: Label del pulsante per la creazione di un nuovo record
             */
            'addRecord': {
                'show_action': false,
                'url': '',
                'editUrl': '',
                'label': 'Aggiungi'
            },

            /**
             * Mostra o nascondi la tabella
             */
            'hideShowTable': true,
            'hideTable': 'Nascondi tabella',
            'showTable': 'Mostra tabella',
        };

        if (options) {
            $.extend(config, options);
        }

        // Validazione dell'url
        var isValidate = _validURL(config.url);
        var getAction = _getAction();
        var isShowTable = config.hideShowTable;

        var isLoadAjaxPagination = true;

        // Se l'url non è valido mostro l'errore
        if (!isValidate) {
            isLoadAjaxPagination = false;
            alert('URL per la paginazione AJAX non valido.')
        }

        // Se l'url è valido faccio la paginazione della tabella
        if (isLoadAjaxPagination) {
            _loadAjaxPagination();
        }

        /**
         * Generazione intestazione tabella
         * @param object
         * @returns {string}
         * @private
         */
        function _generateHeadTable(object) {

            // Prendo le colonne da mostrare nella tabella dal config
            var columnsTHead = config.columns;
            var html = '';

            html += '<div class="table-responsive">';
            html += '<table class="' + config.classTable + '">';
            html += '<thead class="' + config.classHead + '">';
            html += '<tr>';

            if (_inArray(getAction, ['radio', 'checkbox'])) {
                html += '<th>Seleziona</th>';
            }

            for (var i = 0; i < columnsTHead.length; i++) {
                html += '<th>' + columnsTHead[i] + '</th>';
            }

            // Se presenti nel config setto le action
            if (config.addRecord.show_action) {
                html += '<th>Azioni</th>';
            }

            html += '</tr>';
            html += '</thead>';

            return html;
        }

        /**
         * Generazione footer della tabella
         * @param object
         * @returns {string}
         * @private
         */
        function _generateFooterTable(object) {
            var html = '';

            //Controlle nelle configurazioni se il footer va generato o meno
            if (config.footerTable) {
                // Prendo le colonne da mostrare nella tabella dal config (ripeto quelle dell'header)
                var columnsTHead = config.columns;

                html += '<tfoot style="color: #fff;' +
                    '    background-color: #212529;' +
                    '    border-color: #383f45;' +
                    'font-weight: bold;">';
                html += '<tr>';

                if (_inArray(getAction, ['radio', 'checkbox'])) {
                    html += '<td>Seleziona</td>';
                }

                for (var i = 0; i < columnsTHead.length; i++) {
                    html += '<td>' + columnsTHead[i] + '</td>';
                }

                // Se presenti nel config setto le action
                if (config.addRecord.show_action) {
                    html += '<td>Azioni</td>';
                }

                html += '</tr>';
                html += '</tfoot>';
            }

            html += '</table>';
            html += '</div>';

            return html;
        }

        /**
         * Genera il contenuto della tabella html
         * @param response
         * @returns {string}
         * @private
         */
        function _generateContentTable(response) {
            var html = '';
            var tempData = {};
            var current = null;

            if (response !== null) {

                var getValue = _this.getValue();

                // Per ogni record creo una riga nella tabella
                $.each(response, function (key, value) {

                    if (parseInt(config.dataParams['exclude_id'] ?? 0) !== parseInt(value['id'])) {

                        html += '<tr>';

                        // Per ogni campo da mostrare creo una colonna nella tabella
                        config.dataSource.forEach(function (currentValue, index, arr) {

                            let tmpArrayValues = currentValue.split(',');

                            if (Array.isArray(tmpArrayValues) && tmpArrayValues.length > 1) {
                                let tmpField = tmpArrayValues[0];
                                tmpArrayValues.forEach((e) => {
                                    if (value[e]) {
                                        tmpField = value[e] ? e : null;
                                    }
                                });
                                currentValue = tmpField;
                            }

                            if (value.hasOwnProperty(currentValue)) {

                                var currentUi = 'input_select_' + uuid;
                                var currentId = value[currentValue] + '' + uuid + '_input_select';

                                var isChecked = (getValue[value[currentValue]] !== undefined) ? ' checked ' : ' ';

                                // Campo di input per la selezione(radio o checkbox)
                                if (_inArray(getAction, ['radio', 'checkbox']) && config.unique_id == currentValue) {
                                    current = value[currentValue];
                                    html += '<td>'
                                    html += '<input' + isChecked + 'id="' + currentId + '" name="' + currentUi + '" type="' + getAction + '" class="' + currentUi + '" value="' + value[currentValue] + '" >';
                                    html += '</td>';

                                } /*else if (getAction === 'action' && config.unique_id == currentValue) {

                                html += '<td>Azioni</td>';

                            }*/ else {

                                    // Verifico se il campo da mostrare nella colonna è una data e se si la formatto
                                    if (_inArray(currentValue, config.dateFormat) && value[currentValue]) {
                                        let time = new Date(value[currentValue]);
                                        value[currentValue] = time.getDate() + '/' + pad2(time.getMonth() + 1) + '/' + time.getFullYear();
                                        // value[currentValue] = new Date(value[currentValue]).toLocaleDateString('it-IT');
                                    }

                                    // Creo la colonna con il valore da mostrare
                                    html += '<td>';
                                    html += (value[currentValue] != null && value[currentValue] != '') ? value[currentValue] : 'N.D';
                                    html += '</td>';

                                }
                            }
                        });

                        // Colonna delle action
                        if (config.addRecord.show_action) {
                            html += '<td class="text-center"><a type="button" class="patos_edit_item open-modal" href="#!" data-url="' + config.addRecord.editUrl + current + '" title="Edita elemento"><i class="far fa-edit fa-xs"></i></a></td>';
                        }
                        html += '</tr>';
                    }
                });

            }

            return html;
        }

        /**
         * Funzione che formatta il numero corrispondente al mese aggiungendo lo 0 prima se n < 10
         * @param n
         * @returns {*}
         */
        function pad2(n) {
            return (n < 10 ? '0' : '') + n;
        }

        /**
         * Funzione che crea:
         * il pulsante per mostrare o nascondere la tabella;
         * la select per scegliere il numero di record che si vogliono visualizzare per pagina;
         * il campo per la ricerca;
         * il pulsante per l'aggiunta di un nuovo record nella tabella;
         * @returns {string}
         * @private
         */
        function _generateSearchData() {
            $(`#show_hide${uuid}`).remove();
            let styleShowHideTable = (isShowTable) ? '' : ' style="display: none;" ';

            // Se sono il modalità sola lettura non mostro i pulsanti per mostrare/nascondere le tabelle
            if(!config.readOnly) {
                $(`${config.label}`).after(`<a role="button" id="show_hide${uuid}" class="patos_ajax_show_hide_table_${uuid} btn btn-outline-primary ml-2" style="padding:0.1rem .75rem; margin-bottom: .2rem;" href="#!">
                ${((isShowTable === true) ? (config.showTable) : (config.hideTable))}
                </a>`);
            }

            var html = '';
            html += `</div><div class="${_selectorLoading} ${config.readOnly ? '_cont__' : ''}" ${styleShowHideTable}><div style="margin-bottom: .5rem; display:flex; align-items: center; justify-content: space-between">`;
            // pulsante mostra/nascondi
            html += `<div>`;
            // select per settare gli elementi da visualizzare per pagina
            html += '<span style="display:' + ((config.showNumberItems) ? 'inline-block;' : 'none;') + ' margin: 0rem .5rem;">Visualizza';
            html += '<select id="select_per_page_' + uuid + '" class="select_per_page" style="display: inline-block; margin: 0rem .5rem;">';
            html += '<option value="5">5</option>';
            html += '<option value="10">10</option>';
            html += '<option value="20">20</option>';
            html += '<option value="30">30</option>';
            html += '</select>';
            html += 'per pagina</span>';
            // campo di input per la ricerca
            html += '<input type="text" name="' + _textSearchId + '" value="" ' +
                'placeholder="' + config.search_placeholder + '" id="' + _textSearchId + '" class="' + _textSearchId + '">';
            // pulsante per l'aggiunta di un nuovo record nella tabella direttamente dal form, se presente nella configurazione
            // della tabella
            if (config.addRecord.show_action) {
                html += '<button name="add" type="button" id="btn_add_' + uuid + '" style="padding:0.1rem .75rem; margin-bottom: .2rem;" class="btn btn-outline-primary open-modal ml-2" data-url="' + config.addRecord.url + '">' + config.addRecord.label + '&nbsp; <i class="fas fa-plus-circle"></i></button>';
            }
            html += '</div>'
            html += '</div>';

            return html;
        }

        /**
         * Load Ajax Paginations
         * Effettua la chiamata ajax per ottenere i dati e poi effettua la paginazione dei dati.
         * @private
         */
        function _loadAjaxPagination(searchKeyword = null, url = null, perPage = null) {

            var isLiveSearch = false;

            // Elementi da visualizzare per pagina
            if (config.dataParams['per_page'] === undefined) {
                config.dataParams['per_page'] = 5;
            }

            if (searchKeyword !== null) {
                config.dataParams[config.getSearch] = searchKeyword;
                isLiveSearch = true;
            }

            if (url !== null || perPage != null) {
                isLiveSearch = true;
            }

            var ajaxUrl = ((url !== null) ? url : config.url);
            var selector = (!isLiveSearch) ? _this : $('#' + _selectorLoading);

            //Chiamata ajax per ottenere i dati
            $.ajax({
                type: config.type,
                url: ajaxUrl,
                dataType: config.dataType,
                data: config.dataParams,
                // Spinner
                beforeSend: function () {
                    if (isLiveSearch) {
                        $('#' + _selectorLoading).append('<div style="height:100%; width:100%; position:absolute;background:rgba(255,255,255,.9); top:0; left:0; font-size:2rem;"><i class="fas fa-spinner fa-spin" style="position:absolute; transforn:translateX(-50%); left:50%; top:5rem"></i></div>');
                    }
                },
                // Genero la tabella e la paginazione dei dati ottenuti dalla chiamata
                success: function (response) {
                    var results = (typeof response.data.results.data) ? response.data.results.data : null;
                    var htmlResult = '';

                    if (!isLiveSearch) {
                        // let styleShowHideTable = (isShowTable) ? '' : ' style="display: none;" ';
                        htmlResult += _generateSearchData(this);
                        htmlResult += '<div' + ' id="' + _selectorLoading + '" class="position-relative">';
                    }
                    htmlResult += _generateHeadTable(this);

                    if (results.length) {
                        htmlResult += _generateContentTable(results);
                    } else {
                        htmlResult += '<tbody><tr><td align="center" colspan="' + (config.dataSource.length+1) + '">Non ci sono risultati per questa ricerca</td></tr></tbody>';
                    }

                    htmlResult += _generateFooterTable(this);

                    if (results.length) {
                        htmlResult += _setPagination(this, response.data.results)
                    }

                    if (!isLiveSearch) {
                        htmlResult += '</div>';
                    }
                    htmlResult += '</div>';

                    selector.empty().append(htmlResult);
                    _linkpaPination();
                    _liveSearch();
                    _selectValues();
                    _hideShowTable();
                    _changePerPage();
                    _openModal();

                    // Edit
                    _selectedData();

                    //Se sono in un form di sola lettura rimuovo le tabelle di selezione
                    //Mostro solo gli elementi selezionati
                    $('._cont__').remove();
                },
                complete: function () {
                },
                error: function (jqXHR, status) {
                    console.error('Errore nella risposta della chiamata AJAX.');
                }
            });
        }

        /**
         * Funzione che rende asincrona la paginazione
         * @private
         */
        function _linkpaPination() {
            $('.ajax_link' + uuid).on('click', function (e) {
                e.preventDefault();
                var url = $(this).attr('href');
                if (url !== '#!') {
                    _loadAjaxPagination(null, url);
                }
            })
        }

        /**
         * Funzione che nasconde o mostra la tabella corrente
         * @private
         */
        function _hideShowTable() {
            $('#show_hide' + uuid).on('click', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                var attr = $(this).attr('id');

                if (isShowTable === true) {
                    isShowTable = false
                    $('#' + attr).html(config.hideTable);
                    $('#search_' + uuid).prop('disabled', true);
                } else {
                    isShowTable = true
                    $('#' + attr).html(config.showTable);
                    $('#search_' + uuid).prop('disabled', false);
                }
                $('.tbl_' + uuid).slideToggle()
            })
        }

        /**
         * Appende il valore selezionato nel campo di input nascosto
         * @private
         */
        function _setInputDataValue(editMode = false) {

            if (config.setInputDataValue !== null) {

                var getValue = _this.getValue();
                var setData = '';

                if (Object.keys(getValue).length > 0) {

                    for (var property in getValue) {

                        /* Stampa solo dati */
                        if (config.setInputDataValueOnlyId === true) {
                            setData += property + ',';
                        } else {
                            setData += property + '|' + getValue[property] + '~';
                        }

                        $(config.setInputDataValue).val(setData.slice(0, -1));
                    }

                } else {

                    $(config.setInputDataValue).val('');

                }

                $(config.setInputDataValue).change();

            }

        }

        /**
         * Funzione che setta gli elementi gia selezionati se ci sono (in fase di edit).
         * Se non ci sono elementi gia selezionati, mostra il messaggio apposito.
         * @private
         */
        function _selectedData() {

            var getValue = _this.getValue();

            var htmlData = '';
            var selectorData = 'data' + uuid;

            // Setto gli elementi gia selezionati se presenti
            if (!$.isEmptyObject(getValue)) {
                for (var property in getValue) {
                    htmlData += '<span class="patos_ajax_table_data_selected" data-id="' + property + uuid + '">';
                    htmlData += getValue[property];
                    htmlData += (config.readOnly) ? '' : '<a class="patos_remove_item" href="#!">x</a>';
                    htmlData += '</span>';
                }
                htmlData += '<p id="empty_' + uuid + '" style="display: none;" class="no-selectd-element">Nessun elemento selezionato</p>';
            } else {
                // Altrimenti mostro il messaggio che non ci sono elementi selezionati
                htmlData += '<p id="empty_' + uuid + '">Nessun elemento selezionato</p>';
            }

            if (!$('#' + selectorData).is(':visible')) {
                // _this.append(`<div class="select-box" id=${config.errorFields}><div class="select-box-title">` + config.selectedLabel + '</div> <div id="' + selectorData + '"></div></div></div>');
                _this.append(`<div class="select-box"><div class="select-box-title">` + config.selectedLabel + '</div> <div id="' + selectorData + '"></div></div></div>');
            }

            if (getAction === 'radio') {
                $('#' + selectorData).empty();
            }

            $('#' + selectorData).empty().append(htmlData);
            _removeBadge();
        }

        /**
         * Ritorna il valore in forma di oggetto della tabella
         * @returns {string}
         */
        _this.getValue = function () {
            return valueData[uuid];
        }

        /**
         * Elimino(deseleziono) il valore dalla tabella
         * @returns {string}
         */
        _this.deleteValue = function (key) {
            delete valueData[uuid][key];
        }

        /**
         * Setta gli elementi gia selezionati nella tabella e nel campo di input nascosto
         * @param value
         */
        _this.setValue = function (key, value, editMode = false) {

            if (_inArray(getAction, ['radio', 'checkbox'])) {

                if (getAction === 'radio') {
                    valueData[uuid] = {};
                }

                valueData[uuid][key] = value;
                _setInputDataValue(editMode);
            }

            /*
            if(getAction=='crud'){

            }
            */
            return _this;
        }

        $.fn.patOsAjaxPagination.setValue = function (key, value, editMode = false) {
            _this.setValue(key, value, editMode); //
        }

        /**
         * Validazione URL
         * @param str
         * @returns {boolean}
         * @private
         */
        function _validURL(str) {
            if (str === null) {
                return false;
            }
            var regex = /(?:https?):\/\/(\w+:?\w*)?(\S+)(:\d+)?(\/|\/([\w#!:.?+=&%!\-\/]))?/;
            return regex.test(str);
        }

        /**
         * Setto l'Azione della tabella:
         * radio: selezione singola
         * checkbox: selezione multipla
         * @returns {string}
         * @private
         */
        function _getAction() {

            var type = 'radio';

            if (typeof config.action.type !== 'undefined' && !_inArray(config.action.type, ['radio', 'checkbox', 'crud'])) {
                config.action.type = 'radio';
                type = config.action.type;
            } else {
                type = config.action.type;
            }

            return type;
        }

        /**
         * Verififco se il valore passato rientra nei valori di un array
         * @param needle
         * @param haystack
         * @returns {boolean}
         * @private
         */
        function _inArray(needle, haystack) {
            for (var i in haystack) {
                if (haystack[i] == needle) return true;
            }
            return false;
        }

        /**
         * Genera una stringa di intero naturale
         * @param max
         * @returns {number}
         * @private
         */
        function _getRandomInt(max) {
            return Math.floor(Math.random() * max);
        }

        /**
         * Setta il numero di righe(record) per pagina che si vogliono visualizzare
         * @private
         */
        function _changePerPage() {

            $('#select_per_page_' + uuid).on('change', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                config.dataParams['per_page'] = parseInt($(this).val());

                _loadAjaxPagination(null, null, true);
            });
        }

        /**
         * Live search
         * @private
         */
        function _liveSearch() {

            $('#' + _textSearchId).keypress(function (e) {

                var keyCode = e.keyCode || e.which;

                if (keyCode === 13) {
                    e.preventDefault();
                    return false;
                }

            });

            var magicalTimeout = 500;
            var timeout;

            $('#' + _textSearchId).on('keyup', function (e) {
                e.preventDefault();
                e.stopImmediatePropagation();
                clearTimeout(timeout);
                var _value = $(this).val();
                timeout = setTimeout(function () {
                    _loadAjaxPagination(_value);
                }, magicalTimeout)
            });
            return false;
        }

        /**
         * Funzione che crea e appende la paginazione della tabella
         * @param obj
         * @param results
         * @returns {string}
         * @private
         */
        function _setPagination(obj, results) {

            var html = '';
            if (results.total > 0) {

                html += '<div class="row">';
                html += '<div class="col">';
                html += '<div style="display:flex; justify-content:space-between; align-items:center; width:100%">';
                html += '<div>Pagina <strong>' + results.current_page + '</strong> di <strong>' + Math.ceil(results.total / results.per_page) + '</strong>';
                html += '&nbsp; - &nbsp;';
                html += 'Totale records <strong>' + results.total + '</strong></div>';
                html += '<nav>';
                html += '<div class="style-pagination">';
                html += '<ul class="pagination">';

                // BEGIN primo link
                if (results.first_page_url.length >= 1) {
                    html += '<li class="page-item" aria-disabled="true" aria-label="Prima pagina">';
                    html += '<a class="page-link ajax_link' + uuid + '" href="' + results.first_page_url + '" rel="prev" aria-label="Prima pagina">&lsaquo;&lsaquo;</a>';
                    html += '</li>';
                } else {
                    html += '<li class="page-item disabled" aria-disabled="true" aria-label="Prima pagina">';
                    html += '<span class="page-link" aria-hidden="true">&lsaquo;&lsaquo;</span>';
                    html += '</li>';
                }
                // END  primo link

                // BEGIN links
                for (var i = 0; i <= results.links.length - 1; i++) {

                    // Link corrente
                    var link = results.links[i];
                    if (parseInt(link.label) === parseInt(results.current_page)) {

                        html += '<li class="page-item active" aria-current="page">';
                        html += '<span class="page-link">' + link.label + '</span>';
                        html += '</li>';

                    } else {

                        html += '<li class="page-item">';
                        html += '<a class="page-link ajax_link' + uuid + '" href="' + ((link.url !== null) ? link.url : '#!') + '">';


                        if (link.label === '' && i === 0) {
                            html += '&lsaquo;';
                        } else if ((link.label == '') && (i === results.links.length - 1)) {
                            html += '&rsaquo;';
                        } else {
                            html += link.label;
                        }

                        html += '</a>';
                        html += '</li>';

                    }

                }
                // END  links

                // BEGIN ultimo link
                if (results.last_page_url) {
                    html += '<li class="page-item">';
                    html += '<a class="page-link ajax_link' + uuid + '" href="' + results.last_page_url + '" rel="next" aria-label="Ultima pagina">&rsaquo;&rsaquo;</a>';
                    html += '</li>';
                } else {
                    html += '<li class="page-item disabled" aria-disabled="true" aria-label="Ultima pagina">';
                    html += '<span class="page-link" aria-hidden="true">&rsaquo;&rsaquo;</span>';
                    html += '</li>';
                }
                // END  ultimo link

                html += '</ul>';
                html += '</div>';
                html += '</nav>';
                html += '</div>';
                html += '</div>';
            }

            return html;
        }

        /**
         * Aggiunge il record selezionato tra gli elementi selezionati, e aggiunge il suo valore al campo di input nascosto.
         * @private
         */
        function _selectValues(editMode = false) {
            $('.input_select_' + uuid).on('click', function (e) {

                let valueNotDefined = false;
                var add = true;
                var remove = false;

                if ($(this).attr('type') === 'checkbox' && $(this).prop('checked') === false) {
                    add = false;
                    remove = true;
                }

                /* ADD */
                if (add) {
                    let selectedId = $(this).val();

                    var selectorData = 'data' + uuid;
                    var data = $(this).parent().parent();

                    var htmlData = '';
                    var text = '';

                    $('#empty_' + uuid).hide();

                    htmlData += '<span class="patos_ajax_table_data_selected" data-id="' + $(this).val() + uuid + '">';
                    data.children().each(function (index) {
                        if (index >= 1) {
                            if ($(this).text() != 'N.D' && $(this).text() != '') {
                                valueNotDefined = true;
                                text += $(this).text();
                                htmlData += $(this).text();
                            }

                            // Se c'è la colonna azioni -2 altrimenti -1
                            if (index < data.children().length - (config.addRecord.show_action ? 2 : 1)) {
                                // Se il valore non è definito non inserisco il -
                                if (valueNotDefined) {
                                    htmlData += ' - ';
                                    text += ' - ';
                                    valueNotDefined = false;
                                }

                            } else {
                                if ($(this).text() == 'N.D') {
                                    htmlData = htmlData.slice(0, -2);
                                }
                                htmlData += '<a class="patos_remove_item" href="#!" title="Rimuovi elemento">x</a>';
                            }

                        }
                    });
                    htmlData += '</span>';

                    if (!$('#' + selectorData).is(':visible')) {
                        _this.append('<div class="select-box"><div class="select-box-title">Elementi selezionati</div> <div id="' + selectorData + '"></div></div></div>');
                    }

                    if (getAction === 'radio') {
                        $('#' + selectorData).empty().append('<p id="empty_' + uuid + '" style="display: none;">Nessun elemento selezionato</p>');
                    }

                    $('#' + selectorData).append(htmlData);

                    _this.setValue($(this).val(), text);

                    // _setInputDataValue();
                    _removeBadge();
                }

                /* EDIT */
                if (remove) {
                    var id = $(this).val();

                    _this.deleteValue(id);
                    $('.patos_ajax_table_data_selected[data-id="' + id + uuid + '"]').remove();
                    if (!$('#data' + uuid + ' span').length) {
                        $('#empty_' + uuid).show();
                    }
                    _setInputDataValue();
                }

            });
        }

        /**
         * Per l'apertura del modale di creazione/modifica di un elemento direttamente dal form
         * @private
         */
        function _openModal() {
            $('.open-modal').on('click', function (event) {

                event.preventDefault();
                event.stopImmediatePropagation();
                event.stopPropagation();

                $('#new-obj-box').hide();
                $('#spinner').show();
                let url = this.getAttribute('data-url');
                $('#formModal').modal('show');
                $('#new-obj-box').attr('src', url);
                setTimeout(function () {
                    $('#spinner').hide();
                    $('#new-obj-box').show();
                }, 800);
            });
        }

        /**
         * Alla chiusura del modale, dopo il salvataggio, viene effettuato il refresh delle tabelle
         */
        $('#formModal').on('hidden.bs.modal', function (event) {
            _loadAjaxPagination();
        });

        /**
         * Rimuove il badge dell'elemento che si vuole deselezioare
         * @private
         */
        function _removeBadge() {
            $('.patos_remove_item').on('click', function (e) {
                e.preventDefault();
                e.stopPropagation();
                e.stopImmediatePropagation();
                let id = $(this).parent().attr('data-id');

                $(this).parent().remove();
                if (!$('#data' + uuid + ' span').length) {
                    $('#empty_' + uuid).show();
                }
                _this.deleteValue(id.replace(uuid, ''));
                $('#' + id + '_input_select').prop("checked", false);
                _setInputDataValue();
            })
        }

        return this;
    }
})(jQuery);