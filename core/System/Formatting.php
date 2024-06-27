<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class Formatting
{

    public static function utf8UriEncode($utf8String, $length = 0)
    {
        $unicode = '';
        $values = array();
        $numOctets = 1;
        $unicodeLength = 0;

        $stringLength = strlen((string)$utf8String);

        for ($i = 0; $i < $stringLength; $i++) {

            $value = ord($utf8String[$i]);

            if ($value < 128) {

                if ($length && ($unicodeLength >= $length))
                    break;
                $unicode .= chr($value);
                $unicodeLength++;

            } else {

                if (count($values) == 0) $numOctets = ($value < 224) ? 2 : 3;

                $values[] = $value;

                if ($length && ($unicodeLength + ($numOctets * 3)) > $length)
                    break;

                if (count($values) == $numOctets) {

                    if ($numOctets == 3) {

                        $unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]) . '%' . dechex($values[2]);
                        $unicodeLength += 9;

                    } else {

                        $unicode .= '%' . dechex($values[0]) . '%' . dechex($values[1]);
                        $unicodeLength += 6;

                    }

                    $values = array();
                    $numOctets = 1;

                }

            }

        }

        return $unicode;
    }


    public static function seemsUtf8($string)
    {
        $length = strlen((string)$string);
        for ($i = 0; $i < $length; $i++) {

            $c = ord($string[$i]);
            if ($c < 0x80) $n = 0;
            elseif (($c & 0xE0) == 0xC0) $n = 1;
            elseif (($c & 0xF0) == 0xE0) $n = 2;
            elseif (($c & 0xF8) == 0xF0) $n = 3;
            elseif (($c & 0xFC) == 0xF8) $n = 4;
            elseif (($c & 0xFE) == 0xFC) $n = 5;
            else return false;

            for ($j = 0; $j < $n; $j++) {

                if ((++$i == $length) || ((ord($string[$i]) & 0xC0) != 0x80))
                    return false;

            }

        }
        return true;
    }


    public static function sanitize($title)
    {
        $title = strip_tags($title);
        $title = preg_replace('|%([a-fA-F0-9][a-fA-F0-9])|', '---$1---', $title);
        $title = str_replace('%', '', $title);
        $title = preg_replace('|---([a-fA-F0-9][a-fA-F0-9])---|', '%$1', $title);

        if (self::seemsUtf8($title)) {

            if (function_exists('mb_strtolower')) {

                $title = mb_strtolower($title, CHARSET);

            }

            $title = self::utf8UriEncode($title, 200);

        }

        $title = strtolower($title);
        $title = preg_replace('/&.+?;/', '', $title);
        $title = str_replace('.', '-', $title);
        $title = preg_replace('/[^%a-z0-9 _-]/', '', $title);
        $title = preg_replace('/\s+/', '-', $title);
        $title = preg_replace('|-+|', '-', $title);

        return trim($title, '-');
    }

    public static function slug($string = '', $separator = '-', $lowercase = true)
    {
        $table = [
            'Š' => 'S', 'š' => 's', 'Đ' => 'Dj', 'đ' => 'dj', 'Ž' => 'Z', 'ž' => 'z', 'Č' => 'C', 'č' => 'c', 'Ć' => 'C', 'ć' => 'c',
            'À' => 'A', 'Á' => 'A', 'Â' => 'A', 'Ã' => 'A', 'Ä' => 'A', 'Å' => 'A', 'Æ' => 'A', 'Ç' => 'C', 'È' => 'E', 'É' => 'E',
            'Ê' => 'E', 'Ë' => 'E', 'Ì' => 'I', 'Í' => 'I', 'Î' => 'I', 'Ï' => 'I', 'Ñ' => 'N', 'Ò' => 'O', 'Ó' => 'O', 'Ô' => 'O',
            'Õ' => 'O', 'Ö' => 'O', 'Ø' => 'O', 'Ù' => 'U', 'Ú' => 'U', 'Û' => 'U', 'Ü' => 'U', 'Ý' => 'Y', 'Þ' => 'B', 'ß' => 'Ss',
            'à' => 'a', 'á' => 'a', 'â' => 'a', 'ã' => 'a', 'ä' => 'a', 'å' => 'a', 'æ' => 'a', 'ç' => 'c', 'è' => 'e', 'é' => 'e',
            'ê' => 'e', 'ë' => 'e', 'ì' => 'i', 'í' => 'i', 'î' => 'i', 'ï' => 'i', 'ð' => 'o', 'ñ' => 'n', 'ò' => 'o', 'ó' => 'o',
            'ô' => 'o', 'õ' => 'o', 'ö' => 'o', 'ø' => 'o', 'ù' => 'u', 'ú' => 'u', 'û' => 'u', 'ý' => 'y', 'ý' => 'y', 'þ' => 'b',
            'ÿ' => 'y', 'Ŕ' => 'R', 'ŕ' => 'r', '/' => '-', ' ' => in_array($separator, ['-', '_']) ? $separator : '-'
        ];

        $trans = [
            '/\s{2,}/' => ' ',
            '/[\t\n]/' => ' ',
            '&.+?;' => '',
            '[^\w\d _-]' => ''
        ];

        foreach ($trans as $key => $val) {

            $string = preg_replace('#' . $key . '#i' . (UTF8_ENABLED ? 'u' : ''), $val, (string)$string);

        }

        $string = strip_tags($string);
        $string = strtr($string, $table);
        $string = preg_replace('/([-])\1+/', '-', $string);

        return ((bool)$lowercase === true) ? strtolower($string) : $string;

    }

    public static function escapeSql($string = '', $removeInvisibleCharacters = true, $urlEncoded = true)
    {
        if ((bool)$removeInvisibleCharacters === true) {
            $string = self::removeInvisibleCharacters($string, $urlEncoded);
        }

        if (is_array($string)) {
            return array_map(__METHOD__, $string);
        }

        if (!empty($string) && is_string($string)) {
            return str_replace(array('\\', "\0", "\n", "\r", "'", '"', "\x1a"), array('\\\\', '\\0', '\\n', '\\r', "\\'", '\\"', '\\Z'), $string);
        }

        return $string;
    }

    public static function charsetDecodeUtf8($string)
    {
        if (!preg_match("/[\200-\237]/", $string)
            && !preg_match("/[\241-\377]/", $string)
        ) {
            return $string;
        }

        $string = preg_replace("/([\340-\357])([\200-\277])([\200-\277])/e",
            "'&#'.((ord('\\1')-224)*4096 + (ord('\\2')-128)*64 + (ord('\\3')-128)).';'",
            $string
        );

        $string = preg_replace("/([\300-\337])([\200-\277])/e",
            "'&#'.((ord('\\1')-192)*64+(ord('\\2')-128)).';'",
            $string
        );

        return $string;
    }


    public static function removeInvisibleCharacters($string, $urlEncoded = TRUE)
    {
        $nonDisplayables = [];

        if ($urlEncoded) {
            $nonDisplayables[] = '/%0[0-8bcef]/i';
            $nonDisplayables[] = '/%1[0-9a-f]/i';
            $nonDisplayables[] = '/%7f/i';
        }

        $nonDisplayables[] = '/[\x00-\x08\x0B\x0C\x0E-\x1F\x7F]+/S';

        do {

            $string = preg_replace($nonDisplayables, '', $string, -1, $count);

        } while ($count);

        return $string;
    }


    public static function htmlEscape($var = '', $charset = CHARSET, $doubleEncode = TRUE)
    {
        if (empty($var)) {
            return $var;
        }

        if (is_array($var)) {
            foreach (array_keys($var) as $key) {
                $var[$key] = self::htmlEscape($var[$key], $doubleEncode);
            }

            return $var;
        }

        return htmlspecialchars($var, ENT_QUOTES, $charset, $doubleEncode);
    }

    public static function convertEncodeQuotes($input = '', $charset = CHARSET, $htmlEntities = "HTML-ENTITIES")
    {
        return preg_replace_callback("/(&#[0-9]+;)/", function ($m) use ($charset, $htmlEntities) {
            return mb_convert_encoding($m[1], CHARSET, "HTML-ENTITIES");
        }, (string)$input);
    }

    function asciiToEntities($string)
    {
        $out = '';
        $length = defined('MB_OVERLOAD_STRING')
            ? mb_strlen((string)$string, '8bit') - 1
            : strlen((string)$string) - 1;
        for ($i = 0, $count = 1, $temp = array(); $i <= $length; $i++) {
            $ordinal = ord($string[$i]);

            if ($ordinal < 128) {

                if (count($temp) === 1) {
                    $out .= '&#' . array_shift($temp) . ';';
                    $count = 1;
                }

                $out .= $string[$i];
            } else {
                if (count($temp) === 0) {
                    $count = ($ordinal < 224) ? 2 : 3;
                }

                $temp[] = $ordinal;

                if (count($temp) === $count) {
                    $number = ($count === 3)
                        ? (($temp[0] % 16) * 4096) + (($temp[1] % 64) * 64) + ($temp[2] % 64)
                        : (($temp[0] % 32) * 64) + ($temp[1] % 64);

                    $out .= '&#' . $number . ';';
                    $count = 1;
                    $temp = array();
                } elseif ($i === $length) {
                    $out .= '&#' . implode(';', $temp) . ';';
                }
            }
        }

        return $out;
    }

    public static function entitiesToAscii($string, $all = TRUE)
    {
        if (preg_match_all('/\&#(\d+)\;/', $string, $matches)) {
            for ($i = 0, $s = count($matches[0]); $i < $s; $i++) {
                $digits = $matches[1][$i];
                $out = '';

                if ($digits < 128) {
                    $out .= chr($digits);

                } elseif ($digits < 2048) {
                    $out .= chr(192 + (($digits - ($digits % 64)) / 64)) . chr(128 + ($digits % 64));
                } else {
                    $out .= chr(224 + (($digits - ($digits % 4096)) / 4096))
                        . chr(128 + ((($digits % 4096) - ($digits % 64)) / 64))
                        . chr(128 + ($digits % 64));
                }

                $string = str_replace($matches[0][$i], $out, $string);
            }
        }

        if ($all) {
            return str_replace(
                array('&amp;', '&lt;', '&gt;', '&quot;', '&apos;', '&#45;'),
                array('&', '<', '>', '"', "'", '-'),
                $string
            );
        }

        return $string;
    }

    public static function escapeSpecialChars($text, $charset = CHARSET)
    {
        return htmlspecialchars($text, ENT_QUOTES | ENT_SUBSTITUTE, $charset);
    }

    public static function decodeEntities($text, $charset = CHARSET)
    {
        return html_entity_decode($text, ENT_QUOTES, $charset);
    }

    public static function convertAccentedChars($string)
    {
        static $arrayFrom, $arrayTo;

        if (!is_array($arrayFrom)) {
            if (file_exists(APP_PATH . 'config/foreign_chars.php')) {
                include(APP_PATH . 'config/foreign_chars.php');
            }

            if (file_exists(APP_PATH . 'config/foreign_chars.php')) {
                include(APP_PATH . 'config/foreign_chars.php');
            }

            if (empty($foreignCharacters) or !is_array($foreignCharacters)) {
                $arrayFrom = array();
                $arrayTo = array();

                return $string;
            }

            $arrayFrom = array_keys($foreignCharacters);
            $arrayTo = array_values($foreignCharacters);
        }

        return preg_replace($arrayFrom, $arrayTo, $string);
    }

    public static function isUTF8($string)
    {

        $mState = 0;
        $mUcs4 = 0;
        $mBytes = 1;

        $len = strlen((string)$string);

        for ($i = 0; $i < $len; $i++) {

            $in = ord($string[$i]);

            if ($mState == 0) {
                if (0 == (0x80 & ($in))) {
                    $mBytes = 1;
                } else if (0xC0 == (0xE0 & ($in))) {
                    $mUcs4 = ($in);
                    $mUcs4 = ($mUcs4 & 0x1F) << 6;
                    $mState = 1;
                    $mBytes = 2;
                } else if (0xE0 == (0xF0 & ($in))) {
                    $mUcs4 = ($in);
                    $mUcs4 = ($mUcs4 & 0x0F) << 12;
                    $mState = 2;
                    $mBytes = 3;
                } else if (0xF0 == (0xF8 & ($in))) {
                    $mUcs4 = ($in);
                    $mUcs4 = ($mUcs4 & 0x07) << 18;
                    $mState = 3;
                    $mBytes = 4;
                } else if (0xF8 == (0xFC & ($in))) {
                    $mUcs4 = ($in);
                    $mUcs4 = ($mUcs4 & 0x03) << 24;
                    $mState = 4;
                    $mBytes = 5;
                } else if (0xFC == (0xFE & ($in))) {
                    $mUcs4 = ($in);
                    $mUcs4 = ($mUcs4 & 1) << 30;
                    $mState = 5;
                    $mBytes = 6;
                } else {
                    return FALSE;
                }
            } else {
                if (0x80 == (0xC0 & ($in))) {
                    $shift = ($mState - 1) * 6;
                    $tmp = $in;
                    $tmp = ($tmp & 0x0000003F) << $shift;
                    $mUcs4 |= $tmp;
                    if (0 == --$mState) {

                        if (((2 == $mBytes) && ($mUcs4 < 0x0080)) ||
                            ((3 == $mBytes) && ($mUcs4 < 0x0800)) ||
                            ((4 == $mBytes) && ($mUcs4 < 0x10000)) ||
                            (4 < $mBytes) ||
                            (($mUcs4 & 0xFFFFF800) == 0xD800) ||
                            ($mUcs4 > 0x10FFFF)) {

                            return FALSE;
                        }

                        $mState = 0;
                        $mUcs4 = 0;
                        $mBytes = 1;
                    }
                } else {

                    return FALSE;
                }
            }
        }
        return TRUE;
    }

}