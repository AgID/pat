/** QUESTO FILE CONTIENE LE CONFIGURAIZONI DI DEFAULT DA USARE PER LE TOM-SELECT

Nel caso dobbiate modificare dei singoli valori in casi particolari, ricordate che potete scrivere:
new TomSelect("mia-select", { ...TS_DEFAULT, hideSelected: true, placeholder: "Esempio"})

*/

/** Per le select singole senza valore di default. Es. Si/No, oppure OptA / OptB / OptC /... ecc
 * */
const TS_SINGLE = {
    create: false,
    placeholder: "Seleziona",
    onInitialize() {
        //All'inizializzazione impedisce di digitare con il cursore
        this.control_input.readOnly = true;
    }
};

/** Per le select singole con il primo valore preselezionato. Es. Pubblicato/Non pubblicato, Attivo/Non attivo
 * */
const TS_SINGLE_PRESELECT = {
    controlInput: null,
    search: false
};
/**
 * Per le select singole dove deve essere possibile deselezionare l'opzione
 */
const TS_SINGLE_CLEAR = {
    create: false,
    placeholder: "Seleziona",
    plugins: {
        clear_button: {title: "Rimuovi"},
    },
    onInitialize() {
        //All'inizializzazione impedisce di digitare con il cursore
        this.control_input.readOnly = true;
        updateClearButtonClass(this);
    },
    onChange: function () {
        updateClearButtonClass(this);
    },
};

/**
 * Per le select singole - Filtri datatable
 */
const TS_SINGLE_SEARCH_FILTER = {
    create: false,
    placeholder: "Cerca...",

    onDropdownOpen() {
        this.setTextboxValue('');
        this.refreshOptions(false);
    },

    render: {
        no_results() {
            return '<div class="no-results">Nessun risultato trovato</div>';
        }
    },
};



/**
 * Da utilizzare select multiple senza ricerca. Es: Pubblica in
 * */
const TS_MULTI_NO_SEARCH = {
    maxItems: null,   // max elem selezionabili
    closeAfterSelect: true,  // Mantiene aperto il menu dopo ogni selezione. Utile per selezionare più elementi consecutivamente.

    //per impedire la ricerca
    search: false,
    searchField: [],

    placeholder: "Seleziona una o più opzioni",
    create: false, // se true permette di scrivere per creare nuovi elementi da aggiungere
    persist: false, // Ha effetto solo se create:true. Se false, le opzioni create dinamicamente vengono eliminate quando non sono più selezionate.
    hideSelected: false, //lascia le option visibili nella lista come disabilitate dopo avere selezionate
    plugins: {
        remove_button: {title: "Rimuovi"}, // La x vicino ogni opzione
        clear_button: {title: "Rimuovi tutte le selezioni"}, // La X per rimuovere tutto
        //caret_position: {} //possibilità di navigare tra le option con le frecce
    },
    onInitialize: function () {
        updateClearButtonClass(this);
    },
    onChange: function () {
        updateClearButtonClass(this);
    },
}


/**
 * Questa funzione assegna una specifica classe al pulsante di clear
 * per renderlo sempre visibile quando c'è almeno una option selezionata nelle select multiple
 * */
function updateClearButtonClass(elem) {
    const btn = elem.control.querySelector('.clear-button');
    if (!btn) return;
    if (elem.items.length > 0) {
        btn.classList.add('clear-button-os');
    } else {
        btn.classList.remove('clear-button-os');
    }
}


/**
 * Da utilizzare per le select multiple con ricerca ajax. Es. Procedimenti associati
 * */
function createAjaxTomSelect({
                                 selector,
                                 searchUrl,
                                 selectedUrl = null,
                                 selectedIdsSelector = null,
                                 model,
                                 institutionId,
                                 placeholder = "Seleziona o cerca...",
                                 loadSelected = false,
                                 maxItems = null,
                                 extraParams = {},
                                 onChange = null,
                                 onItemAdd = null,
                                 onError = null
                             }) {

    const element = document.querySelector(selector);

    if (!element) {
        console.warn(`Elemento Tom Select non trovato: ${selector}`);
        return null;
    }

    //Rimuove l'eventuale opzione vuota presente nella select originale.
    element.querySelectorAll('option[value="0"]').forEach(option => {
        option.remove();
    });

    const ajaxHeaders = {
        "Accept": "application/json",
        "X-Requested-With": "XMLHttpRequest"
    };

    const plugins = {
        clear_button: {
            title: maxItems === 1 ? "Rimuovi" : "Rimuovi tutte le selezioni"
        }
    };
    if (maxItems !== 1) {
        plugins.remove_button = {
            title: "Rimuovi"
        };
    }

    const tomSelect = new TomSelect(element, {
        maxItems,

        valueField: "id",
        labelField: "text",

        /*
         * La ricerca viene effettuata dal server. Disattivo quella client
         */
        searchField: [],

        placeholder,
        create: false,
        persist: false,
        hideSelected: true,
        closeAfterSelect: true,

        openOnFocus: true,
        preload: "focus",
        loadThrottle: 250,

        plugins,

        shouldLoad: function () {
            return true;
        },

        load(query, callback) {
            const params = new URLSearchParams({
                model: String(model),
                institution_id: String(institutionId),
                searchTerm: query,
                ...extraParams
            });

            fetch(`${searchUrl}?${params.toString()}`, {
                method: "GET",
                headers: ajaxHeaders
            })
                .then(response => {
                    if (!response.ok) {
                        throw new Error(`Errore HTTP ${response.status}`);
                    }

                    return response.json();
                })
                .then(response => {
                    const options = response.data?.options ?? [];

                    /*
                     * Conserva solamente le opzioni già selezionate.
                     */
                    this.clearOptions((option, value) => {
                        return this.items.includes(String(value));
                    });

                    callback(options);

                    /*
                     * Permette di rieseguire in seguito la stessa ricerca.
                     */
                    delete this.loadedSearches[query];
                })
                .catch(error => {
                    delete this.loadedSearches[query];

                    console.error(
                        "Errore durante il caricamento delle opzioni:",
                        error
                    );

                    callback();

                    if (typeof onError === "function") {
                        onError(error);
                    }
                });
        },

        onInitialize: function () {
            updateClearButtonClass(this);
        },

        onItemAdd(value, item) {
            /*
             * Pulisce il testo digitato e invalida la ricerca vuota.
             * La richiesta verrà eseguita alla successiva apertura/focus.
             */
            this.setTextboxValue("");
            this.lastQuery = null;
            delete this.loadedSearches[""];

            if (typeof onItemAdd === "function") {
                onItemAdd.call(this, value, item);
            }
        },
        onDropdownOpen() {
            /*
             * Quando la select viene riaperta, azzera la ricerca precedente
             * e carica nuovamente le opzioni iniziali.
             */
            this.setTextboxValue("");
            this.lastValue = "";
            this.lastQuery = null;
            delete this.loadedSearches[""];

            this.load("");
        },
        onChange(value) {
            updateClearButtonClass(this);

            if (typeof onChange === "function") {
                onChange.call(this, value);
            }
        }
    });

    if (loadSelected && selectedUrl && selectedIdsSelector) {
        const selectedIdsElement =
            document.querySelector(selectedIdsSelector);

        const selectedParams = new URLSearchParams({
            id: selectedIdsElement?.value ?? "",
            model: String(model),
            institution_id: String(institutionId),
            ...extraParams
        });

        fetch(`${selectedUrl}?${selectedParams.toString()}`, {
            method: "GET",
            headers: ajaxHeaders
        })
            .then(response => {
                if (!response.ok) {
                    throw new Error(`Errore HTTP ${response.status}`);
                }

                return response.json();
            })
            .then(response => {
                const selectedItems =
                    response.data?.selected ?? [];

                selectedItems.forEach(item => {
                    const id = String(item.id);

                    tomSelect.addOption({id, text: String(item.text)});
                    tomSelect.addItem(id, true);
                });

                tomSelect.refreshItems();
                updateClearButtonClass(tomSelect);
            })
            .catch(error => {
                console.error("Errore durante il caricamento degli elementi selezionati:", error);
            });
    }

    return tomSelect;
}


function syncTomSelectOptions(selectId, options, selectedValue = null) {
    const element = document.getElementById(selectId);
    const instance = element?.tomselect;

    if (!instance) {
        return;
    }

    const valueToRestore = selectedValue !== null ? String(selectedValue) : String(instance.getValue() ?? '');

    instance.clear(true);
    instance.clearOptions();

    instance.addOptions(
        Object.entries(options).map(([value, text]) => ({
            value: String(value),
            text
        }))
    );

    if (
        valueToRestore &&
        Object.prototype.hasOwnProperty.call(instance.options, valueToRestore)
    ) {
        instance.setValue(valueToRestore, true);
    }

    instance.refreshOptions(false);
}
