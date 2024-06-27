<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

use Exception;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Scope\InstitutionScope;
use System\Model;
use Traits\SearchableTrait;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Modello per la tabella report_publication
 * Destinatari Report Pubblicazione
 */
class ReportPublicationModel extends Model
{
    use SearchableTrait;

    protected string $objectName = 'Report Pubblicazioni - Elenco destinatari';
    protected int $objectId = 61;

    protected $table = 'report_publication';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'name',
        'email',
        'active',
        'owner_id',
        'institution_id',
        'created_at',
        'updated_at'
    ];

    //Campi per log delle attività
    protected array $activityLog = [
        'field' => 'name'
    ];

    /**
     * Campi su cui effettuare la ricerca nel datatable
     * @var string[]
     */
    protected $searchable = [
        'name',
        'email',
    ];

    /**
     * Campi di relazione su cui effettuare la ricerca
     * @var array[]
     */
    protected $searchableWhereHas = [];

    /**
     * Costruttore, viene aggiunta la ricerca per Ente se l'utente è SuperAdmin
     * @param array $attributes Parametri costruttore
     * @throws Exception
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);

        if (isSuperAdmin(true)) {
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
     * Relazione con gli Utenti (Tabella users)
     * Rappresenta l'utente che ha creato il tasso di assenza
     *
     * @return BelongsTo
     */
    public function created_by(): BelongsTo
    {
        return $this->belongsTo(UsersModel::class, 'owner_id');
    }

    /**
     * Relazione con InstitutionsModel
     * Rappresenta l'Ente di appartenenza.
     *
     * @return BelongsTo
     */
    public function institution(): BelongsTo
    {

        return $this->belongsTo(InstitutionsModel::class, 'institution_id');
    }
}
