<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;

use System\Model;
use Traits\SearchableTrait;

/**
 * Modello per la tabella object_grants
 * Sovvenzioni e vantaggi economici
 */
class GrantsModel extends Model
{
    use SearchableTrait;

    protected $table = 'object_grants';
    protected $primaryKey = 'id';

    protected string $archiveName = 'grants';
    protected string $metaModel = 'MetaGrantsModel';

    protected string $objectName = 'Sovvenzioni e vantaggi economici';
    protected int $objectId = 24;

    protected $fillable = [
        'id',
        'owner_id',
        'institution_id',
        'object_structures_id',
        'object_regulations_id',
        'grant_id',
        'state',
        'workflow_state',
        'beneficiary_name',
        'fiscal_data_not_available',
        'fiscal_data',
        'object',
        'typology',
        'type',
        'reference_date',
        'concession_act_date',
        'start_date',
        'end_date',
        'concession_amount',
        'detection_mode',
        'omissis',
        'attachments_id',
        'compensation_paid',
        'compensation_paid_date',
        'privacy',
        'publishing_status',
        'o_id',
        'source_id',
        'number_readings',
        'notes',
        'project_link',
        'last_update_date',
        'publishing_start',
        'publishing_end',
        'unfixed',
        'created_at',
        'updated_at',
        'publishing_responsable'
    ];

    //Campi per log delle attività
    protected array $activityLog = [
        'objectTypeField' => 'typology',
        'objectType' => 'type',
        'grant' => 'object',
        'liquidation' => ['relative_grant', 'object']
    ];

    /**
     * Campi su cui effettuare la ricerca nel datatable
     * @var string[]
     */
    protected $searchable = [
        'object_grants.object',
        'object_grants.typology',
        'object_grants.beneficiary_name',
        'users.name',
    ];

    /**
     * Campi con cui deve essere applicata la crittografia nella ricerca
     * @var string[]
     */
    protected array $encrypted = [
        'users.name'
    ];

    protected $searchableWhereHas = [
        'structure' => [
            'table' => 'object_structures',
            'field' => ['structure_name']
        ],
        'personnel' => [
            'table' => 'object_personnel',
            'field' => ['full_name'],
        ],
        'relative_grant' => [
            'field' => [
                'object',
                'beneficiary_name'
            ],
            'whereAs' => [
                [
                    'table' => 'structure',
                    'field' => 'structure_name'
                ]
            ]
        ],
    ];

    /**
     * @description Relazioni molti a molti del modello, utilizzate nel postDelete per eliminarle
     * @var array
     */
    protected array $relationshipsToDelete = [
        'normatives',
        'personnel'
    ];

    /**
     * Costruttore del modello.
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * Per non chiamare il global scope
     * NomeClasse::withoutGlobalScope(new HasActive)
     */
    protected static function boot()
    {
        parent::boot();

    }

    /**
     * Relazione con gli Utenti (Tabella users)
     * Rappresenta l'utente che ha creato la sovvenzione
     * @return BelongsTo
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
     * @return BelongsTo
     * Relazione con GrantsModel
     * Rappresenta la sovvenzione relativa alla liquidazione
     */
    public function relative_grant(): BelongsTo
    {
        return $this->belongsTo(GrantsModel::class, 'grant_id');
    }

    /**
     * Relazione con le liquidazioni
     * Rappresenta le liquidazioni relative alla sovvenzione
     */
    public function relative_liquidation()
    {
        return $this->hasMany(GrantsModel::class, 'grant_id', 'id')
            ->where('object_grants.type', '=', 'liquidation')
            ->orderBy('object_grants.reference_date', 'ASC');
    }

    /**
     * Relazione con le Strutture (Tabella object_structures)
     * Rappresenta la struttura associata alla sovvenzione
     * @return BelongsTo
     */
    public function structure(): BelongsTo
    {
        return $this->belongsTo(StructuresModel::class, 'object_structures_id');
    }

    /**
     * Relazione con Personale
     * Personale dirigente o funzionario della sovvenzione
     */
    public function personnel()
    {
        $personnel = $this->belongsToMany(PersonnelModel::class, 'rel_grants_personnel', 'object_grants_id', 'object_personnel_id')
            ->withTimestamps();
        return $personnel;
    }

    /**
     * Relazione con Normative
     * Normative associate alla sovvenzione
     */
    public function normatives()
    {
        $normatives = $this->belongsToMany(NormativesModel::class, 'rel_grants_normatives', 'object_grants_id', 'object_normatives_id')
            ->withTimestamps();
        return $normatives;
    }

    /**
     * Relazione con i Regolamenti(tabella object_regulations)
     * Rappresenta il regolamento alla base della sovvenzione
     * @return BelongsTo
     */
    public function regulation(): BelongsTo
    {
        return $this->belongsTo(RegulationsModel::class, 'object_regulations_id');
    }

    /**
     * Relazione con gli allegati
     * Restituisce tutti gli allegati non nascosti, per il front-office
     */
    public function attachs()
    {
        return $this->hasMany(AttachmentsModel::class, 'archive_id', 'id')
            ->select(['id', 'archive_id', 'orig_name', 'label'])
            ->orderBy('sort', 'ASC')
            ->where('active', '=', '1')
            ->where('deleted', '=', '0')
            ->where('archive_name', '=', 'grants');
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
            ->where('archive_name', '=', 'grants');
    }

    /**
     * Scope locale per il filtraggio dei dati delle liquidazioni relative agli incarichi
     * @param \Illuminate\Database\Eloquent\Builder $query       Query
     * @param string|null                           $beneficiary Beneficiario
     * @param int|null                              $structure   Id della struttura
     * @param int|null                              $responsible Id responsabile
     * @param int|null                              $year        Anno
     * @return void
     */
    public function scopeLiquidationFilter(
        \Illuminate\Database\Eloquent\Builder $query,
        string  $beneficiary = null,
        int     $structure = null,
        int     $responsible = null,
        int     $year = null
    ): void
    {
        if (!empty($beneficiary)) {
            $query->join('object_grants as relative', 'relative.id', 'object_grants.grant_id');
            $query->where('relative.beneficiary_name', 'LIKE', '%' . $beneficiary . '%');
        }

        if (!empty($structure)) {
            $query->join('object_grants as relative_str', 'relative_str.id', 'object_grants.grant_id');
            $query->where('relative_str.object_structures_id', '=', $structure);
        }

        if (!empty($responsible)) {
            $query->join('rel_grants_personnel as relative_resp', 'relative_resp.object_grants_id', 'object_grants.grant_id');
            $query->where('relative_resp.object_personnel_id', '=', $responsible);
        }

        if (!empty($year)) {
            $query->where('object_grants.compensation_paid_date', '=', $year);
        }

    }

    /**
     * Scope locale per il filtraggio dei dati delle sovvenzioni
     * @param \Illuminate\Database\Eloquent\Builder $query       Query
     * @param string|null                           $object      Oggetto
     * @param int|null                              $year        Anno
     * @param string|null                           $beneficiary Beneficiario
     * @param int|null                              $structure   Id della struttura
     * @param int|null                              $responsible Id del responsabile
     * @return void
     */
    public function scopeGrantFilter(
        \Illuminate\Database\Eloquent\Builder $query,
        string                                $object = null,
        int                                   $year = null,
        string                                $beneficiary = null,
        int                                   $structure = null,
        int                                   $responsible = null
    ): void
    {
        if (!empty($object)) {
            $query->where('object', 'LIKE', '%' . $object . '%');
        }

        if (!empty($year)) {
            $query->whereYear('concession_act_date', '=', $year);
        }

        if (!empty($beneficiary)) {
            $query->where('privacy', '=', 0)
                ->where('beneficiary_name', 'LIKE', '%' . $beneficiary . '%');
        }

        if (!empty($structure)) {
            $query->where('object_structures_id', '=', $structure);
        }

        if (!empty($responsible)) {
            $query->join('rel_grants_personnel as relative_resp', 'relative_resp.object_grants_id', 'object_grants.id');
            $query->where('relative_resp.object_personnel_id', '=', $responsible);
        }

    }

    /**
     * Scope locale per il filtraggio dei dati dei beneficiari delle sovvenzioni
     * @param \Illuminate\Database\Eloquent\Builder $query Query
     * @param int|null                              $year  Anno
     * @return void
     */
    public function scopeBeneficiaryFilter(
        \Illuminate\Database\Eloquent\Builder $query,
        int $year = null
    ): void
    {
        if (!empty($year)) {
            $query->whereYear('concession_act_date', '=', $year);
        }
    }

    /**
     * Scope locale per la ricerca in front-office delle sovvenzioni.
     * Per le sovvenzioni con il campo Omissis(privacy) a 1 non cerca per il nome del beneficiario, mentre per quelle
     * che hanno il campo Omissis a 0 si
     * @param \Illuminate\Database\Eloquent\Builder $query Query
     * @param string|null                           $term  Beneficiario
     * @return void
     */
    public function scopePrivacyFilter(
        \Illuminate\Database\Eloquent\Builder $query,
        ?string $term = ''
    ): void
    {
        if (!empty($term)) {
            $query->orWhere(function ($query) use ($term) {
                $query->where('privacy', '=', 0)
                    ->where(function ($query) use ($term) {
                        $query->where('beneficiary_name', 'like', '%' . ($term) . '%')
                            ->orWhere('beneficiary_name', 'like', ($term) . '%')
                            ->orWhere('beneficiary_name', 'like', '%' . ($term))
                            ->orWhere('beneficiary_name', '=', ($term));
                    });
            });
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
