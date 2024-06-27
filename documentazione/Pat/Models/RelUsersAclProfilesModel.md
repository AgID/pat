## Modello `RelUsersAclProfilesModel`

Il modello `RelUsersAclProfilesModel` rappresenta la tabella `rel_users_acl_profiles` e gestisce la relazione tra le tabelle `users` e `acl_profiles`.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'rel_users_acl_profiles'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.

### Metodi

  ```
  publicfunctionprofile(): HasOne
  ```
  Questo metodo definisce la relazione `hasOne` con il modello `AclProfilesModel` per rappresentare l'ente di appartenenza dell'utente.

