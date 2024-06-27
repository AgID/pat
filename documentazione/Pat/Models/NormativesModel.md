## Modello `NormativesModel`

Il modello `NormativesModel` rappresenta la tabella `object_normatives` e fornisce funzionalità per la gestione delle normative.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'object_normatives'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.
* `$metaModel`: Il nome della classe del modello dei metadati associati (default: `'MetaNormativesModel'`).
* `$archiveName`: Il nome dell'archivio associato al modello (default: `'normatives'`).
* `$objectName`: Il nome dell'oggetto (default: `'Normativa'`).
* `$objectId`: L'ID dell'oggetto (default: `14`).

### Metodi

```
boot()
```

Il metodo `boot` inizializza il modello e può essere utilizzato per aggiungere o rimuovere global scopes.

```
institution(): BelongsTo
```

Il metodo `institution` definisce la relazione con il modello `InstitutionsModel` rappresentante l'ente di appartenenza della normativa.

```
created_by(): BelongsTo
```

Il metodo `created_by` definisce la relazione con il modello `UsersModel` rappresentante l'utente che ha creato la normativa.

```
structures(): BelongsToMany
```

Il metodo `structures` definisce la relazione con il modello `StructuresModel` rappresentante le strutture per cui è valida la normativa. La relazione è di tipo molti-a-molti.

```
allStructures(): BelongsToMany
```

Il metodo `allStructures` definisce la relazione con il modello `StructuresModel` rappresentante tutte le strutture in relazione con la normativa, indipendentemente dalla tipologia della relazione.

```
grants(): BelongsToMany
```

Il metodo `grants` definisce la relazione con il modello `GrantsModel` rappresentante le sovvenzioni associate alla normativa. La relazione è di tipo molti-a-molti.

```
proceedings(): BelongsToMany
```

Il metodo `proceedings` definisce la relazione con il modello `ProceedingsModel` rappresentante i riferimenti normativi associati al procedimento. La relazione è di tipo molti-a-molti.

```
attachs(): HasMany
```

Il metodo `attachs` definisce la relazione con il modello `AttachmentsModel` rappresentante gli allegati non nascosti associati alla normativa. I risultati sono filtrati per il front-office.

```
all_attachs(): HasMany
```

Il metodo `all_attachs` definisce la relazione con il modello `AttachmentsModel` rappresentante tutti gli allegati associati alla normativa, inclusi quelli nascosti. I risultati sono filtrati per il back-office.

```
scopeFilter(Builder $query, string$normativeName = null, string$typology = null, string$number = null, string$startDate = null, string$endDate = null, int$office = null): void
```

Il metodo `scopeFilter` è uno scope locale che permette di filtrare i dati delle normative in base al nome, alla tipologia, al numero, alla data di inizio, alla data di fine e all'ufficio.

```
meta(): HasMany
```

Il metodo `meta` definisce la relazione con il modello `MetaNormativesModel` rappresentante i metadati associati alla normativa.
