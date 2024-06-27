## Modello `CommissionsModel`

Il modello `CommissionsModel`  rappresenta la tabella `object_commissions` e fornisce funzionalità per la gestione delle commissioni e dei gruppi consiliari.

```
__construct(array$attributes = [])
```

Il costruttore del modello `CommissionsModel` inizializza il modello.

* Parametri:
  * `$attributes`: (Opzionale) Un array di attributi per il modello.

```
boot()
```

Il metodo `boot` inizializza il modello e aggiunge lo scope globale `InstitutionScope` per filtrare i dati in base all'ente.

#### Metodi di relazione

Il modello `CommissionsModel` dichiara diverse relazioni con altri modelli utilizzando i metodi `belongsTo`, `belongsToMany` e `hasMany`.

#### Proprietà `searchable` e `searchableWhereHas`

Le proprietà `searchable` e `searchableWhereHas` specificano i campi su cui effettuare la ricerca nel datatable e nelle relazioni dei modelli.
