<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') or exit('No direct script access allowed');

return [
    'balance_sheets' => [
        'name' => 'Bilanci',
        'model' => '\\Model\\'.'BalanceSheetsModel'
    ],
    'absence_rates' => [
        'name' => 'Tassi di assenza',
        'model' => '\\Model\\'.'AbsenceRatesModel'
    ],
    'assignments' => [
        'name' => 'Incarichi e consulenze',
        'model' => '\\Model\\'.'AssignmentsModel'
    ],
    'lease_canons' => [
        'name' => 'Canoni di locazione',
        'model' => '\\Model\\'.'LeaseCanonsModel'
    ],
    'charges' => [
        'name' => 'Oneri informativi e obblighi',
        'model' => '\\Model\\'.'ChargesModel'
    ],
    'commissions' => [
        'name' => 'Commissioni e gruppi consiliari',
        'model' => '\\Model\\'.'CommissionsModel'
    ],
    'company' => [
        'name' => 'Enti e società controllate',
        'model' => '\\Model\\'.'CompanyModel'
    ],
    'contest' => [
        'name' => 'Bandi di Concorso',
        'model' => '\\Model\\'.'ContestModel'
    ],
    'contest_acts' => [
        'name' => 'Bandi Gare e Contratti',
        'model' => '\\Model\\'.'ContestsActsModel'
    ],
    'general_acts_documents' => [
        'name' => 'Atti e Documenti di carattere generale',
        'model' => '\\Model\\'.'GeneralActsDocumentsModel'
    ],
    'grants' => [
        'name' => 'Sovvenzioni e vantaggi',
        'model' => '\\Model\\'.'GrantsModel'
    ],
    'interventions' => [
        'name' => 'Interventi straordinari e di emergenza',
        'model' => '\\Model\\'.'InterventionsModel'
    ],
    'measures' => [
        'name' => 'Provvedimenti Amministrativi',
        'model' => '\\Model\\'.'MeasuresModel'
    ],
    'modules_regulations' => [
        'name' => 'Modulistica',
        'model' => '\\Model\\'.'ModulesRegulationsModel'
    ],
    'news_notices' => [
        'name' => 'News ed avvisi',
        'model' => '\\Model\\'.'NewsNoticesModel'
    ],
    'normatives' => [
        'name' => 'Normative',
        'model' => '\\Model\\'.'NormativesModel'
    ],
    'notices_acts' => [
        'name' => 'Atti delle amministrazioni',
        'model' => '\\Model\\'.'NoticesActsModel'
    ],
    'personnel' => [
        'name' => 'Personale',
        'model' => '\\Model\\'.'PersonnelModel'
    ],
    'proceedings' => [
        'name' => 'Procedimenti dell\'Ente',
        'model' => '\\Model\\'.'ProceedingsModel'
    ],
    'programming_acts' => [
        'name' => 'Atti di programmazione',
        'model' => '\\Model\\'.'ProgrammingActsModel'
    ],
    'real_estate_asset' => [
        'name' => 'Patrimonio immobiliare',
        'model' => '\\Model\\'.'RealEstateAssetModel'
    ],
    'regulations' => [
        'name' => 'Regolamenti e documentazione',
        'model' => '\\Model\\'.'RegulationsModel'
    ],
    'relief_checks' => [
        'name' => 'Controlli e rilievi',
        'model' => '\\Model\\'.'ReliefChecksModel'
    ],
    'structures' => [
        'name' => 'Strutture organizzative',
        'model' => '\\Model\\'.'StructuresModel'
    ],
    'supplie_list' => [
        'name' => 'Elenco partecipanti/aggiudicatari',
        'model' => '\\Model\\'.'SupplieListModel'
    ],
    'bdncp_procedure' => [
        'name' => 'Procedure Banca Dati Nazionale Contratti Pubblici',
        'model' => '\\Model\\'.'BdncpProcedureModel'
    ]
];
