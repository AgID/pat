## Classe `XmlResponse`

La classe `XmlResponse` consente di creare una risposta in formato XML a partire da un array di dati.

### Metodi

#### `__construct($data)`

Costruttore della classe `XmlResponse`.

* Parametri:
  * `$data`: Un array associativo contenente i dati da convertire in XML.
* Restituisce: Una stringa che rappresenta la risposta in formato XML.

### Metodo Protetto

#### `arrayToXML(array $data, &$output)`

Converte un array in un formato XML utilizzando l'oggetto `SimpleXMLElement`.

* Parametri:
  * `$data`: Un array associativo contenente i dati da convertire in XML.
  * `$output`: Un oggetto `SimpleXMLElement` che rappresenta l'output XML.
* Restituisce: Null.

Il metodo `arrayToXML()` converte ricorsivamente un array associativo in un formato XML utilizzando l'oggetto `SimpleXMLElement`. Viene utilizzato internamente dal costruttore per generare la risposta XML.

```
use System\XmlResponse;

// Dati da convertire in XML
$data = [
    'name' => 'John Doe',
    'age' => 30,
    'email' => 'johndoe@example.com'
];

// Creazione di un'istanza della classe XmlResponse
$xmlResponse = new XmlResponse($data);

// Ottenimento della risposta XML
$xml = $xmlResponse->xml;

// Stampa della risposta XML
header('Content-Type: application/xml');
echo $xml;
```
Nell'esempio sopra, viene creato un'istanza della classe XmlResponse passando un array di dati da convertire in formato XML. Successivamente, viene ottenuta la risposta XML utilizzando la proprietà $xml dell'istanza. Infine, la risposta XML viene stampata con l'header corretto per specificare il tipo di contenuto come XML.

L'esempio illustra come utilizzare la classe XmlResponse per generare una risposta XML a partire da un array di dati. La risposta XML può quindi essere inviata al client o utilizzata come necessario.