## Modello `RelInstitutionTypeSectionsLabelingModel`

Il modello `RelInstitutionTypeSectionsLabelingModel` rappresenta la tabella `rel_institution_type_sections_labeling` e rappresenta la relazione tra le tabelle `institution_type` e `sections`. Questa relazione viene utilizzata per la gestione delle traduzioni dei ruoli.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'rel_institution_type_sections_labeling'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.

### Metodi

Non sono presenti metodi aggiuntivi nel modello `RelInterventionsMeasuresModel` in quanto estende la classe `Pivot` di Eloquent, che fornisce i metodi di base per la gestione delle relazioni molti-a-molti tra le tabelle.
