## Modello `RelNormativeReferencesSectionsFoModel`

Il modello `RelNormativeReferencesSectionsFoModel` rappresenta la tabella `rel_normative_references_sections_fo` e rappresenta la relazione tra le tabelle `object_normative_references` e `sections_fo`. Questa relazione viene utilizzata per gestire le sezioni.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'rel_normative_references_sections_fo'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.

### Metodi

Non sono presenti metodi aggiuntivi nel modello `RelNormativeReferencesSectionsFoModel` in quanto estende la classe `Pivot` di Eloquent, che fornisce i metodi di base per la gestione delle relazioni molti-a-molti tra le tabelle.
