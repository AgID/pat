<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Scope\InstitutionScope;
use System\Model;
use Traits\SearchableTrait;

/**
 * Modello per la tabella object_proceedings
 */
class ProceedingsModel extends Model
{
    use SearchableTrait;

    protected $table = 'object_proceedings';
    protected $primaryKey = 'id';
    protected string $archiveName = 'proceedings';

    protected string $objectName = 'Procedimenti';
    protected int $objectId = 7;

    protected $fillable = [
        'id',
        'owner_id',
        'institution_id',
        'state',
        'workflow_state',
        'name',
        'contact',
        'description',
        'costs',
        'silence_consent',
        'declaration',
        'regulation',
        'url_service',
        'protection_instruments',
        'service_available',
        'deadline',
        'publishing_status',
        'o_id',
        'source_id',
        'number_readings',
        'service_time',
        'public_monitoring_proceeding',
        'archived',
        'archived_end_date',
        'archived_info',
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
        'object_proceedings.name',
        'userName'
    ];

    /**
     * Campi di relazione su cui effettuare la ricerca
     * @var array[]
     */
    protected $searchableWhereHas = [
        'responsibles' => [
            'table' => 'object_personnel',
            'field' => ['full_name'],
        ],
        'offices_responsibles' => [
            'table' => 'object_structures',
            'field' => ['structure_name'],
        ],
    ];

    /**
     * @description Relazioni molti a molti del modello, utilizzate nel postDelete per eliminarle
     * @var array
     */
    protected array $relationshipsToDelete = [
        'charges',
        'modules',
        'normatives',
        'personnel',
        'structures',
        'regulations',
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
        static::addGlobalScope(new InstitutionScope);
    }

    /**
     * Funzione post delete, chiamata automaticamente a seguito della funzione custom deleteWithLogs
     * @param object $element Elemento eliminato
     * @return void
     */
    public function postDelete(object $element): void
    {
        DataMonitoringProceedings::where('proceeding_id', '=', $element['id'])
            ->delete();
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
     * @return BelongsTo
     * Relazione con gli Utenti (Tabella users)
     * Rappresenta l'utente che ha creato il procedimento
     */
    public function created_by(): BelongsTo
    {
        return $this->belongsTo(UsersModel::class, 'owner_id');
    }

    /**
     * Relazione con Personale (Tabella object_personnel)
     * Personale responsabile del procedimento
     * @return BelongsToMany
     */
    public function responsibles(): BelongsToMany
    {
        $responsibles = $this->belongsToMany(PersonnelModel::class, 'rel_proceedings_personnel', 'object_proceedings_id', 'object_personnel_id')
            ->wherePivot('typology', '=', 'responsible')
            ->withTimestamps()
            ->orderBy('object_personnel.full_name');
        return $responsibles;
    }

    /**
     * Relazione con Personale (Tabella object_personnel)
     * Personale responsabile del provvedimento
     * @return BelongsToMany
     */
    public function measure_responsibles(): BelongsToMany
    {
        $measureResponsibles = $this->belongsToMany(PersonnelModel::class, 'rel_proceedings_personnel', 'object_proceedings_id', 'object_personnel_id')
            ->wherePivot('typology', '=', 'measure-responsible')
            ->withTimestamps()
            ->orderBy('object_personnel.full_name');
        return $measureResponsibles;
    }

    /**
     * Relazione con Personale (Tabella object_personnel)
     * Personale responsabile sostitutivo
     * @return BelongsToMany
     */
    public function substitute_responsibles(): BelongsToMany
    {
        $substituteResponsibles = $this->belongsToMany(PersonnelModel::class, 'rel_proceedings_personnel', 'object_proceedings_id', 'object_personnel_id')
            ->wherePivot('typology', '=', 'substitute-responsible')
            ->withTimestamps()
            ->orderBy('object_personnel.full_name');
        return $substituteResponsibles;
    }

    /**
     * Relazione con Personale (Tabella object_personnel)
     * Personale responsabile sostitutivo
     * @return BelongsToMany
     */
    public function to_contacts(): BelongsToMany
    {
        $toContacts = $this->belongsToMany(PersonnelModel::class, 'rel_proceedings_personnel', 'object_proceedings_id', 'object_personnel_id')
            ->wherePivot('typology', '=', 'to-contact')
            ->orderBy('object_personnel.full_name');
        return $toContacts;
    }

    /**
     * Relazione con Personale (Tabella object_personnel)
     * Tutto il Personale in relazione con il Procedimento, indipendentemente alla tipologia della relazione
     * @return BelongsToMany
     */
    public function personnel(): BelongsToMany
    {
        return $this->belongsToMany(PersonnelModel::class, 'rel_proceedings_personnel', 'object_proceedings_id', 'object_personnel_id');
    }

    /**
     * Relazione con Strutture
     * Uffici responsabili del procedimento
     * @return BelongsToMany
     */
    public function offices_responsibles(): BelongsToMany
    {
        $officesResponsibles = $this->belongsToMany(StructuresModel::class, 'rel_proceedings_structures', 'object_proceedings_id', 'object_structures_id')
            ->withTimestamps()
            ->wherePivot('typology', '=', 'office-responsible');
        return $officesResponsibles;
    }

    /**
     * Relazione con Strutture
     * Altre strutture associate al procedimento
     * @return BelongsToMany
     */
    public function other_structures(): BelongsToMany
    {
        $otherStructures = $this->belongsToMany(StructuresModel::class, 'rel_proceedings_structures', 'object_proceedings_id', 'object_structures_id')
            ->withTimestamps()
            ->wherePivot('typology', '=', 'other-structure');
        return $otherStructures;
    }

    /**
     * Relazione con Strutture
     * Tutte le Strutture associate al procedimento, indipendentemente dalla tipologia della relazione
     * @return BelongsToMany
     */
    public function structures(): BelongsToMany
    {
        return $this->belongsToMany(StructuresModel::class, 'rel_proceedings_structures', 'object_proceedings_id', 'object_structures_id');
    }

    /**
     * Relazione con Normative
     * Riferimenti normativi associati al procedimento
     * @return BelongsToMany
     */
    public function normatives(): BelongsToMany
    {
        $normatives = $this->belongsToMany(NormativesModel::class, 'rel_proceedings_normatives', 'object_proceedings_id', 'object_normatives_id')
            ->withTimestamps();
        return $normatives;
    }

    /**
     * Relazione con Modulistica
     * Modulistica per cui è valido il procedimento
     * @return BelongsToMany
     */
    public function modules(): BelongsToMany
    {
        $modules = $this->belongsToMany(ModulesRegulationsModel::class, 'rel_modules_proceedings', 'object_proceedings_id', 'object_modules_regulations_id');
        return $modules;
    }

    /**
     * Relazione con Regolamenti e documentazione
     * Regolamenti e documentazione validi per il procedimento
     * @return BelongsToMany
     */
    public function regulations(): BelongsToMany
    {
        $regulations = $this->belongsToMany(RegulationsModel::class, 'rel_regulations_proceedings', 'object_proceedings_id', 'object_regulations_id');
        return $regulations;
    }

    /**
     * Relazione con Dati di monitoraggio
     * @return HasMany
     */
    public function monitoring_datas(): HasMany
    {
        return $this->hasMany(DataMonitoringProceedings::class, 'proceeding_id', 'id');
    }

    /**
     * Relazione con Oneri informativi
     * Oneri informativi validi per il procedimento
     * @return BelongsToMany
     */
    public function charges(): BelongsToMany
    {
        $charges = $this->belongsToMany(ChargesModel::class, 'rel_charges_proceedings', 'object_proceedings_id', 'object_charges_id');
        return $charges;
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
            ->where('archive_name', '=', 'proceedings');
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
            ->where('archive_name', '=', 'proceedings');
    }
}
