<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

use System\Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Modello per la tabella object_personnel_qualifications. Rappresenta le qualifiche per il personale
 */
class PersonnelQualificationsModel extends Model
{

    protected $table = 'object_personnel_qualifications';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'name',
        'view_in_personnel_form',
        'institution_id',
        'created_at',
        'updated_at'
    ];

    /**
     * Per non chiamare il global scope
     * NomeClasse::withoutGlobalScope(new HasActive)
     */
    protected static function boot(): void
    {
        parent::boot();

    }
}
