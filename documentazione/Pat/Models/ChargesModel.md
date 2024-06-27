## Modello `ChargesModel`

Il modello `ChargesModel` rappresenta la tabella `object_charges` e fornisce funzionalità per la gestione degli oneri informativi e degli obblighi.

### Lista dei metodi

```
__construct(array$attributes = [])
```

Il costruttore del modello `ChargesModel` inizializza il modello.

* Parametri:
  * `$attributes`: (Opzionale) Un array di attributi per il modello.

```
boot()
```

Il metodo `boot` inizializza il modello e aggiunge lo scope globale `InstitutionScope` per filtrare i dati in base all'ente.

#### Metodi di relazione

Il modello `ChargesModel` dichiara diverse relazioni con altri modelli utilizzando i metodi `belongsTo`, `belongsToMany` e `hasMany`.

#### Proprietà `searchable` e `searchableWhereHas`

Le proprietà `searchable` e `searchableWhereHas` specificano i campi su cui effettuare la ricerca nel datatable e nelle relazioni dei modelli.
