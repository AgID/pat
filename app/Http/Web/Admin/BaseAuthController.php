<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Admin;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use Helpers\Security\Acl;
use Jenssegers\Agent\Agent;
use Model\ConcurrentSessModel;
use Model\SectionsBoModel;
use System\Auth;
use System\BaseController;
use System\Breadcrumbs;
use System\Input;
use System\Registry;

class BaseAuthController extends BaseController
{
    public Breadcrumbs $breadcrumb;
    public Auth $auth;
    public Acl $acl;
    public mixed $sectionInfo;

    /**
     * @description Costruttore
     * @param string|null|array $controller Controller della sezione
     * @throws Exception
     */
    public function __construct(array|string $controller = null)
    {
        parent::__construct();
        helper(['form', 'url', 'string', 'app', 'html']);

        if (!authPatOs()->hasIdentity()) {
            redirect('auth');
            die();
        }

        if (isSuperAdmin(true)) {
            // Sono super admin..
            $this->acl = new Acl();
            Acl::notRun();
        } else {

            // Sono in una sezione di b.o. dove sono necessari i permessi
            if ($controller !== 'not_acl') {

                $this->acl = new Acl($controller);
            } else {

                // L'utente è in una sezione dove non sono necessari i permessi(Dashboard o Profilo Utente)
                Acl::notRun();
            }
        }

        // Section info
        if (!empty($controller)) {
            $tmpData = explode('\\', $controller);
            $query = SectionsBoModel::select(['id', 'name', 'controller', 'url'])
                ->where('controller', end($tmpData))
                ->first();
            $this->sectionInfo = ($query !== null) ? $query->toArray() : null;
            Registry::set('section_info_back_office', $this->sectionInfo);
        }

        $this->breadcrumb = new Breadcrumbs();
        $this->auth = authPatOs();
        $agent = new Agent();
        $getIdentity = $this->auth->getIdentity();

        //if(!isSuperAdmin()) {
        $count = ConcurrentSessModel::where('user_id', '=', $getIdentity['id'])
            ->where('created_at', '>=', date("Y-m-d H:i:s", strtotime("-8 hours")))
            ->where('institution_id', '=', checkAlternativeInstitutionId())
            ->where(function ($query) use ($agent, $getIdentity) {
                $query->where('platform', '!=', $agent->platform())
                    ->orWhere('browser', '!=', $agent->browser())
                    ->orWhere('device', '!=', $agent->device())
                    ->orWhere('ip', '!=', Input::ipAddress())
                    ->orWhere('browser_private_mode', '!=', @$getIdentity['options']['browser_private_mode']);
            })
            ->count();

        if ($count >= 1) {

            $resultsConcurrentSess = ConcurrentSessModel::where('user_id', '=', $getIdentity['id'])
                ->where('created_at', '>=', date("Y-m-d H:i:s", strtotime("-8 hours")))
                ->where('institution_id', '=', checkAlternativeInstitutionId())
                ->get();

            Registry::set('session_allowed', true);
            Registry::set('session_allowed_results', $resultsConcurrentSess->toArray());
        }
        //}
    }
}
