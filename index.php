<?php
/**
 * Constante che impedisce l'esecuzione degli script con chimata diretta in GET.
 */
define('_FRAMEWORK_', true);

$getMicrotime = microtime();

/**
 * Ambiente in cui viene avviata l'applicazione
 *  - development : Ambiente di sviluppo
 *  - testing     : Ambiente di test
 *  - production  : Ambiente di produzione
 */
define('ENVIRONMENT', 'development');

/**
 *---------------------------------------------------------------
 * ERROR REPORTING
 *---------------------------------------------------------------
 */
switch (ENVIRONMENT) {
    case 'development':
        error_reporting(-1);
        ini_set('display_errors', 1);
        break;

    case 'testing':
    case 'production':
        ini_set('display_errors', 0);
        error_reporting(0);
//        error_reporting(E_ALL & ~E_NOTICE & ~E_DEPRECATED & ~E_STRICT & ~E_USER_NOTICE & ~E_USER_DEPRECATED);
        break;

    default:
        header('HTTP/1.1 503 Service Unavailable.', TRUE, 503);
        echo "L'ambiente dell'applicazione non &egrave; impostato correttamente.";
        exit(1); // EXIT_ERROR
}

// Il nome del file corrente
define('SELF', pathinfo(__FILE__, PATHINFO_BASENAME));

// Constante del percorso Assoluto dell'applicazione
define('PATH', dirname(__FILE__) . DIRECTORY_SEPARATOR);

// Constante del percorso assoluto dei packages di SISTEMA
define('CORE_PATH', PATH . 'core' . DIRECTORY_SEPARATOR);

// Constante del percorso assoluto dei packages dell'APP
define('APP_PATH', PATH . 'app' . DIRECTORY_SEPARATOR);

// Constante del percorso assoluto dei file media
define('MEDIA_PATH', PATH . 'media' . DIRECTORY_SEPARATOR);

// Constante del percorso assoluto dei file temporanei
define('TEMP_PATH', PATH . 'temp' . DIRECTORY_SEPARATOR);

// Constante del percorso della cartella cache
define('CACHE_PATH', APP_PATH . 'Cache' . DIRECTORY_SEPARATOR);

// Constante del percorso della cartella config
define('CONFIG_PATH', APP_PATH . 'Config' . DIRECTORY_SEPARATOR);

// Constante del percorso della cartella di Logs
define('LOGS_PATH', APP_PATH . 'Logs' . DIRECTORY_SEPARATOR);

// Constante del percorso della cartella dei moduli
define('MODULES_PATH', APP_PATH . 'Modules' . DIRECTORY_SEPARATOR);

// Constante del percorso della cartella dei temi (Viste)
define('THEME_PATH', APP_PATH . 'Themes' . DIRECTORY_SEPARATOR);

// Custom configs
if (file_exists(CONFIG_PATH . 'constants.php')) {
    require_once CONFIG_PATH . 'constants.php';
}

// Includo il Bootstrap (inizializzazione) dell'avvio dell'applicazione.
require_once PATH . 'core/Bootstrap.php';

// Commentare la riga successiva per il Debug di tutte le rotte instanziate nell'applicazione.
// d(\System\Route::allRoutes());
