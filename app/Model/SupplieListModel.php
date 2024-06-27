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
 * Modello per la tabella object_supplie_list
 * Elenco fornitori
 */

class SupplieListModel extends Model
{
    use SearchableTrait;

    protected $table = 'object_supplie_list';
    protected string $archiveName = 'supplie_list';
    protected $primaryKey = 'id';

    protected string $objectName = 'Elenco partecipanti/aggiudicatari';
    protected int $objectId = 17;

    protected $fillable = [
        'id',
        'owner_id',
        'institution_id',
        'state',
        'workflow_state',
        'typology',
        'type',
        'it',
        'name',
        'vat',
        'foreign_tax_identification',
        'address',
        'phone',
        'fax',
        'email',
        'publishing_status',
        'o_id',
        'source_id',
        'number_readings',
        'publishing_responsable',
        'group_name',
        'created_at',
        'updated_at'
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
        'object_supplie_list.name',
        'object_supplie_list.vat',
        'userName'
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
        'members',
        'contestActs',
    ];

    /**
     * Campi con cui deve essere applicata la crittografia nella ricerca
     * @var string[]
     */
    // protected $encrypted = [
    //     '',
    //     '',
    //     ''
    // ];

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
     * Funzione post insert, chiamata automaticamente a seguito della funzione custom createWithLogs
     * @param object $element Elemento inserito
     * @return void
     */
    public function postInsert(object $element): void
    {
    }

    /**
     * Funzione post update, chiamata automaticamente a seguito della funzione custom updateWithLogs
     * @param object $element Elemento aggiornato
     * @return void
     */
    public function postUpdate(object $element): void
    {
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
     * Rappresenta l'utente che ha creato il fornitore
     * @return BelongsTo
     */
    public function created_by(): BelongsTo
    {
        return $this->belongsTo(UsersModel::class, 'owner_id');
    }

    /**
     * Relazione con Elenco partecipanti/aggiudicatari (supplier_list)
     * Tutti i membri del raggruppamento
     * @return BelongsToMany
     */
    public function members(): BelongsToMany
    {
        $members = $this->belongsToMany(SupplieListModel::class, 'rel_supplie_list', 'object_supplie_list_id', 'object_related_supplie_list');
        return $members;
    }

    /**
     * Relazione con Elenco partecipanti/aggiudicatari (supplier_list)
     * Fornitori capogruppo
     * @return BelongsToMany
     */
    public function group_leaders(): BelongsToMany
    {
        $groupLeaders = $this->belongsToMany(SupplieListModel::class, 'rel_supplie_list', 'object_supplie_list_id', 'object_related_supplie_list')
            ->withTimestamps()
            ->wherePivot('typology', '=', 'group_leader');
        return $groupLeaders;
    }

    /**
     * Relazione con Elenco partecipanti/aggiudicatari (supplier_list)
     * Fornitori mandanti
     * @return BelongsToMany
     */
    public function principals(): BelongsToMany
    {
        $principals = $this->belongsToMany(SupplieListModel::class, 'rel_supplie_list', 'object_supplie_list_id', 'object_related_supplie_list')
            ->withTimestamps()
            ->wherePivot('typology', '=', 'principal');
        return $principals;
    }

    /**
     * Relazione con Elenco partecipanti/aggiudicatari (supplier_list)
     * Fornitori mandanti
     * @return BelongsToMany
     */
    public function principalsList(): BelongsToMany
    {
        return $this->belongsToMany(SupplieListModel::class, 'rel_supplie_list', 'object_supplie_list_id', 'object_related_supplie_list')
            ->wherePivot('typology', '=', 'principal');
    }

    /**
     * Relazione con Elenco partecipanti/aggiudicatari (supplier_list)
     * Fornitori mandatari
     * @return BelongsToMany
     */
    public function mandatarie(): BelongsToMany
    {
        $mandatarie = $this->belongsToMany(SupplieListModel::class, 'rel_supplie_list', 'object_supplie_list_id', 'object_related_supplie_list')
            ->withTimestamps()
            ->wherePivot('typology', '=', 'mandatary');
        return $mandatarie;
    }

    /**
     * Relazione con Elenco partecipanti/aggiudicatari (supplier_list)
     * Fornitori mandanti
     * @return BelongsToMany
     */
    public function mandatarieList(): BelongsToMany
    {
        return $this->belongsToMany(SupplieListModel::class, 'rel_supplie_list', 'object_supplie_list_id', 'object_related_supplie_list')
            ->wherePivot('typology', '=', 'mandatary');
    }

    /**
     * Relazione con Elenco partecipanti/aggiudicatari (supplier_list)
     * Fornitori associati
     * @return BelongsToMany
     */
    public function associates(): BelongsToMany
    {
        $associates = $this->belongsToMany(SupplieListModel::class, 'rel_supplie_list', 'object_supplie_list_id', 'object_related_supplie_list')
            ->withTimestamps()
            ->wherePivot('typology', '=', 'associate');
        return $associates;
    }

    /**
     * Relazione con Elenco partecipanti/aggiudicatari (supplier_list)
     * Fornitori consorziati
     * @return BelongsToMany
     */
    public function consortiums(): BelongsToMany
    {
        $consortiums = $this->belongsToMany(SupplieListModel::class, 'rel_supplie_list', 'object_supplie_list_id', 'object_related_supplie_list')
            ->withTimestamps()
            ->wherePivot('typology', '=', 'consortium');
        return $consortiums;
    }

    /**
     * Relazione con Bandi di gara
     * Tutti le gare in relazione con il Fornitore (sia quelle per cui è partecipante sia quelle per cui è aggiudicatario)
     * @return BelongsToMany
     */
    public function contestActs(): BelongsToMany
    {
        return $this->belongsToMany(ContestsActsModel::class, 'rel_contests_acts_supplie_list', 'object_supplie_list_id', 'object_contest_act_id');
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
            ->where('archive_name', '=', 'supplie_list');
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
            ->where('archive_name', '=', 'supplie_list');
    }
}