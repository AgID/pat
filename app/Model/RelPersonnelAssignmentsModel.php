<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Modello per la tabella rel_personnel_assignments. Rappresenta la relazione tra le tabelle object_personnel e object_assignments
 */
class RelPersonnelAssignmentsModel extends Pivot
{
    protected $table = 'rel_personnel_assignments';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'object_personnel_id',
        'object_assignments_id',
        'created_at',
        'updated_at'
    ];
}
