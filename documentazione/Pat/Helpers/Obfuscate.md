## Classe Obfuscate

La classe Obfuscate nella namespace Helpers fornisce metodi per l'obfuscamento e la decodifica di ID.

**Lista delle metodi**

* `encode($id)`: Obfusca un ID numerico.
* `decode($oid)`: Decodifica una stringa obfuscata in ID numerico.
* `setHash($hash)`: Imposta l'hash utilizzato per la generazione dei segmenti di hash.
* `getHash($str, $len)`: Restituisce un segmento di hash di una determinata lunghezza.

### Metodi

`encode($id): mixed`: Il metodo encode obfusca un ID numerico.

* Parametri:
  * `$id`: L'ID numerico da obfuscare.
* Restituisce una stringa obfuscata corrispondente all'ID fornito.

`decode($oid): float|int`: Il metodo decode decodifica una stringa obfuscata in ID numerico.

* Parametri:
  * `$oid`: La stringa obfuscata da decodificare.
* Restituisce l'ID numerico corrispondente alla stringa obfuscata.

`setHash($hash): void`: Il metodo setHash imposta l'hash utilizzato per la generazione dei segmenti di hash.

* Parametri:
  * `$hash`: L'hash da impostare.

`getHash($str, $len): false|S`: Il metodo privato getHash restituisce un segmento di hash di una determinata lunghezza.

* Parametri:
  * `$str`: La stringa da hashare.
  * `$len`: La lunghezza del segmento di hash da restituire.

```
use Helpers\Obfuscate;

// Esempio di obfuscamento di un ID
$id = 1234;
$obfuscatedId = Obfuscate::encode($id);
echo $obfuscatedId; // Output: Stringa obfuscata corrispondente all'ID fornito

// Esempio di decodifica di una stringa obfuscata in ID
$obfuscatedString = "ABC123XYZ";
$decodedId = Obfuscate::decode($obfuscatedString);
echo $decodedId; // Output: ID numerico corrispondente alla stringa obfuscata

// Esempio di impostazione dell'hash
$hash = "mysecretkey";
Obfuscate::setHash($hash);

// Esempio di ottenere un segmento di hash
$str = "Hello World";
$hashSegment = Obfuscate::getHash($str, 8);
echo $hashSegment; // Output: Segmento di hash di 8 caratteri
```
