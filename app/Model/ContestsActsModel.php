<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Illuminate\Database\Eloquent\Builder;
use Illuminate\Database\Eloquent\Relations\BelongsTo;
use Illuminate\Database\Eloquent\Relations\BelongsToMany;
use Illuminate\Database\Eloquent\Relations\HasMany;
use Illuminate\Database\Eloquent\Relations\HasOne;

use System\Model;
use Traits\SearchableTrait;

/**
 * Modello per la tabella object_contests_acts
 * @method static withoutTimestamps()
 */
class ContestsActsModel extends Model
{
    use SearchableTrait;

    protected $table = 'object_contests_acts';
    protected $primaryKey = 'id';

    protected string $metaModel = 'MetaContestsActsModel';
    protected string $archiveName = 'contest_acts';

    protected $fillable = [
        'id',
        'owner_id',
        'institution_id',
        'object_structures_id',
        'object_personnel_id',
        'relative_procedure_id',
        'relative_notice_id',
        'qualification_requirement_id',
        'object_measure_id',
        'state',
        'workflow_state',
        'type',
        'typology',
        'attachments_id',
        'contract',
        'customize_admin_data',
        'adjudicator_name',
        'adjudicator_data',
        'administration_type',
        'province_office',
        'municipality_office',
        'office_address',
        'istat_office',
        'nuts_office',
        'no_amount',
        'asta_base_value',
        'anac_year',
        'act_date',
        'activation_date',
        'expiration_date',
        'guue_date',
        'guri_date',
        'cpv_code_id',
        'codice_scp',
        'decree_163',
        'url_scp',
        'cig',
        'is_multicig',
        'bdncp_link',
        'object',
        'sector',
        'details',
        'contraent_choice',
        'typology_result',
        'award_amount_value',
        'amount_liquidated',
        'publication_date_type',
        'publishing_status',
        'o_id',
        'source_id',
        'number_readings',
        'work_start_date',
        'work_end_date',
        'contracting_stations_publication_date',
        'last_update_date',
        'publishing_end',
        'unfixed',
        'publishing_responsable',
        'register_id',
        '__tag',
        'created_at',
        'updated_at'
    ];

    protected string $objectName = 'Bandi Gare e Contratti';
    protected int $objectId = 19;
    protected $hidden = ['pivot.created_at'];

    //Campi per log delle attività
    protected array $activityLog = [
        'objectTypeField' => 'type',
        'objectType' => 'typology',
        'deliberation' => 'object',
        'notice' => 'object',
        'result' => 'object',
        'foster' => 'object',
        'alert' => 'object',
        'lot' => 'object',
        'liquidation' => 'object',
        'variation' => 'object'
    ];

    /**
     * Campi su cui effettuare la ricerca nel datatable
     * @var string[]
     */
    protected $searchable = [
        'object_contests_acts.object',
        'object_contests_acts.source_id',
        'object_contests_acts.type',
        'object_contests_acts.cig',
//        'users.name'
    ];

    /**
     * Campi con cui deve essere applicata la crittografia nella ricerca
     * @var string[]
     */
    protected array $encrypted = [
        'users.name'
    ];

    /**
     * Campi di relazione su cui effettuare la ricerca
     * @var array[]
     */
    protected $searchableWhereHas = [
//        'structure' => [
//            'table' => 'object_structures',
//            'field' => ['structure_name']
//        ],
        'relative_notice' => [
            'field' => ['cig']
        ],
        'relative_procedure' => [
            'field' => [
                'cig',
            ],
//            'whereAs' => [
//                [
//                    'table' => 'relative_notice',
//                    'field' => 'cig'
//                ],
//                [
//                    'table' => 'structure',
//                    'field' => 'structure_name'
//                ]
//            ],
        ],
//        'awardees' => [
//            'table' => 'object_supplie_list',
//            'field' => ['name'],
//        ],
//        'relative_procedure_awardees' => [
//            'table' => 'object_supplie_list',
//            'field' => ['name'],
//        ],
//        'multi_lots' => [
//            'field' => ['cig']
//        ]
    ];

    /**
     * @description Relazioni molti a molti del modello, utilizzate nel postDelete per eliminarle
     * @var array
     */
    protected array $relationshipsToDelete = [
        'noticesActs',
        'supplierList',
        'public_in',
    ];

    /**
     * Costruttore del modello.
     * @param array $attributes Parametri costruttore
     * @throws Exception
     */
    public function __construct(array $attributes = [])
    {
        parent::__construct($attributes);
    }

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
     * Funzione post insert, chiamata automaticamente a seguito della funzione custom createWithLogs
     * @param object $element Record appena inserito
     * @return void
     */
    public function postInsert(object $element): void
    {
    }

    /**
     * Funzione post update, chiamata automaticamente a seguito della funzione custom updateWithLogs
     * @param object $element Elemento appena aggiornato
     * @return void
     */
    public function postUpdate(object $element): void
    {
    }

    /**
     * Funzione post delete, chiamata automaticamente a seguito della funzione custom deleteWithLogs
     * @param object $element Elemento appena eliminato
     * @return void
     */
    public function postDelete(object $element): void
    {
        RelContestsActsContestsActsModel::where('object_contests_acts_id', '=', $element['id'])
            ->orWhere('object_contests_acts_id1', '=', $element['id'])
            ->delete();
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
     * Relazione con InstitutionsModel
     * Rappresenta l'Ente di appartenenza.
     */
    public function institution_id(): BelongsTo
    {
        return $this->belongsTo(InstitutionsModel::class, 'institution_id');
    }

    /**
     * Relazione con gli Utenti (Tabella users)
     * Rappresenta l'utente che ha creato il bando di gara
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
     * Relazione con le Strutture (Tabella object_structures)
     * Rappresenta la struttura associata al bando di gara
     * @return BelongsTo
     */
    public function structure(): BelongsTo
    {
        return $this->belongsTo(StructuresModel::class, 'object_structures_id');
    }

    /**
     * Relazione con Bandi di gara e contratti
     * Altre procedure relative
     * @return BelongsToMany
     */
    public function proceedings(): BelongsToMany
    {
        $proceedings = $this->belongsToMany(ContestsActsModel::class, 'rel_contests_acts_contests_acts', 'object_contests_acts_id', 'object_contests_acts_id1')
            ->withTimestamps();
        return $proceedings;
    }

    /**
     * Relazione con Bandi di gara e contratti
     * Altri esiti o avvisi relativi all'affidamento
     * @return BelongsToMany
     */
    public function other_proceedings(): BelongsToMany
    {
        $otherProceedings = $this->belongsToMany(ContestsActsModel::class, 'rel_contests_acts_contests_acts', 'object_contests_acts_id1', 'object_contests_acts_id')
            ->whereIn('object_contests_acts.typology', ['result', 'alert']);
        return $otherProceedings;
    }

    /**
     * Relazione con gli esiti di gara
     * Rappresenta gli esiti relativi al bando
     * @return HasMany
     */
    public function relative_results(): HasMany
    {
        return $this->hasMany(ContestsActsModel::class, 'relative_notice_id', 'id')
            ->where('typology', '=', 'result');
    }

    /**
     *
     * @return HasMany
     */
    public function scp(): HasMany
    {
        return $this->relationFromAction('r_d_o', [
            'n' => __FUNCTION__
        ]);
    }

    /**
     * @return HasOne
     */
    public function avcp(): HasOne
    {
        return $this->relationFromAction('r_d_o', [
            'n' => __FUNCTION__,
            'o' => true
        ]);
    }

    /**
     * Relazione con gli avvisi
     * Rappresenta gli avvisi relativi al bando
     * @return HasMany
     */
    public function relative_alerts(): HasMany
    {
        return $this->hasMany(ContestsActsModel::class, 'relative_notice_id', 'id')
            ->where('object_contests_acts.typology', '=', 'alert');
    }

    /**
     * Relazione con gli esiti/affidamenti
     * Rappresenta gli esiti/affidamenti relativi al bando
     * @return HasMany
     */
    public function relative_foster(): HasMany
    {
        return $this->hasMany(ContestsActsModel::class, 'relative_procedure_id', 'id')
            ->where('object_contests_acts.typology', '=', 'foster');
    }

    /**
     * Relazione con i lotti
     * Rappresenta i lotti relativi al bando
     * @return HasMany
     */
    public function relative_lots(): HasMany
    {
        return $this->hasMany(ContestsActsModel::class, 'relative_notice_id', 'id')
            ->where('object_contests_acts.typology', '=', 'lot');
    }

    /**
     * Relazione con i lotti
     * Rappresenta i lotti relativi al bando, utilizzato nelle ricerche
     * @return HasMany
     */
    public function multi_lots(): HasMany
    {
        return $this->hasMany(ContestsActsModel::class, 'relative_notice_id', 'id');
    }

    /**
     * Relazione con le liquidazioni
     * Rappresenta le liquidazioni relativi al bando
     * @return HasMany
     */
    public function relative_liquidation(): HasMany
    {
        return $this->hasMany(ContestsActsModel::class, 'relative_procedure_id', 'id')
            ->where('typology', '=', 'liquidation');
    }

    /**
     * Relazione con tutte le tipologie di bandi di gara tranne gli esiti
     * Rappresenta le Altre procedure di riferimento del bando
     * @return BelongsToMany
     */
    public function relative_deliberation(): BelongsToMany
    {
        $relativeDeliberation = $this->belongsToMany(ContestsActsModel::class, 'rel_contests_acts_contests_acts', 'object_contests_acts_id1', 'object_contests_acts_id')
            ->where('object_contests_acts.typology', '=', 'deliberation');
        return $relativeDeliberation;
    }

    /**
     * Relazione con Requisiti di qualificazione
     * Requisiti di qualificazione relativi al bando di gara
     * @return BelongsToMany
     */
    public function requirements(): BelongsToMany
    {
        return $this->belongsToMany(NoticesForQualificationRequirementsModel::class, 'rel_contests_act_requirements','object_contest_act_id', 'object_requirement_id')
            ->withTimestamps();
    }

    /**
     * @return BelongsTo
     * Relazione con Requisiti di qualificazione
     */
    public function cpv_code(): BelongsTo
    {
        return $this->belongsTo(CPVCodesModel::class, 'cpv_code_id');
    }

    /**
     * Relazione con Fornitori
     * Fornitori partecipanti alla gara
     * @return BelongsToMany
     */
    public function participants(): BelongsToMany
    {
        $participants = $this->belongsToMany(SupplieListModel::class, 'rel_contests_acts_supplie_list', 'object_contest_act_id', 'object_supplie_list_id')
            ->withTimestamps()
            ->wherePivot('typology', '=', 'participant');
        return $participants;
    }

    /**
     * Relazione con Fornitori
     * Fornitori partecipanti alla gara
     * @return BelongsToMany
     */
    public function awardees(): BelongsToMany
    {
        $awardees = $this->belongsToMany(SupplieListModel::class, 'rel_contests_acts_supplie_list', 'object_contest_act_id', 'object_supplie_list_id')
            ->withTimestamps()
            ->wherePivot('typology', '=', 'awardee');
        return $awardees;
    }

    /**
     * Relazione con Fornitori
     * Tutti Aggiudicatari/Fornitori della gara
     * @return BelongsToMany
     */
    public function supplierList(): BelongsToMany
    {
        return $this->belongsToMany(SupplieListModel::class, 'rel_contests_acts_supplie_list', 'object_contest_act_id', 'object_supplie_list_id');
    }

    /**
     * Relazione con gli Esiti/Affidamenti o Esito di gara di un bando
     * Rappresenta la procedura relativa alla liquidazione
     * @return BelongsTo
     */
    public function relative_procedure(): BelongsTo
    {
        return $this->belongsTo(ContestsActsModel::class, 'relative_procedure_id');
    }

    /**
     * Relazione con i Provvedimenti
     * Rappresenta il procedimento associato al bando(avviso, delibera, affidamento, bando, esito)
     * @return BelongsTo
     */
    public function relative_measure(): BelongsTo
    {
        return $this->belongsTo(MeasuresModel::class, 'object_measure_id');
    }

    /**
     * Relazione con i Bandi di gara
     * Rappresenta il bando di gara relativo
     * @return BelongsTo
     */
    public function relative_notice(): BelongsTo
    {
        return $this->belongsTo(ContestsActsModel::class, 'relative_notice_id');
    }

    /**
     * Relazione con le section_fo per la gestione del pubblica in
     * @return BelongsToMany
     */
    public function public_in(): BelongsToMany
    {
        $publicIn = $this->belongsToMany(SectionFoConfigPublicationArchive::class, 'rel_contest_acts_public_in', 'contest_act_id', 'public_in_id', 'id', 'section_fo_id')
            ->withTimestamps()
            ->groupBy('public_in_id');
        return $publicIn;
    }

    /**
     * Relazione con le section_fo per la gestione del pubblica in
     * @return HasMany
     */
    public function public_in_section(): HasMany
    {
        return $this->hasMany(RelContestActsPublicIn::class, 'contest_act_id', 'id');
    }

    /**
     * Relazione con il Personale
     * Rappresenta il rup associato
     * @return BelongsTo
     */
    public function rup(): BelongsTo
    {
        return $this->belongsTo(PersonnelModel::class, 'object_personnel_id');
    }

    /**
     * Relazione con Fornitori
     * Fornitori aggiudicatari della gara della procedura relativa alla liquidazione
     * @return BelongsToMany
     */
    public function relative_procedure_awardees(): BelongsToMany
    {
        $relativeProcedureAwardees = $this->belongsToMany(SupplieListModel::class, 'rel_contests_acts_supplie_list', 'object_contest_act_id', 'object_supplie_list_id', 'relative_procedure_id')
            ->wherePivot('typology', '=', 'awardee');
        return $relativeProcedureAwardees;
    }

    /**
     * @return BelongsTo
     * Relazione con Scelta del contraente
     * Rappresenta la procedura di scelta del contraente associata
     * Attenzione nel caso l'uso di questa funzione dia problemi usare "contraent"
     */
    public function contraent_choice(): BelongsTo
    {
        return $this->belongsTo(ContraentChoice::class, 'contraent_choice');
    }


    /**
     * @return BelongsTo
     * Relazione con Scelta del contraente
     * Rappresenta la procedura di scelta del contraente associata
     */
    public function contraent(): BelongsTo
    {
        return $this->belongsTo(ContraentChoice::class, 'contraent_choice');
    }

    /**
     * Relazione con gli atti delle amministrazioni
     * Atti delle amministrazioni che hanno l'esito/affidamento come procedura relativa
     * @return HasMany
     */
    public function notice_acts(): HasMany
    {
        return $this->hasMany(NoticesActsModel::class, 'object_contests_acts_id', 'id');
    }

    /**
     * Relazione con gli atti delle amministrazioni
     * Atti delle amministrazioni che hanno il pubblica in "Progetti di investimento pubblico"
     * @return BelongsToMany
     */
    public function noticesActs(): BelongsToMany
    {
        return $this->belongsToMany(NoticesActsModel::class, 'rel_notices_acts_contests_acts', 'object_contests_acts_id', 'object_notices_acts_id');
    }

    /**
     * Relazione con i provvedimenti amministrativi
     * Provvedimenti amministrativi che hanno l'esito/affidamento come procedura relativa
     * @return HasMany
     */
    public function measures(): HasMany
    {
        return $this->hasMany(MeasuresModel::class, 'object_contests_acts_id', 'id');
    }

    /**
     * Relazione con gli allegati
     * Restituisce tutti gli allegati non nascosti, per il front-office
     * @return HasMany
     */
    public function attachs(): HasMany
    {
        return $this->hasMany(AttachmentsModel::class, 'archive_id', 'id')
            ->select(['id', 'archive_id', 'orig_name', 'label'])
            ->orderBy('sort', 'ASC')
            ->where('active', '=', '1')
            ->where('deleted', '=', '0')
            ->where('archive_name', '=', 'contest_acts');
    }

    /**
     * Relazione con gli allegati
     * Restituisce tutti gli allegati, per il back-office
     * @return HasMany
     */
    public function all_attachs(): HasMany
    {
        return $this->hasMany(AttachmentsModel::class, 'archive_id', 'id')
            ->select(['id', 'archive_id', 'orig_name', 'label', 'client_name'])
            ->orderBy('sort', 'ASC')
            ->where('deleted', '=', '0')
            ->where('archive_name', '=', 'contest_acts');
    }

    /**
     * Scope locale per il filtraggio dei dati delle liquidazioni relative ai bandi di gara
     * @param \Illuminate\Database\Eloquent\Builder $query Query
     * @param int|null $structure Id struttura
     * @param string|null $object Oggetto
     * @param int|null $contraent Scelta del contraente
     * @param string|null $cig Codice CIG
     * @param string|null $startLiquidationDate Data inizio (liquidazione)
     * @param string|null $endLiquidationDate Data fine (liquidazione)
     * @return void
     */
    public function scopeFilter(
        \Illuminate\Database\Eloquent\Builder $query,
        int                                   $structure = null,
        string                                $object = null,
        int                                   $contraent = null,
        string                                $cig = null,
        string                                $startLiquidationDate = null,
        string                                $endLiquidationDate = null): void
    {
        if (!empty($structure)) {
            $query->join('object_contests_acts as relative', 'relative.id', 'object_contests_acts.relative_procedure_id');
            $query->where('relative.object_structures_id', '=', $structure);
        }

        if (!empty($object)) {
            $query->where('object_contests_acts.object', 'LIKE', '%' . $object . '%');
        }

        if (!empty($contraent)) {
            $query->join('object_contests_acts as relative_contraent', 'relative_contraent.id', 'object_contests_acts.relative_procedure_id');
            $query->where('relative_contraent.contraent_choice', '=', $contraent);
        }

        if (!empty($cig)) {
            $query->join('object_contests_acts as relative_cig', 'relative_cig.id', 'object_contests_acts.relative_procedure_id');
            $query->where('relative_cig.cig', 'LIKE', '%' . $cig . '%');
        }

        if (!empty($startLiquidationDate)) {
            $query->where('object_contests_acts.activation_date', '>=', date('Y-m-d H:i:s', strtotime($startLiquidationDate)));
        }

        if (!empty($endLiquidationDate)) {
            $query->where('object_contests_acts.activation_date', '<=', date('Y-m-d 23:59:59', strtotime($endLiquidationDate)));
        }
    }


    public function scopeVariationFilter(
        \Illuminate\Database\Eloquent\Builder $query, string $object = null): void
    {
        if (!empty($object)) {
            $query->where('object_contests_acts.object', 'LIKE', '%' . $object . '%');
        }
    }

    /**
     * Scope locale per il filtraggio dei dati delle liquidazioni relative ai bandi di gara
     * @param \Illuminate\Database\Eloquent\Builder $query Query
     * @param int|null $structure Id della struttura
     * @param string|null $object Oggetto
     * @param int|null $contraent Scelta del contraente
     * @param string|null $cig Codice CIG
     * @param string|null $startDate Data inizio
     * @param string|null $endDate Data fine
     * @param string|null $sector Settore
     * @return void
     */
    public function scopeContestActsFilter(
        \Illuminate\Database\Eloquent\Builder $query,
        int                                   $structure = null,
        string                                $object = null,
        string|int                            $contraent = null,
        string                                $cig = null,
        string                                $startDate = null,
        string                                $endDate = null,
        string                                $sector = null): void
    {
        $query->join('object_contests_acts as relative', 'relative.id', '=', 'object_contests_acts.relative_notice_id', 'left outer');

        if (!empty($structure)) {
            $query->where(function($query) use ( $structure){
                $query->where('object_contests_acts.object_structures_id', '=', $structure);
                $query->orWhere(function ($query) use ($structure) {
                    $query->where('relative.object_structures_id', '=', $structure);
                });
            });

        }

        if (!empty($object)) {
            $query->where('object_contests_acts.object', 'LIKE', '%' . $object . '%');
        }

        if (!empty($sector)) {
            $query->where('object_contests_acts.sector', '=', $sector);
        }

        if (!empty($contraent)) {
            $query->where(function($query) use ($contraent){
                $query->where('object_contests_acts.contraent_choice', '=', $contraent);
                $query->orWhere(function ($query) use ($contraent) {
                    $query->where('relative.contraent_choice', '=', $contraent);
                });
            });
        }

        if (!empty($cig)) {
            $query->where(function($query) use ($cig){
                $query->where('object_contests_acts.cig', 'LIKE', '%' . $cig . '%');
                $query->orWhere(function ($query) use ($cig) {
                    $query->where('relative.cig', 'LIKE', '%' . $cig . '%');
                });
            });
        }

        if (!empty($startDate)) {
            $query->where('object_contests_acts.activation_date', '>=', date('Y-m-d H:i:s', strtotime($startDate)));
        }

        if (!empty($endDate)) {
            $query->where('object_contests_acts.activation_date', '<=', date('Y-m-d 23:59:59', strtotime($endDate)));
        }
    }

    /**
     * Scope locale per il filtraggio dei dati degli esiti e affidamenti
     * per la sezione 117
     * @param \Illuminate\Database\Eloquent\Builder $query Query
     * @param string|null $object Oggetto
     * @param string|null $cig Codice CIG
     * @param string|null $fromStartWorkDate Data inizio lavori (da)
     * @param string|null $toStartWorkDate Data inizio lavori (a)
     * @param string|null $fromEndWorkDate Data fine lavori (da)
     * @param string|null $toEndWorkDate Data fine lavori (a)
     * @return void
     */
    public function scopeFinancialManagementReports(
        \Illuminate\Database\Eloquent\Builder $query,
        string                                $object = null,
        string                                $cig = null,
        string                                $fromStartWorkDate = null,
        string                                $toStartWorkDate = null,
        string                                $fromEndWorkDate = null,
        string                                $toEndWorkDate = null
    ): void
    {
        if (!empty($object)) {
            $query->where('object_contests_acts.object', 'LIKE', '%' . $object . '%');
        }

        if (!empty($cig)) {
            $query->join('object_contests_acts as relative_cig', 'relative_cig.id', '=', 'object_contests_acts.relative_notice_id');
            $query->where(function ($query) use ($cig) {
                $query->where('relative_cig.cig', 'LIKE', '%' . $cig . '%');
                $query->orWhere('object_contests_acts.cig', 'LIKE', '%' . $cig . '%');
            });
        }

        if (!empty($fromStartWorkDate)) {
            $query->where('object_contests_acts.work_start_date', '>=', date('Y-m-d H:i:s', strtotime($fromStartWorkDate)));
        }

        if (!empty($toStartWorkDate)) {
            $query->where('object_contests_acts.work_start_date', '<=', date('Y-m-d 23:59:59', strtotime($toStartWorkDate)));
        }

        if (!empty($fromEndWorkDate)) {
            $query->where('object_contests_acts.work_end_date', '>=', date('Y-m-d H:i:s', strtotime($fromEndWorkDate)));
        }

        if (!empty($toEndWorkDate)) {
            $query->where('object_contests_acts.work_end_date', '<=', date('Y-m-d 23:59:59', strtotime($toEndWorkDate)));
        }
    }

    /**
     * Scope locale per il filtraggio dei lotti che non hanno ancora ricevuto esito
     * @param \Illuminate\Database\Eloquent\Builder $query Query
     * @return void
     */
    public function scopeLotsWithoutResult(\Illuminate\Database\Eloquent\Builder $query): void
    {
        $query->whereNotIn('id', function ($query) {
            $query->select('relative_notice_id')->from('object_contests_acts')->where('typology', 'result')->whereNotNull('relative_notice_id')->groupBy('relative_notice_id');
        });
    }

    /**
     * Scope locale per il filtraggio dei bandi di gara per data di pubblicazione
     * @param \Illuminate\Database\Eloquent\Builder $query Query
     * @return void
     */
    public function scopeActivationDate(\Illuminate\Database\Eloquent\Builder $query): void
    {
        $query->where(function ($query) {
            $query->whereNotIn('object_contests_acts.typology', ['liquidation'])
                ->where('object_contests_acts.activation_date', '<=', date('Y-m-d H:i:s'));
        });

        // Se è una liquidazione controllo la data di pubblicazione della sua procedura relativa
        $query->orWhere(function ($query) {
            $query->whereIn('object_contests_acts.typology', ['liquidation']);
            $query->whereHas('relative_procedure', function ($query) {
                $query->where('activation_date', '<=', date('Y-m-d H:i:s'));
            });
        });

        // Se è una liquidazione controllo la data di pubblicazione della sua procedura relativa
        $query->orWhere(function ($query) {
            $query->whereIn('object_contests_acts.typology', ['lot']);
            $query->whereHas('relative_notice', function ($query) {
                $query->where('activation_date', '<=', date('Y-m-d H:i:s'));
            });
        });
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

    /**
     * Scope utilizzato per non modificare le date di creazione e aggiornamento.
     * Utilizzato quando un record viene aggiornato in seguito all'aggiornamento di un record in relazione con lui.
     * ES: i lotti quando viene aggiornato il bando relativo
     * @return $this
     */
    public function scopeWithoutTimestamps(): static
    {
        $this->timestamps = false;
        return $this;
    }
}
