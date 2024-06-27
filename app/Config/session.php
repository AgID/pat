<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') or exit('No direct script access allowed');


/**
 * -- MYSQL table sessions:
 * CREATE TABLE `sessions` (
 * `id`     varchar(63) CHARACTER SET ascii NOT NULL DEFAULT '',
 * `data`   text,
 * `expire` int(10) unsigned DEFAULT NULL,
 * `ip`     varchar(64) NOT NULL,
 * PRIMARY KEY (`id`), KEY `expire` (`expire`)
 * )
 * ENGINE = InnoDB
 * DEFAULT CHARSET = utf8;
 */
return [

    /**
     * Il Drivers di storage sul quale salvare la sessione
     * DRIVERS:
     * - file
     * - database
     */
    'drivers' => 'database',

    /**
     * Rigenerazione dell ID di sessione epresso in secondi
     */
    'regenerate_id' => 300,

    //FILE -------------------------------------------------------------------------------------------------------------

    /**
     * Se lo storage della sessione è di tipo file, indicare il percorso
     */
    // 'storage' => APP_PATH . 'Storage' . DIRECTORY_SEPARATOR . 'session' . DIRECTORY_SEPARATOR,

    /**
     * Chiave segreta per la crittografia delle sessioni
     */
    'secure_key' => _env('TOKEN_SESSION_KEY'),

    /**
     * Tempo dio inattività limite validità sessione
     * 28800 : 8 ore
     */
    'expire_limit' => 28800,

    /**
     * Chiave univoca associata all'User Agent nella generazione delle sessioni
     */
    'fringe_print' => _env('TOKEN_FRINGE_PRINT'),

    /**
     * Se lo storage della sessione è di tipo file, indicare il prefisso del salvataggio file si sessione
     */
    'prefix_session_file' => 'sess_',

    //DATABASE ---------------------------------------------------------------------------------------------------------

    /**
     * Se lo storage è il database indicare il nome della tabella
     * Esempio: session
     */
    'table' => 'sessions',

    /**
     * Se lo storage è il database indicare il nome della chiave primaria
     * Esempio: id
     */
    'session_id' => 'id',

    /**
     * Se lo storage è il database indicare il nome della data di scadenza
     * Esempio: expire
     */
    'expire' => 'expire',

    /**
     * Se lo storage è il database indicare il nome dello storage dei dati
     * Esempio: data
     */
    'data' => 'data',

    /**
     * Se lo storage è il database indicare il nome dello storage dell'indirizzo IP
     * Esempio: data
     */
    'ip_address' => 'ip'
];
