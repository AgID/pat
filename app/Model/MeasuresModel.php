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
use Scope\InstitutionScope;
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

    protected string $archiveName = 'measures';
    protected string $objectName = 'Provvedimenti Amministrativi';
    protected int $objectId = 26;

    protected $fillable = [
        'id',
        'owner_id',
        'institution_id',
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
        'created_at',
        'updated_at',
        'publishing_responsable'
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
        'assignments'
    ];

    /**
     * Costruttore, viene aggiunta la ricerca per Ente se l'utente è SuperAdmin
     * @param array $attributes
     * @throws \Exception
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
     * @return BelongsTo
     * Relazione con InstitutionsModel
     * Rappresenta l'Ente di appartenenza.
     * @return BelongsTo
     */
    public function institution()
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

    /**
     * @return BelongsTo
     * Relazione con Bandi di gara dal 01/01/2024
     * Rappresenta la procedura di scelta del contraente associata
     */
    public function relative_bdncp_procedure(): BelongsTo
    {
        return $this->belongsTo(BdncpProcedureModel::class, 'object_bdncp_procedure_id');
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
     * Scope locale per il filtraggio dei dati delle liquidazioni relative agli incarichi
     * @param Builder $query Query
     * @param string|null $typology Tipologia
     * @param string|null $measureFrom
     * @param string|null $measureTo
     * @param int|null $structures
     * @return void
     */
    public function scopeMeasureFilterDataTable( $query, string $typology = null, string $measureFrom = null, string $measureTo = null, int $structures = null): void
    {

        if(!empty($typology)){
            $query->where('object_measures.type','=', (int)$typology);
        }

        if(!empty($measureFrom)){
            $query->where('object_measures.date', '>=', date('Y-m-d H:i:s', strtotime($measureFrom)));
        }

        if(!empty($measureTo)){
            $query->where('object_measures.date', '<=', date('Y-m-d H:i:s', strtotime($measureTo)));
        }

        if (!empty($structures)) {
            $query->whereHas('structures', function ($query) use ($structures){
                $query->where('object_structures.id', '=',$structures);
            });
        }

    }
}
