## Modello `RegulationsModel`

Il modello `RegulationsModel` rappresenta la tabella `object_regulations` e fornisce funzionalità per la gestione dei regolamenti e della documentazione.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'object_regulations'`).
* `$archiveName`: Il nome dell'archivio associato al modello (default: `'regulations'`).
* `$metaModel`: Il nome della classe del modello dei metadati associati (default: `'MetaRegulationModel'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$objectName`: Il nome dell'oggetto (default: `'Regolamenti e documentazione'`).
* `$objectId`: L'ID dell'oggetto (default: `12`).

### Metodi

```
boot()
```

Il metodo `boot` inizializza il modello e può essere utilizzato per aggiungere o rimuovere global scopes.


```
institution(): BelongsTo
```

Il metodo `institution` definisce la relazione con il modello `InstitutionsModel`, rappresentante l'ente di appartenenza dei regolamenti e della documentazione.


```
created_by(): BelongsTo
```

Il metodo `created_by` definisce la relazione con il modello `UsersModel`, rappresentante l'utente che ha creato il regolamento o la documentazione.


```
structures(): BelongsToMany
```

Il metodo `structures` definisce la relazione con il modello `StructuresModel`, rappresentante le strutture per cui è valido il regolamento o la documentazione. La relazione è di tipo molti-a-molti.


```
proceedings(): BelongsToMany
```

Il metodo `proceedings` definisce la relazione con il modello `ProceedingsModel`, rappresentante i procedimenti per cui è valido il regolamento o la documentazione. La relazione è di tipo molti-a-molti.


```
public_in(): BelongsToMany
```

Il metodo `public_in` definisce la relazione con il modello `SectionFoConfigPublicationArchive`, rappresentante le sezioni FO per la gestione del pubblica in dei regolamenti o della documentazione. La relazione è di tipo molti-a-molti.


```
public_in_filter(): HasMany
```

Il metodo `public_in_filter` definisce la relazione con il modello `RelRegulationsPublicIn`, rappresentante i filtri per la gestione del pubblica in dei regolamenti o della documentazione. La relazione è di tipo uno-a-molti.


```
charges(): BelongsToMany
```

Il metodo `charges` definisce la relazione con il modello `ChargesModel`, rappresentante gli oneri associati al regolamento o alla documentazione. La relazione è di tipo molti-a-molti.


```
interventions(): BelongsToMany
```

Il metodo `interventions` definisce la relazione con il modello `InterventionsModel`, rappresentante gli interventi associati al regolamento o alla documentazione. La relazione è di tipo molti-a-molti.


```
attachs(): HasMany
```

Il metodo `attachs` definisce la relazione con il modello `AttachmentsModel`, restituendo tutti gli allegati non nascosti associati ai regolamenti o alla documentazione per il front-office.


```
all_attachs(): HasMany
```

Il metodo `all_attachs` definisce la relazione con il modello `AttachmentsModel`, restituendo tutti gli allegati associati ai regolamenti o alla documentazione, inclusi quelli nascosti, per il back-office.


```
scopeFilter(Builder $query, string$text = null): void
```

Il metodo `scopeFilter` è uno scope locale che permette di filtrare i dati dei regolamenti e della documentazione in base al titolo.


```
meta(): HasMany
```

Il metodo `meta` restituisce una relazione `HasMany` con il modello `MetaRegulationModel`. La relazione viene stabilita tramite la chiave esterna `'reference_id'` dell'istanza corrente del modello `RegulationsModel` e la chiave primaria `'id'` del modello `MetaRegulationModel`.
