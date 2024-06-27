## Modello `RecallsConfigurationModel`

Il modello `RecallsConfigurationModel` rappresenta la tabella `recalls_configuration` e fornisce funzionalità per la gestione della configurazione dei richiami nelle pagine.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'recalls_configuration'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.

### Metodi

```
boot()
```
Il metodo `boot` inizializza il modello e può essere utilizzato per aggiungere o rimuovere global scopes.


```
recalls(): HasMany
```
Il metodo `recalls` definisce la relazione con il modello `RecallsModel`, rappresentante i richiami associati alla configurazione. La relazione è di tipo uno-a-molti.


```
sectionBo(): BelongsTo
```
Il metodo `sectionBo` definisce la relazione con il modello `SectionsBoModel`, rappresentante la sezione BO associata alla configurazione. La relazione è di tipo molti-a-uno.
