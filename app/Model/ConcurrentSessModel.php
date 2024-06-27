<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Scope\InstitutionScope;
use System\Model;

/**
 * Modello per la tabella object_absence_rates, tassi_assenza
 */
class ConcurrentSessModel extends Model
{
    protected $table = 'concurrent_sess';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'institution_id',
        'user_id',
        'platform',
        'browser',
        'device',
        'ip',
        'browser_private_mode',
        'sess_id',
        'created_at',
        'updated_at'
    ];


    /**
     * Per non chiamare il global scope: NomeClasse::withoutGlobalScope(new HasActive)
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new InstitutionScope);
    }

    /**
     * Relazione con Strutture Organizzative (tabella object_structures)
     * Rappresenta la struttura di appartenenza
     * @return BelongsTo
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(InstitutionsModel::class, 'institution_id');
    }
}
