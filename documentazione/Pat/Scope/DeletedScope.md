## Classe `DeletedScope`

La classe `DeletedScope`  implementa uno scope per filtrare gli elementi non eliminati nelle query del modello.

### Lista dei metodi

```
apply(Builder $builder, Model $model): void
```

Il metodo `apply` applica lo scope al builder della query, aggiungendo il filtro per gli elementi non eliminati.

* Parametri:
  * `$builder`: L'oggetto Builder che rappresenta la query.
  * `$model`: L'istanza del modello a cui applicare lo scope.
