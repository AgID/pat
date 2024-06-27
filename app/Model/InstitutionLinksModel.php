<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Scope\InstitutionScope;
use System\Model;

/**
 * Modello per lo storage dei link personalizzati delle'ente.
 */
class InstitutionLinksModel extends Model
{

    protected $table = 'institution_links';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'institution_id',
        'sort',
        'position',
        'title',
        'url',
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
