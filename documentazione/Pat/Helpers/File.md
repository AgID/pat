## Classe File

La classe `File` fornisce un insieme di metodi per la gestione dei file all'interno di un'applicazione. Questi metodi consentono di verificare l'esistenza di un file, eliminarlo, rinominarlo, copiarlo, ottenere informazioni sul file, leggerne il contenuto, impostarne il contenuto e scaricarlo o visualizzarlo nel browser.:

**Lista dei metodi**

`isMultiUpload(string $file_name = 'userfile')`
`exists($filename)`
`delete($filename)`
`rename($from, $to)`
`copy($from, $to)`
`ext($filename)`
`name($filename)`
`scan($folder, $type = null)`
`getContent($filename)`
`setContent($filename, $content, bool $createFile = true, bool $append = false, int $chmod = 0666)`
`lastChange($filename)`
`lastAccess($filename)`
`mime($file, bool $guess = true)`
`download($file, $contentType = null, $filename = null, int $kbps = 0)`
`display($file, $contentType = null, $filename = null)`

### Metodi

* `isMultiUpload(string $file_name = 'userfile')`: Verifica se è stata effettuato l'upload multiplo di file.
  * Parametri:
    * `$file_name` (opzionale): Il nome del campo di input file nell'upload. Il valore predefinito è `'userfile'`.
  * Restituisce:
    * `mixed`: `true` se sono stati caricati più file, altrimenti `false`.
* `exists($filename)`: Verifica se il file specificato esiste.
  * Parametri:
    * `$filename`: Il percorso del file da verificare.
  * Restituisce:
    * `bool`: `true` se il file esiste, altrimenti `false`.
* `delete($filename)`: Elimina il file specificato.
  * Parametri:
    * `$filename`: Il percorso del file da eliminare.
  * Restituisce:
    * `bool|void`: `true` se l'eliminazione avviene con successo, altrimenti `false`.
* `rename($from, $to)`: Rinomina il file specificato.
  * Parametri:
    * `$from`: Il nome originale del file.
    * `$to`: Il nuovo nome da assegnare al file.
  * Restituisce:
    * `bool`: `true` se il file viene rinominato con successo, altrimenti `false`.
* `copy($from, $to)`: Copia il file specificato in una nuova posizione.
  * Parametri:
    * `$from`: Il percorso del file da copiare.
    * `$to`: Il percorso di destinazione in cui copiare il file.
  * Restituisce:
    * `bool`: `true` se la copia avviene con successo, altrimenti `false`.
* `ext($filename)`: Restituisce l'estensione del file specificato.
  * Parametri:
    * `$filename`: Il percorso del file di cui ottenere l'estensione.
  * Restituisce:
    * `bool|string`: L'estensione del file come stringa, oppure `false` se non è presente un'estensione.
* `name($filename)`: Restituisce il nome del file senza estensione.
  * Parametri:
    * `$filename`: Il percorso del file di cui ottenere il nome.
  * Restituisce:
    * `string`: Il nome del file senza estensione.
* `scan($folder, $type = null)`: Effettua una scansione della directory specificata e restituisce un array contenente i nomi dei file presenti.
  * Parametri:
    * `$folder`: La directory da scansionare.
    * `$type` (opzionale): Il tipo di file da filtrare. Può essere una stringa o un array di estensioni. Se non specificato, vengono restituiti tutti i file.
  * Restituisce:
    * `bool|array`: Un array contenente i nomi dei file presenti nella directory specificata.
* `getContent($filename)`: Restituisce il contenuto del file specificato.
  * Parametri:
    * `$filename`: Il percorso del file di cui ottenere il contenuto.
  * Restituisce:
    * `false|string|null`: Il contenuto del file come stringa, oppure `false` se il file non esiste o non è leggibile.
* `setContent($filename, $content, bool $createFile = true, bool $append = false, int $chmod = 0666)`: Imposta il contenuto del file specificato.
  * Parametri:
    * `$filename`: Il percorso del file in cui impostare il contenuto.
    * `$content`: Il contenuto da scrivere nel file.
    * `$createFile` (opContinuazione della descrizione della classe `File`:
* `setContent($filename, $content, bool $createFile = true, bool $append = false, int $chmod = 0666)`: Imposta il contenuto del file specificato.
  * Parametri:
    * `$filename`: Il percorso del file in cui impostare il contenuto.
    * `$content`: Il contenuto da scrivere nel file.
    * `$createFile` (opzionale): Specifica se creare il file se non esiste. Il valore predefinito è `true`.
    * `$append` (opzionale): Specifica se aggiungere il contenuto al file esistente. Il valore predefinito è `false`, che sovrascrive il contenuto esistente.
    * `$chmod` (opzionale): I permessi da assegnare al file. Il valore predefinito è `0666`.
  * Restituisce:
    * `bool`: `true` se il contenuto viene impostato con successo, altrimenti `false`.
* `lastChange($filename)`: Restituisce la data di ultima modifica del file specificato.
  * Parametri:
    * `$filename`: Il percorso del file di cui ottenere la data di ultima modifica.
  * Restituisce:
    * `bool|int`: La data di ultima modifica del file come timestamp UNIX, oppure `false` se il file non esiste o non è leggibile.
* `lastAccess($filename)`: Restituisce la data di ultimo accesso al file specificato.
  * Parametri:
    * `$filename`: Il percorso del file di cui ottenere la data di ultimo accesso.
  * Restituisce:
    * `bool|int`: La data di ultimo accesso al file come timestamp UNIX, oppure `false` se il file non esiste o non è leggibile.
* `mime($file, bool $guess = true)`: Restituisce il tipo MIME del file specificato.
  * Parametri:
    * `$file`: Il percorso del file di cui ottenere il tipo MIME.
    * `$guess` (opzionale): Specifica se indovinare il tipo MIME basandosi sull'estensione del file se non disponibile. Il valore predefinito è `true`.
  * Restituisce:
    * `false|string|null`: Il tipo MIME del file come stringa, oppure `false` se non è possibile determinare il tipo MIME.
* `download($file, $contentType = null, $filename = null, int $kbps = 0)`: Avvia il download del file specificato e lo invia al browser come allegato.
  * Parametri:
    * `$file`: Il percorso del file da scaricare.
    * `$contentType` (opzionale): Il tipo di contenuto del file. Se non specificato, viene determinato automaticamente.
    * `$filename` (opzionale): Il nome del file da visualizzare durante il download.
    * `$kbps` (opzionale): La velocità di download in kilobit al secondo. Se impostato a `0`, il download avviene a velocità massima.
  * Restituisce:
    * `void`
* `display($file, $contentType = null, $filename = null)`: Mostra il contenuto del file specificato direttamente nel browser.
  * Parametri:
    * `$file`: Il percorso del file da visualizzare.
    * `$contentType` (opzionale): Il tipo di contenuto del file. Se non specificato, viene determinato automaticamente.
    * `$filename` (opzionale): Il nome del file da visualizzare nel browser.
  * Restituisce:
    * `void`
