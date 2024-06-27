## Classe `Acl`

La classe `Acl` fornisce metodi per la gestione dei permessi degli utenti in base ai profili Acl che ha associati.

### Metodi

* `__construct(array|string $nameSpace = null)`: Costruttore per la classe `Acl` che gestisce i permessi degli utenti basati sui profili Acl associati.
* `hasPermit(string $className = null)`: Metodo che controlla se l'utente ha i permessi per una determinata sezione.
* `notRun()`: Funzione chiamata nel caso in cui l'utente naviga nella Dashboard o nel Profilo Utente, senza settare i permessi di sezione.
* `setRoute(array|string $method, bool $check = false)`: Metodo che imposta il metodo della rotta e verifica i permessi per le chiamate Ajax.
* `getProfiles(): array`: Metodo che restituisce i permessi dell'utente sulla sezione in cui si trova.
* `getRead(): bool`: Metodo che restituisce il permesso di lettura che ha l'utente sulla sezione in cui si trova.
* `getCreate(): bool`: Metodo che restituisce il permesso di creazione che ha l'utente sulla sezione in cui si trova.
* `getUpdate(): bool`: Metodo che restituisce il permesso di modifica che ha l'utente sulla sezione in cui si trova.
* `getDelete(): bool`: Metodo che restituisce il permesso di eliminazione che ha l'utente sulla sezione in cui si trova.
* `getCrud(): bool|array`: Metodo che restituisce i permessi dell'utente sulla sezione in cui si trova.
* `getVersioning(): bool`: Metodo che restituisce il permesso generale di versioning che ha l'utente.
* `getArchiving(): bool`: Metodo che restituisce il permesso generale di archiviazione che ha l'utente.
* `getLockUser(): bool`: Metodo che restituisce il permesso generale di blocco/sblocco degli utenti che ha l'utente.
* `getModifyProfile(): bool`: Metodo che restituisce il permesso generale di modifica avanzata del profilo che ha l'utente.
* `getExportCsv(): bool`: Metodo che restituisce il permesso generale di esportazione di CSV che ha l'utente.
* `getAclProfileInfo(string $name = ''): bool`: Metodo che restituisce il permesso generale specificato nel parametro.
