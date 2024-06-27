## Modello `RelContestAssignmentsModel`

Il modello `RelContestAssignmentsModel` rappresenta la tabella `rel_contest_assignments` e rappresenta la relazione tra le tabelle `object_contest` e `object_assignments`.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'rel_contest_assignments'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.

### Metodi

Non sono presenti metodi aggiuntivi nel modello `RelContestAssignmentsModel` in quanto estende la classe `Pivot` di Eloquent, che fornisce i metodi di base per la gestione delle relazioni molti-a-molti tra le tabelle.
