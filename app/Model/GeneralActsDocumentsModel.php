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
use Scope\InstitutionScope;
use System\Action;
use System\Model;
use Traits\SearchableTrait;

/**
 * Modello per la tabella object_bdncp_general_acts_documents
 * BDNCP - Atti e Documenti di carattere generale
 */
class GeneralActsDocumentsModel extends Model
{
    use SearchableTrait;

    protected $table = 'object_bdncp_general_acts_documents';
    protected string $archiveName = 'general_acts_documents';
    protected $primaryKey = 'id';

    protected string $objectName = 'Atti e Documenti di carattere generale';
    protected int $objectId = 92;

    protected $fillable = [
        'id',
        'institution_id',
        'owner_id',
        'object',
        'publishing_status',
        'notes',
        'document_date',
        'external_link',
        'typology',
        'cup',
        'start_date',
        'financing_amount',
        'financial_sources',
        'procedural_implementation_status',
        'created_at',
        'updated_at'
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
        'section.name',
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
     * Costruttore, viene aggiunta la ricerca per Ente se l'utente è SuperAdmin
     * @param array $attributes Parametri costruttore
     * @throws Exception
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
        static::addGlobalScope(new InstitutionScope);
    }

    /**
     * Per non chiamare il global scope: NomeClasse::withoutGlobalScope(new HasActive)
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();
        static::addGlobalScope(new InstitutionScope);
    }

    /**
     * Relazione con gli Utenti (Tabella users)
     * Rappresenta l'utente che ha creato il bilancio
     * @return BelongsTo
     */
    public function created_by()
    {
        return $this->belongsTo(UsersModel::class, 'owner_id');
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
     * Relazione con Incarichi (tabella object_assignments)
     * Incarichi associati all'atto
     * @return BelongsTo
     */
    public function public_in(): BelongsToMany
    {
        $publicIn = $this->belongsToMany(SectionFoConfigPublicationArchive::class, 'rel_general_acts_documents_public_in', 'general_acts_documents_id', 'public_in_id', null, 'section_fo_id')
            ->withTimestamps();
        return $publicIn;
    }

    /**
     * Relazione con le section_fo per la gestione del pubblica in come criterio di pubblicazione
     * @return HasMany
     */
    public function public_in_section(): HasMany
    {
        return $this->hasMany(RelGeneralActsDocumentsPublicInModel::class, 'general_acts_documents_id', 'id');
    }

    /**
     * Relazione con gli allegati
     * @return HasMany
     */
    public function attachs(): HasMany
    {
        return $this->hasMany(AttachmentsModel::class, 'archive_id', 'id')
            ->select(['id', 'archive_id', 'orig_name', 'label'])
            ->orderBy('sort', 'ASC')
            ->where('active', '=', '1')
            ->where('deleted', '=', '0')
            ->where('archive_name', '=', 'general_acts_documents');
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
            ->where('archive_name', '=', 'general_acts_documents');
    }

    /**
     * Scope locale per il filtraggio dei dati delle liquidazioni relative agli incarichi
     * @param Builder $query Query
     * @param string|null $object Oggetto
     * @param string|null $startDate Data inizio
     * @param string|null $endDate Data fine
     * @return void
     */
    public function scopeFilter(
        Builder $query,
        string  $object = null,
        string  $startDate = null,
        string  $endDate = null
    ): void
    {
        if (!empty($object)) {
            $query->where('object_bdncp_general_acts_documents.object', 'LIKE', '%' . $object . '%');
        }

        if (!empty($startDate)) {
            $query->where('object_bdncp_general_acts_documents.document_date', '>=', date('Y-m-d H:i:s', strtotime($startDate)));
        }

        if (!empty($endDate)) {
            $query->where('object_bdncp_general_acts_documents.document_date', '<=', date('Y-m-d H:i:s', strtotime($endDate)));
        }
    }
}
