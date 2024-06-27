<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

use Illuminate\Database\Eloquent\Relations\Pivot;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Modello per la tabella rel_proceedings_personnel. Rappresenta la relazione tra le tabelle object_proceedings e object_personnel
 */
class RelProceedingsPersonnelModel extends Pivot
{
    protected $table = 'rel_proceedings_personnel';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'object_proceedings_id',
        'object_personnel_id',
        'typology',
        'created_at',
        'updated_at'
    ];
}
