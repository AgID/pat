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
use System\Model;
use Traits\SearchableTrait;

/**
 * Modello per la tabella rel_ac_rotation_structures. Rappresenta la relazione tra le tabelle object_ac_rotation e object_structures
 */
class SectionsFoModel extends Model
{
    use SearchableTrait;

    protected $table = 'section_fo';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'institution_id',
        'owner_id',
        'parent',
        'text',
        'lineage',
        'deep',
        'sort',
        'controller',
        'url',
        'icon',
        'last_modification_date',
        'activation_date',
        'expiration_date',
        'expiration_type',
        'description',
        'order',
        'id_object',
        'meta_title',
        'meta_keywords',
        'meta_description',
        'is_system',
        'default',
        'typology',
        'paragraph_title',
        'generic_html',
        'modules_title',
        'normative_title',
        'normative_subtitle',
        'normatives',
        'guide',
        'regulations_title',
        'proceedings_tite',
        'measures_title',
        'referents_title',
        'structures_title',
        'assignments_title',
        'state',
        'hide',
        'no_required',
        'workflow_state',
        'created_at',
        'updated_at',
        'deleted_at'
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
     * Campi su cui effettuare la ricerca nel datatable
     *
     * @var string[]
     */
    protected $searchable = [
        'section_fo.name',
        'content.content',
        'content.name'
    ];

    /**
     * Campi di relazione su cui effettuare la ricerca
     * @var array[]
     */
    protected $searchableWhereHas = [
        'contents' => [
            'field' => ['content', 'name'],
            'as' => 'content'
        ],
    ];

    /**
     * Scope locale per il filtraggio in base all'ente
     *
     * @param Builder $query Query
     * @return void
     */
    public function scopeInstitution(Builder $query): void
    {
        // Customizzazioni filtraggio enti
        $getIdentity = authPatOs()->getIdentity();
        $institutionId = (isset($getIdentity['options']['alternative_pat_os_id']) && isSuperAdmin())
            ? $getIdentity['options']['alternative_pat_os_id']
            : PatOsInstituteId();

        $query->where('section_fo.is_system', 1);
        $query->orWhere(function ($q) use ($institutionId) {
            $q->where('section_fo.is_system', '=', 0);
            if ($institutionId != 0) {
                $q->where('section_fo.institution_id', $institutionId);
            }

        });

    }

    /**
     * Relazione con Riferimenti Normativi (tabella rel_normative_references_sections_fo)
     * @return BelongsToMany
     */
    public function normatives(): BelongsToMany
    {
        return $this->belongsToMany(\Addons\Trasparenza\Models\NormativeReferencesModel::class, 'rel_normative_references_sections_fo', 'sections_fo_id', 'normative_references_id');
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
     * @return BelongsTo
     * Relazione con la sezione padre
     * Rappresenta la Sezione padre
     */
    public function parent(): BelongsTo
    {

        return $this->belongsTo(SectionsFoModel::class, 'parent');
    }

    /**
     * Relazione con gli Utenti (Tabella users)
     * Rappresenta l'utente che ha creato la pagina generica
     * @return BelongsTo
     */
    public function created_by(): BelongsTo
    {
        return $this->belongsTo(UsersModel::class, 'owner_id');
    }

    /**
     * Relazione con i paragrafi
     * @return HasMany
     */
    public function contents(): HasMany
    {
        return $this->hasMany(ContentSectionFoModel::class, 'section_fo_id', 'id')
            ->where('content_section_fo.deleted', 0);
    }

    /**
     * Scope locale per il filtraggio dei dati relativi ai paragrafi
     * @param Builder $query Query
     * @param string|null $term Stringa da cercare nel contenuto di un paragrafo
     * @return void
     */
    public function scopeContentFilter(Builder $query, string $term = null): void
    {
        if (!empty($term)) {
            $query->join('content_section_fo as content', 'content.section_fo_id', '=', 'section_fo.id', 'left outer');
            $query->where('content.content', ' LIKE ', '%' . $term . '%');
        }
    }
}
