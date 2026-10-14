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

use System\Log;
use System\Model;
use Traits\SearchableTrait;

class BdncpProcedureModel extends Model
{
    use SearchableTrait;

    protected $table = 'object_bdncp_procedure';
    protected $primaryKey = 'id';
    protected string $metaModel = 'MetaBdncpProcedureModel';
    protected string $archiveName = 'bdncp_procedure';
    protected string $objectName = 'Bandi di gara e contratti (dal 1/1/2024)';
    protected int $objectId = 91;

    protected $fillable = [
        'id',
        'owner_id',
        'institution_id',
        'workflow_state',
        'object_procedure_id',
        'object_structures_id',
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
        'eproc_url',
        'econnect_id',
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
        'publishing_responsable',
        'publishing_start',
        'publishing_end',
        'unfixed',
        'ac_extra_info',
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
        'liquidation' => 'object',
        'variation' => 'object'
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

    protected array $relationshipsToDelete = [
        'rel_variation_procedure',
        'measures'
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
     * Per non chiamare il global scope: NomeClasse::withoutGlobalScope(new HasActive)
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();

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
     * @return BelongsTo
     * Relazione con InstitutionsModel
     * Rappresenta l'Ente di appartenenza.
     */
    public function institution(): BelongsTo
    {

        return $this->belongsTo(InstitutionsModel::class, 'institution_id');
    }

    /**
     * Relazione con gli Incarichi (tabella object_assignments)
     * Rappresenta la composizione della commissione giudicatrice
     * @return BelongsToMany
     */
    public function commission(): BelongsToMany
    {
        $ass = $this->belongsToMany(AssignmentsModel::class, 'rel_bdncp_procedure_assignments', 'object_procedure_id', 'object_assignment_id')
            ->withTimestamps()
            ->wherePivot('typology', '=', 'commission');
        return $ass;
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
     * Relazione con Se stesso
     * Tutte le procedure relative di una variazione
     * @return BelongsToMany
     */
    public function rel_variation_procedure(): BelongsToMany
    {
        $procedures = $this->belongsToMany(BdncpProcedureModel::class, 'rel_bdncp_procedure_bdncp_procedure', 'object_procedure_id', 'object_procedure_id2')
            ->withTimestamps()
            ->wherePivot('typology', '=', 'variation');

        return $procedures;
    }

    /**
     * Tutte le variazioni inserite per un procedura pubblicata
     * @return BelongsToMany
     */
    public function variations(): BelongsToMany
    {
        $variations = $this->belongsToMany(BdncpProcedureModel::class, 'rel_bdncp_procedure_bdncp_procedure', 'object_procedure_id2', 'object_procedure_id')
            ->withTimestamps()
            ->wherePivot('typology', '=', 'variation');

        return $variations;
    }


    /**
     * Relazione con Strutture Organizzative (tabella object_structures)
     * Rappresenta la struttura
     */
    public function structure(): belongsTo
    {
        return $this->belongsTo(StructuresModel::class, 'object_structures_id');
    }


//    Vecchio metodo per la relazione con Provvedimenti, ora sostituito da una relazione BelongsToMany
//    /**
//     * Relazione con Provvedimenti
//     * @return HasMany
//     */
//    public function measures(): HasMany
//    {
//        return $this->hasMany(MeasuresModel::class, 'object_bdncp_procedure_id', 'id');
//    }

    /**
     * Relazione con Provvedimenti
     * @return BelongsToMany
     */
    public function measures(): BelongsToMany
    {
        return $this->belongsToMany(MeasuresModel::class, 'rel_measures_bdncp_procedure', 'object_procedure_id', 'object_measures_id')->withTimestamps();
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
     * @return HasMany
     * Avvisi correlati alla procedura
     */
    public function alerts(): HasMany
    {
        return $this->hasMany(BdncpProcedureModel::class, 'object_procedure_id', 'id')->where('typology', 'alert');
    }

    /**
     * @return HasMany
     * Luquidazioni correlati alla procedura
     */
    public function liquidations(): HasMany
    {
        return $this->hasMany(BdncpProcedureModel::class, 'object_procedure_id', 'id')->where('typology', 'liquidation');
    }


    /**
     * Relazione se stesso
     * Rappresenta i figli di una procedure (sono presenti se multicig)
     * @return HasMany
     */
    public function children_procedure(): HasMany
    {
        return $this->hasMany(BdncpProcedureModel::class, 'object_procedure_id', 'id');
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
     * Scope locale per il filtraggio dei dati dei bandi di gara
     * @param Builder     $query Query
     * @param string|null $name  Nome
     * @param int|null    $year  Anno
     * @return void
     */
    public function scopeFilter(
        Builder $query,
        string $object = null,
        string $cig = null,
        string $dataDal = null,
        string $dataAl = null,
        string $fase = null,
    ): void {
        if (!empty($object)) {
            $query->where('object_bdncp_procedure.object', 'LIKE', '%' . $object . '%');
        }

        if (!empty($cig)) {
            $query->where('object_bdncp_procedure.cig', 'LIKE', '%' . $cig . '%');
        }

        if (!empty($dataDal)) {
            $query->where('object_bdncp_procedure.publishing_start', '>=', date('Y-m-d H:i:s', strtotime($dataDal)));
        }

        if (!empty($dataAl)) {
            $query->where('object_bdncp_procedure.publishing_start', '<=', date('Y-m-d 23:59:59', strtotime($dataAl)));
        }

        if (
            !empty($fase) &&
            in_array($fase, ['public_debate_check', 'notice_documents_check', 'equal_opportunities_af_check', 'local_public_services_check',
                    'equal_opportunities_es_check', 'free_contract_check', 'emergency_foster_check', 'foster_procedure_check',
                'judging_commission_check', 'advisory_board_technical_check'])
        ) {
            $query->where($fase, '=', 1);
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
        string $object = null,
        string $startDate = null,
        string $endDate = null
    ): void {
        if (!empty($object)) {
            $query->where('object_bdncp_procedure.object', 'LIKE', '%' . $object . '%');
        }

        if (!empty($startDate)) {
            $query->where('object_bdncp_procedure.alert_date', '>=', date('Y-m-d H:i:s', strtotime($startDate)));
        }

        if (!empty($endDate)) {
            $query->where('object_bdncp_procedure.alert_date', '<=', date('Y-m-d 23:59:59', strtotime($endDate)));
        }

    }




    /**
     * Scope locale per il filtraggio dei dati delle variazioni in corso d'opera
     * @param Builder     $query     Query
     * @param string|null $object    Oggetto
     * @return void
     */
    public function scopeVariationFilter(
        Builder $query,
        string $object = null
    ): void {
        if (!empty($object)) {
            $query->where('object_bdncp_procedure.object', 'LIKE', '%' . $object . '%');
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
        string $object = null,
        string $cig = null
    ): void {
        if (!empty($object)) {
            $query->where('object_bdncp_procedure.object', 'LIKE', '%' . $object . '%');
        }

        if (!empty($cig)) {
            $query->where('object_bdncp_procedure.cig', 'LIKE', '%' . $cig . '%')
                ->orWhere('object_bdncp_procedure.cig', 'LIKE', $cig . '%');
        }
    }

    /**
     * Scope locale per il filtraggio dei dati delle liquidazioni relative agli incarichi
     * @param Builder      $query        Query
     * @param string|null  $typology     Tipologia
     * @param string|null  $dataFrom     Data dal
     * @param string|null  $dataTo       Data al
     * @param string|null  $user         Utente
     * @return void
     */
    public function scopeBdncpProcedureFilterDataTable(
        $query,
        $typology = null,
        $dataFrom = null,
        $dataTo = null,
        $user = null,
        $hasAttach = null
    ) {
        if (!empty($typology)) {
            $query->where('object_bdncp_procedure.typology', '=', $typology);
        }
        if (!empty($dataFrom)) {
            $query->where('object_bdncp_procedure.created_at', '>=', date('Y-m-d H:i:s', strtotime($dataFrom)));
        }
        if (!empty($dataTo)) {
            $query->where('object_bdncp_procedure.created_at', '<=', date('Y-m-d 23:59:59', strtotime($dataTo)));
        }
        if (!empty($user)) {
            $query->where('object_bdncp_procedure.owner_id', '=', $user);
        }
        if(!empty($hasAttach)){
            if($hasAttach == '1'){
                $query->whereHas('all_attachs');
            }elseif ($hasAttach == '2'){
                $query->whereDoesntHave('all_attachs');
            }

        }

    }

}
