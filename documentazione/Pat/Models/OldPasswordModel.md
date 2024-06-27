## Modello `OldPasswordModel`

Il modello `OldPasswordModel` rappresenta la tabella `old_password` e fornisce funzionalità per la gestione delle vecchie password degli utenti.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'old_password'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.

### Metodi

```
boot()
```

Il metodo `boot` inizializza il modello e può essere utilizzato per aggiungere o rimuovere global scopes.
