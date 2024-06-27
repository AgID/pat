<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class OS
{
    const UNKNOWN = 1;
    const WIN = 2;
    const LINUX = 3;
    const OSX = 4;

    static public function get()
    {
        switch (true) {
            case stristr(PHP_OS, 'DAR'):
                return self::OSX;
            case stristr(PHP_OS, 'WIN'):
                return self::WIN;
            case stristr(PHP_OS, 'LINUX'):
                return self::LINUX;
            default:
                return self::UNKNOWN;
        }
    }

    static public function isWin()
    {

        return (bool) self::get() === self::WIN;
    }

    static public function isOSX()
    {

        return (bool) self::get() === self::OSX;
    }

    static public function isLinux()
    {

        return  (bool) self::get() === self::LINUX;
    }
}