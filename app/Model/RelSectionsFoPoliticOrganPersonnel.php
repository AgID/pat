<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');


use Illuminate\Database\Eloquent\Relations\HasOne;
use System\Model;

/**
 * Modello per la tabella rel_sections_fo_personnell. Rappresenta la relazione tra le tabelle sections_fo e object_personnell
 */
class RelSectionsFoPoliticOrganPersonnel extends Model
{
    protected $table = 'rel_sections_fo_politic_organ_personnel';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'institution_id',
        'section_fo_id',
        'object_personnel_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
    
    protected static function boot()
    {
        parent::boot();
    }


}
