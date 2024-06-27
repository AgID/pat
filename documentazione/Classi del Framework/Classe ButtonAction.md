# Classe per la gestione dei bottoni delle azioni 

**Riferimento path sorgente classe ButtonAction:**  *app/Helpers/Utility/ButtonAction.php*

Il Framework offre una classe per la gestione dei bottoni delle azioni sui record nei datatable in base ai permessi dell'utente.



#### Processo di controllo permessi

- Quando un utente entra in una sezione del back office per visualizzare i suoi dati, nel datatable in base ai sui permessi vengono mostrati soltanto i bottoni delle azioni che può eseguire sui record, cosi da impedirgli di effettuare azioni a lui non consentite.



#### Esempio pratico di controllo dei permessi:

```php
$record 
    
//Setto i bottoni delle actions da mostrare in base ai permessi dell'utente
$ButtonAction = ButtonAction::create([
   	'edit' => $this->acl->getUpdate(),
    'lock_unlock' => getAclLockUser(),
    'versioning' => getAclVersioning(),
    'delete' => $this->acl->getDelete(),
    ])
    ->addEdit('admin/user/edit/' . $record['id'], $record['id'])
    ->addLockUnlock('admin/user/active/' . $record['id'], ($record['active'] === 1 ? 0 : 1),$record['id'])
    ->addVersioning('admin/user/versioning/' . $record['id'], $record['id'])
    ->addDelete('admin/user/delete/' . $record['id'], $record['id'])
    ->render();

```



**Lista dei metodi della classe:**

`$button = new ButtonAction();`

- `$button -> addView()`
- `$button -> addEdit()`
- `$button -> addDuplicate()`
- `$button -> addDelete()`
- `$button -> addVersioning()`
- `$button -> addLockUnlock()`
- `$button -> render()`
- `$button -> create()`
- `$button -> checkList()`



#### Riferimenti della classe.

`$button -> addView($url = '#!', $id = '')`

Questa funzione permette di aggiungere il bottone per la visualizzazione dei dettagli di un record.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$url**(string) - URL della rotta di visualizzazione <br />**$id** - ID del record da visualizzare |
| **Ritorno**         | Istanza della classe ButtonAction                            |
| **Tipo di ritorno** | ButtonAction                                                 |



------

`$button -> addEdit($url = '#!', $id = '')`

Questa funzione permette di aggiungere il bottone per la modifica di un record.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$url**(string) - URL della rotta di modifica<br />**$id** - ID del record da modificare |
| **Ritorno**         | Istanza della classe ButtonAction                            |
| **Tipo di ritorno** | ButtonAction                                                 |



------

`$button -> addDuplicate($url = '#!', $id = '')`

Questa funzione permette di aggiungere il bottone per la duplicazione di un record.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$url**(string) - URL della rotta di duplicazione<br />**$id** - ID del record da duplicare |
| **Ritorno**         | Istanza della classe ButtonAction                            |
| **Tipo di ritorno** | ButtonAction                                                 |



------

`$button -> addDelete($url = '#!', $id = '')`

Questa funzione permette di aggiungere il bottone per l'eliminazione di un record.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$url**(string) - URL della rotta di eliminazione<br />**$id** - ID del record da eliminare |
| **Ritorno**         | Istanza della classe ButtonAction                            |
| **Tipo di ritorno** | ButtonAction                                                 |



------

`$button -> addVersioning($url = '#!', $id = '')`

Questa funzione permette di aggiungere il bottone per la gestione del versioning di un record.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$url**(string) - URL della rotta del versioning<br />**$id** - ID del record di cui gestire il versioning |
| **Ritorno**         | Istanza della classe ButtonAction                            |
| **Tipo di ritorno** | ButtonAction                                                 |



------

`$button -> addLockUnlock($url = '#!', $id = '')`

Questa funzione permette di aggiungere il bottone per l'azioni di blocco/sblocco di un record.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$url**(string) - URL della rotta dell'azione di blocco/sblocco<br />**$id** - ID del record di cui gestire il versioning |
| **Ritorno**         | Istanza della classe ButtonAction                            |
| **Tipo di ritorno** | ButtonAction                                                 |



------

`$button -> render()`

Questa funzione permette di inserire il tag HTML di chiusura.

| Settaggi            | Descrizione             |
| ------------------- | ----------------------- |
| **Parametri**       |                         |
| **Ritorno**         | Tag HTML per il bottone |
| **Tipo di ritorno** | tag HTML                |



------

`$button -> create($profiles = null)`

Questa funzione permette di settare i profili per la visualizzazione dei vari bottoni delle azioni dei datatable.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$profiles** (array) - Array contenente i permessi dell'utente |
| **Ritorno**         | Istanza della classe ButtonAction                            |
| **Tipo di ritorno** | ButtonAction                                                 |

Metodo statico che viene richiamato come Costruttore.



------

`$button -> checkList($name = 'item[]', $id = null, $ClassNameCss = 'checkbox_item')`

Questa funzione permette di settare le checkbox per la selezione multipla di records su cui effettuare le azioni.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$name** (string) - Nome da dare alla checkbox <br />**$id** - ID del record selezionabile con la checkbox <br />**$classNameCss** (string) - Classe  da assegnare alla checkbox |
| **Ritorno**         | Tag checkbox HTML                                            |
| **Tipo di ritorno** | tag HTML                                                     |



------

