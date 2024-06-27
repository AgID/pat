<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use System\Route;

/**
 * BEGIN: ROUTES FRONT OFFICE
 */

Route::get('act/details/:num/:any', '\Addons\Albo\Controllers\RegisterActFrontOffice@details');


Route::get('/', !empty(\System\Registry::exist('route_app_ctrl'))
    ? \System\Registry::get('route_app_ctrl')
    : '\Http\Web\Front\HomeController@index'
);

Route::get('/page/:num/:any', getDynamicController());

if (\System\Registry::exist('__controller_detail')) {

    Route::get('/page/:num/details/:num/:any', \System\Registry::get('__controller_detail'));

}

Route::get('download/:num', '\Http\Web\Front\FileDownloader@index');

// Controller download open data
if (uri()->segment(1, 0) == 'download' && uri()->segment(2, 0) == 'open-data') {
    Route::get('download/open-data/:num', getDynamicControllerOpendata());
}
/**
 * END: ROUTES FRONT OFFICE
 */

// ---------------------------------------------------------------------------------------------------------------------

/**
 * Routes Auth, Lost Password.
 */

// Autenticazione
Route::get('/auth', '\Http\Web\Auth\AuthAdminController@index');
Route::get('/staff', function () {
    helper('url');
    redirect('/auth', 'auto', 301);
});
Route::post('/auth', '\Http\Web\Auth\AuthAdminController@login');

// Recupero password
Route::get('/lost-password', '\Http\Web\Auth\LostPasswordAdminController@index');
Route::post('/lost-password', '\Http\Web\Auth\LostPasswordAdminController@store');
Route::get('/lost-password-success', '\Http\Web\Auth\LostPasswordAdminController@lostPasswordSuccess');
Route::get('/recovery/password', '\Http\Web\Auth\LostPasswordAdminController@edit');
Route::post('/recovery/password', '\Http\Web\Auth\LostPasswordAdminController@update');

// Pagine del risultato di ricerca per il front-office
Route::get('search', 'Http\Web\Front\SearchFrontController@index');
Route::get('search/nums', 'Http\Web\Front\SearchFrontController@resultSearchNums');
Route::get('search/terms', 'Http\Web\Front\SearchFrontController@resultSearchTerms');

// Logout
Route::get('/logout', '\Http\Web\Auth\LogoutAdminController@index');

// Attivazione utente dopo la registrazione
Route::get('/user-activation/:num', '\Http\Web\System\UserActivationController@index');

// ROUTES BACK OFFICE --------------------------------------------------------------------------------------------------
Route::middleware('\Middleware\IsAuthPatOsMiddleware|\Middleware\ForceChangePassword', function () {
    Route::prefix('admin', function () {

        // Dashboard
        Route::get('dashboard', '\Http\Web\Admin\DashboardAdminController@index');

        // File Manager (ElFinder) integrato nel Framework
        Route::get('sys/filemanager', '\Http\Web\Admin\FileManagerAdminController@index');
        Route::post('sys/filemanager', '\Http\Web\Admin\FileManagerAdminController@index');

        // Pagina Profilo utente
        Route::get('profile', '\Http\Web\Admin\ProfiledAdminController@index');
        Route::post('profile/update', '\Http\Web\Admin\ProfiledAdminController@update');
        Route::post('profile/update/password', '\Http\Web\Admin\ProfiledAdminController@updatePassword');
        Route::get('profile/force/password', '\Http\Web\Admin\ProfiledAdminController@forcePassword');

        // Pagine gestione Ente
        Route::get('institution', '\Http\Web\Admin\InstitutionAdminController@index');
        Route::post('institution', '\Http\Web\Admin\InstitutionAdminController@create');
        Route::post('institution/try/sending/email', '\Http\Web\Admin\InstitutionAdminController@trySendingEmail');

        // Ajax CRUD link personalizzati
        Route::ajax('institution/link/save', '\Http\Web\Admin\InstitutionAdminController@storeCustomLinks');
        Route::ajax('institution/link/list', '\Http\Web\Admin\InstitutionAdminController@asyncListCustomLinks');

        // Pagine gestione Utente
        Route::get('user', '\Http\Web\Admin\UsersAdminController@index');
        Route::get('user/list', '\Http\Web\Admin\UsersAdminController@asyncPaginateDatatable');
        Route::get('user/create', '\Http\Web\Admin\UsersAdminController@create');
        Route::post('user/store', '\Http\Web\Admin\UsersAdminController@store');
        Route::get('user/edit/:num', '\Http\Web\Admin\UsersAdminController@edit');
        Route::post('user/update', '\Http\Web\Admin\UsersAdminController@update');
        Route::get('user/delete/:num', '\Http\Web\Admin\UsersAdminController@delete');
        Route::get('user/profiles-list', '\Http\Web\Admin\UsersAdminController@asyncGetProfiles');
        Route::get('user/deletes/', '\Http\Web\Admin\UsersAdminController@deletes');

        // Pagine gestione Acl profile
        Route::get('acl-users-profile', '\Http\Web\Admin\AclUsersProfileAdminController@index');
        Route::get('acl-users-profile/list', '\Http\Web\Admin\AclUsersProfileAdminController@asyncPaginateDatatable');
        Route::get('acl-users-profile/create', '\Http\Web\Admin\AclUsersProfileAdminController@create');
        Route::get('acl-users-profile/clone/:num', '\Http\Web\Admin\AclUsersProfileAdminController@clone');
        Route::get('acl-users-profile/edit/:num', '\Http\Web\Admin\AclUsersProfileAdminController@edit');
        Route::get('acl-users-profile/delete/:num', '\Http\Web\Admin\AclUsersProfileAdminController@delete');
        Route::get('acl-users-profile/read-only/:num', '\Http\Web\Admin\AclUsersProfileAdminController@readOnly');
        Route::post('acl-users-profile/store', '\Http\Web\Admin\AclUsersProfileAdminController@store');
        Route::post('acl-users-profile/update', '\Http\Web\Admin\AclUsersProfileAdminController@update');
        Route::get('acl-users-profile/deletes/', '\Http\Web\Admin\AclUsersProfileAdminController@deletes');

        // Pagine gestione Strutture Organizzative
        Route::get('structure', '\Http\Web\Admin\StructureAdminController@index');
        Route::get('structure/list', '\Http\Web\Admin\StructureAdminController@asyncPaginateDatatable');
        Route::get('structure/create', '\Http\Web\Admin\StructureAdminController@create');
        Route::post('structure/store', '\Http\Web\Admin\StructureAdminController@store');
        Route::get('structure/edit/:num', '\Http\Web\Admin\StructureAdminController@edit');
        Route::get('structure/duplicate/:num', '\Http\Web\Admin\StructureAdminController@edit');
        Route::post('structure/update', '\Http\Web\Admin\StructureAdminController@update');
        Route::get('structure/delete/:num', '\Http\Web\Admin\StructureAdminController@delete');
        Route::get('structure/deletes/', '\Http\Web\Admin\StructureAdminController@deletes');
        Route::get('structure/create-box', '\Http\Web\Admin\StructureAdminController@create');
        Route::get('structure/edit-box/:num', '\Http\Web\Admin\StructureAdminController@edit');

        // Pagine gestione Personale
        Route::get('personnel', '\Http\Web\Admin\PersonnelAdminController@index');
        Route::get('personnel/list', '\Http\Web\Admin\PersonnelAdminController@asyncPaginateDatatable');
        Route::get('personnel/create', '\Http\Web\Admin\PersonnelAdminController@create');
        Route::post('personnel/store', '\Http\Web\Admin\PersonnelAdminController@store');
        Route::get('personnel/edit/:num', '\Http\Web\Admin\PersonnelAdminController@edit');
        Route::get('personnel/duplicate/:num', '\Http\Web\Admin\PersonnelAdminController@edit');
        Route::post('personnel/update', '\Http\Web\Admin\PersonnelAdminController@update');
        Route::get('personnel/delete/:num', '\Http\Web\Admin\PersonnelAdminController@delete');
        Route::get('personnel/deletes/', '\Http\Web\Admin\PersonnelAdminController@deletes');
        Route::get('personnel/create-box', '\Http\Web\Admin\PersonnelAdminController@create');
        Route::get('personnel/edit-box/:num', '\Http\Web\Admin\PersonnelAdminController@edit');

        // Pagine gestione Tassi di assenze
        Route::get('absence-rates', '\Http\Web\Admin\AbsenceRatesAdminController@index');
        Route::get('absence-rates/list', '\Http\Web\Admin\AbsenceRatesAdminController@asyncPaginateDatatable');
        Route::get('absence-rates/create', '\Http\Web\Admin\AbsenceRatesAdminController@create');
        Route::post('absence-rates/store', '\Http\Web\Admin\AbsenceRatesAdminController@store');
        Route::get('absence-rates/edit/:num', '\Http\Web\Admin\AbsenceRatesAdminController@edit');
        Route::get('absence-rates/duplicate/:num', '\Http\Web\Admin\AbsenceRatesAdminController@edit');
        Route::post('absence-rates/update', '\Http\Web\Admin\AbsenceRatesAdminController@update');
        Route::get('absence-rates/delete/:num', '\Http\Web\Admin\AbsenceRatesAdminController@delete');
        Route::get('absence-rates/deletes/', '\Http\Web\Admin\AbsenceRatesAdminController@deletes');

        // Pagine gestione Commissioni
        Route::get('commission', '\Http\Web\Admin\CommissionAdminController@index');
        Route::get('commission/list', '\Http\Web\Admin\CommissionAdminController@asyncPaginateDatatable');
        Route::get('commission/create', '\Http\Web\Admin\CommissionAdminController@create');
        Route::post('commission/store', '\Http\Web\Admin\CommissionAdminController@store');
        Route::get('commission/edit/:num', '\Http\Web\Admin\CommissionAdminController@edit');
        Route::get('commission/duplicate/:num', '\Http\Web\Admin\CommissionAdminController@edit');
        Route::post('commission/update', '\Http\Web\Admin\CommissionAdminController@update');
        Route::get('commission/delete/:num', '\Http\Web\Admin\CommissionAdminController@delete');
        Route::get('commission/deletes/', '\Http\Web\Admin\CommissionAdminController@deletes');
        Route::get('commission/create-box', '\Http\Web\Admin\CommissionAdminController@create');

        // Pagine gestione Enti e società controllate
        Route::get('company', '\Http\Web\Admin\CompanyAdminController@index');
        Route::get('company/list', '\Http\Web\Admin\CompanyAdminController@asyncPaginateDatatable');
        Route::get('company/create', '\Http\Web\Admin\CompanyAdminController@create');
        Route::post('company/store', '\Http\Web\Admin\CompanyAdminController@store');
        Route::get('company/edit/:num', '\Http\Web\Admin\CompanyAdminController@edit');
        Route::get('company/duplicate/:num', '\Http\Web\Admin\CompanyAdminController@edit');
        Route::post('company/update', '\Http\Web\Admin\CompanyAdminController@update');
        Route::get('company/delete/:num', '\Http\Web\Admin\CompanyAdminController@delete');
        Route::get('company/deletes/', '\Http\Web\Admin\CompanyAdminController@deletes');
        Route::get('company/create-box', '\Http\Web\Admin\CompanyAdminController@create');

        // Pagine gestione Procedimenti
        Route::get('proceeding', '\Http\Web\Admin\ProceedingAdminController@index');
        Route::get('proceeding/list', '\Http\Web\Admin\ProceedingAdminController@asyncPaginateDatatable');
        Route::get('proceeding/create', '\Http\Web\Admin\ProceedingAdminController@create');
        Route::post('proceeding/store', '\Http\Web\Admin\ProceedingAdminController@store');
        Route::get('proceeding/edit/:num', '\Http\Web\Admin\ProceedingAdminController@edit');
        Route::get('proceeding/duplicate/:num', '\Http\Web\Admin\ProceedingAdminController@edit');
        Route::post('proceeding/update', '\Http\Web\Admin\ProceedingAdminController@update');
        Route::get('proceeding/delete/:num', '\Http\Web\Admin\ProceedingAdminController@delete');
        Route::get('proceeding/deletes/', '\Http\Web\Admin\ProceedingAdminController@deletes');
        Route::get('proceeding/create-box', '\Http\Web\Admin\ProceedingAdminController@create');

        // Pagine gestione Patrimonio immobiliare
        Route::get('real-estate-asset', '\Http\Web\Admin\RealEstateAssetAdminController@index');
        Route::get('real-estate-asset/list', '\Http\Web\Admin\RealEstateAssetAdminController@asyncPaginateDatatable');
        Route::get('real-estate-asset/create', '\Http\Web\Admin\RealEstateAssetAdminController@create');
        Route::post('real-estate-asset/store', '\Http\Web\Admin\RealEstateAssetAdminController@store');
        Route::get('real-estate-asset/edit/:num', '\Http\Web\Admin\RealEstateAssetAdminController@edit');
        Route::get('real-estate-asset/duplicate/:num', '\Http\Web\Admin\RealEstateAssetAdminController@edit');
        Route::post('real-estate-asset/update', '\Http\Web\Admin\RealEstateAssetAdminController@update');
        Route::get('real-estate-asset/delete/:num', '\Http\Web\Admin\RealEstateAssetAdminController@delete');
        Route::get('real-estate-asset/deletes/', '\Http\Web\Admin\RealEstateAssetAdminController@deletes');
        Route::get('real-estate-asset/create-box', '\Http\Web\Admin\RealEstateAssetAdminController@create');

        // Pagine gestione Canoni di locazione
        Route::get('canon', '\Http\Web\Admin\CanonAdminController@index');
        Route::get('canon/list', '\Http\Web\Admin\CanonAdminController@asyncPaginateDatatable');
        Route::get('canon/create', '\Http\Web\Admin\CanonAdminController@create');
        Route::post('canon/store', '\Http\Web\Admin\CanonAdminController@store');
        Route::get('canon/edit/:num', '\Http\Web\Admin\CanonAdminController@edit');
        Route::get('canon/duplicate/:num', '\Http\Web\Admin\CanonAdminController@edit');
        Route::post('canon/update', '\Http\Web\Admin\CanonAdminController@update');
        Route::get('canon/delete/:num', '\Http\Web\Admin\CanonAdminController@delete');
        Route::get('canon/deletes/', '\Http\Web\Admin\CanonAdminController@deletes');

        // Pagine gestione Controlli e rilievi
        Route::get('relief-check', '\Http\Web\Admin\ReliefCheckAdminController@index');
        Route::get('relief-check/list', '\Http\Web\Admin\ReliefCheckAdminController@asyncPaginateDatatable');
        Route::get('relief-check/create', '\Http\Web\Admin\ReliefCheckAdminController@create');
        Route::post('relief-check/store', '\Http\Web\Admin\ReliefCheckAdminController@store');
        Route::get('relief-check/edit/:num', '\Http\Web\Admin\ReliefCheckAdminController@edit');
        Route::get('relief-check/duplicate/:num', '\Http\Web\Admin\ReliefCheckAdminController@edit');
        Route::post('relief-check/update', '\Http\Web\Admin\ReliefCheckAdminController@update');
        Route::get('relief-check/delete/:num', '\Http\Web\Admin\ReliefCheckAdminController@delete');
        Route::get('relief-check/deletes/', '\Http\Web\Admin\ReliefCheckAdminController@deletes');

        // Pagine gestione Regolamenti e documentazione
        Route::get('regulation', '\Http\Web\Admin\RegulationAdminController@index');
        Route::get('regulation/list', '\Http\Web\Admin\RegulationAdminController@asyncPaginateDatatable');
        Route::get('regulation/create', '\Http\Web\Admin\RegulationAdminController@create');
        Route::post('regulation/store', '\Http\Web\Admin\RegulationAdminController@store');
        Route::get('regulation/edit/:num', '\Http\Web\Admin\RegulationAdminController@edit');
        Route::get('regulation/duplicate/:num', '\Http\Web\Admin\RegulationAdminController@edit');
        Route::post('regulation/update', '\Http\Web\Admin\RegulationAdminController@update');
        Route::get('regulation/delete/:num', '\Http\Web\Admin\RegulationAdminController@delete');
        Route::get('regulation/deletes/', '\Http\Web\Admin\RegulationAdminController@deletes');
        Route::get('regulation/create-box', '\Http\Web\Admin\RegulationAdminController@create');
        Route::get('regulation/edit-box/:num', '\Http\Web\Admin\RegulationAdminController@edit');

        // Pagine gestione Modulistica
        Route::get('module', '\Http\Web\Admin\ModuleAdminController@index');
        Route::get('module/list', '\Http\Web\Admin\ModuleAdminController@asyncPaginateDatatable');
        Route::get('module/create', '\Http\Web\Admin\ModuleAdminController@create');
        Route::get('module/create-box', '\Http\Web\Admin\ModuleAdminController@create');
        Route::post('module/store', '\Http\Web\Admin\ModuleAdminController@store');
        Route::get('module/edit/:num', '\Http\Web\Admin\ModuleAdminController@edit');
        Route::get('module/duplicate/:num', '\Http\Web\Admin\ModuleAdminController@edit');
        Route::post('module/update', '\Http\Web\Admin\ModuleAdminController@update');
        Route::get('module/delete/:num', '\Http\Web\Admin\ModuleAdminController@delete');
        Route::get('module/deletes/', '\Http\Web\Admin\ModuleAdminController@deletes');

        // Pagine gestione Normativa
        Route::get('normative', '\Http\Web\Admin\NormativeAdminController@index');
        Route::get('normative/list', '\Http\Web\Admin\NormativeAdminController@asyncPaginateDatatable');
        Route::get('normative/create', '\Http\Web\Admin\NormativeAdminController@create');
        Route::post('normative/store', '\Http\Web\Admin\NormativeAdminController@store');
        Route::get('normative/edit/:num', '\Http\Web\Admin\NormativeAdminController@edit');
        Route::get('normative/duplicate/:num', '\Http\Web\Admin\NormativeAdminController@edit');
        Route::post('normative/update', '\Http\Web\Admin\NormativeAdminController@update');
        Route::get('normative/delete/:num', '\Http\Web\Admin\NormativeAdminController@delete');
        Route::get('normative/deletes/', '\Http\Web\Admin\NormativeAdminController@deletes');
        Route::get('normative/create-box', '\Http\Web\Admin\NormativeAdminController@create');
        Route::get('normative/edit-box/:num', '\Http\Web\Admin\NormativeAdminController@edit');

        // Pagine gestione Bilancio
        Route::get('balance', '\Http\Web\Admin\BalanceAdminController@index');
        Route::get('balance/list', '\Http\Web\Admin\BalanceAdminController@asyncPaginateDatatable');
        Route::get('balance/create', '\Http\Web\Admin\BalanceAdminController@create');
        Route::get('balance/create-box', '\Http\Web\Admin\BalanceAdminController@create');
        Route::post('balance/store', '\Http\Web\Admin\BalanceAdminController@store');
        Route::get('balance/edit/:num', '\Http\Web\Admin\BalanceAdminController@edit');
        Route::get('balance/duplicate/:num', '\Http\Web\Admin\BalanceAdminController@edit');
        Route::post('balance/update', '\Http\Web\Admin\BalanceAdminController@update');
        Route::get('balance/delete/:num', '\Http\Web\Admin\BalanceAdminController@delete');
        Route::get('balance/deletes/', '\Http\Web\Admin\BalanceAdminController@deletes');

        // Pagine gestione Elenco partecipanti/aggiudicatari
        Route::get('supplier', '\Http\Web\Admin\SupplierAdminController@index');
        Route::get('supplier/list', '\Http\Web\Admin\SupplierAdminController@asyncPaginateDatatable');
        Route::get('supplier/create', '\Http\Web\Admin\SupplierAdminController@create');
        Route::post('supplier/store', '\Http\Web\Admin\SupplierAdminController@store');
        Route::get('supplier/edit/:num', '\Http\Web\Admin\SupplierAdminController@edit');
        Route::get('supplier/duplicate/:num', '\Http\Web\Admin\SupplierAdminController@edit');
        Route::post('supplier/update', '\Http\Web\Admin\SupplierAdminController@update');
        Route::get('supplier/delete/:num', '\Http\Web\Admin\SupplierAdminController@delete');
        Route::get('supplier/deletes/', '\Http\Web\Admin\SupplierAdminController@deletes');
        Route::get('supplier/create-box', '\Http\Web\Admin\SupplierAdminController@create');
        Route::get('supplier/edit-box/:num', '\Http\Web\Admin\SupplierAdminController@edit');

        // Pagine gestione Bandi Gare e Contratti
        Route::get('contests-act', '\Http\Web\Admin\ContestsActAdminController@index');
        Route::get('contests-act/list', '\Http\Web\Admin\ContestsActAdminController@asyncPaginateDatatable');
        Route::get('contests-act/create-deliberation', '\Http\Web\Admin\ContestsActAdminController@createDeliberation');
        Route::post('contests-act/store-deliberation', '\Http\Web\Admin\ContestsActAdminController@storeDeliberation');
        Route::get('contests-act/edit-deliberation/:num', '\Http\Web\Admin\ContestsActAdminController@editDeliberation');
        Route::get('contests-deliberation/edit-deliberation/:num', '\Http\Web\Admin\ContestsActAdminController@editDeliberation');
        Route::get('contests-act/duplicate-deliberation/:num', '\Http\Web\Admin\ContestsActAdminController@editDeliberation');
        Route::post('contests-act/update-deliberation', '\Http\Web\Admin\ContestsActAdminController@updateDeliberation');
        Route::get('contests-act/delete-deliberation/:num', '\Http\Web\Admin\ContestsActAdminController@deleteDeliberation');
        Route::get('contests-act/create-notice', '\Http\Web\Admin\ContestsActAdminController@createNotice');
        Route::get('contests-act/create-box', '\Http\Web\Admin\ContestsActAdminController@createNotice');
        Route::get('contests-foster/create-box', '\Http\Web\Admin\ContestsActAdminController@createFoster');
        Route::get('contests-deliberation/create-box', '\Http\Web\Admin\ContestsActAdminController@createDeliberation');
        Route::get('contests-alert/create-box', '\Http\Web\Admin\ContestsActAdminController@createAlert');
        Route::get('contests-result/create-box', '\Http\Web\Admin\ContestsActAdminController@createResult');
        Route::post('contests-act/store-notice', '\Http\Web\Admin\ContestsActAdminController@storeNotice');
        Route::get('contests-act/edit-notice/:num', '\Http\Web\Admin\ContestsActAdminController@editNotice');
        Route::get('contests-act/duplicate-notice/:num', '\Http\Web\Admin\ContestsActAdminController@editNotice');
        Route::post('contests-act/update-notice', '\Http\Web\Admin\ContestsActAdminController@updateNotice');
        Route::get('contests-act/delete-notice/:num', '\Http\Web\Admin\ContestsActAdminController@deleteNotice');
        Route::get('contests-act/create-lot', '\Http\Web\Admin\ContestsActAdminController@createLot');
        Route::post('contests-act/store-lot', '\Http\Web\Admin\ContestsActAdminController@storeLot');
        Route::get('contests-act/edit-lot/:num', '\Http\Web\Admin\ContestsActAdminController@editLot');
        Route::get('contests-act/duplicate-lot/:num', '\Http\Web\Admin\ContestsActAdminController@editLot');
        Route::post('contests-act/update-lot', '\Http\Web\Admin\ContestsActAdminController@updateLot');
        Route::get('contests-act/delete-lot/:num', '\Http\Web\Admin\ContestsActAdminController@deleteLot');
        Route::get('contests-act/create-result', '\Http\Web\Admin\ContestsActAdminController@createResult');
        Route::post('contests-act/store-result', '\Http\Web\Admin\ContestsActAdminController@storeResult');
        Route::get('contests-act/edit-result/:num', '\Http\Web\Admin\ContestsActAdminController@editResult');
        Route::get('contests-result/edit-result/:num', '\Http\Web\Admin\ContestsActAdminController@editResult');
        Route::get('contests-act/duplicate-result/:num', '\Http\Web\Admin\ContestsActAdminController@editResult');
        Route::post('contests-act/update-result', '\Http\Web\Admin\ContestsActAdminController@updateResult');
        Route::get('contests-act/delete-result/:num', '\Http\Web\Admin\ContestsActAdminController@deleteResult');
        Route::get('contests-act/create-alert', '\Http\Web\Admin\ContestsActAdminController@createAlert');
        Route::post('contests-act/store-alert', '\Http\Web\Admin\ContestsActAdminController@storeAlert');
        Route::get('contests-act/edit-alert/:num', '\Http\Web\Admin\ContestsActAdminController@editAlert');
        Route::get('contests-alert/edit-alert/:num', '\Http\Web\Admin\ContestsActAdminController@editAlert');
        Route::get('contests-act/duplicate-alert/:num', '\Http\Web\Admin\ContestsActAdminController@editAlert');
        Route::post('contests-act/update-alert', '\Http\Web\Admin\ContestsActAdminController@updateAlert');
        Route::get('contests-act/delete-alert/:num', '\Http\Web\Admin\ContestsActAdminController@deleteAlert');
        Route::get('contests-act/create-foster', '\Http\Web\Admin\ContestsActAdminController@createFoster');
        Route::post('contests-act/store-foster', '\Http\Web\Admin\ContestsActAdminController@storeFoster');
        Route::get('contests-act/edit-foster/:num', '\Http\Web\Admin\ContestsActAdminController@editFoster');
        Route::get('contests-foster/edit-foster/:num', '\Http\Web\Admin\ContestsActAdminController@editFoster');
        Route::get('contests-act/duplicate-foster/:num', '\Http\Web\Admin\ContestsActAdminController@editFoster');
        Route::post('contests-act/update-foster', '\Http\Web\Admin\ContestsActAdminController@updateFoster');
        Route::get('contests-act/delete-foster/:num', '\Http\Web\Admin\ContestsActAdminController@deleteFoster');
        Route::get('contests-act/create-liquidation', '\Http\Web\Admin\ContestsActAdminController@createLiquidation');
        Route::post('contests-act/store-liquidation', '\Http\Web\Admin\ContestsActAdminController@storeLiquidation');
        Route::get('contests-act/edit-liquidation/:num', '\Http\Web\Admin\ContestsActAdminController@editLiquidation');
        Route::get('contests-act/duplicate-liquidation/:num', '\Http\Web\Admin\ContestsActAdminController@editLiquidation');
        Route::post('contests-act/update-liquidation', '\Http\Web\Admin\ContestsActAdminController@updateLiquidation');
        Route::get('contests-act/delete-liquidation/:num', '\Http\Web\Admin\ContestsActAdminController@deleteLiquidation');
        Route::get('contests-act/deletes/', '\Http\Web\Admin\ContestsActAdminController@deletes');

        //Pagine gestione Bandi di gara e affidamenti BDNCP dal 01/01/2024
        Route::get('bdncp-procedure', '\Http\Web\Admin\BdncpProcedureController@index');
        Route::get('bdncp-procedure/list', '\Http\Web\Admin\BdncpProcedureController@asyncPaginateDatatable');
        Route::get('bdncp-procedure/create', '\Http\Web\Admin\BdncpProcedureController@create');
        Route::get('bdncp-procedure/create-alert', '\Http\Web\Admin\BdncpProcedureController@createAlert');
        Route::get('bdncp-procedure/create-box', '\Http\Web\Admin\BdncpProcedureController@create');
        Route::post('bdncp-procedure/store', '\Http\Web\Admin\BdncpProcedureController@store');
        Route::post('bdncp-procedure/store-alert', '\Http\Web\Admin\BdncpProcedureController@storeAlert');
        Route::get('bdncp-procedure/edit/:num', '\Http\Web\Admin\BdncpProcedureController@edit');
        Route::get('bdncp-procedure/edit-alert/:num', '\Http\Web\Admin\BdncpProcedureController@editAlert');
        Route::post('bdncp-procedure/update', '\Http\Web\Admin\BdncpProcedureController@update');
        Route::post('bdncp-procedure/update-alert', '\Http\Web\Admin\BdncpProcedureController@updateAlert');
        Route::get('bdncp-procedure/duplicate/:num', '\Http\Web\Admin\BdncpProcedureController@edit');
        Route::get('bdncp-procedure/duplicate-alert/:num', '\Http\Web\Admin\BdncpProcedureController@editAlert');
        Route::get('bdncp-procedure/delete/:num', '\Http\Web\Admin\BdncpProcedureController@delete');
        Route::get('bdncp-procedure/deletes/', '\Http\Web\Admin\BdncpProcedureController@deletes');
        Route::get('bdncp-procedure/export-csv', '\Http\Web\Admin\BdncpProcedureController@exportCsv');

        //Pagine gestione Soluzioni tecnologiche
        Route::get('general-acts-documents', '\Http\Web\Admin\GeneralActsDocumentsController@index');
        Route::get('general-acts-documents/list', '\Http\Web\Admin\GeneralActsDocumentsController@asyncPaginateDatatable');
        Route::get('general-acts-documents/create', '\Http\Web\Admin\GeneralActsDocumentsController@create');
        Route::get('general-acts-documents/create-box', '\Http\Web\Admin\GeneralActsDocumentsController@create');
        Route::post('general-acts-documents/store', '\Http\Web\Admin\GeneralActsDocumentsController@store');
        Route::get('general-acts-documents/edit/:num', '\Http\Web\Admin\GeneralActsDocumentsController@edit');
        Route::get('general-acts-documents/duplicate/:num', '\Http\Web\Admin\GeneralActsDocumentsController@edit');
        Route::post('general-acts-documents/update', '\Http\Web\Admin\GeneralActsDocumentsController@update');
        Route::get('general-acts-documents/delete/:num', '\Http\Web\Admin\GeneralActsDocumentsController@delete');
        Route::get('general-acts-documents/deletes/', '\Http\Web\Admin\GeneralActsDocumentsController@deletes');
        Route::get('general-acts-documents/export-csv', '\Http\Web\Admin\GeneralActsDocumentsController@exportCsv');

        // Pagine gestione Atti delle amministrazioni
        Route::get('notices-act', '\Http\Web\Admin\NoticesActAdminController@index');
        Route::get('notices-act/list', '\Http\Web\Admin\NoticesActAdminController@asyncPaginateDatatable');
        Route::get('notices-act/create', '\Http\Web\Admin\NoticesActAdminController@create');
        Route::get('notice-act/create-box', '\Http\Web\Admin\NoticesActAdminController@create');
        Route::post('notices-act/store', '\Http\Web\Admin\NoticesActAdminController@store');
        Route::get('notices-act/edit/:num', '\Http\Web\Admin\NoticesActAdminController@edit');
        Route::get('notices-act/duplicate/:num', '\Http\Web\Admin\NoticesActAdminController@edit');
        Route::post('notices-act/update', '\Http\Web\Admin\NoticesActAdminController@update');
        Route::get('notices-act/delete/:num', '\Http\Web\Admin\NoticesActAdminController@delete');
        Route::get('notices-act/deletes/', '\Http\Web\Admin\NoticesActAdminController@deletes');

        // Pagine gestione Atti di programmazione
        Route::get('programming-act', '\Http\Web\Admin\ProgrammingActAdminController@index');
        Route::get('programming-act/list', '\Http\Web\Admin\ProgrammingActAdminController@asyncPaginateDatatable');
        Route::get('programming-act/create', '\Http\Web\Admin\ProgrammingActAdminController@create');
        Route::get('programming-act/create-box', '\Http\Web\Admin\ProgrammingActAdminController@create');
        Route::post('programming-act/store', '\Http\Web\Admin\ProgrammingActAdminController@store');
        Route::get('programming-act/edit/:num', '\Http\Web\Admin\ProgrammingActAdminController@edit');
        Route::get('programming-act/duplicate/:num', '\Http\Web\Admin\ProgrammingActAdminController@edit');
        Route::post('programming-act/update', '\Http\Web\Admin\ProgrammingActAdminController@update');
        Route::get('programming-act/delete/:num', '\Http\Web\Admin\ProgrammingActAdminController@delete');
        Route::get('programming-act/deletes/', '\Http\Web\Admin\ProgrammingActAdminController@deletes');

        // Pagine gestione Bandi di Concorso
        Route::get('contest', '\Http\Web\Admin\ContestAdminController@index');
        Route::get('contest/list', '\Http\Web\Admin\ContestAdminController@asyncPaginateDatatable');
        Route::get('contest/create-contest', '\Http\Web\Admin\ContestAdminController@createContest');
        Route::get('contest/create', '\Http\Web\Admin\ContestAdminController@createContest');
        Route::get('contest/create-alert', '\Http\Web\Admin\ContestAdminController@createAlert');
        Route::get('contest/create-result', '\Http\Web\Admin\ContestAdminController@createResult');
        Route::post('contest/store-contest', '\Http\Web\Admin\ContestAdminController@storeContest');
        Route::post('contest/store-alert', '\Http\Web\Admin\ContestAdminController@storeAlert');
        Route::post('contest/store-result', '\Http\Web\Admin\ContestAdminController@storeResult');
        Route::get('contest/edit-contest/:num', '\Http\Web\Admin\ContestAdminController@editContest');
        Route::get('contest/edit-alert/:num', '\Http\Web\Admin\ContestAdminController@editAlert');
        Route::get('contest/edit-result/:num', '\Http\Web\Admin\ContestAdminController@editResult');
        Route::get('contest/duplicate-contest/:num', '\Http\Web\Admin\ContestAdminController@editContest');
        Route::get('contest/duplicate-alert/:num', '\Http\Web\Admin\ContestAdminController@editAlert');
        Route::get('contest/duplicate-result/:num', '\Http\Web\Admin\ContestAdminController@editResult');
        Route::post('contest/update-contest', '\Http\Web\Admin\ContestAdminController@updateContest');
        Route::post('contest/update-alert', '\Http\Web\Admin\ContestAdminController@updateAlert');
        Route::post('contest/update-result', '\Http\Web\Admin\ContestAdminController@updateResult');
        Route::get('contest/delete/:num', '\Http\Web\Admin\ContestAdminController@delete');
        Route::get('contest/deletes/', '\Http\Web\Admin\ContestAdminController@deletes');
        Route::get('contest/create-box', '\Http\Web\Admin\ContestAdminController@createContest');
        Route::get('contest/edit-box/:num', '\Http\Web\Admin\ContestAdminController@edit');

        // Pagine gestione Sovvenzioni e vantaggi
        Route::get('grant', '\Http\Web\Admin\GrantAdminController@index');
        Route::get('grant/list', '\Http\Web\Admin\GrantAdminController@asyncPaginateDatatable');
        Route::get('grant/create-grant', '\Http\Web\Admin\GrantAdminController@createGrant');
        Route::get('grant/create-box', '\Http\Web\Admin\GrantAdminController@createGrant');
        Route::post('grant/store-grant', '\Http\Web\Admin\GrantAdminController@storeGrant');
        Route::get('grant/edit-grant/:num', '\Http\Web\Admin\GrantAdminController@editGrant');
        Route::get('grant/duplicate-grant/:num', '\Http\Web\Admin\GrantAdminController@editGrant');
        Route::post('grant/update-grant', '\Http\Web\Admin\GrantAdminController@updateGrant');
        Route::get('grant/delete-grant/:num', '\Http\Web\Admin\GrantAdminController@deleteGrant');
        Route::get('grant/create-liquidation', '\Http\Web\Admin\GrantAdminController@createLiquidation');
        Route::post('grant/store-liquidation', '\Http\Web\Admin\GrantAdminController@storeLiquidation');
        Route::get('grant/edit-liquidation/:num', '\Http\Web\Admin\GrantAdminController@editLiquidation');
        Route::get('grant/duplicate-liquidation/:num', '\Http\Web\Admin\GrantAdminController@editLiquidation');
        Route::post('grant/update-liquidation', '\Http\Web\Admin\GrantAdminController@updateLiquidation');
        Route::get('grant/delete-liquidation/:num', '\Http\Web\Admin\GrantAdminController@deleteLiquidation');
        Route::get('grant/deletes/', '\Http\Web\Admin\GrantAdminController@deletes');

        // Pagine gestione Incarichi e consulenze
        Route::get('assignment', '\Http\Web\Admin\AssignmentAdminController@index');
        Route::get('assignment/list', '\Http\Web\Admin\AssignmentAdminController@asyncPaginateDatatable');
        Route::get('assignment/create-assignment', '\Http\Web\Admin\AssignmentAdminController@createAssignment');
        Route::post('assignment/store-assignment', '\Http\Web\Admin\AssignmentAdminController@storeAssignment');
        Route::get('assignment/edit-assignment/:num', '\Http\Web\Admin\AssignmentAdminController@editAssignment');
        Route::get('assignment/duplicate-assignment/:num', '\Http\Web\Admin\AssignmentAdminController@editAssignment');
        Route::post('assignment/update-assignment', '\Http\Web\Admin\AssignmentAdminController@updateAssignment');
        Route::get('assignment/delete-assignment/:num', '\Http\Web\Admin\AssignmentAdminController@deleteAssignment');
        Route::get('assignment/create-liquidation', '\Http\Web\Admin\AssignmentAdminController@createLiquidation');
        Route::post('assignment/store-liquidation', '\Http\Web\Admin\AssignmentAdminController@storeLiquidation');
        Route::get('assignment/edit-liquidation/:num', '\Http\Web\Admin\AssignmentAdminController@editLiquidation');
        Route::get('assignment/duplicate-liquidation/:num', '\Http\Web\Admin\AssignmentAdminController@editLiquidation');
        Route::post('assignment/update-liquidation', '\Http\Web\Admin\AssignmentAdminController@updateLiquidation');
        Route::get('assignment/delete-liquidation/:num', '\Http\Web\Admin\AssignmentAdminController@deleteLiquidation');
        Route::get('assignment/deletes/', '\Http\Web\Admin\AssignmentAdminController@deletes');
        Route::get('assignment/create-box', '\Http\Web\Admin\AssignmentAdminController@createAssignment');
        Route::get('assignment/edit-box-assignment/:num', '\Http\Web\Admin\AssignmentAdminController@editAssignment');

        // Pagine gestione Provvedimenti Amministrativi
        Route::get('measure', '\Http\Web\Admin\MeasureAdminController@index');
        Route::get('measure/list', '\Http\Web\Admin\MeasureAdminController@asyncPaginateDatatable');
        Route::get('measure/create', '\Http\Web\Admin\MeasureAdminController@create');
        Route::post('measure/store', '\Http\Web\Admin\MeasureAdminController@store');
        Route::get('measure/edit/:num', '\Http\Web\Admin\MeasureAdminController@edit');
        Route::get('measure/duplicate/:num', '\Http\Web\Admin\MeasureAdminController@edit');
        Route::post('measure/update', '\Http\Web\Admin\MeasureAdminController@update');
        Route::get('measure/delete/:num', '\Http\Web\Admin\MeasureAdminController@delete');
        Route::get('measure/deletes/', '\Http\Web\Admin\MeasureAdminController@deletes');
        Route::get('measure/create-box', '\Http\Web\Admin\MeasureAdminController@create');
        Route::get('measure/edit-box/:num', '\Http\Web\Admin\MeasureAdminController@edit');

        // Pagine gestione Oneri informativi e obblighi
        Route::get('charge', '\Http\Web\Admin\ChargeAdminController@index');
        Route::get('charge/list', '\Http\Web\Admin\ChargeAdminController@asyncPaginateDatatable');
        Route::get('charge/create', '\Http\Web\Admin\ChargeAdminController@create');
        Route::get('charge/create-box', '\Http\Web\Admin\ChargeAdminController@create');
        Route::post('charge/store', '\Http\Web\Admin\ChargeAdminController@store');
        Route::get('charge/edit/:num', '\Http\Web\Admin\ChargeAdminController@edit');
        Route::get('charge/duplicate/:num', '\Http\Web\Admin\ChargeAdminController@edit');
        Route::post('charge/update', '\Http\Web\Admin\ChargeAdminController@update');
        Route::get('charge/delete/:num', '\Http\Web\Admin\ChargeAdminController@delete');
        Route::get('charge/deletes/', '\Http\Web\Admin\ChargeAdminController@deletes');

        // Pagine gestione Interventi straordinari e di emergenza
        Route::get('intervention', '\Http\Web\Admin\InterventionAdminController@index');
        Route::get('intervention/list', '\Http\Web\Admin\InterventionAdminController@asyncPaginateDatatable');
        Route::get('intervention/create', '\Http\Web\Admin\InterventionAdminController@create');
        Route::get('intervention/create-box', '\Http\Web\Admin\InterventionAdminController@create');
        Route::post('intervention/store', '\Http\Web\Admin\InterventionAdminController@store');
        Route::get('intervention/edit/:num', '\Http\Web\Admin\InterventionAdminController@edit');
        Route::get('intervention/duplicate/:num', '\Http\Web\Admin\InterventionAdminController@edit');
        Route::post('intervention/update', '\Http\Web\Admin\InterventionAdminController@update');
        Route::get('intervention/delete/:num', '\Http\Web\Admin\InterventionAdminController@delete');
        Route::get('intervention/deletes/', '\Http\Web\Admin\InterventionAdminController@deletes');

        // Pagine gestione Pagine Generiche
        Route::get('generic-page', '\Http\Web\Admin\GenericPageAdminController@index');
        Route::get('generic-page/list', '\Http\Web\Admin\GenericPageAdminController@asyncGetGenericsPage');
        Route::get('generic-page/get', '\Http\Web\Admin\GenericPageAdminController@getSection');

        // Sezioni
        Route::get('generic-page/recalls', '\Http\Web\Admin\GenericPageAdminController@asyncGetRecallsRecord');
        Route::get('generic-page/section/add/:num', '\Http\Web\Admin\GenericPageAdminController@addSection');
        Route::get('generic-page/section/delete', '\Http\Web\Admin\GenericPageAdminController@deleteSection');
        Route::get('generic-page/section/edit', '\Http\Web\Admin\GenericPageAdminController@editSection');
        Route::get('generic-page/section/duplicate', '\Http\Web\Admin\GenericPageAdminController@duplicateSectionGenerateTree');
        Route::get('generic-page/section/sorting', '\Http\Web\Admin\GenericPageAdminController@sortingSection');
        Route::post('generic-page/section/register', '\Http\Web\Admin\GenericPageAdminController@storeSection');

        // Paragrafi
        Route::get('generic-page/paragraph/add/:num', '\Http\Web\Admin\GenericPageAdminController@addParagraph');
        Route::post('generic-page/paragraph/store', '\Http\Web\Admin\GenericPageAdminController@storeParagraph');
        Route::post('generic-page/paragraph/update', '\Http\Web\Admin\GenericPageAdminController@updateParagraph');
        Route::get('generic-page/paragraph/delete', '\Http\Web\Admin\GenericPageAdminController@deleteParagraph');
        Route::get('generic-page/section/sort-paragraph', '\Http\Web\Admin\GenericPageAdminController@sortingParagraph');
        Route::get('generic-page/paragraph/duplicate/asyncRecalls', '\Http\Web\Admin\GenericPageAdminController@asyncGetParagraphRecalls');
        Route::get('generic-page/paragraph/edit/:num', '\Http\Web\Admin\GenericPageAdminController@editParagraph');
        Route::get('generic-page/paragraph/duplicate/:num', '\Http\Web\Admin\GenericPageAdminController@duplicateParagraph');

        // Pagine gestione Archivio file
        Route::get('file-archive', '\Http\Web\Admin\FileArchiveAdminController@index');

        // Pagine gestione Archivio file - indicizzazione
        Route::get('file-archive-indexing', '\Http\Web\Admin\FileArchiveIndexingAdminController@index');

        // Pagine gestione News ed avvisi
        Route::get('news-notice', '\Http\Web\Admin\NewsNoticeAdminController@index');
        Route::get('news-notice/list', '\Http\Web\Admin\NewsNoticeAdminController@asyncPaginateDatatable');
        Route::get('news-notice/create', '\Http\Web\Admin\NewsNoticeAdminController@create');
        Route::post('news-notice/store', '\Http\Web\Admin\NewsNoticeAdminController@store');
        Route::get('news-notice/edit/:num', '\Http\Web\Admin\NewsNoticeAdminController@edit');
        Route::get('news-notice/duplicate/:num', '\Http\Web\Admin\NewsNoticeAdminController@edit');
        Route::post('news-notice/update', '\Http\Web\Admin\NewsNoticeAdminController@update');
        Route::get('news-notice/delete/:num', '\Http\Web\Admin\NewsNoticeAdminController@delete');
        Route::get('news-notice/deletes/', '\Http\Web\Admin\NewsNoticeAdminController@deletes');

        // Pagine gestione Log delle attività
        Route::get('activity-log', '\Http\Web\Admin\ActivityLogAdminController@index');
        Route::get('activity-log/list', '\Http\Web\Admin\ActivityLogAdminController@asyncPaginateDatatable');

        //Pagine gestione log utenti
        Route::get('user-log', '\Http\Web\Admin\UserLogAdminController@index');
        Route::get('user-log/list', '\Http\Web\Admin\UserLogAdminController@asyncPaginateDatatable');

        //Report pubblicazioni
        Route::get('report-publication-recipients', '\Http\Web\Admin\ReportPublicationRecipientsController@index');
        Route::get('report-publication-recipients/list', '\Http\Web\Admin\ReportPublicationRecipientsController@asyncPaginateDatatable');
        Route::get('report-publication-recipients/create', '\Http\Web\Admin\ReportPublicationRecipientsController@create');
        Route::post('report-publication-recipients/store', '\Http\Web\Admin\ReportPublicationRecipientsController@store');
        Route::get('report-publication-recipients/edit/:num', '\Http\Web\Admin\ReportPublicationRecipientsController@edit');
        Route::get('report-publication-recipients/duplicate/:num', '\Http\Web\Admin\ReportPublicationRecipientsController@edit');
        Route::post('report-publication-recipients/update', '\Http\Web\Admin\ReportPublicationRecipientsController@update');
        Route::get('report-publication-recipients/delete/:num', '\Http\Web\Admin\ReportPublicationRecipientsController@delete');
        Route::get('report-publication-recipients/deletes/', '\Http\Web\Admin\ReportPublicationRecipientsController@deletes');
        Route::get('report-publication-recipients/report/', '\Http\Web\Admin\ReportPublicationRecipientsController@generateReport');

        // Pagine di sistema..
        Route::get('system/list/institutions', '\Http\Web\System\UtilityController@institutes');
        Route::get('system/change/institutions', '\Http\Web\System\UtilityController@changeInstitution');
        Route::get('system/restore/institution', '\Http\Web\System\UtilityController@restoreInstitution');
        Route::get('system/administrator/current', '\Http\Web\System\UtilityController@currentAdministration');

        // Per dati da mostrare nelle select2 nei forms
        Route::get('asyncSelectedData', '\Http\Web\Admin\AjaxDataForSelect@asyncGetSelectedData');
        Route::get('asyncData', '\Http\Web\Admin\AjaxDataForSelect@asyncGetData');
        Route::get('asyncGetDataContestsAct', '\Http\Web\Admin\AjaxDataForSelect@asyncGetDataType');

        // Per le richieste asincrone con dati articolati.
        Route::get('async/get/data', '\Http\Web\Admin\AjaxDataForSelect@asyncGetPaginationSelectedData');
        Route::get('get/config.js', function () {
            $data = [];
            $data['is_box'] = \System\Input::get('box') != null ? 'false' : 'true';
            header('Content-Type: application/javascript;');
            echo \System\View::create('config_js', $data)->display();
        });

        // Pagine del risultato di ricerca
        Route::get('search', '\Http\Web\Admin\SearchAdminController@index');
        Route::get('search/result/nums', '\Http\Web\Admin\SearchAdminController@resultSearchNums');
        Route::get('search/result/terms', '\Http\Web\Admin\SearchAdminController@resultSearchTerms');

    });
});

// Ckeditor config
Route::get('assets/common/ckeditor4/config.js', function () {
    $data = [];
    header('Content-Type: application/javascript;');
    echo \System\View::create('config_ckeditor', $data)->display();
});
