## Modello `PermitsModel`

Il modello `PermitsModel` rappresenta la tabella `permits` e fornisce funzionalità per la gestione dei permessi degli utenti sulle sezioni in base ai loro profili ACL.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'permits'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.

### Metodi

```
boot()
```

Il metodo `boot` inizializza il modello e può essere utilizzato per aggiungere o rimuovere global scopes.
