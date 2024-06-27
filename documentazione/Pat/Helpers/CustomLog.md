## Classe `CustomLog`

La classe `CustomLog` estende la classe `Log` e fornisce metodi personalizzati per la gestione dei log.

### Metodi

`write($notify = 'INFO', $log = '', $exit = false): void`: Il metodo write scrive il log personalizzato.

* Parametri:
  * `$notify`: (Opzionale) Il livello di notifica del log.
  * `$log`: (Opzionale) Il messaggio del log.
  * `$exit`: (Opzionale) Un flag booleano che indica se terminare l'esecuzione dello script dopo il log.

```
use Helpers\CustomLog;

// Esempio di utilizzo del metodo write
CustomLog::write('INFO', 'Messaggio di log');

// Esempio di utilizzo del metodo write con terminazione dello script
CustomLog::write('WARNING', 'Messaggio di log', true);
```
