## Classe `Csrf`

La classe `Csrf` gestisce la protezione da attacchi CSRF (Cross-Site Request Forgery) all'interno di un'applicazione PHP.

### Metodi

#### `generateToken($nameToken = null)`

Genera un token CSRF e lo salva nella sessione.

##### Parametri

* `$nameToken` : Il nome del token CSRF. Se non specificato, viene utilizzato il nome di token predefinito.

##### Restituisce

* `void`

#### `getToken($nameToken = null)`

Restituisce il token CSRF dalla sessione. Se il token non esiste, ne genera uno nuovo e lo salva nella sessione.

##### Parametri

* `$nameToken` : Il nome del token CSRF. Se non specificato, viene utilizzato il nome di token predefinito.

##### Restituisce

* `string`: Il token CSRF.

#### `getTokenName($nameToken)`

Restituisce il nome del token CSRF.

##### Parametri

* `$nameToken`: Il nome del token CSRF.

##### Restituisce

* `string`: Il nome del token CSRF.

#### `validate($requestData = [], $nameToken = '')`

Valida il token CSRF confrontandolo con il token presente nel parametro `$requestData`.

##### Parametri

* `$requestData` : I dati della richiesta contenenti il token CSRF. Se non specificato, viene utilizzato un array vuoto.
* `$nameToken` : Il nome del token CSRF. Se non specificato, viene utilizzato il nome di token predefinito.

##### Restituisce

* `bool`: `true` se il token CSRF è valido, `false` altrimenti.

#### `getHiddenInputString($nameToken = '')`

Restituisce una stringa HTML contenente un campo di input nascosto con il token CSRF come valore.

##### Parametri

* `$nameToken` : Il nome del token CSRF. Se non specificato, viene utilizzato il nome di token predefinito.

##### Restituisce

* `string`: Una stringa HTML contenente un campo di input nascosto.

#### `getQueryString($nameToken = '')`

Restituisce una stringa contenente il token CSRF nel formato di una query string.

##### Parametri

* `$nameToken` : Il nome del token CSRF. Se non specificato, viene utilizzato il nome di token predefinito.

##### Restituisce

* `string`: Una stringa contenente il token CSRF nel formato di una query string.

#### `getTokenAsArray($nameToken = '')`

Restituisce il token CSRF come un array associativo con il nome del token come chiave.

##### Parametri

* `$nameToken` : Il nome del token CSRF. Se non specificato, viene utilizzato il nome di token predefinito.

##### Restituisce

* `array`: Un array associativo contenente il token CSRF.

#### `compare($hasha = '', $hashb = '')`

Confronta due hash per determinare se sono uguali.

##### Parametri

* `$hasha`: Il primo hash da confrontare.
* `$hashb`: Il secondo hash da confrontare.

##### Restituisce

* `bool`: `true` se gli hash sono uguali, `false` altrimenti.

```
// Creazione di un'istanza della classe Csrf
$csrf = new \System\Csrf();

// Generazione di un token CSRF
$csrf->generateToken();

// Recupero del token CSRF
$token = $csrf->getToken();

// Validazione di un token CSRF
$requestData = $_POST; // Supponendo che il token CSRF venga inviato come parametro POST
$isValid = $csrf->validate($requestData);

if ($isValid) {
    // Il token CSRF è valido, procedi con la richiesta
    // ...
} else {
    // Il token CSRF non è valido, gestisci l'errore
    // ...
}

// Generazione di un campo di input nascosto con il token CSRF
$hiddenInput = $csrf->getHiddenInputString();

// Generazione di una stringa di query con il token CSRF
$queryString = $csrf->getQueryString();

// Recupero del nome del token CSRF
$tokenName = $csrf->getTokenName();

// Confronto di due hash
$hashA = '...';
$hashB = '...';
$sonoUguali = $csrf->compare($hashA, $hashB);
```
