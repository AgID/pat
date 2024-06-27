## Classe `Dir`

La classe `Dir` fornisce un insieme di metodi per la gestione delle directory all'interno di un'applicazione PHP. Questi metodi consentono di creare, verificare l'esistenza, controllare i permessi, eliminare, scansionare, ottenere la dimensione e copiare le directory. Di seguito è riportata una descrizione dettagliata della classe `Dir` che può essere utilizzata all'interno di una documentazione:

**Lista dei metodi**

`create($dir, int $chmod = 0775)`
`exists($dir)`
`checkPerm($dir)`
`delete($dir)`
`scan($dir)`
`writable($path)`
`writable($path)`
`size($path)`

### Metodi

* `create($dir, int $chmod = 0775)`: Crea una nuova directory con i permessi specificati.
  * Parametri:
    * `$dir`: Il percorso della directory da creare.
    * `$chmod` (opzionale): I permessi da assegnare alla directory. Il valore predefinito è `0775`.
  * Restituisce:
    * `bool`: `true` se la directory viene creata con successo o se esiste già, altrimenti `false`.
* `exists($dir)`: Verifica se la directory specificata esiste.
  * Parametri:
    * `$dir`: Il percorso della directory da verificare.
  * Restituisce:
    * `bool`: `true` se la directory esiste, altrimenti `false`.
* `checkPerm($dir)`: Controlla i permessi della directory specificata.
  * Parametri:
    * `$dir`: Il percorso della directory di cui controllare i permessi.
  * Restituisce:
    * `bool|string`: I permessi della directory come stringa se la directory esiste, altrimenti `false`.
* `delete($dir)`: Elimina la directory specificata e tutti i suoi contenuti.
  * Parametri:
    * `$dir`: Il percorso della directory da eliminare.
  * Restituisce:
    * `void`
* `scan($dir)`: Effettua una scansione della directory specificata e restituisce un array contenente i nomi delle sottodirectory presenti.
  * Parametri:
    * `$dir`: Il percorso della directory da scansionare.
  * Restituisce:
    * `array|void`: Un array contenente i nomi delle sottodirectory presenti nella directory specificata.
* `writable($path)`: Verifica se la directory specificata è scrivibile.
  * Parametri:
    * `$path`: Il percorso della directory da verificare.
  * Restituisce:
    * `bool`: `true` se la directory è scrivibile, altrimenti `false`.
* `size($path)`: Calcola la dimensione della directory specificata (compresi tutti i file e le sottodirectory).
  * Parametri:
    * `$path`: Il percorso della directory di cui calcolare la dimensione.
  * Restituisce:
    * `bool|int`: La dimensione della directory in byte.
* `copy($src, $dst)`: Copia la directory specificata in una nuova posizione.
  * Parametri:
    * `$src`: Il percorso della directory da copiare.
    * `$dst`: Il percorso di destinazione in cui copiare la directory.
  * Restituisce:
    * `void`
