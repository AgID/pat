<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Modello per la tabella rel_real_estate_heritage_structures. Rappresenta la relazione tra le tabelle object_real_estate_heritage e object_structures
 * @method static where(string $string, mixed $structureId)
 */
class RelRealEstateAssetStructuresModel extends Pivot
{
    protected $table = 'rel_real_estate_asset_structures';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'object_real_estate_asset_id',
        'object_structures_id',
        'created_at',
        'updated_at'
    ];
}
