## Modello `NoticesActsModel`

Il modello `NoticesActsModel` rappresenta la tabella `object_notices_acts` e fornisce funzionalità per la gestione degli atti delle amministrazioni.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'object_notices_acts'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.
* `$metaModel`: Il nome della classe del modello dei metadati associati (default: `'MetaNoticesActsModel'`).
* `$archiveName`: Il nome dell'archivio associato al modello (default: `'notices_acts'`).
* `$objectName`: Il nome dell'oggetto (default: `'Atti delle amministrazioni'`).
* `$objectId`: L'ID dell'oggetto (default: `20`).

### Metodi

```
boot()
```

Il metodo `boot` inizializza il modello e può essere utilizzato per aggiungere o rimuovere global scopes.

```
institution(): BelongsTo
```

Il metodo `institution` definisce la relazione con il modello `InstitutionsModel` rappresentante l'ente di appartenenza dell'atto amministrativo.

```
created_by(): BelongsTo
```

Il metodo `created_by` definisce la relazione con il modello `UsersModel` rappresentante l'utente che ha creato l'atto amministrativo.

```
relative_contest_act(): BelongsToMany
```

Il metodo `relative_contest_act` definisce la relazione con il modello `ContestsActsModel` rappresentante le procedure relative dell'atto amministrativo. La relazione è di tipo molti-a-molti.

```
scp(): HasMany
```

Il metodo `scp` definisce la relazione con il modello `MetaNoticesActsModel` rappresentante i dati aggiuntivi per SCP associati all'atto amministrativo.

```
assignments(): BelongsToMany
```

Il metodo `assignments` definisce la relazione con il modello `AssignmentsModel` rappresentante gli incarichi associati all'atto amministrativo. La relazione è di tipo molti-a-molti.

```
public_in(): BelongsToMany
```

Il metodo `public_in` definisce la relazione con il modello `SectionFoConfigPublicationArchive` rappresentante il pubblica in come criterio di pubblicazione dell'atto amministrativo. La relazione è di tipo molti-a-molti.

```
public_in_section(): HasMany
```

Il metodo `public_in_section` definisce la relazione con il modello `RelNoticeActPublicInModel` rappresentante le sezioni FO per la gestione del pubblica in come criterio di pubblicazione dell'atto amministrativo.

```
attachs(): HasMany
```

Il metodo `attachs` definisce la relazione con il modello `AttachmentsModel` rappresentante gli allegati non nascosti associati all'atto amministrativo. I risultati sono filtrati per il front-office.

```
all_attachs(): HasMany
```

Il metodo `all_attachs` definisce la relazione con il modello `AttachmentsModel` rappresentante tutti gli allegati associati all'atto amministrativo, inclusi quelli nascosti. I risultati sono filtrati per il back-office.

```
scopeFilter(Builder $query, string$object = null, string$startDate = null, string$endDate = null): void
```

Il metodo `scopeFilter` è uno scope locale che permette di filtrare i dati degli atti amministrativi in base all'oggetto, alla data di inizio e alla data di fine.

```
meta(): HasMany
```

Il metodo `meta` definisce la relazione con il modello `MetaNoticesActsModelEcco la documentazione per il modello `NoticesActsModel`:
