<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

use Illuminate\Database\Eloquent\Builder;

class BuilderWithLogs extends \Illuminate\Database\Eloquent\Builder
{
    public function updateWithLogs($element, array $attributes = [], bool $log = true, int $userId = null, string $origin = 'web')
    {
        return $this->getModel()->updateWithLogs($element, $attributes, $log, $userId, $origin);
    }

    public function createWithLogs(array $options = [], int $userId = null, string $origin = 'web', bool $storeLog = true)
    {
        return $this->getModel()->createWithLogs($options, $userId, $origin, $storeLog);
    }

    public function deleteWithLogs($element, int $userId = null, string $origin = 'web')
    {
        return $this->getModel()->deleteWithLogs($element, $userId, $origin);
    }
}
