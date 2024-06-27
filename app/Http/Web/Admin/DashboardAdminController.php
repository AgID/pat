<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Admin;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Model\ActivityLogModel;
use System\Container;

/**
 * Controller Dashboard
 */
class DashboardAdminController extends BaseAuthController
{
    public function __construct()
    {
        parent::__construct('not_acl');
    }

    /**
     * @throws \Exception
     * @url /admin/dashboard.html
     * @method GET
     */
    public function index()
    {
        $container = Container::getInstance();

        $query = ActivityLogModel::select('activity_log.*')
            ->join('users as u', 'u.id', '=', 'activity_log.user_id')
            ->join('institutions as i', 'activity_log.institution_id', '=', 'i.id', 'left outer')
            ->with(['user' => function ($query) {
                $query->select(['id', 'name', 'deleted']);
            }])
            ->with(['institution' => function ($query) {
                $query->select(['id', 'full_name_institution']);
            }])
            ->whereIn('is_superadmin', (isSuperAdmin() === true) ? [0, 1] : [0])
            ->orderBy('id', 'DESC')
            ->limit(10)
            ->get();

        $data = [];
        $data['breadcrumbs'] = $this->breadcrumb->show();
        $data['logs'] = !empty($query) ? $query->toArray() : [];

        //Dati header della sezione
        $data['titleSection'] = 'Dashboard';
        $data['subTitleSection'] = 'SOMMARIO DELLE FUNZIONI E NOTIFICHE PER L\'UTENTE';
        $data['sectionIcon'] = '<i class="fas fa-tv fa-3x"></i>';
        $hasPermit = $data['hasPermit'] = \Helpers\Security\Acl::hasPermit('ActivityLogAdminController');

        $container->singleton('dashboard_has_permit', function () use ($hasPermit) {
            return $hasPermit;
        });


        $data['identity'] = authPatOs()->getIdentity();

        // Controllo doppia autenticazione
        $checkSessionAllowed = session()->getFlash('last_access_auth');
        $data['session_allowed'] = null;
        if ($checkSessionAllowed !== null) {

            $template = 'La piattaforma ha riscontrato una doppia autenticazione in data ' . date('d-m-Y H:i:s', $checkSessionAllowed['date']) . ' con le seguenti informazioni: <br />';
            foreach (unserialize($checkSessionAllowed['extra_info']) as $key => $value) {
                $template .= '- ' . ucfirst(str_replace('_', ' ', $key)) . ' : ' . $value . '<br />';
            }

            $data['session_allowed'] = $template;
        }

        render('dashboard/dashboard', $data, 'admin');
    }
}
