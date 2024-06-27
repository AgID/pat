<?php
/**
 * Fuel is a fast, lightweight, community driven PHP 5.4+ framework.
 *
 * @package    Fuel
 * @version    1.9-dev
 * @author     Fuel Development Team
 * @license    MIT License
 * @copyright  2010 - 2019 Fuel Development Team
 * @link       https://fuelphp.com
 */
namespace System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class Response
{
    const BAD = 400;
    const SUCCESS = 200;
    const ERROR = 500;
    const CREATED = 201;
    const ACCEPTS = 202;
    const FOUND = 302;
    const FORBIDDEN = 403;
    const UNAUTHORIZED = 401;
    const NOT_FOUND = 404;

    public $status = 200;
    public $headers = array();
    public $body = null;
    public static $statuses = [
        100 => 'Continue',
        101 => 'Switching Protocols',
        102 => 'Processing',
        200 => 'OK',
        201 => 'Created',
        202 => 'Accepted',
        203 => 'Non-Authoritative Information',
        204 => 'No Content',
        205 => 'Reset Content',
        206 => 'Partial Content',
        207 => 'Multi-Status',
        208 => 'Already Reported',
        226 => 'IM Used',
        300 => 'Multiple Choices',
        301 => 'Moved Permanently',
        302 => 'Found',
        303 => 'See Other',
        304 => 'Not Modified',
        305 => 'Use Proxy',
        307 => 'Temporary Redirect',
        308 => 'Permanent Redirect',
        400 => 'Bad Request',
        401 => 'Unauthorized',
        402 => 'Payment Required',
        403 => 'Forbidden',
        404 => 'Not Found',
        405 => 'Method Not Allowed',
        406 => 'Not Acceptable',
        407 => 'Proxy Authentication Required',
        408 => 'Request Timeout',
        409 => 'Conflict',
        410 => 'Gone',
        411 => 'Length Required',
        412 => 'Precondition Failed',
        413 => 'Request Entity Too Large',
        414 => 'Request-URI Too Long',
        415 => 'Unsupported Media Type',
        416 => 'Requested Range Not Satisfiable',
        417 => 'Expectation Failed',
        418 => 'I\'m a Teapot',
        422 => 'Unprocessable Entity',
        423 => 'Locked',
        424 => 'Failed Dependency',
        426 => 'Upgrade Required',
        428 => 'Precondition Required',
        429 => 'Too Many Requests',
        431 => 'Request Header Fields Too Large',
        500 => 'Internal Server Error',
        501 => 'Not Implemented',
        502 => 'Bad Gateway',
        503 => 'Service Unavailable',
        504 => 'Gateway Timeout',
        505 => 'HTTP Version Not Supported',
        506 => 'Variant Also Negotiates',
        507 => 'Insufficient Storage',
        508 => 'Loop Detected',
        509 => 'Bandwidth Limit Exceeded',
        510 => 'Not Extended',
        511 => 'Network Authentication Required',
    ];


    public function __construct($body = null, $status = 200, array $headers = array())
    {
        foreach ($headers as $k => $v) {
            $this->setHeader($k, $v);
        }
        $this->body = $body;
        $this->status = $status;
    }

    public function setStatus($status = 200)
    {
        $this->status = $status;
        return $this;
    }

    public function getStatusCode()
    {
        return $this->status;
    }


    public function setHeader($name, $value, $replace = true)
    {
        if ($replace) {

            $this->headers[$name] = $value;

        } else {

            $this->headers[] = array($name, $value);

        }

        return $this;
    }


    public function setHeaders($headers, $replace = true)
    {
        foreach ($headers as $key => $value) {

            $this->setHeader($key, $value, $replace);

        }

        return $this;
    }


    public function getHeader($name = null)
    {
        if (func_num_args()) {

            return isset($this->headers[$name]) ? $this->headers[$name] : null;

        } else {

            return $this->headers;

        }

    }


    public function body($value = false)
    {
        if (func_num_args()) {

            $this->body = $value;

            return $this;

        }

        return $this->body;
    }


    public function sendHeaders()
    {
        if (!headers_sent()) {

            if (!empty($_SERVER['FCGI_SERVER_VERSION'])) {

                header('Status: ' . $this->status . ' ' . static::$statuses[$this->status]);

            } else {

                $protocol = Input::server('SERVER_PROTOCOL') ? Input::server('SERVER_PROTOCOL', true) : 'HTTP/1.1';
                header($protocol . ' ' . $this->status . ' ' . static::$statuses[$this->status]);

            }

            foreach ($this->headers as $name => $value) {

                if (is_int($name) and is_array($value)) {

                    isset($value[0]) and $name = $value[0];
                    isset($value[1]) and $value = $value[1];

                }

                is_string($name) and $value = "{$name}: {$value}";

                header($value, true);

            }
            return true;

        }
        return false;
    }


    public function send($sendHeaders = false, $returnString = true)
    {
        if ($returnString === true) {

            $body = $this->__toString();

        }

        if ($sendHeaders) {

            $this->sendHeaders();

        }

        if ($this->body !== null) {

            if ($returnString === true) {

                echo $body;

            } else {

                return $body;

            }

        }
    }

    public function __toString()
    {

        return (string)$this->body;

    }
}
