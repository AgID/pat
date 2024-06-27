## Modello `RelPersonnelMeasuresModel`

Il modello `RelPersonnelMeasuresModel` rappresenta la tabella `rel_personnel_measures` e rappresenta la relazione tra le tabelle `object_personnel` e `object_measures`. Questa tabella di relazione collega il personale ai provvedimenti.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'rel_personnel_measures'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.

### Metodi

Non sono presenti metodi aggiuntivi nel modello `RelPersonnelMeasuresModel` oltre a quelli forniti dalla classe `Pivot` di Eloquent, che gestisce la relazione molti-a-molti tra le tabelle.
