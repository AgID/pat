<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');


/**
 * Class JsonResponseEncoder
 * Classe per la gestione dei messaggi JSON
 *
 * @package System
 */
class JsonResponse
{
    private $data = [];
    private $errors = [];
    private $token = null;
    private $statusCode = 200;

    /**
     * Imposta un errore sull'oggetto errori
     *
     * @param $key
     * @param null $value
     * @return JsonResponse
     */
    public function setError($key, $value = null)
    {
        return $this->error($key, $value);
    }

    /**
     * Imposta i messaggi di errore della richiesta json.
     *
     * @param $key
     * @param null $value
     * @return $this
     */
    public function error($key, $value = null)
    {
        if (is_array($key)) {
            return $this->mergeErrors($key);
        }

        if (is_null($value)) {
            $this->errors[] = $key;
        } else {
            $this->errors[$key] = $value;
        }

        return $this;
    }

    /**
     * Unisce l'array di errori con gli errori nella risposta json
     *
     * @param array $errors
     * @return $this
     */
    public function mergeErrors(array $errors)
    {
        $this->errors = array_merge($this->errors, $errors);
        return $this;
    }

    /**
     * Concatena l'array degli input con l'array degli errori
     *
     * @param array $errors
     * @return $this
     */
    public function addErrors(array $errors)
    {
        $this->errors = $this->errors + $errors;
        return $this;
    }

    /**
     * Restituisce i dati di errore presenti nell'array degli errori della classe
     *
     * @return array
     */
    public function getErrors()
    {
        return $this->errors;
    }

    /**
     * Imposta una proprietà dati sull'oggetto
     *
     * @param string $key
     * @param mixed $value
     * @return $this
     */
    public function set($key, $value = null)
    {
        if (is_array($key)) {

            $this->merge($key);

        } else {

            $this->data[$key] = $value;

        }


        return $this;
    }

    /**
     * Ottiene i dati della classe
     *
     * @return array
     */
    public function getData()
    {
        return $this->data;
    }

    /**
     * Imposta il codice di stato della richiesta
     *
     * @param $code
     * @return $this
     */
    public function setStatusCode($code)
    {
        $this->statusCode = $code;
        return $this;
    }

    /**
     * Restituisce il codice di stato della richiesta
     *
     * @return int
     */
    public function getStatusCode()
    {
        return $this->statusCode;
    }

    /**
     * Aggiunge un elemento all'elenco dei dati
     *
     * @param mixed $value
     * @return $this
     */
    public function add($value)
    {
        $this->data[] = $value;

        return $this;
    }

    /**
     * Unisce una matrice di dati nella risposta
     *
     * @param array $data
     * @return $this
     */
    public function merge(array $data)
    {
        $this->data = array_merge($this->data, $data);
        return $this;
    }

    /**
     * Rimuove un errore dall'elenco degli errori
     *
     * @param $key
     * @return $this
     */
    public function deleteError($key)
    {
        unset($this->errors[$key]);
        return $this;
    }

    /**
     * Rimuove alcuni dati nel JSON
     *
     * @param $key
     * @return $this
     */
    public function delete($key)
    {
        unset($this->data[$key]);
        return $this;
    }

    /**
     * Restituisce l'oggetto come stringa codificata in JSON
     *
     * @return string
     */
    public function __toString()
    {
        return $this->toString();
    }

    /**
     * Restituisce la matrice codificata JSON dell'oggetto
     *
     * @return string
     */
    public function toString()
    {
        return json_encode($this->toArray());
    }

    /**
     * Converte l'oggetto nell'array leggibile
     *
     * @return array
     */
    public function toArray()
    {
        $data = [
            'data' => $this->data,
            'errors' => $this->errors,
            'success' => $this->isSuccess(),
            'status_code' => $this->statusCode,
            'response_date' => date('Y-m-d H:i:s')
        ];

        if (!is_null($this->token)) {
            $data['token'] = $this->token;
        }

        return $data;
    }

    /**
     * Controlla se l'oggetto ha esito positivo
     *
     * @return bool
     */
    public function isSuccess()
    {
        return count($this->errors) === 0 && $this->statusCode >= 200 && $this->statusCode < 300;
    }

    /**
     * Controlla se la risposta contiene errori
     *
     * @return bool
     */
    public function hasErrors()
    {
        return count($this->errors) > 0;
    }

    /**
     * Ottiene il token di risposta
     *
     * @return null
     */
    public function getToken()
    {
        return $this->token;
    }

    /**
     * Imposta il token della richiesta JSON
     *
     * @param $token
     * @return $this
     */
    public function setToken($token)
    {
        $this->token = $token;
        return $this;
    }

    /**
     * Questo metodo controlla se l'oggetto ha settato o meno il token
     *
     * @return bool
     */
    public function hasToken()
    {
        return isset($this->token);
    }

    /**
     * Ritorna la notazione standard delle riposte REST in formato Json
     *
     * @param bool $send
     */
    public function response($send = true)
    {
        $response = new Response();
        $response->setHeader('Content-Type', 'application/json');
        $response->setStatus($this->statusCode);
        $response->body($this->toString());
        $response->send($send);
        die();
    }

    /**
     * @return mixed
     */
    public function success()
    {
        $response = new Response();
        return $response::SUCCESS;
    }

    /**
     * @return mixed
     */
    public function bad()
    {
        $response = new Response();
        return $response::BAD;
    }

    public function unauthorized()
    {
        $response = new Response();
        return $response::UNAUTHORIZED;
    }
}
