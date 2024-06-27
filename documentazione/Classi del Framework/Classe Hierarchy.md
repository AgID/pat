# Classe `Hierarchy`

La classe `Hierarchy` fornisce metodi per la gestione di una gerarchia di record, consentendo l'inserimento, l'aggiornamento e il recupero dei dati in base alla struttura gerarchica.

## Metodi

### `__construct($field = 'back_office')`

Costruttore della classe `Hierarchy`.

- Parametri:
  - `$field` (opzionale): Il campo di configurazione da utilizzare. Valore predefinito: `'back_office'`.

### `insert($data)`

Inserisce un nuovo record nella gerarchia.

- Parametri:
  - `$data`: Un array associativo dei dati del record da inserire.
- Restituisce: L'ID del record inserito.

### `update($id, $data)`

Aggiorna un record esistente nella gerarchia.

- Parametri:
  - `$id`: L'ID del record da aggiornare.
  - `$data`: Un array associativo dei nuovi dati del record.
- Restituisce: L'ID del record aggiornato.

### `getOne($id = null)`

Recupera un singolo record dalla gerarchia in base alla chiave primaria.

- Parametri:
  - `$id` (opzionale): L'ID del record da recuperare.
- Restituisce: Un array associativo che rappresenta il record recuperato.

### `resync()`

Reinizializza l'albero gerarchico dei record.

### `get($topId = 0, $institutionId = null)`

Recupera tutti i record dalla gerarchia.

- Parametri:
  - `$topId` (opzionale): L'ID del record superiore da cui iniziare la ricerca.
  - `$institutionId` (opzionale): L'ID dell'istituzione associata ai record.
- Restituisce: Un array di record rappresentanti la gerarchia.

### `getGroupedChildren($topId = 0, $institutionId = null)`

Recupera tutti i record figlio raggruppati in base all'ID del record superiore.

- Parametri:
  - `$topId` (opzionale): L'ID del record superiore da cui iniziare la ricerca.
  - `$institutionId` (opzionale): L'ID dell'istituzione associata ai record.
- Restituisce: Un array multidimensionale di record rappresentanti la gerarchia figlio raggruppata.

### `getChildren($parentId = 0)`

Recupera tutti i record figlio diretti in base all'ID del genitore.

- Parametri:
  - `$parentId` (opzionale): L'ID del genitore da cui recuperare i record figlio.
- Restituisce: Un array di record rappresentanti i figli diretti.

### `getCountChildren($id = 0, $parentId = true)`

Conta il numero di record figlio diretti in base all'ID del genitore.

- Parametri:
  - `$id`: L'ID del genitore da cui contare i record figlio.
  - `$parentId` (opzionale): Indica se utilizzare l'ID del genitore come colonna di riferimento. Valore predefinito: `true`.
- Restituisce: Il numero di record figlio diretti.

### `getDescendents($parentId = 0)`

Recupera tutti i record discendenti in base all'ID del genitore.

- Parametri:
  - `$parentId` (opzionale): L'ID del genitore da cui recuperare i record discendenti.
- Restituisce: Un array di record rappresentanti i discendenti.

### `getAncestors($id, $removeThis = false)`

Recupera tutti i record degli antenati in base all'ID.

- Parametri:
  - `$id`: L'ID del record da cui recuperare gli antenati.
  - `$removeThis` (opzionale): Indica se rimuovere il record corrente dai risultati. Valore predefinito: `false`.
- Restituisce: Un array di record rappresentanti gli antenati.

### `getParent($id)`

Recupera il genitore del record in base all'ID.

- Parametri:
  - `$id`: L'ID del record di cui recuperare il genitore.
- Restituisce: Un array rappresentante il genitore del recordMi scuso per l'incomprensione.

#### Esempio di utilizzo del metodo `insert()`:

```
use System\Hierarchy;

$hierarchy = new Hierarchy();

$data = [
'name' => 'New Item',
'parent_id' => 1
];

$insertId = $hierarchy->insert($data);

echo "Record inserito con ID: " . $insertId;
```

Nell'esempio sopra, viene utilizzato il metodo `insert()` per inserire un nuovo record nella gerarchia. Viene passato un array `$data` che contiene i dati del nuovo record, incluso il nome e l'ID del genitore. Il metodo determina automaticamente la profondità e l'ordine del nuovo record. Viene restituito l'ID del record appena inserito, che viene quindi visualizzato.

#### Esempio di utilizzo del metodo `update()`:

```
use System\Hierarchy;

$hierarchy = new Hierarchy();

$id = 1;
$data = [
'name' => 'Updated Item'
];

$hierarchy->update($id, $data);

echo "Record con ID $id aggiornato con successo.";
```

Nell'esempio sopra, viene utilizzato il metodo `update()` per aggiornare un record esistente nella gerarchia. Viene specificato l'ID del record da aggiornare e un array `$data` che contiene i nuovi dati del record. Il metodo esegue l'aggiornamento del record nel database e restituisce l'ID del record aggiornato, che viene quindi visualizzato.

#### Esempio di utilizzo del metodo `getOne()`:

```
use System\Hierarchy;

$hierarchy = new Hierarchy();

$id = 1;

$record = $hierarchy->getOne($id);

echo "Nome: " . $record['name'];
echo "Genitore ID: " . $record['parent_id'];
```

Nell'esempio sopra, viene utilizzato il metodo `getOne()` per recuperare un singolo record dalla gerarchia in base all'ID specificato. Il metodo restituisce un array che rappresenta il record trovato. Vengono quindi visualizzati il nome e l'ID del genitore del record.

#### Esempio di utilizzo del metodo `get()`:

```
use System\Hierarchy;

$hierarchy = new Hierarchy();

$records = $hierarchy->get();

foreach ($recordsas$record) {
echo "Nome: " . $record['name'];
echo "Genitore ID: " . $record['parent_id'];
}
```

Nell'esempio sopra, viene utilizzato il metodo `get()` per recuperare tutti i record dalla gerarchia. Il metodo restituisce un array di record, che viene quindi iterato con un ciclo foreach. Vengono quindi visualizzati il nome e l'ID del genitore di ogni record.

#### Esempio di utilizzo del metodo `getGroupedChildren()`:

```
use System\Hierarchy;

$hierarchy = new Hierarchy();

$parentId = 1;

$groupedChildren = $hierarchy->getGroupedChildren($parentId);

foreach ($groupedChildrenas$group) {
echo "Genitore: " . $group['name'];
echo "Figli:";
foreach ($group['children'] as$child) {
echo "- " . $child['name'];
    }
}
```

Nell'esempio sopra, viene utilizzato il metodo `getGroupedChildren()` per recuperare i record figlio raggruppati in base all'ID genitore specificato. Viene passato l'ID del genitore desiderato e il metodo restituisce un array multidimensionale in cui ogni elemento rappresenta un gruppo di figli del genitore. Viene iterato l'array dei gruppi e per ogni gruppo vengono visualizzati il nome del genitore e i nomi dei figli.

#### Esempio di utilizzo del metodo `getChildren()`:

```
use System\Hierarchy;

$hierarchy = new Hierarchy();

$parentId = 1;

$children = $hierarchy->getChildren($parentId);

foreach ($childrenas$child) {
echo "Nome: " . $child['name'];
echo "Genitore ID: " . $child['parent_id'];
}
```

Nell'esempio sopra, viene utilizzato il metodo `getChildren()` per recuperare i record figlio diretti in base all'ID genitore specificato. Viene passato l'ID del genitore desiderato e il metodo restituisce un array di record figlio. VEcco gli esempi per tutti i metodi documentati della classe `Hierarchy`:
