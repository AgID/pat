<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Modello per la tabella rel_proceedings_normatives. Rappresenta la relazione tra le tabelle object_proceedings e object_normatives
 */
class RelProceedingsNormativesModel extends Pivot
{
    protected $table = 'rel_proceedings_normatives';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'object_proceedings_id',
        'object_normatives_id',
        'created_at',
        'updated_at'
    ];
}
