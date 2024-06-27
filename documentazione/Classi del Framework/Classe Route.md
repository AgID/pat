# Classe per Route per definire la navigazione 

**Riferimento path sorgente classe upload:** *core/System/Route.php*

Nel Framework, si possono definire le “web routes” nel file “app/Routes/web.php”. Le “web routes” corrispondono alle pagine che saranno visitate dagli utenti. E' possibile definire anche un sistema di rotte "Api routes" per applicazione di tipo RESTFul nel file  “app/Routes/Api.php”.

**Lista dei metodi:**

- `Route::get($uri, $callback);`

- `Route::post($uri, $callback);`

- `Route::any($uri, $callback);`

- `Route::patch($uri, $callback);`

- `Route::put($uri, $callback);`

- `Route::ajax($uri, $callback);`

- `Route::options($uri, $callback);`

- `Route::prefix($uri, $callback);`

- `Route::middleware($uri, $callback);`

- `Route::slug($uri, $callback);` ***(in sviluppo)***

  

Le rotte  più elementari accettano un URI come primo parametro ed una funzione anonima `Closure`, fornendo un metodo molto semplice ed espressivo per definire le rotte:

```php
use \System\Route;

Route::get('/', function () {
    echo 'Hello World';
});
```

Il primo parametro `'/'` indica la  **URL dell'applicazione web** in questo caso la home, il secondo parametro, ovvero la funzione anonima `Closure` indica la destinazione da chiamare. In questo esempio  la url intercettata  dell'applicazione,  tramite la `Closure` passata nel secondo paramertro stamperà a video la frase  *'Hello World'*.

Nella gestione delle rotte oltre alla funzioni anonime è possibile invocare anche un controller. Vedi Esempio successivo.

```php
use \System\Route;

Route::get('/', '\Http\HomeController@index');
```

Come nell'esempio precedente, il primo parametro indica **l'URL dell'applicazione web** da intercettare, mentre il secondo parametro è una istanza di un Controller ed il metodo associato allo stesso. Nello specifico per la definizione di un controller tutta la stringa che si trova alla sinistra della @, e l'invocazione della classe di tipo controller *"**\Http\HomeController**@index"* mentre la stringa che si trova a destra della chiocciola è il nome del metodo da invocare nel controller *"\Http\HomeController@**index**"*.

------

#### Placeholder

Nelle rotte è possibile parametrizzare  valori dinamici nella definizione nelle **URL**. 
Ad esempio per il recupero di un valore nel database è possibile indicare in questo modo i valori dinamici nei segmenti delle url:

```php
use \System\Route;

// URL: www.example.com/post/126.html
// Il segmento 2 ha come valore 126 ed è la chiave primaria della tabella news del Database 
// da recuperare per la lettura di una news.

Route::get('/post/:num', '\Http\NewsController@read');

// Nel placeholder ":num" si indica al routing che è un valore dinamico di tipo numerico.
// Se nel placeholder ':num' nel segmento 2 viene passato un'altro valore non numerico
// Il sistema stamperà una pagina di errore.

```

Nell'esempio indicato il secondo parametro dichiarato come un un valore dinamico di tipo numerico.



I placeholder sono stringhe che rappresentano un modello di espressione regolare. 
Durante il processo di instradamento, questi segnaposto vengono sostituiti con il valore dell'espressione regolare.
Ecco la lista dei placeholder che si possono impostare nelle rotte:

| Placeholders     | Description                                                  |
| ---------------- | ------------------------------------------------------------ |
| ***:num***       | Nel segmento impostato, accetta solamente valori di tipo numerici |
| ***:any***       | Nel segmento impostato, accetta tutti i valori passati       |
| ***:alpha_num*** | Nel segmento impostato, accetta solamente valori alfanumerici. |
| ***:alpha***     | Nel segmento impostato, accetta solamente valori alfabetici. |





#### Riferimenti della classe.

Nel Framework è possibile filtrare le rotte in base ai metodi ***(METHODS)*** chiamati nell'applicazione web:

`Route::get($uri, $callback);`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$uri** *(string)* – PATH Url<br />**$callback** *(mix)* - Funzione anonima (Closure) o un'istanza di un Controller |
| **Ritorno**         | Callback del Closure (Funzione anonima) o istanza di un controller |
| **Tipo di ritorno** | Closure                                                      |

Nell'esempio seguente viene impostato nel **GET** una funziona anonima o un controller ad una URL nelle rotte.

```php
// Metodo: HTTP GET

// Closure: URL principale del dominio che chiama una funzione anonima.
Route::get('/', function(){
  echo "Homepage";
});

// Instance Controller: URL principale del dominio che istanzia un controller
Route::get('/', '\Http\HomeController');

```



------



`Route::post($uri, $callback);`

| Settari             | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$uri** *(string)* – PATH Url<br />**$callback** *(mix)* - Funzione anonima (Closure) o un'istanza di un Controller |
| **Ritorno**         | Callback del Closure (Funzione anonima) o istanza di un controller |
| **Tipo di ritorno** | Closure                                                      |

Nell'esempio seguente viene impostato nel **POST** una funziona anonima o un controller ad una URL nella rotta.

```php
// Metodo: HTTP POST

// Closure: URL www.example.com/post.html che chiama una funzione anonima.
Route::post('/post', function(){
  // 
  $DB = new \System\Database();
  $DB->table('users')->insert([
    'username' => \System\Input::post('username');
    'password' => \System\Input::post('password');
  ]);
});

// Instance Controller: callback che istanzia un controller
Route::post('/', '\Http\PostController@insert');

```



------



I metodi  **`Route::patch($uri, $callback)`, `Route::put($uri, $callback)`, `Route::ajax($uri, $callback)`, `Route::options($uri, $callback)`**  sono uguali ai due metodi descritti sopra, a differenza che ognuno di questi vengono istanziati in base al metodo invocato. 
Esempio:

- `Route::patch($uri, $callback)` ***HTTP PATCH***;
- `Route::put($uri, $callback)` ***HTTP PUT***;
- `Route::ajax($uri, $callback)` ***HTTP AJAX***;
- `Route::options($uri, $callback)` ***HTTP OPTIONS***;



------



`Route::any($uri, $callback);`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$uri** *(string)* – PATH Url<br />**$callback** *(mix)* - Funzione anonima (Closure) o un'istanza di un Controller |
| **Ritorno**         | Callback del Closure (Funzione anonima) o istanza di un controller |
| **Tipo di ritorno** | Closure                                                      |

```php
// Metodo: HTTP - POST oppure GET oppure HEAD oppure PUT oppure PATCH oppure DELETE oppure OPTIONS

// Closure: URL www.example.com/example.html, tutti i metodi vengono indirizzati in questa rotta
Route::any('/example', function(){
  echo "Hello word!";
});

// Instance Controller: callback che istanzia un controller
Route::post('/', '\Http\ExampleController@insert');

```

Tutti i metodi delle richieste intercettai nel primo parametro **'/example'** vengono indirizzati nel Closure o in un controller impostato nel secondo parametro.

------



`Route::prefix($uri, $callback);`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$uri** *(string)* – PATH Url<br />**$callback** *(mix)* - Funzione anonima (Closure) o un'istanza di un Controller |
| **Ritorno**         | Callback del Closure (Funzione anonima) o istanza di un controller |
| **Tipo di ritorno** | Closure                                                      |

Se alcune routes condividono un stesso prefisso, possono essere raggruppate in un **Route Prefix**, come nell'esempio successivo

```php
// Raggruppamento rotte con il metodo Prefix

// Gruppo con prefisso Admin
Route::prefix('/admin', function(){
  
  // www.example.com/admin/user.html
  Route::get('/user', function () {
    // Output
	});
  
  // www.example.com/admin/news.html
  Route::get('/news', function () {
    // Output
	});
  
  // www.example.com/admin/media.html
  Route::post('/media', function () {
		// Output
  });
  
});

// Gruppo con prefisso Admin
Route::prefix('/admin', function(){
  
  // www.example.com/admin/user.html
  Route::get('/user', '\Http\UserController@index');
  
  // www.example.com/admin/news.html
  Route::get('/news', '\Http\NewsController@index');
  
  // www.example.com/admin/media.html
  Route::post('/media', '\Http\MediaController@insert');
  
});

```



------



`Route::middleware($middleware, $callback);`

I **Middleware** sono un meccanismo che serve a filtrare programmaticamente le richieste HTTP che riceve l'applicazione.
In pratica, un middleware può essere visto come un layer che si interpone tra la richiesta e la risposta. Ad una Request possono essere associati più middleware.
Le classi che gestiscono i middleware si trovano all’interno della cartella **app/Middleware/**.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$middleware** *(string)* – Singolo o un gruppo di middleware<br />**$callback** *(mix)* - Funzione anonima (closure) o un'istanza di un Controller |
| **Ritorno**         | Callback del Closure (Funzione anonima) o istanza di un controller |
| **Tipo di ritorno** | Closure                                                      |

In questo esempio le rotte di tipo HTTP GET **'/media'** e **'/news'**, vengono filtrare dal Middleware **'\Middleware\Admin'**. Quest'ultimo  verificare se un utente è loggato o meno.
Se l'utente è loggato il sistema fa accedere alle due rotte, altrimenti stampa una pagina di errore mediante la funzione ***showError()***

```php
// Raggruppamento rotte con il metodo Prefix

// Gruppo di route con accesso solo ad utenti autenticati
Route::middleware('\Middleware\Admin', function () {

  // Rotta '/news'
  Route::get('/media', function () {
    echo "Media";
  });
  
	// Rotta '/news'
  Route::get('/news', function () {
    echo "News";
  });

});

// Esempio Middleware Admin in app/Middleware/Admin.php
<?php 
namespace Middleware;

use \System\Auth;

class Admin
{
  public function handle() {

    $auth = new Auth();

    if((bool)$auth->hasIdentity()===true) {
      retun true;
    }

    showError('Attenzione','Non hai i permessi per accedere a questa sezione');
  }
}

```



------



Nella gestione del routing è possibile istanziare più Middleware, concatenando le istanze dei middlewares con il carattere del **pipe "|".** Come nell'esempio successivo:

```php
// Raggruppamento rotte con il metodo Prefix

// Gruppo di route con accesso solo ad utenti autenticati
// Instanzio due middleware separaati dal carattere di pipe '|' 
Route::middleware('\Middleware\Admin|\Middleware\Cors', function () {

  // Rotta '/news'
  Route::get('/media', function () {
    echo "Media";
  });
  
	// Rotta '/news'
  Route::get('/news', function () {
    echo "News";
  });

});

// Esempio Middleware Admin in app/Middleware/Admin.php
<?php 
namespace Middleware;

use \System\Auth;

class Admin
{
  public function handle() {

    $auth = new Auth();

    if((bool)$auth->hasIdentity()===true) {
      retun true;
    }

    showError('Attenzione','Non hai i permessi per accedere a questa sezione');
  }
}

//------------------------------------------------------------------

<?php
namespace Middleware;

class Cors{
  
  public function handle() {
    header("Access-Control-Allow-Origin: {$_SERVER['HTTP_ORIGIN']}");
    header('Access-Control-Allow-Credentials: true');
    header('Access-Control-Max-Age: 86400');
    header("Access-Control-Allow-Methods: GET, POST, OPTIONS");         
  }
}

```

Nel snippet di codice proposto, nel metodo middelware() vengono istanziati due middleware separati dal carattere **pipe "|"** : Route::middleware('*\Middleware\Admin* **|** *'\Middleware\Cors'*, function(){....});

