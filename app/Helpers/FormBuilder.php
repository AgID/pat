<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Helpers;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class FormBuilder
{
    static $formOpen = 'form_open';
    static $formOpenMultipart = 'form_open_multipart';
    static $formHidden = 'form_hidden';
    static $formInput = 'form_input';
    static $formPassword = 'form_password';
    static $formUpload = 'form_upload';
    static $formTextarea = 'form_textarea';
    static $formMultiselect = 'form_multiselect';
    static $formDropdown = 'form_dropdown';
    static $formCheckbox = 'form_checkbox';
    static $formRadio = 'form_radio';
    static $formSubmit = 'form_submit';
    static $formReset = 'form_reset';
    static $formButton = 'form_button';
    static $formLabel = 'form_label';
    static $formFieldset = 'form_fieldset';
    static $formFieldsetClose = 'form_fieldset_close';
    static $formClose = 'form_close';
    static $include = 'include';

    public $data;
    public $ext;
    public $type;
    private $inputPermissions = [

        // Helper Form
        'form_open',
        'form_open_multipart',
        'form_hidden',
        'form_input',
        'form_password',
        'form_upload',
        'form_textarea',
        'form_multiselect',
        'form_dropdown',
        'form_checkbox',
        'form_radio',
        'form_submit',
        'form_reset',
        'form_button',
        'form_label',
        'form_fieldset',
        'form_fieldset_close',
        'form_close',

        'tag',

        // Options Data
        'include'

    ];
    private $parseData = null;
    private $token = true;

    const JSON_NORMAL = null;
    const JSON_TAGS = 'JSON_HEX_TAG';
    const JSON_APOS = 'JSON_HEX_APOS';
    const JSON_QUOT = 'JSON_HEX_QUOT';
    const JSON_AMP = 'JSON_HEX_AMP';
    const JSON_UNICODE = 'JSON_UNESCAPED_UNICODE';
    const JSON_ALL = 'JSON_HEX_TAG | JSON_HEX_APOS | JSON_HEX_QUOT | JSON_HEX_AMP | JSON_UNESCAPED_UNICODE';

    public function __construct($parseData = null, $token = true)
    {
        $this->reset();

        if ($parseData != null && is_array($parseData)) {
            $this->parseData = $parseData;
        }

        $this->token = $token;
        $this->data = null;
        helper('form');
    }

    public function loadArrayFromFile($fileName)
    {

        $fileName = $this->removeExtension($fileName);

        try {

            if (!file_exists($fileName . ".php")) {

                throw new \Exception("{$fileName} was not found!");
            }

        } catch (Exception $e) {

            echo $e->getMessage();
        }

        $this->ext = 'php';
        $this->type = 'array';
        $this->data = include($fileName . ".php");

        return $this;
    }

    public function loadArrayFromVar($data = null)
    {
        try {

            if ($data == null && !is_array($data)) {

                throw new \Exception("Input value not completed. the function expects an array!");
            }

        } catch (Exception $e) {

            echo $e->getMessage();
        }

        $this->ext = null;
        $this->type = 'json';
        $this->data = $data;

        return $this;
    }

    public function loadJsonFromVar($data = null)
    {
        try {

            if ($data == null && !is_object(json_decode($data))) {

                throw new \Exception("Input value not completed. the function expects a json");
            }

        } catch (Exception $e) {

            echo $e->getMessage();
        }

        $this->ext = null;
        $this->type = 'array';
        $this->data = json_decode($data, JSON_PRETTY_PRINT);

        return $this;
    }

    public function loadJsonFromFile($filePath)
    {
        $filePath = str_replace(['.php', '.json'], ['', ''], $filePath);

        try {

            if (!file_exists($filePath . ".php") || !file_exists($filePath . ".json")) {

                throw new \Exception("{$filePath} was not found!");
            }

        } catch (Exception $e) {

            echo $e->getMessage();

        }

        if (file_exists($filePath . ".json")) {

            $this->ext = 'json';
            $this->data = json_decode(file_get_contents($filePath . ".json"), true);

        } elseif (file_exists($filePath . ".php")) {

            $this->ext = 'php';
            $this->data = include($filePath . ".php");

        }

        return $this;
    }

    public function getDataFromArray()
    {
        return $this->data;
    }

    public function getDataFromJson($type = null)
    {
        return json_encode($this->data, $type);
    }

    public function loadFromString($string)
    {
        $this->data = $string;
    }

    public function render()
    {
        return $this->buildForm();
    }

    public function display()
    {
        echo $this->buildForm();
    }

    private function stringifyAttributes($attributes = '', $js = FALSE)
    {
        $atts = NULL;

        if (empty($attributes)) {

            return $atts;

        }

        if (is_string($attributes)) {

            return ' ' . $attributes;

        }

        $attributes = (array)$attributes;

        foreach ($attributes as $key => $val) {

            $atts .= ($js) ? $key . '=' . $val . ',' : ' ' . $key . '="' . $val . '"';

        }

        return rtrim($atts, ',');
    }

    protected function buildForm()
    {
        $formBuild = '';

        if (!empty($this->data)) {

            $i = 0;
            $formSuffix = '';
            foreach ($this->data as $data) {

                if (!empty($data['type'])) {

                    if (in_array($data['type'], $this->inputPermissions)) {

                        $callable = $data['type'];

                        $formBuild .= (!empty($data['prefix']) && strlen((string) $data['prefix']) >= 1) ? $data['prefix'] : null;

                        if ($callable === 'form_open') {

                            if (!empty($data['suffix'])) {
                                $formSuffix = $data['suffix'];
                                unset($data['suffix']);
                            }

                            $formBuild .= $this->formOpen($data);


                        } elseif ($callable === 'form_hidden') {

                            if (!empty($data['attributes'])) {

                                $data['attributes']['type'] = 'hidden';
                                $formBuild .= form_input($data['attributes']);

                            } else {

                                if (!empty($data['name']) && !empty($data['value'])) {

                                    $formBuild .= form_hidden($data['name'], $data['value']);

                                }

                            }

                        } elseif ($callable === 'form_button') {

                            if (!empty($data['prefix'])) {

                                $formBuild .= $data['prefix'];
                            }

                            $formBuild .= form_button($data['attributes']);

                            if (!empty($data['suffix'])) {

                                $formBuild .= $data['suffix'];
                            }

                        } elseif (in_array($callable, ['form_dropdown', 'form_multiselect'])) {


                            if (!empty($data['label'])) {

                                $formBuild .= $this->setLabel($data['label']);

                            }

                            $name = !empty($data['name']) ? $data['name'] : null;
                            $options = !empty($data['options']) ? $data['options'] : null;
                            $value = !empty($data['value']) ? $this->parse($data['value']) : null;
                            $extra = !empty($data['extra']) ? $data['extra'] : null;
                            $formBuild .= $this->formDropdown($name, $options, $value, $extra);

                        } elseif (in_array($callable, ['form_close', 'form_fieldset_close'])) {

                            $extra = !empty($data['extra']) ? $data['extra'] : null;
                            $formBuild .= form_close($extra) . $formSuffix;

                        } elseif ($callable === 'include') {

                            $pathFileName = $data['path'];
                            $vars = $data['vars'];
                            $formBuild .= $this->includeWithVars($pathFileName, $vars);

                        } elseif ($callable === 'tag') {

                            $formBuild .= $data['content'];

                        } else {

                            if (!empty($data['label'])) {

                                $formBuild .= $this->setLabel($data['label']);

                            }

                            if (is_callable($callable)) {

                                array_map(function ($k, $v) {
                                    return [
                                        $k => $this->parse($v, false)
                                    ];
                                }, array_keys($data['attributes']), $data['attributes']);

                                $formBuild .= $callable($data['attributes']);

                            }

                        }

                    }

                    $formBuild .= (!empty($data['suffix']) && strlen((string) $data['suffix']) >= 1) ? $data['suffix'] : null;
                }
            }

            $i++;
        }

        return $this->parse($formBuild);
    }

    private function parse($template = null, $single = true)
    {
        $html = '';
        $patterns = null;
        $replacements = null;

        if (!empty($template) && strlen((string) $template) > 1) {

            if (!empty($this->parseData) && is_array($this->parseData)) {

                foreach ($this->parseData as $key => $value) {

                    if ((bool)preg_match('#{' . $key . '}#s', (string)$template)) {

                        if (!$single) {

                            $html = preg_replace('/{' . $key . '}/', (string) $value, (string) $template);

                        } else {

                            $patterns[] = '/{' . $key . '}/';
                            $replacements[] = $value;

                        }

                    }

                }

                if ($patterns != null && $replacements != null) {

                    $replacements = !empty($replacements) && is_array($replacements) ? $replacements : (string) $replacements;
                    $html = preg_replace($patterns, $replacements, (string) $template);

                } else {

                    $html = $template;

                }

            } else {

                $html = $template;

            }

        }

        return $html;
    }

    private function setLabel($data)
    {

        $html = '';

        if (is_array($data)) {

            if (!empty($data['text'])) {

                $text = $data['text'];
                unset($data['text']);

            } else {

                $text = null;

            }

            $html .= '<label';
            $html .= $this->stringifyAttributes($data);
            $html .= '>' . $text . '</label>';

        } else {

            $html .= $data;

        }

        return $html;
    }

    protected function isJson($string)
    {
        @json_decode($string);
        return (bool)json_last_error() === JSON_ERROR_NONE;
    }

    protected function fileExtension($string)
    {
        $number = strrpos($string, '.');
        return ($number === false) ? '' : substr($string, $number + 1);
    }

    private function removeExtension($fileName)
    {
        return str_replace(['.php', '.json'], ['', ''], (string) $fileName);
    }

    public static function parseOptions($options = [], $selected = [])
    {
        is_array($selected) or $selected = array($selected);
        is_array($options) or $options = array($options);

        $form = '';

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
                        . $optgroup_val . "</option>\n";
                }

                $form .= "</optgroup>\n";

            } else {

                $form .= '<option value="' . htmlEscape($key) . '"'
                    . (in_array($key, $selected) ? ' selected="selected"' : '') . '>'
                    . $val . "</option>\n";
            }
        }

        return $form;
    }

    private function formOpen($data)
    {
        $action = !empty($data['action']) ? $data['action'] : currentQueryStringUrl();
        $attributes = !empty($data['attributes']) && is_array($data['attributes']) ? $data['attributes'] : null;
        $hidden = !empty($data['hidden']) && is_array($data['hidden']) ? $data['hidden'] : null;

        if(!isset($data['csfr_token'])) {

            $CSRFToken= null;

        } else  {

            $CSRFToken = $data['csfr_token'];
            
        } 

        return form_open($action, $attributes, $hidden, $CSRFToken);
    }

    private function formDropdown($data = '', $options = [], $selected = null, $extra = '')
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


        if (is_string($options) && !preg_match('#{*?}#s', $options)) {

            $form .= $options;

        } else {


            $hasTplString = (!empty($options[0]) && (bool)preg_match('#{*?}#s', (string)$options[0]))
                ? true
                : false;

            if (!$hasTplString) {

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
                                . $optgroup_val . "</option>\n";
                        }

                        $form .= "</optgroup>\n";
                    } else {
                        $form .= '<option value="' . htmlEscape($key) . '"'
                            . (in_array($key, $selected) ? ' selected="selected"' : '') . '>'
                            . $val . "</option>\n";
                    }
                }

            } else {

                $form .= $options[0];

            }
        }

        $form .= "</select>\n";

        return $form;

    }

    private function includeWithVars($pathFileName = null, $vars = [])
    {
        $fileName = $this->removeExtension($pathFileName);

        try {

            if (!file_exists($fileName . ".php")) {

                throw new \Exception("{$fileName} was not found!");
            }

        } catch (Exception $e) {

            echo $e->getMessage();

        }

        ob_start();
        extract($vars, EXTR_SKIP);
        include($fileName . '.php');
        $content = ob_get_clean();
        return $content;
    }

    private function reset()
    {
        $this->data = null;
        $this->ext = null;
        $this->type = null;
    }
}