<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Modello per la tabella rel_interventions_regulations. Rappresenta la relazione tra le tabelle object_interventions e object_regulations
 */
class RelInterventionsRegulationsModel extends Pivot
{
    protected $table = 'rel_interventions_regulations';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'object_interventions_id',
        'object_regulations_id',
        'created_at',
        'updated_at'
    ];
}
