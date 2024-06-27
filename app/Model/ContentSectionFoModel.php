<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Scope\InstitutionScope;
use System\Model;
use Traits\SearchableTrait;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class ContentSectionFoModel extends Model
{
    use SearchableTrait;

    protected $table = 'content_section_fo';
    protected $primaryKey = 'id';
    protected $fillable = [
        'owner_id',
        'section_fo_id',
        'section_fo_parent_id',
        'institution_id',
        'user_id',
        'name',
        'sort',
        'content',
        'last_update_date',
        'created_at',
        'updated_at'
    ];

    /**
     * Campi su cui effettuare la ricerca nel datatable
     * @var string[]
     */
    protected $searchable = [
        'name',
        'content'
    ];

    /**
     * Costruttore, viene aggiunta la ricerca per Ente se l'utente è SuperAdmin
     * @param array $attributes Array di valori
     * @throws Exception
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
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

    /**
     * Relazione con InstitutionsModel
     * Rappresenta l'Ente di appartenenza.
     * @return BelongsTo
     */
    public function institution(): BelongsTo
    {

        return $this->belongsTo(InstitutionsModel::class, 'institution_id');
    }

    /**
     * Scope locale per il filtraggio in base all'ente
     *
     * @param Builder $query Query
     * @return void
     */
    public function scopeInstitution(Builder $query): void
    {
        // Customizzazioni filtraggio enti
        $getIdentity = authPatOs()->getIdentity();
        $institutionId = checkAlternativeInstitutionId();

        if ($institutionId != 0) {
            $query->where('institution_id', $institutionId);
        }

    }

    /**
     * Relazione con gli Utenti (Tabella users)
     * Rappresenta l'utente che ha creato il paragrafo
     * @return BelongsTo
     */
    public function created_by(): BelongsTo
    {
        return $this->belongsTo(UsersModel::class, 'user_id');
    }
}
