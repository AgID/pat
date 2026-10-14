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

    public static function utf8Encode(string $s): string
    {
        $s .= $s;
        $len = \strlen($s);

        for ($i = $len >> 1, $j = 0; $i < $len; ++$i, ++$j) {
            switch (true) {
                case $s[$i] < "\x80":
                    $s[$j] = $s[$i];
                    break;
                case $s[$i] < "\xC0":
                    $s[$j] = "\xC2";
                    $s[++$j] = $s[$i];
                    break;
                default:
                    $s[$j] = "\xC3";
                    $s[++$j] = \chr(\ord($s[$i]) - 64);
                    break;
            }
        }

        return substr($s, 0, $j);
    }

    public static function utf8Decode(string $string): string
    {
        $s = (string)$string;
        $len = \strlen($s);

        for ($i = 0, $j = 0; $i < $len; ++$i, ++$j) {
            switch ($s[$i] & "\xF0") {
                case "\xC0":
                case "\xD0":
                    $c = (\ord($s[$i] & "\x1F") << 6) | \ord($s[++$i] & "\x3F");
                    $s[$j] = $c < 256 ? \chr($c) : '?';
                    break;

                case "\xF0":
                    ++$i;
                // no break

                case "\xE0":
                    $s[$j] = '?';
                    $i += 2;
                    break;

                default:
                    $s[$j] = $s[$i];
            }
        }

        return substr($s, 0, $j);
    }

    public static function normalize($string = '')
    {
        if ($string === null || strlen($string)===0) {
            return $string;
        }

        $search = [
            chr(0xE2) . chr(0x82) . chr(0xAC),
            chr(0xE2) . chr(0x80) . chr(0x9A),
            chr(0xC6) . chr(0x92),
            chr(0xE2) . chr(0x80) . chr(0x9E),
            chr(0xE2) . chr(0x80) . chr(0xA6),
            chr(0xE2) . chr(0x80) . chr(0xA0),
            chr(0xE2) . chr(0x80) . chr(0xA1),
            chr(0xCB) . chr(0x86),
            chr(0xE2) . chr(0x80) . chr(0xB0),
            chr(0xC5) . chr(0xA0),
            chr(0xE2) . chr(0x80) . chr(0xB9),
            chr(0xC5) . chr(0x92),
            chr(0xC5) . chr(0xBD),
            chr(0xE2) . chr(0x80) . chr(0x98),
            chr(0xE2) . chr(0x80) . chr(0x99),
            chr(0xE2) . chr(0x80) . chr(0x9C),
            chr(0xE2) . chr(0x80) . chr(0x9D),
            chr(0xE2) . chr(0x80) . chr(0xA2),
            chr(0xE2) . chr(0x80) . chr(0x93),
            chr(0xE2) . chr(0x80) . chr(0x94),
            chr(0xCB) . chr(0x9C),
            chr(0xE2) . chr(0x84) . chr(0xA2),
            chr(0xC5) . chr(0xA1),
            chr(0xE2) . chr(0x80) . chr(0xBA),
            chr(0xC5) . chr(0x93),
            chr(0xC5) . chr(0xBE),
            chr(0xC5) . chr(0xB8),
            chr(0xC2) . chr(0xA0),
            chr(0xC2) . chr(0xA1),
            chr(0xC2) . chr(0xA2),
            chr(0xC2) . chr(0xA3),
            chr(0xC2) . chr(0xA4),
            chr(0xC2) . chr(0xA5),
            chr(0xC2) . chr(0xA6),
            chr(0xC2) . chr(0xA7),
            chr(0xC2) . chr(0xA8),
            chr(0xC2) . chr(0xA9),
            chr(0xC2) . chr(0xAA),
            chr(0xC2) . chr(0xAB),
            chr(0xC2) . chr(0xAC),
            chr(0xC2) . chr(0xAD),
            chr(0xC2) . chr(0xAE),
            chr(0xC2) . chr(0xAF),
            chr(0xC2) . chr(0xB0),
            chr(0xC2) . chr(0xB1),
            chr(0xC2) . chr(0xB2),
            chr(0xC2) . chr(0xB3),
            chr(0xC2) . chr(0xB4),
            chr(0xC2) . chr(0xB5),
            chr(0xC2) . chr(0xB6),
            chr(0xC2) . chr(0xB7),
            chr(0xC2) . chr(0xB8),
            chr(0xC2) . chr(0xB9),
            chr(0xC2) . chr(0xBA),
            chr(0xC2) . chr(0xBB),
            chr(0xC2) . chr(0xBC),
            chr(0xC2) . chr(0xBD),
            chr(0xC2) . chr(0xBE),
            chr(0xC2) . chr(0xBF),
            chr(0xC3) . chr(0x80),
            chr(0xC3) . chr(0x81),
            chr(0xC3) . chr(0x82),
            chr(0xC3) . chr(0x83),
            chr(0xC3) . chr(0x84),
            chr(0xC3) . chr(0x85),
            chr(0xC3) . chr(0x86),
            chr(0xC3) . chr(0x87),
            chr(0xC3) . chr(0x88),
            chr(0xC3) . chr(0x89),
            chr(0xC3) . chr(0x8A),
            chr(0xC3) . chr(0x8B),
            chr(0xC3) . chr(0x8C),
            chr(0xC3) . chr(0x8D),
            chr(0xC3) . chr(0x8E),
            chr(0xC3) . chr(0x8F),
            chr(0xC3) . chr(0x90),
            chr(0xC3) . chr(0x91),
            chr(0xC3) . chr(0x92),
            chr(0xC3) . chr(0x93),
            chr(0xC3) . chr(0x94),
            chr(0xC3) . chr(0x95),
            chr(0xC3) . chr(0x96),
            chr(0xC3) . chr(0x97),
            chr(0xC3) . chr(0x98),
            chr(0xC3) . chr(0x99),
            chr(0xC3) . chr(0x9A),
            chr(0xC3) . chr(0x9B),
            chr(0xC3) . chr(0x9C),
            chr(0xC3) . chr(0x9D),
            chr(0xC3) . chr(0x9E),
            chr(0xC3) . chr(0x9F),
            chr(0xC3) . chr(0xA0),
            chr(0xC3) . chr(0xA1),
            chr(0xC3) . chr(0xA2),
            chr(0xC3) . chr(0xA3),
            chr(0xC3) . chr(0xA4),
            chr(0xC3) . chr(0xA5),
            chr(0xC3) . chr(0xA6),
            chr(0xC3) . chr(0xA7),
            chr(0xC3) . chr(0xA8),
            chr(0xC3) . chr(0xA9),
            chr(0xC3) . chr(0xAA),
            chr(0xC3) . chr(0xAB),
            chr(0xC3) . chr(0xAC),
            chr(0xC3) . chr(0xAD),
            chr(0xC3) . chr(0xAE),
            chr(0xC3) . chr(0xAF),
            chr(0xC3) . chr(0xB0),
            chr(0xC3) . chr(0xB1),
            chr(0xC3) . chr(0xB2),
            chr(0xC3) . chr(0xB3),
            chr(0xC3) . chr(0xB4),
            chr(0xC3) . chr(0xB5),
            chr(0xC3) . chr(0xB6),
            chr(0xC3) . chr(0xB7),
            chr(0xC3) . chr(0xB8),
            chr(0xC3) . chr(0xB9),
            chr(0xC3) . chr(0xBA),
            chr(0xC3) . chr(0xBB),
            chr(0xC3) . chr(0xBC),
            chr(0xC3) . chr(0xBD),
            chr(0xC3) . chr(0xBE),
            chr(0xC3) . chr(0xBF)
        ];

        $replace = [
            '€', '‚', 'ƒ', '„', '…', '†', '‡', 'ˆ', '‰', 'Š', '‹', 'Œ', 'Ž', '‘', '’', '“', '”', '•', ' - ',
            ' - ', '˜', '™', 'š', '›', 'œ', 'ž', 'Ÿ', ' ', '¡', '¢', '£', '¤', '¥', '¦', '§', '¨', '©', 'ª',
            '«', '¬', ' ', '®', '¯', '°', '±', '²', '³', '´', 'µ', '¶', '·', '¸', '¹', 'º', '»', '¼', '½',
            '¾', '¿', 'À', 'Á', 'Â', 'Ã', 'Ä', 'Å', 'Æ', 'Ç', 'È', 'É', 'Ê', 'Ë', 'Ì', 'Í', 'Î', 'Ï', 'Ð',
            'Ñ', 'Ò', 'Ó', 'Ô', 'Õ', 'Ö', '×', 'Ø', 'Ù', 'Ú', 'Û', 'Ü', 'Ý', 'Þ', 'ß', 'à', 'á', 'â', 'ã',
            'ä', 'å', 'æ', 'ç', 'è', 'é', 'ê', 'ë', 'ì', 'í', 'î', 'ï', 'ð', 'ñ', 'ò', 'ó', 'ô', 'õ', 'ö',
            '÷', 'ø', 'ù', 'ú', 'û', 'ü', 'ý', 'þ', 'ÿ'
        ];

        return str_replace($search, $replace, $string);
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
