## Classe `SanitizeRequest`

La classe `SanitizeRequest` fornisce metodi per la pulizia dei dati di input delle richieste.

### Proprietà

#### `allowGetArray`

Una variabile booleana che indica se consentire l'utilizzo dell'array GET.

#### `enableXss`

Una variabile booleana che indica se abilitare il filtro XSS globale.

#### `standardNewlines`

Una variabile booleana che indica se standardizzare i caratteri di nuova linea.

#### `security`

Un'istanza della classe `Security` utilizzata per la pulizia dei dati.

### Metodi

#### `__construct()`

Costruttore della classe `SanitizeRequest`.

* Restituisce: N/A

#### `fetchFromArray(&$array, $index = NULL, $xssClean = true, $sanitizekey = true, $sanitizeData = true, $file = false)`

Recupera un valore da un array utilizzando un indice.

* Parametri:
  * `$array`: L'array da cui recuperare il valore.
  * `$index`: L'indice dal quale recuperare il valore (opzionale, valore predefinito: NULL).
  * `$xssClean`: Indica se applicare la pulizia da XSS al valore (opzionale, valore predefinito: true).
  * `$sanitizekey`: Indica se applicare la pulizia alla chiave richiesta (opzionale, valore predefinito: true).
  * `$sanitizeData`: Indica se applicare la pulizia ai dati richiesti (opzionale, valore predefinito: true).
  * `$file`: Indica se il campo richiesto è un campo file (opzionale, valore predefinito: false).
* Restituisce: Il valore richiesto.

#### `cleanRequestData($data)`

Pulisce i dati di input.

* Parametri:
  * `$data`: I dati di input da pulire.
* Restituisce: I dati di input puliti.

#### `cleanRequestKeys($data, $fatal = true)`

Pulisce le chiavi dei dati di input.

* Parametri:
  * `$data`: La chiave dei dati di input da pulire.
  * `$fatal`: Indica se generare un errore fatale in caso di chiave non valida (opzionale, valore predefinito: true).
* Restituisce: La chiave dei dati di input pulita o false in caso di chiave non valida.

#### `cleanRequest($data)`

Pulisce i dati di input.

* Parametri:
  * `$data`: I dati di input da pulire.
* Restituisce: I dati di input puliti.

```
use System\SanitizeRequest;

// Creazione di un'istanza della classe SanitizeRequest
$sanitizer = new SanitizeRequest();

// Dati di input da pulire
$data = [
    'name' => '<script>alert("XSS attack");</script>',
    'email' => 'john.doe@example.com',
    'message' => 'Hello, World!',
];

// Pulizia dei dati di input
$cleanData = $sanitizer->cleanRequest($data);

// Accesso ai dati puliti
echo "Nome: " . $cleanData['name'] . "<br>";
echo "Email: " . $cleanData['email'] . "<br>";
echo "Messaggio: " . $cleanData['message'] . "<br>";
```

Nell'esempio sopra, viene creata un'istanza della classe `SanitizeRequest`. Vengono definiti dei dati di input da pulire, rappresentati da un array associativo. Successivamente, viene chiamato il metodo `cleanRequest()` per pulire i dati di input. Infine, si accede ai dati puliti e si stampano a schermo.

L'esempio illustra l'utilizzo della classe `SanitizeRequest` per pulire i dati di input, in particolare per proteggere contro gli attacchi di Cross-Site Scripting (XSS). Si può adattare l'esempio alle proprie esigenze per sanificare i dati di input prima di utilizzarli.
