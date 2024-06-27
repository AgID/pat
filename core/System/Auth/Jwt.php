<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System\Auth;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class Jwt
{

    /**
     * @var array|mixed|null|string
     */
    protected $nameTable;

    /**
     * @var array|mixed|null|string
     */
    protected $namePk;

    /**
     * @var array|mixed|null|string
     */
    protected $nameUsername;

    /**
     * @var array|mixed|null|string
     */
    protected $nameEmail;

    /**
     * @var array|mixed|null|string
     */
    protected $namePassword;

    /**
     * @var array|mixed|null|string
     */
    protected $nameActive;

    /**
     * @var \System\Database
     */
    protected $db;

    /**
     * @var string
     */
    protected $hasIdentity;

    /**
     * @var bool
     */
    protected $isValid = false;

    /**
     * @var null
     */
    protected $jwtKey = null;

    /**
     * @var null
     */
    protected $token = null;


    /**
     * Jwt constructor.
     * @throws \Exception
     */
    public function __construct()
    {
        $config = new \Maer\Config\Config();
        $config->load(APP_PATH . 'Config/auth.php');

        $nameTable = $config->get('table');
        $namePk = $config->get('pk');
        $nameUsername = $config->get('username');
        $nameEmail = $config->get('email');
        $namePassword = $config->get('password');
        $nameActive = $config->get('active');

        $this->nameTable = strlen($nameTable) >= 3 ? $nameTable : 'users';
        $this->namePk = strlen($namePk) >= 2 ? $namePk : 'id';
        $this->nameUsername = strlen($nameUsername) > 3 ? $nameUsername : 'username';
        $this->nameEmail = strlen($nameEmail) > 3 ? $nameEmail : 'email';
        $this->namePassword = strlen($namePassword) > 3 ? $namePassword : 'password';
        $this->nameActive = strlen($nameActive) > 3 ? $nameActive : 'active';
        $this->sessionName = $config->get('session_name') . '___auth';
        $this->hasIdentity = $this->sessionName . '___auth' . '_has_identity';

        $this->jwtKey = $config->get('jwt_key');
        $this->expireToken = $config->get('token_expire');

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

                    $userAgent = new \System\Agent();

                    $this->token = $this->generateToken([
                        'id' => $users->{$this->namePk},
                        'username' => $users->{$this->nameUsername},
                        'email' => $users->{$this->nameEmail},
                        'platform' => $userAgent->languages(),
                        'device' => $userAgent->device(),
                        'browser' => $userAgent->browser(),
                        'userAgent' => $userAgent->getUserAgent(),
                        'expire_token' => $this->expireToken(),
                    ]);

                    $this->isValid = true;

                }

            }

        }

        return $this->isValid;
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->isValid;

    }

    /**
     * @return null
     */
    public function clearIdentity()
    {
        return null;
    }

    /**
     * @return null
     */
    public function close()
    {
        return null;
    }

    /**
     * @param null $storage
     * @param null $token
     * @return bool|null|string
     */
    public function addStorage($storage = null, $token = null)
    {
        if (!empty($token) && $getToken = $this->validateToken($token)) {

            $getToken = objectToArray($getToken);

            if (\System\Arr::is_multi($storage) === false) {

                foreach ($storage as $key => $value) {

                    $getToken['options'][$key] = $value;
                }

            }


            $this->token = $this->generateToken($getToken);
            return $this->token;

        }

        return true;
    }

    /**
     * @param null $data
     * @param null $token
     * @return null
     */
    public function getStorage($data = null, $token = null)
    {
        if (!empty($token) && $getToken = $this->validateToken($token)) {

            $getToken = objectToArray($getToken);

            if (!empty($getToken['options'][$data])) {

                return $getToken['options'][$data];

            }
        }

        return null;
    }

    /**
     * @param $token
     * @return bool
     */
    public function hasIdentity($token)
    {
        if (!empty($token) && $this->validateToken($token)) {

            return true;
        }

        return false;
    }

    /**
     * @param null $token
     * @return bool|object
     */
    public function getIdentity($token = null)
    {
        if (!empty($token) && $getToken = $this->validateToken($token)) {

            return $getToken;
        }

        return false;
    }

    /**
     * @return null
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * @return false|string
     */
    public function expireToken()
    {
        return date('Y-m-d H:i:s', time() + $this->expireToken);
    }

    /**
     * @param null $token
     * @return bool
     */
    public function id($token = null)
    {

        if (!empty($token) && $getToken = $this->validateToken($token)) {

            return !empty($getToken->id) ? $getToken->id : false;
        }

        return false;
    }

    /**
     * @param $str
     * @return bool
     */
    protected function validEmail($str)
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
     * @param $token
     * @return object
     */
    protected function validateToken($token)
    {
        return \System\Jwt::decode($token, $this->jwtKey);
    }

    /**
     * @param $data
     * @return string
     */
    protected function generateToken($data)
    {
        return \System\Jwt::encode($data, $this->jwtKey);
    }

}
