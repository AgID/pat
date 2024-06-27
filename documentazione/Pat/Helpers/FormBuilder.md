## Classe `FormBuilder`

La classe `FormBuilder` nella namespace `Helpers` fornisce metodi per la generazione di form HTML.

### Lista delle proprietà

* `$data`: I dati del form.
* `$ext`: L'estensione del file di dati del form.
* `$type`: Il tipo di dati del form.
* `$inputPermissions`: Un array che contiene i nomi delle funzioni consentite per la generazione degli input del form.
* `$parseData`: I dati per l'elaborazione delle variabili del form.
* `$token`: Un flag booleano che indica se includere il token CSRF nel form.

### Lista dei metodi

* `__construct($parseData = null, $token = true)`: Costruttore della classe `FormBuilder`.
* `loadArrayFromFile($fileName)`: Carica i dati del form da un file in formato array.
* `loadArrayFromVar($data = null)`: Carica i dati del form da una variabile in formato array.
* `loadJsonFromVar($data = null)`: Carica i dati del form da una variabile in formato JSON.
* `loadJsonFromFile($filePath)`: Carica i dati del form da un file in formato JSON o array.
* `getDataFromArray()`: Ottiene i dati del form in formato array.
* `getDataFromJson($type = null)`: Ottiene i dati del form in formato JSON.
* `loadFromString($string)`: Carica i dati del form da una stringa.
* `render()`: Genera il codice HTML del form.
* `display()`: Visualizza il codice HTML del form.

### Lista dei metodi

`__construct($parseData = null, $token = true): void`: Il costruttore della classe FormBuilder inizializza l'istanza della classe.

* Parametri:
  * `$parseData`: (Opzionale) I dati per l'elaborazione delle variabili del form.
  * `$token`: (Opzionale) Un flag booleano che indica se includere il token CSRF nel form.

`loadArrayFromFile($fileName): FormBuilder`: Il metodo loadArrayFromFile carica i dati del form da un file in formato array.

* Parametri:
  * `$fileName`: Il nome del file da cui caricare i dati del form.
* Restituisce l'istanza corrente della classe FormBuilder.

`loadArrayFromVar($data = null): FormBuilder`: Il metodo loadArrayFromVar carica i dati del form da una variabile in formato array.

* Parametri:
  * `$data`: (Opzionale) I dati del form da una variabile in formato array.
* Restituisce l'istanza corrente della classe FormBuilder.

`loadJsonFromVar($data = null): FormBuilder`: Il metodo loadJsonFromVar carica i dati del form da una variabile in formato JSON.

* Parametri:
  * `$data`: (Opzionale) I dati del form da una variabile in formato JSON.
* Restituisce l'istanza corrente della classe FormBuilder.

`loadJsonFromFile($filePath): FormBuilder`: Il metodo loadJsonFromFile carica i dati del form da un file in formato JSON o array.

* Parametri:
  * `$filePath`: Il percorso del file da cui caricare i dati del form.
* Restituisce l'istanza corrente della classe FormBuilder.

`getDataFromArray(): array|null`: Il metodo getDataFromArray ottiene i dati del form in formato array.

* Restituisce un array che rappresenta i dati del form, o null se non sono stati caricati dati.

`getDataFromJson($type = null): string|null`: Il metodo getDataFromJson ottiene i dati del form in formato JSON.

* Parametri:
  * `$type`: (Opzionale) Il tipo di formattazione JSON da utilizzare.
* Restituisce una stringa che rappresenta i dati del form in formato JSON, o null se i dati non sono stati caricati.

`loadFromString($string): void`: Il metodo loadFromString carica i dati del form da una stringa.

* Parametri:
  * `$string`: (Opzionale) La stringa contenente i dati del form.
* Restituisce una stringa che rappresenta i dati del form in formato JSON, o null se i dati non sono stati caricati.

`render(): string`: Il metodo render genera il codice HTML del form.

* Restituisce una stringa che rappresenta il codice HTML del form generato.

`display(): void`: Il metodo display visualizza il codice HTML del form.

* Restituisce una stringa che rappresenta il codice HTML del form generato.

```
use Helpers\FormBuilder;

// Creazione di un'istanza di FormBuilder
$formBuilder = new FormBuilder();

// Caricamento dei dati del form da un file in formato array
$formBuilder->loadArrayFromFile('form_data');

// Generazione del codice HTML del form
$formHtml = $formBuilder->render();
echo $formHtml;

// Caricamento dei dati del form da una variabile in formato JSON
$jsonData = '{
    "name": "John Doe",
    "email": "johndoe@example.com",
    "phone": "1234567890"
}';
$formBuilder->loadJsonFromVar($jsonData);

// Generazione del codice HTML del form
$formHtml = $formBuilder->render();
echo $formHtml;
```
