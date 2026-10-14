<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

 use Model\InstitutionTypeModel;

defined('_FRAMEWORK_') or exit('No direct script access allowed');


return [

    // model ---> Modello su cui viene effettuata la query
    // field ---> Campi su cui viene effettuata la ricerca dal motore di ricerca del font-office (vedere SearchFrontController)
    // per_page ---> Paginazione per il motore di ricerca del front-office (vedere SearchFrontController@resultSearchTerms)
    // search_result_field ---> Campi inclusi nella select delle query del motore di ricerca del front-office (vedere SearchAdminController@resultSearchTerms)
    // search_result_template ---> template dei risultati di ricerca per le singole sezioni
    // fieldWhereHas ---> condizione nei campi di relazione con altre tabelle

    /**
     * Definisco l'array per la mappatura dei modelli e dei campi da mostrare nelle select2 all'interno dei form
     */

    // Permessi nelle ricerche
    'exclude_searchable_front' => [
        27, 39,
    ],

    //Per strutture organizzative
    '1' => [
        'model' => 'StructuresModel',
        'field' => [
            'object_structures.structure_name'
        ],
        'per_page' => 9,
        'search_result_field' => [
            'object_structures.id',
            'object_structures.structure_name'
        ],
        'where' => [
            [
                'object_structures.archived',
                '=',
                0
            ]
        ],
        'search_result_template' => 'v1/search/data/structures'
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per personale
    '2' => [
        'model' => 'PersonnelModel',
        'field' => [
            'full_name',
            'firstname',
            'lastname'
        ],
        'per_page' => 9,
        'search_result_field' => [
            'object_personnel.id',
            'object_personnel.full_name',
            'object_personnel.firstname',
            'object_personnel.lastname',
            'object_personnel.email',
            'object_personnel.mobile_phone',
            'object_personnel.not_available_email_txt',
            'object_personnel.role_id',
        ],
        'with' => [
            [
                'relation' => 'role',
                'select' => [
                    'id',
                    'name'
                ],
            ],
        ],
        'where' => [
            [
                'object_personnel.archived',
                '=',
                0
            ]
        ],
        'search_result_template' => 'v1/search/data/personnel'
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per tassi di assenza
    '3' => [
        'model' => 'AbsenceRatesModel',
        'field' => [
            'year',
        ],
        'per_page' => 9,
        'search_result_field' => [
            'object_absence_rates.id',
            'object_absence_rates.year',
            'object_absence_rates.object_structures_id',
            'object_absence_rates.month',
        ],
        'fieldWhereHas' => [
            'structure' => [
                'table' => 'object_structures',
                'field' => ['structure_name'],
                'as' => 'structure'
            ],
        ],
        'with' => [
            [
                'relation' => 'structure',
                'select' => [
                    'id',
                    'structure_name'
                ]
            ],
        ],
        'search_result_template' => 'v1/search/data/absence_rates'
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per commissioni
    '4' => [
        'model' => 'CommissionsModel',
        'field' => [
            'object_commissions.name',
            'typology',
        ],
        'per_page' => 9,
        'search_result_field' => [
            'object_commissions.id',
            'object_commissions.name',
            'object_commissions.typology',
            'object_commissions.president_id',
        ],
        'where' => [
            [
                'object_commissions.archived',
                '=',
                0
            ]
        ],
        'search_result_template' => 'v1/search/data/commission'
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per Enti e società controllate
    '5' => [
        'model' => 'CompanyModel',
        'field' => [
            'company_name',
            'typology'
        ],
        'per_page' => 9,
        'search_result_field' => [
            'object_company.id',
            'object_company.company_name',
            'object_company.typology',
        ],
        'where' => [
            [
                'object_company.archived',
                '=',
                0
            ]
        ],
        'search_result_template' => 'v1/search/data/company'
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per procedimenti
    '6' => [
        'model' => 'ProceedingsModel',
        'field' => [
            'object_proceedings.name',
            'object_proceedings.description'
        ],
        'per_page' => 9,
        'search_result_field' => [
            'object_proceedings.id',
            'object_proceedings.name',
        ],
        'where' => [
            [
                'object_proceedings.archived',
                '=',
                0
            ]
        ],
        'search_result_template' => 'v1/search/data/proceeding'
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per immobili
    '7' => [
        'model' => 'RealEstateAssetModel',
        'field' => [
            'object_real_estate_asset.name',
            'object_real_estate_asset.address'
        ],
        'per_page' => 9,
        'search_result_field' => [
            'object_real_estate_asset.name',
            'object_real_estate_asset.id',
        ],
        'where' => [
            [
                'object_real_estate_asset.archived',
                '=',
                0
            ]
        ],
        'search_result_template' => 'v1/search/data/real_estate_asset'
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per controlli e rilievi
    '9' => [
        'model' => 'ReliefChecksModel',
        'field' => ['object'],
        'per_page' => 9,
        'search_result_field' => [
            'object_relief_checks.id',
            'object_relief_checks.object',
        ],
        'search_result_template' => 'v1/search/data/relief_checks'
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per regolamenti
    '10' => [
        'model' => 'RegulationsModel',
        'field' => ['title'],
        'per_page' => 9,
        'search_result_field' => [
            'object_regulations.id',
            'object_regulations.title',
        ],

        'search_result_template' => 'v1/search/data/regulations'
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per la modulistica
    '11' => [
        'model' => 'ModulesRegulationsModel',
        'field' => [
            'title'
        ],
        'per_page' => 9,
        'search_result_field' => [
            'object_modules_regulations.id',
            'object_modules_regulations.title',
            'object_modules_regulations.description',
        ],
        'search_result_template' => 'v1/search/data/modules'
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per normative
    '12' => [
        'model' => 'NormativesModel',
        'field' => [
            'object_normatives.name',
            'object_normatives.number',
        ],
        'date_field' => [
            'object_normatives.issue_date',
        ],
        'per_page' => 9,
        'search_result_field' => [
            'object_normatives.id',
            'object_normatives.name',
            'object_normatives.description',
        ],
        'search_result_template' => 'v1/search/data/normatives'
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per bilanci
    '13' => [
        'model' => 'BalanceSheetsModel',
        'field' => [
            'object_balance_sheets.name',
            'typology',
            'year'
        ],
        'per_page' => 9,
        'search_result_field' => [
            'object_balance_sheets.id',
            'object_balance_sheets.typology',
            'object_balance_sheets.name',
            'object_balance_sheets.year',
        ],
        'search_result_template' => 'v1/search/data/balance'
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per bandi di gara
    '15' => [
        'model' => 'ContestsActsModel',
        'field' => [
            'object_contests_acts.object',
            'object_contests_acts.type',
            'object_contests_acts.cig',
        ],
        'select_field' => [
            'object_contests_acts.id',
            'object_contests_acts.typology',
            'object_contests_acts.relative_procedure_id',
            'object_contests_acts.relative_notice_id',
        ],
        'per_page' => 9,
        'search_result_field' => [
            'object_contests_acts.id',
            'object_contests_acts.type',
            'object_contests_acts.object',
            'object_contests_acts.relative_procedure_id',
        ],
        'search_result_template' => 'v1/search/data/contest_acts'
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per atti delle amministrazioni
    '16' => [
        'model' => 'NoticesActsModel',
        'field' => [
            'object_notices_acts.object'
        ],
        'per_page' => 9,
        'search_result_field' => [
            'object_notices_acts.id',
            'object_notices_acts.object',
            'object_notices_acts.object_contests_acts_id',
        ],
        'with' => [
            [
                'relation' => 'relative_contest_act:id,object',
            ]
        ],
        'search_result_template' => 'v1/search/data/notice_acts'
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per atti di programmazione
    '18' => [
        'model' => 'ProgrammingActsModel',
        'field' => [
            'object',
            'description',
        ],
        'per_page' => 9,
        'search_result_field' => [
            'object_programming_acts.id',
            'object_programming_acts.object',
            'object_programming_acts.description',
        ],
        'search_result_template' => 'v1/search/data/programming_acts'
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per bandi di concorso
    '19' => [
        'model' => 'ContestModel',
        'field' => [
            'object',
            'typology'
        ],
        'per_page' => 9,
        'search_result_field' => [
            'object_contest.id',
            'object_contest.object',
            'object_contest.typology',
        ],
        'search_result_template' => 'v1/search/data/contests'
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per sovvenzioni
    '20' => [
        'model' => 'GrantsModel',
        'field' => [
            'object_grants.object',
            'object_grants.type',
        ],
        'per_page' => 9,
        'search_result_field' => [
            'object_grants.id',
            'object_grants.object',
            'object_grants.typology',
            'object_grants.type',
            'object_grants.object_structures_id',
            'object_grants.grant_id',
        ],
        'fieldWhereHas' => [
            'structure' => [
                'table' => 'object_structures',
                'field' => ['structure_name']
            ],
        ],
        'with' => [
            [
                'relation' => 'relative_grant',
                'select' => [
                    'id',
                    'object',
                ]
            ]
        ],
        'modelScope' => ['privacyFilter'],
        'search_result_template' => 'v1/search/data/grants'
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per incarichi
    '21' => [
        'model' => 'AssignmentsModel',
        'field' => [
            'object_assignments.object',
            'object_assignments.name',
            'object_assignments.type',
        ],
        'per_page' => 9,
        'search_result_field' => [
            'object_assignments.id',
            'object_assignments.name',
            'object_assignments.object',
            'object_assignments.type',
            'object_assignments.related_assignment_id',
        ],
        'with' => [
            [
                'relation' => 'related_assignment',
                'select' => [
                    'id',
                    'name',
                    'object'
                ]
            ],
        ],
        'search_result_template' => 'v1/search/data/assignments'
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per provvedimenti
    '22' => [
        'model' => 'MeasuresModel',
        'field' => [
            'object',
            'number'
        ],
        'per_page' => 9,
        'search_result_field' => [
            'object_measures.id',
            'object_measures.object',
            'object_measures.type'
        ],
        'with' => [],
        'search_result_template' => 'v1/search/data/measures'
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per pagine generiche
    '23' => [
        'model' => 'SectionsFoModel',
        'field' => ['section_fo.name'],
        'search_field' => ['name'],
        'per_page' => 10,
        'search_result_field' => [
            'section_fo.id',
            'section_fo.name',
        ],
        'with' => [
            [
                'relation' => 'labels',
                'select' => [
                    'id',
                    'label',
                    'sections_id'
                ]
            ]
        ],
        'fieldWhereHas' => [
            'contents' => [
                'field' => ['content', 'name']
            ],
            'labels' => [
                'field' => ['label']
            ]
        ],
        'where' => [
            [
                'section_fo.hide',
                '=',
                0
            ]
        ],
        'search_result_template' => 'v1/search/data/page',
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per interventi
    '25' => [
        'model' => 'InterventionsModel',
        'field' => ['object_interventions.name'],
        'per_page' => 9,
        'search_result_field' => [
            'object_interventions.id',
            'object_interventions.name',
        ],
        'search_result_template' => 'v1/search/data/interventions'
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per BDNCP - Atti e Documenti di carattere generale
    '45' => [
        'model' => 'GeneralActsDocumentsModel',
        'table' => 'object_bdncp_general_acts_documents',
        'field' => ['object'],
        'per_page' => 9,
        'search_result_field' => [
            'object_bdncp_general_acts_documents.*'
        ],
        'with' => [
            [
                'relation' => 'public_in'
            ]
        ],
        'search_result_template' => 'v1/search/data/general_acts_documents'
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per BDNCP - Procedure Banca Dati Nazionale Contratti Pubblici
    '46' => [
        'model' => 'BdncpProcedureModel',
        'table' => 'object_bdncp_procedure',
        'field' => ['object', 'cig'],
        'search_field' => ['object', 'cig'],
        'per_page' => 9,
        'search_result_field' => [
            'object_bdncp_procedure.id',
            'object_bdncp_procedure.cig',
            'object_bdncp_procedure.object',
        ],
        'search_result_template' => 'v1/search/data/bdncp_procedure'
    ],
];
