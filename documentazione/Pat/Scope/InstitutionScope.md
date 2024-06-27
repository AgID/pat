## Classe `InstitutionScope`

La classe `InstitutionScope`  implementa uno scope per filtrare i dati dell'Ente in base al tipo di utenza in sessione.

### Lista dei metodi

```
apply(Builder $builder, Model $model): void
```

Il metodo `apply` applica lo scope al builder della query, aggiungendo il filtro per l'Ente in base al tipo di utenza in sessione.

* Parametri:
  * `$builder`: L'oggetto Builder che rappresenta la query.
  * `$model`: L'istanza del modello a cui applicare lo scope.
