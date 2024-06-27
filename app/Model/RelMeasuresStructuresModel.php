<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Modello per la tabella rel_measures_structures. Rappresenta la relazione tra le tabelle object_measures e object_structures
 * @method static where(string $string, mixed $structureId)
 */
class RelMeasuresStructuresModel extends Pivot
{
    protected $table = 'rel_measures_structures';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'object_measures_id',
        'object_structures_id',
        'created_at',
        'updated_at'
    ];
}
