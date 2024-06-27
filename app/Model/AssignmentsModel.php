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
use Illuminate\Database\Eloquent\Relations\HasOne;
use Scope\InstitutionScope;
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
        'userName',
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
     * Costruttore, viene aggiunta la ricerca per Ente se l'utente è SuperAdmin
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
     */
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new InstitutionScope);
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
    public function measures(): BelongsToMany
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
        return $this->belongsToMany(ContestModel::class, 'rel_contest_assignments', 'object_assignments_id', 'object_contest_id');
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
     * @return HasOne
     * Relazione con AssignmentsModel
     * Rappresenta l'Incarico relativo alla liquidazione
     */
    public function related_assignment()
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
     * @param Builder      $query               Query
     * @param string|null  $typology            Tipologia
     * @param string|null  $assignmentStartFrom Data inizio incarico dal
     * @param string|null  assignmentStartTo    Data inizio incarico al
     * @return void
     */
    public function scopeAssignmentFilterDataTable( $query, $typology = null, $assignmentStartFrom = null, $assignmentStartTo = null): void
    {

        if(!empty($typology)){
            $query->where('object_assignments.typology','=', $typology);
        }
        if(!empty($assignmentStartFrom)){
            $query->where('object_assignments.assignment_start', '>=', date('Y-m-d H:i:s', strtotime($assignmentStartFrom)));
        }
        if(!empty($assignmentStartTo)){
            $query->where('object_assignments.assignment_start', '<=', date('Y-m-d H:i:s', strtotime($assignmentStartTo)));
        }

    }
}
