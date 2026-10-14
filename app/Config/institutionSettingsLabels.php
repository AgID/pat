<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') or exit('No direct script access allowed');

return [

    'institution_settings_labels' => [

        // Dati generali
        'full_name_institution' => 'Nome completo ente',
        'vat' => 'Partita IVA',
        'fiscal_code' => 'Codice fiscale',
        'email_address' => 'Indirizzo email normale',
        'certified_email_address' => 'Indirizzo email certificata',
        'top_level_institution_name' => 'Ente di appartenenza',
        'top_level_institution_url' => 'URL ente di appartenenza',

        // Recapiti dell'ente
        'address_city' => 'Comune',
        'address_street' => 'Indirizzo',
        'address_zip_code' => 'CAP',
        'address_province' => 'Provincia',
        'phone' => 'Recapito telefonico principale',

        // Configurazione e moduli gestibili
        'simple_logo_file' => 'Logo ente',
        'favicon_file' => 'Icon favicon',
        'institutional_website_url' => 'Url portale istituzionale',
        'bulletin_board_url' => 'Url Albo Pretorio',
        'bulletin_board_url_name' => 'Etichetta Url Albo Pretorio',
        'indexable' => 'Indicizzabile dai motori di ricerca',
        'show_regulation_in_structure' => 'Mostra Norma associata alla Struttura organizzativa',
        'trasp_responsible_user_id' => 'Responsabile della Trasparenza',
        'statistics_tracking_code' => 'Codice di monitoraggio statistiche',

        // Campo non più modificabile dal form: con il PNA 2025 la data di aggiornamento
        // deve essere sempre pubblicata, quindi viene inviato come hidden fisso a 1.
        'show_update_date' => 'Mostra la data di ultimo aggiornamento dei contenuti',

        // Altre informazioni
        'publication_responsible' => 'Responsabile del procedimento di pubblicazione',
        'privacy_url' => 'Url Privacy',
        'welcome_text' => 'Testo iniziale homepage',
        'footer_text' => 'Testo nel footer',
        'accessibility_text' => 'Testo accessibilità',

        // Configurazione server Email
        'smtp_user' => 'SMTP - Username',
        'smtp_from' => 'SMTP - From',
        'smtp_pass' => 'SMTP - Password',
        'smtp_host' => 'SMTP - Indirizzo server',
        'smtp_port' => 'SMTP - Porta',
        'smtp_security' => 'SMTP - SSL',
        'show_smtp_auth' => 'SMTP - Usa autenticazione',
        'pec_smtp_user' => 'PEC SMTP - Username',
        'pec_smtp_from' => 'PEC SMTP - From',
        'pec_smtp_pass' => 'PEC SMTP - Password',
        'pec_smtp_host' => 'PEC SMTP - Indirizzo server',
        'pec_smtp_port' => 'PEC SMTP - Porta',
        'pec_smtp_security' => 'PEC SMTP - SSL',
        'pec_show_smtp_auth' => 'PEC SMTP - Usa autenticazione',

        // Impostazioni di sistema
        'trasparenza_urls' => 'Url portale PAT',
        'institution_type_id' => 'Tipo di ente',
    ],

    /**
     * Campi esclusi dal confronto perché non rappresentano una scelta dell'utente:
     *
     * - "id_creator" e "expiration_date" vengono riscritti a ogni salvataggio e risulterebbero
     *   sempre modificati;
     * - "smtp_auth" è derivato da "show_smtp_auth" e produrrebbe una voce duplicata nel log.
     */
    'institution_settings_excluded_fields' => [
        'id_creator',
        'expiration_date',
        'smtp_auth',
    ],
];
