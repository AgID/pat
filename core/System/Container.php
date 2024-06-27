<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class Container
{
    /**
     * @var array
     */
    protected $services = [];

    /**
     * @var array
     */
    protected $instances = [];

    /**
     * @var
     */
    private static $instance;

    /**
     * @return Container
     */
    public static function getInstance()
    {
        if (!self::$instance) {
            self::$instance = new Container();
        }
        return self::$instance;
    }

    /**
     *
     */
    public function register($name, callable $resolver)
    {
        $this->services[$name] = $resolver;
    }

    /**
     * @param $name
     * @param callable $resolver
     * @return void
     */
    public function singleton($name, callable $resolver)
    {
        $this->register($name, function () use ($resolver, $name) {
            if (!isset($this->instances[$name])) {
                $this->instances[$name] = $resolver();
            }
            return $this->instances[$name];
        });
    }

    /**
     * @param $name
     * @return mixed
     * @throws \Exception
     */
    public function make($name)
    {
        if (!isset($this->services[$name])) {
            throw new \Exception("Service '{$name}' not found.");
        }
        return $this->services[$name]();
    }
}