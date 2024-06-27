# Classe URI

**Riferimento path sorgente classe Log:** *core/System/Uri.php*

La classe URI fornisce metodi che consentono di recuperare le informazioni dalle stringhe URI. Se utilizzi il routing URI, puoi anche recuperare le informazioni sui segmenti.

Lista dei metodi della classe URI:  `$uri = new \System\Uri();`

- `$uri->segment()`
- `$uri->rsegment()`
- `$uri->uriToAssoc()`
- `$uri->ruriToAssoc()`
- `$uri->assocToUri()`
- `$uri->slashSegment()`
- `$uri->slashRsegment()`
- `$uri->segmentArray()`
- `$uri->rsegmentArray()`
- `$uri->totalSegments()`
- `$uri->totalRsegments()`
- `$uri->uriString()`
- `$uri->getQueryString()`
- `$uri->fullUrl()`





#### Riferimenti della classe.

```php
$uri->segment($n[, $no_result = NULL]);
```

| Settaggi            | Descrizione                                                  |
| ------------------- | ------------------------------------------------------------ |
| **Parametri**       | **$n** ( *int* ) - Numero di indice del segmento<br /> **$no_result** ( mix ) - Cosa restituire se il segmento cercato non viene trovato |
| **Ritorno**         | Valore del segmento o valore **$no_result** se non trovato   |
| **Tipo di ritorno** | Mix                                                          |

Ti permette di recuperare un segmento specifico. Dove **n** è il numero di segmento che desideri recuperare. I segmenti sono numerati da sinistra a destra. Ad esempio, se il tuo URL completo è questo:

```
http://example.com/index.php/segmento1/segmento2/segmento3/segmento4
```

I numeri del segmento sarebbero questo:

1. segmento1
2. segmento2
3. segmento3
4. segmento4

Il secondo parametro opzionale è impostato su NULL e consente di impostare il valore di ritorno di questo metodo quando manca il segmento URI richiesto. Ad esempio, questo dirà al metodo di restituire il numero zero in caso di errore:

```php
$id  =  $uri->segment(3, 0);
```

Aiuta a evitare di dover scrivere codice come questo:

```php
if  ( $uri->segment(3) === false ) {
  
  $id = 0; 
  
}  else  { 
  
  $id = $uri->segment(3); 
  
}
```

------

