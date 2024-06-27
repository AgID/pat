<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Modello per la tabella rel_contest_assignments. Rappresenta la relazione tra le tabelle object_contest e object_assignments
 */
class RelContestAssignmentsModel extends Pivot
{
    protected $table = 'rel_contest_assignments';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'object_contest_id',
        'object_assignments_id',
        'created_at',
        'updated_at'
    ];
}
