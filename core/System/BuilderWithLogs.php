<?php
/**
 *  Nome applicativo: PAT
 *  Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

use Illuminate\Database\Eloquent\Builder;

class BuilderWithLogs extends \Illuminate\Database\Eloquent\Builder
{
    public function updateWithLogs($element, array $attributes = [], bool $log = true)
    {
        return $this->getModel()->updateWithLogs($element, $attributes, $log);
    }

    public static function createWithLogs(array $options = [])
    {
        return self::createWithLogs($options);
    }

    public function deleteWithLogs($element)
    {
        return $this->getModel()->deleteWithLogs($element);
    }
}
