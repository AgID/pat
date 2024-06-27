## Modello `ContentSectionFoModel`

Il modello `ContentSectionFoModel` rappresenta la tabella `content_section_fo` e fornisce funzionalità per la gestione delle sezioni di contenuto per il front office.

### Listav dei metodi

```
__construct(array$attributes = [])
```

Il costruttore del modello `ContentSectionFoModel` inizializza il modello.

* Parametri:
  * `$attributes`: (Opzionale) Un array di attributi per il modello.

```
boot()
```

Il metodo `boot` inizializza il modello e aggiunge lo scope globale `InstitutionScope` per filtrare i dati in base all'ente.

#### Metodi di relazione

Il modello `ContentSectionFoModel` dichiara diverse relazioni con altri modelli utilizzando i metodi `belongsTo` e `hasMany`.

#### Proprietà `searchable`

La proprietà `searchable` specifica i campi su cui effettuare la ricerca nel datatable.

### Proprietà

* `$table`: Il nome della tabella nel database (default: `'content_section_fo'`).
* `$primaryKey`: Il nome della chiave primaria nella tabella (default: `'id'`).
* `$fillable`: Un array dei nomi delle colonne che possono essere assegnate in massa.
* `$searchable`: Un array di nomi dei campi su cui effettuare la ricerca nel datatable.
