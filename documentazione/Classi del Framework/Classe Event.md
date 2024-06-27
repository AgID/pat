## Classe `Event`

La classe `Event` gestisce la registrazione e l'esecuzione dei gestori di eventi. Questa classe fornisce metodi per aggiungere gestori di eventi, eseguire i gestori registrati per un evento specifico e verificare l'esistenza di gestori per un evento.

### Metodi

* `add($name, $callback)`: Aggiunge un nuovo gestore di eventi associato a un nome specificato.
  * Parametri:
    * `$name`: Il nome dell'evento.
    * `$callback`: La funzione di callback o il nome della classe che gestisce l'evento.
  * Restituisce: `void`
* `call($name, $params)`: Esegue tutti i gestori di eventi registrati per un determinato nome di evento.
  * Parametri:
    * `$name`: Il nome dell'evento.
    * `$params`: Un array di parametri da passare ai gestori di eventi.
  * Restituisce: `bool`
* `exists($name)`: Verifica se esistono gestori di eventi registrati per un determinato nome di evento.
  * Parametri:
    * `$name`: Il nome dell'evento.
  * Restituisce: `bool`
* `e(&$var, $alternate)`: Verifica se una variabile è impostata e restituisce il suo valore. Se la variabile non è impostata, restituisce un valore alternativo.
  * Parametri:
    * `&$var`: La variabile da verificare.
    * `$alternate`: Il valore alternativo da restituire se la variabile non è impostata.
  * Restituisce: Il valore della variabile o il valore alternativo.

### Utilizzo

Per utilizzare la classe `Event`, puoi aggiungere gestori di eventi utilizzando il metodo `add()`, eseguire gli eventi registrati utilizzando il metodo `call()` e verificare l'esistenza di gestori utilizzando il metodo `exists()`.

Ecco un esempio di utilizzo della classe `Event`:

```
useSystem\Event;

// Aggiunta di un gestore di eventi utilizzando una funzione di callback
Event::add('my_event', function ($param1, $param2) {
echo"Evento chiamato con parametri: $param1, $param2";
});

// Esecuzione dell'evento con i parametri specificati
Event::call('my_event', ['valore1', 'valore2']);
```

Nell'esempio sopra, viene aggiunto un gestore di eventi per l'evento "my_event" utilizzando una funzione di callback. Successivamente, viene eseguito l'evento con i parametri specificati. Il gestore di eventi registrato viene eseguito e stamperà i parametri passati.

Puoi anche aggiungere gestori di eventi utilizzando il nome di una classe che implementa un metodo `handle`:

```
useSystem\Event;

classMyEventHandler
{
publicfunctionhandle($param1, $param2)
{
echo"Evento chiamato con parametri: $param1, $param2";
    }
}

// Aggiunta di un gestore di eventi utilizzando il nome della classe
Event::add('my_event', MyEventHandler::class);

// Esecuzione dell'evento con i parametri specificati
Event::call('my_event', ['valore1', 'valore2']);
```

Nell'esempio sopra, viene aggiunto un gestore di eventi utilizzando il nome della classe `MyEventHandler`. La classe `MyEventHandler` contiene un metodo `handle` che viene eseguito quando l'evento viene chiamato.

La classe `Event` fornisce un modo flessibile per gestire gli eventi nell'applicazione. Puoi registrare uno o più gestori di eventi per un determinato evento e chiamarli quando necessario.
