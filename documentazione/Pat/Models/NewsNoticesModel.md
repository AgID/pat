## Modello `NewsNoticesModel`

Il modello `NewsNoticesModel` rappresenta la tabella `object_news_notices` e fornisce funzionalità per la gestione delle news e degli avvisi.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'object_news_notices'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.
* `$metaModel`: Il nome della classe del modello dei metadati associati (default: `'MetaNewsNoticeModel'`).
* `$archiveName`: Il nome dell'archivio associato al modello (default: `'news_notices'`).
* `$objectName`: Il nome dell'oggetto (default: `'News ed avvisi'`).
* `$objectId`: L'ID dell'oggetto (default: `49`).

### Metodi

```
boot()
```

Il metodo `boot` inizializza il modello e può essere utilizzato per aggiungere o rimuovere global scopes.

```
institution(): BelongsTo
```

Il metodo `institution` definisce la relazione con il modello `InstitutionsModel` rappresentante l'ente di appartenenza della news o dell'avviso.

Il metodo `created_by` definisce la relazione con il modello `UsersModel` rappresentante l'utente che ha creato la news o l'avviso.

```
attachs(): HasMany
```

Il metodo `attachs` definisce la relazione con il modello `AttachmentsModel` rappresentante gli allegati non nascosti associati alla news o all'avviso. I risultati sono filtrati per il front-office.

```
all_attachs(): HasMany
```

Il metodo `all_attachs` definisce la relazione con il modello `AttachmentsModel` rappresentante tutti gli allegati associati alla news o all'avviso, inclusi quelli nascosti. I risultati sono filtrati per il back-office.

```
scopeFilter(Builder $query, string$title = null, string$startDate = null, string$endDate = null): void
```

Il metodo `scopeFilter` è uno scope locale che permette di filtrare i dati delle news o degli avvisi in base al titolo, alla data di inizio e alla data di fine.

```
meta(): HasMany
```

Il metodo `meta` definisce la relazione con il modello `MetaNewsNoticeModel` rappresentante i metadati associati alla news o all'avviso.
