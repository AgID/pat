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
 * Modello per la tabella object_personnel
 * Personale
 * @method static search(mixed $searchValue)
 */
class PersonnelModel extends Model
{
    use SearchableTrait;

    protected $table = 'object_personnel';
    protected $primaryKey = 'id';

    protected string $archiveName = 'personnel';

    protected string $objectName = 'Personale';
    protected int $objectId = 3;

    protected $fillable = [
        'id',
        'owner_id',
        'institution_id',
        'role_id',
        'state',
        'workflow_state',
        'title',
        'referent',
        'full_name',
        'firstname',
        'lastname',
        'fiscal_code',
        'qualification',
        'determined_term',
        'political_role',
        'political_organ',
        'delegation',
        'delegation_text',
        'photo',
        'phone',
        'mobile_phone',
        'fax',
        'not_available_email',
        'not_available_email_txt',
        'email',
        'certified_email',
        'details_conferment_act',
        'notes',
        'compensations',
        'trips_import',
        'other_assignments',
        'other_assignments_institutions',
        'personnel_lists',
        'in_office_since',
        'in_office_until',
        'priority',
        'other_info',
        'public_in',
        'information_archive',
        'on_leave',
        'extremes_of_conference',
        'attachments_id',
        'pubblic_in',
        'publishing_status',
        'o_id',
        'source_id',
        'number_readings',
        'archived',
        'archived_info',
        'archived_end_date',
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
        'field' => 'full_name'
    ];

    /**
     * Campi su cui effettuare la ricerca nel datatable
     * @var string[]
     */
    protected $searchable = [
        'full_name',
        'firstname',
        'lastname',
        'political_role',
        'users.name'
    ];


    /**
     * Campi di relazione su cui effettuare la ricerca
     * @var array[]
     */
    protected $searchableWhereHas = [
        'role' => [
            'table' => 'role',
            'field' => ['name'],
            'as' => 'r'
        ],
        'referent_structures' => [
            'table' => 'object_structures',
            'field' => ['structure_name'],
        ]
    ];

    /**
     * @description Relazioni molti a molti del modello, utilizzate nel postDelete per eliminarle
     * @var array
     */
    protected array $relationshipsToDelete = [
        'commissions',
        'grants',
        'measures',
        'assignments',
        'companies',
        'structures',
        'public_in',
        'proceedings',
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
        DataHistoricalPersonnelModel::where('personnel_id', '=', $element['id'])
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
     * Relazione con i ruoli (tabella role)
     * Rappresenta il ruolo del personale
     * @return BelongsTo
     */
    public function role(): BelongsTo
    {
        return $this->belongsTo(RoleModel::class, 'role_id');
    }

    /**
     * Relazione con Strutture Organizzative
     * Strutture per cui il personale è referente
     * @return BelongsToMany
     */
    public function referent_structures(): BelongsToMany
    {
        $str = $this->belongsToMany(StructuresModel::class, 'rel_personnel_for_structures', 'object_personnel_id', 'object_structures_id')
            ->wherePivot('typology', '=', 'referent')
            ->withTimestamps()
            ->orderBy('object_structures.id', 'ASC');
        return $str;
    }

    /**
     * Relazione con Personale (Tabella rel_personnel_for_structures)
     * Rappresenta il personale responsabile per la struttura
     * @return BelongsToMany
     */
    public function responsible_structures(): BelongsToMany
    {
        $str = $this->belongsToMany(StructuresModel::class, 'rel_personnel_for_structures', 'object_personnel_id', 'object_structures_id')
            ->wherePivot('typology', '=', 'responsible');
        return $str;
    }

    /**
     * Relazione con Strutture (Tabella rel_personnel_for_structures)
     * Rappresenta tutte le strutture in relazione con il Personale, indipendentemente dalla tipologia della relazione
     * @return BelongsToMany
     */
    public function structures(): BelongsToMany
    {
        return $this->belongsToMany(StructuresModel::class, 'rel_personnel_for_structures', 'object_personnel_id', 'object_structures_id');
    }

    /**
     * Relazione con gli Utenti (Tabella users)
     * Rappresenta l'utente che ha creato il personale
     * @return BelongsTo
     */
    public function created_by(): BelongsTo
    {
        return $this->belongsTo(UsersModel::class, 'owner_id');
    }

    /**
     * Relazione con Sovvenzioni
     * Sovvenzioni in relazione con il personale
     * @return BelongsToMany
     */
    public function grants(): BelongsToMany
    {
        return $this->belongsToMany(GrantsModel::class, 'rel_grants_personnel', 'object_personnel_id', 'object_grants_id');
    }

    /**
     * Relazione con Enti e Società Controllate
     * Enti/Società in relazione con il personale
     * @return BelongsToMany
     */
    public function companies(): BelongsToMany
    {
        return $this->belongsToMany(CompanyModel::class, 'rel_personnel_company', 'object_personnel_id', 'object_company_id');
    }

    /**
     * Relazione con Incarichi
     * Incarichi associati al personale
     * @return BelongsToMany
     */
    public function assignments(): BelongsToMany
    {
        $assignments = $this->belongsToMany(AssignmentsModel::class, 'rel_personnel_assignments', 'object_personnel_id', 'object_assignments_id')
            ->withTimestamps();
        return $assignments;
    }

    /**
     * Relazione con Procedimenti (Tabella object_proceedings)
     * Procedimenti per il quali il personale è responsabile
     * @return BelongsToMany
     */
    public function responsibles(): BelongsToMany
    {
        $responsibles = $this->belongsToMany(ProceedingsModel::class, 'rel_proceedings_personnel', 'object_personnel_id', 'object_proceedings_id')
            ->wherePivot('typology', '=', 'responsible')
            ->orderBy('object_proceedings.name');
        return $responsibles;
    }

    /**
     * Relazione con Procedimenti (Tabella object_personnel)
     * Procedimenti seguiti come responsabile di procedimento
     * @return BelongsToMany
     */
    public function measure_responsibles(): BelongsToMany
    {
        $measureResponsibles = $this->belongsToMany(ProceedingsModel::class, 'rel_proceedings_personnel', 'object_personnel_id', 'object_proceedings_id')
            ->wherePivot('typology', '=', 'measure-responsible')
            ->orderBy('object_proceedings.name');
        return $measureResponsibles;
    }

    /**
     * Relazione con Procedimenti (Tabella object_proceedings)
     * Procedimenti seguiti come responsabile di procedimento
     * @return BelongsToMany
     */
    public function proceedings(): BelongsToMany
    {
        return $this->belongsToMany(ProceedingsModel::class, 'rel_proceedings_personnel', 'object_personnel_id', 'object_proceedings_id');
    }

    /**
     * Relazione con i Provvedimenti
     * Provvedimenti associati al personale
     * @return BelongsToMany
     */
    public function measures(): BelongsToMany
    {
        $measures = $this->belongsToMany(MeasuresModel::class, 'rel_measures_personnel', 'object_personnel_id', 'object_measures_id')
            ->withTimestamps()
            ->orderBy('object_measures.id');
        return $measures;
    }

    /**
     * Relazione con le commissioni
     * Commissioni di cui fa parte il personale
     * @return BelongsToMany
     */
    public function commissions(): BelongsToMany
    {
        $commissions = $this->belongsToMany(CommissionsModel::class, 'rel_commissions_personnel', 'object_personnel_id', 'object_commissions_id')
            ->withPivot('typology');
        return $commissions;
    }

    /**
     * Relazione con le section_fo per la gestione del pubblica in
     * @return BelongsToMany
     */
    public function public_in(): BelongsToMany
    {
        return $this->belongsToMany(SectionFoConfigPublicationArchive::class, 'rel_personnel_public_in', 'object_personnel_id', 'public_in_id', null, 'section_fo_id')
            ->withTimestamps();
    }

    /**
     * Relazione con gli organi politici
     * @return HasMany
     */
    public function political_organ(): HasMany
    {
        return $this->hasMany(RelPersonnelPoliticalOrgansModel::class, 'object_personnel_id', 'id');
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
            ->where('archive_name', '=', 'personnel');
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
            ->where('archive_name', '=', 'personnel');
    }

    /**
     * Relazione con le section_fo per la gestione del pubblica in
     * Utilizzato per la generazione degli open data
     * @return HasMany
     */
    public function public_in_filter(): HasMany
    {
        return $this->hasMany(RelPersonnelPublicIn::class, 'object_personnel_id', 'id');
    }

    /**
     * Relazione con lo storico incarichi
     * @return HasMany
     */
    public function historical_datas(): HasMany
    {
        return $this->hasMany(DataHistoricalPersonnelModel::class, 'personnel_id', 'id');
    }
}
