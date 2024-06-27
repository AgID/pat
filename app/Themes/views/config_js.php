<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') OR exit('No direct script access allowed'); ?>
// Configurazioni per i datatable delle select nei form
const config = {
    //personale
    personnel: {
        url: '<?php echo siteUrl("/admin/async/get/data"); ?>',
        textLoad: 'Attendere, caricamento personale in corso..',
        columns: [
            'TITOLO',
            'NOME',
            'RUOLO',
            'EMAIL'
        ],
        dataSource: [
            'id', 'title', 'full_name', 'role_name', 'email'
        ],
        addRecord: {
            show_action: <?php echo $is_box; ?>,
            url: '<?php echo siteUrl('admin/personnel/create-box'); ?>',
            editUrl: '<?php echo baseUrl('admin/personnel/edit-box/'); ?>',
            label: 'Aggiungi nuovo'
        },
        footerTable: false,
        classTable: 'table table-hover table-bordered table-striped table-sm',
        hideTable: '<i class="fas fa-plus-circle"></i> MOSTRA TABELLA',
        showTable: '<i class="fas fa-minus-circle"></i> NASCONDI TABELLA',
        search_placeholder: 'Cerca nel personale..',
        archived: 2,
        published: 2,
    },
    //incarichi
    assignment: {
        url: '<?php echo siteUrl("/admin/async/get/data"); ?>',
        textLoad: 'Attendere, caricamento incarichi in corso..',
        columns: [
            'NOMINATIVO',
            'OGGETTO',
            'INIZIO INCARICO',
            'FINE INCARICO'
        ],
        dataSource: [
            'id', 'name', 'object','assignment_start', 'assignment_end'
        ],
        dateFormat: ['assignment_start', 'assignment_end'],
        addRecord: {
            show_action: <?php echo $is_box; ?>,
            url: '<?php echo siteUrl('admin/assignment/create-box'); ?>',
            editUrl: '<?php echo baseUrl('admin/assignment/edit-box-assignment/'); ?>',
            label: 'Aggiungi nuovo'
        },
        footerTable: false,
        classTable: 'table table-hover table-bordered table-striped table-sm',
        hideTable: '<i class="fas fa-plus-circle"></i> MOSTRA TABELLA',
        showTable: '<i class="fas fa-minus-circle"></i> NASCONDI TABELLA',
        search_placeholder: 'Cerca negli incarichi..'
    },
    //concorsi
    contest: {
        url: '<?php echo siteUrl("/admin/async/get/data"); ?>',
        textLoad: 'Attendere, caricamento concorsi in corso..',
        columns: [
            'OGGETTO',
            'TIPO',
            'DATA PUBBLICAZIONE',
            'DATA SCADENZA'
        ],
        dataSource: [
            'id', 'object', 'typology', 'activation_date', 'expiration_date'
        ],
        dateFormat: ['activation_date', 'expiration_date'],
        addRecord: {
            show_action: <?php echo $is_box; ?>,
            url: '<?php echo siteUrl('admin/contest/create-box'); ?>',
            editUrl: '<?php echo baseUrl('admin/contest/edit-box/'); ?>',
            label: 'Aggiungi nuovo'
        },
        footerTable: false,
        classTable: 'table table-hover table-bordered table-striped table-sm',
        hideTable: '<i class="fas fa-plus-circle"></i> MOSTRA TABELLA',
        showTable: '<i class="fas fa-minus-circle"></i> NASCONDI TABELLA',
        search_placeholder: 'Cerca nei concorsi..'
    },
    //bandi di gara fino al 31/12/2023
    notice: {
        url: '<?php echo siteUrl("/admin/async/get/data"); ?>',
        textLoad: 'Attendere, caricamento bandi di gara in corso..',
        columns: [
            'TIPOLOGIA',
            'OGGETTO',
            'CIG',
            'Data di pubblicazione',
            'Data di scadenza'
        ],
        dataSource: [
            'id', 'type', 'object', 'cig,relative_cig', 'activation_date', 'expiration_date'
        ],
        dateFormat: ['activation_date', 'expiration_date'],
        footerTable: false,
        classTable: 'table table-hover table-bordered table-striped table-sm',
        hideTable: '<i class="fas fa-plus-circle"></i> MOSTRA TABELLA',
        showTable: '<i class="fas fa-minus-circle"></i> NASCONDI TABELLA',
        search_placeholder: 'Cerca nei bandi di gara..',
        published: 2
    },
    //fornitori
    supplier: {
        url: '<?php echo siteUrl("/admin/async/get/data"); ?>',
        textLoad: 'Attendere, caricamento fornitori in corso..',
        columns: [
            'NOME',
            'P.IVA',
            'TIPOLOGIA'
        ],
        dataSource: [
            'id', 'name', 'vat','type'
        ],
        addRecord: {
            show_action: <?php echo $is_box; ?>,
            url: '<?php echo siteUrl('admin/supplier/create-box'); ?>',
            editUrl: '<?php echo baseUrl('admin/supplier/edit-box/'); ?>',
            label: 'Aggiungi nuovo'
        },
        footerTable: false,
        classTable: 'table table-hover table-bordered table-striped table-sm',
        hideTable: '<i class="fas fa-plus-circle"></i> MOSTRA TABELLA',
        showTable: '<i class="fas fa-minus-circle"></i> NASCONDI TABELLA',
        search_placeholder: 'Cerca nei fornitori..'
    },
    //provvedimenti
    measure: {
        url: '<?php echo siteUrl("/admin/async/get/data"); ?>',
        textLoad: 'Attendere, caricamento provvedimenti in corso..',
        columns: [
            'OGGETTO',
            'NUMERO',
            'DATA'
        ],
        dataSource: [
            'id', 'object', 'number','date'
        ],
        dateFormat: ['date'],
        addRecord: {
            show_action: <?php echo $is_box; ?>,
            url: '<?php echo siteUrl('admin/measure/create-box'); ?>',
            editUrl: '<?php echo baseUrl('admin/measure/edit-box/'); ?>',
            label: 'Aggiungi nuovo'
        },
        footerTable: false,
        classTable: 'table table-hover table-bordered table-striped table-sm',
        hideTable: '<i class="fas fa-plus-circle"></i> MOSTRA TABELLA',
        showTable: '<i class="fas fa-minus-circle"></i> NASCONDI TABELLA',
        search_placeholder: 'Cerca nei provvedimenti..'
    },
    //strutture
    structure: {
        url: '<?php echo siteUrl("/admin/async/get/data"); ?>',
        textLoad: 'Attendere, caricamento strutture in corso..',
        columns: [
            'NOME STRUTTURA',
            'STRUTTURA DI APPARTENENZA',
            'EMAIL'
        ],
        dataSource: [
            'id', 'structure_name', 'parent_structure', 'reference_email'
        ],
        addRecord: {
            show_action: <?php echo $is_box; ?>,
            url: '<?php echo siteUrl('admin/structure/create-box'); ?>',
            editUrl: '<?php echo baseUrl('admin/structure/edit-box/'); ?>',
            label: 'Aggiungi nuovo'
        },
        footerTable: false,
        classTable: 'table table-hover table-bordered table-striped table-sm',
        hideTable: '<i class="fas fa-plus-circle"></i> MOSTRA TABELLA',
        showTable: '<i class="fas fa-minus-circle"></i> NASCONDI TABELLA',
        search_placeholder: 'Cerca nelle strutture..',
        archived: 1
    },
    //bandi di gara dal 01/01/2024
    bdncp_notice: {
        url: '<?php echo siteUrl("/admin/async/get/data"); ?>',
        textLoad: 'Attendere, caricamento bandi di gara in corso..',
        columns: [
            'OGGETTO',
            'CIG'
        ],
        dataSource: [
            'id', 'object', 'cig'
        ],
        footerTable: false,
        classTable: 'table table-hover table-bordered table-striped table-sm',
        hideTable: '<i class="fas fa-plus-circle"></i> MOSTRA TABELLA',
        showTable: '<i class="fas fa-minus-circle"></i> NASCONDI TABELLA',
        search_placeholder: 'Cerca nei bandi di gara..',
    },
    bdncp_multicig: {
    url: '<?php echo siteUrl("/admin/async/get/data"); ?>',
    textLoad: 'Attendere, caricamento bandi di gara in corso..',
    columns: [
        'OGGETTO'
    ],
    dataSource: [
    'id', 'object'
    ],
    footerTable: false,
    classTable: 'table table-hover table-bordered table-striped table-sm',
    hideTable: '<i class="fas fa-plus-circle"></i> MOSTRA TABELLA',
    showTable: '<i class="fas fa-minus-circle"></i> NASCONDI TABELLA',
    search_placeholder: 'Cerca nei bandi di gara..',
},

};