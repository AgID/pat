<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Modello per la tabella rel_charges_proceedings. Rappresenta la relazione tra le tabelle object_charges e object_proceedings
 */
class RelChargesProceedingsModel extends Pivot
{
    protected $table = 'rel_charges_proceedings';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'object_charges_id',
        'object_proceedings_id',
        'created_at',
        'updated_at'
    ];
}
