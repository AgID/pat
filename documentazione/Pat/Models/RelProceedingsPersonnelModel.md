## Modello `RelProceedingsPersonnelModel`

Il modello `RelProceedingsPersonnelModel` rappresenta la tabella `rel_proceedings_personnel` e rappresenta la relazione tra le tabelle `object_proceedings` e `object_personnel`.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'rel_proceedings_personnel'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.

### Metodi

Non sono presenti metodi aggiuntivi nel modello `RelProceedingsPersonnelModel` oltre a quelli forniti dalla classe `Pivot` di Eloquent, che gestisce la relazione molti-a-molti tra le tabelle.
