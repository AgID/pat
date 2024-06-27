<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use System\Model;

/**
 * Modello per la tabella data_monitoring_proceedings.
 * Sono i dati relativi al monitoraggio dei tempi procedimentali
 */
class DataMonitoringProceedings extends Model
{
    protected $table = 'data_monitoring_proceedings';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'proceeding_id',
        'year',
        'year_concluded_proceedings',
        'conclusion_days',
        'percentage_year_concluded_proceedings',
        'created_at',
        'updated_at'
    ];
}
