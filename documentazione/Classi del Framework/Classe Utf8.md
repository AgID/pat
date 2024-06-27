## Classe `Utf8`

La classe `Utf8` fornisce metodi per la gestione delle stringhe in codifica UTF-8.

### Proprietà

#### `$utf8Enabled`

Una variabile booleana che indica se la codifica UTF-8 è abilitata o meno.

### Metodi

#### `__construct($charset)`

Costruttore della classe `Utf8`.

* Parametri:
  * `$charset`: La codifica dei caratteri da utilizzare.
* Restituisce: N/A

#### `cleanString(string $str): string`

Rimuove i caratteri illegali da una stringa.

* Parametri:
  * `$str`: La stringa da pulire.
* Restituisce: La stringa pulita.

#### `safeAsciiForXml($str)`

Rimuove i caratteri invisibili da una stringa.

* Parametri:
  * `$str`: La stringa da pulire (non crittografata).
* Restituisce: Una stringa senza caratteri invisibili.

#### `convertToUtf8(string $str, string $encoding): bool|string`

Converte una stringa in UTF-8.

* Parametri:
  * `$str`: La stringa da convertire.
  * `$encoding`: La codifica della stringa che deve essere convertita.
* Restituisce: La stringa convertita in UTF-8 o `false` in caso di errore.

#### `isAscii(string $str): bool`

Verifica se una stringa è in codifica ASCII.

* Parametri:
  * `$str`: La stringa da verificare.
* Restituisce: `true` se la stringa è in codifica ASCII, `false` altrimenti.

#### `isUtf8(string $string = ''): bool`

Verifica se una stringa è in codifica UTF-8.

* Parametri:
  * `$string`: La stringa da verificare (opzionale).
* Restituisce: `true` se la stringa è in codifica UTF-8, `false` altrimenti.
  ```
  use System\Utf8;

  // Creazione di un'istanza della classe Utf8
  $utf8 = new Utf8('UTF-8');

  // Pulizia di una stringa da caratteri illegali
  $string = "Héllô Wörld!";
  $cleanString = $utf8->cleanString($string);
  echo "Stringa pulita: " . $cleanString;

  // Rimozione dei caratteri invisibili da una stringa
  $string = "Héllô\nWörld!";
  $safeString = $utf8->safeAsciiForXml($string);
  echo "Stringa sicura: " . $safeString;

  // Verifica se una stringa è in codifica UTF-8
  $string = "Héllô Wörld!";
  $isUtf8 = $utf8->isUtf8($string);
  echo "La stringa è in UTF-8? " . ($isUtf8 ? "Sì" : "No");

  // Conversione di una stringa in UTF-8
  $string = "Héllô Wörld!";
  $convertedString = $utf8->convertToUtf8($string, 'ISO-8859-1');
  echo "Stringa convertita in UTF-8: " . $convertedString;
  ```

Nell'esempio sopra, viene creata un'istanza della classe Utf8. Vengono quindi utilizzati i metodi della classe per eseguire operazioni come la pulizia di una stringa da caratteri illegali, la rimozione dei caratteri invisibili, la verifica se una stringa è in codifica UTF-8 e la conversione di una stringa in UTF-8.
