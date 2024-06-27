## Modello `PasswordHistoryModel`

Il modello `PasswordHistoryModel` rappresenta la tabella `password_history` e fornisce funzionalità per la gestione della cronologia delle password.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'password_history'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.

### Metodi

```
user(): BelongsTo
```

Il metodo `user` definisce la relazione con il modello `UsersModel` rappresentante l'utente associato alla cronologia delle password.
