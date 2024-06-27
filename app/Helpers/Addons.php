<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers;

use System\Registry;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class Addons
{

    /**
     * Carico tutti gli addons HTTP
     */
    public static function init()
    {
        $pathAddons = self::pathAddons();
        $getIdentity = authPatOs()->getIdentity();

        self::loadModels();

        $addonsEnabled = !empty($getIdentity['options']['addons_path'])
            ? unserialize($getIdentity['options']['addons_path'])
            : self::enabledFolders();

        if (is_dir($pathAddons) && $addonsEnabled !== null) {
            $dh = opendir($pathAddons);

            if ($dh) {

                while ($fn = readdir($dh)) {

                    if ($fn != '.' &&
                        $fn != '..' &&
                        is_dir($pathAddons . $fn) &&
                        file_exists($pathAddons . $fn . DIRECTORY_SEPARATOR . 'Register.php') &&
                        in_array($fn, $addonsEnabled)
                    ) {

                        $register = static::read($pathAddons . $fn . DIRECTORY_SEPARATOR . 'Register.php');

                        if (self::validateNamespace($register)) {

                            $nameSpace = $register['name_space'];

                            $pathPlugins = $pathAddons . $fn . DIRECTORY_SEPARATOR . 'Plugins.php';
                            $routeWeb = $pathAddons . $nameSpace . DIRECTORY_SEPARATOR . 'Routes' . DIRECTORY_SEPARATOR . 'Web.php';
                            $routeApi = $pathAddons . $nameSpace . DIRECTORY_SEPARATOR . 'Routes' . DIRECTORY_SEPARATOR . 'Api.php';

                            // Carico tutte le rotte WEB
                            if (file_exists($pathPlugins)) {
                                include_once($pathPlugins);
                            }

                            // Carico tutte le rotte WEB
                            if (file_exists($routeWeb)) {
                                include_once($routeWeb);
                            }

                            // Carico tutte le rotte API
                            if (file_exists($routeApi)) {
                                include_once($routeApi);
                            }

                            // Includo il controller
                            if (isset($register['controllers']) && count($register['controllers']) >= 1) {

                                foreach ($register['controllers'] as $className) {

                                    self::registerClass($pathAddons . $nameSpace . DIRECTORY_SEPARATOR . 'Controllers' . DIRECTORY_SEPARATOR . $className . '.php');
                                }
                            }

                            // Includo il Modello
                            if (isset($register['models']) && count($register['models']) >= 1) {

                                foreach ($register['models'] as $className) {

                                    self::registerClass($pathAddons . $nameSpace . DIRECTORY_SEPARATOR . 'Models' . DIRECTORY_SEPARATOR . $className . '.php');
                                }
                            }

                            // Includo le classi helpers
                            if (isset($register['class_helpers']) && count($register['class_helpers']) >= 1) {

                                foreach ($register['class_helpers'] as $className) {

                                    self::registerClass($pathAddons . $nameSpace . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . $className . '.php');
                                }
                            }

                            // Includo le funzioni helpers
                            if (isset($register['helpers']) && count($register['helpers']) >= 1) {

                                foreach ($register['helpers'] as $fileName) {

                                    $pathFile = $pathAddons . $nameSpace . DIRECTORY_SEPARATOR . 'Helpers' . DIRECTORY_SEPARATOR . $fileName . '.php';

                                    if (file_exists($pathFile)) {
                                        require_once($pathFile);
                                    }
                                }
                            }
                        }
                    }
                }
            }
        }
    }

    /**
     * Carico tutti gli addons in JOB
     */
    public static function initJobs()
    {
        $pathAddons = self::pathAddons();

        self::loadModels();

        if (is_dir($pathAddons) && $dh = opendir($pathAddons)) {

            $pathFiles = [
                'Job.php',
                'Events.php',
                'Plugins.php'
            ];

            while ($fn = readdir($dh)) {

                $dir = $pathAddons . $fn . DIRECTORY_SEPARATOR;

                if ($fn != '.' && $fn != '..' && is_dir($dir)) {

                    foreach ($pathFiles as $file) {

                        if (file_exists($dir . $file)) {

                            require_once($dir . $file);

                        }
                    }
                }
            }
        }
    }

    public static function isActive($str = null)
    {
        $record = false;

        if ($str !== null) {

            $DB = new \System\Database();

            $query = $DB::table('addons')
                ->join('rel_addon_institution', 'addons.id', '=', 'rel_addon_institution.addon_id')
                ->where('addons.path', ucfirst($str))
                ->where('rel_addon_institution.institution_id', checkAlternativeInstitutionId())
                ->first();

            if (!empty($query)) {
                $record = objectToArray($query);
            }

        }

        return $record;
    }

    public static function loadModels()
    {

        $pathAddons = self::pathAddons();

        $models = [
            $pathAddons . 'AddonsModel.php',
            $pathAddons . 'RelAddonInstitutionModel.php'
        ];

        foreach ($models as $model) {
            if (file_exists($model)) {
                self::registerClass($model);
            }
        }
    }

    public static function config($key = null, $pluginName = null, $fileName = null)
    {
        if ($key !== null) {

            $file = APP_PATH . 'Addons' . DIRECTORY_SEPARATOR . ucfirst($pluginName) . DIRECTORY_SEPARATOR . 'Configs' . DIRECTORY_SEPARATOR . $fileName . '.php';
            $config = new \Maer\Config\Config();
            $config->load($file);

            return $config->get($key);
        }

        return null;
    }

    private static function pathAddons()
    {
        return APP_PATH . 'Addons' . DIRECTORY_SEPARATOR;
    }

    public static function path($folder = null)
    {
        return APP_PATH . 'Addons' . DIRECTORY_SEPARATOR . ucfirst(strtolower($folder)) . DIRECTORY_SEPARATOR . 'Views' . DIRECTORY_SEPARATOR;
    }

    private static function registerClass($className = null)
    {

        spl_autoload_register(function () use ($className) {

            $className = str_replace("\\", DIRECTORY_SEPARATOR, $className);

            if (file_exists($className)) {

                include_once($className);
            }
        });
    }

    private static function validateNamespace($register)
    {

        // Validazione Namespace
        if (empty($register['name_space'])) {

            throw new \ReflectionException('Namespace in Addons not found');
        }

        return true;
    }

    private static function read($file)
    {
        $content = include $file;
        return is_array($content) ? $content : [];
    }

    private static function enabledFolders()
    {

        if (Registry::exist('addons_folder_list')) {

            $addonPaths = Registry::get('addons_folder_list');

        } else {

            $addonPaths = null;
            $session = new \System\Session();

            if (!$session->has('addons_folder_list') && $session->get('addons_folder_list') !== null) {

                $query = \Addons\RelAddonInstitutionModel::select(['addons.path AS p'])->where('institution_id', '=', checkAlternativeInstitutionId())
                    ->join('addons', 'addons.id', '=', 'rel_addon_institution.addon_id')
                    ->get();

                if (!empty($query)) {

                    $addonPaths = null;

                    foreach ($query->toArray() as $result) {
                        $addonPaths[] = $result['p'];
                    }

                    if ($addonPaths != null) {

                        $session->set('addons_folder_list', serialize($addonPaths));

                    }
                }

            } elseif ($session->get('addons_folder_list') !== null) {

                $addonPaths = unserialize($session->get('addons_folder_list'));

            }
        }


        return $addonPaths;
    }

}