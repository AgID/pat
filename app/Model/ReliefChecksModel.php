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
 * Modello per la tabella object_relief_checks
 * Controlli e rilievi
 */
class ReliefChecksModel extends Model
{
    use SearchableTrait;

    protected $table = 'object_relief_checks';
    protected $primaryKey = 'id';

    protected string $archiveName = 'relief_checks';

    protected string $objectName = 'Controlli e rilievi';
    protected int $objectId = 10;

    protected $fillable = [
        'id',
        'owner_id',
        'institution_id',
        'object_structures_id',
        'state',
        'workflow_state',
        'object',
        'date',
        'description',
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
        'field' => 'object'
    ];

    /**
     * Campi su cui effettuare la ricerca
     * @var string[]
     */
    protected $searchable = [
        'object',
        'users.name',
    ];

    /**
     * Campi di relazione su cui effettuare la ricerca
     * @var array[]
     */
    protected $searchableWhereHas = [];

    /**
     * @description Relazioni molti a molti del modello, utilizzate nel postDelete per eliminarle
     * @var array
     */
    protected array $relationshipsToDelete = [
        'public_in'
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
     * Rappresenta l'utente che ha creato il controllo
     * @return BelongsTo
     */
    public function created_by(): BelongsTo
    {
        return $this->belongsTo(UsersModel::class, 'owner_id');
    }

    /**
     * Relazione con le strutture organizzative
     * Rappresenta l'ufficio associato al controllo/rilievo
     * @return BelongsTo
     */
    public function office(): BelongsTo
    {
        return $this->belongsTo(StructuresModel::class, 'object_structures_id');
    }

    /**
     * Relazione con le section_fo per la gestione del pubblica in
     * @return BelongsToMany
     */
    public function public_in(): BelongsToMany
    {
        return $this->belongsToMany(SectionFoConfigPublicationArchive::class, 'rel_relief_check_public_in', 'relief_check_id', 'public_in_id', null, 'section_fo_id')
            ->withTimestamps();
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
            ->where('archive_name', '=', 'relief_checks');
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
            ->where('archive_name', '=', 'relief_checks');
    }

    /**
     * Relazione con le section_fo per la gestione del pubblica in
     * Utilizzato per la generazione degli open data
     * @return HasMany
     */
    public function public_in_filter(): HasMany
    {
        return $this->hasMany(RelReliefCheckPublicIn::class, 'relief_check_id', 'id');
    }
}
