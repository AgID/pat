<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Class Cookie
 *
 * @package System
 */
class Cookie
{
    /**
     * @param $name
     * @param string $value
     * @param string $expire
     * @param string $domain
     * @param string $path
     * @param string $prefix
     * @param null $secure
     * @param null $httpOnly
     * @throws \Exception
     */
    public static function set($name, $value = '', $expire = '', $domain = '', $path = '/', $prefix = '', $secure = NULL, $httpOnly = NULL)
    {
        return \System\Input::setCookie($name, $value, $expire, $domain, $path, $prefix, $secure, $httpOnly);
    }

    /**
     * @param null $index
     * @param bool $xssClean
     * @param bool $sanitizeKey
     * @param bool $sanitizeData
     * @return array|null|string|string[]
     */
    public static function get($index = null, $xssClean = false, $sanitizeKey = true, $sanitizeData = true)
    {
        return \System\Input::cookie($index, $xssClean, $sanitizeKey, $sanitizeData);
    }

    /**
     * @param $name
     * @param string $domain
     * @param string $path
     * @param string $prefix
     */
    public static function delete($name, $domain = '', $path = '/', $prefix = '')
    {
        return set_cookie($name, '', '', $domain, $path, $prefix);
    }

}
