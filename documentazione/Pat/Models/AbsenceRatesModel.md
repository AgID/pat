## Classe `AbsenceRatesModel`

La classe `AbsenceRatesModel`  rappresenta il modello per la tabella `object_absence_rates` e fornisce funzionalità per la gestione dei tassi di assenza.

### Lista dei metodi

```
__construct(array$attributes = [])
```

Il costruttore della classe `AbsenceRatesModel` inizializza il modello.

* Parametri:
  * `$attributes`: (Opzionale) Un array di attributi per il modello.

### Metodi di relazione

La classe `AbsenceRatesModel` dichiara diverse relazioni con altri modelli utilizzando i metodi `belongsTo`, `hasMany` e `hasOne`.

### Proprietà `searchable` e `searchableWhereHas`

Le proprietà `searchable` e `searchableWhereHas` specificano i campi su cui effettuare la ricerca nel datatable e nelle relazioni dei modelli.
