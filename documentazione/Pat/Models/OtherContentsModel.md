## Modello `OtherContentsModel`

Il modello `OtherContentsModel` `Model` rappresenta la tabella `object_other_contents` e fornisce funzionalità per la gestione di altri contenuti.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'object_other_contents'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.

### Metodi

```
boot()
```

Il metodo `boot` inizializza il modello e può essere utilizzato per aggiungere o rimuovere global scopes.
