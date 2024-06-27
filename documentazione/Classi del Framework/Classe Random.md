# Classe per la generazione di stringhe random (Random)

**Riferimento path sorgente classe upload:** *core/System/Random.php*



#### **Lista dei metodi della classe Random per la generazione di stringhe random:**

- `basic()`
- `numeric($length = 8)`
- `numericNoZero($length = 8)`
- `alnum($length = 8)`
- `alpha($length = 8)`
- `md5()`
- `sha1()`
- `generateRand($pool, $length)`



#### Riferimenti della classe:

`basic()`



Questa funzione genera una stringa di tipo numerica casuale, basata sulla primitiva  `mt_rand()`.

| Settaggi            | Descrizione   |
| ------------------- | ------------- |
| **Parametri**       |               |
| **Ritorno**         | Intero random |
| **Tipo di ritorno** | int           |

Esempio:

```php
$test = new Random();
trace($test->basic());

// Il codice di esempio di sopra stampa il seguente risultato:
// 1851957398
```



------

`numeric($length = 8)`



Questa funzione genera una stringa contenente solo numeri della lunghezza passata nel parametro `$length` se presente, altrimenti della lunghezza di default che è pari a 8.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$length** (int) - Lunghezza della stringa da generare      |
| **Ritorno**         | Stringa random contenente solo numeri della lunghezza passata nel parametro `$length` |
| **Tipo di ritorno** | string                                                       |

Esempio:

```php
$test = new Random();
trace($test->numeric(10), true);

// Il codice di esempio di sopra stampa il seguente risultato:
// '3897516024'
```



------

`numericNoZero($length = 8)`



Questa funzione genera una stringa contenente solo numeri escluso lo zero della lunghezza passata nel parametro `$length`, altrimenti della lunghezza di default che è pari a 8.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$length** (int) - Lunghezza della stringa da generare      |
| **Ritorno**         | Stringa random contenente solo numeri della lunghezza passata nel parametro `$length` |
| **Tipo di ritorno** | string                                                       |

Esempio:

```php
$test = new Random();
trace($test->numericNoZero(10), true);

// Il codice di esempio di sopra stampa il seguente risultato:
// '3897516824'
```



------

`alnum($length = 8)`



Questa funzione genera una stringa alfanumerica con caratteri minuscoli e maiuscoli della lunghezza passata nel parametro `$length`, altrimenti della lunghezza di default che è pari a 8.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$length** (int) - Lunghezza della stringa da generare      |
| **Ritorno**         | Stringa random alfanumerica con caratteri minuscoli e maiuscoli della lunghezza passata nel parametro `$length` |
| **Tipo di ritorno** | string                                                       |

Esempio:

```php
$test = new Random();
trace($test->alnum(), true);

// Il codice di esempio di sopra stampa il seguente risultato:
// 'D27tVmyO'
```



------

`alpha($length = 8)`



Questa funzione genera una stringa random contenente solo lettere minuscole e maiuscole della lunghezza passata nel parametro `$length`, altrimenti della lunghezza di default che è pari a 8.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$length** (int) - Lunghezza della stringa da generare      |
| **Ritorno**         | Stringa random contenente solo lettere minuscole e maiuscole della lunghezza passata nel parametro `$length` |
| **Tipo di ritorno** | string                                                       |

Esempio:

```php
$test = new Random();
trace($test->alpha(), true);

// Il codice di esempio di sopra stampa il seguente risultato:
// 'dAMZSoqx'
```



------

`md5()`



Questa funzione genera una stringa alfanumerica random crittografata, basata sulla funzione `md5()` con lunghezza fissa di 32.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       |                                                              |
| **Ritorno**         | Stringa alfanumerica casuale crittografata, basata sulla funzione `md5()` |
| **Tipo di ritorno** | string                                                       |

Esempio:

```php
$test = new Random();
trace($test->md5(), true);

// Il codice di esempio di sopra stampa il seguente risultato:
// 'ead7ae49234b8f9e5ad9f03105dbff83'
```



------

`sha1()`



Questa funzione genera una stringa alfanumerica random crittografata, basata sulla funzione `sha1()` con lunghezza fissa di 40.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       |                                                              |
| **Ritorno**         | Stringa numerica alfanumerica crittografata, basata sulla funzione `md5()` |
| **Tipo di ritorno** | string                                                       |

Esempio:

```php
$test = new Random();
trace($test->md5(), true);

// Il codice di esempio di sopra stampa il seguente risultato:
// 'e35b81ecb6cdfeca1cd11914ba0595f3f7f9c423'
```