## Modello `RealEstateAssetModel`

Il modello `RealEstateAssetModel` rappresenta la tabella `object_real_estate_asset` e fornisce funzionalità per la gestione dei patrimoni immobiliari.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'object_real_estate_asset'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.
* `$metaModel`: Il nome della classe del modello dei metadati associati (default: `'MetaRealEstateAssetModel'`).
* `$archiveName`: Il nome dell'archivio associato al modello (default: `'real_estate_asset'`).
* `$objectName`: Il nome dell'oggetto (default: `'Patrimonio immobiliare'`).
* `$objectId`: L'ID dell'oggetto (default: `8`).

### Metodi

```
boot()
```

Il metodo `boot` inizializza il modello e può essere utilizzato per aggiungere o rimuovere global scopes.

```
institution(): BelongsTo
```

Il metodo `institution` definisce la relazione con il modello `InstitutionsModel`, rappresentante l'ente di appartenenza dei patrimoni immobiliari.

```
created_by(): BelongsTo
```

Il metodo `created_by` definisce la relazione con il modello `UsersModel`, rappresentante l'utente che ha creato il patrimonio immobiliare.

```
attachs(): HasMany
```

Il metodo `attachs` definisce la relazione con il modello `AttachmentsModel`, restituendo tutti gli allegati non nascosti associati ai patrimoni immobiliari per il front-office.

```
all_attachs(): HasMany
```

Il metodo `all_attachs` definisce la relazione con il modello `AttachmentsModel`, restituendo tutti gli allegati associati ai patrimoni immobiliari, inclusi quelli nascosti, per il back-office.

```
scopeFilter(Builder $query, string$name = null, string$address = null): void
```

Il metodo `scopeFilter` è uno scope locale che permette di filtrare i dati dei patrimoni immobiliari in base al nome e all'indirizzo.

```
meta(): HasMany
```

Il metodo `meta` definisce la relazione con il modello `MetaRealEstateAssetModel`, rappresentante i metadati associati ai patrimoni immobiliari.

```
offices(): BelongsToMany
```

Il metodo `offices` definisce la relazione con il modello `StructuresModel`, rappresentante gli uffici utilizzatori dei patrimoni immobiliari. La relazione è di tipo molti-a-molti.

```
canons(): BelongsToMany
```

Il metodo `canons` definisce la relazione con il modello `LeaseCanonsModel`, rappresentante i canoni di locazione associati ai patrimoni immobiliari. La relazione è di tipo molti-a-molti.
