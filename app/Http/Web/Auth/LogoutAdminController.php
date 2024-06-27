<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Auth;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Helpers\ActivityLog;
use System\BaseController;
use Model\ConcurrentSessModel;

class LogoutAdminController extends BaseController
{
    /**
     * Costruttore
     */
    public function __construct()
    {
        parent::__construct();
        helper('url');
    }

    /**
     * @throws Exception
     * @url /logout.html
     * @method GET
     * @return void
     */
    public function index(): void
    {

        $getIdentity = authPatOs()->getIdentity();

        if (authPatOs()->hasIdentity()) {

            // Registro nelle attività dei logs
            ActivityLog::create([
                'user_id' => $getIdentity['id'],
                'action' => __('auth_logout_title', null, 'patos_auth'),
                'description' => sprintf(__('auth_logout_description', null, 'patos_auth'), checkDecrypt($getIdentity['email']), date('d-m-Y'), date('H:i:s')),
                'object_id' => 54
            ]);


            // Elimino il log delle sessioni concorrenti
            if(!empty($getIdentity['options']['sess_id'])) {

                // ConcurrentSessModel::where('sess_id','=',$getIdentity['options']['sess_id'])->delete();

            }
        }

        authPatOs()->clearIdentity();
        session()->setFlash('logout_user_ok', 'Logout effettuato con successo!');

        redirect('auth');
        exit();
    }
}
