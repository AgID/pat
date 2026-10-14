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

use System\Model;
use Traits\SearchableTrait;

/**
 * Modello per la tabella object_normatives
 * Normative
 */
class NormativesModel extends Model
{
    use SearchableTrait;

    protected $table = 'object_normatives';
    protected $primaryKey = 'id';

    protected string $metaModel = 'MetaNormativesModel';
    protected string $archiveName = 'normatives';

    protected string $objectName = 'Normativa';
    protected int $objectId = 14;

    protected $fillable = [
        'id',
        'owner_id',
        'institution_id',
        'state',
        'workflow_state',
        'name',
        'issue_date',
        'act_type',
        'number',
        'protocol',
        'normative_link',
        'normative_topic',
        'description',
        'publishing_status',
        'o_id',
        'source_id',
        'number_readings',
        'attachments_id',
        'last_update_date',
        'publishing_start',
        'publishing_end',
        'unfixed',
        'created_at',
        'updated_at',
        'publishing_responsable'
    ];

    /**
     * Campi per log delle attività
     * @var array|string[]
     */
    protected array $activityLog = [
        'field' => 'name'
    ];

    /**
     * Campi su cui effettuare la ricerca
     * @var string[]
     */
    protected $searchable = [
        'object_normatives.name',
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
        'proceedings',
        'allStructures',
        'grants',
    ];

    /**
     * Costruttore del modello.
     * @param array $attributes Parametri costruttore
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
     * Relazione con gli Utenti (Tabella users)
     * Rappresenta l'utente che ha creato la normativa
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
     * Relazione con Strutture
     * Strutture per cui è valida la normativa
     * @return BelongsToMany
     */
    public function structures(): BelongsToMany
    {
        $structures = $this->belongsToMany(StructuresModel::class, 'rel_normatives_structures', 'object_normatives_id', 'object_structures_id')
            ->wherePivot('typology', '=', 'valid-normatives');
        return $structures;
    }

    /**
     * Relazione con Strutture
     * Tutte le Strutture in relazione con la Normativa, indipendentemente dalla tipologia della relazione
     * @return BelongsToMany
     */
    public function allStructures(): BelongsToMany
    {
        return $this->belongsToMany(StructuresModel::class, 'rel_normatives_structures', 'object_normatives_id', 'object_structures_id');
    }

    /**
     * Relazione con Normative
     * Normative associate alla sovvenzione
     * @return BelongsToMany
     */
    public function grants(): BelongsToMany
    {
        return $this->belongsToMany(GrantsModel::class, 'rel_grants_normatives', 'object_normatives_id', 'object_grants_id');
    }

    /**
     * Relazione con Normative
     * Riferimenti normativi associati al procedimento
     * @return BelongsToMany
     */
    public function proceedings(): BelongsToMany
    {
        return $this->belongsToMany(ProceedingsModel::class, 'rel_proceedings_normatives', 'object_normatives_id', 'object_proceedings_id');
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
            ->where('archive_name', '=', 'normatives');
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
            ->where('archive_name', '=', 'normatives');
    }

    /**
     * Scope locale per il filtraggio dei dati delle liquidazioni relative agli incarichi
     * @param Builder     $query         Query
     * @param string|null $normativeName Nome normativa
     * @param string|null $typology      Tipologia
     * @param string|null $number        Numero
     * @param string|null $startDate     Data inizio
     * @param string|null $endDate       Data fine
     * @param int|null    $office        Id ufficio
     * @return void
     */
    public function scopeFilter(
        Builder $query,
        string  $normativeName = null,
        string  $typology = null,
        string  $number = null,
        string  $startDate = null,
        string  $endDate = null,
        int     $office = null
    ): void
    {
        if (!empty($normativeName)) {
            $query->where('object_normatives.name', 'LIKE', '%' . $normativeName . '%');
        }

        if (!empty($typology)) {
            $query->where('object_normatives.act_type', '=', $typology);
        }

        if (!empty($number)) {
            $query->where('object_normatives.number', '=', $number);
        }

        if (!empty($startDate)) {
            $query->where('object_normatives.issue_date', '>=', date('Y-m-d H:i:s', strtotime($startDate)));
        }

        if (!empty($endDate)) {
            $query->where('object_normatives.issue_date', '<=', date('Y-m-d 23:59:59', strtotime($endDate)));
        }

        if (!empty($office)) {
            $query->join('rel_normatives_structures as relative', 'relative.object_normatives_id', 'object_normatives.id');
            $query->where('relative.object_structures_id', '=', $office);
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
