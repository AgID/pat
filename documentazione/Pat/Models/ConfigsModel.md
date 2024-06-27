## Modello `ConfigsModel`

Il modello `ConfigsModel`  rappresenta la tabella `configs` e fornisce funzionalità per la gestione delle configurazioni.

### Lista dei metodi

```
boot()
```

Il metodo `boot` inizializza il modello e aggiunge lo scope globale `InstitutionScope` per filtrare i dati in base all'ente.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'configs'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.
