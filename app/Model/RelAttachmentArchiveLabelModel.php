<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Relations\HasOne;
use System\Model;

class RelAttachmentArchiveLabelModel extends Model
{
    protected $table = 'rel_attachment_label_archive';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'label_id',
        'archive_id', // ID Section FO
        'created_at',
        'updated_at'
    ];

    /**
     * Relazione con gli etichetta allegato (Tabella attachment_label)
     * @return HasOne
     */
    public function label(): HasOne
    {
        return $this->hasOne(AttachmentLabelModel::class, 'id', 'label_id');
    }
}