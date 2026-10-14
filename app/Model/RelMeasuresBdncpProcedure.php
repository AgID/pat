<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use System\Model;

/**
 * Modello per la tabella rel_measures_bdncp_procedure.
 * Rappresenta la relazione tra le tabelle object_measures e object_bdncp_procedure
 */
class RelMeasuresBdncpProcedure extends Model
{
    protected $table = 'rel_measures_bdncp_procedure';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'object_measures_id',
        'object_procedure_id',
        'created_at',
        'updated_at'
    ];
}
