## Classe `Crypto`

La classe `Crypto`  fornisce metodi per la cifratura e decifratura dei dati. Di seguito è riportata una descrizione dei metodi della classe:

`encrypt(string $data)`
`decrypt(string $data)`
`verify(string $data = '')`
`isValidString(string $string = '')`

### Metodi

* `encrypt(string $data)`: Crittografa una stringa.
  * Parametri:
    * `$data`: La stringa da crittografare.
  * Restituisce:
    * `bool|string`: La stringa crittografata se la crittografia ha successo, altrimenti `false`.
* `decrypt(string $data)`: Decrittografa una stringa crittografata.
  * Parametri:
    * `$data`: La stringa crittografata da decrittografare.
  * Restituisce:
    * `bool|string`: La stringa decrittografata se la decrittografia ha successo, altrimenti `false`.
* `verify(string $data = '')`: Verifica se una stringa è crittografata e la decrittografa se necessario.
  * Parametri:
    * `$data` (opzionale): La stringa da verificare e, se crittografata, da decrittografare.
  * Restituisce:
    * `string|bool`: La stringa decrittografata se la verifica ha successo, altrimenti la stringa originale.
* `isValidString(string $string = '')`: Verifica se una stringa è valida.
  * Parametri:
    * `$string` (opzionale): La stringa da verificare.
  * Restituisce:
    * `string|bool`: La stringa valida se supera la verifica, altrimenti `false`.

```
use Helpers\Security\Crypto;

// Crittografa una stringa
$data = 'Hello, world!';
$encryptedData = Crypto::encrypt($data);

// Decrittografa una stringa crittografata
$decryptedData = Crypto::decrypt($encryptedData);

// Verifica e decrittografa una stringa se necessario
$encryptedString = 'encrypted string';
$verifiedData = Crypto::verify($encryptedString);

// Verifica se una stringa è valida
$validString = 'valid string';
$isValid = Crypto::isValidString($validString);
```
