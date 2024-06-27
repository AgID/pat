## Classe `Container`

La classe `Container` fornisce un'implementazione di base per un contenitore di dipendenze all'interno di un'applicazione PHP.

### Metodi

#### `getInstance()`

Restituisce un'istanza singola del contenitore.

##### Restituisce

* `Container`: Un'istanza del contenitore.

#### `register($name, callable $resolver)`

Registra un servizio nel contenitore.

##### Parametri

* `$name`: Il nome del servizio.
* `$resolver`: Un callback che viene chiamato per risolvere il servizio.

##### Restituisce

* `void`

#### `singleton($name, callable $resolver)`

Registra un servizio come singleton nel contenitore.

##### Parametri

* `$name`: Il nome del servizio.
* `$resolver`: Un callback che viene chiamato per risolvere il servizio.

##### Restituisce

* `void`

#### `make($name)`

Risolve un servizio dal contenitore.

##### Parametri

* `$name`: Il nome del servizio da risolvere.

##### Restituisce

* `mixed`: Il servizio risolto.

##### Eccezioni

* `\Exception`: Viene lanciata un'eccezione se il servizio specificato non è stato trovato nel contenitore.

```
// Ottenere un'istanza del contenitore
$container = \System\Container::getInstance();

// Registrazione di un servizio nel contenitore
$container->register('logger', function () {
    return new \MyLogger();
});

// Registrazione di un servizio come singleton nel contenitore
$container->singleton('cache', function () {
    return new \MyCache();
});

// Risoluzione di un servizio dal contenitore
$logger = $container->make('logger');
$cache = $container->make('cache');
```

Nell'esempio sopra viene utilizzata la classe `Container` per creare un contenitore di dipendenze. Viene ottenuta un'istanza del contenitore utilizzando il metodo `getInstance()`. Successivamente, vengono registrati due servizi nel contenitore utilizzando i metodi `register()` e `singleton()`. Infine, i servizi vengono risolti dal contenitore utilizzando il metodo `make()`.
