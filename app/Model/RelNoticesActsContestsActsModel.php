<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Modello per la tabella rel_contests_acts_contests_acts. Rappresenta la relazione tra la tabella object_contests_acts con se stessa
 */
class RelNoticesActsContestsActsModel extends Pivot
{
    protected $table = 'rel_notices_acts_contests_acts';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'object_notices_acts_id',
        'object_contests_acts_id',
        'created_at',
        'updated_at'
    ];
}
