## Classe `Breadcrumbs`

La classe `Breadcrumbs` gestisce la generazione dei breadcrumb nell'interfaccia utente di un'applicazione PHP.

### Metodi

#### `__construct($options = null, $label = null)`

Costruttore della classe `Breadcrumbs`.

##### Parametri

* `$options` : Opzioni per la configurazione dei breadcrumb.
* `$label` : Etichetta per la configurazione dei breadcrumb.

##### Restituisce

* `void`

#### `push($page, $href)`

Aggiunge un elemento ai breadcrumb.

##### Parametri

* `$page`: Il nome o l'etichetta dell'elemento del breadcrumb.
* `$href`: L'URL o il percorso dell'elemento del breadcrumb.

##### Restituisce

* `void`

#### `unshift($page, $href)`

Aggiunge un elemento all'inizio dei breadcrumb.

##### Parametri

* `$page`: Il nome o l'etichetta dell'elemento del breadcrumb.
* `$href`: L'URL o il percorso dell'elemento del breadcrumb.

##### Restituisce

* `void`

#### `show()`

Genera e restituisce l'output dei breadcrumb.

##### Restituisce

* `string`: L'output dei breadcrumb.

### Esempio di utilizzo

php

Copia

```
// Creazione di un'istanza della classe Breadcrumbs
$breadcrumbs = new \System\Breadcrumbs();

// Aggiunta di elementi ai breadcrumb
$breadcrumbs->push('Home', '/');
$breadcrumbs->push('Products', '/products');
$breadcrumbs->push('Category', '/products/category');

// Generazione e visualizzazione dei breadcrumb
echo$breadcrumbs->show();
```
