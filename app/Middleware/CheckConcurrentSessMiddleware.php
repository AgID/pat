<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Middleware;

use Model\ConcurrentSessModel;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class CheckConcurrentSessMiddleware
{

    public function __construct()
    {
    }

    /**
     * @description Verifica se la rotta non prevedere l'autenticazione prima dell'accesso utente.
     * @return bool|void
     */
    public function handle()
    {
        $sessionId = session()->getId();

        $query = ConcurrentSessModel::select(['id'])
            ->where('destroyed_sess_id', 1)
            ->where('sess_id', $sessionId)
            ->where('created_at', '>=', date("Y-m-d H:i:s", strtotime("-8 hours")))
            ->where('institution_id', checkAlternativeInstitutionId())
            ->first();

        if ($query !== null) {
            $result = $query->toArray();

            // Elimino tutte le sessioni associate al session_id
            ConcurrentSessModel::where('id', $result['id'])
                ->where('institution_id', checkAlternativeInstitutionId())
                ->delete();

            // Distruggo la sessione corrente
            authPatOs()->clearIdentity();
            session()->setFlash('logout_user_ok', 'Logout effettuato!');
            redirect('auth');
        }

        return true;
    }

}
