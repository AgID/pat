<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Scope;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Model;
use Illuminate\Database\Eloquent\Scope;

/**
 * Classe che implementa nella query il filtro per gli elementi non eliminati
 */
class DeletedScope implements Scope
{
    /**
     * @param Builder $builder Query
     * @param Model   $model   Modello
     * @return void
     */
    public function apply(Builder $builder, Model $model): void
    {
        $builder->where($model->getTable() . '.deleted', 0);
    }
}
