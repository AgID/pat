<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System\Auth;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Class Database
 * @package System\Auth
 */
class Database
{
    /**
     * @var array|mixed|null|string
     */
    private $nameTable;

    /**
     * @var array|mixed|null|string
     */
    private $namePk;

    /**
     * @var array|mixed|null|string
     */
    private $nameUsername;

    /**
     * @var array|mixed|null|string
     */
    private $nameEmail;

    /**
     * @var array|mixed|null|string
     */
    private $namePassword;

    /**
     * @var array|mixed|null|string
     */
    private $nameActive;

    /**
     * @var \System\Database
     */
    private $db;

    /**
     * @var \System\Encryption
     */
    private $encryption;

    /**
     * @var string
     */
    private $sessionName;

    /**
     * @var string
     */
    private $hasIdentity;

    /**
     * @var \System\Session
     */
    private $session;

    /**
     * @var bool
     */
    private $isValid = false;

    /**
     * @var
     */
    private $hasEncryption;

    /**
     * Database constructor.
     * @param null $registry
     * @throws \Exception
     */
    public function __construct($registry = null)
    {
        $config = new \Maer\Config\Config();
        $config->load(APP_PATH . 'Config/auth.php');

        $nameTable = $config->get('table');
        $namePk = $config->get('pk');
        $nameUsername = $config->get('username');
        $nameEmail = $config->get('email');
        $namePassword = $config->get('password');
        $nameActive = $config->get('active');

        $this->nameTable = strlen((string) $nameTable) >= 3 ? $nameTable : 'users';
        $this->namePk = strlen((string) $namePk) >= 2 ? $namePk : 'id';
        $this->nameUsername = strlen((string) $nameUsername) >= 3 ? $nameUsername : 'username';
        $this->nameEmail = strlen((string) $nameEmail) >= 3 ? $nameEmail : 'email';
        $this->namePassword = strlen((string) $namePassword) >= 3 ? $namePassword : 'password';
        $this->nameActive = strlen((string) $nameActive) >= 3 ? $nameActive : 'active';
        $this->sessionName = $config->get('session_name') . '___auth';
        $this->hasIdentity = $this->sessionName . '___auth' . '_has_identity';
        $this->hasEncryption = $config->get('has_encryption');

        if ($this->hasEncryption === true) {

            $this->encryption = new \System\Encryption([
                'driver' => $config->get('algorithm'),
                'key' => $config->get('encryption_key'),
            ]);

            $this->encryption->create_key(18);
        }

        $this->session = new \System\Session();

        $this->db = new \System\Database();
    }

    /**
     * @param null $usernameOrEmail
     * @param null $password
     * @return bool
     */
    public function authenticate($usernameOrEmail = null, $password = null)
    {
        if (!empty($usernameOrEmail) && !empty($password)) {

            $DB = $this->db;

            if (!$this->validEmail($usernameOrEmail)) {

                $fieldIdentityUser = $this->nameUsername;

            } else {

                $fieldIdentityUser = $this->nameEmail;

            }

            $users = $DB::table($this->nameTable)
                ->where($fieldIdentityUser, '=', $usernameOrEmail)
                ->where($this->nameActive, '=', 1)
                ->limit(1)
                ->first();


            if (!empty($users) && is_object($users)) {

                $verify = (bool)\System\Password::verify($password, $users->password);

                if ($verify === true) {

                    $this->generateSession($users);

                    $this->isValid = true;

                } else {

                    // Se sono autenticati e comunque provano ad accedere tramite autenticazione
                    // Elimino le sessioni.

                    if ($this->session->has($this->hasIdentity)) {

                        $this->session->kill($this->hasIdentity);

                    }

                    if ($this->session->has($this->sessionName)) {

                        $this->session->kill($this->sessionName);

                    }
                }

            }

        }

        return $this->isValid;
    }

    /**
     * @return bool
     */
    public function hasIdentity()
    {
        $hasIdentity = $this->session->has($this->hasIdentity) ? $this->session->get($this->hasIdentity) : false;
        return (bool)$hasIdentity;
    }

    /**
     * @return null|array
     */
    public function getIdentity($data = null)
    {

        $returnData = null;

        if ($this->session->has($this->hasIdentity) && $this->session->has($this->hasIdentity)) {

            $items = $this->session->get($this->sessionName);

            if (!empty($data) && is_array($data)) {

                $items = \System\Arr::filter_keys($items, $data);

            }

            foreach ($items as $key => $value) {

                if ($key !== 'options') {

                    $returnData[$key] = ($this->hasEncryption === true) ? $this->encryption->decrypt($value) : $value;

                }

                if ($key === 'options' && (is_array($value) || is_object($value))) {

                    foreach ($value as $k => $v) {

                        $returnData['options'][$k] = ($this->hasEncryption === true) ? $this->encryption->decrypt($v) : $v;

                    }
                }

            }

        }

        return $returnData;

    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->isValid;
    }

    /**
     * @return bool
     */
    public function clearIdentity()
    {

        $this->session->kill($this->hasIdentity);
        $this->session->kill($this->sessionName);

        return true;
    }

    /**
     * Close connection database
     */
    public function close()
    {
        $DB = $this->db;
        $DB::disconnect();
    }

    /**
     * @param $str
     * @return bool
     */
    private function validEmail($str)
    {
        if (function_exists('idn_to_ascii') && preg_match('#\A([^@]+)@(.+)\z#', $str, $matches)) {

            $domain = defined('INTL_IDNA_VARIANT_UTS46')
                ? idn_to_ascii($matches[2], 0, INTL_IDNA_VARIANT_UTS46)
                : idn_to_ascii($matches[2]);

            if ($domain !== FALSE) {

                $str = $matches[1] . '@' . $domain;

            }
        }

        return (bool)filter_var($str, FILTER_VALIDATE_EMAIL);
    }

    /**
     * @param null $storage
     */
    public function addStorage($storage = null)
    {
        $data = null;

        if ($this->hasIdentity() === true && is_array($storage)) {

            $data = $this->session->get($this->sessionName);

            if (is_array($data)) {

                foreach ($data as $key => $value) {

                    if ($key !== 'options') {

                        $data[$key] = ($this->hasEncryption === true) ? $this->encryption->decrypt($value) : $value;

                        if ($this->session->has($key)) {

                            $this->session->kill($key);

                        }

                    }

                    if ($key === 'options' && (is_array($value) || is_object($value))) {

                        foreach ($value as $k => $v) {

                            $data['options'][$k] = ($this->hasEncryption === true) ? $this->encryption->decrypt((string)$v) : (string)$v;

                            if ($this->session->has($data['options'][$k])) {

                                $this->session->kill($data['options'][$k]);

                            }

                        }

                    }

                }
            }

            // Aggiunge o sovrascrive l'indice options in sessione.
            if (is_array($storage) || is_object($storage)) {

                foreach ($storage as $key => $value) {

                    $data['options'][$key] = $value;

                    if ($this->session->has($data['options'][$key])) {

                        $this->session->kill($data['options'][$key]);

                    }

                }

            }

        }

        if ($data !== null) {

            $this->generateSession($data);
        }

    }

    /**
     * @param null $storage
     */
    public function removeStorage($storage = null)
    {
        $data = null;

        if ($this->hasIdentity() === true && is_array($storage)) {

            $data = $this->session->get($this->sessionName);

            if (is_array($data)) {

                foreach ($data as $key => $value) {

                    if ($key !== 'options') {

                        $data[$key] = ($this->hasEncryption === true) ? $this->encryption->decrypt($value) : $value;

                        if ($this->session->has($key)) {

                            $this->session->kill($key);

                        }

                    }

                    if ($key === 'options' && (is_array($value) || is_object($value))) {

                        foreach ($value as $k => $v) {

                            $data['options'][$k] = ($this->hasEncryption === true) ? $this->encryption->decrypt((string)$v) : (string)$v;

                            if ($this->session->has($data['options'][$k])) {

                                $this->session->kill($data['options'][$k]);

                            }

                        }

                    }

                }
            }

            // Aggiunge o sovrascrive l'indice options in sessione.
            if (is_array($storage) || is_object($storage)) {

                foreach ($storage as $key => $value) {

                    $data['options'][$key] = $value;

                    if ($this->session->has($data['options'][$key])) {

                        $this->session->kill($data['options'][$key]);

                    }

                }

            }

        }

    }

    /**
     * @param null $data
     * @return null
     */
    public function getStorage($data = null)
    {
        $storage = null;

        if ($this->hasIdentity() === true) {

            $identity = $this->getIdentity();

            if ($data === null) {

                $storage = !empty($identity['options']) ? $identity['options'] : null;
            }

            if (is_array($data) || is_object($data)) {

                if (!empty($identity['options']) && is_array($identity['options'])) {

                    foreach ($data as $key) {

                        if (array_key_exists($key, $identity['options']) === true) {

                            $storage[$key] = $identity['options'][$key];
                        }

                    }

                }

            }

            if (is_string($data)) {

                if (!empty($identity['options']) && is_array($identity['options'])) {

                    $storage = (array_key_exists($data, $identity['options']) === true) ? $identity['options'][$data] : null;

                }

            }
        }

        return $storage;

    }

    /**
     * @param null $username
     * @param null $email
     * @param null $password
     * @return string
     */
    public function generateToken($username = null, $email = null, $password = null)
    {

        $string = rand(0000, 9999) . $username . '_' . $email . '_' . microtime();

        return $this->encryption->encrypt(preg_replace("/\W|_/", '', $string));

    }

    /**
     * @return null
     */
    public function id()
    {

        $id = null;

        if ($this->hasIdentity() === true) {

            $identity = $this->getIdentity();

            $id = $identity['id'];
        }

        return $id;
    }


    /**
     * @param $data
     */
    public function regenerateSession($data)
    {
        return $this->generateSession($data);
    }

    /**
     * @param $items
     */
    private function generateSession($items)
    {
        $data = [];
        $items = (array)$items;

        if (!empty($items)) {

            foreach ($items as $key => $value) {

                if ($key !== $this->namePassword && $key !== 'options') {

                    $data[$key] = ($this->hasEncryption === true) ? $this->encryption->encrypt($value) : $value;

                }

                if ($key === 'options' && (is_array($value) || is_object($value))) {

                    foreach ($value as $k => $v) {

                        $data['options'][$k] = ($this->hasEncryption === true) ? $this->encryption->encrypt((string)$v) : (string)$v;

                    }

                }
            }

            if ((bool)array_key_exists('options', $data) === false) {
                $data['options'] = null;
            }

            $this->session->set($this->hasIdentity, true);
            $this->session->set($this->sessionName, $data);
        }

    }
}
