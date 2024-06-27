## Classe `BuilderWithLogs`

La classe `BuilderWithLogs` estende la classe `Illuminate\Database\Eloquent\Builder` e fornisce metodi aggiuntivi per aggiornare, creare e eliminare elementi con la registrazione dei log.

### Metodi

#### `updateWithLogs($element, array $attributes = [], bool $log = true)`

Aggiorna un elemento con la registrazione dei log.

##### Parametri

* `$element`: L'elemento da aggiornare.
* `$attributes` (opzionale): Gli attributi da aggiornare.
* `$log` (opzionale): Indica se registrare il log dell'aggiornamento. Il valore predefinito è `true`.

##### Restituisce

* `mixed`: Il risultato dell'aggiornamento restituito dal modello.

#### `createWithLogs(array $options = [])`

Crea un nuovo elemento con la registrazione dei log.

##### Parametri

* `$options` (opzionale): Opzioni per la creazione dell'elemento.

##### Restituisce

* `mixed`: Il risultato della creazione restituito dal modello.

#### `deleteWithLogs($element)`

Elimina un elemento con la registrazione dei log.

##### Parametri

* `$element`: L'elemento da eliminare.

##### Restituisce

* `mixed`: Il risultato dell'eliminazione restituito dal modello.


