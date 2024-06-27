## Modello `RelChargesRegulationsModel`

Il modello `RelChargesRegulationsModel` rappresenta la tabella `rel_charges_regulations` e rappresenta la relazione tra le tabelle `object_charges` e `object_regulations`.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'rel_charges_regulations'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.

### Metodi

Non sono presenti metodi aggiuntivi nel modello `RelChargesRegulationsModel` in quanto estende la classe `Pivot` di Eloquent, che fornisce i metodi di base per la gestione delle relazioni molti-a-molti tra le tabelle.
