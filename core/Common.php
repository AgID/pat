<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

use System\Cache;
use System\JsonResponse;
use System\Security;
use System\Session;
use System\Uri;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

if (!function_exists('isHttps')) {

    /**
     * Verifica se un dominio è in https
     *
     * @return bool
     */
    function isHttps(): bool
    {
        if (!empty($_SERVER['HTTPS']) && strtolower($_SERVER['HTTPS']) !== 'off') {

            return true;
        } elseif (isset($_SERVER['HTTP_X_FORWARDED_PROTO']) && strtolower($_SERVER['HTTP_X_FORWARDED_PROTO']) === 'https') {

            return true;
        } elseif (!empty($_SERVER['HTTP_FRONT_END_HTTPS']) && strtolower($_SERVER['HTTP_FRONT_END_HTTPS']) !== 'off') {

            return true;
        }

        return false;
    }
}


if (!function_exists('removeInvisibleCharacters')) {

    /**
     * Rimuove i caratteri invisibili della stringa passata
     *
     * @param string|array|null $str
     * @param bool $url_encoded
     * @return null|string|string[]
     */
    function removeInvisibleCharacters(string|array|null $str = '', bool $url_encoded = true): array|string|null
    {
        $nonDisplayables = [];

        if ($url_encoded) {
            $nonDisplayables[] = '/%0[0-8bcef]/i'; // url encoded 00-08, 11, 12, 14, 15
            $nonDisplayables[] = '/%1[0-9a-f]/i'; // url encoded 16-31
            $nonDisplayables[] = '/%7f/i';  // url encoded 127
        }

        $nonDisplayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';    // 00-08, 11, 12, 14-31, 127

        do {

            $str = @preg_replace($nonDisplayables, '', $str, -1, $count);
        } while ($count);

        return $str;
    }
}

if (!function_exists('htmlEscape')) {

    /**
     * escape html
     *
     * @param string|array|null $var
     * @param bool $double_encode
     * @return array|string|null
     */
    function htmlEscape(string|array|null $var = '', bool $double_encode = true): array|string|null
    {
        if (empty($var)) {
            return $var;
        }

        if (is_array($var)) {

            foreach (array_keys($var) as $key) {

                $var[$key] = htmlEscape($var[$key], $double_encode);
            }

            return $var;
        }

        return htmlspecialchars($var, ENT_QUOTES, config('charset', null, 'app'), $double_encode);
    }
}

if (!function_exists('getallheaders')) {

    /**
     * Get all HTTP header key/values as an associative array for the current request.
     *
     * @return array[string] The HTTP header key/value pairs.
     */
    function getallheaders(): array
    {
        $headers = array();

        $copyServer = [
            'CONTENT_TYPE' => 'Content-Type',
            'CONTENT_LENGTH' => 'Content-Length',
            'CONTENT_MD5' => 'Content-Md5',
        ];

        foreach ($_SERVER as $key => $value) {

            if (str_starts_with($key, 'HTTP_')) {

                $key = substr($key, 5);

                if (!isset($copyServer[$key]) || !isset($_SERVER[$key])) {

                    $key = str_replace(' ', '-', ucwords(strtolower(str_replace('_', ' ', $key))));

                    $headers[$key] = $value;
                }
            } elseif (isset($copyServer[$key])) {

                $headers[$copyServer[$key]] = $value;
            }
        }

        if (!isset($headers['Authorization'])) {

            if (isset($_SERVER['REDIRECT_HTTP_AUTHORIZATION'])) {

                $headers['Authorization'] = $_SERVER['REDIRECT_HTTP_AUTHORIZATION'];
            } elseif (isset($_SERVER['PHP_AUTH_USER'])) {

                $basicPass = $_SERVER['PHP_AUTH_PW'] ?? '';

                $headers['Authorization'] = 'Basic ' . base64_encode($_SERVER['PHP_AUTH_USER'] . ':' . $basicPass);
            } elseif (isset($_SERVER['PHP_AUTH_DIGEST'])) {

                $headers['Authorization'] = $_SERVER['PHP_AUTH_DIGEST'];
            }
        }

        return $headers;
    }
}

if (!function_exists('isPhp')) {

    /**
     * Verifico la versione php
     *
     * @param string $version
     * @return mixed
     */
    function isPhp(string $version = ''): mixed
    {
        static $isPhp;

        if (!isset($isPhp[$version])) {

            $isPhp[$version] = version_compare(PHP_VERSION, $version, '>=');
        }

        return $isPhp[$version];
    }
}


// Funzione che carica gli helpers
if (!function_exists('helper')) {

    /**
     * Include le funzioni di helpers
     *
     * @param $helpers
     */
    function helper($helpers = null): void
    {

        if ($helpers !== null || is_array($helpers) || $helpers !== '') {

            if (!is_array($helpers)) {

                $pathFileCore = CORE_PATH . 'Helpers' . DIRECTORY_SEPARATOR . slashUpperCaseFirst($helpers) . '.php';
                $pathFileApp = APP_PATH . 'Helpers' . DIRECTORY_SEPARATOR . slashUpperCaseFirst($helpers) . '.php';

                if (file_exists($pathFileCore) || file_exists($pathFileApp)) {

                    if (file_exists($pathFileCore)) {
                        require_once($pathFileCore);
                    }

                    if (file_exists($pathFileApp)) {
                        require_once($pathFileApp);
                    }
                } else {

                    echo showError('Attenzione', 'Il seguente helper non è stato trovato: ' . $helpers);
                    exit();
                }
            } else {

                foreach ($helpers as $helper) {

                    $pathFileCore = CORE_PATH . 'Helpers' . DIRECTORY_SEPARATOR . slashUpperCaseFirst($helper) . '.php';
                    $pathFileApp = APP_PATH . 'Helpers' . DIRECTORY_SEPARATOR . slashUpperCaseFirst($helper) . '.php';

                    if (file_exists($pathFileCore) || file_exists($pathFileApp)) {

                        if (file_exists($pathFileCore)) {
                            require_once($pathFileCore);
                        }

                        if (file_exists($pathFileApp)) {
                            require_once($pathFileApp);
                        }
                    } else {

                        echo showError('Attenzione', 'Il seguente helper non è stato trovato: ' . $helpers);
                        exit();
                    }
                }
            }
        } else {

            echo showError('Attenzione', 'Nessun argomento passato per il caricamento dell\'helper');
            exit();
        }
    }
}

if (!function_exists('slashUpperCaseFirst')) {

    function slashUpperCaseFirst($string): string
    {
        $data = [];
        $segments = explode('/', $string);

        foreach ($segments as $segment) {

            $data[] = ucfirst($segment);
        }

        return implode('/', $data);
    }
}

if (!function_exists('siteUrl')) {

    /**
     * Ritorna il site url
     *
     * @param string $uri
     * @param null $protocol
     * @return string
     * @throws Exception
     */
    function siteUrl(string $uri = '', $protocol = null): string
    {
        $base = new \System\Base();
        return $base->siteUrl($uri, $protocol);
    }
}

if (!function_exists('baseUrl')) {

    /**
     * ritorna in base url
     *
     * @param string $uri
     * @param null $protocol
     * @return string
     * @throws Exception
     */
    function baseUrl(string $uri = '', $protocol = null): string
    {
        $base = new \System\Base();
        return $base->baseUrl($uri, $protocol);
    }
}

if (!function_exists('currentUrl')) {

    /**
     * Ritorna la url corrente
     *
     * @return string
     * @throws Exception
     */
    function currentUrl(): string
    {
        $base = new \System\Base();
        $uri = new Uri();

        $config = new \Maer\Config\Config();
        $config->load(APP_PATH . 'Config/app.php');

        $urlSuffix = $config->get('url_suffix');

        return $base->siteUrl(preg_replace('/' . $urlSuffix . '/', '', $uri->uriString()));
    }
}

if (!function_exists('currentQueryStringUrl')) {

    /**
     * Ritorna la url e Query String corrente
     *
     * @param bool $xss
     * @param bool $htmlEntities
     * @return string
     * @throws Exception
     */
    function currentQueryStringUrl(bool $xss = true, bool $htmlEntities = true): string
    {
        $base = new \System\Base();
        $uri = new Uri();


        $config = new \Maer\Config\Config();
        $config->load(APP_PATH . 'Config/app.php');

        $urlSuffix = $config->get('url_suffix');
        return $base->siteUrl(preg_replace('/' . $urlSuffix . '/', '', (string)$uri->fullUrl($xss, $htmlEntities)));
    }
}

if (!function_exists('is_cli')) {

    /**
     * Verifica se è stata effettuata una richiesta dalla riga di comando.
     */
    function is_cli(): bool
    {
        return (PHP_SAPI === 'cli' or defined('STDIN'));
    }
}

if (!function_exists('show404')) {

    /**
     * Pagina 404.
     * @throws Exception
     */
    function show404($heading = null, $message = null)
    {

        setStatusHeader(404);

        $config = new \Maer\Config\Config();
        $config->load(APP_PATH . 'Config/app.php');

        $pathTheme = $config->get('theme', 'default');

        $heading = ($heading === null) ? '404 Page Not Found' : $heading;
        $message = ($message === null) ? 'The page you requested was not found.' : $message;

        $d = DIRECTORY_SEPARATOR;

        $filePath = APP_PATH . 'Themes' . $d . $pathTheme . $d . 'errors' . $d . '404.php';

        if (ob_get_level() > 1) {

            ob_end_flush();
        }

        ob_start();
        include($filePath);
        $buffer = ob_get_contents();
        ob_end_clean();

        return $buffer;
    }
}

if (!function_exists('removeInvisibleCharacters')) {

    /**
     * @description Rimuove i caratteri invisibili della stringa passata
     * @param string $string
     * @param bool $urlEncoded
     * @return null|string|string[]
     */
    function removeInvisibleCharacters(string $string = '', bool $urlEncoded = true): array|string|null
    {
        return \System\Formatting::removeInvisibleCharacters($string, $urlEncoded);
    }
}

if (!function_exists('htmlEscape')) {

    /**
     * @description escape html
     * @param string $var
     * @param string $charset
     * @param bool $doubleEncode
     * @return array|string
     */
    function htmlEscape(string $var = '', string $charset = CHARSET, bool $doubleEncode = true): array|string
    {
        return \System\Formatting::htmlEscape($var, $charset, $doubleEncode);
    }
}

if (!function_exists('convertEncodeQuotes')) {

    function convertEncodeQuotes(string $input = '', string $charset = CHARSET, string $htmlEntities = "HTML-ENTITIES"): string
    {
        return \System\Formatting::convertEncodeQuotes($input, $charset, $htmlEntities);
    }
}

if (!function_exists('showError')) {

    /**
     * Pagina di errore generico.
     * @throws Exception
     */
    function showError($heading = null, $message = null, $statusCode = 500, $print = false)
    {

        if (is_cli()) {

            $message = "\t" . (is_array($message) ? implode("\n\t", $message) : $message);

            echo "\nERROR: ", $heading, "\n\n", $message, "\n\n";
        } else {


            setStatusHeader($statusCode);

            $heading = ($heading === null) ? 'Error' : $heading;
            $message = ($message === null) ? 'General error.' : $message;

            $config = new \Maer\Config\Config();
            $config->load(APP_PATH . 'Config/app.php');

            $pathTheme = $config->get('theme', 'default');

            $d = DIRECTORY_SEPARATOR;

            $filePath = APP_PATH . 'Themes' . $d . $pathTheme . $d . 'errors' . $d . 'general_error.php';

            if (ob_get_level() > 1) {

                ob_end_flush();
            }

            ob_start();
            include($filePath);
            $buffer = ob_get_contents();
            ob_end_clean();

            if ($print === true) {

                echo $buffer;
                die();
            } else {

                return $buffer;
            }
        }

        die();
    }
}

if (!function_exists('setStatusHeader')) {

    /**
     * Setta nell'HTTP l'header dello stato
     *
     * @param int|null $code the status code
     * @param string $text
     * @return array|string|string[]|void
     * @throws Exception
     */
    function setStatusHeader(int|null $code = 200, string $text = '')
    {
        if (is_cli()) {
            return;
        }

        if (empty($code) or !is_numeric($code)) {

            echo showError('Il codice di stato deve essere un numero', 500);
            exit();
        }

        if (empty($text)) {

            is_int($code) or $code = (int)$code;

            $stati = [
                100 => 'Continue',
                101 => 'Switching Protocols',

                200 => 'OK',
                201 => 'Created',
                202 => 'Accepted',
                203 => 'Non-Authoritative Information',
                204 => 'No Content',
                205 => 'Reset Content',
                206 => 'Partial Content',

                300 => 'Multiple Choices',
                301 => 'Moved Permanently',
                302 => 'Found',
                303 => 'See Other',
                304 => 'Not Modified',
                305 => 'Use Proxy',
                307 => 'Temporary Redirect',

                400 => 'Bad Request',
                401 => 'Unauthorized',
                402 => 'Payment Required',
                403 => 'Forbidden',
                404 => 'Not Found',
                405 => 'Method Not Allowed',
                406 => 'Not Acceptable',
                407 => 'Proxy Authentication Required',
                408 => 'Request Timeout',
                409 => 'Conflict',
                410 => 'Gone',
                411 => 'Length Required',
                412 => 'Precondition Failed',
                413 => 'Request Entity Too Large',
                414 => 'Request-URI Too Long',
                415 => 'Unsupported Media Type',
                416 => 'Requested Range Not Satisfiable',
                417 => 'Expectation Failed',
                422 => 'Unprocessable Entity',
                426 => 'Upgrade Required',
                428 => 'Precondition Required',
                429 => 'Too Many Requests',
                431 => 'Request Header Fields Too Large',

                500 => 'Internal Server Error',
                501 => 'Not Implemented',
                502 => 'Bad Gateway',
                503 => 'Service Unavailable',
                504 => 'Gateway Timeout',
                505 => 'HTTP Version Not Supported',
                511 => 'Network Authentication Required',
            ];

            if (isset($stati[$code])) {

                $text = $stati[$code];
            } else {

                throw new \Exception('Nessuno codice dello stato trovare. Inserisci uno codice valido.');
            }
        }

        if (strpos(PHP_SAPI, 'cgi') === 0) {
            @header('Status: ' . $code . ' ' . $text, true);
            return;
        }

        $server_protocol = (isset($_SERVER['SERVER_PROTOCOL']) && in_array($_SERVER['SERVER_PROTOCOL'], array('HTTP/1.0', 'HTTP/1.1', 'HTTP/2'), true))
            ? $_SERVER['SERVER_PROTOCOL'] : 'HTTP/1.1';
        header($server_protocol . ' ' . $code . ' ' . $text, true, $code);

        if (!empty($stati[$code]) && $text === '') {

            return $stati[$code];
        }

        if ($text !== '') {

            return [
                $code => $text
            ];
        }
    }
}

if (!function_exists('getMimes')) {

    /**
     * Ritorna la lista dei mimes
     */
    function &getMimes()
    {

        $mimes = file_exists(APP_PATH . 'Config/mimes.php') ? include(APP_PATH . 'Config/mimes.php') : [];

        return $mimes;
    }
}

if (!function_exists('stringifyAttributes')) {
    /**
     * creazione della stringa per gli attributi in uso nei tag HTML
     *
     *
     * Funzione di supporto utilizzata per convertire una stringa, un array o un oggetto
     * in attributi a una stringa.
     *
     * @param mixed    string, array, object
     * @param bool
     * @return    string
     */
    function stringifyAttributes($attributes = '', $js = false)
    {
        $atts = null;

        if (empty($attributes)) {

            return null;
        }

        if (is_string($attributes)) {

            return ' ' . $attributes;
        }

        $attributes = (array)$attributes;

        foreach ($attributes as $key => $val) {

            $atts .= ($js) ? $key . '=' . $val . ',' : ' ' . $key . '="' . $val . '"';
        }

        return rtrim($atts, ',');
    }
}

if (!function_exists('__')) {


    /**
     * Funzione traduzione - internazionalizzazione
     *
     * @param null $key
     * @param null $value
     * @param null $file
     * @return array|mixed|null
     * @throws Exception
     */
    function __($key = null, $value = null, $file = null)
    {
        $config = new \Maer\Config\Config();
        $config->load(APP_PATH . 'Config/app.php');

        $defaultLang = $config->get('language', 'it');

        $fileName = ($file !== null) ? $file : 'langs';

        $pathDefaultLang = 'Langs' . DIRECTORY_SEPARATOR . $defaultLang . DIRECTORY_SEPARATOR . $fileName . '.php';

        if (file_exists(APP_PATH . $pathDefaultLang)) {

            if ($key !== null) {

                $lang = new \Maer\Config\Config();
                $lang->load(APP_PATH . $pathDefaultLang);

                return $lang->get($key, $value);
            }

            return null;
        } else {

            throw new \Exception("Errore caricamento lingua: la lingua che stai provando a caricare non esiste. '{$pathDefaultLang}' ");
        }
    }
}

if (!function_exists('isReallyWritable')) {
    /**
     * Test per la scrivibilità dei file
     *
     * is_writable () restituisce TRUE sui server Windows quando non si può davvero scrivere
     * il file, basato sull'attributo di sola lettura. is_writable() è anche inaffidabile
     * sui server Unix se il safe_mode è attivo.
     *
     * @link    https://bugs.php.net/bug.php?id=54709
     * @param string $file
     * @return    bool
     */
    function isReallyWritable(string $file = ''): bool
    {
        if (DIRECTORY_SEPARATOR === '/' && (isPhp('5.4') or !ini_get('safe_mode'))) {

            return is_writable($file);
        }

        if (is_dir($file)) {

            $file = rtrim($file, '/') . '/' . md5(mt_rand());

            if (($fp = @fopen($file, 'ab')) === false) {

                return false;
            }

            fclose($fp);
            @chmod($file, 0777);
            @unlink($file);
            return true;
        } elseif (!is_file($file) or ($fp = @fopen($file, 'ab')) === false) {

            return false;
        }

        fclose($fp);
        return true;
    }
}

if (!function_exists('loadConfigMail')) {

    function loadConfigMail($provider = 'default')
    {
        $config = new \Maer\Config\Config();
        $config->load(APP_PATH . 'Config/email.php');

        return $config->get($provider);
    }
}

if (!function_exists('function_usable')) {
    /**
     * Function usable
     *
     * @link    http://www.hardened-php.net/suhosin/
     * @param string $functionName Function to check for
     * @return    bool    TRUE if the function exists and is safe to call,
     *            FALSE otherwise.
     */
    function function_usable(string $functionName = ''): bool
    {
        static $suhosinFuncBlacklist;

        if (function_exists($functionName)) {
            if (!isset($suhosinFuncBlacklist)) {
                $suhosinFuncBlacklist = extension_loaded('suhosin')
                    ? explode(',', trim(ini_get('suhosin.executor.func.blacklist')))
                    : array();
            }

            return !in_array($functionName, $suhosinFuncBlacklist, true);
        }

        return false;
    }
}

if (!function_exists('config')) {

    /**
     * @param null $key
     * @param null $default
     * @param null $file
     * @return array|mixed|null
     * @throws Exception
     */
    function config($key = null, $default = null, $file = null): mixed
    {
        if ($key !== null && $file !== null) {

            $config = new \Maer\Config\Config();
            $config->load(APP_PATH . 'Config/' . $file . '.php');

            return $config->get($key, $default);
        }

        return null;
    }
}

if (!function_exists('objectToArray')) {

    /**
     * @param string|array|object|null $data
     * @return array|string|null
     */
    function objectToArray(string|array|null|object $data = ''): array|string|null
    {
        if (is_array($data) || is_object($data)) {

            $result = array();

            foreach ($data as $key => $value) {

                $result[$key] = objectToArray($value);
            }

            return $result;
        }

        return $data;
    }
}

if (!function_exists('render')) {

    /**
     * @param string $layout
     * @param array $data
     * @param null $theme
     * @param bool $overwrite
     * @param array $dirs
     * @return void
     * @throws Exception
     */
    function render(string $layout = '', array $data = [], $theme = null, bool $overwrite = false, array $dirs = []): void
    {
        \System\Layout::view($layout, $data, $theme, $overwrite, $dirs);
    }
}

if (!function_exists('trace')) {
    /**
     * @param string|array|object $var
     * @param bool $exit
     * @param bool $v_dump
     * @param bool $label
     */
    function trace(string|array|object $var = '', bool $exit = false, bool $v_dump = true, bool $label = false): void
    {

        if ($label !== false) {
            echo "<div style='display: block;'><h2 style='color: red;'>" . $label . '</h2></div>';
        }

        echo('<pre>');


        echo "<hr />";
        if ($v_dump === true) {

            var_dump($var);
        } else {

            print_r($var);
        }
        echo "<hr />";

        echo('</pre>');

        if ($exit) {

            exit();
        }
    }
}

if (!function_exists('dump')) {
    /**
     * @return void
     */
    function dump()
    {
        // Ottengo il numero di argomenti passati alla funzione
        $num_args = func_num_args();

        // Verifica se ci sono argomenti da analizzare
        if ($num_args > 0) {
            // Avvia l'output buffering
            ob_start();

            // Stampa un'intestazione
            echo "<pre style='background-color: #f5f5f5; border: 1px solid #ccc; padding: 10px; font-family: monospace;'>";

            // Itera sugli argomenti passati alla funzione
            for ($i = 0; $i < $num_args; $i++) {
                // Ottengo l'argomento corrente
                $arg = func_get_arg($i);

                // Utilizza var_dump() per analizzare e stampare l'argomento
                var_dump($arg);

                // Aggiungi una riga vuota tra gli argomenti, tranne l'ultimo
                if ($i < $num_args - 1) {
                    echo "\n\n";
                }
            }

            // Chiude l'intestazione
            echo "</pre>";

            // Pulisce l'output buffering e invia il contenuto al browser
            ob_end_flush();
        }
    }
}

if (!function_exists('traceHidden')) {

    /**
     * @param string $var
     * @param bool $exit
     * @param bool $v_dump
     */
    function traceHidden(string $var = '', bool $exit = false, bool $v_dump = true): void
    {

        echo "<!--";
        trace($var, $exit, $v_dump, false);
        echo "-->";
    }
}

if (!function_exists('session')) {

    /**
     * @param null $adaptor
     * @param string $registry
     * @return Session
     * @throws Exception
     */
    function session($adaptor = null, string $registry = ''): Session
    {
        return new System\Session($adaptor, $registry);
    }
}

if (!function_exists('cache')) {

    /**
     * @param null $adaptor
     * @param null $expire
     * @return Cache
     * @throws Exception
     */
    function cache($adaptor = null, $expire = null): Cache
    {
        return new System\Cache($adaptor, $expire);
    }
}

if (!function_exists('security')) {

    /**
     * @param bool $charset
     * @return Security
     */
    function security($charset = false): Security
    {
        return new System\Security($charset);
    }
}

if (!function_exists('uri')) {

    /**
     * @return Uri
     */
    function uri(): Uri
    {
        return new System\Uri();
    }
}

if (!function_exists('jsonResponse')) {

    /**
     * Funzione che restituisce un nuovo JsonResponse
     *
     * @return JsonResponse
     */
    function jsonResponse(): JsonResponse
    {
        return new JsonResponse();
    }
}

if (!function_exists('_env')) {

    /**
     * Funzione che restituisce il valore di una variabile di ambiente
     *
     * @param null $string
     * @param null $default
     * @param bool $xssClean
     * @param bool $sanitizeKey
     * @param bool $sanitizeData
     * @return string|null
     */
    function _env($string = null, $default = null, bool $xssClean = true, bool $sanitizeKey = true, bool $sanitizeData = true): ?string
    {

        $env = null;
        $sanitize = new \System\SanitizeRequest();

        if (array_key_exists($string, $_ENV)) {

            $env = $sanitize->fetchFromArray($_ENV, $string, $xssClean, $sanitizeKey, $sanitizeData);
        } else {

            if ($default != null) {

                $env = $sanitize->fetchFromArray($_ENV, $default, $xssClean, $sanitizeKey, $sanitizeData);
            }
        }

        return $env;
    }
}

if (!function_exists('csrf_token')) {

    /**
     * Funzione che restituisce il nome del token
     *
     * @return array|string|null
     * @throws Exception
     */
    function csrf_token(): array|string|null
    {
        return config('csrf_token_name', null, 'app');
    }
}

if (!function_exists('csrf_hash')) {

    /**
     * Funzione che restituisce l'hash del token
     *
     * @param null $hashName
     * @return string|null
     */
    function csrf_hash($hashName = null): ?string
    {
        return \System\Token::getToken();
    }
}

if (!function_exists('csrf_meta')) {

    /**
     * Funzione che il tag meta del token
     *
     * @param null $hashName
     * @return string|null
     * @throws Exception
     */
    function csrf_meta($hashName = null): ?string
    {
        return '<meta name="' . csrf_token() . '" content="' . csrf_hash($hashName) . '">' . "\n";
    }
}

if (!function_exists('escapeXss')) {

    /**
     * Funzione di sanificazione contro gli XSS
     * @param string|array|null $string |array $string $string
     * @param bool $xss
     * @param bool $htmlEscape
     * @return array|string
     */
    function escapeXss(string|array|null $string = '', bool $xss = true, bool $htmlEscape = true): array|string
    {

        if ($xss) {
            $string = (new Security())->xssClean($string);
        }

        if ($htmlEscape) {
            $string = htmlEscape($string);
        }

        return $string;
    }
}
