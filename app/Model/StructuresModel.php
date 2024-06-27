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

class StructuresModel extends Model
{
    use SearchableTrait;

    protected $table = 'object_structures';
    protected string $archiveName = 'structures';
    protected $primaryKey = 'id';

    protected string $objectName = 'Strutture Organizzative';
    protected int $objectId = 2;

    protected $fillable = [
        'id',
        'structure_of_belonging_id',
        'institution_id',
        'owner_id',
        'state',
        'workflow_state',
        'structure_name',
        'responsible_not_available',
        'referent_not_available_txt',
        'ad_interim',
        'reference_email',
        'email_not_available',
        'email_not_available_txt',
        'certified_email',
        'phone',
        'fax',
        'description',
        'articulation',
        'address',
        'lat',
        'lon',
        'based_structure',
        'address_detail',
        'timetables',
        'headquarter',
        'order',
        'archived',
        'archived_end_date',
        'archived_active_to',
        'archived_info',
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
        'field' => 'structure_name'
    ];

    /**
     * Campi su cui effettuare la ricerca nel datatable
     * @var string[]
     */
    protected $searchable = [
        'object_structures.structure_name',
        'users.name'
    ];

    /**
     * Campi di relazione su cui effettuare la ricerca
     * @var array[]
     */
    protected $searchableWhereHas = [
        'structure_of_belonging' => [
            'field' => ['structure_name'],
            'as' => 'os'
        ],
        'responsibles' => [
            'table' => 'object_personnel',
            'field' => ['full_name', 'firstname', 'lastname'],
        ],
    ];

    /**
     * @description Relazioni molti a molti del modello, utilizzate nel postDelete per eliminarle
     * @var array
     */
    protected array $relationshipsToDelete = [
        'regulations',
        'measures',
        'allNormatives',
        'personnel',
        'proceedings',
        'assets'
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
     * Relazione con Personale (Tabella rel_personnel_for_structures)
     * Rappresenta il personale responsabile per la struttura
     * @return BelongsToMany
     */
    public function responsibles(): BelongsToMany
    {
        $resp = $this->belongsToMany(PersonnelModel::class, 'rel_personnel_for_structures', 'object_structures_id', 'object_personnel_id')
            ->withTimestamps()
            ->wherePivot('typology', '=', 'responsible');
        return $resp;
    }

    /**
     * Relazione con Personale (Tabella rel_personnel_for_structures)
     * Rappresenta il personale referente per la struttura
     * @return BelongsToMany
     */
    public function referents(): BelongsToMany
    {
        $ref = $this->belongsToMany(PersonnelModel::class, 'rel_personnel_for_structures', 'object_structures_id', 'object_personnel_id')
            ->withTimestamps()
            ->wherePivot('typology', '=', 'referent')
            ->where('object_personnel.archived', '=', 0);
        return $ref;
    }

    /**
     * Relazione con Personale (Tabella rel_personnel_for_structures)
     * Rappresenta tutto il personale in relazione con la struttura, utilizzata per l'eliminazione
     * @return BelongsToMany
     */
    public function personnel(): BelongsToMany
    {
        return $this->belongsToMany(PersonnelModel::class, 'rel_personnel_for_structures', 'object_structures_id', 'object_personnel_id');
    }

    /**
     * Relazione con Patrimonio Immobiliare (Tabella rel_real_estate_asset_structures)
     * Rappresenta tutto il patrimonio immobiliare in relazione con la struttura, utilizzata per l'eliminazione
     * @return BelongsToMany
     */
    public function assets(): BelongsToMany
    {
        return $this->belongsToMany(RealEstateAssetModel::class, 'rel_real_estate_asset_structures', 'object_structures_id', 'object_real_estate_asset_id');
    }

    /**
     * Relazione con Strutture Organizzative (tabella object_structures)
     * Rappresenta la struttura di appartenenza
     * @return BelongsTo
     */
    public function structure_of_belonging(): BelongsTo
    {
        return $this->belongsTo(StructuresModel::class, 'structure_of_belonging_id');
    }

    /**
     * Relazione con Strutture Organizzative (tabella object_structures)
     * Rappresenta le strutture che appartengono a quella corrente
     * @return HasMany
     */
    public function sub_structures(): HasMany
    {
        return $this->hasMany(StructuresModel::class, 'structure_of_belonging_id', 'id');
    }

    /**
     * Relazione con gli Utenti (Tabella users)
     * Rappresenta l'utente che ha creato la struttura
     * @return BelongsTo
     */
    public function created_by(): BelongsTo
    {
        return $this->belongsTo(UsersModel::class, 'owner_id');
    }

    /**
     * Relazione con Personale (Tabella rel_personnel_for_structures)
     * Rappresenta il personale da contattare per la struttura
     * @return BelongsToMany
     */
    public function to_contact(): BelongsToMany
    {
        $toContact = $this->belongsToMany(PersonnelModel::class, 'rel_personnel_for_structures', 'object_structures_id', 'object_personnel_id')
            ->withTimestamps()
            ->wherePivot('typology', '=', 'toContact');
        return $toContact;
    }

    /**
     * Relazione con i Regolamenti e documenti
     * Regolamenti e documenti validi per la struttura
     * @return BelongsToMany
     */
    public function regulations(): BelongsToMany
    {
        $regulations = $this->belongsToMany(RegulationsModel::class, 'rel_regulations_structures', 'object_structures_id', 'object_regulations_id');
        return $regulations;
    }

    /**
     * Relazione con i Provvedimenti
     * Provvedimenti associati alla struttura
     * @return BelongsToMany
     */
    public function measures(): BelongsToMany
    {
        return $this->belongsToMany(MeasuresModel::class, 'rel_measures_structures', 'object_structures_id', 'object_measures_id');
    }

    /**
     * Relazione con i Procedimenti
     * Procedimenti associati alla struttura, utilizza per l'eliminazione
     * @return BelongsToMany
     */
    public function proceedings(): BelongsToMany
    {
        return $this->belongsToMany(ProceedingsModel::class, 'rel_proceedings_structures', 'object_structures_id', 'object_proceedings_id');
    }

    /**
     * Relazione con Normative (Tabella rel_normatives__structures)
     * Rappresenta i riferimenti normativi associati alla struttura
     * @return BelongsToMany
     */
    public function normatives(): BelongsToMany
    {
        $normatives = $this->belongsToMany(NormativesModel::class, 'rel_normatives_structures', 'object_structures_id', 'object_normatives_id')
            ->withTimestamps()
            ->wherePivot('typology', '=', 'normative-reference');
        return $normatives;
    }

    /**
     * Relazione con Normative (Tabella rel_normatives__structures)
     * Rappresenta tutte le normative associate alla struttura, indipendentemente dalla tipologia
     * @return BelongsToMany
     */
    public function allNormatives(): BelongsToMany
    {
        return $this->belongsToMany(NormativesModel::class, 'rel_normatives_structures', 'object_structures_id', 'object_normatives_id');
    }

    /**
     * Relazione con le Normative
     * Normative valide per la struttura
     * @return BelongsToMany
     */
    public function valid_normatives(): BelongsToMany
    {
        $validNormative = $this->belongsToMany(NormativesModel::class, 'rel_normatives_structures', 'object_structures_id', 'object_normatives_id')
            ->wherePivot('typology', '=', 'valid-normatives');
        return $validNormative;
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
            ->where('archive_name', '=', 'structures');
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
            ->where('archive_name', '=', 'structures');
    }
}
