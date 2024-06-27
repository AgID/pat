## Modello `RelAssignmentsMeasuresModel`

Il modello `RelAssignmentsMeasuresModel` rappresenta la tabella `rel_assignments_measures` e rappresenta la relazione tra le tabelle `object_assignments` e `object_measures`.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'rel_assignments_measures'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.

### Metodi

Non sono presenti metodi aggiuntivi nel modello `RelAssignmentsMeasuresModel` in quanto estende la classe `Pivot` di Eloquent, che fornisce i metodi di base per la gestione delle relazioni molte-a-molte tra le tabelle.
