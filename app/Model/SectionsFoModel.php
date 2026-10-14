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
        'name',
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
        'tag',
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
        'source_id',
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
        $institutionId = PatOsInstituteId();

        $query->where('section_fo.is_system', 1);
        $query->orWhere(function ($q) use ($institutionId) {
            $q->where('section_fo.is_system', '=', 0);
            if ($institutionId != 0) {
                $q->where('section_fo.institution_id', $institutionId);
            }

        });

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
     * @return HasMany
     */
    public function user_last_update(): HasMany
    {
        return $this->hasMany(ActivityLogModel::class, 'record_id', 'id')
            ->where('activity_log.object_id', '=', $this->objectId);
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
     * Relazione con Strutture Organizzative
     * Strutture di livello dirigenziale
     * @return BelongsToMany
     */
    public function management_structure(): BelongsToMany
    {
        $str = $this->belongsToMany(StructuresModel::class, 'rel_sections_fo_politic_organ_structures', 'section_fo_id', 'object_structures_id')
            ->wherePivot('typology', '=', 'management_structure');
        return $str;
    }

    /**
     *  Relazione con Strutture Organizzative
     *  Strutture di livello non dirigenziale
     * @return BelongsToMany
     */
    public function non_management_structure(): BelongsToMany
    {
        $str = $this->belongsToMany(StructuresModel::class, 'rel_sections_fo_politic_organ_structures', 'section_fo_id', 'object_structures_id')
            ->wherePivot('typology', '=', 'non_management_structure');
        return $str;
    }


    /**
     *  Relazione con personale - personale associato all'organo'
     * @return BelongsToMany
     */
    public function politic_personnel(): BelongsToMany
    {
        $pers = $this->belongsToMany(PersonnelModel::class, 'rel_sections_fo_politic_organ_personnel', 'section_fo_id', 'object_personnel_id')
            ->where('object_personnel.archived', '!=', 1);
        return $pers;
    }

    /**
     * Relazione con i paragrafi
     * @return HasMany
     */
    public function labels(): HasMany
    {
        $institutionTypeId = patOsInstituteInfo()['institution_type_id'];
        $institutionId = checkAlternativeInstitutionId();
        return $this->hasMany(RelInstitutionTypeSectionsLabelingModel::class, 'sections_id', 'id')
            ->where(function ($query) use ($institutionTypeId, $institutionId) {
                $query->where(function ($query) use ($institutionTypeId) {
                    $query->where("rel_institution_type_sections_labeling.institution_type_id", "=", $institutionTypeId);
                    $query->whereNull("rel_institution_type_sections_labeling.institution_id");
                })
                    ->orWhere(function ($query) use ($institutionId) {
                        $query->where("rel_institution_type_sections_labeling.institution_id", "=", $institutionId);
                        $query->whereNull("rel_institution_type_sections_labeling.institution_type_id");
                    });
            });

    }

    /**
     * Scope locale per il filtraggio dei dati relativi ai paragrafi
     * @param Builder     $query Query
     * @param string|null $term  Stringa da cercare nel contenuto di un paragrafo
     * @return void
     */
    public function scopeContentFilter(Builder $query, string $term = null): void
    {
        if (!empty($term)) {
            $query->join('content_section_fo as content', 'content.section_fo_id', '=', 'section_fo.id', 'left outer');
            $query->where('content.content', ' LIKE ', '%' . $term . '%');
        }
    }

    /**
     * Scope locale per eventuali traduzioni delle sezioni in base al tipo ente
     * @param Builder  $query             Query
     * @param int|null $institutionTypeId Id tipo ente
     * @return void
     */
    public function scopeLabelFilter(Builder $query, int $institutionTypeId = null, int $instId = null): void
    {
        if (!empty($institutionTypeId)) {
            $institutionId = $instId ?? checkAlternativeInstitutionId();
            $query->leftJoin("rel_institution_type_sections_labeling", function ($join) use ($institutionTypeId, $institutionId) {
                $join->on("rel_institution_type_sections_labeling.sections_id", "=", "section_fo.id")
                    ->where(function ($query) use ($institutionTypeId, $institutionId) {
                        $query->where(function ($query) use ($institutionTypeId) {
                            $query->where("rel_institution_type_sections_labeling.institution_type_id", "=", $institutionTypeId);
                            $query->whereNull("rel_institution_type_sections_labeling.institution_id");
                        })
                            ->orWhere(function ($query) use ($institutionId) {
                                $query->where("rel_institution_type_sections_labeling.institution_id", "=", $institutionId);
                                $query->whereNull("rel_institution_type_sections_labeling.institution_type_id");
                            });
                    });
            })
                ->orderBy('rel_institution_type_sections_labeling.institution_id', 'DESC');
        }
    }
}
