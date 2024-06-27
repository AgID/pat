## Modello `MetaInstitutionModel`

Il modello `MetaInstitutionModel`  rappresenta la tabella `meta_institutions` e fornisce funzionalità per la gestione dei metadati delle istituzioni.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'meta_institutions'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.

### Lista dei metodi

```
boot()
```

Il metodo `boot` inizializza il modello e può essere utilizzato per aggiungere o rimuovere global scopes.
