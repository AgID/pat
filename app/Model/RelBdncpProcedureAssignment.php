<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use System\Model;

/**
 * Modello per la tabella rel_bdncp_procedure_assignments.
 * Rappresenta la relazione tra le tabelle object_bdncp_procedure e object_assignments
 */
class RelBdncpProcedureAssignment extends Model
{
    protected $table = 'rel_bdncp_procedure_assignments';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'object_procedure_id',
        'object_assignment_id',
        'typology',
        'created_at',
        'updated_at'
    ];
}
