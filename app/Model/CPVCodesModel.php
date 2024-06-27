<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use System\Model;
use Traits\SearchableTrait;

/**
 * Modello per la tabella object_notices_for_qualification_requirements
 * Requisiti di qualificazione
 */
class CPVCodesModel extends Model
{
    use SearchableTrait;

    protected $table = 'cpv_codes';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'code',
        'name',
        'created_at',
        'updated_at'
    ];

    /**
     * Campi su cui effettuare la ricerca
     * @var string[]
     */
    protected $searchable = [
        'code',
        'name'
    ];


    /**
     * Per non chiamare il global scope
     * NomeClasse::withoutGlobalScope(new HasActive)
     * @return void
     */
    protected static function boot(): void
    {
        parent::boot();
    }

    /**
     * @return string
     */
    public function getFullDescriptionAttribute(): string
    {
        return '[' . $this->code . '] ' . $this->name;
    }

}