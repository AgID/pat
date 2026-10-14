<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use RuntimeException;

/**
 * Classe Cache - Gestisce la memorizzazione temporanea di dati su file
 *
 * @package System
 */
class Cache
{
    /**
     * Tempo di scadenza predefinito in secondi
     * @var int
     */
    private $expire;

    /**
     * Prefisso dei file di cache
     * @var string
     */
    private $prefix;

    /**
     * Costruttore della classe Cache
     *
     * @param int $expire Tempo di scadenza predefinito in secondi
     * @param string $prefix Prefisso per i file di cache
     * @throws RuntimeException Se la directory di cache non esiste o non è scrivibile
     */
    public function __construct($expire = 3600, $prefix = 'c.')
    {
        // Verifica che la directory di cache esista
        if (!is_dir(CACHE_PATH)) {
            throw new RuntimeException("Directory cache non trovata: " . CACHE_PATH);
        }

        // Verifica che la directory di cache sia scrivibile
        if (!is_writable(CACHE_PATH)) {
            throw new RuntimeException("Directory cache non scrivibile: " . CACHE_PATH);
        }

        $this->expire = (int) $expire;
        $this->prefix = $prefix;
    }


    /**
     * Recupera un elemento dalla cache
     *
     * @param string $key Chiave dell'elemento da recuperare
     * @return mixed|false Il valore memorizzato o false se non trovato/scaduto
     */
    public function get($key)
    {
        // Protezione contro attacchi path traversal utilizzando hash della chiave
        $safeKey = $this->getFilenameFromKey($key);
        $file = CACHE_PATH . $safeKey;

        if (!file_exists($file)) {
            return false;
        }

        try {
            // Apertura del file in modalità lettura
            $handle = @fopen($file, 'r');
            if ($handle === false) {
                return false;
            }

            // Acquisizione del lock condiviso (lettura)
            if (!flock($handle, LOCK_SH)) {
                fclose($handle);
                return false;
            }

            // Verifica dimensione file
            $fileSize = @filesize($file);
            if ($fileSize === false || $fileSize <= 0) {
                flock($handle, LOCK_UN);
                fclose($handle);
                $this->delete($key);
                return false;
            }

            // Lettura del contenuto con buffer sicuro
            $data = stream_get_contents($handle);

            // Rilascio del lock e chiusura del file
            flock($handle, LOCK_UN);
            fclose($handle);

            if ($data === false) {
                $this->delete($key);
                return false;
            }

            // Decodifica JSON
            $decoded = json_decode($data, true);
            if ($decoded === null || !isset($decoded['expire']) || !isset($decoded['data'])) {
                $this->delete($key);
                return false;
            }

            // Verifica scadenza
            if ($decoded['expire'] < time()) {
                $this->delete($key);
                return false;
            }

            return $decoded['data'];
        } catch (\Exception $e) {
            // Gestione eccezioni durante le operazioni di I/O
            return false;
        }
    }

    /**
     * Memorizza un elemento nella cache
     *
     * @param string $key Chiave per identificare l'elemento
     * @param mixed $value Valore da memorizzare
     * @param int|null $expire Tempo di scadenza in secondi, null per usare quello predefinito
     * @return bool True se l'operazione è riuscita, altrimenti false
     */
    public function set($key, $value, $expire = null)
    {
        // Validazione e normalizzazione parametri
        $expire = ($expire !== null && is_numeric($expire)) ? (int) $expire : $this->expire;
        $safeKey = $this->getFilenameFromKey($key);
        $file = CACHE_PATH . $safeKey;

        try {
            // Apertura in modalità c+ (non tronca subito il file come 'w')
            $handle = @fopen($file, 'c+');
            if ($handle === false) {
                return false;
            }

            // Acquisizione del lock esclusivo prima di qualsiasi operazione
            if (!flock($handle, LOCK_EX)) {
                fclose($handle);
                return false;
            }

            // Preparazione dei dati da scrivere
            $data = [
                'expire' => time() + $expire,
                'data' => $value
            ];

            // Codifica JSON con gestione degli errori
            $jsonData = json_encode($data, JSON_UNESCAPED_UNICODE);
            if ($jsonData === false) {
                flock($handle, LOCK_UN);
                fclose($handle);
                return false;
            }

            // Tronca il file solo dopo aver acquisito il lock
            if (@ftruncate($handle, 0) === false) {
                flock($handle, LOCK_UN);
                fclose($handle);
                return false;
            }

            // Riposiziona il puntatore all'inizio del file
            rewind($handle);

            // Scrittura dei dati
            $bytesWritten = @fwrite($handle, $jsonData);
            if ($bytesWritten === false || $bytesWritten != strlen($jsonData)) {
                flock($handle, LOCK_UN);
                fclose($handle);
                return false;
            }

            // Flush e chiusura
            fflush($handle);
            flock($handle, LOCK_UN);
            fclose($handle);

            return true;
        } catch (\Exception $e) {
            // Gestione eccezioni durante le operazioni di I/O
            if (isset($handle) && is_resource($handle)) {
                @flock($handle, LOCK_UN);
                @fclose($handle);
            }
            return false;
        }
    }

    /**
     * Elimina un elemento dalla cache
     *
     * @param string $key Chiave dell'elemento da eliminare
     * @return bool True se l'operazione è riuscita, altrimenti false
     */
    public function delete($key)
    {
        $safeKey = $this->getFilenameFromKey($key);
        $file = CACHE_PATH . $safeKey;

        if (file_exists($file)) {
            return @unlink($file);
        }

        return true; // L'elemento non esisteva già
    }

    /**
     * Genera un nome file sicuro dalla chiave fornita
     * Previene collisioni e path traversal attack
     *
     * @param string $key Chiave originale
     * @return string Nome file sicuro
     */
    private function getFilenameFromKey($key)
    {
        // Usa sha1 per evitare collisioni di chiavi con lo stesso basename
        // e prevenire path traversal attack
        return $this->prefix . sha1($key);
    }

    /**
     * Verifica se un elemento è presente in cache e non è scaduto
     *
     * @param string $key Chiave dell'elemento da verificare
     * @return bool True se l'elemento esiste e non è scaduto, altrimenti false
     */
    public function has($key)
    {
        // Riutilizza il metodo get() per evitare duplicazione di codice
        // ma verifica solo l'esistenza senza restituire il valore
        return $this->get($key) !== false;
    }

    /**
     * Recupera tutti i file di cache corrispondenti al pattern specificato
     *
     * @param string|null $pattern Pattern di ricerca (opzionale)
     * @return array Lista di percorsi di file
     */
    public function getFiles($pattern = null)
    {
        $searchPattern = CACHE_PATH . ($pattern ?: $this->prefix . '*');
        $files = @glob($searchPattern) ?: [];
        return $files;
    }

    /**
     * Pulisce i file di cache scaduti
     *
     * @return int Numero di file di cache eliminati
     */
    public function gc()
    {
        $count = 0;
        $now = time();
        $files = $this->getFiles();

        foreach ($files as $file) {
            try {
                if (!is_file($file)) {
                    continue;
                }

                // Verifica la scadenza di ogni file di cache
                $handle = @fopen($file, 'r');
                if ($handle === false) {
                    continue;
                }

                if (!flock($handle, LOCK_SH)) {
                    fclose($handle);
                    continue;
                }

                $data = stream_get_contents($handle);
                flock($handle, LOCK_UN);
                fclose($handle);

                if ($data === false) {
                    continue;
                }

                $decoded = @json_decode($data, true);
                if (isset($decoded['expire']) && $decoded['expire'] < $now) {
                    // Elemento scaduto, eliminalo
                    if (@unlink($file)) {
                        $count++;
                    }
                }
            } catch (\Exception $e) {
                // Ignora errori durante la pulizia
                continue;
            }
        }

        return $count;
    }

    /**
     * Svuota completamente la cache
     *
     * @return bool True se l'operazione è riuscita, altrimenti false
     */
    public function clear()
    {
        $files = $this->getFiles();
        $success = true;

        foreach ($files as $file) {
            if (is_file($file) && !@unlink($file)) {
                $success = false;
            }
        }

        return $success;
    }
}