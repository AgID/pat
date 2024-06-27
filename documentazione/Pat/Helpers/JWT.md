## Classe `JWT`

La classe `JWT` nella namespace `Helpers` estende la classe `\Firebase\JWT\JWT` e fornisce metodi aggiuntivi per la generazione, decodifica e verifica dei JSON Web Token (JWT).

### Metodi

La classe `JWT` eredita tutti i metodi della classe `\Firebase\JWT\JWT`. Di seguito vengono elencati solo i metodi aggiuntivi specifici della classe `JWT` : Il metodo decode decodifica un token JWT.

`decode(string $jwt, $keyOrKeyArray, stdClass &$headers = null): stdClass`: Il metodo decode decodifica un token JWT.

* Parametri:
  * `$jwt`: Il token JWT da decodificare.
  * `$keyOrKeyArray`: La chiave o l'array di chiavi utilizzate per la verifica del token.
  * `$headers`: (Opzionale) Un oggetto stdClass che conterrà l'intestazione decodificata.
* Restituisce un oggetto stdClass che rappresenta il payload del token JWT decodificato.

```
use Helpers\JWT;

// Esempio di generazione di un token JWT
$key = 'mySecretKey';
$payload = [
    'user_id' => 123,
    'username' => 'john.doe',
    'exp' => time() + 3600 // Scadenza del token dopo 1 ora
];
$token = JWT::encode($payload, $key);
echo $token; // Output: Token JWT generato

// Esempio di decodifica di un token JWT
$decodedToken = JWT::decode($token, $key);
var_dump($decodedToken); // Output: Oggetto stdClass che rappresenta il payload decodificato

// Esempio di verifica della firma di un token JWT
$isValidSignature = JWT::verify($token, $key);
if ($isValidSignature) {
    echo "La firma del token è valida";
} else {
    echo "La firma del token non è valida";
}

// Esempio di generazione di una chiave segreta
$secretKey = JWT::generateSecretKey();
echo $secretKey; // Output: Chiave segreta generata

// Esempio di generazione di una coppia di chiavi pubblica/privata
$keyPair = JWT::generateKeyPair();
echo $keyPair['privateKey']; // Output: Chiave privata generata
echo $keyPair['publicKey']; // Output: Chiave pubblica generata
```
