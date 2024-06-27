<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use \Maer\Config\Config;

class Log
{

    public static function info($log = null)
    {
        self::writeLog('INFO', $log);
    }

    public static function warning($log = null)
    {
        self::writeLog('WARNING', $log);
    }

    public static function danger($log = null)
    {
        self::writeLog('DANGER', $log);
    }

    public static function print($log = null, $printR = true)
    {
        //se è true esegue il print_r
        if($printR){
            self::printLog('PrintR', $log, $printR);
        }else{
            self::printLog('varExport', $log, $printR);
        }
    }

    protected static function writeLog($notify, $log)
    {
        $config = new Config();
        $config->load(APP_PATH . 'Config/app.php');

        if (is_array($log) || is_object($log)) {

            $log = json_encode($log);

        }

        $data = date('Y-m-d');
        $hours = date('H:i:s');
        $pathFile = APP_PATH . $config->get('path_log') . DIRECTORY_SEPARATOR . '[' . $notify . '] - ' . $data . '.php';
        $log = '[' . strtoupper($notify) . ' - ' . $data . ' ' . $hours . ']' . ': ' . $log;
        file_put_contents($pathFile, $log . PHP_EOL, FILE_APPEND);
    }

    protected static function printLog($notify, $log, $printR)
    {
        $config = new Config();
        $config->load(APP_PATH . 'Config/app.php');

        $data = date('Y-m-d');
        $hours = date('H:i:s');
        $pathFile = APP_PATH . $config->get('path_log') . DIRECTORY_SEPARATOR . '[' . $notify . '] - ' . $data . '.php';
        $info = '[' . strtoupper($notify) . ' - ' . $data . ' ' . $hours . ']' . ': ';
        file_put_contents($pathFile, $info . PHP_EOL, FILE_APPEND);
        if($printR){
            file_put_contents($pathFile, print_r($log, true) . PHP_EOL, FILE_APPEND);

        }else{
            file_put_contents($pathFile, var_export($log, true) . PHP_EOL, FILE_APPEND);
        }

        $separator = str_repeat('----------', 50);
        file_put_contents($pathFile, PHP_EOL. $separator . PHP_EOL, FILE_APPEND);
        file_put_contents($pathFile, $separator, FILE_APPEND);
    }

}
