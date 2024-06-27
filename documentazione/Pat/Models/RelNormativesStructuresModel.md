## Modello `RelNormativesStructuresModel`

Il modello `RelNormativesStructuresModel` rappresenta la tabella `rel_normatives_structures` e gestisce la relazione molti-a-molti tra le tabelle `object_normatives` e `object_structures`.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'rel_normatives_structures'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.

### Metodi

Non sono presenti metodi aggiuntivi nel modello `RelNormativesStructuresModel` oltre a quelli forniti dalla classe `Pivot` di Eloquent.
