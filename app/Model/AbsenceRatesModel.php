<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

use System\Database;
use System\Model;
use Traits\SearchableTrait;

/**
 * Modello per la tabella object_absence_rates, tassi_assenza
 * @method static search(mixed $searchValue)
 */
class AbsenceRatesModel extends Model
{
    use SearchableTrait;

    protected $table = 'object_absence_rates';
    protected $primaryKey = 'id';

    protected string $metaModel = 'MetaAbsenceRatesModel';
    protected string $objectName = 'Tasso di assenza';
    protected string $archiveName = 'absence_rates';
    protected int $objectId = 4;

    protected $fillable = [
        'id',
        'owner_id',
        'institution_id',
        'object_structures_id',
        'state',
        'workflow_state',
        'structure_name',
        'month',
        'year',
        'presence_percentage',
        'total_absence',
        'absence_illness',
        'illness_days',
        'publishing_status',
        'o_id',
        'source_id',
        'number_readings',
        'publishing_start',
        'publishing_end',
        'unfixed',
        'attachments_id',
        'last_update_date',
        'created_at',
        'updated_at',
        'publishing_responsable'
    ];

    //Campi per log delle attività
    protected array $activityLog = [
        'field' => ['structure', 'structure_name']
    ];

    /**
     * Campi su cui effettuare la ricerca nel datatable
     *
     * @var string[]
     */
    protected $searchable = [
        'year',
        'structure_name',
        'users.name'
    ];

    /**
     * Campi di relazione su cui effettuare la ricerca
     * @var array[]
     */
    protected $searchableWhereHas = [
        'structure' => [
            'table' => 'object_structures',
            'field' => ['structure_name'],
            'as' => 'structure'
        ]
    ];

    /**
     * Campi con cui deve essere applicata la crittografia nella ricerca
     * @var string[]
     */
    protected array $encrypted = [
        'users.name'
    ];

    /**
     * Costruttore del modello.
     *
     * @param array $attributes
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * Per non chiamare il global scope: NomeClasse::withoutGlobalScope(new HasActive)
     */
    protected static function boot()
    {
        parent::boot();

    }

    /**
     * Relazione con gli Utenti (Tabella users)
     * Rappresenta l'utente che ha creato il tasso di assenza
     *
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
     * Relazione con InstitutionsModel
     * Rappresenta l'Ente di appartenenza.
     *
     * @return BelongsTo
     */
    public function institution()
    {

        return $this->belongsTo(InstitutionsModel::class, 'institution_id');
    }

    /**
     * Relazione con Strutture Organizzative (tabella object_structures)
     * Rappresenta la struttura di appartenenza
     */
    public function structure()
    {
        return $this->belongsTo(StructuresModel::class, 'object_structures_id');
    }

    /**
     * Relazione con gli allegati
     */
    public function attachs()
    {
        return $this->hasMany(AttachmentsModel::class, 'archive_id', 'id')
            ->select(['id', 'archive_id', 'orig_name', 'label'])
            ->orderBy('sort', 'ASC')
            ->where('active', '=', '1')
            ->where('deleted', '=', '0')
            ->where('archive_name', '=', 'absence_rates');
    }

    /**
     * Relazione con gli allegati
     * Restituisce tutti gli allegati, per il back-office
     */
    public function all_attachs()
    {
        return $this->hasMany(AttachmentsModel::class, 'archive_id', 'id')
            ->select(['id', 'archive_id', 'orig_name', 'label', 'client_name'])
            ->orderBy('sort', 'ASC')
            ->where('deleted', '=', '0')
            ->where('archive_name', '=', 'absence_rates');
    }

    /**
     * Scope locale per il filtraggio in base all'ente
     */
    public function scopeFilter($query, $structure = null, $year = null, $month = null, $structureName = null)
    {

        if (!empty($structure)) {
            $query->where('object_structures_id', '=', $structure);
        }

        if (!empty($year)) {
            $query->where('year', '=', $year);
        }

        if (!empty($month)) {
//            $DB = new Database();
//            $query->where('month', 'REGEXP', $DB::raw('CONCAT("[[:<:]]",\'' . $month . '\',"[[:>:]]")'));
            $query->where('month', 'LIKE', '%' . $month . '%');
        }

        if (!empty($structureName)) {
            $query->where('structure_name', 'LIKE', '%' . $structureName . '%');
        }

    }

    /**
     *
     * @return HasMany
     */
    public function meta(): HasMany
    {
        return $this->relationFromAction('r_d_o', [
            'n' => __FUNCTION__
        ]);
    }
}
