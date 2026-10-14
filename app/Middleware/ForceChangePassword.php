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

        // Le utenze tecniche (technical_user = 1) non sono soggette al cambio password
        // forzato: guardia difensiva nel caso il flag "expire_password" sia stato
        // valorizzato in sessione da altri canali di autenticazione.
        $identity = authPatOS()->getIdentity(['technical_user']);
        if (!empty($identity['technical_user'])) {
            return true;
        }

        $getStorage = authPatOS()->getStorage(['expire_password']);


        if (defined('START_SEGMENTS')) {
            $base = str_replace(
                [
                    baseUrl(),
                    '.html'
                ],
                [
                    '',
                    ''
                ],
                siteUrl()
            );
        } else {
            $base = '';
        }
        $paths = [

            // Tenant con path nel nome a dominio
            $base . '/profile/update',
            $base . '/admin/profile',
            $base . '/admin/profile/update/password',
            $base . '/admin/profile/update',
            $base . '/admin/profile/force/password',

            // Tenant senza path nel nome a dominio
            'profile/update',
            'admin/profile',
            'admin/profile/update/password',
            'admin/profile/update',
            'admin/profile/force/password'
        ];

        if (
            !in_array(uri()->uriString(), $paths)
        ) {
            if (isset($getStorage['expire_password']) && (bool) $getStorage['expire_password'] === true) {

                sessionSetNotify('La password per il tuo account &egrave; scaduta, devi impostarne una nuova da utilizzare per l\'accesso.', 'info');
                redirect('admin/profile/force/password');

            }
        }

        return true;
    }
}