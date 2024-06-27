<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use \Maer\Config\Config;

class Input
{
    /**
     * @param null $index
     * @param bool $xssClean
     * @param bool $sanitizeKey
     * @param bool $sanitizeData
     * @return array|null|string|string[]
     */
    public static function post($index = null, $xssClean = false, $sanitizeKey = true, $sanitizeData = true)
    {
        if ($_SERVER['REQUEST_METHOD'] == 'POST') {
            return static::run($_POST, $index, $xssClean, $sanitizeKey, $sanitizeData);
        } else {
            if ($index !== null) { // intercetta solo lo stream con "item" post e non file.
                $index = 'post.' . str_replace(['post.', 'file.'], ['', ''], $index);
            }

            return self::stream($index, $xssClean, $sanitizeKey, $sanitizeData);
        }
    }

    /**
     * @param null $index
     * @param bool $xssClean
     * @param bool $sanitizeKey
     * @param bool $sanitizeData
     * @return array|null|string|string[]
     */
    public static function postGet($index = null, $xssClean = false, $sanitizeKey = true, $sanitizeData = true)
    {
        return !empty($_POST[$index]) && isset($_POST[$index])
            ? static::post($index, $xssClean, $sanitizeKey, $sanitizeData)
            : static::get($index, $xssClean, $sanitizeKey, $sanitizeData);
    }

    /**
     * @param null $index
     * @param bool $xssClean
     * @param bool $sanitizeKey
     * @param bool $sanitizeData
     * @return array|null|string|string[]
     */
    public function getPost($index = null, $xssClean = false, $sanitizeKey = true, $sanitizeData = true)
    {
        return !empty($_GET[$index]) && isset($_GET[$index])
            ? static::get($index, $xssClean, $sanitizeKey, $sanitizeData)
            : static::post($index, $xssClean, $sanitizeKey, $sanitizeData);
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
        return static::run($_GET, $index, $xssClean, $sanitizeKey, $sanitizeData);
    }

    /**
     * @param null $index
     * @param bool $xssClean
     * @param bool $sanitizeKey
     * @param bool $sanitizeData
     * @return array|null|string|string[]
     */
    public static function cookie($index = null, $xssClean = false, $sanitizeKey = true, $sanitizeData = true)
    {
        return static::run($_COOKIE, $index, $xssClean, $sanitizeKey, $sanitizeData);
    }

    public static function requestHeaders($xssClean = true, $sanitizeKey = true, $sanitizeData = true)
    {
        $headers = null;

        if (function_exists('apache_request_headers')) {
            $headers = apache_request_headers();
        } else {
            isset($_SERVER['CONTENT_TYPE']) && $headers['Content-Type'] = $_SERVER['CONTENT_TYPE'];

            foreach ($_SERVER as $key => $val) {
                if (sscanf($key, 'HTTP_%s', $header) === 1) {
                    $header = str_replace('_', ' ', strtolower($header));
                    $header = str_replace(' ', '-', ucwords($header));

                    $headers[$header] = $_SERVER[$key];
                }
            }
        }

        return static::run($headers, null, $xssClean, $sanitizeKey, $sanitizeData);

    }

    /**
     * @param bool $xssClean
     * @return null|string
     */
    public static function getRequestHeader($index = null, $xssClean = false)
    {
        static $headers;
        $setHeaders = null;

        if (!isset($headers)) {

            $setHeaders = static::requestHeaders(false);

            if (!empty($setHeaders) && is_array($setHeaders)) {

                foreach ($setHeaders as $key => $value) {

                    $headers[strtolower($key)] = $value;

                }

            }

        }

        $index = strtolower((string)$index);

        if (!isset($headers[$index])) {
            return NULL;
        }

        $security = new \System\Security();

        return ($xssClean === TRUE)
            ? $security->xssClean($headers[$index])
            : $headers[$index];
    }

    /**
     * @param null $index
     * @param bool $xssClean
     * @param bool $sanitizeKey
     * @param bool $sanitizeData
     * @return array|null|string|string[]
     */
    public static function stream($index = null, $xssClean = false, $sanitizeKey = true, $sanitizeData = true)
    {
        $_data = [];
        new Stream($_data);

        if ($index === null) {
            return static::run($_data, $index, $xssClean, $sanitizeKey, $sanitizeData);
        } else {
            return self::getData($index, $_data, $xssClean, $sanitizeKey, $sanitizeData);
        }
    }

    private static function getData($key, $array, $xssClean, $sanitizeKey, $sanitizeData)
    {
        $sanitizeRequest = new SanitizeRequest();

        $keys = explode('.', $key);

        foreach ($keys as $k) {
            if (!isset($array[$k])) {
                return null;
            }

            $array = $array[$k];
        }

        if ($sanitizeData === true) {
            $array = $sanitizeRequest->cleanRequest($array);

        }

        if ($xssClean === true) {
            $security = new Security();
            $array = $security->xssClean($array);
        }

        return $array;
    }

    /**
     * @param bool $xssClean
     * @return array|null|string|string[]
     */
    public static function userAgent($xssClean = false)
    {
        return static::run($_SERVER, 'HTTP_USER_AGENT', $xssClean, false, false);
    }

    /**
     * @param null $index
     * @param bool $xssClean
     * @param bool $sanitizeKey
     * @param bool $sanitizeData
     * @return array|null|string|string[]
     */
    public static function files($index = null, $xssClean = false, $sanitizeKey = true, $sanitizeData = true)
    {
        if ($_SERVER['REQUEST_METHOD'] !== 'PUT') {
            return static::run('files', $index, $xssClean, $sanitizeKey, $sanitizeData);
        } else {
            $_data = [];
            new Stream($_data);
            if (isset($_data['file']) && !empty($_data['file'])) {
                return $_data['file'];
            }

            return null;
        }
    }

    /**
     * @return array|null|string|string[]
     * @throws \Exception
     */
    public static function ipAddress()
    {
        $config = new Config();
        $config->load(APP_PATH . 'Config/app.php');

        $proxyIps = $config->get('proxy_ips', false);

        if (!empty($proxyIps) && !is_array($proxyIps)) {
            $proxyIps = explode(',', str_replace(' ', '', $proxyIps));
        }

        $ipAddress = self::server('REMOTE_ADDR');

        if ($proxyIps) {
            foreach (array('HTTP_X_FORWARDED_FOR', 'HTTP_CLIENT_IP', 'HTTP_X_CLIENT_IP', 'HTTP_X_CLUSTER_CLIENT_IP') as $header) {
                if (($spoof = self::server($header)) !== NULL) {
                    sscanf($spoof, '%[^,]', $spoof);

                    if (!self::validIp($spoof)) {
                        $spoof = NULL;
                    } else {
                        break;
                    }
                }
            }

            if ($spoof) {
                for ($i = 0, $c = count($proxyIps); $i < $c; $i++) {
                    if (strpos($proxyIps[$i], '/') === FALSE) {
                        if ($proxyIps[$i] === $ipAddress) {
                            $ipAddress = $spoof;
                            break;
                        }

                        continue;
                    }

                    isset($separator) or $separator = self::validIp($ipAddress, 'ipv6') ? ':' : '.';

                    if (strpos($proxyIps[$i], $separator) === FALSE) {
                        continue;
                    }

                    if (!isset($ip, $sprintf)) {
                        if ($separator === ':') {
                            $ip = explode(
                                ':',
                                str_replace(
                                    '::',
                                    str_repeat(':', 9 - substr_count($ipAddress, ':')),
                                    $ipAddress
                                )
                            );

                            for ($j = 0; $j < 8; $j++) {
                                $ip[$j] = intval($ip[$j], 16);
                            }

                            $sprintf = '%016b%016b%016b%016b%016b%016b%016b%016b';
                        } else {
                            $ip = explode('.', $ipAddress);
                            $sprintf = '%08b%08b%08b%08b';
                        }

                        $ip = vsprintf($sprintf, $ip);
                    }

                    sscanf($proxyIps[$i], '%[^/]/%d', $netaddr, $masklen);

                    if ($separator === ':') {
                        $netaddr = explode(':', str_replace('::', str_repeat(':', 9 - substr_count($netaddr, ':')), $netaddr));
                        for ($j = 0; $j < 8; $j++) {
                            $netaddr[$j] = intval($netaddr[$j], 16);
                        }
                    } else {
                        $netaddr = explode('.', $netaddr);
                    }

                    if (strncmp($ip, vsprintf($sprintf, $netaddr), $masklen) === 0) {
                        $ipAddress = $spoof;
                        break;
                    }
                }
            }
        }

        if (!self::validIp($ipAddress)) {
            return $ipAddress = '0.0.0.0';
        }

        return $ipAddress;
    }

    /**
     * @param $ip
     * @param string $which
     * @return bool
     */
    public static function validIp($ip, $which = '')
    {
        switch (strtolower($which)) {
            case 'ipv4':
                $which = FILTER_FLAG_IPV4;
                break;
            case 'ipv6':
                $which = FILTER_FLAG_IPV6;
                break;
            default:
                $which = FILTER_DEFAULT;
                break;
        }

        return (bool)filter_var($ip, FILTER_VALIDATE_IP, $which);
    }

    /**
     * @param $index
     * @param bool $xssClean
     * @return array|null|string|string[]
     */
    public static function server($index = null, $xssClean = false)
    {
        return static::run($_SERVER, $index, $xssClean, false, false);
    }

    public static function method($upper = FALSE)
    {
        return ($upper)
            ? strtoupper(self::server('REQUEST_METHOD'))
            : strtolower(self::server('REQUEST_METHOD'));
    }

    /**
     * @return bool
     */
    public static function isAjax()
    {
        return (!empty($_SERVER['HTTP_X_REQUESTED_WITH']) && strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) === 'xmlhttprequest');
    }

    /**
     * @param $method
     * @param null $index
     * @param bool $xssClean
     * @param bool $sanitizeKey
     * @param bool $sanitizeData
     * @return array|null|string|string[]
     */
    protected static function run($method, $index = null, $xssClean = false, $sanitizeKey = true, $sanitizeData = true)
    {
        if ($method === 'files') {
            $method = $_FILES;
            $file = true;
        } else {
            $file = false;
        }

        $sanitize = new SanitizeRequest();
        return $sanitize->fetchFromArray($method, $index, $xssClean, $sanitizeKey, $sanitizeData, $file);
    }

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
    public static function setCookie($name, $value = '', $expire = '', $domain = '', $path = '/', $prefix = '', $secure = NULL, $httpOnly = NULL)
    {
        if (is_array($name)) {

            foreach (['value', 'expire', 'domain', 'path', 'prefix', 'secure', 'httponly', 'name'] as $item) {

                if (isset($name[$item])) {

                    $$item = $name[$item];

                }

            }

        }

        if ($prefix === '' && config('prefix', null, 'cookie') !== '') {

            $prefix = config('prefix', null, 'cookie');

        }

        if ($domain === '' && config('domain', null, 'cookie') !== '') {

            $domain = config('domain', null, 'cookie');

        }

        if ($path === '/' && config('path', null, 'cookie') !== '/') {

            $path = config('path', null, 'cookie');

        }

        $secure = ($secure === NULL && config('secure', null, 'cookie') !== NULL)
            ? (bool)config('secure', null, 'cookie')
            : (bool)$secure;


        $httpOnly = ($httpOnly === NULL && config('httponly', null, 'cookie') !== NULL)
            ? (bool)config('httponly', null, 'cookie')
            : (bool)$httpOnly;

        if (!is_numeric($expire)) {
            $expire = time() - 86500;
        } else {
            $expire = ($expire > 0) ? time() + $expire : 0;
        }


        return setcookie($prefix . $name, $value, $expire, $path, $domain, $secure, $httpOnly);
    }

    private static function detectStream(): array
    {
        $method = [];
        $blocks = preg_split('/-+/', file_get_contents('php://input'));

        foreach ($blocks as $block) {
            if (empty(trim($block)))
                continue;

            if (preg_match('/name="([^"]+)"\s*([\w\W]+)\s*/i', $block, $matches)) {
                $method[$matches[1]] = trim($matches[2]);
            }
        }

        return $method;
    }

}