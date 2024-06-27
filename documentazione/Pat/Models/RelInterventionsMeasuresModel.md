## Modello `RelInterventionsMeasuresModel`

Il modello `RelInterventionsMeasuresModel` rappresenta la tabella `rel_interventions_measures` e rappresenta la relazione tra le tabelle `object_interventions` e `object_measures`.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'rel_interventions_measures'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.

### Metodi

Non sono presenti metodi aggiuntivi nel modello `RelInterventionsMeasuresModel` in quanto estende la classe `Pivot` di Eloquent, che fornisce i metodi di base per la gestione delle relazioni molti-a-molti tra le tabelle.
