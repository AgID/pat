<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\FileSystem;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class Dir
{

    protected function __construct()
    {
    }

    /**
     * Funzione che crea una nuova directory con i permessi passati nel parametro
     *
     * @param $dir - path della directory da creare
     * @param int $chmod - permessi da settare sulla cartella
     * @return bool
     */
    public static function create($dir, int $chmod = 0775): bool
    {
        $dir = (string)$dir;
        return Dir::exists($dir) || @mkdir($dir, $chmod, true);
    }

    /**
     * Funzione che controlla se la cartella specificata nel parametro esiste o meno
     *
     * @param $dir - path della directory da controllare
     * @return bool
     */
    public static function exists($dir): bool
    {
        $dir = (string)$dir;
        if (file_exists($dir) && is_dir($dir)) return true;
        return false;
    }

    /**
     * Funzione che controlla i permessi della cartella specificata nel parametro
     * @param $dir - path della directory da controllare
     * @return false|string
     */
    public static function checkPerm($dir): bool|string
    {
        $dir = (string)$dir;
        clearstatcache();
        return substr(sprintf('%o', fileperms($dir)), -4);
    }

    /**
     * Funzione che elimina la cartella passata nel parametro
     *
     * @param $dir - path della directory da eliminare
     * @return void
     */
    public static function delete($dir): void
    {
        $dir = (string)$dir;
        if (is_dir($dir)) {
            $ob = scandir($dir);
            foreach ($ob as $o) {
                if ($o != '.' && $o != '..') {
                    if (filetype($dir . '/' . $o) == 'dir') Dir::delete($dir . '/' . $o); else unlink($dir . '/' . $o);
                }
            }
        }
        reset($ob);
        rmdir($dir);
    }

    /**
     * Funzione che effettua una scansione sulla directory
     *
     * @param $dir - path della directory da scansionare
     * @return array|void
     */
    public static function scan($dir)
    {
        $dir = (string)$dir;
        if (is_dir($dir) && $dh = opendir($dir)) {
            $f = array();
            while ($fn = readdir($dh)) {
                if ($fn != '.' && $fn != '..' && is_dir($dir . DIRECTORY_SEPARATOR . $fn)) $f[] = $fn;
            }
            return $f;
        }
    }

    /**
     * @param $path -
     * @return bool
     */
    public static function writable($path): bool
    {
        $path = (string)$path;
        $file = tempnam($path, 'writable');
        if ($file !== false) {
            File::delete($file);
            return true;
        }

        return false;
    }

    /**
     * Funzione che ritorna la dimensione della cartella specificata nel parametro
     *
     * @param $path - path della cartella
     * @return false|int
     */
    public static function size($path): bool|int
    {
        $path = (string)$path;

        $totalSize = 0;
        $files = scandir($path);
        $cleanPath = rtrim($path, '/') . '/';

        foreach ($files as $t) {
            if ($t <> "." && $t <> "..") {
                $currentFile = $cleanPath . $t;
                if (is_dir($currentFile)) {
                    $totalSize += Dir::size($currentFile);
                } else {
                    $totalSize += filesize($currentFile);
                }
            }
        }

        return $totalSize;
    }

    /**
     * Funzione che copia una cartella
     *
     * @param $src - cartella da copiare
     * @param $dst - path di destinazione in cui copiare la cartella
     * @return void
     */
    public static function copy($src, $dst): void
    {
        $dir = opendir($src);
        @mkdir($dst);
        while (false !== ($file = readdir($dir))) {
            if (($file != '.') && ($file != '..')) {
                if (is_dir($src . '/' . $file)) {
                    self::copy($src . '/' . $file, $dst . '/' . $file);
                } else {
                    copy($src . '/' . $file, $dst . '/' . $file);
                }
            }
        }
        closedir($dir);
    }
}