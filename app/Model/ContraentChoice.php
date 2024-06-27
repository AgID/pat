<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use System\Model;
use Traits\SearchableTrait;

/**
 * Modello per la tabella object_contracting_stations
 */
class ContraentChoice extends Model
{
    use SearchableTrait;

    protected $table = 'contraent_choice';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'name',
        'disabled'
    ];

    /**
     * Campi su cui effettuare la ricerca
     * @var string[]
     */
    protected $searchable = [
        'name'
    ];
}
