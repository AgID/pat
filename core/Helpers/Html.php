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
defined('_FRAMEWORK_') OR exit('No direct script access allowed');

if (!function_exists('css')) {

    /**
     * @param string $href
     * @param bool $theme
     * @param string $rel
     * @param string $type
     * @param string $title
     * @param string $media
     * @return string
     * @throws Exception
     */
    function css($href = '', $theme = true, $rel = 'stylesheet', $type = 'text/css', $title = '', $media = '')
    {

        if (is_bool($theme)) {

            $theme = ($theme === true) ? 'assets' . DIRECTORY_SEPARATOR . THEME . DIRECTORY_SEPARATOR : '';

        } else {

            $theme = 'assets' . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR;
        }

        $link = '<link ';

        if (is_array($href)) {

            foreach ($href as $k => $v) {

                if ($k === 'href' && !preg_match('#^([a-z]+:)?//#i', $v)) {

                    $link .= 'href="' . baseUrl($theme . $v) . '" ';

                } else {

                    $link .= $k . '="' . $theme . $v . '" ';

                }

            }

        } else {

            if (preg_match('#^([a-z]+:)?//#i', $href)) {

                $link .= 'href="' . $href . '" ';

            } else {

                $link .= 'href="' . baseUrl($theme . $href) . '" ';

            }

            $link .= 'rel="' . $rel . '" type="' . $type . '" ';

            if ($media !== '') {

                $link .= 'media="' . $media . '" ';

            }

            if ($title !== '') {

                $link .= 'title="' . $title . '" ';

            }

        }

        return $link . "/>\n";
    }
}

// ---------------------------------------------------------------------------------------------------------------------


if (!function_exists('js')) {

    /**
     * @param string $src
     * @param bool $theme
     * @param string $language
     * @param string $type
     * @return string
     * @throws Exception
     */
    function js($src = '', $theme = false, $language = 'javascript', $type = 'text/javascript')
    {

        if (is_bool($theme)) {

            $theme = ($theme === true) ? 'assets' . DIRECTORY_SEPARATOR . THEME . DIRECTORY_SEPARATOR : '';

        } else {

            $theme = 'assets' . DIRECTORY_SEPARATOR . $theme . DIRECTORY_SEPARATOR;
        }

        $script = '<scr' . 'ipt';

        if (is_array($src)) {

            foreach ($src as $k => $v) {

                if ($k == 'src' AND strpos($v, '://') === FALSE) {

                    $script .= ' src="' . baseUrl($theme . $v) . '"';

                } else {

                    $script .= "$k=\"$theme.$v\"";

                }
            }

            $script .= "></scr" . "ipt>\n";

        } else {

            if (strpos($src, '://') !== FALSE) {

                $script .= ' src="' . $src . '" ';

            } else {

                $script .= ' src="' . baseUrl($theme . $src) . '" ';

            }
            $script .= 'language="' . $language . '" type="' . $type . '"';

            $script .= ' /></scr' . "ipt>\n";

        }


        return $script;
    }
}

// ---------------------------------------------------------------------------------------------------------------------


if (!function_exists('doctype')) {

    function doctype()
    {

    }
}


// ---------------------------------------------------------------------------------------------------------------------


if (!function_exists('meta')) {

    /**
     * @param string $name
     * @param string $content
     * @param string $type
     * @param string $newline
     * @return string
     */
    function meta($name = '', $content = '', $type = 'name', $newline = "\n")
    {

        if (!is_array($name)) {

            $name = array(array('name' => $name, 'content' => $content, 'type' => $type, 'newline' => $newline));

        } else {

            if (isset($name['name'])) {

                $name = array($name);

            }
        }

        $str = '';

        foreach ($name as $meta) {

            $type = (!isset($meta['type']) OR $meta['type'] == 'name') ? 'name' : 'http-equiv';
            $name = (!isset($meta['name'])) ? '' : $meta['name'];
            $content = (!isset($meta['content'])) ? '' : $meta['content'];
            $newline = (!isset($meta['newline'])) ? "\n" : $meta['newline'];
            $str .= '<meta ' . $type . '="' . $name . '" content="' . $content . '" />' . $newline;

        }

        return $str;
    }
}

// ---------------------------------------------------------------------------------------------------------------------


if (!function_exists('br')) {

    /**
     * @param int $num
     * @return string
     */
    function br($num = 1)
    {
        return str_repeat("<br />", $num);
    }
}

// ---------------------------------------------------------------------------------------------------------------------


if (!function_exists('nbs')) {

    /**
     * @param int $num
     * @return string
     */
    function nbs($num = 1)
    {
        return str_repeat("&nbsp;", $num);
    }
}

// ---------------------------------------------------------------------------------------------------------------------


if (!function_exists('ol')) {

    /**
     * @param $list
     * @param string $attributes
     * @return string
     */
    function ol($list=null, $attributes = '')
    {
        return _list('ol', $list, $attributes);
    }

}

// ---------------------------------------------------------------------------------------------------------------------


if (!function_exists('ul')) {

    /**
     * @param $list
     * @param string $attributes
     * @return string
     */
    function ul($list, $attributes = '')
    {
        return _list('ul', $list, $attributes);
    }

}

// ---------------------------------------------------------------------------------------------------------------------


if (!function_exists('img')) {

    /**
     * @param string $src
     * @return string
     * @throws Exception
     */
    function img($src = '')
    {
        if (!is_array($src)) {
            $src = array('src' => $src);
        }

        if (!isset($src['alt'])) {
            $src['alt'] = '';
        }

        $img = '<img';

        foreach ($src as $k => $v) {

            if ($k == 'src' AND strpos($v, '://') === FALSE) {
                $img .= ' src="' . baseUrl($v) . '"';
            } else {
                $img .= " $k=\"$v\"";
            }
        }

        $img .= '/>';

        return $img;
    }
}

// ---------------------------------------------------------------------------------------------------------------------


if (!function_exists('heading')) {

    /**
     * @param string $data
     * @param string $h
     * @param string $attributes
     * @return string
     */
    function heading($data = '', $h = '1', $attributes = '')
    {
        $attributes = ($attributes != '') ? ' ' . $attributes : $attributes;
        return '<h' . $h . $attributes . '>' . $data . '</h' . $h . '>';
    }
}

// ---------------------------------------------------------------------------------------------------------------------


if (!function_exists('_list')) {

    /**
     * @param string $type
     * @param $list
     * @param string $attributes
     * @param int $depth
     * @return string
     */
    function _list($type = 'ul', $list=null, $attributes = '', $depth = 0)
    {
        if (!is_array($list)) {
            return $list;
        }

        $out = str_repeat(' ', $depth);

        if (is_array($attributes)) {
            $atts = '';
            foreach ($attributes as $key => $val) {
                $atts .= ' ' . $key . '="' . $val . '"';
            }
            $attributes = $atts;
        } elseif (is_string($attributes) AND strlen($attributes) > 0) {
            $attributes = ' ' . $attributes;
        }

        $out .= '<' . $type . $attributes . ">\n";


        static $_last_list_item = '';
        foreach ($list as $key => $val) {
            $_last_list_item = $key;

            $out .= str_repeat(' ', $depth + 2);
            $out .= '<li>';

            if (!is_array($val)) {
                $out .= $val;
            } else {
                $out .= $_last_list_item . "\n";
                $out .= _list($type, $val, '', $depth + 4);
                $out .= str_repeat(' ', $depth + 2);
            }

            $out .= "</li>\n";
        }

        $out .= str_repeat(' ', $depth);

        $out .= '</' . $type . ">\n";

        return $out;
    }
}
