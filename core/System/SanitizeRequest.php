<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

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
 * @package    CodeIgniter
 * @author    EllisLab Dev Team
 * @copyright    Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright    Copyright (c) 2014 - 2019, British Columbia Institute of Technology (https://bcit.ca/)
 * @copyright    Copyright (c) 2019 - 2022, CodeIgniter Foundation (https://codeigniter.com/)
 * @license    https://opensource.org/licenses/MIT	MIT License
 * @link    https://codeigniter.com
 * @since    Version 1.0.0
 * @filesource
 */

namespace System;

use InvalidArgumentException;
use \Maer\Config\Config;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class SanitizeRequest
{
    protected $allowGetArray;
    protected $enableXss;
    protected $standardNewlines;
    protected $security;

    /**
     * Istanza condivisa per la richiesta.
     *
     * Il costruttore carica Config/app.php e costruisce un Security, e veniva pagato a OGNI
     * lettura di parametro: Input::run() e Input::getData() ne creano uno per chiamata, e i
     * punti di chiamata Input::*() nel codebase sono 11.820. Sulla homepage sono ~1.320
     * caricamenti di app.php, il 58% del totale misurato.
     *
     * Perche' condividere l'istanza e' sicuro: le uniche assegnazioni a $this stanno nel
     * costruttore (security, allowGetArray, enableXss, standardNewlines), tutti i metodi
     * pubblici sono puri e i tre valori letti sono letterali in app.php - nessuno dipende
     * dalla richiesta o dal Registry.
     *
     * Nota per chi tocchera' i file di config: se qualcuno aggiungesse una _env() ad app.php
     * si avrebbe una ricorsione (_env -> SanitizeRequest -> load(app.php) -> _env). Il rischio
     * esiste gia' oggi e questa memoizzazione non lo introduce, ma qui e' piu' facile da
     * dimenticare.
     */
    public static function instance(): self
    {
        static $instance = null;

        if ($instance === null) {
            $instance = new self();
        }

        return $instance;
    }

    public function __construct()
    {
        $config = new Config();
        $config->load(APP_PATH . 'Config/app.php');

        $this->security = new Security();
        $this->allowGetArray = $config->get('allow_get_array', false);
        $this->enableXss = $config->get('global_xss_filtering', true);
        $this->standardNewlines = (bool)$config->get('standardize_newlines');
    }

    public function fetchFromArray(&$array, $index = null, $xssClean = true, $sanitizeKey = true, $sanitizeData = true, $file = false)
    {
        if ($index === null) {
            $index = array_keys($array);
        }

        if (is_array($index)) {
            $output = [];

            foreach ($index as $key) {
                // Sanificazione della chiave richiesta
                if ($sanitizeKey === true) {
                    $key = $this->cleanRequestKeys($key);
                }

                $output[$key] = $this->fetchFromArray($array, $key, $xssClean, $sanitizeKey, $sanitizeData, $file);
            }

            return $output;
        }

        // Sanificazione della chiave richiesta
        if ($sanitizeKey === true) {
            $index = $this->cleanRequestKeys($index);
        }

        $value = $this->getValueFromArray($array, $index);

        // Per il campo file la sanificazione non viene applicata.
        if ($file === true) {
            return $value;
        }

        // Sanificazione del valore della richiesta
        if ($sanitizeData === true) {
            $value = $this->cleanRequestData($value);
        }

        return ($xssClean === true)
            ? $this->security->xssClean($value)
            : $value;
    }

    private function getValueFromArray($array, $index)
    {
        $keys = explode('.', $index);

        foreach ($keys as $key) {
            if (is_array($array) && array_key_exists($key, $array)) {
                $array = $array[$key];
            } else {
                return null;
            }
        }

        return $array;
    }

    public function cleanRequestData($data)
    {
        if ($data === null) {
            return $data;
        }

        $utf8 = new Utf8(CHARSET);

        if (is_array($data)) {
            $newArray = [];
            foreach ($data as $key => $value) {
                $cleanKey = $this->cleanRequestKeys($key);
                $newArray[$cleanKey] = $this->cleanRequestData($value);
            }
            return $newArray;
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
    public function cleanRequestKeys($data, $fatal = false)
    {
        $utf8 = new Utf8(CHARSET);

        // Aggiungiamo il carattere punto '.' ai caratteri consentiti nell'espressione regolare
        if (!empty($data) && !is_array($data) && !preg_match('/^[a-z0-9:._\/|-]+$/i', $data)) {
            if ($fatal === true) {
                throw new \Exception('Disallowed Key Characters.');
            } else {
                \setStatusHeader(403);
                echo 'Disallowed Key Characters.';
                exit(7);
            }
        }

        if (UTF8_ENABLED === true && !empty($data)) {
            return $utf8->cleanString($data);
        }

        return $data;
    }

    public function cleanRequest($data)
    {

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
                foreach ($value as $subKey => $subValue) {
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
