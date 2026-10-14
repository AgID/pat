<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers\Utility;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class FileUpload
{
    /**
     * Restituisce il limite di upload imposto dalla configurazione PHP.
     * Viene considerato il valore piu restrittivo tra post_max_size e
     * upload_max_filesize, così da non mostrare limiti non acquisibili.
     *
     * @return int
     */
    public static function maxSize(): int
    {
        static $serverMaxSize = null;

        if ($serverMaxSize === null) {
            $serverMaxSize = -1;
            $postMaxSize = self::parseSize((string)ini_get('post_max_size'));

            if ($postMaxSize > 0) {
                $serverMaxSize = $postMaxSize;
            }

            $uploadMaxSize = self::parseSize((string)ini_get('upload_max_filesize'));

            if ($uploadMaxSize > 0 && ($serverMaxSize <= 0 || $uploadMaxSize < $serverMaxSize)) {
                $serverMaxSize = $uploadMaxSize;
            }
        }

        return $serverMaxSize;
    }

    /**
     * Restituisce il limite di upload effettivo mostrabile nei form.
     * Il limite PHP dell'host viene confrontato con quello applicativo,
     * così l'utente non riceve un valore superiore a quello acquisibile.
     *
     * @param int|null $applicationMaxSize Limite applicativo espresso in byte
     * @return string
     */
    public static function maxSizeLabel(?int $applicationMaxSize = null): string
    {
        $serverMaxSize = self::maxSize();

        if ($serverMaxSize > 0 && $applicationMaxSize !== null && $applicationMaxSize > 0) {
            $effectiveMaxSize = min($serverMaxSize, $applicationMaxSize);
        } elseif ($serverMaxSize > 0) {
            $effectiveMaxSize = $serverMaxSize;
        } else {
            $effectiveMaxSize = $applicationMaxSize;
        }

        $label = $effectiveMaxSize !== null && $effectiveMaxSize > 0
            ? self::formatBytes($effectiveMaxSize)
            : 'Nessun limite configurato';

        return $label;
    }

    /**
     * Converte un valore PHP come 2M o 1G nella corrispondente quantità di byte.
     *
     * @param string $size Dimensione espressa nel formato accettato da PHP
     * @return int
     */
    public static function parseSize(string $size = ''): int
    {
        $unit = preg_replace('/[^bkmgtpezy]/i', '', $size);
        $numericSize = preg_replace('/[^0-9\.]/', '', $size);

        if ($unit !== '') {
            return (int)round((float)$numericSize * pow(1024, stripos('bkmgtpezy', $unit[0])));
        }

        return (int)round((float)$numericSize);
    }

    /**
     * Formatta una dimensione in byte con l'unita più leggibile.
     *
     * @param int $size Dimensione espressa in byte
     * @param int $precision Numero di decimali da visualizzare
     * @return string
     */
    public static function formatBytes(int $size, int $precision = 2): string
    {
        if ($size <= 0) {
            return '0 B';
        }

        $base = log($size, 1024);
        $suffixes = ['', 'KB', 'MB', 'GB', 'TB'];
        $suffixIndex = min((int)floor($base), count($suffixes) - 1);

        return round(pow(1024, $base - floor($base)), $precision) . ' ' . $suffixes[$suffixIndex];
    }
}
