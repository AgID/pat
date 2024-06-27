## Classe `Response`

La classe `Response` gestisce la creazione e l'invio di una risposta HTTP.

### Proprietà

#### `status`

Lo stato HTTP della risposta.

#### `headers`

Un array associativo che contiene gli header della risposta.

#### `body`

Il contenuto della risposta.

#### `statuses`

Un array che associa i codici di stato HTTP ai relativi messaggi di stato.

### Metodi

#### `__construct($body = null, $status = 200, array $headers = array())`

Costruttore della classe `Response`.

* Parametri:
  * `$body`: Il contenuto della risposta (opzionale, valore predefinito: null).
  * `$status`: Lo stato HTTP della risposta (opzionale, valore predefinito: 200).
  * `$headers`: Un array associativo che contiene gli header della risposta (opzionale, valore predefinito: array()).
* Restituisce: N/A

#### `setStatus($status = 200)`

Imposta lo stato HTTP della risposta.

* Parametri:
  * `$status`: Lo stato HTTP della risposta (opzionale, valore predefinito: 200).
* Restituisce: L'istanza della classe `Response`.

#### `getStatusCode()`

Restituisce il codice di stato HTTP della risposta.

* Restituisce: Il codice di stato HTTP.

#### `setHeader($name, $value, $replace = true)`

Imposta un header della risposta.

* Parametri:
  * `$name`: Il nome dell'header.
  * `$value`: Il valore dell'header.
  * `$replace`: Indica se sostituire un eventuale header già esistente con lo stesso nome (opzionale, valore predefinito: true).
* Restituisce: L'istanza della classe `Response`.

#### `setHeaders($headers, $replace = true)`

Imposta più header della risposta contemporaneamente.

* Parametri:
  * `$headers`: Un array associativo che contiene gli header da impostare.
  * `$replace`: Indica se sostituire eventuali header già esistenti con gli stessi nomi (opzionale, valore predefinito: true).
* Restituisce: L'istanza della classe `Response`.

#### `getHeader($name = null)`

Restituisce il valore di un header della risposta.

* Parametri:
  * `$name`: Il nome dell'header (opzionale).
* Restituisce: Il valore dell'header o un array con tutti gli header se il nome non è specificato.

#### `body($value = false)`

Imposta o restituisce il contenuto della risposta.

* Parametri:
  * `$value`: Il valore del contenuto della risposta (opzionale, valore predefinito: false).
* Restituisce: Il contenuto della risposta o l'istanza della classe `Response`.

#### `sendHeaders()`

Invia gli header della risposta.

* Restituisce: True se gli header sono stati inviati con successo, false altrimenti.

#### `send($sendHeaders = false, $returnString = true)`

Invia la risposta HTTP.

* Parametri:
  * `$sendHeaders`: Indica se inviare anche gli header (opzionale, valore predefinito: false).
  * `$returnString`: Indica se restituire la risposta come stringa (opzionale, valore predefinito: true).
* Restituisce: La risposta come stringa se `$returnString` è true, altrimenti nulla.

#### `__toString()`

Restituisce il contenuto della risposta come stringa.

* Restituisce: Il contenuto della risposta come stringa.

```
use System\Response;

// Creazione di un'istanza della classe Response
$response = new Response();

// Impostazione dello stato della risposta
$response->setStatus(Response::SUCCESS);

// Impostazione degli header della risposta
$response->setHeader('Content-Type', 'text/html');
$response->setHeader('X-My-Header', 'Custom Value');

// Impostazione del contenuto della risposta
$response->body('<h1>Hello, World!</h1>');

// Invio della risposta
$response->send(true);
```

Nell'esempio sopra, viene creata un'istanza della classe Response e vengono eseguite diverse operazioni. Viene impostato lo stato della risposta utilizzando la costante Response::SUCCESS (che corrisponde al codice di stato HTTP 200). Vengono impostati anche degli header personalizzati utilizzando il metodo setHeader(). Successivamente, viene impostato il contenuto della risposta utilizzando il metodo body(). Infine, viene inviata la risposta utilizzando il metodo send().

L'esempio illustra l'utilizzo di alcuni metodi della classe Response per gestire lo stato, gli header e il contenuto di una risposta HTTP.
