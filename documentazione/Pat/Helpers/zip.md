## Classe `Zip`

La classe Zip  fornisce metodi per comprimere e decomprimere file e directory utilizzando il formato ZIP.

**Lista delle metodi
`- compress(string $source, string $destination): bool.  `
`- extract(string $$destination): bool:`**

### Metodi

`compress(string $source, string $destination): bool`: Il metodo compress comprime un file o una directory in un archivio ZIP.
* Parametri:
  * `$source`: Percorso del file o della directory da comprimere.
  * `$destination`: Percorso del file ZIP di destinazione.
* Restituisce true se l'operazione ha avuto successo, altrimenti false.

`extract(string $source, string $destination): bool`: Il metodo extract decomprime un file ZIP in una directory.
* Parametri:
  * `$source`: Percorso del file ZIP da decomprimere..
  * `$destination`: Percorso della directory di destinazione.
* Restituisce true se l'operazione ha avuto successo, altrimenti false.

```
use Helpers\Zip;

// Comprimi un file
$source = '/path/to/file.txt';
$destination = '/path/to/file.zip';
if (Zip::compress($source, $destination)) {
    echo 'File compresso con successo';
} else {
    echo 'Errore durante la compressione del file';
}

// Decomprimi un file
$source = '/path/to/file.zip';
$destination = '/path/to/extracted';
if (Zip::extract($source, $destination)) {
    echo 'File decompresso con successo';
} else {
    echo 'Errore durante la decompressione del file';
}
```
