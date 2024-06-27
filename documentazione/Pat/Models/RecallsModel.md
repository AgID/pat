## Modello `RecallsModel`

Il modello `RecallsModel` rappresenta la tabella `recalls` e fornisce funzionalità per la gestione dei richiami nelle pagine dei record degli oggetti.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'recalls'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.

### Metodi

```
boot()
```

Il metodo `boot` inizializza il modello e può essere utilizzato per aggiungere o rimuovere global scopes.

```
items(string$model): HasMany
```

Il metodo `items` definisce la relazione con un modello specificato e restituisce gli elementi associati al richiamo. Il parametro `$model` indica il nome del modello associato al richiamo. La relazione è di tipo uno-a-molti.
