## Funzione `checkPassword`

La funzione `checkPassword` controlla se una password rispetta i criteri minimi di sicurezza.

### Parametri

* `$input`: (Opzionale) La password da controllare.
* `$min`: (Opzionale) La lunghezza minima richiesta per la password (default: 14).
* `$max`: (Opzionale) La lunghezza massima consentita per la password (default: 32).

### Valore di ritorno

La funzione restituisce `null` se la password rispetta i criteri minimi di sicurezza. Se la password non rispetta i criteri, viene restituito un array con un elemento di errore contenente il messaggio di errore corrispondente.

### Eccezioni

La funzione può generare un'eccezione di tipo `Exception`.

```
$password = 'MyStrongPassword123';

$result = checkPassword($password);

if ($result === null) {
    echo "La password rispetta i criteri minimi di sicurezza.";
} else {
    echo "La password non rispetta i criteri minimi di sicurezza. Errore: " . $result['error'];
}
```
