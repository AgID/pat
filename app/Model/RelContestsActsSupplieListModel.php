<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Modello per la tabella rel_contests_acts_supplie_list. Rappresenta la relazione tra le tabelle object_contests_acts e object_supplie_list
 */
class RelContestsActsSupplieListModel extends Pivot
{
    protected $table = 'rel_contests_acts_supplie_list';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'object_contest_act_id',
        'object_supplie_list_id',
        'typology',
        'created_at',
        'updated_at'
    ];
}
