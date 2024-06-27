## Modello `RelInstitutionTypePublicInSection`

Il modello `RelInstitutionTypePublicInSection` rappresenta la tabella `rel_institution_type_public_in_section` e rappresenta la relazione tra i tipi di ente e le configurazioni per il pubblica in. Aggiungendo un record a questa tabella, si aggiunge una voce alla select per il pubblica in per il tipo di ente specificato.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'rel_institution_type_public_in_section'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.

### Metodi

Non sono presenti metodi aggiuntivi nel modello `RelInstitutionTypePublicInSection` in quanto estende la classe `Pivot` di Eloquent, che fornisce i metodi di base per la gestione delle relazioni molti-a-molti tra le tabelle.
