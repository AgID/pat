<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class Modules
{
    /**
     * $modules
     *
     * @var array
     */
    protected static $modules = [];

    /**
     * $routes
     *
     * @var array
     */
    protected static $routes = [];

    /**
     * Instanza della classe Plugin
     *
     * @var Modules
     */
    protected static $instance = null;

    /**
     * Inizializzazione dei plugins
     *
     * @return Modules
     */
    public static function init()
    {
        if (!isset(self::$instance)) {
            self::$instance = new Modules();
        }
        return self::$instance;
    }

    public static function routes()
    {
        return self::$modules;
    }

    /**
     * Metodo clone protetto per imporre il comportamento singleton.
     *
     * @access  protected
     */
    protected function __clone()
    {

    }

    /**
     * Costruttore della classe
     */
    protected function __construct()
    {
        // Get plugins Table
        self::scannerModules();
    }

    /**
     * @param $data
     */
    public static function register($data)
    {
        self::$modules[] = $data;
    }

    /**
     * Carico tutti i moduli installati
     */
    private static function scannerModules()
    {
        // Path che contiene i moduli per la scannerizzazione.
        $pathModules = APP_PATH . 'Modules' . DIRECTORY_SEPARATOR;

        // Ciclo tutte le cartelle dei mooduli installati
        if (is_dir($pathModules) && $dh = opendir($pathModules)) {

            while ($fn = readdir($dh)) {

                if ($fn != '.' && $fn != '..' && is_dir($pathModules . DIRECTORY_SEPARATOR . $fn)) {

                    $moduleConfig = $pathModules . $fn . DIRECTORY_SEPARATOR . 'Module.php';
                    $RoutesWeb = $pathModules . $fn . DIRECTORY_SEPARATOR . 'Mvc' . DIRECTORY_SEPARATOR . 'Routes' . DIRECTORY_SEPARATOR . 'Web.php';
                    $ModuleApi = $pathModules . $fn . DIRECTORY_SEPARATOR . 'Mvc' . DIRECTORY_SEPARATOR . 'Routes' . DIRECTORY_SEPARATOR . 'Api.php';

                    // Se esiste il fine di configurazione del modulo lo includo.
                    if (file_exists($moduleConfig)) {
                        include_once($moduleConfig);
                    }

                    // Carico tutte le rotte WEB
                    if (file_exists($RoutesWeb)) {
                        include_once($RoutesWeb);
                    }

                    // Carico tutte le rotte API
                    if (file_exists($ModuleApi)) {
                        include_once($ModuleApi);
                    }

                }

            }

        }

        if (!empty(self::$modules) && is_countable(self::$modules) && count(self::$modules) >= 1) {

            foreach (self::$modules as $module) {

                if (!empty($module['install']) && is_callable($module['install'])) {

                    // \call_user_func_array($module['install'], []);

                }

                if (!empty($module['classMap'])) {

                    foreach ($module['classMap'] AS $className) {

                        spl_autoload_register(function () use($className) {

                            // Mappatura delle classi
                            $className = str_replace("\\", DIRECTORY_SEPARATOR, $className);
                            $pathClassName = APP_PATH . 'Modules' . $className . '.php';

                            if (file_exists($pathClassName)) {

                                include_once($pathClassName);

                            }

                        });

                    }

                }

            }

        }

    }
}
