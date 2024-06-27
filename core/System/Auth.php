<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

use Exception;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Class Auth
 * @package System
 */
class Auth
{
    /**
     * @var bool
     */
    private $isValid = false;

    /**
     * @var
     */
    private $adaptor;

    /**
     * Auth constructor.
     * @param $adaptor
     * @param string $registry
     * @throws Exception
     */
    public function __construct($adaptor = null, $registry = '')
    {
        if (empty($adaptor)) {

            $config = new \Maer\Config\Config();
            $config->load(APP_PATH . 'Config/auth.php');

            $adaptor = $config->get('drivers', 'database');

        }

        if (is_callable($adaptor, '__construct')) {

            $className = $adaptor;

        } else {

            $className = '\\System\\Auth\\' . ucfirst($adaptor);
        }

        if (class_exists($className)) {

            if ($registry) {

                $this->adaptor = new $className($registry);

            } else {

                $this->adaptor = new $className();

            }

            // Chiudo la classe auth dopo averla instanziata..
            register_shutdown_function([$this, 'close']);

        } else {

            showError('Errore Facade', 'La classe ' . $adaptor . 'non è stata trovata');
            exit();

        }
    }

    /**
     * @param null $usernameOrEmail
     * @param null $password
     * @param null | array $otherWhere
     * @return mixed
     */
    public function authenticate($usernameOrEmail = NULL, $password = NULL, $otherWhere = null)
    {
        return $this->adaptor->authenticate($usernameOrEmail, $password, $otherWhere);
    }

    /**
     * @return mixed
     */
    public function getErrorAuth()
    {
        return $this->adaptor->error;
    }

    /**
     * @param $error
     */
    protected function setErrorAuth($error)
    {
        $this->adaptor->error = $error;
    }

    /**
     * @param null $token
     * @return mixed
     */
    public function basiAuthAPI($username=null,$password=null)
    {
        return $this->adaptor->basiAuthAPI($username,$password);
    }

    /**
     * @return bool
     */
    public function isValid()
    {
        return $this->adaptor->isValid();
    }

    /**
     * @param null $data
     * @return mixed
     */
    public function addStorage($data = null, $token = null)
    {
        $instanceOfJwt = '\System\Auth\Jwt';

        if ($this->adaptor instanceof $instanceOfJwt) {

            return $this->adaptor->addStorage($data, $token);

        }

        return $this->adaptor->addStorage($data);
    }

    /**
     * @param null $data
     * @return mixed
     */
    public function removeStorage($data = null, $token = null)
    {
        $instanceOfJwt = '\System\Auth\Jwt';

        if ($this->adaptor instanceof $instanceOfJwt) {

            return $this->adaptor->removeStorage($data, $token);

        }

        return $this->adaptor->removeStorage($data);
    }

    /**
     * @param null $data
     * @return mixed
     */
    public function getStorage($data = null, $token = null)
    {
        $instanceOfJwt = '\System\Auth\Jwt';

        if ($this->adaptor instanceof $instanceOfJwt) {

            return $this->adaptor->getStorage($data, $token);

        }

        return $this->adaptor->getStorage($data);
    }

    /**
     * @return mixed
     */
    public function hasIdentity($data = null)
    {
        $instanceOfJwt = '\System\Auth\Jwt';

        if ($this->adaptor instanceof $instanceOfJwt) {

            return $this->adaptor->hasIdentity($data);

        }

        return $this->adaptor->hasIdentity();
    }

    /**
     * @return int
     */
    public function id($data = null)
    {

        $instanceOfJwt = '\System\Auth\Jwt';

        if ($this->adaptor instanceof $instanceOfJwt) {

            return $this->adaptor->id($data);

        }


        return $this->adaptor->id();

    }

    /**
     * @param null $data
     * @return mixed
     */
    public function getIdentity($data = null)
    {
        $instanceOfJwt = '\System\Auth\Jwt';

        if ($this->adaptor instanceof $instanceOfJwt) {

            return $this->adaptor->getIdentity($data);

        }

        return $this->adaptor->getIdentity($data);
    }

    /**
     * @return mixed
     */
    public function clearIdentity()
    {
        return $this->adaptor->clearIdentity();
    }

    /**
     * @return null
     */
    public function getToken()
    {
        $instanceOfJwt = '\System\Auth\Jwt';

        if ($this->adaptor instanceof $instanceOfJwt) {

            return $this->adaptor->getToken();

        }

        return null;
    }

    /**
     * @return mixed
     */
    public function expireToken()
    {
        $instanceOfJwt = '\System\Auth\Jwt';

        if ($this->adaptor instanceof $instanceOfJwt) {

            return $this->adaptor->expireToken();

        }
    }

    /**
     * @param $data
     * @return mixed
     * @Description solo per le sessioni sul database.
     */
    public function regenerateSession($data)
    {

        //$instanceOfDatabase = '\System\Auth\Database';

        // if ($this->adaptor instanceof $instanceOfDatabase) {

        return $this->adaptor->regenerateSession($data);

        //}
    }

    /**
     * @return mixed
     */
    public function close()
    {
        return $this->adaptor->close();
    }

    /**
     * @param null $username
     * @param null $email
     * @return string
     */
    public function generateToken($username = null, $email = null)
    {
        $instanceOfJwt = '\System\Auth\Jwt';

        if ($this->adaptor instanceof $instanceOfJwt) {

            return $this->adaptor->generateToken($username, $email);

        }

        return null;
    }
}
