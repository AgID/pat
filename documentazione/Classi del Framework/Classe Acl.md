# Classe per la gestione dei permessi acl (Acl)

**Riferimento path sorgente classe Acl:**  *app/Helpers/Security/Acl.php*

Il Framework offre una classe per la gestione dei permessi degli utenti in base ai profili acl(access control list) che hanno associati.



#### Processo di controllo permessi

- Quando un utente entra in una sezione del back office, viene controllato per prima cosa se la sezione per essere navigata necessita o meno di permessi da parte dell'utente;
- Dopo il primo controllo, se la sezione necessita dei permessi da parte dell'utente per essere navigata, allora viene effettuato un secondo controllo per vedere i permessi che l'utente ha su quella sezione cosi da impedirgli di effettuare operazioni a lui non consentite.



#### Esempio pratico di controllo dei permessi:

```php
// Controller che chiama il costruttore della classe parent "BaseAuthController"
class PersonnelAdminController extends BaseAuthController
{
    public function __construct()
    {
        parent::__construct(__CLASS__);
    }
    
    // Metodo di index(permesso di lettura) 
	public function index()
    {
        $this->acl->setRoute('read');

        $this->breadcrumb->push('Personale', '/');
        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();
        $data['titleSection'] = 'Personale';
        $data['formAction'] = '/admin/personnel';
        $data['formSettings'] = [
            'name' => 'form_personnel',
            'id' => 'form_personnel',
            'class' => 'form_personnel',
        ];

        render('personnel/index', $data, 'admin');

    }
}

//costruttore della classe parent "BaseAuthController"
public function __construct($controller = null)
    {
        parent::__construct();
       
        if (!authPatOs()->hasIdentity()) {
            redirect('auth');
        }

        // Sono in una sezione di b.o. dove sono necessari i permessi
        if ($controller !== 'not_acl') {

            //Istanzio la classe Acl passandogli la sezione di b.o. dove si trova l'utente
            $this->acl = new Acl($controller);


        } else {

            // L'utente è in una sezione dove non sono necessari i permessi, Dashboard e Profilo Utente
            Acl::notRun();

        }

        $this->breadcrumb = new Breadcrumbs();
        $this->auth = authPatOs();
    }
```



**Lista dei metodi della classe:**

`$acl = new Acl();`

- `$acl -> profileList()`
- `$acl->notRun()`
- `$acl->setRoute()`
- `$acl-> getProfiles()`
- `$acl->getRead()`
- `$acl->getCreate()`
- `$acl->getUpdate()`
- `$acl->getDelete()`
- `$acl->getVersioning()`
- `$acl->getLockUser()`
- `$acl->getModifyProfile()`
- `$acl->getExportCsv()`
- `$acl->getGeneral()`
- `$acl->getSendnotifyAppIo()`



#### Riferimenti della classe.

`$acl -> profileList($record)`

Questo metodo permette di settare i permessi generali dell'utente in base ai profili acl ad esso associati.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$record **(mix) - Record contenente i permessi generali dei profili acl associati all'utente |
| **Ritorno**         |                                                              |
| **Tipo di ritorno** | void                                                         |



------

`$acl->notRun()`

Questo metodo viene chiamato nel caso in cui l'utente naviga in un sezione dove non sono richiesti permessi, ovvero una sezione sempre accessibile, come la Dashboard e il Profilo Utente.

| Settaggi            | Descrizione |
| ------------------- | ----------- |
| **Parametri**       |             |
| **Ritorno**         |             |
| **Tipo di ritorno** | void        |



------

`$acl->setRoute($method)`

Questo metodo permette di settare il metodo della rotta(sezione di back office) in cui si trova l'utente(Read, Create, Update, Delete) in base ai profili acl ad esso associati.

| Settaggi            | Descrizione                                             |
| ------------------- | ------------------------------------------------------- |
| **Parametri**       | **$method** (string) - Il metodo della rotta da settare |
| **Ritorno**         |                                                         |
| **Tipo di ritorno** | void                                                    |



------

`$acl-> getProfiles()`

Questo metodo restituisce i permessi dell'utente sulla sezione di back office in cui si trova in base ai profili acl ad esso associati.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       |                                                              |
| **Ritorno**         | Array contenente i permessi(Read, Create, Update, Delete) dell'utente sulla sezione in cui si trova. |
| **Tipo di ritorno** | array                                                        |



------

`$acl->getRead()`

Questo metodo restituisce il permesso di lettura che l'utente ha sulla sezione di back office in cui si trova in base ai profili acl ad esso associati.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       |                                                              |
| **Ritorno**         | True se l'utente ha il permesso di lettura sulla sezione di back office, altrimenti false. |
| **Tipo di ritorno** | bool                                                         |



------

`$acl->getCreate()`

Questo metodo restituisce il permesso di creazione(inserimento) che l'utente ha sulla sezione di back office in cui si trova in base ai profili acl ad esso associati.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       |                                                              |
| **Ritorno**         | True se l'utente ha il permesso di creazione(inserimento) sulla sezione di back office, altrimenti false. |
| **Tipo di ritorno** | bool                                                         |



------

`$acl->getUpdate()`

Questo metodo restituisce il permesso di modifica che l'utente ha sulla sezione di back office in cui si trova in base ai profili acl ad esso associati.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       |                                                              |
| **Ritorno**         | True se l'utente ha il permesso di modifica sulla sezione di back office, altrimenti false. |
| **Tipo di ritorno** | bool                                                         |



------

`$acl->getDelete()`

Questo metodo restituisce il permesso di eliminazione che l'utente ha sulla sezione di back office in cui si trova in base ai profili acl ad esso associati.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       |                                                              |
| **Ritorno**         | True se l'utente ha il permesso di eliminazione sulla sezione di back office, altrimenti false. |
| **Tipo di ritorno** | bool                                                         |



------

`$acl->getSendnotifyAppIo()`

Questo metodo restituisce il permesso di invio di notifiche per l'app IO che l'utente ha sulla sezione di back office in cui si trova in base ai profili acl ad esso associati.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       |                                                              |
| **Ritorno**         | True se l'utente ha il permesso di invio notifiche per l'app IO sulla sezione di back office, altrimenti false. |
| **Tipo di ritorno** | bool                                                         |



------

`$acl->getVersioning()`

Questo metodo restituisce il permesso generale (su tutti gli oggetti del PAT) di gestione del versioning dell'utente in base ai profili acl ad esso associati.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       |                                                              |
| **Ritorno**         | True se l'utente ha il permesso di versionig, altrimenti false. |
| **Tipo di ritorno** | bool                                                         |



------

`$acl->getLockUser()`

Questo metodo restituisce il permesso generale di gestione dell'operazione di blocco/sblocco degli utenti che ha l'utente in base ai profili acl ad esso associati.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       |                                                              |
| **Ritorno**         | True se l'utente ha il permesso di blocco/sblocco utenti, altrimenti false. |
| **Tipo di ritorno** | bool                                                         |



------

`$acl->getModifyProfile()`

Questo metodo restituisce il permesso generale di modifica avanzata del profilo utente che ha l'utente in base ai profili acl ad esso associati.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       |                                                              |
| **Ritorno**         | True se l'utente ha il permesso di modifica avanzata del profilo utente, altrimenti false. |
| **Tipo di ritorno** | bool                                                         |



------

`$acl->getExportCsv()`

Questo metodo restituisce il permesso generale di esportazione dei dati in CSV che ha l'utente in base ai profili acl ad esso associati.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       |                                                              |
| **Ritorno**         | True se l'utente ha il permesso di esportazione dei dati in CSV, altrimenti false. |
| **Tipo di ritorno** | bool                                                         |



------

`$acl->getGeneral()`

Questo metodo restituisce i permessi generali dell'utente  in base ai profili acl ad esso associati.

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       |                                                              |
| **Ritorno**         | Array contenente i permessi generali dell'utente(Versioning, Advanced, Lock_User, Export_Csv) |
| **Tipo di ritorno** | array                                                        |



------

