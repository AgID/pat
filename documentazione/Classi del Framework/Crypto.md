**__# File che contiene funzioni di crittografia (Crypto)

**Riferimento path sorgente classe upload:** *app/Helpers/Security/Crypto.php*


## Lista dei metodi

- `encrypt($data)`
- `decrypt($data)`
- `verify($data = '')`
- `isValidString($string='')`




## Riferimenti funzioni.




`encrypt($data)`


Funzione che esegue la crittografia della stringa passata


| Settaggi            | Descrizione                           |
|---------------------|---------------------------------------|
| **Parametri**       | **$data**  - Stringa da crittografare |
| **Ritorno**         | Stringa crittografata                 |
| **Tipo di ritorno** | String                                |


Esempio :

```php
$data ='ciao';
var_dump(Crypto::encrypt((string)$data));
```

Risultato

```php
string '31JQQQ=='
```



------

`decrypt($data)`


Funzione che esegue la decrittografia della stringa passata


| Settaggi            | Descrizione                            |
|---------------------|----------------------------------------|
| **Parametri**       | **$data**  - Stringa già crittografata |
| **Ritorno**         | Stringa in chiaro                      |
| **Tipo di ritorno** | String                                 |


Esempio :

```php
$crypt = '31JQQQ==';
var_dump(Crypto::decrypt($crypt));
```

Risultato

```php
string 'ciao'
```


------

`verify($data = '')`


Funzione che controlla se una stringa è valida e crittografata, e nel caso ritorna la stessa decrittografata
altrimenti ritorna la stessa.


| Settaggi            | Descrizione                            |
|---------------------|----------------------------------------|
| **Parametri**       | **$data**  - Stringa già crittografata |
| **Ritorno**         | Stringa in chiaro                      |
| **Tipo di ritorno** | String                                 |


Esempio :

```php
//esempio 1
$ver = 'yl5DR/1t4lk=';
var_dump(Crypto::verify($ver));

//esempio2
$ver = 'Immobile';
var_dump(Crypto::verify($ver));


```

Risultato

```php
//esempio 1
string 'verifica'

//esempio 2
string 'Immobile'

```


------

`isValidString($string='')`

Funzione che controlla se una stringa è valida ai fini della crittografia e la ritorna,
altrimenti la trasforma da Windows-1252 (CP-1252) a CHARSET e la ritorna.


| Settaggi            | Descrizione                      |
|---------------------|----------------------------------|
| **Parametri**       | **$string**  - Stringa in chiaro |
| **Ritorno**         | Stringa in chiaro                |
| **Tipo di ritorno** | String                           |




