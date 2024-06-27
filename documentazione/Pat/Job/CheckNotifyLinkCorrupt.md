## Classe `CheckNotifyLinkCorrupt`

La classe `CheckNotifyLinkCorrupt`  controlla la presenza di link corrotti nelle pagine e li registra nel database.

### lista dei metodi

```
handle(): void
```

Il metodo `handle` gestisce la logica del job per controllare i link corrotti e salvarli nel database.

```
checkCorruptUriDb($i): void
```

Il metodo privato `checkCorruptUriDb` controlla i link corrotti nella tabella del database specificata.

* Parametri:
  * `$i`: L'indice della tabella nella configurazione.

```
findCorruptUri($string, $i, $instituteId, $currentTableId): void
```

Il metodo privato `findCorruptUri` cerca i link corrotti nella stringa fornita e li registra nel database.

* Parametri:
  * `$string`: La stringa in cui cercare i link corrotti.
  * `$i`: L'indice della tabella nella configurazione.
  * `$instituteId`: L'ID dell'ente.
  * `$currentTableId`: L'ID corrente della tabella.

```
urlExists($uri): bool
```

Il metodo privato `urlExists` controlla se l'URL fornito esiste effettuando una richiesta HTTP.

* Parametri:
  * `$uri`: L'URL da controllare.
* Restituisce `true` se l'URL esiste, altrimenti `false`.

```
private$mappingTableEditor = [
// Configurazione delle tabelle per il controllo dei link corrotti
];
```

La proprietà `mappingTableEditor` contiene la configurazione delle tabelle da controllare per i link corrotti.
