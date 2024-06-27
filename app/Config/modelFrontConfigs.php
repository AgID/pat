<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') or exit('No direct script access allowed');
/**
 * @package     Pat OS
 * @author      ISWEB S.p.A
 * @copyright   Copyright (c) 2021, ISWEB S.p.A
 * @since       Version 1.0
 * @filesource
 */

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
            'object_structures.structure_name',
            'object_structures.structure_of_belonging_id',
            'object_structures.created_at',
            'object_structures.updated_at'
        ],
        'fieldWhereHas' => [
            'structure_of_belonging' => [
                'field' => ['structure_name'],
                'as' => 'os'
            ]
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
            'object_personnel.created_at',
            'object_personnel.updated_at',
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
            'object_absence_rates.created_at',
            'object_absence_rates.updated_at',
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
            'object_commissions.created_at',
            'object_commissions.updated_at',
        ],
        'with' => [
            [
                'relation' => 'president',
                'select' => [
                    'id',
                    'full_name',
                ]
            ]
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
            'object_company.created_at',
            'object_company.updated_at',
            'object_company.id',
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
            'object_proceedings.created_at',
            'object_proceedings.updated_at',
        ],
        'with' => [
            [
                'relation' => 'offices_responsibles',
                'join' => [
                    'object_structures',
                    'object_structures.id',
                    '=',
                    'object_structures_id'
                ]
            ]
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
            'object_real_estate_asset.updated_at',
            'object_real_estate_asset.created_at',
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
            'object_relief_checks.updated_at',
            'object_relief_checks.created_at',
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
            'object_regulations.updated_at',
            'object_regulations.created_at',
        ],
        'with' => [
            [
                'relation' => 'proceedings',
                'join' => [
                    'object_proceedings',
                    'object_proceedings.id',
                    '=',
                    'object_proceedings_id'
                ]
            ]
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
            'object_modules_regulations.updated_at',
            'object_modules_regulations.created_at',
        ],
        'with' => [
            [
                'relation' => 'proceedings',
                'join' => [
                    'object_proceedings',
                    'object_proceedings.id',
                    '=',
                    'object_proceedings_id'
                ]
            ]
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
            'object_normatives.updated_at',
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
            'object_balance_sheets.updated_at',
            'object_balance_sheets.created_at',
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
        'fieldWhereHas' => [
            'structure' => [
                'table' => 'object_structures',
                'field' => ['structure_name']
            ],
            'awardees' => [
                'table' => 'object_supplie_list',
                'field' => ['name']
            ],
            'multi_lots' => [
                'field' => ['cig']
            ]
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
            'object_contests_acts.created_at',
            'object_contests_acts.updated_at',
        ],
        'groupBy' => [
            [
                'object_contests_acts.id',
                'object_contests_acts.typology'
            ]
        ],
        'scope' => [
            'activationDate',
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
            'object_notices_acts.created_at',
            'object_notices_acts.updated_at',
            'object_notices_acts.object_contests_acts_id',
        ],
        'fieldWhereHas' => [
            'relative_contest_act' => [
                'table' => 'object_contests_acts',
                'field' => ['object']
            ],
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
            'object_programming_acts.updated_at',
            'object_programming_acts.created_at'
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
            'object_contest.updated_at',
            'object_contest.created_at',
        ],
        'search_result_template' => 'v1/search/data/contests'
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per sovvenzioni
    '20' => [
        'model' => 'GrantsModel',
        'field' => [
            'object_grants.object',
//            'object_grants.beneficiary_name',
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
            'object_grants.updated_at',
            'object_grants.created_at',
        ],
        'fieldWhereHas' => [
            'structure' => [
                'table' => 'object_structures',
                'field' => ['structure_name']
            ],
            'relative_grant' => [
                'field' => [
                    'object',
                    'beneficiary_name'
                ],
                'whereAs' => [
                    [
                        'table' => 'structure',
                        'field' => 'structure_name'
                    ]
                ]
            ],
        ],
        'with' => [
            [
                'relation' => 'structure',
                'select' => [
                    'id',
                    'structure_name',
                ]
            ],
            [
                'relation' => 'relative_grant',
                'select' => [
                    'id',
                    'object',
                ]
            ]
        ],
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
            'object_assignments.created_at',
            'object_assignments.updated_at',
        ],
        'fieldWhereHas' => [
            'related_assignment' => [
                'field' => ['object', 'name']
            ],
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
            'object_measures.type',
            'object_measures.updated_at',
        ],
        'search_result_template' => 'v1/search/data/measures'
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per pagine generiche
    '23' => [
        'model' => 'SectionsFoModel',
        'field' => ['section_fo.name'],
        'search_field' => ['name'],
        'select_field' => ['section_fo.id'],
        'per_page' => 10,
        'search_result_field' => [
            'section_fo.id',
            'section_fo.name',
            'section_fo.updated_at',
        ],
        'fieldWhereHas' => [
            'contents' => [
                'field' => ['content', 'name']
            ],
        ],
        'where' => [
            [
                'section_fo.hide',
                '=',
                0
            ]
        ],
        'groupBy' => [
            [
                'section_fo.id',
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
            'object_interventions.time_limits',
            'object_interventions.effective_cost',
            'object_interventions.updated_at'
        ],
        'search_result_template' => 'v1/search/data/interventions'
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per BDNCP - Atti e Documenti di carattere generale
    '45' => [
        'model' => 'GeneralActsDocumentsModel',
        'table' => 'object_bdncp_general_acts_documents',
        'field' => ['object'],
        'search_field' => ['object', 'cup', 'notes', 'document_date', 'financial_sources', 'procedural_implementation_status'],
        'per_page' => 9,
        'search_result_field' => [
            'object_bdncp_general_acts_documents.*'
        ],
        'with' => [
            [
                'relation' => 'created_by',
                'select' => [
                    'id',
                    'name',
                    'deleted'
                ]
            ],
            [
                'relation' => 'public_in'
            ]
        ],

        'groupBy' => [
            [
                'object_bdncp_general_acts_documents.id'
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
        'search_field' => ['object', 'cig', 'bdncp_link'],
        'per_page' => 9,
        'search_result_field' => [
            'object_bdncp_procedure.*',
        ],
        'with' => [
            [
                'relation' => 'created_by',
                'select' => [
                    'id',
                    'name',
                    'deleted'
                ]
            ],
            [
                'relation' => 'commission:id,name,object',
            ],
            [
                'relation' => 'board:id,name,object',
            ]
        ],
        'fieldWhereHas' => [
            'commission' => [
                'table' => 'object_assignments',
                'field' => ['name', 'object']
            ],
            'board' => [
                'table' => 'object_assignments',
                'field' => ['name', 'object']
            ],
        ],
        'groupBy' => [
            [
                'object_bdncp_procedure.id'
            ]
        ],
        'search_result_template' => 'v1/search/data/bdncp_procedure'
    ],
];
