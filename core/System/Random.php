<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class Random
{
    /**
     * Genera una stringa di tipo numerica casuale basato sulla primitiva mt_rand().
     *
     * @return int
     */
    public static function basic()
    {
        return mt_rand();
    }

    /**
     * Genera una stringa contenente solo numeri, della lunghezza uguale al parametro $length
     *
     * @param int $length
     * @return string
     */
    public static function numeric($length = 8)
    {
        $pool = '0123456789';
        return substr(str_shuffle(str_repeat($pool, ceil($length / strlen((string) $pool)))), 0, $length);
    }

    /**
     * Genera una stringa numerica escluso lo zero della lunghezza passata nel parametro $length
     *
     * @param int $length
     * @return string
     */
    public static function numericNoZero($length = 8)
    {
        $pool = '123456789';
        return substr(str_shuffle(str_repeat($pool, ceil($length / strlen((string) $pool)))), 0, $length);
    }

    /**
     * Genera una stringa alfanumerica con caratteri minuscoli e maiuscoli della lunghezza passata nel parametro $length
     *
     * @param int $length
     * @return string
     */
    public static function alnum($length = 8)
    {
        $pool = '0123456789abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return self::generateRand($pool, $length);
    }

    /**
     * Genera una stringa con solo lettere minuscole e maiuscole della lunghezza passata nel parametro $length
     *
     * @param int $length
     * @return string
     */
    public static function alpha($length = 8)
    {
        $pool = 'abcdefghijklmnopqrstuvwxyzABCDEFGHIJKLMNOPQRSTUVWXYZ';
        return self::generateRand($pool, $length);
    }

    /**
     * Genera una stringa alfanumerica casuale crittografata basato su md5() con lunghezza fissa di 32.
     *
     * @return string
     */
    public static function md5()
    {
        return md5(uniqid(mt_rand()));
    }

    /**
     * Genera un stringa alfanumerica casuale crittografata basata su sha1() con lunghezza fissa di 40.
     *
     * @return string
     */
    public static function sha1()
    {
        return sha1(uniqid(mt_rand(), TRUE));
    }

    /**
     * Metodo protetto che genera la stringa Randomica
     *
     * @param $pool
     * @param $length
     * @return false|string
     */
    protected static function generateRand($pool, $length)
    {
        return substr(str_shuffle(str_repeat($pool, ceil($length / strlen((string) $pool)))), 0, $length);
    }
}