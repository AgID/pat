<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

use Exception;
use Illuminate\Database\Eloquent\Relations\HasMany;
use System\Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Modello per la tabella recalls
 * Utilizzata per la gestione dei richiami nelle pagine dei record degli oggetti
 */
class RecallsModel extends Model
{
    protected $table = 'recalls';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'recalls_configuration_id',
        'record_id',
        'institution_id',
        'paragraph_id',
        'created_at',
        'updated_at'
    ];

    /**
     * Per non chiamare il global scope
     * NomeClasse::withoutGlobalScope(new ScopeName)
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();
    }

    /**
     *
     * @param string $model Modello del richiamo
     * @return HasMany
     * @throws Exception
     */
    public function items(string $model): HasMany
    {
        $model = config($model, null, 'modelConfigs')['model'];

        return $this->hasMany($model, 'recalls_configuration_id', 'id');
    }
}
