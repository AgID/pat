<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */


namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Modello per la tabella rel_modules_proceedings. Rappresenta la relazione tra le tabelle object_modules_regulations e object_proceedings
 */
class RelModulesProceedingsModel extends Pivot
{
    protected $table = 'rel_modules_proceedings';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'object_modules_regulations_id',
        'object_proceedings_id',
        'created_at',
        'updated_at'
    ];
}
