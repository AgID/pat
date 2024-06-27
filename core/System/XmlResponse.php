<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class XmlResponse
{
    public $xml;

    public function __construct($data)
    {
        if (!extension_loaded('simplexml')) {

            throw new \Exception("Class SimpleXMLElement not found");

        }

        $output = new \SimpleXMLElement("<?xml version=\"1.0\"?><response></response>");

        $this->arrayToXML((array)$data, $output);

        return $output->asXML();

    }

    protected function arrayToXML(array $data, &$output)
    {
        foreach ($data as $key => $value) {
            if (is_array($value)) {
                if (is_numeric($key)) {
                    $key = "item{$key}";
                }

                $subnode = $output->addChild("$key");
                $this->arrayToXML($value, $subnode);
            } else {
                if (is_numeric($key)) {
                    $key = "item{$key}";
                }

                $output->addChild("$key", htmlspecialchars("$value"));
            }
        }
    }
}
