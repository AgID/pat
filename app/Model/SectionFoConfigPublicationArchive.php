<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Scope\DeletedScope;
use System\Model;

/**
 * Modello per la tabella section_fo_config_publication_archive.
 * Utilizzata per la gestione del pubblica in degli archivi
 * @method static where(string $string, string $string1, string $string2)
 */
class SectionFoConfigPublicationArchive extends Model
{
    protected $table = 'section_fo_config_publication_archive';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'section_fo_id',
        'archive_name',
        'deleted',
        'tag',
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
        static::addGlobalScope(new DeletedScope);
    }

    /**
     * Relazione con le sezioni di front-office
     * @return BelongsTo
     */
    public function section(): BelongsTo
    {
        return $this->belongsTo(SectionsFoModel::class, 'section_fo_id')
            ->where('section_fo.hide', '!=', 1);
    }

    /**
     * Relazione con le sezioni di front-office
     * @return BelongsTo
     */
    public function all_section(): BelongsTo
    {
        return $this->belongsTo(SectionsFoModel::class, 'section_fo_id');
    }

    /**
     * Scope locale per eventuali traduzioni delle sezioni in base al tipo ente
     * @param Builder  $query             Query
     * @param int|null $institutionTypeId Id tipo ente
     * @return void
     */
    public function scopeLabelFilter(Builder $query, int $institutionTypeId = null): void
    {
        if (!empty($institutionTypeId)) {
            $query->leftJoin("rel_institution_type_sections_labeling", function ($join) use ($institutionTypeId) {
                $join->on("rel_institution_type_sections_labeling.sections_id", "=", "section_fo_config_publication_archive.section_fo_id")
                    ->where("rel_institution_type_sections_labeling.institution_type_id", "=", $institutionTypeId);
            });
        }
    }
}
