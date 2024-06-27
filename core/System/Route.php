<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use System\Input;
use \System\Uri;

class Route
{
    /**
     * @var array
     * @description Lista delle rotte
     */
    private static $routes = [];

    /**
     * @var string
     * @description Setta il middleware temporaneo per la costruzione di ogni rotta
     */
    private static $middleware;

    /**
     * @var string
     * @description Setta il prefix temporaneo per la costruzione di ogni rotta
     */
    private static $prefix;

    /**
     * @var bool
     * description Verififca se la rotta è stata è stata trovato
     */
    private static $routeFound = false;

    public function __construct()
    {
    }

    /**
     * @param $uri
     * @param $callback
     * @description Rotte intercettate a livello di database
     */
    public static function slug($uri, $callback)
    {
        static::add('GET', $uri, $callback);
    }

    /**
     * @param $prefix
     * @param $callback
     * @description Setta le richieste di tipo GET nelle costruzioni delle rotte
     */
    public static function get($uri, $callback)
    {
        static::add('GET', $uri, $callback);
    }

    /**
     * @param $prefix
     * @param $callback
     * @description Setta le richieste di tipo POST nelle costruzioni delle rotte
     */
    public static function post($uri, $callback)
    {
        static::add('POST', $uri, $callback);
    }

    /**
     * @param $prefix
     * @param $callback
     * @description Setta tutte le richieste nelle costruzioni delle rotte
     */
    public static function any($uri, $callback)
    {
        static::add('GET|POST|PATCH|DELETE|PUT|AJAX', $uri, $callback);
    }

    /**
     * @param $prefix
     * @param $callback
     * @description Setta tutte le richieste nelle costruzioni delle rotte
     */
    public static function getDb($uri, $callback)
    {
        static::add('DATABASE', $uri, $callback);
    }

    /**
     * @param $prefix
     * @param $callback
     * @description Setta le richieste di tipo PATCH nelle costruzioni delle rotte
     */
    public static function patch($uri = null, $callback = null)
    {
        static::add('PATCH', $uri, $callback);
    }

    /**
     * @param $prefix
     * @param $callback
     * @description Setta le richieste di tipo DELETE nelle costruzioni delle rotte
     */
    static public function delete($uri = null, $callback = null)
    {
        static::add('DELETE', $uri, $callback);
    }

    /**
     * @param $prefix
     * @param $callback
     * @description Setta le richieste di tipo PUT nelle costruzioni delle rotte
     */
    public static function put($uri = null, $callback = null)
    {
        self::add('PUT', $uri, $callback);
    }

    /**
     * @param $prefix
     * @param $callback
     * @description Setta le richieste di tipo AJAX nelle costruzioni delle rotte
     */
    public static function ajax($uri = null, $callback = null)
    {
        self::add('AJAX', $uri, $callback);
    }

    /**
     * @param $prefix
     * @param $callback
     * @description Setta le richieste di tipo AJAX nelle costruzioni delle rotte
     */
    public static function options($uri = null, $callback = null)
    {
        self::add('OPTIONS', $uri, $callback);
    }

    /**
     * @param $prefix
     * @param $callback
     * @description Setta tutte le richiesta per l'elaborazione e analisi delle rotte
     */
    private static function add($methods, $uri, $callback)
    {
        $segments = defined('CUSTOM_PATH') ? CUSTOM_PATH : '';
//        $uri = trim($uri, '/');
//
//        if (strlen($segments) >= 1 && strlen($uri) >= 1) {
//            $uri = (substr($segments, -1) !== '/' && $uri[0] !== '/')
//                ? $segments . '/' . $uri
//                : $segments . $uri;
//        }
//
//        $uri = rtrim(static::$prefix . '/' . trim($uri, '/'), '/');
//        // $uri = $uri ?: '/';
//        if ((int)strlen($uri) === 0) {
//            $uri = strlen($segments)>=0 ? $segments : '/';
//        }
        $uri = rtrim(static::$prefix . '/' . trim($uri, '/'), '/');
        $uri = verifyFirstOrLastSlash($uri);

        if ((int)strlen($uri) === 0) {
            $uri = strlen($segments)>=0 ? $segments : '/';
        }

        foreach (explode('|', $methods) as $method) {

            static::$routes[] = [
                'uri' => $uri,
                'callback' => $callback,
                'method' => $method,
                'middleware' => static::$middleware,
            ];

        }

    }

    /**
     * @param $prefix
     * @param $callback
     * @description Setta i prefissi in fase di costruzione delle rotte
     */
    public static function prefix($prefix, $callback)
    {
        $parentPrefix = static::$prefix;

        static::$prefix .= '/' . trim($prefix, '/');

        if (is_callable($callback)) {

            call_user_func($callback);

        } else {

            throw new \BadFunctionCallException('[Route/Middleware] - non è una funzione richiamabile nelle Rotte.');

        }

        static::$prefix = $parentPrefix;
    }

    /**
     * @param $middleware
     * @param $callback
     * @return mixed
     * @description Setta i Middleware in fase di costruzione delle rotte
     */
    public static function middleware($middleware, $callback)
    {
        $parentMiddleware = static::$middleware;

        static::$middleware .= '|' . trim($middleware, '|');

        if (is_callable($callback)) {

            call_user_func($callback);

        } else {

            throw new \BadFunctionCallException('[Route/Middleware] - non è una funzione richiamabile nelle Rotte.');

        }

        static::$middleware = $parentMiddleware;

    }

    /**
     * @return mixed
     * @throws \ReflectionException
     * @description Avvia l'analisi delle rotte nelle uri
     */
    public static function handle()
    {

        $config = new \Maer\Config\Config();
        $config->load(APP_PATH . 'Config/app.php');
        $urlSuffix = $config->get('url_suffix');

        $matched = true;

        $request = new Uri();

        $uri = '/' . str_replace($urlSuffix, '', $request->uriString());

        foreach (static::$routes as $route) {
            $route['uri'] = self::setPattern($route['uri']);

            if ((bool)preg_match($route['uri'], $uri, $matches) === true) {

                array_shift($matches);

                $params = array_values($matches);

                foreach ($params as $param) {

                    if (strpos($param, '/')) {

                        $matched = false;

                    }

                }

                if ((string)$route['method'] !== (string)static::getMethod($route['method'])) {

                    $matched = false;

                } else {

                    $matched = true;

                }

                if ($matched === true) {

                    static::$routeFound = true;
                    return static::invoke($route, $params);

                }

            }

        }

        // Rotta non trovata, stampo l'errore 404
        if (static::$routeFound === false) {

            if (!HAS_API) {
                // WEB
                echo show404();
            } else {
                // API
                $response = new \System\JsonResponse();
                $response->error('error', 'Ops.. Risorsa non trovata');
                $response->setStatusCode(404);
                $response->response();
            }

            die();
        }

    }

    /**
     * @return array
     * @description Stampa tutte le rotte
     */
    public static function allRoutes()
    {
        return static::$routes;
    }

    /**
     * @param $pattern
     * @return string
     * @description Espressione regolare per parametrizzare e convertire le rigieste nelle uri
     */
    protected static function setPattern($pattern)
    {

        $pattern = str_replace(
            [
                ':any',
                ':num',
                ':alpha_num',
                ':alpha',
                ':alpha_num_hyphen',
            ],

            [
                '[^/]+',
                '[0-9]+',
                '[a-zA-Z\-0-9]+',
                '[a-zA-Z]+',
                '[\w-]+'
            ],

            self::removeDupleSlash($pattern)
        );

        // defined('CUSTOM_PATH') ? CUSTOM_PATH : '' .
        return trim('#^' . $pattern . '$#');

    }

    /**
     * @param $uri
     * @return null|string|string[]
     * @description Rimuove i doppi slashes nelle richieste uri
     */
    static protected function removeDupleSlash($uri)
    {

        return preg_replace('/\/+/', '/', '/' . $uri);

    }

    /**
     * @param $method
     * @return string
     * @description Verifica la tipologia di richiesta in entrata
     */
    protected static function getMethod($method)
    {

        if ($method === 'AJAX') {

            $getRequest = (Input::isAjax() === true) ? 'AJAX' : '';

        } elseif ($method === 'DATABASE') {

            $getRequest = 'DATABASE';

        } else {

            $getRequest = Input::method(true);
        }

        return $getRequest;
    }

    /**
     * @param $route
     * @param array $params
     * @return mixed
     * @throws \ReflectionException
     * @description Inizializzazione delle rotte
     */
    public static function invoke($route, $params = [])
    {

        static::executeMiddleware($route);

        $callback = $route['callback'];

        if (is_callable($callback)) {

            return call_user_func_array($callback, $params);

        } elseif (strpos((string)$callback, '@') !== false) {

            list($controller, $method) = explode('@', $callback);

            if (class_exists($controller)) {

                $object = new $controller;

                if (method_exists($object, $method)) {

                    return call_user_func_array([$object, $method], $params);

                } else {

                    if (ENVIRONMENT === 'development') {
                        throw new \BadFunctionCallException('[Route/invoke] - il Metodo ' . $method . 'non esiste nel Controlloer ' . $controller);
                    } else {
                        echo showError('Error', '[Route/invoke] - il Metodo ' . $method . 'non esiste nel Controlloer ' . $controller);
                        die();
                    }

                }

            } else {

                if (ENVIRONMENT === 'development') {
                    throw new \ReflectionException('[Route/invoke] - Classe ' . $controller . ' non trovata');
                } else {
                    echo showError('Error', '[Route/invoke] - Classe ' . $controller . ' non trovata');
                    die();
                }

            }

        } else {

            if (ENVIRONMENT === 'development') {
                throw new \InvalidArgumentException('[Route/invoke] - Inserisci una rotta valida');
            } else {
                echo showError('Error', '[Route/invoke] - Inserisci una rotta valida');
                die();
            }

        }
    }

    /**
     * @param $routes
     * @return mixed
     * @throws \ReflectionException
     * @description Esecuzione dei Middleware
     */
    protected static function executeMiddleware($routes)
    {
        $middlewares = explode('|', (string)$routes['middleware']);

        if (!empty($middlewares)) {
            foreach ($middlewares as $middleware) {

                if ($middleware != '') {

                    $middleware = preg_replace('/\|/', '', (string)trim($middleware));

                    if (class_exists($middleware)) {

                        $object = new $middleware;

                        $return = call_user_func_array([$object, 'handle'], []);

                    } else {

                        if (ENVIRONMENT === 'development') {
                            throw new \ReflectionException('[Route/invoke] - Classe Middleware ' . $middleware . ' non trovato');
                        } else {
                            echo showError('Error', '[Route/invoke] - Classe Middleware ' . $middleware . ' non trovato');
                            die();
                        }
                    }

                }

            }
        }
    }

    /**
     * @return bool
     * @description Verifica se è stata trovata una rotta valida
     */
    protected static function routeFound()
    {
        return static::$routeFound;
    }
}