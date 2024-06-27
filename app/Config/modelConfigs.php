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

    /**
     * @var model ---> Modello su cui viene effettuata la query
     * @var field ---> Campi mostrati nelle selct2 (vedere AjaxDataForSelect)
     * @var search_field ---> Campi su cui viene effettuata la ricerca nelle select2 (AjaxDataForSelect@asyncGetData)
     * @var select2_field ---> Campi inclusi nella select per i dati delle select2 (AjaxDataForSelect@asyncGetData)
     * @var global_search_field ---> Campi su cui viene effettuata la ricerca nella ricerca globale (SearchAdminController),
     * cosi non viene effettuata la ricerca per l'utente creatore come invece accade nei datatable
     * @var select_field ---> Campi inclusi nella select delle query del motore di ricerca del back-office (vedere SearchAdminController@resultSearchNums)
     * @var per_page ---> Paginazione per il motore di ricerca del back-office (vedere SearchAdminController@resultSearchTerms)
     * @var search_result_field ---> Campi inclusi nella select delle query del motore di ricerca del back-office (vedere SearchAdminController@resultSearchTerms)
     * @var search_result_template ---> template dei risultati di ricerca per le singole sezioni
     */

    /**
     * Definisco l'array per la mappatura dei modelli e dei campi da mostrare nelle select2 all'interno dei form
     */

    // Permessi nelle ricerche
    'exclude_searchable_front' => [
        27, 39,
    ],

    //Per strutture organizzative
    '1' => [
        'model' => '\\Model\\StructuresModel',
        'table' => 'object_structures',
        'field' => ['object_structures.structure_name'],
        'search_field' => ['object_structures.structure_name', 'os.structure_name'],
        'global_search_field' => ['object_structures.structure_name','object_structures.id'],
        'select_field' => ['object_structures.id'],
        'per_page' => 10,
        'search_result_field' => ['object_structures.id', 'object_structures.structure_name', 'object_structures.updated_at', 'object_structures.archived',
            'object_structures.publishing_status', 'object_structures.institution_id', 'object_structures.owner_id',
            'object_structures.structure_of_belonging_id', 'object_structures.articulation'],
        'select2_field' => ['object_structures.id', 'object_structures.structure_name', 'object_structures.archived',
            'object_structures.publishing_status', 'object_structures.structure_of_belonging_id', 'object_structures.reference_email', 'os.id as parent_id',
            'os.structure_name as parent_structure'],
        'with' => [
            [
                'relation' => 'structure_of_belonging:id,structure_of_belonging_id,structure_name'
            ],
            [
                'relation' => 'responsibles:id,full_name',
            ],
            [
                'relation' => 'created_by',
                'select' => [
                    'id',
                    'name'
                ]
            ]
        ],
        'joinSelect2' => [
            [
                'object_structures as os',
                'os.id',
                '=',
                'object_structures.structure_of_belonging_id',
                'left outer'
            ],
        ],
        'search_result_template' => 'search/data/organizational_structure',
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per personale
    '2' => [
        'model' => '\\Model\\PersonnelModel',
        'table' => 'object_personnel',
        'field' => ['full_name'],
        'search_field' => ['full_name', 'name'],
        'global_search_field' => ['full_name','firstname','lastname','political_role'],
        'select_field' => ['object_personnel.id'],
        'per_page' => 10,
        'search_result_field' => ['object_personnel.id', 'full_name', 'firstname', 'lastname', 'object_personnel.updated_at', 'object_personnel.institution_id',
            'role_id', 'object_personnel.owner_id', 'archived', 'object_personnel.phone', 'object_personnel.mobile_phone', 'object_personnel.email',
            'certified_email', 'fax' ,'publishing_status'],
        'select2_field' => ['object_personnel.id', 'full_name', 'title', 'role_id', 'archived', 'object_personnel.email', 'object_personnel.publishing_status',
            'r.id as role_id', 'r.name as role_name'],
        'with' => [
            [
                'relation' => 'role:id,name'
            ],
            [
                'relation' => 'referent_structures:id,structure_name',
            ],
            [
                'relation' => 'created_by',
                'select' => [
                    'id',
                    'name',
                    'deleted'
                ]
            ],
            [
                'relation' => 'institution:id,full_name_institution'
            ]
        ],
        'joinSelect2' => [
            [
                'role as r',
                'r.id',
                '=',
                'object_personnel.role_id',
                'left outer'
            ]
        ],
        'search_result_template' => 'search/data/personnel',
    ],

    //------------------------------------------------------------------------------------------------------------------

    // Per tassi di assenza
    '3' => [
        'model' => '\\Model\\AbsenceRatesModel',
        'field' => [],
        'search_field' => [],
        'select_field' => ['object_absence_rates.id'],
        'global_search_field' => ['year'],
        'per_page' => 10,
        'search_result_field' => ['object_absence_rates.id', 'object_absence_rates.updated_at', 'object_absence_rates.owner_id', 'object_absence_rates.institution_id', 'object_structures_id',
            'publishing_status', 'year', 'month', 'publishing_status'],
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
                'relation' => 'structure:id,structure_name',
            ],
        ],
        'search_result_template' => 'search/data/absence_rates',
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per commissioni
    '4' => [
        'model' => '\\Model\\CommissionsModel',
        'field' => ['name'],
        'search_field' => ['name'],
        'global_search_field' => ['object_commissions.name', 'typology'],
        'select_field' => ['object_commissions.id'],
        'per_page' => 10,
        'search_result_field' => ['object_commissions.id', 'object_commissions.updated_at', 'object_commissions.owner_id', 'object_commissions.institution_id',
            'object_commissions.name', 'typology', 'president_id', 'object_commissions.archived', 'object_commissions.publishing_status'],
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
                'relation' => 'president:id,full_name',
            ]
        ],
        'search_result_template' => 'search/data/commission',
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per Enti e società controllate
    '5' => [
        'model' => '\\Model\\CompanyModel',
        'field' => ['company_name'],
        'search_field' => ['company_name'],
        'global_search_field' => ['company_name', 'typology'],
        'select_field' => ['object_company.id'],
        'per_page' => 10,
        'search_result_field' => ['object_company.id', 'object_company.updated_at', 'object_company.owner_id', 'object_company.institution_id', 'company_name', 'website_url', 'archived',
            'publishing_status', 'typology'],
        'with' => [
            [
                'relation' => 'created_by',
                'select' => [
                    'id',
                    'name',
                    'deleted'
                ]
            ]
        ],
        'search_result_template' => 'search/data/company',
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per procedimenti
    '6' => [
        'model' => '\\Model\\ProceedingsModel',
        'field' => ['name'],
        'search_field' => ['name'],
        'global_search_field' => ['object_proceedings.name'],
        'select_field' => ['object_proceedings.id'],
        'per_page' => 10,
        'search_result_field' => ['object_proceedings.id', 'object_proceedings.institution_id', 'object_proceedings.owner_id', 'object_proceedings.name',
            'object_proceedings.updated_at', 'archived', 'publishing_status'],
        'with' => [
            [
                'relation' => 'responsibles:id,full_name',
            ],
            [
                'relation' => 'offices_responsibles:id,structure_name',
            ],
            [
                'relation' => 'created_by',
                'select' => [
                    'id',
                    'name',
                    'deleted'
                ]
            ]
        ],
        'search_result_template' => 'search/data/proceeding',
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per immobili
    '7' => [
        'model' => '\\Model\\RealEstateAssetModel',
        'field' => ['name'],
        'search_field' => ['name'],
        'global_search_field' => ['object_real_estate_asset.name', 'object_real_estate_asset.address',],
        'select_field' => ['object_real_estate_asset.id'],
        'per_page' => 10,
        'search_result_field' => ['object_real_estate_asset.id', 'object_real_estate_asset.updated_at', 'object_real_estate_asset.owner_id', 'object_real_estate_asset.institution_id',
            'object_real_estate_asset.name', 'address', 'publishing_status', 'archived'],
        'with' => [
            [
                'relation' => 'created_by',
                'select' => [
                    'id',
                    'name',
                    'deleted'
                ]
            ]
        ],
        'search_result_template' => 'search/data/real_estate_asset',
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per canoni
    '8' => [
        'model' => '\\Model\\LeaseCanonsModel',
        'field' => ['beneficiary'],
        'search_field' => ['beneficiary'],
        'global_search_field' => ['beneficiary', 'amount'],
        'select_field' => ['object_lease_canons.id'],
        'per_page' => 10,
        'search_result_field' => [
            'object_lease_canons.id', 'object_lease_canons.owner_id', 'object_lease_canons.institution_id',
            'canon_type', 'publishing_status', 'beneficiary', 'object_lease_canons.updated_at', 'amount'
        ],
        'with' => [
            [
                'relation' => 'created_by',
                'select' => [
                    'id',
                    'name',
                    'deleted'
                ]
            ]
        ],
        'search_result_template' => 'search/data/canon',
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per controlli e rilievi
    '9' => [
        'model' => '\\Model\\ReliefChecksModel',
        'field' => ['object'],
        'search_field' => ['object'],
        'global_search_field' => ['object'],
        'select_field' => ['object_relief_checks.id'],
        'per_page' => 10,
        'search_result_field' => ['object_relief_checks.id', 'object_relief_checks.owner_id', 'object_structures_id',
            'object', 'date', 'publishing_status', 'object_relief_checks.updated_at'],
        'with' => [
            [
                'relation' => 'created_by',
                'select' => [
                    'id',
                    'name',
                    'deleted'
                ]
            ]
        ],
        'search_result_template' => 'search/data/relief_check',
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per regolamenti
    '10' => [
        'model' => '\\Model\\RegulationsModel',
        'field' => ['title'],
        'search_field' => ['title'],
        'select_field' => ['object_regulations.id'],
        'per_page' => 10,
        'search_result_field' => ['object_regulations.id', 'object_regulations.institution_id', 'object_regulations.owner_id', 'title', 'publishing_status',
            'object_regulations.updated_at', 'section.id as sectionId', 'section.name'],
        'global_search_field' => ['object_regulations.title', 'section.name'],
        'with' => [
            [
                'relation' => 'structures:id,structure_name',
            ],
            [
                'relation' => 'proceedings:id,name',
            ],
            [
                'relation' => 'created_by',
                'select' => [
                    'id',
                    'name',
                    'deleted'
                ]
            ]
        ],
        'join' => [
            [
                'rel_regulations_public_in as public_in',
                'object_regulations.id',
                '=',
                'public_in.object_regulation_id'
            ],
            [
                'section_fo as section',
                'section.id',
                '=',
                'public_in.public_in_id'
            ]
        ],
        'search_result_template' => 'search/data/regulation',
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per la modulistica
    '11' => [
        'model' => '\\Model\\ModulesRegulationsModel',
        'field' => ['description'],
        'search_field' => ['description'],
        'global_search_field' => ['title', 'description'],
        'select_field' => ['object_modules_regulations.id'],
        'per_page' => 10,
        'search_result_field' => [
            'object_modules_regulations.id', 'title', 'object_modules_regulations.owner_id', 'object_modules_regulations.updated_at',
            'object_modules_regulations.institution_id', 'object_modules_regulations.publishing_status', 'typology' ],
        'with' => [
            [
                'relation' => 'proceedings:id,name',
            ],
            [
                'relation' => 'created_by',
                'select' => [
                    'id',
                    'name',
                    'deleted'
                ]
            ]
        ],
        'search_result_template' => 'search/data/modules',
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per normative
    '12' => [
        'model' => '\\Model\\NormativesModel',
        'field' => ['name'],
        'search_field' => ['name'],
        'global_search_field' => ['name'],
        'select_field' => ['object_normatives.id'],
        'per_page' => 10,
        'search_result_field' => [
            'object_normatives.id', 'object_normatives.owner_id', 'object_normatives.institution_id', 'object_normatives.name', 'normative_topic',
            'normative_link', 'publishing_status', 'object_normatives.updated_at'
        ],
        'with' => [
            [
                'relation' => 'structures:id,structure_name',
            ],
            [
                'relation' => 'created_by',
                'select' => [
                    'id',
                    'name',
                    'deleted'
                ]
            ]
        ],
        'search_result_template' => 'search/data/normative',
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per bilanci
    '13' => [
        'model' => '\\Model\\BalanceSheetsModel',
        'field' => ['name'],
        'search_field' => ['name'],
        'global_search_field' => ['name', 'typology', 'year',],
        'select_field' => ['object_balance_sheets.id'],
        'per_page' => 10,
        'search_result_field' => [
            'object_balance_sheets.id', 'object_balance_sheets.owner_id', 'object_balance_sheets.institution_id', 'object_balance_sheets.name',
            'typology', 'year', 'object_balance_sheets.updated_at', 'publishing_status'
        ],
        'with' => [
            [
                'relation' => 'created_by',
                'select' => [
                    'id',
                    'name',
                    'deleted'
                ]
            ]
        ],
        'search_result_template' => 'search/data/balance',
    ],

    //------------------------------------------------------------------------------------------------------------------

    // Per fornitori
    '14' => [
        'model' => '\\Model\\SupplieListModel',
        'table' => 'object_supplie_list',
        'field' => ['name', 'vat'],
        'search_field' => ['name', 'vat'],
        'global_search_field' => ['name', 'vat'],
        'select_field' => ['object_supplie_list.id'],
        'per_page' => 10,
        'search_result_field' => [
            'object_supplie_list.id', 'owner_id', 'object_supplie_list.institution_id', 'typology', 'type', 'object_supplie_list.vat', 'foreign_tax_identification',
            'object_supplie_list.name', 'object_supplie_list.updated_at', 'object_supplie_list.publishing_status'
        ],
        'with' => [
            [
                'relation' => 'created_by',
                'select' => [
                    'id',
                    'name',
                    'deleted'
                ]
            ]
        ],
        'search_result_template' => 'search/data/supplier',
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per bandi di gara
    '15' => [
        'model' => '\\Model\\ContestsActsModel',
        'table' => 'object_contests_acts',
        'field' => ['object'],
        'search_field' => ['object'],
        'global_search_field' => ['object', 'type', 'cig'],
        'per_page' => 10,
        'select_field' => ['object_contests_acts.id', 'object_contests_acts.typology'],
        'search_result_field' => [
            'object_contests_acts.*',
        ],
        'with' => [
            [
                'relation' => 'awardees:id,name',
            ],
            [
                'relation' => 'structure:id,structure_name'
            ],
            [
                'relation' => 'relative_lots:id,relative_notice_id,cig'
            ],
            [
                'relation' => 'created_by',
                'select' => [
                    'id',
                    'name',
                    'deleted'
                ]
            ]
        ],
        'join' => [
            [
                'object_structures as structure',
                'structure.id',
                '=',
                'object_contests_acts.object_structures_id',
                'left outer'
            ]
        ],
        'joinSelect2' => [
            [
                'object_contests_acts as lot',
                'lot.relative_notice_id',
                '=',
                'object_contests_acts.id',
                'left outer'
            ]
        ],
        'groupBy' => [
            [
                'object_contests_acts.id',
                'object_contests_acts.typology'
            ]
        ],
        'search_result_template' => 'search/data/contest_acts',
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per atti delle amministrazioni
    '16' => [
        'model' => '\\Model\\NoticesActsModel',
        'field' => ['object'],
        'search_field' => ['object'],
        'global_search_field' => ['object'],
        'select_field' => ['object_notices_acts.id'],
        'per_page' => 10,
        'search_result_field' => ['object_notices_acts.id', 'object_contests_acts_id', 'object_notices_acts.owner_id', 'object_notices_acts.institution_id',
            'object_notices_acts.object', 'object_notices_acts.date', 'object_notices_acts.updated_at'],
        'with' => [
            [
                'relation' => 'relative_contest_act:id,object'
            ],
            [
                'relation' => 'created_by',
                'select' => [
                    'id',
                    'name',
                    'deleted',
                ]
            ]
        ],
        'search_result_template' => 'search/data/notices_acts',
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per atti di programmazione
    '18' => [
        'model' => '\\Model\\ProgrammingActsModel',
        'field' => ['object'],
        'search_field' => ['object'],
        'global_search_field' => ['object'],
        'select_field' => ['object_programming_acts.id'],
        'per_page' => 10,
        'search_result_field' => ['object_programming_acts.id', 'object_programming_acts.owner_id', 'object_programming_acts.institution_id',
            'object', 'date', 'public_in_public_works', 'publishing_status', 'object_programming_acts.updated_at'],
        'with' => [
            [
                'relation' => 'created_by',
                'select' => [
                    'id',
                    'name',
                    'deleted',
                ]
            ]
        ],
        'search_result_template' => 'search/data/programming_acts',
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per bandi di concorso
    '19' => [
        'model' => '\\Model\\ContestModel',
        'field' => ['object'],
        'search_field' => ['object'],
        'global_search_field' => ['object', 'typology'],
        'select_field' => ['object_contest.id'],
        'per_page' => 10,
        'search_result_field' => ['object_contest.id', 'owner_id', 'object_contest.institution_id', 'typology', 'object', 'publishing_status',
            'object_contest.updated_at', 'object_contest.activation_date', 'object_contest.expiration_date'],
        'with' => [
            [
                'relation' => 'created_by',
                'select' => [
                    'id',
                    'name',
                    'deleted',
                ]
            ]
        ],
        'search_result_template' => 'search/data/contests',
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per sovvenzioni
    '20' => [
        'model' => '\\Model\\GrantsModel',
        'field' => ['object'],
        'search_field' => ['object'],
        'global_search_field' => ['object', 'typology', 'beneficiary_name'],
        'select_field' => ['object_grants.id'],
        'per_page' => 10,
        'search_result_field' => ['object_grants.id', 'object_grants.owner_id', 'object_grants.institution_id', 'object_grants.object_structures_id',
            'object_grants.grant_id', 'object_grants.beneficiary_name', 'object_grants.object', 'object_grants.typology',
            'object_grants.type', 'object_grants.publishing_status', 'object_grants.updated_at'],
        'with' => [
            [
                'relation' => 'personnel:id,full_name',
            ],
            [
                'relation' => 'structure:id,structure_name',
            ],
            [
                'relation' => 'created_by',
                'select' => [
                    'id',
                    'name',
                    'deleted',
                ]
            ]
        ],
        'search_result_template' => 'search/data/grants',
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per incarichi
    '21' => [
        'model' => '\\Model\\AssignmentsModel',
        'table' => 'object_assignments',
        'field' => ['name', 'object'],
        'search_field' => ['object_assignments.name', 'object_assignments.object'],
        'global_search_field' => ['object_assignments.name', 'object_assignments.object'],
        'select_field' => ['object_assignments.id'],
        'per_page' => 10,
        'search_result_field' => ['object_assignments.id', 'object_assignments.owner_id', 'object_assignments.institution_id',
            'object_assignments.object_structures_id', 'object_assignments.related_assignment_id', 'object_assignments.typology',
            'object_assignments.type', 'object_assignments.name', 'object_assignments.object', 'object_assignments.assignment_start',
            'object_assignments.publishing_status', 'object_assignments.updated_at', 'object_assignments.assignment_end'],
        'with' => [
            [
                'relation' => 'created_by',
                'select' => [
                    'id',
                    'name',
                    'deleted',
                ]
            ],
            [
                'relation' => 'related_assignment:id,name,object',
            ],
        ],
        'joinSelect2' => [
            [
                'object_assignments as related_assignment',
                'related_assignment.id',
                '=',
                'object_assignments.related_assignment_id',
                'left outer'
            ]
        ],
        'search_result_template' => 'search/data/assignments',
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per provvedimenti
    '22' => [
        'model' => '\\Model\\MeasuresModel',
        'table' => 'object_measures',
        'field' => ['number', 'object'],
        'search_field' => ['number', 'object'],
        'global_search_field' => ['number', 'object'],
        'select_field' => ['object_measures.id'],
        'per_page' => 10,
        'search_result_field' => ['object_measures.id', 'object_measures.owner_id', 'object_measures.institution_id', 'object',
            'object_measures.updated_at', 'publishing_status', 'date', 'object_measures.type', 'number'],
        'with' => [
            [
                'relation' => 'structures:id,structure_name',
            ],
            [
                'relation' => 'type:id,value',
            ],
            [
                'relation' => 'created_by',
                'select' => [
                    'id',
                    'name',
                    'deleted',
                ]
            ]
        ],
        'search_result_template' => 'search/data/measures',
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per pagine generiche
    '23' => [
        'model' => '\\Model\\SectionsFoModel',
        'field' => ['name'],
        'search_field' => ['name'],
        'global_search_field' => ['name'],
        'select_field' => ['section_fo.id'],
        'per_page' => 10,
        'search_result_field' => [
            'section_fo.*'
        ],
        'with' => [
            [
                'relation' => 'created_by',
                'select' => [
                    'id',
                    'name',
                    'deleted',
                ]
            ]
        ],
        'groupBy' => [
            [
                'section_fo.id',
            ]
        ],
        'search_result_template' => 'search/data/section_fo',
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per oneri informativi
    '24' => [
        'model' => '\\Model\\ChargesModel',
        'field' => ['title'],
        'search_field' => ['title'],
        'global_search_field' => ['title', 'type'],
        'select_field' => ['object_charges.id'],
        'per_page' => 10,
        'search_result_field' => [
            'object_charges.id', 'object_charges.owner_id', 'object_charges.institution_id', 'type', 'title', 'object_charges.updated_at',
            'publishing_status'],
        'with' => [
            [
                'relation' => 'proceedings:id,name',
            ],
            [
                'relation' => 'regulations:id,title',
            ],
            [
                'relation' => 'created_by',
                'select' => [
                    'id',
                    'name',
                    'deleted',
                ]
            ]
        ],
        'search_result_template' => 'search/data/charges',
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per interventi
    '25' => [
        'model' => '\\Model\\InterventionsModel',
        'field' => ['name'],
        'search_field' => ['name'],
        'global_search_field' => ['name'],
        'select_field' => ['object_interventions.id'],
        'per_page' => 10,
        'search_result_field' => [
            'object_interventions.id', 'object_interventions.institution_id', 'owner_id', 'object_interventions.name',
            'time_limits', 'estimated_cost', 'effective_cost', 'object_interventions.updated_at', 'publishing_status'],
        'with' => [
            [
                'relation' => 'measures:id,object'
            ],
            [
                'relation' => 'regulations:id,title'
            ],
            [
                'relation' => 'created_by',
                'select' => [
                    'id',
                    'name',
                    'deleted',
                ]
            ],
        ],
        'search_result_template' => 'search/data/interventions',
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per news e notizie
    '26' => [
        'model' => '\\Model\\NewsNoticesModel',
        'field' => ['title'],
        'search_field' => ['title'],
        'select_field' => ['object_news_notices.id'],
        'per_page' => 10,
        'search_result_field' => [
            'object_news_notices.id', 'institution_id', 'news_date', 'public_in_notice', 'evidence', 'publishing_status',
            'object_news_notices.updated_at', 'title', 'typology', 'owner_id'
        ],
        'search_result_template' => 'search/data/news_notice',
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per profili ACL
    '27' => [
        'model' => '\\Model\\AclProfilesModel',
        'field' => ['name'],
        'search_field' => ['name'],
        'select_field' => ['acl_profiles.id'],
        'per_page' => 10,
        'search_result_field' => [
            'acl_profiles.*'
        ],
        'with' => [
            [
                'relation' => 'institution:id,full_name_institution'
            ]
        ],
        'join' => [
            [
                'institutions as i',
                'acl_profiles.institution_id',
                '=',
                'i.id',
                'left outer'
            ]
        ],
        'scope' => 'institutionfilter',
        'search_result_template' => 'search/data/acl_profiles',
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per scelta del contraente
    '28' => [
        'model' => '\\Model\\ContraentChoice',
        'field' => ['name'],
        'search_field' => ['name'],
        'per_page' => 10
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per requisiti di qualificazione
    '29' => [
        'model' => '\\Model\\NoticesForQualificationRequirementsModel',
        'field' => ['code', 'denomination'],
        'search_field' => ['code', 'denomination'],
        'search_result_field' => ['*',],
        'search_result_template' => '',
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per delibere - campo: Altre procedure relative
    //Per bando gara - campo: Altre procedure
    //Per esito - campo: Altre procedure
    //Per avviso - campo: Altre procedure
    '30' => [
        'model' => '\\Model\\ContestsActsModel',
        'table' => 'object_contests_acts',
        'field' => ['object_contests_acts.type', 'object_contests_acts.cig', 'object_contests_acts.object'],
        'per_page' => 10,
        'search_field' => ['object_contests_acts.type', 'object_contests_acts.cig', 'object_contests_acts.object', 'relative.cig'],
        'select2_field' => ['object_contests_acts.type', 'object_contests_acts.cig', 'object_contests_acts.relative_notice_id', 'object_contests_acts.object', 'relative.cig as relative_cig', 'object_contests_acts.activation_date', 'object_contests_acts.expiration_date', 'object_contests_acts.id'],
        'whereIn' => [
            [
                'object_contests_acts.typology',
                ['deliberation', 'notice', 'result', 'alert', 'foster', 'lot']
            ]
        ],
        'joinSelect2' => [
            [
                'object_contests_acts as relative',
                'object_contests_acts.relative_notice_id',
                '=',
                'relative.id',
                'left outer'
            ]
        ],
        'search_result_field' => ['*',],
        'search_result_template' => '',
    ],

    //------------------------------------------------------------------------------------------------------------------

    // Per lotto - campo: Bando relativo
    '31' => [
        'model' => '\\Model\\ContestsActsModel',
        'table' => 'object_contests_acts',
        'field' => ['object'],
        'per_page' => 10,
        'search_field' => ['object'],
        'search_result_field' => ['*',],
        'where' => [
            [
                'is_multicig',
                '=',
                1
            ],
            [
                'typology',
                '=',
                'notice'
            ]
        ],
        'search_result_template' => '',
    ],

    //------------------------------------------------------------------------------------------------------------------

    // Per esito - campo: Bando di gara
    '32' => [
        'model' => '\\Model\\ContestsActsModel',
        'table' => 'object_contests_acts',
        'field' => ['type', 'cig', 'object'],
        'per_page' => 10,
        'search_field' => ['type', 'cig', 'object'],
        'search_result_field' => ['*',],
        'whereIn' => [
            [
                'typology',
                ['lot', 'notice']
            ]
        ],
        'where' => [
            [
                'is_multicig',
                '=',
                0
            ]
        ],
        'search_result_template' => '',
        'scope' => 'lotsWithoutResult',
    ],

    //------------------------------------------------------------------------------------------------------------------

    // Per liquidazione - campo: Procedura relativa
    '33' => [
        'model' => '\\Model\\ContestsActsModel',
        'table' => 'object_contests_acts',
        'field' => ['object_contests_acts.type', 'ifnull(object_contests_acts.cig, \'\')', 'object_contests_acts.object', 'relative_cig'],
        'select2_field' => ['object_contests_acts.type', 'object_contests_acts.cig', 'object_contests_acts.object', 'relative.cig as relative_cig', 'object_contests_acts.activation_date', 'object_contests_acts.expiration_date', 'object_contests_acts.id'],
        'search_field' => ['object_contests_acts.type', 'object_contests_acts.cig', 'object_contests_acts.object', 'relative.cig'],
        'per_page' => 10,
        'whereIn' => [
            [
                'object_contests_acts.typology',
                ['foster', 'result']
            ]
        ],
        'joinSelect2' => [
            [
                'object_contests_acts as relative',
                'object_contests_acts.relative_notice_id',
                '=',
                'relative.id',
                'left outer'
            ],
        ],
        'search_result_field' => ['*',],
        'search_result_template' => '',
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per bandi di concorso
    '34' => [
        'model' => '\\Model\\ContestModel',
        'table' => 'object_contest',
        'field' => ['object'],
        'whereIn' => [
            [
                'typology',
                ['avviso', 'concorso']
            ]
        ],
        'search_field' => ['object', 'typology'],
        'search_result_field' => ['*',],
        'search_result_template' => '',
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per sovvenzioni
    '35' => [
        'model' => '\\Model\\GrantsModel',
        'field' => ['object'],
        'search_field' => ['object'],
        'per_page' => 10,
        'where' => [
            [
                'typology',
                '=',
                'Sovvenzione'
            ]
        ],
        'search_result_field' => ['*',],
        'search_result_template' => '',
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per incarichi
    '36' => [
        'model' => '\\Model\\AssignmentsModel',
        'table' => 'object_assignments',
        'field' => ['object', 'name'],
        'search_field' => ['object', 'name'],
        'per_page' => 10,
        'where' => [
            [
                'typology',
                '=',
                'assignment'
            ]
        ],
        'search_result_field' => ['*',],
        'search_result_template' => '',
    ],

    //------------------------------------------------------------------------------------------------------------------

    // Per tipologie di provvediementi
    '37' => [
        'model' => '\\Model\\SelectDataModel',
        'field' => ['value'],
        'search_field' => ['value'],
        'per_page' => 10,
        'where' => [
            [
                'typology',
                '=',
                'measure_typology'
            ]
        ],
        'search_result_field' => ['*',],
        'search_result_template' => '',
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per provvedimenti
    '38' => [
        'model' => '\\Model\\ContestsActsModel',
        'table' => 'object_contests_acts',
        'field' => ['object'],
        'search_field' => ['object'],
        'per_page' => 10,
        'search_result_field' => ['*',],
        'whereIn' => [
            [
                'typology',
                ['notice', 'foster']
            ]
        ],
        'search_result_template' => '',
    ],

    //------------------------------------------------------------------------------------------------------------------

    // Per utenti
    '39' => [
        'model' => '\\Model\\UsersModel',
        'field' => ['name', 'username'],
        'select_field' => ['users.id'],
        'per_page' => 10,
        'fields' => '',
        'search_field' => '',
        'search_result_field' => ['users.*'],
        'with' => [
            [
                'relation' => 'institution:id,full_name_institution',
            ],
            [
                'relation' => 'profiles:id,name',
            ]
        ],
        'join' => [
            [
                'institutions as i',
                'users.institution_id',
                '=',
                'i.id',
                'left outer'
            ],
        ],
        'search_result_template' => 'search/data/users',
    ],

    //------------------------------------------------------------------------------------------------------------------

    // Per tipo ente
    '40' => [
        'model' => '\\Model\\InstitutionTypeModel',
        'field' => ['name'],
        'search_field' => ['name'],
        'per_page' => 10,
    ],

    //------------------------------------------------------------------------------------------------------------------

    // Per avviso - campo: Bando di gara relativo
    '41' => [
        'model' => '\\Model\\ContestsActsModel',
        'table' => 'object_contests_acts',
        'field' => ['ifnull(cig, \'\')', 'ifnull(object, \'\')'],
        'search_field' => ['cig', 'object'],
        'per_page' => 10,
        'search_result_field' => ['*',],
        'where' => [
            [
                'typology',
                '=',
                'notice'
            ]
        ],
        'search_result_template' => '',
    ],

    //------------------------------------------------------------------------------------------------------------------

    // Per fornitori(mostra solo quelli singoli e non i ragruppamenti)
    '42' => [
        'model' => '\\Model\\SupplieListModel',
        'table' => 'object_supplie_list',
        'field' => ['name', 'vat'],
        'search_field' => ['name', 'vat'],
        'select_field' => ['object_supplie_list.id'],
        'per_page' => 10,
        'search_result_field' => [
            'object_supplie_list.*',
        ],
        'with' => [
            [
                'relation' => 'institution:id,full_name_institution',
            ]
        ],
        'join' => [
            [
                'institutions as i',
                'object_supplie_list.institution_id',
                '=',
                'i.id',
                'left outer'
            ]
        ],
        'where' => [
            [
                'object_supplie_list.typology',
                '=',
                1
            ],
        ],
        'search_result_template' => 'search/data/supplier',
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per le sezioni di front-office
    '43' => [
        'model' => '\\Model\\SectionsFoModel',
        'table' => 'section_fo',
        'field' => ['section_fo.id', 'section_fo.name'],
        'search_field' => ['section_fo.name', 'section_fo.id'],
        'select_field' => ['section_fo.id'],
        'per_page' => 10,
        'where' => [
            [
                'section_fo.is_system',
                '=',
                1
            ],
        ],
        'search_result_template' => '',
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per CPV Code
    '44' => [
        'model' => '\\Model\\CPVCodesModel',
        'field' => ['code', 'name'],
        'search_field' => ['code', 'name'],
        'search_result_field' => ['*',],
        'search_result_template' => '',
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per BDNCP - Atti e Documenti di carattere generale
    '45' => [
        'model' => '\\Model\\GeneralActsDocumentsModel',
        'table' => 'object_bdncp_general_acts_documents',
        'field' => ['object'],
        'search_field' => ['object'],
        'global_search_field' => ['object', 'cup', 'notes', 'document_date', 'financial_sources', 'procedural_implementation_status'],
        'per_page' => 10,
        'select_field' => ['object_bdncp_general_acts_documents.id'],
        'search_result_field' => [
            'object_bdncp_general_acts_documents.*',
        ],
        'with' => [
            [
                'relation' => 'created_by',
                'select' => [
                    'id',
                    'name',
                    'deleted'
                ]
            ]
        ],

        'groupBy' => [
            [
                'object_bdncp_general_acts_documents.id'
            ]
        ],
        'search_result_template' => 'search/data/general_acts_documents',
    ],

    //------------------------------------------------------------------------------------------------------------------

    //Per BDNCP - Procedure Banca Dati Nazionale Contratti Pubblici
    '46' => [
        'model' => '\\Model\\BdncpProcedureModel',
        'table' => 'object_bdncp_procedure',
        'field' => ['object'],
        'search_field' => ['object', 'cig'],
        'global_search_field' => ['object', 'cig', 'bdncp_link'],
        'per_page' => 10,
        'select_field' => ['object_bdncp_procedure.id'],
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
            ],
        ],
        'groupBy' => [
            [
                'object_bdncp_procedure.id'
            ]
        ],
        'search_result_template' => 'search/data/bdncp_procedure',
    ],

    //Bandi di gara dal 01/01/2024 (solo procedure esclusi gli avvisi)
    '47' => [
        'model' => '\\Model\\BdncpProcedureModel',
        'table' => 'object_bdncp_procedure',
        'field' => ['object'],
        'search_field' => ['object', 'cig'],
        'per_page' => 10,
        'select_field' => ['object_bdncp_procedure.id'],
        'search_result_field' => [
            'object_bdncp_procedure.*',
        ],
        'where' => [
            [
                'typology',
                '=',
                'procedure'
            ]
        ],
        'groupBy' => [
            [
                'object_bdncp_procedure.id'
            ]
        ],
        'search_result_template' => '',
    ],

    // Per utenti
    '48' => [
        'model' => '\\Model\\UsersModel',
        'field' => ['name', 'username'],
        'select_field' => ['users.id'],
        'per_page' => 10,
        'fields' => '',
        'search_field' => '',
        'search_result_field' => ['users.*'],
        'with' => [
            [
                'relation' => 'institution:id,full_name_institution',
            ],
            [
                'relation' => 'profiles:id,name',
            ]
        ],
        'join' => [
            [
                'institutions as i',
                'users.institution_id',
                '=',
                'i.id',
                'left outer'
            ],
        ],
        'search_result_template' => 'search/data/users',
    ],

    //Bandi di gara dal 01/01/2024 solo procedure multicig
    '49' => [
        'model' => '\\Model\\BdncpProcedureModel',
        'table' => 'object_bdncp_procedure',
        'field' => ['object'],
        'search_field' => ['object'],
        'per_page' => 10,
        'select_field' => ['object_bdncp_procedure.id'],
        'search_result_field' => [
            'object_bdncp_procedure.*',
        ],
        'where' => [
            [
                'multicig',
                '=',
                '1'
            ]
        ],
        'groupBy' => [
            [
                'object_bdncp_procedure.id'
            ]
        ],
        'search_result_template' => '',
    ],
];
