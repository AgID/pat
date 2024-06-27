## Modello `RelRegulationsProceedingsModel`

Il modello `RelRegulationsProceedingsModel` rappresenta la tabella `rel_regulations_proceedings` e rappresenta la relazione tra le tabelle `object_regulations` e `object_proceedings`.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'rel_regulations_proceedings'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.

### Metodi

Non sono presenti metodi aggiuntivi nel modello `RelRegulationsProceedingsModel` oltre a quelli forniti dalla classe `Pivot` di Eloquent, che gestisce la relazione molti-a-molti tra le tabelle.
