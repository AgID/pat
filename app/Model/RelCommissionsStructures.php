<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Modello per la tabella rel_commissions_structures. Rappresenta la relazione tra le tabelle object_commissions eobject_structures
 */
class RelCommissionsStructures extends Pivot
{
    protected $table = 'rel_commissions_structures';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'object_commissions_id',
        'typology',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

}
