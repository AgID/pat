<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Scope\InstitutionScope;
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
        'is_superadmin',
        'technical_activity',
        'institution_id',
        'object_id',
        'record_id',
        'area',
        'ip_address',
        'client_info',
        'uri',
        'referer',
        'platform',
        'action',
        'action_type',
        'description',
        'request_post',
        'request_get',
        'request_file',
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
     * @descriotion Costruttore, viene aggiunta la ricerca per Ente se l'utente è SuperAdmin
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
    protected static function boot()
    {
        parent::boot();
        static::addGlobalScope(new InstitutionScope);
    }
}
