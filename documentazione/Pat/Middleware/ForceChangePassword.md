## Classe `ForceChangePassword`

La classe `ForceChangePassword`  implementa un middleware per gestire il cambio forzato della password per gli utenti.

### Lista metodi

```
handle(): bool
```

Il metodo `handle` gestisce la logica del middleware per controllare se l'utente deve cambiare la password.

* Restituisce `true` se l'utente deve cambiare la password, altrimenti imposta un sessione che contiene il messaggio di notifica e riporta nel form di modifica password
