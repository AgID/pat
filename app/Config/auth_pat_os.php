<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

return [
    /**
     * il componente driver sul quale effettuare l'autenticazione
     * DRIVERS:
     * - database
     */
    'driver' => 'database',

    /**
     * Crittografia file di sessione per l'autenticazione
     */
    'has_encryption' => true,

    /**
     * Chiave per la crittografia dei dati in Sessione, JWT es LDAP
     */
    'encryption_key' => _env('AUTH_KEY'),

    /**
     * Algortimo utilizzato per la crittografia
     * - mcrypt
     * - openssl
     */
    'algorithm' => 'openssl',

    /**
     * Il nome della tabella per effettuare la query di autenticazione utente
     */
    'table' => 'users',

    /**
     * Il nome della riga della chiave primaria nella tabella del database
     */
    'pk' => 'id',

    /**
     * Il nome della riga della username nella tabella del database
     */
    'username' => 'username',

    /**
     * Il nome della riga della casella email nella tabella del database
     */
    'email' => 'email',

    /**
     * Il nome della riga della password nella tabella del database
     */
    'password' => 'password',

    /**
     * Il nome della riga della attivazione o disattivazione utente nella tabella del database
     */
    'active' => 'active',

    /**
     * Nome della sessione da associate per l'autenticazione
     */
    'session_name' => '_patos',

    /**
     * Nome colonna utente super admin
     */
    'super_admin' => 'super_admin',

    /**
     * Nome colonna utente admin
     */
    'admin' => 'admin',

    /**
     * Nome colonna utente cancellata
     */
    'deleted' => 'deleted',

    /**
     * Nome colonna utente cancellata
     */
    'last_visit' => 'last_visit',

    /**
     * Nome colonna utente non attivo
     */
    'deactivate_account_no_use' => 'deactivate_account_no_use',

    /**
     * Nome colonna identificativo istituto
     */
    'institutions_id' => 'institution_id',

    /**
     * Nome tabella ente
     */
    'table_institutions' => 'institutions',

    /**
     * Nome colonna limite ultima visita
     */
    'last_visit_time_limit' => 'last_visit_time_limit',

    /**
     * Chiave primaria Institution
     */
    'name_institution_pk' => 'id',

    /**
     * Nome colonna token
     */
    'token_password' => 'password_ws',

    /**
     * abilitazione utente a chiamate API
     */
    'enable_api' => 'is_api',

    /**
     * Limita chiamate api
     */
    'limit_calls_api' => 'limits_call_api'

];
