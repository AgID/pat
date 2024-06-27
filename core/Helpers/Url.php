<?php
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
defined('_FRAMEWORK_') or exit('No direct script access allowed');

if (!function_exists('anchor')) {

    /**
     * Collegamento di ancoraggio nei links
     *
     * Crea un ancoraggio basato sull'URL locale.
     *
     * @param string    the URL
     * @param string    the link title
     * @param mixed    any attributes
     * @return    string
     */
    function anchor($uri = '', $title = '', $attributes = '')
    {
        $title = (string)$title;

        $siteUrl = is_array($uri) ? siteUrl($uri) : (preg_match('#^(\w+:)?//#i', $uri) ? $uri : siteUrl($uri));

        if ($title === '') {

            $title = $siteUrl;

        }

        if ($attributes !== '') {

            $attributes = stringifyAttributes($attributes);

        }

        return '<a href="' . $siteUrl . '"' . $attributes . '>' . $title . '</a>';
    }
}

if (!function_exists('anchorPopup')) {
    /**
     * Ancoraggio Link con pop-up in Javascript
     *
     * Crea un ancoraggio basato sull'URL locale. Il link
     * apre una nuova finestra in base agli attributi specificati.
     *
     * @param string    URL
     * @param string   Il titolo del link
     * @param mixed    solo attributi
     * @return    string
     */
    function anchorPopup($uri = '', $title = '', $attributes = false)
    {
        $title = (string)$title;
        $siteUrl = preg_match('#^(\w+:)?//#i', $uri) ? $uri : siteUrl($uri);

        if ($title === '') {

            $title = $siteUrl;

        }

        if ($attributes === false) {

            return '<a href="' . $siteUrl . '" onclick="window.open(\'' . $siteUrl . "', '_blank'); return false;\">" . $title . '</a>';

        }

        if (!is_array($attributes)) {

            $attributes = array($attributes);

            // Ref: http://www.w3schools.com/jsref/met_win_open.asp
            $windowName = '_blank';

        } elseif (!empty($attributes['window_name'])) {

            $windowName = $attributes['window_name'];

            unset($attributes['window_name']);

        } else {

            $windowName = '_blank';

        }

        foreach (array('width' => '800', 'height' => '600', 'scrollbars' => 'yes', 'menubar' => 'no', 'status' => 'yes', 'resizable' => 'yes', 'screenx' => '0', 'screeny' => '0') as $key => $val) {

            $atts[$key] = isset($attributes[$key]) ? $attributes[$key] : $val;

            unset($attributes[$key]);

        }

        $attributes = stringifyAttributes($attributes);

        return '<a href="' . $siteUrl
            . '" onclick="window.open(\'' . $siteUrl . "', '" . $windowName . "', '" . stringifyAttributes($atts, true) . "'); return false;\""
            . $attributes . '>' . $title . '</a>';
    }
}

if (!function_exists('mailto')) {
    /**
     * Mailto Link
     *
     * @param string    link email
     * @param string    titolo email
     * @param mixed    solo attributi
     * @return    string
     */
    function mailto($email = '', $title = '', $attributes = '')
    {
        $title = (string)$title;

        if ($title === '') {
            $title = $email;
        }

        return '<a href="mailto:' . $email . '"' . stringifyAttributes($attributes) . '>' . $title . '</a>';
    }
}

if (!function_exists('safeMailto')) {
    /**
     * Link Mailto codificato
     *
     * Crea un link mailto protetto dallo spam scritto in Javascript
     *
     * @param string    link email
     * @param string    titolo email
     * @param mixed    solo attributi
     * @return    string
     */
    function safeMailto($email = '', $title = '', $attributes = '')
    {
        $title = (string)$title;

        if ($title === '') {
            $title = $email;
        }

        $x = str_split('<a href="mailto:', 1);

        for ($i = 0, $l = strlen((string) $email); $i < $l; $i++) {
            $x[] = '|' . ord($email[$i]);
        }

        $x[] = '"';

        if ($attributes !== '') {
            if (is_array($attributes)) {
                foreach ($attributes as $key => $val) {
                    $x[] = ' ' . $key . '="';
                    for ($i = 0, $l = strlen((string) $val); $i < $l; $i++) {
                        $x[] = '|' . ord($val[$i]);
                    }
                    $x[] = '"';
                }
            } else {
                for ($i = 0, $l = strlen((string) $attributes); $i < $l; $i++) {
                    $x[] = $attributes[$i];
                }
            }
        }

        $x[] = '>';

        $temp = array();
        for ($i = 0, $l = strlen((string) $title); $i < $l; $i++) {
            $ordinal = ord($title[$i]);

            if ($ordinal < 128) {
                $x[] = '|' . $ordinal;
            } else {
                if (count($temp) === 0) {
                    $count = ($ordinal < 224) ? 2 : 3;
                }

                $temp[] = $ordinal;
                if (count($temp) === $count) {
                    $number = ($count === 3)
                        ? (($temp[0] % 16) * 4096) + (($temp[1] % 64) * 64) + ($temp[2] % 64)
                        : (($temp[0] % 32) * 64) + ($temp[1] % 64);
                    $x[] = '|' . $number;
                    $count = 1;
                    $temp = array();
                }
            }
        }

        $x[] = '<';
        $x[] = '/';
        $x[] = 'a';
        $x[] = '>';

        $x = array_reverse($x);

        $output = "<script type=\"text/javascript\">\n"
            . "\t//<![CDATA[\n"
            . "\tvar l=new Array();\n";

        for ($i = 0, $c = count($x); $i < $c; $i++) {
            $output .= "\tl[" . $i . "] = '" . $x[$i] . "';\n";
        }

        $output .= "\n\tfor (var i = l.length-1; i >= 0; i=i-1) {\n"
            . "\t\tif (l[i].substring(0, 1) === '|') document.write(\"&#\"+unescape(l[i].substring(1))+\";\");\n"
            . "\t\telse document.write(unescape(l[i]));\n"
            . "\t}\n"
            . "\t//]]>\n"
            . '</script>';

        return $output;
    }
}


if (!function_exists('autoLink')) {
    /**
     * Auto-linker
     *
     * Collega automaticamente URL e indirizzi e-mail.
     *
     * @param string Stringa
     * @param string Il tipo: email, url o entrambi
     * @param bool   Impostato a true crea un collegamento Popup
     * @return    string
     */
    function autoLink($str = '', $type = 'both', $popup = false)
    {
        // Trova e sostituisci qualsiasi URL.
        if ($type !== 'email' && preg_match_all('#(\w*://|www\.)[a-z0-9]+(-+[a-z0-9]+)*(\.[a-z0-9]+(-+[a-z0-9]+)*)+(/([^\s()<>;]+\w)?/?)?#i', $str, $matches, PREG_OFFSET_CAPTURE | PREG_SET_ORDER)) {
            //Imposta il nostro HTML di destinazione se utilizzi link popup.
            $target = ($popup) ? ' target="_blank" rel="noopener"' : '';


            // Elaboro i collegamenti in ordine inverso (ultimo -> primo) in modo che
            // gli offset di stringa restituiti da preg_match_all () non ci sono e viene
            // spostato mentre aggiungiamo altro HTML.
            foreach (array_reverse($matches) as $match) {
                // $match [0] è la stringa / collegamento corrispondente
                // $match [1] è un prefisso di protocollo o "www."
                //
                // Con PREG_OFFSET_CAPTURE, entrambi i precedenti sono un array,
                // dove il valore effettivo è contenuto in [0] e il suo offset sull'indice [1].
                $a = '<a href="' . (strpos($match[1][0], '/') ? '' : 'http://') . $match[0][0] . '"' . $target . '>' . $match[0][0] . '</a>';
                $str = substr_replace($str, $a, $match[0][1], strlen((string) $match[0][0]));
            }
        }

        // Trova e sostituisci eventuali email.
        if ($type !== 'url' && preg_match_all('#([\w\.\-\+]+@[a-z0-9\-]+\.[a-z0-9\-\.]+[^[:punct:]\s])#i', $str, $matches, PREG_OFFSET_CAPTURE)) {
            foreach (array_reverse($matches[0]) as $match) {
                if (filter_var($match[0], FILTER_VALIDATE_EMAIL) !== false) {
                    $str = substr_replace($str, safe_mailto($match[0]), $match[1], strlen((string) $match[0]));
                }
            }
        }

        return $str;
    }
}


if (!function_exists('prepUrl')) {
    /**
     * Prep URL
     *
     * Aggiunge semplicemente la parte http: // se non è incluso alcuno schema
     *
     * @param string    the URL
     * @return    string
     */
    function prepUrl($str = '')
    {
        if ($str === 'http://' or $str === '') {
            return '';
        }

        $url = parse_url($str);

        if (!$url or !isset($url['scheme'])) {
            return 'http://' . $str;
        }

        return $str;
    }
}


if (!function_exists('urlTitle')) {
    /**
     * Crea il titolo URL (Slug per URL Friendly)
     *
     * + Accetta una stringa "title" come input e crea un file
     * Stringa URL a misura d'uomo con una stringa "separatore"
     * come separatore di parole.
     *
     * @param string $str Stringa di input
     * @param string $separator Separatore di parole
     *            (usually '-' or '_')
     * @param bool $lowercase Indica se trasformare la stringa di output in minuscolo
     * @return    string
     */
    function urlTitle($string = '', $separator = '-', $lowercase = true)
    {
        return \System\Formatting::slug($string, $separator, $lowercase);
    }
}


if (!function_exists('redirect')) {
    /**
     * Reindirizzamento intestazione
     *
     * Reindirizzamento dell'intestazione in due versioni
     *
     * @param string $uri URL
     * @param string $method Redirect method
     *            'auto', 'location' or 'refresh'
     * @param int $code HTTP Response status code
     * @return    void
     */
    function redirect($uri = '', $method = 'auto', $code = NULL)
    {
        if (!preg_match('#^(\w+:)?//#i', $uri)) {
            $uri = siteUrl($uri);
        }

        // IIS environment likely? Use 'refresh' for better compatibility
        if ($method === 'auto' && isset($_SERVER['SERVER_SOFTWARE']) && strpos($_SERVER['SERVER_SOFTWARE'], 'Microsoft-IIS') !== false) {
            $method = 'refresh';
        } elseif ($method !== 'refresh' && (empty($code) or !is_numeric($code))) {
            if (isset($_SERVER['SERVER_PROTOCOL'], $_SERVER['REQUEST_METHOD']) && $_SERVER['SERVER_PROTOCOL'] === 'HTTP/1.1') {
                $code = ($_SERVER['REQUEST_METHOD'] !== 'GET')
                    ? 303
                    : 307;
            } else {
                $code = 302;
            }
        }

        switch ($method) {
            case 'refresh':
                header('Refresh:0;url=' . $uri);
                break;
            default:
                header('Location: ' . $uri, true, $code);
                break;
        }
        exit;
    }
}
