## Modello `ProceedingsModel`

Il modello `ProceedingsModel`  rappresenta la tabella `object_proceedings` e fornisce funzionalità per la gestione dei procedimenti.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'object_proceedings'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.
* `$metaModel`: Il nome della classe del modello dei metadati associati (default: `'MetaProceedingsModel'`).
* `$archiveName`: Il nome dell'archivio associato al modello (default: `'proceedings'`).
* `$objectName`: Il nome dell'oggetto (default: `'Procedimenti'`).
* `$objectId`: L'ID dell'oggetto (default: `7`).

### Metodi


```
boot()
```

Il metodo `boot` inizializza il modello e può essere utilizzato per aggiungere o rimuovere global scopes.


```
institution(): BelongsTo
```

Il metodo `institution` definisce la relazione con il modello `InstitutionsModel` rappresentante l'ente di appartenenza del procedimento.


```
created_by(): BelongsTo
```

Il metodo `created_by` definisce la relazione con il modello `UsersModel` rappresentante l'utente che ha creato il procedimento.


```
responsibles(): BelongsToMany
```

Il metodo `responsibles` definisce la relazione con il modello `PersonnelModel` rappresentante il personale responsabile del procedimento. La relazione è di tipo molti-a-molti.


```
measure_responsibles(): BelongsToMany
```

Il metodo `measure_responsibles` definisce la relazione con il modello `PersonnelModel` rappresentante il personale responsabile del provvedimento. La relazione è di tipo molti-a-molti.


```
substitute_responsibles(): BelongsToMany
```

Il metodo `substitute_responsibles` definisce la relazione con il modello `PersonnelModel` rappresentante il personale responsabile sostitutivo. La relazione è di tipo molti-a-molti.


```
to_contacts(): BelongsToMany
```

Il metodo `to_contacts` definisce la relazione con il modello `PersonnelModel` rappresentante il personale responsabile dei contatti. La relazione è di tipo molti-a-molti.


```
personnel(): BelongsToMany
```

Il metodo `personnel` definisce la relazione con il modello `PersonnelModel` rappresentante tutto il personale in relazione con il procedimento, indipendentemente dalla tipologia della relazione. La relazione è di tipo molti-a-molti.


```
offices_responsibles(): BelongsToMany
```

Il metodo `offices_responsibles` definisce la relazione con il modello `StructuresModel` rappresentante gli uffici responsabili del procedimento. La relazione è di tipo molti-a-molti.


```
other_structures(): BelongsToMany
```

Il metodo `other_structures` definisce la relazione con il modello `StructuresModel` rappresentante le altre strutture associate al procedimento. La relazione è di tipo molti-a-molti.


```
structures(): BelongsToMany
```

Il metodo `structures` definisce la relazione con il modello `StructuresModel` rappresentante tutte le strutture associate al procedimento, indipendentemente dalla tipologia della relazione. La relazione è di tipo molti-a-molti.


```
normatives(): BelongsToMany
```

Il metodo `normatives` definisce la relazione con il modello `NormativesModel` rappresentante i riferimenti normativi associati al procedimento. La relazione è di tipo molti-a-molti.


```
modules(): BelongsToMany
```

Il metodo `modules` definisce la relazione con il modello `ModulesRegulationsModel` rappresentante la modulistica per cui è valido il procedimento. La relazione è di tipo molti-a-molti.


```
regulations(): BelongsToMany
```

Il metodo `regulations` definisce la relazione con il modello `RegulationsModel` rappresentante i regolamenti e la documentazione valida per il procedimento. La relazione è di tipo molti-a-molti.


```
monitoring_datas(): HasMany
```

Il metodo `monitoring_datas` definisce la relazione con il modello `DataMonitoringProceedings` rappresentante i dati di monitoraggio associati al procedimento. La relazione è di tipo uno-a-molti.


```
charges(): BelongsToMany
```

Il metodo `charges` definisce la relazione con il modello `ChargesModel` rappresentante gli oneri informativi validi per il procedimento. La relazione è di tipo molti-a-molti.


```
meta(): HasMany
```

Il metodo `meta` definisce la relazione con il modello `MetaProceedingsModel` rappresentante i metadati associati al procedimento. La relazione è di tipo uno-a-molti.


```
attachs(): HasMany
```

Il metodo `attachs` definisce la relazione con il modello `AttachmentsModel` rappresentante gli allegati non nascosti associati al procedimento. I risultati sono filtrati per il front-office.


```
all_attachs(): HasMany
```

Il metodo `all_attachs` definisce la relazione con il modello `AttachmentsModel` rappresentante tutti gli allegati associati al procedimento, inclusi quelli nascosti. I risultati sono filtrati per il back-office.


```
scopeFilter(Builder $query, string$name = null, int$structures = null): void
```

Il metodo `scopeFilter` è uno scope locale che permette di filtrare i dati dei procedimenti in base al nome e all'ID della struttura.

```
scopeProceedingFilterDataTable(Builder $query, int$structures = null, int$responsibles = null): void
```

Il metodo `scopeProceedingFilterDataTable` è uno scope locale che permette di filtrare i dati dei procedimenti per il DataTable in base all'ID della struttura e all'ID del responsabile.
