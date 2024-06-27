<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use System\Model;

/**
 * Modello per la tabella rel_personnel_sections_fo. Rappresenta la relazione tra le tabelle object_personnel e sections_fo
 */
class RelPersonnelSectionsFOModel extends Model
{
    protected $table = 'rel_personnel_sections_fo';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'object_personnel_id',
        'sections_fo_id',
        'created_at',
        'updated_at',
        'deleted_at'
    ];
}
