## Modello `ReliefChecksModel`

Il modello `ReliefChecksModel` rappresenta la tabella `object_relief_checks` e fornisce funzionalità per la gestione dei controlli e rilievi.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'object_relief_checks'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).

### Metodi

```
boot()
```

Il metodo `boot` inizializza il modello e può essere utilizzato per aggiungere o rimuovere global scopes.


```
institution(): BelongsTo
```

Il metodo `institution` definisce la relazione con il modello `InstitutionsModel`, rappresentante l'ente di appartenenza dei controlli e rilievi. La relazione è di tipo uno-a-molti.


```
created_by(): BelongsTo
```

Il metodo `created_by` definisce la relazione con il modello `UsersModel`, rappresentante l'utente che ha creato il controllo o rilievo. La relazione è di tipo uno-a-molti.


```
office(): BelongsTo
```

Il metodo `office` definisce la relazione con il modello `StructuresModel`, rappresentante l'ufficio associato al controllo o rilievo. La relazione è di tipo uno-a-molti.


```
public_in(): BelongsToMany
```

Il metodo `public_in` definisce la relazione con il modello `SectionFoConfigPublicationArchive`, rappresentante le sezioni FO per la gestione del pubblica in dei controlli e rilievi. La relazione è di tipo molti-a-molti.


```
attachs(): HasMany
```

Il metodo `attachs` definisce la relazione con il modello `AttachmentsModel`, restituendo tutti gli allegati non nascosti associati ai controlli e rilievi per il front-office.


```
all_attachs(): HasMany
```

Il metodo `all_attachs` definisce la relazione con il modello `AttachmentsModel`, restituendo tutti gli allegati associati ai controlli e rilievi, inclusi quelli nascosti, per il back-office.


```
scopeFilter(Builder $query, int$structure = null, string$object = null, int$year = null): void
```

Il metodo `scopeFilter` è uno scope locale che permette di filtrare i dati dei controlli e rilievi in base all'ufficio, all'oggetto e all'anno.


```
meta(): HasMany
```

Il metodo `meta` definisce la relazione con il modello `MetaChargesModel`, rappresentante i metadati associati ai controlli e rilievi. La relazione è di tipo uno-a-molti.
