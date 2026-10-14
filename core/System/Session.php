<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use System\Input;

class Session
{
    private array $config;
    private bool $sessionStarted = false;

    private $pathConfig = 'session';

    private $handler;

    private $lifetime;

    private $sessionName = null;
    private $cookiePath = null;

    public function __construct(array $config = [])
    {
        (new \Maer\Config\Config())->load(APP_PATH . 'Config/session.php');

        $this->lifetime = config('lifetime', 1440, $this->pathConfig);

        // trace(patOsInstituteInfo(), true);
        $defaultConfig = [
            'save_handler' => config('save_handler', 'file', $this->pathConfig),
            'save_path' => config('save_path', '', $this->pathConfig),
            'lifetime' => $this->lifetime,
            'secure' => config('secure', false, $this->pathConfig),
            'httponly' => config('httponly', true, $this->pathConfig),
            'samesite' => config('samesite', 'Lax', $this->pathConfig),
            'regenerate_interval' => config('regenerate_interval', 300, $this->pathConfig),
            'encryption_key' => config('encryption_key', 'key', $this->pathConfig),
            'name' => config('name', 'PHPSESSION', $this->pathConfig),
            'match_ip' => config('match_ip', false, $this->pathConfig),
        ];

        $this->config = array_merge($defaultConfig, $config);

        $this->initializeSession();
    }

    private function initializeSession(): void
    {

        if (session_status() === PHP_SESSION_NONE) {

            // Configurazione del gestore di sessione
            // $this->sessionName = defined('_APP_S_ID')
            //     ? $this->config['name'] . '_' . _APP_S_ID
            //     : $this->config['name'];


            // $this->cookiePath = defined('_APP_SEGMNENT')
            //     ? _APP_SEGMNENT
            //     : '/';

            $this->sessionName = 'isWeb';
            $this->cookiePath = '/';

            session_name($this->sessionName);
            session_set_cookie_params([
                'lifetime' => $this->config['lifetime'],
                'path' => $this->cookiePath,
                'domain' => '',
                'secure' => $this->config['secure'],
                'httponly' => $this->config['httponly'],
                'samesite' => $this->config['samesite'],
            ]);

            if ($this->config['save_handler'] === 'database') {
                $this->handler = new \System\Session\DatabaseSessionHandler();
                session_set_save_handler($this->handler, true);
            } elseif ($this->config['save_handler'] === 'file') {
                $savePath = $this->config['save_path'] ?: sys_get_temp_dir();
                $this->handler = new \System\Session\FileSessionHandler($savePath);
                session_set_save_handler($this->handler, true);
            }

            $this->start();
        }
    }

    public function start(): void
    {
        if (!$this->sessionStarted) {
            session_start();
            $this->sessionStarted = true;

            if (!isset($_SESSION['session_fingerprint'])) {
                $_SESSION['session_fingerprint'] = $this->fingerPrint();
            } else {
                if ($_SESSION['session_fingerprint'] !== $this->fingerPrint()) {
                    // Distruggo tutte le sessioni.
                    $this->destroy();
                }
            }

            // Verifica se la sessione esiste già nel database
            if ($this->handler instanceof \System\Session\DatabaseSessionHandler) {
                $this->handler->gc($this->lifetime);
                $sessionExists = $this->handler->open('', session_id());
                if (!$sessionExists) {
                    // Se la sessione non esiste nel database, la inizializzo..
                    $this->regenerateId();
                    $_SESSION['initiated'] = true;
                } elseif (isset($this->config['regenerate_interval'])) {
                    $this->regenerateIdPeriodically();
                }
            } elseif ($this->handler instanceof \System\Session\FileSessionHandler) {
                // Aggiunto: verifica se la sessione esiste nel file
                $sessionFilePath = $this->config['save_path'] . DIRECTORY_SEPARATOR . 'sess_' . session_id();
                if (!file_exists($sessionFilePath)) {
                    // Se la sessione non esiste nel file, la inizializzo..
                    $this->regenerateId();
                    $_SESSION['initiated'] = true;
                } elseif (isset($this->config['regenerate_interval'])) {
                    $this->regenerateIdPeriodically();
                }
            }
        }

        $this->cleanupOldSessions();
    }

    public function cleanupOldSessions(): void
    {
        if ($this->handler instanceof \System\Session\DatabaseSessionHandler) {
            $sessioName = $this->config['name'];
            $this->handler->cleanup($this->lifetime, $sessioName);
        }
    }

    public function regenerateId(bool $deleteOldSession = false): void
    {
        $oldSessionId = session_id();

        if ($deleteOldSession && (empty($_SERVER['HTTP_X_REQUESTED_WITH']) || strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest')) {
            $sessionData = $_SESSION;

            // Elimina il record della sessione precedente dal database
            if ($this->handler instanceof \System\Session\DatabaseSessionHandler) {
                $this->handler->destroy($oldSessionId);
            }

            session_regenerate_id($deleteOldSession);
            $_SESSION = $sessionData;
        }
    }

    private function regenerateIdPeriodically(): void
    {
        if (!isset($_SESSION['last_regeneration'])) {
            $_SESSION['last_regeneration'] = time();
        }

        if (time() - $_SESSION['last_regeneration'] > $this->config['regenerate_interval']) {
            $this->regenerateId();
            $_SESSION['last_regeneration'] = time();
        }
    }

    public function getId(): string
    {
        return session_id();
    }

    public function destroy(): void
    {
        $_SESSION = [];

        if (ini_get("session.use_cookies")) {
            $params = session_get_cookie_params();
            setcookie($this->sessionName, '', [
                'expires' => time() - 42000,
                'path' => $params["path"],
                'domain' => $params["domain"],
                'secure' => $params["secure"],
                'httponly' => $params["httponly"],
                'samesite' => $params["samesite"] ?? 'Lax',
            ]);
        }

        session_destroy();
        $this->sessionStarted = false;
    }

    public function close(): void
    {
        @session_write_close();
        $this->sessionStarted = false;
    }

    // Gestione dei dati di sessione
    public function set(string $key, mixed $value): void
    {
        if ($this->verifyDot($key)) {
            $keys = explode('.', $key);
            $sessionRef = &$_SESSION;

            foreach ($keys as $subKey) {
                if (!isset($sessionRef[$subKey]) || !is_array($sessionRef[$subKey])) {
                    $sessionRef[$subKey] = [];
                }
                $sessionRef = &$sessionRef[$subKey];
            }

            if (is_array($value)) {
                $sessionRef = $this->recursiveSetSession($value);
            } else {
                $sessionRef = $this->encrypt($value);
            }
        } else {
            if (is_array($value)) {
                $_SESSION[$key] = $this->recursiveSetSession($value);
            } else {
                $_SESSION[$key] = $this->encrypt($value);
            }
        }
    }

    private function recursiveSetSession(array $data): array
    {
        $tempData = [];

        foreach ($data as $key => $value) {

            if (is_array($value)) {
                $tempData[$key] = $this->recursiveSetSession($value);
            } else {
                $tempData[$key] = $this->encrypt($value);
            }
        }

        return $tempData;
    }

    public function get(string $key, mixed $default = null): mixed
    {
        if ($this->verifyDot($key)) {
            $keys = explode('.', $key);
            $sessionRef = $_SESSION;

            foreach ($keys as $subKey) {
                if (isset($sessionRef[$subKey])) {
                    $sessionRef = $sessionRef[$subKey];
                } else {
                    return $default;
                }
            }

            if (is_array($sessionRef)) {
                return $this->recursiveGetSession($sessionRef);
            } else {
                return $this->decrypt($sessionRef);
            }
        } else {
            if (isset($_SESSION[$key])) {
                if (is_array($_SESSION[$key])) {
                    return $this->recursiveGetSession($_SESSION[$key]);
                } else {
                    return $this->decrypt($_SESSION[$key]);
                }
            }
        }

        return $default;
    }

    private function verifyDot(mixed $key = null): bool
    {
        return is_string($key) && strpos($key, '.') !== false;
    }

    private function recursiveGetSession(array $data): array
    {
        $tempData = [];

        foreach ($data as $key => $value) {

            if (is_array($value)) {
                $tempData[$key] = $this->recursiveGetSession($value);
            } else {
                $tempData[$key] = $this->decrypt($value);
            }
        }

        return $tempData;
    }

    public function has(mixed $key): bool
    {
        if ($this->verifyDot($key)) {
            $keys = explode('.', $key);
            $sessionRef = $_SESSION;

            foreach ($keys as $subKey) {
                if (isset($sessionRef[$subKey])) {
                    $sessionRef = $sessionRef[$subKey];
                } else {
                    return false;
                }
            }
            return true;
        } else {
            return isset($_SESSION[$key]);
        }
    }

    public function all(): array
    {
        $data = [];
        foreach ($_SESSION as $key => $value) {
            if ($key !== 'flash_data' && $key !== 'temp_data') {
                if (is_array($value)) {
                    $data[$key] = $this->recursiveGetSession($value);
                } else {
                    $data[$key] = $this->decrypt($value);
                }
            }
        }
        return $data;
    }

    // Gestione dei dati temporanei
    public function setTemp(string $key, mixed $value, int $ttl): void
    {
        if ($this->verifyDot($key)) {
            $keys = explode('.', $key);
            $sessionRef = &$_SESSION['temp_data'];

            foreach ($keys as $subKey) {
                if (!isset($sessionRef[$subKey]) || !is_array($sessionRef[$subKey])) {
                    $sessionRef[$subKey] = [];
                }
                $sessionRef = &$sessionRef[$subKey];
            }

            $sessionRef = [
                'value' => $this->encrypt($value),
                'expires_at' => time() + $ttl,
            ];
        } else {
            $_SESSION['temp_data'][$key] = [
                'value' => $this->encrypt($value),
                'expires_at' => time() + $ttl,
            ];
        }
    }

    public function getTemp(string $key, mixed $default = null): mixed
    {
        if ($this->verifyDot($key)) {
            $keys = explode('.', $key);
            $sessionRef = $_SESSION['temp_data'] ?? [];

            foreach ($keys as $subKey) {
                if (isset($sessionRef[$subKey])) {
                    $sessionRef = $sessionRef[$subKey];
                } else {
                    return $default;
                }
            }

            if (isset($sessionRef['value'], $sessionRef['expires_at'])) {
                if ($sessionRef['expires_at'] >= time()) {
                    return $this->decrypt($sessionRef['value']);
                } else {
                    $this->killTemp($key);
                }
            }
        } else {
            if (isset($_SESSION['temp_data'][$key])) {
                $data = $_SESSION['temp_data'][$key];
                if ($data['expires_at'] >= time()) {
                    return $this->decrypt($data['value']);
                } else {
                    unset($_SESSION['temp_data'][$key]);
                }
            }
        }

        return $default;
    }

    private function killTemp(string $key): void
    {
        if ($this->verifyDot($key)) {
            $keys = explode('.', $key);
            $sessionRef = &$_SESSION['temp_data'];
            $lastKey = array_pop($keys);

            foreach ($keys as $subKey) {
                if (isset($sessionRef[$subKey])) {
                    $sessionRef = &$sessionRef[$subKey];
                } else {
                    return;
                }
            }

            unset($sessionRef[$lastKey]);
        } else {
            unset($_SESSION['temp_data'][$key]);
        }
    }

    // Gestione dei messaggi flash
    public function setFlash(string $key, mixed $value): void
    {
        if ($this->verifyDot($key)) {
            $keys = explode('.', $key);
            $sessionRef = &$_SESSION['flash_data'];

            foreach ($keys as $subKey) {
                if (!isset($sessionRef[$subKey]) || !is_array($sessionRef[$subKey])) {
                    $sessionRef[$subKey] = [];
                }
                $sessionRef = &$sessionRef[$subKey];
            }

            $sessionRef = $this->encrypt($value);
        } else {
            $_SESSION['flash_data'][$key] = $this->encrypt($value);
        }
    }

    public function getFlash(string $key, mixed $default = null): mixed
    {
        if ($this->verifyDot($key)) {
            $keys = explode('.', $key);
            $sessionRef = &$_SESSION['flash_data'];
            $lastKey = array_pop($keys);

            foreach ($keys as $subKey) {
                if (isset($sessionRef[$subKey])) {
                    $sessionRef = &$sessionRef[$subKey];
                } else {
                    return $default;
                }
            }

            if (isset($sessionRef[$lastKey])) {
                $value = $this->decrypt($sessionRef[$lastKey]);
                unset($sessionRef[$lastKey]);
                return $value;
            }
        } else {
            if (isset($_SESSION['flash_data'][$key])) {
                $value = $this->decrypt($_SESSION['flash_data'][$key]);
                unset($_SESSION['flash_data'][$key]);
                return $value;
            }
        }

        return $default;
    }

    public function kill($key): void
    {
        if ($this->verifyDot((string) $key)) {
            $keys = explode('.', (string) $key);
            $sessionRef = &$_SESSION;
            $lastKey = array_pop($keys);

            foreach ($keys as $subKey) {
                if (isset($sessionRef[$subKey])) {
                    $sessionRef = &$sessionRef[$subKey];
                } else {
                    return;
                }
            }

            unset($sessionRef[$lastKey]);
        } else {
            unset($_SESSION[(string) $key]);
        }
    }

    // Metodi di crittografia
    public function encrypt(mixed $data): string
    {
        $key = hash('sha256', $this->config['encryption_key'], true);
        $ivLength = openssl_cipher_iv_length('AES-256-CBC');
        $iv = random_bytes($ivLength);
        $encrypted = openssl_encrypt(serialize($data), 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

        return base64_encode($iv . $encrypted);
    }

    public function decrypt(string $data): mixed
    {
        $key = hash('sha256', $this->config['encryption_key'], true);
        $data = base64_decode($data);
        $ivLength = openssl_cipher_iv_length('AES-256-CBC');
        $iv = substr($data, 0, $ivLength);
        $encrypted = substr($data, $ivLength);
        $decrypted = openssl_decrypt($encrypted, 'AES-256-CBC', $key, OPENSSL_RAW_DATA, $iv);

        return unserialize($decrypted);
    }

    private function fingerPrint()
    {
        $fingerprint = Input::userAgent();

        if ($this->config['match_ip'] === true) {
            $fingerprint .= Input::ipAddress();
        }

        return hash('sha256', base64_encode($fingerprint . $this->config['encryption_key']));
    }
}