<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use System\Model;

class AttachmentLabelModel extends Model
{
    protected $table = 'attachment_label';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'name',
        'created_at',
        'updated_at'
    ];
}
