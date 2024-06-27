## Classe `BrowsingHistory`

La classe `BrowsingHistory`  gestisce la cronologia di navigazione dell'utente.

### Lista dei metodi

```
__construct($maxHistory = null)
```

Il costruttore della classe `BrowsingHistory` inizializza la cronologia di navigazione nella sessione.

* Parametri:
  * `$maxHistory`: (Opzionale) Il numero massimo di URL nella cronologia (default: 30).

```
addUrl($url): void
```

Il metodo `addUrl` aggiunge una nuova URL e l'ora di accesso alla cronologia di navigazione.

* Parametri:
  * `$url`: L'URL da aggiungere alla cronologia.

```
getHistory(): array|null
```

Il metodo `getHistory` restituisce l'intera cronologia di navigazione.

* Restituisce un array che rappresenta la cronologia di navigazione, o `null` se la cronologia non è presente.

```
getHistoryCount(): int|null
```

Il metodo `getHistoryCount` restituisce il numero di elementi nella cronologia di navigazione.

* Restituisce il numero di elementi nella cronologia di navigazione, o `null` se la cronologia non è presente.

```
getLastPage(): array|null
```

Il metodo `getLastPage` restituisce l'ultima pagina nella cronologia di navigazione.

* Restituisce un array che rappresenta l'ultima pagina nella cronologia di navigazione, o `null` se la cronologia è vuota.

```
getNumPage($n = 0): array|null
```

Il metodo `getNumPage` restituisce l'elemento nella cronologia di navigazione corrispondente al numero specificato.

* Parametri:
  * `$n`: (Opzionale) Il numero dell'elemento nella cronologia di navigazione (default: 0).
* Restituisce un array che rappresenta l'elemento nella cronologia di navigazione corrispondente al numero specificato, o `null` se l'elemento non esiste.

```
use Events\BrowsingHistory;

// Creazione dell'istanza di BrowsingHistory
$history = new BrowsingHistory();

// Aggiunta di una nuova URL alla cronologia
$history->addUrl('/route/one');
$history->addUrl('/route/two');
$history->addUrl('/dashboard');

// Recupero dell'intera cronologia di navigazione
$fullHistory = $history->getHistory();
echo "Cronologia completa: ";
print_r($fullHistory);

// Recupero del numero di elementi nella cronologia
$count = $history->getHistoryCount();
echo "Numero di elementi nella cronologia: " . $count;

// Recupero dell'ultima pagina nella cronologia
$lastPage = $history->getLastPage();
echo "Ultima pagina nella cronologia: ";
print_r($lastPage);

// Recupero di una pagina specifica nella cronologia
$page = $history->getNumPage(1);
echo "Pagina numero 1 nella cronologia: ";
print_r($page);
```
