## Classe `Env`

La classe `Env` gestisce il caricamento e il parsing del file `.env`, che contiene le variabili di ambiente utilizzate dall'applicazione. Questa classe fornisce un modo conveniente per accedere e utilizzare queste variabili nel contesto dell'applicazione.

#### Metodi:

### `__construct($path)`

Il costruttore della classe `Env` accetta il percorso del file `.env` come parametro e carica il file utilizzando il metodo `loadFileEnv()`.

#### Parametri

* `$path`: Il percorso del file `.env`.

#### Lancio delle eccezioni

* `Exception`: Viene lanciata un'eccezione se il file `.env` non viene trovato o non è leggibile.

### `load($path)`

Un metodo statico che crea un'istanza della classe `Env` e carica il file `.env`.

#### Parametri

* `$path` (opzionale): Il percorso del file `.env`. Se non specificato, verrà utilizzato il percorso fornito durante l'inizializzazione della classe.

#### Restituisce

* `Env`: Un'istanza della classe `Env` con il file `.env` caricato.

#### Lancio delle eccezioni

* `Exception`: Viene lanciata un'eccezione se il file `.env` non viene trovato o non è leggibile.

### `loadFileEnv()`

Un metodo privato che carica e analizza il file `.env`. Legge il file riga per riga, ignorando i commenti e assegnando le variabili di ambiente utilizzando `$_ENV` e `$_SERVER`.

#### Restituisce

* `void`

## Utilizzo

Per utilizzare la classe `Env`, è necessario chiamare il metodo `load()` passando il percorso del file `.env`. Successivamente, è possibile accedere alle variabili di ambiente utilizzando `$_ENV` o `$_SERVER`.

```
useSystem\Env;

// Caricamento del file .env
Env::load('/path/to/.env');

// Accesso alle variabili di ambiente
$databaseHost = $_ENV['DB_HOST'];
$databaseUser = $_ENV['DB_USER'];
$databasePassword = $_ENV['DB_PASSWORD'];

// Utilizzo delle variabili di ambiente
// Esempio: connessione al database
$dbConnection = new PDO("mysql:host=$databaseHost;dbname=mydatabase", $databaseUser, $databasePassword);
```

Si noti che è necessario avere un file `.env` valido nel percorso specificato. Il file `.env` dovrebbe contenere le variabili di ambiente necessarie per l'applicazione, come ad esempio le credenziali di accesso al database o altre configurazioni specifiche.

Questa classe semplifica il processo di caricamento e utilizzo delle variabili di ambiente nel contesto dell'applicazione, consentendo di mantenere le configurazioni sensibili separate dal codice sorgente.
