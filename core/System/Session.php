<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

use Exception;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Class Session
 * @package System
 */
class Session
{
    protected $adaptor;
    protected $sessionId;

    /**
     * Session constructor.
     * @param $adaptor
     * @param string $registry
     * @throws Exception
     */
    public function __construct($adaptor = null, $registry = '')
    {
        if (empty($adaptor)) {

            $config = new \Maer\Config\Config();
            $config->load(APP_PATH . 'Config/session.php');

            $adaptor = $config->get('drivers', 'file');

        }

        // Applico il Pattern Adaptor : in OLD style :-)
        $sessionClass = '\\System\\Session\\' . ucfirst($adaptor);

        if (class_exists($sessionClass)) {

            if ($registry) {

                $this->adaptor = new $sessionClass($registry);

            } else {

                $this->adaptor = new $sessionClass();

            }

            // Sicurezza: Chiudo La sessione dopo averla instanziata..
            register_shutdown_function([$this, 'close']);

        } else {

            if(!is_cli()){
                showError('Errore Adaptor', 'La classe ' . $adaptor . 'non è stata trovata');
                exit();
            }

        }
    }

    /**
     * @return mixed
     */
    public function start()
    {
        return $this->adaptor->start();
    }

    /**
     * @description Restituisce l'ID della sessione corrente
     * @return mixed
     */
    public function getId()
    {
        return $this->adaptor->getId();
    }

    /**
     * @description Questa funzione permette di settare dati di sessione
     * @param $key
     * @param $value
     * @return mixed
     */
    public function set($key = null, $value = null)
    {
        return $this->adaptor->set($key, $value);
    }

    /**
     * @description Questa funzione restituisce il valore associato alla chiave passata nel parametro
     * @param null $key
     * @return mixed
     */
    public function get($key = null)
    {
        return $this->adaptor->get($key);
    }

    /**
     * @description Questa funzione controlla se la chiave passata nel parametro esiste o meno nella sessione
     * @param $key
     * @return mixed
     */
    public function has($key = null)
    {
        return $this->adaptor->has($key);
    }

    /**
     * @description Questa funzione permette di settare dati di sessione sotto forma di key=>value con un tempo di scadenza specifico
     * @param $key
     * @param null $value
     * @param $temp
     * @return mixed
     */
    public function setTemp($key = null, $value = null, $temp = null)
    {
        return $this->adaptor->setTemp($key, $value, $temp);
    }


    /**
     * @description Questa funzione restituisce il dato temporaneo presente nella sessione con la chiave passata nel parametro
     * @param null $key
     * @return mixed
     */
    public function getTemp($key = null)
    {
        return $this->adaptor->getTemp($key);
    }

    /**
     * @description Questa funzione permette di settare dati di sessione che saranno disponibili solo per la prossima richiesta, e poi vengono automaticamente cancellati
     * @param $key
     * @param null $value
     * @return mixed
     */
    public function setFlash($key = null, $value = null)
    {
        return $this->adaptor->setFlash($key, $value);
    }

    /**
     * @description Questa funzione restituisce il dato flash presente nella sessione con la chiave passata nel parametro
     * @param null $key
     * @return mixed
     */
    public function getFlash($key = null)
    {
        return $this->adaptor->getFlash($key);
    }

    /**
     * @description Questa funzione restituisce tutti i dati di sessione
     * @return mixed
     */
    public function all()
    {
        return $this->adaptor->all();
    }

    /**
     * @description Questa funzione restituisce tutti i dati di sessione.
     * @param $key
     * @return mixed
     */
    public function kill($key = null)
    {
        return $this->adaptor->kill($key);
    }

    /**
     * @description Questa funzione permette di cancellare la sessione corrente e di conseguenza tutti i dati di sessione
     * @return void
     */
    public function destroy()
    {
        $this->adaptor->destroy();
    }

    /**
     * @description Questa funzione permette di chiudere la sessione
     * @return mixed
     */
    public function close()
    {
        return $this->adaptor->close();
    }

}
