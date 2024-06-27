## Modello `CategoriesModel`

Il modello `CategoriesModel` rappresenta la tabella `categories` e fornisce funzionalità per la gestione delle categorie per gli allegati.

### Lista dei metodi

```
boot()
```

Il metodo `boot` inizializza il modello e aggiunge lo scope globale `InstitutionScope` per filtrare i dati in base all'ente.

```
scopeFilterPublished($query): void
```

Il metodo `scopeFilterPublished` è uno scope locale per il filtraggio dei dati pubblicati.

* Parametri:
  * `$query`: L'oggetto di query.
