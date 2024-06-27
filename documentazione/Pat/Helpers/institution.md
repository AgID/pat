## Funzione `getLogoInstitution`

La funzione `getLogoInstitution` restituisce il logo di un ente, se presente.

`toUtf8($string = null): mixed`: Il metodo toUtf8 converte una stringa da ISO-8859-1 a UTF-8.

* Parametri:
  * `$logoFile`: (Opzionale) Il nome del file del logo dell'ente..
  * `$shortName`: (Opzionale) Il nome breve dell'ente.
  * `$noInstitution`: (Opzionale) Per il testo alternativo, viene impostato a true quando la funzione non viene utilizzata per il logo dell'ente..
* La funzione restituisce una stringa che rappresenta l'elemento HTML del logo dell'ente, se il logo e il nome breve dell'ente sono stati forniti. Altrimenti, restituisce una stringa vuota.
  **Eccezioni**
  La funzione può generare un'eccezione di tipo Exception.

```
// Esempio di utilizzo della funzione getLogoInstitution
$logoFile = 'logo.png';
$shortName = 'myinstitution';
$noInstitution = false;
$logoHtml = getLogoInstitution($logoFile, $shortName, $noInstitution);
echo $logoHtml;
```
