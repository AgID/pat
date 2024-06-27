## Trait `SearchLikeTrait`

Il trait `SearchLikeTrait` fornisce funzionalità per la ricerca di corrispondenze parziali nei campi dei modelli utilizzando l'operatore `LIKE`.

### Lista dei metodi

```
scopeSearch($query, $keyword, $matchAllFields = false)
```

Il metodo `scopeSearch` applica lo scope di ricerca parziale ai campi specificati nel modello.

* Parametri:
  * `$query`: L'istanza dell'oggetto Builder che rappresenta la query.
  * `$keyword`: La parola chiave di ricerca.
  * `$matchAllFields`: (Opzionale) Un flag booleano che indica se corrispondere in tutti i campi o in almeno uno dei campi (default: `false`).
* Restituisce l'istanza dell'oggetto Builder con lo scope di ricerca parziale applicato.

```
getSearchableFields()
```

Il metodo `getSearchableFields` restituisce un array dei campi sul modello su cui applicare la ricerca.

* Restituisce un array dei campi sul modello su cui applicare la ricerca.
