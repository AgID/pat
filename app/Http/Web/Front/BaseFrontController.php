<?php
/**
 * Nome applicativo: PAT
 * Licenza di utilizzo: GNU Affero General Public License» versione 3 e successive: https://spdx.org/licenses/AGPL-3.0-or-later.html
 */

namespace Http\Web\Front;

defined('_FRAMEWORK_') or exit('No direct script access allowed');

use Exception;
use System\BaseController;
use System\Breadcrumbs;

class BaseFrontController extends BaseController
{
    /**
     * @var Breadcrumbs
     */
    protected Breadcrumbs $breadcrumbs;

    /**
     * @throws Exception
     */
    public function __construct()
    {
        parent::__construct();
        helper('FrontOffice/Utility');
    }
}
