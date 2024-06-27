## Modello `PersonnelModel`

Il modello `PersonnelModel` rappresenta la tabella `object_personnel` e fornisce funzionalità per la gestione del personale.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'object_personnel'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.
* `$archiveName`: Il nome dell'archivio associato al modello (default: `'personnel'`).
* `$metaModel`: Il nome della classe del modello dei metadati associati (default: `'MetaPersonnelModel'`).
* `$objectName`: Il nome dell'oggetto (default: `'Personale'`).
* `$objectId`: L'ID dell'oggetto (default: `3`).

### Metodi

```
boot()
```

Il metodo `boot` inizializza il modello e può essere utilizzato per aggiungere o rimuovere global scopes.

```
institution(): BelongsTo
```

Il metodo `institution` definisce la relazione con il modello `InstitutionsModel` rappresentante l'ente di appartenenza del personale.

```
role(): BelongsTo
```

Il metodo `role` definisce la relazione con il modello `RoleModel` rappresentante il ruolo del personale.

```
meta(): HasMany
```

Il metodo `meta` definisce la relazione con il modello `MetaPersonnelModel` rappresentante i metadati associati al personale.

```
referent_structures(): BelongsToMany
```

Il metodo `referent_structures` definisce la relazione con il modello `StructuresModel` rappresentante le strutture per cui il personale è referente. La relazione è di tipo molti-a-molti.

```
responsible_structures(): BelongsToMany
```

Il metodo `responsible_structures` definisce la relazione con il modello `StructuresModel` rappresentante le strutture per cui il personale è responsabile. La relazione è di tipo molti-a-molti.

```
structures(): BelongsToMany
```

Il metodo `structures` definisce la relazione con il modello `StructuresModel` rappresentante tutte le strutture in relazione con il personale, indipendentemente dalla tipologia della relazione. La relazione è di tipo molti-a-molti.

```
created_by(): BelongsTo
```

Il metodo `created_by` definisce la relazione con il modello `UsersModel` rappresentante l'utente che ha creato il personale.

```
grants(): BelongsToMany
```

Il metodo `grants` definisce la relazione con il modello `GrantsModel` rappresentante le sovvenzioni associate al personale. La relazione è di tipo molti-a-molti.

```
companies(): BelongsToMany
```

Il metodo `companies` definisce la relazione con il modello `CompanyModel` rappresentante gli enti e le società controllate associate al personale. La relazione è di tipo molti-a-molti.

```
assignments(): BelongsToMany
```

Il metodo `assignments` definisce la relazione con il modello `AssignmentsModel` rappresentante gli incarichi associati al personale. La relazione è di tipo molti-a-molti.

```
responsibles(): BelongsToMany
```

Il metodo `responsibles` definisce la relazione con il modello `ProceedingsModel` rappresentante i procedimenti per i quali il personale è responsabile. La relazione è di tipo molti-a-molti.

```
measure_responsibles(): BelongsToMany
```

Il metodo `measure_responsibles` definisce la relazione con il modello `ProceedingsModel` rappresentante i procedimenti seguiti come responsabile di procedimento dal personale. La relazione è di tipo molti-a-molti.

```
proceedings(): BelongsToMany
```

Il metodo `proceedings` definisce la relazione con il modello `ProceedingsModel` rappresentante i procedimenti a cui il personale è associato. La relazione è di tipo molti-a-molti.

```
measures(): BelongsToMany
```

Il metodo `measures` definisce la relazione con il modello `MeasuresModel` rappresentante i provvedimenti associati al personale. La relazione è di tipo molti-a-molti.

```
commissions(): BelongsToMany
```

Il metodo `commissions` definisce la relazione con il modello `CommissionsModel` rappresentante le commissioni di cui fa parte il personale. La relazione è di tipo molti-a-molti.

```
public_in(): BelongsToMany
```

Il metodo `public_in` definisce la relazione con il modello `SectionFoConfigPublicationArchive` rappresentante le sezioni FO per la gestione del pubblica in. La relazione è di tipo molti-a-molti.

```
political_organ(): BelongsToMany
```

Il metodo `political_organ` definisce la relazione con il modello `SelectDataModel` rappresentante gli organi politici associati al personale. La relazione è di tipo molti-a-molti.

```
attachs(): HasMany
```

Il metodo `attachs` definisce la relazione con il modello `AttachmentsModel` rappresentante gli allegati non nascosti associati al personale. I risultati sono filtrati per il front-office.

```
all_attachs(): HasMany
```

Il metodo `all_attachs` definisce la relazione con il modello `AttachmentsModel` rappresentante tutti gli allegati associati al personale, inclusi quelli nascosti. I risultati sono filtrati per il back-office.

```
public_in_filter(): HasMany
```

Il metodo `public_in_filter` definisce la relazione con il modello `RelPersonnelPublicIn` rappresentante le sezioni FO per la gestione del pubblica in. Utilizzato per la generazione degli open data.

```
scopeFilter(Builder $query, string$name = null): void
```

Il metodo `scopeFilter` è uno scope locale che permette di filtrare il personale in base al nome.

```
historical_datas(): HasMany
```

Il metodo `historical_datas` definisce la relazione con il modello `DataHistoricalPersonnelModel` rappresentante lo storico degli incarichi associati al personale. La relazione è di tipo uno-a-molti.
