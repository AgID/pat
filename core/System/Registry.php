<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Class Registry
 * @package System
 */
class Registry
{

    private static $instance = null;
    private $storage = [];

    /**
     * Questo metodo permette di recuperare una voce, un valore o un oggetto, memorizzato nel registro associato alla chiave passata nel parametro
     *
     * @param $key
     * @return mixed
     */
    public static function get($key)
    {
        return self::getInstance()->$key;
    }

    /**
     * Questo metodo permette di memorizzare una voce, un valore o un oggetto nel registro nel formato $key=>$val.
     *
     * @param $key
     * @param $val
     * @return false|void
     */
    public static function set($key, $val)
    {
        if (!is_string($key)) {
            return false;
        }
        self::getInstance()->$key = $val;
    }

    /**
     * Questo metodo permette di rimuovere un valore o un oggetto memorizzato nel registro, associato alla chiave passata nel parametro
     *
     * @param $key
     * @return bool
     */
    public static function delete($key)
    {
        $return = false;
        if (self::exist($key)) {
            unset(self::getInstance()->$key);
            $return = true;
        }
        return $return;
    }

    /**
     * Questo metodo permette di verificare se un valore o un oggetto con la chiave passata nel parametro esiste o meno nel registro.
     *
     * @param $key
     * @return bool
     */
    public static function exist($key)
    {
        $return = false;
        if (self::getInstance()->$key) {
            $return = true;
        }
        return $return;
    }

    /**
     * @param $key
     * @param $val
     */
    public function __set($key, $val)
    {
        $this->storage[$key] = $val;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function __get($key)
    {
        return $this->storage[$key] ?? null;
    }

    /**
     * @return Registry|null
     */
    private static function getInstance()
    {
        if (!(self::$instance instanceof Registry) || self::$instance == null) {
            self::$instance = new Registry();
        }
        return self::$instance;
    }

    public function __construct()
    {

    }

    public function __clone()
    {

    }
}