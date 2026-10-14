<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') or exit('No direct script access allowed');

/**
 * Setta e nasconde le variabili nell'handler della gestione degli errori
 */
return [
    'blacklist' => [
        '_GET' => [],
        '_POST' => [],
        '_FILES' => [],
        '_COOKIE' => [
            'PHPSESSID'
        ],
        '_SESSION' => ['___FRAMEWORK___'],
        '_SERVER' => [
            'DB_USE',
            'DB_CONNECTION',
            'DB_HOST',
            'DB_DATABASE',
            'DB_USERNAME',
            'DB_PASSWORD',
            'DB_PATHUB_CONNECTION',
            'DB_PATHUB_HOST',
            'DB_PATHUB_DATABASE',
            'DB_PATHUB_USERNAME',
            'DB_PATHUB_PASSWORD',
            'CHIAVE_SYNC_PAT',
            'TOKEN_JWT',
            'TOKEN_SESSION_KEY',
            'TOKEN_FRINGE_PRINT',
            'AUTH_KEY',
            'MAIL_SMTP_PROTOCOL',
            'MAIL_SMTP_HOST',
            'MAIL_USERNAME',
            'MAIL_PASSWORD',
            'MAIL_SMTP_CRYPTO',
            'MAIL_PORT',
            'REDIRECT_SSL_SESSION_ID',
            'REDIRECT_SSL_SERVER_A_SIG',
            'REDIRECT_SSL_SERVER_A_KEY',
            'REDIRECT_SSL_SERVER_M_SERIAL',
            'REDIRECT_SSL_SECURE_RENEG',
            'PHP_SELF',
            'REDIRECT_SSL_CIPHER',
            'PWD',
            'REMOTE_PORT',
            'ENCRYPTION_KEY',
            'ENCRYPTION_IV',
            'CIPHER',
        ],
        '_ENV' => [
            'DB_USE',
            'DB_CONNECTION',
            'DB_HOST',
            'DB_DATABASE',
            'DB_USERNAME',
            'DB_PASSWORD',
            'TOKEN_JWT',
            'TOKEN_SESSION_KEY',
            'TOKEN_FRINGE_PRINT',
            'AUTH_KEY',
            'MAIL_SMTP_PROTOCOL',
            'MAIL_SMTP_HOST',
            'MAIL_USERNAME',
            'MAIL_PASSWORD',
            'MAIL_SMTP_CRYPTO',
            'MAIL_PORT',
            'REDIRECT_SSL_SESSION_ID',
            'REDIRECT_SSL_SERVER_A_SIG',
            'REDIRECT_SSL_SERVER_A_KEY',
            'REDIRECT_SSL_SERVER_M_SERIAL',
            'REDIRECT_SSL_SECURE_RENEG',
            'PHP_SELF',
            'REDIRECT_SSL_CIPHER',
            'PWD',
            'REMOTE_PORT',
        ],
    ]
];
