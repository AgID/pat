## Classe `Base`

La classe `Base` fornisce metodi per la gestione dell'URL di base e dell'URL del sito all'interno di un'applicazione PHP.

### Metodi

#### `__construct()`

Costruttore della classe `Base`.

##### Eccezioni

* `\Exception`: Viene lanciata un'eccezione se non è possibile caricare il file di configurazione.

#### `baseUrl($uri = '', $protocol = NULL)`

Genera l'URL di base per l'applicazione.

##### Parametri

* `$uri` (opzionale): La parte della URI da aggiungere all'URL di base.
* `$protocol` (opzionale): Il protocollo da utilizzare nell'URL generato (es. 'http', 'https').

##### Restituisce

* `string`: L'URL di base generato.

#### `siteUrl($uri = '', $protocol = NULL)`

Genera l'URL del sito per l'applicazione.

##### Parametri

* `$uri` (opzionale): La parte della URI da aggiungere all'URL del sito.
* `$protocol` (opzionale): Il protocollo da utilizzare nell'URL generato (es. 'http', 'https').

##### Restituisce

* `string`: L'URL del sito generato.

#### `slashItem($item)`

Restituisce l'elemento della configurazione con una barra finale.

##### Parametri

* `$item`: L'elemento della configurazione da restituire.

##### Restituisce

* `string|null`: L'elemento della configurazione con una barra finale se esiste, altrimenti `null`.

#### `setItem($item, $value)`

Imposta il valore di un elemento nella configurazione.

##### Parametri

* `$item`: L'elemento della configurazione da impostare.
* `$value`: Il valore da assegnare all'elemento della configurazione.

##### Restituisce

* `void`

#### `uriString($uri)`

Converte la URI fornita in una stringa.

##### Parametri

* `$uri`: La URI da convertire.

##### Restituisce

* `string`: La URI convertita in stringa.

### Esempio di utilizzo

```
// Creazione di un'istanza della classe Base
$base = new \System\Base();

// Generazione dell'URL di base
$baseUrl = $base->baseUrl();
echo$baseUrl; // Esempio di output: http://localhost/

// Generazione dell'URL di base con una URI specifica
$uri = 'about';
$fullUrl = $base->baseUrl($uri);
echo$fullUrl; // Esempio di output: http://localhost/about

// Generazione dell'URL di base con un protocollo specifico
$protocol = 'https';
$secureUrl = $base->baseUrl($uri, $protocol);
echo$secureUrl; // Esempio di output: https://localhost/about

// Generazione dell'URL del sito completo
$siteUrl = $base->siteUrl();
echo$siteUrl; // Esempio di output: http://localhost/

// Generazione dell'URL del sito completo con una URI specifica
$fullSiteUrl = $base->siteUrl($uri);
echo$fullSiteUrl; // Esempio di output: http://localhost/about

// Generazione dell'URL del sito completo con un protocollo specifico
$secureSiteUrl = $base->siteUrl($uri, $protocol);
echo$secureSiteUrl; // Esempio di output: https://localhost/about
```
