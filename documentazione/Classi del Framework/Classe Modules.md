## Classe `Modules`

La classe `Modules` gestisce i moduli nell'applicazione.

### Proprietà

#### `$modules`

Un array che contiene i moduli registrati.

#### `$routes`

Un array che contiene le rotte dei moduli registrati.

#### `$instance`

Un'istanza della classe `Modules`.

### Metodi

#### `init()`

Inizializza la classe `Modules`.

* Restituisce: Un'istanza della classe `Modules`.

#### `routes()`

Restituisce le rotte dei moduli registrati.

* Restituisce: Un array delle rotte dei moduli registrati.

#### `__clone()`

Metodo clone protetto per imporre il comportamento singleton.

* Restituisce: N/A

#### `__construct()`

Costruttore della classe `Modules`.

* Restituisce: N/A

#### `register($data)`

Registra un modulo.

* Parametri:
  * `$data`: I dati del modulo da registrare.
* Restituisce: N/A

#### `scannerModules()`

Scansiona i moduli installati.

* Restituisce: N/A

```
use System\Modules;

// Inizializzazione della classe Modules
$modules = Modules::init();

// Registrazione di un modulo
$moduleData = [
    'name' => 'MyModule',
    'version' => '1.0',
    'install' => function() {
        echo "Modulo installato.";
    },
    'classMap' => [
        'MyModule\MyClass',
        'MyModule\AnotherClass',
    ],
];
$modules->register($moduleData);

// Ottenimento delle rotte dei moduli registrati
$routes = $modules->routes();
echo "Rotte dei moduli registrati:";
foreach ($routes as $route) {
    echo $route . "<br>";
}

// Esecuzione dello scanner dei moduli
$modules->scannerModules();
```

Nell'esempio sopra, viene inizializzata la classe Modules utilizzando il metodo init(). Viene quindi registrato un modulo utilizzando il metodo register(), specificando i dati del modulo tra cui il nome, la versione, una funzione di installazione e una mappatura delle classi. Successivamente, vengono ottenute le rotte dei moduli registrati utilizzando il metodo routes(). Infine, viene eseguito lo scanner dei moduli utilizzando il metodo scannerModules().

L'esempio illustra l'utilizzo della classe Modules per la gestione dei moduli in un'applicazione.
