<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Modello per la tabella rel_personnel_company. Rappresenta la relazione tra le tabelle object_personnel e object_company
 */
class RelPersonnelCompanyModel extends Pivot
{
    protected $table = 'rel_personnel_company';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'object_personnel_id',
        'object_company_id',
        'created_at',
        'updated_at'
    ];
}
