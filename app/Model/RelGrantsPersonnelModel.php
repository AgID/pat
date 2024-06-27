<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Modello per la tabella rel_grants_personnel. Rappresenta la relazione tra le tabelle object_grants e object_personnel
 */
class RelGrantsPersonnelModel extends Pivot
{
    protected $table = 'rel_grants_personnel';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'object_grants_id',
        'object_personnel_id',
        'created_at',
        'updated_at'
    ];
}
