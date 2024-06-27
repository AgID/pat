## Modello `RecoveryPassword`

Il modello `RecoveryPassword` rappresenta la tabella `recovery_password` e fornisce funzionalità per il recupero delle password.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'recovery_password'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.

### Metodi

```
user(): HasOne
```
Il metodo `user` definisce la relazione con il modello `UsersModel`, rappresentante l'utente associato al recupero password. La relazione è di tipo uno-a-uno.

```
boot(): void
```
Il metodo `boot` inizializza il modello e può essere utilizzato per aggiungere o rimuovere global scopes.
