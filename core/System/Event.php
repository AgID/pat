<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

use ReflectionException;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class Event
{
    /** @var array */
    protected static $e = [];

    /**
     * Aggiungi un nuovo gestore di eventi
     *
     * @param string $name
     * @param callable $callback
     * @return void
     */
    public static function add($name = null, $callback = null)
    {
        self::$e[$name][] = $callback;
    }

    /**
     * Esegue i gestori degli eventi registrati
     *
     * @param null $name
     * @param array $params
     * @return mixed|void
     * @throws ReflectionException
     */
    public static function call($name = null, array $params = [])
    {

        foreach (self::e(self::$e[$name], []) as $value) {

            if (is_callable($value)) {

                return call_user_func_array($value, $params);

            } else {

                if (class_exists($value)) {

                    $object = new $value;

                    if (method_exists($object, 'handle')) {

                        return call_user_func_array([$object, 'handle'], $params);

                    } else {

                        throw new \BadFunctionCallException('[Event] - il Metodo handle non esiste nella clsse ' . $value);

                    }

                } else {

                    throw new ReflectionException('[Event] - Classe ' . $value . ' non trovata');

                }

            }

        }

    }

    public static function exists($name = null)
    {
        return (!empty(self::$e[$name]) && count(self::$e[$name]) >= 1)
            ? true
            : false;
    }

    /**
     * Verifica se la variabile è settato oppure lancia un alternativa
     * @param $var
     * @param null $alternate
     * @return null
     */
    public static function e(&$var, $alternate = null)
    {
        return (isset($var)) ? $var : $alternate;
    }

}