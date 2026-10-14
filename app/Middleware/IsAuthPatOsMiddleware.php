<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Middleware;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class IsAuthPatOsMiddleware
{

    public function __construct(){}

    /**
     * @description Verifica se la rotta non prevedere l'autenticazione prima dell'accesso utente.
     * @return bool|void
     */
    public function handle()
    {
        if (!authPatOS()->hasIdentity()) {

            // registro l'URL di ritorno dop aver effettuato il login
            session()->setTemp('redirect_url_after_login', uri()->fullUrl(true), 60*5);
            // Non autenticato
            helper('url');
            redirect('auth','refresh');
            die();

        }

        return true;
    }

}
