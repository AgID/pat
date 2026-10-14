<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;

use System\Model;
use Traits\SearchableTrait;

/**
 * Modello per la tabella object_lease_canons
 */
class LeaseCanonsModel extends Model
{
    use SearchableTrait;

    protected $table = 'object_lease_canons';
    protected $primaryKey = 'id';
    protected string $metaModel = 'MetaLeaseCanonsModel';
    protected string $archiveName = 'lease_canons';

    protected $objectName = 'Canoni di locazione';
    protected int $objectId = 9;

    protected $fillable = [
        'id',
        'owner_id',
        'institution_id',
        'object_structures_id',
        'state',
        'workflow_state',
        'canon_type',
        'beneficiary',
        'fiscal_code',
        'amount',
        'contract_statements',
        'start_date',
        'end_date',
        'notes',
        'publishing_status',
        'o_id',
        'source_id',
        'number_readings',
        'attachments_id',
        'last_update_date',
        'publishing_start',
        'publishing_end',
        'unfixed',
        'created_at',
        'updated_at',
        'publishing_responsable'
    ];

    //Campi per log delle attività
    protected array $activityLog = [
        'field' => 'beneficiary'
    ];

    /**
     * Campi su cui effettuare la ricerca
     * @var string[]
     */
    protected $searchable = [
        'beneficiary',
        'amount',
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
    protected $searchableWhereHas = [
        'properties' => [
            'table' => 'object_real_estate_asset',
            'field' => ['name']
        ],
    ];

    /**
     * @description Relazioni molti a molti del modello, utilizzate nel postDelete per eliminarle
     * @var array
     */
    protected array $relationshipsToDelete = [
        'properties'
    ];

    /**
     * Costruttore del modello.
     * @param array $attributes Parametri costruttore
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
     * Relazione con gli Utenti (Tabella users)
     * Rappresenta l'utente che ha creato il canone
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
     * Relazione con Patrimonio Immobiliare
     * Immobili associati al canone di locazione
     * @return BelongsToMany
     */
    public function properties(): BelongsToMany
    {
        $properties = $this->belongsToMany(RealEstateAssetModel::class, 'rel_lease_canons_real_estate_asset', 'object_lease_canons_id', 'object_real_estate_asset_id')
            ->withTimestamps();
        return $properties;
    }

    /**
     * Relazione con strutture organizzative
     * Rappresenta l'Ufficio referente per il contratto
     * @return BelongsTo
     */
    public function structure(): BelongsTo
    {
        return $this->belongsTo(StructuresModel::class, 'object_structures_id');
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
            ->where('archive_name', '=', 'lease_canons');
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
            ->where('archive_name', '=', 'lease_canons');
    }

    /**
     * Scope locale per il filtraggio dei dati dei canoni di locazione
     * @param Builder     $query     Query
     * @param int|null    $asset     Id Immobile
     * @param string|null $amount    Importo
     * @param string|null $startDate Data inizio
     * @param string|null $endDate   Data fine
     * @return void
     */
    public function scopeFilter(
        Builder $query,
        int     $asset = null,
        string  $amount = null,
        string  $startDate = null,
        string  $endDate = null
    ): void
    {
        if (!empty($asset)) {
            $query->join('rel_lease_canons_real_estate_asset as rel', 'rel.object_lease_canons_id', 'object_lease_canons.id');
            $query->where('rel.object_real_estate_asset_id', '=', $asset);
        }

        if (!empty($amount)) {
            $query->where('amount', 'LIKE', '%' . $amount . '%');
        }

        if (!empty($startDate)) {
            $query->where('object_lease_canons.start_date', '>=', date('Y-m-d H:i:s', strtotime($startDate)));
        }

        if (!empty($endDate)) {
            $query->where('object_lease_canons.end_date', '<=', date('Y-m-d 23:59:59', strtotime($endDate)));
        }

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
}
