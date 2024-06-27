<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Relations\HasOne;
use Scope\InstitutionScope;
use System\Model;

/**
 * Modello per la tabella recovery_password
 * Per il recupero password
 */
class RecoveryPassword extends Model
{
    protected $table = 'recovery_password';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'user_id',
        'institution_id',
        'token',
        'created_at',
        'updated_at'
    ];

    /**
     * Relazione con gli Utenti
     * @return HasOne
     */
    public function user(): HasOne
    {
        return $this->hasOne('Model\UsersModel', 'id', 'user_id');
    }

    /**
     * Per non chiamare il global scope
     * NomeClasse::withoutGlobalScope(new HasActive)
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new InstitutionScope);
    }
}
