## Classe `Authorization`

La classe `Authorization` fornisce metodi per la gestione dell'autorizzazione e dei token JWT all'interno di un'applicazione PHP.

### Metodi

#### `getSettings()`

Restituisce le impostazioni dell'autorizzazione.

##### Restituisce

* `array`: Un array contenente le impostazioni dell'autorizzazione, come il timeout del token e la chiave JWT.

#### `validateTimestamp($token)`

Valida il timestamp di un token.

##### Parametri

* `$token`: Il token da validare.

##### Restituisce

* `mixed`: Il token validato se il timestamp è valido, altrimenti `false`.

#### `validateToken($token)`

Valida un token JWT.

##### Parametri

* `$token`: Il token da validare.

##### Restituisce

* `mixed`: Il token decodificato se è valido, altrimenti `false`.

#### `generateToken($data)`

Genera un token JWT.

##### Parametri

* `$data`: I dati da includere nel token.

##### Restituisce

* `string`: Il token JWT generato.

### Esempio di utilizzo

```
// Ottenere le impostazioni dell'autorizzazione
$settings = \System\Authorization::getSettings();
$tokenTimeout = $settings['token_timeout'];
$jwtKey = $settings['jwt_key'];

// Validare un timestamp di un token
$token = '...'; // Il token da validare
$validatedToken = \System\Authorization::validateTimestamp($token);
if ($validatedToken !== false) {
// Il timestamp del token è valido
} else {
// Il timestamp del token non è valido o il token stesso non è valido
}

// Validare un token JWT
$token = '...'; // Il token da validare
$decodedToken = \System\Authorization::validateToken($token);
if ($decodedToken !== false) {
// Il token è valido
} else {
// Il token non è valido
}

// Generare un token JWT
$data = ['user_id' => 123]; // I dati da includere nel token
$generatedToken = \System\Authorization::generateToken($data);
```
