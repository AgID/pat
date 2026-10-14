<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;

use System\Model;
use Traits\SearchableTrait;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Modello per la tabella activity_log
 */
class ActivityLogModel extends Model
{
    use SearchableTrait;

    protected $table = 'activity_log';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'user_id',
        'technical_activity',
        'institution_id',
        'object_id',
        'record_id',
        'parent_id',
        'area',
        'ip_address',
        'client_info',
        'uri',
        'referer',
        'platform',
        'action',
        'action_type',
        'description',
        'origin',
        'created_at',
        'updated_at'
    ];

    /**
     * @description Campi su cui effettuare la ricerca
     * @var string[]
     */
    protected $searchable = [
        'action',
        'u.name',
    ];

    /**
     * Campi di relazione su cui effettuare la ricerca
     * @var array[]
     */
    protected $searchableWhereHas = [];

    /**
     * @descriotion Costruttore del modello.
     * @param array $attributes
     * @throws Exception
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

    /**
     * @return BelongsTo
     */
    public function user(): BelongsTo
    {
        return $this->belongsTo('Model\UsersModel', 'user_id');
    }

    /**
     * @return BelongsTo
     */
    public function institution(): BelongsTo
    {
        return $this->belongsTo('Model\InstitutionsModel', 'institution_id');
    }




    /**
     * Per non chiamare il global scope
     * NomeClasse::withoutGlobalScope(new HasActive)
     */
    protected static function boot(): void
    {
        parent::boot();

    }
}
