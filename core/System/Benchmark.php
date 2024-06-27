<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

defined('_FRAMEWORK_') or exit('No direct script access allowed');


class Benchmark
{

    public $marker = array();


    public function mark($name)
    {
        $this->marker[$name] = microtime(TRUE);
    }


    public function elapsedTime($point1 = '', $point2 = '', $decimals = 4)
    {
        if ($point1 === '') {
            return '{elapsed_time}';
        }

        if (!isset($this->marker[$point1])) {
            return '';
        }

        if (!isset($this->marker[$point2])) {
            $this->marker[$point2] = microtime(TRUE);
        }

        return  \Helpers\S::currency($this->marker[$point2] - $this->marker[$point1], $decimals);
    }


    public function memoryUsage()
    {
        return '{memory_usage}';
    }
}
