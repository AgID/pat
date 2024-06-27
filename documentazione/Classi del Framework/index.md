# Guida introduttiva al Framework di sviluppo.

### Requisiti minimi per il funzionamento del Framework:

- PHP >= 7.2.5
- BCMath PHP Extension
- Ctype PHP Extension
- Fileinfo PHP extension
- JSON PHP Extension
- Mbstring PHP Extension
- OpenSSL PHP Extension
- PDO PHP Extension
- Tokenizer PHP Extension
- XML PHP Extension

### Struttura delle cartelle

Il framework è strutturato in maniera tale da rispettare il paradigma del pattern MVC, dividendo quelle che sono le classi, gli helpers ed i file utility di sistema dalle classi di sviluppo di una web app.

Di seguito la struttura della directory

**index.php**
**/app**
**../Cache**
**../Config**
**../Helpers**
**../http**
**../Langs**
**../Logs**
**../Middleware**
**../Models**
**../Modules**
**../Plugins**
**../Routes**
**../Themes**
**../Common.php**
**/core**
**../Helpers**
**../System**
**../Bootstrap.php**
**/media**
**/vendor**

Partendo dalla radice del framework le cartelle principali sono:
- **app**
	Questa è la cartella preposta allo sviluppo di un'applicazione web, infatti tutto il codice sorgente  verrà scritto in questa directory.
- **core**
	Questa cartelle contiene tutte le librerie di sistema del Framework, come ad esempio la libreria per l'invio delle email, l'uploads dei files, la gestione degli input etc.. sono contenuti in questa cartella.
- **media**
	Questa cartella contiene tutti i file media (*.jpg, *.xml, *.pdf, etc..) caricati dagli utenti dell'applicazione web.
- **vendor**
	Questa cartella contiene tutti i packages sviluppati da terzi, e integrati perfettamente nel Framework.
	*Per l'integrazione delle librarie di terze parti il Framework utilizza il package manager composer.*  



------



### Struttura Sotto directory nella cartella "app"


- **Cache**
	Questa cartella contiene tutti i file creati dalla classe cache.
- **Config**
	Questa cartella contiene tutti i file di configurazione del Framework, ad esempio: parametri per l'invio della e-mail, parametri per la connessione al database etc..
- **Helpers**
	Questa cartella contiene tutte le funzioni personalizzate di un'applicazione web.
- **Http**
	In questa cartella vengono inclusi tutti i file di tipo Controller nel paradigma del pattern MCV
- **Langs**
	In questa cartella ci sono tutti i file di traduzione (Multilingua) per l'internazionalizzazione dell'applicazione web.
- **Logs**
	In questa cartella ci sono tuti i file logs creati dall'applicazione web.
- **Middleware**
	In questa cartella ci sono tutte le classi di tipo middleware dell'applicazione web.
- **Models**
	In questa cartella ci sono tutte le classi ti tipo model per la comunicazione con un database.
- **Modules**
	Se con il Framework viene sviluppato un CMS, questa cartella è vista come un'estensione delle funzionalità del CMS; ad esempio se in un  CMS viene sviluppato un calendario eventi, in questa cartella vengono salvati tutti i sorgenti del calendario, dal Controller, helpers, models template etc.. 
- **Plugins**
	Se con il Framework viene sviluppato un CMS in questa cartella vengono salvati tutti i sorgenti atti ad estendere le funzionalità base del CMS.
- **Routes**
	In questa cartella ci sono gli indirizzamenti dei "Controller" chiamati nelle url 
- **Themes**
	Questa cartella è da considerarsi in un paradigma di tipo MVC come le viste, integrando al suo interno una libraria di tipo Template Engine, stile Blade di Laravel.
	Se nel Framewokr viene sviluppato un CMS, questa cartella è da considerarsi come la gestione dei temi del Content Managment System.
- **Common.php**
In questo file php, vengono create funzioni e procedure personalizzate richiamate prima dell'inizializzazione dell'ambiente di sviluppo, quindi - "Routes", "Controllers", "Model" - e dopo l'inizializzazione dell'autoloader delle classi.

---

### Diagramma di flusso di un'applicazine web

Tutte le richieste dell'applicazione passano dalla **index.php**, questo file è da considerarsi come entry point di esecuzione del programma. 

- Index.php; inizializza tutte le risorse base necessarie nell'avvio dell'applicazione.

- Successivamente le richieste vengono indirizzate nel package di routing, dove quest'ultimo inizializzerà un Controller oppure una funzione anonima. La gestione delle rotte è divisa in due specifiche tipologia di chiamata: 

  - **WEB**: Queste rotte sono per le interazione di tipo web, ovvero le richieste che avvengono tramite browser, e sono salvate nella path *"app/Routes/Web.php"*
  - **API**: Queste rotte sono finalizzate alla costruzione di chiamate RESTful API e sono salvate nella path  *"app/Routes/Api.php"*

  una volta chiamato il "Controller" si ha l'accesso a tutto il Patter MCV, chiamare i modelli, le viste e tutte le librerie messe a disposizione del Framework.

  *NB: In realtà le librerie messe a disposizione dal Framework possono essere inizializzate anche*  prima dell'inizializzazione del Controller. Ad esempio per la realizzazione di una classe di tipo Middleware, oppure nella chiamata e registrazione di una classe di tipo Event::fire();

  

------



### MVC Model-View-Controller

Il Framework si basa sul modello di sviluppo Model-View-Controller. Il Pattern MVC è un approccio software che separa la logica dell'applicazione dalla presentazione. 
In pratica, consente alle applicazioni web di contenere uno script minimo poiché la presentazione è separata dallo scripting PHP.

- Il **modello** rappresenta le tue strutture dati. In genere le classi del modello conterranno funzioni che consentono di recuperare, inserire e aggiornare le informazioni nel database.
- La **vista** è l'informazione che viene presentata a un utente. Una vista sarà normalmente una pagina web, ma nel Framework una vista può anche essere un frammento di pagina come un'intestazione o un piè di pagina. Può anche essere una pagina RSS, robot.txt, sitemap.xml o qualsiasi altro tipo di "pagina".
   Questo fa in modo che ogni vista può essere dinamicizzata con una certa logica di programmazione.
- Il **Controller** funge da *intermediario* tra il Modello, la Vista e qualsiasi altra risorsa necessaria per elaborare la richiesta HTTP e generare una pagina web o una risorsa.

I packages del Framework sono abbastanza autonomi, questo fa in modo che le prestazioni dell'applicazione siano più performanti in fase di avvio. Ogni package può essere caricato autonomamente senza richiedere il carico delle dipendenze di altre librerie in fase di inizializzazione.

Prima di iniziare con i tutorial per la creazione delle rotte, Controller, model e caricamenti degli helper e librerie diamo un sguardo a quelle che sono le funzioni e constanti riservate di sistema.



------



### Constanti riservate di sistema:

| Nome delle costanti | Descrizione delle constati                                   |
| ------------------- | ------------------------------------------------------------ |
| **SELF**            | Il nome del file corrente                                    |
| **PATH**            | Constante del percorso Assoluto dell'applicazione            |
| **CORE_PATH**       | Constante del percorso assoluto dei packages di SISTEMA      |
| **APP_PATH**        | Constante del percorso assoluto dei packages dell'APP        |
| **MEDIA_PATH**      | Constante del percorso assoluto dei file media               |
| **CACHE_PATH**      | Constante del percorso della cartella cache                  |
| **CONFIG_PATH**     | Constante del percorso della cartella config                 |
| **LOGS_PATH**       | Constante del percorso della cartella di Logs                |
| **MODULES_PATH**    | Constante del percorso della cartella dei moduli             |
| **THEME_PATH**      | Constante del percorso della cartella dei temi (Viste)       |
| **VERSION**         | Versione del Framework                                       |
| **MB_ENABLED**      | Verifica se la funzione mbstring (multibyte string) è installata sul server.<br />Maggiori informazioni:  [mbsrtring](https://www.php.net/manual/en/mbstring.installation.php) |
| **ICONV_ENABLED**   | Verifica se la funzione iconv è installata sul server. <br />Maggiorni informazioni: [iconv](https://www.php.net/manual/en/function.iconv.ph) |



------



### Funzioni riservate di sistema

- `isHttps()`

- `config()`

- `removeInvisibleCharacters()`

- `htmlEscape()`

- `getallheaders()`

- `isPhp()`

- `helper()`

- `siteUrl()`

- `baseUlr()`

- `currentUrl()`

- `currentQueryStringUrl()`

- `is_cli()`

- `show404()`

- `showError()`

- `setStatusHeader()`

- `getMimes()`

- `stringifyAttributes()`

- `__()`

- `isReallyWritable()`

- `loadConfigMail()`

- `function_usable()`

- `config()`

- `render()`

  

`isHttps()`: Verifica se nell'applicativo è installato o meno il certificato https. Se il certificato è installato ritorna true altrimenti ritorna false;

```php

// Verifica nel seguente link http://www.example.com se il certificato è installato
var_dump(isHttps()); // ritorna TRUE
      
// Verifica nel seguente link https://www.example.com se il certificato è installato
var_dump(isHttps()); // ritorna FALSE
```



------



`removeInvisibleCharacters($string ,$url_encoded):` Questa funzione impedisce l'inserimento di caratteri NULLI  tra i caratteri ASCII.

| Parametri:       | **$str** (*string*) – Input string <br />**$url_encoded** : (*bool*) – Indica se rimuovere anche i caratteri codificati in URL |
| :--------------- | ------------------------------------------------------------ |
| Ritona:          | Stringa sanificata                                           |
| Tipo di ritorno: | string                                                       |

```php
$string = 'Java\\0script'; 
echo removeInvisibleCharacters($string);
// stampa: Javascript

```



------



`htmlEscape($string):` Questa funzione funge da *alias* per la funzione nativa htmlspecialchars () di PHP, con il vantaggio di poter accettare un array di stringhe. Viene usata nella classe Security per prevenire Cross Site Scripting (XSS).

| Parametri:       | $string (*mix) – Variabile per l'escape (accetta una stringa o un array) |
| :--------------- | ------------------------------------------------------------ |
| Ritona:          | ritorna l'escape di HTML di una stringa o stringhe           |
| Tipo di ritorno: | Mix: strtinga o array                                        |



------



`getallheaders()` : Verifica se nel server è installata questa funzione. Se non è installata la emula e ritorna tutte le chiavi / valori dell'intestazione HTTP come array associativo per la richiesta corrente.
Tipo di ritorno:  Le coppie chiave / valore dell'intestazione HTTP.



------



`isPhp($version)` : Determina se la versione PHP utilizzata è maggiore del numero di versione fornito.

| Parameters:      | **$version** (*string*) – Numero versione php                |
| :--------------- | ------------------------------------------------------------ |
| Ritona:          | Determina se la versione PHP utilizzata è maggiore del numero di versione fornito. |
| Tipo di ritorno: | bool                                                         |

```php
// Versione di php 5.6.40 installata sul server
if (isPhp('5.3')) {
  echo "La versione del php da cerificare è maggiorni alla 5.3";
}
```



------



`helper($string)`: Questa funzione ha il compito di caricare un helper nell'applicazione web.

| Parameters:          | **$string** (mix) – nome dell'helper come stringa o array contenente più helper |
| :------------------- | ------------------------------------------------------------ |
| **Ritona**:          | Nullo, include l'helper se si passa il nome dell'helper sotto forma di stringa oppure gli helpers se si passa i nomi degli helpers sotto forma di array. <br />Se l'helper in questione non è stato trovato stampa una schermata di errore. |
| **Tipo di ritorno:** | Nullo                                                        |



Per includere un singolo helper nella funzione si passa l'argomento sotto forma di stringa:

```php
// Includo un singolo helper, in questo caso carico la lista delle funzioni per la manipolazione degli array
helper('arr');

// Ora è possibile utilizzare la lista delle funzione per la manipolazione degli array:
$array = ['foo' => 'bar'];
list($keys, $values) = array_divide($array);
print_r($keys);
print_r($values);

```



Per includere una lista degli helper è possibile passare un array con i nomi degli helpers come argomento nella funzione:



```php
// Includo una lista degli helpers sotto forma di array:
helper(['arr','url']);

// Ora è possibile utilizzare la lista delle funzione per la manipolazione degli array e per la manipolazioni delle url:
$array = [
  ['name' => 'Taylor'],
  ['name' => 'Dayle']
];
$array = array_pluck($array, 'name');
print_r($array);

// Ed è anche possibile utilizzare la lista delle funzioni per la manipolazione delle url.
echo urlTitle('stringa "url friendly"'); // stringa-url-friendly
echo reditect('http://www.google.it'); // redirect alla seguente pagine http://www.google.it

```



------



`siteUrl([$uri = ''[, $protocol = NULL]])`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$uri** (*string*) – Stringa dell'URI <br />**$protocol** (*string*) – Protocollo, es:. ‘http’ or ‘https’ |
| **Ritorno**         | URL dell'applicazione web                                    |
| **Tipo di ritorno** | String                                                       |

Restituisce l'URL del tuo sito, come specificato nel file di configurazione. Il file index.php (o qualunque cosa tu abbia impostato come sito **index_page** nel tuo file di configurazione) verrà aggiunto all'URL, così come tutti i segmenti URI che passi alla funzione, più **url_suffix** come impostato nel tuo file di configurazione.

Sei incoraggiato a utilizzare questa funzione ogni volta che devi generare un URL locale in modo che le tue pagine diventino più portabili nel caso in cui il tuo URL cambi.

I segmenti possono essere facoltativamente passati alla funzione come stringa o matrice. Ecco un esempio di stringa:

```php
// Supponiamo che nel file (app/Config/app.php) di configurazione siano impostati nell'array i seguenti parametri:
[..omissis..]
'site_url' => 'http://example.com/',
'url_suffix' => '.html',
[..omissis..]

// Dato il seguente script:
echo siteUrl('segmento1/segmento1/123');
// il risultatao sarà : http://example.com/segmento1/segmento2/123.html

```



Di seguito è riportato un esempio con segmenti passati come array:

```php
// Supponiamo che nel file (app/Config/app.php) di configurazione siano impostati nell'array i seguenti parametri:
[..omissis..]
'site_url' => 'http://example.com/',
'url_suffix' => '.html',
[..omissis..]

// Dato il seguente script:
$segments = ['segmento1','segmento2','123']; 
echo  siteUrl($segments);
// il risultatao sarà : http://example.com/segmento1/segmento2/123.html

```



------



`baseUlr([$uri = '' [, $protocol = NULL]])`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$uri** (*string*) – Stringa dell'URI <br />**$protocol** (*string*) – Prosocollo, es:. ‘http’ or ‘https’ |
| **Ritorno**         | Base URL dell'applicazione web                               |
| **Tipo di ritorno** | String                                                       |



Restituisce l'URL di base del sito, come specificato nel file di configurazione. Esempio:

```php
// Questa funzione restituisce la stessa cosa di site_url(), senza che vengano aggiunti index_page o url_suffix .

echo baseUlr();
```



Inoltre la funzione `baseUlr()` fornisce segmenti come una stringa o un array. Ecco un esempio di stringa:

```php
// Dato il seguente script:
echo baseUrl('segmento2/segmento2/123')
// Il risultato sarà: http://example.com/segmento2/123
```



Questo è utile perché `baseUrl()` a differenza di `siteUrl()` può fornire una stringa diretta, che punti ad un file, un'immagine o un foglio di stile.

```php
// Dato il seguente script:
echo  baseUrl("assets/images/edit.png");
// Il risultato sarà: http://example.com/assets/images/edit.png
```



------



`currentUrl()`

| Settaggi            | Descrizione             |
| ------------------- | ----------------------- |
| **Parametri**       | Nessuno                 |
| **Ritorno**         | Ritorna la url corrente |
| **Tipo di ritorno** | String                  |

La funzione `currentUrl()` restituisce la url corrente senza la query string associata, Esempio:

```php
// Da la seguente URL: http://www.example.com/segmento1/segmentodue.html?id=1&tag=tags

echo currentUrl();
// La funziona ritorna: http://www.example.com/segmento1/segmentodue.html
```



------



`currentQueryStringUrl()`

| Settaggi            | Descrizione                                      |
| ------------------- | ------------------------------------------------ |
| **Parametri**       | Nessuno                                          |
| **Ritorno**         | Ritorna la url corrente compresa la query string |
| **Tipo di ritorno** | String                                           |

La funzione `currentQueryStringUrl()` restituisce la url corrente con la query string associata, Esempio:

```php
// Da la seguente URL: http://www.example.com/segmento1/segmentodue.html?id=1&tag=tags

echo currentQueryStringUrl();
// La funzione ritorna: http://www.example.com/segmento1/segmentodue.html?id=1&tag=tags
```



------



`is_cli()`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | Nessuno                                                      |
| **Ritorno**         | Ritorna **TRUE** se lo script corrente viene lanciato in CLI *(Command Line Interface)* **FALSE** in caso contrario |
| **Tipo di ritorno** | Bool                                                         |

Questa funzione controlla se  i valori PHP_SAPI STDIN sono definite o meno.

```php
// Lo snippet di codice controlla se lo script è stato lanciato da linea di comando o meno
if(is_cli()===true) { 
  
  echo "Script lanciato da linea di comando";
    
} else {
  
  echo "Script NON lanciato da linea di comando";
  
}
```



------



`show404($heading = null, $message = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$heading** *(string)*  – Frase intestazione errore<br />**$message** *(string)*  – Messaggio di errore |
| **Ritorno**         | Questa funzione visualizzerà un messaggio di errore 404.     |
| **Tipo di ritorno** | Vuoto                                                        |

Questa funzione di default viene lanciata dalla classe `Route` nel momento in cui la gestione delle rotte non trova un controller o una funzione anonima associata alla URL;
Se non viene settato nessun parametro di ingresso,  i valori di default saranno i seguenti:

`$heading` : *404 Page Not Found*
`$message` : *The page you requested was not found.

Il template **HTML** della funzione può essere personalizzato in `app/themes/nome_tema/errors/404.php`



------



`showError($heading = null, $message = null, $statusCode = 500)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$heading** *(string)*  – Frase intestazione errore<br />**$message** *(string)*  – Messaggio di errore<br />**$statusCode** *(int)* – Codice di stato della risposta HTTP |
| **Ritorno**         | Questa funzione visualizzerà un messaggio di errore 404.     |
| **Tipo di ritorno** | Vuoto                                                        |

Questa funzione manda in output un messaggio di errore con codice di stato nella risposta HTTP. Se non viene settato nessun parametro di ingresso,  i valori di default saranno i seguenti:

`$heading` : *404 Page Not Found*
`$message` : *The page you requested was not found.*
`$statusCode`: *500*

Il template **HTML** della funzione può essere personalizzato in `app/themes/nome_tema/errors/general_error.php`



------



`setStatusHeader()`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$code** ( *int* ) - Codice di stato della risposta HTTP <br />**$text** ( *stringa* ) - Un messaggio personalizzato da impostare con il codice di stato |
| **Ritorno**         | Vuoto                                                        |
| **Tipo di ritorno** | Vuoto                                                        |

Consente di impostare manualmente un'intestazione di stato del server. Esempio:

```php
// Imposta l'intestazione come: Non autorizzato
set_status_header(401); 

// Elenco completo del codice di stato supportato:

100 => 'Continue',
101 => 'Switching Protocols',
200 => 'OK',
201 => 'Created',
202 => 'Accepted',
203 => 'Non-Authoritative Information',
204 => 'No Content',
205 => 'Reset Content',
206 => 'Partial Content',
300 => 'Multiple Choices',
301 => 'Moved Permanently',
302 => 'Found',
303 => 'See Other',
304 => 'Not Modified',
305 => 'Use Proxy',
307 => 'Temporary Redirect',
400 => 'Bad Request',
401 => 'Unauthorized',
402 => 'Payment Required',
403 => 'Forbidden',
404 => 'Not Found',
405 => 'Method Not Allowed',
406 => 'Not Acceptable',
407 => 'Proxy Authentication Required',
408 => 'Request Timeout',
409 => 'Conflict',
410 => 'Gone',
411 => 'Length Required',
412 => 'Precondition Failed',
413 => 'Request Entity Too Large',
414 => 'Request-URI Too Long',
415 => 'Unsupported Media Type',
416 => 'Requested Range Not Satisfiable',
417 => 'Expectation Failed',
422 => 'Unprocessable Entity',
426 => 'Upgrade Required',
428 => 'Precondition Required',
429 => 'Too Many Requests',
431 => 'Request Header Fields Too Large',
500 => 'Internal Server Error',
501 => 'Not Implemented',
502 => 'Bad Gateway',
503 => 'Service Unavailable',
504 => 'Gateway Timeout',
505 => 'HTTP Version Not Supported',
511 => 'Network Authentication Required',
```



------



`getMimes()`

| Settaggi            | Descrizione                          |
| ------------------- | ------------------------------------ |
| **Parametri**       | --                                   |
| **Ritorno**         | Un array associativo di tipi di file |
| **Tipo di ritorno** | Array                                |

Questa funzione restituisce un riferimento all'array MIME da application `app/Config/mimes.php`

```php
// Ritorna un array associatovo della lista dei MIME in app/Config/mimes.php.
print_r(&getMimes());
```



------



`function_usable($function_name)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$function_name** *( string )* - Nome della funzione        |
| **Ritorno**         | TRUE se la funzione può essere utilizzata, FALSE in caso contrario |
| **Tipo di ritorno** | Bool                                                         |

Restituisce TRUE se una funzione esiste ed è utilizzabile, FALSE in caso contrario.

È utile se si desidera verificare la disponibilità di funzioni come `eval()`e `exec()`, che sono pericolose e potrebbero essere disabilitate su server con criteri di sicurezza altamente restrittivi.

```php
// Esempio
if (function_usable('exec')) {
  // La funzione exec è utilizzabile;
}
```



------



`stringifyAttributes($attributes, $js = FALSE)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$attributes** ( *misto* ) -  stringa, array di coppie di valori chiave o oggetto<br />**$js** ( *bool* ) - TRUE se i valori non necessitano di virgolette (stile Javascript) |
| **Ritorno**         | Stringa contenente le coppie chiave / valore dell'attributo, separate da virgole |
| **Tipo di ritorno** | Mix                                                          |

Funzione di supporto utilizzata per convertire una stringa, un array o un oggetto di attributi in una stringa.

```php
// Esempio
echo stringifyAttributes([
  'class'=>'row',
  'id'=>'lg'
]);
// stampa: class="row" id="lg"
```



------



`__($key = null, $value = null, $file = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$key** *( string )* - Chiave dell'array che contiene il valore del testo di traduzione<br />**$value** *(string)* - Se non esiste una striga di traduzione viene chiamata questa variabile che contiene il testo della tradizione<br />**$file** *(string)* - include un file contente una matrice chiave/valore per la traduzione |
| **Ritorno**         | Una stringa contente il testo della traduzione.              |
| **Tipo di ritorno** | string                                                       |

La funzione è adibita per la traduzione del multilingua da File System. Tutti i file sorgenti della traduzione sono allocati in `app/Langs/` 
Il caricamento di un testo traduzione viene  creato in un array nella cartella sopra indicata, sotto forma di chiave/valore, dove la chiave è l'indice da chiamare nella funzione ed il valore è la stringa di traduzione che restituisce la funzione.
Per specificare una lingua di default da caricare, nel file di configurazione principale, ovvero `app/Config/app.php` nella chiave **"language"** della matrice, si dichiare il nome della lingua che in generale è la cartella dove sono allocati  tutti i file di traduzione.

Algoritmo del metodo di caricamento dei file di traduzione:

```php
/**
Struttura delle cartelle dei files di traduzione in 'app/Langs'

[omissis]
app/
./Langs
../it
.../langs.php
../en
.../langs.php
[omissis]
*/

// se nell'array di configurazione del file 'app/Config/app.php' il valore della chiave "language" è settato ad una stringa contente la parola 'it':
[
  [omissis]
  'language' => 'it',
  [omissis]
]

// La funzione carica i files che sono in 'app/Langs/it/*.php'


```



Esempio utilizzo della funzione `__()`

```php
// Caricamento di un file di testo di traduzione:

// Esempio di default

/* 
Se nalla funzione non viene specificato come terzo parametro, un file da caricare allocato nella cartela /app/Langs/it/, quest'ultima includerà di default langs.php
*/
<?php echo __('upload_userfile_not_set'); ?>
/*
La funzione stampa il valore associato alla chiave 'upload_userfile_not_set', ovvero: "Impossibile trovare nel post una variabile chiamata userfile"
*/

  
// Esempio con ritorno una stringa di default:
  
/*
Se vogliamo caricare un testo di default qualora la la chiave non esite, nel file di traduzione passiamo nel secondo parametro della funzione la stringa
*/
<?php echo __('index_not_valued','Testo traduzione di default'); ?>
/*
La funzione stampa "Testo traduzione di default" poiche la chiave 'index_not_valued' nella matrice del file di traduzione non esiste.
*/

  
  
// Esempio di caricamento di un file di traduzione:
/*
Se vogliamo caricare un file traduttore allocato nel File System in:
'/app/Langs/it/custom.php', come terzo parametro indichiamo il file da includere:
*/
  
<?php echo __('label_form_input', null, 'custom'); ?>
/*
La funzione stampa il valore della chiave label_form_input.
*/

```



------



`isReallyWritable($file)`

| Settaggi            | Descrizione                                               |
| ------------------- | --------------------------------------------------------- |
| **Parametri**       | **$file** *( string )* - Percorso del file                |
| **Ritorno**         | TRUE se il percorso è scrivibile, FALSE in caso contrario |
| **Tipo di ritorno** | bool                                                      |

La funzione restituisce TRUE sui server Windows quando non è possibile scrivere nel file poiché il sistema operativo segnala a PHP come FALSE solo se l'attributo di sola lettura è contrassegnato.

Questa funzione determina se un file è effettivamente scrivibile tentando prima di scriverlo. Generalmente consigliato solo su piattaforme in cui queste informazioni potrebbero essere inaffidabili.

```php
if(isReallyWritable('file.txt')) {
  
  echo  "Si, il file è scrivibile" ; 

} else {
  
  echo  "No, il file non è scrivibile" ; 

}
```



`__($key = null, $value = null, $file = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$key** *( string )* -  Chiama una chiave dell'array traduzione <br />**$value** *(string*) - Se non trova la chiave dell'array traduzione ritorna il valore impostato<br />**$file** *(string)* - carica un file traduttore |
| **Ritorno**         | Se il parametro viene settato valorizza i valori di invio email altrimenti setta i valori di default. |
| **Tipo di ritorno** | Array                                                        |

I parametri per stabilire la connessione per l'invio di una email, sono scritti fisicamenti in `app/Config/email.php`. 
Questi parametri sono formattati sotto forma array multidimensionale. Per caricare i parametri di un provider si invoca la chiave dell'array di primo livello che chiamerà successivamente la lita degli array associati a quest'ultimo.

Esempio: 

```php
/*
Parametri dei providers di servizio per l'invio della e-mail
in: app/Config/email.php;
*/
return [

    /**
     * Configurazione invio Email di "default"
     */
    'default' => [
      'protocol' => 'sendmail',
      'mailpath' => '/usr/sbin/sendmail',
      'charset'  => 'iso-8859-1',
      'wordwrap' => TRUE,
		]
  
     /**
     * Configurazione invio Email "provider_custom"
     */
		'provider_custom' => [
      'smtp_host' => 'ssl://smtp.googlemail.com',
      'smtp_user' => 'mario.rossi@gmail.com',
      'smtp_pass' => 'password123',
      'smtp_port' => 465,
      'smtp_timeout' => 5,
      'crlf' => '\r\n',
  	]
];

/*
Se tu vuoi caricare il provider "provider_custom" e non quello di default per l'invio della email, semplicemnete invochiamo la funzione loadConfigMail() ed impostiamo come parametro il provider 'provider_custom' come valore di ingresso.
VEDI ESEMPIO SEGUENTE:
*/

// paramento di ingresso: 'provider_custom'
$configs = loadConfigMail('provider_custom');

/*
Dico alla classe di usare il provider 'provider_custom' inizializzato nella variabile $config
*/
$email = new \System\Email($configs);

$email->from($configs['smtp_user']);
$email->to('example@example.com');
$email->subject('Invio Messaggio Framework');
$email->set_alt_message('Messaggio alternativo testo semplice');
$email->message('Messaggio in formato html');
$send = $email->send();

if (!$send) {

  echo $email->print_debugger();

} else {

  echo "email inviata con successo";

}

```





------



`loadConfigMail($provider = 'default')`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$provider** ( *string* ) : Configurazione del provider per l'invio di una email |
| **Ritorno**         | Se il parametro viene settato valorizza i valori di invio email altrimenti setta i valori di default. |
| **Tipo di ritorno** | Array                                                        |

I parametri per stabilire la connessione per l'invio di una email, sono scritti fisicamenti in `app/Config/email.php`. 
Questi parametri sono formattati sotto forma array multidimensionale. Per caricare i parametri di un provider si invoca la chiave dell'array di primo livello che chiamerà successivamente la lista degli array associati a quest'ultimo.

Esempio: 

```php
/*
Parametri dei providers di servizio per l'invio della e-mail
in: app/Config/email.php;
*/
return [

    /**
     * Configurazione invio Email di "default"
     */
    'default' => [
      'protocol' => 'sendmail',
      'mailpath' => '/usr/sbin/sendmail',
      'charset'  => 'iso-8859-1',
      'wordwrap' => TRUE,
		]
  
     /**
     * Configurazione invio Email "provider_custom"
     */
		'provider_custom' => [
      'smtp_host' => 'ssl://smtp.googlemail.com',
      'smtp_user' => 'mario.rossi@gmail.com',
      'smtp_pass' => 'password123',
      'smtp_port' => 465,
      'smtp_timeout' => 5,
      'crlf' => '\r\n',
  	]
];

/*
Se tu vuoi caricare il provider "provider_custom" e non quello di default per l'invio della email, semplicemnete invochiamo la funzione loadConfigMail() ed impostiamo come parametro il provider 'provider_custom' come valore di ingresso.
VEDI ESEMPIO SEGUENTE:
*/

// paramento di ingresso: 'provider_custom'
$configs = loadConfigMail('provider_custom');

/*
Dico alla classe di usare il provider 'provider_custom' inizializzati nella variabile $config
*/
$email = new \System\Email($configs);

$email->from($configs['smtp_user']);
$email->to('example@example.com');
$email->subject('Invio Messaggio Framework');
$email->set_alt_message('Messaggio alternativo testo semplice');
$email->message('Messaggio in formato html');
$send = $email->send();

if (!$send) {

  echo $email->print_debugger();

} else {

  echo "email inviata con successo";

}

```



------



`config($key = null, $default = null, $file = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$key** (*string*) - la chiave che contiene il valore dell'array  di configurazione;<br />**$default** (*string*)  - Se il valore non esiste ne assegna uno;<br />**$file** (*string*) - il file da includere nella cartella `app/Config/name_file.php`. |
| **Ritorno**         | una stringa con il valore di un indice della matrice nel file di configurazione. |
| **Tipo di ritorno** | Mix                                                          |

Questa funzione ha lo scopo di caricare una chiave contenente il valore in un file di configurazione.

```php
/*

*/

```



------



`render($layout = '', $data = [], $theme = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$layout ** (*string*) - il path del layout da renderizzare;<br />**$data** (*array*)  - Array con i dati da visualizzare nel layout ;<br />**$theme** (*string*) - il tema da utilizzare. |
| **Ritorno**         | una stringa con il valore di un indice della matrice nel file di configurazione. |
| **Tipo di ritorno** | Mix                                                          |

Questa funzione permette di renderizzare il layout specificato nel parametro `$layout` con i dati passati nel parametro `$data`.
