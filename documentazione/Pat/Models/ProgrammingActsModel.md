## Modello `ProgrammingActsModel`

Il modello `ProgrammingActsModel` rappresenta la tabella `object_programming_acts` e fornisce funzionalità per la gestione degli atti di programmazione.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'object_programming_acts'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.
* `$metaModel`: Il nome della classe del modello dei metadati associati (default: `'MetaProgrammingActsModel'`).
* `$archiveName`: Il nome dell'archivio associato al modello (default: `'programming_acts'`).
* `$objectName`: Il nome dell'oggetto (default: `'Atti di programmazione'`).
* `$objectId`: L'ID dell'oggetto (default: `22`).

### Metodi


```
boot()
```

Il metodo `boot` inizializza il modello e può essere utilizzato per aggiungere o rimuovere global scopes.


```
institution(): BelongsTo
```

Il metodo `institution` definisce la relazione con il modello `InstitutionsModel`, rappresentante l'ente di appartenenza degli atti di programmazione.


```
created_by(): BelongsTo
```

Il metodo `created_by` definisce la relazione con il modello `UsersModel`, rappresentante l'utente che ha creato l'atto di programmazione.


```
attachs(): HasMany
```

Il metodo `attachs` definisce la relazione con il modello `AttachmentsModel`, restituendo tutti gli allegati non nascosti associati agli atti di programmazione per il front-office.


```
all_attachs(): HasMany
```

Il metodo `all_attachs` definisce la relazione con il modello `AttachmentsModel`, restituendo tutti gli allegati associati agli atti di programmazione, inclusi quelli nascosti, per il back-office.


```
scopeFilter(Builder $query, string$object = null, string$description = null, string$startDate = null, string$endDate = null): void
```

Il metodo `scopeFilter` è uno scope locale che permette di filtrare i dati degli atti di programmazione in base all'oggetto, alla descrizione, alla data di inizio e alla data di fine.


```
meta(): HasMany
```

Il metodo `meta` definisce la relazione con il modello `MetaProgrammingActsModel`, rappresentante i metadati associati agli atti di programmazione.
