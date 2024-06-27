<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use System\Model;

/**
 * Modello per la tabella rel_categories_attachments. Rappresenta la relazione tra le tabelle object_categories e object_attachments per la gestione degli allegati.
 */
class RelCategoriesAttachmentsModel extends Model
{
    protected $table = 'rel_categories_attachments';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'attachments_id',
        'categories_id',
        'institution_id',
        'archive',
        'id_archive',
        'created_at',
        'updated_at'
    ];
}
