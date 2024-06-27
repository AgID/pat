## Modello `AttemptsModel`

Il modello `AttemptsModel`  rappresenta la tabella `attempts` e fornisce funzionalità per la gestione dei tentativi di accesso in fase di autenticazione.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'attempts'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.
