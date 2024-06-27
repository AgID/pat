<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use System\Model;

/**
 * Modello per la tabella data_historical_personnel.
 * Sono i dati relativi allo storico incarichi
 */
class DataHistoricalPersonnelModel extends Model
{
    protected $table = 'data_historical_personnel';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'personnel_id',
        'historical_role',
        'historical_structure',
        'historical_from_date',
        'historical_to_date',
        'created_at',
        'updated_at'
    ];
}
