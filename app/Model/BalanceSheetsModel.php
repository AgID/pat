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

/**
 * Modello per la tabella object_balance_sheets, bilanci
 */
class BalanceSheetsModel extends Model
{
    use SearchableTrait;

    protected $table = 'object_balance_sheets';
    protected $primaryKey = 'id';

    protected string $metaModel = 'MetaBalanceSheetsModel';
    protected string $archiveName = 'balance_sheets';

    protected string $objectName = 'Bilanci';
    protected int $objectId = 15;

    protected $fillable = [
        'id',
        'owner_id',
        'institution_id',
        'object_measure_id',
        'object_structures_id',
        'state',
        'workflow_state',
        'name',
        'typology',
        'year',
        'description',
        'publishing_status',
        'o_id',
        'source_id',
        'number_readings',
        'publishing_start',
        'publishing_end',
        'unfixed',
        'attachments_id',
        'last_update_date',
        'created_at',
        'updated_at',
        'publishing_responsable'
    ];

    //Campi per log delle attività
    protected array $activityLog = [
        'field' => 'name'
    ];

    /**
     * Campi su cui effettuare la ricerca
     * @var string[]
     */
    protected $searchable = [
        'object_balance_sheets.name',
        'typology',
        'year',
        'users.name'
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
     * Costruttore del modello.
     * @param array $attributes
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
     * Rappresenta l'utente che ha creato il bilancio
     * @return BelongsTo
     */
    public function created_by()
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
     * Relazione con i Provvedimenti
     * Rappresenta il procedimento associato al bilancio
     * @return BelongsTo
     */
    public function related_measure(): BelongsTo
    {
        return $this->belongsTo(MeasuresModel::class, 'object_measure_id');
    }

    /**
     * Relazione con gli allegati
     * Restituisce tutti gli allegati non nascosti, per il front-office
     * @return HasMany
     */
    public function attachs(): HasMany
    {
        return $this->hasMany(AttachmentsModel::class, 'archive_id', 'id')
            ->select(['id', 'archive_id', 'orig_name', 'label', 'client_name'])
            ->orderBy('sort', 'ASC')
            ->where('active', '=', '1')
            ->where('deleted', '=', '0')
            ->where('archive_name', '=', 'balance_sheets');
    }

    /**
     * Relazione con gli allegati
     * Restituisce tutti gli allegati, per il back-office
     */
    public function all_attachs()
    {
        return $this->hasMany(AttachmentsModel::class, 'archive_id', 'id')
            ->select(['id', 'archive_id', 'orig_name', 'label', 'client_name'])
            ->orderBy('sort', 'ASC')
            ->where('deleted', '=', '0')
            ->where('archive_name', '=', 'balance_sheets');
    }

    /**
     * Scope locale per il filtraggio dei dati dei bilanci
     * @param Builder $query Query
     * @param string|null $name Nome
     * @param int|null $year Anno
     * @param string|null $typ
     * @return void
     */
    public function scopeFilter(
        Builder $query,
        string  $name = null,
        int $year = null,
        string|null $typ = ''
    ): void
    {
        if (!empty($name)) {
            $query->where('object_balance_sheets.name', 'LIKE', '%' . $name . '%');
        }

        if (!empty($year)) {
            $query->where('object_balance_sheets.year', 'LIKE', '%' . $year . '%');
        }

        if (!empty($typ)) {
            $query->where('object_balance_sheets.typology', $typ);
        }
    }

    /**
     * Relazione con Strutture Organizzative (tabella object_structures)
     * Rappresenta la struttura
     */
    public function structure(): belongsTo
    {
        return $this->belongsTo(StructuresModel::class, 'object_structures_id');
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
