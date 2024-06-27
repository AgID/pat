<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

use Illuminate\Database\Eloquent\Relations\Pivot;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Modello per la tabella rel_personnel_political_organs.
 * Rappresenta la relazione tra le tabelle object_personnel e gli organi politici
 */
class RelPersonnelPoliticalOrgansModel extends Pivot
{
    protected $table = 'rel_personnel_political_organ';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'object_personnel_id',
        'political_organ_id',
        'created_at',
        'updated_at'
    ];
}
