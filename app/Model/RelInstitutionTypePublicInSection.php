<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Relations\Pivot;

/**
 * Modello per la tabella rel_institution_type_public_in_section.
 * Rappresenta la relazione tra i tipi ente e le configurazioni per il pubblica in
 * Aggiungendo un record, si aggiunge una voce alla select per il pubblica in per il tipo ente
 */
class RelInstitutionTypePublicInSection extends Pivot
{
    protected $table = 'rel_institution_type_public_in_section';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'institution_type_id',
        'section_public_in_id',
        'label'
    ];
}
