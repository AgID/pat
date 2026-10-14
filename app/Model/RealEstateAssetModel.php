<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

use System\Model;
use Traits\SearchableTrait;

/**
 * Modello per la tabella object_real_estate_heritage
 * Patrimoni Immobiliari
 */
class RealEstateAssetModel extends Model
{
    use SearchableTrait;

    protected $table = 'object_real_estate_asset';
    protected $primaryKey = 'id';
    protected string $metaModel = 'MetaRealEstateAssetModel';
    protected string $archiveName = 'real_estate_asset';

    protected string $objectName = 'Patrimonio immobiliare';
    protected int $objectId = 8;

    protected $fillable = [
        'id',
        'owner_id',
        'institution_id',
        'state',
        'workflow_state',
        'name',
        'address',
        'attachments_id',
        'user_office',
        'sheet',
        'particle',
        'subaltern',
        'gross_surface',
        'discovered_surface',
        'archived',
        'archived_info',
        'archived_end_date',
        'description',
        'source_id',
        'o_id',
        'publishing_status',
        'number_readings',
        'last_update_date',
        'publishing_start',
        'publishing_end',
        'unfixed',
        'created_at',
        'updated_at',
        'publishing_responsable'
    ];

    /**
     * Campi per log delle attività
     * @var array|string[]
     */
    protected array $activityLog = [
        'field' => 'name'
    ];

    /**
     * Campi su cui effettuare la ricerca
     * @var string[]
     */
    protected $searchable = [
        'object_real_estate_asset.name',
        'object_real_estate_asset.address',
        'users.name',
    ];

    /**
     * Campi con cui deve essere applicata la crittografia nella ricerca
     * @var string[]
     */
    protected array $encrypted = [
        'users.name'
    ];

    /**
     * Campi di relazione su cui effettuare la ricerca
     * @var array[]
     */
    protected $searchableWhereHas = [];

    /**
     * @description Relazioni molti a molti del modello, utilizzate nel postDelete per eliminarle
     * @var array
     */
    protected array $relationshipsToDelete = [
        'canons',
        'offices'
    ];

    /**
     * Costruttore del modello.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * @return BelongsTo
     * Relazione con InstitutionsModel
     * Rappresenta l'Ente di appartenenza.
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(InstitutionsModel::class, 'institution_id');
    }

    /**
     * Per non chiamare il global scope
     * NomeClasse::withoutGlobalScope(new HasActive)
     */
    protected static function boot()
    {
        parent::boot();

    }

    /**
     * Relazione con gli Utenti (Tabella users)
     * Rappresenta l'utente che ha creato il patrimonio immobiliare
     * @return BelongsTo
     */
    public function created_by(): BelongsTo
    {
        return $this->belongsTo(UsersModel::class, 'owner_id');
    }

    /**
     * @return HasMany
     */
    public function user_last_update(): HasMany
    {
        return $this->hasMany(ActivityLogModel::class, 'record_id', 'id')
            ->where('activity_log.object_id', '=', $this->objectId);
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

    /**
     * Relazione con gli allegati
     * Restituisce tutti gli allegati non nascosti, per il front-office
     * @return HasMany
     */
    public function attachs(): HasMany
    {
        return $this->hasMany(AttachmentsModel::class, 'archive_id', 'id')
            ->select(['id', 'archive_id', 'orig_name', 'label'])
            ->orderBy('sort', 'ASC')
            ->where('active', '=', '1')
            ->where('deleted', '=', '0')
            ->where('archive_name', '=', 'real_estate_asset');
    }

    /**
     * Relazione con gli allegati
     * Restituisce tutti gli allegati, per il back-office
     * @return HasMany
     */
    public function all_attachs(): HasMany
    {
        return $this->hasMany(AttachmentsModel::class, 'archive_id', 'id')
            ->select(['id', 'archive_id', 'orig_name', 'label', 'client_name'])
            ->orderBy('sort', 'ASC')
            ->where('deleted', '=', '0')
            ->where('archive_name', '=', 'real_estate_asset');
    }

    /**
     * Relazione con Strutture
     * Uffici utilizzatori del patrimonio immobiliare
     * @return BelongsToMany
     */
    public function offices(): BelongsToMany
    {
        $offices = $this->belongsToMany(StructuresModel::class, 'rel_real_estate_asset_structures', 'object_real_estate_asset_id', 'object_structures_id')
            ->withTimestamps();
        return $offices;
    }

    /**
     * Relazione con Canoni di Locazione
     * Canoni di Locazione associati all'Immobile
     * @return BelongsToMany
     */
    public function canons(): BelongsToMany
    {
        return $this->belongsToMany(LeaseCanonsModel::class, 'rel_lease_canons_real_estate_asset', 'object_real_estate_asset_id', 'object_lease_canons_id');
    }

    /**
     * Scope locale per il filtraggio dei dati in front-office
     * @param Builder     $query   Query
     * @param string|null $name    Nome Immobile
     * @param string|null $address Indirizzo Immobile
     * @return void
     */
    public function scopeFilter(
        Builder $query,
        string  $name = null,
        string $address = null
    ): void
    {
        if (!empty($name)) {
            $query->where('name', 'LIKE', '%' . $name . '%');
        }

        if (!empty($address)) {
            $query->where('address', 'LIKE', '%' . $address . '%');
        }
    }
}
