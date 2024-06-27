# Classe per la gestione dei messaggi JSON (JsonResponse)

**Riferimento path sorgente classe upload:** *core/System/JsonResponse.php*



Il framework mette a disposizione la classe **JsonResponse** per la gestione dei messaggi nel formato JSON.



#### Lista dei metodi della classe:

- `setError()`
- `error()`
- `mergeErrors()`
- `addErrors()`
- `getErrors()`
- `getData()`
- `getStatusCode()`
- `setStatusCode()`
- `set()`
- `add()`
- `merge()`
- `deleteError()`
- `delete()`
- `__toString()`
- `toString()`
- `toArray()`
- `isSuccess()`
- `hasErrors()`
- `getToken()`
- `setToken()`
- `hasToken()`
- `response()`
- `success()`
- `bad()`



#### Riferimenti della classe:

`setError($key, $value = null)`



Questo metodo permette di impostare un errore nel formato `$key=>$value` nell'array degli errori.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$key** (string) - Chiave identificativa del dato da settare nell'errore<br />**$value** (mix) - Valore del dato da settare nell'errore |
| **Ritorno**         | Istanza della classe JsonResponse                            |
| **Tipo di ritorno** | JsonResponse                                                 |

Esempio:

```php
// Setto un messaggio di errore nell'oggetto $json

$json = new JsonResponse();
$json->setError('test', 'Errore di test');
```



------

`error($key, $value = null)`



Questo metodo permette di impostare, nell'array degli errori, i messaggi di errore della richiesta Json nel formato `$key=>$value`. A differenza del metodo di sopra `setError()`, permette di settare un più errori passandogli un array.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$key** (string\|array) - Chiave(i) identificativa del dato(i) da settare nell'errore<br />**$value** (mix) - Valore(i) del dato da settare nell'errore |
| **Ritorno**         | Istanza della classe JsonResponse                            |
| **Tipo di ritorno** | JsonResponse                                                 |

Esempio:

```php
// Setto più messaggi di errore

$json = new JsonResponse();
$json->error('test', 'Errore di test');
$test = ['errore 1', 'errore 2'];
$json->error($test);
```



------

`mergeErrors(array $errors)`



Questo metodo permette di unire l'array degli errori con gli errori nella risposta Json, effettua quindi un merge. E' utilizzata internamente nel metodo visto sopra `error()`.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$errors** (array) - Array contenente gli errori da inserire nell'array degli errori dell'oggetto json |
| **Ritorno**         | Istanza della classe JsonResponse                            |
| **Tipo di ritorno** | JsonResponse                                                 |

Esempio:

```php
// Setto più messaggi di errore

$json = new JsonResponse();
$test = ['errore 1', 'errore 2'];
$json->mergeErrors($test);
```



------

`addErrors(array $errors)`



Questo metodo permette di concatenare l'array degli errori con gli errori nella risposta Json.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$errors** (array) - Array contenente gli errori da concatenare nell'array degli errori dell'oggetto json |
| **Ritorno**         | Istanza della classe JsonResponse                            |
| **Tipo di ritorno** | JsonResponse                                                 |

Esempio:

```php
// Setto più messaggi di errore

$json = new JsonResponse();
$json->error('test', 'Errore di test');
$test = ['errore 1', 'errore 2'];
$json->addErrors($test);
```



------

`getErrors()`



Questo metodo restituisce gli errori settati nell'array degli errori dell'istanza della classe.

| Settaggi            | Descrizione        |
| ------------------- | ------------------ |
| **Parametri**       |                    |
| **Ritorno**         | Array degli errori |
| **Tipo di ritorno** | array              |

Esempio:

```php
// Stampo i messaggi di errore ottenuti con la funzione getErrors

$json = new JsonResponse();
$json->error('test', 'Errore di test');
$test = ['errore 1', 'errore 2'];
$json->addErrors($test);
trace($json->getErrors());

// L'esempio stamperà gli errori come si può vedere di seguito:
/*
array (size=3)
  'test' =>  'Errore di test' 
  0 =>  'errore 1' 
  1 =>  'errore 2' 
*/
```



------

`set($key, $value = null)`



Questo metodo permette di settare una proprietà nell'array dei dati dell'oggetto nel formato `$key=>value`.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$key** (string) - Chiave identificativa del dato da settare nell'array dei dati dell'oggetto<br />**$value** (mix) - Valore del dato da settare |
| **Ritorno**         | Istanza della classe JsonResponse                            |
| **Tipo di ritorno** | JsonResponse                                                 |

Esempio:

```php
// Setto una proprietà nell'array dei dati dell'oggetto

$json = new JsonResponse();
$json->set('esempio', 'Proprietà di esempio');
```



------

`getData()`



Questo metodo restituisce i dati dell'istanza della classe.

| Settaggi            | Descrizione                 |
| ------------------- | --------------------------- |
| **Parametri**       |                             |
| **Ritorno**         | Array dei dati della classe |
| **Tipo di ritorno** | array                       |

Esempio:

```php
// Stampo i dati nell'array dei dati dell'oggetto ottenuti con il metodo getData()

$json = new JsonResponse();
$json->set('esempio', 'Proprietà di esempio');

// L'esempio stamperà i dati come si può vedere di seguito:
// 'esempio' => string 'Proprietà di esempio'
```



------

`setStatusCode($code)`



Questo metodo permette di impostare il codice di stato della richiesta.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$code** (int) - Codice di stato da impostare nella richiesta |
| **Ritorno**         | Istanza della classe JsonResponse                            |
| **Tipo di ritorno** | JsonResponse                                                 |

Esempio:

```php
//Setto il codice di stato 200(Successo) nell'oggetto json

$json = new JsonResponse();
$json->setStatusCode(200);
```



------

`getStatusCode()`



Questo metodo restituisce il codice di stato della richiesta.

| Settaggi            | Descrizione                     |
| ------------------- | ------------------------------- |
| **Parametri**       |                                 |
| **Ritorno**         | Codice di stato della richiesta |
| **Tipo di ritorno** | int                             |

Esempio:

```php
//Setto il codice di stato 200(Successo) nell'oggetto json

$json = new JsonResponse();
$json->setStatusCode(200);
trace($json->getStatusCode());

// L'esempio stamperà il codice di stato della richiesta come si può vedere di seguito:
// int 200
```



------

`add($value)`



Questo metodo permette di aggiungere un elemento nell'array dei dati dell'oggetto.

| Settaggi            | Descrizione                                      |
| ------------------- | ------------------------------------------------ |
| **Parametri**       | **$value** (mix) - Valore del dato da aggiungere |
| **Ritorno**         | Istanza della classe JsonResponse                |
| **Tipo di ritorno** | JsonResponse                                     |

Esempio:

```php
//Aggiungo un elemento nell'array dei dati dell'oggetto

$json = new JsonResponse();
$json->set('esempio', 'Proprietà di esempio');
$json->add('Dato di esempio');
```



------

`merge(array $data)`



Questo metodo permette di unire un'array di dati nella risposta.

| Settaggi            | Descrizione                                     |
| ------------------- | ----------------------------------------------- |
| **Parametri**       | **$data** (array) - Array di dati da aggiungere |
| **Ritorno**         | Istanza della classe JsonResponse               |
| **Tipo di ritorno** | JsonResponse                                    |

Esempio:

```php
//Aggiungo un elemento nell'array dei dati dell'oggetto

$json = new JsonResponse();
$json->set('esempio', 'Proprietà di esempio');
$test = ['Dato 1', 'Dato 2'];
$json->merge($test);
```



------

`deleteError($key)`



Questo metodo permette di rimuovere gli errore associati alla chiave passata nel parametro `$key` dall'array degli errori.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$key** (mix) - Chiave identificativa dell'errore da rimuovere |
| **Ritorno**         | Istanza della classe JsonResponse                            |
| **Tipo di ritorno** | JsonResponse                                                 |

Esempio:

```php
//Elimino un errore dagli errori dell'oggetto

$json = new JsonResponse();
$json->setError('esempio', 'Errore di esempio');
$json->setError('test', 'Errore di test');
trace($json->getErrors());

//Stampa degli errori prima dell'operazione di cancellazione
/*
  'esempio' => 'Errore di esempio'
  'test' => 'Errore di test'
*/

$json->deleteError('esempio');
trace($json->getErrors());

//Stampa degli errori dopo l'operazione di cancellazione
/*
	'test' => 'Errore di test'
*/
```



------

`delete($key)`



Questo metodo permette di rimuovere i dati associati alla chiave passata nel parametro `$key` dall'array dei dati.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$key** (mix) - Chiave identificativa del dato da rimuovere |
| **Ritorno**         | Istanza della classe JsonResponse                            |
| **Tipo di ritorno** | JsonResponse                                                 |



Esempio:

```php
//Elimino un dato dall'array dei dati dell'oggetto

$json = new JsonResponse();
$json->set('esempio', 'Dato di esempio');
$json->set('test', 'Dato di test');
trace($json->getData());

//Stampa dei dati prima dell'operazione di cancellazione
/*
  'esempio' =>  'Dato di esempio'
  'test' =>  'Dato di test'
*/

$json->delete('esempio');
trace($json->getData());

//Stampa dei dati dopo l'operazione di cancellazione
/*
	'test' =>  'Dato di test'
*/
```



------

`toString()`



Questo metodo restituisce l'oggetto come stringa codificata in JSON.

| Settaggi            | Descrizione                             |
| ------------------- | --------------------------------------- |
| **Parametri**       |                                         |
| **Ritorno**         | Stringa codificata in JSON dell'oggetto |
| **Tipo di ritorno** | string                                  |

Esempio:

```php
$json = new JsonResponse();
$json->setError('test', 'Errore di test');
$json->setError('esempio', 'Errore di esempio');
trce($json->toString());

//Stampa dell'oggetto convertito in stringa codificata in JSON:

/*
	'{
		"data":{"prova":"Datp di prova"},
		"errors":{
					"esempio":"Errore di esempio",
				  	"test":"Errore di test"
				  },
		"success":false,
		"status_code":200,
		"response_date":"2021-12-09 11:10:31"
	}'
*/
```



------

`toArray()`



Questo metodo converte l'oggetto in un array.

| Settaggi            | Descrizione                    |
| ------------------- | ------------------------------ |
| **Parametri**       |                                |
| **Ritorno**         | L'oggetto sotto forma di array |
| **Tipo di ritorno** | array                          |

Esempio:

```php
$json = new JsonResponse();
$json->setError('test', 'Errore di test');
$json->setError('esempio', 'Errore di esempio');
trce($json->toArray());

//Stampa dell'oggetto convertito in array:

/*
array (size=5)
  'data' => 
    array (size=1)
      'prova' => string 'Datp di prova' 
  'errors' => 
    array (size=2)
      'esempio' => string 'Errore di esempio' 
      'test' => string 'Errore di test' 
  'success' => boolean false
  'status_code' => int 200
  'response_date' => string '2021-12-09 11:17:11' 
*/
```



------

`isSuccess()`



Questo metodo permette di controllare se l'oggetto ha esito positivo.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       |                                                              |
| **Ritorno**         | True se non ci sono errori e il codice di stato è un codice di successo, altrimenti false |
| **Tipo di ritorno** | bool                                                         |

Esempio:

```php
$json = new JsonResponse();
[omissis]
$json->isSuccess();
```



------

`hasErrors()`



Questo metodo controlla se la risposta contiene o meno errori.

| Settaggi            | Descrizione                                  |
| ------------------- | -------------------------------------------- |
| **Parametri**       |                                              |
| **Ritorno**         | True se non ci sono errori, altrimenti false |
| **Tipo di ritorno** | bool                                         |

Esempio:

```php
$json = new JsonResponse();
[omissis]
$json->hasErrors();
```



------

`getToken()`



Questo metodo restituisce il token della risposta.

| Settaggi            | Descrizione                         |
| ------------------- | ----------------------------------- |
| **Parametri**       |                                     |
| **Ritorno**         | Il token della risposta se presente |
| **Tipo di ritorno** | string\|null                        |

Esempio:

```php
$json = new JsonResponse();
[omissis]
$json->getToken();
```



------

`setToken($token)`



Questo metodo permette di impostare il token della richiesta JSON.

| Settaggi            | Descrizione                                              |
| ------------------- | -------------------------------------------------------- |
| **Parametri**       | **$token** (string) - Token da impostare nella richiesta |
| **Ritorno**         | Istanza della classe JsonResponse                        |
| **Tipo di ritorno** | JsonResponse                                             |

Esempio:

```php
$json = new JsonResponse();
[omissis]
$json->setToken('Token di test');
```



------

`hasToken()`



Questo metodo controlla se l'oggetto ha settato o meno il token. Se il token è settato restituisce true, altrimenti false.

| Settaggi            | Descrizione                                  |
| ------------------- | -------------------------------------------- |
| **Parametri**       |                                              |
| **Ritorno**         | True se il token è settato, altrimenti false |
| **Tipo di ritorno** | bool                                         |

Esempio:

```php
$json = new JsonResponse();
$json->setToken('Token di test');
$json->hasToken();

// In questo caso la funzione hasToken restituisce true in quanto il token è settato.
```



------

`response($send=true)`



Questo metodo restituisce la notazione standard delle risposte REST in formato JSON.

| Settaggi            | Descrizione        |
| ------------------- | ------------------ |
| **Parametri**       | **$send** (bool) - |
| **Ritorno**         |                    |
| **Tipo di ritorno** | null               |

Esempio:

```php
$json = new JsonResponse();
[omissis]
$json->response();
```



------

`success()`



Questo metodo crea un nuovo oggetto di tipo `Response` di successo.

| Settaggi            | Descrizione                    |
| ------------------- | ------------------------------ |
| **Parametri**       |                                |
| **Ritorno**         | Il codice di stato di successo |
| **Tipo di ritorno** | int                            |

Esempio:

```php
$json = new JsonResponse();
[omissis]
$code = $json->success();
$json->setStatusCode($code);
```



------

`bad()`



Questo metodo crea un nuovo oggetto di tipo `Response` di errore.

| Settaggi            | Descrizione                  |
| ------------------- | ---------------------------- |
| **Parametri**       |                              |
| **Ritorno**         | Il codice di stato di errore |
| **Tipo di ritorno** | int                          |

Esempio:

```php
$json = new JsonResponse();
[omissis]
$code = $json->bad();
$json->setStatusCode($code);
```



------

