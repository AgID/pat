<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace System;

use Illuminate\Database\Eloquent\Builder;

class BuilderContestAct extends \Illuminate\Database\Eloquent\Builder
{
    public function updateContestActs($noticeId, array $attributes = [], array $options = [])
    {
        return $this->getModel()->updateContestActs($noticeId, $attributes, $options);
    }
}
