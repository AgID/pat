<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

use Scope\InstitutionScope;
use System\Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Modello per la tabella configs
 */
class ConfigsModel extends Model
{
    protected $table = 'configs';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'institution_id',
        'opt_key',
        'opt_value',
        'opt_group',
        'created_at',
        'updated_at',
        'deleted_at'
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
