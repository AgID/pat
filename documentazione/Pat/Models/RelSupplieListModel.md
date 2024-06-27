## Modello `RelSupplieListModel`

Il modello `RelSupplieListModel` rappresenta la tabella `rel_supplie_list` e gestisce la relazione tra la tabella `object_supplie_list` e se stessa.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'rel_supplie_list'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.

### Metodi

Non sono presenti metodi aggiuntivi nel modello `RelSupplieListModel` oltre a quelli forniti dalla classe `Pivot` di Eloquent, che gestisce la relazione molti-a-molti tra le tabelle.
