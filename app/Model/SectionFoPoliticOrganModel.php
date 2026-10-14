<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

use System\Database;
use System\Model;
use Traits\SearchableTrait;

/**
 * Modello per la tabella rel_section_fo_politic_organ
 * @method static search(mixed $searchValue)
 */
class SectionFoPoliticOrganModel extends Model
{
    use SearchableTrait;

    protected $table = 'section_fo_politic_organ';
    protected $primaryKey = 'id';
    protected string $objectName = 'Dettaglio organi politici';

    protected string $archiveName = 'politic_organ';
    protected int $objectId = 111;

    protected $fillable = [
        'id',
        'institution_id',
        'validation_state',
        'object_structures_id',
        'section_fo_id',
        'denomination',
        'skills',
        'absence_office_reason',
        'show_personnel',
        'created_at',
        'updated_at',
    ];

    //Campi per log delle attività
    protected array $activityLog = [
        'field' => 'denomination'
    ];

    /**
     * Campi su cui effettuare la ricerca nel datatable
     *
     * @var string[]
     */
    protected $searchable = [
        'denomination',
    ];

    /**
     * Campi di relazione su cui effettuare la ricerca
     * @var array[]
     */
    protected $searchableWhereHas = [];

    /**
     * Campi con cui deve essere applicata la crittografia nella ricerca
     * @var string[]
     */
    protected array $encrypted = [
        'users.name'
    ];

    /**
     * Costruttore del modello.
     *
     * @param array $attributes
     * @throws \Exception
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
     * Relazione con InstitutionsModel
     * Rappresenta l'Ente di appartenenza.
     *
     * @return BelongsTo
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo(InstitutionsModel::class, 'institution_id');
    }


    /**
     * Relazione con i beneficiari
     */
    public function structure(): BelongsTo
    {
        return $this->belongsTo(StructuresModel::class,  'object_structures_id');
    }

}
