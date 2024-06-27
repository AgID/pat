<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Modello per la tabella rel_contests_act_requirements.
 * Rappresenta la relazione tra le tabelle object_contest_acts e object_notices_for_qualification_requirements
 */
class RelContestsActRequirementsModel extends Pivot
{
    protected $table = 'rel_contests_act_requirements';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'object_contest_act_id',
        'object_requirement_id',
        'created_at',
        'updated_at'
    ];
}
