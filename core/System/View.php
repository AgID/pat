<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

use Exception;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class View
{
    /**
     * @var string
     */
    public $path;

    /**
     * @var array
     */
    public $variables = [];

    /**
     * view constructor.
     *
     * @param null $filePath
     * @param array $variables
     * @param bool $theme
     * @throws Exception
     */
    public function __construct($filePath = null, $variables = [], $theme = false)
    {

        $this->variables = array_merge($this->variables, $variables);

        if ($theme === false) {

            $this->path = APP_PATH . 'Themes' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $filePath . '.php';

        } else {

            // Plugin
            if (substr($theme, 0, 6) == 'plugin') {

                $tmpData = explode(':', $theme);

                $this->path = APP_PATH . 'Plugins' . DIRECTORY_SEPARATOR . $tmpData[1] . DIRECTORY_SEPARATOR . $filePath . '.php';

                    // Addons
            } elseif  (substr($theme, 0, 5) == 'addon') {

                $tmpData = explode(':', $theme);
                $this->path = APP_PATH . 'Addons' . DIRECTORY_SEPARATOR . ucfirst($tmpData[1]) . DIRECTORY_SEPARATOR .  'Views'  . DIRECTORY_SEPARATOR  .  $filePath . '.php';

                if(!file_exists($this->path)) {

                    $this->path = APP_PATH . 'Addons' . DIRECTORY_SEPARATOR . $tmpData[1] . DIRECTORY_SEPARATOR .  'views'  . DIRECTORY_SEPARATOR  .  $filePath . '.php';

                } else {

                    if(!file_exists($this->path)) {

                        $this->path = APP_PATH . 'Addons' . DIRECTORY_SEPARATOR .ucfirst($tmpData[1]) . DIRECTORY_SEPARATOR .  'views'  . DIRECTORY_SEPARATOR  .  $filePath . '.php';

                    } else {

                        if(!file_exists($this->path)) {

                            $this->path = APP_PATH . 'Addons' . DIRECTORY_SEPARATOR .$tmpData[1] . DIRECTORY_SEPARATOR .  'Views'  . DIRECTORY_SEPARATOR  .  $filePath . '.php';

                        } 
                    }
                }

            } else {

                $found = true;

                if ($theme === true) {

                    $this->path = APP_PATH . 'Themes' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . THEME . DIRECTORY_SEPARATOR . $filePath . '.php';

                } else {

                    $this->path = APP_PATH . 'Themes' . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR . $filePath . '.php';

                    $found = (bool)file_exists($this->path);
                }

                if (!$found) {

                    $path = APP_PATH . 'Themes' . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR . $filePath . '.php';

                    if (file_exists($path)) {

                        $this->path = $path;

                    } else {

                        $path = APP_PATH . 'Themes' . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR . 'views' . DIRECTORY_SEPARATOR . $filePath . '.php';
                        $this->path = $path;
                    }

                }
            }

        }

        if (!file_exists($this->path)) {

            throw new Exception("View '{$this->path}' not found!");

        }
    }

    /**
     * @param $method
     * @param $arguments
     * @return view
     * @throws Exception
     */
    public static function __callStatic($method, $arguments)
    {
        $variables = count($arguments) ? current($arguments) : [];

        return new static($method, $variables);
    }

    /**
     * @param $name
     * @param $path
     * @param array $variables
     * @param bool $theme
     * @return $this
     */
    public function partial($name, $path, $variables = [], $theme = false)
    {
        $this->variables[$name] = static::create($path, $variables, $theme)->render();

        return $this;
    }

    /**
     * @return string
     */
    public function render()
    {
        ob_start();

        extract($this->variables, EXTR_SKIP);

        require $this->path;

        return ob_get_clean();
    }

    /**
     * @return void
     */
    public function display(): void
    {
        echo $this->render();
    }

    /**
     * @param $path
     * @param array $variables
     * @param bool $theme
     * @return View
     * @throws Exception
     */
    public static function create($path, $variables = [], $theme = false): View
    {
        return new static($path, $variables, $theme);
    }
}