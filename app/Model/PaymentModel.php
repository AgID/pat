<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Scope\InstitutionScope;
use System\Model;
use Traits\SearchableTrait;

/**
 * Modello per la tabella object_payment, Dati sui pagamenti
 */

class PaymentModel extends Model
{
    use SearchableTrait;

    protected $table = 'object_payment';
    protected $primaryKey = 'id';

    protected string $metaModel = 'MetaPaymentModel';

    protected string $objectName = 'Pagamento';
    protected string $archiveName = 'payment';
    protected int $objectId = 110;

    protected $fillable = [
        'id',
        'owner_id',
        'institution_id',
        'object_supplie_id',
        'object_structures_id',
        'supplier',
        'state',
        'workflow_state',
        'validation_state',
        'object',
        'year',
        'trimester',
        'compensation',
        'expense_category',
        'expense_typology',
        'omissis',
        'notes',
        'publishing_status',
        'source_id',
        'o_id',
        'publishing_start',
        'publishing_end',
        'unfixed',
        'publishing_responsable',
        'created_at',
        'updated_at'
    ];

    //Campi per log delle attività
    protected array $activityLog = [
        'field' => 'object'

    ];

    /**
     * Campi su cui effettuare la ricerca nel datatable
     *
     * @var string[]
     */
    protected $searchable = [
        'object_payment.object',
        'object_payment.year',
        'users.name'
    ];


    /**
     * Campi con cui deve essere applicata la crittografia nella ricerca
     * @var string[]
     */
    protected array $encrypted = [
    ];

    /**
     * Campi di relazione su cui effettuare la ricerca
     * @var array[]
     */
    protected $searchableWhereHas = [];

    /**
     * Costruttore del modello.
     *
     * @param array $attributes
     * @throws Exception
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * Per non chiamare il global scope: NomeClasse::withoutGlobalScope(new HasActive)
     */
    protected static function boot(): void
    {
        parent::boot();

    }

    /**
     * Relazione con gli Utenti (Tabella users)
     * Rappresenta l'utente che ha creato il tasso di assenza
     *
     */
    public function created_by()
    {
        return $this->belongsTo(UsersModel::class, 'owner_id');
    }

    /**
     * Relazione con InstitutionsModel
     * Rappresenta l'Ente di appartenenza.
     *
     */
    public function institution()
    {
        return $this->belongsTo(InstitutionsModel::class, 'institution_id');
    }

    /**
     * Relazione con i beneficiari
     */
    public function deprecated_supplier(): BelongsTo
    {
        return $this->belongsTo(SupplieListModel::class,  'object_supplie_id');
    }

    /**
     * Relazione con i beneficiari
     */
    public function structure(): BelongsTo
    {
        return $this->belongsTo(StructuresModel::class,  'object_structures_id');
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
            ->where('archive_name', '=', 'payment');
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
            ->where('archive_name', '=', 'payment');
    }

    /**
     * Scope locale per il filtraggio in base all'ente
     */
    public function scopeFilter($query, $year = null, $category = null, $typology = null, $trimester = null, $supplierTypology = '')
    {

        if (!empty($year)) {
            $query->where('object_payment.year', $year);
        }

        if (!empty($category)) {
            $query->where('object_payment.expense_category', $category);
        }

        if (!empty($typology)) {
            $query->where('object_payment.expense_typology', $typology);
        }

        if (!empty($trimester)) {
            $query->where('object_payment.trimester', $trimester);
        }

        if (!empty($supplierTypology)) {
            $query->where('object_payment.supplier', $supplierTypology);
        }

    }

    /**
     * @return HasMany
     */
    public function meta(): HasMany
    {
        return $this->relationFromAction('r_d_o', [
            'n' => __FUNCTION__
        ]);
    }
}
