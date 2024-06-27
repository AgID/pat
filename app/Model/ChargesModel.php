<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Scope\InstitutionScope;
use System\Model;
use Traits\SearchableTrait;

/**
 * Modello per la tabella object_charges
 */
class ChargesModel extends Model
{
    use SearchableTrait;

    protected $table = 'object_charges';
    protected $primaryKey = 'id';

    protected string $archiveName = 'charges';

    protected string $objectName = 'Oneri informativi e obblighi';
    protected int $objectId = 27;

    protected $fillable = [
        'id',
        'owner_id',
        'institution_id',
        'state',
        'workflow_state',
        'type',
        'citizen',
        'companies',
        'normative_id',
        'title',
        'expiration_date',
        'description',
        'info_url',
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
        'field' => 'title'
    ];

    /**
     * Campi su cui effettuare la ricerca nel datatable
     * @var string[]
     */
    protected $searchable = [
        'title',
        'type',
        'users.name'
    ];

    /**
     * Campi di relazione su cui effettuare la ricerca
     * @var array[]
     */
    protected $searchableWhereHas = [
        'proceedings' => [
            'table' => 'object_proceedings',
            'field' => ['name']
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
        'proceedings',
        'regulations'
    ];

    /**
     * Costruttore, viene aggiunta la ricerca per Ente se l'utente è SuperAdmin
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
     * Per non chiamare il global scope
     * NomeClasse::withoutGlobalScope(new HasActive)
     */
    protected static function boot()
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
     * Rappresenta l'utente che ha creato l'onere
     */
    public function created_by()
    {
        return $this->belongsTo(UsersModel::class, 'owner_id');
    }

    /**
     * Relazione con Procedimenti
     * Procedimenti associati all'onere
     */
    public function proceedings()
    {
        $proceedings = $this->belongsToMany(ProceedingsModel::class, 'rel_charges_proceedings', 'object_charges_id', 'object_proceedings_id')
            ->withTimestamps();
        return $proceedings;
    }

    /**
     * Relazione con Provvedimenti
     * Provvedimenti associati all'onere
     */
    public function measures()
    {
        $measures = $this->belongsToMany(MeasuresModel::class, 'rel_charges_measures', 'object_charges_id', 'object_measures_id')
            ->withTimestamps();
        return $measures;
    }

    /**
     * Relazione con Regolamenti
     * Regolamenti associati all'onere
     */
    public function regulations()
    {
        $regulations = $this->belongsToMany(RegulationsModel::class, 'rel_charges_regulations', 'object_charges_id', 'object_regulations_id')
            ->withTimestamps()
            ->orderBy('rel_charges_regulations.object_regulations_id');
        return $regulations;
    }

    /**
     * @return BelongsTo
     * Relazione con NormativesModel
     * Rappresenta il riferimento normativo
     */
    public function normative(): BelongsTo
    {
        return $this->belongsTo(NormativesModel::class, 'normative_id');
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
            ->where('archive_name', '=', 'charges');
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
            ->where('archive_name', '=', 'charges');
    }
}
