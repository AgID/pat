<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use System\Model;

/**
 * Modello per la tabella sessions
 */
class SessionsModel extends Model
{
    protected $table = 'sessions';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'data',
        'expire',
        'ip',
    ];
}
