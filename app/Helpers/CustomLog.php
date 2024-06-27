<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers;

use System\Log;

class CustomLog extends Log
{
    /**
     * @descriotion Customizzazione del LOG.
     * @param $notity
     * @param $log
     * @return string|null
     */
    public static function write($notity = 'INFO', $log = '', $exit = false)
    {
        self::writeLog($notity, $log);

        if ($exit) {
            die();
        }
    }
}