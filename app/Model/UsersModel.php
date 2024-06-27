<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Scope\DeletedScope;
use Scope\InstitutionScope;
use System\Model;
use Traits\SearchableTrait;

// use Traits\SearchTrait;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Modello per la tabella users
 */
class UsersModel extends Model
{
    use SearchableTrait;

    protected $table = 'users';
    protected $primaryKey = 'id';

    protected $objectName = 'Utenti';
    protected int $objectId = 54;

    protected $fillable = [
        'id',
        'institution_id',
        'name',
        'username',
        'password',
        'email',
        'phone',
        'spid_code',
        'fiscal_code',
        'active',
        'active_key',
        'deleted',
        'last_visit',
        'registration_date',
        'super_admin',
        'admin',
        'prevent_password_repetition',
        'password_expiration_days',
        'refresh_password',
        'profile_image',
        'registration_type',
        'filter_owner_record',
        'prevent_password_change_day',
        'deactivate_account_no_use',
        'notes',
        'created_at',
        'updated_at',
        'deleted_at'
    ];

    //Campi per log delle attività
    protected array $activityLog = [
        'area' => 'users',
        'platform' => 'all',
        'create' => 'addUserInstance',
        'update' => 'updateUserInstance',
        'field' => 'name'
    ];

    /**
     * Campi su cui effettuare la ricerca nel datatable
     * @var string[]
     */
    protected $searchable = [
        'name',
        'username',
        'email'
    ];

    /**
     * Campi con cui deve essere applicata la crittografia nella ricerca
     * @var string[]
     */
    protected $encrypted = [
        'name',
        'username',
        'email'
    ];

    /**
     * Costruttore, viene aggiunta la ricerca per Ente se l'utente è SuperAdmin
     * @param array $attributes Parametri costruttore
     * @throws Exception
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        if (isSuperAdmin()) {
            $this->searchable[] = 'i.full_name_institution';
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
        static::addGlobalScope(new InstitutionScope);
        static::addGlobalScope(new DeletedScope);
    }

    /**
     * @return BelongsTo
     * Relazione con InstitutionsModel
     * Rappresenta l'Ente di appartenenza.
     */
    public function institution(): BelongsTo
    {

        return $this->belongsTo(\Model\InstitutionsModel::class, 'institution_id');
    }

    /**
     * Relazione con Profili ACL
     * Rappresenta i Profili ACL assegnati all'utente
     * @return BelongsToMany
     */
    public function profiles(): BelongsToMany
    {
        return $this->belongsToMany(AclProfilesModel::class, 'rel_users_acl_profiles', 'user_id', 'acl_profile_id');
    }

    /**
     * Relazione con lo storico delle password
     * @return HasMany
     */
    public function passwordHistory(): HasMany
    {
        return $this->hasMany(PasswordHistoryModel::class, 'user_id', 'id');
    }
}
