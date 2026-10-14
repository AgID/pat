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
use Illuminate\Database\Eloquent\Relations\HasOne;

use System\Log;
use System\Model;
use Traits\SearchableTrait;

/**
 * Modello per la tabella object_contest
 */
class ContestModel extends Model
{
    use SearchableTrait;

    protected $table = 'object_contest';
    protected $primaryKey = 'id';

    protected string $objectName = 'Bandi di Concorso';
    protected string $archiveName = 'contest';
    protected string $metaModel = 'MetaContestModel';
    protected int $objectId = 23;

    protected $fillable = [
        'id',
        'related_contest_id',
        'owner_id',
        'object_structures_id',
        'object_measure_id',
        'institution_id',
        'state',
        'workflow_state',
        'typology',
        'object',
        'province_office',
        'city_office',
        'office_address',
        'activation_date',
        'expiration_date',
        'expiration_contest_date',
        'expiration_time',
        'expected_expenditure',
        'expenditures_made',
        'hired_employees',
        'external_assignment',
        'description',
        'test_calendar',
        'evaluation_criteria',
        'traces_written_tests',
        'attachments_id',
        'publishing_status',
        'o_id',
        'source_id',
        'number_readings',
        'last_update_date',
        'publishing_end',
        'unfixed',
        'publishing_responsable',
        'register_id',
        '__tag',
        'created_at',
        'updated_at'
    ];

    /**
     * Campi per log delle attività
     * @var array|string[]
     */
    protected array $activityLog = [
        'field' => 'object',
        'objectTypeField' => 'typology',
        'objectType' => 'typology',
        'avviso' => 'object',
        'concorso' => 'object',
        'esito' => 'object',
    ];

    /**
     * Campi su cui effettuare la ricerca nel datatable
     * @var string[]
     */
    protected $searchable = [
        'object',
        'source_id',
        'typology',
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
     * @description Relazioni molti a molti del modello, utilizzate nel postDelete per eliminarle
     * @var array
     */
    protected array $relationshipsToDelete = [
        'assignments'
    ];

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
     * Relazione con gli Utenti (Tabella users)
     * Rappresenta l'utente che ha creato il bando
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
     * @return BelongsTo
     * Relazione con InstitutionsModel
     * Rappresenta l'Ente di appartenenza.
     */
    public function institution(): BelongsTo
    {

        return $this->belongsTo(InstitutionsModel::class, 'institution_id');
    }

    /**
     * Relazione con Incarichi
     * Incarichi in relazione con il Concorso
     * @return BelongsToMany
     */
    public function assignments(): BelongsToMany
    {
        $assignments = $this->belongsToMany(AssignmentsModel::class, 'rel_contest_assignments', 'object_contest_id', 'object_assignments_id')
            ->wherePivot('typology', '=', 'commission')
            ->withTimestamps();
        return $assignments;
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
            ->where('archive_name', '=', 'contest');
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
            ->where('archive_name', '=', 'contest');
    }

    /**
     * @return BelongsTo
     * Relazione con StructuresModel
     * Rappresenta l'ufficio di riferimento del bando di concorso
     */
    public function office(): BelongsTo
    {
        return $this->belongsTo(StructuresModel::class, 'object_structures_id');
    }

    /**
     * @return BelongsTo
     * Relazione con ContestModel
     * Restituisce la procedura relativa che il bando di concorso ha correlato
     */
    public function related_contest(): BelongsTo
    {
        return $this->belongsTo(ContestModel::class, 'related_contest_id');
    }

    /**
     * @return BelongsTo
     * Relazione con i Provvedimenti
     * Rappresenta il procedimento associato al bando di concorso
     */
    public function relative_measure(): BelongsTo
    {
        return $this->belongsTo(MeasuresModel::class, 'object_measure_id');
    }

    /**
     * @return HasMany
     * Relazione con ContestModel
     * Restituisce gli avvisi che hanno correlato il bando di concorso
     */
    public function alerts(): HasMany
    {
        return $this->hasMany(ContestModel::class, 'related_contest_id', 'id')
            ->where('typology', '=', 'avviso');
    }

    /**
     * @return HasMany
     * Relazione con ContestModel
     * Restituisce gli esiti che hanno correlato il bando di concorso
     */
    public function outcomes(): HasMany
    {
        return $this->hasMany(ContestModel::class, 'related_contest_id', 'id')
            ->where('typology', '=', 'esito');
    }

    /**
     * Scope locale per il filtraggio dei dati
     * @param Builder     $query      Query
     * @param string|null $object     Oggetto bando
     * @param string|null $startPDate Data Inizio pubblicazione (da)
     * @param string|null $endPDate   Data Inizio pubblicazione (a)
     * @param string|null $startSDate Data fine pubblicazione (da)
     * @param string|null $endSDate   Data fine pubblicazione (a)
     * @return void
     */
    public function scopeFilter(
        Builder $query,
        string  $object = null,
        int|string|array  $structureId = null,
        string  $startPDate = null,
        string  $endPDate = null,
        string  $startSDate = null,
        string $endSDate = null
    ): void
    {
        if (!empty($object)) {
            $query->where('object_contest.object', 'LIKE', '%' . $object . '%');
        }

        if (!empty($structureId)) {
            $query->whereIn('object_contest.object_structures_id',  (array)$structureId);
        }

        if (!empty($startPDate)) {
            $query->where('object_contest.activation_date', '>=', date('Y-m-d H:i:s', strtotime($startPDate)));
        }

        if (!empty($endPDate)) {
            $query->where('object_contest.activation_date', '<=', date('Y-m-d  23:59:59', strtotime($endPDate)));
        }

        if (!empty($startSDate)) {
            $query->where('object_contest.expiration_date', '>=', date('Y-m-d H:i:s', strtotime($startSDate)));
        }

        if (!empty($endSDate)) {
            $query->where('object_contest.expiration_date', '<=', date('Y-m-d 23:59:59', strtotime($endSDate)));
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
