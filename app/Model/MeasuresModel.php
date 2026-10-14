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

use System\Log;
use System\Model;
use Traits\SearchableTrait;

/**
 * Modello per la tabella object_measures
 */
class MeasuresModel extends Model
{
    use SearchableTrait;

    protected $table = 'object_measures';
    protected $primaryKey = 'id';

    protected string $metaModel = 'MetaMeasuresModel';
    protected string $archiveName = 'measures';
    protected string $objectName = 'Provvedimenti Amministrativi';
    protected int $objectId = 26;

    protected int $institutionTypeID = 0; //NON rimuovere - utilizzata da cli per l'export

    protected $fillable = [
        'id',
        'owner_id',
        'institution_id',
        'workflow_state',
        'state',
        'object_contests_acts_id',
        'object_bdncp_procedure_id',
        'number',
        'object',
        'type',
        'article_type',
        'date',
        'content',
        'expense',
        'extremes',
        'choice_of_contractor',
        'notes',
        'publishing_status',
        'o_id',
        'source_id',
        'number_readings',
        'attachments_id',
        'last_update_date',
        'accordi_pa_dir',
        'publishing_start',
        'publishing_end',
        'unfixed',
        'publishing_responsable',
        '__tag',
        'created_at',
        'updated_at'
    ];

    /**
     * Campi per log delle attività
     * @var array|string[]
     */
    protected array $activityLog = [
        'field' => 'object'
    ];

    /**
     * Campi su cui effettuare la ricerca nel datatable
     * @var string[]
     */
    protected $searchable = [
        'object',
        'number',
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
        'structures' => [
            'table' => 'object_structures',
            'field' => ['structure_name']
        ],
    ];

    /**
     * @description Relazioni molti a molti del modello, utilizzate nel postDelete per eliminarle
     * @var array
     */
    protected array $relationshipsToDelete = [
        'personnel',
        'structures',
        'interventions',
        'charges',
        'assignments',
        'bdncp_procedures'
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
     * Relazione con InstitutionsModel
     * Rappresenta l'Ente di appartenenza.
     * @return BelongsTo
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(\Model\InstitutionsModel::class, 'institution_id');
    }

    /**
     * Relazione con gli Utenti (Tabella users)
     * Rappresenta l'utente che ha creato i Provvedimenti
     * @return BelongsTo
     */
    public function created_by(): BelongsTo
    {
        return $this->belongsTo(\Model\UsersModel::class, 'owner_id');
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
     * NON rimuovere - utilizzata da cli per l'export
     */
    public function setInstitutionType($id = 0): void
    {
        $this->institutionTypeID = $id;
    }

    /**
     * Relazione con gli Interventi
     * Interventi associati al provvedimento
     * @return BelongsToMany
     */
    public function interventions(): BelongsToMany
    {
        return $this->belongsToMany(InterventionsModel::class, 'rel_interventions_measures', 'object_measures_id', 'object_interventions_id');
    }

    /**
     * Relazione con Incarichi
     * Incarichi associati al Provvedimento
     * @return BelongsToMany
     */
    public function assignments(): BelongsToMany
    {
        return $this->belongsToMany(AssignmentsModel::class, 'rel_assignments_measures', 'object_measures_id', 'object_assignments_id');
    }

    /**
     *
     * @return HasMany
     */
    public function scp(): HasMany
    {
        return $this->relationFromAction('r_d_o', [
            'n' => __FUNCTION__
        ]);
    }

    /**
     * Relazione con Strutture
     * Strutture organizzative responsabili del provvedimento
     * @return BelongsToMany
     */
    public function structures(): BelongsToMany
    {
        $structures = $this->belongsToMany(StructuresModel::class, 'rel_measures_structures', 'object_measures_id', 'object_structures_id')
            ->withTimestamps();
        return $structures;
    }

    /**
     * Relazione con Personale
     * Personale responsabile del provvedimento
     * @return BelongsToMany
     */
    public function personnel(): BelongsToMany
    {
        $personnel = $this->belongsToMany(PersonnelModel::class, 'rel_measures_personnel', 'object_measures_id', 'object_personnel_id')
            ->withTimestamps();
        return $personnel;
    }

    /**
     * @return BelongsTo
     * Relazione con Scelta del contraente
     * Rappresenta la procedura di scelta del contraente associata
     */
    public function relative_procedure_contraent(): BelongsTo
    {
        return $this->belongsTo(ContestsActsModel::class, 'object_contests_acts_id');
    }

//    Vecchia relazione con Bandi di gara dal 01/01/2024, sostituita da bdncp_procedures
//    /**
//     * @return BelongsTo
//     * Relazione con Bandi di gara dal 01/01/2024
//     * Rappresenta la procedura di scelta del contraente associata
//     */
//    public function relative_bdncp_procedure(): BelongsTo
//    {
//        return $this->belongsTo(BdncpProcedureModel::class, 'object_bdncp_procedure_id');
//    }

    /**
     * Relazione con Bandi di gara dal 01/01/2024
     * Rappresenta la procedura di scelta del contraente associata
     * @return BelongsToMany
     */
    public function bdncp_procedures(): BelongsToMany
    {
        return $this->belongsToMany(BdncpProcedureModel::class,'rel_measures_bdncp_procedure','object_measures_id','object_procedure_id')->withTimestamps();
    }

    /**
     * Relazione con Oneri informativi
     * Oneri informativi validi per il provvedimento
     * @return BelongsToMany
     */
    public function charges(): BelongsToMany
    {
        $charges = $this->belongsToMany(ChargesModel::class, 'rel_charges_measures', 'object_measures_id', 'object_charges_id');
        return $charges;
    }


    /**
     * Relazione con gli allegati
     * Restituisce tutti gli allegati non nascosti, per il front-office
     * @return HasMany
     */
    public function attachs(): HasMany
    {
        return $this->hasMany(\Model\AttachmentsModel::class, 'archive_id', 'id')
            ->select(['id', 'archive_id', 'orig_name', 'label'])
            ->orderBy('sort', 'ASC')
            ->where('active', '=', '1')
            ->where('deleted', '=', '0')
            ->where('archive_name', '=', 'measures');
    }

    /**
     * Relazione con gli allegati
     * Restituisce tutti gli allegati, per il back-office
     * @return HasMany
     */
    public function all_attachs(): HasMany
    {
        return $this->hasMany(\Model\AttachmentsModel::class, 'archive_id', 'id')
            ->select(['id', 'archive_id', 'orig_name', 'label', 'client_name'])
            ->orderBy('sort', 'ASC')
            ->where('deleted', '=', '0')
            ->where('archive_name', '=', 'measures');
    }


    /**
     * Scope locale per il filtraggio dei dati dei Provvedimenti
     * @param Builder     $query       Query
     * @param string|null $number      Numero provvedimento
     * @param string|null $object      Oggetto provvedimento
     * @param int|null    $year        Anno
     * @param string|null $startDate   Data inizio
     * @param string|null $endDate     Data fine
     * @param int|null    $structure   Id struttura
     * @param int|null    $responsible Id responsabile
     * @return void
     */
    public function scopeFilter(
        Builder $query,
        string  $number = null,
        string  $object = null,
        int     $year = null,
        string  $startDate = null,
        string  $endDate = null,
        int     $structure = null,
        int     $responsible = null
    ): void
    {
        if (!empty($number)) {
            $query->where('object_measures.number', 'LIKE', '%' . $number . '%');
        }

        if (!empty($object)) {
            $query->where('object_measures.object', 'LIKE', '%' . $object . '%');
        }

        if (!empty($year)) {
            $query->whereYear('object_measures.date', '=', $year);
        }

        if (!empty($startDate)) {
            $query->where('object_measures.date', '>=', date('Y-m-d H:i:s', strtotime($startDate)));
        }

        if (!empty($endDate)) {
            $query->where('object_measures.date', '<=', date('Y-m-d 23:59:59', strtotime($endDate)));
        }

        if (!empty($structure)) {
            $query->join('rel_measures_structures as rest', 'rest.object_measures_id', '=', 'object_measures.id')
                ->where('rest.object_structures_id', '=', $structure);
        }

        if (!empty($responsible)) {
            $query->join('rel_measures_personnel as repe', 'repe.object_measures_id', '=', 'object_measures.id')
                ->where('repe.object_personnel_id', '=', $responsible);
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
