## Classe `Formatting`

La classe `Formatting` fornisce metodi per la formattazione e la manipolazione delle stringhe. Questi metodi includono la codifica e la decodifica delle stringhe in formato UTF-8, la rimozione dei caratteri invisibili, l'escape dei caratteri speciali, la conversione di stringhe in formato slug e altro ancora.

### Metodi

#### `utf8UriEncode($utf8String, $length = 0)`

Codifica una stringa UTF-8 per l'utilizzo in un URL.

* Parametri:
  * `$utf8String`: La stringa UTF-8 da codificare.
  * `$length` (opzionale): La lunghezza massima della stringa codificata. Se specificato, la stringa codificata verrà troncata se supera questa lunghezza.
* Restituisce: La stringa UTF-8 codificata.

#### `seemsUtf8($string)`

Verifica se una stringa sembra essere codificata in formato UTF-8.

* Parametri:
  * `$string`: La stringa da verificare.
* Restituisce: `true` se la stringa sembra essere codificata in UTF-8, `false` altrimenti.

#### `sanitize($title)`

Rimuove caratteri non validi e formatta una stringa per essere utilizzata come slug in un URL.

* Parametri:
  * `$title`: La stringa da formattare.
* Restituisce: La stringa formattata come slug.

#### `slug($string = '', $separator = '-', $lowercase = true)`

Converte una stringa in formato slug, sostituendo gli spazi con il separatore specificato.

* Parametri:
  * `$string` (opzionale): La stringa da convertire. Se non specificata, verrà utilizzata una stringa vuota.
  * `$separator` (opzionale): Il separatore da utilizzare per sostituire gli spazi. Valore predefinito: `'-'`.
  * `$lowercase` (opzionale): Indica se la stringa convertita deve essere in minuscolo. Valore predefinito: `true`.
* Restituisce: La stringa convertita in formato slug.

#### `escapeSql($string = '', $removeInvisibleCharacters = true, $urlEncoded = true)`

Esegue l'escape dei caratteri speciali per l'inserimento di una stringa in una query SQL.

* Parametri:
  * `$string` (opzionale): La stringa da cui eseguire l'escape. Se non specificata, verrà utilizzata una stringa vuota.
  * `$removeInvisibleCharacters` (opzionale): Indica se rimuovere i caratteri invisibili dalla stringa. Valore predefinito: `true`.
  * `$urlEncoded` (opzionale): Indica se la stringa è codificata come URL. Valore predefinito: `true`.
* Restituisce: La stringa con i caratteri speciali escapati per l'inserimento in una query SQL.

#### `charsetDecodeUtf8($string)`

Decodifica una stringa codificata in UTF-8 e restituisce la versione originale.

* Parametri:
  * `$string`: La stringa UTF-8 da decodificare.
* Restituisce: La stringa decodificata.

#### `removeInvisibleCharacters($string, $urlEncoded = TRUE)`

Rimuove i caratteri invisibili da una stringa.

* Parametri:
  * `$string`: La stringa da cui rimuovere i caratteri invisibili.
  * `$urlEncoded` (opzionale): Indica se la stringa è codificata come URL. Valore predefinito: `TRUE`.
* Restituisce: La stringa con i caratteri invisibili rimossi.

#### `htmlEscape($var = '', $charset = CHARSET, $doubleEncode = TRUE)`

Esegue l'escape dei caratteri speciali in una stringa per l'inserimento in un contesto HTML.

* Parametri:
  * `$var` (opzionale): La stringa da cui eseguire l'escape. Se non specificata, verrà utilizzata una stringa vuota.
  * `$charset` (opzionale): L'encoding dei caratteri. Valore predefinito: `CHARSET`.
  * `$doubleEncode` (opzionale): Indica se es## Classe `Formatting`

La classe `Formatting` fornisce metodi per la formattazione e la manipolazione delle stringhe. Questi metodi includono la codifica e la decodifica delle stringhe in formato UTF-8, la rimozione dei caratteri invisibili, l'escape dei caratteri speciali, la conversione di stringhe in formato slug e altro ancora.

#### `convertEncodeQuotes($input = '', $charset = CHARSET, $htmlEntities = "HTML-ENTITIES")`

Converte le quotazioni codificate in una stringa in un formato specificato.

* Parametri:
  * `$input` (opzionale): La stringa da convertire. Se non specificata, verrà utilizzata una stringa vuota.
  * `$charset` (opzionale): L'encoding dei caratteri. Valore predefinito: `CHARSET`.
  * `$htmlEntities` (opzionale): Il formato di codifica delle entità HTML. Valore predefinito: `"HTML-ENTITIES"`.
* Restituisce: La stringa convertita con le quotazioni codificate nel formato specificato.

#### `asciiToEntities($string)`

Converte i caratteri ASCII nella stringa in entità HTML.

* Parametri:
  * `$string`: La stringa da convertire.
* Restituisce: La stringa con i caratteri ASCII convertiti in entità HTML.

#### `entitiesToAscii($string, $all = TRUE)`

Converte le entità HTML nella stringa in caratteri ASCII corrispondenti.

* Parametri:
  * `$string`: La stringa da convertire.
  * `$all` (opzionale): Indica se convertire tutte le entità HTML. Valore predefinito: `TRUE`.
* Restituisce: La stringa con le entità HTML convertite in caratteri ASCII corrispondenti.

#### `escapeSpecialChars($text, $charset = CHARSET)`

Esegue l'escape dei caratteri speciali in una stringa utilizzando l'encoding specificato.

* Parametri:
  * `$text`: La stringa da cui eseguire l'escape.
  * `$charset` (opzionale): L'encoding dei caratteri. Valore predefinito: `CHARSET`.
* Restituisce: La stringa con i caratteri speciali escapati.

#### `decodeEntities($text, $charset = CHARSET)`

Decodifica le entità HTML in una stringa utilizzando l'encoding specificato.

* Parametri:
  * `$text`: La stringa da decodificare.
  * `$charset` (opzionale): L'encoding dei caratteri. Valore predefinito: `CHARSET`.
* Restituisce: La stringa decodificata.

#### `convertAccentedChars($string)`

Converte i caratteri con accenti nella stringa in caratteri senza accenti corrispondenti.

* Parametri:
  * `$string`: La stringa da convertire.
* Restituisce: La stringa con i caratteri con accenti convertiti in caratteri senza accenti corrispondenti.

Ecco gli esempi per tutti i metodi documentati della classe `Formatting`:

#### Esempio di utilizzo del metodo `htmlEscape()`:

```
useSystem\Formatting;

$html = '<p>This is a <strong>sample</strong> text.</p>';

$escapedHtml = Formatting::htmlEscape($html);

echo $escapedHtml;
```

```
<p>This is a <strong>sample</strong> text.</p>
```

Nell'esempio sopra, viene utilizzato il metodo `htmlEscape()` per eseguire l'escape dei caratteri speciali nella stringa HTML. La stringa originale viene passata al metodo, che restituisce la stessa stringa con i caratteri speciali escapati per l'inserimento in un contesto HTML. L'output verrà visualizzato come `&lt;p&gt;This is a &lt;strong&gt;sample&lt;/strong&gt; text.&lt;/p&gt;`, dove i caratteri `<`, `>`, e `"` sono stati sostituiti con le rispettive entità HTML.

#### Esempio di utilizzo del metodo `convertEncodeQuotes()`:

```
useSystem\Formatting;

$input = 'This is "quoted" text.';

$converted = Formatting::convertEncodeQuotes($input);

echo $converted;
```

```
This is "quoted" text.
```

Nell'esempio sopra, viene utilizzato il metodo `convertEncodeQuotes()` per convertire le quotazioni codificate nella stringa di input nel formato specificato. La stringa originale viene passata al metodo, che restituisce la stessa stringa con le quotazioni convertite nel formato specificato. L'output verrà visualizzato come `This is &quot;quoted&quot; text.`, dove le quotazioni `"` sono state convertite in entità HTML `&quot;`.

#### Esempio di utilizzo del metodo `asciiToEntities()`:

```
useSystem\Formatting;

$string = 'This is "quoted" text.';

$converted = Formatting::asciiToEntities($string);

echo $converted;
```

```
This is "quoted" text.
```

Nell'esempio sopra, viene utilizzato il metodo `asciiToEntities()` per convertire i caratteri ASCII nella stringa in entità HTML. La stringa originale viene passata al metodo, che restituisce la stessa stringa con i caratteri ASCII convertiti in entità HTML. L'output verrà visualizzato come `This is &quot;quoted&quot; text.`, dove le quotazioni `"` sono state convertite in entità HTML `&quot;`.

#### Esempio di utilizzo del metodo `entitiesToAscii()`:

```
useSystem\Formatting;

$string = 'This is "quoted" text.';

$converted = Formatting::entitiesToAscii($string);

echo $converted;
```

```
This is "quoted"text.
```

Nell'esempio sopra, viene utilizzato il metodo `entitiesToAscii()` per convertire le entità HTML nella stringa in caratteri ASCII corrispondenti. La stringa originale viene passata al metodo, che restituisce la stessa stringa con le entità HTML convertite in caratteri ASCII. L'output verrà visualizzato come `This is "quoted" text.`, dove le entità HTML `&quot;` sono state convertite nella corrispondente quotazione `"`.
