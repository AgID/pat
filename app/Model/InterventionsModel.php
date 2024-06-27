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
 * Modello per la tabella object_interventions
 */
class InterventionsModel extends Model
{
    use SearchableTrait;

    protected $table = 'object_interventions';
    protected $primaryKey = 'id';

    protected string $archiveName = 'interventions';

    protected string $objectName = 'Interventi straordinari e di emergenza';
    protected int $objectId = 28;

    protected $fillable = [
        'id',
        'institution_id',
        'owner_id',
        'state',
        'workflow_state',
        'name',
        'description',
        'derogations',
        'time_limits',
        'estimated_cost',
        'effective_cost',
        'publishing_status',
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
        'field' => 'name'
    ];

    /**
     * Campi su cui effettuare la ricerca nel datatable
     * @var string[]
     */
    protected $searchable = [
        'object_interventions.name',
        'userName',
    ];

    /**
     * Campi di relazione su cui effettuare la ricerca
     * @var array[]
     */
    protected $searchableWhereHas = [
        'measures' => [
            'table' => 'object_measures',
            'field' => ['object']
        ],
        'regulations' => [
            'table' => 'object_regulations',
            'field' => ['title']
        ],
    ];

    /**
     * @description Relazioni molti a molti del modello, utilizzate nel postDelete per eliminarle
     * @var array
     */
    protected array $relationshipsToDelete = [
        'measures',
        'regulations'
    ];

    /**
     * Costruttore, viene aggiunta la ricerca per Ente se l'utente è SuperAdmin
     * @param array $attributes Array di valori
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
     * Rappresenta l'utente che ha creato l'intervento
     * @return BelongsTo
     */
    public function created_by(): BelongsTo
    {
        return $this->belongsTo(UsersModel::class, 'owner_id');
    }

    /**
     * Relazione con i Provvedimenti
     * Provvedimenti associati all'intervento
     * @return BelongsToMany
     */
    public function measures(): BelongsToMany
    {
        $measures = $this->belongsToMany(MeasuresModel::class, 'rel_interventions_measures', 'object_interventions_id', 'object_measures_id')
            ->withTimestamps();
        return $measures;
    }

    /**
     * Relazione con Regolamenti
     * Regolamenti associati all'intervento
     * @return BelongsToMany
     */
    public function regulations(): BelongsToMany
    {
        $regulations = $this->belongsToMany(RegulationsModel::class, 'rel_interventions_regulations', 'object_interventions_id', 'object_regulations_id');
        return $regulations;
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
            ->where('archive_name', '=', 'interventions');
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
            ->where('archive_name', '=', 'interventions');
    }
}
