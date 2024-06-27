## Modello `ModulesRegulationsModel`

Il modello `ModulesRegulationsModel` rappresenta la tabella `object_modules_regulations` e fornisce funzionalità per la gestione della modulistica.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'object_modules_regulations'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.
* `$metaModel`: Il nome della classe del modello dei metadati associati (default: `'MetaModulesRegulationsModel'`).
* `$archiveName`: Il nome dell'archivio associato al modello (default: `'modules_regulations'`).
* `$objectName`: Il nome dell'oggetto (default: `'Modulistica'`).
* `$objectId`: L'ID dell'oggetto (default: `13`).

### Metodi

```
boot()
```

Il metodo `boot` inizializza il modello e può essere utilizzato per aggiungere o rimuovere global scopes.

```
institution(): BelongsTo
```

Il metodo `institution` definisce la relazione con il modello `InstitutionsModel` rappresentante l'ente di appartenenza del modulo.

```
created_by(): BelongsTo
```

Il metodo `created_by` definisce la relazione con il modello `UsersModel` rappresentante l'utente che ha creato il modulo.

```
proceedings(): BelongsToMany
```

Il metodo `proceedings` definisce la relazione con il modello `ProceedingsModel` rappresentante i procedimenti associati al modulo. La relazione è di tipo molti-a-molti.

```
attachs(): HasMany
```

Il metodo `attachs` definisce la relazione con il modello `AttachmentsModel` rappresentante gli allegati non nascosti associati al modulo. I risultati sono filtrati per il front-office.

```
all_attachs(): HasMany
```

Il metodo `all_attachs` definisce la relazione con il modello `AttachmentsModel` rappresentante tutti gli allegati associati al modulo, inclusi quelli nascosti. I risultati sono filtrati per il back-office.

```
scopeFilter(Builder $query, string$text = null): void
```

Il metodo `scopeFilter` è uno scope locale che permette di filtrare i dati dei moduli in base al titolo. Viene utilizzato nel filtro di ricerca.

```
meta(): HasMany
```

Il metodo `meta` definisce la relazione con il modello `MetaModulesRegulationsModel` rappresentante i metadati associati al modulo.
