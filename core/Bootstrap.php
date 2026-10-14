<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') or exit('No direct script access allowed');

$installedPhpVersion = phpversion();

/**
 * Verifico la versione DELL'interprete php INSTALLATO
 */

if (version_compare(phpversion(), '8.0.0', '<=')) {

    $html = str_replace('{php_version}', phpversion(), file_get_contents(APP_PATH . 'Themes/php_obsolete.php'));
    echo $html;
    exit();
}

//utilizzato per stampare anche in out of memory //256kb
$reservedMemory = str_repeat('x', 256 * 1024);
register_shutdown_function(function () use (&$reservedMemory): void {

    // Libera la memoria, prima di qualsiasi altra operazione.
    $reservedMemory = null;

    $error = error_get_last();

    if ($error === null) {
        return;
    }

    $fatalTypes = [E_ERROR, E_PARSE, E_CORE_ERROR, E_COMPILE_ERROR, E_USER_ERROR, E_RECOVERABLE_ERROR];

    if (!in_array($error['type'] ?? 0, $fatalTypes, true)) {
        return;
    }

    $message = sprintf(
        "[%s]\nFatal error: %s\nFile: %s\nLine: %d\nURL: %s\nMethod: %s\n",
        date('Y-m-d H:i:s'),
        $error['message'] ?? 'Errore sconosciuto',
        $error['file'] ?? 'File sconosciuto',
        $error['line'] ?? 0,
        $_SERVER['REQUEST_URI'] ?? 'CLI',
        $_SERVER['REQUEST_METHOD'] ?? 'CLI'
    );

    $isCli = PHP_SAPI === 'cli' && defined('STDIN');

    if ($isCli) {
        fwrite(STDERR, PHP_EOL . $message . PHP_EOL);
        return;
    }

    $logDirectory = APP_PATH . 'Logs';
    $logFile = $logDirectory . DIRECTORY_SEPARATOR . '[FATAL-ERROR] ' . date('Y-m-d') . '.txt';

    if (!is_dir($logDirectory)) {
        @mkdir($logDirectory, 0775, true);
    }

    $written = @file_put_contents(
        $logFile,
        PHP_EOL . $message . PHP_EOL,
        FILE_APPEND | LOCK_EX
    );

    // Fallback sul log standard di PHP.
    if ($written === false) {
        error_log($message);
    }
});

/**
 * Includo il Pattern singleton Registry
 */
if (file_exists(CORE_PATH . 'System/Registry.php')) {
    require_once(CORE_PATH . 'System/Registry.php');
}
//----------------------------------------------------------------------------------------------------------------------

/**
 * Includo il Pattern singleton Registry
 */
if (file_exists(CORE_PATH . 'System/WhoopsPrettyPageHandler.php')) {
    require_once(CORE_PATH . 'System/WhoopsPrettyPageHandler.php');
}
//----------------------------------------------------------------------------------------------------------------------


/**
 * Carico il vendor esterno
 */
if (file_exists(PATH . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php')) {
    include_once(PATH . 'vendor' . DIRECTORY_SEPARATOR . 'autoload.php');
}


//----------------------------------------------------------------------------------------------------------------------

/**
 * Inizializzazione configurazione di sistema
 */
$config = new \Maer\Config\Config();
$config->load(APP_PATH . 'Config/app.php');
define('CHARSET', strtoupper($config->get('charset')));
//----------------------------------------------------------------------------------------------------------------------

/**
 * Carico la libreria per la gestione degli errori
 */
$config->load(APP_PATH . 'Config/errors_blacklist.php');
$blacklist = $config->get('blacklist');

//----------------------------------------------------------------------------------------------------------------------

/**
 * Inizializzazione configurazione di ambiente ENV
 */
if (file_exists(CORE_PATH . 'System/Env.php')) {
    require_once(CORE_PATH . 'System/Env.php');
}

/**
 * Inizializzazione configurazione dei plugins
 */
if (file_exists(CORE_PATH . 'System/Plugins.php')) {
    require_once(CORE_PATH . 'System/Plugins.php');
}

System\Env::load(PATH . '.env');
$whoops = new \Whoops\Run;

if (PHP_SAPI !== 'cli' || !defined('STDIN')) {

    if (ENVIRONMENT == 'development') {

        $errorPage = new Whoops\Handler\PrettyPageHandler();
        $reflection = new \ReflectionClass($errorPage);
        $property = $reflection->getProperty('searchPaths');
        $property->setAccessible(true);

        $property->setValue($errorPage, [THEME_PATH . 'views' . DIRECTORY_SEPARATOR . 'errors']);

        $errorPage->setPageTitle("Pat OS");
        if (!empty($blacklist) && is_array($blacklist)) {
            foreach ($blacklist as $key => $item) {
                if (!empty($item)) {
                    foreach ($item as $value) {
                        $errorPage->blacklist($key, $value);
                    }
                }
            }
        }

        if (
            (!empty($_SERVER['HTTP_X_REQUESTED_WITH'])
                && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest')
        ) {
            $whoops->pushHandler(new Whoops\Handler\JsonResponseHandler());
        } else {
            $whoops->pushHandler($errorPage);
        }

        $whoops->register();
    }
}

if(PHP_SAPI !== 'cli'){
    $whoops->register();
}

//----------------------------------------------------------------------------------------------------------------------

// Chart set di default
ini_set('default_charset', CHARSET);

/**
 *  Includo la retrocompatibilità con librerie della crittografia
 */

if (extension_loaded('mbstring')) {

    define('MB_ENABLED', TRUE);
    @ini_set('mbstring.internal_encoding', CHARSET);
    mb_substitute_character('none');
} else {

    define('MB_ENABLED', FALSE);
}


if (extension_loaded('iconv')) {

    define('ICONV_ENABLED', TRUE);
    @ini_set('iconv.internal_encoding', CHARSET);
} else {

    define('ICONV_ENABLED', FALSE);
}
//----------------------------------------------------------------------------------------------------------------------

/**
 * Caricamento delle classi in automatico
 */
spl_autoload_register(function ($className) {

    // Mappatura delle classi
    $className = str_replace("\\", DIRECTORY_SEPARATOR, $className);

    // Caricamento package - librerie di sistema
    if (file_exists(CORE_PATH . $className . '.php')) {
        include_once(CORE_PATH . $className . '.php');
    }


    // Caricamento classi HTTP
    if (file_exists(APP_PATH . $className . '.php')) {
        include_once(APP_PATH . $className . '.php');
    }
});

//----------------------------------------------------------------------------------------------------------------------

/**
 * Definisco la costante del tema
 */
define('THEME', $config->get('theme'));


//----------------------------------------------------------------------------------------------------------------------

/**
 * Se esiste carico le funzioni personalizzate dell'app
 */
if (file_exists(APP_PATH . 'Common.php')) {
    include_once APP_PATH . 'Common.php';
}

/**
 * Se esiste carico le funzioni personalizzate della tipologia di applicativo
 */
if (file_exists(APP_PATH . 'Common' . APP_TYPE . '.php')) {
    include_once APP_PATH . 'Common' . APP_TYPE . '.php';
}

/**
 * Caricamento le funzioni di sistema
 */
if (!file_exists(CORE_PATH . DIRECTORY_SEPARATOR . 'Common.php')) {
    exit('Funzioni di sistema non presente');
}
include_once(CORE_PATH . DIRECTORY_SEPARATOR . 'Common.php');

if (isPhp('5.6')) {

    @ini_set('php.internal_encoding', CHARSET);
}

//----------------------------------------------------------------------------------------------------------------------

/**
 * Registro la classe database nel container
 */
$container = System\Container::getInstance();
$container->singleton('db', function () {
    $getPdoInstance = new System\Database();
    return $getPdoInstance->getConnectionInstance();
});

//----------------------------------------------------------------------------------------------------------------------

/**
 * Registro il timezone nel framework
 */
$timezone = System\TimezoneHelper::getInstance();
$timezone->setTimezone(_env('TIMEZONE'));
$getTimezone = $timezone->getTimezone();
$container->singleton('timezone', function () use ($getTimezone) {
    return $getTimezone;
});

//----------------------------------------------------------------------------------------------------------------------

/**
 * Verifico e genero il CSRF Token e setto l'ini "gc_maxlifetime"
 */
$config->load(APP_PATH . 'Config/session.php');

if (!in_array('ini_set', explode(',', ini_get('disable_functions')))) {
    @ini_set('session.gc_maxlifetime', $config->get('max_lifetime'));
}

if ($config->get('csrf_enable') === true) {

    \System\Token::generate((int) $config->get('csrf_expire'));
}

//----------------------------------------------------------------------------------------------------------------------


// Alcune configurazioni Server non hanno le librerie di crittografia installate, quindi includo le funzioni che simulano
// le librerie di sistema per la crittografia HASH.
require_once(CORE_PATH . 'compat' . DIRECTORY_SEPARATOR . 'mbstring.php');
require_once(CORE_PATH . 'compat' . DIRECTORY_SEPARATOR . 'hash.php');
require_once(CORE_PATH . 'compat' . DIRECTORY_SEPARATOR . 'password.php');
require_once(CORE_PATH . 'compat' . DIRECTORY_SEPARATOR . 'standard.php');

//----------------------------------------------------------------------------------------------------------------------

/**
 * Includo tutti gli eventi.
 */
require_once(APP_PATH . 'Plugins.php');
require_once(APP_PATH . 'Events.php');

//----------------------------------------------------------------------------------------------------------------------

if (!is_cli()) {

    /**
     * Eventi lanciati in pre system
     */
    \System\Event::call('pre_system', []);

    /**
     * Eventi lanciati in pre route
     */
    \System\Event::call('pre_route', []);

    //----------------------------------------------------------------------------------------------------------------------

    $forceHeaders = config('force_send_header_output', null, 'app');

    if ($forceHeaders === true) {

        header('Content-Type: text/html; charset=' . CHARSET);
    }

    /**
     * Inizializzazione delle Rotte
     */

    // Rotte WEB
    require_once(APP_PATH . 'Routes' . DIRECTORY_SEPARATOR . 'Web.php');

    /**
     * Esecuzione delle rotte ed "Entry point" dell'applicazione.
     */
    \System\Route::handle();

    //----------------------------------------------------------------------------------------------------------------------


    /**
     * Eventi lanciati in post route
     */
    \System\Event::call('post_route', []);

    //----------------------------------------------------------------------------------------------------------------------

    /**
     * Eventi lanciati in post system
     */
    \System\Event::call('post_system', []);
} else {
    define('CRONTAB_NOW', time());

    /**
     * Eventi lanciati in pre system
     */
    \System\Event::call('pre_job', []);

    $fileJob = APP_PATH . 'Job.php';
    if (file_exists($fileJob)) {
        require_once($fileJob);
    }

    /**
     * Eventi lanciati in pre system
     */
    \System\Event::call('post_job', []);

    \System\CronQueue::getInstance()->exec();
}
