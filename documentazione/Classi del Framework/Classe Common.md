# File che contiene funzioni di utility (Common)

**Riferimento path sorgente classe upload:** *app/Common.php*

Il Framework offre una libreria che contiene vari metodi di utilità generali.



#### Lista dei metodi:

- `PatOsInstituteId()`
- `patOsInstituteInfo($args = null)`
- `patOsConfigMail($merge = false)`
- `arrayMergeRecursiveDistinct($array1, $array2)`
- `authPatOs()`
- `isSuperAdmin()`
- `allowCORS()`
- `paginateBootstrap($elements, $amount = false, $totalHours = false)`
- `br2nl($string)`
- `toFloat($num)`
- `resolveStringBearer($bearer)`
- `translateMonth($month = null)`
- `iconBtn($name = 'Salva', $id = "icon-save", $ico = 'fa-save')`
- `form_editor($data = '', $value = '', $extra = '')`
- `sessionSetNotify($message = null, $type = 'success')`
- `sessionHasNotify()`
- `sessionGetNotify()`
- `sessionTypeNotify()`
- `filesUploaded($field = 'userfile')`
- `getIdentity($data = null)`
- `avatar()`
- `isIe11()`
- `guard()`
- `getSectionPagesBackOffice()`
- `removeDotHtml($str = null)`
- `setUpperCaseRowTable($string, $mode = false, $strong = false)`
- `btnSave($id = "btn_save", $title = false)`
- `searchArrayByField($value = null, $data = [], $field = '')`
- `multiSearch(array $array, array $pairs)`
- `convertDateToDatabase($dateTime)`
- `convertDateToForm($timeStamp)`
- `setDefaultData($data = null, $default = null, $expected = [null, 0, false])`
- `setOrderDatatable($columnName = null, array $orderable = [], string $default = '')`
- `getAclVersioning()`
- `getAclLockUser()`
- `getAclModifyProfile()`
- `getAclExportCsv()`
- `getAclDelete()`
- `getAclAdd()`
- `createdByCheckDeleted($name = null, $deleted = 0)`
- `instituteNameSelected()`
- `instituteDir($fullName = null, $instituteId = null)`
- `wordLimiter($str, $limit = 100, $end_char = '&#8230;')`
- `shortInstitutionName($str = null)`
- `moveFileInDirMedia($fileName = null, $instituteDir = null)`
- `write_file($path, $data, $mode = 'wb')`
- `checkAlternativeInstitutionId()`
- `createinstituteDirectory()`



#### Riferimenti funzioni.

`PatOsInstituteId()`



Funzione che restituisce l'ID dell'ente del portale di amministrazione trasparente.

| Settaggi            | Descrizione  |
| ------------------- | ------------ |
| **Parametri**       |              |
| **Ritorno**         | ID dell'ente |
| **Tipo di ritorno** | int          |



Esempio:

```php
$institutionId = PatOsInstituteId();
trace($$institutionId);
```

L'esempio precedente stamperebbe:

```php
int 1
```



------

`patOsInstituteInfo($args = null)`



Funzione che restituisce tutte le informazioni sull'ente del portale di amministrazione trasparente, oppure solo le informazioni specificate nel parametro.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$args** (array) - Campi dell'ente che si desidera ottenere |
| **Ritorno**         | Informazioni dell'ente                                       |
| **Tipo di ritorno** | string\|array\|bool                                          |



Esempio:

```php
$institutionInfo = patOsInstituteInfo();
trace($institutionInfo, true);
```

L'esempio precedente stamperebbe, tutte le informazioni dell'ente visto che non è stato passato nessun parametro nella funzione, come si può vedere di seguito:

```
  'id' => int 1
  'id_creator' => int 2
  'institution_type_id' => int 1
  'state' => int 1
  'full_name_institution' => string 'Comune di Esempio' (length=17)
  'short_institution_name' => string 'comune_di_esempio' (length=17)
  'vat' => string '01722270665' (length=11)
  'email_address' => string 'supporto@isweb.it' (length=17)
  'certified_email_address' => string 'pec@isweb.it' (length=12)
  'institutional_website_name' => string 'Example' (length=7)
  'institutional_website_url' => string 'http://www.example.com' (length=22)
  'top_level_institution_name' => string 'Esempio' (length=7)
  'top_level_institution_url' => string 'http://www.esempio.it' (length=21)
  'welcome_text' => string '<h2>Benvenuti</h2>' (length=18)
  'footer_text' => string '<h3>Footer</h3>' (length=15)
  'accessibility_text' => string '<p>acc</p>' (length=10)
  'address_street' => string 'Via XX Settembre' (length=16)
  'address_zip_code' => string '67055' (length=5)
  'address_city' => string 'Avezzano' (length=8)
  'address_province' => string 'AQ' (length=2)
  'phone' => string '3332564589' (length=10)
  'two_factors_identification' => int 1
  'trasparenza_logo_file' => string 'NULL' (length=4)
  'activation_date' => null
  'expiration_date' => string '2022-10-06 10:42:04' (length=19)
  'cancellation' => int 0
  'trasparenza_urls' => string 'http://patos.local' (length=18)
  'bulletin_board_url' => string 'http://patos.local.it' (length=21)
  'simple_logo_file' => string '45ceb11448d940c0d6b6134b35e140b5.png' (length=36)
  'favicon_file' => string 'NULL' (length=4)
  'opendata_channel' => null
  'show_update_date' => null
  'statistic_snippet_code' => string 'NULL' (length=4)
  'google_maps_api_key' => string 'null' (length=4)
  'indexable' => int 1
  'support' => int 0
  'show_regulation_in_structure' => null
  'tabular_display_org_ind_pol' => int 1
  'max_users' => null
  'client_code' => null
  'smtp_username' => string 'patos@isweb.it' (length=14)
  'smtp_pec_username' => string 'username@pec.test.it' (length=20)
  'smtp_password' => string 'passwordTest95!' (length=15)
  'smtp_pec_password' => string 'passwordTest95!' (length=15)
  'smtp_host' => string 'mail.internetsoluzioni.it' (length=25)
  'smtp_pec_host' => string 'mail.internetsoluzioni.it' (length=25)
  'smtp_port' => string '587' (length=3)
  'smtp_pec_port' => string '588' (length=3)
  'smtp_security' => int 2
  'smtp_pec_security' => int 3
  'smtp_auth' => null
  'show_smtp_auth' => int 1
  'smtp_test_email' => string 'a.paris@isweb.it' (length=16)
  'smtp_pec_auth' => int 2
  'email_notifications' => null
  'email_pec_notifications' => string 'esempio@pec.test.it' (length=19)
  'publication_responsible' => string 'Vincenzo Apostolo' (length=17)
  'privacy_url' => string 'htttp://www.esempio.it' (length=22)
  'private_token' => null
  'last_visit_time_limit' => int 2592000
  'personnel_roles' => null
  'created_at' => string '2021-10-06T08:42:04.000000Z' (length=27)
  'deleted_at' => null
  'updated_at' => string '2021-12-03T14:47:07.000000Z' (length=27)
```

Se invece passiamo nel parametro della funzione i campi che ci interessano, verranno restituiti solo questi, come di seguito:

```php
$institutionInfo = patOsInstituteInfo(['id', 'full_name_institution', 'email_address']);
trace($institutionInfo, true);

//Stampa solo le informazioni passate nel parametro

/*
'id' => int 1
'full_name_institution' => string 'Comune di Esempio' (length=17)
'email_address' => string 'supporto@isweb.it' (length=17)
*/
```



------

`patOsConfigMail($merge = false)`



Funzione che permette di configurare i parametri per l'invio email.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$merge** (bool) - Valore booleano, che se settato a true viene effettuato un merge tra le configurazioni di sistema del framework per l'invio di email e quelle dell'ente. |
| **Ritorno**         | Ritorna le configurazioni impostate                          |
| **Tipo di ritorno** | array                                                        |



------

`arrayMergeRecursiveDistinct($array1, $array2)`



Funzione che esegue il merge di due array multidimensionali.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$array1** (array) - Primo array su cui effettuare il merge<br />**$array2** (array) - Secondo array su cui effettuare il merge |
| **Ritorno**         | Ritorna l'array risultante dall'operazione di merge          |
| **Tipo di ritorno** | array                                                        |



------

`authPatOs()`



Funzione di autenticazione custom Pat-Os.

| Settaggi            | Descrizione |
| ------------------- | ----------- |
| **Parametri**       |             |
| **Ritorno**         |             |
| **Tipo di ritorno** | Auth        |

Per maggiori informazioni vedere il file AuthPatOS.



------

`isSuperAdmin()`



Funzione che verifica se l'utente in sessione è un super admin (Amministratore della piattaforma). 

| Settaggi            | Descrizione                                      |
| ------------------- | ------------------------------------------------ |
| **Parametri**       |                                                  |
| **Ritorno**         | True se l'utente è super admin, false altrimenti |
| **Tipo di ritorno** | bool                                             |



------

`allowCORS()`



Funzione che abilita le richieste CORS.

| Settaggi            | Descrizione |
| ------------------- | ----------- |
| **Parametri**       |             |
| **Ritorno**         |             |
| **Tipo di ritorno** | null        |



------

`paginateBootstrap($elements)`



Funzione per la paginazione con Bootstrap 4.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$elements** (array) - Numero degli elementi dell'array generati dall'ORM nell'interrogazione di una tabella |
| **Ritorno**         |                                                              |
| **Tipo di ritorno** | HTML                                                         |



------

`br2nl($string)`



Funzione che trasforma i tag `<br />` in new line.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$string** (string) - Stringa in cui trasformare i tag `<br />` in new line |
| **Ritorno**         | La stringa senza i tag  `<br />`                             |
| **Tipo di ritorno** | array\|string\|string[]\|null                                |



------

`toFloat($num)`



Funzione che trasforma il valore passato come parametro nel tipo float.

| Settaggi            | Descrizione                                     |
| ------------------- | ----------------------------------------------- |
| **Parametri**       | **$num** - Numero da trasformare nel tipo float |
| **Ritorno**         | Il valore trasformato nel tipo float            |
| **Tipo di ritorno** | float                                           |



------

`resolveStringBearer($bearer)`



Funzione che nell'autenticazione con JWT ritorna il token dali headers.

| Settaggi            | Descrizione                                             |
| ------------------- | ------------------------------------------------------- |
| **Parametri**       | **$bearer** (string) - Stringa in cui prendere il token |
| **Ritorno**         | Il token                                                |
| **Tipo di ritorno** | null\|string                                            |



------

`translateMonth($month = null)`



Funzione che dato un numero restituisce il nome del mese corrispondente.

| Settaggi            | Descrizione                                              |
| ------------------- | -------------------------------------------------------- |
| **Parametri**       | **$month** (int) - Numero del mese che si vuole ottenere |
| **Ritorno**         | Il nome del mese                                         |
| **Tipo di ritorno** | string                                                   |



------

`iconBtn($name = 'Salva', $id = "icon-save", $ico = 'fa-save')`



Funzione per la creazione dell'icona di un pulsante.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$name** (string) - Testo da visualizzare nel pulsante<br />**$id** (string) - ID dell'icona desiderata<br />**$ico** (string) - Tipo di icona |
| **Ritorno**         | Tag HTML per l'icona                                         |
| **Tipo di ritorno** | HTML                                                         |



------

`form_editor($data = '', $value = '', $extra = '')`



Funziona che genera un campi di input di tipo "editor".

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$data** (*array*) - Dati degli attributi del campo <br />**$value** (*string*) - Valore del campo di input<br />**$extra** (*mixed*) - Attributi extra da aggiungere al tag come un array o una stringa letterale |
| **Ritorno**         | Un tag HTML di input di tipo CKEditor                        |
| **Tipo di ritorno** | string                                                       |



Esempio:

```php+HTML
{{form_editor([
	'name' => 'welcome_text',
    'value' => 'Testo di benvenuto',
    'id' => 'input_welcome_text',
    'class' => 'form-control input_welcome_text'
]) }}
```



------

`sessionSetNotify($message = null, $type = 'success')`



Funzione che permette di settare una notifica con il messaggio.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$message** (string) - Messaggio da visualizzare nella notifica<br />**$type** (string) - Il tipo di notifica ['success', 'warning', 'info', 'danger'] |
| **Ritorno**         |                                                              |
| **Tipo di ritorno** | null                                                         |

Esempio:

```php
sessionSetNotify('Messaggio di errore', 'danger'));
```



------

`sessionHasNotify()`



Funzione che ritorna true se c'è una notifica in sessione, false altrimenti.

| Settaggi            | Descrizione                                            |
| ------------------- | ------------------------------------------------------ |
| **Parametri**       |                                                        |
| **Ritorno**         | True se c'è una notifica in sessione, false altrimenti |
| **Tipo di ritorno** | bool                                                   |



Esempio:

```php
sessionSetNotify('Messaggio di errore', 'danger'));
trace(sessionHasNotify());

//Stampa true
```



------

`sessionGetNotify()`



Funzione che ritorna il messaggio della notifica in sessione se presente, altrimenti null.

| Settaggi            | Descrizione                                                |
| ------------------- | ---------------------------------------------------------- |
| **Parametri**       |                                                            |
| **Ritorno**         | Il messaggio della notifica in sessione se presente o null |
| **Tipo di ritorno** | string\|null                                               |



Esempio:

```php
sessionSetNotify('Messaggio di errore', 'danger'));
trace(sessionGetNotify());

//Stampa 'Errore'
```



------

`sessionTypeNotify()`



Funzione che ritorna il tipo della notifica in sessione se presente, altrimenti null.

| Settaggi            | Descrizione                                          |
| ------------------- | ---------------------------------------------------- |
| **Parametri**       |                                                      |
| **Ritorno**         | Il tipodella notifica in sessione se presente o null |
| **Tipo di ritorno** | string\|null                                         |



Esempio:

```php
sessionSetNotify('Messaggio di errore', 'danger'));
trace(sessionTypeNotify());

//Stampa 'danger'
```



------

`filesUploaded($field = 'userfile')`



Funzione che permette l'upload dei files.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$field** (string) - Nome del campo contenente il file da caricare |
| **Ritorno**         | True in caso di successo, altrimenti false                   |
| **Tipo di ritorno** | bool                                                         |



Esempio:

```php
[omissis]

if ((bool)filesUploaded('profile_image') === true) {
    
	$doUpload = $this->doUpload();
	$hasError = (bool)$doUpload['success'];
    
}

[omissis]
```



------

`getIdentity($data = null)`



Funzione che restituisce tutte le informazioni dell'utente in sessione, oppure solamente i campi passati nel parametro `$data`.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$data** (string) - Campi dell'utente che si vuole ottenere |
| **Ritorno**         | Array con le informazioni dell'utente                        |
| **Tipo di ritorno** | array\|null                                                  |



Esempio:

```php
trace(authPatOs()->getIdentity());

// Restituisce tutte le informazioni dell'utente, come di seguto
/*
'id' => string '2' 
  'institution_id' => string '1' 
  'name' => string 'Mario Rossi'
  'username' => string 'm.rossi'
  'email' => string 'm.rossi@esempio.it' 
  'phone' => string '' 
  'spid_code' => string ''
  'fiscal_code' => string '' 
  'active' => string '1' 
  'active_key' => string '' 
  'deleted' => string '0'
  'last_visit' => string '2021-12-06 17:54:40' 
  'registration_date' => string '2021-10-08 14:25:06' 
  'super_admin' => string '0' 
  'prevent_password_repetition' => string '2' 
  'prevent_password_repetition_6_months' => string '' 
  'password_expiration_days' => string '25' 
  'refresh_password' => string '' 
  'prevent_password_change_day' => string '' 
  'deactivate_account_no_use' => string '0' 
  'filter_owner_record' => string '' 
  'notes' => string '' 
  'registration_type' => string '' 
  'profile_image' => string '' 
  'created_at' => string '2021-10-08 14:25:06' 
  'updated_at' => string '2021-11-30 18:03:35'
  'deleted_at' => string '' 
  'last_visit_limit' => string '2592000' 
  'options' => 
    array (size=4)
      'last_date_access' => string '07-12-2021'
      'last_hour_access' => string '09:09' 
      'institute_id' => string '1' 
      'profiles' => string 'a:2:{i:0;i:1;i:1;i:3;}'
*/
```

Se invece si vuole solo determinati campi, basta specificarli nel parametro della funzione come nell'esempio di seguito:

```php
trace(authPatOs()->getIdentity(['id', 'name', 'email']));

// Restituisce solo i campi dell'utente specificati nel parametro, come di seguto
/*
  'id' => string '2'
  'name' => string 'Mario Rossi'
  'email' => string 'm.rossi@esempio.it' 
*/
```



------

`avatar()`



Funzione che restituisce l'avatar con l'immagine del profilo dell'utente.

| Settaggi            | Descrizione                   |
| ------------------- | ----------------------------- |
| **Parametri**       |                               |
| **Ritorno**         | URL dell'immagine dell'avatar |
| **Tipo di ritorno** | string                        |



Esempio:

```html
<img src="{{ avatar() }}" class="img-circle elevation-2" alt="Avatar">
```



------

`isIe11()`



Funzione che controlla se il browser in uso è Internet Explorer 11.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       |                                                              |
| **Ritorno**         | True se il browser in uso è Internet Explorer 11, altrimenti false |
| **Tipo di ritorno** | bool                                                         |



------

`guard()`



Funzione che verifica se un determinato utente ha il permesso di accedere a una determina sezione.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       |                                                              |
| **Ritorno**         | True se l'utente ha il permesso per accedere alla pagina, altrimenti false |
| **Tipo di ritorno** | bool                                                         |



------

`getSectionPagesBackOffice()`



Funzione che restituisce il menù laterale di sinistra, in tutta la sua alberatura.

| Settaggi            | Descrizione                                                 |
| ------------------- | ----------------------------------------------------------- |
| **Parametri**       |                                                             |
| **Ritorno**         | Un array con tutte le sezioni del menù laterale di sinistra |
| **Tipo di ritorno** | array                                                       |



Esempio:

```php
trace(getSectionPagesBackOffice());

//Restituisce un array con tutte le sezioni del menù laterale e la loro alberatura, come di seguito:
/*
0 => 
    array (size=14)
      'id' => int 1
      'parent_id' => int 0
      'name' => string 'Organizzazione dell'Ente' (length=24)
      'lineage' => string '000001' (length=6)
      'deep' => int 1
      'sort' => int 1
      'notify_app_io' => int 0
      'created_at' => string '2021-10-29 09:34:49' (length=19)
      'updated_at' => null
      'deleted_at' => null
      'controller' => string 'control_name' (length=12)
      'url' => string '#!' (length=2)
      'icon' => string '<i class="fas fa-cog"></i>' (length=26)
      'children' => 
        array (size=9)
          0 => 
            array (size=14)
              ...
          1 => 
            array (size=14)
              ...
          2 => 
            array (size=14)
              ...
          3 => 
            array (size=14)
              ...
          4 => 
            array (size=14)
              ...
          5 => 
            array (size=14)
              ...
          6 => 
            array (size=14)
              ...
          7 => 
            array (size=14)
              ...
          8 => 
            array (size=14)
              ...
  1 => 
    array (size=14)
      'id' => int 11
      'parent_id' => int 0
      'name' => string 'Documenti e Moduli' (length=18)
      'lineage' => string '000011' (length=6)
      'deep' => int 1
      'sort' => int 1
      'notify_app_io' => int 0
      'created_at' => string '2021-10-29 09:48:34' (length=19)
      'updated_at' => null
      'deleted_at' => null
      'controller' => string 'controller_name' (length=15)
      'url' => string '#!' (length=2)
      'icon' => string '<i class="far fa-folder-open"></i>' (length=34)
      'children' => 
        array (size=4)
          0 => 
            array (size=14)
              ...
          1 => 
            array (size=14)
              ...
          2 => 
            array (size=14)
              ...
          3 => 
            array (size=14)
              ...
  2 => 
    array (size=14)
      'id' => int 16
      'parent_id' => int 0
      'name' => string 'Atti e pubblicazioni' (length=20)
      'lineage' => string '000016' (length=6)
      'deep' => int 1
      'sort' => int 1
      'notify_app_io' => int 0
      'created_at' => string '2021-10-29 09:53:10' (length=19)
      'updated_at' => null
      'deleted_at' => null
      'controller' => string 'controller_name' (length=15)
      'url' => string '#!' (length=2)
      'icon' => string '<i class="fas fa-gavel"></i>' (length=28)
      'children' => 
        array (size=11)
          0 => 
            array (size=14)
              ...
          1 => 
            array (size=14)
              ...
          2 => 
            array (size=14)
              ...
          3 => 
            array (size=14)
              ...
          4 => 
            array (size=14)
              ...
          5 => 
            array (size=14)
              ...
          6 => 
            array (size=14)
              ...
          7 => 
            array (size=14)
              ...
          8 => 
            array (size=14)
              ...
          9 => 
            array (size=14)
              ...
          10 => 
            array (size=14)
              ...
[omissis]
*/
```



------

`removeDotHtml($str = null)`



Funzione che rimuove ".html" e ".htm" da una stringa passata nel parametro `$str`.

| Settaggi            | Descrizione                                                 |
| ------------------- | ----------------------------------------------------------- |
| **Parametri**       | **$str** (string) - Stringa su cui effettuare la rimozione. |
| **Ritorno**         | La stringa passata nel parametro senza ".html" e ".htm"     |
| **Tipo di ritorno** | string                                                      |



Esempio:

```php
 trace(removeDotHtml('http://patos.local/admin/dashboard.html'));

//Stampa la stringa senza l'estensione .html, come di seguito:
// 'http://patos.local/admin/dashboard'
```



------

`setUpperCaseRowTable($string, $mode = false, $strong = false)`



Funzione che converte tutti i caratteri di una riga di una tabella in caratteri maiuscoli.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$str** (string) - Stringa su cui effettuare la conversione<br />**$mode** (bool) - Se settato a true effetua la conversione dei caratteri in maiuscolo<br />**$strong** (bool) - Se settato a true mette il tag `<strong>` per il grassetto |
| **Ritorno**         | La stringa passata nel parametro senza ".html" e ".htm"      |
| **Tipo di ritorno** | string                                                       |



------

`btnSave($id = "btn_save", $title = false)`



Funzione che crea un pulsante per il salvataggio.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$id** (string) - ID del pulsante di salvataggio<br />**$title** (bool) - Se settato a false, utilizza il titolo di default |
| **Ritorno**         | Tag HTML per un pulsante di salvataggio                      |
| **Tipo di ritorno** | HTML                                                         |



Esempio:

```php
{{ btnSave() }}
```

L'esempio di sopra produce il seguente codice HTML:

```html
<button name="send" type="submit" id="btn_save" class="btn btn-outline-primary"><i class="far fa-save"></i>&nbsp; Salva</button>&nbsp;&nbsp;<span></span>
```



------

`searchArrayByField($value = null, $data = [], $field = '')`



Funzione che cerca una riga all'interno di un array multidimensionale.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$value** () - Valore da cercare nell'array<br />**$data** (array) - Array in cui cercare<br />**$field** (string) - Key dell'array |
| **Ritorno**         | Restituisce la riga dell'array che contiene la key           |
| **Tipo di ritorno** | array\|null                                                  |



------

`multiSearch(array $array, array $pairs)`



Funzione che 

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$array** (array) - Array in cui cercare <br />**$pairs** (array) - Uno o piu parametri sottoforma di array key=>value da cercare nell'array nel parametro `$array` |
| **Ritorno**         | Riga dell'array trovata                                      |
| **Tipo di ritorno** | array\|null                                                  |



------

`convertDateToDatabase($dateTime)`



Funzione che converte una data nel formato datetime per essere salvata sul database.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$dateTime** (date) - Data da convertire nel formato datetime del database |
| **Ritorno**         | La data nel formato datetime del database                    |
| **Tipo di ritorno** | datetime                                                     |



Esempio:

```php
$dateToConvert = '07/12/2021';
trace(convertDateToDatabase($dateToConvert));

//L'esempio di sopra stamperà la data convertita, come di seguito:
'2021-12-07' 
```



------

`convertDateToForm($timeStamp)`



Funzione che converte una data nel formato timestamp accettato dal form. Esegue l'operazione inversa rispetto alla funzione di sopra `convertDateToDatabase($timeStamp)` .

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$timeStamp** (date) - Data da convertire nel formato timestamp |
| **Ritorno**         | La data nel formato datetime del database                    |
| **Tipo di ritorno** | timestamp                                                    |



Esempio:

```php
$dateToConvert = '2021-12-07 00:00:00.00';
trace(convertDateToForm($dateToConvert));

//L'esempio di sopra stamperà la data convertita, come di seguito:
/*
	'date' => string '07/12/2021' (length=10)
  	'hours' => string '00:00' (length=5)
*/
```



------

`setDefaultData($data = null, $default = null, $expected = [null, 0, false])`



Funzione che ritorna un parametro predefinito in base al valore passato nei parametri.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$data** (date) - Valore da controllare <br />**$default** () - Parametro predefinito da ritornare<br />**$expected** () - Array di valori che se assegnati al valore del parametro `$data` va ritornato il parametro di default |
| **Ritorno**         | Parametro predefinito `$default` se il valore del parametro `$data` è presente nell'array `$expected`, altrimenti restituisce il valore del parametro `$data` stesso |
| **Tipo di ritorno** | mix\|null                                                    |



Esempio:

```php
$test = setDefaultData('Test', 'Default', ['', null, 'Test']);
trace($test);

//L'esempio di sopra stamperà il valore assegnato alla varibaile $test come di seguito:
// 'Default'
```



------

`setOrderDatatable($columnName = null, array $orderable = [], string $default = '')`



Funzione che imposta la colonna su cui effettuare l'ordinamento nel datatable.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$columnName** (date) - Nome della colonna <br />**$orderable** () - Array contenente le colonne della tabella<br />**$expected** () - Colonna di default per l'ordinamento |
| **Ritorno**         |                                                              |
| **Tipo di ritorno** | void                                                         |



Esempio:

```php
$order = setOrderDatatable($columnName, $orderable, 'object');
```



------

`getAclVersioning()`



Funzione che restituisce il permesso di versioning che ha l'utente.

| Settaggi            | Descrizione                            |
| ------------------- | -------------------------------------- |
| **Parametri**       |                                        |
| **Ritorno**         | Permesso di versioning che ha l'utente |
| **Tipo di ritorno** | bool                                   |



------

`getAclLockUser()`



Funzione che restituisce il permesso di blocco/sblocco degli utenti che ha l'utente.

| Settaggi            | Descrizione                                |
| ------------------- | ------------------------------------------ |
| **Parametri**       |                                            |
| **Ritorno**         | Permesso di blocco/sblocco che ha l'utente |
| **Tipo di ritorno** | bool                                       |



------

`getAclModifyProfile()`



Funzione che restituisce il permesso di modifica avanzata del profilo che ha l'utente.

| Settaggi            | Descrizione                                               |
| ------------------- | --------------------------------------------------------- |
| **Parametri**       |                                                           |
| **Ritorno**         | Permesso di modifica avanzata del profilo che ha l'utente |
| **Tipo di ritorno** | bool                                                      |



------

`getAclExportCsv()`



Funzione che restituisce il permesso di export dei dati in CSV dell'utente.

| Settaggi            | Descrizione                                        |
| ------------------- | -------------------------------------------------- |
| **Parametri**       |                                                    |
| **Ritorno**         | Permesso di export dei dati in CSV che ha l'utente |
| **Tipo di ritorno** | bool                                               |



------

`getAclDelete()`



Funzione che restituisce i permessi di eliminazione dell'utente su una determinata sezione.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       |                                                              |
| **Ritorno**         | Permesso di eliminazione su una determinata sezione che ha l'utente |
| **Tipo di ritorno** | bool                                                         |



------

`getAclAdd()`



Funzione che restituisce i permessi di inserimento dell'utente su una determinata sezione.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       |                                                              |
| **Ritorno**         | Permesso di inserimento su una determinata sezione che ha l'utente |
| **Tipo di ritorno** | bool                                                         |



------

`createdByCheckDeleted($name = null, $deleted = 0)`



Funzione che mostra nel datatable se un utente è stato cancellato.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$name** - Nome dell'utente<br />**$deleted** - Booleano per vedere se l'utente è stato eliminato o meno |
| **Ritorno**         | Stringa contenente il nome dell'utente                       |
| **Tipo di ritorno** | string                                                       |



------

`instituteNameSelected()`



Funzione che ritorna il nome dell'ente selezionato.

| Settaggi            | Descrizione                                      |
| ------------------- | ------------------------------------------------ |
| **Parametri**       |                                                  |
| **Ritorno**         | Stringa contenente il nome dell'ente selezionato |
| **Tipo di ritorno** | string                                           |



------

`instituteDir($shortName = null)`



Funzione che ritorna il nome della cartella dei media dell'Ente specificato nei parametri, altrimenti dell'Ente in cui si è loggati.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$shortName** - Nome breve dell'Ente di cui si vuole ottenere il nome della cartella dei media |
| **Ritorno**         | Stringa contenente il nome della cartella dei media dell'Ente |
| **Tipo di ritorno** | string\|null                                                 |



Esempio:

```php
$directory = instituteDir('Comune di Esempio', 1);

trace($test);

//L'esempio di sopra stamperà il nome della cartella dei media dell'Ente, come di seguito:
// comune_di_esempio
```



------

`wordLimiter($str, $limit = 100, $end_char = '&#8230;')`



Funzione che limita una stringa a un numero X di parole.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$str** - Stringa da limitare <br />**$limit** - Limite di parole <br />**$end_char** - Carattere finale |
| **Ritorno**         | Stringa limitata                                             |
| **Tipo di ritorno** | string                                                       |



------

`shortInstitutionName($str = null)`



Funzione che restituisce il nome breve dell'ente.

| Settaggi            | Descrizione                        |
| ------------------- | ---------------------------------- |
| **Parametri**       | **$str** - Nome completo dell'ente |
| **Ritorno**         | Nome breve dell'ente               |
| **Tipo di ritorno** | string                             |



Esempio:

```php
$shortName = shortInstitutionName('Comune di Esempio');

print($shortName);

//L'esempio di sopra stamperà il nome breve dell'Ente, come di seguito:
// comune_di_esempio
```



------

`moveFileInDirMedia($fileName = null, $instituteDir = null)`



Funzione che sposta un file nella cartella media dell'Ente quando viene creato.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$fileName** - Nome del file da spostare <br />**$instituteDir** - Nome della cartella media dell'Ente |
| **Ritorno**         |                                                              |
| **Tipo di ritorno** | void                                                         |



Esempio:

```php
moveFileInDirMedia('logo.png', 'comune_di_esempio');
```



------

`write_file($path, $data, $mode = 'wb')`



Funzione che permette di scrivere i dati nel file specificato nel percorso. 

Se il file non esiste lo crea.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$path** - Percorso del file su cui scrivere, o in cui viene creato se non esiste <br />**$data** - Dati da scrivere nel file <br />**$mode** - Specifica il tipo di accesso al file(di defautl 'wb' permesso di scrittura in binary mode) |
| **Ritorno**         | Ritorna TRUE se la scrittura ha avuto successo, FALSE in caso di errore |
| **Tipo di ritorno** | bool                                                         |



Esempio:

```php
write_file(MEDIA_PATH . '/' . 'index.css', $data);
```



------

`loadElfinderJs($elements = null)`



Funzione per la paginazione del file-manager **elFinder**.

| Settaggi            | Descrizione                                 |
| ------------------- | ------------------------------------------- |
| **Parametri**       | **$elements** -                             |
| **Ritorno**         | Ritorna la vista contenente il file manager |
| **Tipo di ritorno** | Istanza classe View                         |



------

`checkAlternativeInstitutionId()`



Funzione che ritorna l'ID dell'ente che si sta gestendo.

Se l'utente è super admin ritorna 0 se si stanno gestendo tutti gli enti oppure l'ID dell'ente che ha selezionato per la gestione.

Se l'utente non è super admin, ritorna l'ID dell'ente di appartenenza del dominio in cui si è loggato.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       |                                                              |
| **Ritorno**         | Ritorna l'ID dell'ente che si sta gestendo, oppure 0 se si stanno gestendo tutti gli enti come super admin |
| **Tipo di ritorno** | bool\|int                                                    |



------

`createinstituteDirectory($shortName = null)`



Funzione che crea le cartelle dei media per l'ente appena viene creato.

Cartelle create:

- cartella_nome_breve_ente
  - object_attachs
  - file_archive
  - assets
    - js
    - css
    - images

In ogni cartella, vengono creati i file *.htaccess* e *index.hmtl*.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$shortName** - Nome breve dell'ente                        |
| **Ritorno**         | Ritorna true se le cartelle vengono create con successo, false altrimenti. |
| **Tipo di ritorno** | false\|null                                                  |

