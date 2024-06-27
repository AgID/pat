<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Modello per la tabella rel_lease_canons_real_estate_heritage. Rappresenta la relazione tra le tabelle object_lease_canons e object_real_estate_asset
 */
class RelLeaseCanonsRealEstateAssetModel extends Pivot
{
    protected $table = 'rel_lease_canons_real_estate_asset';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'object_lease_canons_id',
        'object_real_estate_asset_id',
        'deleted',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
