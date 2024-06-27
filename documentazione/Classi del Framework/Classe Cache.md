# Classe Cache 

**Riferimento path sorgente classe upload:** *core/System/Cache.php*

Il Framework offre diverse alternative per la memorizzazione della cache. Il metodo più comune per lo storage della cache è il salvataggio dei dati su File System, e non richiede nessuna configurazione lato Server. Tutti gli altri metodi, prevedono requisiti specifici lato server per lo storage  della classe Cache. Gli adattatori supportati nel packeage di Cache sono:

- **File**
- **APC**
- **Memcached**
- **Mem**
- **Redis**

E' possibile configurare la tipologia dello storage della cache nel file `app/Config/cache.php`

Di default lo storage della Cache è settato su ***File***, questo servizio non richiede ulteriori parametri di configurazione. E' possibile utilizzare un altro servizio di storage a patto che il serivizio sia installato e  configurato nel Server.



##### - Memcached

Per connettersi al servizio di ***Memcached*** sono richiesti determinati parametri, configurabili nel file `app/Config/cache.php`

```php
return[
  // [..omissis..]
  'memcached_hostname' => 'localhost',
  'memcached_port' => 11211,
  'cached_prefix' => 'memcached_cache',
   // [..omissis..]
];

```

nella matrice del configuratore in `app/Config/cache.php`, le chiavi da valorizzare  per la comunicazione del servizio Memcached sono:

- **memcached_hostname** : L'indirizzo dell'HOST del server Memcached 
- **memcached_port**: La porta dell'HOST del server Memcached 
- **cached_prefix**: il prefisso de nome del salvataggio dei dati cache



##### - APC

Per connettersi al servizio di ***APC*** sono richiesti determinati parametri, configurabili nel file `app/Config/cache.php`

```php
return[
  // [..omissis..]
   'apc_prefix' => 'apc_cache',
];

```

nella matrice del configuratore in `app/Config/cache.php`, le chiavi da valorizzare  per la comunicazione del servizio APC sono:

- **apc_prefix** : il prefisso de nome del salvataggio dei dati cache 



##### - REDIS

Per connettersi al servizio di ***REDIS*** sono richiesti determinati parametri, configurabili nel file `app/Config/cache.php`

```php
return[
  // [..omissis..]
  'redis_hostname' => 'localhost',
  'redis_port' => 11211,
  'redis_prefix' => 'redis_cache',
  // [..omissis..]
];

```

nella matrice del configuratore in `app/Config/cache.php`, le chiavi da valorizzare  per la comunicazione del servizio Redis sono:

- **memcached_hostname** : L'indirizzo dell'HOST del server Redis 
- **memcached_port**: La porta dell'HOST del server Redis 
- **redis_prefix**: il prefisso de nome del salvataggio dei dati cache



##### - MEM

Per connettersi al servizio di ***MEM*** sono richiesti determinati parametri, configurabili nel file `app/Config/cache.php`

```php
return[
  // [..omissis..]
  'mem_hostname' => 'localhost',
  'mem_port' => 11211,
  'mem_prefix' => 'redis_cache',
  // [..omissis..]
];

```

nella matrice del configuratore in `app/Config/cache.php`, le chiavi da valorizzare  per la comunicazione del servizio Mem sono:

- **mem_hostname** : L'indirizzo dell'HOST del server Mem 
- **mem_port**: La porta dell'HOST del server Mem 
- **mem_prefix**: il prefisso de nome del salvataggio dei dati cache



**Lista dei metodi della classe**

`$cache = new \System\cache();`

- `$cache->set($key, $value, $expire = null)`
- `$cache->get($key)`
- `$cache->delete($key)`



#### Riferimenti della Classe

```php
$cache->set($key, $value, $expire = null)
```

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$key** ( *string* ) - Nome dell'elemento indice nella cache  <br />**$value** ( mix ) - i dati da salvare nello storage<br />**$expire** ( int ) - Il tempo limite della memorizzazione dei dati prima della scadenza |
| **Ritorno**         | Istanza Cache (concatenamento di metodi)                     |
| **Tipo di ritorno** | Cache                                                        |

Nel seguente esempio vediamo come istanziare ed usare la classe Cache:

```php
// Istanza oggetto della classe
$cache = new \System\cache();

// Verifico Se la variabile ed eventualmente salvo in cache 
if (!$users = $cache->get('users')) {
  
  // Connessione al database
  $DB = new \System\Daatabase();
  
  // Estraggo tutti gli users
  $users = $DB->table('users')->getAll();
  
  // Salvo in cache
  $cache->set('users', $users, 3600);
  
}

// Ciclo la variabile $users
foreach($users AS $user) {
  // ...
}


```

In questo esempio verifico nella condizione **IF** con il metodo get(), se è allocata nello storage una matrice contenente i dati dell'utente. Se il ritorno del metodo è `false`,  lo script entra dentro **l'IF**  e valorizza con il metodo set() la chiave **users** dei dati recuperati degli utenti nella Query SQL. Se nel metodo get() nell'**IF**, è valorizzata la matrice,  lo script non entra all'interno **dell'IF** ma assegna nella variabile `$users` i dati allocati nello storage della cache.

**NB:** Di default il tempo massimo di allocazione della cache è di 3600 secondi. Questo valore di default è configurabile nel file di configurazione  `app/Config/cache.php`, nella chiave ***expire***.
Tuttavia in ogni metodo set() della classe Cache nel terzo parametro puoi settare il numero dei secondi di validità della cache: $cache->set('users', $users, **3600**);

------



```php
$cache->get($key)
```

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$key** ( *string* ) - Nome dell'elemento indice nella cache |
| **Ritorno**         | Il valore dei dati di storage oppure false se non trovato    |
| **Tipo di ritorno** | misto                                                        |

```php
$cache = new \System\cache();
$users = $cache->get('users')
```



------



```php
$cache->delete($key)
```

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$key** ( *string* ) - nome dell'elemento memorizzato nella cache |
| **Ritorno**         | VERO in caso di successo, FALSO in caso di fallimento        |
| **Tipo di ritorno** | Bool                                                         |

```php
$cache = new \System\cache();
$users = $cache->delete('users')
```

