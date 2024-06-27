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
 * Modello per la tabella object_notices_acts
 */
class NoticesActsModel extends Model
{
    use SearchableTrait;

    protected $table = 'object_notices_acts';
    protected $primaryKey = 'id';

    protected string $archiveName = 'notices_acts';

    protected string $objectName = 'Atti delle amministrazioni';
    protected int $objectId = 20;

    protected $fillable = [
        'id',
        'object_contests_acts_id',
        'owner_id',
        'institution_id',
        'state',
        'workflow_state',
        'object',
        'date',
        'projects_start_date',
        'implementation_state',
        'financial_sources',
        'total_fin_amount',
        'cup',
        'details',
        'attachments_id',
        'content',
        'public_in',
        'publishing_status',
        'o_id',
        'source_id',
        'number_readings',
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
     * Campi su cui effettuare la ricerca nel datatable
     * @var string[]
     */
    protected $searchable = [
        'object_notices_acts.object',
        'users.name'
    ];

    /**
     * Campi di relazione su cui effettuare la ricerca
     * @var array[]
     */
    protected $searchableWhereHas = [
        'relative_contest_act' => [
            'field' => ['object'],
            'as' => 'contest_act'
        ],
    ];

    /**
     * @description Relazioni molti a molti del modello, utilizzate nel postDelete per eliminarle
     * @var array
     */
    protected array $relationshipsToDelete = [
        'assignments',
        'public_in',
        'relative_contest_act',
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
     * @return BelongsTo
     * Relazione con gli Utenti (Tabella users)
     * Rappresenta l'utente che ha creato l'atto amministrativo
     */
    public function created_by(): BelongsTo
    {
        return $this->belongsTo(UsersModel::class, 'owner_id');
    }

    /**
     * Relazione con i Bandi di gara e contratti (Tabella rel_notices_acts_contests_act)
     * Rappresenta le procedura relativa dell'atto amministrativo (multiple solo per un pubblica in specifico)
     * @return BelongsToMany
     */
    public function relative_contest_act(): BelongsToMany
    {
        $contestsActs = $this->belongsToMany(ContestsActsModel::class, 'rel_notices_acts_contests_acts', 'object_notices_acts_id', 'object_contests_acts_id')
            ->withTimestamps();
        return $contestsActs;
    }

    /**
     * Relazione con Incarichi (tabella object_assignments)
     * Incarichi associati all'atto
     * @return BelongsToMany
     */
    public function assignments(): BelongsToMany
    {
        $assignments = $this->belongsToMany(AssignmentsModel::class, 'rel_assignments_notices_acts', 'object_notices_acts_id', 'object_assignments_id')
            ->withTimestamps();
        return $assignments;
    }

    /**
     * Relazione con Incarichi (tabella object_assignments)
     * Incarichi associati all'atto
     * @return BelongsTo
     */
    public function public_in(): BelongsToMany
    {
        $publicIn = $this->belongsToMany(SectionFoConfigPublicationArchive::class, 'rel_notice_acts_public_in', 'notice_act_id', 'public_in_id', null, 'section_fo_id')
            ->withTimestamps();
        return $publicIn;
    }

    /**
     * Relazione con le section_fo per la gestione del pubblica in come criterio di pubblicazione
     * @return HasMany
     */
    public function public_in_section(): HasMany
    {
        return $this->hasMany(RelNoticeActPublicInModel::class, 'notice_act_id', 'id');
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
            ->where('archive_name', '=', 'notices_acts');
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
            ->where('archive_name', '=', 'notices_acts');
    }
}
