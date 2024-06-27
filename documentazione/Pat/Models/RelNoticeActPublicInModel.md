## Modello `RelNoticeActPublicInModel`

Il modello `RelNoticeActPublicInModel` rappresenta la tabella `rel_notice_acts_public_in` e rappresenta la relazione tra le tabelle `object_notice_acts` e le sezioni per il pubblica in.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'rel_notice_acts_public_in'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.

### Metodi

Non sono presenti metodi aggiuntivi nel modello `RelNoticeActPublicInModel` in quanto estende la classe `Pivot` di Eloquent, che fornisce i metodi di base per la gestione delle relazioni molti-a-molti tra le tabelle.
