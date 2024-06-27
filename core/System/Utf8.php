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

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class Utf8
{
    protected $utf8Enabled = false;

    public function __construct($charset)
    {
        if (!defined('UTF8_ENABLED')) {
            if (
                defined('PREG_BAD_UTF8_ERROR')
                && (ICONV_ENABLED === TRUE or MB_ENABLED === TRUE)
                && $charset === 'UTF-8'
            ) {
                define('UTF8_ENABLED', TRUE);
            } else {
                define('UTF8_ENABLED', FALSE);
            }
        }
    }


    /**
     * Funzione che rimuove i caratteri illegali da una stringa
     * @param string $str Stringa da pulire
     * @return string
     */
    public function cleanString(string $str): string
    {
        if ($this->isAscii($str) === FALSE) {
            if (MB_ENABLED) {
                $str = mb_convert_encoding($str, 'UTF-8', 'UTF-8');
            } elseif (ICONV_ENABLED) {
                $str = @iconv('UTF-8', 'UTF-8//IGNORE', $str);
            }
        }

        return $str;
    }

    /**
     * Funzione che rimuove i caratteri invisibili
     * @param string $str Stringa da pulire (non crittografata)
     * @return bool|string
     */
    public function safeAsciiForXml($str)
    {
        return removeInvisibleCharacters($str, FALSE);
    }


    /**
     * Converte la stringa in utf-8
     * @param string $str Stringa da covertire
     * @param string $encoding Codifica della stringa che deve essere convertita
     * @return bool|string
     */

    public function convertToUtf8(string $str, string $encoding): bool|string
    {
        if (MB_ENABLED) {
            return mb_convert_encoding($str, 'UTF-8', $encoding);
        } elseif (ICONV_ENABLED) {
            return @iconv($encoding, 'UTF-8', $str);
        }

        return false;
    }

    /**
     * Controlla se la crittografia è Ascii
     * @param string $str Stringa da verificare
     * @return bool
     */
    public function isAscii(string $str): bool
    {
        return (preg_match('/[^\x00-\x7F]/S', $str) === 0);
    }


    /**
     * Controlla se la crittografia è utf-8
     * @param string $string Stringa da verificare
     * @return bool
     */
    public function isUtf8(string $string = ''): bool
    {

        return (bool)mb_detect_encoding($string, 'utf-8', true);
    }
}
