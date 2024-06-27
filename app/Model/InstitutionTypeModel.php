<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */


namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Relations\HasMany;
use System\Model;
use Traits\SearchableTrait;

/**
 * Modello per la tabella institution_type
 */
class InstitutionTypeModel extends Model
{
    use SearchableTrait;

    protected $table = 'institution_type';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'owner_id',
        'label_institution_type_id',
        'state',
        'workflow_state',
        'name',
        'created_at',
        'updated_at'
    ];

    /**
     * Campi su cui effettuare la ricerca nel datatable
     *
     * @var string[]
     */
    protected $searchable = [
        'name'
    ];

    /**
     * Per non chiamare il global scope
     * NomeClasse::withoutGlobalScope(new HasActive)
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

    }

    /**
     * Relazione con RelSectionExcludedModel
     * Relazione "OneToMany" per prelevare le sezioni escluse per l'ente
     *
     * @return HasMany
     */
    public function excluded_sections(): HasMany
    {
        return $this->hasMany(RelSectionExcludedModel::class, 'institution_type_id', 'id');
    }
}
