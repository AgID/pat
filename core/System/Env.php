<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');


class Env
{
    private $path;

    /**
     * @param $path
     * @throws \Exception
     */
    public function __construct($path)
    {

        $this->path = $path;

        if (!file_exists($this->path)) {
            throw new \Exception(sprintf('file "%s" not found!', $this->path));
        }

        if (!is_readable($this->path)) {
            throw new \Exception(sprintf('%s file is not readable', $this->path));
        }

        $this->loadFileEnv();
    }

    /**
     * @description includo il file .ENV
     * @param $path
     * @return static
     * @throws \Exception
     */
    public static function load($path = null)
    {
        return new static($path);
    }


    /**
     * @description Carico e parso il file .env
     * @return void
     */
    private function loadFileEnv()
    {
        $getLines = file($this->path, FILE_IGNORE_NEW_LINES | FILE_SKIP_EMPTY_LINES);

        if ($getLines) {

            foreach ($getLines as $line) {

                // Non tengo in considerazione i commenti
                if (strpos(trim($line), '#') === false) {

                    // Key Value
                    list($name, $value) = explode('=', $line, 2);

                    $name = trim($name);
                    $value = trim($value);

                    // Setto le variabili di ambiente
                    $_ENV[$name]=$value;

                    // Setto le variabili server
                    $_SERVER[$name] = $value;
                }

            }

        }

    }
}
