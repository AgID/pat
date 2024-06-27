<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Relations\HasOne;
use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Modello per la tabella rel_users_acl_profiles. Rappresenta la relazione tra le tabelle users e acl_profiles
 */
class RelUsersAclProfilesModel extends Pivot
{
    protected $table = 'rel_users_acl_profiles';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'user_id',
        'acl_profile_id',
        'created_at',
        'updated_at',
    ];

    /**
     * @return HasOne
     * Relazione AclProfile
     * Rappresenta l'Ente di appartenenza.
     */
    public function profile(): HasOne
    {
        return $this->hasOne(AclProfilesModel::class, 'id', 'acl_profile_id');
    }
}
