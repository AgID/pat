<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use System\Model;

/**
 * Modello per la tabella rel_sectios_fo_assignments. Rappresenta la relazione tra le tabelle structsections_fo e object_assignments
 */
class RelSectiosFoAssignmentsModel extends Model
{
    protected $table = 'rel_sectios_fo_assignments';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'structsections_fo_id',
        'object_assignments_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
