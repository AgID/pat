<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use System\Model;

/**
 * Modello per la tabella permits
 * Permessi degli utenti sulle sezioni in base ai suoi profili ACL
 */
class PermitsModel extends Model
{
    protected $table = 'permits';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'acl_profiles_id',
        'sections_bo_id',
        'sections_fo_id',
        'institution_id',
        'create',
        'read',
        'update',
        'delete',
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
//        static::addGlobalScope(new InstitutionScope);
    }
}
