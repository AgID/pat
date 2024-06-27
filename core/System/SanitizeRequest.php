<?php
/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2019 - 2022, CodeIgniter Foundation
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2019, British Columbia Institute of Technology (https://bcit.ca/)
 * @copyright	Copyright (c) 2019 - 2022, CodeIgniter Foundation (https://codeigniter.com/)
 * @license	https://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */

namespace System;

use \Maer\Config\Config;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class SanitizeRequest
{
    protected $allowGetArray;
    protected $enableXss;
    protected $standardNewlines;
    protected $security;

    public function __construct()
    {
        $config = new Config();
        $config->load(APP_PATH . 'Config/app.php');

        $this->security = new Security();
        $this->allowGetArray = $config->get('allow_get_array', false);
        $this->enableXss = $config->get('global_xss_filtering', true);
        $this->standardNewlines = (bool)$config->get('standardize_newlines');
    }

    public function  fetchFromArray(&$array, $index = NULL, $xssClean = true, $sanitizekey = true, $sanitizeData = true, $file = false)
    {

        isset($index) or $index = array_keys($array);

        if (is_array($index)) {
            $output = [];

            foreach ($index as $key) {
                // Sanificazione della chiave  richiesta

                if ($sanitizekey === true) {
                    $key = $this->cleanRequestKeys($key);
                }

                $output[$key] = $this->fetchFromArray($array, $key, $xssClean, $sanitizekey = true, $sanitizeData = true);

            }

            return $output;
        }

        if (isset($array[$index])) {
            $value = $array[$index];

        } elseif (($count = preg_match_all('/(?:^[^\[]+)|\[[^]]*\]/', $index, $matches)) > 1) {
            $value = $array;
            for ($i = 0; $i < $count; $i++) {
                $key = trim($matches[0][$i], '[]');
                if ($key === '') {
                    break;
                }

                if (isset($value[$key])) {
                    $value = $value[$key];
                } else {
                    return NULL;
                }
            }
        } else {
            return NULL;
        }

        // Per il campo file la sanificazione non viene applicata.
        if ($file === true) {
            return $value;
        }

        // Sanificazione del valore della richiesta
        if ($sanitizeData === true) {
            $value = $this->cleanRequestData($value);
        }

        return ($xssClean === TRUE)
            ? $this->security->xssClean($value)
            : $value;
    }

    public function cleanRequestData($data)
    {
        $utf8 = new Utf8(CHARSET);

        if (is_array($data)) {
            $newArray = [];
            foreach (array_keys($data) as $key) {
                $newArray[$this->cleanRequestKeys($key)] = $this->cleanRequestKeys($data[$key]);
            }
            return $newArray;
        }


        if (!isPhp('5.4') && get_magic_quotes_gpc()) {
            $data = stripslashes($data);
        }


        if (UTF8_ENABLED === true) {
            $data = $utf8->cleanString($data);
        }

        $data = removeInvisibleCharacters($data, false);


        if ($this->standardNewlines === true) {
            return preg_replace('/(?:\r\n|[\r\n])/', PHP_EOL, $data);
        }

        return $data;
    }

    /**
     * Metodo per assicurare la pulizia delle stringhe per utenti malintenzionati
     * @param $data
     * @param bool $fatal
     * @return bool|string
     */
    public function cleanRequestKeys($data, $fatal = true)
    {
        $utf8 = new Utf8(CHARSET);

        if (!empty($data) && !is_array($data) && !preg_match('/^[a-z0-9:_\/|-]+$/i', $data)) {
            if ($fatal === true) {
                return false;
            } else {
                set_status_header(503);
                echo 'Disallowed Key Characters.';
                exit(7);
            }
        }


        if (UTF8_ENABLED === true && !empty($data) && !is_array($data)) {
            return $utf8->cleanString($data);
        }

        return $data;
    }

    public function cleanRequest($data) {

        if (is_string($data)) {
            return $this->cleanRequestData($data);
        }

        if (!is_array($data)) {
            throw new InvalidArgumentException('Invalid input data');
        }

        $cleanData = [];

        foreach ($data as $key => $value) {
            if (is_array($value)) {
                $cleanSubArray = [];
                foreach($value as $subKey => $subValue) {
                    if (!is_array($subKey)) {
                        $cleanSubKey = $this->cleanRequestKeys($subKey);

                        $cleanSubValue = is_string($subValue) ? $this->cleanRequestData($subValue) : $subValue;

                        $cleanSubArray[$cleanSubKey] = $cleanSubValue;
                    }
                }

                $cleanKey = $this->cleanRequestKeys($key);
                $cleanData[$cleanKey] = $cleanSubArray;
            } else {

                $cleanKey = $this->cleanRequestKeys($key);
                $cleanValue = is_string($value) ? $this->cleanRequestData($value) : $value;

                $cleanData[$cleanKey] = $cleanValue;
            }
        }

        return $cleanData;
    }
}
