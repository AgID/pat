<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\FileSystem;

use RecursiveDirectoryIterator;
use RecursiveIteratorIterator;
use RuntimeException;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class File
{
    protected function __construct()
    {
    }

    /**
     * @param string $file_name
     * @return mixed|null
     */
    public static function isMultiUpload(string $file_name = 'userfile'): mixed
    {
        $count = NULL;
        if (!empty($_FILES[$file_name]["name"])) {
            $count = $_FILES[$file_name]["name"];
        }

        return $count;
    }

    /**
     * Funzione che controlla se il file esiste o meno
     *
     * @param $filename - nomo del file di cui controllare l'esistenza
     * @return bool
     */
    public static function exists($filename): bool
    {
        $filename = (string)$filename;
        return (file_exists($filename) && is_file($filename));
    }

    /**
     * Funzione che elimina un file
     *
     * @param $filename - nome del file da eliminare
     * @return bool|void
     */
    public static function delete($filename)
    {
        if (is_array($filename)) {
            foreach ($filename as $file) {
                @unlink((string)$file);
            }
        } else {
            return @unlink((string)$filename);
        }
    }

    /**
     * Funzione che rinomina un file
     *
     * @param $from - nome iniziale del file
     * @param $to - nuovo nome da assegnare al file
     * @return bool
     */
    public static function rename($from, $to): bool
    {
        $from = (string)$from;
        $to = (string)$to;
        if (!File::exists($to)) return rename($from, $to);

        return false;
    }

    /**
     * Funzione che copia un file
     *
     * @param $from - path da dove prendere il file da copiare
     * @param $to - path in cui copiare il file
     * @return bool
     */
    public static function copy($from, $to): bool
    {
        $from = (string)$from;
        $to = (string)$to;
        if (!File::exists($from) || File::exists($to)) return false;
        return copy($from, $to);
    }

    /**
     * Funzione che ritorna l'estensione del file
     *
     * @param $filename - nome del file
     * @return false|string
     */
    public static function ext($filename): bool|string
    {
        $filename = (string)$filename;
        return substr(strrchr($filename, '.'), 1);
    }

    /**
     * @param $filename
     * @return string
     */
    public static function name($filename): string
    {
        $filename = (string)$filename;
        return basename($filename, '.' . File::ext($filename));
    }

    /**
     * @param $folder
     * @param $type
     * @return array|false
     */
    public static function scan($folder, $type = null): bool|array
    {
        $data = array();
        if (is_dir($folder)) {
            $iterator = new RecursiveDirectoryIterator($folder);
            foreach (new RecursiveIteratorIterator($iterator) as $file) {
                if ($type !== null) {
                    if (is_array($type)) {
                        $fileExt = substr(strrchr($file->getFilename(), '.'), 1);
                        if (in_array($fileExt, $type)) {
                            if (strpos($file->getFilename(), $fileExt, 1)) {
                                $data[] = $file->getFilename();
                            }
                        }
                    } else {
                        if (strpos($file->getFilename(), $type, 1)) {
                            $data[] = $file->getFilename();
                        }
                    }
                } else {
                    if ($file->getFilename() !== '.' && $file->getFilename() !== '..') $data[] = $file->getFilename();
                }
            }
            return $data;
        } else {
            return false;
        }
    }

    /**
     * @param $filename
     * @return false|string|void
     */
    public static function getContent($filename)
    {
        $filename = (string)$filename;

        if (File::exists($filename)) {
            return file_get_contents($filename);
        }
    }

    /**
     * @param $filename
     * @param $content
     * @param bool $createFile
     * @param bool $append
     * @param int $chmod
     * @return bool
     */
    public static function setContent($filename, $content, bool $createFile = true, bool $append = false, int $chmod = 0666): bool
    {
        $filename = (string)$filename;
        $content = (string)$content;

        if (!File::exists($filename)) {
            return false;
        }

        Dir::create(dirname($filename));
        $handler = ($append)
            ? @fopen($filename, 'a')
            : @fopen($filename, 'w');

        if ($handler === false) {
            return false;
        }

        $level = error_reporting();
        error_reporting(0);
        $write = fwrite($handler, $content);

        if ($write === false) {
            return false;
        }

        fclose($handler);
        chmod($filename, $chmod);
        error_reporting($level);
        return true;
    }

    public static function writeFile($path, $data, $mode = 'wb'): bool
    {
        if (!$fp = @fopen($path, $mode)) {
            return FALSE;
        }

        flock($fp, LOCK_EX);

        for ($result = $written = 0, $length = strlen((string) $data); $written < $length; $written += $result) {
            if (($result = fwrite($fp, substr((string) $data, $written))) === FALSE) {
                break;
            }
        }

        flock($fp, LOCK_UN);
        fclose($fp);

        return is_int($result);
    }

    public static function deleteFiles($path, $delDir = FALSE, $htdocs = FALSE, $level = 0): bool
    {

        $path = rtrim($path, '/\\');

        if (!$currentDir = @opendir($path)) {
            return FALSE;
        }

        while (FALSE !== ($filename = @readdir($currentDir))) {
            if ($filename !== '.' && $filename !== '..') {
                $filepath = $path . DIRECTORY_SEPARATOR . $filename;

                if (is_dir($filepath) && $filename[0] !== '.' && !is_link($filepath)) {
                    self::deleteFiles($filepath, $delDir, $htdocs, $level + 1);
                } elseif ($htdocs !== true or !preg_match('/^(\.htaccess|index\.(html|htm|php)|web\.config)$/i', $filename)) {
                    @unlink($filepath);
                }
            }
        }

        closedir($currentDir);

        return !($delDir === true && $level > 0) || @rmdir($path);
    }

    /**
     * Funzione che ritorna la data di ultima modifica del file
     *
     * @param $filename - nome del file
     * @return false|int
     */
    public static function lastChange($filename): bool|int
    {
        $filename = (string)$filename;
        if (File::exists($filename)) {
            return filemtime($filename);
        }
        return false;
    }


    /**
     * Funzione che ritorna l'ultimo accesso al file
     *
     * @param $filename - nome del file
     * @return false|int
     */
    public static function lastAccess($filename): bool|int
    {
        $filename = (string)$filename;
        if (File::exists($filename)) {
            return fileatime($filename);
        }

        return false;
    }


    /**
     * @param $file
     * @param bool $guess
     * @return false|mixed|string
     * @noinspection PhpComposerExtensionStubsInspection
     */
    public static function mime($file, bool $guess = true): mixed
    {
        $file = (string)$file;
        if (function_exists('finfo_open')) {
            $info = finfo_open(FILEINFO_MIME_TYPE);
            $mime = finfo_file($info, $file);
            finfo_close($info);
            return $mime;
        } else {
            if ($guess === true) {
                $mimeTypes = getMimes();
                $extension = pathinfo($file, PATHINFO_EXTENSION);
                return $mimeTypes[$extension] ?? false;
            } else {
                return false;
            }
        }

    }

    /**
     * @param $file
     * @param $contentType
     * @param $filename
     * @param int $kbps
     * @return void
     */
    public static function download($file, $contentType = null, $filename = null, int $kbps = 0): void
    {
        $file = (string)$file;
        $contentType = ($contentType === null) ? null : (string)$contentType;
        $filename = ($filename === null) ? null : (string)$filename;

        if (file_exists($file) === false || is_readable($file) === false) {
            throw new RuntimeException(vsprintf("%s(): Failed to open stream.", array(__METHOD__)));
        }

        while (ob_get_level() > 0) ob_end_clean();
        if ($contentType === null) $contentType = File::mime($file);
        if ($filename === null) $filename = basename($file);

        header('Content-type: ' . $contentType);
        header('Content-Disposition: attachment; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($file));
        @set_time_limit(0);

        if ($kbps === 0) {
            readfile($file);
        } else {
            $handle = fopen($file, 'r');
            while (!feof($handle) && !connection_aborted()) {
                $s = microtime(true);
                echo fread($handle, round($kbps * 1024));
                if (($wait = 1e6 - (microtime(true) - $s)) > 0) usleep($wait);

            }
            fclose($handle);
        }
        exit();
    }


    /**
     * @param $file
     * @param $contentType
     * @param $filename
     * @return void
     */
    public static function display($file, $contentType = null, $filename = null): void
    {
        $file = (string)$file;
        $contentType = ($contentType === null) ? null : (string)$contentType;
        $filename = ($filename === null) ? null : (string)$filename;

        if (file_exists($file) === false || is_readable($file) === false) {
            throw new RuntimeException(vsprintf("%s(): Failed to open stream.", array(__METHOD__)));
        }

        while (ob_get_level() > 0) ob_end_clean();

        if ($contentType === null) $contentType = File::mime($file);
        if ($filename === null) $filename = basename($file);

        header('Content-type: ' . $contentType);
        header('Content-Disposition: inline; filename="' . $filename . '"');
        header('Content-Length: ' . filesize($file));
        readfile($file);
        exit();
    }

    /**
     * @param $file
     * @return bool|void
     */
    public static function writable($file)
    {

        $file = (string)$file;
        if (!file_exists($file)) throw new RuntimeException(vsprintf("%s(): The file '{$file}' doesn't exist", array(__METHOD__)));
        $perms = fileperms($file);
        if (is_writable($file) || ($perms & 0x0080) || ($perms & 0x0010) || ($perms & 0x0002)) return true;
    }

    /**
     * @return array|mixed
     */
    public static function getMimes(): mixed
    {
        return getMimes();
    }

}
