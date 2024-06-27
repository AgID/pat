<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') or exit('No direct script access allowed');

if (!file_exists('checkSqlInjectionRule')) {

    /**
     * @param string|null $string $string Stringa da analizzare
     * @return string[]|null
     */
    function checkSqlInjectionRule(?string $string = null): ?array
    {
        $regex = "[\x22\x27](\s)*(or|and)(\s).*(\s)*\x3d|";
        $regex .= "cmd=ls|cmd%3Dls|";
        $regex .= "(drop|alter|create|truncate).*(index|table|database)|";
        $regex .= "insert(\s).*(into|member.|value.)|";
        $regex .= "(select|union|order).*(select|union|order)|";
        $regex .= "0x[0-9a-f][0-9a-f]|";
        $regex .= "benchmark\([0-9]+,[a-z]+|benchmark\%28+[0-9]+%2c[a-z]+|";
        $regex .= "eval\(.*\(.*|eval%28.*%28.*|";
        $regex .= "update.*set.*=|delete.*from";

        $matched = (bool)preg_match("/^.*(" . $regex . ").*/i", strtolower((string)$string));

        if ($matched === true) {

            return ['error' => 'Valore non permesso'];

        }

        return null;
    }

}
