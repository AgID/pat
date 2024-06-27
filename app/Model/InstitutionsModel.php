<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Model;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Illuminate\Database\Eloquent\Relations\HasMany;
use System\Model;
use Traits\SearchableTrait;

/**
 * Modello per la tabella Enti
 */
class InstitutionsModel extends Model
{
    use SearchableTrait;

    protected $table = 'institutions';
    protected $primaryKey = 'id';
    protected $fillable = [
        'id',
        'id_creator',
        'institution_type_id',
        'state',
        'trasp_responsible_user_id',
        'full_name_institution',
        'short_institution_name',
        'vat',
        'email_address',
        'certified_email_address',
        'institutional_website_name',
        'institutional_website_url',
        'top_level_institution_name',
        'top_level_institution_url',
        'welcome_text',
        'footer_text',
        'accessibility_text',
        'address_street',
        'address_zip_code',
        'address_city',
        'address_province',
        'phone',
        'two_factors_identification',
        'trasparenza_logo_file',
        'activation_date',
        'expiration_date',
        'trasparenza_urls',
        'url_pat',
        'bulletin_board_url',
        'online_register_id',
        'customer_support',
        'domain_cookies',
        'simple_logo_file',
        'custom_css',
        'favicon_file',
        'opendata_channel',
        'show_update_date',
        'google_maps_api_key',
        'indexable',
        'support',
        'show_regulation_in_structure',
        'tabular_display_org_ind_pol',
        'max_users',
        'client_code',
        'smtp_username',
        'smtp_pec_username',
        'smtp_password',
        'smtp_pec_password',
        'smtp_host',
        'smtp_pec_host',
        'smtp_port',
        'smtp_pec_port',
        'smtp_security',
        'smtp_pec_security',
        'smtp_auth',
        'show_smtp_auth',
        'smtp_test_email',
        'smtp_pec_auth',
        'email_notifications',
        'email_pec_notifications',
        'link_site_home',
        'condition_archive_notices',
        'private_token',
        'limits_call_api',
        'token',
        'refresh_token',
        'active',
        'deleted',
        'created_at',
        'updated_at',
        'updated_at',
    ];

    /**
     * Campi su cui effettuare la ricerca nel datatable
     * @var string[]
     */
    protected $searchable = [
        'full_name_institution',
        'email_address',
    ];

    /**
     * Per non chiamare il global scope
     * NomeClasse::withoutGlobalScope(new HasActive)
     */
    protected static function boot()
    {
        parent::boot();

    }

    /**
     * Relazione con RelSectionExcludedModel
     * Relazione "OneToMany" per prelevare le sezioni escluse per l'ente
     *
     * @return \Illuminate\Database\Eloquent\Relations\HasMany
     */
    public function excluded_sections()
    {
        return $this->hasMany(\Model\RelSectionExcludedModel::class, 'institution_id', 'id');
    }

    /**
     * Relazione con Strutture Organizzative (tabella object_structures)
     * Rappresenta le strutture dell'ente
     * @return HasMany
     */
    public function structures(): HasMany
    {
        return $this->hasMany(\Model\StructuresModel::class, 'institution_id', 'id');
    }

    /**
     * Relazione con Personale (tabella object_personnel)
     * Rappresenta il personale dell'ente
     * @return HasMany
     */
    public function personnel(): HasMany
    {
        return $this->hasMany(\Model\PersonnelModel::class, 'institution_id', 'id');
    }

    /**
     * Relazione con Tassi di assenza (tabella object_absence_rates)
     * Rappresenta i tassi di assenza dell'ente
     * @return HasMany
     */
    public function absence_rates(): HasMany
    {
        return $this->hasMany(\Model\AbsenceRatesModel::class, 'institution_id', 'id');
    }

    /**
     * Relazione con Commissioni/Gruppi consiliari (tabella object_commissions)
     * Rappresenta le commissioni/gruppi consiliari dell'ente
     * @return HasMany
     */
    public function commissions(): HasMany
    {
        return $this->hasMany(\Model\CommissionsModel::class, 'institution_id', 'id');
    }

    /**
     * Relazione con Enti controllate (tabella object_company)
     * Rappresenta gli enti controllati dell'ente
     * @return HasMany
     */
    public function companies(): HasMany
    {
        return $this->hasMany(\Model\CompanyModel::class, 'institution_id', 'id');
    }

    /**
     * Relazione con Procedimenti(tabella object_proceedings)
     * Rappresenta i Procedimenti dell'ente
     * @return HasMany
     */
    public function proceedings(): HasMany
    {
        return $this->hasMany(\Model\ProceedingsModel::class, 'institution_id', 'id');
    }

    /**
     * Relazione con i Patrimoni Immobiliari(tabella object_real_estate_asset)
     * Rappresenta i Patrimoni Immobiliari dell'ente
     * @return HasMany
     */
    public function real_estate_assets(): HasMany
    {
        return $this->hasMany(\Model\RealEstateAssetModel::class, 'institution_id', 'id');
    }

    /**
     * Relazione con Canoni di locazione(tabella object_lease_canons)
     * Rappresenta i Canoni di Locazione dell'ente
     * @return HasMany
     */
    public function canons(): HasMany
    {
        return $this->hasMany(\Model\LeaseCanonsModel::class, 'institution_id', 'id');
    }

    /**
     * Relazione con Controlli rilievi(tabella object_relief_checks)
     * Rappresenta i Controlli e rilievi dell'ente
     * @return HasMany
     */
    public function relief_checks(): HasMany
    {
        return $this->hasMany(\Model\ReliefChecksModel::class, 'institution_id', 'id');
    }

    /**
     * Relazione con Regolamenti e documentazione(tabella object_regulations)
     * Rappresenta i Regolamenti e documentazione dell'ente
     * @return HasMany
     */
    public function regulations(): HasMany
    {
        return $this->hasMany(\Model\RegulationsModel::class, 'institution_id', 'id');
    }

    /**
     * Relazione con Modulistica(tabella object_modules_regulations)
     * Rappresenta la Modulistica dell'ente
     * @return HasMany
     */
    public function modules(): HasMany
    {
        return $this->hasMany(\Model\ModulesRegulationsModel::class, 'institution_id', 'id');
    }

    /**
     * Relazione con Normative(tabella object_normatives)
     * Rappresenta le Normative dell'ente
     * @return HasMany
     */
    public function normatives(): HasMany
    {
        return $this->hasMany(\Model\NormativesModel::class, 'institution_id', 'id');
    }

    /**
     * Relazione con Bilanci(tabella object_balance_sheets)
     * Rappresenta i Bilanci dell'ente
     * @return HasMany
     */
    public function balances(): HasMany
    {
        return $this->hasMany(\Model\BalanceSheetsModel::class, 'institution_id', 'id');
    }

    /**
     * Relazione con Partecipanti/Aggiudicatari(tabella object_supplie_list)
     * Rappresenta i Partecipanti/Aggiudicatari dell'ente
     * @return HasMany
     */
    public function suppliers(): HasMany
    {
        return $this->hasMany(\Model\SupplieListModel::class, 'institution_id', 'id');
    }

    /**
     * Relazione con Bandi di gara e contratti(tabella object_contests_acts)
     * Rappresenta i Bandi di gara dell'ente
     * @return HasMany
     */
    public function contest_acts(): HasMany
    {
        return $this->hasMany(\Model\ContestsActsModel::class, 'institution_id', 'id');
    }

    /**
     * Relazione con Atti delle amministrazioni(tabella object_notices_acts)
     * Rappresenta gli Atti amministrativi dell'ente
     * @return HasMany
     */
    public function notice_acts(): HasMany
    {
        return $this->hasMany(\Model\NoticesActsModel::class, 'institution_id', 'id');
    }

    /**
     * Relazione con Atti di programmazione(tabella object_programming_acts)
     * Rappresenta gli Atti di programmazione dell'ente
     * @return HasMany
     */
    public function programming_acts(): HasMany
    {
        return $this->hasMany(\Model\ProgrammingActsModel::class, 'institution_id', 'id');
    }

    /**
     * Relazione con Bandi di concorso(tabella object_contest)
     * Rappresenta i Bandi di concorso dell'ente
     * @return HasMany
     */
    public function contests(): HasMany
    {
        return $this->hasMany(\Model\ContestModel::class, 'institution_id', 'id');
    }

    /**
     * Relazione con Sovvenzioni e vantaggi economici(tabella object_grants)
     * Rappresenta le Sovvenzioni dell'ente
     * @return HasMany
     */
    public function grants(): HasMany
    {
        return $this->hasMany(\Model\GrantsModel::class, 'institution_id', 'id');
    }

    /**
     * Relazione con Incarichi e consulenze(tabella object_assignments)
     * Rappresenta gli Incarichi dell'ente
     * @return HasMany
     */
    public function assignments(): HasMany
    {
        return $this->hasMany(\Model\AssignmentsModel::class, 'institution_id', 'id');
    }

    /**
     * Relazione con Provvedimenti Amministrativi(tabella object_measures)
     * Rappresenta i Provvedimenti dell'ente
     * @return HasMany
     */
    public function measures(): HasMany
    {
        return $this->hasMany(\Model\MeasuresModel::class, 'institution_id', 'id');
    }

    /**
     * Relazione con Oneri informativi e obblighi(tabella object_charges)
     * Rappresenta gli Oneri dell'ente
     * @return HasMany
     */
    public function charges(): HasMany
    {
        return $this->hasMany(\Model\ChargesModel::class, 'institution_id', 'id');
    }

    /**
     * Relazione con Interventi straordinari e di emergenza(tabella object_interventions)
     * Rappresenta gli Interventi straordinari dell'ente
     * @return HasMany
     */
    public function interventions(): HasMany
    {
        return $this->hasMany(\Model\InterventionsModel::class, 'institution_id', 'id');
    }

    /**
     * Relazione con News ed avvisi(tabella object_news_notices)
     * Rappresenta le News ed avvisi dell'ente
     * @return HasMany
     */
    public function news(): HasMany
    {
        return $this->hasMany(\Model\NewsNoticesModel::class, 'institution_id', 'id');
    }

}