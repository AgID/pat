# Classe Registry

**Riferimento path sorgente classe upload:** *core/System/Registry.php*

Il framework mette a disposizione una classe, la classe **Registry**, per la memorizzazione di oggetti e valori nello spazio dell'applicazione. Un registro non è altro che un contenitore dove memorizzare questi valori.

Memorizzando il valore in un registro, lo stesso oggetto è sempre disponibile in tutta la vostra applicazione. Questo meccanismo è un'alternativa all'uso della memorizzazione globale.

Il metodo tipico per usare i registri con è attraverso metodi statici nella classe Registry. In alternativa, il registro può essere usato come un oggetto array, in modo da poter accedere agli elementi memorizzati al suo interno con una comoda interfaccia tipo array.



#### Lista dei metodi della classe:

- `set()`
- `get()`
- `delete()`
- `exist()`



#### Riferimenti della classe:

`set($key, $val)`



Questo metodo permette di memorizzare una voce, un valore o un oggetto nel registro nel formato `$key=>$val`.

l valore restituito può essere un oggetto, un array o un valore. Si può cambiare il valore memorizzato in una specifica voce del registro chiamando il metodo `set()` per impostare la voce ad un nuovo valore.

L'indice può essere un valore (NULL, stringa o numero), come un normale array.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$key** (string) - Chiave identificativa dell'oggetto o del valore da memorizzare nel registro<br />**$val** (mix) - Valore o oggetto da memorizzare nel registro |
| **Ritorno**         | Un oggetto, un array oppure un valore.                       |
| **Tipo di ritorno** | mix                                                          |

Esempio:

```php
//Esempio di memorizzazione dell'oggetto $user nel registro

$user = new User();
[omissis]
Registry::set('user', $user);
```



------

`get($key)`



Questo metodo permette di recuperare una voce, un valore o un oggetto, memorizzato nel registro associato alla chiave passata nel parametro `$key`.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$key** (string) - Chiave identificativa dell'oggetto o del valore da restituire dal registro |
| **Ritorno**         | Il valore o oggetto memorizzato nel registro                 |
| **Tipo di ritorno** | mix                                                          |

Esempio:

```php
//Esempio di recupero dell'oggetto $user nel registro
$user = new User();
[omissis]
Registry::set('user', $user);
[omissis]
$utente = Registry::get('user');
```



------

`delete($key)`



Questo metodo permette di rimuovere un valore o un oggetto memorizzato nel registro, associato alla chiave passata nel parametro `$key`.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$key** (string) - Chiave identificativa dell'oggetto o del valore da rimuovere dal registro |
| **Ritorno**         | True se l'operazione avviene con successo, altrimenti false  |
| **Tipo di ritorno** | bool                                                         |

Esempio:

```php
//Esempio di eliminazione dell'oggetto $user nel registro
$user = new User();
[omissis]
Registry::set('user', $user);
[omissis]
Registry::delete('user');
```



------

`exist($key)`



Questo metodo permette di verificare se un valore o un oggetto con la chiave passata nel parametro `$key` esiste o meno nel registro.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$key** (string) - Chiave identificativa dell'oggetto o del valore di cui verificarne la presenza nel registro |
| **Ritorno**         | True se nel registro è presente o meno un valore o un oggetto con la chiave passata nel parametro `$key`, altrimenti false |
| **Tipo di ritorno** | bool                                                         |

Esempio:

```php
//Esempio di verifica dell'esistenza di una voce nel registro con la chiave 'user'
$user = new User();
[omissis]
Registry::set('user', $user);
[omissis]
Registry::exist('user');
```

------

