## Classe `DomainInfo`

La classe `DomainInfo` nel namespace `Events` identifica il nome di dominio corrente e imposta le costanti di sistema corrispondenti.

### Lista dei metodi

```
handle(): void
```

Il metodo `handle` imposta il dominio corrente, definisce le costanti di sistema e gestisce le informazioni dell'ente.

```
getDomain($url): array
```

Il metodo privato `getDomain` identifica il nome del dominio a partire dall'URL fornito.

* Parametri:
  * `$url`: L'URL da cui estrarre il nome di dominio.
* Restituisce un array che rappresenta il nome del dominio con le relative informazioni.
