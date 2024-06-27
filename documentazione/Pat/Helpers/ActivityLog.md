## Classe `ActivityLog`

La classe `ActivityLog`  fornisce le funzionalità per la registrazione delle attività quotidiane nel sistema Pat OS.

### Lista dei metodi

```
create(array$data, bool$isRegisterIp = false): void
```

Il metodo `create` registra nel database le attività quotidiane svolte nel Pat OS.

* Parametri:
  * `$data`: Un array contenente i dati per il log delle attività.
  * `$isRegisterIp`: (Opzionale) Un flag booleano che indica se registrare o meno l'indirizzo IP.

```
userAgent(): string
```

Il metodo `userAgent` restituisce una stringa che rappresenta le informazioni sul client dell'utente.

* Restituisce una stringa che rappresenta le informazioni sul client dell'utente.

```
compress($string = null, $level = 9): string|false
```

Il metodo `compress` comprime una stringa utilizzando la compressione gzip.

* Parametri:
  * `$string`: (Opzionale) La stringa da comprimere.
  * `$level`: (Opzionale) Il livello di compressione (default: 9).
* Restituisce una stringa che rappresenta la stringa compressa, o `false` in caso di errore.

```
unCompress($string = null): mixed
```

Il metodo `unCompress` scompatta una stringa compressa utilizzando la compressione gzip.

* Parametri:
  * `$string`: (Opzionale) La stringa compressa da scompattare.
* Restituisce la stringa scompattata o `null` se la stringa è vuota o non è stata fornita.

```
use Helpers\ActivityLog;

// Esempio di utilizzo del metodo create
$data = [
    'user_id' => 123,
    'action' => 'Login',
    'action_type' => 'Login success',
    'description' => 'User logged in successfully',
    'request_post' => $_POST,
    'request_get' => $_GET,
    'uri' => $_SERVER['REQUEST_URI'],
    'referer' => $_SERVER['HTTP_REFERER'],
    'platform' => 'Web',
];

try {
    ActivityLog::create($data, true);
    echo "Attività registrata con successo!";
} catch (Exception $e) {
    echo "Errore durante la registrazione dell'attività: " . $e->getMessage();
}


// Esempio di utilizzo del metodo userAgent
$userAgent = ActivityLog::userAgent();
echo "User Agent: " . $userAgent;


// Esempio di utilizzo del metodo compress
$string = "Hello, world!";
$compressedString = ActivityLog::compress($string);
echo "Stringa compressa: " . $compressedString;

// Esempio di utilizzo del metodo unCompress
$uncompressedString = ActivityLog::unCompress($compressedString);
echo "Stringa scompattata: " . $uncompressedString;
```
