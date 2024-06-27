<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Modello per la tabella rel_normatives_structures. Rappresenta la relazione tra le tabelle object_normatives e object_structures
 * @method static insert(array $dataNormatives)
 * @method static where(string $string, mixed $structureId)
 */
class RelNormativesStructuresModel extends Pivot
{
    protected $table = 'rel_normatives_structures';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'object_normatives_id',
        'object_structures_id',
        'typology',
        'created_at',
        'updated_at'
    ];
}
