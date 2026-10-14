<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers;

use Maer\Config\Config;
use System\Log;

class CustomLog extends Log
{
    /**
     * @descriotion Customizzazione del LOG.
     * @param $notity
     * @param $log
     * @return string|null
     */
    public static function write($notify = 'INFO', $log = '', $exit = false)
    {
        self::writeLog($notify, $log);

        if ($exit) {
            die();
        }
    }

    public static function stats($notify = 'INFO', $log = '', $exit = false)
    {
        $config = new Config();
        $config->load(APP_PATH . 'Config/app.php');

        if (is_array($log) || is_object($log)) {
            $log = json_encode($log);
        }

        $pathFile = APP_PATH . $config->get('path_log') . DIRECTORY_SEPARATOR . '[' . $notify . ']' . '.log';

        file_put_contents($pathFile, $log);
    }
}