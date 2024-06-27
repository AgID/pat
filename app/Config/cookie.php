<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

defined('_FRAMEWORK_') or exit('No direct script access allowed');

return [

    /**
     * Impostare un prefisso per il nome del cookie se è necessario evitare collisioni
     */
    'prefix' => '',

    /**
     * Impostare su .your-domain.com per i cookie a livello di sito
     */
    'domain' => '',

    /**
     * Tipicamente è lo slash
     */
    'path' => '/',

    /**
     * Il cookie verrà impostato solo se esiste una connessione con protocollo HTTPS sicura
     */
    'secure' => false,

    /**
     * Il cookie sarà accessibile solo tramite HTTP (S) (no javascript)
     */
    'httponly' => false
];
