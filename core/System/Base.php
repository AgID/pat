<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class Base
{
    private $config = [];
    public $urlSuffix;

    /**
     * Base constructor.
     *
     * @throws \Exception
     */
    public function __construct()
    {
        $config = new \Maer\Config\Config();
        $config->load(APP_PATH . 'Config/app.php');

        $siteUrl = rtrim((string)$config->get('site_url', null), '/');
        $this->urlSuffix = $config->get('url_suffix', null);

        if (empty($siteUrl)) {

            if (isset($_SERVER['SERVER_ADDR'])) {

                if (strpos($_SERVER['SERVER_ADDR'], ':') !== FALSE) {

                    $serverAddr = '[' . $_SERVER['SERVER_ADDR'] . ']';

                } else {

                    $serverAddr = $_SERVER['SERVER_ADDR'];

                }

                $baseUrl = (isHttps() ? 'https' : 'http') . '://' . $serverAddr
                    . substr($_SERVER['SCRIPT_NAME'], 0, strpos($_SERVER['SCRIPT_NAME'], basename($_SERVER['SCRIPT_FILENAME'])));
            } else {

                $baseUrl = 'http://localhost/';

            }

            $this->setItem('base_url', $baseUrl);

        } else {

            $this->setItem('base_url', $siteUrl);

        }
    }

    /**
     * @param string $uri
     * @param null $protocol
     * @return string
     */
    public function baseUrl($uri = '', $protocol = NULL)
    {
        $baseUrl = $this->slashItem('base_url');


        if (isset($protocol)) {

            if ($protocol === '') {

                $baseUrl = substr($baseUrl, strpos($baseUrl, '//'));

            } else {

                $baseUrl = $protocol . substr($baseUrl, strpos($baseUrl, '://'));

            }
        }

        return $baseUrl . $this->uriString($uri);
    }

    /**
     * @param string $uri
     * @param null $protocol
     * @return string
     */
    public function siteUrl($uri = '', $protocol = NULL)
    {
        $baseUrl = $this->slashItem('base_url');

        if (isset($protocol)) {

            if ($protocol === '') {

                $baseUrl = substr($baseUrl, strpos($baseUrl, '//'));

            } else {

                $baseUrl = $protocol . substr($baseUrl, strpos($baseUrl, '://'));

            }

        }

        if (empty($uri)) {

            return trim($baseUrl);

        }

        $uri = ltrim($this->uriString($uri), '/');

        // Verifico se esiste il punto interrogativo (?) nella URI.
        if ((bool)preg_match('/\?/', $uri) === true) {

            $explodeUri = explode('?', $uri);

            $uri = rtrim($explodeUri[0], '/') . $this->urlSuffix;
            $uri .= !empty($explodeUri[1]) ? '?' . $explodeUri[1] : '';

        } else {

            $uri = rtrim($uri, '/') . $this->urlSuffix;

        }

        return trim($baseUrl . $uri);
    }

    /**
     * @param $item
     * @return null|strin
     */
    public function slashItem($item)
    {
        if (!isset($this->config[$item])) {

            return NULL;

        } elseif (trim($this->config[$item]) === '') {

            return '';

        }

        return rtrim($this->config[$item], '/') . '/';
    }

    /**
     * @param $item
     * @param $value
     */
    public function setItem($item, $value)
    {
        $this->config[$item] = $value;
    }

    /**
     * @param $uri
     * @return string
     */
    public function uriString($uri)
    {
        is_array($uri) && $uri = implode('/', $uri);
        return ltrim($uri, '/');
    }

}
