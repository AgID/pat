<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use System\Model;
use Traits\SearchableTrait;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Modello per la tabella acl_prfiles
 */
class AclProfilesModel extends Model
{
    use SearchableTrait;

    public $timestamps = true;
    protected $table = 'acl_profiles';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'institution_id',
        'is_system',
        'name',
        'workflow',
        'description',
        'versioning',
        'lock_user',
        'advanced',
        'archiving',
        'export_csv',
        'editor_wishing'
    ];

    /**
     * Campi su cui effettuare la ricerca nel datatable
     *
     * @var string[]
     */
    protected $searchable = [
        'name',
        'description'
    ];

    /**
     * Campi di relazione su cui effettuare la ricerca
     * @var array[]
     */
    protected $searchableWhereHas = [];

    /**
     * Costruttore, viene aggiunta la ricerca per Ente se l'utente è SuperAdmin
     *
     * @param array $attributes
     * @throws Exception
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        if (isSuperAdmin()) {
            $this->searchableWhereHas['institution'] = [
                'field' => ['full_name_institution'],
                'as' => 'i'
            ];
        }
    }

    /**
     * Per non chiamare il global scope
     * NomeClasse::withoutGlobalScope(new HasActive)
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();
//        static::addGlobalScope(new InstitutionScope);
    }

    /**
     * Relazione con InstitutionsModel
     * Rappresenta l'Ente di appartenenza.
     *
     * @return BelongsTo
     */
    public function institution(): BelongsTo
    {

        return $this->belongsTo(\Model\InstitutionsModel::class, 'institution_id');
    }

    /**
     * Permessi sulle varie sezioni del Profilo
     * @return HasMany
     */
    public function permits(): HasMany
    {
        return $this->hasMany(PermitsModel::class, 'acl_profiles_id', 'id');
    }

    /**
     * Utenti che hanno il profilo associato
     * @return HasMany
     */
    public function usersAclProfiles(): HasMany
    {
        return $this->hasMany(RelUsersAclProfilesModel::class, 'acl_profile_id', 'id');
    }

    public function delete()
    {
        $this->permits()->delete();
        $this->usersAclProfiles()->delete();
        return parent::delete();
    }

    /**
     * Scope locale per il filtraggio in base all'ente
     *
     * @param Builder $query
     * @return Builder
     */
    public function scopeInstitutionFilter(Builder $query)
    {
        $institutionId = checkAlternativeInstitutionId() ? checkAlternativeInstitutionId() : PatOsInstituteId();

        $query->where(function ($query) use ($institutionId) {
            $query->where('acl_profiles.institution_id', $institutionId)
                ->where('acl_profiles.is_system', 0);
        })
            ->orWhere(function ($query) {
                $query->where('acl_profiles.is_system', 1)
                    ->whereNull('acl_profiles.institution_id');
            });
    }
}
