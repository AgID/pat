<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use System\Model;

/**
 * Modello per la tabella rel_regulations_structures. Rappresenta la relazione tra le tabelle object_regulations e object_structures
 * @method static where(string $string, mixed $structureId)
 */
class RelRegulationsStructuresModel extends Model
{
    protected $table = 'rel_regulations_structures';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'object_regulations_id',
        'object_structures_id',
        'created_at',
        'updated_at'
    ];
}
