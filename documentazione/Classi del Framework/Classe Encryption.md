## Classe `Encryption`

La classe `Encryption` della libreria di CodeIgniter 3 fornisce funzionalità di crittografia bidirezionale dei dati. Per utilizzare questa classe, è necessario soddisfare alcune dipendenze legate alle estensioni PHP OpenSSL o MCrypt.

## Inizializzazione della classe

Per utilizzare la classe `Encryption`, è necessario caricarla nel controller utilizzando il metodo `$this->load->library('encryption')`.

```
$encryption = new Encryption()
```

Una volta caricata, l'oggetto della libreria di crittografia sarà disponibile tramite `$this->encryption`.

## Configurazione della chiave di crittografia

È necessario configurare una chiave di crittografia per utilizzare la classe `Encryption`. La chiave di crittografia deve essere una stringa casuale e sicura. Puoi generare una chiave casuale utilizzando il metodo `create_key()` della classe `Encryption`.

```
$key = $encryption->create_key(16);
```

## Crittografia dei dati

Per crittografare i dati, puoi utilizzare il metodo `encrypt()` della classe `Encryption`.

```
$data = 'Hello, World!';
$encryptedData = $encryption->encrypt($data);
```

Il metodo `encrypt()` restituirà i dati crittografati come una stringa.

## Decrittografia dei dati

Per decrittografare i dati crittografati, puoi utilizzare il metodo `decrypt()` della classe `Encryption`.

```
$decryptedData = $encryption->decrypt($encryptedData);
```

Il metodo `decrypt()` restituirà i dati decrittografati come una stringa.

È importante gestire gli errori che possono verificarsi durante la crittografia e la decrittografia dei dati, controllando i valori restituiti da questi metodi. In caso di errore, i metodi `encrypt()` e `decrypt()` restituiranno `FALSE`.

Questa è una panoramica di base sull'utilizzo della classe `Encryption` di CodeIgniter 3. Puoi fare riferimento alla documentazione ufficiale di CodeIgniter per ulteriori dettagli e opzioni avanzate.

```
// Caricamento della libreria di crittografia
$encryption= new Encryption());

// Definizione dei dati da crittografare
$data = 'Hello, World!';

// Crittografia dei dati
$encryptedData = $encryption->encrypt($data);

if ($encryptedData !== false) {
    // Decrittografia dei dati
    $decryptedData = $encryption->decrypt($encryptedData);
  
    if ($decryptedData !== false) {
        // I dati sono stati decrittografati correttamente
        echo "Dati decrittografati: " . $decryptedData;
    } else {
        // Errore nella decrittografia dei dati
        echo "Errore nella decrittografia dei dati.";
    }
} else {
    // Errore nella crittografia dei dati
    echo "Errore nella crittografia dei dati.";
}
```


Nell'esempio sopra, viene caricata la libreria `Encryption`, successivamente, viene definito un dato da crittografare nella variabile `$data`. Viene utilizzato il metodo `encrypt()` per crittografare i dati e il metodo `decrypt()` per decrittografarli.

È importante gestire gli errori che possono verificarsi durante la crittografia e la decrittografia dei dati, controllando i valori restituiti dai metodi `encrypt()` e `decrypt()`. In caso di successo, i dati decrittografati vengono stampati a schermo.
