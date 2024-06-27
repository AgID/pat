<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') or exit('No direct script access allowed');


return [
    /**
     * il componente driver sul quale effettuare l'auteticazione
     * DRIVERS:
     * - database
     * - ldap
     * - jwt
     * DEFAULT database
     */
    'driver' => 'database',

    /**
     * Criptazione file di sessione per l'autenticazion
     */
    'has_encryption' => true,

    /**
     * Chiave per la crittografia dei dati in Sessione, JWT es LDAP
     */
    'encryption_key' => _env('TOKEN_JWT'),

    /**
     * Algortimo utilizzato per la crittografia
     * - mcrypt
     * - openssl
     */
    'algorithm' => 'openssl',


    //DATABASE ---------------------------------------------------------------------------------------------------------

    /**
     * Il nome della tabella per effettuare la query di autenticazione utente
     */
    'table' => '',

    /**
     * Il nome della riga della chiave primaria nella tabella del database
     */
    'pk' => '',

    /**
     * Il nome della riga della username nella tabella del database
     */
    'username' => '',

    /**
     * Il nome della riga della casella email nella tabella del database
     */
    'email' => '',

    /**
     * Il nome della riga della password nella tabella del database
     */
    'password' => '',

    /**
     * Il nome della riga della attivazione o disattivazione utente nella tabella del database
     */
    'active' => '',

    /**
     * Nome della sessione da associate per l'autenticazione
     */
    'session_name' => '_sess',

    // Custom PAT OS ---------------------------------------------------------------------------------------------------

    /**
     * Nome colonna utente super admin
     */
    'super_admin' => 'super_admin',

    /**
     * Nome colonna utente cancellata
     */
    'deleted' => 'deleted',

    //JWT --------------------------------------------------------------------------------------------------------------

    /**
     * Temp di validità del token espresso in secondi
     */
    'token_expire' => 28800,

];
