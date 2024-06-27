<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

//// Comprimi un file
//$source = '/path/to/file.txt';
//$destination = '/path/to/file.zip';
//if (Zip::compress($source, $destination)) {
//    echo 'File compresso con successo';
//} else {
//    echo 'Errore durante la compressione del file';
//}
//
//// Decomprimi un file
//$source = '/path/to/file.zip';
//$destination = '/path/to/extracted';
//if (Zip::extract($source, $destination)) {
//    echo 'File decompresso con successo';
//} else {
//    echo 'Errore durante la decompressione del file';
//}
class Zip
{
    /**
     * Comprime un file o una directory in un archivio zip
     *
     * @param string $source Percorso del file o della directory da comprimere
     * @param string $destination Percorso del file zip di destinazione
     *
     * @return bool True se l'operazione ha avuto successo, false altrimenti
     */
    public static function compress(string $source, string $destination): bool
    {
        // Verifica se l'estensione zip è caricata e se il file sorgente esiste
        if (!extension_loaded('zip') || !file_exists($source)) {
            return false;
        }

        // Apre l'oggetto ZipArchive
        $zip = new \ZipArchive();
        if (!$zip->open($destination, ZIPARCHIVE::CREATE)) {
            return false;
        }

        // Sostituisce i backslash con slash nel percorso del file sorgente
        $source = str_replace('\\', '/', realpath($source));
        // Se la sorgente è una directory, zippa i file all'interno
        if (is_dir($source) === true) {
            $files = new \RecursiveIteratorIterator(new \RecursiveDirectoryIterator($source), \RecursiveIteratorIterator::SELF_FIRST);
            foreach ($files as $file) {
                $file = str_replace('\\', '/', $file);
                // Ignora le cartelle "." e ".."
                if (in_array(substr($file, strrpos($file, '/') + 1), array('.', '..')))
                    continue;
                $file = realpath($file);
                if (is_dir($file) === true) {
                    $zip->addEmptyDir(str_replace($source . '/', '', $file . '/'));
                } else if (is_file($file) === true) {
                    $zip->addFromString(str_replace($source . '/', '', $file), file_get_contents($file));
                }
            }
        } else if (is_file($source) === true) {
            // Se la sorgente è un file, lo zippa
            $zip->addFromString(basename($source), file_get_contents($source));
        }
        return $zip->close();
    }

    /**
     * Decomprime un file zip in una directory
     *
     * @param string $source Percorso del file zip da decomprimere
     * @param string $destination Percorso della directory di destinazione
     *
     * @return bool True se l'operazione ha avuto successo, false altrimenti
     */
    public static function extract(string $source, string $destination): bool
    {
        $zip = new \ZipArchive();
        if ($zip->open($source) === true) {
            $zip->extractTo($destination);
            $zip->close();
            return true;
        }
        return false;
    }
}