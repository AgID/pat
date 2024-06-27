# Classe per il caricamento dei file (Upload)

**Riferimento path sorgente classe upload:** *core/System/Upload.php*

La classe del Framework consente il caricamento dei file. È possibile impostare varie preferenze, limitando il tipo e la dimensione dei file.

#### **Processo del caricamento**

Il caricamento di un file implica il seguente processo generale:

- Viene visualizzato un modulo di caricamento, che consente a un utente di selezionare un file e caricarlo;
- Quando il modulo viene inviato, il file viene caricato nella destinazione specificata;
- Nel processo di caricamento, il file viene convalidato per assicurarsi che possa essere caricato in base alle preferenze impostate;
-  Una volta terminato il processo, all'utente verrà mostrato un messaggio di successo oppure di errore;

##### Esempio pratico del caricamento di un file sul server:

1) Nella vista creare un modulo contenente un campo di input di tipo file. Come il Seguente esempio:

```php+HTML
<html>
  <head>
    <title>Test Upload Form</title>
  </head>
<body>
  
  <!-- Se la classe di upload scatenza un errore lo stampo a video -->
  <?php if(!empty($displayErrors)):?>
		<?php echo $displayErrors;?>
  <?php endif;?>
  
  <!-- Se l'upload è andato a buon fine stampo i dati del file caricato  -->
  <?php if(!empty($succesUploadData) && is_array($succesUploadData) ):?>
    <ul>
      <?php foreach ($succesUploadData as $item => $value):?>
        <li><?php echo $item;?>: <?php echo $value;?></li>
      <?php endforeach; ?>
    </ul>
  <?php endif;?>
  
  <!-- Vista che contiene il modulo per il caricamento di un file--> 
	<form action="<?php echo siteUrl('/upload') ?>" 
        method="POST" enctype="multipart/form-data">
    
    <input type="file" name="upload" size="20" />
    <br />
    <input type="submit" value="upload" />
    
	</form>
  
</body>
</html>
```

2) Creare un controller per istanziare la classe per il caricamento dei file

```php
<?php
/**
 * Homepage sito web
 */

namespace Http;

defined('_FRAMEWORK_') OR exit('No direct script access allowed');

// Controller di test per testare la classe di caricamento dei file
class HomeController
{
    public function __construct()
    {
				parent::__contruct();
    }

    /**
     * Text Layout
     */
    public function index()
    {
      	// inizializzo nell'array che nella vista sarà una variabile di errore a NULL
      	$data['displayErrors'] = null;
      
      	// inizializzo nell'array che nella vista sarà una variabile di errore a NULL
      	$data['succesUploadData'] = null;
            
      	// Instanzio la classe di upload
      	// E' anche possibile passare i paramentri di configurazione 
      	// all'inizializzazione della classe.
      	// Come nell'esempio corrente:  new \system\Uploads($config)
        $upload = new \system\Uploads();

      	// Verifico se il campop di file con il nome "upload" è settato
      	if(!empty(\System\Input::files('upload'))) {
          
          // Configuro i parametri per l'upload del file
          $config['upload_path'] = './media/'; // Caretlla media nella root principale
          $config['allowed_types'] = 'png|jpeg|gif'; // quale ext sono permesse
          $config['encrypt_name'] = true; // rinomino il file dopo l'upload
          $config['file_ext_tolower'] = true; // scrivo in minuscolo estensione del file
          
          // Setto i paremtri di configurazione
          $upload->initialize($config);

          // Avvio l'upload del file chiamando il nome del file di input nel 
          // metodo $upload->doUpload()
          if ($upload->doUpload('upload')) {
            
            	// Upload è andato a buon fine, prendo i dati dopo l'upload
              $data['succesUploadData'] = $upload->data());
            
          } else {
            
            	// Se qualcosa è andato storto stampo l'errore nella vista
              $data['displayErrors'] = $upload->displayErrors();
            
          }
         
        }
      	
      	// Carico la vista del Template Engine.
        \System\Layout::view('home', $data);
    }
}
```

In questo esempio la cartella per l'upload dei files è chiamata media posizionata nella root primcipale della cartella del vhost, ed ha i permessi su 777.



#### Parametri di settaggi che si possono impostare nella classe di upload

Sono disponibili le seguenti preferenze. Il valore predefinito indica cosa verrà utilizzato se non si specifica tale preferenza.

| Preferenze settaggi        | Valori di default | Optioni              | Descrizione                                                  |
| -------------------------- | ----------------- | -------------------- | ------------------------------------------------------------ |
| **upload_path**            | No                | No                   | Il percorso della directory in cui deve essere posizionato il caricamento. La directory deve essere scrivibile e il percorso può essere assoluto o relativo. |
| **allowed_types**          | No                | No                   | I tipi MIME corrispondenti ai tipi di file consentiti al caricamento. Di solito l'estensione del file può essere utilizzata come tipo MIME. La direttiva impostata può essere un array o una stringa separata da pipe. |
| **file_name**              | None              | Nome file desiderato | Se impostato nel Framework, rinominerà il file caricato con questo nome. Anche l'estensione fornita nel nome file deve essere un tipo di file consentito. Se non viene fornita alcuna estensione, verrà utilizzato il nome file originale. |
| **file_ext_tolower**       | FALSE             | TRUE/FALSE (boolean) | Se impostato su TRUE, l'estensione del file sarà forzata con caratteri in minuscolo. |
| **overwrite**              | FALSE             | TRUE/FALSE (boolean) | Se impostato su true, ed esiste un file con lo stesso nome di quello che stai caricando, verrà sovrascritto. Se impostato su false, verrà aggiunto un numero sul file caricato al fine di non sovrascrivere i file con lo stesso nome. |
| **max_size**               | 0                 | No                   | La dimensione massima (in kilobyte) che può avere il file. Impostare a zero per nessun limite. Nota: la maggior parte delle installazioni PHP ha il proprio limite, come specificato nel file php.ini. Di solito 2 MB (o 2048 KB) per impostazione predefinita. |
| **max_width**              | 0                 | No                   | La larghezza massima (in pixel) che può avere l'immagine. Se si imposta a zero non ha nessun limite. |
| **max_height**             | 0                 | No                   | L'altezza massima (in pixel) che può avere l'immagine. Se si imposta a zero non ha nessun limite. |
| **min_width**              | 0                 | No                   | La larghezza minima (in pixel) che può avere l'immagine. Impostare a zero per nessun limite. |
| **min_height**             | 0                 | No                   | L'altezza minima (in pixel) che può avere l'immagine. Impostare a zero per nessun limite. |
| **max_filename**           | 0                 | No                   | La lunghezza massima dei caratteri che può avere un nome del file. Impostare a zero per nessun limite. |
| **max_filename_increment** | 100               | No                   | Quando la sovrascrittura è impostata su FALSE, si può usere per impostare l'incremento massimo del nome del file che il Framework deve aggiungere al nome del file. |
| **encrypt_name**           | FALSE             | TRUE/FALSE (boolean) | Se impostato su TRUE, il nome del file verrà convertito in una stringa crittografata casuale. Questo può essere utile se desideri che il file venga salvato con un nome che non può essere individuato dalla persona che lo carica. |
| **remove_spaces**          | TRUE              | TRUE/FALSE (boolean) | Se impostato su TRUE, eventuali spazi nel nome del file verranno convertiti in trattini bassi. **Questo è consigliato.** |
| **detect_mime**            | TRUE              | TRUE/FALSE (boolean) | Se impostato su TRUE, verrà eseguito un rilevamento lato server del tipo di file al fine di evitare attacchi di iniezione di codice. **NON** disabilitare questa opzione a meno che non si  abbia un alternativa valida, in quanto ciò potrebbe causare un rischio per la sicurezza. |
| **mod_mime_fix**           | TRUE              | TRUE/FALSE (boolean) | Se impostato su TRUE, a più estensioni di file verrà aggiunto un carattere di sottolineatura per evitare l'attivazione di un possibile  [mod_mime](https://httpd.apache.org/docs/2.0/mod/mod_mime.html#multipleext). **NON** disattivare questa opzione se la directory di caricamento è pubblica, poiché questo è un rischio per la sicurezza. |



#### Riferimenti della classe.



```php
$upload->initialize([array $config = array()[, $reset = TRUE]]);
```

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$config** (*array*) – Parametri di gonfigurazione upload<br />**$reset** (*bool*) – Se ripristinare le preferenze (che non sono fornite in $config) ai valori predefiniti |
| **Ritorno**         | Istanza della classe di upload instance (metodo di concatenamento) |
| **Tipo di ritorno** | Nessun ritorno                                               |



------



```php
$upload->doUpload([$field = 'userfile']);
```

| Settaggi            | Descrizione                                           |
| ------------------- | ----------------------------------------------------- |
| **Parametri**       | **$field** (*string*) – Name of the form field        |
| **Ritorno**         | VERO in caso di successo, FALSO in caso di fallimento |
| **Tipo di ritorno** | bool                                                  |

Esegue il caricamento in base alle preferenze che hai impostato.

**Per impostazione predefinita, la routine di caricamento prevede che il file provenga da un campo del modulo denominato "userfile" e il modulo deve essere di tipo "multipart".**

```html
<form method="post" action="some_action" enctype="multipart/form-data" />
```

Se desideri impostare il tuo nome di campo, passa semplicemente il suo valore al metodo doUpload():

```php
$upload->doUpload('upload');
```



------



```php
$upload->displayErrors([$open = '<p>'[, $close = '</p>']])
```

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$open** (*string*) –  Markup in html di apertura tag<br />**$close** (*string*) – Markup in html di chiusura tag |
| **Ritorno**         | Messaggi di errore formattati con tag html                   |
| **Tipo di ritorno** | String                                                       |

Recupera eventuali messaggi di errore se il metodo doUpload () ha restituito false. Il metodo non viene visualizzato automaticamente, restituisce i dati in modo che tu possa assegnarli come preferisci.

**Formattazione messaggi di errore:**
Per impostazione predefinita, il metodo precedente racchiude eventuali errori all'interno dei tag <p>. Puoi impostare i tuoi delimitatori in questo modo:

```php
$upload->displayErrors('<div clas ="erro_file_upload">',</div>');
```



------



```php
$upload->data([$index = NULL])
```

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$data** (*string*) –  Elemento da restituire al posto dell'intero array |
| **Ritorno**         | Informazioni sul file caricato                               |
| **Tipo di ritorno** | mixed                                                        |

Questo è un metodo di supporto che restituisce un array contenente tutti i dati relativi al file caricato. 
**Ecco un esempio del ritorno del metodo:**

```php
Array
(
        [file_name]     => mypic.jpg
        [file_type]     => image/jpeg
        [file_path]     => /path/to/your/upload/
        [full_path]     => /path/to/your/upload/jpg.jpg
        [raw_name]      => mypic
        [orig_name]     => mypic.jpg
        [client_name]   => mypic.jpg
        [file_ext]      => .jpg
        [file_size]     => 22.2
        [is_image]      => 1
        [image_width]   => 800
        [image_height]  => 600
        [image_type]    => jpeg
        [image_size_str] => width="800" height="200"
)
```

Per restituire un elemento dall'array:

```php
$upload->data('file_name'); // Ritorna l'elemento "file_name" dell'array per un'immagine caricata: mypic.jpg
```

Tabella che descrive gli elementi dell'array sopra visualizzati:

| Indice array       | Descrizione                                                  |
| ------------------ | ------------------------------------------------------------ |
| **file_name**      | Nome del file che è stato caricato, inclusa l'estensione.    |
| **file_type**      | Identifica il tipo MIME del file                             |
| **file_path**      | Percorso server assoluto del file                            |
| **full_path**      | Percorso del server assoluto, incluso il nome del file       |
| **raw_name**       | Nome del file, senza estensione                              |
| **orig_name**      | Nome file originale. Ciò è utile solo se si utilizza l'opzione del nome crittografato. |
| **client_name**    | Nome file fornito dal "client user agent"  sanificato,       |
| **file_ext**       | Estensione del nome del file.                                |
| **file_size**      | Dimensioni del file in kilobyte                              |
| **is_image**       | Se il file è un'immagine o meno. 1 = immagine. 0 = no.       |
| **image_width**    | Larghezza dell'immagine.                                     |
| **image_height**   | Altezza dell'immagine.                                       |
| **image_type**     | Tipo di immagine (di solito l'estensione del nome del file senza punto). |
| **image_size_str** | Una stringa contenente la larghezza e l'altezza (utile da inserire in un tag immagine). |



