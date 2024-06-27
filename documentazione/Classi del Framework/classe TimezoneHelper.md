## Classe `TimezoneHelper`

La classe `TimezoneHelper` fornisce metodi per gestire il fuso orario.

### Proprietà

#### `$instance`

Un'istanza della classe `TimezoneHelper`.

#### `$timezone`

Il fuso orario corrente.

### Metodi

#### `__construct($timezone = null)`

Costruttore della classe `TimezoneHelper`.

* Parametri:
  * `$timezone`: Il fuso orario da utilizzare (opzionale, valore predefinito: fuso orario predefinito del sistema).
* Restituisce: N/A

#### `getInstance($timezone = null)`

Restituisce un'istanza della classe `TimezoneHelper`.

* Parametri:
  * `$timezone`: Il fuso orario da utilizzare (opzionale, valore predefinito: fuso orario predefinito del sistema).
* Restituisce: Un'istanza della classe `TimezoneHelper`.

#### `setTimezone($timezone)`

Imposta il fuso orario.

* Parametri:
  * `$timezone`: Il fuso orario da impostare.
* Restituisce: N/A

#### `getTimezone()`

Restituisce il fuso orario corrente.

* Restituisce: Il fuso orario corrente.

#### `listTimezones()`

Restituisce un array di tutti i fusi orari disponibili.

* Restituisce: Un array di tutti i fusi orari disponibili.

```
use System\TimezoneHelper;

// Ottenimento dell'istanza della classe TimezoneHelper
$timezoneHelper = TimezoneHelper::getInstance();

// Impostazione del fuso orario
$timezoneHelper->setTimezone('Europe/Rome');

// Ottenimento del fuso orario corrente
$timezone = $timezoneHelper->getTimezone();
echo "Fuso orario corrente: " . $timezone;

// Ottenimento della lista dei fusi orari disponibili
$timezones = $timezoneHelper->listTimezones();
echo "Fusi orari disponibili:";
foreach ($timezones as $tz) {
    echo $tz . "<br>";
}
```
Nell'esempio sopra, viene ottenuta un'istanza della classe TimezoneHelper utilizzando il metodo getInstance(). Successivamente, viene impostato il fuso orario utilizzando il metodo setTimezone(). Viene quindi ottenuto il fuso orario corrente utilizzando il metodo getTimezone(). Infine, viene ottenuta la lista dei fusi orari disponibili utilizzando il metodo listTimezones().

L'esempio illustra l'utilizzo della classe TimezoneHelper per gestire il fuso orario in un'applicazione. Si può adattare l'esempio per impostare e ottenere il fuso orario in base alle proprie esigenze.