<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use System\Model;

/**
 * Modello per la tabella select_data
 * @method static where(string $string, string $string1, string $string2)
 */
class SelectDataModel extends Model
{
    protected $table = 'select_data';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'institution_id',
        'typology',
        'value',
        'is_default'
    ];
}
