<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

use Exceptio;
use Exception;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class  Validator
{
    protected $value;
    protected $label;
    protected $field;
    protected $file = false;
    protected $typeRequest;
    protected $fileType;
    protected $errors = [];
    protected $errorsField = [];
    protected $standardDateFormat = 'Y-m-d H:i:s';
    protected $hasRequired = false;
    protected $hasError = false;
    protected $hasErrorFile = false;

    /**
     * Validator constructor.
     */
    public function __construct()
    {
    }

    protected function state()
    {

        $passed = true;

        if ($this->value == '' || $this->value == null) {

            $passed = false;
        }

        if(is_array($this->value)) {
            if(count((array)$this->value) == 0) {
                $passed = false;
            }
        } else {
            if (strlen(trim((string)$this->value)) == 0) {

                $passed = false;

            }
        }

        if ($this->value === false || $this->value === 0) {

            $passed = true;
        }

        return $passed;
    }

    /**
     * Nome del campo da analizzare
     *
     * @param null $label
     * @return $this
     */
    public function label($label = null)
    {

        $this->hasError = false;

        $this->hasErrorFile = false;

        $this->label = $label;

        return $this;
    }

    /**
     * ID del campo da analizzare
     *
     * @param null $field
     * @return $this
     */
    public function field($field = null)
    {

        $this->hasError = false;

        $this->hasErrorFile = false;

        $this->field = $field;

        return $this;
    }

    public function getErrorField($field = null)
    {
        return $this->errorsField;
    }

    /**
     * Verifica se il CSRF Token valido o nel metodo GET oppure POST
     *
     * @param null $value
     * @return $this
     */
    public function verifyToken($index=null,$errorMsg = null)
    {
        if (!\System\Token::verify($index)) {

            $this->hasError = true;
            $this->errors[] = $this->setMessageError($errorMsg, 'validator_token');
        }

        return $this;
    }

    /**
     * Valore stringa da analizzare GET|POST|STRING da analizzare
     *
     * @param null $value
     * @return $this
     */
    public function value($value = null)
    {
        $this->hasError = false;

        $this->hasErrorFile = false;

        $this->setTypeRequest('value');

        $this->value = $value;

        return $this;
    }

    /**
     * Valore file da analizzare FILES
     *
     * @param $value
     * @return $this
     */
    public function file($value = null)
    {
        $this->setTypeRequest('file');

        $this->hasErrorFile = false;

        $this->file = $value;

        return $this;
    }


    /**
     * Reset variabili.
     *
     * @return $this
     */
    public function end()
    {
        $this->value = null;
        $this->label = null;
        $this->field = null;
        $this->file = false;
        $this->typeRequest = null;
        $this->fileType = null;
        $this->hasRequired = false;
        $this->hasError = false;
        $this->hasErrorFile = false;

        return $this;
    }

    /**
     * Valore richiesto
     *
     * @param null $errorMsg
     * @return $this
     * @throws Exception
     */
    public function required($errorMsg = null)
    {
        $passed = true;

        if ($this->getTypeRequest() === 'file') {

            $this->hasRequired = true;

            if (!isset($this->file) || $this->file['error'] == 4) {

                $passed = false;
            }
        }

        if ($this->getTypeRequest() === 'value') {

            $passed = $this->state();
        }

        if (!$passed) {

            $this->hasError = true;
            $this->errors[] = $this->setMessageError($errorMsg, 'validator_required');
//            $this->errorsField[] = $this->field;
            $this->errorsField[$this->field][] = $this->setMessageError($errorMsg, 'validator_required');
        }

        return $this;
    }

    /**
     * estensione accettate nel file
     *
     * @param $ext
     * @param null $errorMsg
     * @return $this
     * @throws Exception
     */
    public function allowed($ext, $errorMsg = null)
    {

        $ext = is_array($ext) ? $ext : explode(',', strtolower($ext));

        if ((isset($this->file) &&
            $this->file['error'] != 4) &&
            !in_array(pathinfo($this->file['name'], PATHINFO_EXTENSION), $ext) &&
            !in_array(strtolower(pathinfo($this->file['name'], PATHINFO_EXTENSION)), $ext)
        ) {

            $this->hasErrorFile = false;
            $this->errors[] = $this->setMessageError($errorMsg, 'validator_allowed');
            $this->errorsField[] = $this->field;
        }

        return $this;
    }


    /**
     * Dimensione massima del file
     *
     * @param $size
     * @param null $errorMsg
     * @return $this
     * @throws Exception
     */
    public function maxSize($size = 0, $errorMsg = null)
    {

        $sizeToBytes = $this->toBytes($size);

        if (isset($this->file) && $this->file['error'] != 4 && $this->file['size'] > $sizeToBytes) {

            $this->hasErrorFile = false;

            $msg = ($errorMsg !== null)
                ? sprintf($errorMsg, $this->label, $size)
                : sprintf(__('validator_invalid_max_filesize', $errorMsg, 'langs'), $this->label, $size);

            $this->errors[] = $msg;
            $this->errorsField[] = $this->field;
        }

        return $this;
    }

    /**
     * Dimensione minime del file
     *
     * @param $size
     * @param null $errorMsg
     * @return $this
     * @throws Exception
     */
    public function minSize($size = 0, $errorMsg = null)
    {
        $sizeToBytes = $this->toBytes($size);

        if (isset($this->file) && $this->file['error'] != 4 && $this->file['size'] < $sizeToBytes) {

            $this->hasErrorFile = false;

            $msg = ($errorMsg !== null)
                ? sprintf($errorMsg, $this->label, $size)
                : sprintf(__('validator_invalid_min_filesize', $errorMsg, 'langs'), $this->label, $size);

            $this->errors[] = $msg;
            $this->errorsField[] = $this->field;
        }

        return $this;
    }

    /**
     * Setta le dimensioni minime e massime di un'immagine.
     *
     * @param null $minWidth
     * @param null $maxWidth
     * @param null $minHeight
     * @param null $maxHeight
     * @param null $errorMsg
     * @return $this
     * @throws Exception
     */
    public function allowedDimensions($minWidth = null, $maxWidth = null, $minHeight = null, $maxHeight = null, $errorMsg = null)
    {

        $minWidth = $this->removeAlphaChars($minWidth);
        $maxWidth = $this->removeAlphaChars($maxWidth);
        $minHeight = $this->removeAlphaChars($minHeight);
        $maxHeight = $this->removeAlphaChars($maxHeight);

        if (!$this->isImage() && isset($this->file) && $this->file['error'] != 4) {

            $hasError = false;

            if (function_exists('getimagesize')) {

                $d = @getimagesize($this->file['tmp_name']);

                if ($maxWidth > 0 && $d[0] > $maxWidth) {

                    $hasError = true;
                }

                if ($maxHeight > 0 && $d[1] > $maxHeight) {

                    $hasError = true;
                }

                if ($minWidth > 0 && $d[0] < $minWidth) {

                    $hasError = true;
                }

                if ($minHeight > 0 && $d[1] < $minHeight) {

                    $hasError = true;
                }

                // Stampo l'errore
                if ($hasError == true) {

                    $this->hasErrorFile = false;
                    $this->errors[] = $this->setMessageError($errorMsg, 'validator_invalid_dimensions');
                    $this->errorsField[] = $this->field;
                }
            }
        }

        return $this;
    }

    /**
     * Controlla se il valore passato è un numero intero o un decimale
     *
     * @param null $errorMsg
     * @return $this
     * @throws Exception
     */
    public function isNumeric($errorMsg = null)
    {
        $hasError = true;

        if ($this->state() && !$this->hasError) {

            if ((bool)preg_match_all('/[0-9]+\.?[0-9]*\,?[0-9]*$/', $this->value) == false) {

                $hasError = false;
            }

            if (!$hasError) {

                $this->hasError = true;
                $this->errors[] = $this->setMessageError($errorMsg, 'validator_is_numeric');
                $this->errorsField[] = $this->field;
            }
        }

        return $this;
    }


    /**
     * Controlla la lunghezza minima di una stringa
     *
     * @param $length
     * @param null $errorMsg
     * @return $this
     * @throws Exception
     */
    public function minLength($length = 0, $errorMsg = null)
    {

        $hasError = true;

        if ($this->state() && !$this->hasError) {

            if (!is_numeric($length)) {

                $hasError = false;
            }

            if ($length >= mb_strlen((string) $this->value)) {

                $hasError = false;
            }

            if (!$hasError) {

                $this->hasError = true;
                $this->errors[] = $this->setMessageError($errorMsg, 'validator_min');
                $this->errorsField[] = $this->field;
            }
        }

        return $this;
    }


    /**
     * Controlla la lunghezza massima di una stringa
     *
     * @param $length
     * @param null $errorMsg
     * @return $this
     * @throws Exception
     */
    public function maxLength($length = 0, $errorMsg = null)
    {

        $hasError = true;

        if ($this->state() && !$this->hasError) {

            if (!is_numeric($length)) {

                $hasError = false;
            }

            if ($length <= mb_strlen((string) $this->value)) {

                $hasError = false;
            }

            if (!$hasError) {

                $this->hasError = true;
                $this->errors[] = $this->setMessageError($errorMsg, 'validator_max');
                $this->errorsField[] = $this->field;
            }
        }

        return $this;
    }


    /**
     * Controlla la lunghezza esatta di una stringa
     *
     * @param $length
     * @param null $errorMsg
     * @return $this
     * @throws Exception
     */
    public function exactLength($length, $errorMsg = null)
    {
        $hasError = true;

        if ($this->state() && !$this->hasError) {

            if (!is_numeric($length)) {

                $hasError = false;
            }

            if (mb_strlen((string) $this->value) !== (int)$length) {

                $hasError = false;
            }

            if (!$hasError) {

                $this->hasError = true;

                $msg = ($errorMsg !== null)
                    ? sprintf($errorMsg, $this->label, $length)
                    : sprintf(__('validator_exact', null, 'langs'), $this->label, $length);

                $this->errors[] = $msg;
            }
        }

        return $this;
    }

    /**
     * Verifica se il valore è all'interno di un elenco predeterminato.
     *
     * @param string $list
     * @param null $errorMsg
     * @return $this
     * @throws Exception
     */
    public function in($list = '', $errorMsg = null)
    {
        if ($this->state() && !$this->hasError) {

            if (!in_array($this->value, explode(',', $list), TRUE)) {

                $this->hasError = true;

                $msg = ($errorMsg !== null)
                    ? sprintf($errorMsg, $this->label)
                    : sprintf(__('validator_in_list', null, 'langs'), $this->label, $this->value);

                $this->errors[] = $msg; // $this->setMessageError($msg, 'validator_in_list');

            }
        }

        return $this;
    }


    /**
     * Verifica se il valore non è all'interno di un elenco predeterminato.
     *
     * @param string $list
     * @param null $errorMsg
     * @return $this
     * @throws Exception
     */
    public function notIn($list = '', $errorMsg = null)
    {
        if ($this->state() && !$this->hasError) {

            if (in_array($this->value, explode(',', $list), TRUE)) {

                $this->hasError = true;

                $msg = ($errorMsg !== null)
                    ? sprintf($errorMsg, $this->label)
                    : sprintf(__('validator_not_in_list', null, 'langs'), $this->label, $this->value);

                $this->errors[] = $msg;
            }
        }

        return $this;
    }

    /**
     * Verifica se il valore è un numero naturale
     *
     * @param null $errorMsg
     * @return $this
     * @throws Exception
     */
    public function isNatural($errorMsg = null)
    {

        if ($this->state() && !$this->hasError) {

            if ((bool)ctype_digit((string)$this->value) !== true) {

                $this->hasError = true;

                $this->errors[] = $this->setMessageError($errorMsg, 'validator_natural');
                $this->errorsField[] = $this->field;
            }
        }

        return $this;
    }

    /**
     * Verifica se il valore è un numero naturale escluso lo zero
     *
     * @param null $errorMsg
     * @return $this
     * @throws Exception
     */
    public function isNaturalNoZero($errorMsg = null)
    {
        if ($this->state() && !$this->hasError) {

            if (($this->value != 0 && (bool)ctype_digit((string)$this->value)) !== true) {

                $this->hasError = true;
                $this->errors[] = $this->setMessageError($errorMsg, 'validator_natural_no_zero');
                $this->errorsField[] = $this->field;
            }
        }

        return $this;
    }

    /**
     * Verifica se il valore è un numero intero
     *
     * @param null $errorMsg
     * @return $this
     * @throws Exception
     */
    public function isInt($errorMsg = null)
    {

        if ($this->state() && !$this->hasError) {

            if (!(bool)filter_var($this->value, FILTER_VALIDATE_INT)) {

                $this->hasError = true;

                $msg = ($errorMsg !== null)
                    ? sprintf($errorMsg, $this->label)
                    : sprintf(__('validator_integer', null, 'langs'), $this->label);

                $this->errors[] = $msg;
//                $this->errorsField = $this->field;
                $this->errorsField[$this->field][] = $msg;
            }
        }

        return $this;
    }

    /**
     * Verifica se il valore contiene qualcosa di diverso da un numero decimale
     *
     * @param null $errorMsg
     * @return $this
     * @throws Exception
     */
    public function isDecimal($errorMsg = null)
    {

        if ($this->state() && !$this->hasError) {

            if ((bool)preg_match('/^[\-+]?[0-9]+\.[0-9]+$/', $this->value) == false) {

                $this->hasError = true;

                $this->errors[] = $this->setMessageError($errorMsg, 'validator_decimal');
                $this->errorsField[] = $this->field;
            }
        }

        return $this;
    }

    /**
     * Verifica se il valore è una lettera dell'alfabeto
     *
     * @param null $errorMsg
     * @return $this
     * @throws Exception
     */
    public function isAlpha($errorMsg = null)
    {

        if ($this->state() && !$this->hasError) {

            if (!(bool)filter_var($this->value, FILTER_VALIDATE_REGEXP, ['options' => ['regexp' => "/^[a-zA-Z]+$/"]])) {

                $this->hasError = true;
                $this->errors[] = $this->setMessageError($errorMsg, 'validator_alpha');
                $this->errorsField[] = $this->field;
            }
        }

        return $this;
    }

    /**
     * Verifica se il valore è una lettera o un numero
     *
     * @param null $errorMsg
     * @return $this
     * @throws Exception
     */
    public function isAlphaNum($errorMsg = null)
    {

        if ($this->state() && !$this->hasError) {

            if (!(bool)filter_var($this->value, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[a-zA-Z0-9]+$/")))) {

                $this->hasError = true;
                $this->errors[] = $this->setMessageError($errorMsg, 'validator_alpha_numeric');
                $this->errorsField[] = $this->field;
            }
        }

        return $this;
    }

    /**
     * Verifica se il valore è un url
     *
     * @param null $errorMsg
     * @return $this
     * @throws Exception
     */
    public function isUrl($errorMsg = null, $regex = false)
    {
        if ($this->state() && !$this->hasError) {

            $validateUrl = true;

            if (!$regex) {

                $expRegex = '%^(?:(?:https?|ftp)://)(?:\S+(?::\S*)?@|\d{1,3}(?:\.\d{1,3}){3}|(?:(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)(?:\.(?:[a-z\d\x{00a1}-\x{ffff}]+-?)*[a-z\d\x{00a1}-\x{ffff}]+)*(?:\.[a-z\x{00a1}-\x{ffff}]{2,6}))(?::\d+)?(?:[^\s]*)?$%iu';
                $validateUrl = (bool)preg_match($expRegex, $this->value);
            } else {

                $str = $this->value;

                if (preg_match('/^(?:([^:]*)\:)?\/\/(.+)$/', $str, $matches)) {
                    if (empty($matches[2])) {
                        $validateUrl = false;
                    } elseif (!in_array(strtolower($matches[1]), array('http', 'https'), TRUE)) {
                        $validateUrl = false;
                    }

                    $str = $matches[2];
                }


                if (ctype_digit($str)) {
                    $validateUrl = false;
                }


                if (preg_match('/^\[([^\]]+)\]/', $str, $matches) && !is_php('7') && filter_var($matches[1], FILTER_VALIDATE_IP, FILTER_FLAG_IPV6) !== FALSE) {
                    $str = 'ipv6.host' . substr($str, strlen((string) $matches[1]) + 2);
                }


                if (filter_var('http://' . $str, FILTER_VALIDATE_URL) === false) {
                    $validateUrl = false;
                }
            }


            if (!$validateUrl) {

                $this->hasError = true;
                $this->errors[] = $this->setMessageError($errorMsg, 'validator_url');
                $this->errorsField[] = $this->field;
            }
        }

        return $this;
    }

    /**
     * Verifica se il valore è un uri
     *
     * @param null $errorMsg
     * @return $this|bool
     * @throws Exception
     */
    public function isUri($errorMsg = null)
    {

        if ($this->state() && !$this->hasError) {

            if ((bool)filter_var($this->value, FILTER_VALIDATE_REGEXP, array('options' => array('regexp' => "/^[A-Za-z0-9-\/_]+$/")))) {

                $this->hasError = true;
                $this->errors[] = $this->setMessageError($errorMsg, 'validator_uri');
                $this->errorsField[] = $this->field;
            }
        }

        return $this;
    }

    /**
     * Verifica se il valore è true o false
     *
     * @param null $errorMsg
     * @return $this
     * @throws Exception
     */
    public function isBool($errorMsg = null)
    {

        if ($this->state() && !$this->hasError) {

            if (!is_bool(filter_var($this->value, FILTER_VALIDATE_BOOLEAN, FILTER_NULL_ON_FAILURE))) {

                $this->hasError = true;
                $this->errors[] = $this->setMessageError($errorMsg, 'validator_bool');
                $this->errorsField[] = $this->field;
            }
        }

        return $this;
    }

    /**
     * Verifica se il valore è un'e-mail
     *
     * @param null $errorMsg
     * @return $this
     * @throws Exception
     */
    public function isEmail($errorMsg = null)
    {

        if ($this->state() && !$this->hasError) {

            if (!(bool)filter_var($this->value, FILTER_VALIDATE_EMAIL)) {

                $this->hasError = true;
                $this->errors[] = $this->setMessageError($errorMsg, 'validator_email');
                $this->errorsField[] = $this->field;
            }
        }

        return $this;
    }


    /**
     * Verifica se il valore è un formato data valido.
     *
     * @param null $format
     * @param null $errorMsg
     * @return $this
     * @throws Exception
     */
    public function isDate($format = null, $errorMsg = null)
    {

        if ($this->state() && !$this->hasError) {

            if (is_null($format) || $format == false || $format == '') {

                $format = $this->standardDateFormat;
            }

            if (function_exists('date_parse_from_format')) {

                $parsed = date_parse_from_format($format, $this->value);
            } else {

                $parsed = $this->dateParseFromFormat($format, $this->value);
            }

            if ($parsed['warning_count'] > 0 or $parsed['error_count'] > 0) {

                $this->hasError = true;
                $this->errors[] = $this->setMessageError($errorMsg, 'validator_date');
                $this->errorsField[] = $this->field;
            }
        }

        return $this;
    }

    /**
     * Verifica se il valore è un formato ora valido.
     *
     * @param null $type
     * @param null $errorMsg
     * @return $this|bool
     * @throws Exception
     */
    public function isHour($type = null, $errorMsg = null)
    {

        if ($this->state() && !$this->hasError) {

            if (substr_count($this->value, ':') >= 2) {

                $has_seconds = TRUE;
            } else {

                $has_seconds = FALSE;
            }

            $pattern = "/^" . (($type == '24H')
                ? "([1-2][0-3]|[01]?[1-9])"
                : "(1[0-2]|0?[1-9])") . ":([0-5]?[0-9])" . (($has_seconds) ? ":([0-5]?[0-9])" : "") . (($type == '24H')
                ? ''
                : '( AM| PM| am| pm)') . "$/";

            if ((bool)preg_match($pattern, $this->value) === true) {

                $this->hasError = true;
                $this->errors[] = $this->setMessageError($errorMsg, 'validator_hour');
                $this->errorsField[] = $this->field;
            }
        }

        return $this;
    }

    /**
     * Verifica se la stringa è una base64 Valido
     *
     * @param null $errorMsg
     * @return $this
     * @throws Exception
     */
    public function isBase64($errorMsg = null)
    {

        if ($this->state() && !$this->hasError) {

            if (base64_encode(base64_decode($this->value)) !== $this->value) {

                $this->hasError = true;
                $this->errors[] = $this->setMessageError($errorMsg, 'validator_base');
                $this->errorsField[] = $this->field;
            }
        }

        return $this;
    }

    /**
     * Verifica se solo caratteri alfa-numerico, underscore e punto.
     *
     * @param null $errorMsg
     * @return $this
     * @throws Exception
     */
    public function isAlphaDash($errorMsg = null)
    {

        if ($this->state() && !$this->hasError) {

            if ((bool)preg_match('/^[a-z0-9_-]+$/i', $this->value) === false) {

                $this->hasError = true;
                $this->errors[] = $this->setMessageError($errorMsg, 'validator_alpha_dash');
                $this->errorsField[] = $this->field;
            }
        }

        return $this;
    }

    /**
     * Verifica se solo caratteri alfa-numerici e di spaziatura.
     *
     * @param null $errorMsg
     * @return $this
     * @throws Exception
     */
    public function isAlphaNumSpaces($errorMsg = null)
    {

        if ($this->state() && !$this->hasError) {

            if ((bool)preg_match('/^[A-Z0-9 ]+$/i', $this->value) === false) {

                $this->hasError = true;
                $this->errors[] = $this->setMessageError($errorMsg, 'validator_alpha_num_spaces');
                $this->errorsField[] = $this->field;
            }
        }

        return $this;
    }

    /**
     * Verifica se la stringa è un mac address valido.
     *
     * @param null $errorMsg
     * @return $this
     * @throws Exception
     */
    public function isMacAddress($errorMsg = null)
    {

        if ($this->state() && !$this->hasError) {

            if (!(bool)filter_var($this->value, FILTER_VALIDATE_MAC)) {

                $this->hasError = true;
                $this->errors[] = $this->setMessageError($errorMsg, 'validator_mac_address');
                $this->errorsField[] = $this->field;
            }
        }

        return $this;
    }

    /**
     * Verifica se un numero di carta di credito è valida
     *
     * @param $type
     * @param null $errorMsg
     * @return $this
     * @throws Exception
     */
    public function isCreditCard($type = null, $errorMsg = null)
    {

        $hasError = true;

        if ($this->state() && !$this->hasError) {

            if (($this->value = preg_replace('/\D+/', '', $this->value)) === '') {

                $hasError = false;
            }

            if ($type == NULL) {

                $type = 'default';
            } elseif (is_array($type)) {

                foreach ($type as $t) {

                    if ($this->isCreditCard($t, $errorMsg)) {
                        return TRUE;
                    }
                }

                return FALSE;
            }

            $cards = [
                'default' => [
                    'length' => '13,14,15,16,17,18,19',
                    'prefix' => '',
                    'luhn' => true
                ],
                'american express' => [
                    'length' => '15',
                    'prefix' => '3[47]',
                    'luhn' => true
                ],
                'diners club' => [
                    'length' => '14,16',
                    'prefix' => '36|55|30[0-5]',
                    'luhn' => true
                ],
                'discover' => [
                    'length' => '16',
                    'prefix' => '6(?:5|011)',
                    'luhn' => true,
                ],
                'jcb' => [
                    'length' => '15,16',
                    'prefix' => '3|1800|2131',
                    'luhn' => true
                ],
                'maestro' => [
                    'length' => '16,18',
                    'prefix' => '50(?:20|38)|6(?:304|759)',
                    'luhn' => true
                ],
                'mastercard' => [
                    'length' => '16',
                    'prefix' => '5[1-5]',
                    'luhn' => true
                ],
                'visa' => [
                    'length' => '13,16',
                    'prefix' => '4',
                    'luhn' => true
                ],
            ];


            $type = strtolower($type);

            if (!isset($cards[$type])) {
                $hasError = false;
            }

            $length = strlen($this->value);

            if (!in_array($length, preg_split('/\D+/', $cards[$type]['length']))) {
                $hasError = false;
            }

            if (!preg_match('/^' . $cards[$type]['prefix'] . '/', $this->value)) {
                $hasError = false;
            }

            if ($cards[$type]['luhn'] == FALSE) {
                $hasError = false;
            }

            $checksum = 0;

            for ($i = $length - 1; $i >= 0; $i -= 2) {

                $checksum += substr($this->value, $i, 1);
            }

            for ($i = $length - 2; $i >= 0; $i -= 2) {

                $double = substr($this->value, $i, 1) * 2;

                $checksum += ($double >= 10) ? $double - 9 : $double;
            }


            if ($checksum % 10 !== 0) {

                $hasError = false;
            }

            if (!$hasError) {

                $this->hasError = true;
                $this->errors[] = $this->setMessageError($errorMsg, 'validator_credit_card');
                $this->errorsField[] = $this->field;
            }
        }

        return $this;
    }

    /**
     * Verifica se la stringa passatta rientra nei parametri di accettazione
     *
     * @param null $errorMsg
     * @return $this
     * @throws Exception
     */
    public function accept($errorMsg = null)
    {

        $acceptables = ['yes', 'y', 'on', '1', 1, true, 'true'];

        if ($this->state() && !$this->hasError) {

            if (!in_array($this->value, $acceptables, true)) {

                $this->hasError = true;
                $this->errors[] = $this->setMessageError($errorMsg, 'validator_accept');
                $this->errorsField[] = $this->field;
            }
        }

        return $this;
    }

    /**
     * Verifica se la stringa passatta rientra nei parametro minimo e massimo
     *
     * @param int $min
     * @param int $max
     * @param null $errorMsg
     * @return $this
     * @throws Exception
     */
    public function betweenString($min = 0, $max = 100000, $errorMsg = null)
    {

        if ($this->state() && !$this->hasError) {

            if (strlen($this->value) < $min || strlen($this->value) > $max) {

                $this->hasError = true;

                $msg = ($errorMsg !== null)
                    ? sprintf($errorMsg, $this->label, $min, $max)
                    : sprintf(__('validator_between_string', null, 'langs'), $this->label, $min, $max);

                $this->errors[] = $msg;
            }
        }

        return $this;
    }

    /**
     * Verifica se la stringa è un indirizzo IP valido.
     *
     * @param null $errorMsg
     * @return $this
     * @throws Exception
     */
    public function isIp($errorMsg = null)
    {

        if ($this->state() && !$this->hasError) {

            if (!filter_var($this->value, FILTER_VALIDATE_IP) !== false) {

                $this->hasError = true;
                $this->errors[] = $this->setMessageError($errorMsg, 'validator_ip');
                $this->errorsField[] = $this->field;
            }
        }

        return $this;
    }

    /**
     * Verifica se la stringa passata rispecchia il pattern impostato nell'espressione regolare.
     *
     * @param $pattern
     * @param null $errorMsg
     * @return $this
     * @throws Exception
     */
    public function regex($pattern, $errorMsg = null)
    {

        if ($this->state() && !$this->hasError) {

            if (!(bool)preg_match($pattern, $this->value)) {

                $this->hasError = true;
                $this->errors[] = $this->setMessageError($errorMsg, 'validator_regex');
                $this->errorsField[] = $this->field;
            }
        }

        return $this;
    }

    /**
     * Verifica se le due stringhe sono diverse.
     *
     * @param $value
     * @param $label
     * @param null $errorMsg
     * @return $this
     * @throws Exception
     */
    public function isDiffers($value = null, $label = null, $errorMsg = null)
    {

        if ($this->state() && !$this->hasError) {

            $passed = (!(isset($value) && $this->value === $value));

            if ($passed === true) {

                $this->hasError = true;

                $msg = ($errorMsg !== null)
                    ? sprintf($errorMsg, $this->label, $label)
                    : sprintf(__('validator_differs', null, 'langs'), $this->label, $label);

                $this->errors[] = $msg;
            }
        }

        return $this;
    }

    /**
     * Verifica se le due stringhe sono uguali.
     *
     * @param $value
     * @param $label
     * @param null $errorMsg
     * @return $this
     * @throws Exception
     */
    public function isMatches($value = null, $label = null, $errorMsg = null)
    {

        if ($this->state() && !$this->hasError) {

            $passed = (isset($value) && ($this->value === $value)) ? true : false;

            if ($passed !== true) {

                $this->hasError = true;

                $msg = ($errorMsg !== null)
                    ? sprintf($errorMsg, $this->label, $label)
                    : sprintf(__('validator_match', null, 'langs'), $this->label, $label);

                $this->errors[] = $msg;
            }
        }

        return $this;
    }

    /**
     * Verifica se il valore passato è maggiore al numero impostato nella variabile $min
     *
     * @param int $min
     * @param null $errorMsg
     * @return $this
     * @throws Exception
     */
    public function isGreaterThan($min = 0, $errorMsg = null)
    {

        if ($this->state() && !$this->hasError) {

            $passed = true;

            if (!is_numeric($this->value)) {

                $passed = false;
            }

            if ($passed === true && ($this->value <= $min)) {


                $passed = false;
            }

            if (!$passed) {

                $this->hasError = true;

                $msg = ($errorMsg !== null)
                    ? sprintf($errorMsg, $this->label, $min)
                    : sprintf(__('validator_greater_than', null, 'langs'), $this->label, $min);

                $this->errors[] = $msg;
            }
        }

        return $this;
    }

    /**
     * Verifica se il valore passato è maggiore o uguale al numero impostato nella variabile $min
     *
     * @param int $min
     * @param null $errorMsg
     * @return $this
     * @throws Exception
     */
    public function isGreaterThanOrEqual($min = 0, $errorMsg = null)
    {

        if ($this->state() && !$this->hasError) {

            $passed = true;

            if (!is_numeric($this->value)) {

                $passed = false;
            }

            if ($passed === true && ($this->value < $min)) {

                $passed = false;
            }

            if (!$passed) {

                $this->hasError = true;

                $msg = ($errorMsg !== null)
                    ? sprintf($errorMsg, $this->label, $min)
                    : sprintf(__('validator_greater_than_or_equal', null, 'langs'), $this->label, $min);

                $this->errors[] = $msg;
            }
        }

        return $this;
    }

    /**
     * Verifica se il valore passato è minore del numero impostato nella variabile $max
     *
     * @param int $max
     * @param null $errorMsg
     * @return $this
     * @throws Exception
     */
    public function isLessThan($max = 0, $errorMsg = null)
    {

        if ($this->state() && !$this->hasError) {

            $passed = true;

            if (!is_numeric($this->value)) {

                $passed = false;
            }

            if ($passed === true && ($this->value >= $max)) {


                $passed = false;
            }

            if (!$passed) {

                $this->hasError = true;

                $msg = ($errorMsg !== null)
                    ? sprintf($errorMsg, $this->label, $max)
                    : sprintf(__('validator_less_than', null, 'langs'), $this->label, $max);

                $this->errors[] = $msg;
            }
        }

        return $this;
    }

    /**
     * Verifica se il valore passato è minore del numero impostato nella variabile $max
     *
     * @param int $max
     * @param null $errorMsg
     * @return $this
     * @throws Exception
     */
    public function isLessThanOrEqual($max = 0, $errorMsg = null)
    {

        if ($this->state() && !$this->hasError) {

            $passed = true;

            if (!is_numeric($this->value)) {

                $passed = false;
            }

            if ($passed === true && ($this->value > $max)) {

                $passed = false;
            }

            if (!$passed) {

                $this->hasError = true;

                $msg = ($errorMsg !== null)
                    ? sprintf($errorMsg, $this->label, $max)
                    : sprintf(__('validator_less_than_or_equal', null, 'langs'), $this->label, $max);

                $this->errors[] = $msg;
            }
        }

        return $this;
    }

    /**
     * funzione che estende in modo astratto la classe validator
     *
     * @param $callback
     * @param null $errorMsg
     * @return $this
     */
    public function add($callback, $errorMsg = null)
    {
        if ($this->state() && !$this->hasError) {

            $arguments = [
                $this->label,
                $this->value,
                $errorMsg
            ];

            $result = call_user_func_array($callback, $arguments);

            if (!empty($result['error'])) {

                $this->hasError = true;

                $error = !empty($errorMsg)
                    ? $errorMsg
                    : $result['error'];

                $this->errors[] = $error;
                $this->errorsField[] = $this->field;
            }
        }

        return $this;
    }

    /**
     * Ritorna true se la validazioen è andata a buon fine in caso contrario falso
     *
     * @return bool
     */
    public function isSuccess()
    {
        return !empty($this->errors) ? false : true;
    }

    /**
     * Ritorna l'errore del valore non validato o null se tutti i valori sono validati
     *
     * @return array|null
     */
    public function getErrors()
    {
        return ($this->isSuccess() == false) ? $this->errors : null;
    }

    /**
     * Ritorna un elemento html della lista dei valori non validati
     *
     * @return string
     */
    public function getErrorsHtml()
    {
        $html = '';
        if ($this->isSuccess() === false) {
            $html = '<ul>';
            foreach ($this->getErrors() as $error) {
                $html .= '<li>' . $error . '</li>';
            }
            $html .= '</ul>';
        }
        return $html;
    }

    /**
     * Setta il messaggio di errore
     *
     * @param null $errorMsg
     * @param null $labelError
     * @return null|string
     * @throws Exception
     */
    protected function setMessageError($errorMsg = null, $labelError = null)
    {
        return ($errorMsg !== null)
            ? $errorMsg
            : sprintf(__($labelError, null, 'langs'), $this->label, $this->value);
    }

    /**
     * Setta il tipo di richiesta
     *
     * @return void
     */
    protected function setTypeRequest($typeRequest)
    {
        $this->typeRequest = $typeRequest;
    }

    /**
     * Ritorna il tipo di richiesta
     *
     * @return mixed
     */
    protected function getTypeRequest()
    {
        return $this->typeRequest;
    }

    /**
     * conversione grandezza in byte
     *
     * @param $from
     * @return float|int|null|string|string[]
     */
    protected function toBytes($from)
    {
        $units = ['B', 'KB', 'MB', 'GB', 'TB', 'PB'];
        $number = substr($from, 0, -2);
        $suffix = strtoupper(substr($from, -2));

        if (is_numeric(substr($suffix, 0, 1))) {
            return preg_replace('/[^\d]/', '', $from);
        }

        $exponent = array_flip($units)[$suffix] ?? null;
        if ($exponent === null) {
            return null;
        }

        return $number * (1024 ** $exponent);
    }

    /**
     * Verifica se il file è un'immagine.
     *
     * @return bool
     */
    protected function isImage()
    {
        $pngMimes = ['image/x-png'];
        $jpegMimes = ['image/jpg', 'image/jpe', 'image/jpeg', 'image/pjpeg'];

        if (in_array($this->fileType, $pngMimes)) {

            $this->fileType = 'image/png';
        } elseif (in_array($this->fileType, $jpegMimes)) {

            $this->fileType = 'image/jpeg';
        }

        $imgMimes = ['image/gif', 'image/jpeg', 'image/png'];

        return in_array($this->fileType, $imgMimes, TRUE);
    }

    /**
     * Mantiene solamente caratteri numerici
     *
     * @param null $str
     * @return null|string|string[]
     */
    protected function removeAlphaChars($str = null)
    {

        return preg_replace('/[^0-9]/', '', $str);
    }

    /**
     * Verifica se la data da analizzare è valida.
     *
     * @param $format
     * @param $date
     * @return array
     */
    protected function dateParseFromFormat($format, $date)
    {
        $keys = [
            'Y' => ['year', '\d{4}'],
            'y' => ['year', '\d{2}'],
            'm' => ['month', '\d{2}'],
            'n' => ['month', '\d{1,2}'],
            'M' => ['month', '[A-Z][a-z]{3}'],
            'F' => ['month', '[A-Z][a-z]{2,8}'],
            'd' => ['day', '\d{2}'],
            'j' => ['day', '\d{1,2}'],
            'D' => ['day', '[A-Z][a-z]{2}'],
            'l' => ['day', '[A-Z][a-z]{6,9}'],
            'u' => ['hour', '\d{1,6}'],
            'h' => ['hour', '\d{2}'],
            'H' => ['hour', '\d{2}'],
            'g' => ['hour', '\d{1,2}'],
            'G' => ['hour', '\d{1,2}'],
            'i' => ['minute', '\d{2}'],
            's' => ['second', '\d{2}']
        ];

        $regex = '';
        $chars = str_split($format);

        foreach ($chars as $n => $char) {

            $lastChar = isset($chars[$n - 1]) ? $chars[$n - 1] : '';
            $skipCurrent = '\\' == $lastChar;

            if (!$skipCurrent && isset($keys[$char])) {

                $regex .= '(?P<' . $keys[$char][0] . '>' . $keys[$char][1] . ')';
            } else if ('\\' == $char) {

                $regex .= $char;
            } else {

                $regex .= preg_quote($char);
            }
        }

        $dt = [];

        if (preg_match('#^' . $regex . '$#', $date, $dt)) {

            foreach ($dt as $k => $v) {

                if (is_int($k)) {

                    unset($dt[$k]);
                }
            }

            if (!checkdate($dt['month'], $dt['day'], $dt['year'])) {

                $dt['error_count'] = 1;
            } else {

                $dt['error_count'] = 0;
            }
        } else {

            $dt['error_count'] = 1;
        }

        $dt['errors'] = [];
        $dt['fraction'] = '';
        $dt['warning_count'] = 0;
        $dt['warnings'] = [];
        $dt['is_localtime'] = 0;
        $dt['zone_type'] = 0;
        $dt['zone'] = 0;
        $dt['is_dst'] = '';

        return $dt;
    }
}