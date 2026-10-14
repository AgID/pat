<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Helpers\FileSystem\Dir;
use \Maer\Config\Config;

class Log
{

    /**
     * @param mixed|null  $log  Contenuto del log
     * @param string|null $path Cartella specifica in cui inserire i log all'interno del percorso di default
     * @return void
     * @throws Exception
     */
    public static function info(mixed $log = null, ?string $path = ''): void
    {
        self::writeLog('INFO', $log, $path);
    }

    /**
     * @param mixed|null  $log  Contenuto del log
     * @param string|null $path Cartella specifica in cui inserire i log all'interno del percorso di default
     * @return void
     * @throws Exception
     */
    public static function warning(mixed $log = null, ?string $path = ''): void
    {
        self::writeLog('WARNING', $log, $path);
    }

    /**
     * @param mixed|null  $log  Contenuto del log
     * @param string|null $path Cartella specifica in cui inserire i log all'interno del percorso di default
     * @return void
     * @throws Exception
     */
    public static function danger(mixed $log = null, ?string $path = ''): void
    {
        self::writeLog('DANGER', $log, $path);
    }


    public static function print($log = null, $printR = true, string $file = '', bool $printSeparator = true, $avoidTimeInfo = false)
    {
        //se è true esegue il print_r
        if($printR){
            self::printLog('PrintR', $log, $printR, $file, $printSeparator, $avoidTimeInfo);
        }else{
            self::printLog('varExport', $log, $printR, $file, $printSeparator, $avoidTimeInfo);
        }
    }


    public static function printAll(...$arguments): void
    {
        self::printLog('PrintAll', $arguments, true, null, false);
    }

    /**
     * Scrive i log all'interno dei file nel percorso specificato o in quello di default
     * @param string      $notify Tipo di log (info, danger, warning)
     * @param mixed       $log    Contenuto del log
     * @param string|null $path   Specifica cartella in cui salvare i logs
     * @return void
     * @throws Exception
     */
    protected static function writeLog(string $notify = 'info', mixed $log = '', ?string $path = ''): void
    {
        $config = new Config();
        $config->load(APP_PATH . 'Config/app.php');

        if (is_array($log) || is_object($log)) {

            $log = json_encode($log);

        }

        $data = date('Y-m-d');
        $hours = date('H:i:s');

        if(!empty($path)) {

            if (!Dir::exists(APP_PATH . $config->get('path_log') . DIRECTORY_SEPARATOR . $path)) {
                Dir::create(APP_PATH . $config->get('path_log') . DIRECTORY_SEPARATOR . $path);
            }

            $pathFile = APP_PATH . $config->get('path_log') . DIRECTORY_SEPARATOR . $path . DIRECTORY_SEPARATOR . '[' . $notify . '] - ' . $data . '.log';
        } else {
            $pathFile = APP_PATH . $config->get('path_log') . DIRECTORY_SEPARATOR . '[' . $notify . '] - ' . $data . '.log';
        }

        $log = '[' . strtoupper($notify) . ' - ' . $data . ' ' . $hours . ']' . ': ' . $log;
        file_put_contents($pathFile, $log . PHP_EOL, FILE_APPEND);
    }

    protected static function printLog($notify, $log, $printR, $file ,$printSeparator = true, $avoidTimeInfo = false)
    {
        $config = new Config();
        $config->load(APP_PATH . 'Config/app.php');

        $data = date('Y-m-d');
        $hours = date('H:i:s');
        $pathFile = APP_PATH . $config->get('path_log') . DIRECTORY_SEPARATOR . '[' . $notify . '] - ' . $data . '.log';

        if(!empty($file)){
            $pathFile = APP_PATH . $config->get('path_log') . DIRECTORY_SEPARATOR . $file;
        }

        if(!$avoidTimeInfo){
            $info = '[' . strtoupper($notify) . ' - ' . $data . ' ' . $hours . ']' . ': ';
            file_put_contents($pathFile, $info . PHP_EOL, FILE_APPEND);
        }
        if($printR){
            file_put_contents($pathFile, print_r($log, true) . PHP_EOL, FILE_APPEND);
        }else{
            file_put_contents($pathFile, var_export($log, true) . PHP_EOL, FILE_APPEND);
        }

        if($printSeparator){
            $separator = str_repeat('----------', 50);
            file_put_contents($pathFile, PHP_EOL. $separator . PHP_EOL, FILE_APPEND);
            file_put_contents($pathFile, $separator, FILE_APPEND);
        }

    }
}
