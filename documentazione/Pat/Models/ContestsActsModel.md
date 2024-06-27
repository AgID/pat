## Modello `ContestsActsModel`

Il modello `ContestsActsModel` rappresenta la tabella `object_contests_acts` e fornisce funzionalità per la gestione dei bandi, delle gare e dei contratti.

### Lista dei metodi

```
__construct(array$attributes = [])
```

Il costruttore del modello `ContestsActsModel` inizializza il modello.

* Parametri:
  * `$attributes`: (Opzionale) Un array di attributi per il modello.

```
boot()
```

Il metodo `boot` inizializza il modello e aggiunge lo scope globale `InstitutionScope` per filtrare i dati in base all'ente.

#### Metodi di relazione

Il modello `ContestsActsModel` dichiara diverse relazioni con altri modelli utilizzando i metodi `belongsTo`, `belongsToMany` e `hasMany`.

#### Proprietà `searchable` e `searchableWhereHas`

Le proprietà `searchable` e `searchableWhereHas` specificano i campi su cui effettuare la ricerca nel datatable e nelle relazioni dei modelli.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'object_contests_acts'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.
* `$searchable`: Un array di nomi dei campi su cui effettuare la ricerca nel datatable.
