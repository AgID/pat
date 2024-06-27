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
 * Modello per la tabella object_commissions
 */
class CommissionsModel extends Model
{
    use SearchableTrait;

    protected $table = 'object_commissions';
    protected $primaryKey = 'id';

    protected string $archiveName = 'commissions';

    protected string $objectName = 'Commissioni e gruppi consiliari';
    protected int $objectId = 5;

    protected $fillable = [
        'id',
        'owner_id',
        'institution_id',
        'state',
        'workflow_state',
        'name',
        'typology',
        'president_id',
        'description',
        'image',
        'phone',
        'fax',
        'address',
        'email',
        'order',
        'archive',
        'publishing_status',
        'o_id',
        'source_id',
        'number_readings',
        'archived_end_date',
        'activation_date',
        'expiration_date',
        'attachments_id',
        'archived',
        'archived_info',
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
     * Campi su cui effettuare la ricerca
     * @var string[]
     */
    protected $searchable = [
        'object_commissions.name',
        'typology',
        'userName',
    ];

    /**
     * Campi di relazione su cui effettuare la ricerca
     * @var array[]
     */
    protected $searchableWhereHas = [
        'president' => [
            'field' => ['full_name', 'firstname', 'lastname']
        ],
    ];

    /**
     * @description Relazioni molti a molti del modello, utilizzate nel postDelete per eliminarle
     * @var array
     */
    protected array $relationshipsToDelete = [
        'allComponents',
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
     * Rappresenta l'utente che ha creato la commissione
     * @return BelongsTo
     */
    public function created_by(): BelongsTo
    {
        return $this->belongsTo(UsersModel::class, 'owner_id');
    }

    /**
     * Relazione con il Personale (Tabella object_personnel)
     * Rappresenta l'utente che ha creato la commissione
     * @return BelongsTo
     */
    public function president(): BelongsTo
    {
        return $this->belongsTo(PersonnelModel::class, 'president_id');
    }

    /**
     * Relazione con Personale (Tabella object_personnel)
     * Personale vicepresidente della commissione o gruppo
     */
    public function vicepresidents()
    {
        $vicepresidents = $this->belongsToMany(PersonnelModel::class, 'rel_commissions_personnel', 'object_commissions_id', 'object_personnel_id')
            ->withTimestamps()
            ->wherePivot('typology', '=', 'vice-president');
        return $vicepresidents;
    }

    /**
     * Relazione con Personale (Tabella object_personnel)
     * Personale segrtari della commissione o gruppo
     */
    public function secretaries()
    {
        $secretaries = $this->belongsToMany(PersonnelModel::class, 'rel_commissions_personnel', 'object_commissions_id', 'object_personnel_id')
            ->withTimestamps()
            ->wherePivot('typology', '=', 'secretarie');
        return $secretaries;
    }

    /**
     * Relazione con Personale (Tabella object_personnel)
     * Personale membri supplenti della commissione o gruppo
     */
    public function substitutes()
    {
        $substitutes = $this->belongsToMany(PersonnelModel::class, 'rel_commissions_personnel', 'object_commissions_id', 'object_personnel_id')
            ->withTimestamps()
            ->wherePivot('typology', '=', 'substitute');
        return $substitutes;
    }

    /**
     * Relazione con Personale (Tabella object_personnel)
     * Personale membri della commissione o gruppo
     */
    public function members()
    {
        $members = $this->belongsToMany(PersonnelModel::class, 'rel_commissions_personnel', 'object_commissions_id', 'object_personnel_id')
            ->withTimestamps()
            ->wherePivot('typology', '=', 'member');
        return $members;
    }

    /**
     * Relazione con Personale (Tabella object_personnel)
     * Tutti i componenti della commissione indipendentemente dal ruolo
     */
    public function allComponents()
    {
        return $this->belongsToMany(PersonnelModel::class, 'rel_commissions_personnel', 'object_commissions_id', 'object_personnel_id');
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
            ->where('archive_name', '=', 'commissions');
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
            ->where('archive_name', '=', 'commissions');
    }
}
