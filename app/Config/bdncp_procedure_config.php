<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') or exit('No direct script access allowed');

return [
    /**
     * title ---> Indica il Titolo della Checkbox
     * field ---> Indica il corrispondente campo sul db
     * step ---> Indica la fase di appartenenza
     * attachs ---> Indca che non sono previsti allegati per la checkbox
     */


    'config' => [
        '_publicDebate' => [
            'title' => 'Dibattito pubblico',
            'field' => 'public_debate',
            'step' => 'publication',
            'fase' => 'FASE PUBBLICAZIONE'
        ],
        '_noticeDocuments' => [
            'title' => 'Documenti di gara',
            'field' => 'notice_documents',
            'step' => 'publication',
            'fase' => 'FASE PUBBLICAZIONE'
        ],
        '_equalOpportunitiesAf' => [
            'title' => 'Pari opportunità e inclusione lavorativa nei contratti pubblici PNRR e PNC e nei contratti riservati',
            'field' => 'equal_opportunities_af',
            'step' => 'fostering',
            'fase' => 'FASE AFFIDAMENTO'
        ],
        '_localPublicServices' => [
            'title' => 'Procedura di affidamento dei servizi pubblici locali',
            'field' => 'local_public_services',
            'step' => 'fostering',
            'fase' => 'FASE AFFIDAMENTO'
        ],
        '_equalOpportunitiesEs' => [
            'title' => 'Pari opportunità e inclusione lavorativa nei contratti pubblici PNRR e PNC e nei contratti riservati',
            'field' => 'equal_opportunities_es',
            'step' => 'executive',
            'fase' => 'FASE ESECUTIVA'
        ],
        '_freeContract' => [
            'title' => 'Contratti gratuiti e forme speciali di partenariato',
            'field' => 'free_contract',
            'step' => 'sponsorship',
            'fase' => 'FASE SPONSORIZZAZIONI'
        ],
        '_emergencyFoster' => [
            'title' => 'Atti e documenti relativi agli affidamenti di somma urgenza',
            'field' => 'emergency_foster',
            'step' => 'emergency_procedure',
            'fase' => 'FASE PROCEDURE DI SOMMA URGENZA E DI PROTEZIONE CIVILE'
        ],
        '_fosterProcedure' => [
            'title' => 'Procedura di affidamento',
            'field' => 'foster_procedure',
            'step' => 'project_finance',
            'fase' => 'FASE FINANZA DI PROGETTO'
        ],
        '_judgingCommission' => [
            'title' => 'Composizione delle commissioni giudicatrici e CV dei componenti',
            'field' => 'judging_commission',
            'subTitle' => 'Composizione delle commissioni giudicatrici',
            'step' => 'fostering',
            'attachs' => 'false',
            'fase' => 'FASE AFFIDAMENTO'
        ],
        '_advisoryBoardTechnical' => [
            'title' => 'Composizione del Collegio consultivo tecnico',
            'field' => 'advisory_board_technical',
            'subTitle' => 'Composizione del Collegio consultivo tecnico',
            'step' => 'executive',
            'attachs' => 'false',
            'fase' => 'FASE ESECUTIVA'
        ],
    ]
];
