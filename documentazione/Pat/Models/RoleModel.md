## Modello `RoleModel`

Il modello `RoleModel` rappresenta la tabella `role` nel database e gestisce i ruoli per il personale.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'role'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.

### Metodi

Il modello `RoleModel` eredita i metodi dalla classe base `Model` di Eloquent, che gestisce le operazioni di base per l'interazione con il database.
