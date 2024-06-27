## Funzione `checkSecurityPassword`

La funzione `checkSecurityPassword` controlla se una password rispetta i criteri minimi di sicurezza.

### Parametri

* `$password`: La password da controllare.
* `$minLength`: (Opzionale) La lunghezza minima richiesta per la password (default: 14).
* `$maxLength`: (Opzionale) La lunghezza massima consentita per la password (default: 32).

### Valore di ritorno

La funzione restituisce `true` se la password rispetta i criteri minimi di sicurezza, altrimenti `false`.

---

## Funzione `createDirByUserId`

La funzione `createDirByUserId` crea la directory associata all'utente per l'upload dei file.

### Parametri

* `$userId`: L'ID dell'utente per cui creare la directory.

### Eccezioni

La funzione può generare un'eccezione di tipo `Exception`.

---

## Funzione `deleteUserFolder`

La funzione `deleteUserFolder` elimina la cartella di un utente.

### Parametri

* `$userId`: L'ID dell'utente di cui eliminare la cartella.

### Eccezioni

La funzione può generare un'eccezione di tipo `Exception`.


```
use System\Log;

// Esempio di utilizzo della funzione checkSecurityPassword
$password = 'MySecurePassword123';
if (checkSecurityPassword($password)) {
    echo "La password rispetta i criteri di sicurezza.";
} else {
    echo "La password non rispetta i criteri di sicurezza.";
}

// Esempio di utilizzo della funzione createDirByUserId
$userId = 123;
try {
    createDirByUserId($userId);
    echo "Cartella utente creata con successo.";
} catch (Exception $e) {
    echo "Errore durante la creazione della cartella utente: " . $e->getMessage();
}

// Esempio di utilizzo della funzione deleteUserFolder
$userId = 123;
try {
    deleteUserFolder($userId);
    echo "Cartella utente eliminata con successo.";
} catch (Exception $e) {
    echo "Errore durante l'eliminazione della cartella utente: " . $e->getMessage();
}
```
