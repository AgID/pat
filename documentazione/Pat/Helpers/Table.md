## Classe `Table`

La classe `Table`  fornisce metodi per la generazione di tabelle HTML.

### Proprietà

* `$rows`: Un array che rappresenta le righe della tabella.
* `$heading`: Un array che rappresenta l'intestazione della tabella.
* `$auto_heading`: Un flag booleano che indica se generare automaticamente l'intestazione della tabella.
* `$caption`: Una stringa che rappresenta la didascalia della tabella.
* `$template`: Un array che rappresenta il template utilizzato per la generazione della tabella.
* `$newline`: Una stringa che rappresenta il carattere di nuova linea.
* `$empty_cells`: Una stringa che rappresenta il valore da utilizzare per le celle vuote.
* `$function`: Una funzione di callback da utilizzare per la manipolazione del contenuto delle celle.

### Lista dei metodi

* `__construct($config = array())`: Costruttore della classe.
* `set_template($template)`: Imposta il template per la generazione della tabella.
* `set_heading($args = array())`: Imposta l'intestazione della tabella.
* `make_columns($array = array(), $col_limit = 0)`: Genera le colonne della tabella a partire da un array.
* `set_empty($value)`: Imposta il valore da utilizzare per le celle vuote.
* `add_row($args = array())`: Aggiunge una riga alla tabella.
* `set_caption($caption)`: Imposta la didascalia della tabella.
* `generate($table_data = NULL)`: Genera il codice HTML per la tabella.
* `clear()`: Resetta la tabella eliminando righe e intestazione.
* `_set_from_db_result($object)`: Imposta i dati della tabella a partire da un oggetto `CI_DB_result`.
* `_set_from_array($data)`: Imposta i dati della tabella a partire da un array.
* `_compile_template()`: Compila il template per la generazione della tabella.
* `_default_template()`: Restituisce il template di default per la generazione della tabella.


### Metodi
`__construct($config = array()): void`: Il costruttore della classe Table inizializza la configurazione della tabella.
* Parametri:
  * `$config`: Opzionale) Un array di configurazione per la tabella.

`set_template($template): bool`: Il metodo set_template imposta il template utilizzato per la generazione della tabella.
* Parametri:
  * `$template`: L'array di template per la generazione della tabella.
* Restituisce true se il template è stato impostato correttamente, altrimenti false.

`set_heading($args = array()): Table`: Il metodo set_heading imposta l'intestazione della tabella.
* Parametri:
  * `$args`: (Opzionale) Gli argomenti per l'intestazione della tabella.
* Restituisce l'istanza corrente della classe Table.

`make_columns($array = array(), $col_limit = 0): array|false`: Il metodo make_columns genera le colonne della tabella a partire da un array.
* Parametri:
  * `$array`: (Opzionale) L'array di dati per la generazione delle colonne.
  * `$col_limit`: (Opzionale) Il limite massimo di colonne per riga.
* Restituisce un array di colonne generate, oppure false se i parametri non sono validi.

`set_empty($value): Table`: Il metodo set_empty imposta il valore da utilizzare per le celle vuote della tabella.
* Parametri:
  * `$value`: Il valore da utilizzare per le celle vuote.
* Restituisce l'istanza corrente della classe Table.

`add_row($args = array()): Table`: Il metodo add_row aggiunge una riga alla tabella.
* Parametri:
  * `$args`: (Opzionale) Gli argomenti per la riga da aggiungere.
* Restituisce l'istanza corrente della classe Table.

`set_caption($caption): Table`: Il metodo set_caption imposta la didascalia della tabella.
* Parametri:
  * `$caption`: La didascalia da impostare.
* Restituisce l'istanza corrente della classe Table.

`generate($table_data = NULL): string`: Il metodo generate genera il codice HTML per la tabella.
* Parametri:
  * `$table_data`: (Opzionale) I dati della tabella da utilizzare per la generazione.
* Restituisce una stringa che rappresenta il codice HTML generato per la tabella.

`clear(): Table`: Il metodo clear resetta la tabella eliminando righe e intestazione.
* Parametri:
  * `$table_data`: (Opzionale) I dati della tabella da utilizzare per la generazione.
* Restituisce una stringa che rappresenta il codice HTML generato per la tabella.

```
use Helpers\Table;

// Creazione di un'istanza della classe Table
$table = new Table();

// Impostazione dell'intestazione della tabella
$table->set_heading('Nome', 'Cognome', 'Età');

// Aggiunta di righe alla tabella
$table->add_row('John', 'Doe', 30);
$table->add_row('Jane', 'Smith', 25);
$table->add_row('Mark', 'Johnson', 40);

// Impostazione del template per la generazione della tabella
$template = array(
    'table_open' => '<table border="1" cellpadding="5" cellspacing="0">'
);
$table->set_template($template);

// Generazione del codice HTML per la tabella
$tableHtml = $table->generate();

echo $tableHtml;
```
