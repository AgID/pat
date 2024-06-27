## Classe `DateFormatter`

La classe `DateFormatter` nella namespace `Helpers` fornisce metodi per formattare le date in diversi formati.

### Costanti

* `DATETIME_FORMAT`: Il formato data e ora completa.
* `DATE_FORMAT`: Il formato solo data.
* `DATE_NO_YEAR_FORMAT`: Il formato data senza anno.
* `CUSTOM_FORMAT`: Il formato personalizzato della data.
* `DATE_FORMAT_NO_HOUR`: Il formato data senza ora.

### Metodi

`formatDate(string $dateString, string $format = self::DATETIME_FORMAT, string $separatorDate = "/", string $separatorTime = "-"): string`: Il metodo formatDate formatta una stringa di data in un formato personalizzato.

* Parametri:
  * `$dateString`: La stringa di data da formattare.
  * `$format`: (Opzionale) Il formato di uscita della data.
  * `$separatorDate`: (Opzionale) Il separatore della data per il formato personalizzato.
  * `$separatorTime`: (Opzionale) Il separatore dell'ora per il formato personalizzato.
* Restituisce una stringa formattata in base al formato richiesto.


```
use Helpers\DateFormatter;

// Esempio 1: Formattazione di una data in formato data e ora completa
$dateString = '2022-03-24 15:30:00';
$formattedDate = DateFormatter::formatDate($dateString);
echo "Data formattata: " . $formattedDate . "\n";

// Esempio 2: Formattazione di una data in formato solo data
$dateString = '2022-03-24 15:30:00';
$formattedDate = DateFormatter::formatDate($dateString, DateFormatter::DATE_FORMAT);
echo "Data formattata: " . $formattedDate . "\n";

// Esempio 3: Formattazione di una data in formato data senza anno
$dateString = '2022-03-24 15:30:00';
$formattedDate = DateFormatter::formatDate($dateString, DateFormatter::DATE_NO_YEAR_FORMAT);
echo "Data formattata: " . $formattedDate . "\n";

// Esempio 4: Formattazione di una data in formato personalizzato
$dateString = '2022-03-24 15:30:00';
$formattedDate = DateFormatter::formatDate($dateString, DateFormatter::CUSTOM_FORMAT, ".", ":");
echo "Data formattata: " . $formattedDate . "\n";

// Esempio 5: Formattazione di una data senza specificare il formato
$dateString = '2022-03-24 15:30:00';
$formattedDate = DateFormatter::formatDate($dateString);
echo "Data formattata: " . $formattedDate . "\n";
```
