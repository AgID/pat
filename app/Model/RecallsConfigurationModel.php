<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Scope\DeletedScope;
use System\Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Modello per la tabella recalls_configuration
 * Utilizzata per la gestione della configurazione dei richiami nelle pagine
 */
class RecallsConfigurationModel extends Model
{
    protected $table = 'recalls_configuration';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'section_fo_id',
        'section_bo_id',
        'recall_label',
        'recall_fo_id',
        'created_at',
        'updated_at'
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
    }

    /**
     * Relazione con Personale (Tabella rel_personnel_for_structures)
     * Rappresenta il personale responsabile per la struttura
     * @return HasMany
     */
    public function recalls(): HasMany
    {
        return $this->hasMany(RecallsModel::class, 'recalls_configuration_id', 'id');
    }

    /**
     * Relazione con Personale (Tabella rel_personnel_for_structures)
     * Rappresenta il personale responsabile per la struttura
     * @return BelongsTo
     */
    public function sectionBo(): BelongsTo
    {
        return $this->belongsTo(SectionsBoModel::class, 'section_bo_id');
    }
}
