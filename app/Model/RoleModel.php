<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use System\Model;

/**
 * Modello per la tabella role. Rappresenta i ruoli per il personale
 * @method static where(string $string, string $string1, int $int)
 */
class RoleModel extends Model
{
    protected $table = 'role';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'name',
        'standard',
        'political',
        'institution_id',
        'created_at',
        'updated_at'
    ];
}
