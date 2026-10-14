<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System\Session;

use SessionHandlerInterface;
use System\Container;
use System\Input;
use System\Agent;
use System\Security;
use Illuminate\Database\QueryException;
use System\Cookie;
use System\Config;

class DatabaseSessionHandler implements SessionHandlerInterface
{
    protected string $table = 'sessions';
    private $db;
    private static $instance = null;

    private $config = null;

    private $lastActivity;

    private $cryptIpUserAgent = false;

    private $ip;
    private $userAgent;

    public function __construct()
    {
        $container = Container::getInstance();
        $this->config = new Config();
        $pathConfig = APP_PATH . DIRECTORY_SEPARATOR . 'Config' . DIRECTORY_SEPARATOR . 'session.php';
        $this->config->load($pathConfig);
        $this->cryptIpUserAgent = $this->config->get('encryption_ip_ua_session_db');
        $this->db = $container->make('db');
        $this->lastActivity = time();
        $this->ip = $this->getIpAddress();
        $this->userAgent = $this->getUserAgent();
    }

    public function open(string $save_path, string $session_id): bool
    {
        return true;
    }

    public function close(): bool
    {
        return true;
    }

    public function read(string $session_id): string|false
    {
        $this->db->beginTransaction();
        try {
            $session = $this->db->table($this->table)
                ->where('id', $session_id)
                ->first();

            if ($session && $this->isValidSession($session)) {
                // Aggiorna last_activity per mantenere attiva la sessione
                $this->db->table($this->table)
                    ->where('id', $session_id)
                    ->update(['last_activity' => time()]);

                // Deserializza i dati della sessione
                $this->db->commit();
                return $session->payload ? unserialize($session->payload) : '';
            } else {
                $this->db->table($this->table)
                    ->where('id', $session_id)
                    ->delete();
            }

            $this->db->commit();
            return '';
        } catch (\Exception $e) {
            $this->db->rollback();
            _log('Session', 'Session read error: ' . $e->getMessage());
            error_log('Session read error: ' . $e->getMessage());
            return '';
        }
    }

    public function write(string $sessionId, string $data): bool
    {
        try {
            $userId = $_SESSION['user_id'] ?? null;
            $ipAddress = $this->ip;
            $userAgent = $this->userAgent;
            $currentTime = time();

            // Sanitizzazione e validazione dei dati
            $userId = $userId !== null ? filter_var($userId, FILTER_SANITIZE_NUMBER_INT) : null;

            // Valida l'indirizzo IP
            if (!$this->cryptIpUserAgent && $ipAddress !== null && !filter_var($ipAddress, FILTER_VALIDATE_IP)) {
                $ipAddress = null;
            }

            // Rimuove i caratteri di controllo dallo User Agent e limita la lunghezza
            if ($userAgent !== null) {
                // Rimuove caratteri di controllo
                $userAgent = preg_replace('/[[:cntrl:]]/', '', $userAgent);
                // Limita la lunghezza a 500 caratteri
                $userAgent = substr($userAgent, 0, 500);
            }

            $existingSession = $this->db->table($this->table)
                ->where('id', $sessionId)
                ->first();

            // Serializza i dati prima di salvarli
            $serializedData = serialize($data);

            if ($existingSession) {
                // Aggiorna la sessione esistente
                $this->db->table($this->table)
                    ->where('id', $sessionId)
                    ->update([
                        'user_id' => $userId,
                        'ip_address' => $ipAddress,
                        'user_agent' => $userAgent,
                        'payload' => $serializedData, // Salva i dati serializzati
                        'fp' => $this->fingerPrint(),
                        'last_activity' => $currentTime,
                    ]);
            } else {
                // Crea una nuova sessione
                $this->db->table($this->table)
                    ->insert([
                        'id' => $sessionId,
                        'user_id' => $userId,
                        'ip_address' => $ipAddress,
                        'user_agent' => $userAgent,
                        'payload' => $serializedData, // Salva i dati serializzati
                        'fp' => $this->fingerPrint(),
                        'last_activity' => $currentTime,
                    ]);
            }

            return true;
        } catch (\Exception $e) {
            _log('Session', 'Session write error: ' . $e->getMessage());
            error_log('Session write error: ' . $e->getMessage());
            return false;
        }
    }

    public function destroy(string $sessionId): bool
    {
        try {

            $deletedRows = $this->db->table($this->table)
                ->where('id', $sessionId)
                ->delete();

            return true;
        } catch (\Exception $e) {
            _log('Session', 'Session destroy error: ' . $e->getMessage());
            error_log('Session destroy error: ' . $e->getMessage());
            return false;
        }
    }

    public function gc(int $max_lifetime): int|false
    {

        $this->db->beginTransaction();

        try {
            $timestamp = time() - $max_lifetime;
            $deleted = $this->db->table($this->table)
                ->where('last_activity', '<', $timestamp)
                ->limit(100)
                ->delete();

            if ($deleted === false) {
                return false;
            }

            $this->db->commit();

            // Restituisce il numero di sessioni eliminate
            return $deleted;
        } catch (QueryException $e) {
            $this->db->rollback();
            // Gestisci l'errore di query
            return false;
        } catch (\Exception $e) {
            _log('Session', 'Session garbage collection error: ' . $e->getMessage());
            error_log('Session garbage collection error: ' . $e->getMessage());
            return false;
        }
    }

    private function isValidSession($session): bool
    {
        $currentUserAgent = $this->userAgent;
        // $currentIpAddress = $this->ip;

        $isValid = $session->user_agent === $currentUserAgent
            //&& $session->ip_address === $currentIpAddress
            && $session->last_activity >= time() - (int) ini_get('session.gc_maxlifetime');

        if (!$isValid) {
            $this->db->table($this->table)
                ->where('id', $session->id)
                ->delete();

            _log('Session', "Invalid session detected for ID: {$session->id}");
            error_log("Invalid session detected for ID: {$session->id}");
        }

        return $isValid;
    }
    public function cleanup($lifetime = null, $sessionName = null): void
    {
        $expirationTime = time() - $lifetime;

        $this->db->beginTransaction();

        try {

            if (!empty($_COOKIE[session_name()])) {
                $deleteWithFp = $this->db->table('sessions')->where('fp', '=', $this->fingerPrint())
                    ->where('id', '!=', (new Security())->sanitize_filename($_COOKIE[session_name()]))
                    ->limit(100)
                    ->delete();

                _log('Session', 'Cleanup Session fp: ' . $deleteWithFp);
            }


            // Elimino record che non hanno l'user agent impostato
            $deleteWithUA = $this->db->table('sessions')
                ->whereNull('user_agent')
                ->limit(100)
                ->delete();

            _log('Session', 'Cleanup Session user_agent: ' . $deleteWithUA);

            $this->db->commit();

        } catch (QueryException $e) {
            $this->db->rollback();
        } catch (\Exception $e) {
            _log('Session', 'Cleanup Session DB error: ' . $e->getMessage());
            error_log('Cleanup Session DB error: ' . $e->getMessage());
        }

    }

    private function fingerPrint(): string
    {
        $detect = true;

        $agent = new Agent();
        $lang = $agent->languages();
        $lang = is_array($lang) ? implode('_', $lang) : (string) $lang;
        $platform = $agent->platform();
        $browser = $agent->browser();
        $userAgent = Input::userAgent();
        $ip = Input::ipAddress();
        $userAgent = $browser . '|' . $platform . '|' . $lang . '|' . $ip . '|' . $userAgent;
        // if ($userAgent !== null && strlen($userAgent) <= 1) {
        // $userAgent = $browser . '|' . $platform . '|' . $lang . '|' . $ip . '|' . $userAgent;
        // } else {
        //     $detect = false;
        //     _log('no_ua', Input::ipAddress());
        //     $userAgent = $this->lastActivity;
        // }

        $md5FringerPrint = md5(strlen($userAgent) . $userAgent);

        // if (!$detect) {
        //     Cookie::set('_fp', $md5FringerPrint);
        // }
        return $md5FringerPrint;
    }


    private function encrypt(string $data): bool|string
    {
        $ciphering = _env('CIPHER');
        openssl_cipher_iv_length($ciphering);
        $options = 0;
        $encryptionIv = _env('ENCRYPTION_IV');
        $encryptionKey = _env('ENCRYPTION_KEY');
        return openssl_encrypt($data, $ciphering, $encryptionKey, $options, $encryptionIv);
    }

    /**
     * @param string $data La stringa da decifrare
     * @return false|string
     */
    private function decrypt(string $data): bool|string
    {
        $ciphering = _env('CIPHER');
        openssl_cipher_iv_length($ciphering);
        $options = 0;
        $encryptionIv = _env('ENCRYPTION_IV');
        $encryptionKey = _env('ENCRYPTION_KEY');
        return openssl_decrypt($data, $ciphering, $encryptionKey, $options, $encryptionIv);
    }

    private function getIpAddress(): string|null
    {

        $ip = Input::ipAddress() ?? null;

        if ($ip !== null) {
            return $this->cryptIpUserAgent
                ? $this->encrypt($ip)
                : $ip;
        }

        return null;
    }

    private function getUserAgent(): string|null
    {
        $userAgent = Input::userAgent() ?? null;

        if ($userAgent !== null) {
            return $this->cryptIpUserAgent
                ? $this->encrypt($userAgent)
                : $userAgent;
        }

        return null;
    }

}
