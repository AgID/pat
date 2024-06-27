## Modello `MeasuresModel`

Il modello `MeasuresModel`  rappresenta la tabella `object_measures` e fornisce funzionalità per la gestione dei provvedimenti amministrativi.

#### Proprietà `searchable`

La proprietà `searchable` specifica i campi su cui effettuare la ricerca nel datatable.

#### Proprietà `searchableWhereHas`

La proprietà `searchableWhereHas` specifica i campi di relazione su cui effettuare la ricerca.

#### Metodi di relazione

Il modello `MeasuresModel` dichiara diverse relazioni con altri modelli utilizzando i metodi `belongsTo`, `belongsToMany` e `hasMany`.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'object_measures'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.
* `$searchable`: Un array di nomi dei campi su cui effettuare la ricerca nel datatable.
* `$searchableWhereHas`: Un array che specifica i campi di relazione su cui effettuare la ricerca.
