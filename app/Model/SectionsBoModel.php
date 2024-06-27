<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Relations\HasOne;
use Scope\DeletedScope;
use System\Model;

/**
 * Modello per la tabella sections_bo, sono le sezioni Back-Office
 */
class SectionsBoModel extends Model
{
    protected $table = 'section_bo';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'parent_id',
        'addon_id',
        'name',
        'lineage',
        'deep',
        'search_sort',
        'categorization',
        'notify_app_io',
        'controller',
        'model',
        'model_class',
        'url',
        'icon',
        'hidden_profile_acl',
        'searchable',
        'hide',
        'icon',
        'deleted',
        'deleted_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Per non chiamare il global scope
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        //Per le pagine di sistema
        static::addGlobalScope(new DeletedScope);
    }

    /**
     * Relazione con le relative sezioni di front-office
     * @return HasOne
     */
    public function sectionFo(): HasOne
    {
        return $this->hasOne(SectionsFoModel::class, 'section_bo_id', 'id')
            ->select(['id', 'section_bo_id']);
    }
}
