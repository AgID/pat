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
 * Modello per la tabella object_modules_regulations
 * Modulistica
 */
class ModulesRegulationsModel extends Model
{
    use SearchableTrait;

    protected $table = 'object_modules_regulations';
    protected $primaryKey = 'id';

    protected string $metaModel = 'MetaModulesRegulationsModel';
    protected string $archiveName = 'modules_regulations';

    protected string $objectName = 'Modulistica';
    protected int $objectId = 13;

    protected $fillable = [
        'id',
        'owner_id',
        'institution_id',
        'state',
        'workflow_state',
        'typology',
        'title',
        'object_structures_id',
        'description',
        'publishing_status',
        'o_id',
        'source_id',
        'number_readings',
        'order',
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
        'field' => 'title'
    ];

    /**
     * Campi su cui effettuare la ricerca
     * @var string[]
     */
    protected $searchable = [
        'title',
        'description',
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
        'proceedings' => [
            'table' => 'object_proceedings',
            'field' => ['name'],
        ],
    ];

    /**
     * @description Relazioni molti a molti del modello, utilizzate nel postDelete per eliminarle
     * @var array
     */
    protected array $relationshipsToDelete = [
        'proceedings'
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
     * @return BelongsTo
     * Relazione con InstitutionsModel
     * Rappresenta l'Ente di appartenenza.
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(InstitutionsModel::class, 'institution_id');
    }

    /**
     * Relazione con gli Utenti (Tabella users)
     * Rappresenta l'utente che ha creato il modulo
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
     * Relazione con Procedimenti
     * Procedimenti per cui è valido il modulo
     * @return BelongsToMany
     */
    public function proceedings(): BelongsToMany
    {
        $proceedings = $this->belongsToMany(ProceedingsModel::class, 'rel_modules_proceedings', 'object_modules_regulations_id', 'object_proceedings_id')
            ->withTimestamps();
        return $proceedings;
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
            ->where('archive_name', '=', 'modules_regulations');
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
            ->where('archive_name', '=', 'modules_regulations');
    }

    /**
     * Scope locale per il filtraggio dei dati delle liquidazioni relative agli incarichi
     * @param Builder     $query Query
     * @param string|null $text  Titolo modulistica
     * @return void
     */
    public function scopeFilter(
        Builder $query,
        string  $text = null
    ): void
    {
        if (!empty($text)) {
            $query->where('object_modules_regulations.title', 'LIKE', '%' . $text . '%');
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

    /**
     * Relazione con Strutture Organizzative (tabella object_structures)
     * Rappresenta la struttura di appartenenza
     */
    public function structure(): belongsTo
    {
        return $this->belongsTo(StructuresModel::class, 'object_structures_id');
    }
}
