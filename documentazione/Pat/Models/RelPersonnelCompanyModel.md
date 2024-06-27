## Modello `RelPersonnelCompanyModel`

Il modello `RelPersonnelCompanyModel` rappresenta la tabella `rel_personnel_company` e rappresenta la relazione tra le tabelle `object_personnel` e `object_company`.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'rel_personnel_company'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.

### Metodi

Non sono presenti metodi aggiuntivi nel modello `RelPersonnelCompanyModel` in quanto estende la classe `Pivot` di Eloquent, che fornisce i metodi di base per la gestione delle relazioni molti-a-molti tra le tabelle.
