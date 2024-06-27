**Riferimento path sorgente classe upload:** *core/System/Session.php*

Il Framework offre due alternative per la memorizzazione della sessione. Il metodo classico, prevede lo storage delle sessioni nel File System,  oppure l'alternativa è la memorizzazione della sessione nel Database.
L'ultima soluzione prevede che nel nel database ci sia una tabella preposta al salvataggio della sessione.

La tipologia dello storage delle sessione può essere configurata nel file di configurazione allocato in `app/Config/session.php` Di default le sessioni sono salvate sul file system.



**Lista dei metodi per lo storage delle sessioni:**

`$session = new \System\Session();`

- `$session->start()`
- `$session->getId()`
- `$session->set()`
- `$session->get()`
- `$session->has()`
- `$session->setTemp()`
- `$session->getTemp()`
- `$session->setFlash()`
- `$session->getFlash()`
- `$session->all()`
- `$session->kill()`
- `$session->destroy()`
- `$session->close()`

##### Riferimenti della classe.

`$session->start();`



Questa funzione permette di inizializzare la Sessione.

| Settaggi            | Descrizione               |
| ------------------- | ------------------------- |
| **Parametri**       |                           |
| **Ritorno**         | La sessione inizializzata |
| **Tipo di ritorno** | Session                   |



------

`$session->getId();`



Quest funzione restituisce l'ID della sessione corrente.

| Settaggi            | Descrizione                |
| ------------------- | -------------------------- |
| **Parametri**       |                            |
| **Ritorno**         | ID della sessione corrente |
| **Tipo di ritorno** | string                     |

Esempio:

```php
$session = new Session();
trace($session->getId());

//L'esempio di sopra stamperà l'ID della sessione, come di seguito:

// '1cmu5ukfdv2bebgumqde8pcfaq'
```



------

`$session->set($key, $value = null);`



Questa funzione permette di settare dati di  sessione sottoforma di  `key=>value`

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$key** (string) - Chiave identificativa del dato da settare nella sessione<br />**$value**  (mixed) - Valore del dato da settare nella sessione |
| **Ritorno**         |                                                              |
| **Tipo di ritorno** | null                                                         |

Esempio:

```php
$session = new \System\Session();
$session->set('test', 'Valore di test');
```



------

`$session->get($key);`



Questa funzione restituisce il valore associato alla chiave passata nel parametro `key`.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$key** (string) - Chiave del dato in sessione da restituire |
| **Ritorno**         | Il dato in sessione associato alla chiave specificata nel parametro `$key` se presente, altrimenti null |
| **Tipo di ritorno** | mix\|null                                                    |

Esempio:

```php
$session = new \System\Session();
$session->set('test', 'Valore di test');
$session->get('test');

//L'esempio di sopra stamperà il valore associato alla chiave passata come parametro $key, come di seguito:

// 'Valore di test'
```



------

`$session->has($key);`



Questa funzione controlla se la chiave passata nel parametro `$key` esiste o meno nella sessione.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       |                                                              |
| **Ritorno**         | True se la chiave passata nel parametro `$key` esiste nella sessione, false altrimenti |
| **Tipo di ritorno** | bool                                                         |

Esempio:

```php
$session = new \System\Session();
$session->set('test', 'Valore di test');
$session->has('test');

//L'esempio di sopra stamperà true o false a seconda se la chiave passata nel parametro $key esiste o meno nella sessione, come //di seguito: 

// true
```



------

`$session->setTemp($key, $value = null, $temp);`



Questa funzione permette di settare dati di sessione sottoforma di  `key=>value` con un tempo di scadenza specifico.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$key** (string) - Chiave identificativa del dato da settare nella sessione<br />**$value**  (mixed) - Valore del dato da settare nella sessione<br />**$temp** (int) - Numero di secondi dopo il quale il dato scade |
| **Ritorno**         |                                                              |
| **Tipo di ritorno** | null                                                         |

Esempio:

```php
$session = new \System\Session();
$session->setTemp('test', 'Valore di test', 5);

//L'esempio di sopra setta il dato nella sessione con un tempo di scadenza di 5 secondi.
```



------

`$session->getTemp($key);`



Questa funzione restituisce il dato temporaneo presente nella sessione con  la chiave passata nel parametro `$key`.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$key** (string) - Chiave identificativa del dato di sessione da restituire |
| **Ritorno**         | Il dato in sessione associato alla chiave specificata nel parametro `$key` se presente, altrimenti null |
| **Tipo di ritorno** | mix\|null                                                    |

Esempio:

```php
$session = new \System\Session();
$session->setTemp('test', 'Valore di test', 5);
trace($session->getTemp('test'));
sleep(8);
trace($session->getTemp('test'));

//Di sopra un esempio dove il dato dopo il tempo di scadenza impostato venga eliminato dalla sessione, come possiamo vedere di //seguito: 

// 'Valore di test' (stampa prima dei 5 secondi)
// null             (stampa dopo i 5 secondi)
```



------

`$session->setFlash($key, $value = null);`



Questa funzione permette di settare dati di sessione che saranno disponibili solo per la prossima richiesta, e poi vengono automaticamente cancellati.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$key** (string) - Chiave identificativa del dato da settare nella sessione<br />**$value**  (mixed) - Valore del dato da settare nella sessione |
| **Ritorno**         |                                                              |
| **Tipo di ritorno** | null                                                         |

Esempio:

```php
$session = new \System\Session();
$session->setFlash('flash', 'Valore di flash');
```



------

`$session->getFlash($key);`



Questa funzione restituisce il dato flash presente nella sessione con  la chiave passata nel parametro `$key`.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$key** (string) - Chiave identificativa del dato di sessione da restituire |
| **Ritorno**         | Il dato flash in sessione associato alla chiave specificata nel parametro `$key` se ancora presente, altrimenti null |
| **Tipo di ritorno** | mix\|null                                                    |

Esempio:

```php
$session = new \System\Session();
$session->setFlash('flash', 'Valore di flash');
trace($session->getFlash('flash'));
trace($session->getFlash('flash'));

//Come possiamo vedere di seguito dal risultato dell'esempio di sopra, il dato flash è disponibile solo per una chiamata:

// 'Valore di flash'	(prima chiamata dato disponibile)
// null					(seconda chiamata dato non più disponibile)
```



------

`$session->all();`



Questa funzione restituisce tutti i dati di sessione.

| Settaggi            | Descrizione                               |
| ------------------- | ----------------------------------------- |
| **Parametri**       |                                           |
| **Ritorno**         | Array contenente tutti i dati di sessione |
| **Tipo di ritorno** | array                                     |

Esempio:

```php
 $session = new Session();
 trace($session->all());

//L'esempio di sopra restituisce tutti i dati di sessione, come possiamo vedere di seguito:

/*
	array (size=7)
  '___FREMAWORK___' => 
    array (size=2)
      'last_regen' => int 1638877672
      'begin' => int 1638864577
  'pat_os_domain_info' => 
    array (size=64)
      'id' => int 1
      'id_creator' => int 2
      'institution_type_id' => int 1
      'state' => int 1
      'full_name_institution' => string 'Comune di Esempio' (length=17)
      'short_institution_name' => string 'comune_di_esempio' (length=17)
      'vat' => string '01722270665' (length=11)
      'email_address' => string 'supporto@isweb.it' (length=17)
      'certified_email_address' => string 'pec@isweb.it' (length=12)
      'institutional_website_name' => string 'Example' (length=7)
      'institutional_website_url' => string 'http://www.example.com' (length=22)
      'top_level_institution_name' => string 'Esempio' (length=7)
      'top_level_institution_url' => string 'http://www.esempio.it' (length=21)
      'welcome_text' => string '<h2>Benvenuti</h2>' (length=18)
      'footer_text' => string '<h3>Footer</h3>' (length=15)
      'accessibility_text' => string '<p>acc</p>' (length=10)
      'address_street' => string 'Via XX Settembre' (length=16)
      'address_zip_code' => string '67055' (length=5)
      'address_city' => string 'Avezzano' (length=8)
      'address_province' => string 'AQ' (length=2)
      'phone' => string '3332564589' (length=10)
      'two_factors_identification' => int 1
      'trasparenza_logo_file' => string 'NULL' (length=4)
      'activation_date' => null
      'expiration_date' => string '2022-10-06 10:42:04' (length=19)
      'cancellation' => int 0
      'trasparenza_urls' => string 'http://patos.local' (length=18)
      'bulletin_board_url' => string 'http://patos.local.it' (length=21)
      'simple_logo_file' => string '45ceb11448d940c0d6b6134b35e140b5.png' (length=36)
      'favicon_file' => string 'NULL' (length=4)
      'opendata_channel' => null
      'show_update_date' => null
      'statistic_snippet_code' => string 'NULL' (length=4)
      'google_maps_api_key' => string 'null' (length=4)
      'indexable' => int 1
      'support' => int 0
      'show_regulation_in_structure' => null
      'tabular_display_org_ind_pol' => int 1
      'max_users' => null
      'client_code' => null
      'smtp_username' => string 'patos@isweb.it' (length=14)
      'smtp_pec_username' => string 'username@pec.test.it' (length=20)
      'smtp_password' => string 'passwordTest95!' (length=15)
      'smtp_pec_password' => string 'passwordTest95!' (length=15)
      'smtp_host' => string 'mail.internetsoluzioni.it' (length=25)
      'smtp_pec_host' => string 'mail.internetsoluzioni.it' (length=25)
      'smtp_port' => string '587' (length=3)
      'smtp_pec_port' => string '588' (length=3)
      'smtp_security' => int 2
      'smtp_pec_security' => int 3
      'smtp_auth' => null
      'show_smtp_auth' => int 1
      'smtp_test_email' => string 'a.paris@isweb.it' (length=16)
      'smtp_pec_auth' => int 2
      'email_notifications' => null
      'email_pec_notifications' => string 'esempio@pec.test.it' (length=19)
      'publication_responsible' => string 'Vincenzo Apostolo' (length=17)
      'privacy_url' => string 'htttp://www.esempio.it' (length=22)
      'private_token' => null
      'last_visit_time_limit' => int 2592000
      'personnel_roles' => null
      'created_at' => string '2021-10-06T08:42:04.000000Z' (length=27)
      'deleted_at' => null
      'updated_at' => string '2021-12-03T14:47:07.000000Z' (length=27)
  .....
  [omissis]
*/
```



------

`$session->kill($key=null);`



Questa funzione elimina i campi nei dati di sessione con le chiavi passate nel parametro `$key` se presenti altrimenti non fa nulla.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$key** (string\|array) - Chiavi identificative dei capi da eliminare nei dati di sessione |
| **Ritorno**         |                                                              |
| **Tipo di ritorno** | null                                                         |

Esempio:

```php
$session = new Session();
$session->set('esempio', 'Valore di esempio');
trace($session->get('esempio'));
$session->kill('esempio');
trace($session->get('esempio'));

// Come possiamo vedere dal risultato dell'esempio di seguito, dopo l'operazione di kill(), il dato viene eliminato:

// 'Valore di esempio'			(stampa prima dell'operazione di kill)
//  null						(stampa dopo l'operazione di kill)
```



------

`$session->destroy();`



Questa funzione permette di cancellare la sessione corrente e di conseguenza tutti i dati di sessione.

| Settaggi            | Descrizione |
| ------------------- | ----------- |
| **Parametri**       |             |
| **Ritorno**         |             |
| **Tipo di ritorno** | null        |

Esempio:

```php
$session = new Session();
$session->set('esempio', 'Valore di esempio');
$session->destroy();
```



------

`$session->close();`



Questa funzione permette di chiudere la sessione.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       |                                                              |
| **Ritorno**         | True in caso di operazione riuscita con successo, false atrimenti |
| **Tipo di ritorno** | bool                                                         |

Esempio:

```php
$session = new Session();
[omissis]
$session->close();
```

