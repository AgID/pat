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

use System\Model;
use Traits\SearchableTrait;

/**
 * Modello per la tabella object_assignments
 */
class AssignmentsModel extends Model
{
    use SearchableTrait;

    protected $table = 'object_assignments';
    protected $primaryKey = 'id';

    protected string $metaModel = 'MetaAssignmentsModel';
    protected string $objectName = 'Incarichi e consulenze';
    protected string $archiveName = 'assignments';
    protected int $objectId = 25;

    protected $fillable = [
        'id',
        'owner_id',
        'institution_id',
        'object_structures_id',
        'related_assignment_id',
        'state',
        'workflow_state',
        'typology',
        'type',
        'consulting_type',
        'name',
        'object',
        'assignment_type',
        'assignment_start',
        'assignment_end',
        'end_of_assignment_not_available',
        'end_of_assignment_not_available_txt',
        'compensation',
        'compensation_provided',
        'compensation_provided_date',
        'liquidation_date',
        'liquidation_year',
        'variable_compensation',
        'notes',
        'publishing_status',
        'acts_extremes',
        'attachments_id',
        'dirigente',
        'assignment_reason',
        'full_description',
        'contraent_procedure_type',
        'contraent_procedure_number',
        'publishing_status',
        'o_id',
        'source_id',
        'number_readings',
        'publishing_start',
        'publishing_end',
        'unfixed',
        'last_update_date',
        'created_at',
        'updated_at',
        'publishing_responsable'
    ];

    //Campi per log delle attività
    protected array $activityLog = [
        'objectTypeField' => 'type',
        'objectType' => 'typology',
        'assignment' => 'object',
        'liquidation' => ['related_assignment', 'object'],
    ];

    /**
     * Campi su cui effettuare la ricerca nel datatable
     * @var string[]
     */
    protected $searchable = [
        'object_assignments.object',
        'object_assignments.name',
        'object_assignments.type',
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
    protected $searchableWhereHas = [
        'related_assignment' => [
            'field' => ['object', 'name']
        ],
    ];

    /**
     * @description Relazioni molti a molti del modello, utilizzate nel postDelete per eliminarle
     * @var array
     */
    protected array $relationshipsToDelete = [
        'measures',
        'contests',
        'notice_acts',
        'personnel'
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
     */
    protected static function boot(): void
    {
        parent::boot();

    }

    /**
     * Per i campi di select
     * @return string
     */
    public function getFullDescriptionAttribute()
    {
        return $this->name . ' - ' . $this->object;
    }

    /**
     * Relazione con gli Utenti (Tabella users)
     * Rappresenta l'utente che ha creato l'incarico
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
     * @return BelongsTo
     * Relazione con InstitutionsModel
     * Rappresenta l'Ente di appartenenza.
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(InstitutionsModel::class, 'institution_id');
    }

    /**
     * Relazione con Provvedimenti
     * Provvedimenti associati all'incarico
     * @return BelongsToMany
     */
    public function measures()
    {
        $measures = $this->belongsToMany(MeasuresModel::class, 'rel_assignments_measures', 'object_assignments_id', 'object_measures_id')
            ->withTimestamps();
        return $measures;
    }

    /**
     * Relazione con le Strutture (Tabella object_structures)
     * Rappresenta la struttura organizzativa responsabile dell'incarico
     */
    public function structure()
    {
        return $this->belongsTo(StructuresModel::class, 'object_structures_id');
    }

    /**
     * Relazione con Bandi di concorso
     * Tutti i concorsi in relazione con l'Incarico
     */
    public function contests()
    {
        return $this->belongsToMany(ContestModel::class, 'rel_contest_assignments', 'object_assignments_id', 'object_contest_id')
        ->wherePivot('typology', '=', 'contest')
        ->withTimestamps();
    }

    /**
     * Relazione con Atti delle amministrazioni
     * Atti associati all'incarico
     * @return BelongsToMany
     */
    public function notice_acts(): BelongsToMany
    {
        return $this->belongsToMany(NoticesActsModel::class, 'rel_assignments_notices_acts', 'object_assignments_id', 'object_notices_acts_id');
    }

    /**
     * Relazione con Personale
     * Personale a cui è associato l'Incarico
     */
    public function personnel(): BelongsToMany
    {
        return $this->belongsToMany(PersonnelModel::class, 'rel_personnel_assignments', 'object_assignments_id', 'object_personnel_id');
    }

    /**
     * @return BelongsTo
     * Relazione con AssignmentsModel
     * Rappresenta l'Incarico relativo alla liquidazione
     */
    public function related_assignment(): BelongsTo
    {
        return $this->belongsTo(AssignmentsModel::class, 'related_assignment_id');
    }

    /**
     * Relazione con le liquidazioni
     * Rappresenta le liquidazioni relativi al bando
     */
    public function relative_liquidation()
    {
        return $this->hasMany(AssignmentsModel::class, 'related_assignment_id', 'id')
            ->where('object_assignments.typology', '=', 'liquidation');
    }

    /**
     * Relazione con gli allegati
     * Restituisce tutti gli allegati non nascosti, per il front-office
     */
    public function attachs()
    {
        return $this->hasMany(AttachmentsModel::class, 'archive_id', 'id')
            ->select(['id', 'archive_id', 'orig_name', 'client_name', 'label'])
            ->orderBy('sort', 'ASC')
            ->where('active', '=', '1')
            ->where('deleted', '=', '0')
            ->where('archive_name', '=', 'assignments');
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
            ->where('archive_name', '=', 'assignments');
    }

    /**
     * Scope locale per il filtraggio dei dati delle liquidazioni relative agli incarichi
     * @param Builder     $query    Query
     * @param string|null $fullName Nominativo incarico
     * @param string|null $object   Oggetto incarico
     * @param int|null    $year     Anno liquidazione
     * @return void
     */
    public function scopeLiquidationFilter(
        Builder $query,
        string  $fullName = null,
        string  $object = null,
        int     $year = null): void
    {
        if (!empty($year)) {
            $query->where('object_assignments.liquidation_year', '=', $year);
        }

        if (!empty($fullName)) {
            $query->join('object_assignments as relative', 'relative.id', 'object_assignments.related_assignment_id');
            $query->where('relative.name', 'LIKE', '%' . $fullName . '%');
        }

        if (!empty($object)) {
            $query->join('object_assignments as relative_obj', 'relative_obj.id', 'object_assignments.related_assignment_id');
            $query->where('relative_obj.object', 'LIKE', '%' . $object . '%');
        }
    }

    /**
     * Scope locale per il filtraggio dei dati delle liquidazioni relative agli incarichi
     * @param Builder     $query               Query
     * @param string|null $fullName            Nominativo incarico
     * @param string|null $object              Oggetto incarico
     * @param int|null    $office              Id ufficio incarico
     * @param string|null $startDate           Data inizio incarico
     * @param string|null $endDate             Data fine incarico
     * @param int|null    $assignmentStartYear Anno di inizio incarico
     * @return void
     */
    public function scopeAssignmentFilter(
        Builder $query,
        string  $fullName = null,
        string  $object = null,
        int     $office = null,
        string  $startDate = null,
        string $endDate = null,
        int|null $assignmentStartYear = null
    ): void
    {
        if (!empty($fullName)) {

            $fullName = trim($fullName);
            $items = explode(" ", $fullName);

            $query->where(function ($q) use ($items, $fullName){
                $q->where('object_assignments.name', 'LIKE', '%' . $fullName . '%');
                $q->orWhere(function($q2) use ($items){
                    foreach ($items as $itm){
                        $q2->where('object_assignments.name', 'LIKE', '%' . $itm . '%');
                    }
                });
            });

        }

        if (!empty($object)) {
            $query->where('object_assignments.object', 'LIKE', '%' . $object . '%');
        }

        if (!empty($office)) {
            $query->where('object_assignments.object_structures_id', '=', $office);
        }

        if (!empty($startDate)) {
            $query->where('object_assignments.assignment_start', '>=', date('Y-m-d H:i:s', strtotime($startDate)));
        }

        if (!empty($endDate)) {
            $query->where('object_assignments.assignment_end', '<=', date('Y-m-d 23:59:59', strtotime($endDate)));
        }

        if (!empty($assignmentStartYear)) {
            $query->whereYear('object_assignments.assignment_start', (int)$assignmentStartYear);
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
