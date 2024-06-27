**__# File che contiene funzioni di utility (Table)

**Riferimento path sorgente classe upload:** *app/Helpers/Table.php*

Il Framework offre una libreria che contiene metodi per l'utilizzo delle tabelle.

## Lista dei metodi

- `set_template($template)`
- `set_heading($args = array())`
- `make_columns($array = array(), $col_limit = 0)`
- `set_empty($value)`
- `add_row($args = array())`
- `_prep_args($args)`
- `set_caption($caption)`
- `generate($table_data = NULL)`
- `clear()`
- `_set_from_db_result($object)`
- `_set_from_array($data)`
- `_compile_template()`
- `_default_template()`








## Riferimenti funzioni.



`set_template($template)`

Funzione che setta un nuovo template per le tabelle


| Settaggi            | Descrizione             |
|---------------------|-------------------------|
| **Parametri**       | **$template** -   array |
| **Ritorno**         |                         |
| **Tipo di ritorno** | bool                    |


Esempio :

```php

$template = array(
        'table_open'            => '<table border="0" cellpadding="4" cellspacing="0">',

        'thead_open'            => '<thead>',
        'thead_close'           => '</thead>',

        'heading_row_start'     => '<tr>',
        'heading_row_end'       => '</tr>',
        'heading_cell_start'    => '<th>',
        'heading_cell_end'      => '</th>',

        'tbody_open'            => '<tbody>',
        'tbody_close'           => '</tbody>',

        'row_start'             => '<tr>',
        'row_end'               => '</tr>',
        'cell_start'            => '<td>',
        'cell_end'              => '</td>',

        'row_alt_start'         => '<tr>',
        'row_alt_end'           => '</tr>',
        'cell_alt_start'        => '<td>',
        'cell_alt_end'          => '</td>',

        'table_close'           => '</table>'
);

$this->table->set_template($template);

//E' possibile settare anche solo alcune configurazioni - Esempio :

$template = array(
        'table_open' => '<table border="1" cellpadding="2" cellspacing="1" class="mytable">'
);

$this->table->set_template($template);
```

------

`set_heading($args = array())`

Funzione che setta un il nome delle colonne della tabella


| Settaggi            | Descrizione                    |
|---------------------|--------------------------------|
| **Parametri**       | **$args** elenco delle colonne |
| **Ritorno**         | CI_Table                       |
| **Tipo di ritorno** |                                |


Esempio :

```php
$table->set_heading('Struttura', 'Anno', 'Periodo', '% Presenza', '% Assenza totale');
``` 

------


`make_columns($array = array(), $col_limit = 0)`


Funzione che trasforma un array monodimensionale, e ne crea uno multidimensionale,
con una profondità uguale al numero di colonne. Permette ad un array con moli elementi
di essere mostrato in una tabella con un numero fissato di colonne.


| Settaggi            | Descrizione                                                                         |
|---------------------|-------------------------------------------------------------------------------------|
| **Parametri**       | **$array** - elementi da mostrare <br/> **$col_limit** - numero di elementi massimo |
| **Ritorno**         | array                                                                               |
| **Tipo di ritorno** | array                                                                               |


Esempio :

```php
$array = array('Nome', 'Cognome', 'Età','Città','Nazionalità');
$col_limit = 5;
$colonne = $table->make_columns($array, $col_limit);
var_dump($colonne); 




//Esempio 2
$array = array('Nome', 'Cognome', 'Età','Città','Nazionalità','Data di nascita');
$col_limit = 4;
$colonne = $table->make_columns($array, $col_limit);
var_dump($colonne);
```

Risultato
```php
array (size=1)
  0 => 
    array (size=5)
      0 => string 'Nome' (length=4)
      1 => string 'Cognome' (length=7)
      2 => string 'Età' (length=4)
      3 => string 'Città' (length=6)
      4 => string 'Nazionalità' (length=12)



//Risultato 2
array (size=2)
  0 => 
    array (size=4)
      0 => string 'Nome' (length=4)
      1 => string 'Cognome' (length=7)
      2 => string 'Età' (length=4)
      3 => string 'Città' (length=6)
  1 => 
    array (size=4)
      0 => string 'Nazionalità' (length=12)
      1 => string 'Data di nascita' (length=15)
      2 => string '&nbsp;' (length=6)
      3 => string '&nbsp;' (length=6)

```

------

`set_empty($value)`

Setta il valore contenuto delle celle vuote


| Settaggi            | Descrizione                      |
|---------------------|----------------------------------|
| **Parametri**       | **$value** - valore da impostare |
| **Ritorno**         | CI_Table                         |
| **Tipo di ritorno** |                                  |


Esempio : 

```php
$table->set_empty('');
```

----------

`add_row($args = array())`

Funzione che aggiunge una riga alla tabella


| Settaggi            | Descrizione                                         |
|---------------------|-----------------------------------------------------|
| **Parametri**       | **$args** - valori contenuti nelle celle della riga |
| **Ritorno**         | CI_Table                                            |
| **Tipo di ritorno** |                                                     |


Esempio :


```php
$table->add_row('Mario','Rossi','23','Napoli','Italiana');
```

-----
`_prep_args($args)`


| Settaggi            | Descrizione                     |
|---------------------|---------------------------------|
| **Parametri**       | **$args** - array chiave-valore |
| **Ritorno**         | array                           |
| **Tipo di ritorno** |                                 |


Esempio :


```php
$array = array(
    'chiave' => 'valore',
    'nome' => 'cognome',
    'test'=> 'stringa'
);
var_dump($table->_prep_args($array));
```


Risultato : 
```php
array (size=3)
  'chiave' => 
    array (size=1)
      'data' => string 'valore' (length=6)
  'nome' => 
    array (size=1)
      'data' => string 'cognome' (length=7)
  'test' => 
    array (size=1)
      'data' => string 'stringa' (length=7)
```


-----

`set_caption($caption)`

Funzione che imposta una descizione alla tabella


| Settaggi            | Descrizione                 |
|---------------------|-----------------------------|
| **Parametri**       | **$caption** -descrizione   |
| **Ritorno**         | CI_Table                    |
| **Tipo di ritorno** |                             |


Esempio : 

```php
$table->set_caption('Questa è una descrizione');
```


-----

`generate($table_data = NULL)`

Funzione che genera la tabella


| Settaggi            | Descrizione             |
|---------------------|-------------------------|
| **Parametri**       | **$table_data** - dati  |
| **Ritorno**         | CI_Table                |
| **Tipo di ritorno** |                         |



Esempio :

```php
$table->generate();
```


-----

`clear()`

Funzione che "pulisce" la tabella



| Settaggi            | Descrizione |
|---------------------|-------------|
| **Parametri**       |             |
| **Ritorno**         | CI_Table    |
| **Tipo di ritorno** |             |


-----

`_set_from_db_result($object)`

Funzione che setta i dati partendo da un oggetto restituito da un db



| Settaggi            | Descrizione           |
|---------------------|-----------------------|
| **Parametri**       | **$object** - oggetto |
| **Ritorno**         | void                  |
| **Tipo di ritorno** |                       |



-----

`_set_from_array($data)`


Funzione che setta i dati partendo da un array


| Settaggi            | Descrizione       |
|---------------------|-------------------|
| **Parametri**       | **$data** - array |
| **Ritorno**         | void              |
| **Tipo di ritorno** |                   |


-----

`_compile_template()`

Funzione che setta i valori del template di default


-----

`_default_template()`

Funzione contenente i settaggi di default per il template