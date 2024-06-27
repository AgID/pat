## Modello `GrantsModel`

Il modello `GrantsModel` rappresenta la tabella `object_grants` e fornisce funzionalità per la gestione delle sovvenzioni e vantaggi economici.

### Lista dei metodi

```
__construct(array$attributes = [])
```

Il costruttore del modello `GrantsModel` inizializza il modello.

* Parametri:
  * `$attributes`: (Opzionale) Un array di attributi per il modello.

```
boot()
```

Il metodo `boot` inizializza il modello e aggiunge lo scope globale `InstitutionScope` per filtrare i dati in base all'ente.

#### Metodi di relazione

Il modello `GrantsModel` dichiara diverse relazioni con altri modelli utilizzando i metodi `belongsTo`, `hasMany` e `belongsToMany`.

#### Proprietà `searchable` e `searchableWhereHas`

Le proprietà `searchable` e `searchableWhereHas` specificano i campi su cui effettuare la ricerca nel datatable e nelle relazioni dei modelli.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'object_grants'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.
* `$searchable`: Un array di nomi dei campi su cui effettuare la ricerca nel datatable.
