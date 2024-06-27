## Modello `ConcurrentSessModel`

Il modello `ConcurrentSessModel` rappresenta la tabella `concurrent_sess` e fornisce funzionalità per la gestione delle sessioni concorrenti.

### Lista dei metodi

```
boot()
```

Il metodo `boot` inizializza il modello e aggiunge lo scope globale `InstitutionScope` per filtrare i dati in base all'ente.

```
institution(): BelongsTo
```

La relazione `institution` rappresenta la struttura organizzativa di appartenenza.

* Restituisce: Oggetto `BelongsTo` che rappresenta la relazione.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'concurrent_sess'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.
