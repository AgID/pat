<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Class Token
 * @package System
 */
class Token
{
    const PREFIX = '_sys_framework_csfr_';

    /**
     * Token constructor.
     */
    protected function __construct()
    {
    }

    /**
     * @param bool $new
     * @return mixed|string
     * @throws \Exception
     */
    public static function generate($expire = 7200, $name = null)
    {
        $session = new Session();
        $name = (string)($name === null) ? static::getName() : $name;
        $token = sha1(uniqid(mt_rand() . time(), true));

        if ($expire === 0 || $expire === false) {
            $session->setFlash(self::PREFIX . $name, $token);
        } else {

            $session->setTemp(self::PREFIX . $name, $token, $expire);

        }

        return $token;
    }

    /**
     * @param $expire
     * @param $name
     * @return void
     */
    public static function forceRegenerate($expire = 7200, $name = null)
    {
        if (!empty($_SESSION['temp'])) {

            $name = (string)($name === null)
                ? static::getName()
                : $name;

            $session = new Session();

            if(!empty( $_SESSION['temp'][self::PREFIX . $name]['value'])) {
                $_SESSION['temp'][self::PREFIX . $name]['value'] = null;
                unset($_SESSION['temp'][self::PREFIX . $name]['value']);
            }

            if(!empty($_SESSION['temp'][self::PREFIX . $name]['expire'])){
                $_SESSION['temp'][self::PREFIX . $name]['expire'] = null;
                unset($_SESSION['temp'][self::PREFIX . $name]['expire']);
            }

            $token = sha1(uniqid(mt_rand() . time(), true));

            if ($expire === 0 || $expire === false) {
                $session->setFlash(self::PREFIX . $name, $token);
            } else {
                $session->setTemp(self::PREFIX . $name, $token, $expire);
            }

            return $token;
        }

        return null;
    }

    public static function getToken($name = null)
    {
        $session = new Session();
        $name = (string)($name === null)
            ? static::getName()
            : $name;
        $getTemp = $session->getTemp(self::PREFIX . $name);

        if ($getTemp !== null) {
            return $getTemp;
        } else if ($getTFlash = $session->getFlash(self::PREFIX . $name)) {
            return $getTFlash;
        }

        return null;
    }

    /**
     * @return array|mixed|null
     * @throws \Exception
     */

    public static function getName()
    {
        return config('csrf_token_name', null, 'app');
    }


    /**
     * @param string $input
     * @return bool
     * @throws \Exception
     */
    public static function verify($index=null)
    {
        $session = new Session();
        $check = false;
        $name = ($index === null)
            ? static::getName()
            : $index;
        
        $getToken = (string)Input::postGet($name, true);
        $token = (string)$session->getTemp(self::PREFIX . $name);

        if ($getToken === $token) {
            $check = true;
        }

        if (!$check) {

            $token = (string)$session->getFlash(self::PREFIX . $name);
   
            if (
                    !empty($getToken) && 
                    !empty($token) && 
                    str_len($getToken)>=30 && 
                    str_len($getToken)<=33 && 
                    str_len($token)>=30 && 
                    str_len($token)<=33 && 
                    $getToken === $token
                ) 
            {

                $check = true;
            }
        }
      
        return $check;
    }
}