## Modello `InterventionsModel`

Il modello `InterventionsModel` rappresenta la tabella `object_interventions` e fornisce funzionalità per la gestione degli interventi straordinari e di emergenza.

#### Proprietà

* `$table`: Il nome della tabella nel database (default: `'object_interventions'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.
* `$searchable`: Un array di nomi dei campi su cui effettuare la ricerca nel datatable.

#### Metodi di relazione

Il modello `InterventionsModel` dichiara diverse relazioni con altri modelli utilizzando i metodi `belongsTo`, `belongsToMany` e `hasMany`.
