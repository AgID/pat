<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Scope\InstitutionScope;
use System\Action;
use System\Model;
use Traits\SearchableTrait;


class BdncpProcedureModel extends Model
{
    use SearchableTrait;

    protected $table = 'object_bdncp_procedure';
    protected $primaryKey = 'id';
    protected string $archiveName = 'bdncp_procedure';
    protected string $objectName = 'Bandi di gara e contratti (dal 1/1/2024)';
    protected int $objectId = 91;

    protected $fillable = [
        'id',
        'owner_id',
        'institution_id',
        'object_procedure_id',
        'source_id',
        'o_id',
        'publishing_status',
        'object',
        'cig',
        'procurement_id',
        'typology',
        'type',
        'alert_date',
        'liquidation_date',
        'amount_liquidated',
        'bdncp_link',
        'multicig',
        'publish_father_document',
        'notes',
        'public_debate_check',
        'public_debate_notes',
        'notice_documents_check',
        'notice_documents_notes',
        'judging_commission_check',
        'judging_commission_notes',
        'equal_opportunities_af_check',
        'equal_opportunities_af_notes',
        'local_public_services_check',
        'local_public_services_notes',
        'advisory_board_technical_check',
        'advisory_board_technical_notes',
        'equal_opportunities_es_check',
        'equal_opportunities_es_notes',
        'free_contract_check',
        'free_contract_notes',
        'emergency_foster_check',
        'emergency_foster_notes',
        'foster_procedure_check',
        'foster_procedure_notes',
        'source_id',
        'o_id',
        '__tag',
        'created_at',
        'updated_at',
    ];

    //Campi per log delle attività
    protected array $activityLog = [
        'objectTypeField' => 'type',
        'objectType' => 'typology',
        'alert' => 'object',
        'procedure' => 'object',
        'liquidation' => 'object'
    ];

    /**
     * Campi su cui effettuare la ricerca
     * @var string[]
     */
    protected $searchable = [
        'object',
        'cig',
        'users.name'
    ];

    /**
     * Campi di relazione su cui effettuare la ricerca
     * @var array[]
     */
    protected $searchableWhereHas = [
        'commission' => [
            'table' => 'object_assignments',
            'field' => ['object', 'name'],
        ],
        'board' => [
            'table' => 'object_assignments',
            'field' => ['object', 'name'],
        ],
    ];

    /**
     * Campi con cui deve essere applicata la crittografia nella ricerca
     * @var string[]
     */
    protected array $encrypted = [
        'users.name'
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
     * Per non chiamare il global scope: NomeClasse::withoutGlobalScope(new HasActive)
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new InstitutionScope);
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
     * @return BelongsTo
     * Relazione con InstitutionsModel
     * Rappresenta l'Ente di appartenenza.
     */
    public function institution(): BelongsTo
    {

        return $this->belongsTo(InstitutionsModel::class, 'institution_id');
    }

    /**
     * Funzione post update, chiamata automaticamente a seguito della funzione custom updateWithLogs
     * @param object $element Elemento aggiornato
     * @return void
     */
    public function postInsert(object $element): void
    {
    }

    /**
     * Funzione post update, chiamata automaticamente a seguito della funzione custom updateWithLogs
     * @param object $element Elemento aggiornato
     * @return void
     */
    public function postUpdate(object $element): void
    {
    }

    /**
     * Relazione con gli Incarichi (tabella object_assignments)
     * Rappresenta la composizione della commissione giudicatrice
     * @return BelongsToMany
     */
    public function commission(): BelongsToMany
    {
        return $this->belongsToMany(AssignmentsModel::class, 'rel_bdncp_procedure_assignments', 'object_procedure_id', 'object_assignment_id')
            ->withTimestamps()
            ->wherePivot('typology', '=', 'commission');
    }

    /**
     * Relazione con gli Incarichi (tabella object_assignments)
     * Rappresenta la composizione del collegio consultivo tecnico
     * @return BelongsToMany
     */
    public function board(): BelongsToMany
    {
        $ass = $this->belongsToMany(AssignmentsModel::class, 'rel_bdncp_procedure_assignments', 'object_procedure_id', 'object_assignment_id')
            ->withTimestamps()
            ->wherePivot('typology', '=', 'board');
        return $ass;
    }

    /**
     * Relazione con Strutture Organizzative (tabella object_structures)
     * Rappresenta le strutture che appartengono a quella corrente
     * @return HasMany
     */
    public function measures(): HasMany
    {
        return $this->hasMany(MeasuresModel::class, 'object_bdncp_procedure_id', 'id');
    }

    /**
     * @return BelongsTo
     * Procedura relativa per gli avvisi
     */
    public function relative_bdncp_procedure(): BelongsTo
    {
        return $this->belongsTo(BdncpProcedureModel::class, 'object_procedure_id');
    }

    /**
     * Relazione con gli allegati
     * @return HasMany
     */
    public function attachs(): HasMany
    {
        return $this->hasMany(AttachmentsModel::class, 'archive_id', 'id')
            ->select(['id', 'archive_id', 'orig_name', 'label', 'client_name', 'bdncp_cat'])
            ->orderBy('sort', 'ASC')
            ->where('active', '=', '1')
            ->where('deleted', '=', '0')
            ->where('archive_name', '=', 'bdncp_procedure');
    }

    /**
     * Relazione con gli allegati
     * Restituisce tutti gli allegati, per il back-office
     * @return HasMany
     */
    public function all_attachs(): HasMany
    {
        return $this->hasMany(AttachmentsModel::class, 'archive_id', 'id')
            ->select(['id', 'archive_id', 'orig_name', 'label', 'client_name', 'bdncp_cat'])
            ->orderBy('bdncp_cat', 'ASC')
            ->orderBy('sort', 'ASC')
            ->where('deleted', '=', '0')
            ->where('archive_name', '=', 'bdncp_procedure');
    }

    /**
     * Scope locale per il filtraggio dei dati dei bilanci
     * @param Builder     $query Query
     * @param string|null $name  Nome
     * @param int|null    $year  Anno
     * @return void
     */
    public function scopeFilter(
        Builder $query,
        string  $object = null,
        string $cig = null
    ): void
    {
        if (!empty($object)) {
            $query->where('object_bdncp_procedure.object', 'LIKE', '%' . $object . '%');
        }

        if (!empty($cig)) {
            $query->where('object_bdncp_procedure.cig', 'LIKE', '%' . $cig . '%');
        }
    }

    /**
     * Scope locale per il filtraggio dei dati delle liquidazioni relative agli incarichi
     * @param Builder     $query     Query
     * @param string|null $object    Oggetto
     * @param string|null $startDate Data inizio
     * @param string|null $endDate   Data fine
     * @return void
     */
    public function scopeAlertFilter(
        Builder $query,
        string  $object = null,
        string  $startDate = null,
        string  $endDate = null
    ): void
    {
        if (!empty($object)) {
            $query->where('object_bdncp_procedure.object', 'LIKE', '%' . $object . '%');
        }

        if (!empty($startDate)) {
            $query->where('object_bdncp_procedure.alert_date', '>=', date('Y-m-d H:i:s', strtotime($startDate)));
        }

        if (!empty($endDate)) {
            $query->where('object_bdncp_procedure.alert_date', '<=', date('Y-m-d H:i:s', strtotime($endDate)));
        }
    }

    /**
     * Scope locale per il filtraggio dei dati delle liquidazioni
     * @param Builder     $query     Query
     * @param string|null $object    Oggetto
     * @param string|null $startDate Cig
     * @return void
     */
    public function scopeLiquidationFilter(
        Builder $query,
        string  $object = null,
        string  $cig = null
    ): void
    {
        if (!empty($object)) {
            $query->where('object_bdncp_procedure.object', 'LIKE', '%' . $object . '%');
        }

        if (!empty($cig)) {
            $query->where('object_bdncp_procedure.cig', 'LIKE', '%' . $cig . '%')
                ->orWhere('object_bdncp_procedure.cig', 'LIKE', $cig . '%');
        }
    }
}
