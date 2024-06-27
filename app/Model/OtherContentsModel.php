<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

use Scope\InstitutionScope;
use System\Model;

/**
 * Modello per la tabella object_other_contents
 */
class OtherContentsModel extends Model
{
    protected $table = 'object_other_contents';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'owner_id',
        'institution_id',
        'state',
        'workflow_state',
        'title',
        'content',
        'created_at',
        'updated_at'
    ];

    /**
     * Per non chiamare il global scope
     * NomeClasse::withoutGlobalScope(new HasActive)
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new InstitutionScope);
    }
}
