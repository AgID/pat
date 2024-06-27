<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use System\Model;

/**
 * Modello per la tabella attempts
 */
class AttemptsModel extends Model
{
    protected $table = 'attempts';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'ip',
        'client_info',
        'created_at',
        'updated_at'
    ];
}
