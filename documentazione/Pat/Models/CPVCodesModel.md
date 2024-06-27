## Modello `CPVCodesModel`

Il modello `CPVCodesModel`  rappresenta la tabella `cpv_codes` e fornisce funzionalità per la gestione dei codici CPV (Requisiti di qualificazione).

### Lista dei metodi

#### Proprietà `searchable`

La proprietà `searchable` specifica i campi su cui effettuare la ricerca.

```
boot()
```

Il metodo `boot` inizializza il modello.

```
getFullDescriptionAttribute(): string
```

Il metodo `getFullDescriptionAttribute` restituisce la descrizione completa del codice CPV.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'cpv_codes'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.
* `$searchable`: Un array di nomi dei campi su cui effettuare la ricerca nel datatable.
