## Modello `RelNoticesActsContestsActsModel`

Il modello `RelNoticesActsContestsActsModel` rappresenta la tabella `rel_notices_acts_contests_acts` e rappresenta la relazione tra la tabella `object_notices_acts` con se stessa.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'rel_notices_acts_contests_acts'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.

### Metodi

Non sono presenti metodi aggiuntivi nel modello `RelNoticesActsContestsActsModel` in quanto estende la classe `Pivot` di Eloquent, che fornisce i metodi di base per la gestione delle relazioni molti-a-molti tra le tabelle.
