<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Relations\HasMany;
use Scope\DeletedScope;

use System\Model;

class AttachmentsModel extends Model
{
    protected $table = 'attachments';
    protected $primaryKey = 'id';

    protected $fillable = [
        'id',
        'institution_id',
        'source_id',
        'cat_id',
        'owner_id',
        'o_id',
        'sort',
        'bdncp_cat',
        'archive_name',
        'archive_id',
        'file_name',
        'file_type',
        'file_path',
        'full_path',
        'raw_name',
        'orig_name',
        'client_name',
        'file_ext',
        'file_size',
        'is_image',
        'image_width',
        'image_height',
        'image_type',
        'image_size_str',
        'fingerprint',
        'label',
        'indexable',
        'active',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    /**
     * Per non chiamare il global scope
     * NomeClasse::withoutGlobalScope(new HasActive)
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

        static::addGlobalScope(new DeletedScope);

        static::updating(function ($model) {
            $model->timestamps = false;
        });
    }

    /**
     * @return HasMany
     */
    public function meta(): HasMany
    {
        return $this->relationFromAction('r_d_o', [
            'n' => __FUNCTION__
        ]);
    }



}
