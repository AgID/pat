<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Modello per la tabella rel_normative_references_sections_fo. Rappresenta la relazione tra le tabelle object_normative_references e sections_fo - Utilizzata per gestire le sezioni.
 */
class RelNormativeReferencesSectionsFoModel extends Pivot
{
    protected $table = 'rel_normative_references_sections_fo';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'normative_references_id',
        'sections_fo_id',
        'created_at',
        'updated_at'
    ];
}
