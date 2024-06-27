## Classe `IsAuthPatOsMiddleware`

La classe `IsAuthPatOsMiddleware` nel namespace `Middleware` implementa un middleware per verificare se l'utente è autenticato prima di accedere a determinate rotte.

### Metodi

#### Metodo `handle`

php

Copia

```
handle(): bool|void
```

Il metodo `handle` gestisce la logica del middleware per verificare se l'utente è autenticato.

* Restituisce `true` se l'utente è autenticato, altrimenti reindirizza l'utente alla pagina di autenticazione e terminando l'esecuzione.
