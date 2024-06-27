<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Scope\InstitutionScope;
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
     * Costruttore, viene aggiunta la ricerca per Ente se l'utente è SuperAdmin
     *
     * @param array $attributes
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
     */
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new InstitutionScope);
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
}
