<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use System\Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Modello per la tabella users
 */
class PasswordHistoryModel extends Model
{
    protected $table = 'password_history';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'user_id',
        'password',
        'created_at',
        'updated_at'
    ];


    /**
     * Relazione con users
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo(UsersModel::class, 'user_id');
    }
}
