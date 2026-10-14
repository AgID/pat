<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

/**
 * CodeIgniter
 *
 * An open source application development framework for PHP
 *
 * This content is released under the MIT License (MIT)
 *
 * Copyright (c) 2019 - 2022, CodeIgniter Foundation
 *
 * Permission is hereby granted, free of charge, to any person obtaining a copy
 * of this software and associated documentation files (the "Software"), to deal
 * in the Software without restriction, including without limitation the rights
 * to use, copy, modify, merge, publish, distribute, sublicense, and/or sell
 * copies of the Software, and to permit persons to whom the Software is
 * furnished to do so, subject to the following conditions:
 *
 * The above copyright notice and this permission notice shall be included in
 * all copies or substantial portions of the Software.
 *
 * THE SOFTWARE IS PROVIDED "AS IS", WITHOUT WARRANTY OF ANY KIND, EXPRESS OR
 * IMPLIED, INCLUDING BUT NOT LIMITED TO THE WARRANTIES OF MERCHANTABILITY,
 * FITNESS FOR A PARTICULAR PURPOSE AND NONINFRINGEMENT. IN NO EVENT SHALL THE
 * AUTHORS OR COPYRIGHT HOLDERS BE LIABLE FOR ANY CLAIM, DAMAGES OR OTHER
 * LIABILITY, WHETHER IN AN ACTION OF CONTRACT, TORT OR OTHERWISE, ARISING FROM,
 * OUT OF OR IN CONNECTION WITH THE SOFTWARE OR THE USE OR OTHER DEALINGS IN
 * THE SOFTWARE.
 *
 * @package	CodeIgniter
 * @author	EllisLab Dev Team
 * @copyright	Copyright (c) 2008 - 2014, EllisLab, Inc. (https://ellislab.com/)
 * @copyright	Copyright (c) 2014 - 2019, British Columbia Institute of Technology (https://bcit.ca/)
 * @copyright	Copyright (c) 2019 - 2022, CodeIgniter Foundation (https://codeigniter.com/)
 * @license	https://opensource.org/licenses/MIT	MIT License
 * @link	https://codeigniter.com
 * @since	Version 1.0.0
 * @filesource
 */

namespace System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class Email
{

    public $useragent = 'Email';
    public $mailpath = '/usr/sbin/sendmail';
    public $protocol = 'mail';
    public $smtp_host = '';
    public $smtp_user = '';
    public $smtp_from = '';
    public $smtp_pass = '';
    public $smtp_port = 25;
    public $smtp_timeout = 5;
    public $smtp_keepalive = FALSE;
    public $smtp_crypto = '';
    public $smtp_auth = NULL;
    public $wordwrap = TRUE;
    public $wrapchars = 76;
    public $mailtype = 'text';
    public $charset = 'UTF-8';
    public $multipart = 'mixed';
    public $alt_message = '';
    public $validate = FALSE;
    public $priority = 3;
    public $newline = "\n";
    public $crlf = "\n";
    public $send_multipart = TRUE;
    public $bcc_batch_mode = FALSE;
    public $bcc_batch_size = 200;
    public $dsn = FALSE;
    public $_debug_enabled = FALSE;
    protected $_subject = '';
    protected $_body = '';
    protected $_finalbody = '';
    protected $_header_str = '';
    protected $_smtp_connect = '';
    protected $_encoding = '8bit';
    protected $_smtp_auth = FALSE;
    protected $_replyto_flag = FALSE;
    protected $_debug_msg = array();
    protected $_recipients = array();
    protected $_cc_array = array();
    protected $_bcc_array = array();
    protected $_headers = array();

    protected $_attachments = array();
    protected $_protocols = array('mail', 'sendmail', 'smtp');
    protected $_base_charsets = array('us-ascii', 'iso-2022-');
    protected $_bit_depths = array('7bit', '8bit');
    protected $_priorities = array(
        1 => '1 (Highest)',
        2 => '2 (High)',
        3 => '3 (Normal)',
        4 => '4 (Low)',
        5 => '5 (Lowest)'
    );
    protected static $func_overload;


    protected $debug = false;

    public function __construct(array $config = array())
    {
        $this->charset = CHARSET;
        $this->initialize($config);

        isset(self::$func_overload) || self::$func_overload = (! $this->isPhp('8.0') && extension_loaded('mbstring') && @ini_get('mbstring.func_overload'));
    }

    public function initialize(array $config = array())
    {
        $this->clear();

        foreach ($config as $key => $val) {
            if (isset($this->$key)) {
                $method = 'set_' . $key;

                if (method_exists($this, $method)) {
                    $this->$method($val);
                } else {
                    $this->$key = $val;
                }
            }
        }

        if (isset($config['debug'])) {
            $this->set_debug($config['debug']);
        }

        $this->charset = strtoupper($this->charset);

        if (!isset($config['protocol']) && $this->protocol === 'mail') {
            $this->protocol = 'mail';
        }

        $this->_smtp_auth = isset($this->smtp_user[0], $this->smtp_pass[0]);

        if (isset($config['smtp_auth'])) {
            $this->_smtp_auth = (bool)$config['smtp_auth'];
        }

        return $this;
    }

    public function is_smtp_valid()
    {
        $current_debug = $this->debug;

        $this->debug = true;

        try {
            $connection_resource = $this->_smtp_connect();
            $is_connected = $connection_resource !== false;
        } catch (\Exception $e) {
            $is_connected = false;
        }

        $this->debug = $current_debug;

        if ($is_connected) {
            $this->_send_command('quit');

            if (is_resource($connection_resource)) {
                fclose($connection_resource);
            }

            return true;
        }

        return false;
    }

    public function clear($clear_attachments = FALSE)
    {
        $this->_subject = '';
        $this->_body = '';
        $this->_finalbody = '';
        $this->_header_str = '';
        $this->_replyto_flag = FALSE;
        $this->_recipients = array();
        $this->_cc_array = array();
        $this->_bcc_array = array();
        $this->_headers = array();
        $this->_debug_msg = array();

        $this->set_header('Date', $this->_set_date());

        if ($clear_attachments !== FALSE) {
            $this->_attachments = array();
        }

        return $this;
    }

    public function from($from, $name = '', $return_path = NULL)
    {
        /*
         * Per le configurazioni SMTP dell'Ente il mittente puo essere distinto
         * dall'utenza usata per autenticarsi. Se non configurato, viene mantenuto
         * il valore passato dal chiamante, che storicamente coincide con lo username.
         */
        if (!empty($this->smtp_from)) {
            $from = $this->smtp_from;
        }

        if (preg_match('/\<(.*)\>/', $from, $match)) {
            $from = $match[1];
        }

        if ($this->validate) {
            $this->validate_email($this->_str_to_array($from));
            if ($return_path) {
                $this->validate_email($this->_str_to_array($return_path));
            }
        }

        if ($name !== '') {
            if (!preg_match('/[\200-\377]/', $name)) {
                $name = '"' . addcslashes($name, "\0..\37\177'\"\\") . '"';
            } else {
                $name = $this->_prep_q_encoding($name);
            }
        }

        $this->set_header('From', $name . ' <' . $from . '>');

        isset($return_path) or $return_path = $from;
        $this->set_header('Return-Path', '<' . $return_path . '>');

        return $this;
    }

    public function reply_to($replyto, $name = '')
    {
        if (preg_match('/\<(.*)\>/', $replyto, $match)) {
            $replyto = $match[1];
        }

        if ($this->validate) {
            $this->validate_email($this->_str_to_array($replyto));
        }

        if ($name !== '') {
            if (!preg_match('/[\200-\377]/', $name)) {
                $name = '"' . addcslashes($name, "\0..\37\177'\"\\") . '"';
            } else {
                $name = $this->_prep_q_encoding($name);
            }
        }

        $this->set_header('Reply-To', $name . ' <' . $replyto . '>');
        $this->_replyto_flag = TRUE;

        return $this;
    }

    public function to($to)
    {
        $to = $this->_str_to_array($to);
        $to = $this->clean_email($to);

        if ($this->validate) {
            $this->validate_email($to);
        }

        if ($this->_get_protocol() !== 'mail') {
            $this->set_header('To', implode(', ', $to));
        }

        $this->_recipients = $to;

        return $this;
    }

    public function cc($cc)
    {
        $cc = $this->clean_email($this->_str_to_array($cc));

        if ($this->validate) {
            $this->validate_email($cc);
        }

        $this->set_header('Cc', implode(', ', $cc));

        $protocol = $this->_get_protocol();
        if ($this->_debug_enabled) {
            $this->_debug_msg[] = "=== CC DEBUG ===\n";
            $this->_debug_msg[] = "Protocollo rilevato in cc(): " . $protocol . "\n";
            $this->_debug_msg[] = "CC destinatari: " . implode(', ', $cc) . "\n";
        }

        if ($protocol === 'smtp') {
            $this->_cc_array = $cc;
            if ($this->_debug_enabled) {
                $this->_debug_msg[] = "CC Array popolato con " . count($cc) . " destinatari\n";
            }
        } else {
            if ($this->_debug_enabled) {
                $this->_debug_msg[] = "CC Array NON popolato (protocollo: " . $protocol . ")\n";
            }
        }

        return $this;
    }

    public function bcc($bcc, $limit = '')
    {
        if ($limit !== '' && is_numeric($limit)) {
            $this->bcc_batch_mode = TRUE;
            $this->bcc_batch_size = $limit;
        }

        $bcc = $this->clean_email($this->_str_to_array($bcc));

        if ($this->validate) {
            $this->validate_email($bcc);
        }

        $protocol = $this->_get_protocol();
        if ($this->_debug_enabled) {
            $this->_debug_msg[] = "=== BCC DEBUG ===\n";
            $this->_debug_msg[] = "Protocollo rilevato in bcc(): " . $protocol . "\n";
            $this->_debug_msg[] = "BCC destinatari: " . implode(', ', $bcc) . "\n";
            $this->_debug_msg[] = "BCC batch mode: " . ($this->bcc_batch_mode ? 'SI' : 'NO') . "\n";
        }

        if ($protocol === 'smtp' || ($this->bcc_batch_mode && count($bcc) > $this->bcc_batch_size)) {
            $this->_bcc_array = $bcc;
            if ($this->_debug_enabled) {
                $this->_debug_msg[] = "BCC Array popolato con " . count($bcc) . " destinatari\n";
            }
        } else {
            $this->set_header('Bcc', implode(', ', $bcc));
            if ($this->_debug_enabled) {
                $this->_debug_msg[] = "BCC Array NON popolato, impostato header Bcc\n";
            }
        }

        return $this;
    }

    public function subject($subject)
    {
        $rawSubject = $subject;
        $subject = $this->_prep_q_encoding($subject);
        $this->_subject = $rawSubject;
        $this->set_header('Subject', $subject);
        return $this;
    }

    public function message($body)
    {
        $this->_body = rtrim(str_replace("\r", '', $body));

        return $this;
    }

    public function attach($file, $disposition = '', $newname = NULL, $mime = '')
    {
        if ($mime === '') {
            if (strpos($file, '://') === FALSE && !file_exists($file)) {
                $this->_set_error_message('lang:email_attachment_missing', $file);
                return FALSE;
            }

            if (!$fp = @fopen($file, 'rb')) {
                $this->_set_error_message('lang:email_attachment_unreadable', $file);
                return FALSE;
            }

            $file_content = stream_get_contents($fp);
            $mime = $this->_mime_types(pathinfo($file, PATHINFO_EXTENSION));
            fclose($fp);
        } else {
            $file_content = &$file;
        }

        $this->_attachments[] = array(
            'name' => array($file, $newname),
            'disposition' => empty($disposition) ? 'attachment' : $disposition,
            'type' => $mime,
            'content' => chunk_split(base64_encode($file_content)),
            'multipart' => 'mixed'
        );

        return $this;
    }

    public function attachment_cid($filename)
    {
        for ($i = 0, $c = count($this->_attachments); $i < $c; $i++) {
            if ($this->_attachments[$i]['name'][0] === $filename) {
                $this->_attachments[$i]['multipart'] = 'related';
                $this->_attachments[$i]['cid'] = uniqid(basename($this->_attachments[$i]['name'][0]) . '@');
                return $this->_attachments[$i]['cid'];
            }
        }

        return FALSE;
    }

    public function set_header($header, $value)
    {
        $this->_headers[$header] = str_replace(array("\n", "\r"), '', $value);
        return $this;
    }

    protected function _str_to_array($email)
    {
        if (!is_array($email)) {
            return (strpos($email, ',') !== FALSE)
                ? preg_split('/[\s,]/', $email, -1, PREG_SPLIT_NO_EMPTY)
                : (array)trim($email);
        }

        return $email;
    }

    public function set_alt_message($str)
    {
        $this->alt_message = (string)$str;
        return $this;
    }

    public function set_mailtype($type = 'text')
    {
        $this->mailtype = ($type === 'html') ? 'html' : 'text';
        return $this;
    }

    public function set_wordwrap($wordwrap = TRUE)
    {
        $this->wordwrap = (bool)$wordwrap;
        return $this;
    }

    public function set_protocol($protocol = 'mail')
    {
        $this->protocol = in_array($protocol, $this->_protocols, TRUE) ? strtolower($protocol) : 'mail';
        return $this;
    }

    public function set_priority($n = 3)
    {
        $this->priority = preg_match('/^[1-5]$/', $n) ? (int)$n : 3;
        return $this;
    }

    public function set_newline($newline = "\n")
    {
        $this->newline = in_array($newline, array("\n", "\r\n", "\r")) ? $newline : "\n";
        return $this;
    }

    public function set_crlf($crlf = "\n")
    {
        $this->crlf = ($crlf !== "\n" && $crlf !== "\r\n" && $crlf !== "\r") ? "\n" : $crlf;
        return $this;
    }

    public function set_debug($debug = TRUE)
    {
        $this->debug = (bool)$debug;
        $this->_debug_enabled = (bool)$debug;
        return $this;
    }

    protected function _get_message_id()
    {
        $from = str_replace(array('>', '<'), '', $this->_headers['Return-Path']);
        return '<' . uniqid('') . strstr($from, '@') . '>';
    }

    protected function _get_protocol()
    {
        $this->protocol = strtolower($this->protocol);

        if ($this->_debug_enabled) {
            $this->_debug_msg[] = "=== _get_protocol() ===\n";
            $this->_debug_msg[] = "Protocollo iniziale: " . $this->protocol . "\n";
            $this->_debug_msg[] = "Protocolli supportati: " . implode(', ', $this->_protocols) . "\n";
        }

        if (!in_array($this->protocol, $this->_protocols, TRUE)) {
            if ($this->_debug_enabled) {
                $this->_debug_msg[] = "Protocollo '" . $this->protocol . "' non supportato, uso 'mail'\n";
            }
            $this->protocol = 'mail';
        } else {
            if ($this->_debug_enabled) {
                $this->_debug_msg[] = "Protocollo '" . $this->protocol . "' supportato\n";
            }
        }

        return $this->protocol;
    }

    protected function _get_encoding()
    {
        in_array($this->_encoding, $this->_bit_depths) or $this->_encoding = '8bit';

        foreach ($this->_base_charsets as $charset) {
            if (strpos($this->charset, $charset) === 0) {
                $this->_encoding = '7bit';
            }
        }

        return $this->_encoding;
    }

    protected function _get_content_type()
    {
        if ($this->mailtype === 'html') {
            return empty($this->_attachments) ? 'html' : 'html-attach';
        } elseif ($this->mailtype === 'text' && !empty($this->_attachments)) {
            return 'plain-attach';
        }

        return 'plain';
    }

    protected function _set_date()
    {
        $timezone = date('Z');
        $operator = ($timezone[0] === '-') ? '-' : '+';
        $timezone = abs($timezone);
        $timezone = floor($timezone / 3600) * 100 + ($timezone % 3600) / 60;

        return sprintf('%s %s%04d', date('D, j M Y H:i:s'), $operator, $timezone);
    }

    protected function _get_mime_message()
    {
        return 'This is a multi-part message in MIME format.' . $this->newline . 'Your email application may not support this format.';
    }

    public function validate_email($email)
    {
        if (!is_array($email)) {
            $this->_set_error_message('lang:email_must_be_array');
            return FALSE;
        }

        foreach ($email as $val) {
            if (!$this->valid_email($val)) {
                $this->_set_error_message('lang:email_invalid_address', $val);
                return FALSE;
            }
        }

        return TRUE;
    }

    public function valid_email($email)
    {
        if (function_exists('idn_to_ascii') && strpos($email, '@')) {
            list($account, $domain) = explode('@', $email, 2);
            $domain = defined('INTL_IDNA_VARIANT_UTS46')
                ? idn_to_ascii($domain, 0, INTL_IDNA_VARIANT_UTS46)
                : idn_to_ascii($domain);

            if ($domain !== FALSE) {
                $email = $account . '@' . $domain;
            }
        }

        return (bool)filter_var($email, FILTER_VALIDATE_EMAIL);
    }

    public function clean_email($email)
    {
        if (!is_array($email)) {
            return preg_match('/\<(.*)\>/', $email, $match) ? $match[1] : $email;
        }

        $clean_email = array();

        foreach ($email as $addy) {
            $clean_email[] = preg_match('/\<(.*)\>/', $addy, $match) ? $match[1] : $addy;
        }

        return $clean_email;
    }

    protected function _get_alt_message()
    {
        if (!empty($this->alt_message)) {
            return ($this->wordwrap)
                ? $this->word_wrap($this->alt_message, 76)
                : $this->alt_message;
        }

        $body = preg_match('/\<body.*?\>(.*)\<\/body\>/si', $this->_body, $match) ? $match[1] : $this->_body;
        $body = str_replace("\t", '', preg_replace('#<!--(.*)--\>#', '', trim(strip_tags($body))));

        for ($i = 20; $i >= 3; $i--) {
            $body = str_replace(str_repeat("\n", $i), "\n\n", $body);
        }

        $body = preg_replace('| +|', ' ', $body);

        return ($this->wordwrap)
            ? $this->word_wrap($body, 76)
            : $body;
    }

    public function word_wrap($str, $charlim = NULL)
    {
        if (empty($charlim)) {
            $charlim = empty($this->wrapchars) ? 76 : $this->wrapchars;
        }

        if (strpos($str, "\r") !== FALSE) {
            $str = str_replace(array("\r\n", "\r"), "\n", $str);
        }

        $str = preg_replace('| +\n|', "\n", $str);

        $unwrap = array();
        if (preg_match_all('|\{unwrap\}(.+?)\{/unwrap\}|s', $str, $matches)) {
            for ($i = 0, $c = count($matches[0]); $i < $c; $i++) {
                $unwrap[] = $matches[1][$i];
                $str = str_replace($matches[0][$i], '{{unwrapped' . $i . '}}', $str);
            }
        }

        $str = wordwrap($str, $charlim, "\n", FALSE);

        $output = '';
        foreach (explode("\n", $str) as $line) {
            if (self::strlen($line) <= $charlim) {
                $output .= $line . $this->newline;
                continue;
            }

            $temp = '';
            do {
                if (preg_match('!\[url.+\]|://|www\.!', $line)) {
                    break;
                }

                $temp .= self::substr($line, 0, $charlim - 1);
                $line = self::substr($line, $charlim - 1);
            } while (self::strlen($line) > $charlim);

            if ($temp !== '') {
                $output .= $temp . $this->newline;
            }

            $output .= $line . $this->newline;
        }

        if (count($unwrap) > 0) {
            foreach ($unwrap as $key => $val) {
                $output = str_replace('{{unwrapped' . $key . '}}', $val, $output);
            }
        }

        return $output;
    }

    protected function _build_headers()
    {
        $this->set_header('User-Agent', $this->useragent);
        $this->set_header('X-Sender', $this->clean_email($this->_headers['From']));
        $this->set_header('X-Mailer', $this->useragent);
        $this->set_header('X-Priority', $this->_priorities[$this->priority]);
        $this->set_header('Message-ID', $this->_get_message_id());
        $this->set_header('Mime-Version', '1.0');
    }

    protected function _write_headers()
    {
        if ($this->protocol === 'mail') {
            if (isset($this->_headers['Subject'])) {
                $this->_subject = $this->_headers['Subject'];
                unset($this->_headers['Subject']);
            }
        }

        reset($this->_headers);
        $this->_header_str = '';

        foreach ($this->_headers as $key => $val) {
            $val = trim($val);

            if ($val !== '') {
                $this->_header_str .= $key . ': ' . $val . $this->newline;
            }
        }

        if ($this->_get_protocol() === 'mail') {
            $this->_header_str = rtrim($this->_header_str);
        }
    }

    protected function _build_message()
    {
        if ($this->wordwrap === TRUE && $this->mailtype !== 'html') {
            $this->_body = $this->word_wrap($this->_body);
        }

        $this->_write_headers();

        $hdr = ($this->_get_protocol() === 'mail') ? $this->newline : '';
        $body = '';

        switch ($this->_get_content_type()) {
            case 'plain':

                $hdr .= 'Content-Type: text/plain; charset=' . $this->charset . $this->newline
                    . 'Content-Transfer-Encoding: ' . $this->_get_encoding();

                if ($this->_get_protocol() === 'mail') {
                    $this->_header_str .= $hdr;
                    $this->_finalbody = $this->_body;
                } else {
                    $this->_finalbody = $hdr . $this->newline . $this->newline . $this->_body;
                }

                return;

            case 'html':

                if ($this->send_multipart === FALSE) {
                    $hdr .= 'Content-Type: text/html; charset=' . $this->charset . $this->newline
                        . 'Content-Transfer-Encoding: quoted-printable';
                } else {
                    $boundary = uniqid('B_ALT_');
                    $hdr .= 'Content-Type: multipart/alternative; boundary="' . $boundary . '"';

                    $body .= $this->_get_mime_message() . $this->newline . $this->newline
                        . '--' . $boundary . $this->newline

                        . 'Content-Type: text/plain; charset=' . $this->charset . $this->newline
                        . 'Content-Transfer-Encoding: ' . $this->_get_encoding() . $this->newline . $this->newline
                        . $this->_get_alt_message() . $this->newline . $this->newline
                        . '--' . $boundary . $this->newline

                        . 'Content-Type: text/html; charset=' . $this->charset . $this->newline
                        . 'Content-Transfer-Encoding: quoted-printable' . $this->newline . $this->newline;
                }

                $this->_finalbody = $body . $this->_prep_quoted_printable($this->_body) . $this->newline . $this->newline;

                if ($this->_get_protocol() === 'mail') {
                    $this->_header_str .= $hdr;
                } else {
                    $this->_finalbody = $hdr . $this->newline . $this->newline . $this->_finalbody;
                }

                if ($this->send_multipart !== FALSE) {
                    $this->_finalbody .= '--' . $boundary . '--';
                }

                return;

            case 'plain-attach':

                $boundary = uniqid('B_ATC_');
                $hdr .= 'Content-Type: multipart/mixed; boundary="' . $boundary . '"';

                if ($this->_get_protocol() === 'mail') {
                    $this->_header_str .= $hdr;
                }

                $body .= $this->_get_mime_message() . $this->newline
                    . $this->newline
                    . '--' . $boundary . $this->newline
                    . 'Content-Type: text/plain; charset=' . $this->charset . $this->newline
                    . 'Content-Transfer-Encoding: ' . $this->_get_encoding() . $this->newline
                    . $this->newline
                    . $this->_body . $this->newline . $this->newline;

                $this->_append_attachments($body, $boundary);

                break;
            case 'html-attach':

                $alt_boundary = uniqid('B_ALT_');
                $last_boundary = NULL;

                if ($this->_attachments_have_multipart('mixed')) {
                    $atc_boundary = uniqid('B_ATC_');
                    $hdr .= 'Content-Type: multipart/mixed; boundary="' . $atc_boundary . '"';
                    $last_boundary = $atc_boundary;
                }

                if ($this->_attachments_have_multipart('related')) {
                    $rel_boundary = uniqid('B_REL_');
                    $rel_boundary_header = 'Content-Type: multipart/related; boundary="' . $rel_boundary . '"';

                    if (isset($last_boundary)) {
                        $body .= '--' . $last_boundary . $this->newline . $rel_boundary_header;
                    } else {
                        $hdr .= $rel_boundary_header;
                    }

                    $last_boundary = $rel_boundary;
                }

                if ($this->_get_protocol() === 'mail') {
                    $this->_header_str .= $hdr;
                }

                self::strlen($body) && $body .= $this->newline . $this->newline;
                $body .= $this->_get_mime_message() . $this->newline . $this->newline
                    . '--' . $last_boundary . $this->newline

                    . 'Content-Type: multipart/alternative; boundary="' . $alt_boundary . '"' . $this->newline . $this->newline
                    . '--' . $alt_boundary . $this->newline

                    . 'Content-Type: text/plain; charset=' . $this->charset . $this->newline
                    . 'Content-Transfer-Encoding: ' . $this->_get_encoding() . $this->newline . $this->newline
                    . $this->_get_alt_message() . $this->newline . $this->newline
                    . '--' . $alt_boundary . $this->newline

                    . 'Content-Type: text/html; charset=' . $this->charset . $this->newline
                    . 'Content-Transfer-Encoding: quoted-printable' . $this->newline . $this->newline

                    . $this->_prep_quoted_printable($this->_body) . $this->newline . $this->newline
                    . '--' . $alt_boundary . '--' . $this->newline . $this->newline;

                if (!empty($rel_boundary)) {
                    $body .= $this->newline . $this->newline;
                    $this->_append_attachments($body, $rel_boundary, 'related');
                }

                if (!empty($atc_boundary)) {
                    $body .= $this->newline . $this->newline;
                    $this->_append_attachments($body, $atc_boundary, 'mixed');
                }

                break;
        }

        $this->_finalbody = ($this->_get_protocol() === 'mail')
            ? $body
            : $hdr . $this->newline . $this->newline . $body;

        return TRUE;
    }

    protected function _attachments_have_multipart($type)
    {
        foreach ($this->_attachments as &$attachment) {
            if ($attachment['multipart'] === $type) {
                return TRUE;
            }
        }

        return FALSE;
    }

    protected function _append_attachments(&$body, $boundary, $multipart = null)
    {
        for ($i = 0, $c = count($this->_attachments); $i < $c; $i++) {
            if (isset($multipart) && $this->_attachments[$i]['multipart'] !== $multipart) {
                continue;
            }

            $name = isset($this->_attachments[$i]['name'][1])
                ? $this->_attachments[$i]['name'][1]
                : basename($this->_attachments[$i]['name'][0]);

            $body .= '--' . $boundary . $this->newline
                . 'Content-Type: ' . $this->_attachments[$i]['type'] . '; name="' . $name . '"' . $this->newline
                . 'Content-Disposition: ' . $this->_attachments[$i]['disposition'] . ';' . $this->newline
                . 'Content-Transfer-Encoding: base64' . $this->newline
                . (empty($this->_attachments[$i]['cid']) ? '' : 'Content-ID: <' . $this->_attachments[$i]['cid'] . '>' . $this->newline)
                . $this->newline
                . $this->_attachments[$i]['content'] . $this->newline;
        }

        empty($name) or $body .= '--' . $boundary . '--';
    }

    protected function _prep_quoted_printable($str)
    {
        static $ascii_safe_chars = array(
            39,
            40,
            41,
            43,
            44,
            45,
            46,
            47,
            58,
            61,
            63,
            48,
            49,
            50,
            51,
            52,
            53,
            54,
            55,
            56,
            57,
            65,
            66,
            67,
            68,
            69,
            70,
            71,
            72,
            73,
            74,
            75,
            76,
            77,
            78,
            79,
            80,
            81,
            82,
            83,
            84,
            85,
            86,
            87,
            88,
            89,
            90,
            97,
            98,
            99,
            100,
            101,
            102,
            103,
            104,
            105,
            106,
            107,
            108,
            109,
            110,
            111,
            112,
            113,
            114,
            115,
            116,
            117,
            118,
            119,
            120,
            121,
            122
        );

        $str = str_replace(array('{unwrap}', '{/unwrap}'), '', $str);

        if ($this->crlf === "\r\n") {
            return quoted_printable_encode($str);
        }

        $str = preg_replace(array('| +|', '/\x00+/'), array(' ', ''), $str);

        if (strpos($str, "\r") !== FALSE) {
            $str = str_replace(array("\r\n", "\r"), "\n", $str);
        }

        $escape = '=';
        $output = '';

        foreach (explode("\n", $str) as $line) {
            $length = self::strlen($line);
            $temp = '';

            for ($i = 0; $i < $length; $i++) {
                $char = $line[$i];
                $ascii = ord($char);

                if ($ascii === 32 or $ascii === 9) {
                    if ($i === ($length - 1)) {
                        $char = $escape . sprintf('%02s', dechex($ascii));
                    }
                } elseif ($ascii === 61) {
                    $char = $escape . strtoupper(sprintf('%02s', dechex($ascii)));
                } elseif (!in_array($ascii, $ascii_safe_chars, TRUE)) {
                    $char = $escape . strtoupper(sprintf('%02s', dechex($ascii)));
                }

                if ((self::strlen($temp) + self::strlen($char)) >= 76) {
                    $output .= $temp . $escape . $this->crlf;
                    $temp = '';
                }

                $temp .= $char;
            }

            $output .= $temp . $this->crlf;
        }

        return self::substr($output, 0, self::strlen($this->crlf) * -1);
    }


    protected function _prep_q_encoding($str)
    {
        $str = str_replace(array("\r", "\n"), '', $str);

        if ($this->charset === 'UTF-8') {
            if (ICONV_ENABLED === TRUE) {
                $output = @iconv_mime_encode(
                    '',
                    $str,
                    array(
                        'scheme' => 'Q',
                        'line-length' => 76,
                        'input-charset' => $this->charset,
                        'output-charset' => $this->charset,
                        'line-break-chars' => $this->crlf
                    )
                );

                if ($output !== FALSE) {
                    return self::substr($output, 2);
                }

                $chars = iconv_strlen($str, 'UTF-8');
            } elseif (MB_ENABLED === TRUE) {
                $chars = mb_strlen($str, 'UTF-8');
            }
        }

        isset($chars) or $chars = self::strlen($str);

        $output = '=?' . $this->charset . '?Q?';
        for ($i = 0, $length = self::strlen($output); $i < $chars; $i++) {
            $chr = ($this->charset === 'UTF-8' && ICONV_ENABLED === TRUE)
                ? '=' . implode('=', str_split(strtoupper(bin2hex(iconv_substr($str, $i, 1, $this->charset))), 2))
                : '=' . strtoupper(bin2hex($str[$i]));

            if ($length + ($l = self::strlen($chr)) > 74) {
                $output .= '?=' . $this->crlf
                    . ' =?' . $this->charset . '?Q?' . $chr;
                $length = 6 + self::strlen($this->charset) + $l;
            } else {
                $output .= $chr;
                $length += $l;
            }
        }

        return $output . '?=';
    }


    public function send($auto_clear = TRUE)
    {
        if ($this->_debug_enabled) {
            $this->_debug_msg[] = "=== INIZIO send() ===\n";
        }

        if (!isset($this->_headers['From'])) {
            $this->_set_error_message('lang:email_no_from');
            if ($this->_debug_enabled) {
                $this->_debug_msg[] = "ERRORE: Nessun mittente specificato\n";
            }
            return FALSE;
        }

        if ($this->_replyto_flag === FALSE) {
            $this->reply_to($this->_headers['From']);
        }

        if (
            !isset($this->_recipients) && !isset($this->_headers['To'])
            && !isset($this->_bcc_array) && !isset($this->_headers['Bcc'])
            && !isset($this->_headers['Cc'])
        ) {
            $this->_set_error_message('lang:email_no_recipients');
            if ($this->_debug_enabled) {
                $this->_debug_msg[] = "ERRORE: Nessun destinatario specificato\n";
            }
            return FALSE;
        }

        if ($this->_debug_enabled) {
            $this->_debug_msg[] = "Costruzione headers...\n";
        }
        $this->_build_headers();

        if ($this->bcc_batch_mode && count($this->_bcc_array) > $this->bcc_batch_size) {
            if ($this->_debug_enabled) {
                $this->_debug_msg[] = "Modalità batch BCC attivata\n";
            }
            $result = $this->batch_bcc_send();

            if ($result && $auto_clear) {
                $this->clear();
            }

            return $result;
        }

        if ($this->_debug_enabled) {
            $this->_debug_msg[] = "Costruzione messaggio...\n";
        }
        if ($this->_build_message() === FALSE) {
            if ($this->_debug_enabled) {
                $this->_debug_msg[] = "ERRORE: _build_message() ha restituito FALSE\n";
            }
            return FALSE;
        }

        if ($this->_debug_enabled) {
            $this->_debug_msg[] = "Chiamata _spool_email()...\n";
        }
        $result = $this->_spool_email();

        if ($result && $auto_clear) {
            $this->clear();
        }

        if ($this->_debug_enabled) {
            $this->_debug_msg[] = "=== FINE send() - Risultato: " . ($result ? 'TRUE' : 'FALSE') . " ===\n";
        }
        return $result;
    }


    public function batch_bcc_send()
    {
        $float = $this->bcc_batch_size - 1;
        $set = '';
        $chunk = array();

        for ($i = 0, $c = count($this->_bcc_array); $i < $c; $i++) {
            if (isset($this->_bcc_array[$i])) {
                $set .= ', ' . $this->_bcc_array[$i];
            }

            if ($i === $float) {
                $chunk[] = self::substr($set, 1);
                $float += $this->bcc_batch_size;
                $set = '';
            }

            if ($i === $c - 1) {
                $chunk[] = self::substr($set, 1);
            }
        }

        for ($i = 0, $c = count($chunk); $i < $c; $i++) {
            unset($this->_headers['Bcc']);

            $bcc = $this->clean_email($this->_str_to_array($chunk[$i]));

            if ($this->protocol !== 'smtp') {
                $this->set_header('Bcc', implode(', ', $bcc));
            } else {
                $this->_bcc_array = $bcc;
            }

            if ($this->_build_message() === FALSE) {
                return FALSE;
            }

            $this->_spool_email();
        }
    }


    protected function _unwrap_specials()
    {
        $this->_finalbody = preg_replace_callback('/\{unwrap\}(.*?)\{\/unwrap\}/si', array($this, '_remove_nl_callback'), $this->_finalbody);
    }


    protected function _remove_nl_callback($matches)
    {
        if (strpos($matches[1], "\r") !== FALSE or strpos($matches[1], "\n") !== FALSE) {
            $matches[1] = str_replace(array("\r\n", "\r", "\n"), '', $matches[1]);
        }

        return $matches[1];
    }


    protected function _spool_email()
    {
        $this->_unwrap_specials();

        $protocol = $this->_get_protocol();
        $method = '_send_with_' . $protocol;

        if ($this->_debug_enabled) {
            $this->_debug_msg[] = "=== _spool_email() ===\n";
            $this->_debug_msg[] = "Protocollo rilevato: " . $protocol . "\n";
            $this->_debug_msg[] = "Metodo da chiamare: " . $method . "\n";
            $this->_debug_msg[] = "Metodo esiste: " . (method_exists($this, $method) ? 'SI' : 'NO') . "\n";
        }

        if (!$this->$method()) {
            if ($this->_debug_enabled) {
                $this->_debug_msg[] = "ERRORE: Metodo " . $method . "() ha restituito FALSE\n";
            }
            $this->_set_error_message('lang:email_send_failure_' . ($protocol === 'mail' ? 'phpmail' : $protocol));
            return FALSE;
        }

        if ($this->_debug_enabled) {
            $this->_debug_msg[] = "SUCCESSO: Metodo " . $method . "() ha restituito TRUE\n";
        }
        $this->_set_error_message('lang:email_sent', $protocol);
        return TRUE;
    }


    protected function _validate_email_for_shell(&$email)
    {
        if (function_exists('idn_to_ascii') && strpos($email, '@')) {
            list($account, $domain) = explode('@', $email, 2);
            $domain = defined('INTL_IDNA_VARIANT_UTS46')
                ? idn_to_ascii($domain, 0, INTL_IDNA_VARIANT_UTS46)
                : idn_to_ascii($domain);

            if ($domain !== FALSE) {
                $email = $account . '@' . $domain;
            }
        }

        return (filter_var($email, FILTER_VALIDATE_EMAIL) === $email && preg_match('#\A[a-z0-9._+-]+@[a-z0-9.-]{1,253}\z#i', $email));
    }


    protected function _send_with_mail()
    {
        $recipients = is_array($this->_recipients)
            ? implode(', ', $this->_recipients)
            : (string)$this->_recipients;

        $from = $this->clean_email($this->_headers['Return-Path']);
        if (is_array($from)) {
            $from = implode(', ', $from);
        }

        $subject = (string)$this->_subject;
        $finalbody = (string)$this->_finalbody;
        $headerStr = (string)$this->_header_str;

        if (!$this->_validate_email_for_shell($from)) {
            return mail($recipients, $subject, $finalbody, $headerStr);
        }

        return mail($recipients, $subject, $finalbody, $headerStr, '-f ' . $from);
    }


    protected function _send_with_sendmail()
    {
        $from = $this->clean_email($this->_headers['From']);
        if (is_array($from)) {
            $from = implode(', ', $from);
        }
        if ($this->_validate_email_for_shell($from)) {
            $from = '-f ' . $from;
        } else {
            $from = '';
        }

        if (!function_usable('popen') or FALSE === ($fp = @popen($this->mailpath . ' -oi ' . $from . ' -t', 'w'))) {
            return FALSE;
        }

        fputs($fp, $this->_header_str);
        fputs($fp, $this->_finalbody);

        $status = pclose($fp);

        if ($status !== 0) {
            $this->_set_error_message('lang:email_exit_status', $status);
            $this->_set_error_message('lang:email_no_socket');
            return FALSE;
        }

        return TRUE;
    }


    protected function _send_with_smtp()
    {
        if ($this->smtp_host === '') {
            $this->_set_error_message('lang:email_no_hostname');
            return FALSE;
        }

        if (!$this->_smtp_connect() or !$this->_smtp_authenticate()) {
            return FALSE;
        }

        if (!$this->_send_command('from', $this->clean_email($this->_headers['From']))) {
            $this->_smtp_end();
            return FALSE;
        }

        foreach ($this->_recipients as $val) {
            if (!$this->_send_command('to', $val)) {
                $this->_smtp_end();
                return FALSE;
            }
        }

        if (count($this->_cc_array) > 0) {
            foreach ($this->_cc_array as $val) {
                if ($val !== '' && !$this->_send_command('to', $val)) {
                    $this->_smtp_end();
                    return FALSE;
                }
            }
        }

        if (count($this->_bcc_array) > 0) {
            foreach ($this->_bcc_array as $val) {
                if ($val !== '' && !$this->_send_command('to', $val)) {
                    $this->_smtp_end();
                    return FALSE;
                }
            }
        }

        if (!$this->_send_command('data')) {
            $this->_smtp_end();
            return FALSE;
        }

        $this->_send_data($this->_header_str . preg_replace('/^\./m', '..$1', $this->_finalbody));

        $this->_send_data('.');

        $reply = $this->_get_smtp_data();
        $this->_set_error_message($reply);

        $this->_smtp_end();

        if (strpos($reply, '250') !== 0) {
            $this->_set_error_message('lang:email_smtp_error', $reply);
            return FALSE;
        }

        return TRUE;
    }


    protected function _smtp_end()
    {
        ($this->smtp_keepalive)
            ? $this->_send_command('reset')
            : $this->_send_command('quit');
    }


    protected function _smtp_connect()
    {
        if (is_resource($this->_smtp_connect)) {
            return TRUE;
        }

        $ssl = (strtolower($this->smtp_crypto) === 'ssl') ? 'ssl://' : '';

        $this->_smtp_connect = @fsockopen(
            $ssl . $this->smtp_host,
            $this->smtp_port,
            $errno,
            $errstr,
            $this->smtp_timeout
        );

        if (!is_resource($this->_smtp_connect)) {
            $this->_set_error_message('lang:email_smtp_error', $errno . ' ' . $errstr);
            return FALSE;
        }

        stream_set_timeout($this->_smtp_connect, $this->smtp_timeout);

        // Un server SMTP deve inviare il banner 220 subito dopo l'apertura della connessione.
        // Se il banner non arriva, _get_smtp_data() ha gia registrato se il socket e scaduto o
        // e stato chiuso: proseguire con EHLO causerebbe una seconda attesa dello stesso timeout
        // e aggiungerebbe un errore "hello" fuorviante rispetto alla causa iniziale.
        $banner = $this->_get_smtp_data();
        if ($banner === '') {
            return FALSE;
        }

        $this->_set_error_message($banner);

        if ($this->smtp_crypto === 'tls') {
            // Se l'HELO fallisce non ha senso proseguire con STARTTLS: senza questo controllo
            // il codice tentava comunque la negoziazione TLS producendo una cascata di errori
            // generici e fuorvianti, invece di fermarsi al vero problema (HELO senza risposta).
            if (!$this->_send_command('hello')) {
                return FALSE;
            }
            if (!$this->_send_command('starttls')) {
                return FALSE;
            }

            $method = $this->isPhp('5.6')
                ? STREAM_CRYPTO_METHOD_TLSv1_0_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_1_CLIENT | STREAM_CRYPTO_METHOD_TLSv1_2_CLIENT
                : STREAM_CRYPTO_METHOD_TLS_CLIENT;

            // Azzera l'ultimo errore PHP registrato prima della chiamata soppressa con @: senza
            // questo, in caso di fallimento senza un warning proprio, error_get_last() potrebbe
            // riportare un errore precedente e non correlato, come se fosse la causa del fallimento TLS.
            error_clear_last();
            $crypto = @stream_socket_enable_crypto($this->_smtp_connect, TRUE, $method);

            if ($crypto !== TRUE) {
                // stream_socket_enable_crypto() viene chiamata con @ per non generare warning
                // visibili, ma l'errore PHP/OpenSSL reale resta recuperabile con error_get_last()
                // ed è spesso più utile della sola risposta SMTP (che a questo punto può essere vuota).
                $lastError = error_get_last();
                if ($lastError !== null) {
                    $this->_debug_msg[] = 'Ultimo errore PHP registrato durante il negoziato TLS (potrebbe non essere correlato): ' . htmlspecialchars($lastError['message']) . '<br />';
                }
                $this->_set_error_message('lang:email_smtp_error', $this->_get_smtp_data());
                return FALSE;
            }
        }

        return $this->_send_command('hello');
    }


    protected function _send_command($cmd, $data = '')
    {
        switch ($cmd) {
            case 'hello':

                if ($this->_smtp_auth or $this->_get_encoding() === '8bit') {
                    $this->_send_data('EHLO ' . $this->_get_hostname());
                } else {
                    $this->_send_data('HELO ' . $this->_get_hostname());
                }

                $resp = 250;
                break;
            case 'starttls':

                $this->_send_data('STARTTLS');
                $resp = 220;
                break;
            case 'from':

                $this->_send_data('MAIL FROM:<' . $data . '>');
                $resp = 250;
                break;
            case 'to':

                if (isset($this->dsn) && $this->dsn) {
                    $this->_send_data('RCPT TO:<' . $data . '> NOTIFY=SUCCESS,DELAY,FAILURE ORCPT=rfc822;' . $data);
                } else {
                    $this->_send_data('RCPT TO:<' . $data . '>');
                }

                $resp = 250;
                break;
            case 'data':

                $this->_send_data('DATA');
                $resp = 354;
                break;
            case 'reset':

                $this->_send_data('RSET');
                $resp = 250;
                break;
            case 'quit':

                $this->_send_data('QUIT');
                $resp = 221;
                break;
        }

        $reply = $this->_get_smtp_data();

        $this->_debug_msg[] = '<pre>' . $cmd . ': ' . $reply . '</pre>';

        if ((int)self::substr($reply, 0, 3) !== $resp) {
            $this->_set_error_message('lang:email_smtp_error', $reply);
            return FALSE;
        }

        if ($cmd === 'quit') {
            fclose($this->_smtp_connect);
        }

        return TRUE;
    }

    protected function _smtp_authenticate()
    {
        if (!$this->_smtp_auth) {
            return TRUE;
        }

        if ($this->smtp_user === '' && $this->smtp_pass === '') {
            $this->_set_error_message('lang:email_no_smtp_unpw');
            return FALSE;
        }

        $this->_send_data('AUTH LOGIN');

        $reply = $this->_get_smtp_data();

        if (strpos($reply, '503') === 0) {
            return TRUE;
        } elseif (strpos($reply, '334') !== 0) {
            $this->_set_error_message('lang:email_failed_smtp_login', $reply);
            return FALSE;
        }

        $this->_send_data(base64_encode($this->smtp_user));

        $reply = $this->_get_smtp_data();

        if (strpos($reply, '334') !== 0) {
            $this->_set_error_message('lang:email_smtp_auth_un', $reply);
            return FALSE;
        }

        $this->_send_data(base64_encode($this->smtp_pass));

        $reply = $this->_get_smtp_data();

        if (strpos($reply, '235') !== 0) {
            $this->_set_error_message('lang:email_smtp_auth_pw', $reply);
            return FALSE;
        }

        if ($this->smtp_keepalive) {
            $this->_smtp_auth = FALSE;
        }

        return TRUE;
    }


    protected function _send_data($data)
    {
        $data .= $this->newline;
        for ($written = $timestamp = 0, $length = self::strlen($data); $written < $length; $written += $result) {
            if (($result = fwrite($this->_smtp_connect, self::substr($data, $written))) === FALSE) {
                break;
            } elseif ($result === 0) {
                if ($timestamp === 0) {
                    $timestamp = time();
                } elseif ($timestamp < (time() - $this->smtp_timeout)) {
                    $result = FALSE;
                    break;
                }

                usleep(250000);
                continue;
            }

            $timestamp = 0;
        }

        if ($result === FALSE) {
            // Una scrittura fallita subito dopo un HELO/banner senza risposta è quasi sempre
            // dovuta al server che ha già chiuso la connessione: segnaliamolo esplicitamente
            // per distinguerlo da un problema di rete generico.
            $closed = is_resource($this->_smtp_connect) && feof($this->_smtp_connect);
            $this->_debug_msg[] = 'Scrittura sul socket SMTP fallita' . ($closed ? ' (connessione già chiusa dal server)' : '') . '<br />';
            // Non passare mai $data grezzo al messaggio di errore: questo metodo scrive sul
            // socket sia comandi innocui (es. STARTTLS) sia contenuti sensibili (credenziali
            // AUTH LOGIN codificate in base64, corpo del messaggio), quindi si logga un
            // valore fisso per evitare che credenziali finiscano in chiaro nei log.
            $this->_set_error_message('lang:email_smtp_data_failure', '[contenuto omesso per sicurezza]');
            return FALSE;
        }

        return TRUE;
    }


    protected function _get_smtp_data()
    {
        $data = '';

        while ($str = fgets($this->_smtp_connect, 512)) {
            $data .= $str;

            if ($str[3] === ' ') {
                break;
            }
        }

        if ($data === '') {
            // Nessuna riga letta dal socket: distinguiamo timeout scaduto (server irraggiungibile o
            // lento) da connessione chiusa dal server, per non lasciare solo una stringa vuota
            // silenziosa nel log di debug quando manca la risposta SMTP.
            $meta = is_resource($this->_smtp_connect) ? stream_get_meta_data($this->_smtp_connect) : array();
            $reason = !empty($meta['timed_out'])
                ? 'timeout scaduto dopo ' . $this->smtp_timeout . 's in attesa di una risposta'
                : 'connessione chiusa dal server senza risposta';
            $this->_debug_msg[] = '(Nessuna risposta SMTP ricevuta: ' . $reason . ')<br />';
        }

        return $data;
    }


    protected function _get_hostname()
    {
        if (isset($_SERVER['SERVER_NAME'])) {
            return $_SERVER['SERVER_NAME'];
        }

        return isset($_SERVER['SERVER_ADDR']) ? '[' . $_SERVER['SERVER_ADDR'] . ']' : '[127.0.0.1]';
    }


    public function print_debugger($include = array('headers', 'subject', 'body'))
    {
        $msg = '';

        if (count($this->_debug_msg) > 0) {
            foreach ($this->_debug_msg as $val) {
                $msg .= $val;
            }
        }

        $raw_data = '';
        is_array($include) or $include = array($include);

        if (in_array('headers', $include, TRUE)) {
            $raw_data = htmlspecialchars($this->_header_str ?? '') . "\n";
        }

        if (in_array('subject', $include, TRUE)) {
            $raw_data .= htmlspecialchars($this->_subject ?? '') . "\n";
        }

        if (in_array('body', $include, TRUE)) {
            $raw_data .= htmlspecialchars($this->_finalbody ?? '');
        }

        return $msg . ($raw_data === '' ? '' : '<pre>' . $raw_data . '</pre>');
    }


    protected function _set_error_message($msg, $val = '')
    {
        if (sscanf($msg, 'lang:%s', $line) !== 1 or FALSE === ($line = $this->__($line))) {
            $this->_debug_msg[] = str_replace('%s', (string) $val, (string) $msg) . '<br />';
        } else {
            $this->_debug_msg[] = str_replace('%s', (string) $val, (string) $line) . '<br />';
        }
    }

    protected function __($key = null, $value = null, $file = null)
    {
        $config = new \Maer\Config\Config();
        $config->load(APP_PATH . 'Config/app.php');

        $defaultLang = $config->get('language', 'it');

        $fileName = ($file !== null) ? $file : 'langs';

        $pathDefaultLang = 'Langs' . DIRECTORY_SEPARATOR . $defaultLang . DIRECTORY_SEPARATOR . $fileName . '.php';

        if (file_exists(APP_PATH . $pathDefaultLang)) {
            if ($key !== null) {
                $lang = new \Maer\Config\Config();
                $lang->load(APP_PATH . $pathDefaultLang);

                return $lang->get($key, $value);
            }

            return null;
        } else {
            throw new \Exception("Errore caricamento lingua: la lingua che stai provando a caricare non esiste. '{$pathDefaultLang}' ");
        }
    }


    protected function _mime_types($ext = '')
    {
        $ext = strtolower($ext);

        $mimes = &getMimes();

        if (isset($mimes[$ext])) {
            return is_array($mimes[$ext])
                ? current($mimes[$ext])
                : $mimes[$ext];
        }

        return 'application/x-unknown-content-type';
    }


    public function __destruct()
    {
        is_resource($this->_smtp_connect) && $this->_send_command('quit');
    }


    protected static function strlen($str)
    {
        return (self::$func_overload)
            ? mb_strlen((string) $str, '8bit')
            : strlen((string) $str);
    }


    protected static function substr($str, $start, $length = NULL)
    {
        if (self::$func_overload) {
            return mb_substr($str, $start, $length, '8bit');
        }

        return isset($length)
            ? substr($str, $start, $length)
            : substr($str, $start);
    }

    private function isPhp(string $version = ''): mixed
    {
        static $isPhp;

        if (!isset($isPhp[$version])) {
            $isPhp[$version] = version_compare(PHP_VERSION, $version, '>=');
        }

        return $isPhp[$version];
    }

    public function get_cc_array()
    {
        return $this->_cc_array;
    }

    public function get_bcc_array()
    {
        return $this->_bcc_array;
    }
}
