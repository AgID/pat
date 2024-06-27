## Modello `RelCommissionsPersonnelModel`

Il modello `RelCommissionsPersonnelModel` rappresenta la tabella `rel_commissions_personnel` e rappresenta la relazione tra le tabelle `object_commissions` e `object_personnel_id`.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'rel_commissions_personnel'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.

### Metodi

Non sono presenti metodi aggiuntivi nel modello `RelCommissionsPersonnelModel` in quanto estende la classe `Pivot` di Eloquent, che fornisce i metodi di base per la gestione delle relazioni molti-a-molti tra le tabelle.
