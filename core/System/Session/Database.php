<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System\Session;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Class Database
 * @package System\Session
 */
class Database
{
    public $regenerateId;
    public $expire;

    public function __construct()
    {
        $this->regenerateId = config('regenerate_id', 300, 'session');
        $this->expire = config('expire_limit', 28800, 'session');


        $this->start();
    }

    /**
     * @return bool
     */
    public function start()
    {

        if (!session_id()) {

            // $handler = new SecureDbHandler();
            // @session_set_save_handler($handler, true);

            // Alcune settaggi per la sicurezza delle sessioni..
            @ini_set('session.use_trans_sid', 0);
            @ini_set('session.use_strict_mode', 1);
            @ini_set('session.use_cookies', 1);
            @ini_set('session.use_only_cookies', 1);
            @ini_set('session.cookie_lifetime', 0);
            @ini_set('session.cookie_httponly ', 1);

            $session = @session_start();

            // Rigenerazione sessione..
            if (
                (empty($_SERVER['HTTP_X_REQUESTED_WITH']) or strtolower($_SERVER['HTTP_X_REQUESTED_WITH']) !== 'xmlhttprequest') &&
                (!empty($_SESSION['___FREMAWORK___']['last_regen']) && $_SESSION['___FREMAWORK___']['last_regen'] + $this->regenerateId < time())
            ) {

                $_SESSION['___FREMAWORK___']['last_regen'] = time();

                @session_regenerate_id(false);
            } else {

                if (empty($_SESSION['___FREMAWORK___']['last_regen'])) {

                    $_SESSION['___FREMAWORK___']['last_regen'] = time();
                }
            }

            // Limit access..
            if (!empty($_SESSION['___FREMAWORK___']['begin']) && $_SESSION['___FREMAWORK___']['begin'] + $this->expire < time()) {

                $_SESSION['___FREMAWORK___']['begin'] = time();

                $this->destroy();
                return false;
            } else {

                if (empty($_SESSION['___FREMAWORK___']['begin'])) {

                    $_SESSION['___FREMAWORK___']['begin'] = time();
                }
            }

            if (empty($_SESSION['framework___ua'])) {

                $_SESSION['framework___ua'] = $this->fingerPrint();
            }

            if ($_SESSION['framework___ua'] !== $this->fingerPrint()) {
                $this->destroy();
                return false;
            }

            return $session;
        }

        return true;
    }

    /**
     * @description Close Session
     * @return void
     */
    public function close()
    {

        return @session_write_close();
    }

    /**
     * @description Close Session
     * @return void
     */
    public function destroy()
    {

        if (session_id()) {

            session_unset();

            session_destroy();

            $_SESSION = array();

            @setcookie("PHPSESSID", null);

            @setcookie("KEY_PHPSESSID", null);
        }
    }

    /**
     * @param $key
     * @return bool
     */
    public function has($key)
    {

        if (!session_id()) {

            //            $this->start();

        }

        foreach (func_get_args() as $argument) {

            if (is_array($argument)) {

                foreach ($argument as $key) {

                    if (!isset($_SESSION[(string)$key])) return false;
                }
            } else {

                if (!isset($_SESSION[(string)$argument])) return false;
            }
        }

        return true;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function get($key)
    {

        if (!session_id()) {

            //            $this->start();
        }

        $key = (string)$key;

        if ($this->has($key)) {

            return $_SESSION[(string)$key];
        }

        return null;
    }

    /**
     * @return string
     */
    public function getId()
    {
        if (!session_id()) {

            //            $this->start();

        }

        return session_id();
    }

    /**
     * @param $key
     * @param $value
     */
    public function set($key, $value = null)
    {
        if (!session_id()) {

            //            $this->start();

        }

        if (is_array($key)) {

            $this->fetchFromArray($key, 'set');
        } else {

            $_SESSION[(string)$key] = $value;
        }
    }

    /**
     * @param $key
     * @param $value
     */
    public function setFlash($key, $value)
    {

        if (!session_id()) {

            //            $this->start();

        }

        if (is_array($key)) {

            $this->fetchFromArray($key, 'setFlash');
        } else {

            $_SESSION['flash'][(string)$key] = $value;
        }
    }

    /**
     * @param $key
     * @return null
     */
    public function getFlash($key)
    {

        if (!session_id()) {

            //            $this->start();

        }

        if (!empty($_SESSION['flash'][(string)$key])) {

            $flash = $_SESSION['flash'][(string)$key];

            unset($_SESSION['flash'][(string)$key]);

            return $flash;
        }

        return null;
    }

    /**
     * @param $key
     * @param $value
     * @param $temp
     */
    public function setTemp($key, $value, $temp)
    {
        if (!session_id()) {

            //            $this->start();

        }

        if (is_array($key)) {

            $this->fetchFromArray($key, 'setTemp', $temp);
        } else {

            if (empty($_SESSION['temp'][$key]['expire'])) {

                $_SESSION['temp'][(string)$key] = [
                    'value' => $value,
                    'expire' => $temp + time()
                ];

                return true;
            } else {

                if (!$this->checkExpireTempData($key)) {

                    $_SESSION['temp'][(string)$key] = [
                        'value' => $value,
                        'expire' => $temp + time()
                    ];

                    return true;
                }
            }
        }

        return null;
    }

    private function checkExpireTempData($key = null)
    {

        $check = true;
        if (
            !empty($_SESSION['temp'][$key]['expire']) &&
            (($_SESSION['temp'][$key]['expire'] - time()) <= 0)
        ) {
            $check = false;
        }
        return $check;
    }

    /**
     * @param $key
     * @return null
     */
    public function getTemp($key)
    {
        if (!session_id()) {

            // $this->start();
        }

        $sessionTemp = !empty($_SESSION['temp'][$key])
            ? $_SESSION['temp'][$key]
            : null;

        if (!empty($sessionTemp)) {

            $temp = $sessionTemp['expire'];

            if (($temp - time()) >= 0) {

                return $sessionTemp['value'];
            } else {

                unset($_SESSION['temp'][$key]);
            }
        }

        return null;
    }

    /**
     * @param $key
     * @return mixed
     */
    public function kill($key)
    {
        if (!session_id()) {

            //            $this->start();

        }

        if (is_array($key)) {

            $this->fetchFromArray($key, 'kill');
        } else {

            if (isset($_SESSION[(string)$key])) {

                $_SESSION[(string)$key] = null;
                unset($_SESSION[(string)$key]);
            }
        }

        return null;
    }

    /**
     * @return mixed
     */
    public function all()
    {

        if (!session_id()) {

            //            $this->start();

        }

        return $_SESSION;
    }

    /**
     * @param $data
     * @param string $method
     * @param null $temp
     */
    private function fetchFromArray($data, $method = 'set', $temp = null)
    {
        foreach ($data as $key => $value) {

            if ($method === 'kill') {

                $this->$method($value);
            }

            if ($method === 'set') {

                $this->$method($key, $value);
            }

            if ($method === 'setTemp') {

                $this->$method($key, $value, $temp);
            }

            if ($method === 'setFlash') {

                $this->$method($key, $value);
            }
        }
    }

    private function winOs()
    {
        return PHP_OS_FAMILY === 'Windows';
    }


    private function fingerPrint()
    {
        $agent = new \System\Agent();
        $lang = $agent->languages();
        $lang = is_array($agent->languages())
            ? implode('_', $agent->languages())
            : (string) $lang;
        $platform = $agent->platform();
        $browser = $agent->browser();
        $userAgent = $browser . '|' . $platform . '|' . $lang;
        $fingerPrint = strlen($userAgent) . $userAgent . config('fringe_print', null, 'session');
        return md5($fingerPrint);
    }
}