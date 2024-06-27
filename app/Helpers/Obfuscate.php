<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class Obfuscate
{
    private static $hash = '';

    /**
     * @param $id
     * @return array|false|S|S[]
     */
    public static function encode($id)
    {
        if (!is_numeric($id) or $id < 1) {
            return FALSE;
        }
        $id = (int)$id;
        if ($id > pow(2, 31)) {
            return FALSE;
        }
        $segment1 = self::getHash($id, 16);
        $segment2 = self::getHash($segment1, 8);
        $dec = (int)base_convert($segment2, 16, 10);
        $dec = ($dec > $id) ? $dec - $id : $dec + $id;
        $segment2 = base_convert($dec, 10, 16);
        $segment2 = str_pad($segment2, 8, '0', STR_PAD_LEFT);
        $segment3 = self::getHash($segment1 . $segment2, 8);
        $hex = $segment1 . $segment2 . $segment3;
        $bin = pack('H*', $hex);
        $oid = base64_encode($bin);
        $oid = str_replace(array('+', '/', '='), array('$', ':', ''), $oid);
        return $oid;
    }

    /**
     * @param $oid
     * @return float|int
     */
    public static function decode($oid)
    {
        if (!preg_match('/^[A-Z0-9\:\$]{21,23}$/i', $oid)) {
            return 0;
        }
        $oid = str_replace(array('$', ':'), array('+', '/'), $oid);
        $bin = base64_decode($oid);
        $hex = unpack('H*', $bin);
        $hex = $hex[1];
        if (!preg_match('/^[0-9a-f]{32}$/', $hex)) {
            return 0;
        }
        $segment1 = substr($hex, 0, 16);
        $segment2 = substr($hex, 16, 8);
        $segment3 = substr($hex, 24, 8);
        $exp2 = self::getHash($segment1, 8);
        $exp3 = self::getHash($segment1 . $segment2, 8);
        if ($segment3 != $exp3) {
            return 0;
        }
        $v1 = (int)base_convert($segment2, 16, 10);
        $v2 = (int)base_convert($exp2, 16, 10);
        $id = abs($v1 - $v2);
        return $id;
    }

    /**
     * @param $hash
     */
    public static function setHash($hash)
    {
        self::$hash = !empty($hash) ? $hash : '';
    }

    /**
     * @param $str
     * @param $len
     * @return false|S
     */
    private static function getHash($str, $len)
    {
        return substr(sha1($str . self::$hash), 0, $len);
    }
}