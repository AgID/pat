<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers;

defined('_FRAMEWORK_') or exit ('No direct script access allowed');

class S
{

    /**
     * Converte una stringa da ISO-8859-1 a UTF-8
     * @param $string
     * @return array|false|S|S[]|null
     */
    public static function toUtf8($string = null)
    {
        return mb_convert_encoding($string, "UTF-8", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
    }

    /**
     * Converte una stringa da UTF-8 a ISO-8859-1
     * @param $string
     * @return array|false|S|S[]|null
     */
    public static function toIso($string = null)
    {
        return mb_convert_encoding($string, "ISO-8859-1", mb_detect_encoding($string, "UTF-8, ISO-8859-1, ISO-8859-15", true));
    }

    /**
     * Sanifica tipo: URL
     * @param $string
     * @return mixed
     */
    public static function sanitizeUrl($string = null)
    {
        return self::sanitizeItem($string, 'url');
    }

    /**
     * Sanifica tipo: INTERO
     * @param $string
     * @return mixed
     */
    public static function sanitizeInt($string = null)
    {
        return self::sanitizeItem($string, 'int');
    }

    /**
     * Sanifica tipo: FLOAT
     * @param $string
     * @return mixed
     */
    public static function sanitizeFloat($string = null)
    {
        return self::sanitizeItem($string, 'float');
    }

    /**
     * Sanifica tipo: EMAIL
     * @param $string
     * @return mixed
     */
    public static function sanitizeEmail($string = null)
    {
        return self::sanitizeItem($string, 'email');
    }

    /**
     * Sanifica tipo: STRING
     * @param $string
     * @return mixed
     */
    public static function sanitizeString($string = null)
    {
        return self::sanitizeItem($string, 'string');
    }

    /**
     * Sanifica livello globale: CSS
     * @return void
     */
    public static function sanitizeGlobalXSS()
    {
        $_GET = filter_input_array(INPUT_GET, FILTER_SANITIZE_STRING);
        $_POST = filter_input_array(INPUT_POST, FILTER_SANITIZE_STRING);
        $_REQUEST = (array)$_POST + (array)$_GET + (array)$_REQUEST;
    }

    /**
     * Safinicatore
     * @param $var
     * @param $type
     * @return mixed
     */
    protected static function sanitizeItem($var = null, $type = null)
    {
        $flags = NULL;
        switch ($type) {
            case 'url':
                $filter = FILTER_SANITIZE_URL;
                break;
            case 'int':
                $filter = FILTER_SANITIZE_NUMBER_INT;
                break;
            case 'float':
                $filter = FILTER_SANITIZE_NUMBER_FLOAT;
                $flags = FILTER_FLAG_ALLOW_FRACTION | FILTER_FLAG_ALLOW_THOUSAND;
                break;
            case 'email':
                $var = substr($var, 0, 254);
                $filter = FILTER_SANITIZE_EMAIL;
                break;
            case 'string':
            default:
                $filter = FILTER_SANITIZE_STRING;
                $flags = FILTER_FLAG_NO_ENCODE_QUOTES;
                break;
        }
        $output = filter_var($var, $filter, $flags);
        return ($output);
    }

    /**
     * Eliminazione tag html, elementi di tabulazioni e nuova linea
     * @param $string
     * @return array|string|string[]|null
     */
    public static function stripTags($string = null)
    {
        return trim(preg_replace('/(\v|\s)+/', ' ', strip_tags($string)));
    }


    /**
     * Metodo che formatta i separatori decimali di una stringa ad un formato valuta valido.
     * @param $number
     * @param $decimals
     * @param $decimalSep
     * @param $thousandsSep
     * @return string
     */
    public static function currency($number, $decimals = 2, $decimalSep = '.', $thousandsSep = ',', $hasNumberFormat = true)
    {

        if (strlen((string)$number) === 0) {
            return null;
        }

        $getComma = strpos($number, ',', 1);
        $getPoint = strpos($number, '.', 1);

        if ($getComma >= 1 && $getPoint >= 1) {

            if ($getPoint < $getComma) {

                $number = preg_replace('/\./u', '', $number);
                $number = preg_replace('/,/u', '.', $number);
            } else {

                $number = preg_replace('/,/u', '', $number);
                $number = preg_replace('/\./u', '.', $number);
            }
        } else if ($getComma >= 1 && $getPoint == 0) {

            $number = self::converterFormat($number);
        } else if ($getPoint >= 1 && $getComma == 0 && !floatval($number)) {

            $number = self::converterFormat($number, '.');
        }

        return ($hasNumberFormat) ? number_format($number, $decimals, $decimalSep, $thousandsSep) : $number;
    }

    /**
     * @param $number
     * @param $symbol
     * @return string
     */
    private static function converterFormat($number = '', $symbol = ',')
    {

        $number = (string)$number;
        $countSymbol = strpos($number, $symbol);

        $r = '';
        $found = 0;

        for ($i = 0; $i < strlen((string)$number); $i++) {

            $tmpString = '';

            if ($number[$i] == $symbol) {

                $found++;

                if ((int)$found === (int)$countSymbol) {

                    $tmpString = '.';
                }
            } else {

                $tmpString = $number[$i];
            }

            $r .= $tmpString;
        }

        return $r;
    }

    public static function chartsEntityDecode($string = null, $quote = ENT_QUOTES)
    {
        return html_entity_decode(htmlspecialchars_decode($string), $quote);
    }

    public static function specialChars($string = null)
    {
        return htmlspecialchars($string, ENT_QUOTES | ENT_SUBSTITUTE | ENT_HTML401, CHARSET);
    }

    public static function startsWith($search, $string)
    {
        return substr($string, 0, strlen($search)) === $search;
    }

    public static function endsWith($search, $string)
    {
        return substr($string, -strlen($search)) == $search;
    }

    public static function escapeXss($string = '', $xss = true, $htmlEscape = true)
    {

        if ($xss) {
            $string = (new \System\Security())->xssClean($string);
        }

        if ($htmlEscape) {
            $string = htmlEscape($string);
        }

        return $string;
    }

    public static function ellipsizeString($str, $maxLength = 20, $ellipsis = '&hellip;') {
        $str = trim(strip_tags($str));

        if (mb_strlen($str) <= $maxLength) {
            return $str;
        }

        $trimmedLength = $maxLength - mb_strlen($ellipsis);
        $startLength = ceil($trimmedLength / 2);
        $endLength = floor($trimmedLength / 2);

        $start = mb_substr($str, 0, $startLength);
        $end = mb_substr($str, -$endLength);

        return $start . $ellipsis . $end;
    }
}