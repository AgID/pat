<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Traits;

use Illuminate\Support\Facades\Schema;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

trait SearchLikeTrait
{
    public static function scopeSearch($query, $keyword, $matchAllFields = false)
    {
        return static::where(function ($query) use ($keyword, $matchAllFields) {

            foreach (static::getSearchableFields() as $field) {
                if ($matchAllFields) {
                    $query->where($field, 'LIKE', "%$keyword%");
                } else {
                    $query->orWhere($field, 'LIKE', "%$keyword%");
                }
            }

        });
    }

    public static function getSearchableFields()
    {
        $model = new static;

        $fields = $model->search;

        if (empty($fields)) {
            $fields = Schema::getColumnListing($model->getTable());

            $ignoredColumns = [
                $model->getKeyName(),
                $model->getUpdatedAtColumn(),
                $model->getCreatedAtColumn(),
            ];

            if (method_exists($model, 'getDeletedAtColumn')) {
                $ignoredColumns[] = $model->getDeletedAtColumn();
            }

            $fields = array_diff($fields, $model->getHidden(), $ignoredColumns);
        }

        return $fields;
    }
}