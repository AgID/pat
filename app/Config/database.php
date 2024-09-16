<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') or exit('No direct script access allowed');

return [

    // Connessione al database di default
    'db_connect_default' => _env('DB_USE'),

    'params' => [

        // Connessione database default
        'default' => [
            'driver' => _env('DB_CONNECTION'),
            'host' => _env('DB_HOST'),
            'database' => _env('DB_DATABASE'),
            'username' => _env('DB_USERNAME'),
            'password' => _env('DB_PASSWORD'),
            'charset' => 'utf8mb4',
            'collation' => 'utf8mb4_unicode_ci',
            'prefix' => '',
        ]
    ]
];
