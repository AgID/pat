## Modello `LeaseCanonsModel`

Il modello `LeaseCanonsModel`  rappresenta la tabella `object_lease_canons` e fornisce funzionalità per la gestione dei canoni di locazione.

#### Proprietà `searchable`

La proprietà `searchable` specifica i campi su cui effettuare la ricerca nel datatable.

#### Metodi di relazione

Il modello `LeaseCanonsModel` dichiara diverse relazioni con altri modelli utilizzando i metodi `belongsTo`, `belongsToMany` e `hasMany`.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'object_lease_canons'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.
* `$searchable`: Un array di nomi dei campi su cui effettuare la ricerca nel datatable.
