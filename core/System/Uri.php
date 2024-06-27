<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use System\Security;


class Uri
{
    /**
     * @var array
     */
    public $keyVal = array();

    /**
     * @var string
     */
    public $uriString = '';

    /**
     * @var array
     */
    public $segments = array();

    /**
     * @var array
     */
    public $rsegments = array();

    /**
     * @var array|mixed|null
     */
    protected $permittedUriChars;

    /**
     * @var \Maer\Config\Config
     */
    private $config;

    public function __construct()
    {
        $this->config = new \Maer\Config\Config();
        $this->config->load(APP_PATH . 'Config/app.php');

        $enableQueryStrings = $this->config->get('enable_query_strings');

        if (is_cli() || $enableQueryStrings !== TRUE) {

            $permittedUriChars = $this->config->get('permitted_uri_chars');
            $this->permittedUriChars = $permittedUriChars;

            if (is_cli()) {
                $uri = $this->parseArgv();
            } else {
                $protocol = $this->config->get('uri_protocol');
                empty($protocol) && $protocol = 'REQUEST_URI';

                switch ($protocol) {
                    case 'AUTO':
                    case 'REQUEST_URI':
                        $uri = $this->parseRequestUri();
                        break;
                    case 'QUERY_STRING':
                        $uri = $this->parseQueryString();
                        break;
                    case 'PATH_INFO':
                    default:
                        $uri = isset($_SERVER[$protocol]) ? $_SERVER[$protocol] : $this->parseRequestUri();
                        break;
                }
            }

            $this->setUriString($uri);
        }

    }


    protected function setUriString($str)
    {
        $this->uriString = trim(removeInvisibleCharacters($str, FALSE), '/');

        if ($this->uriString !== '') {

            if (($suffix = config('url_suffix', null, 'app')) !== '') {
                $sLen = strlen($suffix);

                if (substr($this->uriString, -$sLen) === $suffix) {
                    $this->uriString = substr($this->uriString, 0, -$sLen);
                }
            }

            $this->segments[0] = NULL;

            foreach (explode('/', trim($this->uriString, '/')) as $val) {
                $val = trim($val);

                $this->filterUri($val);

                if ($val !== '') {
                    $this->segments[] = $val;
                }
            }

            unset($this->segments[0]);
        }
    }

    /**
     * @return string
     */
    protected function parseRequestUri()
    {
        if (!isset($_SERVER['REQUEST_URI'], $_SERVER['SCRIPT_NAME'])) {
            return '';
        }

        $uri = parse_url('http://dummy' . $_SERVER['REQUEST_URI']);
        $query = isset($uri['query']) ? $uri['query'] : '';
        $uri = isset($uri['path']) ? $uri['path'] : '';

        if (isset($_SERVER['SCRIPT_NAME'][0])) {
            if (strpos($uri, $_SERVER['SCRIPT_NAME']) === 0) {
                $uri = (string)substr($uri, strlen($_SERVER['SCRIPT_NAME']));
            } elseif (strpos($uri, dirname($_SERVER['SCRIPT_NAME'])) === 0) {
                $uri = (string)substr($uri, strlen(dirname($_SERVER['SCRIPT_NAME'])));
            }
        }

        if (trim($uri, '/') === '' && strncmp($query, '/', 1) === 0) {
            $query = explode('?', $query, 2);
            $uri = $query[0];
            $_SERVER['QUERY_STRING'] = isset($query[1]) ? $query[1] : '';
        } else {
            $_SERVER['QUERY_STRING'] = $query;
        }

        parse_str($_SERVER['QUERY_STRING'], $_GET);

        if ($uri === '/' or $uri === '') {
            return '/';
        }


        return $this->_remove_relative_directory($uri);
    }

    /**
     * @return string
     */
    protected function parseQueryString()
    {
        $uri = isset($_SERVER['QUERY_STRING']) ? $_SERVER['QUERY_STRING'] : @getenv('QUERY_STRING');

        if (trim($uri, '/') === '') {
            return '';
        } elseif (strncmp($uri, '/', 1) === 0) {
            $uri = explode('?', $uri, 2);
            $_SERVER['QUERY_STRING'] = isset($uri[1]) ? $uri[1] : '';
            $uri = $uri[0];
        }

        parse_str($_SERVER['QUERY_STRING'], $_GET);

        return $this->_remove_relative_directory($uri);
    }

    /**
     * @return string
     */
    protected function parseArgv()
    {
        $args = array_slice($_SERVER['argv'], 1);
        return $args ? implode('/', $args) : '';
    }

    /**
     * @param $uri
     * @return string
     */
    protected function _remove_relative_directory($uri)
    {
        $uris = array();
        $tok = strtok($uri, '/');
        while ($tok !== FALSE) {
            if ((!empty($tok) or $tok === '0') && $tok !== '..') {
                $uris[] = $tok;
            }
            $tok = strtok('/');
        }

        return implode('/', $uris);
    }

    /**
     * @param $str
     * @return void
     */
    public function filterUri(&$str)
    {
        $utf8Enabled = strtoupper(CHARSET) == 'UTF-8' ? true : false;
        if (!empty($str) && !empty($this->permittedUriChars) && !preg_match('/^[' . $this->permittedUriChars . ']+$/i' . ($utf8Enabled ? 'u' : ''), $str)) {
            http_response_code(400);
            echo "Caratteri non premessi";
            exit();
        }
    }

    /**
     * @param $n
     * @param $noResult
     * @return mixed|null
     */
    public function segment($n, $noResult = NULL)
    {
        $n = $this->getSegments($n);
        return isset($this->segments[$n]) ? $this->segments[$n] : $noResult;
    }

    /**
     * @param $n
     * @param $noResult
     * @return mixed|null
     */
    public function rsegment($n, $noResult = NULL)
    {
        $n = $this->getSegments($n);
        return isset($this->rsegments[$n]) ? $this->rsegments[$n] : $noResult;
    }

    protected function getSegments($n): int
    {
        if (defined('CUSTOM_PATH') && CUSTOM_PATH !== '/') {
            $s = explode('/', CUSTOM_PATH);
            $n = (int)(count($s) - 1) + $n;
        }

        return $n;
    }

    /**
     * @param $n
     * @param $default
     * @return array|mixed
     */
    public function uriToAssoc($n = 3, $default = array())
    {
        $n = $this->getSegments($n);
        return $this->setUriToAssoc($n, $default, 'segment');
    }

    /**
     * @param $n
     * @param $default
     * @return array|mixed
     */
    public function ruriToAssoc($n = 3, $default = array())
    {
        $n = $this->getSegments($n);
        return $this->setUriToAssoc($n, $default, 'rsegment');
    }

    /**
     * @param $n
     * @param $default
     * @param $which
     * @return array|mixed
     */
    protected function setUriToAssoc($n = 3, $default = array(), $which = 'segment')
    {
        $n = $this->getSegments($n);

        if (!is_numeric($n)) {
            return $default;
        }

        if (isset($this->keyVal[$which], $this->keyVal[$which][$n])) {
            return $this->keyVal[$which][$n];
        }

        $total_segments = "total_{$which}s";
        $segment_array = "{$which}_array";

        if ($this->$total_segments() < $n) {
            return (count($default) === 0)
                ? array()
                : array_fill_keys($default, NULL);
        }

        $segments = array_slice($this->$segment_array(), ($n - 1));
        $i = 0;
        $lastval = '';
        $retval = array();
        foreach ($segments as $seg) {
            if ($i % 2) {
                $retval[$lastval] = $seg;
            } else {
                $retval[$seg] = NULL;
                $lastval = $seg;
            }

            $i++;
        }

        if (count($default) > 0) {
            foreach ($default as $val) {
                if (!array_key_exists($val, $retval)) {
                    $retval[$val] = NULL;
                }
            }
        }

        isset($this->keyVal[$which]) or $this->keyVal[$which] = array();
        $this->keyVal[$which][$n] = $retval;
        return $retval;
    }

    /**
     * @param $array
     * @return string
     */
    public function assocToUri($array)
    {
        $temp = array();
        foreach ((array)$array as $key => $val) {
            $temp[] = $key;
            $temp[] = $val;
        }

        return implode('/', $temp);
    }

    /**
     * @param $n
     * @param $where
     * @return string
     */
    public function slashSegment($n, $where = 'trailing')
    {
        $n = $this->getSegments($n);
        return $this->setSlashSegment($n, $where, 'segment');
    }

    /**
     * @param $n
     * @param $where
     * @return string
     */
    public function slashRsegment($n, $where = 'trailing')
    {
        $n = $this->getSegments($n);
        return $this->setSlashSegment($n, $where, 'rsegment');
    }

    /**
     * @param $n
     * @param $where
     * @param $which
     * @return string
     */
    protected function setSlashSegment($n, $where = 'trailing', $which = 'segment')
    {
        $n = $this->getSegments($n);

        $leading = $trailing = '/';

        if ($where === 'trailing') {
            $leading = '';
        } elseif ($where === 'leading') {
            $trailing = '';
        }

        return $leading . $this->$which($n) . $trailing;
    }

    /**
     * @return array
     */
    public function segmentArray()
    {
        return $this->segments;
    }

    /**
     * @return array
     */
    public function rsegmentArray()
    {
        return $this->rsegments;
    }

    /**
     * @return int
     */
    public function totalSegments()
    {
        return count($this->segments);
    }

    /**
     * @return int
     */
    public function totalRsegments()
    {
        return count($this->rsegments);
    }

    /**
     * @return string
     */
    public function uriString()
    {
        return $this->uriString;

    }

    /**
     * @return string
     */
    public function getQueryString()
    {
        return $this->parseQueryString();
    }

    /**
     * @return string|null
     */
    public function fullUrl($xss = true, $htmlentities = true)
    {
        if (!empty($this->uriString())) {

            if ($xss === true && $htmlentities == true) {

                $security = new Security();
                $queryString = htmlentities($security->xssClean($this->parseQueryString()), ENT_QUOTES | ENT_HTML5, CHARSET);

            } else if ($xss === false && $htmlentities == true) {

                $queryString = htmlentities($this->parseQueryString(), ENT_QUOTES | ENT_HTML5, CHARSET);

            } else if ($xss === true && $htmlentities == false) {

                $security = new Security();
                $queryString = $security->xssClean($this->parseQueryString());

            } else {

                $queryString = $this->parseQueryString();
            }

            return $this->uriString() . '?' . $queryString;

        }

        return null;
    }

}
