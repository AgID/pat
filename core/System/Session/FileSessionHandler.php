<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System\Session;

defined('_FRAMEWORK_') or exit('No direct script access allowed');


use SessionHandlerInterface;

class FileSessionHandler implements SessionHandlerInterface
{
    protected string $savePath;

    public function __construct(string $savePath)
    {
        $this->savePath = $savePath;
    }

    public function open(string $save_path, string $name): bool
    {
        if (!is_dir($this->savePath)) {
            mkdir($this->savePath, 0700, true);
        }

        return true;
    }

    public function close(): bool
    {
        // Gestione non necessaria
        return true;
    }

    public function read(string $session_id): string
    {
        $file = $this->getFileName($session_id);

        if (file_exists($file)) {
            // Legge il contenuto del file e deserializza i dati
            $data = file_get_contents($file);
            return $data !== false ? unserialize($data) : '';
        }

        return '';
    }

    public function write(string $session_id, string $session_data): bool
    {
        $file = $this->getFileName($session_id);
        // Serializza i dati prima di scriverli nel file
        $serializedData = serialize($session_data);
        return file_put_contents($file, $serializedData) !== false;
    }

    public function destroy(string $session_id): bool
    {
        $file = $this->getFileName($session_id);

        if (file_exists($file)) {
            unlink($file);
        }

        return true;
    }

    public function gc(int $max_lifetime): int|false
    {
        foreach (glob("{$this->savePath}/sess_*") as $file) {
            if (filemtime($file) + $max_lifetime < time() && file_exists($file)) {
                unlink($file);
            }
        }

        return true;
    }

    private function getFileName(string $session_id): string
    {
        return $this->savePath . DIRECTORY_SEPARATOR . 'sess_' . $session_id;
    }
}