<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Modello per la tabella rel_assignments_notices_acts. Rappresenta la relazione tra le tabelle object_ac_assignments e object_notices_acts
 */
class RelAssignmentsNoticesActsModel extends Pivot
{
    protected $table = 'rel_assignments_notices_acts';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'object_assignments_id',
        'object_notices_acts_id',
        'created_at',
        'updated_at'
    ];
}
