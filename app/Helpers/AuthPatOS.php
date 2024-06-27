<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Maer\Config\Config;
use System\Arr;
use System\Database;
use System\Encryption;
use System\Input;
use System\Password;
use System\Session;

class AuthPatOS
{
    /**
     * @var array|mixed|null|S
     */
    private $nameTable;

    /**
     * @var array|mixed|null|S
     */
    private $nameTableInstitution;

    /**
     * @var array|mixed|null|S
     */
    private $namePk;

    /**
     * @var
     */
    private $nameInstitutionPk;

    /**
     * @var array|mixed|null|S
     */
    private $nameUsername;

    /**
     * @var array|mixed|null|S
     */
    private $nameEmail;

    /**
     * @var array|mixed|null|S
     */
    private $namePassword;

    /**
     * @var array|mixed|null|S
     */
    private $nameActive;

    /**
     * @var Database
     */
    private $db;

    /**
     * @var Encryption
     */
    private $encryption;

    /**
     * @var S
     */
    private $sessionName;

    /**
     * @var S
     */
    private $hasIdentity;

    /**
     * @var Session
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
     * @var
     */
    private $nameSuperAdmin;

    /**
     * @var
     */
    private $nameAdmin;

    /**
     * @var
     */
    private $nameDeleted;

    /**
     * @var
     */
    private $nameIsAPI;

    /**
     * @var
     */
    private $namePasswordToken;

    /**
     * @var
     */
    private $lastVisited;

    /**
     * @var
     */
    private $institutionsId;

    /**
     * @var
     */
    private $lastVisitTimeLimit;

    /**
     * @var bool
     */
    private $isAuthTwoFactor = false;

    /**
     * @var
     */
    private $limitCallsApi;

    /**
     * @var null
     */
    public $error = null;

    /**
     * Database constructor.
     * @param null $registry
     * @throws Exception
     */
    public function __construct($registry = null)
    {

        $config = new Config();
        $config->load(APP_PATH . 'Config/auth_pat_os.php');

        $this->nameTable = $config->get('table');
        $this->nameTableInstitution = $config->get('table_institutions');
        $this->lastVisitTimeLimit = $config->get('last_visit_time_limit');
        $this->namePk = $config->get('pk');
        $this->nameInstitutionPk = $config->get('name_institution_pk');
        $this->nameUsername = $config->get('username');
        $this->nameEmail = $config->get('email');
        $this->namePassword = $config->get('password');
        $this->nameActive = $config->get('active');
        $this->nameSuperAdmin = $config->get('super_admin');
        $this->nameAdmin = $config->get('admin');
        $this->nameDeleted = $config->get('deleted');
        $this->lastVisited = $config->get('last_visit');
        $this->institutionsId = $config->get('institutions_id');
        $this->hasEncryption = $config->get('has_encryption');
        $this->nameIsAPI = $config->get('enable_api');
        $this->namePasswordToken = $config->get('token_password');
        $this->limitCallsApi = $config->get('limit_calls_api');

        $this->sessionName = $config->get('session_name') . '___pat_os_auth';
        $this->hasIdentity = $this->sessionName . '___auth' . '_pat_os_has_identity';

        if ($this->hasEncryption === true) {

            $this->encryption = new Encryption([
                'driver' => $config->get('algorithm'),
                'key' => $config->get('encryption_key'),
            ]);

            $this->encryption->create_key(18);
        }

        $this->session = new Session();
        $this->db = new Database();
    }

    public function basiAuthAPI($username = null, $password = null)
    {
        $hasIdentity = false;
        $user = null;
        $DB = $this->db;

        $query = $DB::table($this->nameTable)
            ->select(
                $this->nameTable . '.*',
                $this->nameTableInstitution . '.' . $this->limitCallsApi . ' AS limits_call_api'
            )
            ->join(
                $this->nameTableInstitution,
                $this->nameTableInstitution . '.' . $this->nameInstitutionPk, '=', $this->nameTable . '.' . $this->institutionsId,
                'left outer'
            )
            ->where(function ($query) use ($username) {
                $query->where($this->nameUsername, '=', $username)
                    ->orWhere($this->nameUsername, '=', checkEncrypt($username));
            })
            ->where($this->nameIsAPI, '=', 1)
            ->where($this->nameTable . '.' . $this->nameActive, '=', 1)
            ->where($this->nameTable . '.' . $this->nameDeleted, '=', 0)
            ->where(function ($q) {
                $q->where($this->institutionsId, '=', PatOsInstituteId());
            })
            ->first();

        if ($query !== null) {

            $user = objectToArray($query);

            if (Password::verify($password, $user[$this->namePasswordToken])) {
                $hasIdentity = $user;
            }

        }

        return $hasIdentity;
    }


    /**
     * @param null $usernameOrEmail
     * @param null $password
     * @return bool
     * @throws Exception
     */
    public function authenticate($usernameOrEmail = null, $password = null)
    {
        $hasIdentity = false;
        $user = null;

        // Se username o password non valorizzati non li faccio entrare
        if (!empty($usernameOrEmail) && !empty($password)) {

            $DB = $this->db;

            if (!$this->validEmail($usernameOrEmail)) {

                $fieldIdentityUser = $this->nameUsername;

            } else {

                $fieldIdentityUser = $this->nameEmail;

            }

            // Query Auth
            $result = $DB::table($this->nameTable)
                ->select(
                    $this->nameTable . '.*',
                    $this->nameTableInstitution . '.' . $this->lastVisitTimeLimit . ' AS last_visit_limit'
                )
                ->join(
                    $this->nameTableInstitution,
                    $this->nameTableInstitution . '.' . $this->nameInstitutionPk, '=', $this->nameTable . '.' . $this->institutionsId,
                    'left outer'
                )->where(function ($query) use ($fieldIdentityUser, $usernameOrEmail) {
                    $query->where($fieldIdentityUser, '=', $usernameOrEmail)
                        ->orWhere($fieldIdentityUser, '=', checkEncrypt($usernameOrEmail));
                })
                ->where($this->nameTable . '.' . $this->nameActive, '=', 1)
                ->where($this->nameTable . '.' . $this->nameDeleted, '=', 0)
                ->where(function ($q) {
                    $q->where($this->institutionsId, '=', PatOsInstituteId())
                        ->orWhere($this->nameSuperAdmin, '=', 1)
                        ->orWhere($this->nameAdmin, '=', 1);
                })
                ->limit(1)
                ->first();

            $user = objectToArray($result);

            if (!empty($result) && is_object($result)) {

                $pp = null;
                $sp = false;
                $vpp = false;

                if (!$sp) {

                    // Controllo per la prima autenticazione di un nuovo utente
                    if ($this->lastVisited !== null) {
                        $lastActivity = (strtotime($user[$this->lastVisited]) + ((int)$user['deactivate_account_no_use'] * 24 * 60 * 60));
                    } else {
                        $lastActivity = time() + 1;
                    }

                    // Controllo che la data dell'ultima visita sia inferiore alla data attuale
                    if ($lastActivity > time()) {

                        // Verifico se la password è al livello utente oppure con il passepartout
                        if (Password::verify($password, $user[$this->namePassword]) ||
                            $vpp
                        ) {

                            $hasIdentity = true;

                            $DB::table($this->nameTable)->where($this->namePk, $user[$this->namePk])->update([
                                $this->lastVisited => date('Y-m-d H:i:s')
                            ]);

                        } else {

                            $this->setErrorAuth(__('username_or_email_not_valid', null, 'patos_auth'));

                        }

                    } else {

                        $post = Input::post();

                        if (!empty($post['password'])) {
                            $post['password'] = 'hidden';
                        }

                        // Registro nei log la disattivazione dell'utente.
                        ActivityLog::create([
                            'user_id' => $user['id'],
                            'action' => 'Disattivazione utente "' . checkDecrypt($user['name']) . '" per mancato utilizzo.',
                            'username' => checkDecrypt($user['username']),
                            'request_post' => [
                                'post' => $post,
                                'get' => Input::get(),
                                'server' => Input::server(),
                            ],
                        ]);

                        // Query Disattivazione utente
                        $DB::table($this->nameTable)
                            ->where($this->namePk, $user[$this->namePk])
                            ->update([$this->nameActive => 0]);

                        // Gestione Labeling "l'utente è stato disabilitato per mancato utilizzo"
                        $this->setErrorAuth(__('user_disabled', null, 'patos_auth'));

                    }

                }

            } else {

                $this->isValid = false;
                $this->setErrorAuth(__('username_or_email_not_valid', null, 'patos_auth'));

            }

            // Verifico autenticazione
            if ($hasIdentity === true && $user !== null) {

                // Autenticazione andata a buon fine.
                $this->isValid = true;

                // Genera sessione.
                $this->generateSession($user);

            } else {

                // Se sono autenticato e provo ad accedere tramite form di login elimino le sessioni.
                if ($this->session->has($this->hasIdentity)) {

                    $this->session->kill($this->hasIdentity);

                }

                if ($this->session->has($this->sessionName)) {

                    $this->session->kill($this->sessionName);

                }

            }

        }

        return $this->isValid;
    }

    public function getErrorAuth()
    {
        return $this->error;
    }

    protected function setErrorAuth($error)
    {
        $this->error = $error;
    }

    /**
     * @return bool
     */
    public function isAuthTwoFactor()
    {

        return $this->isAuthTwoFactor;

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

                $items = Arr::filter_keys($items, $data);

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
        $params = session_get_cookie_params();
        setcookie(session_name(), '', time() - 42000,
            $params["path"], $params["domain"],
            $params["secure"], $params["httponly"]
        );
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
                ? idn_to_ascii($matches[2])
                : idn_to_ascii($matches[2]);

            if ($domain !== false) {

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
     * @param null $data
     * @return mixed
     */
    public function removeStorage($storage = null)
    {

        if ($this->hasIdentity() === true && $storage !== null) {

            $identity = $this->getIdentity();

            if (is_array($storage)) {

                foreach ($storage as $value) {

                    if (isset($identity['options'][$value])) {

                        $_SESSION['_patos___pat_os_auth']['options'][$value] = null;
                        unset($_SESSION['_patos___pat_os_auth']['options'][$value]);
                    }

                    if (isset($identity[$value])) {

                        $_SESSION['_patos___pat_os_auth'][$value] = null;
                        unset($_SESSION['_patos___pat_os_auth'][$value]);

                    }

                }

            } else {

                if (isset($identity['options'][$storage])) {

                    $_SESSION['_patos___pat_os_auth']['options'][$storage] = null;
                    unset($_SESSION['_patos___pat_os_auth']['options'][$storage]);
                }

                if (isset($identity[$storage])) {

                    $_SESSION['_patos___pat_os_auth'][$storage] = null;
                    unset($_SESSION['_patos___pat_os_auth'][$storage]);

                }

            }

            return true;
        }

        return null;
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
     * @return S
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
        $getIdentity = $this->getIdentity();

        if (!empty($items)) {

            if (!empty($getIdentity)) {

                $items = array_replace($getIdentity, $items);

            }

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
