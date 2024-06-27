<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Modello per la tabella rel_supplie_list. Rappresenta la relazione tra la tabella object_supplie_list e se stessa.
 */
class RelSupplieListModel extends Pivot
{
    protected $table = 'rel_supplie_list';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'object_supplie_list_id',
        'object_related_supplie_list',
        'typology',
        'created_at',
        'updated_at'
    ];
}
