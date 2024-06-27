## Classe `Benchmark`

La classe `Benchmark` fornisce metodi per il benchmarking delle prestazioni di un'applicazione PHP.

### Metodi

#### `mark($name)`

Registra un punto di riferimento nel benchmark.

##### Parametri

* `$name`: Il nome del punto di riferimento.

##### Restituisce

* `void`

#### `elapsedTime($point1 = '', $point2 = '', $decimals = 4)`

Calcola il tempo trascorso tra due punti di riferimento nel benchmark.

##### Parametri

* `$point1` (opzionale): Il nome del primo punto di riferimento.
* `$point2` (opzionale): Il nome del secondo punto di riferimento.
* `$decimals` (opzionale): Il numero di decimali da utilizzare per la formattazione del tempo trascorso. Il valore predefinito è 4.

##### Restituisce

* `string`: Il tempo trascorso tra i punti di riferimento specificati, formattato come una stringa.

#### `memoryUsage()`

Restituisce l'utilizzo della memoria nel benchmark.

##### Restituisce

* `string`: L'utilizzo della memoria, formattato come una stringa.

### Esempio di utilizzo

```
// Creazione di un'istanza della classe Benchmark
$benchmark = new \System\Benchmark();

// Registrazione di un punto di riferimento
$benchmark->mark('start');

// Esecuzione di un'operazione

// Registrazione di un altro punto di riferimento
$benchmark->mark('end');

// Calcolo del tempo trascorso tra i punti di riferimento
$elapsedTime = $benchmark->elapsedTime('start', 'end');
echo'Tempo trascorso: ' . $elapsedTime;

// Calcolo dell'utilizzo della memoria
$memoryUsage = $benchmark->memoryUsage();
echo'Utilizzo della memoria: ' . $memoryUsage;
```
