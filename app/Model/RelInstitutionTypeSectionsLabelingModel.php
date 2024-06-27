<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Modello per la tabella rel_institution_type_sections_labeling. Rappresenta la relazione tra le tabelle institution_type e sections - Utilizzata per la gestione delle traduzioni dei ruoli.
 */
class RelInstitutionTypeSectionsLabelingModel extends Pivot
{
    protected $table = 'rel_institution_type_sections_labeling';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'institution_type_id',
        'institution_id',
        'sections_id',
        'label',
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
//        static::addGlobalScope(new InstitutionScope);
    }
}
