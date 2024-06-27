## Modello `InstitutionsModel`

Il modello `InstitutionsModel`  rappresenta la tabella `institutions` e fornisce funzionalità per la gestione degli enti.

#### Proprietà `searchable`

La proprietà `searchable` specifica i campi su cui effettuare la ricerca nel datatable.

#### Metodi di relazione

Il modello `InstitutionsModel` dichiara diverse relazioni con altri modelli utilizzando i metodi `hasOne`, `hasMany`, e `belongsTo`.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'institutions'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.
* `$searchable`: Un array di nomi dei campi su cui effettuare la ricerca nel datatable.
