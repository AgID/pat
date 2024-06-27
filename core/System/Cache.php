<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');


class Cache
{
    private $expire;
    private $prefix = 'c.';


    public function __construct($expire = 3600)
    {

        if (!is_dir(CACHE_PATH)) {
            throw new Exception("Dir Cache not found");
        }

        $this->expire = $expire;
    }


    public function get($key)
    {
        $file = CACHE_PATH . $this->prefix . basename($key);

        if (file_exists($file)) {

            $handle = fopen($file, 'r');

            if (!flock($handle, LOCK_SH)) {
                return false;
            }

            $data = fread($handle, filesize($file));

            flock($handle, LOCK_UN);
            fclose($handle);

            $data = json_decode($data, true);

            if ($data['expire'] < time()) {
                $this->delete($key);
                return false;
            }

            return $data['data'];
        }

        return false;
    }

    public function set($key, $value, $expire = null)
    {
        $expire = ($expire !== null && is_numeric($expire)) ? $expire : $this->expire;
        $file = CACHE_PATH . $this->prefix . basename($key);

        $handle = fopen($file, 'w');

        if (!flock($handle, LOCK_EX)) {
            return false;
        }

        $data = [
            'expire' => time() + $expire,
            'data' => $value
        ];

        fwrite($handle, json_encode($data));
        fflush($handle);
        flock($handle, LOCK_UN);
        fclose($handle);

        return true;
    }

    public function delete($key)
    {
        $file = CACHE_PATH . $this->prefix . basename($key);

        if (file_exists($file)) {
            unlink($file);
            clearstatcache(false, $file);
        }
    }

    private function scan($path = null)
    {
        if (!empty($path)) {
            return glob(CACHE_PATH . $path);
        }

        return null;
    }

    public function has($key)
    {
        $file = CACHE_PATH . $this->prefix . basename($key);

        if (file_exists($file)) {
            $handle = fopen($file, 'r');

            if (!flock($handle, LOCK_SH)) {
                return false;
            }

            $data = fread($handle, filesize($file));

            flock($handle, LOCK_UN);
            fclose($handle);

            $data = json_decode($data, true);

            if ($data['expire'] < time()) {
                $this->delete($key);
                return false;
            }

            return true;
        }

        return false;
    }
}