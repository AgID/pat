## Modello `BalanceSheetsModel`

Il modello `BalanceSheetsModel`  rappresenta la tabella `object_balance_sheets` e fornisce funzionalità per la gestione dei bilanci.

### Lista dei metodi

```
__construct(array$attributes = [])
```

Il costruttore del modello `BalanceSheetsModel`.

* Parametri:
  * `$attributes`: (Opzionale) Un array di attributi per il modello.

```
boot()
```

Il metodo `boot` inizializza il modello e aggiunge lo scope globale `InstitutionScope` per filtrare i dati in base all'ente.

#### Metodi di relazione

Il modello `BalanceSheetsModel` dichiara diverse relazioni con altri modelli utilizzando i metodi `belongsTo`, `hasMany` e `hasOne`.

#### Proprietà `searchable` e `searchableWhereHas`

Le proprietà `searchable` e `searchableWhereHas` specificano i campi su cui effettuare la ricerca nel datatable e nelle relazioni dei modelli.
