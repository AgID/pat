# Classe per la validazione dei forms(Validator)

**Riferimento path sorgente classe Validator:** *core/System/Validator.php*

Il Framework offre una libreria per la validazione dei campi di un modulo, valori in GET|POST oppure validazione di una stringa.

#### **Processo del caricamento**

La validazione di un modulo(form) implica il seguente processo generale:

- Viene visualizzato un modulo di inserimento dei dati;
- Quando il modulo viene inviato, i dati vengono passati al Validatore che effettua tutti i controlli del caso impostati;
- Una volta terminato il processo di validazione, all'utente verrà mostrato un messaggio di successo oppure di errore;



**Esempio pratico della validazione di un modulo**:

1. Nella vista creare un modulo contenente una form di input, come di seguito:

```html
<html>
  <head>
    <title>Test Input Form</title>
  </head>
  <body>
    <!-- Vista che contiene il modulo per il salvataggio di un utente --> 
	<form action="<?php echo siteUrl('/store') ?>" method="POST" name="form_user" id="form_user" class="form_user" 						enctype="multipart/form-data" method="post" accept-charset="utf-8">
        
        <label for="name">Nome utente *</label>
        <input type="text" name="name" value="" placeholder="Nome utente" id="input_name" />
        
        <label for="username">Username *</label>
        <input type="text" name="username" value="" placeholder="Username" id="input_username" />
        
        <label for="email">Email *</label>
        <input type="text" name="email" value="" placeholder="Email" id="input_email" />
  </body>
</html>
```

​	2.Creare un controller per istanziare la classe per la validazione del modulo:

```php
<?php

namespace Http\Web\Admin;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class UsersAdminController extends BaseAuthController
{
    public function __construct()
    {
        parent::__construct();
        helper('checkPassword');
    }
    
    public function store()
    {
        // Instanzio la classe per la validazione new core/System/Validator
    	validator = new Validator();
    	
        /*Controllo il campo 'Nome' del modulo di creazione di un nuovo utente
          passandogli la label ed il value
          ed in fine, i metodi per le validazioni da effettuare su questo campo,
          nell'esempio corrente il campo è obbligatorio e la sua lunghezza deve
          rientrare nellìintervallo (5,45) caratteri.*/
    	validator->label('Nome')
            ->value(Input::post('name'))
            ->required()
            ->betweenString(5, 45)
            ->end();
        
        /*Controllo il campo 'Username' del modulo di creazione di un nuovo utente
          passandogli la label ed il value ed in fine, i metodi per le validazioni da effettuare su questo campo,
          nell'esempio corrente il campo è obbligatorio, la sua lunghezza deve
          rientrare nellìintervallo (5,45) caratteri. In fine viene effettuato un ultimo
          controllo per verificare se lo username inserito è già esistente o meno nel sistema.*/
        validator->label('Username')
            ->value(Input::post('username'))
            ->required()
            ->betweenString(5, 45)
            ->add(function () {
               $usernameExist = UsersModel::where('username', Input::post('username'))->first();
               if (!empty($usernameExist)) {
                   //In caso lo username esite già, viene stampatp il messaggio di errore nella vista
                   return ['error' => 'Username inserito già esistente'];
               }
               return null;
             })
            ->end();
        
        /*Controllo il campo 'Email' del modulo di creazione di un nuovo utente
          passandogli la label ed il value ed in fine, i metodi per le validazioni da effettuare su questo campo,
          nell'esempio corrente il campo è obbligatorio e deve essere una email valida. In fine viene effettuato un ultimo
          controllo per verificare se l'email inserita è già esistente o meno nel sistema.*/
        validator->label('Email')
           ->value(Input::post('email'))
           ->required()
           ->isEmail()
           ->add(function () {
               $emailExist = UsersModel::where('email', Input::post('email'))->first();
               if (!empty($emailExist)) {
                   //In caso l'email esite già, viene stampatp il messaggio di errore nella vista
                   return ['error' => 'Email inserita già esistente'];
               }
               return null;
            })
            ->end(); 
        
       // Setto il messaggio di errore se qualcosa è andato storto
       if(!$validator->isSuccess()) {
           echo $validator->getErrorsHtml();
       }     
        
       [omissis]  
    }
}
```

**Lista dei metodi per la validazione dei campi delle forms:**

`$validator = new \System\Validator();`

- `$validator->label()`

- `$validator->value()`

- `$validator->file()`

- `$validator->required()`

- `$validator->allowed()`

- `$validator->maxSize()`

- `$validator->minSize()`

- `$validator->allowedDimensions()`

- `$validator->minLength()`

- `$validator->maxLength()`

- `$validator->exactLength()`

- `$validator->in()`

- `$validator->notIn()`

- `$validator->isNatural()`

- `$validator->isNaturalNoZero()`

- `$validator->isInt()`

- `$validator->isDecimal()`

- `$validator->isAlpha()`

- `$validator->isAlphaNum()`

- `$validator->isUrl()`

- `$validator->isUri()`

- `$validator->isBool()`

- `$validator->isEmail()`

- `$validator->isDate()`

- `$validator->isHour()`

- `$validator->isBase64()`

- `$validator->isAlphaDash()`

- `$validator->isAlphaNumSpaces()`

- `$validator->isMacAddress()`

- `$validator->isCreditCard()`

- `$validator->accept()`

- `$validator->betweenString()`

- `$validator->isIp()`

- `$validator->regex()`

- `$validator->isDiffers()`

- `$validator->isMatches()`

- `$validator->isGreaterThan()`

- `$validator->isGreaterThanOrEqual()`

- `$validator->isLessThan()`

- `$validator->isLessThanOrEqual()`

- `$validator->add()`

- `$validator->isSuccess()`

- `$validator->getErrors()`

- `$validator->getErrorsHtml()`

  


#### Riferimenti della classe.

`$validator->label($label)`

| Settaggi            | Descrizione                                          |
| ------------------- | ---------------------------------------------------- |
| **Parametri**       | **$label **( string ) - Nome del campo da analizzare |
| **Ritorno**         | $this - Istanza della classe Validator               |
| **Tipo di ritorno** | Validator                                            |

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
    	 ->end();

```



------

``$validator->value($value)``

| Settaggi            | Descrizione                                         |
| ------------------- | --------------------------------------------------- |
| **Parametri**       | **$value** - Valore da analizzare GET\|POST\|STRING |
| **Ritorno**         | $this - Istanza della classe Validator              |
| **Tipo di ritorno** | Validator                                           |

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->end();
```



------

`$validator->file($value)`

| Settaggi            | Descrizione                              |
| ------------------- | ---------------------------------------- |
| Parametri****       | **$value** - Valore del file da caricare |
| **Ritorno**         | $this - Istanza della classe Validator   |
| **Tipo di ritorno** | Validator                                |

Esempio:

```php
validator = new Validator();

validator->label('Foto Allegata')
         ->file(Input::files('photo'))
         ->end();
```



------

`$validator->end()`

| Settaggi            | Descrizione                            |
| ------------------- | -------------------------------------- |
| Parametri****       |                                        |
| **Ritorno**         | $this - Istanza della classe Validator |
| **Tipo di ritorno** | Validator                              |

Metodo per il reset dei valori delle variabili.

Esempio: 

```php
validator = new Validator();

validator->label('Foto Allegata')
         ->file(Input::files('photo'))
         ->end();
```



------

`$validator->required($errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| Parametri****       | **$errorMsg** (string) - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this -  Istanza della classe Validator                      |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per i campi che vanno obbligatoriamente compilati. In caso di mancata compilazione del campo, stampa il messaggio di errore.

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->required()
         ->end();        
```



------

`$validator->allowed($ext, $errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$ext** - Estensioni accettate per il file da caricare <br />**$errorMsg** - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this - Istanza della classe Validator                       |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per impostare le estensioni accettate per il file da caricare.

Esempio:

```php
validator = new Validator();

validator->label('Foto Allegata')
         ->file(Input::files('photo'))
         ->allowed(['png', 'jpeg', 'jpg', 'gif'])
         ->end();
```



------

`$validator->maxSize($size=0, $errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| Parametri****       | **$size** - Dimensione massima del file da caricare <br />**$errorMsg** - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this - Istanza della classe Validator                       |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per impostare la dimensione massima accettata per il file da caricare.

Esempio:

```php
validator = new Validator();

validator->label('Foto Allegata')
         ->file(Input::files('photo'))
         ->allowed(['png', 'jpeg', 'jpg', 'gif'])
         ->maxSize('5MB')
         ->end();
```



------

`$validator->allowedDimensions($minWidth = null, $maxWidth = null, $minHeight = null, $maxHeight = null, $errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$minWidth** - Larghezza minima dell'immagine da caricare<br />**$maxWidth** - Larghezza massima dell'immagine da caricare<br />**$minHeight** - Altezza minima dell'immagine da caricare <br />**$maxHeight** - Altezza massima dell'immagine da caricare <br />**$errorMsg** - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this - Istanza della classe Validator                       |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per impostare le dimensioni minime e massime di un' immagine.

Esempio:

```php
validator = new Validator();

validator->label('Foto Allegata')
         ->file(Input::files('photo'))
         ->allowed(['png', 'jpeg', 'jpg', 'gif'])
         ->maxSize('5MB')
         ->allowedDimensions('50', '1024', '50', '1024')
         ->end();
```



------

`$validator->minLength($length=0, $errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$length** - lunghezza minima della stringa da validare<br />**$errorMsg** - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this - Istanza della classe Validator                       |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per impostare la lunghezza minima che deve avere una stringa.

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->minLength(8)
         ->end();       
```



------

`$validator->maxLength($length=0, $errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$length** - lunghezza minima della stringa da validare<br />**$errorMsg** - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this - Istanza della classe Validator                       |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per impostare la lunghezza massima che può avere una stringa.

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->maxLength(40)
         ->end();  
```



------

`$validator->exactLength($length, $errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$length** - lunghezza esatta della stringa da validare<br />**$errorMsg** - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this - Istanza della classe Validator                       |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per impostare la lunghezza esatta che deve avere una stringa.

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->exactLength(15)
         ->end();  
```



------

`$validator->in($list = '', $errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$list** - Elenco dei valori accettati<br />**$errorMsg** - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this - Istanza della classe Validator                       |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per impostare una lista predefinita di valori accettati.

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->in('1, 5, 8, 20, 50, ciao, hello')
         ->end();  
```



------

`$validator->notIn($list = '', $errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$list** - Elenco dei valori non accettati<br />**$errorMsg** - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this - Istanza della classe Validator                       |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per impostare una lista predefinita di valori non accettati.

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->notIn('1, 5, 8, 20, 50, ciao, hello')
         ->end();  
```



------

`$validator->isNatural($errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$errorMsg** - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this - Istanza della classe Validator                       |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per verificare se il valore da analizzare è un numero naturale.

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->isNatural()
         ->end();  
```



------

`$validator->isNaturalNoZero($errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$errorMsg** - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this - Istanza della classe Validator                       |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per verificare se il valore da analizzare è un numero naturale escluso lo zero.

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->isNaturalNoZero()
         ->end();  
```



------

`$validator->isInt($errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$errorMsg** - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this - Istanza della classe Validator                       |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per verificare se il valore da analizzare è un numero intero.

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->isInt()
         ->end();  
```



------

`$validator->isDecimal($errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$errorMsg** - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this - Istanza della classe Validator                       |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per verificare se il valore da analizzare è un numero decimale.

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->isDecimal()
         ->end();  
```



------

`$validator->isAlpha($errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$errorMsg** - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this - Istanza della classe Validator                       |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per verificare se il valore da analizzare è una lettera dell'alfabeto.

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->isAlpha()
         ->end();  
```



------

`$validator->isAlphaNum($errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$errorMsg** - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this - Istanza della classe Validator                       |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per verificare se il valore da analizzare è una lettera dell'alfabeto o un numero.

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->isAlphaNum()
         ->end();  
```



------

`$validator->isUrl($errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$errorMsg** - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this - Istanza della classe Validator                       |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per verificare se il valore da analizzare è un URL valido.

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->isUrl()
         ->end();  
```



------

`$validator->isUri($errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$errorMsg** - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this - Istanza della classe Validator                       |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per verificare se il valore da analizzare è un Uri.

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->isUri()
         ->end();  
```



------

`$validator->isBool($errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$errorMsg** - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this - Istanza della classe Validator                       |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per verificare se il valore da analizzare è true o false.

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->isBool()
         ->end();  
```



------

`$validator->isEmail($errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$errorMsg** - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this - Istanza della classe Validator                       |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per verificare se il valore da analizzare è una email valida.

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->isEmail()
         ->end(); 
```



------

`$validator->isDate($format = null, $errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$format** - formato data accettato<br />**$errorMsg** - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this - Istanza della classe Validator                       |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per verificare se il valore da analizzare è una data valida nel formato passato come parametro.

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->isDate($format = 'd/m/Y')
         ->end(); 
```



------

`$validator->isHour($type = null, $errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$type** - formato ora accettato<br />**$errorMsg** - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this - Istanza della classe Validator                       |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per verificare se il valore da analizzare è un orario nel formato passato come parametro.

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->isHour($type = 'H:m')
         ->end(); 
```



------

`$validator->isBase64($errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$errorMsg** - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this - Istanza della classe Validator                       |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per verificare se il valore da analizzare è una base64 valida.

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->isBase64()
         ->end();
```



------

`$validator->isAlphaDash($errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$errorMsg** - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this - Istanza della classe Validator                       |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per verificare se il valore da analizzare contiene solo caratteri alfa-numerici, underscore e punto.

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->isAlphaDash()
         ->end();
```



------

`$validator->isAlphaNumSpaces($errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$errorMsg** - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this - Istanza della classe Validator                       |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per verificare se la stringa da analizzare contiene solo caratteri alfa-numerici e di spaziatura.

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->isAlphaNumSpaces()
         ->end();
```



------

`$validator->isMacAddress($errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$errorMsg** - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this - Istanza della classe Validator                       |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per verificare se la stringa da analizzare è un MAC address valido.

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->isMacAddress()
         ->end();
```



------

`$validator->isCreditCard($type = null, $errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$type** - Tipo carta di credito accettato<br />**$errorMsg** - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this - Istanza della classe Validator                       |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per verificare se il valore da analizzare è un numero di carta di credito valido.

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->isCreditCard('visa')
         ->end();
```



------

`$validator->accept($errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$errorMsg** - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this - Istanza della classe Validator                       |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per verificare se il valore da analizzare rientra nei parametri di accettazione: ['yes', 'y', 'on', '1', 1, true, 'true'].

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->accept()
         ->end();
```



------

`$validator->betweenString($min = 0, $max = 100000, $errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$min** - Lunghezza minima che deve avere la stringa<br/>**$max** - Lunghezza massima che può avere la stringa<br/>**$errorMsg** - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this - Istanza della classe Validator                       |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per verificare se la lunghezza della stringa da analizzare rientra nei parametro minimo e massimo

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->betweenString(5,20)
         ->end();
```



------

`$validator->isIp($errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$errorMsg** - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this - Istanza della classe Validator                       |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per verificare se il valore da analizzare è un indirizzo IP valido.

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->isIp()
         ->end();
```



------

`$validator->regex($pattern, $errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| Parametri****       | **$pattern** - Pattern che deve rispettare l'espressione regolare<br/>**$errorMsg** - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this - Istanza della classe Validator                       |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per verificare se il valore da analizzare rispetta il pattern impostato nell'espressione regolare.

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->regex('/^([0-9\(\)\.\-\+\ ]){3,17}$/')
         ->end();
```



------

`$validator->isDiffers($value = null, $label = null, $errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| Parametri****       | **$value** - Stringa che deve differire dal valore da validare<br />**$label** - Label del campo di input utilizzata per il messaggio di errore<br />**$errorMsg** - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this - Istanza della classe Validator                       |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per verificare se la stringa da analizzare è diversa dalla stringa passata nel parametro $value.

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->isDiffers('test')
         ->end();
```



------

`$validator->isMatches($value = null, $label = null, $errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$value** - Stringa che deve differire dal valore da validare<br />**$label** - Label del campo di input utilizzata per il messaggio di errore<br />**$errorMsg** - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this - Istanza della classe Validator                       |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per verificare se la stringa da analizzare è uguale alla stringa passata nel parametro $value.

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->isMatches('test')
         ->end();
```



------

`$validator->isGreaterThan($min = 0, $errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$min** - Valore minimo accettato<br />**$errorMsg** - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this - Istanza della classe Validator                       |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per verificare se il valore da analizzare è maggiore al numero impostato nel parametro $min.

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->isGreaterThan(10)
         ->end();
```



------

`$validator->isGreaterThanOrEqual($min = 0, $errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$min** - Valore minimo accettato<br />**$errorMsg** - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this - Istanza della classe Validator                       |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per verificare se il valore da analizzare è maggiore o uguale al numero impostato nel parametro $min.

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->isGreaterThanOrEqual(10)
         ->end();
```



------

`$validator->isLessThan($max = 0, $errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$max** - Valore massimo accettato<br />**$errorMsg** - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this - Istanza della classe Validator                       |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per verificare se il valore da analizzare è minore al numero impostato nel parametro $max.

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->isLessThan(20)
         ->end();
```



------

`$validator->isLessThanOrEqual($max = 0, $errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$max** - Valore massimo accettato<br />**$errorMsg** - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this - Istanza della classe Validator                       |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per verificare se il valore da analizzare è minore o uguale al numero impostato nel parametro $max.

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->isLessThanOrEqual(20)
         ->end();
```



------

`$validator->isNumeric($errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$errorMsg** - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this - Istanza della classe Validator                       |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per verificare se il valore da analizzare è un numero intero o decimale.

Esempio: 

```
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->isNumeric()
         ->end();
```



------

`$validator->add($callback, $errorMsg = null)`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$callback** - Funzione eseguita dal validatore<br />**$errorMsg** - Messaggio da mostrare in caso di mancata validazione |
| **Ritorno**         | $this - Istanza della classe Validator                       |
| **Tipo di ritorno** | Validator                                                    |

Metodo utilizzato per estendere in modo astratto la classe Validator.

Esempio:

```php
validator->label('Email')
         ->value(Input::post('email'))
         ->required()
         ->isEmail()
         ->add(function () {
              $emailExist = UsersModel::where('email', Input::post('email'))->first();
              if (!empty($emailExist)) {
                 return ['error' => 'Email inserita già esistente'];
              }
              return null;
         });
```



------

`$validator->isSuccess()`

| Settaggi            | Descrizione |
| ------------------- | ----------- |
| **Parametri**       |             |
| **Ritorno**         | true\|false |
| **Tipo di ritorno** | Bool        |

Metodo che ritorna true se la validazione è andata a buon fine oppure false in caso contrario.

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->isAlphaNumSpaces()
         ->end();
                 
if(validator->isSuccess()) {
    [omissis]
}                 
```



------

`$validator->getErrors()`

| Settaggi            | Descrizione                              |
| ------------------- | ---------------------------------------- |
| **Parametri**       |                                          |
| **Ritorno**         | $errors - Errore del valore non validato |
| **Tipo di ritorno** | null\|array                              |

Metodo che ritorna l'errore del valore non validato o null se tutti i valori sono validati

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->required()
         ->end();
                 
if(validator->isSuccess()) {
    
    [omissis]
    
} else {
    
    return $validator->getErrors();
    
}          
```





------

`$validator->getErrorsHtml()`

| Settaggi            | Descrizione                                       |
| ------------------- | ------------------------------------------------- |
| **Parametri**       |                                                   |
| **Ritorno**         | Elemento html della lista dei valori non validati |
| **Tipo di ritorno** | array\|null                                       |

Metodo che ritorna un elemento html della lista dei valori non validati

Esempio:

```php
validator = new Validator();

validator->label('Nome campo')
	     ->value(Input::post('campo')
         ->required()
         ->end();
                 
if(validator->isSuccess()) {
    
    [omissis]
    
} else {
    
    return $validator->getErrorsHtml();
    
}          
```
