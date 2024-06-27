## Classe `Addons`

La classe `Addons` fornisce funzionalità per la gestione degli addons nel sistema Pat OS.

### Metodi

`init(): void`: Il metodo init carica tutti gli addons.

`initJobs(): void`: Il metodo init carica tutti gli addons nei JOB.

`isActive($str = null): bool|array`: Il metodo isActive verifica se un addon è attivo.

* Parametri:
  * `$str`: (Opzionale) Il nome dell'addon da verificare.
* Restituisce false se l'addon non è attivo, altrimenti restituisce un array con i dettagli dell'addon.

`loadModels(): void`: Il metodo loadModels carica i modelli degli addons.

`config($key = null, $pluginName = null, $fileName = null): mixed`: Il metodo config restituisce una configurazione specifica per un addon.

* Parametri:
  * `$key`: (Opzionale) La chiave della configurazione da recuperare.
  * `$pluginName`: Opzionale) Il nome dell'addon.
  * `$fileName`: (Opzionale) Il nome del file di configurazione.
* Restituisce il valore della configurazione specificata.

`path($folder = null): string`: Il metodo path restituisce il percorso della cartella di un addon.

* Parametri:
  * `$folder`: (Opzionale) Il nome della cartella dell'addon.
