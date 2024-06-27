## Modello `RelContestsActRequirementsModel`

Il modello `RelContestsActRequirementsModel` rappresenta la tabella `rel_contests_act_requirements` e rappresenta la relazione tra le tabelle `object_contest_acts` e `object_notices_for_qualification_requirements`.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'rel_contests_act_requirements'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.

### Metodi

Non sono presenti metodi aggiuntivi nel modello `RelContestsActRequirementsModel` in quanto estende la classe `Pivot` di Eloquent, che fornisce i metodi di base per la gestione delle relazioni molti-a-molti tra le tabelle.
