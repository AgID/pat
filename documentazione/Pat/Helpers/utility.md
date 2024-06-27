## Funzione utility

### Funzione isAuth
 `isAuth(): bool|array`

La funzione `isAuth` verifica se un utente ha effettuato l'autenticazione.

* Restituisce `false` se l'utente non è autenticato.
* Restituisce un array contenente le informazioni sull'identità dell'utente se è autenticato.

### Funzione getFavicon
 `getFavicon(): ?string `

La funzione getFavicon restituisce il markup HTML per visualizzare la favicon dell'ente.

* Restituisce null se la favicon non è presente.
* Restituisce il markup HTML per visualizzare la favicon se presente.

### Funzione renderFront
 `renderFront(string $layout = '', array $data = [], $theme = null): void`

La funzione `renderFront` è utilizzata per renderizzare una vista nel front-end.

* Parametri:
  * `$layout`: Il file della vista da renderizzare.
  * `$data`: Dati da passare alla vista.
  * `$theme`: Il tema da utilizzare per il render. Se non specificato, viene utilizzato il tema predefinito.

### Funzione getInstitutionLogo
 `getInstitutionLogo(): string`

La funzione getInstitutionLogo restituisce il markup HTML per visualizzare il logo dell'ente di appartenenza.
Restituisce il markup HTML per visualizzare il logo dell'ente.

###Funzione getInstitutionFullAddress
`getInstitutionFullAddress(): string`

La funzione `getInstitutionFullAddress` restituisce l'indirizzo completo dell'ente.

### Funzione getRightOrBottomMenu
`getRightOrBottomMenu($sectionId = null, $parentId = null): mixed`

La funzione `getRightOrBottomMenu` restituisce le voci da mostrare nel menu laterale destro o in fondo alla pagina, in base alla sezione corrente.

* Parametri:
  * `$sectionId`: ID della pagina corrente.
  * `$parentId`: ID della pagina padre della pagina corrente.
* Restituisce un array con le voci del menu.

### Funzione getPageContents
`getPageContents(int $currentPageId = null): array`

La funzione getPageContents restituisce i contenuti di una pagina, inclusi i paragrafi e i loro richiami.

* Parametri:
  * `$currentPageId`: ID della pagina corrente.
* Restituisce un array di contenuti.

### Funzione getSocialLinks
`getSocialLinks(): mixed `

La funzione `getSocialLinks` restituisce i link ai canali social dell'ente.

* Restituisce un array con i link ai canali social dell'ente.

### Funzione filterArrayByKeyValue
`filterArrayByKeyValue(array $array, int|string $key, mixed $keyValue): array `

La funzione `filterArrayByKeyValue` filtra un array in base a una chiave e un valore specificati.

* Parametri:
  * `$array`: L'array da filtrare.
  * `$key`: La chiave da cercare nell'array.
  * `$keyValue`: Il valore da cercare nell'array.
* Restituisce un array filtrato.

### Funzione burgerMenuHtml
`burgerMenuHtml(): string`
La funzione burgerMenuHtml crea l'HTML per il menu a burger.
* Restituisce una stringa con l'HTML per il menu a burger.

### Funzione nestedNodeMenuHtml
`nestedNodeMenuHtml(&$tree): string`
La funzione nestedNodeMenuHtml è una funzione ricorsiva che costruisce la struttura del menu a burger.

* Parametri:
  * `$tree:` L'albero del menu.
* Restituisce una stringa con l'HTML per il menu a burger.



### Funzione getBreadcrumb
`getBreadcrumb(Breadcrumbs|array $bread = null, bool $concat = false): ?string`
La funzione getBreadcrumb restituisce l'HTML per il breadcrumb della pagina.

* Parametri:
  * `$bread:`: Breadcrumbs personalizzati (opzionale).
  * `$concat`: Indica se concatenare i link dei breadcrumbs (opzionale).
* Restituisce una stringa contenente l'HTML del breadcrumb menu.


### Funzione getCustomLinks
`getCustomLinks(string $position = 'header'): ?string`
La funzione getCustomLinks restituisce gli eventuali custom link dell'ente da inserire nel menu nell'header.
* Parametri:
  * `$position:`: Indica la posizione dei link (es: "header", "footer").
* Restituisce una stringa contenente l'HTML dei custom link, o null se non ci sono custom link disponibili..

### Funzione getItMonth
`getItMonth(int|string $numMonth): string`
La funzione getItMonth restituisce la stringa del nome del mese in lingua italiana.
* Parametri:
  * `$numMonth:`: Numero che indica il mese.
* Restituisce una stringa con il nome del mese in italiano corrispondente al numero.

### Funzione getCommissionRole
`getCommissionRole(string $type = null): ?string`
La funzione getCommissionRole restituisce il nome del ruolo ricoperto dal personale all'interno di una commissione nel front-office.
* Parametri:
  * `$type:`: Ruolo all'interno della commissione.
* Restituisce il nome del ruolo corrispondente al tipo specificato, o null se il tipo non è valido.

### Funzione getTitle
`getCommissionRole(string $type = null): ?string`
La funzione getTitle restituisce il titolo della pagina.
* Parametri:
  * `$title:`: Titolo della pagina.
* Restituisce una stringa con il titolo della pagina.

### Funzione getCurrentPageId
`getCurrentPageId(): int`
La funzione getCurrentPageId restituisce l'ID della pagina corrente.
* Restituisce un intero rappresentante l'ID della pagina corrente