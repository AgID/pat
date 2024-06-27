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

if (!function_exists('form_open')) {
    /**
     * @param string    the URI segments of the form destination
     * @param array    a key/value pair of attributes
     * @param array    a key/value pair hidden data
     * @return    string
     */
    function form_open($action = '', $attributes = [], $hidden = [], $csfr_token = null, $reload_all_request=false)
    {

        $base = new \System\Base();
        $uri = new \System\Uri();
        $config = new \Maer\Config\Config();
        $config->load(APP_PATH . 'Config/app.php');
        $csfrIsEnable = $config->get('csrf_enable');

        if (!$action) {

            $action = $base->siteUrl($uri->uriString());
        } elseif (preg_match('#{*?}#s', $action)) {

            $action = $action;
        } elseif (strpos($action, '://') === FALSE) {

            $action = $base->siteUrl($action);
        }

        $attributes = stringifyAttributes($attributes);

        if (stripos($attributes, 'method=') === FALSE) {
            $attributes .= ' method="post"';
        }

        if (stripos($attributes, 'accept-charset=') === FALSE) {
            $attributes .= ' accept-charset="' . strtolower(config('charset', null, 'app')) . '"';
        }

        $form = '<form action="' . $action . '"' . $attributes . ">\n";

        if (is_array($hidden)) {
            foreach ($hidden as $name => $value) {
                $form .= '<input type="hidden" name="' . $name . '" value="' . htmlEscape($value) . '" />' . "\n";
            }
        }

        if (($csfrIsEnable === true) && ($csfr_token === true || $csfr_token === null)) {

            $form .= csrf_input_token($reload_all_request);
        }

        return $form . "\n";
    }
}

if (!function_exists('csrf_input_token')) {

    function csrf_input_token($reload_all_request=false)
    {
        return sprintf(
            '<input type="hidden" name="%s" value="%s" />%s',
            config('csrf_token_name', null, 'app'),
            \System\Token::getToken(),
            "\n"
        );
    }
}

if (!function_exists('form_open_multipart')) {
    /**
     * @param string    the URI segments of the form destination
     * @param array    a key/value pair of attributes
     * @param array    a key/value pair hidden data
     * @return    string
     */
    function form_open_multipart($action = '', $attributes = [], $hidden = [], $csfr_token = null)
    {
        if (is_string($attributes)) {
            $attributes .= ' enctype="multipart/form-data"';
        } else {
            $attributes['enctype'] = 'multipart/form-data';
        }

        return form_open($action, $attributes, $hidden, $csfr_token);
    }
}

if (!function_exists('form_hidden')) {
    /**
     * @param mixed $name Field name
     * @param string $value Field value
     * @param bool $recursing
     * @return    string
     */
    function form_hidden($name = '', $value = '', $recursing = FALSE)
    {
        static $form;

        if ($recursing === FALSE) {
            $form = "\n";
        }

        if (is_array($name)) {
            foreach ($name as $key => $val) {
                form_hidden($key, $val, TRUE);
            }

            return $form;
        }

        if (!is_array($value)) {
            $form .= '<input type="hidden" name="' . $name . '" value="' . htmlEscape($value) . "\" />\n";
        } else {
            foreach ($value as $k => $v) {
                $k = is_int($k) ? '' : $k;
                form_hidden($name . '[' . $k . ']', $v, TRUE);
            }
        }

        return $form;
    }
}

if (!function_exists('form_input')) {
    /**
     * @param mixed
     * @param string
     * @param mixed
     * @return    string
     */
    function form_input($data = '', $value = '', $extra = '')
    {
        $defaults = array(
            'type' => 'text',
            'name' => is_array($data) ? '' : $data,
            'value' => $value
        );

        return '<input ' . _parse_form_attributes($data, $defaults) . stringifyAttributes($extra) . " />\n";
    }
}

if (!function_exists('form_password')) {
    /**
     * @param mixed
     * @param string
     * @param mixed
     * @return    string
     */
    function form_password($data = '', $value = '', $extra = '')
    {
        is_array($data) or $data = array('name' => $data);
        $data['type'] = 'password';
        return form_input($data, $value, $extra);
    }
}

if (!function_exists('form_upload')) {
    /**
     * @param mixed
     * @param mixed
     * @return    string
     */
    function form_upload($data = '', $extra = '')
    {
        $defaults = array('type' => 'file', 'name' => '');
        is_array($data) or $data = array('name' => $data);
        $data['type'] = 'file';

        return '<input ' . _parse_form_attributes($data, $defaults) . stringifyAttributes($extra) . " />\n";
    }
}

if (!function_exists('form_textarea')) {
    /**
     * @param mixed $data
     * @param string $value
     * @param mixed $extra
     * @return    string
     */
    function form_textarea($data = '', $value = '', $extra = '')
    {
        $defaults = array(
            'name' => is_array($data) ? '' : $data,
            'cols' => '40',
            'rows' => '10'
        );

        if (!is_array($data) or !isset($data['value'])) {
            $val = $value;
        } else {
            $val = $data['value'];
            unset($data['value']);
        }

        return '<textarea ' . _parse_form_attributes($data, $defaults) . stringifyAttributes($extra) . '>'
            . htmlEscape($val)
            . "</textarea>\n";
    }
}

if (!function_exists('form_multiselect')) {
    /**
     * @param string
     * @param array
     * @param mixed
     * @param mixed
     * @return    string
     */
    function form_multiselect($name = '', $options = [], $selected = [], $extra = '')
    {
        $extra = stringifyAttributes($extra);
        if (stripos($extra, 'multiple') === FALSE) {
            $extra .= ' multiple="multiple"';
        }

        return form_dropdown($name, $options, $selected, $extra);
    }
}

if (!function_exists('form_dropdown')) {
    /**
     * @param mixed $data
     * @param mixed $options
     * @param mixed $selected
     * @param mixed $extra
     * @return    string
     */
    function form_dropdown($data = '', $options = [], $selected = [], $extra = '')
    {
        $defaults = [];

        if (is_array($data)) {
            if (isset($data['selected'])) {
                $selected = $data['selected'];
                unset($data['selected']);
            }

            if (isset($data['options'])) {
                $options = $data['options'];
                unset($data['options']);
            }
        } else {
            $defaults = array('name' => $data);
        }

        is_array($selected) or $selected = array($selected);
        is_array($options) or $options = array($options);

        if (empty($selected)) {
            if (is_array($data)) {
                if (isset($data['name'], $_POST[$data['name']])) {
                    $selected = array($_POST[$data['name']]);
                }
            } elseif (isset($_POST[$data])) {
                $selected = array($_POST[$data]);
            }
        }

        $extra = stringifyAttributes($extra);

        $multiple = (count($selected) > 1 && stripos($extra, 'multiple') === FALSE) ? ' multiple="multiple"' : '';

        $form = '<select ' . rtrim(_parse_form_attributes($data, $defaults)) . $extra . $multiple . ">\n";

        foreach ($options as $key => $val) {
            $key = (string)$key;

            if (is_array($val)) {
                if (empty($val)) {
                    continue;
                }

                $form .= '<optgroup label="' . $key . "\">\n";

                foreach ($val as $optgroup_key => $optgroup_val) {
                    $sel = in_array($optgroup_key, $selected) ? ' selected="selected"' : '';
                    $form .= '<option value="' . htmlEscape($optgroup_key) . '"' . $sel . '>'
                        . (string)$optgroup_val . "</option>\n";
                }

                $form .= "</optgroup>\n";
            } else {
                $form .= '<option value="' . htmlEscape($key) . '"'
                    . (in_array($key, $selected) ? ' selected="selected"' : '') . '>'
                    . (string)$val . "</option>\n";
            }
        }

        return $form . "</select>\n";
    }
}

if (!function_exists('form_checkbox')) {
    /**
     * @param mixed
     * @param string
     * @param bool
     * @param mixed
     * @return    string
     */
    function form_checkbox($data = '', $value = '', $checked = FALSE, $extra = '')
    {
        $defaults = array('type' => 'checkbox', 'name' => (!is_array($data) ? $data : ''), 'value' => $value);

        if (is_array($data) && array_key_exists('checked', $data)) {
            $checked = $data['checked'];

            if ($checked == FALSE) {
                unset($data['checked']);
            } else {
                $data['checked'] = 'checked';
            }
        }

        if ($checked == TRUE) {
            $defaults['checked'] = 'checked';
        } else {
            unset($defaults['checked']);
        }

        return '<input ' . _parse_form_attributes($data, $defaults) . stringifyAttributes($extra) . " />\n";
    }
}

if (!function_exists('form_radio')) {
    /**
     * @param mixed
     * @param string
     * @param bool
     * @param mixed
     * @return    string
     */
    function form_radio($data = '', $value = '', $checked = FALSE, $extra = '')
    {
        is_array($data) or $data = array('name' => $data);
        $data['type'] = 'radio';

        return form_checkbox($data, $value, $checked, $extra);
    }
}

if (!function_exists('form_submit')) {
    /**
     * @param mixed
     * @param string
     * @param mixed
     * @return    string
     */
    function form_submit($data = '', $value = '', $extra = '')
    {
        $defaults = array(
            'type' => 'submit',
            'name' => is_array($data) ? '' : $data,
            'value' => $value
        );

        return '<input ' . _parse_form_attributes($data, $defaults) . stringifyAttributes($extra) . " />\n";
    }
}

if (!function_exists('form_reset')) {
    /**
     * @param mixed
     * @param string
     * @param mixed
     * @return    string
     */
    function form_reset($data = '', $value = '', $extra = '')
    {
        $defaults = array(
            'type' => 'reset',
            'name' => is_array($data) ? '' : $data,
            'value' => $value
        );

        return '<input ' . _parse_form_attributes($data, $defaults) . stringifyAttributes($extra) . " />\n";
    }
}

if (!function_exists('form_button')) {
    /**
     * @param mixed
     * @param string
     * @param mixed
     * @return    string
     */
    function form_button($data = '', $content = '', $extra = '')
    {
        $defaults = array(
            'name' => is_array($data) ? '' : $data,
            'type' => 'button'
        );

        if (is_array($data) && isset($data['content'])) {
            $content = $data['content'];
            unset($data['content']); // content is not an attribute
        }

        return '<button ' . _parse_form_attributes($data, $defaults) . stringifyAttributes($extra) . '>'
            . $content
            . "</button>\n";
    }
}

if (!function_exists('form_label')) {
    /**
     * @param string    The text to appear onscreen
     * @param string    The id the label applies to
     * @param mixed    Additional attributes
     * @return    string
     */
    function form_label($label_text = '', $id = '', $attributes = [])
    {

        $label = '<label';

        if ($id !== '') {
            $label .= ' for="' . $id . '"';
        }

        $label .= stringifyAttributes($attributes);

        return $label . '>' . $label_text . '</label>';
    }
}

if (!function_exists('form_fieldset')) {
    /**
     *
     * @param string    The legend text
     * @param array    Additional attributes
     * @return    string
     */
    function form_fieldset($legend_text = '', $attributes = [])
    {
        $fieldset = '<fieldset' . stringifyAttributes($attributes) . ">\n";
        if ($legend_text !== '') {
            return $fieldset . '<legend>' . $legend_text . "</legend>\n";
        }

        return $fieldset;
    }
}

if (!function_exists('form_fieldset_close')) {
    /**
     * @param string
     * @return    string
     */
    function form_fieldset_close($extra = '')
    {
        return '</fieldset>' . $extra;
    }
}

if (!function_exists('form_close')) {
    /**
     * @param string
     * @return    string
     */
    function form_close($extra = '')
    {
        return '</form>' . $extra;
    }
}

if (!function_exists('_parse_form_attributes')) {
    /**
     * @param array $attributes List of attributes
     * @param array $default Default values
     * @return    string
     */
    function _parse_form_attributes($attributes, $default)
    {
        if (is_array($attributes)) {
            foreach ($default as $key => $val) {
                if (isset($attributes[$key])) {
                    $default[$key] = $attributes[$key];
                    unset($attributes[$key]);
                }
            }

            if (count($attributes) > 0) {
                $default = array_merge($default, $attributes);
            }
        }

        $att = '';

        foreach ($default as $key => $val) {
            if ($key === 'value') {
                $val = htmlEscape($val);
            } elseif ($key === 'name' && !strlen($default['name'])) {
                continue;
            }

            $att .= $key . '="' . $val . '" ';
        }

        return $att;
    }
}

if (!function_exists('set_checkbox')) {
    function set_checkbox($field, $value = '', $default = FALSE)
    {

        $value = (string) $value;
        isset($_POST[$field]) OR $_POST[$field] = array();

        $set = isset($_POST[$field])
            ? (is_array($_POST[$field]) ? in_array($value, $_POST[$field], TRUE) : ($_POST[$field] === $value))
            : ($default === TRUE);

        return $set ? 'checked="checked"' : '';
    }
}