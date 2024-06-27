## Modello `RelContestsActsSupplieListModel`

Il modello `RelContestsActsSupplieListModel` rappresenta la tabella `rel_contests_acts_supplie_list` e rappresenta la relazione tra le tabelle `object_contests_acts` e `object_supplie_list`.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'rel_contests_acts_supplie_list'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.

### Metodi

Non sono presenti metodi aggiuntivi nel modello `RelContestsActsSupplieListModel` in quanto estende la classe `Pivot` di Eloquent, che fornisce i metodi di base per la gestione delle relazioni molti-a-molti tra le tabelle.
