<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * -- MYSQL table sessions:
 * create table sessions
 * (
 * id            varchar(191) not null
 * primary key,
 * user_id       int          null,
 * ip_address    varchar(45)  null,
 * user_agent    text         null,
 * payload       text         null,
 * fp            varchar(255) null,
 * last_activity int          null
 * )
 * charset = utf8mb4
 * row_format = COMPACT;
 */
return [
    /**
     * Storage delle sessioni: 'native' o 'file' o 'database'
     * Se è impostato sul valore native, avvia le sessioni nativa di php.
     */
    'save_handler' => 'native',

    /**
     * Percorso per i file di sessione (valorizzarlo solamente se lo storage di sessione è impostato su file)
     */
    'save_path' => '',

    /**
     * nome della session
     */
    'name' => 'IsWeb',

    /**
     * Ciclo di vita della sessione espressa in secondi
     */
    'lifetime' => 10800,

    /**
     * Ciclo di vita della sessione espressa in secondi in sostituzione del php.ini (Verificare sempre i permessi)
     */
    'max_lifetime' => 43200,

    /**
     * Sicurezza nei cookie
     */
    'secure' => false,

    /**
     *
     */
    'httponly' => true,

    /**
     *
     */
    'samesite' => 'Lax',

    /**
     * Rigenerazioni delle sessioni espressa in secondi
     */
    'regenerate_interval' => 7200,

    /**
     * Chiave per crittografia
     */
    'encryption_key' => _env('TOKEN_SESSION_KEY'),

    /**
     * Stabilisce se associare la sessione all'indirizzo IP (Session Binding)
     * Se true, la sessione verrà invalidata se l'IP cambia.
     */
    'match_ip' => true,

    /**
     * Stabilisce se nelle sessioni salvate sul database vegono crittografati IP ed User Agent
     */
    'encryption_ip_ua_session_db' => true,
];