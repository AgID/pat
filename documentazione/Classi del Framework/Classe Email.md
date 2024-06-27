# Classe per l'invio di E-mail (Email)

**Riferimento path sorgente classe upload:** *core/System/Email.php*

La  classe di posta elettronica del Framework supporta le seguenti funzionalità:

- Protocolli multipli: Mail, Sendmail e SMTP
- Crittografia TLS e SSL per SMTP
- Più destinatari
- CC e BCC
- Email HTML o in testo normale
- Allegati
- Word wrapping
- Priorità
- Modalità batch BCC, che consente di suddividere elenchi di e-mail di grandi dimensioni in piccoli batch BCC.
- Strumenti di debug della posta elettronica

#### Utilizzo della libreria di posta elettronica

##### Invio di e-mail

L'invio di e-mail non è solo semplice, ma puoi configurarlo al volo o impostare le tue preferenze in un file di configurazione.

Esempio:

```php

// Carico i parametri di configurazione per l'invio di una email.
// Il percorso dei parametri di configurazione sono in:
// app/Config/email.php
$configs = loadConfigMail();

$email = new \System\Email($configs);

$send = $email->from($configs['smtp_user'])
              ->to('example@example.com')
              ->subject('Invio Messaggio Framework')
              ->set_alt_message('Messaggio alternativo testo semplice')
              ->message('Messaggio in formato html')
              ->send();

// Test Invio email
if (!$send) {

  echo $email->print_debugger();

} else {

  echo "email inviata con successo";

}
```

#### Impostazione delle preferenze e-mail

Sono disponibili 21 diverse preferenze per personalizzare la modalità di invio dei messaggi di posta elettronica. Puoi impostarli manualmente come descritto qui o automaticamente tramite le preferenze memorizzate nel tuo file di configurazione, descritto di seguito:

Le preferenze vengono impostate passando un array di valori di preferenza al metodo di inizializzazione dell'email. Ecco un esempio di come potresti impostare alcune preferenze:

```php
/*
In questo esempio i parametri di configurazione sono settati in linea e non salvati sul file di configurazione: app/Config/email.php
*/
$config['protocol']  =  'sendmail' ; 
$config ['mailpath']  =  '/usr/sbin/sendmail' ; 
$config ['charset']  =  'iso-8859-1' ; 
$config ["wordwrap"]  =  TRUE ;

// E' possibile impostare i paramentri di configurazione nel costruttore della classe 
$email = new \System\Email($configs);

/*
In alternativa è possibile istanziare i parametri di configurazione nel metodo       initialize($config), come in questo esempio:
*/
$email = new \System\Email();
$email->initialize($configs);
```

###### **NOTA: La maggior parte delle preferenze ha valori predefiniti, che verranno utilizzati se non vengono impostati.**

#### Impostare le preferenze e-mail in un file di configurazione

E' buona norma impostare tutti i parametri di configurazione di invio email in un array multidimensionale nel file configuratore residente in `app/Config/email.php`;
In questo file è possibile impostare più provider di servizi di invio email, e selezionare lo stesso semplicemente chiamando la funzione `loadConfigMail('provider_custom')`. Se non viene passato nessun valore dentro la funzione, quest'ultima userà come riferimento il provider *'default'*:
Ecco un esempio:

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



#### Preferenze e settaggi di configurazione per l'email

Di seguito è riportato un elenco di tutte le preferenze che possono essere impostate durante l'invio di e-mail.

| Preferenza         | Valore predefinito        | Opzioni                           | Descrizione                                                  |
| ------------------ | ------------------------- | --------------------------------- | ------------------------------------------------------------ |
| **useragent**      | Framework                 | Nessuna                           | Il nome dell'User Agent per l'invio della email.             |
| **protocol**       | mail                      | mail, sendmail o smtp             | Il protocollo di invio della posta.                          |
| **mailpath**       | /usr /sbin /sendmail      | Nessuna                           | Il percorso del server a Sendmail.                           |
| **smtp_host**      | Nessun valore predefinito | Nessuna                           | Indirizzo server SMTP.                                       |
| **smtp_user**      | Nessun valore predefinito | Nessuna                           | Nome utente SMTP.                                            |
| **smtp_pass**      | Nessun valore predefinito | Nessuna                           | Password SMTP.                                               |
| **smtp_port**      | 25                        | Nessuna                           | Porta SMTP.                                                  |
| **smtp_timeout**   | 5                         | Nessuna                           | Timeout SMTP (in secondi).                                   |
| **smtp_keepalive** | FALSO                     | VERO o FALSO (booleano)           | Abilita connessioni SMTP persistenti.                        |
| **smtp_crypto**    | Nessun valore predefinito | tls o ssl                         | Crittografia SMTP                                            |
| **wordwrap**       | VERO                      | VERO o FALSO (booleano)           | Abilita il ritorno a capo automatico.                        |
| **wrapchars**      | 76                        |                                   | Conteggio caratteri da concludere in una riga.               |
| **mailtype**       | testo                     | testo o html                      | Tipo di posta. Se invii un'e-mail HTML, devi inviarla come una pagina web completa. Assicurati di non avere collegamenti relativi o percorsi di immagini relativi altrimenti non funzioneranno. |
| **charset**        | `$config['charset']`      |                                   | Set di caratteri (utf-8, iso-8859-1, ecc.).                  |
| **validate**       | FALSO                     | VERO o FALSO (booleano)           | Se convalidare l'indirizzo e-mail.                           |
| **priority**       | 3                         | 1, 2, 3, 4, 5                     | Priorità email. 1 = più alto. 5 = più basso. 3 = normale.    |
| **crlf**           | \ n                       | **"\r \n"** o **"\n"** **o "\r"** | Carattere di nuova riga. (Usa "\ r \ n" per conformarti a RFC 822). |
| **newline**        | \ n                       | **"\r\n"** o **"\n"** o **"\r"**  | Carattere di nuova riga. (Usa "\ r \ n" per conformarti a RFC 822). |
| **bcc_batch_mode** | FALSO                     | VERO o FALSO (booleano)           | Abilita la modalità batch BCC.                               |
| **bcc_batch_size** | 200                       | Nessuna                           | Numero di messaggi di posta elettronica in ogni batch BCC.   |
| **dsn**            | FALSO                     | VERO o FALSO (booleano)           | Abilita notifica messaggio dal server                        |

#### Sovrascrittura a capo automatico

Se hai abilitato il word wrapping (consigliato per la conformità con RFC 822) e hai un collegamento molto lungo nella tua e-mail, anche questo può essere "incapsulato", rendendolo non cliccabile dalla persona che lo riceve. Il Framework ti consente di sostituire manualmente il wrapping delle parole all'interno di una parte del tuo messaggio in questo modo:

```php+HTML
Il  testo  della  tua  email  che 
viene  inserito  normalmente .

{unwrap}http://example.com/link.html{/unwrap}

Più  testo  che  verrà  essere 
avvolto  normalmente .
```

Puoi posizionare l'elemento che non vuoi racchiudere in una parola tra: {unfrap} {/ unfrap}



#### Riferimenti della classe:

```php
$email->from($from[, $name = ''[, $return_path = NULL]])
```

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$from** ( *string* ) - Indirizzo e-mail "from" <br />**$name** ( *string* ) - Nome visualizzato "from" <br />**$return_path** ( *string* ) - Indirizzo e-mail opzionale a cui reindirizzare i messaggi di posta elettronica non consegnati |
| **Ritorno**         | Istanza Email (concatenamento di metodi)                     |
| **Tipo di ritorno** | Email                                                        |

Imposta l'indirizzo e-mail e il nome della persona che invia l'e-mail:

```php
$email->from('marco.rossi@example.com','Marco Rossi');
```

Puoi anche impostare un percorso di ritorno, per aiutare a reindirizzare la posta non consegnata:

```php
$email->from('marco.rossi@example.com','Marco Rossi', 'return@example.com');
```

*** NOTA:  Return-Path non può essere utilizzato se hai configurato "smtp" come protocollo.**



------



```php
$email->reply_to($replyto[, $name = '']);
```

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$replyto** ( *string* ) - Indirizzo e-mail per le risposte<br /> **$name** ( *string* ) - Visualizza il nome per l'indirizzo e-mail di risposta |
| **Ritorno**         | Istanza Email (concatenamento di metodi)                     |
| **Tipo di ritorno** | Email                                                        |

Imposta l'indirizzo per le risposte. Se le informazioni non vengono fornite, vengono utilizzate le informazioni nel metodo: meth: from. Esempio:

```php
$email->reply_to('marco.rossi@example.com ' , 'Your Name');
```



------



```php
$email->to($to);
```

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$to** ( *mix* ): stringa delimitata da virgole o matrice di indirizzi di posta elettronica |
| **Ritorno**         | Istanza Email (concatenamento di metodi)                     |
| **Tipo di ritorno** | Email                                                        |

Imposta gli indirizzi e-mail dei destinatari. Può essere una singola e-mail, un elenco delimitato da virgole o un array:

```php
$email->to('someone@example.com');
```

```php
$email->to('one@example.com, two@example.com, three@example.com');
```

```php
$email->to( ['one@example.com', 'two@example.com', 'three@example.com'] );
```



------



```php
$email->cc($cc);
```

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$cc** ( *mix* ): stringa delimitata da virgole o matrice di indirizzi di posta elettronica |
| **Ritorno**         | Istanza Email (concatenamento di metodi)                     |
| **Tipo di ritorno** | Email                                                        |

Imposta gli indirizzi e-mail CC. Proprio come il "**to()**", può essere una singola e-mail, un elenco delimitato da virgole o un array.



------



```php
$email->bcc($bcc[, $limit = ''])
```

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$bcc** ( *mix* ): stringa delimitata da virgole o matrice di indirizzi di posta elettronica<br />**$limit** ( *int* ) - Numero massimo di e-mail da inviare per batch |
| **Ritorno**         | Istanza Email (concatenamento di metodi)                     |
| **Tipo di ritorno** | Email                                                        |

Imposta gli indirizzi e-mail BCC. Proprio come il metodo **to()** , può essere una singola e-mail, un elenco delimitato da virgole o un array.

Se **$limit** è impostato, verrà abilitata la "modalità batch", che invierà le e-mail ai batch, con ogni batch non superiore a quello specificato $limit.



------



```php
$email->subject($subject)
```

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$subject** ( *string* ) - Oggetto del messaggio di posta elettronica |
| **Ritorno**         | Istanza Email (concatenamento di metodi)                     |
| **Tipo di ritorno** | Email                                                        |

Imposta l'oggetto dell'email:

```php
$email->subject('Questo è il mio messaggio');
```



------



```php
$email->message($body)
```

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$body** (*string*) – Corpo del messaggio di posta elettronica |
| **Ritorno**         | Istanza Email (concatenamento di metodi)                     |
| **Tipo di ritorno** | Email                                                        |

Imposta il corpo del messaggio di posta elettronica:

```php
$email->message('<p>Questo è il messaggio della e-mail</p>');
```



------



```php
$email->set_alt_message($str)
```

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$str** ( *string* ) - Corpo del messaggio di posta elettronica alternativo |
| **Ritorno**         | Istanza Email (concatenamento di metodi)                     |
| **Tipo di ritorno** | Email                                                        |

Imposta il corpo del messaggio di posta elettronica:

```php
$email->set_alt_message('Questo è il messaggio alternativo non in html');
```



------



```php
$email->set_header($header, $value);
```

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$header** (*string*) – nome dell'intestazione <br />**$value** (*string*) – valore dell'intestazione |
| **Ritorno**         | Istanza Email (concatenamento di metodi)                     |
| **Tipo di ritorno** | Email                                                        |

Aggiunge intestazioni aggiuntive all'e-mail:

```php
$email->set_header('Header1', 'Value1');
$email->set_header('Header2', 'Value2');
```



------



```php
$email->clear([$clear_attachments = FALSE]);
```

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$clear_attachments** (*bool*) – Indica se cancellare o meno gli allegati |
| **Ritorno**         | Istanza Email (concatenamento di metodi)                     |
| **Tipo di ritorno** | Email                                                        |

Inizializza tutte le variabili di posta elettronica in uno stato vuoto. Questo metodo è destinato all'uso se si esegue il metodo di invio di posta elettronica in un ciclo, consentendo il ripristino dei dati tra i cicli.

```php
foreach ($list as $name => $address)
{
        $email->clear();

        $email->to($address);
        $email->from('your@example.com');
        $email->subject('Informazioni per '.$name);
        $email->message('Ciao '.$name.' Ecco le informazioni che hai richiesto.');
        $email->send();
}
```



------



```php
$email->send([$auto_clear = TRUE])
```

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$auto_clear** (*bool*) – Indica se cancellare automaticamente i dati del messaggio |
| **Ritorno**         | VERO in caso di successo, FALSO in caso di fallimento        |
| **Tipo di ritorno** | bool                                                         |

Il metodo di invio dell'e-mail. Restituisce un valore booleano VERO o FALSO in base a successo o fallimento, consentendone l'uso condizionale

```php
if ( !$email->send() )
{
  // Genera un errore
  echo $email->print_debugger();
}
```



Questo metodo cancellerà automaticamente tutti i parametri se la richiesta ha avuto successo. Per interrompere questo comportamento, passare FALSE:

```php
if  ( $email->send ( false )) 
{ 
  // I parametri non verranno cancellati 
  echo $email->print_debugger();
}
```

*** NOTA: Per utilizzare il metodo print_debugger(), è necessario evitare di cancellare i parametri dell'email..**



------



```php
$email->attach($filename[, $disposition = ''[, $newname = NULL[, $mime = '']]])
```

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$filename** (*string*) – Nome del file <br />**$disposition** (*string*) – "disposizione" dell'allegato. La maggior parte dei client di posta elettronica prende la propria decisione indipendentemente dalla specifica MIME utilizzata qui. https://www.iana.org/assignments/cont-disp/cont-disp.xhtml <br />**$newname** (*string*) – Nome del file personalizzato da utilizzare nell'e-mail<br />**$mime** (*string*) – Tipo MIME da usare (utile per i dati memorizzati nel buffer) |
| **Ritorno**         | Istanza Email (concatenamento di metodi)                     |
| **Tipo di ritorno** | Email                                                        |

Il metodo ti consente di inviare un allegato. Per allegare un documento, inserisci il percorso/nome del file nel primo parametro. 
Per più allegati utilizzare il metodo più volte. Per esempio:

```php
$email->attach('/path/to/photo1.jpg');
$email->attach('/path/to/photo2.jpg');
$email->attach('/path/to/photo3.jpg');
```



Per utilizzare la disposizione predefinita degli allegati, lasciare vuoto il secondo parametro, altrimenti utilizzare una disposizione personalizzata:

```php
$email->attach('image.jpg', 'inline');
```



Puoi anche utilizzare un URL:

```php
$email->attach('http://example.com/filename.pdf');

```



Se desideri utilizzare un nome file personalizzato, puoi utilizzare il terzo parametro:

```php
$email->attach('filename.pdf', 'attachment', 'report.pdf');

```



Se hai bisogno di usare una stringa di buffer invece di un file reale - fisico - puoi usare il primo parametro come buffer, il terzo parametro come nome file e il quarto parametro come tipo mime:

```php
$email->attach($buffer, 'attachment', 'report.pdf', 'application/pdf');

```



------



```php
$email->attachment_cid($filename)
```

| Settaggi            | Descrizione                                            |
| ------------------- | ------------------------------------------------------ |
| **Parametri**       | **$filename** (*string*) –Nome file allegato esistente |
| **Ritorno**         | il Content-ID dell'allegato o FALSE se non trovato     |
| **Tipo di ritorno** | string                                                 |

Imposta e restituisce il Content-ID di un allegato, che consente di incorporare un allegato in linea (immagine) in HTML. Il primo parametro deve essere il nome del file già allegato.

```php
$filename = '/img/photo1.jpg';
$email->attach($filename);

foreach ($list as $address) {
  
        $email->to($address);
        $cid = $email->attachment_cid($filename);
        $email->message('<img src="cid:'. $cid .'" alt="photo1" />');
        $email->send();
  
}
```

*** NOTA: L'ID del contenuto per ogni e-mail deve essere ricreato affinché sia univoco.**



------



```php
$email->print_debugger([$include = array('headers', 'subject', 'body')])
```

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$include** (*array*) – Quali parti del messaggio di errore deve stampare |
| **Ritorno**         | Debug dei dati formattai                                     |
| **Tipo di ritorno** | string                                                       |

Restituisce una stringa contenente tutti i messaggi del server, le intestazioni di posta elettronica e il messaggio di posta elettronica. Utile per il debug.

Facoltativamente, è possibile specificare quali parti del messaggio devono essere stampate. Le opzioni valide sono: **intestazioni** , **oggetto** , **corpo** .

```php
// È necessario passare FALSE durante l'invio affinché i dati dell'email 
// non vengano cancellati - se ciò accade, print_debugger() non avrebbe 
// nulla da visualizzare. 
$email->send(FALSE);

// Stamperà solo le intestazioni delle e-mail, escludendo l'oggetto e il corpo del messaggio 
$email->print_debugger( ['headers'] );

```

*** NOTA: Per impostazione predefinita, verranno stampati tutti i dati "grezzi"..**

