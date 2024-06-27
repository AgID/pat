## Classe `Auth`

La classe `Auth` gestisce l'autenticazione e l'autorizzazione all'interno di un'applicazione PHP.

### Metodi

#### `__construct($adaptor = null, $registry = '')`

Costruttore della classe `Auth`.

##### Parametri

* `$adaptor`: L'adattatore da utilizzare per l'autenticazione. Se non specificato, viene utilizzato l'adattatore impostato nel file di configurazione.
* `$registry`: Il registro da utilizzare per l'adattatore. Se non specificato, viene utilizzato il registro predefinito.

##### Eccezioni

* `Exception`: Viene lanciata un'eccezione se l'adattatore specificato non è valido o non è stato trovato.

#### `authenticate($usernameOrEmail = NULL, $password = NULL, $otherWhere = null)`

Effettua l'autenticazione utilizzando l'adattatore specificato.

##### Parametri

* `$usernameOrEmail` : Lo username o l'email dell'utente da autenticare.
* `$password` : La password dell'utente da autenticare.
* `$otherWhere` : Altre condizioni da utilizzare nella query di autenticazione.

##### Restituisce

* `mixed`: Il risultato dell'autenticazione restituito dall'adattatore.

#### `getErrorAuth()`

Restituisce l'errore dell'autenticazione.

##### Restituisce

* `mixed`: L'errore dell'autenticazione restituito dall'adattatore.

#### `setErrorAuth($error)`

Imposta l'errore dell'autenticazione.

##### Parametri

* `$error`: L'errore da impostare.

##### Restituisce

* `void`

#### `basiAuthAPI($username = null, $password = null)`

Effettua l'autenticazione di base per un'API utilizzando lo username e la password specificati.

##### Parametri

* `$username`: Lo username dell'utente per l'autenticazione di base.
* `$password` : La password dell'utente per l'autenticazione di base.

##### Restituisce

* `mixed`: Il risultato dell'autenticazione di base restituito dall'adattatore.

#### `isValid()`

Verifica se l'autenticazione è valida.

##### Restituisce

* `bool`: `true` se l'autenticazione è valida, altrimenti `false`.

#### `addStorage($data = null, $token = null)`

Aggiunge dati alla memoria di archiviazione dell'autenticazione.

##### Parametri

* `$data` (opzionale): I dati da aggiungere alla memoria di archiviazione.
* `$token` (opzionale): Il token associato ai dati.

##### Restituisce

* `mixed`: Il risultato dell'operazione di aggiunta restituito dall'adattatore.

#### `removeStorage($data = null, $token = null)`

Rimuove dati dalla memoria di archiviazione dell'autenticazione.

##### Parametri

* `$data` : I dati da rimuovere dalla memoria di archiviazione.
* `$token` : Il token associato ai dati.

##### Restituisce

* `mixed`: Il risultato dell'operazione di rimozione restituito dall'adattatore.

#### `getStorage($data = null, $token = null)`

Recupera dati dalla memoria di archiviazione dell'autenticazione.

##### Parametri

* `$data` : I dati da recuperare dalla memoria di archiviazione.
* `$token` : Il token associato ai dati.

##### Restituisce

* `mixed`: I dati recuperati dalla memoria di archiviazione restituiti dall'adattatore.

#### `hasIdentity($data = null)`

Verifica se è presente un'identità nell'autenticazione.

##### Parametri

* `$data` (opzionale): I dati da utilizzare per la verifica dell'identità.

##### Restituisce

* `mixed`: Il risultato della verifica dell'identità restituito dall'adattatore.

#### `id($data = null)`

Restituisce l'ID dell'identità nell'autenticazione.

##### Parametri

* `$data` : I dati da utilizzare per ottenere l'ID dell'identità.

##### Restituisce

* `int`: L'ID dell'identità restituito dall'adattatore `getIdentity($data = null)`

Recupera l'identità dall'autenticazione.

##### Parametri

* `$data` (opzionale): I dati da utilizzare per il recupero dell'identità.

##### Restituisce

* `mixed`: L'identità recuperata dall'adattatore.

#### `clearIdentity()`

Cancella l'identità dall'autenticazione.

##### Restituisce

* `mixed`: Il risultato dell'operazione di cancellazione restituito dall'adattatore.

#### `getToken()`

Restituisce il token dell'autenticazione.

##### Restituisce

* `mixed`: Il token dell'autenticazione restituito dall'adattatore.

#### `expireToken()`

Scade il token dell'autenticazione.

##### Restituisce

* `mixed`: Il risultato dell'operazione di scadenza del token restituito dall'adattatore.

#### `regenerateSession($data)`

Rigenera la sessione dell'autenticazione.

##### Parametri

* `$data`: I dati da utilizzare per la rigenerazione della sessione.

##### Restituisce

* `mixed`: Il risultato dell'operazione di rigenerazione della sessione restituito dall'adattatore.

#### `close()`

Chiude l'autenticazione.

##### Restituisce

* `mixed`: Il risultato dell'operazione di chiusura restituito dall'adattatore.

#### `generateToken($username = null, $email = null)`

Genera un token per l'autenticazione.

##### Parametri

* `$username` (opzionale): Lo username dell'utente per il quale generare il token.
* `$email` (opzionale): L'email dell'utente per il quale generare il token.

##### Restituisce

* `string`: Il token generato dall'adattatore.

### Esempio di utilizzo

```
// Creazione di un'istanza della classe Auth
$auth = new \System\Auth();

// Autenticazione dell'utente
$result = $auth->authenticate('username', 'password');
if ($result === true) {
// L'utente è autenticato con successo
} else {
// L'autenticazione ha fallito
$error = $auth->getErrorAuth();
}

// Verifica se l'autenticazione è valida
if ($auth->isValid()) {
// L'autenticazione è valida
}

// Aggiunta di dati alla memoria di archiviazione dell'autenticazione
$data = ['user_id' => 123];
$token = '...';
$auth->addStorage($data, $token);

// Recupero dei dati dalla memoria di archiviazione dell'autenticazione
$retrievedData = $auth->getStorage($data, $token);

// Verifica se è presente un'identità nell'autenticazione
if ($auth->hasIdentity()) {
// È presente un'identità nell'autenticazione
}

// Recupero dell'ID dell'identità nell'autenticazione
$identityId = $auth->id();

// Recupero dell'identità dall'autenticazione
$identity = $auth->getIdentity();

// Cancella l'identità dall'autenticazione
$auth->clearIdentity();

// Restituisce il token dell'autenticazione
$token = $auth->getToken();

// Scade il token dell'autenticazione
$auth->expireToken();

// Rigenera la sessione dell'autenticazione
$auth->regenerateSession($data);

// Chiude l'autenticazione
$auth->close();

// Genera un token per l'autenticazione
$newToken = $auth->generateToken('username', 'email');
```
