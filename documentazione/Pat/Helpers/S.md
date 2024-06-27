## Classe `S`

La classe `S` fornisce una serie di metodi di utilità per la manipolazione e la sanificazione delle stringhe.

**Lista delle metodi**

* `toUtf8($string = null)`: Converte una stringa da ISO-8859-1 a UTF-8.
* `toIso($string = null)`: Converte una stringa da UTF-8 a ISO-8859-1.
* `sanitizeUrl($string = null)`: Sanifica una stringa nel formato URL.
* `sanitizeInt($string = null)`: Sanifica una stringa nel formato intero.
* `sanitizeFloat($string = null)`: Sanifica una stringa nel formato float.
* `sanitizeEmail($string = null)`: Sanifica una stringa nel formato email.
* `sanitizeString($string = null)`: Sanifica una stringa nel formato stringa.
* `sanitizeGlobalXSS()`: Sanifica il livello globale per la prevenzione di XSS.
* `sanitizeItem($var = null, $type = null)`: Sanifica un elemento specifico in base al tipo.
* `stripTags($string = null)`: Elimina i tag HTML, elementi di tabulazioni e nuove linee da una stringa.
* `currency($number, $decimals = 2, $decimalSep = '.', $thousandsSep = ',', $hasNumberFormat = true)`: Formatta una stringa numerica come valore di valuta.
* `chartsEntityDecode($string = null, $quote = ENT_QUOTES)`: Decodifica le entità HTML nelle stringhe.
* `specialChars($string = null)`: Converte i caratteri speciali in entità HTML.
* `startsWith($search, $string)`: Verifica se una stringa inizia con un determinato valore.
* `endsWith($search, $string)`: Verifica se una stringa termina con un determinato valore.
* `escapeXss($string = '', $xss = true, $htmlEscape = true)`: Sanifica una stringa per prevenire attacchi XSS e applica l'escape HTML.
* `ellipsizeString($str, $maxLength = 20, $ellipsis = '&hellip;')`: Riduce una stringa alla lunghezza massima specificata e aggiunge un ellissi se necessario.

### **Metodi**

`toUtf8($string = null): mixed`: Il metodo toUtf8 converte una stringa da ISO-8859-1 a UTF-8.
* Parametri:
  * `$string`: La stringa da convertire.
* Restituisce una stringa convertita da ISO-8859-1 a UTF-8.

`toIso($string = null): mixed`: Il metodo toIso converte una stringa da UTF-8 a ISO-8859-1.
* Parametri:
  * `$string`: La stringa da convertire.
* Restituisce una stringa convertita da UTF-8 a ISO-8859-1.

`sanitizeUrl($string = null): mixed`: Il metodo sanitizeUrl sanifica una stringa nel formato URL.
* Parametri:
  * `$string`: La stringa da sanificare.
* Restituisce la stringa sanificata nel formato URL.


`sanitizeInt($string = null): mixed`: Il metodo sanitizeInt sanifica una stringa nel formato intero.
* Parametri:
  * `$string`: La stringa da sanificare.
* Restituisce la stringa sanificata nel formato intero

`sanitizeFloat($string = null): mixed`: Il metodo sanitizeFloat sanifica una stringa nel formato float.
* Parametri:
  * `$string`: La stringa da sanificare.
* Restituisce la stringa sanificata nel formato float.

`sanitizeEmail($string = null): mixed`: Il metodo sanitizeEmail sanifica una stringa nel formato email.
* Parametri:
  * `$string`: La stringa da sanificare.
* Restituisce la stringa sanificata nel formato email.

`sanitizeString($string = null): mixed`: Il metodo sanitizeString sanifica una stringa nel formato stringa.
* Parametri:
  * `$string`: La stringa da sanificare.
* Restituisce la stringa sanificata nel formato stringa.

`stripTags($string = null): mixed`: Il metodo stripTags elimina i tag HTML, elementi di tabulazioni e nuove linee da una stringa.
* Parametri:
  * `$string`: La stringa da elaborare.
* Restituisce la stringa senza tag HTML, elementi di tabulazioni e nuove linee.

`currency($number, $decimals = 2, $decimalSep = '.', $thousandsSep = ',', $hasNumberFormat = true): string|null`: Il metodo currency formatta una stringa numerica come valore di valuta.
* Parametri:
  * `$number`: Il numero da formattare.
  * `$decimals`: Il numero di decimali (default: 2).
  * `$decimalSep`: Il separatore decimale (default: '.').
  * `$thousandsSep`: Il separatore delle migliaia (default: ',').
  * `$hasNumberFormat`: ndica se utilizzare la funzione number_format per la formattazione (default: true).
* Restituisce la stringa numerica formattata come valore di valuta.

`chartsEntityDecode($string = null, $quote = ENT_QUOTES): mixed`: Il metodo chartsEntityDecode decodifica le entità HTML nelle stringhe.
* Parametri:
  * `$string`: La stringa da decodificare.
  * `$quote`: Opzioni per la decodifica delle entità (default: ENT_QUOTES).
* Restituisce la stringa decodificata delle entità HTML.

`specialChars($string = null): mixed`: Il metodo specialChars converte i caratteri speciali in entità HTML.
* Parametri:
  * `$string`: La stringa da convertire.
* Restituisce la stringa con i caratteri speciali convertiti in entità HTML.

`startsWith($search, $string): bool`: Il metodo startsWith verifica se una stringa inizia con un valore specifico.
* Parametri:
  * `$search`: Il valore da cercare all'inizio della stringa.
  * `$string`: La stringa su cui eseguire la verifica.
* Restituisce true se la stringa inizia con il valore specificato, altrimenti restituisce false.

`endsWith($search, $string): bool`: Il metodo endsWith verifica se una stringa termina con un valore specifico.
* Parametri:
  * `$search`: Il valore da cercare alla fine della stringa.
  * `$string`: La stringa su cui eseguire la verifica.
* Restituisce true se la stringa termina con il valore specificato, altrimenti restituisce false.

`escapeXss($string = '', $xss = true, $htmlEscape = true): mixed`: Il metodo escapeXss sanifica una stringa per prevenire attacchi XSS e applica l'escape HTML.
* Parametri:
  * `$search`: La stringa da sanificare (predefinito: vuota).
  * `$xss`: Indica se eseguire la sanificazione per prevenire attacchi XSS (predefinito: true).
  * `$htmlEscape`: Indica se applicare l'escape HTML alla stringa (predefinito: true).
* Restituisce la stringa sanificata.

`ellipsizeString($str, $maxLength = 20, $ellipsis = '&hellip;'): mixed`: Il metodo ellipsizeString riduce una stringa alla lunghezza massima specificata e aggiunge un ellissi se necessario.
* Parametri:
  * `$str`: La stringa da ridurre.
  * `$maxLength`: La lunghezza massima desiderata (predefinito: 20).
  * `$ellipsis`: L'ellissi da aggiungere alla fine della stringa se viene ridotta (predefinito: '…').
* Restituisce la stringa ridotta alla lunghezza massima con un ellissi se necessario.

```
use Helpers\S;

// Esempio di conversione da ISO-8859-1 a UTF-8
$string = "Café";
$utf8String = S::toUtf8($string);
echo $utf8String; // Output: Café

// Esempio di conversione da UTF-8 a ISO-8859-1
$string = "Café";
$isoString = S::toIso($string);
echo $isoString; // Output: Caf&eacute;

// Esempio di sanificazione di una URL
$url = "http://example.com/?param=<script>alert('XSS');</script>";
$sanitizedUrl = S::sanitizeUrl($url);
echo $sanitizedUrl; // Output: http://example.com/?param=%3Cscript%3Ealert%28%27XSS%27%29%3B%3C%2Fscript%3E

// Esempio di sanificazione di un numero intero
$number = "123abc";
$sanitizedInt = S::sanitizeInt($number);
echo $sanitizedInt; // Output: 123

// Esempio di sanificazione di un numero float
$number = "12.34abc";
$sanitizedFloat = S::sanitizeFloat($number);
echo $sanitizedFloat; // Output: 12.34

// Esempio di sanificazione di una stringa email
$email = "user@example.com<script>alert('XSS');</script>";
$sanitizedEmail = S::sanitizeEmail($email);
echo $sanitizedEmail; // Output: user@example.com

// Esempio di sanificazione di una stringa generica
$string = "<script>alert('XSS');</script>";
$sanitizedString = S::sanitizeString($string);
echo $sanitizedString; // Output: &lt;script&gt;alert('XSS');&lt;/script&gt;

// Esempio di sanificazione di un elemento specifico in base al tipo
$item = "<script>alert('XSS');</script>";
$sanitizedItem = S::sanitizeItem($item, 'string');
echo $sanitizedItem; // Output: &lt;script&gt;alert('XSS');&lt;/script&gt;

// Esempio di eliminazione dei tag HTML, elementi di tabulazioni e nuove linee da una stringa
$htmlString = "<p>Hello <strong>world</strong>!</p>";
$strippedString = S::stripTags($htmlString);
echo $strippedString; // Output: Hello world!

// Esempio di formattazione di una stringa numerica come valore di valuta
$amount = "1234.56";
$formattedAmount = S::currency($amount, 2, '.', ',');
echo $formattedAmount; // Output: 1,234.56

// Esempio di decodifica delle entità HTML nelle stringhe
$string = "This &lt;em&gt;is&lt;/em&gt; a &amp;quot;quoted&amp;quot; text.";
$decodedString = S::chartsEntityDecode($string);
echo $decodedString; // Output: This <em>is</em> a "quoted" text.

// Esempio di conversione dei caratteri speciali in entità HTML
$string = "<script>alert('XSS');</script>";
$specialCharsString = S::specialChars($string);
echo $specialCharsString; // Output: &lt;script&gt;alert('XSS');&lt;/script&gt;

// Esempio di verifica se una stringa inizia con un determinato valore
$string = "Hello, world!";
if (S::startsWith("Hello", $string)) {
    echo "La stringa inizia con 'Hello'";
} else {
    echo "La stringa non inizia con 'Hello'";
}

// Esempio di verifica se una stringa termina con un determinato valore
$string = "Hello, world!";
if (S::endsWith("world!", $string)) {
    echo "La stringa termina con 'world!'";
} else {
    echo "La stringa non termina con 'world!'";
}

// Esempio di sanificazione di una stringa per prevenire attacchi XSS e applicazione dell'escape HTML
$string = "<script>alert('XSS');</script>";
$sanitizedString = S::escapeXss($string);
echo $sanitizedString; // Output: &lt;script&gt;alert('XSS');&lt;/script&gt;

// Esempio di riduzione di una stringa alla lunghezza massima specificata con ellissi
$string = "Lorem ipsum dolor sit amet, consectetur adipiscing elit.";
$ellipsizedString = S::ellipsizeString($string, 20);
echo $ellipsizedString; // Output: Lorem ipsum dolor...
```
