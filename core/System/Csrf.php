<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

class Csrf
{
    /**
     * @var array|mixed|null
     */
    private $nameToken;

    /**
     * @var Session
     */
    private $session;

    public function __construct()
    {
        $this->session = new Session();
        $this->nameToken = config('csrf_token_name', null, 'app');
    }

    /**
     * @return void
     */
    public function generateToken($nameToken = null)
    {
        $nameToken = ($nameToken !== null) ? $nameToken : $this->nameToken;

        $salt = !empty($_SERVER['REMOTE_ADDR']) ? $_SERVER['REMOTE_ADDR'] : uniqid();
        $this->session->set($nameToken, sha1(uniqid(sha1(rand(000, 999) . $salt . time()), true)));

        return $salt;
    }

    /**
     * @param string $nameToken
     * @return string
     */
    public function getToken($nameToken = null)
    {
        $nameToken = ($nameToken !== null) ? $nameToken : $this->nameToken;

        if ($this->session->has($nameToken)) {

            $this->generateToken($nameToken);

        }

        return $this->session->get($nameToken);
    }

    /**
     * @param string $nameToken
     * @return string
     */
    public function getTokenName($nameToken)
    {
        return ($nameToken !== null) ? $nameToken : $this->nameToken;
    }

    /**
     * @param array $requestData
     * @param string $nameToken
     * @return bool
     */
    public function validate($requestData = [], $nameToken = '')
    {

        $nameToken = ($nameToken !== null) ? $nameToken : $this->nameToken;

        if ($this->session->has($nameToken)) {

            $this->generateToken($nameToken);

            return false;

        } elseif (empty($requestData[$nameToken])) {

            return false;

        } else {

            if ($this->compare($requestData[$nameToken], $this->getToken($nameToken))) {

                $this->generateToken($nameToken);

                return true;

            } else {

                return false;
            }

        }
    }

    /**
     * @param string $nameToken
     * @return string
     */
    public function getHiddenInputString($nameToken = '')
    {
        $nameToken = ($nameToken !== null) ? $nameToken : $this->nameToken;

        return sprintf('<input type="hidden" name="%s" value="%s"/>', $nameToken, $this->getToken($nameToken));
    }

    /**
     * @param string $nameToken
     * @return string
     */
    public function getQueryString($nameToken = '')
    {
        $nameToken = ($nameToken !== null) ? $nameToken : $this->nameToken;

        return sprintf('%s=%s', $nameToken, $this->getToken($nameToken));
    }

    /**
     * @param string $nameToken
     * @return array
     */
    public function getTokenAsArray($nameToken = '')
    {
        $nameToken = ($nameToken !== null) ? $nameToken : $this->nameToken;

        return array(
            $nameToken => $this->getToken($nameToken)
        );
    }

    /**
     * @param string $hasha
     * @param string $hashb
     * @return bool
     */
    public function compare($hasha = '', $hashb = '')
    {
        $hashes_are_not_equal = strlen((string) $hasha) ^ strlen($hashb);

        $length = min(strlen((string) $hasha), strlen((string) $hashb));
        $hasha = substr((string) $hasha, 0, $length);
        $hashb = substr((string) $hashb, 0, $length);

        for ($i = 0; $i < strlen($hasha); $i++) {
            $hashes_are_not_equal += !(ord($hasha[$i]) === ord($hashb[$i]));
        }

        return !$hashes_are_not_equal;
    }
}
