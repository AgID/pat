## Classe `OS`

La classe `OS` nella namespace `Helpers` fornisce metodi per ottenere informazioni sul sistema operativo in esecuzione.

### Costanti

La classe OS definisce le seguenti costanti:

`UNKNOWN:` Rappresenta un sistema operativo sconosciuto.
`WIN:` Rappresenta il sistema operativo Windows.
`LINUX:` Rappresenta il sistema operativo Linux.
`OSX:` Rappresenta il sistema operativo macOS (OS X).

### Metodi

`get(): int` : Il metodo get restituisce il codice numerico che rappresenta il sistema operativo in esecuzione.

* Restituisce una delle costanti della classe OS: UNKNOWN, WIN, LINUX o OSX.

`isWin(): int` : Il metodo isWin verifica se il sistema operativo in esecuzione è Windows.

* Restituisce true se il sistema operativo è Windows, altrimenti restituisce false.

`isOSX(): int` : Il metodo isOSX verifica se il sistema operativo in esecuzione è macOS (OS X).

* Restituisce true se il sistema operativo è macOS, altrimenti restituisce false.

`isLinux(): int` : Il metodo isLinux verifica se il sistema operativo in esecuzione è Linux.

* Restituisce true se il sistema operativo è Linux, altrimenti restituisce false.

`isLinux(): int` : Il metodo isLinux verifica se il sistema operativo in esecuzione è Linux.

* Restituisce true se il sistema operativo è Linux, altrimenti restituisce false.

```
use Helpers\OS;

// Esempio di ottenere il codice del sistema operativo
$osCode = OS::get();
echo $osCode; // Output: Restituirà una delle costanti della classe OS

// Esempio di verifica se il sistema operativo è Windows
if (OS::isWin()) {
    echo "Sistema operativo: Windows";
} else {
    echo "Sistema operativo diverso da Windows";
}

// Esempio di verifica se il sistema operativo è macOS (OS X)
if (OS::isOSX()) {
    echo "Sistema operativo: macOS";
} else {
    echo "Sistema operativo diverso da macOS";
}

// Esempio di verifica se il sistema operativo è Linux
if (OS::isLinux()) {
    echo "Sistema operativo: Linux";
} else {
    echo "Sistema operativo diverso da Linux";
}
```
