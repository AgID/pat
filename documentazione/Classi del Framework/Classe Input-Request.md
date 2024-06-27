# Classe di input

La classe di input ha due scopi:

- Pre-elaborare i dati di input globali per la sicurezza. 
- Fornisce alcuni metodi di supporto per recuperare i dati di input e pre-elaborarli.



#### Filtri di sicurezza

- Se nelle configurazioni in 'app/Config/app.php' l'indice  *'allow_get_array'* è FALSE (il valore predefinito è TRUE), distrugge l'array GET globale.
- Distrugge tutte le variabili globali nell'evento register_globals è attivato.
- Filtra le chiavi degli array GET / POST / COOKIE, consentendo solo caratteri alfanumerici (e pochi altri).
- Fornisce filtri XSS (Cross-site Scripting Hacks). 
- Standardizza i caratteri di nuova riga in `PHP_EOL`(**\n** nei sistemi operativi basati su UNIX, **\r \n** in Windows). Questo è configurabile.



#### Filtro XSS

La classe "Input" ha la capacità di filtrare l'input  per prevenire attacchi di scripting tra siti.
Per avviare il sistema di controllo su possibili attacchi XSS (Cross-site Scripting Hacks):  La classe Input  fa riferimento alla classe Security .



#### Accesso ai dati del modulo

**Utilizzo dei dati POST, GET, COOKIE o SERVER**

Il Framework viene fornito con metodi di supporto che ti consentono di recuperare elementi POST, GET, COOKIE, FILES o SERVER. Il vantaggio principale dell'utilizzo dei metodi forniti dalla classe, piuttosto che il recupero di un elemento direttamente ( `$_POST['field']`), è che i metodi verificheranno se l'elemento è impostato e restituiranno NULL in caso contrario. Ciò consente di utilizzare comodamente i dati senza dover prima verificare se un elemento esiste. In altre parole, normalmente potresti fare qualcosa del genere:

```php
// Script nativo in PHP per recuperare un certo valore dal modulo:
$value = isset($_POST['value']) ? $_POST['value'] : null;
```

Con i metodi integrati nel Framework puoi semplicemente fare questo:

```php
# Metodo nel FRAMEWORK per recuperare un certo valore dal modulo:

// Chiamata con il metodo assoluto della classe senza l'ausilio della direttava 'use'
$value = \System\Input::post('value');

// Oppure usando la direttiova 'use'
use \System\Input;
$value = Input::post('value');
```



**I metodi principali e quelli più utilizzati sono :**

- `\System\Input::post()`

- `\System\Input::get()`

- `\System\Input::cookie()`

- `\System\Input::server()`

  

**Utilizzando il flusso di  `php://input` stream**

Se si desidera utilizzare i metodi PUT, DELETE, PATCH  o altri metodi di richiesta, è possibile accedervi solo tramite uno speciale flusso di **input**, che può essere letto solo una volta. Non è così facile come leggere, ad esempio `$_POST`, dall'array, perché esisterà sempre e puoi provare ad accedere a più variabili senza preoccuparti di avere solo una possibilità per tutti i dati POST. Puoi accedere ai suoi valori chiamando il metodo stream **stream()**:

```php
// php://input (Stream)
\System\Input::stream();
```

Simile ad altri metodi come **get()** e **post()**, se i dati richiesti non vengono trovati, restituirà NULL. Puoi anche decidere se eseguire i dati **xssClean()** passando un valore booleano come secondo parametro:

```php
// php://input (Stream)
\System\Input::stream('key',true); // Sanificazione XSS Clean
\System\Input::stream('key',false); // Non sanificato
```

***NOTA: Puoi utilizzare `method()`per sapere se stai leggendo i dati PUT, DELETE o PATCH.**



#### Riferimenti della Classe:

- **\System\Input::post()**

```php
\System\Input::post([$index=null[,$xssClean=false[,$sanitizeKey=true,[,$sanitizeData=true]]]])
```

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$index** ( *mix* ) - nome del parametro POST <br />**$xssClean** ( *bool* ) - Indica se applicare il filtro XSS<br />**$sanitizeKey** ( *bool* ) - Sanifica la chiave del POST<br />**$sanitizeData** ( *bool* ) - Sanifica il valore del POST |
| **Ritorno**         | ritorna tutti i *$ _POST* se non vengono forniti parametri, altrimenti il valore POST se trovato o NULL in caso contrario |
| **Tipo di ritorno** | mixed                                                        |

E' **fortemente consigliato** lasciare i valori `$sanitizeKey` e `$sanitizeData` a `true` per rafforzare la sicurezza dei dati prelevati dai moduli.



Il primo parametro conterrà il nome dell'elemento POST che stai cercando:

```php
// Il metodo restituisce NULL se l'elemento che si sta tentando di recuperare non esiste.
\System\Input::post('some_data'); 
```



Il secondo parametro facoltativo consente di eseguire i dati attraverso il filtro XSS.

```php
// Il metodo restituisce i dati filtrati da possibili attacchi di tipo XSS.
\System\Input::post('some_data',true); 
```



Disabilitare la sanificazione della chiave e valore del post

```php
// Se impostato il terzo e quarto parametro a FALSE, il sistema non applicherà i filtri di sanificazione al valore del post (SCONSIGLIATO),
\System\Input::post('some_data', false, false, false); 
```



Per restituire un array di tutti gli elementi POST, chiamare senza parametri.

```php
// Restituisce tutti i $_POST:
\System\Input::post(); 
```



Per restituire tutti gli elementi POST e passarli attraverso il filtro XSS impostare il primo parametro NULL mentre si imposta il secondo parametro su booleano TRUE.

```php
// Restituisce tutti i $_POST:
\System\Input::post(null, true); // Restituisce tutti gli elementi con filtro XSS
\System\Input::post(null, false); // Restituisce tutti gli elementi senza filtro XSS
```



Per restituire un array di più parametri di tipo `$_POST`, passare tutte le chiavi richieste come array.

```php
// Restituisce tutti i $_POST sottoforma di array con chiave 'some_data' , 'some_data_1' e 'some_data_2':
\System\Input::post( ['some_data','some_data_1','some_data_2'] );
```



Stessa regola applicata qui, per recuperare i parametri con il filtro XSS abilitato, impostare il secondo parametro su booleano TRUE.

```php
// Restituisce tutti i $_POST sottoforma di array con chiave 'some_data' , 'some_data_1' e 'some_data_2'con filtro XSS attivo:
\System\Input::post( ['some_data','some_data_1','some_data_2'], true );
```



------

- **\System\Input::get()**

```php
\System\Input::get([$index=null[,$xssClean=false[,$sanitizeKey=true,[,$sanitizeData=true]]]])
```

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$index** ( *mix* ) - nome del parametro GET <br />**$xssClean** ( *bool* ) - Indica se applicare il filtro XSS<br />**$sanitizeKey** ( *bool* ) - Sanifica la chiave del GET<br />**$sanitizeData** ( *bool* ) - Sanifica il valore del GET |
| **Ritorno**         | ritorna tutti i *$ _GET* se non vengono forniti parametri, altrimenti il valore GET se trovato o NULL in caso contrario |
| **Tipo di ritorno** | mixed                                                        |

E' **fortemente consigliato** lasciare i valori `$sanitizeKey` e `$sanitizeData` a `true` per rafforzare la sicurezza dei dati prelevati dai moduli.



Algoritmo del flusso del metodo **get()** è identico al metodo **post()**

Il primo parametro conterrà il nome dell'elemento GET che stai cercando:

```php
// Il metodo restituisce NULL se l'elemento che si sta tentando di recuperare non esiste.
\System\Input::get('some_data'); 
```



Il secondo parametro facoltativo consente di eseguire i dati attraverso il filtro XSS.

```php
// Il metodo restituisce i dati filtrati da possibili attacchi di tipo XSS.
\System\Input::get('some_data',true); 
```



Disabilitare la sanificazione della chiave e valore del get

```php
// Se impostato il terzo e quarto parametro a FALSE, il sistema non applicherà i filtri di sanificazione al valore del get (SCONSIGLIATO),
\System\Input::get('some_data', false, false, false); 
```



Per restituire un array di tutti gli elementi GET, chiamare senza parametri.

```php
// Restituisce tutti i $_GET:
\System\Input::get(); 
```



Per restituire tutti gli elementi GET e passarli attraverso il filtro XSS impostare il primo parametro NULL mentre si imposta il secondo parametro su booleano TRUE.

```php
// Restituisce tutti i $_GET:
\System\Input::get(null, true); // Restituisce tutti gli elementi con filtro XSS
\System\Input::get(null, false); // Restituisce tutti gli elementi senza filtro XSS
```



Per restituire un array di più parametri di tipo `$_GET`, passare tutte le chiavi richieste come array.

```php
// Restituisce tutti i $_GET sottoforma di array con chiave 'some_data' , 'some_data_1' e 'some_data_2':
\System\Input::get( ['some_data','some_data_1','some_data_2'] );
```



Stessa regola applicata qui, per recuperare i parametri con il filtro XSS abilitato, impostare il secondo parametro su booleano TRUE.

```php
// Restituisce tutti i $_GET sottoforma di array con chiave 'some_data' , 'some_data_1' e 'some_data_2'con filtro XSS attivo:
\System\Input::get( ['some_data','some_data_1','some_data_2'], true );
```



------

- **\System\Input::postGet()**

```php
\System\Input::postGet([$index=null[,$xssClean=false[,$sanitizeKey=true,[,$sanitizeData=true]]]])
```

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$index** ( *mix* ) - Nome del parametro POST / GET <br />**$xssClean** ( *bool* ) - Indica se applicare il filtro XSS<br />**$sanitizeKey** ( *bool* ) - Sanifica la chiave del POST / GET<br />**$sanitizeData** ( *bool* ) - Sanifica il valore del POST / GET |
| **Ritorno**         | Ritorna il valore POST / GET se trovato, NULL in caso contrario. |
| **Tipo di ritorno** | mixed                                                        |

E' **fortemente consigliato** lasciare i valori `$sanitizeKey` e `$sanitizeData` a `true` per rafforzare la sicurezza dei dati prelevati dai moduli.

**Questo metodo funziona più o meno allo stesso modo di `post()`e `get()`, solo combinato. Cercherà i dati nei flussi POST e GET, cercando prima in POST e poi in GET:**



Il primo parametro conterrà il nome dell'elemento `$_POST / $_GET` che stai cercando:

```php
// Il metodo restituisce NULL se l'elemento che si sta tentando di recuperare non esiste.
\System\Input::postGet('some_data'); 
```



Il secondo parametro facoltativo consente di eseguire i dati attraverso il filtro XSS.

```php
// Il metodo restituisce i dati filtrati da possibili attacchi di tipo XSS.
\System\Input::postGet('some_data',true); 
```



Disabilitare la sanificazione della chiave e valore del `$_POST / $_GET`

```php
// Se impostato il terzo e quarto parametro a FALSE, il sistema non applicherà i filtri di sanificazione al valore del $_POST e $_GET (SCONSIGLIATO),
\System\Input::postGet('some_data', false, false, false); 
```



Per restituire un array di tutti gli elementi `$_POST / $_GET`, chiamare senza parametri.

```php
// Restituisce tutti i dati prima $_POST e poi $_GET:
\System\Input::postGet(); 
```



Per restituire tutti gli elementi `$_POST / $_GET` e passarli attraverso il filtro XSS impostare il primo parametro NULL mentre si imposta il secondo parametro su booleano TRUE.

```php
// Restituisce tutti i dati prima $_POST e poi $_GET:
\System\Input::postGet(null, true); // Restituisce tutti gli elementi con filtro XSS
\System\Input::postGet(null, false); // Restituisce tutti gli elementi senza filtro XSS
```



Per restituire un array di più parametri di tipo`$_POST / $_GET`, passare tutte le chiavi richieste come array.

```php
// Restituisce tutti i dati prima $_POST e poi $_GET sottoforma di array con chiave 'some_data', 'some_data_1' e 'some_data_2':
\System\Input::postGet( ['some_data','some_data_1','some_data_2'] );
```



Stessa regola applicata qui, per recuperare i parametri con il filtro XSS abilitato, impostare il secondo parametro su booleano TRUE.

```php
// Restituisce tutti i dati prima $_POST e poi $_GET sottoforma di array con chiave 'some_data', 'some_data_1' e 'some_data_2'con filtro XSS attivo:
\System\Input::postGet( ['some_data','some_data_1','some_data_2'], true );
```



------



- **\System\Input::getPost()**

```php
\System\Input::getPost([$index=null[,$xssClean=false[,$sanitizeKey=true,[,$sanitizeData=true]]]])
```

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$index** ( *mix* ) - Nome del parametro GET / POST <br />**$xssClean** ( *bool* ) - Indica se applicare il filtro XSS<br />**$sanitizeKey** ( *bool* ) - Sanifica la chiave del GET / POST<br />**$sanitizeData** ( *bool* ) - Sanifica il valore del GET / POST |
| **Ritorno**         | Ritorna il valore GET / POST  se trovato, NULL in caso contrario. |
| **Tipo di ritorno** | mixed                                                        |

E' **fortemente consigliato** lasciare i valori `$sanitizeKey` e `$sanitizeData` a `true` per rafforzare la sicurezza dei dati prelevati dai moduli.

**Questo metodo funziona allo stesso modo di `postGet()` a differenza dell'ordine della ricerca dei valori.**
**Cercherà i dati nei flussi GET e POST, cercando prima in GET e poi in POST:**



Il primo parametro conterrà il nome dell'elemento ` $_GET / $_POST` che stai cercando:

```php
// Il metodo restituisce NULL se l'elemento che si sta tentando di recuperare non esiste.
\System\Input::getPost('some_data'); 
```



Il secondo parametro facoltativo consente di eseguire i dati attraverso il filtro XSS.

```php
// Il metodo restituisce i dati filtrati da possibili attacchi di tipo XSS.
\System\Input::getPost('some_data',true); 
```



Disabilitare la sanificazione della chiave e valore del `$_GET / $_POST`

```php
// Se impostato il terzo e quarto parametro a FALSE, il sistema non applicherà i filtri di sanificazione al valore del $_GET e poi $_POST (SCONSIGLIATO),
\System\Input::postGet('some_data', false, false, false); 
```



Per restituire un array di tutti gli elementi `$_GET / $_POST`, chiamare senza parametri.

```php
// Restituisce tutti i dati prima $_GET e poi $_POST:
\System\Input::getPost(); 
```



Per restituire tutti gli elementi `$_GET / $_POST` e passarli attraverso il filtro XSS impostare il primo parametro NULL mentre si imposta il secondo parametro su booleano TRUE.

```php
// Restituisce tutti i dati prima $_POST e poi $_GET:
\System\Input::getPost(null, true); // Restituisce tutti gli elementi filtro XSS
\System\Input::getPost(null, false); // Restituisce tutti gli elementi senza filtro XSS
```



Per restituire un array di più parametri di tipo`$_GET / $_POST`, passare tutte le chiavi richieste come array.

```php
// Restituisce tutti i dati prima $_GET e poi $_POST sottoforma di array con chiave 'some_data', 'some_data_1' e 'some_data_2':
\System\Input::getPost( ['some_data','some_data_1','some_data_2'] );
```



Stessa regola applicata qui, per recuperare i parametri con il filtro XSS abilitato, impostare il secondo parametro su booleano TRUE.

```php
// Restituisce tutti i dati prima $_GET e poi $_POST sottoforma di array con chiave 'some_data', 'some_data_1' e 'some_data_2'con filtro XSS attivo:
\System\Input::getPost( ['some_data','some_data_1','some_data_2'], true );
```



------



- **\System\Input::files()**

```php
\System\Input::files([$index=null[,$xssClean=false[,$sanitizeKey=true,[,$sanitizeData=true]]]])
```

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$index** ( *mix* ) - Nome del parametro FILES <br />**$xssClean** ( *bool* ) - Indica se applicare il filtro XSS<br />**$sanitizeKey** ( *bool* ) - Sanifica la chiave del FILES<br />**$sanitizeData** ( *bool* ) - Sanifica il valore del FILES |
| **Ritorno**         | Ritorna il valore $_FILES  se trovato, NULL in caso contrario. |
| **Tipo di ritorno** | mixed                                                        |

E' **fortemente consigliato** lasciare i valori `$sanitizeKey` e `$sanitizeData` a `true` per rafforzare la sicurezza dei dati prelevati dai moduli.

**Questo metodo funziona allo stesso modo di `postGet()` a differenza dell'ordine della ricerca dei valori.**

Il primo parametro conterrà il nome dell'elemento ` $_FILES` che stai cercando:

```php
// Il metodo restituisce NULL se l'elemento che si sta tentando di recuperare non esiste.
\System\Input::files('some_data'); 
```



Il secondo parametro facoltativo consente di eseguire i dati attraverso il filtro XSS.

```php
// Il metodo restituisce i dati filtrati da possibili attacchi di tipo XSS.
\System\Input::files('some_data',true); 
```



Disabilitare la sanificazione della chiave e valore del ` $_FILES`

```php
// Se impostato il terzo e quarto parametro a FALSE, il sistema non applicherà i filtri di sanificazione al valore del  $_FILES (SCONSIGLIATO),
\System\Input::files('some_data', false, false, false); 
```



Per restituire un array di tutti gli elementi ` $_FILES`, chiamare senza parametri.

```php
// Restituisce tutti i dati $_FILES:
\System\Input::files(); 
```



Per restituire tutti gli elementi ` $_FILES` e passarli attraverso il filtro XSS impostare il primo parametro NULL mentre si imposta il secondo parametro su booleano TRUE.

```php
// Restituisce tutti i dati di tipo $_FILES:
\System\Input::files(null, true); // Restituisce tutti gli elementi filtro XSS
\System\Input::files(null, false); // Restituisce tutti gli elementi senza filtro XSS
```



Per restituire un array di più parametri di tipo` $_FILES`, passare tutte le chiavi richieste come array.

```php
// Restituisce tutti i dati $_FILES sottoforma di array con chiave 'some_data', 'some_data_1' e 'some_data_2':
\System\Input::files( ['some_data','some_data_1','some_data_2'] );
```



Stessa regola applicata qui, per recuperare i parametri con il filtro XSS abilitato, impostare il secondo parametro su booleano TRUE.

```php
// Restituisce tutti i dati prima $_GET e poi $_POST sottoforma di array con chiave 'some_data', 'some_data_1' e 'some_data_2'con filtro XSS attivo:
\System\Input::files( ['some_data','some_data_1','some_data_2'], true );
```



------



- **\System\Input::cookie()**

```php
\System\Input::cookie([$index=null[,$xssClean=false[,$sanitizeKey=true,[,$sanitizeData=true]]]])
```

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$index** ( *mix* ) - Nome del parametro COOKIE <br />**$xssClean** ( *bool* ) - Indica se applicare il filtro XSS<br />**$sanitizeKey** ( *bool* ) - Sanifica la chiave del COOKIE<br />**$sanitizeData** ( *bool* ) - Sanifica il valore del COOKIE |
| **Ritorno**         | Ritorna tutti valori **$ _COOKIE** se nessun parametro fornito, altrimenti il valore COOKIE se trovato o NULL in caso contrario. |
| **Tipo di ritorno** | mixed                                                        |

E' **fortemente consigliato** lasciare i valori `$sanitizeKey` e `$sanitizeData` a `true` per rafforzare la sicurezza dei dati prelevati dai moduli.

**Questo metodo è identico a `post()`e `get()`, solo che recupera i dati dei cookie:**



Il primo parametro conterrà il nome dell'elemento ` $_COOKIE` che stai cercando:

```php
// Il metodo restituisce NULL se l'elemento che si sta tentando di recuperare non esiste.
\System\Input::cookie('some_data'); 
```



Il secondo parametro facoltativo consente di eseguire i dati attraverso il filtro XSS.

```php
// Il metodo restituisce i dati filtrati da possibili attacchi di tipo XSS.
\System\Input::cookie('some_data',true); 
```



Disabilitare la sanificazione della chiave e valore del ` $_COOKIE`

```php
// Se impostato il terzo e quarto parametro a FALSE, il sistema non applicherà i filtri di sanificazione al $_COOKIE (SCONSIGLIATO),
\System\Input::cookie('some_data', false, false, false); 
```



Per restituire un array di tutti gli elementi ` $_COOKIE`, chiamare senza parametri.

```php
// Restituisce tutti i $_COOKIE:
\System\Input::cookie(); 
```



Per restituire tutti gli elementi ` $_COOKIE`e passati attraverso il filtro XSS impostare il primo parametro NULL mentre si imposta il secondo parametro su booleano TRUE.

```php
// Restituisce tutti i $_COOKIE:
\System\Input::cookie(null, true); // Restituisce tutti gli elementi filtro XSS
\System\Input::cookie(null, false); // Restituisce tutti gli elementi senza filtro XSS
```



Per restituire un array di più parametri di tipo` $_COOKIE`, passare tutte le chiavi richieste come array.

```php
// Restituisce tutti i $_COOKIE sottoforma di array con chiave 'some_data', 'some_data_1' e 'some_data_2':
\System\Input::cookie( ['some_data','some_data_1','some_data_2'] );
```



Stessa regola applicata qui, per recuperare i parametri con il filtro XSS abilitato, impostare il secondo parametro su booleano TRUE.

```php
// Restituisce tutti i $_COOKIE sottoforma di array con chiave 'some_data', 'some_data_1' e 'some_data_2'con filtro XSS attivo:
\System\Input::cookie( ['some_data','some_data_1','some_data_2'], true );
```



------



- **\System\Input::server()**

```php
\System\Input::server([$index=null[,$xssClean=false[,$sanitizeKey=true,[,$sanitizeData=true]]]])
```

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$index** ( *mix* ) - Nome del parametro SERVER <br />**$xssClean** ( *bool* ) - Indica se applicare il filtro XSS<br />**$sanitizeKey** ( *bool* ) - Sanifica la chiave del SERVER<br />**$sanitizeData** ( *bool* ) - Sanifica il valore del SERVER |
| **Ritorno**         | Ritorna il valore **$ _SERVER** dell'elemento se trovato, NULL in caso contrario. |
| **Tipo di ritorno** | mixed                                                        |

E' **fortemente consigliato** lasciare i valori `$sanitizeKey` e `$sanitizeData` a `true` per rafforzare la sicurezza dei dati prelevati dai moduli.

**Questo metodo è identico ai metodi `post()`, `get()`e `cookie()` , solo recupera i dati del server ( `$_SERVER`):**



Il primo parametro conterrà il nome dell'elemento `$_SERVER` che stai cercando:

```php
// Il metodo restituisce NULL se l'elemento che si sta tentando di recuperare non esiste.
\System\Input::server('some_data'); 
```



Il secondo parametro facoltativo consente di eseguire i dati attraverso il filtro XSS.

```php
// Il metodo restituisce i dati filtrati da possibili attacchi di tipo XSS.
\System\Input::server('some_data',true); 
```



Disabilitare la sanificazione della chiave e valore del `$_SERVER`

```php
// Se impostato il terzo e quarto parametro a FALSE, il sistema non applicherà i filtri di sanificazione al $_SERVER (SCONSIGLIATO),
\System\Input::server('some_data', false, false, false); 
```



Per restituire un array di tutti gli elementi `$_SERVER`, chiamare senza parametri.

```php
// Restituisce tutti i $_SERVER:
\System\Input::server(); 
```



Per restituire tutti gli elementi `$_SERVER` passati attraverso il filtro XSS, impostare il primo parametro NULL mentre si imposta il secondo parametro su booleano TRUE.

```php
// Restituisce tutti i $_SERVER:
\System\Input::server(null, true); // Restituisce tutti gli elementi filtro XSS
\System\Input::server(null, false); // Restituisce tutti gli elementi senza filtro XSS
```



Per restituire un array di più parametri di tipo`$_SERVER`, passare tutte le chiavi richieste come array.

```php
// Restituisce tutti i $_SERVER sottoforma di array con chiave 'some_data', 'some_data_1' e 'some_data_2':
\System\Input::server( ['some_data','some_data_1','some_data_2'] );
```



Stessa regola applicata qui, per recuperare i parametri con il filtro XSS abilitato, impostare il secondo parametro su booleano TRUE.

```php
// Restituisce tutti i $_SERVER sottoforma di array con chiave 'some_data', 'some_data_1' e 'some_data_2'con filtro XSS attivo:
\System\Input::server( ['some_data','some_data_1','some_data_2'], true );
```



------



- **\System\Input::stream()**

```php
\System\Input::stream([$index=null[,$xssClean=false[,$sanitizeKey=true,[,$sanitizeData=true]]]])
```

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$index** ( *mix* ) - Nome del parametro php://input <br />**$xssClean** ( *bool* ) - Indica se applicare il filtro XSS<br />**$sanitizeKey** ( *bool* ) - Sanifica la chiave del php://input<br />**$sanitizeData** ( *bool* ) - Sanifica il valore del php://input |
| **Ritorno**         | Ritorna il valore **php://input** dell'elemento se trovato, NULL in caso contrario. |
| **Tipo di ritorno** | mixed                                                        |

E' **fortemente consigliato** lasciare i valori `$sanitizeKey` e `$sanitizeData` a `true` per rafforzare la sicurezza dei dati prelevati dai moduli.

**Questo metodo è identico ai metodi `post()`, `get()`e `cookie()` , solo recupera i dati del STREAM ( ` php://input`):**



Il primo parametro conterrà il nome dell'elemento ` php://input` che stai cercando:

```php
// Il metodo restituisce NULL se l'elemento che si sta tentando di recuperare non esiste.
\System\Input::stream('some_data'); 
```



Il secondo parametro facoltativo consente di eseguire i dati attraverso il filtro XSS.

```php
// Il metodo restituisce i dati filtrati da possibili attacchi di tipo XSS.
\System\Input::stream('some_data',true); 
```



Disabilitare la sanificazione della chiave e valore del ` php://input`

```php
// Se impostato il terzo e quarto parametro a FALSE, il sistema non applicherà i filtri di sanificazione al php://input (SCONSIGLIATO),
\System\Input::stream('some_data', false, false, false); 
```



Per restituire un array di tutti gli elementi ` php://input`, chiamare senza parametri.

```php
// Restituisce tutti gli php://input:
\System\Input::stream(); 
```



Per restituire tutti gli elementi ` php://input` passati attraverso il filtro XSS, impostare il primo parametro NULL mentre si imposta il secondo parametro su booleano TRUE.

```php
// Restituisce tutti gli php://input:
\System\Input::stream(null, true); // Restituisce tutti gli elementi filtro XSS
\System\Input::stream(null, false); // Restituisce tutti gli elementi senza filtro XSS
```



Per restituire un array di più parametri di tipo `php://input`, passare tutte le chiavi richieste come array.

```php
// Restituisce tutti gli php://input sottoforma di array con chiave 'some_data', 'some_data_1' e 'some_data_2':
\System\Input::stream( ['some_data','some_data_1','some_data_2'] );
```



Stessa regola applicata qui, per recuperare i parametri con il filtro XSS abilitato, impostare il secondo parametro su booleano TRUE.

```php
// Restituisce tutti gli php://input sottoforma di array con chiave 'some_data', 'some_data_1' e 'some_data_2'con filtro XSS attivo:
\System\Input::stream( ['some_data','some_data_1','some_data_2'], true );
```



------



- **\System\Input::requestHeaders()**

```php
\System\Input::requestHeaders([$xssClean=false[,$sanitizeKey=true,[,$sanitizeData=true]]])
```

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$xssClean** ( *bool* ) - Indica se applicare il filtro XSS<br />**$sanitizeKey** ( *bool* ) - Sanifica la chiave del php://input<br />**$sanitizeData** ( *bool* ) - Sanifica il valore del php://input |
| **Ritorno**         | Ritorna Un array di intestazioni delle richieste HTTP.       |
| **Tipo di ritorno** | Array                                                        |

E' **fortemente consigliato** lasciare i valori `$sanitizeKey` e `$sanitizeData` a `true` per rafforzare la sicurezza dei dati prelevati dai moduli.

**Restituisce un array di intestazioni di richiesta HTTP. Utile se si esegue in un ambiente non Apache in cui apache_request_headers () non sarà supportato.**

```php
// Il metodo restituisce NULL se l'elemento che si sta tentando di recuperare non esiste.
\System\Input::requestHeaders('some_data'); 
```



------



- **\System\Input::requestHeaders()**

```php
\System\Input::getRequestHeader([$index=null[,$xssClean=false]])
```

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$index** ( *mix* ) - Nome dell'intestazione della richiesta HTTP <br />**$xssClean** ( *bool* ) - Indica se applicare il filtro XSS |
| **Ritorno**         | Un'intestazione della richiesta HTTP o NULL se non trovata   |
| **Tipo di ritorno** | string                                                       |

**Restituisce un singolo indice del vettore (array) delle intestazioni della richiesta o NULL se l'intestazione cercata non viene trovata.**

```php
// Il metodo restituisce NULL se l'elemento che si sta tentando di recuperare non esiste.
\System\Input::getRequestHeader('some_data'); 
```



------



- **\System\Input::isAjax()**

```php
\System\Input::isAjax()
```

| Settaggi            | Descrizione                                           |
| ------------------- | ----------------------------------------------------- |
| **Ritorno**         | VERO se è una richiesta Ajax, FALSO in caso contrario |
| **Tipo di ritorno** | bool                                                  |

**Controlla se l'intestazione del server HTTP_X_REQUESTED_WITH è stata impostata e restituisce il valore booleano TRUE se lo è o FALSE in caso contrario.**

```php
// Il metodo restituisce TRUE se la richiesta è di tipo ajax oppure FALSE
\System\Input::isAjax()
```



------



- **\System\Input::userAgent()**

```php
\System\Input::userAgent([$xssClean = FALSE])
```

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$xss_clean** ( *bool* ) - Indica se applicare il filtro XSS |
| **Ritorno**         | Stringa agente utente o NULL se non impostato                |
| **Tipo di ritorno** | Mix                                                          |

**Restituisce la stringa dell'agente utente (browser web) utilizzata dall'utente corrente o NULL se non è disponibile.**

```php
// Stampa la stringa del client User Agent
echo \System\Input::userAgent();
// Esempio: Mozilla/5.0 (Macintosh; Intel Mac OS X 10_15_4) AppleWebKit/537.36 (KHTML, like Gecko) Chrome/84.0.4147.135 Safari/537.36
```

------



- **\System\Input::ipAddress()**

```php
\System\Input::ipAddress()
```

| Settaggi            | Descrizione                                           |
| ------------------- | ----------------------------------------------------- |
| **Ritorno**         | Indirizzo IP del visitatore o "0.0.0.0" se non valido |
| **Ritorno**         | mixed                                                 |
| **Tipo di ritorno** | String                                                |

**Restituisce il `$_SERVER['REQUEST_METHOD']`, con l'opzione di impostarlo in maiuscolo o minuscolo..**

```php
echo \System\Input::ipAddress(); // Ritorna l'indirizzo IP
```



------



- **\System\Input::method()**

```php
\System\Input::method([$upper = FALSE])
```

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$upper** ( *bool* ) - Indica se restituire il nome del metodo di richiesta in lettere maiuscole o minuscole |
| **Ritorno**         | Metodo di richiesta HTTP                                     |
| **Tipo di ritorno** | string                                                       |

**Restituisce il `$_SERVER['REQUEST_METHOD']`, con l'opzione di impostarlo in maiuscolo o minuscolo..**

```php
echo \System\Input::method(TRUE); // Outputs: POST
echo \System\Input::method(FALSE); // Outputs: post
echo \System\Input::method(); // Outputs: post
```

