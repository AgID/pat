## Classe `AuthPatOS`

La classe `AuthPatOS` nella namespace `Helpers` fornisce funzionalità per l'autenticazione e l'autorizzazione degli utenti nel sistema Pat OS.

### Proprietà

* `$nameTable`: Il nome della tabella degli utenti nel database.
* `$nameTableInstitution`: Il nome della tabella delle istituzioni nel database.
* `$namePk`: Il nome della chiave primaria nella tabella degli utenti.
* `$nameInstitutionPk`: Il nome della chiave primaria nella tabella delle istituzioni.
* `$nameUsername`: Il nome del campo che rappresenta l'username nella tabella degli utenti.
* `$nameEmail`: Il nome del campo che rappresenta l'email nella tabella degli utenti.
* `$namePassword`: Il nome del campo che rappresenta la password nella tabella degli utenti.
* `$nameActive`: Il nome del campo che rappresenta lo stato di attività nella tabella degli utenti.
* `$db`: Un'istanza della classe `Database` per l'interazione con il database.
* `$encryption`: Un'istanza della classe `Encryption` per la crittografia dei dati.
* `$sessionName`: Il nome della sessione per l'autenticazione.
* `$hasIdentity`: Il nome della variabile di sessione che indica se l'utente ha un'identità autenticata.
* `$session`: Un'istanza della classe `Session` per la gestione delle sessioni.
* `$isValid`: Un flag booleano che indica se l'utente è valido (autenticato).
* `$hasEncryption`: Un flag booleano che indica se è abilitata la crittografia dei dati.
* `$nameSuperAdmin`: Il nome del campo che indica se l'utente è un super amministratore nella tabella degli utenti.
* `$nameAdmin`: Il nome del campo che indica se l'utente è un amministratore nella tabella degli utenti.
* `$nameDeleted`: Il nome del campo che indica se l'utente è stato eliminato nella tabella degli utenti.
* `$nameIsAPI`: Il nome del campo che indica se l'utente è abilitato per l'API nella tabella degli utenti.
* `$namePasswordToken`: Il nome del campo che contiene il token della password nella tabella degli utenti.
* `$lastVisited`: Il nome del campo che rappresenta l'ultima visita dell'utente nella tabella degli utenti.
* `$institutionsId`: Il nome del campo che rappresenta l'ID dell'istituzione nella tabella degli utenti.
* `$lastVisitTimeLimit`: Il limite di tempo per la validità dell'ultima visita dell'utente.
* `$isAuthTwoFactor`: Un flag booleano che indica se l'autenticazione a due fattori è abilitata.
* `$limitCallsApi`: Il limite di chiamate API consentite per l'utente.
* `$error`: Un messaggio di errore per l'autenticazione (opzionale).

### Metodi

`authenticate($usernameOrEmail = null, $password = null): bool`: Il metodo authenticate gestisce l'autenticazione dell'utente.

* Parametri:
  * `$usernameOrEmail`: (Opzionale) L'username o l'email dell'utente.
  * `$password`: (Opzionale) La password dell'utente.
* Restituisce true se l'autenticazione ha successo, altrimenti false.

`hasIdentity(): bool`: Il metodo hasIdentity verifica se l'utente ha un'identità autenticata.

* Restituisce true se l'utente ha un'identità autenticata, altrimenti false.

`getIdentity($data = null): mixed`: Il metodo getIdentity restituisce l'identità dell'utente autenticato.

* Parametri:
  * `$data`: (Opzionale) Un array o un oggetto contenente i nomi delle proprietà desiderate dell'identità.
* Restituisce un array o un oggetto che rappresenta l'identità dell'utente autenticato.

`isValid(): bool`: Il metodo isValid verifica se l'utente è valido (autenticato).

* Restituisce true se l'utente è valido, altrimenti false.

`clearIdentity(): bool`: Il metodo clearIdentity cancella l'identità dell'utente.

* Restituisce true se l'operazione ha successo, altrimenti false.

`close(): void`: Il metodo addStorage aggiunge dati allo storage dell'utente autenticato.

`addStorage($storage = null): void`: Il metodo addStorage aggiunge dati allo storage dell'utente autenticato.

* Parametri:
  * `$storage`: (Opzionale) Un array o un oggetto contenente i dati da aggiungere allo storage.

`removeStorage($storage = null): mixed`: Il metodo removeStorage rimuove dati dallo storage dell'utente autenticato.

* Parametri:
  * `$storage`: (Opzionale) Un array o un valore singolo contenente i dati da rimuovere dallo storage.

`getStorage($data = null): mixed`: Il metodo getStorage restituisce i dati presenti nello storage dell'utente autenticato.

* Parametri:
  * `$storage`: (Opzionale) Un array o un oggetto contenente i nomi delle proprietà desiderate.
* Restituisce un array o un oggetto che rappresenta i dati nello storage dell'utente autenticato.

`generateToken($username = null, $email = null, $password = null): string`: Il metodo generateToken genera un token di autenticazione.

* Parametri:
  * `$username`: (Opzionale) L'username dell'utente.
  * `$email`: (Opzionale) L'email dell'utente.
  * `$password`: (Opzionale) La password dell'utente.
* Restituisce una stringa che rappresenta il token generato.

```
use Helpers\AuthPatOS;

// Creazione di un'istanza di AuthPatOS
$auth = new AuthPatOS();

// Autenticazione dell'utente
$username = 'username';
$password = 'password';

if ($auth->authenticate($username, $password)) {
    echo "Autenticazione riuscita!";
    // Accesso consentito
} else {
    echo "Autenticazione fallita!";
    // Accesso non consentito
}

// Verifica se l'utente ha un'identità autenticata
if ($auth->hasIdentity()) {
    // L'utente è autenticato
    // Recupero l'identità dell'utente
    $identity = $auth->getIdentity();
    // Utilizzo dell'identità per operazioni successive
} else {
    // L'utente non è autenticato
}
```
