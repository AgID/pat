<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Middleware;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

class ForceChangePassword
{
    /**
     * Costruttore
     */
    public function __construct()
    {

    }

    /**
     * @return bool
     */
    public function handle(): bool
    {
        helper('url');
        $getStorage = authPatOS()->getStorage(['expire_password']);

        if (!in_array(uri()->uriString(), ['profile/update', 'admin/profile', 'admin/profile/update/password', 'admin/profile/update', 'admin/profile/force/password'])) {
            if ((bool)$getStorage['expire_password'] === true) {

                sessionSetNotify('La password per il tuo account &egrave; scaduta, devi impostarne una nuova da utilizzare per l\'accesso.', 'info');
                redirect('admin/profile/force/password');

            }
        }

        return true;
    }
}
