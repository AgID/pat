## Modello `RelAssignmentsNoticesActsModel`

Il modello `RelAssignmentsNoticesActsModel` rappresenta la tabella `rel_assignments_notices_acts` e rappresenta la relazione tra le tabelle `object_ac_assignments` e `object_notices_acts`.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'rel_assignments_notices_acts'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.

### Metodi

Non sono presenti metodi aggiuntivi nel modello `RelAssignmentsNoticesActsModel` in quanto estende la classe `Pivot` di Eloquent, che fornisce i metodi di base per la gestione delle relazioni molte-a-molte tra le tabelle.
