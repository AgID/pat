## Classe `Rsa`

La classe `Rsa`  fornisce metodi per la gestione delle chiavi RSA, inclusa la generazione di chiavi private e pubbliche, la firma e la verifica dei dati utilizzando RSA.

**Lista delle metodi**
`__construct(int $privateKeyBits = 2048, int $privateKeyType = OPENSSL_KEYTYPE_RSA, $digestAlg = null)`
`create($privateKeyBits = 2048, $privateKeyType = OPENSSL_KEYTYPE_RSA, $digestAlg = null)`
`buildRSA()`
`getPrivateKey()`
`getPublicKey()`
`sign(string $data = null, array|OpenSSLAsymmetricKey|string|OpenSSLCertificate $privateKey = null, int|string $algo = null)`
`verify(?string $data = null, ?string $signature = null, OpenSSLAsymmetricKey|OpenSSLCertificate|array|string|null $publicKey = null, string|int $algo = "sha256WithRSAEncryption")`

### Metodi

* `__construct(int $privateKeyBits = 2048, int $privateKeyType = OPENSSL_KEYTYPE_RSA, $digestAlg = null)`: Costruttore per la classe `Rsa` che genera una nuova coppia di chiavi private e pubbliche RSA.
  * Parametri:
    * `$privateKeyBits`: Numero di bit per la chiave privata. Valore predefinito: `2048`.
    * `$privateKeyType`: Tipo di chiave privata RSA. Valore predefinito: `OPENSSL_KEYTYPE_RSA`.
    * `$digestAlg`: Algoritmo di digest per la generazione delle chiavi. Se non specificato, viene utilizzato il valore predefinito di OpenSSL.
* `create($privateKeyBits = 2048, $privateKeyType = OPENSSL_KEYTYPE_RSA, $digestAlg = null)`: Metodo statico per creare un'istanza della classe `Rsa` e generare una nuova coppia di chiavi private e pubbliche RSA.
  * Parametri:
    * `$privateKeyBits`: Numero di bit per la chiave privata. Valore predefinito: `2048`.
    * `$privateKeyType`: Tipo di chiave privata RSA. Valore predefinito: `OPENSSL_KEYTYPE_RSA`.
    * `$digestAlg`: Algoritmo di digest per la generazione delle chiavi. Se non specificato, viene utilizzato il valore predefinito di OpenSSL.
  * Restituisce:
    * `Rsa`: Un'istanza della classe `Rsa`.
* `getPrivateKey()`: Restituisce la chiave privata generata.
  * Restituisce:
    * `null|string`: La chiave privata generata.
* `getPublicKey()`: Restituisce la chiave pubblica generata.
  * Restituisce:
    * `null|string`: La chiave pubblica generata.
* `sign(string $data = null, array|OpenSSLAsymmetricKey|string|OpenSSLCertificate $privateKey = null, int|string $algo = null)`: Aggiunge una firma digitale a un dato utilizzando la chiave privata.
  * Parametri:
    * `$data`: Il dato su cui aggiungere la firma digitale.
    * `$privateKey`: La chiave privata utilizzata per la firma. Se non specificata, viene utilizzata la chiave privata generata dalla classe.
    * `$algo`: L'algoritmo di firma da utilizzare. Se non specificato, viene utilizzato il valore predefinito di OpenSSL.
  * Restituisce:
    * `string`: La firma digitale generata.
* `verify(?string $data = null, ?string $signature = null, OpenSSLAsymmetricKey|OpenSSLCertificate|array|string|null $publicKey = null, string|int $algo = "sha256WithRSAEncryption")`: Verifica la firma digitale di un dato utilizzando la chiave pubblica.
  * Parametri:
    * `$data`: La stringa di dati utilizzata per generare la firma digitale.
    * `$signature`: La firma digitale da verificare.
    * `$publicKey`: La chiave pubblica utilizzata per la verifica. Se non specificata, viene utilizzata la chiave pubblica generata dalla classe.
    * `$algo`: L'algoritmo di firma utilizzato per generare la firma digitale. Se non specificato, viene utilizzato il valore predefinito di OpenSSL.
  * Restituisce:
    * `bool|int`: `true` se la firma digitale è valida, `false` altrimenti.


```
use Helpers\Security\Rsa;

// Creazione di una nuova istanza di Rsa e generazione delle chiavi
$rsa = new Rsa();
$privateKey = $rsa->getPrivateKey();
$publicKey = $rsa->getPublicKey();

// Aggiunta di una firma digitale a un dato
$data = 'Hello, world!';
$signature = $rsa->sign($data);

// Verifica della firma digitale
$isVerified = $rsa->verify($data, $signature);

// Creazione di una nuova istanza di Rsa utilizzando il metodo statico create()
$rsa = Rsa::create();
```
