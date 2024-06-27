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
 * Modello per la tabella object_regulations
 * Regolamenti e documentazione
 */
class RegulationsModel extends Model
{
    use SearchableTrait;

    protected $table = 'object_regulations';
    protected string $archiveName = 'regulations';

    protected $primaryKey = 'id';

    protected string $objectName = 'Regolamenti e documentazione';
    protected int $objectId = 12;

    protected $fillable = [
        'id',
        'owner_id',
        'institution_id',
        'state',
        'workflow_state',
        'description',
        'title',
        'issue_date',
        'number',
        'protocol',
        'order',
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
        'section.name',
        'users.name',
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
        'structures' => [
            'table' => 'object_structures',
            'field' => ['structure_name'],
        ],
    ];

    /**
     * @description Relazioni molti a molti del modello, utilizzate nel postDelete per eliminarle
     * @var array
     */
    protected array $relationshipsToDelete = [
        'structures',
        'public_in',
        'proceedings',
        'charges',
        'interventions'
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
     * Rappresenta l'utente che ha creato il regolamento
     * @return BelongsTo
     */
    public function created_by(): BelongsTo
    {
        return $this->belongsTo(UsersModel::class, 'owner_id');
    }

    /**
     * Relazione con Strutture
     * Strutture per cui è valido il regolamento\documentazione
     * @return BelongsToMany
     */
    public function structures(): BelongsToMany
    {
        $structures = $this->belongsToMany(StructuresModel::class, 'rel_regulations_structures', 'object_regulations_id', 'object_structures_id')
            ->withTimestamps();
        return $structures;
    }

    /**
     * Relazione con Procedimenti
     * Procedimenti per cui è valido il regolamento\documentazione
     * @return BelongsToMany
     */
    public function proceedings(): BelongsToMany
    {
        $proceedings = $this->belongsToMany(ProceedingsModel::class, 'rel_regulations_proceedings', 'object_regulations_id', 'object_proceedings_id')
            ->withTimestamps();
        return $proceedings;
    }

    /**
     * Relazione con le section_fo per la gestione del pubblica in
     * @return BelongsToMany
     */
    public function public_in(): BelongsToMany
    {
        return $this->belongsToMany(SectionFoConfigPublicationArchive::class, 'rel_regulations_public_in', 'object_regulation_id', 'public_in_id', null, 'section_fo_id')
            ->withTimestamps();
    }

    /**
     * Relazione con le section_fo per la gestione del pubblica in
     * Utilizzato per la generazione degli open data
     * @return HasMany
     */
    public function public_in_filter(): HasMany
    {
        return $this->hasMany(RelRegulationsPublicIn::class, 'object_regulation_id', 'id');
    }

    /**
     * Relazione con gli Oneri
     * Oneri associati al regolamento
     * @return BelongsToMany
     */
    public function charges(): BelongsToMany
    {
        $charges = $this->belongsToMany(ChargesModel::class, 'rel_charges_regulations', 'object_regulations_id', 'object_charges_id');
        return $charges;
    }

    /**
     * Relazione con Interventi
     * Interventi associati al regolamento
     * @return BelongsToMany
     */
    public function interventions(): BelongsToMany
    {
        $interventions = $this->belongsToMany(InterventionsModel::class, 'rel_interventions_regulations', 'object_regulations_id', 'object_interventions_id');
        return $interventions;
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
            ->where('archive_name', '=', 'regulations');
    }
}
