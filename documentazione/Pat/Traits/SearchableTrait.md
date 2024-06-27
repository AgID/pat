## Trait `SearchableTrait`

Il trait `SearchableTrait` fornisce funzionalità per la ricerca avanzata nei modelli.

### Metodi

```
fullTextWildcards(string$term): string
```

Il metodo `fullTextWildcards` controlla se la stringa di ricerca è un indirizzo email e, in caso affermativo, rimuove i caratteri speciali dalla stringa.

* Parametri:
  * `$term`: La stringa di ricerca da controllare.
* Restituisce una stringa che rappresenta la stringa di ricerca senza caratteri speciali se è un indirizzo email, altrimenti restituisce la stringa originale.

```
hasMail(string$term = ''): bool
```

Il metodo `hasMail` controlla se la stringa è un indirizzo email.

* Parametri:
  * `$term`: (Opzionale) La stringa da controllare.
* Restituisce `true` se la stringa è un indirizzo email, altrimenti `false`.

```
scopeSearch(Builder $query, ?string$term, bool|array|null$select = false, array|null$fieldWhereHas = null, bool|null$frontOffice = false, array|null$dateField = null, array$modelScope = []): Builder
```

Il metodo `scopeSearch` genera la query di ricerca avanzata in base alla stringa inserita dall'utente e ai campi dei vari modelli.

* Parametri:
  * `$query`: L'istanza dell'oggetto Builder che rappresenta la query.
  * `$term`: (Opzionale) La stringa di ricerca inserita dall'utente.
  * `$select`: (Opzionale) Se specificato come un array, indica i campi su cui effettuare la ricerca. Se `false`, utilizza i campi dichiarati nel modello.
  * `$fieldWhereHas`: (Opzionale) Un array di campi di ricerca nelle tabelle di relazione.
  * `$frontOffice`: (Opzionale) Indica se la ricerca viene effettuata nel front-office.
  * `$dateField`: (Opzionale) Un array di campi di ricerca di tipo data.
  * `$modelScope`: (Opzionale) Un array di scope aggiuntivi del modello.
* Restituisce l'istanza dell'oggetto Builder con la query di ricerca applicata.
